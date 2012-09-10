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
  const NO_RESULT_MESSAGE = 'Aucun lien n\'a été trouvé';
 
  /**
   * Retourne les notices liées
   *
   * @param $model Class_Notice
   * @return string
   */
  public function frbr($model) {
    $this->notice_html = new Class_NoticeHtml();
    $sourceLinks = $model->getLinksAsSource();
    $targetLinks = $model->getLinksAsTarget();

    if (0 == count($sourceLinks) and 0 == count($targetLinks)) {
      
      return self::NO_RESULT_MESSAGE; }

    
    $html = '';
    foreach ($this->_getLinksBySourceTypes($sourceLinks) as $label => $links)
      $html .= $this->_getTargetTypeLinks($label, $links);
    
    foreach ($this->_getLinksByTargetTypes($targetLinks) as $label => $links)
      $html .= $this->_getSourceTypeLinks($label, $links);
    return $html;
  }
  

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
      
      $html .= '<div class="notice_info_titre">' . $label . '</div>';
      $notices = [];
      foreach ($links as $link)
	$notices[] = $callback($link);
      
      $html .= $this->notice_html->getListeNotices($notices, $this->view);
      
      return $html;
    }
  

}


?>
