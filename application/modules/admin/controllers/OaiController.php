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
class Admin_OaiController extends ZendAfi_Controller_Action {
	public function getRessourceDefinitions() {
		return array(
								 'model' => array('class' => 'Class_EntrepotOAI',
																	'name' => 'entrepot'),
								 'messages' => array('successful_add' => 'Entrepôt %s ajouté',
																		 'successful_save' => 'Entrepôt %s sauvegardé',
																		 'successful_delete' => 'Entrepôt %s supprimé'),

								 'actions' => array('edit' => array('title' => 'Modifier un entrepôt OAI'),
																		'add'  => array('title' => 'Ajouter un entrepôt OAI'),
																		'index' => array('title' => 'Entrepôts OAI')),

								 'display_groups' => array('categorie' => array('legend' => 'Entrepôt',
																																'elements' => array(
																																										'libelle' => array('element' => 'text',
																																																			 'options' =>  array('label' => 'Libellé *',
																																																													 'size'	=> 30,
																																																													 'required' => true,
																																																													 'allowEmpty' => false)),
																																										'handler' => array('element' => 'text',
																																																			 'options' => array('label' => 'Url *',
																																																													'size' => '90',
																																																													'required' => true,
																																																													'allowEmpty' => false,
																																																													'validators' => array('url'))
																																																	 )
																																										)
																																)
																					 )
								 );
	}


	
	public function indexAction() {
		parent::indexAction();
		if ($expression_recherche = $this->_getParam('expression')) {
			try {
				$this->view->notices = Class_NoticeOAI::findNoticesByExpression($expression_recherche);
			} catch (Class_SearchException $e) {
				$this->view->notices = array();
				$this->view->error = $e->getMessage();
			}
		}
		$this->view->search_form = $this->searchForm($expression_recherche);
	}


	public function browseAction() {
		$entrepot_id = $this->_getparam("id");
		if ($entrepot_id) {
			$entrepot = Class_EntrepotOAI::getLoader()->find($entrepot_id);

			$oai_service = new Class_WebService_OAI();
			$oai_service->setOAIHandler($entrepot->getHandler());

			try {
				$oai_sets = $oai_service->getSets();
				$oai_set = $this->view->oai_sets[0];
			} catch (Exception $e) {
				$this->view->communication_error = $e->getMessage();
			}
		} 

		$this->view->subview = $this->view->partial('oai/browse.phtml',
																								array('titre' => sprintf('Parcours de l\'entrepôt "%s"', $entrepot->getLibelle()),
																											'entrepot_id' => $entrepot_id,
																											'oai_sets' => $oai_sets,
																											'oai_set' => $oai_set));
		$this->_forward('index');
	}


	public function searchAction() {
		$expression_recherche = $this->_getParam('expression');
		$this->_redirect('admin/oai/index/expression/'.$expression_recherche);
	}


	public function harvestAction() {
		$this->_helper->viewRenderer->setNoRender();

		$entrepot_id = $this->_getparam("entrepot_id");
		$resumption_token = $this->_getparam("resumption_token");
		$oai_set=$this->_getparam("oai_set");

		$entrepot = Class_EntrepotOAI::getLoader()->find($entrepot_id);
		$notice_oai = new Class_NoticeOAI();

		if ($resumption_token) {
			$token = new Class_WebService_ResumptionToken();
			$token->setToken($resumption_token);
			$next_token = $notice_oai->resumeHarvest($entrepot, $token);
		}	else {
			$next_token = $notice_oai->harvestSet($entrepot, $oai_set);
		}

		if ($next_token)
			echo $next_token->toJSON();
	}


	public function searchForm($expression) {
		return $this->view
			->newForm(array('id' => 'search'))
			->setAction($this->view->url(array('action' => 'search')))
			->setMethod('get')
			->addElement('text', 'expression', array('label' => 'Rechercher dans le catalogue OAI',
																							 'value' => $expression))
			->addElement('submit', 'OK');
	}


	public function importAction() {
		$notice = Class_NoticeOAI::getLoader()->find($this->_getParam('id'));
		$album = Class_Album::getLoader()->newInstance()
			->beOAI()
			->setTitre($notice->getTitre())
			->setAuteur($notice->getAuteur())
			->setEditeur($notice->getEditeur())
			->setAnnee($notice->getDate())
			->setIdOrigine($notice->getIdOai());

		$album->save();
		$this->_helper->notify($this->view->_('L\'album "%s" a été créé', $album->getTitre()));
		$this->_redirect('admin/oai/index/expression/'.$this->_getParam('expression'));
	}
	
}

?>