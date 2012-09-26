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

class Class_WebService_SIGB_Opsys_Service extends Class_WebService_SIGB_AbstractService {
	/** @var Class_WebService_MappedSoapClient */
	protected $search_client;

	/** @var Class_WebService_MappedSoapClient */
	protected $catalog_client;

	protected $guid;


	/**
	 * @param Class_WebService_MappedSoapClient $search_client
	 * @param Class_WebService_MappedSoapClient $catalog_client
	 */
	public function __construct($search_client, $catalog_client = null){
		$this->search_client = $search_client;
		$this->catalog_client = $catalog_client;
		$this->connect();
	}


	public function __destruct(){
		$this->disconnect();
	}


	public function getCatalogClient() {
		return $this->catalog_client;
	}


	public function isConnected(){
		return (isset($this->guid) && ($this->guid !== ""));
	}


	public function connect(){
		try {
			$osr = $this->search_client->OuvrirSession(new OuvrirSession());
			$this->guid = $osr->getGUID();
		} catch (SoapFault $e) {
			return false;
		}
		return ($this->isConnected());
	}


		/** @codeCoverageIgnore */
	protected function _dumpSoapTrace() {
		var_dump($this->search_client->__getLastRequestHeaders());
		var_dump($this->search_client->__getLastRequest());
		var_dump($this->search_client->__getLastResponseHeaders());
		var_dump($this->search_client->__getLastResponse());
	}



	public function disconnect(){
		$fs=new FermerSession($this->guid);
		try {
			$fsr = $this->search_client->FermerSession($fs);
		} catch (Exception $e) {
				//Aloes V190 plante parfois sur les FermerSession
		}
	}


	public function getEmpruntsOf($emprunteur) {
		// prets pas en retard
		$liste_prets_result = $this->search_client->EmprListerEntite(
																					EmprListerEntite::prets($this->guid));
		$prets = $liste_prets_result->getEntites('Class_WebService_SIGB_Emprunt');


		// prets en retard
		$liste_prets_retard = $this->search_client->EmprListerEntite(
																	EmprListerEntite::prets_en_retard($this->guid));
		$prets_retard = $liste_prets_retard->getEntites('Class_WebService_SIGB_Emprunt');
		foreach($prets_retard as $retard)
			$retard->setEnRetard(true);

		return array_merge($prets, $prets_retard);
	}


	public function getReservationsOf($emprunteur) {
		$reserv_result = $this->search_client->EmprListerEntite(
																	EmprListerEntite::reservations($this->guid));
		return $reserv_result->getEntites('Class_WebService_SIGB_Reservation');
	}


	/**
	 * @param Class_Users $user
	 * @return type
	 */
	public function getEmprunteur($user) {
		return $this->authentifierEmprunteur($user);
	}


	/**
	 * @param Class_Users $user
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function authentifierEmprunteur($user) {
		$auth = new EmprAuthentifier($this->guid, $user->getLogin(), $user->getPassword());
		$auth_result = $this->search_client->EmprAuthentifier($auth);
		return $auth_result
			->createEmprunteur()
			->setService($this);
	}


	/**
	 * @param Class_Users $user
	 * @param int $exemplaire_id
	 * @param string $code_annexe
	 * @return array
	 */
	public function reserverExemplaire($user, $exemplaire, $code_annexe){
		$emprunteur = $this->authentifierEmprunteur($user);
		$reserv_result = $this->search_client->EmprReserver(
																								 new EmprReserver($this->guid,
																																	$exemplaire->getIdOrigine(),
																																	$code_annexe));
		return $reserv_result->getReussite();
	}


	/**
	 * @param Class_Users $user
	 * @param int $reservation_id
	 * @return array
	 */
	public function supprimerReservation($user, $reservation_id){
		$emprunteur = $this->authentifierEmprunteur($user);
		$supp_result = $this->search_client->EmprSupprResa(
																								new EmprSupprResa($this->guid,
																																	$reservation_id));
		return $supp_result->getReussite();
	}


	/**
	 * @param Class_Users $user
	 * @param int $pret_id
	 * @return array
	 */
	public function prolongerPret($user, $pret_id){
		$emprunteur = $this->authentifierEmprunteur($user);
		$prolong_result = $this->search_client->EmprProlong(
																								 new EmprProlong($this->guid,
																																 $pret_id));

		return $prolong_result->getReussite();
	}


	public function getNotice($id){
		try {
			$notice_result = $this->search_client->RecupererNotice(
				new RecupererNotice($this->guid, $id));
			return $notice_result->createNotice();
		} catch (Exception $e) {
			$this->_dumpSoapTrace();
		}
	}


	public function getGUID(){
		return $this->guid;
	}


	public function _setGUID($guid){
		$this->guid=$guid;
	}


	public function saveEmprunteur($emprunteur) {
		$ecrire_notice = new EcrireNotice();
		$ecrire_notice
			->setGUID($this->guid)
			->setGrille('AFI')
			->setCatalogue('E')
			->readEmprunteur($emprunteur);

		$result = $this->catalog_client->EcrireNotice($ecrire_notice);

		if (!isset($result->EcrireNoticeResult->ErreurService))
			return $this;

		$code = $result->EcrireNoticeResult->ErreurService->CodeErreur;
		$libelle = $result->EcrireNoticeResult->ErreurService->LibelleErreur;
		throw new Exception("($code) $libelle");
	}
}


/*
 * Classes pour le mapping WSDL
 */
class ListerServeurs {
	public $Param; // Entree

	function __construct(){
		$this->Param = new Entree();
	}
}

class Entree {
	public $Catalogue; // string
	public $GUIDSession; // string

	function __construct(){
		$this->Catalogue='';
		$this->GUIDSession='';
	}
}

class ListerServeursResponse {
	public $ListerServeursResult; // RspListeServeurs

	public function getServeurSessions(){
		return $this->ListerServeursResult->ServeursSession->ServeurSession;
	}

	public function firstNomServeur(){
		$serveurSessions = ($this->getServeurSessions());
		$firstServeurSession = $serveurSessions[0];
		return $firstServeurSession->NomServeur;
	}
}

class RspListeServeurs {
	public $GUIDSession; // string
	public $ErreurService; // WebSrvErreur
	public $ServeursSession; // ArrayOfServeurSession
}

class WebSrvErreur {
	public $CodeErreur; // string
	public $LibelleErreur; // string
	public $DetailErreur; // string
}

class ServeurSession {
	public $BasesServeurs; // ArrayOfBaseSession
	public $NomServeur; // string

	function __construct($NomServeur){
		$this->NomServeur=$NomServeur;
		$this->BasesServeurs=array();
	}
}

class BaseSession {
	public $NomBase; // string
}

class RecupererParametres {
	public $Catalogue; // string
}

class RecupererParametresResponse {
	public $RecupererParametresResult; // RspRecupererParam
}

class RspRecupererParam {
	public $GUIDSession; // string
	public $ErreurService; // WebSrvErreur
	public $ParamListe; // ArrayOfParamExport
	public $ParamNotice; // ArrayOfParamExport
	public $ParamMotCles; // ArrayOfParamEntite
	public $ParamComsurPlace; // ArrayOfParamComsurPlace
}

class ParamExport {
	public $TypeParam; // string
	public $ExportPret; // string
	public $Texte; // ArrayOfParamEntite
	public $Publipostage; // ArrayOfParamEntite
	public $ISO2709; // ArrayOfParamEntite
}

class ParamEntite {
	public $Code; // string
	public $Libelle; // string
	public $Prefix; // string
}

class ParamComsurPlace {
	public $Poste; // string
	public $Description; // string
	public $Masque; // string
}

class RecupererParamTable {
	public $NomTable; // string
	public $Catalogue; // string
}

class RecupererParamTableResponse {
	public $RecupererParamTableResult; // RspRecupererParamTable
}

class RspRecupererParamTable {
	public $GUIDSession; // string
	public $ErreurService; // WebSrvErreur
	public $Elements; // ArrayOfParamEntite
}

class OuvrirSession {
	public $Param; // EntOuvrirSession

	function __construct($NomServeur='INTERNET'){
		$this->Param = new EntOuvrirSession($NomServeur);
	}

}

class EntOuvrirSession extends Entree {
	public $NomMachine = 'INTERNET'; // string
	public $ListeServeurs; // ArrayOfServeurSession

	function __construct($NomServeur){
		$this->ListeServeurs=array();
		$this->ListeServeurs[]=new ServeurSession($NomServeur);
	}
}

class OuvrirSessionResponse {
	public $OuvrirSessionResult; // RspOuvrirSession

	function getGUID(){
		return $this->OuvrirSessionResult->GUIDSession;
	}
}

class RspOuvrirSession {
	public $GUIDSession; // string
	public $ErreurService; // WebSrvErreur
	public $OuvrirSessions; // ArrayOfEtatSession
}


class EtatSession {
	public $NomServeur; // string
	public $ErreurServeur; // WebSrvErreur
	public $EtatConnexion; // string
	public $CodeEtat; // string
}

class FermerSession {
	public $Param; // EntOuvrirSession

	function __construct($guid){
		$this->Param = new EntOuvrirSession('');
		$this->Param->GUIDSession=$guid;
	}
}

class FermerSessionResponse {
	public $FermerSessionResult; // RspOuvrirSession
}

class Rechercher {
	public $Param; // EntRechercher
}

class EntRechercher {
	public $Phrase; // string
	public $Descente; // boolean
	public $IDEtape; // string
	public $FondEnPret; // boolean
	public $SupprMotsInconnus; // boolean
	public $ListeFondsRecherche; // string
	public $Triordre; // Triordre
}

class Triordre {
	public $Nom; // string
	public $Criteres; // ArrayOfTriordreCritere
}

class TriordreCritere {
	public $Codes; // ArrayOfTriordreCode
	public $Libelle; // string
}

class TriordreCode {
	public $_; // string
	public $Libelle; // string
}

class RechercherResponse {
	public $RechercherResult; // RspRechercher
}

class RspRechercher {
	public $GUIDSession; // string
	public $ErreurService; // WebSrvErreur
	public $EquationRetenue; // string
	public $IDEtape; // string
	public $RechercheNormale; // RechercheNormale
	public $MotsInconnus; // ArrayOfMotInconnu
	public $ResultParServeur; // ArrayOfRechercheServeur
}

class RechercheNormale {
	public $NombreNotice; // int
	public $TypeNotice; // string
}

class MotInconnu {
	public $CodeIndex; // string
	public $NomMot; // string
	public $PositionMot; // int
}

class RechercheServeur {
	public $NomServeur; // string
	public $ErreurServeur; // WebSrvErreur
	public $NbNotices; // int
}

class DocRattaches {
	public $Param; // EntDocRattaches
}

class EntDocRattaches {
	public $Notices; // ArrayOfString
	public $IDEtape; // string
	public $Statuts; // string
	public $Restricteurs; // string
}

class DocRattachesResponse {
	public $DocRattachesResult; // RspRechercher
}

class TrierListe {
	public $Param; // EntTrierListe
}

class EntTrierListe {
	public $IDEtape; // string
	public $ModeTri; // string
	public $TriAscendant; // boolean
}

class TrierListeResponse {
	public $TrierListeResult; // RspTrierListe
}

class RspTrierListe {
	public $GUIDSession; // string
	public $ErreurService; // WebSrvErreur
	public $IDEtapeTriee; // string
}

class RecupererListe {
	public $Param; // EntRecupererListe
}

class EntRecupererListe {
	public $RangDepart; // int
	public $NombreNotices; // int
	public $IDEtape; // string
	public $Affichage; // AffichageNotice
}

class AffichageNotice {
	public $CodeAffichage; // string
	public $NiveauAffichage; // string
	public $LibelleAffichage; // string

	public function __construct(){
		$this->NiveauAffichage=0;
	}
}

class RecupererListeResponse {
	public $RecupererListeResult; // RspRecupererListe
}

class RspRecupererListe {
	public $GUIDSession; // string
	public $ErreurService; // WebSrvErreur
	public $Notices; // ArrayOfNoticeListe
	public $NbNotices; // int
	public $RangPrecedent; // int
	public $RangSuivant; // int
	public $IDEtape; // string
	public $IDEtapeTriee; // string
	public $ErreurParServeur; // ArrayOfReponseServeur
}

class NoticeListe {
	public $ServeursSource; // ArrayOfServeurSession
	public $ServeurSource; // string
	public $BaseSource; // string
	public $NumNotice; // string
	public $ISBN; // string
	public $SupportNotice; // string
	public $RegleNotice; // string
	public $Contenu; // string
	public $NbNoticesRattaches; // int
	public $ISBD; // ArrayOfParagrapheISBD
	public $ISBDComplet; // ArrayOfParagrapheISBD
	public $RangNotice; // int
	public $TypeNotice; // string
}

class ParagrapheISBD {
	public $Textes; // ArrayOfTexteISBD
}

class TexteISBD {
	public $TypeNotice; // string
	public $NumNotice; // string
	public $Contenu; // string
}

class ReponseServeur {
	public $NomServeur; // string
	public $ErreurServeur; // WebSrvErreur
}

class RecupererIndex {
	public $Param; // EntRecupererIndex
}

class EntRecupererIndex {
	public $CodeIndex; // string
	public $MotIndex; // string
	public $NombreMots; // int
	public $PositionMot; // EUIndexPositionMot
}

class EUIndexPositionMot {
	const Debut = 'Debut';
	const Millieu = 'Millieu';
	const Fin = 'Fin';
}

class RecupererIndexResponse {
	public $RecupererIndexResult; // RspRecupererIndex
}

class RspRecupererIndex {
	public $GUIDSession; // string
	public $ErreurService; // WebSrvErreur
	public $MotPageDeb; // string
	public $PosPagedeb; // EUIndexPositionMot
	public $MotPagePrec; // string
	public $PosPagePrec; // EUIndexPositionMot
	public $MotPageSuiv; // string
	public $PosPageSuiv; // EUIndexPositionMot
	public $MotPagefin; // string
	public $PosPagefin; // EUIndexPositionMot
	public $Navigation; // boolean
	public $ListeMots; // ArrayOfMotIndex
}

class MotIndex {
	public $NomMot; // string
	public $RefMot; // string
	public $NbNoticesLiees; // int
}

class RecupererNotice {
	public $Param; // EntRecupererNotice

	public function __construct($guid, $numNotice){
		$this->Param=new EntRecupererNotice();
		$this->Param->GUIDSession=$guid;
		$this->Param->NumNotice=$numNotice;
	}
}

class EntRecupererNotice extends Entree{
	public $RangNotice; // int
	public $NumNotice; // string
	public $IDEtape; // string
	public $Affichage; // AffichageNotice
	public $Restricteurs; // ArrayOfString
	public $FondsEnPret; // boolean;

	function __construct(){
		$this->RangNotice="1";
		$this->Affichage=new AffichageNotice();
		$this->FondsEnPret = true;
	}
}

class RecupererNoticeResponse {
	public $RecupererNoticeResult; // RspRecupererNotice

	public function createNotice(){
		$rsp_notice = $this->RecupererNoticeResult->Notice;
		$notice = new Class_WebService_SIGB_Notice($rsp_notice->NumNotice);
		$notice->setReservable($rsp_notice->Reservable);
		$noticesFilles = $rsp_notice->Exemplaires->NoticesFilles;

		if (! is_array($noticesFilles->NoticeFille)) return $notice;

		foreach ( $noticesFilles->NoticeFille as $nfille) {
			$id = $nfille->NumFille;
			$reservable = $nfille->Reservable;
			$exemplaire = new Class_WebService_SIGB_Exemplaire($id);
			$exemplaire->setDisponibilite($nfille->getDisponibilite());
			$exemplaire->setSection($nfille->getSection());
			$exemplaire->setBibliotheque($nfille->getBibliotheque());
			$exemplaire->setCodeBarre($nfille->getCodeBarre());
			$exemplaire->setDateRetour($nfille->getDateRetour());
			$exemplaire->setReservable($nfille->Reservable);
			$exemplaire->setCote($nfille->getCote());
			$exemplaire->setEmplacement($nfille->getEmplacement());
			$notice->addExemplaire($exemplaire);
		}
		return $notice;
	}
}

class RspRecupererNotice {
	public $GUIDSession; // string
	public $ErreurService; // WebSrvErreur
	public $Notice; // DetailNotice
}

class DetailNotice {
	public $ServeursSource; // ArrayOfServeurSession
	public $ServeurSource; // string
	public $BaseSource; // string
	public $NumNotice; // string
	public $ISBN; // string
	public $SupportNotice; // string
	public $RegleNotice; // string
	public $Contenu; // string
	public $NbNoticesRattaches; // int
	public $ISBD; // ArrayOfParagrapheISBD
	public $ISBDComplet; // ArrayOfParagrapheISBD
	public $DateDebut; // string
	public $DateFin; // string
	public $Exposition; // string
	public $Exemplaires; // ListeNoticesFilles
	public $Acquisitions; // ListeNoticesFilles
	public $Abonnements; // ListeNoticesFilles
	public $Commentaires; // ListeNoticesFilles
	public $AutresFilles; // ListeNoticesFilles
	public $Multimedias; // ArrayOfMultiMedia
	public $LieuxReservation; // ArrayOfLieuReservation
	public $StatusNotice; // string
	public $DocumentRattaches; // boolean
	public $Reservable; // boolean
	public $Imagette; // string
	public $NumNoticePrecedente; // string
	public $NumNoticeSuivante; // string
}

class ListeNoticesFilles {
	public $DescriptionFilles; // string
	public $NoticesFilles; // ArrayOfNoticeFille
}




class NoticeFille {
	public $NumFille; // string
	public $StatutFille; // string
	public $DetailFille; // string
	public $Reservable; // boolean
	public $Demandable; // boolean
	public $ImagePlan; // string
	public $CoordonneesPlan; // string
	public $DonneesFille; // ArrayOfDonneeFille

	protected function getData($name){
		foreach($this->DonneesFille->DonneeFille as $data){
			if (false !== strpos(strtolower($data->NomDonnee), strtolower($name)))
				return $data->ValeurDonnee;
		}
		return "";
	}


	public function getDisponibilite(){
		$dispo = $this->getData("Disponibilité");
		if (!$piege = $this->getData("Piège"))
			$piege = $this->getData("Piege");
		
		if (strlen($piege) > 0) $dispo .= " ($piege)";
		return $dispo;
	}


	public function getSection(){
		return $this->getData('Section');
	}


	public function getBibliotheque(){
		return $this->getData("Site");
	}


	public function getCodeBarre(){
		return $this->getData("Code barre");
	}


	public function getDateRetour(){
		return $this->getData("Retour");
	}


	public function getCote() {
		return $this->getData('Cote');
	}


	public function getEmplacement() {
		if (!$code = $this->getData('Emplacement'))
			return '';
		if (!$emplacement = Class_CodifEmplacement::findFirstBy(['regles' => '995$u='.$code]))
			return '';
		return $emplacement->getId();
	}
}



class DonneeFille {
	public $NomDonnee; // string
	public $ValeurDonnee; // string
}

class MultiMedia {
	public $TitreMedia; // string
	public $AdresseMedia; // string
	public $CategorieMedia; // EUCategorieMedia
	public $PositionMedia; // EUPositionMedia
	public $AffichageMedia; // EUAffichageMedia
}

class EUCategorieMedia {
	const Son = 'Son';
	const Video = 'Video';
	const Image = 'Image';
	const Autre = 'Autre';
}

class EUPositionMedia {
	const Indirect = 'Indirect';
	const Avant = 'Avant';
	const Droite = 'Droite';
	const Gauche = 'Gauche';
	const Apres = 'Apres';
}

class EUAffichageMedia {
	const DirectSansTitre = 'DirectSansTitre';
	const DirectAvecTitre = 'DirectAvecTitre';
	const LienIndirect = 'LienIndirect';
}

class LieuReservation {
	public $Code; // string
	public $Libelle; // string
}

class Exporter {
	public $Param; // EntExporter
}

class EntExporter {
	public $ParamExport; // Exportation
	public $NumNotices; // ArrayOfString
	public $IDEtape; // string
	public $RangDepart; // int
	public $NombreNotices; // int
}

class Exportation {
	public $TypeExport; // string
	public $CodeExport; // string
	public $NiveauExport; // string
}

class ExporterResponse {
	public $ExporterResult; // RspExporter
}

class RspExporter {
	public $GUIDSession; // string
	public $ErreurService; // WebSrvErreur
	public $NomFichier; // string
	public $AdresseFichier; // string
}

class SupprFichierExport {
	public $Param; // EntFichiersSupprimer
}

class EntFichiersSupprimer {
	public $NomsFichiers; // ArrayOfString
}

class SupprFichierExportResponse {
	public $SupprFichierExportResult; // RspEmprAction
}

class RspEmprAction {
	public $MessageRetour; // string
	public $GUIDSession; // string
	public $ErreurService; // WebSrvErreur
	public $Reussite; // boolean

	public function getReussite(){
		$statut = (int)($this->Reussite == "true");
		$reussite = array("statut" => $statut, "erreur" => "");

		if (! $statut)
			$reussite["erreur"] = $this->ErreurService->LibelleErreur;
		return $reussite;
	}
}

class PanierLister {
	public $Param; // Entree
}

class PanierListerResponse {
	public $PanierListerResult; // RspPanierLister
}

class RspPanierLister {
	public $GUIDSession; // string
	public $ErreurService; // WebSrvErreur
	public $Paniers; // ArrayOfPanier
}

class Panier {
	public $NomPanier; // string
	public $NbNotices; // int
	public $DateExpiration; // string
	public $TypeNotices; // string
}

class PanierAfficher {
	public $Param; // EntPanierAfficher
}

class EntPanierAfficher {
	public $NomPanier; // string
	public $IDEtape; // string
}

class PanierAfficherResponse {
	public $PanierAfficherResult; // RspRechercher
}

class PanierRenommer {
	public $Param; // EntPanierRenommer
}

class EntPanierRenommer {
	public $NomPanier; // string
	public $NouveauNom; // string
}

class PanierRenommerResponse {
	public $PanierRenommerResult; // RspEmprAction
}

class PanierAction {
	public $Param; // EntPanierAction
}

class EntPanierAction {
	public $Operation; // EUPanierOperation
	public $NomPanier; // string
	public $IDEntite; // string
}

class EUPanierOperation {
	const AjouterNotice = 'AjouterNotice';
	const AjouterListe = 'AjouterListe';
	const EnleverNotice = 'EnleverNotice';
	const EnleverListe = 'EnleverListe';
}

class PanierActionResponse {
	public $PanierActionResult; // RspEmprAction
}

class PanierSupprimer {
	public $Param; // EntPanierSupprimer
}

class EntPanierSupprimer {
	public $NomsPanier; // ArrayOfString
}

class PanierSupprimerResponse {
	public $PanierSupprimerResult; // RspEmprAction
}

class EmprAuthentifier{
	public $Param; // EntEmprAuthentifier

	public function __construct($guid, $IDEmprunteur, $MotDePasse){
		$this->Param=new EntEmprAuthentifier();
		$this->Param->IDEmprunteur=$IDEmprunteur;
		$this->Param->MotDePasse=$MotDePasse;
		$this->Param->GUIDSession=$guid;
	}
}


class EntEmprAuthentifier extends Entree {
	public $IDEmprunteur; // string
	public $MotDePasse; // string
	public $Place; // string
}


class EmprAuthentifierResponse {
	public $EmprAuthentifierResult; // RspEmprAuthentifier

	/**
	 * @return Class_WebService_SIGB_Emprunteur
	 */
	public function createEmprunteur(){
		$emprunteur = new Class_WebService_SIGB_Emprunteur(
																 $this->EmprAuthentifierResult->IDEmprunteur,
																 $this->EmprAuthentifierResult->IdentiteEmpr);
		$emprunteur
			->setEmail($this->EmprAuthentifierResult->EmailEmpr)
			->setNbReservations($this->EmprAuthentifierResult->NombreReservations)
			->setNbEmprunts($this->EmprAuthentifierResult->NombrePrets)
			->setNbPretsEnRetard($this->EmprAuthentifierResult->NombreRetards);

		if ($this->EmprAuthentifierResult->IDEmprunteur)
			$emprunteur->beValid();

		return $emprunteur;
	}
}


class RspEmprAuthentifier {
	public $GUIDSession; // string
	public $ErreurService; // WebSrvErreur
	public $TypeEmpr; // string
	public $DescriptionEmpr; // string
	public $IdentiteEmpr; // string
	public $EmailEmpr; // string
	public $NombrePrets; // int
	public $NombrePretsReserve; // int
	public $NombreRetards; // int
	public $NombreReservations; // int
	public $NombreDemandeReservations; // int
	public $NombreCommunicationSurPlace; // int
	public $NombreMessages; // int
	public $NombreRequetes; // int
	public $NombrePaniers; // int
	public $DroitsEmpr; // DroitsEmpr
	public $IDEmprunteur; // string
}

class DroitsEmpr {
	public $MultiSelectExeResa; // boolean
	public $NombrePaniersMax; // int
	public $NombreRequetesMax; // int
	public $Telecharger; // boolean
	public $RechercherFondenPret; // boolean
	public $Imprimer; // boolean
	public $ReserverExpo; // boolean
}

class EmprListerEntite {
	public $Param; // EntEmprListerEntite

	public function __construct($guid, $entite){
		$this->Param = new EntEmprListerEntite();
		$this->Param->GUIDSession=$guid;
		$this->Param->IdEntite=$entite;
	}

	public static function prets($guid){
		return new self($guid, EUEntiteEmp::ListePret);
	}

	public static function prets_en_retard($guid){
		return new self($guid, EUEntiteEmp::ListeRetard);
	}


	public static function reservations($guid){
		return new self($guid, EUEntiteEmp::ListeResa);
	}
}

class EntEmprListerEntite extends Entree {
	public $IdEntite; // EUEntiteEmp
}

class EUEntiteEmp {
	const ListeInfo = 'ListeInfo';
	const ListePret = 'ListePret';
	const ListeResa = 'ListeResa';
	const ListeDDR = 'ListeDDR';
	const ListePretResa = 'ListePretResa';
	const ListeMessage = 'ListeMessage';
	const ListeRetard = 'ListeRetard';
	const ListeComsurPlace = 'ListeComsurPlace';
}

class EmprListerEntiteResponse {
	public $EmprListerEntiteResult; // RspEmprListerEntite

	public function getEntites($container_class){
		return $this->EmprListerEntiteResult->getExemplaires($container_class);
	}
}

class RspEmprListerEntite {
	public $GUIDSession; // string
	public $ErreurService; // WebSrvErreur
	public $DescriptionEntite; // string
	public $Entite; // EntiteEmp
	public $CodesEntite; // ArrayOfEntiteEmp


	public function getExemplaires($container_class){
		if (!isset($this->Entite)) return array();
		return $this->Entite->getExemplaires($container_class);
	}
}




class EntiteEmp {
	public $LibelleDonnee; // ArrayOfString
	public $Titre; // string
	public $Donnees; // ArrayOfDonneeEmp
	public $Nombre; // int


	protected function findAttribute($name, &$attributes) {
		foreach($attributes as $libelle => $value) {
			if (false !== strpos(strtolower($libelle), strtolower($name)))
				return $value;
		}
		return "";
	}
	

	public function getExemplaires($container_class){
		$entites = array();
		foreach($this->Donnees->Lignes as $data){
			$attributes = array_combine($this->LibelleDonnee->string, $data->ValeursDonnees->string);

			$exemplaire = new Class_WebService_SIGB_Exemplaire(NULL);
			$exemplaire->setTitre($this->findAttribute('Titre', $attributes));

			if ($code_barre = $this->findAttribute('code', $attributes))
				$exemplaire_params = array('code_barres' => $code_barre);
			else
				$exemplaire_params = array('id_origine' => $this->findAttribute('notice', $attributes));
			
			$exemplaire_opac = Class_Exemplaire::getLoader()->findFirstBy($exemplaire_params);
			$exemplaire->setExemplaireOPAC($exemplaire_opac);
			
			$entite = new $container_class($data->ValeursDonnees->string[0], $exemplaire);
			$entite->parseExtraAttributes($attributes);

			$entites[]=$entite;
		}

		return $entites;
	}
}




class DonneeEmp {
	public $ValeursDonnees; // ArrayOfString
}

class EmprReserver {
	public $Param; // EntEmprReserver
	public $CodeLieu; // string

	public function __construct($guid, $exemplaire_id, $code_annexe){
		$this->Param = new EntEmprReserver();
		$this->Param->GUIDSession=$guid;
		$this->Param->addNotice($exemplaire_id);
		$this->CodeLieu = $code_annexe;
	}
}

class EntEmprReserver extends Entree {
	public $NumNotices; // ArrayOfString

	public function __construct(){
		$this->NumNotices = new StdClass();
		$this->NumNotices->string = array();
	}

	public function addNotice($id){
		$this->NumNotices->string[]=$id;
	}
}

class EmprReserverResponse {
	public $EmprReserverResult; // RspEmprAction

	public function getReussite(){
		return $this->EmprReserverResult->getReussite();
	}
}

class EmprSupprResa {
	public $Param; // EntEmprSupprResa

	public function __construct($guid, $reservation_id){
		$this->Param = new EntEmprSupprResa();
		$this->Param->GUIDSession = $guid;
		$this->Param->addReservation(
																 EUTypeReservation::Reservation,
																 $reservation_id);
	}
}

class EntEmprSupprResa extends Entree{
	public $ListeResa; // ArrayOfResaIdType

	public function __construct(){
		$this->ListeResa = new StdClass();
		$this->ListeResa->ResaIdType = array();
	}

	public function addReservation($type_reservation, $reservation_id){
		$this->ListeResa->ResaIdType[]= new ResaIdType($type_reservation, $reservation_id);
	}
}

class ResaIdType {
	public $IDResa; // string
	public $TypeResa; // EUTypeReservation

	public function __construct($type_reservation, $reservation_id){
		$this->IDResa = $reservation_id;
		$this->TypeResa = $type_reservation;
	}
}

class EUTypeReservation {
	const Reservation = 'Reservation';
	const DemandeDeReservation = 'DemandeDeReservation';
}

class EmprSupprResaResponse {
	public $EmprSupprResaResult; // RspEmprAction

	public function getReussite(){
		return $this->EmprSupprResaResult->Reussite;
	}
}

class EmprProlong {
	public $Param; // EntEmprProlong

	public function __construct($guid, $reservation_id){
		$this->Param = new EntEmprProlong();
		$this->Param->GUIDSession = $guid;
		$this->Param->ListeProlong = array($reservation_id);
	}
}

class EntEmprProlong {
	public $ListeProlong; // ArrayOfString
}


class EmprProlongResponse {
	public $EmprProlongResult; // RspEmprAction

	public function getReussite(){
		$result = $this->EmprProlongResult->getReussite();

		// cf test OpsysServiceTestProlongerPret::testEmprProlongNotDone
		$message = $this->EmprProlongResult->MessageRetour;
		if (('' === $message) || strpos($message, 'Aucune') !== false) {
			$result['erreur'] = 'La prolongation de ce document est impossible';
			$result['statut'] = 0;
		}
		return $result;
	}
}


class EmprListerDestinataires {
	public $Param; // Entree
}

class EmprListerDestinatairesResponse {
	public $EmprListerDestinatairesResult; // RspEmprListerDestinataires
}

class RspEmprListerDestinataires {
	public $GUIDSession; // string
	public $ErreurService; // WebSrvErreur
	public $Destinataires; // ArrayOfDestinataire
}

class Destinataire {
	public $CodeDestinataire; // string
	public $LibelleDestinataire; // string
}

class EmprEnvoyerMessage {
	public $Param; // EntEmprEnvoyerMessage
}

class EntEmprEnvoyerMessage {
	public $Destinataire; // string
	public $Message; // string
}

class EmprEnvoyerMessageResponse {
	public $EmprEnvoyerMessageResult; // RspEmprAction
}

class EmprEffacerMessage {
	public $Param; // EntEmprEffacerMessage
}

class EntEmprEffacerMessage {
	public $ListeIDMessages; // ArrayOfString
}

class EmprEffacerMessageResponse {
	public $EmprEffacerMessageResult; // RspEmprAction
}

class RequeteLister {
	public $Param; // Entree
}

class RequeteListerResponse {
	public $RequeteListerResult; // RspRequeteLister
}

class RspRequeteLister {
	public $GUIDSession; // string
	public $ErreurService; // WebSrvErreur
	public $Requetes; // ArrayOfRequete
}

class Requete {
	public $LibelleReq; // string
	public $NomReq; // string
	public $Phrase; // string
	public $Descente; // boolean
	public $DateCreationReq; // string
	public $NbNoticeCreationReq; // int
	public $PeriodiciteReq; // int
	public $TypeExportReq; // Exportation
	public $DateExecutionReq; // string
	public $NbNoticeExecReq; // int
	public $ModeEnvoiReq; // string
	public $EmailReq; // string
}

class RequeteMemoriser {
	public $Param; // EntReqMemoriser
}

class EntReqMemoriser {
	public $Requete; // Requete
}

class RequeteMemoriserResponse {
	public $RequeteMemoriserResult; // RspEmprAction
}

class RequeteExecuter {
	public $Param; // EntReqExecuter
}

class EntReqExecuter {
	public $NomRequete; // string
	public $IDEtape; // string
}

class RequeteExecuterResponse {
	public $RequeteExecuterResult; // RspRechercher
}

class RequeteSupprimer {
	public $Param; // EntReqSupprimer
}

class EntReqSupprimer {
	public $NomRequetes; // ArrayOfString
}

class RequeteSupprimerResponse {
	public $RequeteSupprimerResult; // RspEmprAction
}

class Dedoublonnage {
	public $Param; // Entree
}

class DedoublonnageResponse {
}

class EtatDedoublonnage {
	public $Param; // Entree
}

class EtatDedoublonnageResponse {
	public $EtatDedoublonnageResult; // int
}

class NombreDedoublonnage {
	public $Param; // Entree
}

class NombreDedoublonnageResponse {
	public $NombreDedoublonnageResult; // int
}

class EnvoyerFichier {
	public $CheminLocal; // string
	public $FichierLocal; // string
	public $CheminDistant; // string
	public $FichierDistant; // string
}

class EnvoyerFichierResponse {
	public $EnvoyerFichierResult; // string
}

class RecevoirFichier {
	public $CheminLocal; // string
	public $FichierLocal; // string
	public $CheminDistant; // string
	public $FichierDistant; // string
}

class RecevoirFichierResponse {
	public $RecevoirFichierResult; // string
}

class TransfererMedia {
	public $Fichier; // string
}

class TransfererMediaResponse {
	public $TransfererMediaResult; // string
}

class ListerNoticesXML {
	public $Phrase; // string
	public $CodeAffichage; // string
	public $NiveauDetail; // string
	public $NombreNotices; // int
}

class ListerNoticesXMLResponse {
	public $ListerNoticesXMLResult; // ListeNoticeSimple
}

class ListeNoticeSimple {
	public $NbNotices; // int
	public $Notices; // ArrayOfNoticeSimple
	public $Erreur; // string
}

class NoticeSimple {
	public $Numero; // string
	public $Regle; // string
	public $Type; // string
	public $Contenu; // string
	public $NbNoticesRattaches; // int
	public $Rang; // int
	public $ISBN; // string
}

class ListerEmprunteursXML {
	public $Phrase; // string
	public $CodeAffichage; // string
	public $NiveauDetail; // string
	public $NombreNotices; // int
}

class ListerEmprunteursXMLResponse {
	public $ListerEmprunteursXMLResult; // ListeNoticeSimple
}

class LireTriordreGen {
	public $GUIDSession; // string
	public $NomTriordre; // string
}

class LireTriordreGenResponse {
	public $LireTriordreGenResult; // Triordre
}

class ReponseTriordre {
	public $Param; // Triordre
}

class ReponseTriordreResponse {
	public $ReponseTriordreResult; // RspTriordre
}

class RspTriordre {
	public $GUIDSession; // string
	public $ErreurService; // WebSrvErreur
	public $TypeGen; // string
	public $DescriptionEntite; // string
	public $Entite; // EntiteEmp
	public $CodesEntite; // ArrayOfEntiteEmp
}

class LireTriordrePret {
	public $GUIDSession; // string
	public $NomTriordre; // string
}

class LireTriordrePretResponse {
	public $LireTriordrePretResult; // Triordre
}

class ExporterPret {
	public $Param; // Triordre
	public $ParamExport; // Exportation
}

class ExporterPretResponse {
	public $ExporterPretResult; // RspExporter
}

class InfoExposition {
	public $GUIDSession; // string
	public $NoNotice; // string
	public $dateDebut; // string
	public $dateFin; // string
}

class InfoExpositionResponse {
	public $InfoExpositionResult; // Exposition
}

class Exposition {
	public $DateDebut; // string
	public $DateFin; // string
	public $NumNotice; // string
	public $Erreur; // string
	public $GUIDSession; // string
	public $Dates_x0020_Expo; // ArrayOfInfoExpo
}

class InfoExpo {
	public $DateDebut; // string
	public $DateFin; // string
	public $Libelle; // string
	public $Id; // string
	public $DateCurseur; // string
}

class ReserverExposition {
	public $GUIDSession; // string
	public $NoNotice; // string
	public $dateDebut; // string
	public $dateFin; // string
}

class ReserverExpositionResponse {
	public $ReserverExpositionResult; // string
}

class EnrCommentaire {
	public $p_avisNotice; // AvisNotice
}

class AvisNotice {
	public $GUIDSession; // string
	public $NumNotice; // string
	public $Titre; // string
	public $Commentaire; // string
	public $Note; // int
}

class EnrCommentaireResponse {
	public $EnrCommentaireResult; // string
}

class PublierCommentaires {
	public $p_listeAvis; // string
}

class PublierCommentairesResponse {
	public $PublierCommentairesResult; // string
}

class SupprimerCommentaires {
	public $p_listeAvis; // string
}

class SupprimerCommentairesResponse {
	public $SupprimerCommentairesResult; // string
}

class ModifierCommentaire {
	public $p_Avis; // string
	public $p_Titre; // string
	public $p_Commentaire; // string
}

class ModifierCommentaireResponse {
	public $ModifierCommentaireResult; // string
}



/* Classes pour le WebService Catalogue */

class EcrireNotice {
  public $paramModifNotice; // MaNotice
  public $NumNotice; // string
  public $CouE; // string
  public $CodeGrille; // string

	public function __construct() {
		$this->paramModifNotice = new MaNotice();
	}

	public function setGUID($guid) {
		$this->paramModifNotice->GUIDSession = $guid;
		return $this;
	}

	public function setGrille($grille) {
		$this->CodeGrille = $grille;
		return $this;
	}

	public function setCatalogue($catalogue) {
		$this->CouE = $catalogue;
		return $this;
	}

	public function readEmprunteur($emprunteur) {
		$this->NumNotice = $emprunteur->getId();
		$this->paramModifNotice->readEmprunteur($emprunteur);
	}
}

class EcrireNoticeResponse {
  public $EcrireNoticeResult; // MaNotice
}

class MaNotice {
  public $GUIDSession; // string
  public $ErreurService; // WebSrvErreur
  public $Champs; // ArrayOfImportChamp

	public function __construct() {
		//		$this->Champs = new StdClass();
		//		$this->Champs->ImportChamp = array();
		$this->Champs = array();
	}

	public function readEmprunteur($emprunteur) {
		$this
			->addChamp('100')
			->addSousChamp('100$a', $emprunteur->getNom())
			->addSousChamp('100$b', $emprunteur->getPrenom());
		$this
			->addChamp('115')
			->addSousChamp('115$e', $emprunteur->getEmail());
		$this
			->addChamp('120')
			->addSousChamp('120$a', $emprunteur->getPassword());
		return $this;
	}

	public function addChamp($etiquette) {
		$champ = new ImportChamp();
		$champ->setEtiquette($etiquette);
		// 		$this->Champs->ImportChamp []= $champ;
		$this->Champs []= $champ;
		return $champ;
	}
}

class ImportChamp {
  public $SousChamps; // ArrayOfImportSousChamp
  public $Etiquette; // string
  public $Description; // string

	public function __construct() {
		//		$this->SousChamps = new StdClass();
		//		$this->SousChamps->ImportSousChamp = array();
		$this->SousChamps  = array();
	}

	public function setEtiquette($etiquette) {
		$this->Etiquette = $etiquette;
		return $this;
	}

	public function addSousChamp($etiquette, $value) {
		$sous_champ = new ImportSousChamp();
		$sous_champ->Etiquette = $etiquette;
		$sous_champ->_ = $value;
		//		$this->SousChamps->ImportSousChamp []= $sous_champ;
		$this->SousChamps []= $sous_champ;
		return $this;
	}
}

class ImportSousChamp {
  public $_; // string
  public $Etiquette; // string
  public $Description; // string
}


?>