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
class Admin_SystemeController extends Zend_Controller_Action {

//------------------------------------------------------------------------------------------------------
// Test des web-services
//------------------------------------------------------------------------------------------------------
	function webservicesAction()
	{
		$this->view->titre = 'Test des Web Services';
		$cls=new Class_WebService_AllServices();
		$id_service=$this->_getparam("id_service");
		$id_fonction=$this->_getparam("id_fonction");

		// Test de service
		if($id_service) $this->view->tests=$cls->testService($id_service,$id_fonction);

		// Liste des services
		$this->view->services=$cls->getServices();
	}

//------------------------------------------------------------------------------------------------------
// Cache des images
//------------------------------------------------------------------------------------------------------
	function cacheimagesAction()
	{
		$this->view->titre = 'Contrôle du cache des images';
		$path=getcwd()."/temp/vignettes_titre";

		// Effacer tout le cache
		if($_REQUEST['mode']=="reset_all")
		{
			sqlExecute("update notices set url_vignette='',url_image=''");
		}

		// Effacer les images non reconnues
		if($_REQUEST['mode']=="reset_no")
		{
			sqlExecute("update notices set url_vignette='',url_image='' where url_vignette='NO'");
		}

		// Nombres
		$this->view->nb_notices=fetchOne("select count(*) from notices");
		$this->view->nb_reconnu=fetchOne("select count(*) from notices where url_vignette > '' and url_vignette != 'NO'");
		$this->view->nb_pas_reconnu=fetchOne("select count(*) from notices where url_vignette ='NO'");

		// Calcul de l'espace occupé
		$dir=opendir($path);
    while(false !== ($file = readdir($dir)))
    {
    	$extension=substr($file,-4);
    	if($extension != ".gif" and $extension !=".png" and $extension !=".jpg") continue;
    	if($_REQUEST['mode']=="reset_all")
			{
				unlink($path.'/'.$file);
				continue;
			}
			$taille+=filesize($path.'/'.$file);
			$this->view->nb_cache++;
    }
    closedir($dir);
		$this->view->taille_cache=(int)($taille/1024);
	}

//------------------------------------------------------------------------------------------------------
// Constitution du cache des images
//------------------------------------------------------------------------------------------------------
	function makecacheimagesAction()
	{
		// nombre de notices a traiter en 1 appel
		$limit=10;

		// nombre a traiter
		$nb_a_traiter=fetchOne("select count(*) from notices where url_vignette=''");

		// Traitement
		if($nb_a_traiter)
		{
			// Instanciations
			$cls_notice= new Class_Notice();
			$cls_vignette=new Class_WebService_Vignette();

			// Lecture notices
			$data=fetchAll("select id_notice from notices where url_vignette='' order by id_notice desc limit 0,".$limit);
			foreach($data as $ligne)
			{
				$id_notice=$ligne["id_notice"];
				$notice=$cls_notice->getNotice($id_notice,"TA");
				$urls=$cls_vignette->getUrls($notice);
				if($urls==false) $urls=array("vignette"=>"NO","image"=>"NO");
				sqlExecute("update notices set url_vignette='".$urls["vignette"]."',url_image='".$urls["image"]."' where id_notice=$id_notice");
				$nb_a_traiter--;
			}
		}

		// Retour infos
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setNoRender();
		if($nb_a_traiter > 0) $script='<script>makeCacheImages("/admin/systeme/makecacheimages")</script>';
		print($nb_a_traiter.$script);
		exit;
	}


//------------------------------------------------------------------------------------------------------
// Import des avis OPAC2
//------------------------------------------------------------------------------------------------------
	function importavisopac2Action()
	{
		$this->view->titre="Import des avis opac2";
		if($_REQUEST["mode"])
		{
			$this->view->mode="import";
			if(!trim($_REQUEST["data_opac2"])) $this->view->erreur="Il n'y a aucune donnée à importer.";
			else $this->view->data_opac2=$_REQUEST["data_opac2"];
		}
		else $this->view->mode="intro";
	}


	public function phpinfoAction() {	
		$this->view->titre = 'Informations système';
	}


	public function mailtestAction() {
		$this->view->titre = 'Test de l\'envoi des mails';

		$form = $this->view->newForm(array('id' => 'mailtest'))
			->addElement('text', 'sender', array('label' => 'Emetteur',
																					 'size' => 50,
																					 'required' => true,
																					 'allowEmpty' => false,
																					 'validators' => array('emailAddress'),
																					 'value' => Class_Profil::getLoader()->getPortail()->getMailSite()))
			->addElement('text', 'recipient', array('label' => 'Destinataire',
																							'size' => 50,
																							'required' => true,
																							'allowEmpty' => false,
																							'validators' => array('emailAddress')))
			->addDisplayGroup(array('sender', 'recipient'),
												'mail',
												array('legend' => 'Général'))
			->addElement('submit', 'send', array('label' => 'Envoyer'));


		if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
			$mail = new ZendAfi_Mail('utf8');
			$mail
				->setFrom($this->_request->getPost('sender'))
				->addTo($this->_request->getPost('recipient'))
				->setSubject('[AFI-OPAC2.0] test envoi mails')
				->setBodyText('Envoyé depuis '.BASE_URL)
				->send();

			$this->view->message = 'Le mail a bien été envoyé';
		}

		$this->view->form = $form;
	}
	
}

?>