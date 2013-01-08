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
// COMMUNICATION POUR LES TRANSACTIONS FAITES SUR L'OPAC
//
// Types de transactions :
//		- RESA_INSERT=6
//		- RESA_DELETE=7
///////////////////////////////////////////////////////////////////////////////////////

class transaction_opac
{
	private $sep="|";								// Separateur de données

// ----------------------------------------------------------------
// Envoi des transactions en attente
// ----------------------------------------------------------------
	public function sendTransactions()
	{
		$sep=$this->sep;

		global $sql;
		$transacs=$sql->fetchAll("select * from transactions");
		foreach($transacs as $ligne)
		{
			$bloc=$ligne["TYPE_MVT"].$sep;
			$bloc.=$ligne["ID_MVT"].$sep;
			$bloc.=$ligne["DATA"];
			$data[]=$bloc;
		}
		$this->envoiReponse("ok",$data);
	}

// ----------------------------------------------------------------
// Suppression d'une transaction intégrée par pergame
// ----------------------------------------------------------------
	public function transactionDone($id_transaction)
	{
		// verif parametre
		$id_transaction=(int)$id_transaction;
		if(!$id_transaction) $this->erreur("Identifiant de la transaction absent ou incorrect");

		// Suppression de la transaction
		global $sql;
		$sql->execute("delete from transactions where ID_MVT=$id_transaction");
		$this->envoiReponse("ok","");
	}

// ----------------------------------------------------------------
// Erreur
// ----------------------------------------------------------------
	public function erreur($erreur)
	{
		echo "statut=erreur".chr(9);
		echo "erreur=".$erreur.chr(9);
		exit;
	}

// ----------------------------------------------------------------
// Envoi de la reponse
// ----------------------------------------------------------------
	public function envoiReponse($statut,$data)
	{
		if(!is_array($data)) $data=array($data);
		if(!$data[0]) $nb_lignes=0; else $nb_lignes=count($data);
		echo "statut=".$statut.chr(9);
		echo "nb_lignes=".$nb_lignes.chr(9).CRLF;
		foreach($data as $ligne) echo $ligne.CRLF;
		exit;
	}

}

?>