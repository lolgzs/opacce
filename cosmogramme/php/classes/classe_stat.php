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
////////////////////////////////////////////////////////////////////////////////////////
// STATISTIQUES
///////////////////////////////////////////////////////////////////////////////////////

class statistique
{
	private $url_google;						// Url google stats pour graphe
	private $lib_mois;							// Mois en francais
	
// ----------------------------------------------------------------
// Constructeur : initialise l'url google pour les graphes
// ----------------------------------------------------------------
	function __construct()
	{
		$this->url_google="http://chart.apis.google.com/chart?";
		$this->lib_mois=array("","janvier","février","mars","avril","mai","juin","juillet","août","septembre","octobre","novembre","décembre");
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
			if(!$total) $pct=0;
			else $pct=intval(($nombre/$total) *100);
			if($chd) {$chd.=","; $chl.="|";}
			$chd.=$pct;
			$chl.=str_replace(" ","%20",$libelle); // Ca ne supporte pas les accents
		}
		$taille="chs=450x200&amp;cht=p3&amp;&chbh=a"; //p3
		$url_google=$this->url_google.$taille."&amp;chd=t:".$chd."&amp;chl=".$chl;
		$html='<img src="'.$url_google.'" border="0">';
		return $html;
	}

// ----------------------------------------------------------------
// Ometer google	
// ----------------------------------------------------------------
	public function getGoogleMeter($nombre)
	{
			$url=$this->url_google;
			$url.="&amp;chs=60x30&amp;cht=gom";
			//$url.="&amp;chco=FF0000,FF8040,FFFF00,00FF00,00FFFF,0000FF,800080";
			$url.="&amp;&chd=t:".$nombre;
			$html='<img src="'.$url.'" border="0" style="float:left">';
			return $html;
	}
	
// ----------------------------------------------------------------
// Rend le html pour une ligne de tableau stat	
// ----------------------------------------------------------------
	function printLigneStat($rubrique,$nombre,$total)
	{
		global $stat;
		if(!$nombre) {$pct="0"; $sp=""; }
		else {$pct=($nombre/$total) * 100; $sp="&nbsp;";}
		$graphe=$stat->getGoogleMeter((int)$pct);
		$graphe.='<div style="height:20px;width:200px;background-color:#FFFFFF;margin-top:5px"><div class="jauge_avance" style="width:'.(int)($pct * 2).'px">'.$sp.'</div></div>';
		$pct=number_format($pct, 2, ',', ' ');
		print ('<tr><td>'.$rubrique.'</td><td align="right"><b>'.number_format($nombre,0, '', ' ').'</b></td><td align="right">'.$pct.' %</td>');
		print('<td align="center">'.$graphe.'</td>');
		print('</tr>');
		flush();
	}
}

?>