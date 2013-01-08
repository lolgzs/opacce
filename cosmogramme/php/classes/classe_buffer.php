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
////////////////////////////////////////////////////////////////////////
// BUFFER d'affichage Pour les gros traitements 
// 	- Crée un fichier du nom de la session 
// 	- Mémorise des données html par pages 
////////////////////////////////////////////////////////////////////////

class buffer
{
	private $path;							// Chemin pour ecrire le fichier
	private $nomFic;						// Racine du nom du fichier (session id)
	private $fic;								// Handle du fichier
	private $taillePage;				// Taille max pour 1 page (ligne se terminant par <br>)
	private $page;							// Page courante en cours d'ecriture

	// Contructeur
	function __construct()
	{
		$this->taillePage=10000;
		$this->path=getVariable("cache_path");
		if(!$this->path) afficherErreur("La variable : cache_path n'est pas définie.");
		if( strRight($this->path,1) != "/" ) $this->path .="/";
		$this->nomFic = $this->path . "s". $_REQUEST["PHPSESSID"];
	}
	
	// Ouverture fichier et controle de l'historique
	public function open($page=false)
	{
		// Controle de l'historique	
		@$dir = opendir( $this->path) or AfficherErreur("Impossible d'ouvrir le dossier : " .$this->path);
		while(($file = readdir($dir)) !== false) 
		{
			if(is_file($this->path.$file))
			{
				$date_modif=filemtime($this->path.$file);
				$temps=time()-$date_modif;
				if($temps > (3600*24)) unlink($this->path .$file);
				elseif(strLeft($this->path.$file,strLen($this->nomFic)) == $this->nomFic)
				{
					if(!$page) unlink($this->path .$file);
				}
			}
		}
		closedir( $dir);
		if(!$page) $page=1;
		$this->page=$page;
		@$this->fic=fopen( $this->nomFic.".".$page, "a") or afficherErreur("Impossible d'ouvrir le fichier de bufferisation");
	}

	// Ecrire
	public function ecrire($texte,$forcerPage=false)
	{
		if( $forcerPage == true ) 
		{ 
			fclose($this->fic); 
			$page=$this->page+1; 
		}
		else
		{
			$page=$this->page;
			if(ftell($this->fic) + strlen($texte) > $this->taillePage)
			{
				if(strRight($texte,8) == '</table>') 
				{
					$page ++;
					fwrite($this->fic, $texte);
					fclose($this->fic);
					$texte="";
				}
			}
		}
		if($page != $this->page)
		{
			$this->page=$page;
			$nom = $this->nomFic.".".$page;
			$this->fic=fopen( $nom, "a");
		}
		fwrite($this->fic, $texte);
	}
	
	// Afficher le contenu du log
	public function afficher($page=1)
	{
		$nom=$this->nomFic.".".$page;
		if( !fileSize($nom))
		{
			print("Ce fichier est vide.");
			return false;
		}
		$fic=fopen($nom,"r");
		while (!feof($fic))
		{
      	$buffer = fread($fic, 4096);
      	print( $buffer);
      }
      flush();
      fclose($fic);
      
      // Pager
 		$nbPages=$this->getNombrePages();
      if($page < $nbPages) 
      {
      	$suivant=rendBouton("Page suivante",$_SERVER["PHP_SELF"],"action=AFFICHER&page=".($page+1));
      	$dernier=str_repeat("&nbsp;",5).rendBouton("Résumé de l'analyse",$_SERVER["PHP_SELF"],"action=AFFICHER&page=".$nbPages);
      }
      if($page > 1)
      {
      	$premier=rendBouton("Retour au début",$_SERVER["PHP_SELF"],"action=AFFICHER&page=1").str_repeat("&nbsp;",5);
      	$precedent=rendBouton("Page précédente",$_SERVER["PHP_SELF"],"action=AFFICHER&page=".($page-1)).str_repeat("&nbsp;",5);
      }
      print(BR.'</center><div style="width:700px;margin-left:20px;"><center>'.$premier.$precedent.$suivant.$dernier.'</div>'.BR.BR);
	}
	
	// Fermeture
	public function close()
	{
		fclose($this->fic);
		return $this->page;
	}
	
	private function getNombrePages()
	{
		$dir = opendir( $this->path);
		while(($file = readdir($dir)) !== false) 
		{
			if(is_file($this->path.$file))
			{
				if(strLeft($this->path.$file,strLen($this->nomFic)) == $this->nomFic)$nbPages++;
			}
		}
		closedir( $dir);
		return $nbPages;
	}
}

?>