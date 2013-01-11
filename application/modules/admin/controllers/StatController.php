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
//-----------------------------------------------------------------------
// OPAC3 - Statistiques notices
//-----------------------------------------------------------------------
class Admin_StatController extends Zend_Controller_Action
{
	private $cls_stat;											// Instance classe stat

	function indexAction()
	{
		$this->_redirect('admin/index');
	}

//-----------------------------------------------------------------------
// Initialisation
//-----------------------------------------------------------------------
	function init()
	{
		$this->cls_stat=new Class_StatsNotices();
		$css = '<link rel="stylesheet" type="text/css" media="screen" href="'.URL_ADMIN_CSS.'statistique.css" />';
		$this->getResponse()->setBody($css);
	}

//-----------------------------------------------------------------------
// Visualisations de notices
//-----------------------------------------------------------------------
	function visunoticeAction()
	{
		$this->view->titre = 'Statistiques de visualisation des notices';
		$this->view->periode=$this->cls_stat->getPeriode(0,0);
		$this->view->table_stat=$this->cls_stat->getRecapVisu();
	}
	
//-----------------------------------------------------------------------
// Palmares Visualisations de notices
//-----------------------------------------------------------------------
	function palmaresvisunoticeAction()
	{
		$this->view->titre = 'Palmarès des visualisations de notices';
		$this->view->periode = $this->cls_stat->getPeriode(0,0);
		$this->view->table_stat = $this->cls_stat->getPalmaresVisu($_REQUEST["type_doc"]);
	}
	
//-----------------------------------------------------------------------
// Recherches infructueuses
//-----------------------------------------------------------------------
	function rechercheinfructueuseAction()
	{
		$this->view->titre = 'Recherches infructueuses';
		$this->view->nb_par_page = 20;
		$page = $this->_getParam('page');
		if(!$page) $page=1;
		$this->view->page=$page;
		$limit=($page-1) * $this->view->nb_par_page;
		$limit=" limit ".$limit.",".$this->view->nb_par_page;
		$this->view->nombre_total=fetchOne("select count(*) from stats_recherche_echec");
		$req="select * from stats_recherche_echec order by id desc ".$limit;
		$this->view->liste = fetchAll($req);
	}

//-----------------------------------------------------------------------
// Réservations de notices
//-----------------------------------------------------------------------
	function reservationnoticeAction()
	{
		$this->view->titre = 'Statistiques des réservations de notices';
		$this->view->periode=$this->cls_stat->getPeriode(0,0);
		$this->view->table_stat=$this->cls_stat->getRecapReservation();
	}

//-----------------------------------------------------------------------
// Palmares Réservations de notices
//-----------------------------------------------------------------------
	function palmaresreservationnoticeAction()
	{
		$this->view->titre = 'Palmarès des réservations de notices';
		$this->view->periode=$this->cls_stat->getPeriode(0,0);
		$this->view->table_stat=$this->cls_stat->getPalmaresReservation($_REQUEST["type_doc"]);
	}
}