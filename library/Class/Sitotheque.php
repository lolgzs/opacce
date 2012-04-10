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

class SitoCategorie extends Zend_Db_Table_Abstract {
	protected $_name = 'sito_categorie';
}

class SitoUrl extends Zend_Db_Table_Abstract {
	protected $_name = 'sito_url';
}

class Class_SitothequeModelCategorie extends ItemCategory {
	public function getLabel(){
		return $this->libelle;
	}
}

class Class_SitothequeModelSite extends BaseItem {
	public function getLabel(){
		return $this->titre;
	}
}




class SitothequeLoader extends Storm_Model_Loader {
	/**
	 * @param array $id_sites
	 * @param array $id_categories
	 * @return array
	 */
	public function getSitesFromIdsAndCategories($id_sites, $id_categories) {
		$sites = array();
		$feeds = array();

		foreach ($id_sites as $id_site) 	{
			if ($id_site)
				$feeds[] = $this->find($id_site);
		}

		$categories = array();
		foreach ($id_categories as $id_cat) {
			if ($categorie = Class_SitothequeCategorie::getLoader()->find($id_cat)) {
				$categories = array_merge($categories, $categorie->getRecursiveSousCategories());
				$categories[] = $categorie;
			}
		}

		$categories = $this->_filterOnId($categories);

		foreach ($categories as $categorie) {
			$feeds = array_merge($feeds, $categorie->getSitotheques());
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
}




class Class_Sitotheque extends Storm_Model_Abstract {
	protected $_loader_class = 'SitothequeLoader';
	protected $_table_name = 'sito_url';
	protected $_table_primary = 'ID_SITO';
	protected $_belongs_to = array('categorie' => array('model' => 'Class_SitothequeCategorie',
																											'referenced_in' => 'id_cat'),
																 'notice' => array('model' => 'Class_Notice',
																									 'referenced_in' => 'id_notice'));


	private $_dataBaseError = "Problème d'accès à la base de données";
	private $_sitoCategorie;
	private $_sitoUrl;
	private $sql;
	public $arbre_array;


	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}

	public function __construct()
	{
		try{
			$this->_sitoCategorie = new SitoCategorie();
			$this->_sitoUrl = new SitoUrl();
			$this->sql = Zend_Registry::get('sql');
		}catch (Exception $e){
			logErrorMessage('Class: Class_Sitotheque; Function: __construct' . NL . $e->getMessage());
		}
	}


	//------------------------------------------------------------------------------------------------------
	// Get site
	//------------------------------------------------------------------------------------------------------
	public function getSite($id_site)
	{
		if(!$id_site) return false;
		$site=fetchEnreg("select * from sito_url where ID_SITO=$id_site");
		return $site;
	}

	//------------------------------------------------------------------------------------------------------
	// Get catégorie
	//------------------------------------------------------------------------------------------------------
	public function getCategorie($id_categorie)
	{
		if(!$id_categorie) return false;
		$categorie=fetchEnreg("select * from sito_categorie where ID_CAT=$id_categorie");
		return $categorie;
	}


	public function getCategorieLibelle() {
		if ($categorie = parent::_get('categorie'))
			return $categorie->getLibelle();
		return '';
	}

	//------------------------------------------------------------------------------------------------------
	// Renvoie recursivement toutes les sous-categories d'une categorie
	//------------------------------------------------------------------------------------------------------
	public function getSousCategories($id_categorie)
	{
		$data=fetchAll("select ID_CAT from sito_categorie where ID_CAT_MERE=$id_categorie");
		if(!$data) return false;
		foreach($data as $scat)
			{
				$sous_categories[]=$scat["ID_CAT"];
				$ids=$this->getSousCategories($scat["ID_CAT"]);
				if($ids) $sous_categories=array_merge($sous_categories, $ids);
			}
		return $sous_categories;
	}

	//------------------------------------------------------------------------------------------------------
	// Renvoie tous les articles pour une categorie et ses sous-categories
	//------------------------------------------------------------------------------------------------------
	public function getSitesCategorie($id_categorie)
	{
		if(!$id_categorie) return array();
		$categories=$this->getSousCategories($id_categorie);
		$categories[]=$id_categorie;
		$inSql = '';
		foreach($categories as $categorie)
			{
				if($inSql) $inSql.=",";
				$inSql.=$categorie;
			}

		$fetch_result=fetchAll("select * from sito_url where ID_CAT in(".$inSql.")");
		if (is_array($fetch_result))
				return $fetch_result;
		else
				return array();
	}



	//////////////////////////////////////// OLD ////////////////////////////////

	// par id bib
	public function getAllCategorie($id_bib = "all")
	{
		try{
			if($id_bib == "all")
				{
					$select = $this->_sitoCategorie->getAdapter()->select()
						->from('sito_categorie',array('ID_CAT'=>'ID_CAT', 'ID_CAT_MERE'=>'ID_CAT_MERE','ID_SITE'=>'ID_SITE','LIBELLE'=>'LIBELLE'))
						->where('ID_SITE >=?', 0);
					$stmt = $select->query();
					$row = $stmt->fetchAll();
					return $row;
				}
			else
				{
					$select = $this->_sitoCategorie->getAdapter()->select()
						->from('sito_categorie',array('ID_CAT'=>'ID_CAT', 'ID_CAT_MERE'=>'ID_CAT_MERE','ID_SITE'=>'ID_SITE','LIBELLE'=>'LIBELLE'))
						->where('ID_SITE=?', $id_bib);
					$stmt = $select->query();
					$row = $stmt->fetchAll();
					return $row;
				}
		}catch (Exception $e){
			logErrorMessage('Class: Class_Sitotheque; Function: getAllCategorie' . NL . $e->getMessage());
			return $this->_dataBaseError;
		}
	}

	public function getCategorieByIdCat($id_cat)
	{
		try{
			$where = $this->_sitoCategorie->getAdapter()->quoteInto('ID_CAT=?', $id_cat);
			return $fetch = $this->_sitoCategorie->fetchRow($where);
		}catch (Exception $e){
			logErrorMessage('Class: Class_Sitotheque; Function: getCategorieByIdCat' . NL . $e->getMessage());
			return $this->_dataBaseError;
		}
	}

	public function getAllSousCategorie($id_cat_mere = "all")
	{
		try{
			if($id_cat_mere == "all") { return $fetch = $this->_sitoCategorie->fetchAll(); }
			else
				{
					$select = $this->_sitoCategorie->getAdapter()->select()
						->from('sito_categorie',array('ID_CAT'=>'ID_CAT', 'ID_CAT_MERE'=>'ID_CAT_MERE','LIBELLE'=>'LIBELLE','ID_SITE'=>'ID_SITE'))
						->where('ID_CAT_MERE=?', $id_cat_mere);
					$stmt = $select->query();
					$row = $stmt->fetchAll();
					return $row;
				}
		}catch (Exception $e){
			logErrorMessage('Class: Class_Sitotheque; Function: getAllSousCategorie' . NL . $e->getMessage());
			return $this->_dataBaseError;
		}
	}

	public function getAllSitoByIdCat($id_cat)
	{
		try{
			$select = $this->_sitoUrl->getAdapter()->select()
				->from('sito_url',array('ID_CAT'=>'ID_CAT','ID_SITO'=>'ID_SITO', 'TITRE'=>'TITRE', 'DESCRIPTION' => 'DESCRIPTION','URL' => 'URL',))
				->where('ID_CAT=?', $id_cat);
			$stmt = $select->query();
			$row = $stmt->fetchAll();
			return $row;
		}catch (Exception $e){
			logErrorMessage('Class: Class_Sitotheque; Function: getAllSitoByIdCat' . NL . $e->getMessage());
			return $this->_dataBaseError;
		}
	}

	public function getSitoById($id_sito)
	{
		if ( $this->_sitoUrl == null ){return $this->_dataBaseError;}
		try{
			$where = $this->_sitoUrl->getAdapter()->quoteInto('ID_SITO=?', $id_sito);
			return $fetch = $this->_sitoUrl->fetchRow($where);
		}catch (Exception $e){
			logErrorMessage('Class: Class_Sitotheque; Function: getSitoById' . NL . $e->getMessage());
			return $this->_dataBaseError;
		}
	}

	public function getLastSito($nb)  {
		$sql = Zend_Registry::get('sql');
		try{
			$fetch = $sql->fetchAll("select * from sito_url order by DATE_MAJ DESC LIMIT $nb");
			return $fetch;
		}catch (Exception $e){
			logErrorMessage('Class: Class_CMS; Function: getLastArticles' . NL . $e->getMessage());
			return $this->_dataBaseError;
		}
	}

	// pour le deploy arbre
	public function getIdCatMereByIdCat($id_cat_fille)
	{
		$select = $this->_sitoCategorie->getAdapter()->select()
			->from('sito_categorie',array('ID_CAT'=>'ID_CAT', 'ID_CAT_MERE'=>'ID_CAT_MERE'))
			->where('ID_CAT=?', $id_cat_fille);
		$stmt = $select->query();
		$row = $stmt->fetchAll();
		return ($row[0]["ID_CAT_MERE"]);
	}

	public function rendComboCategorie($id_bib,$id_cat)
	{
		$html[] = '<select name="id_cat">';
		$cat_array = $this->getAllCategorie($id_bib);
		foreach($cat_array as $cat)
			{
				if ($id_cat == $cat["ID_CAT"]) $sel ='selected="selected"'; else $sel ='';
				$html[] = '<option value="'.$cat["ID_CAT"].'" '.$sel.'>'.$cat["LIBELLE"].'</option>';
			}
		$html[] = '</select>';
		return (implode('',$html));
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//                                                           Méthode Admin
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

	// ajoute
	public function addCategorie($data)
	{
		if ( $this->_sitoCategorie == null ){return $this->_dataBaseError;}
		$errorMessage = $this->verifCategorieData($data);
		if ($errorMessage == ''){
			try{
				$this->_sitoCategorie->insert($data);
			}catch (Exception $e){
				logErrorMessage('Class: Class_Sitotheque; Function: addCategorie' . NL . $e->getMessage());
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
					$where = $this->_sitoCategorie->getAdapter()->quoteInto('ID_CAT=?', $id_cat);
					$this->_sitoCategorie->update($data, $where);
				}catch (Exception $e){
					logErrorMessage('Class: Class_Sitotheque; Function: editCategorie' . NL . $e->getMessage());
					$errorMessage = $this->_dataBaseError;
				}
			}
		return $errorMessage;
	}

	// Supprimer une cat
	public function deleteCategorie($id_cat)
	{
		$errorMessage = '';
		$cat_avec_news = $this->getAllSitoByIdCat($id_cat); if(count($cat_avec_news) > 0) $errorMessage = "Vous ne pouvez pas supprimer une catégorie contenant des articles";
		$cat_avec_subcat = $this->getAllSousCategorie($id_cat); if(count($cat_avec_subcat) > 0) $errorMessage = "Vous ne pouvez pas supprimer une catégorie contenant des sous catégories";

		if ($errorMessage=='')
			{
				try{
					$where = $this->_sitoCategorie->getAdapter()->quoteInto('ID_CAT=?', $id_cat);
					$this->_sitoCategorie->delete($where);
				}catch (Exception $e){
					logErrorMessage('Class: Class_Sitotheque; Function: deleteCategorie' . NL . $e->getMessage());
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
			$errorMessage = "Le champ 'Nom de la catégorie' doit Ãªtre inférieur Ã  50 caractères";
		}
		return $errorMessage;
	}
	///////////////////////////////////////////////
	//                 Sito
	///////////////////////////////////////////////

	// Ajoute Sito
	public function addSito($data)
	{
		$errorMessage = $this->verifSitoData($data);
		if ($errorMessage == ''){
			try{
				$this->_sitoUrl->insert($data);
			}catch (Exception $e){
				logErrorMessage('Class: Class_Sitotheque; Function: addSito' . NL . $e->getMessage());
				$errorMessage = $this->_dataBaseError;
			}
		}
		return $errorMessage;
	}
	// Edit Sito
	public function editSito($data, $id_sito)
	{
		if ( $this->_sitoUrl == null ){return $this->_dataBaseError;}
		$errorMessage = $this->verifSitoData($data);

		if ($errorMessage == ''){
			try{
				$where = $this->_sitoUrl->getAdapter()->quoteInto('ID_SITO=?', $id_sito);
				$this->_sitoUrl->update($data, $where);
			}catch (Exception $e){
				logErrorMessage('Class: Class_Sitotheque; Function: editSito' . NL . $e->getMessage());
				$errorMessage = $this->_dataBaseError;
			}
		}
		return $errorMessage;
	}

	// Supprime Sito
	public function deleteSito($id_sito)
	{
		$errorMessage = '';
		try{
			$where = $this->_sitoUrl->getAdapter()->quoteInto('ID_SITO=?', $id_sito);
			$this->_sitoUrl->delete($where);
			// Supprimer la notice associée si necessaire
			$id_notice=fetchOne("select ID_NOTICE from sito_url where ID_SITO=$id_sito");
			if($id_notice)
				{
					sqlExecute("delete from notices where id_notice=$id_notice");
					sqlExecute("delete from exemplaires where id_notice=$id_notice");
				}
		}catch (Exception $e){
			logErrorMessage('Class: Class_Sitotheque; Function: delSito' . NL . $e->getMessage());
			$errorMessage = $this->_dataBaseError;
		}
		return $errorMessage;
	}

	public function verifSitoData($data)
	{
		$errorMessage = '';

		if ( $data['TITRE'] == '' ){$errorMessage = "Vous devez compléter le champ 'Titre'";}
		elseif (  strlen_utf8($data['TITRE']) > 100 ){$errorMessage = "Le champ 'Titre' doit être inférieur à 100 caractères";}
		elseif ( $data['URL'] == '' ){$errorMessage = "Vous devez compléter le champ 'Url'";}
		elseif (  strlen_utf8($data['URL']) > 250 ){$errorMessage = "Le champ 'Url' doit être inférieur à 250 caractères";}
		elseif (  strlen_utf8($data['DESCRIPTION']) > 250 ){$errorMessage = "Le champ 'Commentaire' doit être inférieur à 250 caractères";}
		else{
			try{
				$httpClient = Zend_Registry::get('httpClient');
				$httpClient->setUri($data['URL']);
				$response = $httpClient->request();
				if ($response->isError()){
					$errorMessage = "Il y a un problème avec l'URL";
				}
			}catch (Exception $e){
				$errorMessage = "Il y a un problème avec l'URL";
			}
		}
		return $errorMessage;
	}

	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// 								    ARBRE pour lister les cat+ subcat sur lindex admin
	////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	// recursive ! -> Renvoie un array pour créer un treemenu
	public function rendArbreArray($id_bib, $niveau , $id_cat_mere )
	{
		// Lis les cat
		$req = "Select * from sito_categorie where ID_CAT_MERE = $id_cat_mere and ID_SITE=$id_bib";
		$res = $this->sql->fetchAll($req);
		if (!$res) {return false;}
		$niveau++;

		foreach($res as $cat)
			{
				$cat["NIVEAU"] = $niveau;
				$this->arbre_array[] = $cat;
				$this->rendArbreArray($id_bib, $niveau,$cat["ID_CAT"]);
				// articles
				$sito_array=$this->getAllSitoByIdCat($cat["ID_CAT"]);
				foreach($sito_array as $sito)
					{
						$sito["NIVEAU"]=($niveau+1);
						$this->arbre_array[]=$sito;
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


	//Retourne tous les sites dont les ids et catégories (array) sont donnés
	public function getSitesFromIdsAndCategories($id_sites, $id_categories)
	{
		$sites=array();

		foreach($id_sites as $id_site)
			{
				if($id_site) $sites[]=$this->getSite($id_site);
			}

		foreach($id_categories as $id_cat)
			{
				$sites_in_cat = $this->getSitesCategorie($id_cat);
				$sites = array_merge($sites, $sites_in_cat);
			}
		return $sites;
	}


	// methode a apeller de l'exterieur pour rendre le html, une fois l'array créer
	public function rendHTML()
	{
		$niveau=0; $id=0;
		$html= '<ul class="treeMenu">';
		if(count($this->arbre_array) > 0)
			{
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
            $cat_avec_sito = $this->getAllSitoByIdCat($menu["ID_CAT"]);
            $cat_avec_subcat = $this->getAllSousCategorie($menu["ID_CAT"]);

            if(count($cat_avec_sito) > 0 || count($cat_avec_subcat) > 0) {$ico_del='<a href="#" onclick="alert(\'Suppression non autorisée : cette catégorie contient encore des sous-catégories ou des sites\')"><img src="'.URL_ADMIN_IMG.'ico/del.gif" class="ico" alt="Supprimer" title="Supprimer"/></a>';}
            else {$ico_del='<a href="'.BASE_URL.'/admin/sito/catdel/id/'.$menu["ID_CAT"].'" onclick="javascript:if(!confirm(\'Êtes vous sûr vouloir supprimer cette catégorie ?\')) return false;"><img src="'.URL_ADMIN_IMG.'ico/del.gif" class="ico" alt="Supprimer" title="Supprimer"/></a>';}


            $ico_add_cat='<a href="'.BASE_URL.'/admin/sito/catadd/id/'.$menu["ID_CAT"].'"><img src="'.URL_ADMIN_IMG.'ico/add_cat.gif" class="ico" alt="Ajouter une sous-catégorie"  style="width:22px;" title="Ajouter une sous-catégorie"/></a>';
            $ico_add_news='<a href="'.BASE_URL.'/admin/sito/sitoadd/id/'.$menu["ID_CAT"].'"><img src="'.URL_ADMIN_IMG.'ico/add_news.gif" class="ico" title="Ajouter un site"/></a>';
            $ico_edit='<a href="'.BASE_URL.'/admin/sito/catedit/id/'.$menu["ID_CAT"].'"><img src="'.URL_ADMIN_IMG.'ico/edit.gif" class="ico" alt="Modifier" title="Modifier"/></a>';

            $ico_edit_news='<a href="'.BASE_URL.'/admin/sito/sitoedit/id/'.$menu["ID_SITO"].'"><img src="'.URL_ADMIN_IMG.'ico/edit.gif" class="ico" alt="Modifier" title="Modifier"/></a>';
            $ico_del_news='<a href="'.BASE_URL.'/admin/sito/sitodel/id/'.$menu["ID_SITO"].'" onclick="javascript:if(!confirm(\'Êtes vous sûr que de vouloir supprimer ce site ?\')) return false;"><img src="'.URL_ADMIN_IMG.'ico/del.gif" class="ico" alt="Supprimer" title="Supprimer"/></a>';
            $ico_cat='<img src="'.URL_ADMIN_IMG.'ico/cat.gif" class="ico" alt="Afficher les sous catégories" style="width:19px; height:12px;"/>';
            $ico_news='<img src="'.URL_ADMIN_IMG.'ico/liste.gif" class="ico" alt="Afficher le lien" title="Afficher le lien" align="bottom"/>';

            if(strlen($menu["LIBELLE"]) >= 50){$libelle_cat = substr($menu["LIBELLE"],0,50).'...'; } else $libelle_cat =$menu["LIBELLE"] ;
            if(strlen($menu["TITRE"]) >= 50){$libelle_sito = substr($menu["TITRE"],0,50).'...'; } else $libelle_sito =$menu["TITRE"] ;

            $nb_items = count($cat_avec_sito) + count($cat_avec_subcat);

            $html.= '<li style="width:100%; height:25px;padding-top:10px;border-bottom:1px solid #DEE4E8;" id="m_'.$menu["ID_CAT"].'"><div style="float:left;cursor:pointer;" onclick="show(\'sm_'.$menu["ID_CAT"].'\');">';
            if($menu["ID_SITO"]) $html.='<a href="'.BASE_URL.'/admin/sito/viewsito?id='.$menu["ID_SITO"].'" onclick="show(\'sm_'.$menu["ID_CAT"].'\');">'.$ico_news.'&nbsp;'.$libelle_sito. '</a></div> <div style="height:20px;" align="right"> '.$ico_edit_news.$ico_del_news.'</div>';
            else $html.=$ico_cat.'&nbsp;'.$libelle_cat. ' ('.$nb_items.')</div> <div style="height:20px;" align="right">'.$ico_add_cat.$ico_add_news.$ico_edit.$ico_del.'</div>';
					}
        $html.= "</li></ul>";
        if($html=='<ul class="treeMenu"></li></ul>') $html="";
        return ($html);
			}
	}
}
?>