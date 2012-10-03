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

class Class_Systeme_Sql {
	private $hpdo;															// Handle de connexion
	private $ignore_erreurs=false;							// Pour l'intégration les erreurs sont ignorées
	private $log;																// Instance classe de log
	private $filtre_write;											// Filtre pour les ecritures

// ---------------------------------------------------
// Constructeur : connexion à la base
// ---------------------------------------------------	
	function __construct($server,$user,$pwd,$base)
	{
		$dns="mysql:dbname=".$base.";host=".$server;
		try
		{
			$this->hpdo=new PDO($dns,$user,$pwd);
		}
		catch (PDOException $e)
		{
			print('<font color="red"><b>Impossible de se connecter au moteur Mysql</b><br>'.$e->getMessage().'</font>');
			exit;
		}
		$this->hpdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		$this->hpdo->query('set names "UTF8"');
		$this->filtre_write=new ZendAfi_Filters_WriteSql();
	}


// ---------------------------------------------------
// Resultat pour 1 seule colonne et 1 seul enreg
// ---------------------------------------------------	
	public function fetchOne($req)	{
		$result = $this->hpdo->prepare($req);
		$result->execute();
		$data = $result->fetch(PDO::FETCH_NUM);
		return(stripslashes($data[0]));
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
			if ($req=="select id_notice from notices where MATCH(titres,auteurs,editeur,collection,matieres,dewey) AGAINST(' (IRM IRMS )') and id_notice !=197143 Limit 0,10")
				var_dump(!$data);
			if (!$data)
			{
				return false;
			}
			array_walk_recursive($data, array('ZendAfi_Filters_ReadSql', 'filtre'));
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
	public function fetchAll($req,$num=false)	{
		$result = $this->hpdo->prepare($req);
		try	{
			$result->execute();
		}	catch(PDOException $e) {
			$this->traiteErreur($req, $e);
			return array();
		}

		if($num == true ) 
			$data = $result->fetchAll(PDO::FETCH_NUM);
		else 
			$data = $result->fetchAll(PDO::FETCH_ASSOC);
		if (!$data)
				return array();

		array_walk_recursive($data, array('ZendAfi_Filters_ReadSql', 'filtre'));
		return($data);
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


	public function query($req)	{
	  return $this->execute($req);
	}


	public function quote($str){
		return $this->hpdo->quote($str);
	}



// ---------------------------------------------------
// Requete update
// ---------------------------------------------------
	public function update($req,$data,$force_quote=false)
	{
		$set = '';
		foreach($data as $col => $valeur)	{
			if ($set) $set.=",";
			$set.= $col . "=".$this->filtre_write->filter($valeur,$force_quote);
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
	public function insert($table,$data,$force_quote=false)
	{
		foreach($data as $col => $valeur)
		{
			if($cols)
			{
				$cols.=",";
				$values .=",";
			}
			$cols.= $col;
			$values .= $this->filtre_write->filter($valeur,$force_quote);
		}

		$req="Insert into " .$table. "(" . $cols . ") Values(" . $values . ")";
		try
		{
			$nombre=$this->hpdo->exec($req);
			return $this->hpdo->lastInsertId();
		}
		catch(PDOException $e)
		{
			$this->traiteErreur($req,$e);
			return false;
		}
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
		print('<h3 style="color:red;margin-bottom:5px">Erreur SQL</h3>');
		// Texte erreur
		$msg='<div style="margin-left:10px;border:1px solid #E0E0E0;background-color:#CC99FF;padding:5px;margin-bottom:5px">';
		$msg.="<b>Code : </b>".$erreur[1].BR;
		$msg.='<b>Requete : </b>'.$requete.BR;
		$msg.='<b>Erreur : </b>'.$erreur[2].BR.BR;
		$stack=debug_backtrace();
		for($i=count($stack)-1; $i>0; $i--)
		{
			$lig=$stack[$i];
			if (array_key_exists('file', $lig))
				$msg.="<b>Script : </b>". $lig["file"];

			if (array_key_exists('line', $lig))
				$msg.=" - <b>Ligne : </b>". $lig["line"];

			$msg.=" - <b>Fonction : </b>". $lig["function"].BR;
		}
		$msg.='</div>';
		print('</center>'.$msg);

		// On log l'erreur
//		if(!$this->log) $this->log=new Class_log("sql",false);
//		$this->log->open(true);
//		$this->log->ecrire($msg);
//		$this->log->close();

		// On arrete tout
		exit;
	}
}

?>