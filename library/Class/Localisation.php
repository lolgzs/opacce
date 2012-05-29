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

//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 : Plans et localisations des bibliotheques
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class Class_Localisation
{

	// ----------------------------------------------------------------
	// Localisations
	// ----------------------------------------------------------------
	public function getLocalisations($id_bib,$id_localisation=0)
	{
		if(!$id_localisation) $ret=fetchAll("select * from bib_localisations where ID_BIB=$id_bib order by LIBELLE");
		else $ret=fetchEnreg("select * from bib_localisations where ID_LOCALISATION=$id_localisation");
		return $ret;
	}

	// ----------------------------------------------------------------
	// Ecrire localisation
	// ----------------------------------------------------------------
	public function ecrireLocalisation($id_localisation,$enreg)
	{
		if(!$id_localisation) $id_localisation=sqlInsert("bib_localisations",$enreg);
		else sqlUpdate("update bib_localisations set @SET@ where ID_LOCALISATION=$id_localisation",$enreg);

		// Renommer l'image s'il y a lieu
		if(!$enreg["IMAGE"]) return true;
		$extension=explode(".",$enreg["IMAGE"]);
		$extension=$extension[count($extension)-1];
		$image="bib_".$enreg["ID_BIB"]."_localisation_".$id_localisation.".".$extension;
		if($image == $enreg["IMAGE"]) return true;
		$root=getcwd()."/userfiles/photobib/localisations/";
		rename($root.$enreg["IMAGE"], $root.$image);
		sqlExecute("update bib_localisations set IMAGE='$image' where ID_LOCALISATION=$id_localisation");
	}

	// ----------------------------------------------------------------
	// Supprimer localisations
	// ----------------------------------------------------------------
	public function deleteLocalisation($id_localisation)
	{
		// Supprimer l'image
		$image=fetchOne("select IMAGE from bib_localisations where ID_LOCALISATION=$id_localisation");
		$image=getcwd()."/userfiles/photobib/localisations/".$image;
		if(file_exists($image)) unlink($image);

		// Supprimer l'enreg
		sqlExecute("delete from bib_localisations where ID_LOCALISATION=$id_localisation");
	}

	// ----------------------------------------------------------------
	// Plans
	// ----------------------------------------------------------------
	public function getPlans($id_bib,$id_plan=0)
	{
		if(!$id_plan) $ret=fetchAll("select * from bib_plans where ID_BIB=$id_bib order by LIBELLE");
		else $ret=fetchEnreg("select * from bib_plans where ID_PLAN=$id_plan");
		return $ret;
	}

	// ----------------------------------------------------------------
	// Ecrire plan
	// ----------------------------------------------------------------
	public function ecrirePlan($id_plan,$enreg)
	{
		if(!$id_plan) $id_plan=sqlInsert("bib_plans",$enreg);
		else sqlUpdate("update bib_plans set @SET@ where ID_PLAN=$id_plan",$enreg);

		// Renommer l'image s'il y a lieu
		$extension=explode(".",$enreg["IMAGE"]);
		$extension=$extension[count($extension)-1];
		$image="bib_".$enreg["ID_BIB"]."_plan_".$id_plan.".".$extension;
		if($image == $enreg["IMAGE"]) return true;
		$root=getcwd()."/userfiles/photobib/plans/";
		rename($root.$enreg["IMAGE"], $root.$image);
		sqlExecute("update bib_plans set IMAGE='$image' where ID_PLAN=$id_plan");
	}

	// ----------------------------------------------------------------
	// Supprimer plan
	// ----------------------------------------------------------------
	public function deletePlan($id_plan)
	{
		// Supprimer l'image
		$image=fetchOne("select IMAGE from bib_plans where ID_PLAN=$id_plan");
		$image=getcwd()."/userfiles/photobib/plans/".$image;
		if(file_exists($image)) unlink($image);

		// Supprimer l'enreg
		sqlExecute("delete from bib_plans where ID_PLAN=$id_plan");
	}

	// ----------------------------------------------------------------
	// Rend les caracteristiques de l'image pour un plan
	// ----------------------------------------------------------------
	public function getImagePlan($id_plan)
	{
		$image=fetchOne("select IMAGE from bib_plans where ID_PLAN=$id_plan");
		$img = "/userfiles/photobib/plans/".$image;
    if ($image == "" or file_exists(getcwd().$img)==false) return false;
		$ret=getimagesize(getcwd().$img);
		$ret["url"]=BASE_URL.$img;
		return $ret;
	}

	// ----------------------------------------------------------------
	// Rend les données de localisation pour 1 exemplaire
	// ----------------------------------------------------------------
	public function getLocFromExemplaire($id_bib,$cote,$code_barres)
	{
		$erreur_not_found = Zend_Registry::get('translate')->_("Aucune donnée de localisation trouvée pour cet exemplaire");

		// Lire les localisations
		$localisations=$this->getLocalisations($id_bib);
		if(!$localisations) return array("erreur"=> $erreur_not_found);

		// Lire les données de l'exemplaire
		if($code_barres > '') $ex=fetchEnreg("select id_notice,cote,annexe,section,emplacement from exemplaires where code_barres='$code_barres' and id_bib=$id_bib");
		else $ex=fetchEnreg("select id_notice,cote,annexe,section,emplacement from exemplaires where cote='$cote' and id_bib=$id_bib");
		$ex["type_doc"]=fetchOne("select type_doc from notices where id_notice=".$ex["id_notice"]);

		// Analyse des regles
		foreach($localisations as $localisation)
		{
			if($localisation["TYPE_DOC"] and strpos(";".$localisation["TYPE_DOC"].";",";".$ex["type_doc"].";") === false) continue;
			if($localisation["ANNEXE"] and strpos(";".$localisation["ANNEXE"].";",";".$ex["annexe"].";") === false) continue;
			if($localisation["SECTION"] and strpos(";".$localisation["SECTION"].";",";".$ex["section"].";") === false) continue;
			if($localisation["EMPLACEMENT"] and strpos(";".$localisation["EMPLACEMENT"].";",";".$ex["emplacement"].";") === false) continue;
			if($localisation["COTE_DEBUT"] and substr($ex["cote"],0,strlen($localisation["COTE_DEBUT"])) < $localisation["COTE_DEBUT"]) continue;
			if($localisation["COTE_FIN"] and substr($ex["cote"],0,strlen($localisation["COTE_FIN"])) > $localisation["COTE_FIN"]) continue;
			$ok=true;
			break;
		}
		// Lecture des infos
		if(!$ok) return array("erreur"=>$erreur_not_found);
		$image=$this->getImagePlan($localisation["ID_PLAN"]);
		$ret["url"]=$image["url"];
		$ret["posX"]=$localisation["POS_X"];
		$ret["posY"]=$localisation["POS_Y"];
		if(trim($localisation["DESCRIPTION"])) $ret["description"]=$localisation["DESCRIPTION"];
		else $ret["description"]=$localisation["LIBELLE"];
		$ret["titre"]=$localisation["LIBELLE"];
		if($localisation["IMAGE"]) $localisation["IMAGE"]=BASE_URL."/userfiles/photobib/localisations/".$localisation["IMAGE"];
		$ret["photo"]=$localisation["IMAGE"];
		if(!$localisation["ANIMATION"]) $localisation["ANIMATION"]="genie.gif";
		$ret["animation"]=URL_ADMIN_IMG.'animation_plan/'.$localisation["ANIMATION"];
		return $ret;
	}
}
?>