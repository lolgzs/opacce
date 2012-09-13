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

class ZendAfi_View_Helper_Frbr extends Zend_View_Helper_HtmlElement{
   protected $linksRenderer;

  /**
   * Retourne les notices liées
   *
   * @param $model Class_Notice
   * @return string
   */
  public function frbr($model) {
    $this->linksRenderer = $this->getLinksRenderer();
    $sourceLinks = $model->getLinksAsSource();
    $targetLinks = $model->getLinksAsTarget();

    if (0 == count($sourceLinks) and 0 == count($targetLinks)){
      $noResultMessage = $this->linksRenderer->returnNoResultMessage();
      return $noResultMessage;
      }
 
    
    $html = '';
    foreach ($this->_getLinksBySourceTypes($sourceLinks) as $label => $links)
      $html .= $this->_getTargetTypeLinks($label, $links);
    
    foreach ($this->_getLinksByTargetTypes($targetLinks) as $label => $links)
      $html .= $this->_getSourceTypeLinks($label, $links);

    if ('' == $html){
      $noResultMessage = $this->linksRenderer->returnNoResultMessage();
      return $noResultMessage;
    }

    return $html;}
    
  

    protected function _getLinksBySourceTypes($links) {
      return $this->_getLinksByType($links, function ($link) {
	  return $link->getTypeLabelFromSource();
	});
    }


    protected function _getLinksByTargetTypes($links) {
      return $this->_getLinksByType($links, function ($link) {
	  return $link->getTypeLabelFromTarget();
	});
    }


    protected function _getLinksByType($links, $callback) {
      $byTypes = [];
      foreach ($links as $link) {
	$typeLabel = $callback($link);
	if (!array_key_exists($typeLabel, $byTypes))
	  $byTypes[$typeLabel] = [];
	
	$byTypes[$typeLabel][] = $link;
      }
      return $byTypes;
    }


    protected function _getTargetTypeLinks($label, $links) {
      return $this->_getTypeLinks($label, $links, function($link) {
	  return $link->getTargetNotice();
	});
    }
    

    protected function _getSourceTypeLinks($label, $links) {
      return $this->_getTypeLinks($label, $links, function($link) {
	  return $link->getSourceNotice();
	});
    }


    protected function _getTypeLinks($label, $links, $callback) {
      $html = '';
      if (!$links)
	return $html;
      
      $html .= $this->linksRenderer->renderType($label);
      $notices = [];
      foreach ($links as $link) {
	if ($model = $callback($link))
	  $notices[] = $model;
      }

      if (empty($notices))
	return '';
      
      $html .= $this->linksRenderer->render($notices, $this->view);
      
      return $html;
    }


    public function getLinksRenderer(){
      return new FrbrNoticesOpacRenderer();
    }
}



class FrbrNoticesOpacRenderer {
  public function render($notices, $view){
    $noticeHtml = new Class_NoticeHtml();
    return $noticeHtml->getListeNotices($notices, $view);
  }


  public function renderType($type) {
    return '<div class="notice_info_titre">' . $type . '</div>';
  } 
  

  public function returnNoResultMessage() {
    return 'Aucun lien n\'a été trouvé';
  }
} 
?>
