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
// OPAC3 - Constitution notice html
//////////////////////////////////////////////////////////////////////////////////////////

class Class_NoticeHtml
{
	public $notice;								// Structure complete de la notice
	public $haut_onglet;					// Html ligne du haut avec image fermer pour les onglets
	private $java_script_auto;		// JavaScript pour ouverture automatique onglets et blocs
	public $preferences;					// Préférences d'affichage pour les blocs et les onglets
	private $_translate;
	
//------------------------------------------------------------------------------------------------------
// Constructeur initialise la notice
//------------------------------------------------------------------------------------------------------
	function __construct($notice="")
	{
		$this->notice=$notice;
		$this->preferences = Class_Profil::getCurrentProfil()->getCfgNoticeAsArray();
		$this->_translate = Zend_Registry::get('translate');
	}

//------------------------------------------------------------------------------------------------------
// Champs d'Entete (selon preferences)
//------------------------------------------------------------------------------------------------------
	public function getEntete()
	{
		if(!$this->notice["entete"]) return false;
		$html='<table id="entete_notice">';
		foreach($this->notice["entete"] as $libelle => $valeurs)
		{
			if(!$valeurs) continue;
			$html.='<tr>';
			$html.='<td style="white-space:nowrap;text-align:right;padding-right:5px;vertical-align:top;">'.$libelle.'&nbsp;:</td>';
			$html.='<td valign="top">';
			if(gettype($valeurs) != "array") 
				$html.=$valeurs.BR;
			else	{
				foreach($valeurs as $item) {
					if (gettype($item) == "array"){ 
						if($item["url"]) $html.='<a href="'.$item["url"].'" class="notice">'.$item["libelle"].'</a>.';
					} else {
						$html.=$item;
					}

					$html.=BR;
				}
			}
			$html.='</td>';
			$html.='</tr>';
		}
		$html.='</table>';
		return $html;
	}

//------------------------------------------------------------------------------------------------------
// Boite a onglets
//------------------------------------------------------------------------------------------------------
	public function getOnglets()
	{
		// Init
		if(!$this->notice["onglets"]) return false;
		$id=$this->notice["id_notice"];
		$isbn=$this->notice["isbn"];
		
		// Html du set
		$tag_patience ='<div class="notice_patience" style="text-align:right;width:15px"><img src="'.URL_IMG.'patience.gif" border="0" alt="'.$this->_translate->_('Chargement en cours').'" /></div>';
		$tag_patience.='<div class="notice_patience">'.$this->_translate->_('Veuillez patienter : lecture en cours...').'</div>';

		$html_onglets = $html_contenu = '';

		// Onglets
		$nb_onglets=count($this->notice['onglets']);
		$i=0;
		$tabs = array();

		foreach($this->notice["onglets"] as $onglet) {
			$id_onglet = sprintf('set%d_onglet_%d', $id, $i++);
			$js_onclick = $this->getOnclick($onglet["type"],$isbn,$id_onglet);

			if($i==1) 
				$this->java_script_auto[]="infos_onglet". str_replace("this.id","'".$id_onglet."'", $js_onclick);

			$html_onglets.= sprintf('<div id="%s" class="titre_onglet" style="width:%d%%" onclick="infos_onglet%s">%s</div>',
															$id_onglet,
															$onglet["largeur"] ? $onglet["largeur"] : 20,
															$js_onclick,
															$onglet["titre"]);

			// Boite contenu
			$html_contenu.=sprintf('<div id="%s_contenu_row">%s</div>', 
														 $id_onglet, 
														 sprintf('<div id="%s_contenu" class="onglet">%s</div>', 
																		 $id_onglet, 
																		 $tag_patience));
		}

		// Contenu et fin
		return sprintf('<div class="onglets">'.
									   '<div class="onglets_titre">'.
									     '<div>%s</div>'.
									   '</div>'.
									   '<div class="onglets_contenu">%s</div>'.
									 '</div>', 
									 $html_onglets, 
									 $html_contenu);
	}
	
//------------------------------------------------------------------------------------------------------
// Blocs et leurs contenus
//------------------------------------------------------------------------------------------------------
	public function getBlocs()
	{
		// Init
		if(!$this->notice["blocs"]) return false;
		$id=$this->notice["id_notice"];
		$isbn=$this->notice["isbn"];
		
		// Conteneur
		$html='<table cellspacing="0" cellpadding="0" width="100%">';

		// Blocs
		$i=0;
		foreach($this->notice["blocs"] as $bloc)
		{
			$id_bloc="bloc_".$id."_".$i++;
			$js='infos_bloc'.$this->getOnclick($bloc["type"],$isbn,$id_bloc);
			if($bloc["aff"]==1) $this->java_script_auto[]="infos_bloc". str_replace("this.id","'".$id_bloc."'",$this->getOnclick($bloc["type"],$isbn,$id_bloc));
			// Titre
			$html.='<tr>';
			$html.='<td width="10" style="text-align:center" valign="top"><img id="I'.$id_bloc.'" src="'.URL_IMG.'bouton/plus_carre.gif" border="0" onclick="'.$js.'" style="cursor:pointer;margin-top:5px"  alt="Déplier"  /></td>';
			$html.='<td><div id="'.$id_bloc.'" class="notice_bloc_titre" onclick="'. $js.'">'.$bloc["titre"].'</div></td>';
			$html.='</tr>';
			// Boite contenu
			$html.='<tr id="'.$id_bloc.'_contenu_row"><td></td>';
			$html.='<td><div id="'.$id_bloc.'_contenu" class="notice_bloc">';
			$html.='<table><tr>';
			$html.='<td class="notice_patience" style="text-align:right;width:15px"><img src="'.URL_IMG.'patience.gif" border="0"  alt="'.$this->_translate->_('Chargement en cours').'" /></td>';
			$html.='<td class="notice_patience">'.$this->_translate->_('Veuillez patienter : lecture en cours...').'</td>';
			$html.='</tr></table></div></td></tr>';
		}
		
		// fin
		$html.='</table>';
		return $html;
	}

//------------------------------------------------------------------------------------------------------
// Onclick pour onglet ou bloc
//------------------------------------------------------------------------------------------------------
	private function getOnclick($rubrique,$isbn,$id_onglet)
	{
		$action = sprintf("(this.id,'','%s',0,'',0)", $rubrique);
		switch($rubrique) {
			case "avis" : $action="(this.id,'".$isbn."','avis',0,'',1)"; break;
			case "exemplaires" : $action="(this.id,'".$isbn."','exemplaires',0,'',1)"; break;
			case "tags" : 
				if (is_array($isbn))
					$isbn = $isbn["isbn"];
				$action="(this.id,'".$isbn."','tags',0,'',0)"; 
        break;
			case "resume" : $action="(this.id,'".$isbn."','resume',0,'',0)"; break;
			case "similaires" : $action="(this.id,'".$isbn."','similaires',0,'',0)"; break;
		}
		$_SESSION["onglets"][$rubrique]=$id_onglet;
		return $action;
	}

//------------------------------------------------------------------------------------------------------
// Java script pour ouverture automatique du 1er bloc et du 1er onglet
//------------------------------------------------------------------------------------------------------
	public function getJavaScriptAuto()
	{
		if(!$this->java_script_auto) return false;
		$html.='<script type="text/javascript">';
		foreach($this->java_script_auto as $js) $html.=$js.";";
		$html.='</script>';
		return $html;
	}
	
//------------------------------------------------------------------------------------------------------
// Conteneur ajax pour une notice
//------------------------------------------------------------------------------------------------------
	public function getConteneurNotice($id_notice)
	{
		$template.='<div class="notice" id="N'.$id_notice.'">';
		$template.='<table class="notice" align="center"><tr>';
		$template.='<td class="notice_patience" style="text-align:right"><img src="'.URL_IMG.'patience.gif" border="0"  alt="'.$this->_translate->_('Chargement en cours').'" /></td>';
		$template.='<td class="notice_patience" width="80%">'.$this->_translate->_('Veuillez patienter : lecture en cours...').'</td>';
		$template.='</tr></table></div>';
		return $template;
	}
	
//------------------------------------------------------------------------------------------------------
// 1ere ligne pour les onglets
//------------------------------------------------------------------------------------------------------
	public function initHautOnglet($id_onglet)	{
		$this->haut_onglet = '';
		return;
		$this->haut_onglet='<table width="100%"><tr><td style="text-align:right">';
		$this->haut_onglet.='<img src="'.URL_IMG.'bouton/contracter.gif" border="0" title="'.$this->_translate->_('Fermer les onglets').'"';
		$this->haut_onglet.=' onclick="javascript:fermer_infos_notice(\''.$id_onglet.'\')" style="cursor:pointer" alt="'.$this->_translate->_('Refermer').'" />';
		$this->haut_onglet.='</td></tr></table>';
	}
	
//------------------------------------------------------------------------------------------------------
// Ligne de message : pas d'info trouvée
//------------------------------------------------------------------------------------------------------
	public function getNonTrouve($msg="",$haut_onglet=false)
	{
		if(!$msg) 
			$msg=$this->_translate->_("Aucune information n'a été trouvée");
		if($haut_onglet == true) $html=$this->haut_onglet;
		$html ='<table width="100%"><tr>';
		$html.='<td>'.$msg.'</td>';
		$html.='</tr></table>';
		return $html;
	}

//------------------------------------------------------------------------------------------------------
// Notice détaillée
//------------------------------------------------------------------------------------------------------
	public function getNoticeDetaillee($notice,$id_onglet)
	{
		$this->notice=$notice;
		return $this->haut_onglet.$this->getEntete();
	}

//------------------------------------------------------------------------------------------------------
// Notice détaillée : articles de périodique
//------------------------------------------------------------------------------------------------------
	public function getArticlesPeriodique($articles)
	{
		if($articles[0]["note"]) $html.='<div style="margin-top:5px;margin-bottom:5px;font-weight:bold">'.$articles[0]["note"].'</div>';
		$html.='<table><tr><td class="notice_info_ligne_titre">'.$this->_translate->_('Articles').' :</td</tr></table>';
		if(!$articles) return $this->haut_onglet.$html.$this->_translate->_("Aucun article n'a été trouvé");

		$style=' style="text-align:right;padding-right:5px;padding-left:10px;vertical-align:top;"';
		$html.='<table id="entete_notice">';
		foreach($articles as $article)
		{
			$num++;
			$html.='<tr><td style="padding-top:7px" colspan="2" align="left"><b>'.$num." - ".$article["titre"].'</b></td></tr>';

			$article_line = '<tr><td %s>%s&nbsp;:</td><td>%s</td></tr>';
			
			if($article["pagination"]) 
				$html .= sprintf($article_line, 
												$style, 
												$this->_translate->_('Pagination'), 
												str_replace("pp.","",$article["pagination"]));

			if($article["auteur"]) 
				$html .= sprintf($article_line,
												 $style,
												 $this->_translate->_('Auteur'),
												 $article["auteur"]);

			if($article["resume"]) 
				$html .= sprintf($article_line,
												 $style,
												 $this->_translate->_('Résumé'),
												 $article["resume"]);

			if($article["matieres"])
			{
				$nm=0;
				foreach($article["matieres"] as $sujet)
				{
					$nm++;
					$html.='<tr>';
					if($nm==1) 
						$html.= sprintf('<td %s>%s&nbsp;:</td>', $style, $this->_translate->_('Sujet(s)'));
					else 
						$html.='<td'.$style.'>&nbsp;</td>';

					$html.='<td>'.$sujet.'</td>';
					$html.='</tr>';
				}
			}
		}
		$html.='</table>';
		return $this->haut_onglet.$html;
	}

//------------------------------------------------------------------------------------------------------
// Tableau des exemplaires
//------------------------------------------------------------------------------------------------------
	public function getExemplaires($exemplaires,$nb_notices_oeuvre=0,$aff="normal")
	{
    if(!$exemplaires) return false;
		$preferences=$this->preferences["exemplaires"];
		// Recup des donnees de dispo et reservable
		if($preferences["grouper"]==1)
		{
			$cls_comm=new Class_CommSigb();
			$exemplaires=$cls_comm->getDispoExemplaires($exemplaires);
		}

		$html=$this->haut_onglet;
		$html.='<table class="exemplaires" cellpadding="5" cellspacing="1">';
		$html.='<tr>';
    $html.='<th class="exemplaires" width="5%">'.$this->_translate->_('n°').'</th>';
    if($preferences["bib"]==1) $html.='<th class="exemplaires" width="35%">'.$this->_translate->_('Bibliothèque').'</th>';
		if($preferences["annexe"]==1) $html.='<th class="exemplaires" width="10%">'.$this->_translate->_('Bibliothèque').'</th>';
		if($preferences["section"]==1) $html.='<th class="exemplaires" width="10%">'.$this->_translate->_('Section').'</th>';
		if($preferences["emplacement"]==1) $html.='<th class="exemplaires" width="10%">'.$this->_translate->_('Emplacement').'</th>';
    if($preferences["grouper"]==0) $html.='<th class="exemplaires" width="5%">'.$this->_translate->_('Exemplaires').'</th>';
    $html.='<th class="exemplaires" width="10%">'.$this->_translate->_('Cote').'</th>';
    if($preferences["dispo"]==1) $html.='<th class="exemplaires" width="20%">'.$this->_translate->_('Disponibilité').'</th>';
    if($preferences["date_retour"]==1) $html.='<th class="exemplaires" width="15%">'.$this->_translate->_('Retour').'</th>';
		if($preferences["localisation"]==1) $html.='<th class="exemplaires" width="5%">'.$this->_translate->_('Situer.').'</th>';
    if($preferences["plan"]==1) $html.='<th class="exemplaires" width="5%">'.$this->_translate->_('Plan').'</th>';
    if($preferences["resa"]==1 and $aff=="normal") $html.='<th class="exemplaires" width="5%">'.$this->_translate->_('Réserver').'</th>';
		if($aff=="oeuvre") $html.='<th class="exemplaires" width="5%">'.$this->_translate->_('Voir').'</th>';
    $html.='</tr>';
    $num=0;

    foreach($exemplaires as $ex)
		{
			// Infos bib
			$bib=fetchEnreg("select LIBELLE,GOOGLE_MAP,INTERDIRE_RESA from bib_c_site where ID_SITE=".$ex["id_bib"]);
			
			// html
			$html.='<tr>';
			$html.='<td class="exemplaires">'.++$num.'</td>';
			if($preferences["bib"]==1) $html.='<td class="exemplaires">'.$bib["LIBELLE"].'</td>';

			//Annexe
			$ex["code_annexe"] = $ex["annexe"]; // LL: pour Opsys nous avons besoin de l'identifiant de l'annexe, d'où sauvegarde pour réutilisation plus bas
			if($preferences["annexe"]==1)
			{
				if($ex["annexe"] > '') $ex["annexe"]=fetchOne("select libelle from codif_annexe where id_bib=".$ex["id_bib"]." and code='".$ex["annexe"]."'");
				if(!$ex["annexe"]) $ex["annexe"]="&nbsp;";
				$html.='<td class="exemplaires" style="text-align:left">'.$ex["annexe"].'</td>';
			}
			if($preferences["section"]==1) $html.='<td class="exemplaires">'.Class_Codification::getLibelleFacette("S".$ex["section"]).'</td>';
			if($preferences["emplacement"]==1) $html.='<td class="exemplaires">'.Class_Codification::getLibelleFacette("E".$ex["emplacement"]).'</td>';
			if($preferences["grouper"]==0) $html.='<td class="exemplaires">'.$ex["count(*)"].' ex.</td>';
			$html.='<td class="exemplaires">'.$ex["cote"].'</td>';
			if($preferences["dispo"]==1) $html.='<td class="exemplaires">'.$ex["dispo"].'</td>';
			if($preferences["date_retour"]==1) $html.='<td class="exemplaires">'.$ex["date_retour"].'</td>';

			//Localisation sur le pan
			if($preferences["localisation"]==1)
			{
				$html.='<td class="exemplaires" style="text-align:center">';
				
				if(!isset($controle[$ex["id_bib"]])) 
					$controle[$ex["id_bib"]]=fetchOne("select count(*) from bib_localisations where ID_BIB=".$ex["id_bib"]);
				
				$onclick="localisationExemplaire(this,".$ex["id_bib"].",'".$ex["cote"]."','".$ex["code_barres"]."')";

				if($controle[$ex["id_bib"]]>0) 
					$html.= sprintf('<img src="%s" border="0"  title="%s" style="cursor:pointer" onclick="%s" alt="%s" />',
													URL_ADMIN_IMG.'picto/localisation.png',
													$this->_translate->_('Situer cet exemplaire dans la bibliothèque'),
													$onclick,
													$this->_translate->_('Situer en exemplaire'));
				else 
					$html.='&nbsp;';
				$html.='</td>';
			}
			// Google maps
			if($preferences["plan"]==1)
			{
				$html.='<td class="exemplaires" style="text-align:center;">';
				if($bib["GOOGLE_MAP"] > "") 
					$html .= sprintf('<a href="%s"><img src="%s" border="0" alt="%s" title="%s" /></a>',
													 BASE_URL.'/bib/mapview?id_bib='.$ex["id_bib"].'&amp;retour=notice',
													 URL_ADMIN_IMG.'picto/map.gif',
													 $this->_translate->_('Afficher la carte'),
													 $this->_translate->_('Afficher la carte'));
				else $html.='&nbsp;';
				$html.='</td>';
			}
			// Réservation
			if($preferences["resa"]==1 and $aff=="normal")
			{
				if($bib["INTERDIRE_RESA"]==1) $html.='&nbsp;';
				else
				{
					$html.='<td class="exemplaires" style="text-align:center;">';

					if(isset($cls_comm)) {
						$type_comm=$cls_comm->getTypeComm($ex["id_bib"]); 
					}
					else 
						$type_comm="";

					if(!$type_comm) 
						$html .= sprintf('<a href="%s"><img src="%s" border="0" title="%s" alt="%s"/></a>',
														 BASE_URL.'/recherche/reservation?b='.$ex["id_bib"].'&amp;n='.$ex["id_notice"].'&amp;cote='.$ex["cote"],
														 URL_IMG.'resa.gif',
														 $this->_translate->_('Réserver'),
														 $this->_translate->_('Réserver'));
					else
					{
						if($ex["reservable"]==true)
						{
							$onclick="reservationAjax(this,'".$ex["id_bib"]."','".$ex["id"]."', '".$ex["code_annexe"]."')";
							$html.= sprintf('<img src="%s" border="0" alt="%s" title="%s" onclick="%s" style="cursor:pointer" />',
															URL_IMG.'resa.gif',
															$this->_translate->_('Réserver'),
															$this->_translate->_('Réserver'),
															$onclick);
						}
						else $html.='&nbsp;';
					}
				}
				$html.='</td>';
			}

			// Lien vers notice en affichage oeuvre
			if($aff=="oeuvre")
			{
				$onclick="document.location=baseUrl+'/recherche/viewnotice/id/".$ex["id_notice"]."'";
				$html.='<td class="exemplaires" style="text-align:center;">';
				$html.= sprintf('<img src="%s" border="0" alt="%s" title="%s" onclick="%s" style="cursor:pointer" />',
												URL_IMG.'bouton/loupe.gif',
												$this->_translate->_('Afficher la notice'),
												$this->_translate->_('Afficher la notice'),
												$onclick);
				$html.='</td>';
			}

			// fin ligne
			$html.='</tr>';
		}
		$html.='</table>';

		// lien pour exemplaires de la meme oeuvre
		if($nb_notices_oeuvre)
		{
			$onclick="$('#exemplaires_oeuvre').show().load(baseUrl+'/opac/noticeajax/exemplaires?id_notice=".$ex["id_notice"]."&data=OEUVRE')";
			$html.='<div class="notice_bloc_titre" style="padding:10px;" onclick="'.$onclick.'">';
			$html.=sprintf('<b>%s</b>', $this->_translate->_('Afficher toutes les éditions de ce document'));
			$html.='</div>';
			$html.='<div id="exemplaires_oeuvre" style="display:none"><img src="'.URL_IMG.'patience.gif"></div>';
		}
		
		// Pour afficher la localisation sur le plan
		$html.='<div id="plan_localisation" style="display:none"></div>';

		$html.=sprintf('<div id="point_localisation" style="%s"><img src="" border="0" alt="%s" /></div>',
									 'position:absolute;z-index:10000;display:none;cursor:pointer',
									 $this->_translate->_('Localisation'));

		$html.='<div id="bulle_localisation" style="display:none"></div>';
		return $html;
	}

//------------------------------------------------------------------------------------------------------
// Résumés et analyses
//------------------------------------------------------------------------------------------------------
	public function getResume($data)
	{
		$html=$this->haut_onglet;
		if(!$data) return $html.$this->getNonTrouve();
		$html.='<div>';
		foreach($data as $lig)
		{
			$html.='<div>';
			$html.=sprintf('<div class="notice_info_titre">%s : %s</div>',
										 $this->_translate->_('Source'),
										 $lig["source"]);
			$html.='<div class="notice_info">'.$lig["texte"].'</div>';
			$html.='</div>';
		}
		$html.='</div>';
		return $html;
	}
	
//------------------------------------------------------------------------------------------------------
// Liste de notices
//------------------------------------------------------------------------------------------------------
	public function getListeNotices($notices, $view, $base_url = BASE_URL) {
		$html=$this->haut_onglet;

		if(!$notices) 
			return $html.$this->getNonTrouve();

		$html.='<table cellspacing="0" width="100%">';

		if($nb = count($notices)>1) 
			$nb = $this->_translate->_("%s livres", $nb); 
		else 
			$nb = $this->_translate->_("%s livre", $nb);

		$read_speaker_helper = new ZendAfi_View_Helper_ReadSpeaker();

		$num=0;
		foreach($notices as $notice)
		{
			$num++;
			$url_notice="document.location.replace('".$base_url."/recherche/viewnotice/id/".$notice["id_notice"]."/type_doc/".$notice["type_doc"]."')";
			$img=Class_WebService_Vignette::getUrl($notice["id_notice"]);
			
			$read_speaker_tag = $read_speaker_helper->readSpeaker('recherche', 
																														'readnotice', 
																														array("id" => $notice["id_notice"]));


			$html.='</tr><tr><td><table><tr><td valign="top">';
			$html.='<table width="80px" cellspacing="0"><tr>';
			$html.='<td class="notice_info_ligne" align="center">';
			$html.=sprintf('<img src="%s" style="%s" width="55" onclick="%s" alt="%s" /></td>',
										 $img["vignette"],
										 'border:1px solid #bfbfbf;cursor:pointer;margin-top:5px',
										 $url_notice,
										 $this->_translate->_('Vignette'));
			$html.='</tr></table><td width="100%" valign="top">';
			$html.='<table align="left">';

			$html.=sprintf('<tr><td align="left" class="notice_info_ligne_titre" onclick="%s" style="cursor:pointer">%s %s</td><td>%s</td></tr>',
										 $url_notice,
										 $view->iconeSupport($notice["type_doc"]),
										 $notice["titre_principal"],
										 $read_speaker_tag);

			foreach (array('Auteur' => 'auteur_principal', 'Année' => 'annee') as $label => $key)
				$html.=sprintf('<tr><td class="notice_info_ligne">%s : %s</td></tr>',
											 $this->_translate->_($label),
											 $notice[$key]);

			$html.='</table>';

			$html.='</td></tr></table>';
			$html.='<tr><td colspan="2" style="background:transparent url('. URL_IMG .'separ.gif) repeat-x scroll center bottom"></td></tr>';
			// Pour la suite
			if($num==5) 
			{
				$id_name="similaires_suite".$num;
				$html.= sprintf('<tr id="similaires_msg%s" style="display:block"><td colspan="2" style="text-align:center;cursor:pointer;" onclick="%s"><a href="#'.$id_name.'">%s</a></td></tr>',
												$num,
												'document.getElementById(\''.$id_name.'\').style.display=\'block\';document.getElementById(\'similaires_msg'.$num.'\').style.display=\'none\';',
												$this->_translate->_('Afficher plus de notices...'));
				$html.='</table><div id="'.$id_name.'" name="'.$id_name.'"style="display:none"><table cellspacing="0" width="100%">';
			}
		}
		$html.='</table>';
		if($num >5) $html.='</div>';
		return $html;
	}

//------------------------------------------------------------------------------------------------------
// Biographie
//------------------------------------------------------------------------------------------------------
	public function getBiographie($data,$notice)
	{
		if(!$data["biographie"]) return $this->getNonTrouve("",true);
		$html=$this->haut_onglet;
		$html.='<table width="100%">';
		$html.=sprintf('<tr><td class="notice_info_titre" align="left" width="100%%">%s<font size="-2">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(%s : %s)</font></td></tr>',
									 $notice["A"],
									 $this->_translate->_('Source'),
									 $data["source"]);
		foreach($data["biographie"] as $ligne)
		{
			if(!$ligne["texte"]) continue;
			if($ligne["liste"])
			{
				$liste='<ul class="notice_info">';
				foreach($ligne["liste"] as $item) $liste.='<li class="notice_liste">'.$item.'</li>';
				$liste.='</ul>';
				$ligne["texte"]=str_replace("@LISTE@",$liste,$ligne["texte"]);
			}
			if($suite)
			{
				if($ligne["titre"]) $html.='<tr><td class="notice_info_titre" align="left" width="100%">'.$ligne["titre"].'</td></tr>';
				$html.='<tr><td class="notice_info" align="left" width="100%">'.$ligne["texte"].'</td></tr>';
			}
			else
			{
				if(!$data["vignette"]) $vignette='';
				else
				{
					$vignette = $this->_translate->_('<img src="%s" border="0" style="%s" alt="%s" />',
																					 $data["vignette"],
																					 'width:100px;float:left;margin-right:5px;cursor:pointer',
																					 $this->_translate->_('Vignette'));
					if($data["image"])
					{
						$id="auteur_".$notice["id_notice"];
						$vignette='<a id="'.$id.'" href="'.$data["image"].'" rel="lightbox" title="'.$notice["A"].'">'.$vignette;
						$vignette.='</a>';
						$vignette.='<script type="text/javascript">$("a[id=\''.$id.'\']").slimbox({}, null, null)</script>';
					}
				}
				
				$html.='<tr><td><table>';
				$html.='<tr><td class="notice_info">'.$vignette;
				$html.=$ligne["texte"].'</td></tr>';
				$html.='</table></td></tr>';
				$suite=true;
			}
		}
		$html.='</table>';
		return $html;
	}

//------------------------------------------------------------------------------------------------------
// Bande annonce
//------------------------------------------------------------------------------------------------------	
	public function getBandeAnnonce($source,$bo)
	{
		$html=$this->haut_onglet;
		if(!$bo) return $html.$this->getNonTrouve();
		$html.='<table width="100%">';
		$html.=sprintf('<tr><td class="notice_info_titre" align="left" colspan="3">%s : %s</td></tr>',
									 $this->_translate->_('Source'),
									 $source);
		$html.='<tr><td><div align="center" style="margin-top:10px;margin-bottom:20px">';
		$html.=$bo;
		$html.='</div></td></tr>';
		$html.='</table>';
		return $html;
	}

//------------------------------------------------------------------------------------------------------
// Interviews
//------------------------------------------------------------------------------------------------------
	public function getInterviews($source,$videos)
	{
		if(!$videos) 
			return $html.$this->getNonTrouve($this->_translate->_("Aucune vidéo n'a été trouvée."),true);

		$html=$this->haut_onglet;
		$html.='<table width="100%" cellspacing="0">';
		$num=0;
		foreach($videos as $video)
		{
			$num++;
			$url=BASE_URL.'/noticeajax/videos?num_video='.$num;
			if($num==1)
			{
				$src=$url;
				$display="block";
			}
			else
			{
				$display="none";
				$src="";
			}
			$onclick="$('iframe[id^=interview]').hide('slow').attr('src','');$('#interview_".$num."').show().attr('src','".$url."')";
			$html.='<tr><td class="notice_info_titre" align="left" width="95%" onclick="'.$onclick.'" style="cursor:pointer">'.$num.' - '.$video["titre"].'</td>';
			$html.=sprintf('<td class="notice_info_titre" align="right" style="%s"><font size="-2">(%s&nbsp;:&nbsp;%s)</font></td></tr>',
										 'padding-left:3px;padding-right:3px',
										 $this->_translate->_('Source'),
										 $source);
			$html.='<tr><td align="left" colspan="2">'.$video["contenu"].'</td></tr>';
			$html.='<tr><td  colspan="2"><div align="center" style="margin-bottom:10px">';
			$html.='<iframe id="interview_'.$num.'" style="display:'.$display.'" height="400" frameborder="0" width="500" scrolling="no" src="'.$src.'"></iframe>';
			$html.='</div></td></tr>';
		}
		$html.='</table>';
		return $html;
	}
	
//------------------------------------------------------------------------------------------------------
// Photos
//------------------------------------------------------------------------------------------------------	
	public function getPhotos($photos)
	{
		$html=$this->haut_onglet;
		if(!$photos) return $html.$this->getNonTrouve($this->_translate->_("Aucune photo n'a été trouvée"));

		// source
		$html.='<table width="100%">';
		$html.='<tr><td class="notice_info_titre" align="left">source : Last.fm</td></tr>';
		$html.='</table>';

		// photos
		$html.='<table class="photo_onglet" cellspacing="5"><tr>';
		$num=0;
		foreach($photos as $photo)
		{
			if( $num % 4 == 0 and $num>0) $html.='</tr><tr>';
			$img = sprintf('<img class="photo" src="%s" title="%s" border="0" onclick="%s" style="cursor:pointer" alt="%s" />',
										 $photo,
										 $this->_translate->_("Agrandir l'image"),
										 "afficher_image('".str_replace("126b","_",$photo)."')",
										 $this->_translate->_("Agrandir l'image"));

			$html.='<td class="photo_onglet">'.$img.'</td>';
			$num++;
		}
		$html.='</tr></table>';
		return $html;
	}
	
//------------------------------------------------------------------------------------------------------	
// Morceaux docs sonores
//------------------------------------------------------------------------------------------------------	
	public function getMorceaux($notice,$source)
	{
		$ix= new Class_Indexation();
		$html=$this->haut_onglet;
		if(!$notice["morceaux"]) return $html.$this->getNonTrouve();
		$html.='<table width="100%">';
		$html.=sprintf('<tr><td class="notice_info_titre" align="left" colspan="4">%s : %s</td></tr>',
									 $this->_translate->_('Source'),
									 $source);
		$volume=0;	
		
		forEach($notice["morceaux"]  as $vol)
		{
			$volume++;
			if($notice["nombre_volumes"]>1) 
				$html.=sprintf('<tr><td class="notice_info_ligne_titre" align="left" colspan="4">%s</td></tr>',
											 $this->_translate->_('Volume n° %s', $volume));

			$plage=0;
			forEach($vol as $morceau)
			{
				$plage++;
				// Amazon
				if($notice["asin"]) 
				{
					$id_div=$notice["asin"]."_".$volume."_".$plage;
					$player=$morceau["url_ecoute"];
					$js_video="chercher_videos('".$id_div."','".addslashes($notice["auteur"])."','".addslashes($morceau["titre"])."')";
					$img_video=sprintf('<img src="%s" border="0" onclick="%s" style="cursor:pointer" title="%s" alt="%s" />',
														 URL_IMG.'bouton/voir_video.gif',
														 $js_video,
														 $this->_translate->_("Clip vidéo"),
														 $this->_translate->_('Voir vidéo'));
					$close=sprintf('<img src="%s" border="0" onclick="%s" style="cursor:pointer" alt="%s" title="%s" />',
												 URL_IMG.'bouton/contracter.gif',
												 "afficher_media('".$id_div."','close','')",
												 $this->_translate->_("Replier"),
												 $this->_translate->_("Replier"));

					//$img_ecoute='<img src="'.URL_IMG.'bouton/ecouter.gif" border="0" onclick="afficher_media(\''.$id_div.'\',\''.$player.'\',\'real_audio\')" style="cursor:pointer" title="Ecouter un extrait">';
				}
				// Last.fm
				else 
				{
					$id_div=$notice["id_notice"]."_".$volume."_".$plage;
					$js_video="chercher_videos('".$id_div."','".addslashes($notice["auteur"])."','".addslashes($morceau["titre"])."')";
					$img_video=sprintf('<img src="%s" border="0" onclick="%s" style="cursor:pointer" title="%s" alt="%s" />',
														 URL_IMG.'bouton/voir_video.gif',
														 $js_video,
														 $this->_translate->_("Clip vidéo"),
														 $this->_translate->_('Voir vidéo') );
					$close=sprintf('<img src="%s" border="0" onclick="%s" style="cursor:pointer" alt="%s" title="%s" />',
												 URL_IMG.'bouton/contracter.gif',
												 "afficher_media('".$id_div."','close','')",
												 $this->_translate->_("Replier"),
												 $this->_translate->_("Replier"));
					//if($morceau["url_ecoute"])$img_ecoute='<img src="'.URL_IMG.'bouton/ecouter.gif" border="0" onclick="afficher_media(\''.$id_div.'\',\''.$morceau["url_ecoute"].'\',\'last_fm\')" style="cursor:pointer" title="Ecouter un extrait">';
					//else $img_ecoute="&nbsp;";
				}
				// Html
				$img_ecoute="&nbsp;";
				$html.='<tr><td class="notice_info_ligne" align="left" width="100%">'.$plage.' : '.$morceau["titre"].'</td><td style="text-align:center">'.$img_ecoute.'</td><td>'.$img_video.'</td><td>'.$close.'</td></tr>';
				$html.='<tr><td colspan="4" style="text-align:center"><div id="'.$id_div.'" rel="video" style="display:none;"></div></td>';
				$html.='</tr>';
			}
		}
		$html.='</table>';
		return $html;
	}
	
//------------------------------------------------------------------------------------------------------
// Bibliographies
//------------------------------------------------------------------------------------------------------
	public function getBibliographie($notices,$auteur)
	{
		$html=$this->haut_onglet;
		if(!$notices) return $html.$this->getNonTrouve();
		$html.='<table cellspacing="0" width="100%">';
		$html.=sprintf('<tr><td class="notice_info_titre" align="left" colspan="4">%s : %s&nbsp;&nbsp;&nbsp;(%s : Last.fm)</td></tr>',
									 $this->_translate->_('Discographie complète de'),
									 $auteur,
									 $this->_translate->_('source'));
		foreach($notices as $notice)
		{
			$html.='</tr><tr><td><table><tr><td valign="top">';
			$html.='<table width="80" cellspacing="0"><tr>';
			$html.='<td class="notice_info_ligne" align="center">';
			$html.=sprintf('<img src="%s" style="%s" alt="%s" /></td>',
										 $notice["vignette"],
										 "border:1px solid;border-color:#bfbfbf;width:55px;margin-top:5px",
										 $this->_translate->_('Vignette'));

			$html.='</tr></table><td width="100%" valign="top">';
			$html.='<table align="left">';
			$html.='<tr><td align="left" class="notice_info_ligne_titre">'.$notice["titre"].'</td></tr>';
			// Infos suplementaires
			if($notice["infos"])
			{
				foreach($notice["infos"] as $info) $html.='<tr><td align="left">'.$info.'</td></tr>';
			}
			$html.='</table>';
			$html.='</td></tr></table>';
			$html.='<tr><td colspan="2" style="background:transparent url('. URL_IMG .'separ.gif) repeat-x scroll center bottom"></td></tr>';
		}
		$html.='</table>';
		return $html;
	}

//------------------------------------------------------------------------------------------------------
// Avis
//------------------------------------------------------------------------------------------------------
	public function getAvis($notice,$avis)	{
		$id_notice = $notice->getId();
		$cls_rating=new ZendAfi_View_Helper_TagRating();
		
		// Identité user connecté
		$user = Zend_Auth::getInstance()->getIdentity();

		// Debut html
		$html=$this->haut_onglet;
		$html.='<table cellspacing="0" width="100%">';

		$url_avis = '';
		if ((Class_AdminVar::get('AVIS_BIB_SEULEMENT') != 1) or ($user->ROLE_LEVEL > 2))
			$url_avis=sprintf('<a class=notice href="%s">&raquo;&nbsp;%s</a>',
												"javascript:fonction_abonne('".$user->ID_USER."','/abonne/avis?id_notice=".$id_notice."')",
												$this->_translate->_('Donnez ou modifiez votre avis'));
		$html.='<tr><td style="text-align:left" colspan="3">'.$url_avis.'</td></tr>';
		$html.='<tr><td colspan="3"><ul class="notice_info" style="margin-top:2px">';

		// Recap du haut
		foreach($avis as $source => $ligne) {
			$ev="aucune évaluation";
			if(0 <= $nb = $avis[$source]["nombre"])	{
				$ev=$nb." évaluation";
				if($nb>1) $ev.="s";
			}

			if(substr($_REQUEST["onglet"],0,4) == "bloc") $fct="infos_bloc"; else $fct="infos_onglet";
			$url_site="javascript:".$fct."('".$_REQUEST["onglet"]."','".$id_notice."','avis','".$source."',1,1)";
			if($nb > 0) 
				$html.='<li>'.$cls_rating->TagRating("note_".$id_notice."_".$source, $ligne["note"]).'&nbsp;&nbsp;<a class="notice" href="'.$url_site.'">'. $avis[$source]["titre"]. ' </a><small>('.$ev.")</small>".$url_bibAvis.'</li>';
		}
		$html.='</ul></td></tr>';

		// Avis page courante
		$source=$_REQUEST["cherche"];
		if(!$source) {
			if($avis["bib"]["nombre"] > 0) $source="bib";
			elseif($avis["abonne"]["nombre"] > 0) $source="abonne";
		}

		if($source)	{
			$avis_helper = new ZendAfi_View_Helper_Avis();

			$html.='<tr><td colspan="3">&nbsp;</td></tr>';
			$html.='<tr><td class="notice_info_ligne_titre" align="left" colspan="3">' . $avis[$source]["titre"] . '</td></tr>';
			if($avis[$source]["nombre"]>0)
			{
				$num=0;
				foreach($avis[$source]["liste"] as $detail) {
					if ($detail->isVisibleForUser($user))
						$html .= '<tr><td colspan="3">'.$avis_helper->contenu_avis($detail).'</td>';
				}

				if($avis[$source]["nb_pages"]>1)
				{
					$pager=new ZendAfi_View_Helper_Pager();
					$lien="javascript:".$fct."('".$_REQUEST["onglet"]."','".$id_notice."','avis','".$source."',1,@PAGE@)";
					$urlPagesHtml = $pager->Pager($avis[$source]["nombre"], 5, $_REQUEST["page"],$lien);
					$html.='<tr><td colspan="3" class="notice_info" style="text-align:center"><b>'.$urlPagesHtml.'</b></td></tr>';
				}
			}
			else $html.='<tr><td colspan="3">&nbsp;</td></tr><tr><td colspan="3" class="notice_info" style="text-align:left">'.
						 $this->_translate->_('Aucun avis pour le moment').
						 '</td></tr><tr><td colspan="3">&nbsp;</td></tr>';
		}

		// Fin
		$html.='</table>';
		return $html;
	}


//------------------------------------------------------------------------------------------------------	
// Tags utilisateur
//------------------------------------------------------------------------------------------------------	
	public function getTags($tags,$id_notice)
	{
		// Identité user connecté
		$user = Zend_Auth::getInstance()->getIdentity();
		
		$html=$this->haut_onglet;
		$nuage=new ZendAfi_View_Helper_NuageTags();
		$html.=$nuage->nuageTags($tags,array("calcul" => 3));
		$url="javascript:fonction_abonne('".$user->ID_USER."','/abonne/tagnotice?id_notice=".$id_notice."')";
		$html.=sprintf('<div style="text-align:left"><a href="%s">&raquo;&nbsp;%s</a></div>',
									 $url,
									 $this->_translate->_('Proposer des tags pour cette notice'));
		return $html;
	}
}