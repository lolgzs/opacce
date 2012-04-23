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
//////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Controleur Newsletter
//////////////////////////////////////////////////////////////////////////////////////////

class Admin_NewsletterController extends Zend_Controller_Action {
	function init() {
		$this->view->titre = "Lettres d'information";
	}


	function indexAction() {
		//évite de mocker findAll à chaque fois dans les tests
		if (! $newsletter = Class_Newsletter::getLoader()->findAll())
			$newsletter = array();
		$this->view->newsletters = $newsletter;
	}


	protected function _newsletterForm($newsletter) {
		$form = $this->view->newForm('newsletter');
		$form
			->setAction($this->view->url(array('action' => 'edit')))
			->setMethod('post')
			->setAttrib('id', 'newsletter');

		$titre = $form
			->createElement('text', 'titre')
			->setLabel('Titre')
			->setAttrib('size', 30)
			->setRequired(true);

		$expediteur = $form
			->createElement('text', 'expediteur')
			->setLabel('Expéditeur')
			->setAttrib('size', 30)
			->addValidator(new Zend_Validate_EmailAddress());

		$contenu = $form
			->createElement('ckeditor', 'contenu')
			->setRequired(true);

		$id_catalogue = $form
			->createElement('select', 'id_catalogue', array("onchange"=>"$('#id_panier').val('')"))
			->setLabel('Catalogue')
			->setMultiOptions(Class_Catalogue::getCataloguesForCombo());


		$id_panier = $form
			->createElement('select', 'id_panier', array("onchange"=>"$('#id_catalogue').val('')"))
			->setLabel('Panier');

		$paniers_admin = Class_PanierNotice::getLoader()->findAllBelongsToAdmin();
		$id_panier->addMultiOption(null, '');
		foreach($paniers_admin as $panier) 
			$id_panier->addMultiOption($panier->getId(), $panier->getLibelle());

		$nb_notices = $form
			->createElement('text', 'nb_notices')
			->setLabel('Nombre à afficher')
			->setAttrib('size', 10)
			->setRequired(true)
			->setValue(0)
			->addValidator(new Zend_Validate_Int());


		$form
			->addElement($titre)
			->addElement($expediteur)
			->addElement($contenu)
			->addDisplayGroup(array('titre', 'expediteur'), 
												'letter', 
												array("legend" => "Lettre"))
			->addDisplayGroup(array('contenu'), 
												'contenu_html', 
												array("legend" => "Contenu HTML"))
			->addElement($id_catalogue)
			->addElement($id_panier)
			->addElement($nb_notices)
			->addDisplayGroup(array('id_catalogue', 'id_panier', 'nb_notices'), 
												'notices', 
												array("legend" => "Notices"));

		$form ->populate($newsletter->toArray());

		return $form;
	}


	function addAction() {
		$newsletter = new Class_Newsletter();
		$form = $this->_newsletterForm($newsletter);
		$form
			->setAction($this->view->url(array('action' => 'add')));

		if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
			$newsletter
				->updateAttributes($_POST)
				->save();
			$this->_redirect('admin/newsletter/index');
		}

		$this->view->newsletter = $newsletter;
		$this->view->form = $form;
		$this->view->titre = "Créer une lettre d'information";
	}


	function editAction() {
		if ($id = $this->_request->getParam('id'))
			$newsletter = Class_Newsletter::getLoader()->find($id);
		else
			$newsletter = new Class_Newsletter();

		$form = $this->_newsletterForm($newsletter);
		$newsletter->updateAttributes($_POST);

		if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
			$newsletter->save();
			$this->_redirect("admin/newsletter/preview/id/$id");
		}

		$this->view->subview = $this->view->partial('newsletter/edit.phtml',
																								array(
																											'form' => $form,
																											'newsletter' => $newsletter));
		$this->_forward('index');
	}


	function deleteAction() {
		$id = $this->_request->getParam('id');
		$newsletter = Class_Newsletter::getLoader()->find($id);
		$newsletter->delete();
		$this->_redirect('admin/newsletter/index');
	}


	function sendAction() {		
		$id = $this->_request->getParam('id');
		$newsletter = Class_Newsletter::getLoader()->find($id);

		try {
			$newsletter->send();
			$message = "Lettre envoyée";
		} catch(Exception $e) {
			$message = "Erreur à l'envoi de la lettre: ".$e->getMessage();
			$this->getResponse()->setHttpResponseCode(500);
		}

		$this->_helper->viewRenderer->setNoRender();
		echo $message;
	}


	protected function _sendTestForm($newsletter) {
		$form = new Zend_Form;
		$form
			->setAction($this->view->url(array('action' => 'sendtest')))
			->setMethod('post')
			->setAttrib('id', 'sendparams')
			->setAttrib('class', 'form');

		$destinataire = $form
			->createElement('text', 'destinataire')
			->setLabel('Destinataire')
			->setAttrib('size', 30)
			->setRequired(true)
			->setValue($newsletter->getExpediteur())
			->addValidator(new Zend_Validate_EmailAddress());

		$form
			->addElement($destinataire)
			->addElement('submit', 
									 'submit', 
									 array('label' => 'Envoyer à cette adresse'));
		return $form;
	}


	public function sendtestAction() {
		$id = $this->_request->getParam('id');
		$newsletter = Class_Newsletter::getLoader()->find($id);
		$form = $this->_sendTestForm($newsletter);

		if ($this->getRequest()->isPost() && $form->isValid($_POST)) {
			$destinataire = $this->_request->getParam('destinataire');
			try {
				$newsletter->sendTo($destinataire);

				$this->view->subview = sprintf('Lettre "%s" envoyée à %s',
																			 $newsletter->getTitre(),
																			 $destinataire);
				$this->_forward('index');
				return;
			} catch (Exception $e) {
				$form
					->addError(sprintf("Echec de l'envoi: %s",$e->getMessage()))
					->addDecorator('Errors');
			}
		}

		$subview = $this->view->partial('newsletter/sendtest.phtml',
																		array(
																					'form' => $form,
																					'newsletter' => $newsletter));
		$this->view->subview = $subview;
		$this->_forward('index');
	}


	function previewAction() {
		$id = $this->_request->getParam('id');
		$newsletter = Class_Newsletter::getLoader()->find($id);
		$this->view->subview = $this->view->partial('newsletter/preview.phtml',
																								array(
																											'newsletter' => $newsletter,
																											'mail' => $newsletter->generateMail()));
		$this->_forward('index');
	}
}