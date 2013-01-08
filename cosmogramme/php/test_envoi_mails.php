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
///////////////////////////////////////////////////////////////////
//
//         TEST DE L'ENVOI DES MAILS
//
////////////////////////////////////////////////////////////////////
include("_init_frame.php");
include("classe_mail.php");
?>

<h1>Test envoi mails</h1>


<?PHP

$courriel = $_POST["courriel"];
if (!isset($courriel))
	$courriel = getVariable("mail_admin");
?>


<form  action="<?php $_PHP_SELF ?>" method="POST">
	<label for="courriel">Envoyer un mail de test à cette adresse:</label>
	<input type="text" name="courriel"  size="40" value="<?php echo $courriel ?>"/>
	<input type="submit" value="Envoyer" />
</form>



<?php

if ($_POST["courriel"]) {
	echo "<h2>1. Envoi par la fonction mail</h2>";

	if (mail($courriel, "Test mail Cosmogramme 1", "Envoi par la fonction mail"))
		echo "<p>Le mail a été envoyé</p>";
	else 
		echo "<p>Echec à l'envoi du mail</p>";



	echo "<h2>2. Envoi par la fonction cosmogramme, vérification paramètres</h2>";
	$cmail = new classe_mail();
	if ($erreur = $cmail->sendMail($courriel, "Envoi par la fonction cosmogramme, vérification paramètres"))
		echo "<p>Erreur à l'envoi: $erreur</p>";
	else
		echo "<p>Le mail a été envoyé</p>";
}

?>