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
//////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 : DEFINITION DES ROLES
//
// @TODO@ : fonctions a suppprimer : voir en bas de page
//////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_Acl_AdminControllerRoles extends Zend_Acl {
	const INVITE = 0;
	const ABONNE = 1;
	const ABONNE_SIGB = 2;
	const MODO_BIB = 3;
	const ADMIN_BIB = 4;
	const MODO_PORTAIL = 5;
	const ADMIN_PORTAIL = 6;
	const SUPER_ADMIN = 7;
	
	protected static $listeRole = array(
	'0' => array('abrege' => 'invite','libelle' => 'invité'),   													// 0 - Invité - Par default axx contenu.
	//	'1' => array('abrege' => 'abonne','libelle' => 'abonné'),															// 1 - Abonne (registered) - Utilisateur du site (fonctionnalité non pergame reservation par mail, etc...)
	'2' => array('abrege' => 'abonne_sigb','libelle' => 'abonné identifié SIGB'), 		// 2 - Abonne identifié dans 1 sigb - Utilisateur de site bib.( axx fonctionalité pergame)
	'3' => array('abrege' => 'modo_bib','libelle' => 'rédacteur bibliothèque'),				// 3 - Modo bib - Modo all MAIS que sur sa bib
	'4' => array('abrege' => 'admin_bib','libelle' => 'administrateur bibliothèque'),	// 4 - Admin de bib - Axx all MAIS que sur sa bib
	'5' => array('abrege' => 'modo_portail','libelle' => 'rédacteur portail'),				// 5 - Modo portail - Axx moderation tag / avis / fiche bib / cms / etc
	'6' => array('abrege' => 'admin_portail','libelle' => 'administrateur portail'),		// 6 - Admin portail - Axx all sauf traitement AFI
	'7' => array('abrege' => 'super_admin','libelle' => 'super administrateur'),					// 7 - SuperAdmin - Axx all
	);

 //----------------------------------------------------------------------------------
 // Contrusteur : init des autorisations basiques
 //----------------------------------------------------------------------------------
	public function __construct()
	{
		//All Admin Controllers OPAC 2
		$this->add(new Zend_Acl_Resource('agenda'));
		$this->add(new Zend_Acl_Resource('auth'));
		$this->add(new Zend_Acl_Resource('cms'));
		$this->add(new Zend_Acl_Resource('data'));
		$this->add(new Zend_Acl_Resource('error'));
		$this->add(new Zend_Acl_Resource('modo'));
		$this->add(new Zend_Acl_Resource('nouveaute'));
		$this->add(new Zend_Acl_Resource('panier'));
		$this->add(new Zend_Acl_Resource('planaccess'));
		$this->add(new Zend_Acl_Resource('rss'));
		$this->add(new Zend_Acl_Resource('sito'));
		$this->add(new Zend_Acl_Resource('menus'));
		$this->add(new Zend_Acl_Resource('catalogue'));
		$this->add(new Zend_Acl_Resource('accueil'));
		
		// Ressources reprise en OPAC 3
		$this->add(new Zend_Acl_Resource('index'));
		$this->add(new Zend_Acl_Resource('zone'));
		$this->add(new Zend_Acl_Resource('bib'));
		$this->add(new Zend_Acl_Resource('users'));
		$this->add(new Zend_Acl_Resource('usergroup'));
		$this->add(new Zend_Acl_Resource('formation'));
		$this->add(new Zend_Acl_Resource('profil'));
		$this->add(new Zend_Acl_Resource('stat'));
		$this->add(new Zend_Acl_Resource('lieu'));

		//Roles
		$this->addRole(new Zend_Acl_Role('invite'));
		$this->addRole(new Zend_Acl_Role('abonne'), 'invite');
		$this->addRole(new Zend_Acl_Role('abonne_sigb'), 'abonne');
		$this->addRole(new Zend_Acl_Role('modo_bib'),'abonne_sigb');
		$this->addRole(new Zend_Acl_Role('admin_bib'), 'modo_bib');
		$this->addRole(new Zend_Acl_Role('modo_portail'), 'admin_bib');
		$this->addRole(new Zend_Acl_Role('admin_portail'), 'modo_portail');
		$this->addRole(new Zend_Acl_Role('super_admin'), 'admin_portail');

		//Access Rules
		$this->allow('invite','auth');
		
		$this->allow('modo_bib','cms');
		$this->allow('modo_bib','modo');
		$this->allow('modo_bib','panier');
		$this->allow('modo_bib','rss');
		$this->allow('modo_bib','sito');
		$this->allow('modo_bib','agenda');
		$this->allow('modo_bib','index');
		$this->allow('modo_bib','stat');
		$this->allow('modo_bib','accueil');
		$this->allow('modo_bib','bib');

		$this->allow('admin_bib','users');
		$this->allow('admin_bib','profil');
		$this->allow('admin_bib','modo');
		$this->allow('admin_bib','menus');
		$this->allow('admin_bib','catalogue');

		$this->allow('modo_portail');
		$this->allow('admin_portail');
		$this->allow('super_admin');
	}


	public static function getListeRoles() {
		$roles = array();
		foreach(self::$listeRole as $level => $role)
			$roles[$level] = $role['libelle'];
		return $roles;
	}


	public static function getListeRolesWithoutSuperAdmin() {
		$roles = static::getListeRoles();
		unset($roles[static::SUPER_ADMIN]);
		return $roles;
	}

 //----------------------------------------------------------------------------------
 // Rend le libelle d'un role
 //----------------------------------------------------------------------------------
	public static function getLibelleRole($role_level)
 	{
 		return self::$listeRole[$role_level]["libelle"];
	}
	
	public static function getNomRole($role_level)
 	{
 		return self::$listeRole[$role_level]["abrege"];
	}
  
 //----------------------------------------------------------------------------------
 // Rend la combo des roles
 //----------------------------------------------------------------------------------
	public static function rendCombo($selected,$user_role_level,$tous=false)
	{
	$html[]='<select name="role">';
	if($tous==true)
	{
		$sel = ($selected=="") ? ' selected="selected"' : '';
		$html[]='<option value=""'.$sel.'>&nbsp;</option>';
	}
	foreach(self::$listeRole as $level => $role)	{
		if ($role["abrege"] == $selected or ($selected > "" and $level==(int)$selected )) $ligne='selected="selected"'; else $ligne="";
		if ($level <= $user_role_level and $level!=7) $html[]= '<option value="'.$level.'" '.$ligne.'>'. $role["libelle"] . '</option>';
	}
	$html[]='</select>';
	return (implode($html));
	}

	public static function rendNomRole($level_role)
	{
		return (self::$listeRole[$level_role]["abrege"]);
	}
	
}