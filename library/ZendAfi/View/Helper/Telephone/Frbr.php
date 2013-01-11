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
class ZendAfi_View_Helper_Telephone_Frbr extends ZendAfi_View_Helper_Frbr {
  public function frbr($model) {
    return '<ul data-role="listview">' . parent::frbr($model) . '</ul>';
  }


  public function getLinksRenderer() {
    return new FrbrNoticesTelephoneRenderer();
  }
}

class FrbrNoticesTelephoneRenderer {

  public function render($notices, $view) {
    $html = '';
    foreach ($notices as $notice) {
      $titrePrincipal = $notice->getTitrePrincipal();
      $auteurPrincipal = $notice->getAuteurPrincipal();
      $id_notice = $notice->getId();
      $url =  $view->urlNotice($notice);
      $img = Class_WebService_Vignette::getUrl($id_notice);

      $html .= '<li date-theme="c"><a href="' . $url . '" data-transition="slide"><img src="' . $img['vignette']  . '"><h3> ' . $titrePrincipal . '</h3><p>'. $auteurPrincipal .  '</p></a></li>';
    }
    return $html;
  }


  public function renderType($type) {
    return '<li data-role="list-divider">' . $type . '</li>';
  }

  
  public function returnNoResultMessage() {
    return '<li data-role="list-divider">' . 'Aucun lien n\'a été trouvé' . '</li>';
  }
}

  

?>