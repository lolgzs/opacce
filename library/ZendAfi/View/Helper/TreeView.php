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

	/** Abstract_TreeViewRenderItem  */
	protected $_item_render_strategy;


	public function renderItemWithIconeSupport() {
		$this->_item_render_strategy = new TreeViewRenderItemWithIconeSupportStrategy($this->view);
		return $this;
	}

	/**
	 * @param array $elements
	 * @param array $containerActions
	 * @return string
	 */
	public function treeView(array $elements,
													 array $containerActions = array(),
													 array $itemActions = array(),
													 $withWorkflow = true) {
		$html = '';

		if (0 == count($elements)) {
			return $html;
		}

		$html .= '
		<div class="treeView">
			<label for="treeViewSearch">' . $this->view->_('Rechercher') . '</label> :
			<input type="text" size="20" class="treeViewSearch" id="treeViewSearch" />';

		if ($withWorkflow && Class_AdminVar::isWorkflowEnabled()) {
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
			$html .= '<h3><a href="#">' . $data['bib']->getLibelle() . '</a></h3><div>';
			if (array_key_exists('add_link', $data)) {
				$html .= $data['add_link'];
			}
			$html .= '<ul class="root">';

			foreach ($data['containers'] as $container)
				$html .= $this->_renderContainer($container);
		 
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

				xdebug_break();
		if ($container->hasChildren()) {
			$html .= '<ul style="display:none;" id="child-of-' . $container->getId() . '">';
			
			foreach ($container->getSousCategories() as $subContainer)
				$html .= $this->_renderContainer($subContainer);

			$items = $container->getItems();
			foreach ($items as $item)
				$html .= $this->_renderItem($item);
			$html .= '</ul>';
		}

		return $html .= '</li>';
	}


	public function getItemRenderStrategy() {
		if (isset($this->_item_render_strategy))
			return $this->_item_render_strategy;

		return $this->_item_render_strategy = new TreeViewRenderItemDefaultStrategy($this->view);
	}


	/**
	 * @param Storm_Model_Abstract $item
	 * @return string
	 */
	protected function _renderItem($item) {
		$html = $this->getItemRenderStrategy()->render($item);
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
		if ($container->isNew())
			return '';
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

			if (array_key_exists('caption', $action)) {
				$action['caption'] = $model->{$action['caption']}();
			}

			$action['url'] = sprintf($action['url'], $model->getId());

			if (isset($action['icon']) && is_a($action['icon'], 'Closure'))
				$action['icon'] = $action['icon']($model);

			$html .= $this->_renderAction($action);
		}

		return '<div class="actions">' . $html . '</div>';
	}

	/**
	 * @param array $options
	 * @return string
	 */
	protected function _renderAction(array $options) {
		$anchorOptions = array();
		if (array_key_exists('anchorOptions', $options)) {
			$anchorOptions = array_merge($anchorOptions, $options['anchorOptions']);
		}

		$content = $this->view->tagImg(URL_ADMIN_IMG . $options['icon'],
																	 array('alt' => $options['label'], 'class' => 'ico'));
		if (array_key_exists('caption', $options))
			$content .= $options['caption'];

		return $this->view->tagAnchor($options['url'], $content, $anchorOptions);
	}
}




abstract class Abstract_TreeViewRenderItem {
	protected $view;

	public function __construct($view) {
		$this->view = $view;
	}

	public function render($item) {}
}




class TreeViewRenderItemWithIconeSupportStrategy extends Abstract_TreeViewRenderItem {
	public function render($item) {
		return '<div>' . $this->view->iconeSupport($item->getTypeDocId()) . '</div>'
			. '<div class="item-label">' . $item->getTitre() . '</div>';
	}
}




class TreeViewRenderItemDefaultStrategy extends Abstract_TreeViewRenderItem {
	public function render($item) {
		return '<div>' . $this->view->tagImg(URL_ADMIN_IMG . 'ico/liste.gif') . '</div>'
			. '<div class="item-label">' . $item->getTitre() . '</div>';
	}
}

?>