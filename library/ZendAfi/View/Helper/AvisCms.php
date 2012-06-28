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
class ZendAfi_View_Helper_AvisCms extends Zend_View_Helper_HtmlElement {
	public function avisCms($article) {
		return $this->rendHtmlBlockAvis($article);
	}

	
	function rendHtmlBlockAvis($article)	{
		$info_bib = $this->rendInfoCmsAvis($article, 1);
		$info_abo = $this->rendInfoCmsAvis($article, 0);

		$nb_avis = 0;
		if ($rank = $article->getRank())
			$nb_avis = $rank->getNbAvisTotal();

		$txt_nb_avis = ($nb_avis == 0) ?"&nbsp;Aucun avis" : "&nbsp;Avis (".$nb_avis.")";
		$id_news = $article->getId();
		$html = '<div class="avis_show_avis" onclick="showCmsAvis('.$id_news.', this)"><img src="'.URL_IMG.'bouton/plus_carre.gif" style="float:left" border="0" alt="Voir les avis" title="Voir les avis" id="plus"/> '.$txt_nb_avis.' </div>';
		$html.='<div id="avis_'.$id_news.'" style="display:none;padding-left:5px;">';
		$html.='<br />';
		$html.='<a href="#" onclick="javascript:fonction_abonne(\''.Class_Users::currentUserId().'\',\'/opac/abonne/cmsavis?id='.$id_news.'\')">&raquo; Donner ou modifier votre avis</a>';
		$html.='<ul class="notice_info">';
		$html.='<li>'.$info_bib["NOTE"].' <a href="#" onclick="showAvis('.$id_news.',\'bib\');return false;">Avis de bibliothécaires</a> '.$info_bib["AVIS"].'</li>';
		$html.='<li>'.$info_abo["NOTE"].' <a href="#" onclick="showAvis('.$id_news.',\'abo\');return false;">Avis de lecteurs du portail</a> '.$info_abo["AVIS"].'</li>';
		$html.='</ul>';
        
		$view = (getVar('MODO_AVIS_BIBLIO') == 1) ? 1 : "";
        
		$liste_avis = $this->getCmsAvisBiblio($article, $view);

		$style = (count($liste_avis) == 0) ? "none" : "block";
		
		// Tableau bib
		$html.='<div id="bib_'.$id_news.'" style="display:'.$style.'"><table cellpadding="0" cellspacing="0" border="0" style="width:100%">';
		$html.='<tr><td class="avis_from">Avis des bibliothécaires</td></tr>';
		$html.='<tr><td>'.$this->rendHTMLCmsAvis($liste_avis,1).'</td></tr>';
		$html.='</table></div>';

		$view = (getVar('MODO_AVIS') == 1) ? 1 : "";
        
		// Tableau Abonne
		$liste_avis_abo = $this->getCmsAvisAbo($article, $view);

		$style = (count($liste_avis_abo) == 0) ? "none" : "block";

		$html.='<div id="abo_'.$id_news.'" style="display:'.$style.'"><table cellpadding="0" cellspacing="0" border="0" style="width:100%">';
		$html.='<tr><td class="avis_from">Avis des lecteurs du portail</td></tr>';
		$html.='<tr><td>'.$this->rendHTMLCmsAvis($liste_avis_abo,0).'</td></tr>';
        
		$html.='</table></div>';
		$html.='</div>';
		$html.='<div style="width:100%;background:transparent url('.URL_IMG.'box/menu/separ.gif) repeat-x scroll center bottom">&nbsp;</div>';
        
		return($html);
	}


	public function rendInfoCmsAvis($article, $abon_ou_bib) {
		if (!$rank = $article->getRank())
			return array('NOTE' => 0, 'AVIS' => 0, 'ABON_NOMBRE_AVIS' => 0, 'BIB_NOMBRE_AVIS' => 0);

		$abon_nb_avis = $rank->getAbonNombreAvis();
		$bib_nb_avis = $rank->getBibNombreAvis();
		
		if($abon_ou_bib == 0)	{
				if ($abon_nb_avis == 0 || $abon_nb_avis == null) $nb_eva = "(aucune évaluation)";
				elseif($abon_nb_avis == 1) $nb_eva = "(1 évaluation)";
				elseif($abon_nb_avis > 1) $nb_eva = "(".$abon_nb_avis." évaluations)";
            
			$note = $rank->getAbonNote();
		}	else {
				if ($bib_nb_avis == 0 || $bib_nb_avis == null) $nb_eva = "(aucune évaluation)";
				elseif($bib_nb_avis == 1) $nb_eva = "(1 évaluation)";
				elseif($bib_nb_avis > 1) $nb_eva = "(".$bib_nb_avis." évaluations)";

			$note = $rank->getBibNote();
		}

		$note_r = str_replace('.','-',$note);
		$note_r = str_replace('-0','',$note_r);
		if ($note_r == '') $note_r = "0";
		$img = '<img src="'.URL_ADMIN_IMG.'stars/stars-'.$note_r.'.gif"  alt="note:'.$note.'" border="0"/>';
		$info["NOTE"] = $img;

		$info["AVIS"] = $nb_eva;
		return($info);
	}
    

	function getCmsAvisBiblio($article, $statut = "")	{
		return $this->getCmsAvis($article, Class_Avis::AVIS_BIBLIO, $statut);
	}


	function getCmsAvisAbo($article, $statut = "") {
		return $this->getCmsAvis($article, Class_Avis::AVIS_ABONNE, $statut);
	}


	function getCmsAvis($article, $abon_ou_bib, $statut = "")	{
		$params = array('id_cms' => $article->getId(),
										'order' => 'date_avis desc',
										'abon_ou_bib' => $abon_ou_bib);
		if ("" !== $statut)
			$params['statut'] = $statut;

		return Class_Avis::getLoader()->findAllBy($params);
	}

    
	// type =0 -> rend bulle blanche pour abonne
	// type =1 -> rend bulle bleue pour bibliothecaire
	function rendHTMLCmsAvis($avis_array, $type=0)	{
		if(!is_array($avis_array))	
			return '';

		$html = array();
		foreach($avis_array as $avis)	{
			$date_avis = Class_Date::humanDate($avis->getDateAvis(), 'd MMMM yyyy');
			$contenu_avis = nl2br($avis->getAvis());

			$img = URL_ADMIN_IMG."stars/stars-".str_replace(".",",",$avis->getNote()).".gif"; $img=str_replace(",0","",$img);

			$html[]='<style type="text/css">';
			$html[]='table.avis {border: 2px solid #ddd; padding: 4px; border-radius: 5px}';
			$html[]='table.avis tr:first-child {font-weight: bold;}';
			$html[]='table.avis tr:first-child td {padding-bottom: 10px;}';
			$html[]='table.avis tr:first-child td:last-child {text-align:right}';
			$html[]='table.avis tr:last-child td:last-child {text-align:right}';
			$html[]='</style>';


			$html[]='<table class="avis">';
			$html[]='<tr>';
			$html[]=sprintf('<td>%s %s</td>',
				              $this->view->tagAnchor(array('module' => 'admin',
													'controller' => 'modo',
													'action' => 'delete-cms-avis',
													'id' => $avis->getId()),
												$this->view->boutonIco('type=del')),
				              
											$avis->getEntete());
			$html[]='<td><img src="'.$img.'"></td>';
			$html[]='</tr>';
			$html[]='<tr>';
			$html[]='<td colspan="2">';
			$html[]='<img src="'.URL_ADMIN_IMG.'avis/quote_up.jpg"/>';
			$html[]='&nbsp;'.$contenu_avis.'&nbsp;';
			$html[]='<img src="'.URL_ADMIN_IMG.'avis/quote_down.jpg" />';
			$html[]='</td>';
			$html[]='</tr>';
			$html[]='<tr>';
			$html[]='<td></td>';
			$html[]='<td>';
			$html[]='<div style="padding-bottom:10px;">';
			$html[]='par : <b>'.$avis->getAuteur()->getNomAff().'</b>&nbsp;le&nbsp;<i>'.$date_avis.'</i></div></td>';
			$html[]='</tr>';
			$html[]='</table>';
		}

		if(is_array($html)) 
			return(implode("", $html));

		return('<div style="padding-left:7px">&nbsp;Aucun avis pour le moment.</div>');
	}
}

?>