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
//          PROGRAMMATION DES INTEGRATIONS AUTOMATIQUES
//
////////////////////////////////////////////////////////////////////
include("_init_frame.php");

require_once( "fonctions/objets_saisie.php");
require_once("classe_maj_auto.php");
require_once("classe_profil_donnees.php");

define('COM_PERGAME', 1);
define('COM_OPSYS', 2);
define('COM_Z3950', 3);
define('COM_VSMART', 4);
define('COM_KOHA', 5);
define('COM_CARTHAME', 6);
define('COM_NANOOK', 7);
define('COM_ORPHEE', 8);
define('COM_MICROBIB', 9);
define('COM_BIBLIXNET', 10);
define('COM_DYNIX', 11);

$maj=new maj_auto();
$oProfil=new profil_donnees();

?>

<h1>Configuration des intégrations programmées</h1>
 
<?PHP

$bib_deploy=$_REQUEST["id_bib"];

//---------------------------------------------------------------------------------
// MODIFICATION DE LA VALEUR
//---------------------------------------------------------------------------------

if($_REQUEST["action"]=="VALIDER")
{
	$id_bib=$_REQUEST["id_bib"];
	extract($_POST);

	// fiche bib
	if($_REQUEST["fiche"] == "oui")
	{
		// recup des parametres de communication 
		foreach($_POST as $clef => $valeur)
		{
			$clef_type="comp_".$comm_sigb."_";
			$lg=strlen($clef_type);
			if(substr($clef,0,$lg) != $clef_type) continue;
			$param=substr($clef,$lg);
			$comm_params[$param]=trim($valeur);
		}
		
		// ecriture
		require_once("classe_bib.php");
		$ficheBib = new bibliotheque();
		$ficheBib->ecrire($id_bib,$nom_court,$mail,$qualite,$ecart_ajouts,$sigb,$comm_sigb,$comm_params,$pas_exporter);
		$id_prog=0;
	}

// fiche programmation
	else
	{
		$id_prog=$_REQUEST["id"];
		$maj->ecrire($id_prog,$id_bib,$libelle,$profil,$type_operation,$fichier,$rang,$type_doc);
		if($id_prog == 0 )
		{ 
			$ret=$maj->getDerniereCreation();
			$id_bib=$ret["id_bib"];
			$id_prog=$ret["id_prog"];
		}
	}
	$goto_anchor='<script>window.location.hash="b'.$id_bib."p".$id_prog .'"</script>';
}

//---------------------------------------------------------------------------------
// SUPPRESSION
//---------------------------------------------------------------------------------
if($_REQUEST["action"]=="SUPPRIMER")
{
	$id_prog=$_REQUEST["id"];
	$maj->supprimer($id_prog);
}

//---------------------------------------------------------------------------------
// CREATION
//---------------------------------------------------------------------------------
if($_REQUEST["action"]=="CREER")
{
	require_once("classe_bib.php");
	$listeBib = new bibliotheque();
	$rangMax=$maj->getRangMax();
	
	print(BR.BR.'<div id="new" class="form" style="width:700px">');
	print('<form method="post" action="'.URL_BASE.'php/config_integrations.php?action=VALIDER&id_bib=0&id=0">');
	print('<table class="form" width="100%" cellspacing="0" cellpadding="5">');
	print('<tr><th class="form" colspan="2">Nouvelle programmation</th></tr>');
	printLigne("Bibliothèque",$listeBib->getComboNoms(),"form_first");
	printLigne("Description",getChamp("libelle","** nouvelle programmation **",45));
	printLigne("Nom du fichier (chemin complet)",getChamp("fichier","",45));
	printLigne("Rang dans la liste des traitements",getChamp("rang",$rangMax +1,3));
	printLigne("Type d'opération",getComboCodif("type_operation","import_type_operation",0));
	printLigne("Profil de données",$oProfil->getCombo(1));
	printLigne("Forcer un type de document",getComboTypeDoc(""));
	print('<tr><th class="form" colspan="2" align="center"><input type="submit" class="bouton" value="Valider"></th></tr>');
	print('</table></form></div>');
	exit;
}

//---------------------------------------------------------------------------------
// LISTE PAR BIBLIOTHEQUES
//---------------------------------------------------------------------------------

$liste = $maj->listeProgrammation();
if(! $liste) print(BR.BR."<h3>Il n'y a aucune programmation<h3>");
else
{
	print('<div class="liste">');
	foreach( $liste as $id_bib => $bib)
	{
		$img="plus.gif";
		$display="none";
		if($id_bib == $bib_deploy)
		{
			$img="moins.gif";
			$display="block";
		}
		print('<div class="liste_img">');
		print('<img id="I'.$id_bib.'" src="'.URL_IMG.$img.'" onclick="contracter_bloc(\''.$id_bib.'\')" style="cursor:pointer"></div>');
		print('<div class="liste_titre">'.$bib["nom"].' - '.$bib["id_bib"].'</div>');
		print('<div id="'.$id_bib.'" style="display:'.$display.'">');
		
		$bouton_modif='<input type="submit" class="bouton" value="Valider">';
		
		// fiche bibliotheque
		print('<div class="form" style="margin-bottom:10px;margin-left:20px">');
		print('<form method="post" action="'.URL_BASE.'php/config_integrations.php?action=VALIDER&id_bib='.$id_bib.'&fiche=oui">');
		print('<table class="form" width="100%" cellspacing="0" cellpadding="5">');
		print('<tr>');
		print('<th class="form" align="left" colspan="2"><a name="b'.$id_bib.'p0"><b>Paramètres bibliothèque</b></a></th></tr>');
		printLigne("Nom abrégé",getChamp("nom_court",$bib["nom"],45),"form_first");
		printLigne("Sigb",getComboCodif("sigb","sigb",$bib["sigb"]));
		printLigne("Code qualité des notices",getComboCodif("qualite","code_qualite",$bib["qualite"]));
		printLigne("Ecart maxi entre 2 intégrations",getChamp("ecart_ajouts",$bib["ecart_ajouts"],3));
		printLigne("Exclure des exports du catalogue",getComboSimple("pas_exporter",$bib["pas_exporter"],array("0"=>"non","1"=>"oui")));
		printLigne("Adresse E-mail",getChamp("mail",$bib["mail"],45));
		$blocs_params=getBlocsParams($id_bib,$bib["comm_sigb"],$bib["comm_params"]);
		$event="activerBlocCommBib('".$id_bib."',this.value)";
		printLigne("Mode de communication avec le sigb",getComboCodif("comm_sigb","comm_sigb",$bib["comm_sigb"],'onchange="'.$event.'"').$blocs_params);
		print('<tr><th colspan="2" class="form" align="center">'. $bouton_modif .'</th></tr>');
		print('</table></form></div>');	

		// Lignes
		$num=0;
		foreach( $bib["prog"] as $ligne)
		{
			$num++;
			$ligne["libelle"]=stripslashes($ligne["libelle"]);
			$bouton_suppr=rendBouton("Supprimer","config_integrations","action=SUPPRIMER&id_bib=$id_bib&id=".$ligne["id_prog"]);
			$anchor="b".$id_bib."p".$ligne["id_prog"];
			
			print('<div class="form" style="margin-bottom:10px;margin-left:20px">');
			print('<form method="post" action="'.URL_BASE.'php/config_integrations.php?action=VALIDER&id_bib='.$id_bib.'&id='.$ligne["id_prog"].'">');
			print('<table class="form" width="100%" cellspacing="0" cellpadding="5">');
			print('<tr>');
			print('<th class="form" align="left" colspan="2"><a name="'.$anchor.'"><b>'.$num.'</b></a>&nbsp;&nbsp;');
			print(getChamp("libelle",$ligne["libelle"],45).'</th></tr>');
			printLigne("Nom du fichier (chemin complet)",getChamp("fichier",$ligne["nom_fichier"],45),"form_first");
			printLigne("Rang dans la liste des traitements",getChamp("rang",$ligne["rang"],3));
			printLigne("Type d'opération",getComboCodif("type_operation","import_type_operation",$ligne["type_operation"]));
			printLigne("Profil de données",$oProfil->getCombo($ligne["profil"]));
			printLigne("Forcer un type de document",getComboTypeDoc($ligne["type_doc"]));
			print('<tr><th colspan="2" class="form" align="center">'. $bouton_modif .'&nbsp;&nbsp;&nbsp;'.$bouton_suppr.'</th></tr>');
			print('</table></form></div>');
		}
		print('</div>');
	}
}

// Bouton ajout
$bouton_ajout=rendBouton("Créer une nouvelle programmation","config_integrations","action=CREER&id_bib=0&id=0");
print('</div><br>'.$bouton_ajout.BR.BR);
print($goto_anchor);
print('</body></html>');

function printLigne( $masque, $valeur, $class="form")
{
	$template='<tr><td class="'.$class.'" align="right" width="48%" valign="top">@MASQUE@</td><td class="'.$class.'" align="left">@VALEUR@</td></tr>';
	$ligne=str_replace("@MASQUE@",$masque,$template); 
	$ligne=str_replace("@VALEUR@",$valeur,$ligne); 
	print($ligne);
}

function getComboTypeDoc($valeur)
{
	$data = array(""  => "",
								"a" => "Livre",
								"j" => "Disque",
								"g" => "Vidéo, diapo",
								"l" => "Cédérom",
								"c"	=> "Partition",
								"f"	=> "Carte, image"
								);
								
	$champ_valeur='<select name="type_doc">';
	foreach($data as $code => $libelle)
	{
		if($valeur==$code) $selected=" selected"; else $selected="";
		$champ_valeur.='<option value="'.$code.'"'.$selected.'>'.stripSlashes($libelle).'</option>';
	}
	$champ_valeur.='</select>';
	return $champ_valeur;
}

// Blocs des parametres de communication avec le sigb
function getBlocsParams($id_bib,$type,$valeurs)
{
	$valeurs=unserialize($valeurs);
	$types=getCodifsVariable("comm_sigb");
	foreach($types as $item)
	{
		$clef=$item["code"];
		if($clef == $type) $display="block"; else $display="none";
		$bloc.='<div id="comm_'.$id_bib.'_'.$clef.'" style="display:'.$display.'">';
		
		// Parametres en fonction du type
		unset($params);
		unset($champs_params);
		$titres[0]="Paramètres";

		if (in_array($clef, array(COM_VSMART, COM_KOHA, COM_CARTHAME, COM_NANOOK, COM_ORPHEE, COM_MICROBIB, COM_BIBLIXNET))) 
			$champs_params[0]=array("url_serveur");

		if ($clef==COM_OPSYS) $champs_params[0]=array("url_serveur", "catalogue_web");

		if ($clef==COM_DYNIX) $champs_params[0]=array("url_serveur", "client_id");

		if($clef==COM_Z3950) $champs_params[0]=array("url_serveur","login","password","nom_base");			// Serveur z39.50

		if($clef==COM_PERGAME) //pergame
		{
			$titres=array("Règles de réservations","Règles de prolongations");
			//$aide=array(BR."0=non,  1=oui");
			$champs_params[0]=array("Autoriser_docs_disponibles","Max_par_carte","Max_par_document");
			$champs_params[1]=array("Autoriser_prolongations","Interdire_si_reservation","Nombre_max_par_document","Duree_en_jours","Anteriorite_max_en_jours");
		}
		
		// valorisation
		for($i=0;$i<count($champs_params);$i++)
		{
			if($champs_params[$i])
			{
				$num_aide=0;
				$bloc.='<div style="margin-top:5px"><b>'.$titres[$i].'</b></div>';
				$bloc.='<table>';
				foreach($champs_params[$i] as $param)
				{
					if($clef == $type) $valeur=$valeurs[$param]; else $valeur="";
					$bloc.='<tr><td class="form">'.$param.$aide[$num_aide].'</td><td class="form">'.getChamp("comp_".$clef."_".$param,$valeur,30).'</td></tr>';
					$num_aide++;
				}
				$bloc.='</table>';

				if ($clef==COM_NANOOK)
				{
					$bloc .= "Format(0.8.7): ip:port/chemin_tomcat/ilsdi/nom_base <br/>";
					$bloc .= "Ex: 62.193.55.152:8080/afi_NanookWs/ilsdi/NANOOK <br/>";
					$bloc .= sprintf("<a target='_blank' href='tester_comm_nanook.php?url=%s'>Tester la communication</a>",htmlspecialchars($valeurs['url_serveur']));
				}
			}
		}
		$bloc.='</div>';
	}
	return $bloc;
}

?>