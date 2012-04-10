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
///////////////////////////////////////////////////////////////////////////////////////
// OPAC 3 :STATISTIQUES SUR LES NOTICES
///////////////////////////////////////////////////////////////////////////////////////

class Class_StatsNotices
{
	private $url_google;						// Url google stats pour graphe
	private $lib_mois;							// Mois en francais

// ----------------------------------------------------------------
// Constructeur : initialise la black-list pour les robots
// ----------------------------------------------------------------
	function __construct()
	{
		$this->url_google="http://chart.apis.google.com/chart?";
		$this->lib_mois=array("","janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre");
	}

// ----------------------------------------------------------------
// Incremente stat visualisation
// ----------------------------------------------------------------
	public function addStatVisu($id_notice)
	{
		// Controle black list robots
		$robots = explode(";",getVar("BLACK_LIST_ROBOT"));
		$client = null;
		if (array_isset("REMOTE_HOST", $_SERVER))
			$client = $_SERVER["REMOTE_HOST"];
			
		if (!$client and array_isset('REMOTE_ADDR', $_SERVER)) 
			$client = str_replace("-",".",$_SERVER["REMOTE_ADDR"]);

		if (!$client) return;

		foreach($robots as $robot) if( trim($robot) and striPos($client,$robot) !== false ) return;
		
		// Add stat
		$annee=date("Y");
		$mois=date("m");
		sqlExecute("update notices set nb_visu = nb_visu + 1 where id_notice=$id_notice");
		$controle=fetchOne("select count(*) from stats_notices where annee=$annee and mois=$mois");
		if(!$controle) sqlExecute("insert into stats_notices(annee,mois) Values($annee,$mois)");
		sqlExecute("update stats_notices set nb_visu = nb_visu + 1 where annee=$annee and mois=$mois");
	}

// ----------------------------------------------------------------
// Incremente stat réservations
// ----------------------------------------------------------------
	public function addStatReservation($id_notice)
	{
		// Controle black list robots
		$robots=explode(";",getVar("BLACK_LIST_ROBOT"));
		$client=$_SERVER["REMOTE_HOST"];
		if(!$client) $client=str_replace("-",".",$_SERVER["REMOTE_ADDR"]);
		foreach($robots as $robot) if(trim($robot) and striPos($client,$robot) !== false) return;
		
		// Add stat
		$annee=date("Y");
		$mois=date("m");
		sqlExecute("update notices set nb_resa = nb_resa + 1 where id_notice=$id_notice");
		$controle=fetchOne("select count(*) from stats_notices where annee=$annee and mois=$mois");
		if(!$controle) sqlExecute("insert into stats_notices(annee,mois) Values($annee,$mois)");
		sqlExecute("update stats_notices set nb_resa = nb_resa + 1 where annee=$annee and mois=$mois");
	}
	
// ----------------------------------------------------------------
// Periode d'analyse
// ----------------------------------------------------------------
	public function getPeriode($annee=0,$mois=0){
		if($mois) $msg=$this->lib_mois[$mois];
		if($annee) $msg.=" ".$annee;
		if($msg)$msg="en ".$msg;
		else
		{
			// Recherche bornes maxi
			if ($annee=fetchOne("select min(annee) from stats_notices"))
				$mois=fetchOne("select min(mois) from stats_notices where annee=$annee");
			$msg="depuis ".$this->lib_mois[$mois]." ".$annee;
		}
		return $msg;
	}

// ----------------------------------------------------------------
// Récapitulatif Visualisations de notices
// ----------------------------------------------------------------
	public function getRecapVisu()
	{
		// Par années
		$liste=fetchAll("Select annee,sum(nb_visu) from stats_notices group by 1");
		$total=0;
		foreach($liste as $stat)
		{
			$annee=$stat["annee"];
			$nombre=$stat["sum(nb_visu)"];
			$total+=$nombre;
			$ret["annees"][$annee]=$nombre;
		}
		$ret["total"]=$total;
		$ret["graphes"]["annees"]=$this->getGraphe($ret["annees"],$total);
		
		// Par mois
		$liste=fetchAll("Select mois,sum(nb_visu) from stats_notices group by 1");
		foreach($liste as $stat)
		{
			$mois=$this->lib_mois[$stat["mois"]];
			$nombre=$stat["sum(nb_visu)"];
			$ret["mois"][$mois]=$nombre;
		}
		$ret["graphes"]["mois"]=$this->getGraphe($ret["mois"],$total);
		return $ret;
	}

// ----------------------------------------------------------------
// Palmares des visualisations de notices
// ----------------------------------------------------------------
	public function getPalmaresVisu($type_doc)
	{
		$cls_notice=new Class_Notice();
		if($type_doc) $where = " and type_doc=".$type_doc;
			
		// Lancer la requete
		$req="select id_notice,nb_visu from notices Where nb_visu > 0".$where." order by 2 desc LIMIT 0,50";
		$liste=fetchAll($req);
		foreach($liste as $item)
		{
			$id_notice=$item["id_notice"];
			$nombre=$item["nb_visu"];
			$notice=$cls_notice->getNotice($id_notice,"TA");
			$lig["id_notice"]=$id_notice;
			$lig["nombre"]=$nombre;
			$lig["type_doc"]=$notice["type_doc"];
			$lig["titre"]=$notice["T"];
			$lig["auteur"]=$notice["A"];
			$ret[]=$lig;
		}
		return $ret;
	}

// ----------------------------------------------------------------
// Récapitulatif Réservations de notices
// ----------------------------------------------------------------
	public function getRecapReservation()
	{
		// Par années
		$liste=fetchAll("Select annee,sum(nb_resa) from stats_notices group by 1");
		$total=0;
		foreach($liste as $stat)
		{
			$annee=$stat["annee"];
			$nombre=$stat["sum(nb_resa)"];
			$total+=$nombre;
			$ret["annees"][$annee]=$nombre;
		}
		$ret["total"]=$total;
		$ret["graphes"]["annees"]=$this->getGraphe($ret["annees"],$total);
		
		// Par mois
		$liste=fetchAll("Select mois,sum(nb_resa) from stats_notices group by 1");
		foreach($liste as $stat)
		{
			$mois=$this->lib_mois[$stat["mois"]];
			$nombre=$stat["sum(nb_resa)"];
			$ret["mois"][$mois]=$nombre;
		}
		$ret["graphes"]["mois"]=$this->getGraphe($ret["mois"],$total);
		return $ret;
	}

// ----------------------------------------------------------------
// Palmares des Réservations de notices
// ----------------------------------------------------------------
	public function getPalmaresReservation($type_doc)
	{
		$cls_notice=new Class_Notice();
		if($type_doc) $where = " and type_doc=".$type_doc;
			
		// Lancer la requete
		$req="select id_notice,nb_resa from notices Where nb_resa > 0".$where." order by 2 desc LIMIT 0,50";
		$liste=fetchAll($req);
		foreach($liste as $item)
		{
			$id_notice=$item["id_notice"];
			$nombre=$item["nb_resa"];
			$notice=$cls_notice->getNotice($id_notice,"TA");
			$lig["id_notice"]=$id_notice;
			$lig["nombre"]=$nombre;
			$lig["type_doc"]=$notice["type_doc"];
			$lig["titre"]=$notice["T"];
			$lig["auteur"]=$notice["A"];
			$ret[]=$lig;
		}
		return $ret;
	}

// ----------------------------------------------------------------
// Graphe google stats
// ----------------------------------------------------------------
	public function getGraphe($data_graphe,$total)
	{
		$nb_rubriques=count($data_graphe);
		if(!$nb_rubriques) return false;
		
		// Constituer les arguments pour google
		forEach($data_graphe as $libelle => $nombre)
		{
			if($nombre and $total) $pct=intval(($nombre/$total) *100); else $pct=0;
			if($chd) {$chd.=","; $chl.="|";}
			$chd.=$pct;
			$chl.=$libelle;
		}
		if($nb_rubriques < 5) $taille="chs=200x120&amp;cht=bvg";
		else $taille="chs=450x200&amp;cht=p3";
		$url_google=$this->url_google.$taille."&amp;chd=t:".$chd."&amp;chl=".$chl;
		$html='<img src="'.$url_google.'" border="0">';
		return $html;
	}

}
?>