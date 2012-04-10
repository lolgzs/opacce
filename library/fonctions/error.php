<?php
/**
 * Copyright (c) 2012, Agence FranÃ§aise Informatique (AFI). All rights reserved.
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
function logErrorMessage($message) 
{ 
    echo '<b>keskil pass</b><br />'.$message.'<br />'; 
}

function traceDebug($trace,$exit=false)
{
	// Script et fonction
	$stack=debug_backtrace();
	$lig=$stack[1];
	print('<div style="margin-left:10px;margin-bottom:5px;margin-top:5px;border:1px solid;border-color:#E0E0E0;background-color:#CCFF99;padding:5px;text-align:left">');
	if($niveau==100) dump_array($stack);
	else
	{
		print("<b>Script : </b>". $lig["file"]);
		print(" - <b>Ligne : </b>". $lig["line"]);
		if($lig["class"]) print(" - <b>Classe : </b>". $lig["class"]);
		print(" - <b>Fonction : </b>". $lig["function"].BR);
		// Données
		if($trace)
		{
			$type=gettype($trace);
			if( $type == "array" or $type=="object") dump_array($trace);
			else print('<b>Message : </b>'.$trace.BR);
		}
	}
	print('</div>');
	flush();
	if($exit == true) exit;
}
?>