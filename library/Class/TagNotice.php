<?php
/**
 * Copyright (c) 2012, Agence FranÃ§aise Informatique (AFI). All rights reserved.
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
//////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Tags utilisateurs
//////////////////////////////////////////////////////////////////////////////////////////

class Class_TagNotice
{
	
// ----------------------------------------------------------------
// Rend une liste pour un champ suggestion
// ----------------------------------------------------------------
	public function getListeSuggestion($recherche,$mode,$limite_resultat)
	{
		// Transformer en code alpha
		$ix=new Class_Indexation();
		$recherche=$ix->alphaMaj($recherche);
		
		// Lancer la recherche
		if($mode=="1") $condition="like '".$recherche."%'";
		if($mode=="2") $condition="like '%".$recherche."%'";
		$req="select id_tag,libelle from codif_tags where code_alpha ".$condition." order by code_alpha limit ".$limite_resultat;
		$resultat=fetchAll($req,true);
		return $resultat;
	}

//------------------------------------------------------------------------------------------------------ 
// Creer un nouveau tag
//------------------------------------------------------------------------------------------------------ 
	public function creer_tag($tag, $id_notice)
	{
		$sql = Zend_Registry::get('sql');
		$mot=trim($sql->quote($tag));
		if(strlen_utf8($tag)< 2) return false;
	
		// Controle table de codif
		$id_tag=$this->getIdTag($tag);
		$code_facette=" Z".$id_tag;
		
		// Controle si deja dans la notice
		$req="select facettes from notices where id_notice=$id_notice";
		$facettes=fetchOne($req);
		if(strpos($facettes,$code_facette) !== false) return; 
		
		// Ecrire dans notices
		$facettes.=$code_facette;
		sqlExecute("update notices set facettes='$facettes' where id_notice=$id_notice");
		
		// ecrire dans codif_tags
		$code_notice=";".$id_notice.";";
		$enreg=fetchEnreg("select notices,a_moderer from codif_tags where id_tag=$id_tag");
		$notices=$enreg["notices"];
		$a_moderer=$enreg["a_moderer"];
		if(strpos($notices,$code_notice) === false) if(!$notices) $notices.=$code_notice; else $notices.=$id_notice.";";
		if(strpos($a_moderer,$code_notice) === false) if(!$a_moderer) $a_moderer.=$code_notice; else $a_moderer.=$id_notice.";";
		sqlExecute("update codif_tags set notices='$notices', a_moderer='$a_moderer' where id_tag=$id_tag");
	}
	
//------------------------------------------------------------------------------------------------------ 
// Get id tag dans table de codif
//------------------------------------------------------------------------------------------------------ 
	private function getIdTag($tag)
	{
		$ix=new Class_Indexation();
		$code=$ix->alphaMaj($tag);
		$id=fetchOne("select id_tag from codif_tags where code_alpha='$code'");
		// Si pas trouvé on le cree
		if(!$id)
		{
			$tag=addslashes($tag);
			$req="insert into codif_tags(libelle,code_alpha) Values('$tag','$code')";
			sqlExecute($req);
			$id=fetchOne("select id_tag from codif_tags where code_alpha='$code'");
		}
		return $id;
	}
	
//------------------------------------------------------------------------------------------------------ 
// Valider tag (moderation)
//------------------------------------------------------------------------------------------------------ 
	public function validerTagNotice($id_tag,$id_notice)
	{
		$a_moderer=fetchOne("select a_moderer from codif_tags where id_tag=$id_tag");
		$a_moderer=str_replace(";".$id_notice.";",";",$a_moderer);
		if(trim($a_moderer)==";")$a_moderer="";
		sqlExecute("update codif_tags set a_moderer='$a_moderer' where id_tag=$id_tag");
	}
	
//------------------------------------------------------------------------------------------------------ 
// Supprimer tag (moderation)
//------------------------------------------------------------------------------------------------------ 
	public function supprimerTagNotice($id_tag,$id_notice)
	{
		// Supprimer dans codif_tags
		$enreg=fetchEnreg("select * from codif_tags where id_tag=$id_tag");
		$a_moderer=$enreg["a_moderer"];
		$notices=$enreg["notices"];
		$a_moderer=str_replace(";".$id_notice.";",";",$a_moderer);
		if(trim($a_moderer)==";")$a_moderer="";
		$notices=str_replace(";".$id_notice.";",";",$notices);
		if(trim($notices)==";") $req="delete from codif_tags where id_tag=$id_tag";
		else $req="update codif_tags set a_moderer='$a_moderer' where id_tag=$id_tag";
		sqlExecute($req);
		
		// Supprimer dans notice
		$clef_tag="Z".$id_tag;
		$facettes=fetchOne("select facettes  from notices where id_notice=$id_notice");
		$facettes=trim(str_replace($clef_tag,"",$facettes));
		sqlExecute("update notices set facettes='$facettes' where id_notice=$id_notice");
	}
}