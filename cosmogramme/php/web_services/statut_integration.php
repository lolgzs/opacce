<?PHP
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
////////////////////////////////////////////////////////////////////////////////////////////
// RENVOIE LES DATES DE LA DERNIERE INTEGRATION
// ex d'appel : http://localhost/cosmogramme/php/web_services/statut_integration.php?admin_login=monopac&admin_pwd=monpassword
////////////////////////////////////////////////////////////////////////////////////////////
include("../_init_frame.php");


// Lire la derniere integration
$enreg=$sql->fetchEnreg("select date_transfert,traite from integrations where traite>'' order by id desc");
if(!$enreg)
{
	$enreg["date_transfert"]="vide";
	$enreg["traite"]="vide";
}
echo "transfert|".$enreg["date_transfert"]."|";
echo "integration|".$enreg["traite"];
exit;

?>