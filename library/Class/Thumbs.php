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

class Class_Thumbs
{

	var $config = array(
		'album' => array(
				"max_width" => 9999,
				"max_height" => 9999,
				"thumb_max_width" => 160,
				"thumb_max_height" => 120
		)	
	);

	private $name;
	private $path_to_save;
	private $type;
	private $working_img;

	function __construct($path_to_img="",$type_img='avatar')
	{
		$this->type = $type_img;
		$this->name = str_replace(dirname($path_to_img).'/','',$path_to_img);
		$this->path_to_save = dirname($path_to_img).'/';
		$this->working_img = $path_to_img;
	}

	//---------------------------------------------------------------------
	// traite une image fabrique le thumb et retaille la grande image
	//---------------------------------------------------------------------
	public function traiteImage($adresse_fichier,$creer_thumb,$type)
	{
		// parametres du fichier
		if(file_exists($adresse_fichier)==false) return array("erreur_image"=>"fichier image non trouvé");

		// test si assez de memoire
		$ret=$this->testConsoMemoire($adresse_fichier);

		// Si pas assez de memoire : on degage
		if($ret["statut"]==false)
		{
			return false;
		}

		// Grande image
		$thumb = Thumbs_ThumbLib::create($adresse_fichier);
		if($this->config[$type]["max_width"] != 9999)
		{
			$thumb->resize($this->config[$type]["max_width"], $this->config[$type]["max_height"]);
			$thumb->save($adresse_fichier);
		}

		// thumb
		if($creer_thumb==true)
		{
			$fic=pathinfo($adresse_fichier);
			$path_thumbs=str_replace("/big","/thumbs",$fic["dirname"]);
			if(file_exists($path_thumbs)==false) mkdir($path_thumbs);
			$thumb->resize($this->config[$type]["thumb_max_width"], $this->config[$type]["thumb_max_height"]);
			$thumb->save($path_thumbs.'/'.$fic["basename"]);
		}

		// retour
		return $ret;
	}
	
	//---------------------------------------------------------------------
	// teste la capacité de la memoire pour traiter l'image
	//---------------------------------------------------------------------
	function testConsoMemoire($fic)
{
	// parametres
	$facteur_pixels=204;
	$marge_securite=100;
	
	// limite memoire 
	$mem_limit=ini_get("memory_limit");
	$mem_max=intval(str_replace("M","",$mem_limit))*1024;
	$mem_used=intval(memory_get_usage()/1024);
	$mem_dispo=($mem_max-$mem_used)-$marge_securite;
	
	// estimation memoire pour l'image
	$info=getimagesize ($fic);
	$pixels=$info[0]*$info[1];
	$mem_image=intval($pixels/$facteur_pixels);
	
	// si pas assez de memoire calcul de la taille pour retrecir
	if($mem_image<$mem_dispo) $ret["statut"]=true;
	else 
	{
		$ret["statut"]=false;
		$facteur=1.0;
		$new_mem=$mem_image;
		
		While($new_mem > $mem_dispo)
		{
			$facteur-=0.01;
			$new_witdh=intval($info[0]*$facteur);
			$new_height=intval($info[1]*$facteur);
			$new_mem=intval(($new_witdh*$new_height)/204);
		}
		$facteur=(1.0-$facteur)*100;
		$new_dim=intval($new_witdh)." x ".$new_height;
	}
	
	
	// retour
	$ret["fichier"]=$fic;
	$ret["memoire_php_ini"]=$mem_limit."o";
	$ret["memoire_utilisee"]=$mem_used.' Ko';
	$ret["memoire_disponible"]=$mem_dispo.' Ko';
	$ret["memoire_image"]=$mem_image.' Ko';
	$ret["dimentions_origine"]=$info[0]." x ".$info[1]." pixels";
	if($ret["statut"]==false)
	{
		$ret["dimentions_max"]=$new_dim." pixels";
		$ret["diminution"]=$facteur."%";
		$ret["msg"]="l'image est trop grande. Elle doit être réduite de ".$ret["diminution"].". ";
		$ret["msg"].="Sa taille d'origine est de ".$ret["dimentions_origine"].". ";
		$ret["msg"].="Sa nouvelle taille doit être au maximum de ".$ret["dimentions_max"];
	}
	return $ret;
}

	//---------------------------------------------------------------------
	// proportions pour un container sans deformer l'image
	//---------------------------------------------------------------------
	static function getProportionsAffichage($hauteur_max,$largeur_max,$adresse_img,$ne_pas_forcer=false)
	{
		// dimentions initiales de l'image
		$info=getimagesize($adresse_img);
		$largeur=$info[0];
		$hauteur=$info[1];
		if(!$largeur or !$hauteur) return "";

		// si option ne_pas_forcer et si l'image est plus petite on n'y touche pas
		if($ne_pas_forcer and $largeur <= $largeur_max and $hauteur <= $hauteur_max) return;

		// calcul du ratio
		$ratio_hauteur = $hauteur_max/$hauteur;
		$ratio_largeur = $largeur_max/$largeur;
		$ratio = min($ratio_hauteur, $ratio_largeur);

		// nouvelles dimentions
		$largeur = intval($ratio*$largeur);
		$hauteur = intval($ratio*$hauteur);

		// retour sous la forme html
		$html=' height="'.$hauteur.'px" width="'.$largeur.'px" ';
		return $html;
	}

	//---------------------------------------------------------------------
	// fabrique les thumbs pour tout un dossier
	// attention : les images doivent etre de type .jpg
	//---------------------------------------------------------------------
	function saveDossier()
	{
		// verifie si le dossier existe et cree le dossier thumbs
		if(file_exists($this->path_to_save)== false) return false;
		$controle=file_exists($this->path_to_save.'thumbs');
		if(file_exists($this->path_to_save.'thumbs')== false) mkdir($this->path_to_save.'thumbs');

		// parcourir le dossier
		$handle = opendir($this->path_to_save);
		if(!$handle) return false;
		while(false !== ($fic = readdir($handle)))
		{
			if(substr($fic,-4)!= ".jpg") continue;
			$this->working_img=$this->path_to_save.$fic;
			$this->name=$fic;
			$this->saveImg();
		}
		closedir($handle);
	}

	function saveImg()
	{
		// Grande image
		$thumb = Thumbs_ThumbLib::create($this->working_img);
		$thumb->resize($this->config[$this->type]["max_width"], $this->config[$this->type]["max_height"]);
		$thumb->save($this->path_to_save.$this->name);

		// thumb
		$thumb->resize($this->config[$this->type]["thumb_max_width"], $this->config[$this->type]["thumb_max_height"]);
		$thumb->save($this->path_to_save.'thumbs/'.$this->name);
	}

	function createThumb()
	{
		$thumb = Thumbs_ThumbLib::create($this->working_img);
		$thumb->resize($this->config[$this->type]["thumb_max_width"], $this->config[$this->type]["thumb_max_height"]);
		$thumb->save(getcwd().$this->path_to_save.'thumbs/'.$this->name);
	}

	function renameImage($new_name)
	{
		$this->name=$new_name;
	}

	function changeSaveDir($path_to_save)
	{
		$this->path_to_save = $path_to_save;
	}

}