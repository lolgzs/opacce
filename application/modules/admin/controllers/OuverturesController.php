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

class Admin_OuverturesController extends ZendAfi_Controller_Action {
	public function getRessourceDefinitions() {
		return array(
								 'model' => array('class' => 'Class_Ouverture',
																	'name' => 'ouverture',
																	'order' => 'debut_matin'),
								 'messages' => array('successful_add' => 'Plage d\'ouverture %s ajoutée',
																		 'successful_save' => 'Plage d\'ouverture %s sauvegardée',
																		 'successful_delete' => 'Plage d\'ouverture %s supprimée'),

								 'actions' => array('edit' => array('title' => 'Modifier une plage d\'ouverture'),
																		'add'  => array('title' => 'Ajouter une plage d\'ouverture'),
																		'index' => array('title' => 'Plages d\'ouverture')),

								 'display_groups' => array('plage_ouverture' => array('legend' => 'Plage d\'ouverture',
																																			'elements' => array(
																																													'debut_matin' => array('element' => 'select',
																																																								 'options' => array('label' => 'Début matinée',
																																																																		'multiOptions' => Class_Multimedia_Location::getLoader()->getPossibleHours(30))
																																																								 )
																																													)
																																)
																					 )
								 );
	}

}

?>