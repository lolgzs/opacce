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
class ZendAfi_View_Helper_Accueil_Newsletters extends ZendAfi_View_Helper_Accueil_AbonneAbstract  {
	protected $_boite_id = 'newsletters';

	public function getModels() {
		return Class_Newsletter::findAll();
	}

	public function renderModel($newsletter) {
		$start_li='<li>';
		$titre = $newsletter->getTitre();
		
		$button_subscribe = $this->view->tagAnchor(['controller' => 'abonne',
																								'action' => 'subscribe-newsletter',
																								'id' => $newsletter->getId()],
																							 $this->view->_("S'inscrire"));
		
		$button_unsubscribe = $this->view->tagAnchor(['controller' => 'abonne',
																									'action' => 'unsubscribe-newsletter',
																									'id' => $newsletter->getId()],
																								 $this->view->_('Se désinscrire'));

		foreach( $this->_abonne->getNewsletters() as $user_newsletter ) {
			if ($newsletter->getId() == $user_newsletter->getId()) 
				return  $start_li.$titre.' '.$button_unsubscribe.'</li>';
		}
		
		return $start_li.$titre.' '.$button_subscribe.'</li>';
	}

	public function isBoiteVisible() {
		return ( parent::isBoiteVisible()&&
						 Class_Newsletter::count()>0);
	}
}

?>