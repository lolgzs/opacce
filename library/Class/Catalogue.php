<?php
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
////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Catalogues de notices
////////////////////////////////////////////////////////////////////////////////
class CatalogueLoader extends Storm_Model_Loader {
	const DEFAULT_ITEMS_BY_PAGE = 100;

	public function loadNoticesFor($catalogue, $itemsByPage = self::DEFAULT_ITEMS_BY_PAGE, $page = 1, $find_all_params = null) {
		if (!is_array($find_all_params))
			$find_all_params = array();

		if (!isset($find_all_params['limitPage']))
			$find_all_params['limitPage'] = array($page, $itemsByPage);

		if (null == $catalogue)
			return array();

		if ('' == ($where = $this->clausesFor($catalogue)))
			return array();
		
		$find_all_params['where'] = $where;
		return Class_Notice::getLoader()->findAllBy($find_all_params);
	}


	public function countNoticesFor($catalogue) {
		if (!$catalogue)
			return 0;
		
		if ('' == ($where = $this->clausesFor($catalogue)))
			return 0;

		return Class_Notice::getLoader()->countBy(array('where' => $where));
	}


	public function clausesFor($catalogue) {
		$conditions = array();
		if ($fromUntil = $this->fromUntilClauseFor($catalogue))
			$conditions[] = $fromUntil;

		if ($catalogue->isMatchingAllNotices())
			return $fromUntil ? $fromUntil : '1=1';


		if ($facets = $this->facetsClauseFor($catalogue))
			$conditions[] = $facets;
		
		if ($docType = $this->docTypeClauseFor($catalogue)) 
			$conditions[] = $docType;

		if ($year = $this->yearClauseFor($catalogue)) 
			$conditions[] = $year;

		if ($cote = $this->coteClauseFor($catalogue))
			$conditions[] = $cote;

		if ($new = $this->nouveauteClauseFor($catalogue))
			$conditions[] = $new;


		if (0 == count($conditions))
			return '';
		
		return implode(' and ', $conditions);
	}


	/**
	 * @param $catalogue Class_Catalogue
	 * @return string
	 */
	public function facetsClauseFor($catalogue, $against = '') {
		$against_ou = '';
		$facets = array('B' => $catalogue->getBibliotheque(),
										'S' => $catalogue->getSection(),
										'G' => $catalogue->getGenre(),
										'L' => $catalogue->getLangue(),
										'Y' => $catalogue->getAnnexe(),
										'E' => $catalogue->getEmplacement());

		foreach ($facets as $k => $v) 
			$against .= Class_Catalogue::getSelectionFacette($k, $v);
		
		$facets = array('A' => $catalogue->getAuteur(),
										'M' => $catalogue->getMatiere(),
										'D' => $catalogue->getDewey(),
										'P' => $catalogue->getPcdm4(),
										'T' => $catalogue->getTags(),
										'F' => $catalogue->getInteret());

		foreach ($facets as $k => $v) 
			$against_ou .= Class_Catalogue::getSelectionFacette($k, $v, in_array($k, array('M', 'D', 'P')), false);


		if ('' != $against_ou) 
			$against .= ' +(' . $against_ou . ")";

		if ('' == $against)
			return '';

		return "MATCH(facettes) AGAINST('".$against."' IN BOOLEAN MODE)";
	}


	public function docTypeClauseFor($catalogue) {
		if (!$docType = $catalogue->getTypeDoc())
			return '';

		$parts = explode(';', $docType);
		if (1 == count($parts)) 
			return 'type_doc=' . $parts[0];

		return 'type_doc IN (' . implode(', ', $parts) .  ')';
	}


	public function yearClauseFor($catalogue) {
		$clauses = array();
		if ($start = $catalogue->getAnneeDebut()) 
			$clauses[] = "annee >= '" . $start . "'";

		if($end = $catalogue->getAnneeFin()) 
			$clauses[] = "annee <= '" . $end . "'";

		if (0 == count($clauses))
			return '';

		return implode(' and ', $clauses);
	}


	public function coteClauseFor($catalogue) {
		$clauses = array();
		if ($start = $catalogue->getCoteDebut()) 
			$clauses[] = "cote >= '" . strtoupper($start) . "'";

		if ($end = $catalogue->getCoteFin()) 
			$clauses[] = "cote <= '". strtoupper($end) . "'";

		if (0 == count($clauses))
			return '';

		return implode(' and ', $clauses);
	}


	public function nouveauteClauseFor($catalogue) {
		if (1 != $catalogue->getNouveaute())
			return '';

		return 'date_creation >= \'' . date('Y-m-d') . '\'';
	}


	public function fromUntilClauseFor($catalogue) {
		$clauses = array();

		if ($start = $catalogue->getFrom()) 
			$clauses[] = "left(date_maj, 10) >= '" . $start . "'";

		if($end = $catalogue->getUntil()) 
			$clauses[] = "left(date_maj, 10) <= '" . $end . "'";

		if (0 == count($clauses))
			return '';

		return implode(' and ', $clauses);
	}
}


class Class_Catalogue extends Storm_Model_Abstract {
	protected $_table_name = 'catalogue';
	protected $_table_primary = 'ID_CATALOGUE';
	protected $_loader_class = 'CatalogueLoader';

	protected $_default_attribute_values = array('oai_spec' => '',
																							 'description' => '',
																							 'bibliotheque' => '',
																							 'section' => '',
																							 'genre' => '',
																							 'langue' => '',
																							 'annexe' => '',
																							 'emplacement' => '',
																							 'auteur' => '',
																							 'matiere' => '',
																							 'dewey' => '',
																							 'pcdm4' => '',
																							 'tags' => '',
																							 'interet' => '',
																							 'type_doc' => '',
																							 'annee_debut' => '',
																							 'annee_fin' => '',
																							 'cote_debut' => '',
																							 'cote_fin' => '',
																							 'nouveaute' => '');

	protected $_from;
	protected $_until;

	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	public static function newCatalogueForAll() {
		return new AllNoticesCatalogue();
	}


	public function acceptVisitor($visitor) {
		$visitor->visitOaiSpec($this->getOaiSpec());
		$visitor->visitLibelle($this->getLibelle());
		if ($this->hasDescription())
			$visitor->visitDescription($this->getDescription());
	}


	public function getNotices($page = 1, $itemsByPage = CatalogueLoader::DEFAULT_ITEMS_BY_PAGE, $params = null) {
		return self::getLoader()->loadNoticesFor($this, $itemsByPage, $page, $params);
	}


	public function getNoticesCount() {
		return self::getLoader()->countNoticesFor($this);
	}

	
	public function isMatchingAllNotices() {
		return false;
	}


//------------------------------------------------------------------------------
// Rend les notices et les stats (test d'un catalogue)
//------------------------------------------------------------------------------
	public function getTestCatalogue() {
		// Notices et temps d'execution
		$preferences = $this->toArray();
		$preferences['nb_notices'] = 20;
		$requetes=$this->getRequetes($preferences);
		$ret["requete"]=$requetes["req_liste"];
		$temps=time();

		$ret["notices"] = $this->getNotices(null, null, 
																				array('limitPage' => array(1, $preferences['nb_notices']),
																							'order' => 'alpha_titre'));
		$ret["temps_execution"]=(time()-$temps);
		$ret["nb_notices"]=fetchOne($requetes["req_comptage"]);

		// Avec vignettes en cache
		$req=$requetes["req_comptage"];
		if(strpos($req,"where") > 0) $req.=" and "; else $req.=" where ";
		$req.="url_vignette > '' and url_vignette != 'NO'";
		$ret["avec_vignettes"]=fetchOne($req);
		
		return $ret;
	}


	public function shouldCacheContent() {
		if (Class_Users::getLoader()->isCurrentUserAdmin())
			return false;

		return Class_AdminVar::isCacheEnabled();
	}


	public function getNoticesFromCacheByPreferences($preferences, $cache_vignette) {
		$cache_key = md5(serialize($preferences).$cache_vignette);
		$cache = Zend_Registry::get('cache');

		if ($this->shouldCacheContent() && $cache->test($cache_key))
			return unserialize($cache->load($cache_key));

		$notices = $this->_fetchAllNoticesByPreferences($preferences, $cache_vignette);			
		$cache->save(serialize($notices), $cache_key);
		return $notices;
	}

//------------------------------------------------------------------------------
// Rend les notices selon les preferences (kiosques)
//------------------------------------------------------------------------------
	public function getNoticesByPreferences($preferences,$cache_vignette=false)	{
		$notices = $this->getNoticesFromCacheByPreferences($preferences, $cache_vignette);

		if ((int)$preferences["aleatoire"] !== 1) 
			return $notices;

		shuffle($notices);
		return array_slice ($notices, 0, $preferences["nb_notices"]);   
	}


	public function _fetchAllNoticesByPreferences($preferences, $cache_vignette) {
		// Lancer la requete
		$requetes=$this->getRequetes($preferences);
		if (!array_key_exists("req_liste", $requetes))
			return array();

		$req_liste = str_replace('select *',
														 'select notices.id_notice, notices.editeur, notices.annee, notices.date_creation, notices.facettes, notices.clef_oeuvre', 
														 $requetes["req_liste"]);

		$catalogue=fetchAll($req_liste);
		if (!$catalogue) 
			return array();
		
		//Instanciations
		$class_notice = new Class_Notice();
		$class_img = new Class_WebService_Vignette();
		
		$notices = array();
		// Formatter les notices
		foreach($catalogue as $notice)	{
			$enreg=$class_notice->getNotice($notice["id_notice"],'TA');
			$vignette = '';
			if ($cache_vignette)	{
				if($cache_vignette=="url") $mode=false; else $mode=true;
				$vignette=$class_img->getImage($enreg["id_notice"],$mode);
			}

			if (!$cache_vignette or $vignette) {
				$notices[]=array(
											 "id_notice" => $enreg["id_notice"],
											 "titre" => $enreg["T"], 
											 "auteur" => $enreg["A"],
											 "vignette" => $vignette,
											 "type_doc" => $enreg["type_doc"],
											 "editeur" => $notice["editeur"],
											 "annee" => $notice["annee"],
											 "date_creation" => $notice["date_creation"],
											 "facettes" => $notice["facettes"],
											 "clef_oeuvre" => $notice["clef_oeuvre"]);
			}
		}
		return $notices;
	}



//------------------------------------------------------------------------------
// Rend les notices selon les preferences
//------------------------------------------------------------------------------
	public function getRequetes($preferences, $no_limit=false)	{
		// Si panier traitement special
		if (isset($preferences["id_panier"])  && (0 !== (int)$preferences["id_panier"])) 
			return $this->getRequetesPanier($preferences);

		// Lire les proprietes du catalogue
		$against = $this->selectionFacettesForCatalogueRequestByPreferences($preferences);
		if ($catalogue = $this->getLoader()->find($preferences['id_catalogue'])) {
			$conditions = array($this->getLoader()->facetsClauseFor($catalogue, $against));

			$conditions []= $this->getLoader()->docTypeClauseFor($catalogue);
			$conditions []= $this->getLoader()->yearClauseFor($catalogue);
			$conditions []= $this->getLoader()->coteClauseFor($catalogue);
			$conditions []= $this->getLoader()->nouveauteClauseFor($catalogue);
		} else {
			$conditions = $against ? array("MATCH(facettes) AGAINST('".$against."' IN BOOLEAN MODE)") : array();
		}

		// Notices avec vignettes uniquement
		if (isset($preferences['only_img']) && ($preferences["only_img"] == 1)) 
			$conditions[]="url_vignette > '' and url_vignette != 'NO'";

		// Notices avec avis seulement
		$join = (isset($preferences['avec_avis']) && ($preferences["avec_avis"] == 1)) 
			?	" INNER JOIN notices_avis ON notices.clef_oeuvre=notices_avis.clef_oeuvre " 
			: '';

		// Clause where
		if ($where = implode(' and ', array_filter($conditions)))
			$where = ' where '.$where;

		// Calcul des requetes
		$order_by = $this->orderByForCatalogueRequestByPreferences($preferences);
		$limite = $this->limitForCatalogueRequestByPreferences($preferences, $no_limit);
		$ret["req_liste"]="select * from notices ".$join.$where.$order_by.$limite;
		$ret["req_comptage"]="select count(*) from notices ".$join.$where;
		$ret["req_facettes"]="select notices.id_notice,type_doc,facettes from notices ".$join.$where.$limite;

		return $ret;
	}


	public function selectionFacettesForCatalogueRequestByPreferences($preferences) {
		if (!isset($preferences["facettes"]))
			return '';

		$against = '';
		$facettes=explode(";", $preferences["facettes"]);
		foreach($facettes as $facette) {
			$facette=trim($facette); 
			$against.=$this->getSelectionFacette(substr($facette,0,1),substr($facette,1));
		}

		return $against;
	}


	public function orderByForCatalogueRequestByPreferences($preferences) {
		if(!array_key_exists("tri", $preferences) || $preferences["tri"]==0) 
			return " order by alpha_titre ";
		
		if ($preferences["tri"]==1) 
			return " order by date_creation DESC ";
		
		if ($preferences["tri"]==2) 
			return " order by nb_visu DESC ";
	}


	public function limitForCatalogueRequestByPreferences($preferences, $no_limit=false) {
		$limite = 0;

		if (isset($preferences["aleatoire"]) && (int)$preferences["aleatoire"]==1) 
			$limite = (int)$preferences["nb_analyse"];
		else if (isset($preferences['nb_notices']))
			$limite = (int)$preferences["nb_notices"];

		if ($limite and !$no_limit) 
			return " LIMIT 0,".$limite;

	  return " LIMIT 5000"; //LL: j'ai rajouté une limite max car explosion mémoire sur des catalogues mal définis
	}

	
	//----------------------------------------------------------------------------
	// Calcul de la clause against pour une facette
	//----------------------------------------------------------------------------
	public static function getSelectionFacette($type, $valeurs, $descendants = false, $signe = true) {
		if (!$valeurs) 
			return false;

		$valeurs = explode(';', $valeurs);
		$cond = '';
		foreach ($valeurs as $valeur) {
			if (!$valeur)
        continue;

			if (!$descendants) {
				$cond .= $type . $valeur . ' ';
				continue;
			}

			if ('M' != $type) {
				$cond .= $type . $valeur . '* ';
				continue;
			}

			if (!$matiere = Class_Matiere::getLoader()->find($valeur))
				continue;
			
			if ('' != ($sous_vedettes = trim($matiere->getSousVedettes())))
			  $valeur .= str_replace(' ', ' M', ' ' . $sous_vedettes);
			$cond .= $type . $valeur . ' ';
		}

		$cond = trim($cond);

		if ($signe) 
			return ' +(' . $cond . ')';

		return ' ' . $cond;
	}

	//------------------------------------------------------------------------------
	// Rend les requetes pour un panier selon les preferences
	//------------------------------------------------------------------------------
	public function getRequetesPanier($preferences)
	{
		if (array_key_exists('id_user', $preferences))
			$panier = Class_PanierNotice::getLoader()->findFirstBy(array('id_user' => $preferences['id_user'],
																																	 'id_panier' => $preferences['id_panier']));
		else $panier = Class_PanierNotice::getLoader()->find($preferences['id_panier']);
		if (!$panier) 
			return array("nombre" => 0);

		$cles_notices = $panier->getClesNotices();
		if (empty($cles_notices))
		{
			$ret["nombre"]=0;
			return $ret;
		}
		
		foreach($cles_notices as $notice) {
			if(!trim($notice)) continue;
			if(isset($in_sql)) $in_sql .=","; else $in_sql = '';
			$in_sql.="'".$notice."'";
		}

		// Nombre a lire
		if($preferences["aleatoire"]==1) $limite=$preferences["nb_analyse"];
		else $limite=$preferences["nb_notices"];
		if($limite) $limite="LIMIT 0,".$limite; else $limite="";

		// Ordre
		$order_by ="";
		if($preferences["tri"]==0) $order_by=" order by alpha_titre ";
		if($preferences["tri"]==1) $order_by=" order by date_creation DESC ";
		if($preferences["tri"]==2) $order_by=" order by nb_visu DESC ";

		$condition = '';
		// Notices avec vignettes uniquement
		if (array_isset("only_img", $preferences) && $preferences["only_img"] == 1) 
			$condition=" and url_vignette > '' and url_vignette != 'NO' ";

		// Notices avec avis seulement
		$join = '';
		if (array_isset("avec_avis", $preferences) && $preferences["avec_avis"] == 1) 
			$join = " INNER JOIN notices_avis ON notices.clef_oeuvre=notices_avis.clef_oeuvre ";

		// Retour
		$ret["req_liste"]="select * from notices ".$join."where notices.clef_alpha in(".$in_sql.")".$condition.$order_by.$limite;
		$ret["req_comptage"]="select count(*) from notices ".$join."where notices.clef_alpha in(".$in_sql.")".$condition;
		$ret["req_facettes"]="select id_notice,type_doc,facettes from notices ".$join."where notices.clef_alpha in(".$in_sql.") ".$condition.$limite;
		return $ret;
	}

	//-------------------------------------------------------------------------------
	// liste des catalogues (structure complete)
	//-------------------------------------------------------------------------------
	public function getCatalogue($id_catalogue)
	{
		if($id_catalogue) return fetchEnreg("select * from catalogue where ID_CATALOGUE=$id_catalogue");
		else return fetchAll("select * from catalogue order by LIBELLE");
	}
	
	//-------------------------------------------------------------------------------
	// liste des catalogues pour une combo
	//-------------------------------------------------------------------------------
	static function getCataloguesForCombo()	{
		$liste = array();
		$catalogues=fetchAll("select * from catalogue order by libelle");

		if(!$catalogues)	return $liste;

		$liste[""]=" ";
		foreach($catalogues as $catalogue) 
			$liste[$catalogue["ID_CATALOGUE"]]=$catalogue["LIBELLE"];
		return $liste;
	}


	public function setAnneeDebut($value) {
		return $this->checkAndSetAnnee('annee_debut', $value);
	}


	public function setAnneeFin($value) {
		return $this->checkAndSetAnnee('annee_fin', $value);
	}
	

	public function checkAndSetAnnee($attribute, $value) {
		$value = (int)$value;
		if ($value < 1000 || $value > date("Y"))
			$value = '';
		return parent::_set($attribute, $value);
	}


	public function validate() {
		$this->checkAttribute('libelle', $this->getLibelle(), 'Le libellé est requis');
		$this->checkAttribute('annee_fin', 
													!($this->getAnneeDebut() and $this->getAnneeFin()) || $this->getAnneeDebut() <= $this->getAnneeFin(),
													"L'année de début doit être inférieure ou égale à l'année de fin");
		$this->checkAttribute('oai_spec',
													!$this->getOaiSpec() || preg_match('/^[a-zA-Z0-9_.-]+$/', $this->getOaiSpec()),
													"La spec OAI ne peut contenir que les caractères suivants: de a à z, 0 à 9, - _ ."); 
		
	}


	public function setFrom($from) {
		$this->_from = $from;
		return $this;
	}


	public function getFrom() {
		return $this->_from;
	}


	public function setUntil($until) {
		$this->_until = $until;
		return $this;
	}


	public function getUntil() {
		return $this->_until;
	}
}




class AllNoticesCatalogue extends Class_Catalogue {
	public function isMatchingAllNotices() {
		return true;
	}
}

?>