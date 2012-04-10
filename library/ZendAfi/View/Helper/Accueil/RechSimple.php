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
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Class module Recherche Simple
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Accueil_RechSimple extends ZendAfi_View_Helper_Accueil_Base
{

//---------------------------------------------------------------------
// Construction du Html
//---------------------------------------------------------------------
	public function getHtml()
	{
		$this->titre = $this->preferences["titre"];

		// Si division 2 on met champ de recherche et types de docs côte a côte
		$float = '';
		if($this->division=="2" and $this->preferences["select_doc"]) $float=' style="float:left;width:50%"';

		// filtres
		$arg_filtre = '';
		if(array_isset("type_doc", $this->preferences)) $arg_filtre="?facette=T".$this->preferences["type_doc"];

		$this->contenu.='<form name="form" action="'.BASE_URL.'/opac/recherche/simple'.$arg_filtre.'" method="post" class="rechSimpleForm">';
		$this->contenu.= '<div>';
		$this->contenu.='<div'.$float.'><label for="expressionRecherche">'.$this->preferences["message"].'&nbsp;</label></div>';

		if($float) $this->contenu.=sprintf('<div><label for="select_type_doc">%s&nbsp;</label></div>', $this->translate()->_('Type de document'));

		$this->contenu.='<div'.$float.'>'.$this->getChampSaisie().'</div>';
		if($float) $this->contenu.='<div>'.$this->getComboTypesDocs().'</div>';

		if($this->preferences["select_doc"] and $this->division!="2")
			$this->contenu.= sprintf('<div style="margin-top:5px"><label for="select_type_doc">%s</label>%s</div>',
															 $this->translate()->_('Type de document'),
															 $this->getComboTypesDocs());
		if($this->preferences["select_annexe"] and $this->division!="2")
		{
			$this->contenu.='<div style="margin-top:5px">'.$this->translate()->_('Site').'</div>';
			$this->contenu.=$this->getComboAnnexes();
		}

		if($this->preferences["select_bib"] == 1)	$this->contenu.='<div>'. $_SESSION["selection_bib"]["html"] .'</div>';
		$this->contenu.='</div></form>';

		if ($this->preferences["recherche_avancee"] == "on")
			$this->contenu.='<div class="recherche_avancee">'.
				'<a href="'.BASE_URL.'/recherche/avancee?statut=reset'.'">Recherche avancée</a>'.
				'</div>';

		$this->contenu .= '<div class="clear"></div>';
		return $this->getHtmlArray();
	}

//---------------------------------------------------------------------
// Champ de saisie
//---------------------------------------------------------------------
	private function getChampSaisie()
	{
		$ret= '<input type="text" id="expressionRecherche" name="expressionRecherche" value="" style="width:'.$this->preferences["largeur"].'px"
							onkeypress="if (event.keyCode == 13) {this.form.submit();return false;}" />
						<input name="button" type="submit" class="submit" value="" />';

		if ($this->preferences["exemple"])
			$ret.='<div>'.$this->preferences["exemple"].'</div>';

		return $ret;
		
	}

//---------------------------------------------------------------------
// Combo des types de documents
//---------------------------------------------------------------------
	public function getComboTypesDocs()
	{
		$cls=new ZendAfi_View_Helper_ComboCodification();
		$combo=$cls->ComboCodification('type_doc', '');
		return $combo;
	}

//---------------------------------------------------------------------
// Combo des annexes
//---------------------------------------------------------------------
	public function getComboAnnexes()
	{
		$annexes=fetchAll("select code,libelle from codif_annexe where invisible=0 order by libelle");
		if($annexes)
		{
			$data=array(""=>"tous");
			foreach($annexes as $annexe)
			{
				$data[$annexe["code"]]=$annexe["libelle"];
			}
			$combo='<select name="annexe">';
			foreach($data as $key=>$valeur)
			{
				$combo.='<option value="'.$key.'">'.stripSlashes($valeur).'</option>';
			}
			$combo.='</select>';
		}
		return $combo;
	}
}