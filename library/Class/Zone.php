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
//  OPAC 3 -                                                    Table Name 
//
// @TODO@ : A nettoyer
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class BibCZone extends Zend_Db_Table_Abstract
{
	protected $_name = 'bib_c_zone';
}
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  OPAC 3 -                                             Class_Zone -> gestion des territoires 
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class Class_Zone extends Storm_Model_Abstract {
	private $sql;
	private $_dataBaseError = "Problème d'accès à  la base de données";
	private $statut_bib = array('Invisible','N\'envoie pas de données','Envoie des données');

	protected $_table_name = 'bib_c_zone';
	protected $_table_primary = 'id_zone';
	protected $_has_many = array('bibs' => array( 'model' => 'Class_Bib',
																								 'role' => 'zone',
																								 'referenced_in' => 'ID_ZONE',
																								 'order' => 'libelle'));

	protected $_default_attribute_values = array('libelle' => '',
																							  'couleur' => '',
																							  'map_coords' => '',
																							  'image' => '');



	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}

	
	public function __construct()	{
		$this->sql = Zend_Registry::get('sql');
	}


	public function getVisibleBibs() {
		$bibs = $this->getBibs();
		$visible_bibs = array();
		foreach ($bibs as $bib) {
			if ($bib->getVisibilite())
				$visible_bibs []= $bib;
		}
		return $visible_bibs;
	}


	public function setCouleur($couleur) {
		if (substr($couleur,0,1)!="#") 
			$couleur="#".$couleur;
		parent::_set('couleur', $couleur);
		return $this;
	}


	//----------------------------------------------------------------------------------
	// Liste des zones ou 1 si critère de sélection
	//----------------------------------------------------------------------------------
	public function getZones($id_zone=0)
	{
		$where = '';
		if(intval($id_zone)) $where=" where ID_ZONE=$id_zone";
		$data=fetchAll("select * from bib_c_zone ".$where. " order by LIBELLE");
		return $data;
	}
	
	// Lire tous les territoires  == OLD FUNCTION =======
	public function getAllZone()
	{
		try{
			$BibCZone = new BibCZone();
			return $fetch = $BibCZone->fetchAll();
		}catch (Exception $e){
			logErrorMessage('Class: Class_Zone; Function: getAllZone' . NL . $e->getMessage());
			return $this->_dataBaseError;
		}
	}
	
	public function getZoneById($id_zone)
	{
		try{
			$BibCZone = new BibCZone();
			$where = $BibCZone->getAdapter()->quoteInto('ID_ZONE=?', $id_zone);
			return $fetch = $BibCZone->fetchRow($where);
		}catch (Exception $e){
			logErrorMessage('Class: Class_Zone; Function: getZoneById' . NL . $e->getMessage());
			return $this->_dataBaseError;
		}
	}


  //---------------------------------------------------------------------
	// Rend l'image d'une zone sinon blank.gif
	//---------------------------------------------------------------------
	public function getImageZone($image=null, $infos=false)	{
		$img_infos = $this->getImageWithInfos($image);
		if (!$infos) return $img_infos['url'];

		return $img_infos;
	}

	
	public function getImageWithInfos($image = null) {
		if (!$image)	$image = $this->getImage();
		$img = "/userfiles/photobib/".$image;
		$local_path = getcwd().$img;

		if ($image == "" or file_exists($local_path)==false) {
			$img = "/public/admin/images/blank.gif";
			$local_path = getcwd().$img;
		}

		$ret = getimagesize($local_path);
		$ret["url"]=BASE_URL.$img;
		return $ret;
	}
	
	
	// Rend un select avec la liste des zones pour la page d'accueil
	public function getComboZoneAvecUrl($id_selected =0)
	{
		$zone_array = $this->getAllZone();
		if ($id_selected == 0) $select = 'selected="selected"'; else $select = '';
		$url = "location='".BASE_URL."/bib/zoneview/id/'+ this.options[this.selectedIndex].value";
		$html[]='<select name="zone" id="zone" onChange="'.$url.'">';
		$html[]='<option value="0" selected="selected">Liste des territoires</option>';
		foreach ($zone_array as $zone)
			{
				if ($id_selected == $zone->ID_ZONE) $sel = 'selected="selected"'; else $sel = '';
				$html[]='<option value="'.$zone->ID_ZONE.'" '.$sel.'>'.$zone->LIBELLE.'</option>';
			}
		$html[]='</select>';
		return implode('',$html);
	}

	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
	//                                                      méthode ADMIN 
	//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
  //---------------------------------------------------------------------
	// Insert zone
	//---------------------------------------------------------------------
	public function addZone($data)
	{
		$id_zone=sqlInsert("bib_c_zone",$data);
	}
	
  //---------------------------------------------------------------------
	// Update zone
	//---------------------------------------------------------------------
	public function editZone($data, $id_zone)
	{
		// Supprimer l'ancienne image si ella a changé
		$old_img=fetchOne("select IMAGE from bib_c_zone where ID_ZONE=$id_zone");
		if($old_img and $old_img != $data["IMAGE"])
			{
				$adresse_img=getcwd()."/userfiles/photobib/".$old_img;
				if(file_exists($adresse_img)) unlink($adresse_img);
			}
		
		// Ecriture
		sqlUpdate("update bib_c_zone set @SET@ where ID_ZONE=$id_zone",$data);
	}

	
  //---------------------------------------------------------------------
	// supprimer zone
	//---------------------------------------------------------------------
	public function deleteZone($id_zone)
	{
		// Supprimer l'image si il y en a une
		$old_img=fetchOne("select IMAGE from bib_c_zone where ID_ZONE=$id_zone");
		if($old_img and $old_img != $data["IMAGE"])
			{
				$adresse_img=getcwd()."/userfiles/photobib/".$old_img;
				if(file_exists($adresse_img)) unlink($adresse_img);
			}
		sqlExecute("delete from bib_c_zone where ID_ZONE=$id_zone");
	}

	// Rend un select avec la liste des zones
	public function getComboZone($id_selected)
	{
		$zone_array = $this->getAllZone();
		$html[]='<select name="zone" id="zone" style="width:100%">';
		$html[]='<option value="0">** toutes **</option>';
		foreach ($zone_array as $zone)
			{
				if ($id_selected == $zone->ID_ZONE) $selected='selected="selected"';
				else $selected='';
				$html[]='<option value="'.$zone->ID_ZONE.'" '.$selected.'>'.$zone->LIBELLE.'</option>';
			}
		$html[]='</select>';
		return implode('',$html);
	}
	
  //---------------------------------------------------------------------
	// Vérification de saisie
	//---------------------------------------------------------------------
	public function verifData($data)
	{
		$errorMessage = '';
		if ( $data['LIBELLE'] == '')
			{
				$errorMessage = "Vous devez compléter le champ 'Nom'";
			}
		return $errorMessage;
	}


	public function validate() {
		$this->check($this->getLibelle(), "Vous devez compléter le champ 'Nom'");
	}
}

?>