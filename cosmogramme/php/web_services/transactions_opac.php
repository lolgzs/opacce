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
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// TRANSACTIONS FAITES SUR L'OPAC
//
// Actions :
//		- GET : renvoie toutes les transactions en attente
//		- DONE : supprime les transactions intégrées par Pergame
//		- SET  : Integration d'une transaction (temps réel)
//
// ex d'appel : http://localhost/cosmogramme/php/web_services/transactions_opac.php?action=GET&admin_login=monopac&admin_pwd=monpassword
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
include("../_init_frame.php");

// classe de communication
include("classe_transaction_opac.php");
$transac=new transaction_opac();

// Appel fonction par service
switch($_REQUEST["action"])
{
	case "GET": $transac->sendTransactions(); break;
	case "DONE" : $transac->transactionDone($_REQUEST["id_mvt"]); break;
	case "SET" : break;
	default : $transac->erreur("Paramètre action incorrect"); break;
}
exit;

?>