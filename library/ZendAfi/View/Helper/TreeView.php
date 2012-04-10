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

class ZendAfi_View_Helper_TreeView extends Zend_View_Helper_Abstract {
	const NODE_CONTAINER = 'container';
	const NODE_ITEM			 = 'item';

	/** @var array */
	protected $_containerActions;

	/** @var array */
	protected $_itemActions;

	/**
	 * @param array $elements
	 * @param array $containerActions
	 * @return string
	 */
	public function treeView(array $elements,
														array $containerActions = array(),
														array $itemActions = array()) {
		$html = '';

		if (0 == count($elements)) {
			return $html;
		}

		$html .= '
		<div class="treeView">
			<label for="treeViewSearch">' . $this->view->_('Rechercher') . '</label> :
			<input type="text" size="20" class="treeViewSearch" id="treeViewSearch" />';

		if (Class_AdminVar::isWorkflowEnabled()) {
			$html .= '<div class="treeViewSearchStatus" style="margin:5px 0;float:right;">'
									. $this->view->_('Filtrer par statut : ');
			$statuses = array($this->view->tagAnchor(
												'#', $this->view->_('Tous'), array('rel' => 'status-all')
											));
			foreach (Class_Article::getKnownStatus() as $k => $v) {
				$statuses[] = $this->view->tagAnchor(
												'#', $v, array('rel' => 'status-' . $k)
											);
			}

			$html .= ' ' . implode(' - ', $statuses) . '</div>';

		}

		$html .= '<div class="tree">';

		$this->_containerActions = $containerActions;
		$this->_itemActions = $itemActions;

		foreach ($elements as $data) {
			$html .= '<h3><a href="#">' . $data['bib']->getLibelle() . '</a></h3>
				<div>'
						. $this->view->tagAnchor(
								$this->view->url(array(
																	'module' => 'admin',
																	'controller' => 'cms',
																	'action' => 'catadd',
																	'id_bib' => $data['bib']->getId()
																)),
								$this->view->tagImg(URL_ADMIN_IMG . 'ico/add_cat.gif')
										. $this->view->_(' Ajouter une catégorie')
						)
					.'<ul class="root">';

			foreach ($data['containers'] as $container) {
				$html .= $this->_renderContainer($container);
			}

			$html .= '</ul></div>';
		}

		return $html . '</div></div>';
	}

	/**
	 * @param Storm_Model_Abstract $container
	 * @return string
	 */
	protected function _renderContainer($container) {
		$html = '<li class="categorie">'
						. '<div>' . $this->view->tagImg(URL_ADMIN_IMG . 'ico/cat.gif') . '</div>'
						. '<div class="label">' . $this->view->tagAnchor(
													'#',
													$container->getLibelle() . ' <span class="count"></span>',
													array(
														'class' => 'containerTriggerer',
														'rel' => 'child-of-' . $container->getId()
													)
												) . '</div>'
						;

		$html .= $this->_renderContainerActions($container);

		if ($container->hasChildren()) {
			$html .= '<ul style="display:none;" id="child-of-' . $container->getId() . '">';

			foreach ($container->getSousCategories() as $subContainer) {
				$html .= $this->_renderContainer($subContainer);
			}

			foreach ($container->getItems() as $item) {
				$html .= $this->_renderItem($item);
			}

			$html .= '</ul>';
		}

		return $html .= '</li>';
	}

	/**
	 * @param Storm_Model_Abstract $item
	 * @return string
	 */
	protected function _renderItem($item) {
		$html = '<div>' . $this->view->tagImg(URL_ADMIN_IMG . 'ico/liste.gif') . '</div>'
						. '<div class="item-label">' . $item->getTitre() . '</div>';

		$html .= $this->_renderItemActions($item);

		return '<li class="item status-' . $item->getStatus() . '">' . $html . '</li>';
	}

	/**
	 * @param Storm_Model_Abstract $item
	 * @return string
	 */
	protected function _renderItemActions($item) {
		return $this->_renderActions(self::NODE_ITEM, $item);
	}

	/**
	 * @param Storm_Model_Abstract $container
	 * @return string
	 */
	protected function _renderContainerActions($container) {
		return $this->_renderActions(self::NODE_CONTAINER, $container);
	}

	/**
	 * @param string $type
	 * @param Storm_Model_Abstract $model
	 * @return string
	 */
	protected function _renderActions($type, $model) {
		$html = '';

		foreach ($this->{'_' . $type . 'Actions'} as $key => $action) {
			if (array_key_exists('condition', $action)){
				$methodName = $action['condition'];

				if (!$model->{$methodName}()) {
					continue;
				}
			}

			$action['id'] = $model->getId();

			$html .= $this->_renderAction($action);
		}

		return '<div class="actions">' . $html . '</div>';
	}

	/**
	 * @param array $options
	 * @return string
	 */
	protected function _renderAction(array $options) {
		$url = $this->view->url(
			array(
				'module'			=> $options['module'],
				'controller'	=> $options['controller'],
				'action'			=> $options['action'],
				(array_key_exists('idName', $options)) ? $options['idName'] : 'id'
					=> $options['id']
			),
			null,
			true
		);

		$anchorOptions = array();
		if (array_key_exists('anchorOptions', $options)) {
			$anchorOptions = array_merge($anchorOptions, $options['anchorOptions']);
		}

		return $this->view->tagAnchor(
			$url,
			$this->view->tagImg(URL_ADMIN_IMG . $options['icon'],
													array('alt' => $options['label'], 'class' => 'ico')),
			$anchorOptions
		);
	}
}
?>