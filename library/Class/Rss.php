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
require_once dirname(__FILE__)."/CompositeBuilder.php";

class bibRssFlux extends Zend_Db_Table_Abstract {
	protected $_name = 'rss_flux';
}

class bibRssCategorie extends Zend_Db_Table_Abstract {
	protected $_name = 'rss_categorie';
}

class Class_RssModelCategorie extends ItemCategory {
	public function getLabel(){
		return $this->libelle;
	}
}

class Class_RssModelFlux extends BaseItem {
	public function getLabel(){
		return $this->titre;
	}
}

class RssLoader extends Storm_Model_Loader {
	/**
	 * @param array $id_feeds
	 * @param array $id_categories
	 * @return array
	 */
	public function getFluxFromIdsAndCategories($id_feeds, $id_categories) {
		$feeds = array();

		foreach ($id_feeds as $id_feed) 	{
			if ($id_feed)
				$feeds[] = $this->find($id_feed);
		}

		$categories = array();
		foreach ($id_categories as $id_cat) {
			if ($categorie = Class_RssCategorie::getLoader()->find($id_cat)) {
				$categories = array_merge($categories, $categorie->getRecursiveSousCategories());
				$categories[] = $categorie;
			}
		}

		$categories = $this->_filterOnId($categories);

		foreach ($categories as $categorie) {
			$feeds = array_merge($feeds, $categorie->getFeeds());
		}

		return $this->_filterOnId(array_filter($feeds));
	}


	/**
	 * @param array $items
	 * @return array
	 */
	protected function _filterOnId($items) {
		$filtered = array();
		$existingIds = array();

		foreach ($items as $item) {
			if (!in_array($item->getId(), $existingIds)) {
				$filtered[] = $item;
				$existingIds[] = $item->getId();
			}
		}

		return $filtered;
	}


	/**
	 * @param int $nb
	 * @return array
	 */
	public function getLastRss($nb) {
		if (!(int)$nb) $nb = 1;
		return $this->findAllBy(array('limit' => $nb,
																	 'order' => 'DATE_MAJ DESC'));
	}

}



class Class_Rss extends Storm_Model_Abstract {
	protected $_loader_class = 'RssLoader';
	protected $_table_name = 'rss_flux';
	protected $_table_primary = 'ID_RSS';
	protected $_belongs_to = array('categorie' => array('model' => 'Class_RssCategorie',
																											'referenced_in' => 'ID_CAT'),
																  'notice' => array('model' => 'Class_Notice',
																									  'referenced_in' => 'ID_NOTICE'));

	protected $_feed_items;

	private $_dataBaseError = "Problème d'accès à la base de données";
	private $_rssCategorie ;
	private $_rssFlux;
	public $arbre_array;
	private $sql;

	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}

	public function __construct() {
		$this->_rssCategorie = new bibRssCategorie();
		$this->_rssFlux = new bibRssFlux();
		$this->sql = Zend_Registry::get('sql');
	}

	/**
	 * @param int $id_flux
	 * @return array
	 */
	public function getFlux($id_flux) {
		if (!$id_flux)
			return false;

		return fetchEnreg('select * from rss_flux where ID_RSS=' . (int)$id_flux);
	}

	/**
	 * @param int $id_categorie
	 * @return array
	 */
	public function getCategorie($id_categorie)
	{
		if (!$id_categorie)
			return false;

		return fetchEnreg('select * from rss_categorie where ID_CAT=' . (int)$id_categorie);
	}

	/**
	 * @param int $id_categorie
	 * @return array
	 */
	public function getSousCategories($id_categorie) {
		if (false == ($data = fetchAll('select ID_CAT from rss_categorie where ID_CAT_MERE=' . (int)$id_categorie)))
			return false;

		foreach($data as $scat) {
			$sous_categories[] = $scat["ID_CAT"];
			$ids = $this->getSousCategories($scat["ID_CAT"]);
			if ($ids) {
				$sous_categories = array_merge($sous_categories, $ids);
			}

		}

		return $sous_categories;
	}

	/**
	 * @param int $id_categorie
	 * @return array
	 */
	public function getFluxCategorie($id_categorie) {
		if (!$id_categorie)
			return array();

		$categories = $this->getSousCategories($id_categorie);
		$categories[] = $id_categorie;

		$fluxs = fetchAll('select * from rss_flux where ID_CAT in (' . implode(',', $categories) . ')');

		if (false == $fluxs)
			$fluxs = array();

		return $fluxs;
	}


	/**
	 * @return Class_Rss
	 */
	public function loadFeedItems() {
			$this->_feed_items = array();

			ZendAfi_Feed::setHttpClient(Zend_Registry::get('httpClient'));
			$feed = ZendAfi_Feed::import($this->getUrl());

			foreach($feed as $item)
				$this->_feed_items []= new Class_RssItem($item);

			return $this;
	}


	/**
	 * @return array
	 */
	public function getFeedItems() {
		if (!isset($this->_feed_items)) {
			$this->loadFeedItems();
		}

		return $this->_feed_items;
	}

	//////////////////////////////////////// OLD ////////////////////////////////

	public function getRssById($id_rss)
	{
		try
			{
				$select = $this->_rssFlux->getAdapter()->select()
					->from('rss_flux',array('ID_CAT'=>'ID_CAT','ID_RSS'=>'ID_RSS', 'TITRE'=>'TITRE','DESCRIPTION' => 'DESCRIPTION', 'URL' => 'URL','TAGS' => 'TAGS',))
					->where('ID_RSS=?', $id_rss);
				$stmt = $select->query();
				$row = $stmt->fetchAll();
				return($row);
			}
		catch (Exception $e)
			{
				logErrorMessage('Class: Class_Rss; Function: getRssById' . NL . $e->getMessage());
				return false;
			}
	}

	/**
	 * @param int $nb
	 * @return array
	 */
	public function getLastRss($nb) {
		try{
			$fetch = Zend_Registry::get('sql')->fetchAll("select * from rss_flux order by DATE_MAJ DESC LIMIT $nb");
			return $fetch;
		}catch (Exception $e){
			logErrorMessage('Class: Class_Rss; Function: getLastRss' . NL . $e->getMessage());
			return $this->_dataBaseError;
		}
	}

	public function getAllCategorieByIdBib($id_bib ="all")
	{
		try
			{
				if($id_bib == "all")
					{
						$select = $this->_rssCategorie->getAdapter()->select()
							->from('rss_categorie',array('ID_CAT'=>'ID_CAT', 'ID_CAT_MERE'=>'ID_CAT_MERE','ID_SITE'=>'ID_SITE','LIBELLE'=>'LIBELLE'))
							->where('ID_SITE >=?', 0);
						$stmt = $select->query();
						$row = $stmt->fetchAll();
						return $row;
					}
				else
					{
						$select = $this->_rssCategorie->getAdapter()->select()
							->from('rss_categorie',array('ID_CAT'=>'ID_CAT', 'ID_CAT_MERE'=>'ID_CAT_MERE','ID_SITE'=>'ID_SITE','LIBELLE'=>'LIBELLE'))
							->where('ID_SITE=?', $id_bib);
						$stmt = $select->query();
						$row = $stmt->fetchAll();
						return $row;
					}
			}catch (Exception $e)
				 {
					 logErrorMessage('Class: Class_Rss; Function: getAllCategorieByIdBib' . NL . $e->getMessage());
					 return $this->_dataBaseError;
				 }
	}

	public function getCategorieByIdCat($id_cat)
	{
		try{
			$where = $this->_rssCategorie->getAdapter()->quoteInto('ID_CAT=?', $id_cat);
			return $fetch = $this->_rssCategorie->fetchRow($where);
		}catch (Exception $e){
			logErrorMessage('Class: Class_Rss; Function: getCategorieByIdCat' . NL . $e->getMessage());
			return $this->_dataBaseError;
		}
	}

	public function getAllSousCategorie($id_cat_mere ="all")
	{
		try{
			if($id_cat_mere == "all") { return $fetch = $this->_cmsCategorie->fetchAll(); }
			else
				{
					$select = $this->_rssCategorie->getAdapter()->select()
						->from('rss_categorie',array('ID_CAT'=>'ID_CAT', 'ID_CAT_MERE'=>'ID_CAT_MERE','LIBELLE'=>'LIBELLE','ID_SITE'=>'ID_SITE'))
						->where('ID_CAT_MERE=?', $id_cat_mere);
					$stmt = $select->query();
					$row = $stmt->fetchAll();
					return $row;
        }
		}catch (Exception $e){
			logErrorMessage('Class: Class_Rss; Function: getAllSousCategorie' . NL . $e->getMessage());
			return $this->_dataBaseError;
		}
	}

	public function getAllFluxByIdCat($id_cat)
	{
		try{
			$select = $this->_rssFlux->getAdapter()->select()
				->from('rss_flux',array('ID_CAT'=>'ID_CAT','ID_RSS'=>'ID_RSS', 'TITRE'=>'TITRE','DESCRIPTION' => 'DESCRIPTION', 'URL' => 'URL','TAGS' => 'TAGS',))
				->where('ID_CAT=?', $id_cat);
			$stmt = $select->query();
			$row = $stmt->fetchAll();
			return($row);
		}catch (Exception $e){
			logErrorMessage('Class: Class_Rss; Function: getAllFluxByIdCat' . NL . $e->getMessage());
			return $this->_dataBaseError;
		}
	}


	/**
	 * @param array $id_feeds
	 * @param array $id_categories
	 * @return array
	 */
	public function getFluxFromIdsAndCategories($id_feeds, $id_categories) {
		$feeds = array();

		foreach($id_feeds as $id_feed) 	{
			if ($id_feed)
				$feeds []= $this->getFlux($id_feed);

		}

		foreach($id_categories as $id_cat) {
			$feeds_in_cat = $this->getFluxCategorie($id_cat);
			$feeds = array_merge($feeds, $feeds_in_cat);
		}

		return $feeds;
	}



	// pour le deploy arbre
	public function getIdCatMereByIdCat($id_cat_fille)
	{
		$select = $this->_rssCategorie->getAdapter()->select()
			->from('rss_categorie',array('ID_CAT'=>'ID_CAT', 'ID_CAT_MERE'=>'ID_CAT_MERE'))
			->where('ID_CAT=?', $id_cat_fille);
		$stmt = $select->query();
		$row = $stmt->fetchAll();
		return ($row[0]["ID_CAT_MERE"]);
	}

	public function rendComboCategorie($id_bib,$id_cat)
	{
		$html[] = '<select name="id_cat">';
		$cat_array = $this->getAllCategorieByIdBib($id_bib);
		foreach($cat_array as $cat)
			{
				if ($id_cat == $cat["ID_CAT"]) $sel ='selected="selected"'; else $sel ='';
				$html[] = '<option value="'.$cat["ID_CAT"].'" '.$sel.'>'.$cat["LIBELLE"].'</option>';
			}
		$html[] = '</select>';
		return (implode('',$html));
	}
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//                                                     Méthode admin
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// ajoute
	public function addCategorie($data)
	{
    if ( $this->_rssCategorie == null ){return $this->_dataBaseError;}
    $errorMessage = $this->verifCategorieData($data);
    if ($errorMessage == ''){
			try{
				$this->_rssCategorie->insert($data);
			}catch (Exception $e){
				logErrorMessage('Class: Class_Rss; Function: addCategorie' . NL . $e->getMessage());
				$errorMessage = $this->_dataBaseError;
			}
		}
		return $errorMessage;
	}
	// Edit une cat
	public function editCategorie($data, $id_cat)
	{
    $errorMessage = $this->verifCategorieData($data);
    if ($errorMessage == '')
			{
        try{
					$where = $this->_rssCategorie->getAdapter()->quoteInto('ID_CAT=?', $id_cat);
					$this->_rssCategorie->update($data, $where);
				}catch (Exception $e){
					logErrorMessage('Class: Clas_Rss; Function: editCategorie' . NL . $e->getMessage());
					$errorMessage = $this->_dataBaseError;
				}
			}
		return $errorMessage;
	}

	// Supprimer une cat
	public function delCategorie($id_cat)
	{
		$errorMessage = '';
		$cat_avec_news = $this->getAllFluxByIdCat($id_cat); if(count($cat_avec_news) > 0) $errorMessage = "Suppression non autorisée : cette catégorie contient encore des fils Rss";
		$cat_avec_subcat = $this->getAllSousCategorie($id_cat); if(count($cat_avec_subcat) > 0) $errorMessage = "Vous ne pouvez pas supprimer une catégorie contenant des sous catégories";

		if ($errorMessage=='')
			{
				try{
					$where = $this->_rssCategorie->getAdapter()->quoteInto('ID_CAT=?', $id_cat);
					$this->_rssCategorie->delete($where);
				}catch (Exception $e){
					logErrorMessage('Class: Clas_Rss; Function: delCategorie' . NL . $e->getMessage());
					$errorMessage = $this->_dataBaseError;
				}
			}
		return $errorMessage;
	}
	// on verif les cat
	public function verifCategorieData($data)
	{
		$errorMessage = '';
		if ( $data['LIBELLE'] == '' ){
			$errorMessage = "Vous devez compléter le champ 'Nom de la catégorie'";
		}elseif (  strlen_utf8($data['libelle']) > 50 ){
			$errorMessage = "Le champ 'Nom de la catégorie' doit être inférieur à 50 caractères";
		}
		return $errorMessage;
	}
	///////////////////////////////////////////////
	//                 RSS
	///////////////////////////////////////////////

	// Ajoute flux
	public function addRss($data)
	{
		$errorMessage = $this->verifRssData($data);
		if ($errorMessage == '')
			{
				try
					{
						$this->_rssFlux->insert($data);
					}
				catch (Exception $e)
					{
						logErrorMessage('Class: Class_Rss; Function: addRss' . NL . $e->getMessage());
						$errorMessage = $this->_dataBaseError;
					}
			}
		return $errorMessage;
	}
	// Edit flux
	public function editRss($data, $id_rss)
	{
		if ( $this->_rssFlux == null ){return $this->_dataBaseError;}
		$errorMessage = $this->verifRssData($data);
		if($errorMessage == "")
			{
				try
					{
						$where = $this->_rssFlux->getAdapter()->quoteInto('ID_RSS=?', $id_rss);
						$this->_rssFlux->update($data, $where);
					}
				catch (Exception $e)
					{
						logErrorMessage('Class: Class_Rss; Function: editRss' . NL . $e->getMessage());
						$errorMessage = $this->_dataBaseError;
					}
				return($errorMessage);
			}
		else return ($errorMessage);
	}

	// Supprime un flux
	public function deleteRss($id_rss)
	{
		if ( $this->_rssFlux == null ){return $this->_dataBaseError;}
		$errorMessage = '';
		try
			{
				$where = $this->_rssFlux->getAdapter()->quoteInto('ID_RSS=?', $id_rss);
				$this->_rssFlux->delete($where);
				// Supprimer la notice associée si necessaire
				$id_notice=fetchOne("select ID_NOTICE from rss_flux where ID_RSS=$id_rss");
				if($id_notice)
					{
						sqlExecute("delete from notices where id_notice=$id_notice");
						sqlExecute("delete from exemplaires where id_notice=$id_notice");
					}
			}
		catch (Exception $e)
			{
				logErrorMessage('Class: Class_Rss; Function: deleteRss' . NL . $e->getMessage());
				$errorMessage = $this->_dataBaseError;
			}
		return $errorMessage;
	}


	// verif les data
	public function verifRssData($data)
	{
		$errorMessage = '';
		if ( $data['TITRE'] == '' ){$errorMessage = "Vous devez compléter le champ 'Titre'";}
		elseif ( $data['URL'] == '' ){$errorMessage = "Vous devez compléter le champ 'Url'";}
		elseif (  strlen_utf8($data['URL']) > 250 ){$errorMessage = "Le champ 'Url' doit être inférieur à 250 caractères";}
		else{
			// verify that the RSS URL is valid
			$httpClient = Zend_Registry::get('httpClient');
			try{
				Zend_Feed::setHttpClient($httpClient);
				$link = $data['URL'];
				ZendAfi_Feed::import($link);
			}catch(Exception $e){
				$errorMessage = "Il y a un problème avec l'adresse du flux RSS";
			}
		}
		return $errorMessage;
	}
	/* créaion d'un flux
		 $data_array = 'titre',
		 'description',
		 'lien',
		 'items' = array('titre','lien','desc')
	*/
	public function createFluxRss($data_array)	{
		$flux='<?xml version="1.0" ?>
        <rss version="2.0">
          <channel>
            <title>'.$data_array["titre"].'</title>
            <link>'.$data_array["lien"].'</link>
            <description>'.$data_array["description"].'</description>
            <image>
                <url>http://'.$_SERVER['SERVER_NAME'].URL_IMG.'site/logo.jpg</url>
                <link>'.$data_array["lien"].'</link>
                <title>'.$data_array["titre"].'</title>
            </image>';

		foreach($data_array["items"] as $item)
			{
				$flux.='<item>
                   <title>'.$item["titre"].'</title>
                   <link>'.$item["lien"].'</link>
                   <description>'.$item["desc"].'</description>
                </item>';
			}

		$flux.='</channel></rss>';
		return($flux);
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// 								    ARBRE pour lister les cat+ subcat sur lindex admin
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// recursif !
	public function rendArbreArray($id_bib, $niveau , $id_cat_mere )
	{
		// Lis les cat
		$req = "Select * from rss_categorie where ID_CAT_MERE = $id_cat_mere and ID_SITE=$id_bib";
		$res = $this->sql->fetchAll($req);
		if (!$res) {return false;}
		$niveau++;

		foreach($res as $cat)
			{
				$cat["NIVEAU"] = $niveau;
				$this->arbre_array[] = $cat;
				$this->rendArbreArray($id_bib, $niveau,$cat["ID_CAT"]);
				// articles
				$flux_array = $this->getAllFluxByIdCat($cat["ID_CAT"]);
				if (count($flux_array) > 0)
					{
						foreach($flux_array as $flux)
							{
								$flux["NIVEAU"]=($niveau+1);
								$this->arbre_array[]= $flux;
							}
					}
			}
	}
	// methode a apeller de l'exterieur pour créer l'array
	public function rendArray($id_bib = 0, $niveau = -1, $id_cat_mere = 0)
	{
		if($id_bib==null) return false;
		if($id_bib=="PORTAIL") $id_bib=0;
		$this->rendArbreArray($id_bib, $niveau , $id_cat_mere);
		return ($this->arbre_array);
	}
	// methode a apeller de l'exterieur pour rendre le html, une fois l'array créer
	public function rendHTML()
	{
		$niveau=0; $id=0;
		$html= '<ul class="treeMenu">';
		if(count($this->arbre_array) > 0){
			foreach($this->arbre_array as $menu)
				{
					if ($niveau != $menu["NIVEAU"])
						{
							$id++;
							if(!$menu["ID_CAT_MERE"]) $test = $menu["ID_CAT"]; else $test = $menu["ID_CAT_MERE"];
							if ($niveau < $menu["NIVEAU"] ) $html.='</li><li class="sousTreeMenu" id="sm_'.$test.'" style="display:none;"><ul class="sousTreeMenu">';
							else $html.= str_repeat('</ul></li>',($niveau - $menu["NIVEAU"] ));
							$niveau = $menu["NIVEAU"];
						}
					$cat_avec_rss = $this->getAllFluxByIdCat($menu["ID_CAT"]);
					$cat_avec_subcat = $this->getAllSousCategorie($menu["ID_CAT"]);

					if(count($cat_avec_rss) > 0 || count($cat_avec_subcat) > 0) {$ico_del='<a href="#" onclick="alert(\'Suppression non autorisée : cette catégorie contient des fils Rss.\')"><img src="'.URL_ADMIN_IMG.'ico/del.gif" class="ico" alt="Supprimer" title="Supprimer" /></a>';}
					else {$ico_del='<a href="'.BASE_URL.'/admin/rss/catdel/id/'.$menu["ID_CAT"].'" onclick="javascript:if(!confirm(\'Êtes vous sûr de vouloir supprimer catte catégorie ?\')) return false;"><img src="'.URL_ADMIN_IMG.'ico/del.gif" class="ico" alt="Supprimer" title="Supprimer"/></a>';}

					$ico_add_cat='<a href="'.BASE_URL.'/admin/rss/catadd/id/'.$menu["ID_CAT"].'"><img src="'.URL_ADMIN_IMG.'ico/add_cat.gif" class="ico" alt="Ajouter une sous-catégorie"  title="Ajouter une sous-catégorie" style="width:22px;"/></a>';
					$ico_add_news='<a href="'.BASE_URL.'/admin/rss/rssadd/id/'.$menu["ID_CAT"].'"><img src="'.URL_ADMIN_IMG.'ico/add_news.gif" class="ico" title="Ajouter un fil Rss" alt="Ajouter un fil Rss" /></a>';
					$ico_edit='<a href="'.BASE_URL.'/admin/rss/catedit/id/'.$menu["ID_CAT"].'"><img src="'.URL_ADMIN_IMG.'ico/edit.gif" class="ico" title="Modifier" alt="Modifier" /></a>';
					$ico_edit_news='<a href="'.BASE_URL.'/admin/rss/rssedit/id/'.$menu["ID_RSS"].'"><img src="'.URL_ADMIN_IMG.'ico/edit.gif" class="ico" title="Modifier" alt="Modifier"/></a>';
					$ico_del_news='<a href="'.BASE_URL.'/admin/rss/rssdel/id/'.$menu["ID_RSS"].'" onclick="javascript:if(!confirm(\'Êtes vous sûr de vouloir supprimer ce fil Rss ?\')) return false;"><img src="'.URL_ADMIN_IMG.'ico/del.gif" class="ico" alt="Supprimer" title="Supprimer" /></a>';
					$ico_cat='<img src="'.URL_ADMIN_IMG.'ico/cat.gif" class="ico" alt="Afficher les sous catégories" style="width:19px; height:12px;"/>';
					$ico_news='<img src="'.URL_ADMIN_IMG.'ico/liste.gif" class="ico" alt="Afficher le flux" title="Afficher le flux" align="bottom"/>';

					if(strlen($menu["LIBELLE"]) >= 50){$libelle_cat = substr($menu["LIBELLE"],0,50).'...'; } else $libelle_cat =$menu["LIBELLE"] ;
					if(strlen($menu["TITRE"]) >= 50){$libelle_rss = substr($menu["TITRE"],0,50).'...'; } else $libelle_rss =$menu["TITRE"] ;

					$nb_items = count($cat_avec_rss) + count($cat_avec_subcat);

					$html.= '<li style="width:100%; height:25px;padding-top:10px;border-bottom:1px solid #DEE4E8;" id="m_'.$menu["ID_CAT"].'"><div style="float:left;cursor:pointer;" onclick="show(\'sm_'.$menu["ID_CAT"].'\');">';
					if ($menu["ID_RSS"])
						$html.='<a id="rss_a_'.$menu["ID_RSS"].'" href="#" onclick="requestRss(\''.$menu["ID_RSS"].'\');show(\'sm_'.$menu["ID_CAT"].'\');">'.$ico_news.'&nbsp;'.$libelle_rss. '</a></div> <div style="height:20px;" align="right"> '.$ico_edit_news.$ico_del_news.'</div>';
					else
						$html.=$ico_cat.'&nbsp;'.$libelle_cat. ' ('.$nb_items.')</div> <div style="height:20px;" align="right">'.$ico_add_cat.$ico_add_news.$ico_edit.$ico_del.'</div>';
					$html.='</li>';
				}

			$html.= "</li></ul>";
			if($html=='<ul class="treeMenu"></li></ul>') $html="";
			return ($html);
		}
	}
}

?>