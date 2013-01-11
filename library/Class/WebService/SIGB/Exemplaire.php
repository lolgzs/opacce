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

class Class_WebService_SIGB_Exemplaire {
	const DISPO_EN_PRET = 'En prêt';
	const DISPO_LIBRE = 'Disponible';
	const DISPO_INDISPONIBLE = 'Indisponible';
	const DISPO_PERDU = 'Perdu';
	const DISPO_PILONNE = 'Pilonné';
	const DISPO_ENDOMMAGE = 'Endommagé';
	const DISPO_TRANSIT = 'En transit';
	const DISPO_DEJA_RESERVE = 'Réservé';
	const DISPO_EN_COMMANDE = 'En commande';


	protected $id;

	/** @var Class_WebService_SIGB_Notice */
	protected $notice;

	protected $titre;
	protected $reservable;
	protected $bibliotheque;
	protected $section;
	protected $auteur;
	protected $no_notice;
	protected $code_barre;
	protected $date_retour;
	protected $visible_opac;
	protected $code_annexe;
	protected $_notice_opac;
	protected $_exemplaire_opac;
	protected $_disponibiliteLabel;
	protected $_isEnPret;
	protected $_cote;
	protected $_emplacement;


	public static function newInstance() {
		return new self(0);
	}


	public function __construct($id){
		$this->id = $id;
		$this->titre = "";
		$this->disponibilite = self::DISPO_INDISPONIBLE;
		$this->bibliotheque = '';
		$this->section = '';
		$this->auteur = '';
		$this->code_barre = '';
		$this->visible_opac = true;
		$this->_disponibiliteLabel = null;
		$this->_isEnPret = false;
	}


	public function setNotice($notice){
		$this->notice = $notice;
		return $this;
	}


	public function setId($id) {
		$this->id = $id;
		return $this;
	}


	public function getNotice(){
		return $this->notice;
	}


	/**
	 * @return Class_Exemplaire
	 */
	public function getExemplaireOPAC() {
		if (isset($this->_exemplaire_opac))
			return $this->_exemplaire_opac;
		
		if ($no_notice = $this->getNoNotice())
			$params = array('id_origine' => $no_notice);
		
		if ($this->code_barre)
			$params = array('code_barres' => $this->code_barre);

		if (!isset($params))
			return null;
		
		return $this->_exemplaire_opac = Class_Exemplaire::getLoader()->findFirstBy($params);
	}


	/**
	 * @param exemplaire Class_Exemplaire
	 * @return Class_WebService_SIGB_Exemplaire
	 */
	public function setExemplaireOPAC($exemplaire) {
		$this->_exemplaire_opac = $exemplaire;
		return $this;
	}


	/**
	 * @param notice Class_Notice
	 * @return Class_WebService_SIGB_Exemplaire
	 */
	public function setNoticeOPAC($notice) {
		$this->_notice_opac = $notice;
		return $this;
	}


	/**
	 * @return Class_Notice
	 */
	public function getNoticeOPAC() {
		if (!isset($this->_notice_opac) and ($exemplaire_opac=$this->getExemplaireOPAC())) {
			$this->_notice_opac = $exemplaire_opac->getNotice();
		}
		return $this->_notice_opac;
	}


	public function getTitre(){
		if (!$this->titre  and ($notice = $this->getNoticeOPAC()))
			$this->titre = $notice->getTitrePrincipal();

		return $this->titre;
	}


	public function setTitre($titre){
		$this->titre=$titre;
		return $this;
	}


	public function setVisibleOpac($visible) {
		$this->visible_opac = $visible;
		return $this;
	}


	public function isVisibleOpac() {
		return $this->visible_opac;
	}


	public function setBibliotheque($bibliotheque){
		$this->bibliotheque = $bibliotheque;
		return $this;
	}


	public function getBibliotheque(){
		if (!$this->bibliotheque  and ($exemplaire = $this->getExemplaireOPAC()))
			$this->bibliotheque = $exemplaire->getBib()->getLibelle();

		return $this->bibliotheque;
	}


	public function getAuteur(){
		if (!$this->auteur  and ($notice = $this->getNoticeOPAC()))
			$this->auteur = $notice->getAuteurPrincipal();

		return $this->auteur;
	}


	public function setAuteur($auteur){
		$this->auteur = $auteur;
		return $this;
	}


	public function setSection($section){
		$this->section = $section;
		return $this;
	}


	public function getSection(){
		return $this->section;
	}


	public function setDateRetour($date_retour) {
		$this->date_retour = $date_retour;
		return $this;
	}


	public function getDateRetour(){
		return $this->date_retour;
	}


	public function getCodeBarre(){
		if (!$this->code_barre && ($ex_opac = $this->getExemplaireOPAC()))
			$this->code_barre = $ex_opac->getCodeBarres();
		return $this->code_barre;
	}


	public function setCodeBarre($code_barre){
		$this->code_barre = $code_barre;
		return $this;
	}


	public function setCote($cote) {
		$this->_cote = $cote;
		return $this;
	}


	public function getCote() {
		return $this->_cote;
	}


	public function setEmplacement($emplacement) {
		$this->_emplacement = $emplacement;
		return $this;
	}


	public function getEmplacement() {
		return $this->_emplacement;
	}


	public function getId(){
		return $this->id;
	}


	public function setNoNotice($no_notice) {
		$this->no_notice = $no_notice;
		return $this;
	}


	public function getNoNotice()	{
		return $this->no_notice;
	}


	/**
	 * @return string
	 */
	public function getDisponibilite() {
		if ($this->_isEnPret)
			return $this->disponibilite;
		if (null !== $this->_disponibiliteLabel)
			return $this->_disponibiliteLabel;
		return $this->disponibilite;
	}


	public function setDisponibiliteEnPret() {
		$this->setDisponibilite($this->getLibelleDispoEnPret());
		$this->_isEnPret = true;
		return $this;
	}


	public function setDisponibiliteEnTransit() {
		return $this->setDisponibilite(self::DISPO_TRANSIT);
	}


	public function getLibelleDispoEnPret() {
		if (!$tmp = Class_Profil::getCurrentProfil()->getCfgNoticeAsArray())
			return self::DISPO_EN_PRET;

		if (array_isset("en_pret", $tmp["exemplaires"]))
			 return $tmp["exemplaires"]["en_pret"];

		return self::DISPO_EN_PRET;
	}


	public function setDisponibiliteLibre()	{
		$this->setDisponibilite(self::DISPO_LIBRE);
		return $this;
	}


	public function setDisponibiliteIndisponible()	{
		$this->setDisponibilite(self::DISPO_INDISPONIBLE);
		return $this;
	}


	public function setDisponibilitePerdu()	{
		$this->setDisponibilite(self::DISPO_PERDU);
		return $this;
	}


	public function setDisponibilitePilonne()	{
		$this->setDisponibilite(self::DISPO_PILONNE);
		return $this;
	}


	public function setDisponibiliteEndommage()	{
		$this->setDisponibilite(self::DISPO_ENDOMMAGE);
		return $this;
	}


	public function setDisponibiliteDejaReserve() {
		$this->setDisponibilite(self::DISPO_DEJA_RESERVE);
		return $this;
	}


	public function setDisponibiliteEnCommande() {
		$this->setDisponibilite(self::DISPO_EN_COMMANDE);
		return $this;
	}


	public function setDisponibilite($disponibilite){
		$this->disponibilite = $disponibilite;
		if ($this->disponibilite == "") $this->setDisponibiliteLibre();
		return $this;
	}


	public function getDisponibiliteLabel() {
		return $this->_disponibiliteLabel;
	}


	public function setDisponibiliteLabel($label) {
		$this->_disponibiliteLabel = $label;
		return $this;
	}


	public function setReservable($reservable){
		$this->reservable = ($reservable == "true");
		return $this;
	}


	public function beReservable(){
		return $this->setReservable(true);
	}


	public function getCodeAnnexe() {
		return $this->code_annexe;
	}


	public function setCodeAnnexe($id) {
		$this->code_annexe = $id;
		return $this;
	}


	/**
	 * @return bool
	 */
	public function isReservable(){
		if (isset($this->reservable))
			return $this->reservable;

		if (isset($this->notice))
			return $this->notice->isReservable();

		return false;
	}


	/**
	 * @return bool
	 */
	public function isPilonne(){
		return $this->getDisponibilite() == self::DISPO_PILONNE;
	}


	public function isValid() {
		return (null !== $this->getId());
	}


	/** @codeCoverageIgnore */
	public function __toString(){
		$str = $this->titre;
		if (isset($this->id)) $str.=" #".$this->id.",";
		$str .= '  Reservable: '.($this->isReservable() ? 'true' : 'false').",";
		$str .= '  Disponibilite: '.$this->getDisponibilite();
		$str .= '  Section:'.$this->getSection();
		$str .= '  Bibliothèque:'.$this->getBibliotheque();
		$str .= '  Auteur:'.$this->getAuteur();
		return $str;
	}
}

?>