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
// OPAC3 - Menu admin
//////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Admin_MenuGaucheAdmin extends ZendAfi_View_Helper_BaseHelper
{
	public function menuGaucheAdmin() {
		// User connecté
		$auth = Zend_Auth::getInstance();
		if ($auth->hasIdentity()) $this->user = Zend_Auth::getInstance()->getIdentity();

		$moderer = new Class_Moderer();
		$modstats = $moderer->getModerationStats();
		$demandes_inscription = $modstats['demandes_inscription']['count'];
		$nb_moderations = $modstats['avis_notices']['count'] + $modstats['avis_articles']['count'] + $modstats['tags_notices']['count'];

		$acl_all = array('super_admin', 'admin_bib', 'admin_portail', 'modo_bib', 'modo_portail');
		$acl_admins = array('super_admin', 'admin_bib', 'admin_portail');
		$acl_admins_portail = array('super_admin', 'admin_portail');
		$acl_super_admin = array('super_admin');

		// Menu Modules
		$menu_modules  = $this->openBoite($this->translate()->_("Gestionnaire de contenu"));
		$menu_modules .= $this->addMenu("cms.gif",				$this->translate()->_("Articles"),								"/admin/cms",								 $acl_all);
		$menu_modules .= $this->addMenu("catalogues.png",	$this->translate()->_("Catalogues dynamiques"),		"/admin/catalogue",					 $acl_admins);
		$menu_modules .= $this->addMenu("rss.gif",				$this->translate()->_("Fils RSS"),								"/admin/rss",								 $acl_admins);
		$menu_modules .= $this->addMenu("web.gif",				$this->translate()->_("Sitothèque"),							"/admin/sito",							 $acl_admins);
		if (Class_AdminVar::isBibNumEnabled()) {
			$menu_modules .= $this->addMenu("images.png",		$this->translate()->_("Bibliothèque numérique"),  "/admin/album",							 $acl_admins);
		}
		$menu_modules .= $this->addMenu("moderation.gif",	$this->translate()->_("Modération"),							"/admin/modo/",							 $acl_all,								 $nb_moderations);
    $menu_modules .= $this->addMenu("vcard.png",			$this->translate()->_("Demandes d'inscription"),	"/admin/modo/membreview",		 $acl_all,								 $demandes_inscription);
		$menu_modules .= $this->addMenu("mail.png",				$this->translate()->_("Lettres d'information"),		"/admin/newsletter",				 $acl_admins);

		if (Class_AdminVar::isFormationEnabled()) {
			$menu_modules .= $this->addMenu("formation.png",$this->translate()->_("Formations"),							"/admin/formation",					 $acl_admins);
		}

		$menu_modules .= $this->closeBoite();

		$menu_page  = $this->openBoite($this->translate()->_("Mise en page"));
		$menu_page .= $this->addMenu("ecran.png",			$this->translate()->_("Profils"),									"/admin/profil",				 $acl_admins);
		$menu_page .= $this->addMenu("ledred.png",		$this->translate()->_("Pictogrammes des genres"),	"/admin/profil/genres",	 $acl_admins);

		if (Class_AdminVar::isTranslationEnabled())
			$menu_page .= $this->addMenu("catalogues.png",		$this->translate()->_("Traductions"),	"/admin/i18n",	 $acl_admins);


		$menu_page .= $this->closeBoite();


		$menu_stat = $this->openBoite($this->translate()->_("Statistiques"));
		$menu_stat.=$this->addMenu("find.gif",						$this->translate()->_("Recherches infructueuses"),			"/admin/stat/rechercheinfructueuse",		 $acl_all);
		$menu_stat.=$this->addMenu("show.gif",						$this->translate()->_("Visualisations de notices"),			"/admin/stat/visunotice",								 $acl_all);
		$menu_stat.=$this->addMenu("portail.png",					$this->translate()->_("Palmarès des visualisations"),		"/admin/stat/palmaresvisunotice",				 $acl_all);
		//		$menu_stat.=$this->addMenu("stats.png",						$this->translate()->_("Réservations de notices"),				"/admin/stat/reservationnotice",				 $acl_all);
		//		$menu_stat.=$this->addMenu("localisation.gif",		$this->translate()->_("Palmarès des réservations"),			"/admin/stat/palmaresreservationnotice", $acl_all);
		$menu_stat.= $this->closeBoite();


		$menu_portail  = $this->openBoite($this->translate()->_("Administration du portail"));
		$menu_portail .= $this->addMenu("map.gif",				$this->translate()->_("Territoires"),		"/admin/zone",	 $acl_admins_portail);
		$menu_portail .= $this->addMenu("service.png",		$this->translate()->_("Bibliothèques"),	"/admin/bib",		 $acl_admins);
		$menu_portail .= $this->addMenu("abonnes.gif",		$this->translate()->_("Utilisateurs"),	"/admin/users",	 $acl_admins);

		if (Class_AdminVar::isFormationEnabled()) {
			$menu_portail .= $this->addMenu("usergroups.png",		$this->translate()->_("Groupes"),				"/admin/usergroup",	 $acl_admins);
		}
		$menu_portail .= $this->closeBoite();


		// Menu systeme (super admin)
		$menu_systeme  = $this->openBoite($this->translate()->_("Système"));
		$menu_systeme .= $this->addMenu("database.png",		$this->translate()->_("Accès à Cosmogramme"),			getVar("URL_COSMOGRAMME"),		 $acl_admins_portail);
    $menu_systeme .= $this->addMenu("systeme.png",		$this->translate()->_("Variables"),								"/admin/index/adminvar",			 $acl_super_admin);
		$menu_systeme .= $this->addMenu("tester.gif",			$this->translate()->_("Test des web-services"),		"/admin/systeme/webservices",	 $acl_super_admin);
		$menu_systeme .= $this->addMenu("images.png",			$this->translate()->_("Cache des images"),				"/admin/systeme/cacheimages",	 $acl_super_admin);
		$menu_systeme .= $this->addMenu("website.gif",		$this->translate()->_("Ressources OAI"),					"/admin/systeme/ressourcesoai",$acl_admins_portail);
		$menu_systeme .= $this->addMenu("chat.gif.png",		$this->translate()->_("Import avis opac2"),					"/admin/systeme/importavisopac2",$acl_super_admin);
		$menu_systeme .= $this->closeBoite();

		// Activation des menus en fonction du rôle
		$html_menu=$menu_modules;
		if (in_array($this->user->ROLE, $acl_admins)) $html_menu .= $menu_page;
		$html_menu .= $menu_stat;
		if (in_array($this->user->ROLE, $acl_admins)) $html_menu .= $menu_portail;
		if (in_array($this->user->ROLE, $acl_admins_portail)) $html_menu .= $menu_systeme;

		return $html_menu;
	}

//------------------------------------------------------------------------------------------------------
// Fonctions de constitution des menus
//------------------------------------------------------------------------------------------------------
	function openBoite($titre)
	{
		$html='<div class="menuGaucheAdmin">
			<table cellspacing="0" cellpadding="0">
				<tr>
					<td class="titre">'.$titre.'</td>
				</tr>
				<tr>
					<td class="contenuMenu"><ul class="menuAdmin">';
		return($html);
	}

	function closeBoite()
	{
		$html='</ul></td></tr></table></div>';
		return($html);
	}

	function addMenu($img,$titre,$lien, $acl, $extra_infos='')	{
		if (! in_array($this->user->ROLE, $acl)) return '';

		if(!preg_match('^http://^',$lien))
			$lien = BASE_URL.$lien;

    $ico = '<img src="'.URL_ADMIN_IMG.'picto/'.$img.'" alt="'.$titre.'" />';
		$url= array('START'=>'<a href="'.$lien.'">','END'=>'</a>');

		if (!empty($extra_infos))
			$extra_infos = "<span class='menu_info'>$extra_infos</span>";
		else
			$extra_infos = '';


		$class_selected = '';
		if (array_key_exists('REQUEST_URI', $_SERVER)
				and $_SERVER['REQUEST_URI'] == $lien) {
			$class_selected = 'class="selected"';
		}

		$html = '<li '.$class_selected.'>'.$ico.$url["START"].$titre.$url["END"].$extra_infos.'</li>';
		return ($html);
	}
}