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
// OPAC3 - Fonctions sql simplifiées
//////////////////////////////////////////////////////////////////////////////////////////

//------------------------------------------------------------------------------------------------------
// Fetch one (pour eviter de le reecrire 100 fois)
//------------------------------------------------------------------------------------------------------

function fetchOne($req)
{
	$afi_sql = Zend_Registry::get('sql');
	return $afi_sql->fetchOne($req);
}

//------------------------------------------------------------------------------------------------------
// Fetch enreg  : renvoie 1 enregistrement dans un tableau associatif
//------------------------------------------------------------------------------------------------------
function fetchEnreg($req,$num=false)
{
	$afi_sql = Zend_Registry::get('sql');
	return $afi_sql->fetchEnreg($req,$num);
}

//------------------------------------------------------------------------------------------------------
// Execute une requete
//------------------------------------------------------------------------------------------------------
function sqlExecute($req)
{
	$afi_sql = Zend_Registry::get('sql');
	$afi_sql->execute($req);
}

//------------------------------------------------------------------------------------------------------
// Fetch all (pour eviter de le reecrire 100 fois)
//------------------------------------------------------------------------------------------------------
function fetchAll($req,$num=false)
{
	$afi_sql = Zend_Registry::get('sql');
	if (!$result = $afi_sql->fetchAll($req,$num))
		return array();
	return $result;
}

// ---------------------------------------------------
// Renvoie un where a partir d'un array de conditions
// ---------------------------------------------------
function getWhereSql($conditions)
{
	if(!$conditions) return "";
	if(gettype($conditions)=="string") $conditions[0]=$conditions;
	foreach($conditions as $condition)
	{
		if(!trim($condition)) continue;
		if($where) $where.=" and ";
		$where.=$condition;
	}
	if(!$where) return "";
	$where=" where ".$where;
	return $where;
}

// ---------------------------------------------------
// Requete update
// ---------------------------------------------------
function sqlUpdate($req,$data,$force_quote=false)
{
	$afi_sql = Zend_Registry::get('sql');
	return $afi_sql->update($req,$data,$force_quote);
}

// ---------------------------------------------------
// Requete insert
// ---------------------------------------------------
function sqlInsert($table,$data,$force_quote=false)
{
	$afi_sql = Zend_Registry::get('sql');
	return $afi_sql->insert($table,$data,$force_quote);
}


// ---------------------------------------------------
// Renvoie une clause limt sql
// ---------------------------------------------------
function getLimitSql($nb_par_page,$page)
{
	if(!$page) $page=1;
	$limit = ($page-1) * $nb_par_page;
	$limit = " LIMIT ".$limit.",". $nb_par_page;
	return $limit;
}
?>