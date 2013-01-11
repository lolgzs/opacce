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
//////////////////////////////////////////////////////////////////////////////////////
//                  FONCTIONS CHAINES DE CARACTERES
/////////////////////////////////////////////////////////////////////////////////////

function strRight( $chaine, $n )
{
	$len = strLen( $chaine );
	if( $n < 0 ) return substr( $chaine, -$n, $len );
	return subStr( $chaine, ($len-$n), $n );
}

function strLeft( $chaine, $n )
{

	return substr( $chaine, 0, $n<0?strLen($chaine)+$n:$n );
}

function strMid( $chaine, $deb, $len )
{
	return substr( $chaine, $deb, $len );
}

function strScan( $chaine, $cherche, $posDeb=0)
{
	$posdeb=0;
	if(!trim($cherche)) return -1;
	if( $posDeb >0 ) $chaine = strRight( $chaine, -$posDeb );
	$pos = strpos( $chaine, $cherche, $posdeb );
	if( $pos > 0 ) return $pos + $posDeb;
	if( strLeft($chaine, strLen($cherche)) == $cherche) return $posDeb;
	return -1;
}

function strScanReverse( $chaine, $cherche, $pos )
{
	$len = strLen($cherche);
	if( $pos == -1 ) $pos = strLen($chaine);
	for( $i=$pos; $i>=0; $i-- )
	{
		if( substr( $chaine, $i, $len ) == $cherche ) return $i;
	}
	return -1;
}

?>