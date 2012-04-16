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

class IndexController extends Zend_Controller_Action {
	function indexAction()	{
		// Mettre le layout
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setLayoutScript('portail.phtml');
	}


	function embedmoduleAction() {
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setLayoutScript('iframe.phtml');

		$id_module = $this->_getParam('id_module');
		$id_profil = $this->_getParam('id_profil');
		$profil = Class_Profil::getCurrentProfil();
		$this->view->module = $profil->getModuleAccueilConfig($id_module);
		$this->view->id_module = $id_module;
	}


	function sitedownAction()	{
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setLayoutScript('sansMenuGauche.phtml');
	}


	public function shareAction() {
		$this->_helper->getHelper('viewRenderer')
			->setNoRender();

		$profil = Class_Profil::getCurrentProfil();

		$rs = new Class_WebService_ReseauxSociaux();
		$body = sprintf("window.open('%s','_blank','location=yes, width=800, height=410')",
										$rs->getUrl($this->_getParam('on'), 
																$this->view->url(array('id_profil' => $profil->getId()), null, true),
																urldecode($this->_getParam('titre'))));

		$this->getResponse()->setHeader('Content-Type', 'application/javascript; charset=utf-8');
		$this->getResponse()->setBody($body);
	}


	public function formulairecontactAction() {
		$form = $this->_formulaireContact();
		if ($this->_request->isPost() && $form->isValid($this->_request->getPost())) {
			try {
				$this->_sendFormulaireContact();
				$this->_redirect('index/formulairecontactsent');
			}	catch (Exception $e) {
				$this->_helper->notify($e->getMessage());
				$this->_redirect('index/formulairecontacterror');
			}
			return;
		} 

		$this->view->form = $form;
	}


	public function formulairecontactsentAction() {}

	public function formulairecontacterrorAction() {}


	protected function _formulaireContact() {
		$form = $this->view->newForm(array('id' => 'contact'))
			->setAttrib('class', 'zend_form')
			->addElement('text', 'nom', array(
																				'label' => $this->view->_('Nom').' *',
																				'size'	=> 50,
																				'required' => true,
																				'allowEmpty' => false))
			->addElement('text', 'prenom', array(
																				'label' => $this->view->_('Prénom').' *',
																				'size'	=> 50,
																				'required' => true,
																				'allowEmpty' => false))
			->addElement('text', 'adresse', array(
																				'label' => $this->view->_('Adresse'),
																				'size'	=> 50))
			->addElement('text', 'code_postal', array(
																								'label' => $this->view->_('Code postal').' *',
																								'size'	=> 8,
																								'required' => true,
																								'allowEmpty' => false))
			->addElement('text', 'ville', array(
																					'label' => $this->view->_('Ville'),
																					'size'	=> 50))
			->addElement('text', 'mail', array(
																				 'label' => $this->view->_('E-mail').' *',
																				 'size'	=> 50,
																				 'required' => true,
																				 'allowEmpty' => false,
																				 'validators' => array('emailAddress')))
			->addElement('text', 'sujet', array(
																				'label' => $this->view->_('Sujet').' *',
																				'size'	=> 50,
																				'required' => true,
																				'allowEmpty' => false))
			->addElement('textarea', 'message', array(
																							'label' => $this->view->_('Message').' *',
																							'cols'	=> 60,
																							'required' => true,
																							'allowEmpty' => false))
			->Adddisplaygroup(
												array('nom', 
															'prenom',
															'adresse',
															'ville',
															'code_postal',
															'mail'), 
												'form_coordonnees',
												array('legend' => $this->view->_('Vos coordonnées')))
			->addDisplayGroup(
												array('sujet', 
															'message'),
												'form_message',
												array('legend' => $this->view->_('Votre message')));

		if (!defined('NOCAPTCHA')) {//desactive les captchas pour les tests :( 
			$this->_deleteCaptchaOlderThanOneMinute();

			$form
				->addElement('captcha', 'captcha', array('captcha' => 'Image',
																								 'label' => $this->view->_('Recopiez le code'),
																								 'captchaOptions' => array('font' => PATH_FONTS.'/Vera.ttf',
																																					 'imgDir' => PATH_CAPTCHA,
																																					 'imgUrl' => URL_CAPTCHA)))
			->addDisplayGroup(
												array('captcha'),
												'form_security',
												array('legend' => $this->view->_('Sécurité')));
		}
		return $form;
	}

	
	protected function _deleteCaptchaOlderThanOneMinute() {
		if (!file_exists(PATH_CAPTCHA))
				mkdir(PATH_CAPTCHA);

		$pngs = glob( PATH_CAPTCHA.'*.png' );
		if (!is_array($pngs))
			return;

		foreach ($pngs as $png) {
			if ((time() - filemtime($png )) > 60 )
				unlink($png);
		}
	}


	protected function _sendFormulaireContact() {
			if (!$mail_address = Class_Profil::getCurrentProfil()->getMailSiteOrPortail())
				throw new Exception($this->view->_("Erreur à l'envoi du mail: destinataire non configuré"));

			$data = ZendAfi_Filters_Post::filterStatic($this->_request->getPost());

			$mail = new Zend_Mail('utf8');
			$mail
				->setFrom('no-reply@'.$this->_request->getHttpHost())
				->addTo($mail_address)
				->setSubject('[AFI-OPAC] '.$data['sujet'])
				->setBodyText(sprintf("%s \n\nExpéditeur\nNom: %s\nPrénom: %s\nAdresse: %s\nVille: %s\nCode postal: %s\nE-mail: %s",
															$data['message'],
															$data['nom'],
															$data['prenom'],
															$data['adresse'],
															$data['ville'],
															$data['code_postal'],
															$data['mail']))
				->send();
	}
}