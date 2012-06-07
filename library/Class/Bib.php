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
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//																											Table Name
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
require_once dirname(__FILE__)."/CompositeBuilder.php";

class BibCSite extends Zend_Db_Table_Abstract
{
	protected $_name = 'bib_c_site';
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//																							 Class_Bib -> gestion des bibliothèques
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////



class BibLoader extends Storm_Model_Loader {
	protected $_portail;

	public function findAllWithPortail() {
		$all_bibs = $this->findAll();
		array_unshift($all_bibs, $this->getPortail());
		return $all_bibs;
	}


	public function findAllByWithPortail($args) {
		$bibs = $this->findAllBy($args);
		array_unshift($bibs, $this->getPortail());
		return $bibs;
	}


	public function getPortail() {
		if (!isset($this->_portail))
			$this->_portail = $this->newInstanceWithId(0)->setLibelle('Portail');
		return $this->_portail;
	}
}



class Class_Bib extends Storm_Model_Abstract {
	const V_INVISIBLE = 0;
	const V_NODATA = 1;
	const V_DATA = 2;

	private $_dataBaseError = "Problème d'accès à  la base de données";
	private $statut_bib = array('Invisible','N\'envoie pas de données','Envoie des données');

  protected $_loader_class = 'BibLoader';
	protected $_table_name = 'bib_c_site';
	protected $_table_primary = 'ID_SITE';
	protected $_has_many = array('profils' => array('model' => 'Class_Profil',
																									'role' => 'bib'),

															 'article_categories' => array('model' => 'Class_ArticleCategorie',
																														 'role' => 'bib',
																														 'scope' => array('ID_CAT_MERE' => 0),
																														 'order' => 'libelle'));

	protected $_belongs_to = array('zone' => array('model' => 'Class_Zone',
																								  'role' => 'bib',
																								  'referenced_in' => 'id_zone'));

	protected $_default_attribute_values = array('visibilite' => 0,
																							 'libelle' => '',
																							 'id_zone' => 0,
																							 'ville' => '',
																							 'aff_zone' => '');

	protected $_translate;

	public function __construct() {
		$this->_translate = Zend_Registry::get('translate');
		$this->statut_bib = array($this->_translate->_('Invisible'),
															$this->_translate->_('N\'envoie pas de données'),
															$this->_translate->_('Envoie des données'));
	}


	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	public function getLieu() {
		return Class_Lieu::getLoader()
			->newInstance()
			->setAdresse($this->getAdresse())
			->setCodePostal($this->getCp())
			->setVille($this->getVille());
	}


	//----------------------------------------------------------------------------
	// Rend une bib
	//----------------------------------------------------------------------------
	public function getBib($id_bib)	{
		$bib=fetchEnreg("select * from bib_c_site where ID_SITE=$id_bib");
		return $bib;
	}


	public function getAffZoneAsArray() {
		if (!$aff_zone = ZendAfi_Filters_Serialize::unserialize($this->getAffZone()))
			$aff_zone = array('profilID' => null, 
												'libelle' => '', 
												'posX' => 0, 
												'posY' => 0, 
												'posPoint' => '');
		return $aff_zone;
	}


	public function getUrl() {
		$props = $this->getAffZoneAsArray();
		if (array_isset("profilID", $props) and (1 < ($profilID = $props["profilID"]))) // mode moulins => adresse du profil précisé en config
			return sprintf("%s?id_profil=%d",BASE_URL, $profilID);

		return sprintf("%s/bib/bibview/id/%d",
									 BASE_URL,
									 $this->getId()); //mode calice => pointe vers la bib
	}


	public function getBibsBySite($id_site=0)	{
		$where = '';
		if($id_site!=0)
			$where=" Where ID_SITE=".$id_site;

		$bibs=fetchAll("select * from bib_c_site".$where." order by LIBELLE");
		if ($bibs==false) $bibs = array();
		return $bibs;
	}

	//----------------------------------------------------------------------------
	// Rend une liste de bibs
	//----------------------------------------------------------------------------
	public function getBibs($id_zone=0)
	{
		$where = '';
		if($id_zone and $id_zone !="ALL")
		{
			if($id_zone=="PORTAIL") $id_zone=0;
			$where=" Where ID_ZONE=".$id_zone;
		}
		$bibs=fetchAll("select * from bib_c_site".$where." order by LIBELLE");
		if ($bibs==false) $bibs = array();
		return $bibs;
	}

	//----------------------------------------------------------------------------
	// Supprimer une bibliotheque
	//----------------------------------------------------------------------------
	public function deleteBib($id_bib)
	{
		$id_bib=intval($id_bib);
		if(!$id_bib) return false;
		sqlExecute("delete from bib_c_site where ID_SITE=$id_bib");
		$this->majCosmogramme($id_bib,"","0");
	}

	//----------------------------------------------------------------------------
	// Verif la saisie
	//----------------------------------------------------------------------------
	private function verifData($data)
	{
		if ( trim($data['LIBELLE']) == '')
			$errorMessage = $this->_translate->_("Vous devez compléter le champ 'Nom'");

		if ( trim($data['VILLE']) == '')
			$errorMessage = $this->_translate->_("Vous devez compléter le champ 'Ville'");

		return $errorMessage;
	}

	//----------------------------------------------------------------------------
	// Controle de suppression d'une bibliothèque
	//----------------------------------------------------------------------------
	function isBibDeletable($id_bib)
	{
		$cms=fetchOne("Select count(*) from cms_categorie where ID_SITE=$id_bib");
		$rss=fetchOne("Select count(*) from rss_categorie where ID_SITE=$id_bib");
		$sito=fetchOne("Select count(*) from sito_categorie where ID_SITE=$id_bib");
		$user=fetchOne("Select count(*) from bib_admin_users where ID_SITE=$id_bib");
		$ex=fetchOne("select count(*) from exemplaires where id_bib=$id_bib");

		if($cms+$rss+$sito+$user+$ex > 0) return false;
		else return true;
	}

	// ----------------------------------------------------------------
	// Mise à jour de la table int-bib dans cosmogramme
	// ----------------------------------------------------------------
	private function majCosmogramme($id_bib,$ville,$visibilite)
	{
		$ville=addslashes($ville);
		if($visibilite > "1")
		{
			$controle=fetchOne("select count(*) from int_bib where id_bib=$id_bib");
			if(!$controle) sqlExecute("insert into int_bib(id_bib,nom_court) Values($id_bib,'$ville')");
			else sqlExecute("update int_bib set nom_court='$ville' where id_bib=$id_bib");
		}
		else
		{
			$controle=fetchOne("select count(*) from exemplaires where id_bib=$id_bib");
			if($controle) return false;
			sqlExecute("delete from int_bib where id_bib=$id_bib");
			sqlExecute("delete from notices_succintes where id_bib=$id_bib");
			sqlExecute("delete from integrations where id_bib=$id_bib");
		}
	}

	// ----------------------------------------------------------------
	// Return un select avec la liste des bibs groupées par zone
	// ----------------------------------------------------------------
	public function getComboBib($id_bib=0,$id_zone=0,$all=true, $tag_name="bib") {
		$selectAll = '';
		if (!$id_bib) $selectAll = 'selected="selected"';
		$html[]='<select name="'.$tag_name.'" id="'.$tag_name.'" style="width:100%">';
		if($all) $html[]='<option value="0" '.$selectAll.'>** '.$this->_translate->_('Toutes').' **</option>';
		$zone_class = new Class_Zone();
		$zone_array = $zone_class->getZones($id_zone);
		foreach ($zone_array as $zone)
		{
			$html[]='<optgroup label="'.$zone["LIBELLE"].'" style="font-style:normal;color:#FF6600">';
			$bib_array = $this->getBibs($zone["ID_ZONE"]);
			// les bibs
			if(!$bib_array) continue;
			foreach ($bib_array as $bib)
			{
				if ($id_bib == $bib["ID_SITE"]) $selected='selected="selected"'; else $selected='';
				$html[]='<option style="color:#575757" value="'.$bib["ID_SITE"].'" '.$selected.'>'.$bib["LIBELLE"].'</option>';
			}
			$html[]='</optgroup>';
		}
		$html[]='</select>';
		return implode('',$html);
	}


	// Lire les bibs par une id_zone =============== OLD FONCTION =========

	public function getAllBibByIdZone($id_zone=0)
	{
		try
		{
			$BibCSite = new BibCSite();
			if($id_zone == 0)
			{
				$select = $BibCSite->getAdapter()->select()
					->from('bib_c_site',array('ID_SITE'=>'ID_SITE','LIBELLE'=>'LIBELLE', 'ID_ZONE'=>'ID_ZONE','VILLE'=>'VILLE','MAIL'=>'MAIL','URL_WEB'=>'URL_WEB','TELEPHONE'=>'TELEPHONE','VISIBILITE'=>'VISIBILITE'))
					->where('ID_ZONE >=?', 0)
					->order('VILLE');
				$stmt = $select->query();
				$row = $stmt->fetchAll();
				return $row;
			}
			else
			{
				$select = $BibCSite->getAdapter()->select()
					->from('bib_c_site',array('ID_SITE'=>'ID_SITE','LIBELLE'=>'LIBELLE', 'ID_ZONE'=>'ID_ZONE','VILLE'=>'VILLE','MAIL'=>'MAIL','URL_WEB'=>'URL_WEB','TELEPHONE'=>'TELEPHONE','VISIBILITE'=>'VISIBILITE'))
					->where('ID_ZONE=?', $id_zone)
					->order('VILLE');
				$stmt = $select->query();
				$row = $stmt->fetchAll();
				return $row;
			}
		}catch (Exception $e)
		{
			logErrorMessage('Class: Class_Zone; Function: getAllBibByIdZone' . NL . $e->getMessage());
			return $this->_dataBaseError;
		}
	}

	public function getBibById($id_bib)
	{
		try
		{
			$BibCSite = new BibCSite();
			$where = $BibCSite->getAdapter()->quoteInto('ID_SITE=?', $id_bib);
			return $fetch = $BibCSite->fetchRow($where);
		}catch (Exception $e)
		{
			logErrorMessage('Class: Class_Zone; Function: getBibById' . NL . $e->getMessage());
			return $this->_dataBaseError;
		}
	}

	// Rend l'image si trouve, sinon rend l'url
	public function getImageBib($id_bib, $taille = "auto")
	{
		$img = URL_ADMIN_IMG .'bib/bib_'.$id_bib.'.jpg';
		if (file_exists( '..'.$img)) return '<img src="'.$img.'" border="0" style="width:'.$taille.';"/>';
		return 'Pas d\'image';
	}

	public function getComboStatutBib($id_selected)
	{
		$html[]='<select name="statut" id="statut" style="width:100%">';
		$i = 0;
		foreach ($this->statut_bib as $statut)
		{
			if ($id_selected == $i) $selected='selected="selected"';
			else $selected='';
			$html[]='<option value="'.$i.'" '.$selected.'>'.$statut.'</option>';
			$i++;
		}
		$html[]='</select>';
		return implode('',$html);
	}


	// crée un dictionnaire depuis un liste d'enregistrements.
	// la clé est la valeur du champ id_field
	private function rowsToDict(&$rows, $id_field) {
		$dict = array();
		foreach ($rows as $row) {
			$key = $row[$id_field];
			$dict[$key] = $row;
		}
		return $dict;
	}


	// crée un dictionnaire depuis un liste d'enregistrements.
	// la clé est la valeur du champ id_field
	// Chaque clé est associé à un array d'enregistrements correspondant
	private function groupRowsBy(&$rows, $id_field) {
		$dict = array();
		foreach ($rows as $row) {
			$key = $row[$id_field];
			if (!array_key_exists($key, $dict)) $dict[$key] = array();
			$dict[$key] []= $row;
		}
		return $dict;
	}

	private function treeConfig($type) {
		// liste des tables, classes, ... pour chaque type de données
		$configs = array(
								 'rss' => array(
																'cat_class' => 'Class_RssModelCategorie',
																'item_class' => 'Class_RssModelFlux',
																'cat_table' => 'rss_categorie',
																'item_table' => 'rss_flux',
																'cat_id_field' => 'ID_CAT',
																'item_id_field' => 'ID_RSS',
																'cat_parent_id_field' => 'ID_CAT_MERE',
																'require' => 'Rss.php'),
								 'sito' => array(
																'cat_class' => 'Class_SitothequeModelCategorie',
																'item_class' => 'Class_SitothequeModelSite',
																'cat_table' => 'sito_categorie',
																'item_table' => 'sito_url',
																'cat_id_field' => 'ID_CAT',
																'item_id_field' => 'ID_SITO',
																'cat_parent_id_field' => 'ID_CAT_MERE',
																'require' => 'Sitotheque.php')
										 );
		return $configs[$type];
	}


	private function getCategoriesForBibAndTable($id_bib, $cat_table) {
		$cat_where_clause = $id_bib==0 ? '' : ' WHERE ID_SITE='.$id_bib;
		$categories = fetchAll('SELECT	ID_SITE, ID_CAT,ID_CAT_MERE, LIBELLE '.
													 'FROM '.$cat_table.
													 $cat_where_clause.
													 ' ORDER BY LIBELLE');
		if ($categories==false) $categories = array();
		return $categories;
	}

	/*
		Retourne la liste des arborescences bibliothèques / catégories / articles qui ont des articles visibles
		Utilisé pour construire le JSON envoyer au widget jquery TreeSelect.
	 * type = sito | rss
	 * id_bib : l'id de la bib, 0 signifie toutes les bibliothèques
	 * do_load_items: si true, les items (articles, flux ou sites) ne seront chargés (sinon seulement les catégories).
	*/
	public function buildBibTree($id_bib, $type, $do_load_items){
		//LL: TODO quand j'aurai le courage, faire une class de base pour les RSS/Sito/CMS et mettre en
		// place du Strategy pour supprimer toutes les duplications de code

		//va chercher les tables, classes, noms de champs utilisés
		$config = $this->treeConfig($type);
		require_once($config['require']);

		//en mode portail on doit afficher toutes les bibliothèques
		$is_portail = ($id_bib==0);

		$bibs = $this->getBibsBySite($id_bib);
		if ($is_portail) $bibs []= array('ID_SITE' => 0, 'LIBELLE' => 'Portail');
		//on indexe les bib par identifiant pour pouvoir retrouver la bib. de l'item (article, rss, sito) par la suite
		$bibs = $this->rowsToDict($bibs, 'ID_SITE');

		//recherche les catégories puis indexation pour retrouver toutes les catégories d'une bib
		//(Tout ça pour éviter de faire plusieurs requêtes à la base de données)
		$categories = $this->getCategoriesForBibAndTable($id_bib, $config['cat_table']);
		$cat_by_bib = $this->groupRowsBy($categories, 'ID_SITE');
		$categories = $this->rowsToDict($categories, $config['cat_id_field']);


		//Recherche des items par rapport à toutes les catégories trouvées si nécessaire
		if ((count($categories) > 0) and $do_load_items) {
			$where_clause = 'WHERE '.$config['cat_id_field'].' IN('.implode(',', array_keys($categories)).')';
			//si on est sur le CMS, filtre pour ne prendre que les articles avec date de publication valide

			$items = fetchAll('SELECT '.$config['cat_id_field'].', '.$config['item_id_field'].', TITRE'.
												' FROM '.$config['item_table'].' '.
												$where_clause.
												' ORDER BY '.$config['cat_id_field'].', TITRE');
			if ($items==false) $items = array();
			$items_by_cat = $this->groupRowsBy($items, $config['cat_id_field']);
		}

		//on a les bibs, catégories et items, on construit les arbres pour chaque bib
		$bib_trees = array();
		foreach ($bibs as $bib) {
			$builder = new CompositeBuilder($config['cat_class'], $config['item_class']);
			$categories = $cat_by_bib[$bib['ID_SITE']];
			if (!$categories && $is_portail) continue;

			$builder->getRoot()->addCategoriesFromRows($categories, $config['cat_id_field'], $config['cat_parent_id_field']);

			foreach($categories as $cat) {
				$items = $items_by_cat[$cat['ID_CAT']];
				if ($items)
					$builder->getRoot()->addItemsFromRows($items, $config['item_id_field'], $config['cat_id_field']);
			}

			$bib_cat = new ItemCategory($bib["ID_SITE"]);
			$bib_cat->setLabel($bib["LIBELLE"]);
			$bib_cat->addAllCategories($builder->getRoot()->getCategories());
			$bib_trees []= $bib_cat;
		}

		return $bib_trees;
	}



//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	FONCTIONS DE MISES A JOUR
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// Ajouter une Bib
	public function addBib($data)
	{
		$errorMessage = $this->verifData($data);
		if ($errorMessage == '')
		{
			try
			{
				$BibCSite = new BibCSite();
				$BibCSite->insert($data);
				// Recup id pour maj cosmogramme
				$id_bib=fetchOne("select max(ID_SITE) from bib_c_site");
				$this->majCosmogramme($id_bib,$data["VILLE"],$data["VISIBILITE"]);
			}catch (Exception $e)
			{
				logErrorMessage('Class: Class_Bib; Function: addBib' . NL . $e->getMessage());
				$errorMessage = $this->_dataBaseError;
			}
		}
		return $errorMessage;
	}

	// Modifier une bibliotheque
	public function editBib($data, $id_bib)
	{
		if ($errorMessage == '')
		{
			try
			{
				$BibCSite = new BibCSite();
				$where = $BibCSite->getAdapter()->quoteInto('ID_SITE=?', $id_bib);
				$BibCSite->update($data, $where);
				$this->majCosmogramme($id_bib,$data["VILLE"],$data["VISIBILITE"]);
			}catch (Exception $e)
			{
				logErrorMessage('Class: Class_Zone; Function: editBib' . NL . $e->getMessage());
				$errorMessage = $this->_dataBaseError;
			}
		}
		return $errorMessage;
	}


	public function articlesToJSON($include_items = true) {
		$json_categories = array();
		$categories = $this->getArticleCategories();
		foreach($categories as $cat)
			$json_categories []= $cat->toJSON($include_items);

		return  '{'.
			'"id":'.$this->getId().','.
			'"label": "'.htmlspecialchars(trim($this->getLibelle())).'",'.
			'"categories": ['.implode(",", $json_categories).'],'.
			'"items": []}';
	}
}
?>