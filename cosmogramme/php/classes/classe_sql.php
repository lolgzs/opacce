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
/////////////////////////////////////////////////////////////////////////////////////////////////
//   SQL qui s'appuie sur PDO
/////////////////////////////////////////////////////////////////////////////////////////////////

require_once("classe_log.php");

class sql
{
	private $hpdo;															// Handle de connexion
	private $ignore_erreurs=false;							// Pour l'intégration les erreurs sont ignorées
	private $log;																// Instance classe de log
	private $statements = [];                   // liste des prepare statements pour les insertions

// ---------------------------------------------------
// Constructeur : connexion à la base
// ---------------------------------------------------	
	function __construct($server,$user,$pwd,$base)
	{
		$dns="mysql:dbname=".$base.";host=".$server;
		try {	$this->hpdo=new PDO($dns,$user,$pwd); }
		catch (PDOException $e) { afficherErreur('<b><font color="red">Impossible de se connecter au moteur Mysql</font></b><br>'.$e->getMessage());}
		$this->hpdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}
// ---------------------------------------------------
// Resultat pour 1 seule colonne et 1 seul enreg
// ---------------------------------------------------	
	public function fetchOne($req)
	{
		try
		{
			$result = $this->hpdo->prepare($req);
			$result->execute();
			$data = $result->fetch(PDO::FETCH_NUM);
			return(stripslashes($data[0]));
		}
		catch(PDOException $e)
		{
			$this->traiteErreur($req,$e);
			return false;
		}
	}
// ---------------------------------------------------
// Resultat pour 1 enreg (plusieurs colonnes)
// ---------------------------------------------------
	public function fetchEnreg($req,$num=false)
	{
		try
		{
			$result = $this->hpdo->prepare($req);
			$result->execute();
			if($num == true ) $data = $result->fetch(PDO::FETCH_NUM);
			else $data = $result->fetch(PDO::FETCH_ASSOC);
			return($data);
		}
		catch(PDOException $e)
		{
			$this->traiteErreur($req,$e);
			return false;
		}
	}
// ---------------------------------------------------
// Resultat liste
// ---------------------------------------------------
	public function fetchAll($req,$num=false)
	{
		try
		{
			$result = $this->hpdo->prepare($req);
			$result->execute();
			if($num == true ) $data = $result->fetchAll(PDO::FETCH_NUM);
			else $data = $result->fetchAll(PDO::FETCH_ASSOC);
			return($data);
		}
		catch(PDOException $e)
		{
			$this->traiteErreur($req,$e);
			return false;
		}
	}
// ---------------------------------------------------
// Requete delete,update ou insert sans addslashes
// ---------------------------------------------------
	public function execute($req)
	{
		try 
		{ 
			$nombre=$this->hpdo->exec($req);
			return $nombre;
		}
		catch(PDOException $e)
		{
			$this->traiteErreur($req,$e);
			return false;
		}
	}
// ---------------------------------------------------
// Requete update (avec addslashes)
// ---------------------------------------------------
	public function update($req,$data)
	{
		foreach($data as $col => $valeur)
		{
			if($set) $set.=",";
			$set.= $col . "='".addslashes($valeur)."'";
		}
		$req=str_replace("@SET@",$set,$req);
		try
		{
			$nombre=$this->hpdo->exec($req);
			return $nombre;
		}
		catch(PDOException $e)
		{
			$this->traiteErreur($req,$e);
			return false;
		}
	}
// ---------------------------------------------------
// Requete insert (avec addslashes)
// ---------------------------------------------------
	public function insert($table,$data)	{
		$cols = implode(',', array_keys($data));
		$statement = isset($this->statements[$table][$cols])
			? $this->statements[$table][$cols] 
			: $this->statements[$table][$cols] = $this->createInsertPrepareStatement($table, $data);
		
		foreach($data as $col => $valeur)
			$statement->bindParam(":$col", trim($valeur));

		try	{
			$result = $statement->execute();
			return $this->hpdo->lastInsertId();
		}
		catch(PDOException $e)
		{
			$this->traiteErreur($req,$e);
			return false;
		}
	}


	private function createInsertPrepareStatement($table, &$data) {
		$cols = array_keys($data);
		$placeholders = array();
		foreach($cols as $col)
			$placeholders []= ':'.$col;
		return $this->hpdo->prepare('insert into '.$table.' ('.implode(',', $cols).') VALUES ('.implode(',', $placeholders).')'); 
	}

// ---------------------------------------------------
// Lance une requete pour parser avec des fetchNext
// ---------------------------------------------------
	public function prepareListe($req)
	{
		try
		{
			$result = $this->hpdo->prepare($req);
			$result->execute();
			return $result;
		}
		catch(PDOException $e)
		{
			$this->traiteErreur($req,$e);
			return false;
		}
	}
// ---------------------------------------------------
//	FetchNext d'une requete preparee avec pepareListe
// ---------------------------------------------------
	public function fetchNext(&$result,$onlyOne=0)
	{
		try
		{
			if($onlyOne==1) $data = $result->fetch(PDO::FETCH_NUM);
			else$data = $result->fetch(PDO::FETCH_ASSOC);
			if($onlyOne==1) $data=$data[0];
			return $data;
		}
		catch(PDOException $e)
		{
			$this->traiteErreur($req,$e);
			return false;
		}
	}
// ---------------------------------------------------
// Positionne le flag pour ignorer les erreurs
// ---------------------------------------------------
	public function ignoreErreurs($mode)
	{
		$this->ignore_erreurs=$mode; 
	}
// ---------------------------------------------------
// Traitement des erreurs
// ---------------------------------------------------
	private function traiteErreur($requete,$e)
	{
		$erreur=$e->errorInfo;
		// En mode exception on leve une exception
		if($this->ignore_erreurs == true)
		{
			throw new Exception($erreur[2], $erreur[1]);
		}

		// Sinon on affiche l'erreur
		print('<h3 class="erreur">Erreur SQL</h3>');
		$msg='<div class="erreur_sql">';
		$msg.="<b>Code : </b>".$erreur[1].BR;
		$msg.='<b>Requete : </b>'.$requete.BR;
		$msg.='<b>Erreur : </b>'.$erreur[2].BR.BR;
		$stack=debug_backtrace();
		for($i=count($stack)-1; $i>0; $i--)
		{
			$lig=$stack[$i];
			$msg.="<b>Script : </b>". $lig["file"];
			$msg.=" - <b>Ligne : </b>". $lig["line"];
			$msg.=" - <b>Fonction : </b>". $lig["function"].BR;
		}
		$msg.='</div>';
		
		// Afficher l'erreur
		if( getVariable("sql_debug") == 1) print('</center>'.$msg);
		
		// On log l'erreur
		if(!$this->log) $this->log=new Class_log("sql",false);
		$this->log->open(true);
		$this->log->ecrire($msg);
		$this->log->close();
		//exit;
	}
}

?>