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
////////////////////////////////////////////////////////////////////////
// LOGS
////////////////////////////////////////////////////////////////////////

class Class_log
{
	private $path;							// Chemin pour les logs
	private $fic;								// Handle du fichier
	private $typeLog;						// Type de fichier log (préfixe du nom de fichier)
	private $maxLog;						// Nombre maxi de fichiers pour 1 type de log
	private $afficher;					// Afficher à l'écran ou pas
	private $entete;						// Entetes pour les logs de type tableau

// ----------------------------------------------------------------
// Contructeur
// ----------------------------------------------------------------
	function __construct($typeLog,$afficher=true)
	{
		$this->typeLog = $typeLog;
		$this->afficher=$afficher;
		$this->path=getVariable("log_path");
		if(!$this->path) afficherErreur("La variable : log_path n'est pas définie.");
		if( strRight($this->path,1) != "/" ) $this->path .="/";
		$this->maxLog=getVariable("log_max");
		if(!$this->maxLog) $this->maxLog=100;

		// Entetes
		if($typeLog=="erreur") $this->entete=array("n°","Bibliothèque","Type d'opération","Erreur");
		elseif($typeLog=="warning") $this->entete=array("n°","Bibliothèque","Type d'opération","Alerte","Valeur");
	}

// ----------------------------------------------------------------
// Ouverture fichier
// ----------------------------------------------------------------
	public function open($append=0)
	{
		// Controle de l'historique
		if($append==0)
		{
			@$dir = opendir( $this->path) or AfficherErreur("Impossible d'ouvrir le dossier des logs : " .$this->path);
			while (($file = readdir($dir)) !== false)
			{
				if(strLeft($file, strLen($this->typeLog))== $this->typeLog) $liste[]=$file;
			}
			closedir( $dir);
			if( count($liste) > $this->maxLog )
			{
				$nb_destroy=count($liste)-$this->maxLog;
				sort($liste);
				for($i=0; $i < $nb_destroy; $i++)
				{
					unlink($this->path .$liste[$i]);
					if($this->typeLog != "sql") @unlink($this->path.str_replace($this->typeLog,"notice",$liste[$i]));
				}
			}
		}
		// Ouverture du nouveau log
		$nom = $this->path . $this->typeLog . "_" . dateDuJour(0) .".log";
		if($append == true )$mode="a"; else $mode="w";
		umask(0002);
		$this->fic=fopen($nom, $mode);
	}

// ----------------------------------------------------------------
// Ecrire
// ----------------------------------------------------------------
	public function ecrire($texte)
	{
		global $mode_cron;
		fwrite($this->fic, $texte ."\n");
		if($this->afficher == false) return false;
		if($mode_cron)
		{
			$texte=strip_tags($texte);
			if(trim($texte)) print($texte ."\n");
		}
		else print($texte);
		flush();
	}

// ----------------------------------------------------------------
// Taille des logs
// ----------------------------------------------------------------
	public function getTailleLogs()
	{
		// Parse directory
		@$dir = opendir( $this->path) or AfficherErreur("Impossible d'ouvrir le dossier des logs : " .$this->path);
		$taille=0;
		$nb_fic=0;
		while (($file = readdir($dir)) !== false)
		{
			$fic=$this->path.$file;
			if(!is_file($fic)) continue;
			$nb_fic++;
			$taille+=filesize($fic);
		}
		$ret["nb_fic"]=$nb_fic;
		$ret["taille"]=(int)($taille / 1024);
		$ret["taille"]=number_format($ret["taille"], 0, ',', ' ')." ko";
		return $ret;
	}

// ----------------------------------------------------------------
// Liste des logs
// ----------------------------------------------------------------
	public function rendListe()
	{
		// Parse directory
		@$dir = opendir( $this->path) or AfficherErreur("Impossible d'ouvrir le dossier des logs : " .$this->path);
		while (($file = readdir($dir)) !== false)
		{
			if(strLeft($file, strLen($this->typeLog))== $this->typeLog) $liste[]=$file;
		}
		closedir( $dir);
		// Liste triee
		if($liste) sort ($liste);
		$ret = array();
		for($i=count($liste); $i > 0; $i--)
		{
			$fic=$liste[($i-1)];
			$index=count($ret);
			$ret[$index]["taille"]=number_format((filesize($this->path.$fic) /1024),0, ',', ' ')." ko";
			$ret[$index]["fic"]=$fic;
			$date=str_replace($this->typeLog . "_", "", $fic);
			$date=str_replace(".log","",$date);
			$ret[$index]["date_sql"]=$date;
			$ret[$index]["date"]=rendDate($date,3);
		}
		return $ret;
	}

// ----------------------------------------------------------------
// Afficher le contenu du log mode texte
// ----------------------------------------------------------------
	public function afficher($log)
	{
		if($log == "") $log = $this->typeLog . "_" . dateDuJour(0) .".log";
		if( !fileSize($this->path.$log))
		{
			print("Ce fichier log est vide.");
			return false;
		}
		$fic=fopen($this->path.$log,"r");
		while (!feof($fic))
		{
      $buffer = fgets($fic, 4096);
      print( $buffer);
    }
    fclose($fic);
	}
// ----------------------------------------------------------------
// Afficher le contenu du log en mode tableau
// ----------------------------------------------------------------
	public function afficherTableau($log,$arg_bib="",$arg_type_erreur="")
	{
		if($log == "") $log = $this->typeLog . "_" . dateDuJour(0) .".log";
		if( !fileSize($this->path.$log))
		{
			print("Ce fichier log est vide.");
			return false;
		}
		// Entete du tableau
		print('<table class="blank" cellspacing="0"><tr>');
		foreach($this->entete as $col) print('<th class="blank">'.$col.'</th>');
		print('</tr>');

		// Parser les lignes
		$fic=fopen($this->path.$log,"r");
		while (!feof($fic))
		{
      $buffer = fgets($fic, 4096);
      $data=explode(chr(9),$buffer);
      // selection en mode detail
      if($arg_bib and trim($data[1]) != $arg_bib) continue;
  		if($arg_type_erreur and trim($data[3]) != $arg_type_erreur) continue;
      // Afficher ligne
      $nb++;
      if($nb > 2000) break;
      if($data[0])
      {
      	print('<tr>');
      	$prem=true;
      	foreach($data as $col)
        {
        	if($prem==true)
        	{
        		$nom_fic="notice".substr($log,strlen($this->typeLog));
        		$col=rendUrlImg("loupe.png", "analyse_afficher_notice.php","mode=LOG&adresse=".$col."&fichier=".$nom_fic);
        		$prem=false;
        	}
        	if(!trim($col)) $col="&nbsp;";
        	print('<td class="blank">'.$col.'</td>');
        }
        print('</tr>');
      }
    }
    fclose($fic);
    print('</table>');
    if($nb > 2000) print('<h3>Arrêt de l\'affichage à 2000 lignes</h3>');
	}
// ----------------------------------------------------------------
// rend le contenu du log en mode Synthese par bibliotheques
// ----------------------------------------------------------------
	public function getTableauSynthese($log)
	{
		if($log == "") $log = $this->typeLog . "_" . dateDuJour(0) .".log";
		if(!fileSize($this->path.$log))	return false;

		// Parser les lignes
		$fic=fopen($this->path.$log,"r");
		while (!feof($fic))
		{
      $buffer = fgets($fic, 4096);
      $data=explode(chr(9),$buffer);
      if($data[0])
      {
  			$bib=$data[1];
  			$erreur=trim($data[3]);
  			$table[$bib][$this->typeLog][$erreur]++;
      }
    }
    fclose($fic);
    return $table;
	}

// ----------------------------------------------------------------
// Fermeture
// ----------------------------------------------------------------
	public function close()
	{
		fclose($this->fic);
	}
}

?>