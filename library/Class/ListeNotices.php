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
// OPAC3 : Liste de notices
//////////////////////////////////////////////////////////////////////////////////////////

class Class_ListeNotices
{
	public $nb_par_page;								// Nombre par pages
	private $champs;										// Champs a recuperer
	private $notice;										// Instance de la classe notice

//------------------------------------------------------------------------------------------------------
// Constructeur
//------------------------------------------------------------------------------------------------------
	function __construct($nb_par_page,$champs)
	{
		$this->nb_par_page=$nb_par_page;
		if(!$this->nb_par_page) $this->nb_par_page=10;
		$this->champs=$champs;
		$this->notice=new Class_Notice();
	}

//------------------------------------------------------------------------------------------------------
// Execute une requete notices et rend 1 page avec les champs paramétrés dans les préférences
//------------------------------------------------------------------------------------------------------
	function getListe($req, $page=1)	{
		if (!$req)
			return ['statut' => 'erreur',
							'erreur' => 'Aucune notice trouvée',
							'nb_mots' => 1];

		if (!$page) $page=1;

		$ret =  array();

		$fin_limit = 0;
		if(strpos($req," LIMIT ") === false)
		{
			$limit = ($page-1) * $this->nb_par_page;
			$limit = " LIMIT ".$limit.",". $this->nb_par_page;
			$req.=$limit;
		}
		else
		{
			$debut_limit=($page-1) * $this->nb_par_page;
			$fin_limit=$this->nb_par_page;
		}

		// Execute la requete
		if (!$ids=fetchAll($req))
			$ids = array();

		if($fin_limit) $ids=array_slice($ids, $debut_limit, $fin_limit);

		foreach($ids as $lig)
			$ret[]=$this->notice->getNotice($lig["id_notice"],$this->champs);

		return $ret;
	}

}