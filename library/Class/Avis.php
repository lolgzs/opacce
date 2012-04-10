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
// OPAC3: classe de Gestion des Avis sur les documents
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class Class_Avis
{
	var $sql;	// Curseur sql
	var $_today;

	//------------------------------------------------------------------------------------------------------  
	// Constructeur
	//------------------------------------------------------------------------------------------------------  
	function __construct()
	{
		$this->sql = Zend_Registry::get('sql');
		$class_date = new Class_Date();
		$this->_today = $class_date->DateTimeDuJour();
	}

	//------------------------------------------------------------------------------------------------------  
	// html Avis pour le CMS pour les boites
	//------------------------------------------------------------------------------------------------------  
	function getCmsAvisById($id_user,$id_news)
	{
		// News
		$req_news = "Select * from cms_avis Where ID_CMS=$id_news AND ID_USER=$id_user";
		return($this->sql->fetchAll($req_news));
	}
    
	//------------------------------------------------------------------------------------------------------  
	// Ecrire 1 avis (update si existe déjà)
	//------------------------------------------------------------------------------------------------------  
	function ecrireCmsAvis($id_user,$role_level,$id_news,$note,$entete,$avis) 
	{
		$modo_avis_abo = getVar('MODO_AVIS'); // 0 apres / 1 avant de publier sur le site
		$modo_avis_bib = getVar('MODO_AVIS_BIBLIO'); // 0 apres / 1 avant de publier sur le site

		if($role_level < 3)
			{
				$abon_ou_bib=0;
				if($modo_avis_abo == 1){$statut = 0;}
				if($modo_avis_abo == 0){$statut = 1;}
			}
		else 
			{
				$abon_ou_bib=1;
				if($modo_avis_bib == 1) $statut=0;
				if($modo_avis_bib == 0) $statut=1;
			}

		try{
			$entete=strLeft($this->sql->quote($entete),100);
			$avis=$this->sql->quote($avis);
			$controle=fetchOne("select count(*) from cms_avis where ID_USER=$id_user and ID_CMS=$id_news");
			if(!$controle) 
				$req="Insert into cms_avis(ID_USER,ID_NOTICE,ID_CMS,DATE_AVIS,DATE_MOD,NOTE,ENTETE,AVIS,STATUT,ABON_OU_BIB) values($id_user,'',$id_news,'$this->_today','','$note',$entete,$avis,$statut,'$abon_ou_bib')";
			else 
				$req="Update cms_avis Set NOTE='$note',ENTETE=$entete,AVIS=$avis,STATUT=$statut,ABON_OU_BIB='$abon_ou_bib' Where ID_USER=$id_user and ID_CMS=$id_news";
			$stmt = $this->sql->query($req);
			
			if($modo_avis_abo == 0 && $role_level < 3) {$this->maj_note_cms($id_news,$abon_ou_bib);}
			if($modo_avis_bib == 0 && $role_level >= 3) {$this->maj_note_cms($id_news,$abon_ou_bib);}
            
		}catch (Exception $e){
			logErrorMessage('Class: Class_Avis; Function: ecrireCmsAvis' . NL . $req . NL . $e->getMessage());
			return false;
		}
	}

	//------------------------------------------------------------------------------------------------------  
	// Suppression d'un avis
	//------------------------------------------------------------------------------------------------------ 
	public function supprimerCmsAvis($id_user,$id_news)
	{
		sqlExecute("delete from cms_avis where ID_USER=$id_user and ID_CMS=$id_news");
		$role_level=fetchOne("select ROLE_LEVEL from bib_admin_users where ID_USER=$id_user");
		if($role_level < 3) $abon_ou_bib=0; else $abon_ou_bib=1;
		$this->maj_note_cms($id_news,$abon_ou_bib);
	}
	//------------------------------------------------------------------------------------------------------  
	// Maj note et rang notice
	//------------------------------------------------------------------------------------------------------  
	function maj_note_cms($id_news,$abon_ou_bib)
	{
		$sqlStmt = "Select count(*), avg(NOTE) From cms_avis Where ID_CMS=$id_news and ABON_OU_BIB='$abon_ou_bib' AND STATUT=1";
		$data = $this->sql->fetchAll($sqlStmt);
		$note=round($data[0]["avg(NOTE)"],1);
		if
			(strlen($note)==1) $note.=".0";
		else	{
				$dec=strRight($note,1);
				if($dec<3) $dec="0"; elseif($dec<"8")$dec="5";else {$note+=1; $dec=0;}
				$note=strLeft($note,1).".".$dec;
			}

		$nombre=$data[0]["count(*)"];
		// UPDATE `opac3`.`cms_avis` SET `STATUT` = '0' WHERE `cms_avis`.`ID_CMS` =1;
		// Lire enreg rank
		$enreg=fetchEnreg("select * from cms_rank where ID_CMS=$id_news");
		$sqlStmt = "Delete from cms_rank Where ID_CMS=$id_news";
		$stmt = $this->sql->query($sqlStmt);
		if($abon_ou_bib==1)
			{
				$abon_nombre_avis=$enreg["ABON_NOMBRE_AVIS"];
				$abon_note=$enreg["ABON_NOTE"];
				$bib_nombre_avis=$nombre;
				$bib_note=$note;
			}
		else
			{
				$abon_nombre_avis=$nombre;
				$abon_note=$note;
				$bib_nombre_avis=$enreg["BIB_NOMBRE_AVIS"];
				$bib_note=$enreg["BIB_NOTE"];
			}
    
		if(!$abon_nombre_avis) $abon_nombre_avis=0;
		if(!$bib_nombre_avis) $bib_nombre_avis=0;
		//if(!$abon_nombre_avis and !$bib_nombre_avis) return false;
		$sqlStmt = "Insert Into cms_rank(ID_CMS,ABON_NOMBRE_AVIS,ABON_NOTE,BIB_NOMBRE_AVIS,BIB_NOTE) Values($id_news,$abon_nombre_avis,'$abon_note',$bib_nombre_avis,'$bib_note')";
		$stmt = $this->sql->query($sqlStmt);	
	}
    
	function HtmlCmsAvecAvis($id_news)
	{
		// News
		$req_news = "Select * from cms_article Where ID_ARTICLE=$id_news";
		$news = $this->sql->fetchAll($req_news);
        
		if($news[0]["AVIS"] == 1)
			{
				$sqlStmt = "Select * from cms_rank Where ID_CMS=$id_news";
				$ret = $this->sql->fetchAll($sqlStmt);

				$nb_avis = (int)$ret[0]["BIB_NOMBRE_AVIS"] + (int)$ret[0]["ABON_NOMBRE_AVIS"];
				if($nb_avis ==0) return ('<div align="right"><a href="'.BASE_URL.'/cms/articleview/id/'.$id_news.'">&raquo;aucun avis</a></div>');
				else return ('<div align="right"><a href="'.BASE_URL.'/cms/articleview/id/'.$id_news.'">&raquo; avis ('.$nb_avis.')</a></div>');
			}
		else
			{
				return(' ');
			}	
	}
    
	function rendHtmlBlockAvis($id_news)
	{
		$class_user = new Class_Users();
		$user = Zend_Auth::getInstance()->getIdentity();
		$info_bib = $this->rendInfoCmsAvis($id_news,1);
		$info_abo = $this->rendInfoCmsAvis($id_news,0);
        
		$sqlStmt = "Select * from cms_rank Where ID_CMS=$id_news";
		$ret = $this->sql->fetchAll($sqlStmt);
		$nb_avis = (int)$ret[0]["BIB_NOMBRE_AVIS"] + (int)$ret[0]["ABON_NOMBRE_AVIS"];
		if($nb_avis ==0) $txt_nb_avis = "&nbsp;Aucun avis";
		else $txt_nb_avis = "&nbsp;Avis (".$nb_avis.")";
       
		$html.='<div class="avis_show_avis" onclick="showCmsAvis('.$id_news.', this)"><img src="'.URL_IMG.'bouton/plus_carre.gif" style="float:left" border="0" alt="Voir les avis" title="Voir les avis" id="plus"/> '.$txt_nb_avis.' </div>';
		$html.='<div id="avis_'.$id_news.'" style="display:none;padding-left:5px;">';
		$html.='<br />';
		$html.='<a href="#" onclick="javascript:fonction_abonne(\''.$user->ID_USER.'\',\'/opac/abonne/cmsavis?id='.$id_news.'\')">&raquo; Donner ou modifier votre avis</a>';
		$html.='<ul class="notice_info">';
		$html.='<li>'.$info_bib["NOTE"].' <a href="#" onclick="showAvis('.$id_news.',\'bib\');return false;">Avis de bibliothécaires</a> '.$info_bib["AVIS"].'</li>';
		$html.='<li>'.$info_abo["NOTE"].' <a href="#" onclick="showAvis('.$id_news.',\'abo\');return false;">Avis de lecteurs du portail</a> '.$info_abo["AVIS"].'</li>';
		$html.='</ul>';
        
		$modo_avis_bib = getVar('MODO_AVIS_BIBLIO');
		if($modo_avis_bib == 1) $view = 1;
		if($modo_avis_bib == 0) $view = "";
        
		$liste_avis = $this->getCmsAvisBiblio($id_news,$view);
		if(count($liste_avis) == 0)$style="none"; else $style="block";
		// Tableau bib
		$html.='<div id="bib_'.$id_news.'" style="display:'.$style.'"><table cellpadding="0" cellspacing="0" border="0" style="width:100%">';
		$html.='<tr><td class="avis_from">Avis des bibliothécaires</td></tr>';
		$html.='<tr><td>'.$this->rendHTMLCmsAvis($liste_avis,1).'</td></tr>';
		$html.='</table></div>';
        
		$modo_avis_abo = getVar('MODO_AVIS');
		if($modo_avis_abo == 1) $view = 1;
		if($modo_avis_abo == 0) $view = "";
        
		// Tableau Abonne
		$liste_avis_abo = $this->getCmsAvisAbo($id_news,$view);
		if(count($liste_avis_abo) == 0)$style="none"; else $style="block";
		$html.='<div id="abo_'.$id_news.'" style="display:'.$style.'"><table cellpadding="0" cellspacing="0" border="0" style="width:100%">';
		$html.='<tr><td class="avis_from">Avis des lecteurs du portail</td></tr>';
		$html.='<tr><td>'.$this->rendHTMLCmsAvis($liste_avis_abo,0).'</td></tr>';
        
		$html.='</table></div>';
		$html.='</div>';
		$html.='<div style="width:100%;background:transparent url('.URL_IMG.'box/menu/separ.gif) repeat-x scroll center bottom">&nbsp;</div>';
        
		$sqlStmt = "Select * from cms_avis Where ID_CMS=$id_news order by DATE_AVIS DESC";
		$ret = count($this->sql->fetchAll($sqlStmt));
        
		return($html);
	}
    
	public function rendInfoCmsAvis($id_news,$abon_ou_bib)
	{
		$sqlStmt = "Select * from cms_rank Where ID_CMS=$id_news";
		$data = $this->sql->fetchAll($sqlStmt);
		$data = $data[0];

		if($abon_ou_bib == 0)	{
				if($data["ABON_NOMBRE_AVIS"] == 0 || $data["ABON_NOMBRE_AVIS"] == null) $nb_eva = "(aucune évaluation)";
				elseif($data["ABON_NOMBRE_AVIS"] == 1) $nb_eva = "(1 évaluation)";
				elseif($data["ABON_NOMBRE_AVIS"] > 1) $nb_eva = "(".$data["ABON_NOMBRE_AVIS"]." évaluations)";
            
				$note = $data["ABON_NOTE"];
		}	else {
				if($data["BIB_NOMBRE_AVIS"] == 0 || $data["BIB_NOMBRE_AVIS"] == null) $nb_eva = "(aucune évaluation)";
				elseif($data["BIB_NOMBRE_AVIS"] == 1) $nb_eva = "(1 évaluation)";
				elseif($data["BIB_NOMBRE_AVIS"] > 1) $nb_eva = "(".$data["BIB_NOMBRE_AVIS"]." évaluations)";

				$note = $data["BIB_NOTE"];
		}

		$note_r = str_replace('.','-',$note);
		$note_r = str_replace('-0','',$note_r);
		if ($note_r == '') $note_r = "0";
		$img = '<img src="'.URL_ADMIN_IMG.'stars/stars-'.$note_r.'.gif"  alt="note:'.$note.'" border="0"/>';
		$info["NOTE"] = $img;

		$info["AVIS"] = $nb_eva;
		return($info);
	}
    
	// rend avis biblio par id_news
	function getCmsAvisBiblio($id_news,$statut = "")
	{
		if($statut == 1) {$sqlStmt = "Select * from cms_avis Where ID_CMS=$id_news AND ABON_OU_BIB=1 AND STATUT=1 order by DATE_AVIS DESC";}
		if($statut == 0) {$sqlStmt = "Select * from cms_avis Where ID_CMS=$id_news AND ABON_OU_BIB=1 AND STATUT=0 order by DATE_AVIS DESC";}
		if($statut =="") {$sqlStmt = "Select * from cms_avis Where ID_CMS=$id_news AND ABON_OU_BIB=1  order by DATE_AVIS DESC";}

		return($this->sql->fetchAll($sqlStmt));
	}

	// rend avis Abonne par id_news
	function getCmsAvisAbo($id_news,$statut = "")
	{
		if($statut == 1) {$sqlStmt = "Select * from cms_avis Where ID_CMS=$id_news AND ABON_OU_BIB=0 AND STATUT=1 order by DATE_AVIS DESC";}
		if($statut == 0) {$sqlStmt = "Select * from cms_avis Where ID_CMS=$id_news AND ABON_OU_BIB=0 AND STATUT=0 order by DATE_AVIS DESC";}
		if($statut ==""){$sqlStmt = "Select * from cms_avis Where ID_CMS=$id_news AND ABON_OU_BIB=0  order by DATE_AVIS DESC";}
    
		return($this->sql->fetchAll($sqlStmt));
	}
    
	// type =0 -> rend bulle blanche pour abonne
	// type =1 -> rend bulle bleue pour bibliothecaire
	function rendHTMLCmsAvis($avis_array,$type=0)
	{
		$class_user = new Class_Users();
        
		if(is_array($avis_array))
			{
				foreach($avis_array as $avis)
					{
						$user = $class_user->getUser($avis["ID_USER"]);

						$zendDate =  new Zend_Date($avis["DATE_AVIS"]);
						$date_avis = $zendDate->toString('dd-MM-yyyy');
						$txt_avis = urlencode($avis["AVIS"]);
						$contenu_avis = str_replace('%0D%0A','<br />',$txt_avis );

						$img=URL_ADMIN_IMG."stars/stars-".str_replace(".",",",$avis["NOTE"]).".gif"; $img=str_replace(",0","",$img);

						$html[]='<style type="text/css">';
						$html[]='table.avis {border: 2px solid #ddd; padding: 4px; -moz-border-radius: 5px; -webkit-border-radius: 5px}';
						$html[]='table.avis tr:first-child {font-weight: bold;}';
						$html[]='table.avis tr:first-child td {padding-bottom: 10px;}';
						$html[]='table.avis tr:first-child td:last-child {text-align:right}';
						$html[]='table.avis tr:last-child td:last-child {text-align:right}';
						$html[]='</style>';


						$html[]='<table class="avis">';
						$html[]='<tr>';
						$html[]='<td>'. $avis["ENTETE"] .'</td>';
						$html[]='<td><img src="'.$img.'"></td>';
						$html[]='</tr>';
						$html[]='<tr>';
						$html[]='<td colspan="2">';
						$html[]='<img src="'.URL_ADMIN_IMG.'avis/quote_up.jpg"/>';
						$html[]='&nbsp;'.urldecode($contenu_avis).'&nbsp;';
						$html[]='<img src="'.URL_ADMIN_IMG.'avis/quote_down.jpg" />';
						$html[]='</td>';
						$html[]='</tr>';
						$html[]='<tr>';
						$html[]='<td></td>';
						$html[]='<td>';
						$html[]='<div style="padding-bottom:10px;">';
						$html[]='par : <b>'.$class_user->getNomAff($user['ID_USER']).'</b>&nbsp;le&nbsp;<i>'.$date_avis.'</i></div></td>';
						$html[]='</tr>';
						$html[]='</table>';
					}
				if(is_array($html)) return(implode("",$html));
				else return('<div style="padding-left:7px">&nbsp;Aucun avis pour le moment.</div>');
			}
		else return(' ');
	}
}