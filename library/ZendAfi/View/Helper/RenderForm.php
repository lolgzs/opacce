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
class ZendAfi_View_Helper_RenderForm extends ZendAfi_View_Helper_BaseHelper {
	/**
	 * @param Zend_Form $form
	 * @return string
	 */
	public function renderForm($form) {
		if (!$form)
			return;

		$form->setAttrib('class', trim($form->getAttrib('class').' form'));

		// compatibilité avec les tables admin standard
		$this->setupTableDecoratorsForForm($form);

		if ($this->isFormContainsSubmit($form))
			return $form->render();


		return $this->renderFormWithButtonsValiderAnnuler($form);
	}


	public function renderFormWithButtonsValiderAnnuler($form) {
		Class_ScriptLoader::getInstance()
			->addAdminScript('controle_maj')
			->addJQueryReady('$("form input").change(function(){setFlagMaj(true)})');

		return $form->render().$this->_buttonsFor($form->getAttrib('id'));
	}


	public function isFormContainsSubmit($form) {
		$isFormContainsSubmit = false;

		foreach($form->getElements() as $element)
			$isFormContainsSubmit = ($isFormContainsSubmit || ($element->helper == 'formSubmit'));

		return $isFormContainsSubmit;
	}


	public function setupTableDecoratorsForForm($form) {
		$form
			->setDisplayGroupDecorators(array(
																				'FormElements',
																				array('HtmlTag', array('tag' => 'table')),
																				'Fieldset'))
			->removeDecorator('HtmlTag');
		

		foreach ($form->getElements() as $element)
			$element->setDecorators($this->_decoratorsForTableRendering($element));			

		return $this;
	}


	protected function _decoratorsForTableRendering($element) {
			$newDecorators = array();
			$decorators	= $element->getDecorators();

			foreach ($decorators as $name => $decorator) {
				$name = explode('_', $name);
				$name = end($name);
				$name = strtolower($name);

				switch ($name) {
				case 'label':
					$newDecorators[] = array(array('input_data' => 'HtmlTag'),
																	 array('tag' => 'td', 'class' => 'gauche'));
					$decorator->setOption('tag', 'td');
					$newDecorators[$name] = $decorator;
					$newDecorators[] = array('HtmlTag', array('tag' => 'tr'));
					break;

				case 'dtddwrapper':
					break;

				case 'viewhelper':
					$decorator->setOption('tag', 'td');
					$newDecorators[$name] = $decorator;
					break;

				default:
					$newDecorators[$name] = $decorator;
					break;
				}
			}

			return $newDecorators;
	}


	protected function _buttonsFor($id) {
		return "
		<table>
	    <tr>
        <td align='right'>".$this->view->bouton('type=V', "form=$id", "javascript=;setFlagMaj(false);")."</td>
        <td align='left'>".$this->view->bouton('id=29',
																							 'picto=del.gif',
																							 sprintf('texte=%s', $this->translate()->_('Annuler')),
																							 'url='.$this->view->url(array('action' => 'index')),
																							 'largeur=120px')."</td>
    	</tr>
    </table>";
	}
}