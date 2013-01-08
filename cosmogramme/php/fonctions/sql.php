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
// OPAC3 - Fonctions sql simplifi�es
//////////////////////////////////////////////////////////////////////////////////////////

//------------------------------------------------------------------------------------------------------
// Fetch one (pour eviter de le reecrire 100 fois)
//------------------------------------------------------------------------------------------------------
function fetchOne($req)
{
	global $sql;
	return $sql->fetchOne($req);
}

//------------------------------------------------------------------------------------------------------
// Fetch enreg  : renvoie 1 enregistrement dans un tableau associatif
//------------------------------------------------------------------------------------------------------
function fetchEnreg($req,$num=false)
{
	global $sql;
	return $sql->fetchEnreg($req,$num);
}

//------------------------------------------------------------------------------------------------------
// Execute une requete
//------------------------------------------------------------------------------------------------------
function sqlExecute($req)
{
	global $sql;
	$sql->execute($req);
}

//------------------------------------------------------------------------------------------------------
// Fetch all (pour eviter de le reecrire 100 fois)
//------------------------------------------------------------------------------------------------------
function fetchAll($req,$num=false)
{
	global $sql;
	return $sql->fetchAll($req,$num);
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
function sqlUpdate($req,$data)
{
	global $sql;
	return $sql->update($req,$data);
}

// ---------------------------------------------------
// Requete insert
// ---------------------------------------------------
function sqlInsert($table,$data)
{
	global $sql;
	return $sql->insert($table,$data);
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