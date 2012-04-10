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
class Admin_I18nController extends Zend_Controller_Action {
	/**
	 * @var Class_I18n
	 */
	protected $_i18n;

	public function init() {
		$this->_helper->getHelper('AjaxContext')
										->addActionContext('update', 'json')
										->initContext()
										;
	}

	public function preDispatch() {
		$this->_i18n = Class_I18n::getInstance();

	}

	public function indexAction() {
		// à chaque accès à cet écran on force la mise à jour du master
		$this->_i18n->setProfilExtractor(new Class_Profil_I18nStringExtractor())->generate();

		// puis on lit le master
		$master = $this->_i18n->read();
		asort($master, SORT_STRING);

		// et les traduction pour chaque langue configurée
		$translated = array();

		foreach(Class_AdminVar::getLanguesWithoutDefault() as $langue) {
			$translated[$langue] = $this->_i18n->read($langue);

		}

		$form = $this->_getForm($master, $translated);

		$this->_processForm($form);

		$this->view->titre = 'Traductions';
		$this->view->headScript()->appendFile(URL_ADMIN_JS . 'i18n.js');

		$this->view->master = $master;
		$this->view->translated = $translated;
		$this->view->form = $form;

	}

	public function updateAction() {
		if (
			('' == (string)$this->_getParam('lang'))
			|| ('' == (string)$this->_getParam('field'))
		) {
			$this->_response->setHttpResponseCode(500)->clearBody()	;
			$this->_response->sendResponse();

		}

		$this->_i18n->update((string)$this->_getParam('lang'),
												(string)$this->_getParam('field'),
												(string)$this->_getParam('value'));

		$this->_helper->json(array('succes' => true));

	}

	/**
	 * @return Zend_Form
	 */
	private function _getForm(array $master, array $languages) {
		$form = new Zend_Form(array(
			'id'			=> 'i18nForm',
			'method'	=> Zend_Form::METHOD_POST
		));

		foreach ($languages as $langue => $v) {
			// séparation en 2 sections pour classement
			// les pas encore traduits par alpha du master
			// puis ceux qui sont traduits par alpha du master
			$empties = $translated = array();

			foreach ($master as $key => $value) {
				if (
					isset($v[$key])
					&& ('' != $v[$key])
				) {
					$translated[$key] = $v[$key];

				} else {
					$empties[$key] = '';

				}
			}

			$elements = array();

			foreach ($empties as $key => $value) {
				$elements[$langue . '_' . $key] = $this->_createElementFor($langue, $key, $value, $master[$key]);
			}

			foreach ($translated as $key => $value) {
				$elements[$langue . '_' . $key] = $this->_createElementFor($langue, $key, $value, $master[$key]);
			}

			if (0 < count($elements)) {
				$form->addElements($elements);
				$form->addDisplayGroup(array_keys($elements), $langue);

			}

		}

		$form->addElement(new Zend_Form_Element_Hidden('i18nFormId', array('value' => 1)));

		return $form;

	}

	/**
	 * @param string $lang
	 * @param string $key
	 * @param string $value
	 * @param string $original
	 * @return Zend_Form_Element
	 */
	private function _createElementFor($lang, $key, $value, $original) {
		if (0 < strpos($original, "\n")) {
			$element = new Zend_Form_Element_Textarea($lang . '_' . $key);
			$element->setAttrib('rows', 1);

		} else {
			$element = new Zend_Form_Element_Text($lang . '_' . $key);

		}

		$element->setAttrib('style', 'width:500px;')
						->setAttrib('class', 'i18n_field')
						->setValue($value)
						->setLabel($original)
						->removeDecorator('label')
						->removeDecorator('htmlTag')
						;

		return $element;

	}

	/**
	 * @param Zend_Form $form
	 */
	private function _processForm($form) {
		if (
			$this->_request->isPost()
			&& (1 == $this->_getParam('i18nFormId'))
			&& $form->isValid($this->_request->getPost())
		) {
			foreach(Class_AdminVar::getLanguesWithoutDefault() as $langue) {
				if (null !== ($displayGroup = $form->getDisplayGroup($langue))) {
					$values = array();

					foreach ($displayGroup->getElements() as $element) {
						$values[substr($element->getName(), strlen($langue)+1)] = $element->getValue();

					}

					if (0 < count($values)) {
						$this->_i18n->updateAll($langue, $values);

					}
				}
			}

			// redirection
			$this->_redirect($this->_helper->url('index'));

		}
	}
}
?>