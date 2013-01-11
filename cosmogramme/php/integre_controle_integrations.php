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
//////////////////////////////////////////////////
// CONTROLE DES INTEGRATIONS
//////////////////////////////////////////////////
include("_init_frame.php");
require_once("classe_bib.php");
$oBib = new bibliotheque();
require_once("classe_profil_donnees.php");
$cls_profil= new profil_donnees();

?>

<h1>Contrôle des intégrations</h1>
<div align="center">
	
<?PHP
////////////////////////////////////////////////////////////////////////////////////
// INTEGRATIONS D'UNE BIB
////////////////////////////////////////////////////////////////////////////////////

if( $_REQUEST["action"] == "1" )
{
	$id_bib = $_REQUEST["ID_bib"];

	print( '<div class="message_grand">' . $_REQUEST["nom"] . '</div>'.BR );
	print('<h3>Plannification : '.rend_plannification($id_bib).'</h3>');
	$controle=$sql->fetchOne("select count(*) from integrations where id_bib=$id_bib order by id DESC");
	if($controle==0)
	{
		print(BR.BR.'<h3 style="margin-left:30px">Cette bibliothèque n\'a jamais été intégrée.</h3>');
		print('</body></html>'); 
		exit;
	}

	// Entête
	print('<table style="width:600px;margin-left:20px">');
	print(' <tr>
 	<th width="20%" align="left">Jour</th>
 	<th width="20%" align="left">Date</th>
 	<th width="30%" align="left">Type de transaction</th>
	<th>Fichier</th>
	<th>Type</th>
	<th>Taille</th>
 	<th width="9%" align="left">Notices traitées</th>
 	<th width="9%" align="left">Erreurs</th>
 	<th width="9%" align="left">Anomalies</th>
	</tr>');

	// Lignes
	$req="select * from integrations where id_bib=$id_bib order by traite DESC LIMIT 0,300";
	$liste=$sql->fetchAll($req);
	foreach ($liste as $ligne) 
	{ 
    $infos=$cls_profil->getInfosFichierIntegration($ligne["id"]);
		if($ligne["traite"]=="non")
    {
    	$sql_date=rendDate($ligne["date_transfert"],1);
    	$jour=rend_jour_semaine($ligne["date_transfert"],"LONG");
    	$nb_notices="en&nbsp;cours...";
    }
    else
    {
    	$sql_date=rendDate($ligne["traite"],1);
    	$jour=rend_jour_semaine($ligne["traite"],"LONG");
    	$nb_notices=number_format($ligne["pointeur_reprise"], 0, ',', ' ');
    }
    if($jour=="") $jour="&nbsp;";
    $sql_type=getLibCodifVariable("import_type_operation",$ligne["type_operation"]);
			
		print ("<tr><td>$jour</td>");
		print ('<td align="center">'.$sql_date.'</td>');
		print ("<td>$sql_type</td>");
		print('<td align="left">'.$infos["fichier"].'</td>');
		print('<td align="left">'.$infos["type_fichier"].'</td>');
		print('<td align="right">'.$infos["taille"].'</td>');
		print ('<td align="right">'.$nb_notices.'</td>');
		print ('<td align="right">'.$ligne["nb_erreurs"].'</td>');
		print ('<td align="right">'.$ligne["nb_warnings"].'</td>');
		print("</tr>");
	}

	// Fini
	print('</table></div>');
	$bouton_retour='<input type="button" class="bouton" value="Retour" onclick="history.back()" style="margin-left:20px">';
	print(BR.$bouton_retour.BR.BR);
	print('</body></html>');	
}

////////////////////////////////////////////////////////////////////////////////////
// AFFICHAGE DE LA LISTE 
////////////////////////////////////////////////////////////////////////////////////
else
{
  print('<table width="700px">');
  print(' <tr>
    <th width="3%" align="center" valign="middle">&nbsp;</th>
    <th width="20%" align="left" valign="middle">Bibliothèque</th>
    <th width="23%" align="left" valign="middle">Plannification</th>
    <th width="4%" align="center" valign="middle">ok</th>
    <th width="25%" align="left" valign="middle">Diagnostic</th>
    <th width="25%" align="center" valign="middle">Dernière intégration</th>
    </tr>');

	// Affichage de la liste des Bibliothèques
	$bibs=$oBib->getAll();
	foreach($bibs as $ligne)
	{ 
    $id_bib=$ligne["id_bib"];
    $nom_court=$ligne["nom_court"];
    // Dernière date d'intégration
    $date=$sql->fetchOne("select max(traite) from integrations where id_bib=$id_bib");
    if($date == "non") $date_aff="&nbsp;";
    else $date_aff=rendDate($date,3);
		$ret=diagnostic($id_bib, $date);
		$statut=$ret[0];
		if($statut==-1) $img_statut='<img src="'.URL_IMG.'suppression.gif" border="0">';
		elseif($statut==0) $img_statut='<img src="'.URL_IMG.'alerte.gif" border="0">';
		else $img_statut='<img src="'.URL_IMG.'coche_verte.gif" border="0">';
		$diagnostic=$ret[1];
		$plannification=$ret[2];
		$url_detail="<a href=\"integre_controle_integrations.php?action=1&ID_bib=$id_bib&nom=$nom_court\"><img src=\"".URL_IMG."loupe.png\" alt=\"Historique des intégrations\" border=\"0\"></a>";

		print ('<tr>');
		print('<td align="center">'.$url_detail.'</td>');
		print ("<td>$nom_court</td>");
		print ("<td>$plannification</td>");
		print ("<td align=\"center\">$img_statut</td>");
		print ("<td align=\"left\">$diagnostic</td>");
		print ("<td align=\"center\">$date_aff</td>");
		print('</tr>');
		flush();
	}
	print ("</table>\n");
	print('<br><br>');
	$url="integre_plannification.php";
	print('<input type="button" class="bouton" value="Modifier la plannification" onclick="document.location=\''.$url.'\';">');
	
	print("</div><br><br></body></html>");
}

function diagnostic($id_bib, $date_maj)
{
	global $sql;
	// Lecture de la plannif
	$enreg=$sql->fetchEnreg("Select * from int_bib where id_bib=".$id_bib);
	$plannification=rend_plannification($id_bib);

	// Pas de date
	if($date_maj=="non") return array(1,"En cours d'intégration",$plannification);
	if(! $date_maj) return array(-1,"Jamais intégré",$plannification);
	if($plannification=="Aucune") return array(1,"&nbsp;",$plannification);
	
	// Calcul date presumee de derniere integration
	$hier= mktime(0, 0, 0, date("m")  , date("d")-1, date("Y"));
	$dt_integre=strtotime($date_maj);
	
	// Mode régulier
	if($enreg["planif_mode"]=="r")
	{
		//calcul de la date presumée d'integration
		for($i=0; $i<7;$i++)
		{
			$controle= mktime(0, 0, 0, date("m",$hier)  , date("d",$hier)-$i, date("Y",$hier));
			$jour=date("w",$controle)-1;
			if($enreg["planif_jours"]{$jour}=="1") {$ddd= date("Y m d",$controle); break;}
		}
		if($controle==$dt_integre) $ecart=0;
		else
		{
			$ecart=(($dt_integre-$controle)/86400);
			$ecart=(-$ecart)+1;
			$ecart=(int)$ecart;
		}
		if($ecart==0) return array(1,"Ok",$plannification);
		$msg=$ecart." jour";
		if(abs($ecart) > 1) $msg.="s";
		if($ecart<0)
		{ 
			$msg=str_replace("-","",$msg);
			$msg.= " en avance";
			return array(1,$msg,$plannification);
		}
		else 
		{
			$msg.= " de retard";
			return array(0,$msg,$plannification);
		}
	}	
	if($enreg["planif_mode"]=="i" && $enreg["planif_fois"]>0)
	{
		// calcul date en fonction de la plannif
		$nbj=0; $nbm=0; $nba=0;
		switch($enreg["planif_par"])
		{
			case "s": $nbj=7; break;
			case "q": $nbj=14; break;
			case "m": $nbm=1; break;
			case "a": $nba=1; break;
		}
		$controle= mktime(0, 0, 0, date("m")-$nbm, date("d")-$nbj, date("Y")-$nba);
		$date_controle=date("Y-m-d",$controle);
		// Lire dans la base
		$nb=$sql->fetchOne("select count(*) from integrations where id_bib=$id_bib and traite !='non' and date_transfert >='$date_controle'");
		// Retour
		if($nb==0) return array(0,"Jamais intégré",$plannification);
		$diff=$enreg["planif_fois"]-$nb;
		if($diff < 0) $msg=abs($diff)." de plus que prévu";
		else if($diff > 0) $msg=$diff." de moins que prévu";
		else $msg="OK";
		if($nb<$enreg["planif_fois"]) $statut=0; else $statut=1;
		return array($statut,$msg,$plannification);
	}
	
}

function rend_plannification($id_bib)
{
	global $sql;
	// Lecture de la plannif
	$enreg=$sql->fetchEnreg("Select * from int_bib where id_bib=".$id_bib);
	
	// Planif incorrecte
	if($enreg["planif_mode"]=="i" && $enreg["planif_fois"]==0) return "Aucune";
  else if($enreg["planif_mode"]=="r" && $enreg["planif_jours"]=="0000000") return "Aucune";
  
  // Calcul texte plannification
  if($enreg["planif_mode"]=="i" && $enreg["planif_fois"]>0)
  { 
  	$plannification = $enreg["planif_fois"]." fois par ";
  	if($enreg["planif_par"]=="s") $plannification .= "semaine";
  	else if($enreg["planif_par"]=="q") $plannification .= "quinzaine";
  	else if($enreg["planif_par"]=="m") $plannification .= "mois";
  	else if($enreg["planif_par"]=="a") $plannification .= "an";
  }
	else
	{
		$nb=0;
		for($i=0; $i<7; $i++)
		{
			if($enreg["planif_jours"]{$i}=="1")
			{
				$nb++;
				if($plannification > "") $plannification.=", ";
				switch($i)
				{
					case 0: $plannification.="Lu"; break;
					case 1: $plannification.="Ma"; break;
					case 2: $plannification.="Me"; break;
					case 3: $plannification.="Je"; break;
					case 4: $plannification.="Ve"; break;
					case 5: $plannification.="Sa"; break;
					case 6: $plannification.="Di"; break;
				}
			}
		}
		if($nb==1)
		{
			switch($plannification)
				{
					case "Lu": $plannification="Le Lundi"; break;
					case "Ma": $plannification="Le Mardi"; break;
					case "Me": $plannification="Le Mercredi"; break;
					case "Je": $plannification="Le Jeudi"; break;
					case "Ve": $plannification="Le Vendredi"; break;
					case "Sa": $plannification="Le Samedi"; break;
					case "Di": $plannification	="Le Dimanche"; break;
				}
			}
		}
		return $plannification;
}

function rend_jour_semaine($date,$format)
{
	if( ! $date) return "";
	$date=strtotime($date);
	if($date == false) return "";
	$jour=date("w",$date);
	switch($jour)
	{
		case 0: $j="Dimanche"; break;
		case 1: $j="Lundi"; break;
		case 2: $j="Mardi"; break;
		case 3: $j="Mercredi"; break;
		case 4: $j="Jeudi"; break;
		case 5: $j="Vendredi"; break;
		case 6: $j="Samedi"; break;
	}
	if($format == "COURT") $j=substr($j,0,2);
	return $j;
}

?>