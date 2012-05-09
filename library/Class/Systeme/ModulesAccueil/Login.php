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
class Class_Systeme_ModulesAccueil_Login extends Class_Systeme_ModulesAccueil_Null{
	/** @var string */
	protected $_group = Class_Systeme_ModulesAccueil::GROUP_SITE;
	
	/** @var string */
	protected $_libelle = 'Boite de connexion';

	/** @var string */
	protected $_action = 'login';

	/** @var int */
	protected $_popupWidth = 570;

	/** @var int */
	protected $_popupHeight = 400;

	/** @var bool */
	protected $_isPhone = true;

	/** @var array */
	protected $_defaultValues = array(
		'titre' => "Se connecter",			// Titre de la boite
		'identifiant' => 'Identifiant',
		'identifiant_exemple' => '',    //texte du placeholder
		'mot_de_passe' => 'Mot de passe',
		'mot_de_passe_exemple' => '',    // texte du placeholder,
		'lien_connexion' => '» Se connecter',
		'lien_mot_de_passe_oublie' => '» Mot de passe oublié ?' 
	);
}
?>