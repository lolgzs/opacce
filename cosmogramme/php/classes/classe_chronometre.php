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
/////////////////////////////////////////////////////////////////////
// Classe Chronometre
////////////////////////////////////////////////////////////////////
class chronometre
{
	public $timeStart;							// Temps de début
	private $heure_limite;					// Timestamp

// ----------------------------------------------------------------
// Mémo temps début
// ----------------------------------------------------------------
	function start($timeStart=0)
	{
		if($timeStart>0) $this->timeStart=$timeStart;
		else $this->timeStart = time();
	}

// ----------------------------------------------------------------
// Rend le temps écoulé en secondes depuis Start
// ----------------------------------------------------------------
	function tempsPasse($start=false)
	{
		if(!$start) $start=$this->timeStart;
		$t = time() - $start;
		return $t;
	}

// ----------------------------------------------------------------
// Memorise une heure limite de traitement
// ----------------------------------------------------------------
	function setHeureLimite($heure)
	{
		if($heure > "")
		{
			$h=substr($heure,0,2);
			$m=substr($heure,3,2);
			$heure=mktime($h, $m, 0, date("m"), date("d"), date("Y"));
			if($heure < time()) $heure  = mktime($h, $m, 0, date("m",$heure)  , date("d",$heure)+1, date("Y",$heure));
			$this->heure_limite=$heure;
			return date("H:i",$heure);
		}
		$this->heure_limite=0;
		return "aucune";
	}

// ----------------------------------------------------------------
// Teste l'heure limite false=ok true=dépassée
// ----------------------------------------------------------------
	function testHeureLimite()
	{
		if( $heure_limite > 0 And time() > $heure_limite ) return true;
		else return false;
	}

// ----------------------------------------------------------------
// retourne le temps d'éxécution
// ----------------------------------------------------------------
	function end()
	{
		$temps = "";
		$t = time();
		$t=$t - $this->timeStart;
		$secondes = $t % 60;
		$minutes = (int)($t/60);
		$heures = (int)($minutes/60);
		$minutes = $minutes % 60;
		if( $heures > 0 )$temps = $heures . " h ";
		if( $minutes > 0 ) $temps .= $minutes . " min. ";
		$temps .= $secondes . " sec.";
		return $temps;
	}

// ----------------------------------------------------------------
// Rend une moyenne par minute
// ----------------------------------------------------------------
	function moyenne($nombre, $libelle)
	{
		$temps=time()-$this->timeStart;
		if($temps == 0)$temps=1;
		$moyenne=$nombre / ($temps / 60);
		$txt=number_format($moyenne,0,",",".");
		$txt .= " " . $libelle . " par minute";
		return $txt;
	}
}

?>