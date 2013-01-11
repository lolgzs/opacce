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
//////////////////////////////////////////////////////////////////////////////////////
// CLASSE LISTE DE NOTICES
//////////////////////////////////////////////////////////////////////////////////////

require_once("classe_unimarc.php");

class liste_notices
{
	private $nb_par_page;											// Nombre de notices par page
	private $page;														// Derniere page demandee
	private $fin;															// On atteint la fin de la liste
	
// ----------------------------------------------------------------
// Constructeur
// ----------------------------------------------------------------
	function __construct()
	{
		$this->nb_par_page=20;
	}
// ----------------------------------------------------------------
// Lance une requete et rend les id_notices
// ----------------------------------------------------------------
	public function getListe($req,$page)
	{
		global $sql;
		if(!$page) $page=1;
		$limit = ($page-1) * $this->nb_par_page;
		$limit = " LIMIT ".$limit.",". $this->nb_par_page;
		
		$liste=$sql->fetchAll($req.$limit);
		if(!$liste) return false;
		foreach($liste as $notice)
		{
			if($notice["id_notice"]) $ret[]=$notice["id_notice"];
			if($notice["id_article"]) $ret[]=$notice["id_article"];
		}
		$this->page=$page;
		if(count($ret) < $this->nb_par_page ) $this->fin=true; else $this->fin=false;
		return $ret;
	}
// ----------------------------------------------------------------
// Lance une requete a partir d'une colonne et rend les id_notices
// ----------------------------------------------------------------
	public function getListeByColonne($req,$colonne,$page,$exemplaire=false)
	{
		global $sql;
		if(!$page) $page=1;
		$limit = ($page-1) * $this->nb_par_page;
		$limit = " LIMIT ".$limit.",". $this->nb_par_page;
		
		$liste=$sql->fetchAll($req.$limit);
		if(!$liste) return false;
		foreach($liste as $notice)
		{ 
			$valeur=$notice[$colonne];
			if($exemplaire == true ) $ret[]=$sql->fetchOne("select id_notice from exemplaires where ".$colonne." ='$valeur'");
			else 
			{
				$ids=$sql->fetchAll("select id_notice from notices where ".$colonne." ='$valeur' order by ".$colonne."");
				foreach($ids as $id) $ret[]=$id["id_notice"];
			}
		}
		$this->page=$page;
		if(count($ret) < $this->nb_par_page ) $this->fin=true; else $this->fin=false;
		return $ret;
	}
// ----------------------------------------------------------------
// Lance une requete a partir de plusieurs colonnes et rend les id_notices
// ----------------------------------------------------------------
	public function getListeByColonnes($req,$page,$exemplaire=false)
	{
		global $sql;
		if(!$page) $page=1;
		$limit = ($page-1) * $this->nb_par_page;
		$limit = " LIMIT ".$limit.",". $this->nb_par_page;
		
		$liste=$sql->fetchAll($req.$limit);
		if(!$liste) return false;
		foreach($liste as $enreg)
		{
			$req_ligne="";
			foreach($enreg as $col => $valeur)
			{
				if($req_ligne) $req_ligne.=" and ";
				$req_ligne .="$col ='$valeur'";
			}
			if($exemplaire == true) $req_ligne="select id_notice from exemplaires where ".$req_ligne;
			else $req_ligne="select id_notice from notices where ".$req_ligne;
			$ids=$sql->fetchAll($req_ligne);
			foreach($ids as $id) $ret[]=$id["id_notice"];
		}
		$this->page=$page;
		if(count($ret) < $this->nb_par_page ) $this->fin=true; else $this->fin=false;
		return $ret;
	}

// ----------------------------------------------------------------
// Rend le html d'une liste a partir d'une liste d'id_notices
// ----------------------------------------------------------------
	public function getHtml($liste_id,$args_url,$type_doc=false)
	{
		// articles de périodiques
		if($type_doc==100) return $this->getHtmlArticles($liste_id,$args_url);

		global $sql;
		$unimarc=new notice_unimarc();
		
		$html[]='<div><table><tr>';
		$html[]='<th>&nbsp;</th>';
		$html[]='<th>Type</th>';
		$html[]='<th>Titre</th>';
		$html[]='<th>Auteur</th>';
		$html[]='<th>Editeur</th>';
		$html[]='<th>Année</th>';
		$html[]='</tr>';
		foreach($liste_id as $id_notice)
		{
			$bloc=$sql->fetchEnreg("select type_doc,unimarc from notices where id_notice=$id_notice");
			$type_doc=getLibCodifVariable("types_docs",$bloc["type_doc"]);
			$unimarc->ouvrirNotice($bloc["unimarc"],0,0);
			$titre=$unimarc->getTitrePrincipal();
			$url=rendUrlImg("loupe.png", "analyse_afficher_notice_full.php","id_notice=".$id_notice);
			
			$html[]='<tr>';
			$html[]='<td>'.$url.'</td>';
			$html[]='<td>'.$type_doc.'</td>';
			$html[]='<td>'.$titre.'</td>';
			$html[]='<td>'.$unimarc->getAuteurs(true).'</td>';
			$html[]='<td>'.$unimarc->getEditeur().'</td>';
			$html[]='<td>'.$unimarc->getAnnee().'</td>';
			$html[]='</tr>';
		}
		$html[]='</table>';
		
		// Pager
		$url=$_SERVER["PHP_SELF"];	
		$args=$args_url."&page=";
		$page=$this->page;
		if(!$this->fin) 
		{
			$suivant=rendBouton("Page suivante",$url,$args.($page+1));
		}
		if($page > 1)
		{
			$premier=rendBouton("Retour au début",$url,$args."1").str_repeat("&nbsp;",5);
			$precedent=rendBouton("Page précédente",$url,$args.($page-1)).str_repeat("&nbsp;",5);
		}
		$html[]=BR.'<div>&nbsp;&nbsp;'.$premier.$precedent.$suivant.$dernier.'</div></center>'.BR.BR.'</div>';	
		return implode($html);
	}

// ----------------------------------------------------------------
// Rend le html d'une liste d'articles de périodiques
// ----------------------------------------------------------------
	public function getHtmlArticles($liste_id,$args_url)
	{
		global $sql;
		$unimarc=new notice_unimarc();

		$html[]='<div><table><tr>';
		$html[]='<th>&nbsp;</th>';
		$html[]='<th>Titre du numéro</th>';
		$html[]='<th>Article</th>';
		$html[]='<th>Auteur</th>';
		$html[]='<th>Année</th>';
		$html[]='<th>Statut</th>';
		$html[]='</tr>';
		foreach($liste_id as $id_article)
		{
			$bloc=$sql->fetchEnreg("select * from notices_articles where id_article=$id_article");
			$titre_numero=$bloc["clef_chapeau"]." n° ".$bloc["clef_numero"];
			// pas d'unimarc (notice opsys incorrecte)
			if($bloc["unimarc"])
			{
				$unimarc->ouvrirNotice($bloc["unimarc"],0,0);
				$notice=$unimarc->getNoticeIntegrationArticlePeriodique();
				$titre=$unimarc->getTitrePrincipal();
				$auteur=$unimarc->getAuteurs(true);
				$annee=$unimarc->getAnnee();
				$url=rendUrlImg("loupe.png", "analyse_afficher_notice_periodique.php","id_notice=".$id_article);

				// controle chapeau
				if($bloc["clef_chapeau"])	$statut="ok";
				else
				{
					$statut='<span style="color:red">notice orpheline sans notice mère</span>';
					$titre_numero="";
				}
			}
			else
			{
				$statut='<span style="color:red">notice orpheline sans unimarc</span>';
				$titre="";
				$auteur="";
				$annee="";
				$url="&nbsp;";
			}

			// html
			$html[]='<tr>';
			$html[]='<td>'.$url.'</td>';
			$html[]='<td>'.$titre_numero.'</td>';
			$html[]='<td>'.$titre.'</td>';
			$html[]='<td>'.$auteur.'</td>';
			$html[]='<td>'.$annee.'</td>';
			$html[]='<td>'.$statut.'</td>';
			$html[]='</tr>';
		}
		$html[]='</table>';

		// Pager
		$url=$_SERVER["PHP_SELF"];
		$args=$args_url."&page=";
		$page=$this->page;
		if(!$this->fin)
		{
			$suivant=rendBouton("Page suivante",$url,$args.($page+1));
		}
		if($page > 1)
		{
			$premier=rendBouton("Retour au début",$url,$args."1").str_repeat("&nbsp;",5);
			$precedent=rendBouton("Page précédente",$url,$args.($page-1)).str_repeat("&nbsp;",5);
		}
		$html[]=BR.'<div>&nbsp;&nbsp;'.$premier.$precedent.$suivant.$dernier.'</div></center>'.BR.BR.'</div>';
		return implode($html);
	}
}
?>