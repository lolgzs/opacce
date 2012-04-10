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
//  OPAC3: Specifique Pergame
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class Class_Systeme_PergameService
{
	private $user;

	//------------------------------------------------------------------------------------------------------
	// Constructeur
	//------------------------------------------------------------------------------------------------------
	function  __construct($user)
	{
		$this->user=$user;
	}

	//------------------------------------------------------------------------------------------------------
	// Nombre de prets en retard
	//------------------------------------------------------------------------------------------------------
	public function getNbPretsEnRetard()
	{
		$date=date("Y-m-d");
		$req="select count(*) from prets where IDABON='".$this->user->IDABON."' and ORDREABON=".$this->user->ORDREABON." and EN_COURS=1 and DATE_RETOUR < '$date'";
		$nb_retard=fetchOne($req);
		return $nb_retard;
	}


	// pour le polymorphisme avec Class_WebService_SIGB_Emprunteur
	public function getUserInformationsPopupUrl() {
		return null;
	}

	//------------------------------------------------------------------------------------------------------
	// Nombre de prets en cours
	//------------------------------------------------------------------------------------------------------
	public function getNbEmprunts()
	{
		$nb_prets=fetchOne("select count(*) from prets where IDABON='".$this->user->IDABON."' and ORDREABON=".$this->user->ORDREABON." and EN_COURS=1");
		return $nb_prets;
	}

	//------------------------------------------------------------------------------------------------------
	// Nombre de reservations
	//------------------------------------------------------------------------------------------------------
	public function getNbReservations()
	{
		$nb_resas=fetchOne("select count(*) from reservations where IDABON='".$this->user->IDABON."' and ORDREABON=".$this->user->ORDREABON);
		return $nb_resas;
	}
	
	//------------------------------------------------------------------------------------------------------
	// Liste des prets en cours
	//------------------------------------------------------------------------------------------------------
	public function getPrets()
	{
		$data=fetchAll("select * from prets where IDABON='".$this->user->IDABON."' and ORDREABON=".$this->user->ORDREABON." and EN_COURS=1");
		if(!$data) return array();
		$date_ref=date("Y-m-d");
		foreach($data as $ligne)
		{
			if($ligne["DATE_RETOUR"]<$date_ref) $pret["retard"]=true;
			$pret["date_retour"]=formatDate($ligne["DATE_RETOUR"],1);
			$notice=$this->getNoticeFromCodeBarres($ligne["ID_SITE"],$ligne["CODE_BARRES"]);
			$pret["titre"]=$notice["T"];
			$pret["auteur"]=$notice["A"];
			$pret["bibliotheque"]=fetchOne("select LIBELLE from bib_c_site where ID_SITE=".$ligne["ID_SITE"]);
			$prets[]=$pret;
		}
		return $prets;
	}


	//------------------------------------------------------------------------------------------------------
	// Disponibilite d'un exemplaire
	//------------------------------------------------------------------------------------------------------
	public function getDisponibilite($ex)
	{
		// terme en pret
		$tmp=Class_Profil::getCurrentProfil()->getCfgNoticeAsArray();
		$libelle_en_pret="emprunté";
		if (array_isset("en_pret", $tmp["exemplaires"]))
			$libelle_en_pret=$tmp["exemplaires"]["en_pret"];
		
		// verif pret
		$code_barres=$ex["code_barres"];
		$prets = fetchAll("select * from prets where code_barres='$code_barres' and EN_COURS=1");

		// Activité
		$ex["dispo"]=$ex["activite"];
		$ex["reservable"]=true;
		if ($prets)
		{
			$ex["dispo"]=$libelle_en_pret;
			$ex["date_retour"] = strftime('%d/%m/%y' ,strtotime($prets[0]['DATE_RETOUR']));
		}	
		else
		{
			$regles=$this->getReglesReservation($ex["id_bib"]);
			if($regles["Autoriser_docs_disponibles"]==1) $ex["reservable"]=true;
			else $ex["reservable"]=false;
		}
		
		// nombre de réservations
		$nb_resas=fetchOne("select count(*) from reservations where ID_NOTICE_ORIGINE=".intval($ex["id_origine"]));
		if($nb_resas)
		{
			if($nb_resas>1) $pluriel="s";
			if($nb_resas) $ex["dispo"]=$nb_resas." réservation".$pluriel." en cours";
		}

		return $ex;
	}

	//------------------------------------------------------------------------------------------------------
	// Liste des réservations en cours pour 1 abonné
	//------------------------------------------------------------------------------------------------------
	public function getReservations()
	{
		$data=fetchAll("select * from reservations where IDABON='".$this->user->IDABON."' and ORDREABON=".$this->user->ORDREABON);
		if(!$data) return array();
		foreach($data as $enreg)
		{
			$notice=$this->getNoticeFromTransaction($enreg["SUPPORT"],$enreg["ID_NOTICE_ORIGINE"]);
			$resa["titre"]=$notice["T"];
			if($notice["A"]) $resa["titre"].=" / ".$notice["A"];

			// rang
			$rang=fetchOne("select count(*) from reservations where ID_NOTICE_ORIGINE=".$enreg["ID_NOTICE_ORIGINE"]." and DATE_RESA<'".$enreg["DATE_RESA"]."'");
			$resa["rang"]=$rang+1;
			$resa["id_suppr"]=$enreg["ID_RESA"];

			// Controle si en pret
			$en_pret=fetchOne("select count(*) from prets where ID_NOTICE_ORIGINE=".$enreg["ID_NOTICE_ORIGINE"]." and EN_COURS=1");
			if($en_pret>0) $resa["etat"]="en prêt";
			elseif($resa["rang"]==1) $resa["etat"]="disponible";
			else $resa["etat"]="réservé";

			// empiler
			$reservations[]=$resa;
		}
		return $reservations;
	}

	//------------------------------------------------------------------------------------------------------
	// Rend une notice pour un code_barres
	//------------------------------------------------------------------------------------------------------
	private function getNoticeFromCodeBarres($id_bib,$code_barres)
	{
		$cls_notice=new Class_Notice();
		$id_notice=fetchOne("select id_notice from exemplaires where code_barres='$code_barres' and id_bib=$id_bib");
		if(!$id_notice)
		{
			$notice["T"]="Anomalie de lecture du titre";
			return $notice;
		}
		$notice=$cls_notice->getNotice($id_notice,"TA");
		return $notice;
	}

	//------------------------------------------------------------------------------------------------------
	// Rend une notice pour un id_notice_pergame et un support
	//------------------------------------------------------------------------------------------------------
	private function getNoticeFromTransaction($support,$id_notice_pergame)
	{
		$cls_notice=new Class_Notice();
		$ids=fetchAll("select id_notice from exemplaires where id_origine=$id_notice_pergame");
		if(!$ids)
		{
			$notice["T"]="Anomalie de lecture du titre";
			return $notice;
		}
		foreach($ids as $id)
		{
			if($inSql > '') $inSql.=",";
			$inSql.=$id["id_notice"];
		}
		$id_notice=fetchOne("select id_notice from notices where id_notice in($inSql) and type_doc=$support");
		$notice=$cls_notice->getNotice($id_notice,"TA");
		return $notice;
	}

	//------------------------------------------------------------------------------------------------------
	// Ecrire une réservation
	//------------------------------------------------------------------------------------------------------
	public function ReserverExemplaire($id_bib,$id_origine, $code_annexe)
	{
		// Controle connexion
		if(!$this->user->ID_USER) $ret["erreur"]="Vous devez être connecté pour réserver un document";
		else if(!$this->user->IDABON) $ret["erreur"]="Vous devez être connecté en tant qu'abonné pour réserver un document";
		if($ret["erreur"]) return $ret;

		// Lire exemplaire et notice
		$ex=fetchEnreg("select * from exemplaires where id_origine='$id_origine' and id_bib=$id_bib");
		if(!$ex) return array("erreur"=>"Une erreur s'est produite lors de la lecture de la notice.");
		$id_notice=$ex["id_notice"];
		$support=fetchOne("select type_doc from notices where id_notice=$id_notice");

		// Controle si deja réservé par l'abonné
		$id_abon=$this->user->IDABON;
		$ordre_abon=$this->user->ORDREABON;
		$resa=fetchEnreg("select * from reservations where ID_NOTICE_ORIGINE='$id_origine' and ID_SITE=$id_bib and IDABON='$id_abon' and ORDREABON=$ordre_abon");
		if($resa) $ret["erreur"]="Vous avez déjà réservé ce document le ".formatDate($resa["DATE_RESA"], 1);
		if($ret["erreur"]) return $ret;

		// lecture des regles de reservations
		$regles=$this->getReglesReservation($ex["id_bib"]);

		// controle quota par carte
		$nb=fetchOne("select count(*) from reservations where IDABON=$id_abon");
		if($nb>=$regles["Max_par_carte"])
		{
			$ret["popup"]="La réservation est impossible car vous avez atteint le nombre maximum de réservations sur votre carte.";
			return $ret;
		}

		// controle quota par document
		$nb=fetchOne("select count(*) from reservations where ID_NOTICE_ORIGINE=$id_origine");
		if($nb>=$regles["Max_par_document"])
		{
			$ret["popup"]="La réservation est impossible car le nombre maximum de réservations pour ce document a été atteint (".$regles["Max_par_document"].").";
			return $ret;
		}
		
		// Ecrire enreg reservation
		$req="insert into reservations(ID_SITE,ID_PERGAME,IDABON,ORDREABON,DATE_RESA,SUPPORT,ID_NOTICE_ORIGINE)";
		$req.="Values($id_bib,0,$id_abon,$ordre_abon,'".date("Y-m-d")."',$support,'$id_origine')";
		sqlExecute($req);

		// Ecrire enreg transaction
		$date=date("Y-m-d H:i:s");
		$heure=date("H");
		$this->ecrireTransaction(6,array($id_abon,$ordre_abon,$support,$id_origine,$date,$code_annexe,$heure));
	}

	//------------------------------------------------------------------------------------------------------
	// Supprimer une réservation
	//------------------------------------------------------------------------------------------------------
	public function supprimerReservation($id_reservation)
	{
		// lire et detruire la réservation
		$resa=fetchEnreg("select * from reservations where ID_RESA=$id_reservation");
		if(!$resa) return false;
		sqlExecute("delete from reservations where ID_RESA=$id_reservation");

		// On cree la transaction
		$id_abon=$this->user->IDABON;
		$ordre_abon=$this->user->ORDREABON;
		$id_origine=$resa["ID_NOTICE_ORIGINE"];
		$date=$resa["DATE_RESA"];
		$id_site=$resa["ID_SITE"];
		$this->ecrireTransaction(7,array($id_abon,$ordre_abon,$date,$id_origine,$id_site));
	}

	//------------------------------------------------------------------------------------------------------
	// Ecrire une transaction
	//------------------------------------------------------------------------------------------------------
	private function ecrireTransaction($type_mvt,$enreg)
	{
		// Compacter les données
		foreach($enreg as $item) $data.=$item."|";

		// Ecrire
		$req="insert into transactions(TYPE_MVT,DATA) Values($type_mvt,'$data')";
		sqlExecute($req);
	}

	//------------------------------------------------------------------------------------------------------
	// Lire les regles de réservation
	//------------------------------------------------------------------------------------------------------
	private function getReglesReservation($id_bib)
	{
		if(!$id_bib) return false;
		$data=fetchOne("select comm_params from int_bib where id_bib=$id_bib");
		$data=unserialize($data);
		if(!isset($data["Max_par_carte"])) $data["Max_par_carte"]=3;
		if(!isset($data["Max_par_document"])) $data["Max_par_document"]=3;
		return $data;
	}
}

?>
