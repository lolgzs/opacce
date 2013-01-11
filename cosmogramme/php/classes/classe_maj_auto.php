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
//////////////////////////////////////////////////////////////////////////////////////
// CLASSE pour la table maj_auto
//////////////////////////////////////////////////////////////////////////////////////

class maj_auto
{
	private $clsbib;											// Classe bibliotheque
	
// ----------------------------------------------------------------
// Constructeur
// ----------------------------------------------------------------
	function __construct()
	{
		require_once("classe_bib.php");
		$this->clsbib=new bibliotheque();
	}
	
// ----------------------------------------------------------------
// Liste des programmations triées par bibliotheques
// ----------------------------------------------------------------
	public function listeProgrammation()
	{
		global $sql;
		$bibs=$this->clsbib->getAll();
		foreach( $bibs as $bib)
		{
			// Lire les programmations
			$id_bib=$bib["id_bib"];
			$progs=$sql->fetchAll("select * from int_maj_auto where id_bib = $id_bib order by rang,id_prog ");
			if( ! $progs ) continue;
			$bib["nom"]=$bib["nom_court"];
			$ret[$id_bib]=$bib;
			foreach( $progs as $prog) $ret[$id_bib]["prog"][]=$prog;
		}
		return $ret;
	}
// ----------------------------------------------------------------
// Déplacement des fichiers du dossier transferts vers integration
// ----------------------------------------------------------------
	public function transfertfichiersFtp()
	{
		global $log,$sql;
		// Init path
		$ftp_path=getVariable("ftp_path");
		$integration_path=getVariable("integration_path");
		if( ! $ftp_path) return "La variable ftp_path n'est pas définie";
		if( ! $integration_path) return "La variable integration_path n'est pas définie";
		$log->ecrire('<table class="blank" cellspacing="0" cellpadding="5px">');
		
		// Lire maj_auto
		$lignes=$sql->fetchAll("select * from int_maj_auto order by rang") ;
		foreach($lignes as $ligne) 
		{
			$id_bib = $ligne["id_bib"];
			$profil = $ligne["profil"];
			$type_operation = $ligne["type_operation"];
			$type_doc=$ligne["type_doc"];
			$log->ecrire( '<tr><td class="blank"><span class="bib">' . $this->clsbib->getNomCourt($id_bib) .'</span></td><td class="blank">'.$ligne["nom_fichier"].'</td><td class="blank">');
   		
   		// Déplacement du fichier
   		$ficFtp = $this->getNomFicUpload($ftp_path,$ligne["nom_fichier"]);
			$id_upload = getVariable("ID_upload")+1;
   		$ficNew = "integre".$id_upload.".pan";
   		if($ficFtp and is_file($ficFtp))
   		{
   			if(@rename($ficFtp, $integration_path.$ficNew) == true)
   			{
   				$log->ecrire('<span class="vert">transfert vers '.$ficNew .'</span></td>');
   				$date=dateDuJour(0);
   				$sql->execute("insert into integrations(id_bib,type_operation,profil,type_doc,date_transfert,fichier,traite) 
   									Values($id_bib,$type_operation,$profil,'$type_doc','$date','$ficNew','non')");
   				setVariable("ID_upload",$id_upload);
   			}
   			else
   			{ 
   				$log->ecrire('<span class="rouge">erreur au transfert du fichier</span></td>');
   				incrementeVariable("traitement_erreurs");
   			}
   		}
   		else $log->ecrire("pas de transfert</td>");
		}
		$log->ecrire('</table>');
	}

// ----------------------------------------------------------------
// Nom de fichier et recherche si le fichier commence par [DATE]
// ----------------------------------------------------------------
	public function getNomFicUpload($ftp_path,$nomfic)
	{
		if(!$ftp_path) $ftp_path=getVariable("ftp_path");

		if(strpos($nomfic,"[DATE]") === false) return $ftp_path.$nomfic;

		// parcourir le dossier pour chercher le fichier
		$nomfic=str_replace("[DATE]","",$nomfic);
		while(true)
		{
			if (false===$pos=strpos($nomfic,"/"))
						$pos=strpos($nomfic,"-");
			if($pos===false) break;
			$pos++;
			$ftp_path.=substr($nomfic,0,$pos);
			$nomfic=substr($nomfic,$pos);
		}
		$dir = opendir($ftp_path);
		while (($file = readdir($dir)) !== false)
		{
			if(strpos($file,$nomfic)===false) continue;
			$ret=$ftp_path.$file;
			break;
		}
		closedir($dir);
		return $ret;
	}

// ----------------------------------------------------------------
// Suppression des fichiers d'entete pergame
// ----------------------------------------------------------------
public function supprimerEntetesPergame()
{
	$ftp_path=getVariable("ftp_path");
	if(!$ftp_path) return false;
	global $sql;
	$lignes=$sql->fetchAll("select nom_fichier from int_maj_auto,int_bib where int_maj_auto.id_bib=int_bib.id_bib and sigb=1") ;
	for($i=0; $i<count($lignes); $i++)
	{
		$nom_fic=$ftp_path.$lignes[$i]["nom_fichier"];
		if(strpos($nom_fic,"site") === false) continue;
		$pos=0;
		while(true)
		{
			$pos=strpos($nom_fic,"/",($pos+1));
			if($pos === false) break;
			$pos1=$pos+1;
		}
		$nom_fic=substr($nom_fic,0,$pos1)."entete.cfg";
		@unlink($nom_fic);
	}
}

// ----------------------------------------------------------------
// Ecrire un enreg
// ----------------------------------------------------------------
	public function ecrire($id_prog,$id_bib,$libelle,$profil,$type_operation,$nomfichier,$rang,$type_doc)
	{
		global $sql;
		$libelle=addslashes($libelle);
		$nomfichier=addslashes($nomfichier);
		// Creation
		if($id_prog == 0)
		{
			$sql->execute("insert into int_maj_auto
			(id_bib,profil,libelle,type_operation,nom_fichier,type_doc,rang)
			Values($id_bib,$profil,'$libelle',$type_operation,'$nomfichier','$type_doc',$rang)");
		}
		// Update
		else
		{
			$req="Update int_maj_auto set
				profil=$profil,
				libelle='$libelle',
				type_operation=$type_operation,
				nom_fichier='$nomfichier',
				type_doc='$type_doc',
				rang=$rang
				Where id_prog=$id_prog";
				$sql->execute($req);
		}
	}
// ----------------------------------------------------------------
// Supprimer un enreg
// ----------------------------------------------------------------
	public function supprimer($id_prog)
	{
		global $sql;
		$sql->execute("delete from int_maj_auto Where id_prog=$id_prog");
	}
// ----------------------------------------------------------------
// Rend id_bib et id_prog du dernier créé
// ----------------------------------------------------------------
	public function getDerniereCreation()
	{
		global $sql;
		$id_prog=$sql->fetchOne("Select max(id_prog) from int_maj_auto");
		$id_bib=$sql->fetchOne( "Select id_bib from int_maj_auto where id_prog=$id_prog");
		$ret["id_prog"]=$id_prog;
		$ret["id_bib"]=$id_bib;
		return $ret;
	}
// ----------------------------------------------------------------
// Rend le rang de programmation le plus haut
// ----------------------------------------------------------------
	public function getRangMax()
	{
		global $sql;
		$rang=$sql->fetchOne("Select max(rang) from int_maj_auto");
		return ($rang + 10);
	}
}
?>