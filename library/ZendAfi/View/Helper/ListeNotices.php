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
//////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 :	Liste de notices
//					formats : 1=liste normale
//										2=liste accordéon
//										3=liste vignette
//										4= Liste images bookflip
//////////////////////////////////////////////////////////////////////////////////////////

class ZendAfi_View_Helper_ListeNotices extends ZendAfi_View_Helper_BaseHelper {
	/**
	 * @param array $notices
	 * @param int $nombre_resultats
	 * @param int $page
	 * @param array $preferences
	 * @param string $url
	 * @param string $tri
	 * @return string
	 */
	public function listeNotices($notices, $nombre_resultats, $page, $preferences, $url="", $tri="") {
		$html = '<div class="liste_notices">';

		// Message d'erreur
		if (array_key_exists('statut', $notices) && ($notices["statut"]=="erreur")) {
			$html.='<h2>'.$notices["erreur"].'</h2>';
			if($notices["nb_mots"] > 1 and !$_REQUEST["pertinence"])	{
				$html.=sprintf('<span>%s</span>', $this->translate()->_('Lancement de la recherche élargie à tous les mots en cours...'));
				$html.=sprintf('<script>document.location="%s?pertinence=1"</script>',
											 $this->view->url(array('controller' => 'recherche',
																							'action' => 'simple')));
			}
			return $html.'</div>';
		}

		// Nombre de resultats et n° de page
		$html.='<table><tr><td align="left" width="100%">';
		if(!intval($page)) $page=1;
    if(!$nombre_resultats) $html.= $this->translate()->_('Aucune notice trouvée');
    if($nombre_resultats == 1) $html.=$this->translate()->_('1 notice trouvée');
    if($nombre_resultats > 1) $html.=sprintf($nombre_resultats.' %s',$this->translate()->_('notices trouvées'));
		$html.='</td>';

		// combo tri
		if($url)	{
			$onchange="var tri=$('#tri').val();document.location='".$url.(strpos($url, '?') ? '&' : '?')."tri='+tri;";
			$html.='<td align="right">Trier&nbsp;par </td>';
			$html.='<td style="padding-right:10px">';

			$html.=$this->view->formSelect('tri', 
																		 $tri, 
																		 ['onchange' => $onchange], 
																		 ['*' => $this->view->_('Pertinence'),
																		  'alpha_titre' => $this->view->_('Titre et auteur'),
																			'annee desc' => $this->view->_('Année de publication'),
																			'type_doc,alpha_titre' => $this->view->_('Type de document'),
																			'date_creation desc' => $this->view->_('Date de nouveauté')]);
		}

		$html.=' <td align="right">page&nbsp;'.$page.'</td>';
    $html.='</td></tr></table>';
    if(!$nombre_resultats) return $html;

    // Liste en fonction du format
    switch($preferences["liste_format"])
    {
    	case 1: $html.=$this->listeTableau($notices,$preferences["liste_codes"]); break;
    	case 2: $html.=$this->listeAccordeon($notices); break;
    	case 3: $html.=$this->listeVignette($notices,$preferences["liste_codes"]); break;
    	case 4: $html.=$this->listeBookFlip($notices); break;
    }
    return ($html.'</div>');
	}
//------------------------------------------------------------------------------------------------------
// Format 1 : TABLEAU avec notice sur une autre page
//------------------------------------------------------------------------------------------------------
	public function listeTableau($data,$champs)
	{
		// Entête
		$html='<table cellspacing="0" cellpadding="3" border="0" width="100%">';
		$html.='<tr><td class="listeTitre" width="26px">&nbsp;</td>';
		for($i=0; $i < strlen($champs); $i++)
		{
			$champ=Class_Codification::getNomChamp($champs[$i]);
			$html.='<td class="listeTitre">'.$champ.'</td>';
		}
		$html.='</tr>';

		// Notices
		$lig=0;
		foreach ($data as $notice)
		{
			if($lig % 2) $style_css="listeImpaire"; else $style_css="listePaire";
			$html.='<tr>';
			$html.=sprintf('<td class="%s" style="text-align:center">%s</td>',
										 $style_css,
										 $this->view->iconeSupport($notice["type_doc"]));

			for($i=0; $i < strlen($champs); $i++)
			{
				$champ=$champs[$i];
				if($champ == "N") $align='style="text-align:center"'; else $align="";
				if($champ=="T") 
					$html.= sprintf('<td class="%s"><a href="%s">%s</a></td>',
													$style_css,
													$this->view->urlNotice($notice),
													$notice["T"]);

				else $html.='<td class="'. $style_css .'" '.$align.'>'.$notice[$champ].'</td>';
				$lig++;
			}
			$html.='</tr>';
		}
		$html.='</table>';
		return $html;
	}


//------------------------------------------------------------------------------------------------------
// Format 2 : ACCORDEON
//------------------------------------------------------------------------------------------------------
	public function listeAccordeon($data)
	{
		// Javascripts pour affichage localisation
		$html.='<link rel="stylesheet" type="text/css" media="screen" href="'.URL_ADMIN_JS.'slimbox/slimbox2.css">';
		$html.='<link rel="stylesheet" type="text/css" media="screen" href="'.URL_ADMIN_JS.'jquery_ui/css/jquery.ui.all.css">';
		$html.='<link rel="stylesheet" type="text/css" media="screen" href="'.URL_ADMIN_JS.'rating/jquery.rating.css">';

		$html.='<script type="text/javascript" src="'.URL_ADMIN_JS.'slimbox/slimbox2.js"> </script>';
		$html.='<script type="text/javascript" src="'.URL_ADMIN_JS.'rating/jquery.rating.pack.js"> </script>';
		$html.='<script type="text/javascript" src="'.URL_ADMIN_JS.'jquery_ui/jquery.ui.core.min.js"> </script>';
		$html.='<script type="text/javascript" src="'.URL_ADMIN_JS.'jquery_ui/jquery.ui.widget.min.js"> </script>';
		$html.='<script type="text/javascript" src="'.URL_ADMIN_JS.'jquery_ui/jquery.ui.mouse.min.js"> </script>';
		$html.='<script type="text/javascript" src="'.URL_ADMIN_JS.'jquery_ui/jquery.ui.draggable.min.js"> </script>';
		$html.='<script type="text/javascript" src="'.URL_ADMIN_JS.'jquery_ui/jquery.ui.position.min.js"> </script>';
		$html.='<script type="text/javascript" src="'.URL_ADMIN_JS.'jquery_ui/jquery.ui.resizable.min.js"> </script>';
		$html.='<script type="text/javascript" src="'.URL_ADMIN_JS.'jquery_ui/jquery.ui.dialog.min.js"> </script>';

		// Notices
		$notice_html= new Class_NoticeHtml("");
		$lig=0;
		foreach ($data as $notice)
		{
			$html.='<table cellspacing="0" cellpadding="3" border="0" width="100%">';
			if($lig % 2) $style_css="listeImpaire"; else $style_css="listePaire";
			$div_notice="N".$notice["id_notice"];
			$onclick="deployer_contracter('".$div_notice."');getNoticeAjax('".$div_notice."','".$div_notice."','".$notice["type_doc"]."')";
			$html.='<tr>';
			$html.=sprintf('<td class="%s" width="10" style="text-align:center"><img id="I'.$div_notice.'" src="'.URL_IMG.'bouton/plus_carre.gif" border="0" onclick="%s" style="cursor:pointer" alt="%s"/></td>',
										 $style_css,
										 $onclick,
										 $this->translate()->_('déplier'));

			$html.=sprintf('<td class="%s" width="26" style="text-align:center">%s</td>',
										 $style_css,
										 $this->view->iconeSupport($notice["type_doc"]));

			$html.='<td class="'. $style_css .'" width="100%"><a href="#" onclick="'.$onclick.'">'.$notice["T"].'</a>';
			$html.=' / '.$notice["A"].'</td>';
			$html.='</tr>';

			// container notice
			$patience=sprintf('<table><tr><td><img border="0" src="'.URL_IMG.'patience.gif" alt="%s" /></td><td>%s...</td></tr></table>',
												$this->translate()->_('Chargement en cours'),
												$this->translate()->_('Veuillez patienter : traitement en cours'));

			$html.='<tr><td colspan="10">'.$notice_html->getConteneurNotice($notice["id_notice"]).'</td></tr>';
			$html.='</table>';
		}
		return $html;
	}
//------------------------------------------------------------------------------------------------------
// Format 3 : VIGNETTE
//------------------------------------------------------------------------------------------------------
	public function listeVignette($data,$champs)
	{

		$lig=0;
		$html = '';
		foreach($data as $notice)
		{
			// calcul url en fonction du type de doc
			if($notice["type_doc"]>7 and $notice["type_doc"]<11)
			{
				$cls_notice=new Class_Notice();
				$cls_notice->getNotice($notice["id_notice"]);
				$id_ressource=$cls_notice->getChamp856b();
				switch($notice["type_doc"])
				{
					case "8": $url_notice=BASE_URL.'/cms/articleview/id/'.$id_ressource; break;
					case "9": $url_notice=BASE_URL.'/rss/main/id_flux/'.$id_ressource; break;
					case "10": $url_notice=BASE_URL.'/sito/sitoview/id/'.$id_ressource; break;
				}
			}
			else 
				$url_notice = $this->view->urlNotice($notice); 

			// style selon parité des lignes
			if($lig % 2) $style_css="listeImpaire"; else $style_css="listePaire";
			$html.='<div class="liste_vignette"><table width="100%" cellspacing="0" border="0">';

			// Image
			$notice["titre_principal"]=$notice["T"];
			$notice["auteur_principal"]=$notice["A"];
			$img=Class_WebService_Vignette::getUrl($notice["id_notice"]);
			$html.=sprintf('<tr><td class="%s" width="100px" style="vertical-align:top"><a href="%s"><img src="%s" border="0" width="90px" alt="%s" title="%s"/></a></td>',
										 $style_css,
										 $url_notice,
										 $img["vignette"],
										 $this->translate()->_('Vignette'),
										 "source : ".Class_WebService_Vignette::getSource($img["vignette"]));

			// Titre / auteur principal
			$html.='<td class="'. $style_css .'" style="text-align:left;vertical-align:top;width:100%">';
			$html.='<a href="'.$url_notice.'">';
			$html.=$notice["T"].BR.$notice["A"];
			$html.='</a>';

			// Type de document
			$html.=BR.'<table cellspacing="0" style="border-color:#bfbfbf;border-left:none;border-right:none;border-bottom:none;border-top:1px solid;width:100%;margin-top:5px">';
			$html.=sprintf('<tr><td class="%s">%s</td>',
										 $style_css,
										 $this->view->iconeSupport($notice["type_doc"]));
			$html.='<td class="'. $style_css .'"> : '.Class_Codification::getLibelleFacette("T".$notice["type_doc"]).'</td></tr>';
			
			// Données variables
			for($i=0; $i < strlen($champs); $i++)
			{
				$champ=$champs[$i];
				if($champ=="T" or $champ=="A") continue;
				if(trim($notice[$champ]) == '') continue;
				$html.='<tr><td class="'. $style_css .'" style="vertical-align:top">'.Class_Codification::getNomChamp($champ).' </td>';
				$html.='<td class="'. $style_css .'" style="width:100%"> : '.$notice[$champ].'</td></tr>';
				$lig++;
			}
			$html.='</table>';
			$html.='</td></tr></table></div>';
			$lig++;
		}
		return $html;
	}
//------------------------------------------------------------------------------------------------------
// Format 4 : BOOKFLIP
//------------------------------------------------------------------------------------------------------
	public function listeBookflip($data)
	{
		return('<div>TEST</div>');
		// Container images
		$html.=sprintf('<div id="Book" style="position:relative"><img src="'.URL_IMG.'blank.gif" width="144" height="227" alt="%s" /></div>',
									 $this->translate()->_('Livre'));
		// Images et urls
		foreach($data as $notice)
		{
			// Image
			$notice["titre_principal"]=$notice["T"];
			$notice["auteur_principal"]=$notice["A"];
			$img=Class_WebService_Vignette::getUrl($notice["id_notice"],true);
			$url = $this->view->urlNotice($notice); 
			//$url='javascript:document.getElementById(\'Nnotice\').style.display=\'block\';getNoticeAjax(\'N'.$notice["id_notice"].'\',\'Nnotice\')';
			if($images) $images.=",";
			$images.='"'.$img["vignette"].'","'.$url.'"';

		}
		$html.='<script>Book_Image_Sources=new Array('.$images.');';
		$html.=' $(document).ready(ImageBook);</script>';
		return $html;
	}

}