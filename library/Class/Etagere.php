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
// OPAC3 - Etageres de notices
////////////////////////////////////////////////////////////////////////////////

class Class_Etagere
{
	private $path_flash;							// Path pour le modèle de settings
	private $path_etagere;								// Path pour settings affichage

	//-------------------------------------------------------------------------------
	// Constructeur
	//-------------------------------------------------------------------------------
	function  __construct()
	{
		$this->path_flash=PATH_FLASH."image_submenu/";
		$this->path_etagere=USERFILESPATH."/etageres/";
	}

	//-------------------------------------------------------------------------------
	// Liste des étagères (structure complete)
	//-------------------------------------------------------------------------------
	public function getEtagere($id_etagere,$id_mere)
	{
		if($id_etagere) return fetchEnreg("select * from etageres where id_etagere=$id_etagere");
		else if($id_mere >='0') return fetchAll("select * from etageres where id_mere=$id_mere order by libelle");
		else return fetchAll("select * from etageres order by libelle");
	}

	//-------------------------------------------------------------------------------
	// Settings pour un niveau d'étageres
	//-------------------------------------------------------------------------------
	public function ecrireSettings($id_groupe)
	{
		if(!$id_groupe) $id_groupe=0;
		
		// settings
		$settings=file_get_contents($this->path_flash."settings.xml");
		$settings=str_replace("images.xml", $id_groupe."/images.xml", $settings);

		$etagere_group_path = $this->path_etagere.$id_groupe;
		if (!file_exists($etagere_group_path))
			mkdir($etagere_group_path);
		file_put_contents($etagere_group_path."/settings.xml",$settings);

		// Lire les images 1er niveau
		$data=fetchAll("select id_etagere,libelle,description,vignette from etageres where groupe=$id_groupe and id_mere=0");
		if(!$data) return false;
		
		// images.xml
		$xml="<images>";
		foreach($data as $etagere)
		{
			$path_relatif="../../../../userfiles/etageres/".$id_groupe."/";
			// entree principale
			$xml.='<item>';
			$xml.='<head link="'.$path_relatif.$etagere["vignette"].'"/>';
			$entries=fetchAll("select id_etagere,libelle,description,vignette from etageres where id_mere=".$etagere["id_etagere"]);

			// sous-menu
			if($entries)
			{
				foreach($entries as $entry)
				{
					//$url=BASE_URL."/opac/etagere/appelmenu?id_etagere=".$id_groupe."&id_kiosque=".$entry["id_etagere"];
					$url=BASE_URL."/etagere/appelmenu?id_etagere=".$id_groupe."&id_kiosque=".$entry["id_etagere"];
					$xml.='<photo link="'.$path_relatif.$entry["vignette"].'" url="'.$url.'">';
					$xml.='<![CDATA['.utf8_decode($entry["libelle"]).']]>';
					$xml.='</photo>'.CRLF;
				}
			}
			// fin entree
			$xml.='</item>';
		}
		$xml.="</images>";
		file_put_contents($etagere_group_path."/images.xml",$xml);

		return true;
	}

	//-------------------------------------------------------------------------------
	// Lire les notices
	//-------------------------------------------------------------------------------
	public function getNotices($recherche)
	{
		$moteur=new Class_MoteurRecherche();
		// EN DUR POUR LA DEMO
		if($_SERVER["REMOTE_ADDR"] == "127.0.0.1") $section="S2"; else $section="S6";
		$selection=array(
				//"selection_sections"=>$section,
				"expressionRecherche"=>$recherche,
				"avec_vignette"=>true
				);
		$ret=$moteur->lancerRechercheSimple($selection);
		if(!$ret["nombre"]) return false;
		$limit=" LIMIT 0,20";
		$notices=fetchAll($ret["req_liste"].$limit);
		return $notices;
	}

	//-------------------------------------------------------------------------------
	// Niveau final : kiosque
	//-------------------------------------------------------------------------------
	public function getKiosque($notices,$id_kiosque)
	{
		if(!$notices) return false;	
		$cls_notice= new Class_Notice();
		$cls_img = new Class_WebService_Vignette();

		// Settings xml
		$clef_settings=$id_kiosque.".xml";
		$path_settings=$this->path_etagere;
		$largeur_flash="720";

		// Constitution du settings.xml
		$fic_settings=PATH_FLASH."dockmenu_horizontal/settings.xml";

		$settings=file_get_contents($fic_settings);
		$settings=str_replace('"images.xml"','"kiosque_images_'.$clef_settings.'"',$settings);
		$settings=$this->setValueXml("dockWidth",$largeur_flash,$settings);
		//$settings=$this->setValueXml("autoScroll",$preferences["op_autoScroll"],$settings);
		//$settings=$this->setValueXml("imagesInfluence",$preferences["op_imagesInfluence"],$settings);

		file_put_contents($path_settings."kiosque_settings_".$clef_settings,$settings);

		// Images xml
		$xml='<dockmenu>';
		foreach($notices as $notice)
		{
			$notice=$cls_notice->getNotice($notice["id_notice"],'TA');
			$vignette=str_replace("../","../../temp/",$cls_img->getImage($notice["id_notice"],true));
			$xml.='<photo image="'.$vignette.'" bigimage="'.$vignette.'" url="'.BASE_URL."/recherche/viewnotice/id/".$notice["id_notice"].'?type_doc='.$notice["type_doc"].'" target="_self">';
			$xml.='<![CDATA['.wordwrap($notice["T"],20,BR).']]></photo>';
		}
		$xml.='</dockmenu>';
		file_put_contents($path_settings."kiosque_images_".$clef_settings,$xml);
	}

	//-------------------------------------------------------------------------------
	// Changer un setting xml
	//-------------------------------------------------------------------------------
	private function setValueXml($clef,$valeur,$settings)
	{
		$pos=strpos($settings,"<".$clef." value");
		if($pos === false) return $settings;
		$posfin=strpos($settings,'/>',$pos);
		$settings=substr($settings,0,$pos). '<' .$clef.' value="'.$valeur.'" '.substr($settings,$posfin);
		return $settings;
	}
}

?>
