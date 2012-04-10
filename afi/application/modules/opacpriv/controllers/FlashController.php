<?php
/////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Objets flash
/////////////////////////////////////////////////////////////////////////////////////////////////////////////

class Opacpriv_FlashController extends Zend_Controller_Action
{
	private $wrap=15;										// Nb caracteres pour les césures de titres sur plusieurs lignes
	private $coeff_largeur=0.95;				// Coefficient a appliquer pour la largeur des objets flash
	private $afi_path_flash;
	
//-------------------------------------------------------------------------------
// Met le layout
//-------------------------------------------------------------------------------
	function init()	{
		$this->afi_path_flash = './afi/public/opacpriv/flash/';
		$this->view->path_flash = $this->afi_path_flash;
		$this->view->url_flash = BASE_URL . '/afi/public/opacpriv/flash/';

		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setLayoutScript('iframe.phtml');
		$this->view->addScriptPath("application/modules/opac/views/scripts/");
		$this->view->addScriptPath("afi/application/modules/opacpriv/view/scripts/");
	}

//-------------------------------------------------------------------------------
// Images tournantes en flash
//-------------------------------------------------------------------------------
	function coverflowAction()
	{
		// Contexte
		$id_profil=$_SESSION["id_profil"];
		$id_module=$this->_getParam("id_module");
		$largeur_flash=$this->getLargeurFlash();

		$preferences=$this->view->profil->getModuleAccueilPreferences($id_module);
		$clef_settings="_".$id_profil."_".$id_module.".xml";
		$path_settings=PATH_TEMP."flash/";
	
		// Lire les notices
		$catalogue=new Class_Catalogue();
		$notices=$catalogue->getNotices($preferences,"cache");
		
		// Constitution du settings.xml
		$fic_settings=$this->afi_path_flash."coverflow/settings.xml";
		$settings=file_get_contents($fic_settings);
		
		$settings=str_replace('"images.xml"','"images'.$clef_settings.'"',$settings);
		$settings=$this->setValueXml("widthComponent",$largeur_flash,$settings);
		$settings=$this->setValueXml("planeSeparation",$preferences["op_planeSeparation"],$settings);
		$settings=$this->setValueXml("planeAngle",$preferences["op_planeAngle"],$settings);
		$settings=$this->setValueXml("planeOffsetZ",$preferences["op_planeOffsetZ"],$settings);
		$settings=$this->setValueXml("positionFreeX",$preferences["op_positionFreeX"],$settings);
		$settings=$this->setValueXml("width",$preferences["op_width"],$settings);

		file_put_contents($path_settings."settings".$clef_settings,$settings);
		
		// Images xml
		$xml='<images>';
		foreach($notices as $notice)
		{
			$xml.='<image url = "'.$notice["vignette"].'" link = "'.BASE_URL."/recherche/viewnotice/id/".$notice["id_notice"].'?type_doc='.$notice["type_doc"].'" target="_parent" title = "'.$notice["titre"].'"></image>';
		}
		$xml.='</images>';
		file_put_contents($path_settings."images".$clef_settings,$xml);
		
		// Variables de vue
		$this->view->fic_settings="settings".$clef_settings;
	}

//-------------------------------------------------------------------------------
// Carrousel horizontal
//-------------------------------------------------------------------------------
	function carrouselhorizontalAction()
	{
		// Contexte
		$id_module=$this->_getParam("id_module");
		$preferences = Class_Profil::getCurrentProfil()->getModuleAccueilPreferences($id_module);
		$clef_settings="_".$id_profil."_".$id_module.".xml";
		$path_settings=PATH_TEMP."flash/";
		$largeur_flash = $this->getLargeurFlash();
		$this->view->largeur_flash=$largeur_flash;
	
		// Lire les notices
		$catalogue=new Class_Catalogue();
		$notices=$catalogue->getNotices($preferences,"cache");
		
		// Constitution du settings.xml
		$fic_settings=$this->afi_path_flash."carrousel_horizontal/settings.xml";
		$settings=file_get_contents($fic_settings);

		$settings=str_replace('"images.xml"','"images'.$clef_settings.'"',$settings);
		$settings=$this->setValueXml("carouselWidth",$largeur_flash,$settings);
		$settings=$this->setValueXml("radiusX",$preferences["op_radiusX"],$settings);
		$settings=$this->setValueXml("thumbWidth",$preferences["op_thumbWidth"],$settings);
		$settings=$this->setValueXml("thumbHeight",$preferences["op_thumbHeight"],$settings);
		$settings=$this->setValueXml("centerX",$preferences["op_centerX"],$settings);
		$settings=$this->setValueXml("centerY",$preferences["op_centerY"],$settings);

		file_put_contents($path_settings."settings".$clef_settings,$settings);
		
		// Images xml
		$ix=new Class_Indexation();
		$xml='<Carousel centeredImage="../../public/opac/flash/carrousel_horizontal/images/logo.png">';
		foreach($notices as $notice)
		{
			$xml.='<photo image="'.$notice["vignette"].'" bigimage="'.$notice["vignette"].'" url="'.BASE_URL."/recherche/viewnotice/id/".$notice["id_notice"].'?type_doc='.$notice["type_doc"].'" target="_parent">';
			$xml.='<![CDATA['.strtolower($ix->alphaMaj($notice["titre"])).']]></photo>';
		}
		$xml.='</Carousel>';
		file_put_contents($path_settings."images".$clef_settings,$xml);
		
		// Variables de vue
		$this->view->fic_settings="settings".$clef_settings;
	}
	
//-------------------------------------------------------------------------------
// Carrousel vertical
//-------------------------------------------------------------------------------
	function carrouselverticalAction()
	{
		// Contexte
		$id_profil=$_SESSION["id_profil"];
		$id_module=$this->_getParam("id_module");
		$preferences=$this->view->profil->getModuleAccueilPreferences($id_module);

		$clef_settings="_".$id_profil."_".$id_module.".xml";
		$path_settings=PATH_TEMP."flash/";
		$largeur_flash=$this->getLargeurFlash();
		$this->view->largeur_flash=$largeur_flash;
	
		// Lire les notices
		$catalogue=new Class_Catalogue();
		$notices=$catalogue->getNotices($preferences,"cache");
		
		// Constitution du settings.xml
		$fic_settings=$this->afi_path_flash."carrousel_vertical/settings.xml";
		$settings=file_get_contents($fic_settings);
		$settings=str_replace('"images.xml"','"images'.$clef_settings.'"',$settings);
		$settings=$this->setValueXml("carouselWidth",$largeur_flash,$settings);
		$settings=$this->setValueXml("radiusX",$preferences["op_radiusX"],$settings);
		$settings=$this->setValueXml("thumbWidth",$preferences["op_thumbWidth"],$settings);
		$settings=$this->setValueXml("thumbHeight",$preferences["op_thumbHeight"],$settings);
		$settings=$this->setValueXml("centerX",$preferences["op_centerX"],$settings);
		$settings=$this->setValueXml("centerY",$preferences["op_centerY"],$settings);
		file_put_contents($path_settings."settings".$clef_settings,$settings);
		
		// Images xml
		$ix=new Class_Indexation();
		$xml='<Carousel centeredImage="../../public/opac/flash/carrousel_vertical/images/logo.png">';
		foreach($notices as $notice)
		{
			$xml.='<photo image="'.$notice["vignette"].'" bigimage="'.$notice["vignette"].'" url="'.BASE_URL."/recherche/viewnotice/id/".$notice["id_notice"].'?type_doc='.$notice["type_doc"].'" target="_parent">';
			$xml.='<![CDATA['.wordwrap(strtolower($ix->alphaMaj($notice["titre"])),$this->wrap,BR).']]></photo>';
		}
		$xml.='</Carousel>';
		file_put_contents($path_settings."images".$clef_settings,$xml);
		
		// Variables de vue
		$this->view->fic_settings="settings".$clef_settings;
	}
	
//-------------------------------------------------------------------------------
// DockMenu horizontal
//-------------------------------------------------------------------------------
	function dockmenuhAction()
	{
		// Contexte
		$id_profil=$_SESSION["id_profil"];
		$id_module=$this->_getParam("id_module");
		$preferences=$this->view->profil->getModuleAccueilPreferences($id_module);

		$clef_settings="_".$id_profil."_".$id_module.".xml";
		$path_settings=PATH_TEMP."flash/";
		$largeur_flash=$this->getLargeurFlash();
	
		// Lire les notices
		$catalogue=new Class_Catalogue();
		$notices=$catalogue->getNotices($preferences,"cache");
		
		// Constitution du settings.xml
		$fic_settings=$this->afi_path_flash."dockmenu_horizontal/settings.xml";

		$settings=file_get_contents($fic_settings);
		$settings=str_replace('"images.xml"','"images'.$clef_settings.'"',$settings);
		$settings=$this->setValueXml("dockWidth",$largeur_flash,$settings);
		$settings=$this->setValueXml("autoScroll",$preferences["op_autoScroll"],$settings);
		$settings=$this->setValueXml("imagesInfluence",$preferences["op_imagesInfluence"],$settings);

		file_put_contents($path_settings."settings".$clef_settings,$settings);
		
		// Images xml
		$xml='<dockmenu>';
		foreach($notices as $notice)
		{
			$xml.='<photo image="'.$notice["vignette"].'" bigimage="'.$notice["vignette"].'" url="'.BASE_URL."/recherche/viewnotice/id/".$notice["id_notice"].'?type_doc='.$notice["type_doc"].'" target="_parent">';
			$xml.='<![CDATA['.wordwrap($notice["titre"],$this->wrap,BR).']]></photo>';
		}
		$xml.='</dockmenu>';
		file_put_contents($path_settings."images".$clef_settings,$xml);
		
		// Variables de vue
		$this->view->fic_settings="settings".$clef_settings;
	}
	
//-------------------------------------------------------------------------------
// DockMenu Vertical
//-------------------------------------------------------------------------------
	function dockmenuvAction()
	{
		// Contexte
		$id_profil=$_SESSION["id_profil"];
		$id_module=$this->_getParam("id_module");
		$preferences=$this->view->profil->getModuleAccueilPreferences($id_module);

		$clef_settings="_".$id_profil."_".$id_module.".xml";
		$path_settings=PATH_TEMP."flash/";
		$largeur_flash=$this->getLargeurFlash();
	
		// Lire les notices
		$catalogue=new Class_Catalogue();
		$notices=$catalogue->getNotices($preferences,"cache");
		
		// Constitution du settings.xml
		$fic_settings=$this->afi_path_flash."dockmenu_vertical/settings.xml";
		$settings=file_get_contents($fic_settings);

		$settings=str_replace('"images.xml"','"images'.$clef_settings.'"',$settings);
		$settings=$this->setValueXml("dockWidth",$largeur_flash,$settings);
		$settings=$this->setValueXml("autoScroll",$preferences["op_autoScroll"],$settings);
		$settings=$this->setValueXml("imagesInfluence",$preferences["op_imagesInfluence"],$settings);

		file_put_contents($path_settings."settings".$clef_settings,$settings);
		
		// Images xml
		$xml='<dockmenu>';
		foreach($notices as $notice)
		{
			$xml.='<photo image="'.$notice["vignette"].'" bigimage="'.$notice["vignette"].'" url="'.BASE_URL."/recherche/viewnotice/id/".$notice["id_notice"].'?type_doc='.$notice["type_doc"].'" target="_parent">';
			$xml.='<![CDATA['.wordwrap($notice["titre"],$this->wrap,BR).']]></photo>';
		}
		$xml.='</dockmenu>';
		file_put_contents($path_settings."images".$clef_settings,$xml);
		
		// Variables de vue
		$this->view->fic_settings="settings".$clef_settings;
	}
	
	//-------------------------------------------------------------------------------
	// DockMenu Vertical
	//-------------------------------------------------------------------------------
	function pyramidhorizontalAction()
	{
		// Contexte
		$id_profil=$_SESSION["id_profil"];
		$id_module=$this->_getParam("id_module");
		$preferences=$this->view->profil->getModuleAccueilPreferences($id_module);

		$clef_settings="_".$id_profil."_".$id_module.".xml";
		$path_settings=PATH_TEMP."flash/";
		$largeur_flash=$this->getLargeurFlash();
	
		// Lire les notices
		$catalogue=new Class_Catalogue();
		$notices=$catalogue->getNotices($preferences,"cache");
		
		// Constitution du settings.xml
		$fic_settings=$this->afi_path_flash."pyramid_gallery/settings.xml";
		$settings=file_get_contents($fic_settings);
		
		$settings=str_replace('"images.xml"','"images'.$clef_settings.'"',$settings);
		$settings=$this->setValueXml("galleryWidth",$largeur_flash,$settings);
		$settings=$this->setValueXml("pictureDistance",$preferences["op_pictureDistance"],$settings);
		$settings=$this->setValueXml("columns",$preferences["op_columns"],$settings);

		//file_put_contents($path_settings."settings".$clef_settings,$settings); // BUG DE l'OBJET FLASH
		file_put_contents($path_settings."settings.xml",$settings);
		
		// Images xml
		$xml='<pyramidGallery>';
		foreach($notices as $notice)
		{
			$xml.='<photo image="'.$notice["vignette"].'" url="'.BASE_URL."/recherche/viewnotice/id/".$notice["id_notice"].'?type_doc='.$notice["type_doc"].'" target="_parent">';
			$xml.='<![CDATA[<tooltip>'.wordwrap($notice["titre"],$this->wrap,BR).'</tooltip>]]></photo>';
		}
		$xml.='</pyramidGallery>';
		file_put_contents($path_settings."images".$clef_settings,$xml);
		
		// Variables de vue
		//$this->view->fic_settings="settings".$clef_settings;  A CAUSE DU BUG DE L'OBJET FLASH CA DOIT S'APPELER settings.xml et pas autrement
		$this->view->fic_settings="settings.xml";
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

	//--------------------------------------------------------------------------------
	// Rend la largeur interne de l'objet flash en fonction de la taille des divisions
	//--------------------------------------------------------------------------------
	private function getLargeurFlash()
	{
		$id_module=$this->_getParam("id_module");

		$module=$this->view->profil->getModuleAccueilConfig($id_module);
		$division = $module["division"] ? $module["division"] : 1;
		
		$largeur_division = Class_Profil::getCurrentProfil()->_get("largeur_division".$division);
		$largeur_flash=(int)($largeur_division * $this->coeff_largeur);
		return $largeur_flash;
	}


	public function carouselhorizontaldescAction() {
		$this->getHelper('ViewRenderer')->setNoRender();
		echo $this->view->_('Kiosque de notices, carousel horizontal');
	}
}