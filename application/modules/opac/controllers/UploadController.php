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
class UploadController extends Zend_Controller_Action
{

	function init()
	{
		// Changer le layout
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setLayoutScript('iframe.phtml');

		// Dimensions de la vignette
		$this->view->largeur_vignette=$this->_getParam("largeur_vignette");
		$this->view->hauteur_vignette=$this->_getParam("hauteur_vignette");

		// Champ hidden a valoriser
		$this->view->input_name=$this->_getParam("input_name");

		// Message sur l'image
		$msg = $this->view->_("*NB : l'image doit être de type \"%s\", avoir une taille inférieure à %s ko", 
													urldecode($this->_getParam("extensions")), 
													$this->_getParam("poids"));

		if ($this->_getParam("largeur_conseil")) 
			$msg.= $this->view->_(" et des dimensions se rapprochant de %s x %s pixels", 
														$this->_getParam("largeur_conseil"), 
														$this->_getParam("hauteur_conseil"));
		$msg.=".";
		$this->view->conseil=$msg;
	}
	//---------------------------------------------------------------------
	// formulaire
	//---------------------------------------------------------------------
	function formAction()
	{
		$path = "/userfiles/".$this->_getParam("path").'/'.urldecode($this->_getParam("filename"));

		// Controle image
		$adresse_img=getcwd().$path;

		if($this->_getParam("filename") and file_exists($adresse_img)) 
			$this->view->image=BASE_URL.$path;
		else $this->view->image = URL_ADMIN_IMG."blank.gif";
		$this->view->filename=$this->_getParam("filename");

		// Reformer l'url
		$this->view->url=str_replace("form","upload",$this->_request->REQUEST_URI);
	}

	//---------------------------------------------------------------------
	// upload
	//---------------------------------------------------------------------
	function uploadAction()
	{
		$fic = $_FILES["photo"];
		if($fic["error"] > 0)
		{
			if(!$fic["name"]) $erreur=$this->view->_("Vous devez sélectionner une image en cliquant sur le bouton : parcourir");
			elseif(!$fic["size"]) $erreur=$this->view->_("Le fichier que vous avez sélectionné est vide.");
			else $erreur=$this->view->_('Erreur au téléchargement du fichier : L\'image que vous essayez de télécharger est trop volumineuse ou inaccessible.');
		}
		else
		{
			// Controles
			$ext=array_pop(explode(".",$fic["name"]));
			$ext=".".strtolower($ext);

			$taille=(int)($fic["size"]/1024);
			if(strpos($this->_getParam("extensions"),$ext) === false) 
				$erreur=$this->view->_("L'image que vous avez sélectionnée doit être de type : '$s' et pas de type : %s", $this->_getParam("extensions"), $ext);
			elseif($taille > $this->_getParam("poids") ) 
				$erreur=$this->view->_("L'image que vous avez sélectionnée est trop volumiseuse. Taille maximum : %d ko", $this->_getParam("poids"));
			else	{
				$path_img = "/userfiles/".urldecode($this->_getParam("path")).'/'.$fic["name"];
				// Controle extension de l'image
				$adresse_img=getcwd().$path_img;

				if(move_uploaded_file($fic['tmp_name'],$adresse_img))
				{ 
					$this->view->filename=$fic["name"];
					$this->view->image=BASE_URL.$path_img;
					$viewRenderer = $this->getHelper('ViewRenderer');
					$viewRenderer->renderScript('upload/form.phtml');
				}
				else 
					$erreur=$this->view->_("Erreur au transfert du fichier vers userfiles");
			}
		}
		if($erreur)
		{
			$this->view->filename=$this->_getParam("filename");
			$this->view->erreur=$erreur;
			$this->view->fichier=$_FILES["photo"];
			$this->view->url_retour=urldecode(str_replace("upload?","form?",$this->_request->REQUEST_URI));
		}
	}

	//---------------------------------------------------------------------
	// upload multiple
	//---------------------------------------------------------------------
	function uploadmultipleAction()
	{
		$this->view->type=$this->_getParam("type");
		$this->view->id_dossier=$this->_getParam("id_dossier");
	}

	//---------------------------------------------------------------------
	// upload multiple : reception d'un fichier
	//---------------------------------------------------------------------
	function fichiermultipleAction()
	{
		// parametres
		$type=$this->_getParam("type");
		$id_dossier=$this->_getParam("id_dossier");
		
		// classe de traitemnt de l'image
		switch($type)
		{
			case "album" :
				$cls=new Class_Album(); break;
			default : $ret=array("succes"=>false,"erreur"=>"type incorrect");
		}
		
		// traiter l'enregistement et l'image
		if($cls)
		{
			if(is_array($cls) and $cls["erreur"]) $ret=array("succes"=>false,"erreur"=>$cls["erreur"]);
			else $ret=$cls->ajouterImage($id_dossier);
		}
		else $ret=array("succes"=>false,"erreur"=>"erreur à l'instanciation de la classe multiupload");

		// retour
		$ret["error"]=$ret["erreur"];
		echo htmlspecialchars(json_encode($ret), ENT_NOQUOTES);
		exit;
	}
}

?>