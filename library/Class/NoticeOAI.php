<?PHP
/**
 * Copyright (c) 2012, Agence Française Informatique (AFI). All rights reserved.
 *
 * AFI-OPAC 2.0 is free software; you can redistribute it and/or modify
 * it under the terms of the GNU AFFERO GENERAL PUBLIC LICENSE as published by
 * the Free Software Foundation.
 *
 * There are special exceptions to the terms and conditions of the AGPL as it
 * is applied to this software (see README file).
 *
 * AFI-OPAC 2.0 is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU AFFERO GENERAL PUBLIC LICENSE for more details.
 *
 * You should have received a copy of the GNU AFFERO GENERAL PUBLIC LICENSE
 * along with AFI-OPAC 2.0; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA 02110-1301  USA 
 */

class TableNoticesOAI extends Zend_Db_Table_Abstract {
    protected $_name = 'oai_notices';
		
		public function insertOrUpdate($data) {
			$statement = 
				"INSERT INTO ".$this->_name." (date, id_entrepot, id_oai, alpha_titre, data, recherche) ".
				"VALUES ('".$data['date']."','".$data['id_entrepot']."','".$data['id_oai']."','".$data['alpha_titre']."','".$data['data']."','".$data['recherche']."') ".
				"ON DUPLICATE KEY UPDATE date='".$data['date']."', alpha_titre='".$data['alpha_titre']."', data='".$data['data']."', recherche='".$data['recherche']."';";
			sqlExecute($statement);
		}
}


class Class_NoticeOAI extends Storm_Model_Abstract {
	protected $_table_name = 'oai_notices';
	protected $_belongs_to = array('entrepot' => array('model' => 'Class_EntrepotOAI',
																										  'referenced_in' => 'id_entrepot'));

	protected $_table_notices;
	protected $_oai_service;
	protected $_entrepot_oai;


	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	public static function findNoticesByExpression($expression) {
		$instance = new self();
		$requetes = $instance->recherche(array('expressionRecherche' => $expression));

		if ($requetes['erreur'])
			throw new Class_SearchException($requetes['erreur']);

		$rows = $instance->getPageResultat($requetes['req_liste']);
		$notices = array();
		
		foreach($rows as $row)
			$notices[] = self::getLoader()->newFromRow($row);
		
		return $notices;
	}


	public function updateAttributes(Array $datas) {
		parent::updateAttributes($datas);
		if (array_key_exists('data', $datas))
			$this->updateAttributes(unserialize($datas['data']));
		return $this;
	}


	public function harvestSet($entrepot_oai, $set) {
		$this->_entrepot_oai = $entrepot_oai;

		$records = $this
			->getOAIService()
			->setOAIHandler($this->_entrepot_oai->getHandler())
			->getRecordsFromSet($set);

		$this->saveRecords($records);		
		return $this->getOAIService()->getListRecordsResumptionToken();
	}


	public function getDataAsArray() {
		return unserialize($this->getData());
	}


	public function getTitre() {
		if ($titre = $this->TITRE)
			return $titre;

		return $this->extractData('titre');
  }


	public function getAuteur() {
		return $this->extractData('auteur');
	}


	public function extractData($name) {
	  $datas = $this->getDataAsArray();
		return isset($datas[$name]) ? $datas[$name] : '';
	}


	public function resumeHarvest($entrepot_oai, $resumptionToken) {
		$this->_entrepot_oai = $entrepot_oai;

		$oai_service = $this
			->getOAIService()
			->setOAIHandler($this->_entrepot_oai->getHandler())
			->setListRecordsResumptionToken($resumptionToken);

		if (!$oai_service->hasNextRecords())
			return null;

		$records = $oai_service->getNextRecords();
		$this->saveRecords($records);		
		return $oai_service->getListRecordsResumptionToken();
	}


	public function getTableNoticesOAI() {
		if (!isset($this->_table_notices))
			$this->_table_notices = new TableNoticesOAI();
		return $this->_table_notices;
	}


	public function setTableNoticesOAI($table) {
		$this->_table_notices = $table;
	}


	public function getOAIService() {
		if (!isset($this->_oai_service))
			$this->_oai_service = new Class_WebService_OAI();
		return $this->_oai_service;
	}


	public function setOAIService($service) {
		$this->_oai_service = $service;
	}


	protected function saveRecords(&$records) {
		foreach ($records as $record)
			$this->saveRecord($record);
	}


	protected function getIndexation() {
		if (!isset($this->_indexation))
			$this->_indexation = new Class_Indexation();
		return $this->_indexation;
	}
	
	protected function toAlpha($data) {
		return $this->getIndexation()->alphaMaj($data);
	}


	protected function saveRecord(&$record) {
		$recherche = array(
											 'titre' => $record['titre'],
											 'auteur' => $record['auteur'],
											 'editeur' => $record['editeur'],
											 'description' => $record['description'],
											 'date' => $record['date']);

			$this
				->getTableNoticesOAI()
				->insertOrUpdate(array(
															 'date' => $record['date'],
															 'id_oai' => addslashes($record['id_oai']),
															 'id_entrepot' => $this->_entrepot_oai->getId(),
															 'alpha_titre' => addslashes($this->toAlpha($record['titre'])),
															 'data' => addslashes(serialize($record)),
															 'recherche' => addslashes($this->getIndexation()->getFullText($recherche))
															 ));
	}

	//------------------------------------------------------------------------------------------------------
	// LISTE DES ENTREPOTS QUI ONT DES NOTICES
	//------------------------------------------------------------------------------------------------------
	public function getEntrepots()
	{
		$liste=fetchAll("select distinct(id_entrepot) from oai_notices");
		if(!$liste) return false;
		$ret['']="** toutes **";
		foreach($liste as $entrepot)
		{
			$id_entrepot=$entrepot["id_entrepot"];
			$libelle=fetchOne("select libelle from oai_entrepots where id=$id_entrepot");
			$ret[$id_entrepot]=$libelle;
		}
		return $ret;
	}

	//------------------------------------------------------------------------------------------------------
	// RECHERCHE
	//------------------------------------------------------------------------------------------------------
	public function recherche($rech)	{
		$translate = Zend_Registry::get('translate');
		$ix = new Class_Indexation();
		$ret = array('nb_mots' => 0);

		// Analyse de l'expression
		$mots=$ix->getMots($rech["expressionRecherche"]);
		$recherche="";
		foreach($mots as $mot)	{
			if($mot = $ix->getExpressionRecherche($mot))	{
				$ret["nb_mots"]++;
				$recherche.=" +".$mot;
			}
		}

		$recherche=trim($recherche);
		if(!$recherche)  {
			$ret["statut"]="erreur"; 
			$ret["erreur"]=$translate->_("Il n'y aucun mot assez significatif pour la recherche"); 
			return $ret;}

		// Constitution des requetes
		$against = " AGAINST('".$recherche."' IN BOOLEAN MODE)";
		$where = 'where MATCH(recherche)';
		$conditions = '';
		if (isset($rech["id_entrepot"]))
			$conditions = " and id_entrepot=".$rech["id_entrepot"];

		$order_by=" order by alpha_titre";
		$req_liste = "select id from oai_notices ".$where.$against.$conditions.$order_by;
		$req_comptage = "Select count(*) from oai_notices ".$where.$against.$conditions;

		// Lancer les requetes
		$nb=fetchOne($req_comptage);
		if(!$nb) {
			$ret["statut"]="erreur";
			$ret["erreur"]=$translate->_("Aucun résultat trouvé");
			return $ret;
		}
		$ret["nombre"]=$nb;
		$ret["req_liste"]=$req_liste;
		return $ret;
	}

//------------------------------------------------------------------------------------------------------
// Execute une requete notices et rend 1 page
//------------------------------------------------------------------------------------------------------
	public function getPageResultat($req, $page = 1)	{
		// Nombre par page
		$this->nb_par_page=10;

		// Calcul de la limite
		$page = (int)$page ? (int)$page : 1;
		$debut_limit = $fin_limit = 0;

		if(strpos($req," LIMIT ") === false) {
			$limit = ($page-1) * $this->nb_par_page;
			$limit = " LIMIT ".$limit.",". $this->nb_par_page;
			$req.=$limit;
		}
		else {
			$debut_limit=($page-1) * $this->nb_par_page;
			$fin_limit=$this->nb_par_page;
		}

		// Execute la requete
		$ids=fetchAll($req);
		if($fin_limit) $ids=array_slice ($ids, $debut_limit, $fin_limit);
		foreach($ids as $lig)	{
			$ret[]=$this->getNotice($lig["id"]);
		}
		return $ret;
	}

	//------------------------------------------------------------------------------------------------------
	// Rend les elements d'une notice affichable
	//------------------------------------------------------------------------------------------------------
	public function getNotice($id_notice)
	{
		$enreg=fetchEnreg("select * from oai_notices where id=$id_notice");
		$data=unserialize($enreg["data"]);
		$data["id"] = $enreg["id"];
		$data["source"]=$enreg["id_entrepot"];
		return $data;
	}
}

?>