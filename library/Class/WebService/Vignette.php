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
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//  OPAC3: Vignettes documents 
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class Class_WebService_Vignette
{

//------------------------------------------------------------------------------------------------------
// Clef de controle anti hack
//------------------------------------------------------------------------------------------------------  
	static function getClefControle()
	{
		$clef="IMG".date("DxzxYxM")."VIG";
		return md5($clef);
	}

//------------------------------------------------------------------------------------------------------
// Cherche l'image dans les web-services et renvoie une url
//------------------------------------------------------------------------------------------------------
	public function getImage($id_notice,$cache=false)
	{
		// Chercher dans le cache
		$url=fetchOne("select url_vignette from notices where id_notice=".$id_notice);
		if($url) 
		{
			if($url == "NO")
			{
				$cls_notice=new Class_Notice();
				$notice=$cls_notice->getNotice($id_notice,"T");
				$fic=$this->saveImage($notice["id_notice"],$notice["T"],$notice["type_doc"]);
				return BASE_URL.$fic;
			}
			else
			{ 
				if($cache==true) $url=$this->writeImageCache($id_notice,$url);
				return $url;
			}
		}
		// Pas dans le cache on la cherche
		$cls_notice=new Class_Notice();
		$notice=$cls_notice->getNotice($id_notice,"TA");
		$urls=$this->getUrls($notice);
		if($urls)
		{ 
			sqlExecute("update notices set url_vignette='".$urls["vignette"]."', url_image='".$urls["image"]."' where id_notice=$id_notice");
			if($cache==true) $urls["vignette"]=$this->writeImageCache($id_notice,$urls["vignette"]);
			return $urls["vignette"];
		}
		
		// url pas trouvée -> on cree une image avec le titre
		sqlExecute("update notices set url_vignette='NO',url_image='NO' where id_notice=$id_notice");
		$fic=$this->saveImage($notice["id_notice"],$notice["T"],$notice["type_doc"]);
		return BASE_URL.$fic;
	}

//------------------------------------------------------------------------------------------------------
// Url ajax
//------------------------------------------------------------------------------------------------------  
	static function getUrl($id_notice,$js=true)
	{
		// Chercher dans la base de données
		$url=fetchOne("select url_vignette from notices where id_notice=".$id_notice);
		if($url) 
		{
			if($url == "NO")
			{ 
				$ret["vignette"]=URL_ADMIN_IMG."supports/vignette_vide.gif";
				$url_image="";
			}
			else 
			{
				$ret["vignette"]=$url;
				$url_image=fetchOne("select url_image from notices where id_notice=".$id_notice);
			}
		}
		else
		{
			// Url de retour pour chercher avec les web services
			$clef_controle=Class_WebService_Vignette::getClefControle();
			$ret["vignette"]= BASE_URL."/recherche/vignette?clef=".$clef_controle."&id_notice=".$id_notice;
			$url_image="";
		}
		
		if($js==true) $ret["image"]= "afficher_image('".$url_image."');";
		else $ret["image"]=$url_image;
		return $ret;
	}

//------------------------------------------------------------------------------------------------------
// Nom de la source pour bulle sur vignette
//------------------------------------------------------------------------------------------------------
	static function getSource($url)
	{
		// règles de reconnaissance
		$defaut="Serveur Afi";
		$sources=array
		(
			"Amazon"=>"amazon",
			"Decitre"=>"decitre",
			"Last-fm"=>"lastfm",
			"Deezer"=>"deezer",
			"Virgin"=>"virginmega",
			"Comme-au-cinema"=>"commeaucinema",
			"Fan-de-cinema"=>"fan-de-cinema",
			"Premiere"=>"premiere"
		);
		
		// renvoyer la source
		if(!$url) return $defaut;
		foreach($sources as $key => $valeur)
		{
			if(stripos($url, $valeur) > 0) return $key;
		}
		return $defaut;
	}

//------------------------------------------------------------------------------------------------------
// Rend le contenu binaire d'une image au navigateur
//------------------------------------------------------------------------------------------------------    
	public function getFluxImage($clef_controle,$id_notice)
	{
		// Verif clef de controle
		if($clef_controle != Class_WebService_Vignette::getClefControle()) return false;
		
		// lire la notice
		$id_notice=abs((int)$id_notice);
		$cls_notice=new Class_Notice();
		$notice=$cls_notice->getNotice($id_notice,"TA");
		if(!$notice) return false;
		
		// Chercher dans les web-services
		$urls=$this->getUrls($notice);
		if($urls)
		{ 
			sqlExecute("update notices set url_vignette='".$urls["vignette"]."', url_image='".$urls["image"]."' where id_notice=$id_notice");
		
			// Http request
			$httpClient = Zend_Registry::get('httpClient');
			$httpClient->setUri($urls["vignette"]);
			$response = $httpClient->request();
			$data = $response->getBody();
		}
		else
		{
			sqlExecute("update notices set url_vignette='NO',url_image='NO' where id_notice=$id_notice");
			$img=$this->createimage("",100,90,$notice["type_doc"]);
			header("Content-type: image/jpeg");
			imagejpeg($img);
			exit;
		}
		
		// On envoie le flux de l'image
		if(strpos(substr($data,0,10),"PNG") !== false)	header ("Content-type: image/png");
		if(strpos(substr($data,0,10),"GIF") !== false)	header ("Content-type: image/gif");
		else header("Content-type: image/jpeg");  // @TODO : gerer les images vides des differents fournisseurs
		print($data);
		exit;
	}

//------------------------------------------------------------------------------------------------------
// Rend les urls pour la vignette et la grande image
//------------------------------------------------------------------------------------------------------    
	public function getUrls($notice)
	{
		// Decouper les arguments
		$isbn=$notice["isbn"];
		if($isbn) $isbn=Class_IsbnEan::getIsbn10($isbn);
		$ean=$notice["ean"];
		$titre=$notice["T"];
		$auteur=$notice["A"];
		$type_doc=$notice["type_doc"];
		if($type_doc==2)
		{
			$cls_notice=new Class_Notice();
			$notice=$cls_notice->getDataSerie($notice["id_notice"]);
			$titre=$notice["clef_chapeau"];
			$numero=$notice["tome_alpha"];
		}
		
		$args=array("titre"=>$titre,"auteur"=>$auteur,"isbn"=>$isbn,"ean"=>$ean,"type_doc"=>$type_doc,"numero"=>$numero);
		$response=Class_WebService_AllServices::runServiceAfi(10,$args);
		if($response["statut_recherche"]==2)
		{
			$ret["vignette"]=$response["vignette"];
			$ret["image"]=$response["image"];
			return $ret;
		}
		else return false;
	}
 
//------------------------------------------------------------------------------------------------------
// Crée l'image avec le titre et la sauvegarde dans le dossier temp
//------------------------------------------------------------------------------------------------------  
	private function saveImage($id_notice,$titre,$type_doc)	{
		$path = getcwd();
		$nom_fic = "/temp/vignettes_titre/notice_".$id_notice.".png";
		if(file_exists($path.$nom_fic)==false) {
			$image=$this->createImage($titre,$width=100,$height=90,$type_doc);
			imagepng($image, $path.$nom_fic);
		}
		return $nom_fic;
	}


	static function deleteVignetteCacheForNotice($id) {
    $vignette_cache = PATH_TEMP.'vignettes_titre/notice_'.$id.'.';
    foreach(['jpg', 'png'] as $ext) {
      $filepath = $vignette_cache.$ext;
 		  if (file_exists($filepath)) unlink($filepath);
    }
	}
  
//------------------------------------------------------------------------------------------------------
// Prend l'image sur internet et la met en cache (pour contourner le pb des objets flash)
//------------------------------------------------------------------------------------------------------ 
	private function writeImageCache($id_notice,$url)
	{
		$path=PATH_TEMP."vignettes_titre/";
		$nom_fic="notice_".$id_notice.".jpg";
		try {
			if(file_exists($path.$nom_fic)==false)
				{ 
					$httpClient = Zend_Registry::get('httpClient');
					$httpClient->setUri($url);
					$response = $httpClient->request();
					$data = $response->getBody();
					file_put_contents($path.$nom_fic,$data);
				}
		} catch(Exception $e) {
			return null;
		}
		$url="../vignettes_titre/".$nom_fic;
		return $url;
	}
  
//------------------------------------------------------------------------------------------------------
// Crée l'image avec le titre
//------------------------------------------------------------------------------------------------------    
	private function createImage($titre,$width=100,$height=110,$type_doc)
  {
		$image=imagecreatetruecolor($width,$height);
		
		// Couleur de fond
		$fond = imagecolorallocate($image, 230, 230, 230);
		imagefill($image, 0, 0, $fond);
		
		// Titre
		if($titre > "") $image=$this->writeText($titre,$image);
		return $image;
	}
    
//------------------------------------------------------------------------------------------------------
// Ecrit le texte
//------------------------------------------------------------------------------------------------------
	private function writeText ($titre, $image_obj, $position = 0)
	{
		// Tronçonner le texte
		$texte=wordwrap(utf8_decode(str_replace('<br />', ';', $titre)), 12, ';');
		$texte=explode(";", $texte);

		
		// Parametres
		$font = 3;
		$couleur = imagecolorallocate($image_obj,212, 65, 0);
		$pos_x=0;
		$pos_y=2;
		$hauteur=15;
		$largeur=100;
		
		// Afficher
		foreach($texte as $ligne)
		{
			$pos_x=($largeur/2)-(strlen($ligne) * 3.5);
			imagestring($image_obj,$font, $pos_x, $pos_y, $ligne, $couleur);
			$pos_y+=$hauteur;
		}
		return $image_obj;
	}
}
