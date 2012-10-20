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

//////////////////////////////////////////////////////////////////////////////////////
// OPAC3 : CLASSE NOTICE (Surcharge la classe Class_NoticeUnimarc)
//////////////////////////////////////////////////////////////////////////////////////

class NoticeLoader extends Storm_Model_Loader
{

	public function getNoticesFromPreferences($preferences)
	{
		$catalogue = new Class_Catalogue();
		$requetes = $catalogue->getRequetes($preferences);
		$notices = $this->findAll($requetes["req_liste"]);

		// Tirage aleatoire
		if ($preferences["aleatoire"] == 1)
		{
			shuffle($notices);
			$notices = array_slice($notices, 0, $preferences["nb_notices"]);
		}

		return $notices;
	}


	public function getNoticeByClefAlpha($clef) {
		return Class_Notice::findFirstBy(['clef_alpha' => $clef]);
	}


	public function getAllNoticesByClefAlpha($clef) {
		return Class_Notice::findAllBy(['clef_alpha' => $clef]);
	}


	public function findAllByCatalogue($catalogue) {
		return Class_Catalogue::getLoader()->loadNoticesFor($catalogue);
	}


	public function getEarliestNotice() {
		$result = $this->findAllBy(array('limit' => 1,
																		 'order' => 'date_maj asc'));
		if (0 == count($result))
			return null;

		return $result[0];
	}
}




class Class_Notice extends Storm_Model_Abstract {
	protected $_loader_class = 'NoticeLoader';
	protected $_table_name = 'notices';
	protected $_table_primary = 'id_notice';
	/** @var Class_NoticeUnimarc */
	protected $_notice_unimarc;
	protected $_has_many = ['exemplaires' => ['model' => 'Class_Exemplaire',
																						'role' => 'notice']];


	protected
		$_titre_principal,
		$_auteur_principal,
		$_avis,
		$_moderated_avis,
		$_resume,
		$_matieres,
		$_editeur,
		$_langueCodes;

	protected $_default_attribute_values = ['type_doc' => 0,
																					'annee' => null,
																					'isbn' => null,
																					'ean' => null,
																					'tome_alpha' => '',
																					'clef_chapeau' => '',
																					'facettes' => ''];


	public function getAvisByUser($user)	{
		return Class_AvisNotice::getLoader()
			->findAllBy(['clef_oeuvre' => $this->getClefOeuvre(),
					         'id_user' => $user->getId()]);
	}


	public function getAvis() {
		if (!isset($this->_avis))	{
			$avis_loader = Class_AvisNotice::getLoader();
			$this->_avis = $avis_loader->findAllBy(['clef_oeuvre' => $this->getClefOeuvre()]);
		}

		return $this->_avis;
	}


	public function getAvisBibliothecaire() {
		return Class_AvisNotice::filterByBibliothequaire($this->getAvis());
	}


	public function getAvisAbonne() {
		return Class_AvisNotice::filterByAbonne($this->getAvis());
	}


	public function getExemplairesByIdSite($id_site) {
		$all_exemplaires = $this->getExemplaires();
		$exemplaire_on_site = array();
		foreach ($all_exemplaires as $exemplaire) {
			if ($id_site == $exemplaire->getIdBib())
				$exemplaire_on_site []= $exemplaire;
		}
			
		return $exemplaire_on_site;
	}


	/**
	 * @return Class_Album
	 */
	public function getAlbum() {
		if ($first_exemplaire = $this->getFirstExemplaire())
			return Class_Album::find($first_exemplaire->getIdOrigine());
		return null;
	}


	/**
	 * @return Class_Exemplaire
	 */
	public function getFirstExemplaire() {
		if (!$this->hasExemplaires())
			return null;
		return array_first($this->getExemplaires());
	}


	/**
	 * @return boolean
	 */
	public function isLivreNumerique() {
		return ($this->getTypeDoc() == Class_TypeDoc::LIVRE_NUM);
	}


	/**
	 * @return Class_Notice
	 */
	 public function beLivreNumerique() {
		 return $this->setTypeDoc(Class_TypeDoc::LIVRE_NUM);
	 }


	 /**
		* @return Class_Notice
		*/
	 public function beLivre() {
		 return $this->setTypeDoc(Class_TypeDoc::LIVRE);
	 }


	 public function getAllAvisPerSource($page = null)
	 {
		 $all_avis = array('bib' => array('liste' => $avis_bib = $this->getAvisBibliothequaires(),
						 'note' => $this->getNoteMoyenneAvisBibliothequaires(),
						 'nombre' => count($avis_bib),
						 'titre' => 'Bibliothécaires'),
				 'abonne' => array('liste' => $avis_abon = $this->getAvisAbonnes(),
						 'note' => $this->getNoteMoyenneAvisAbonnes(),
						 'nombre' => count($avis_abon),
						 'titre' => 'Lecteurs du portail'));

		 foreach (array('Class_WebService_Babelio', 'Class_WebService_Amazon') as $provider_class)
		 {
			 $provider = new $provider_class();
			 $source = strtolower(array_last(explode('_', $provider_class)));
			 if ($data = $provider->getAvis($this, $page)) $all_avis[$source] = $data;
		 }

				return $all_avis;
	}

	public function getAvisBibliothequaires()
	{
		return Class_AvisNotice::filterByBibliothequaire($this->getAvis());
	}

	public function getAvisAbonnes()
	{
		return Class_AvisNotice::filterByAbonne($this->getAvis());
	}

	public function getNoteMoyenneAvisBibliothequaires()
	{
		return Class_AvisNotice::getNoteAverage($this->getAvisBibliothequaires());
	}

	public function getNoteMoyenneAvisAbonnes()
	{
		return Class_AvisNotice::getNoteAverage($this->getAvisAbonnes());
	}

	public function setAvis($list_avis)
	{
		$this->_avis = $list_avis;
	}

	public function setUnimarc($unimarc)
	{
		$this->getNoticeUnimarc()->setNotice($unimarc, 0);
		parent::setUnimarc($unimarc);
	}


	public function getUrlVignette() {
		if ($this->hasVignette()) return $this->_attributes['url_vignette'];

		$vignette_image = Class_WebService_Vignette::getUrl($this->getId());
		return $vignette_image['vignette'];
	}


	public function getUrlImage() {
		if ($this->hasVignette()) return $this->_attributes['url_image'];

		$image = Class_WebService_Vignette::getUrl($this->getId());
		return $image['image'];
	}


	public function hasVignette()	{
		try	{
			$url = $this->_attributes['url_vignette'];
		} catch (Exception $e)	{
			return false;
		}
		$has_vignette = (!empty($url) && ($url != 'NO'));
		return $has_vignette;
	}


	public function isLivre()	{
		return $this->getTypeDoc() == Class_TypeDoc::LIVRE;
	}


	public function isPeriodique() {
		return $this->getTypeDoc() == Class_TypeDoc::PERIODIQUE;
	}


	public function isDVD() {
		return $this->getTypeDoc() == Class_TypeDoc::DVD;
	}


	public function isArteVOD() {
		return ($this->getTypeDoc() == Class_TypeDoc::ARTEVOD);
	}

	public function isVignetteUpdatableToCacheServer() {
		return ($this->getTypeDoc() <= 5);
	}


	// ----------------------------------------------------------------
	// délégation des appels notice unimarc (Class_Notice n'hérite plus NoticeUnimarc)
	// ----------------------------------------------------------------
	public function __call($method, $args) {
		if (method_exists($this->getNoticeUnimarc(), $method)) {
			if (!$this->getNoticeUnimarc()->hasNotice()
				  and array_key_exists('unimarc', $this->_attributes))
				$this->getNoticeUnimarc()->setNotice($this->_attributes['unimarc']);
			return call_user_func_array(array($this->getNoticeUnimarc(), $method), $args);
		}

		return parent::__call($method, $args);
	}


	protected function _resetFieldsForCompatibility() {
		// c'est LL le coupable, en attendant de convertir en joli objets
		unset($this->_titre_principal);
		unset($this->_auteur_principal);
		unset($this->_resume);
	}


	public function getIsbnOrEan() {
		return $this->hasIsbn() ? str_replace("-", "", $this->getIsbn()) :  $this->getEan();
	}


// ----------------------------------------------------------------
// Rend la structure notice pour affichage liste
// ----------------------------------------------------------------
	public function getNotice($id_notice, $champs = '')	{
		$this->_resetFieldsForCompatibility();	// note: ne pas enlever sinon impossible à tester les notices

		if (!$id_notice)
			return null;

		// Lire la notice
		$req = "select type_doc,facettes,isbn,ean,annee,tome_alpha,clef_alpha,unimarc from notices where id_notice=$id_notice";
		$data = fetchEnreg($req);
		if (!$data["unimarc"])
			return false;

		$this->getNoticeUnimarc()->setNotice($data["unimarc"], 0);

		// Champs de base
		$notice["id_notice"] = $id_notice;
		$notice["facettes"] = $data["facettes"];
		$notice["isbn"] = $data["isbn"];
		$notice["ean"] = $data["ean"];
		$notice["type_doc"] = $data["type_doc"];
		$notice["tome_alpha"] = $data["tome_alpha"];
		$notice["N"] = $data["annee"];
		$notice["clef_alpha"] = $data["clef_alpha"];

		// Id service (isbn ou ean)
		$notice["id_service"] = ($data["isbn"]) ? str_replace("-", "", $data["isbn"]) : $data["ean"];

		// Champs demandés
		if ($champs) {
			for ($i = 0; $i < strlen($champs); $i++) {
				switch ($champs[$i]) {
					case "T": $notice["T"] = $this->getTitrePrincipal($notice["type_doc"], $notice["tome_alpha"]); break;
					case "A": $notice["A"] = $this->getAuteurPrincipal(); break;
					case "E": $notice["E"] = $this->getEditeur(); break;
					case "F": $notice["F"] = $this->getCentreInteret(); break;
					case "C": $notice["C"] = $this->getCollection(true); break;
					case "O": $notice["O"] = $this->getNotes(); break;
					case "R": $notice["R"] = $this->getResume(); break;
					case "U": $notice["U"] = $data["unimarc"]; break;
					case "G": $notice["G"] = $this->getChampNotice("G", $notice["facettes"]); break;
				}
			}
		}
		return $notice;
	}

// ----------------------------------------------------------------
// Rend la structure notice par sa clef alpha pour affichage liste
// ----------------------------------------------------------------
	public function getNoticeByClefAlpha($clef_alpha, $champs="")
	{
		$id_notice = fetchOne("select id_notice from notices where clef_alpha='$clef_alpha'");
		if (!$id_notice) return false;
		return $this->getNotice($id_notice, $champs);
	}

// ----------------------------------------------------------------
// Structure pour affichage notice (entete onglets et blocs)
// ----------------------------------------------------------------
	public function getNoticeDetail($id_notice, $preferences)
	{
		// Lire la notice de base
		$notice = $this->getNotice($id_notice, "N");
		if (!$notice) return false;

		// Entete
		$notice["titre_principal"] = $this->getTitrePrincipal($notice["type_doc"], $notice["tome_alpha"]);
		$notice["auteur_principal"] = $this->getAuteurPrincipal();
		$champs = $preferences["entete"];

		if ($champs)
		{
			for ($i = 0; $i < strlen($champs); $i++)
			{
				$clef = $champs[$i];
				$rubrique = Class_Codification::getNomChamp($clef, 1);
				if ($clef == "N") $notice["entete"][$rubrique] = $notice[$clef];
				else $notice["entete"][$rubrique] = $this->getChampNotice($clef, $notice["facettes"]);
			}
		}

		// Blocs et onglets
		if (!$preferences["onglets"]) $preferences["onglets"] = array();
		$notice["blocs"] = array();
		$notice["onglets"] = array();
		foreach ($preferences["onglets"] as $rubrique => $valeurs)
		{
			if (!$valeurs["aff"]) continue;
			$r = array();
			$index = $valeurs["ordre"];
			$titre = $valeurs["titre"];
			if (!$titre) $titre = Class_Codification::getNomOnglet($rubrique);
			if ($valeurs["aff"] == "3")
			{
				$notice["onglets"][$index]["titre"] = $titre;
				$notice["onglets"][$index]["type"] = $rubrique;
				$notice["onglets"][$index]["largeur"] = $valeurs["largeur"];
			} else
			{
				$notice["blocs"][$index]["titre"] = $titre;
				$notice["blocs"][$index]["type"] = $rubrique;
				$notice["blocs"][$index]["aff"] = $valeurs["aff"];
			}
		}

		ksort($notice["blocs"]);
		ksort($notice["onglets"]);

		return $notice;
	}

// ----------------------------------------------------------------
// Renvoie tous les champs de la notice (notice detaillee)
// ----------------------------------------------------------------
	public function getTousChamps($id_notice) {
		// Lire la notice de base
		$notice = $this->getNotice($id_notice, "N");
		if (!$notice)
			return false;

		// Champs
		$champs = "TAKEFCNMDGPILOR8";
		for ($i = 0; $i < strlen($champs); $i++) {
			$clef = $champs[$i];
			$rubrique = Class_Codification::getNomChamp($clef, 1);
			if ($clef == "N") {
				$notice["entete"][$rubrique] = $notice["N"];
			} elseif ($clef == "I") {
				if ($notice["isbn"])
					$notice["entete"]["Isbn"] = $notice["isbn"];
				else
					$notice["entete"]["Ean"] = $notice["ean"];
			} elseif ($clef == "A") {
				$notice["entete"][$rubrique] = $this->getAuteurs(false, true);
			} elseif ($clef == "L") {
				$notice["entete"][$rubrique] = $this->getLangues();
			} else {
				$notice["entete"][$rubrique] = $this->getChampNotice($clef, $notice["facettes"]);
			}
		}
		return $notice;
	}

// ----------------------------------------------------------------
// Renvoie la clef chapeau et le no de partie
// ----------------------------------------------------------------
	public function getDataSerie($id_notice)
	{
		$data = fetchEnreg("select clef_chapeau,tome_alpha from notices where id_notice=$id_notice");
		return $data;
	}

// ----------------------------------------------------------------
// Renvoie les articles d'un périodique
// ----------------------------------------------------------------
	public function getArticlesPeriodique($id_notice) {
		// lire dans la base
		$notice = fetchEnreg("select clef_chapeau,tome_alpha from notices where id_notice=$id_notice");
		$data = fetchAll("select unimarc from notices_articles where clef_chapeau='" . $notice["clef_chapeau"] . "' and clef_numero='" . $notice["tome_alpha"] . "'");
		if (!$data)
			return false;
		foreach ($data as $enreg) {
			if (!$enreg["unimarc"])
				continue;
			$this->getNoticeUnimarc()->setNotice($enreg["unimarc"], 0);
			$article["titre"] = $this->getTitrePrincipal();
			$complement = $this->getComplementTitre();
			if ($complement)
				$article["titre"].=" : " . $complement;
			$auteurs = $this->getAuteurs(true);
			$article["auteur"] = $auteurs[0];
			$article["pagination"] = $this->getCollation();
			$note = $data = $this->get_subfield("300", "a");
			$article["note"] = trim($note[0]);
			$article["resume"] = $this->getResume();
			$article["matieres"] = $this->getMatieres();
			$ret[] = $article;
		}
		return $ret;
	}

// ----------------------------------------------------------------
// Rend un champ notice détaillé avec rebond et libellé
// ----------------------------------------------------------------
	public function getChampNotice($champ, $facettes = '') {
		$ret = [];
		// Si facettte
		if (strPos("ADPMG", $champ) !== false) {
			$items = array_filter(explode(' ', trim($facettes)));
			foreach ($items as $item) {
				$type = $item[0];
				if ($type != $champ)
					continue;
				$id = substr($item, 1);
				$libelle = Class_Codification::getLibelleFacette($item);
				$url = BASE_URL . "/recherche/rebond?facette=reset&amp;code_rebond=" . $item;
				$ret[] = compact("id", "libelle", "url");
			}
		}
		// Champ texte
		else
		{
			switch ($champ) {
				case "T": $ret = $this->getZonesTitre(); break;
				case "E": $ret = $this->getEditeur(); break;
				case "F": $ret = $this->getCentreInteret(); break;
				case "C": $ret = $this->getCollection(true); break;
				case "O": $ret = $this->getNotes(); break;
				case "K": $ret = $this->getCollation(); break;
				case "R": $ret = $this->getResume(); break;
				case "8": $ret = $this->get856a(); break;
			}
		}
		return $ret;
	}

// ----------------------------------------------------------------
// Notices similaires
// ----------------------------------------------------------------
	public function getNoticesSimilaires($id_notice)
	{
		$ix = new Class_Indexation();

		// Preferences
		$champs = "titres,auteurs,collection,matieres,dewey";
		$nb_max = 10;

		// Lire les champs les plus significatifs
		$notice = fetchEnreg("select " . $champs . " from notices where id_notice=$id_notice");
		if (!$notice) return array();

		// Decoupage des mots index
		$recherche = '';
		foreach ($notice as $champ)
		{
			$mots = explode(" ", trim($champ));
			foreach ($mots as $mot)
			{
				$mot = $ix->getExpressionRecherche($mot);
				if ($mot) $recherche.=" " . $mot;
			}
		}
		// Lancer la requete
		$req = "select id_notice from notices where MATCH(titres,auteurs,editeur,collection,matieres,dewey) AGAINST('" . $recherche . "') and id_notice !=$id_notice Limit 0," . $nb_max;
		$items = fetchAll($req);

		// Completer les donnees
		$notices = array();
		for ($i = 0; $i < count($items); $i++)
		{
			if (!$notice = $this->getNotice($items[$i]["id_notice"], "TAN")) continue;
			$items[$i]["id_service"] = $notice["id_service"];
			$items[$i]["titre_principal"] = $notice["T"];
			$items[$i]["auteur_principal"] = $notice["A"];
			$items[$i]["annee"] = $notice["N"];
			$items[$i]["type_doc"] = $notice["type_doc"];
			$items[$i]["clef_alpha"] = $notice["clef_alpha"];
			$notices[] = $items[$i];
		}
		return $notices;
	}

// ----------------------------------------------------------------
// Tags
// ----------------------------------------------------------------
	public function getTags($id_notice = null)	{
		if (!$id_notice)
			$id_notice = $this->getId();

		$ix = new Class_Indexation();

		// Preferences
		$champs = "titres,auteurs,collection,matieres,dewey";
		$champs_tags = "DPMZ";
		$nb_max = 50;
		$limite_notices = 15;

		// Lire les champs les plus significatifs
		$notice = fetchEnreg("select " . $champs . " from notices where id_notice=$id_notice");
		if (!$notice) return false;

		// Decoupage des mots index
		$recherche = '';
		foreach ($notice as $champ) {
			$mots = explode(" ", trim($champ));
			foreach ($mots as $mot) {
				$mot = $ix->getExpressionRecherche($mot);
				if ($mot) 
					$recherche .= ' ' . $mot;
			}
		}

		// Lancer la requete
		$req = "select facettes from notices where MATCH(titres,auteurs,editeur,collection,matieres,dewey) AGAINST('" . $recherche . "') and id_notice !=$id_notice Limit 0," . $limite_notices;
		$items = fetchAll($req);

		// Récup des tags uniques et comptage des occurences
		if (!count($items)) return array();
		foreach ($items as $item)
		{
			$facettes = explode(" ", $item["facettes"]);
			if (!count($facettes)) continue;
			foreach ($facettes as $facette)
			{
				if (!trim($facette)) continue;
				if (strPos($champs_tags, substr($facette, 0, 1)) === false) continue;
				$temp[$facette] = fetchOne("select count(*) from notices where MATCH(facettes) AGAINST(' +" . $facette . " 'IN BOOLEAN MODE)");
			}
		}

		// Trier,Limiter le nombre et completer les infos
		if (!$temp) return array();
		arsort($temp);
		$nb = 0;
		foreach ($temp as $facette => $nombre)
		{
			$nb++;
			if ($nb > $nb_max) break;
			$tag["id"] = $facette;
			$tag["libelle"] = Class_Codification::getLibelleFacette($facette);
			$tag["nombre"] = $nombre;
			$tags[] = $tag;
		}
		return $tags;
	}

// ----------------------------------------------------------------
// Champ 856$b (identifiants cms sito et rss)
// ----------------------------------------------------------------
	public function getChamp856b()
	{
		$data = $this->get_subfield(856, "b");
		return $data[0];
	}


	public function getTitrePrincipal($type_doc = false, $tome = false) {
		if (!isset($this->_titre_principal)) {
			// 200$a
			$titre = '';
			if ($titres = $this->get_subfield('200', 'a'))
				$titre = trim($titres[0]);

			// Périodique on cherche le chapeau et le n°
			if ($data = $this->get_subfield("461", "t")) {
				$chapeau = trim($data[0]);
				if ($chapeau) {
					if ($titre == $chapeau) $titre = "";
					if ($tome) $chapeau .= " n° " . $tome;
					if ($titre) $titre = $chapeau . BR . $titre;
					else $titre = $chapeau;
				}
			}

			$titre = $this->filtreTitre($titre);
			$this->_titre_principal = $titre;
		}
		return $this->_titre_principal;
	}

	public function setTitrePrincipal($titre)
	{
		$this->_titre_principal = $titre;
		return $this;
	}

// ----------------------------------------------------------------
// Complément du titre (1er seulement)
// ----------------------------------------------------------------
	public function getComplementTitre()
	{
		$titre = $this->get_subfield("200", "e");
		$titre = $this->filtreTitre($titre[0]);
		return trim($titre);
	}


	/**
	 * @return array
	 */
	protected function getZonesTitre() {
		// Recup des zones titres dans les variables
		$zones = fetchOne("Select valeur from variables where clef='unimarc_zone_titre'");
		$zones = explode(';', trim($zones));
		return $this->_getTitresDansZones($zones);
	}


	/**
	 * @param $zones array of string ex:200$a
	 * @return array
	 */
	protected function _getTitresDansZones($zones) {
		if (!is_array($zones) || empty($zones))
			return [];

		$titres = [];
		foreach ($zones as $elem) {
			$zone = strLeft($elem, 3);
			$champ = strRight($elem, 1);
			$data = $this->get_subfield($zone);
			foreach ($data as $items) {
				$sous_champs = $this->decoupe_bloc_champ($items);
				foreach ($sous_champs as $item) {
					if ($item['code'] == $champ) {
						$item = $this->filtreTitre($item['valeur']);
						if ($item) $titres[] = $item;
					}
				}
			}
		}
		return $titres;
	}


	private function filtreTitre($titre) {
		$titre = str_replace(BR, "#BR#", $titre);
		$titre = str_replace("[", "", $titre);
		$titre = str_replace("]", " ", $titre);
		$titre = str_replace("<", "", $titre);
		$titre = str_replace(">", " ", $titre);
		$titre = str_replace("  ", " ", $titre);
		$titre = str_replace(chr(136), "", $titre);
		$titre = str_replace(chr(137), "", $titre);

		if (substr($titre, 0, 1) == '?') {
			$deb = substr($titre, 0, 6);
			$titre = str_replace('?', '', $deb) . substr($titre, 6);
		}
		
		// filtrage caractères terminaux
		$filtre = ["/", ";", ",", ":"];
		foreach($filtre as $car) {
			if (substr($titre,-1,1) == $car)
				$titre = substr($titre, 0, strlen($titre)-1);
		}
		
		$titre = str_replace("#BR#",BR, $titre);
		return $titre;
	}

	
	public function getAuteurPrincipal() {
		if (!isset($this->_auteur_principal)) {
			$this->_auteur_principal = '';
			if (count($auteurs = $this->getAuteurs(true))) {
				$this->_auteur_principal = $auteurs[0];
			} else {
				if ($data = $this->get_subfield("200", "f"))
					$this->_auteur_principal = $data[0];
			}
		}

		return $this->_auteur_principal;
	}


	public function setAuteurPrincipal($auteur) {
		$this->_auteur_principal = $auteur;
		return $this;
	}


	public function getUnimarcZone($zone) {
		return $this->get_subfield($zone);
	}


	public function getAuteurs($auteurPrincipal = false, $getFonction = false) {
		$indexation = new Class_Indexation();
		$auteurs = [];
		$zones = ['700', '710', '720', '730', '701', '702', '711', '712', '721', '722'];

		foreach ($zones as $zone) {
			if ($auteurPrincipal
				  && $auteur = $this->_getPremierAuteurDansZone($zone, $indexation))
				return [$auteur];

			$auteurs = array_merge($auteurs, $this->_getAuteursDansZone($zone, $indexation, $getFonction));
		}

		// Si fonctions on constitue une nouvelle matrice détaillée
		if ($getFonction and 0 < count($auteurs))
			return $this->_getAuteursAvecFonctions($auteurs);
		
		return $auteurs;
	}


	/**
	 * @param $zone string
	 * @param $indexation Class_Indexation
	 * @return string
	 */
	protected function _getPremierAuteurDansZone($zone, $indexation) {
		$data = $this->get_subfield($zone);
		foreach ($data as $items) {
			$auteur = $this->_getAuteurDansSousChamps($this->decoupe_bloc_champ($items));
			$libelle = $auteur->nom . '|' . $auteur->prenom;
			
			if ($this->_isAuteurDansZoneValide($libelle, $indexation)) {
				return trim($auteur->prenom . ' ' . $auteur->nom);
			}
		}
		return '';
	}


	/**
	 * @param $zone string
	 * @param $indexation Class_Indexation
	 * @param $avec_fonction boolean
	 * @return array
	 */
	protected function _getAuteursDansZone($zone, $indexation, $avec_fonction = false) {
		$auteurs = [];
		$data = $this->get_subfield($zone);
		foreach ($data as $items) {
			$libelle = $this->_getAuteurDansZone($items, $avec_fonction);
			
			if ($this->_isAuteurDansZoneValide($libelle, $indexation)) {
				$auteurs[] = $libelle;
			}
		}

		return $auteurs;
	}


	/**
	 * @param $data array
	 * @param $indexation Class_Indexation
	 * @param $avec_fonction boolean
	 * @return string
	 */
	protected function _getAuteurDansZone($data, $avec_fonction = false) {
		$auteur = $this->_getAuteurDansSousChamps($this->decoupe_bloc_champ($data));
		$libelle = $auteur->nom . '|' . $auteur->prenom;
		if ($avec_fonction)
			$libelle .= '|' . $auteur->fonction . '|' . $auteur->fonction_pergame;
	 
		return $libelle;
	}


	/**
	 * @param $auteur string
	 * @param $indexation Class_Indexation
	 * @return boolean
	 */
	protected function _isAuteurDansZoneValide($auteur, $indexation) {
		return (strlen($auteur) > 2 or $indexation->isMotInclu($auteur))
				and striPos($auteur, 'ANONYME') === false;
	}


	/**
	 * @param $data array
	 * @return stdClass
	 */
	protected function _getAuteurDansSousChamps($data) {
		$auteur = new stdClass();
		$auteur->nom = $auteur->prenom = $auteur->fonction = $auteur->fonction_pergame = '';
		foreach ($data as $item) {
			if ($item['code'] == 'a')
				$auteur->nom = trim($item['valeur']);
			elseif ($item['code'] == 'b')
				$auteur->prenom = trim($item['valeur']);
			elseif ($item['code'] == '4')
				$auteur->fonction = trim($item['valeur']);
			elseif ($item['code'] == 'g')
				$auteur->fonction_pergame = trim($item['valeur']);
		}
		return $auteur;
	}


	/**
	 * @param $auteur array
	 * @return array
	 */
	protected function _getAuteursAvecFonctions($auteurs) {
		if (!is_array($auteurs) || empty($auteurs))
			return [];
		
		$ix = new Class_Indexation();
		$result = [];
		foreach ($auteurs as $auteur) {
			$avec_fonction = $this->_getAuteurAvecFonction($auteur, $ix);
			if (!empty($avec_fonction))
				$result[] = $avec_fonction;
		}
 
		return $result;
	}


	/**
	 * @param $auteur string formatted
	 * @see getAuteurs
	 */
	protected function _getAuteurAvecFonction($auteur, $indexation) {
		$item = explode('|', $auteur);
		$nom = $item[0];
		$prenom = $item[1];
		$fonction = $item[2];
		$fonction_pergame = $item[3];
		$code_alpha = $indexation->alphaMaj($nom . '|' . $prenom);
		$code_alpha = str_replace(' ', 'x', $code_alpha);
		if (!$code_alpha)
			return [];

		if (!$codif_auteur = Class_CodifAuteur::findFirstBy(['where' => "MATCH(formes) AGAINST('" . $code_alpha . "' IN BOOLEAN MODE)"]))
			return [];

		if ($fonction_pergame)
			$fonction = $fonction_pergame;
		else {
			if ($auteur_fonction = Class_CodifAuteurFonction::find($fonction))
				$fonction = $auteur_fonction->getLibelle();
		}
		
		return [
			'id' => $codif_auteur->getId(),
			'libelle' => trim($prenom . ' ' . $nom) . (($fonction) ? ' <font color="#666666">(' . $fonction . ')</font>': ''),
			'url' => BASE_URL . "/recherche/rebond?facette=reset&amp;code_rebond=A" . $codif_auteur->getId()
		];
	}


// ----------------------------------------------------------------
// EDITEUR
// ----------------------------------------------------------------
	public function getEditeur() 
	{
		if ($this->_editeur)
			return $this->_editeur;

		if (!$data = $this->get_subfield("210", "c"))
			return '';
		return trim($data[0]);
	}


	public function getEditeurAvecVille() {
		$editeur = $this->getEditeur();
		if ($data = $this->get_subfield('210', 'a'))
			$editeur .= ' (' . trim($data[0]) . ')';
		return $editeur;
	}


	/**
	 * @category testing
	 */
	public function setEditeur($editeur) {
		$this->_editeur = $editeur;
		return $this;
	}

// ----------------------------------------------------------------
// Collections
// ----------------------------------------------------------------
	public function getCollection($principale=false)
	{
		if (!$data = $this->get_subfield(225, "a")) return null;
		if ($principale == true) return $data[0];
		else return $data;
	}

// ----------------------------------------------------------------
// Matieres
// ----------------------------------------------------------------
	public function getMatieres()
	{
		if (isset($this->_matieres))
			return $this->_matieres;
		
		$matiere = array();

		// Recup des zones matières dans les variables
		$zones = fetchOne("select valeur from variables where clef='unimarc_zone_matiere'");
		$zones = explode(";", trim($zones));
		foreach ($zones as $elem)
		{
			$data = $this->get_subfield(strLeft($elem, 3));
			$champs = strMid($elem, 3, 10);
			foreach ($data as $items)
			{
				$sous_champs = $this->decoupe_bloc_champ($items);
				$mot = "";
				foreach ($sous_champs as $item)
				{
					if (strpos($champs, $item["code"]) !== false)
					{
						if ($mot) $mot.=" : ";
						$mot.=$item["valeur"];
					}
				}
				$matiere[] = trim($mot);
			}
		}
		return($matiere);
	}


	/**
	 * @param $matieres Array
	 */
	public function setMatieres($matieres) {
		$this->_matieres = $matieres;
		return $this;
	}


// ----------------------------------------------------------------
// CENTRE D'INTERET PERGAME
// ----------------------------------------------------------------
	public function getCentreInteret() {
		$interet = array();
		$data = $this->get_subfield("932");
		foreach ($data as $items)	{
			$sous_champs = $this->decoupe_bloc_champ($items);
			foreach ($sous_champs as $item)	{
				if ($item["code"] == "a")	{
					if (trim($item["valeur"])) $interet[] = $item["valeur"];
				}
			}
		}
		return $interet;
	}

// ----------------------------------------------------------------
// Langues
// ----------------------------------------------------------------
	public function getLangues()	{
		return implode(', ', $this->getLanguesList());
	}


	public function getLanguesList()	{
		$codes = $this->getLangueCodes();
		if (0 == count($codes))
			return array();
		
		$langues = array();
		foreach ($codes as $code) {
			if ($langue = Class_CodifLangue::getLoader()->find($code))
				$langues[] = ($langue->getLibelle()) ? $langue->getLibelle() : $code;
		}
		return $langues;
	}


	public function getLangueCodes() {
		if ($this->_langueCodes) 
			return $this->_langueCodes;
			
		$langues = array();
		$data = $this->get_subfield(101);
		foreach ($data as $items) {
			$sous_champs = $this->decoupe_bloc_champ($items);
			foreach ($sous_champs as $item) {
				if ('a' != $item['code'])
					continue;
				$code = substr(strtolower($item['valeur']), 0, 3);
				if ('und' == $code)
					continue;
				if ('fra' == $code) 
					$code = 'fre';
				$langues[] = $code;
			}
		}
		return $langues;
	}


	/**
	 * @category testing
	 */
	public function setLangueCodes($codes) {
		$this->_langueCodes = $codes;
		return $this;
	}

// ----------------------------------------------------------------
// Notes bibliographiques
// ----------------------------------------------------------------
	public function getNotes()
	{
		$zones = array('200b', '300a', '303a', '304a', '305a', '306a', '307a', '308a', '310a', '312a', '313a', '314a', '316a', '317a', '320a', '321a', '323a', '334abcd', '337a', '345a');
		$notes = array();

		foreach ($zones as $elem)
		{
			$data = $this->get_subfield(substr($elem, 0, 3));
			$champs = substr($elem, 3);
			foreach ($data as $items)
			{
				$sous_champs = $this->decoupe_bloc_champ($items);
				$mot = '';
				foreach ($sous_champs as $item)
				{
					if (strpos($champs, $item['code']) !== false)
					{
						if ($mot) $mot.=', ';
						$mot.=$item['valeur'];
					}
				}
				if ($mot) 
					$notes[] = trim($mot);
			}
		}
		return($notes);
	}


	public function getCollation() {
		if (!isset($this->_collation))	{
			$collation = '';
			$data = $this->get_subfield('215');
			foreach ($data as $items)	{
					$sous_champs = $this->decoupe_bloc_champ($items);
					foreach ($sous_champs as $item)	{
							if ($collation) $collation .= " ; ";
							$collation.=$item["valeur"];
						}
			}
			$this->_collation = $collation;
		}
		return $this->_collation;
	}


	/**
	 * @return array
	 */
	public function getCollations() {
		$collations = [];
		$data = $this->get_subfield('215');
		foreach ($data as $items)	{
			$sous_champs = $this->decoupe_bloc_champ($items);
			foreach ($sous_champs as $item)	{
				$collations[] = $item['valeur'];
			}
		}

		return $collations;
	}
	

// ----------------------------------------------------------------
// Résumé
// ----------------------------------------------------------------
	public function getResume()	{
		if (isset($this->_resume))	
			return $this->_resume;

		if ($album = $this->getAlbum())
			return $album->getDescription();

		$resume = '';
		$data = $this->get_subfield("330", "a");
		foreach ($data as $item)
			if (strlen($item) > strlen($resume)) $resume = trim($item);

		if ($resume && substr($resume, -1) != ".") 
			$resume.=".";
		
		return $this->_resume = $resume;
	}


	public function setResume($resume) {
		$this->_resume = $resume;
		return $this;
	}

// ----------------------------------------------------------------
// Champ 856$a et 856$u (liens internet)
// ----------------------------------------------------------------
	public function get856a()	{
		$lien = array();
		$data = $this->get_subfield(856, "a");
		$data1 = $this->get_subfield(856, "u");
		$result = array_merge($data, $data1);

		if (isset($result[0]))	{
			// black list
			$trav = fetchOne("select valeur from variables where clef='black_list_856'");
			if (trim($trav)) $black_list = explode(";", $trav);

			// controle url pour target
			$target = fetchOne("select valeur from variables where clef='url_site'");
			$trav = explode("/", $target);
			$ctrl_target = array_pop($trav);
			if (!trim($ctrl_target)) $ctrl_target = array_pop($trav);

			// tableau des liens
			foreach ($result as $item)
			{
				$controle = true;
				if ($black_list)
				{
					foreach ($black_list as $mot)
					{
						if (stripos($item, $mot) !== false)
						{ $controle = false; break; }
					}
				}
				if ($controle == true)
				{
					if (substr(strtoupper($item), 0, 4) != "HTTP") $item = "http://" . $item;
					if (strpos($item, $ctrl_target) === false) $target = 'target="_blank"'; else $target="";
					$lien[] = '<a href="' . trim($item) . '" ' . $target . ">" . trim($item) . '</a>';
				}
			}
		}
		return($lien);
	}



	public function acceptVisitor($visitor) {
		$indexation = new Class_Indexation();
		$visitor->visitClefAlpha($this->getClefAlpha());
		$visitor->visitTitres($this->_getTitresDansZones(['200$a', '200$e']));
		$visitor->visitAuteurs($this->_getAuteursDansZone('700', $indexation));
		$visitor->visitContributeurs($this->_getAuteursDansZone('702', $indexation));
		foreach ($this->getMatieres() as $matiere)
			$visitor->visitMatiere($matiere);
		$visitor->visitFormats($this->getCollations());
		$visitor->visitResume($this->getResume());
		$visitor->visitDateMaj($this->getDateMaj());
		$visitor->visitAnnee($this->getAnnee());
		$visitor->visitEditeur($this->getEditeurAvecVille());
		$visitor->visitLangues($this->getLangueCodes());
		$visitor->visitTypeDoc($this->getTypeDoc());
		if ($this->hasVignette())
			$visitor->visitVignette($this->getUrlVignette());
		$visitor->visitIsbn($this->getIsbn());
		$visitor->visitEan($this->getEan());
		$visitor->visitAlbum($this->getAlbum());
		$visitor->visitSource(array_merge($this->get_subfield('801', 'b'),
				                              $this->get_subfield('852', 'k')));
	}




	public function findAllResumes() {
		$avis = array();

		if($resume = $this->getResume())
			$avis[] = array('source' => 'Bibliothèque',
											'texte' => $resume);

		$providers = array('Class_WebService_Fnac',
											 'Class_WebService_Babelio',
											 'Class_WebService_Premiere');
												 
		foreach ($providers as $provider_class) {
			$provider = new $provider_class();
			$avis = array_merge($avis, $provider->getResumes($this));
		}
		
		return $avis;
	}


	public function getLinksAsSource() {
		return Class_FRBR_Link::getLinksForSource($this->getClefAlpha());
	}


	public function getLinksAsTarget() {
		return Class_FRBR_Link::getLinksForTarget($this->getClefAlpha());
	}


	/**
	 * return int identifiant type doc codifié Pergame
	 */
	public function getTypeDocPergame() {
		return Class_TypeDoc::find($this->getTypeDoc())->toPergame();
	}


	/**
	 * @param $noticeUnimarc Class_NoticeUnimarc
	 * @category testing
	 * @return Class_Notice
	 */
	public function setNoticeUnimarc($noticeUnimarc) {
		$this->_notice_unimarc = $noticeUnimarc;
		return $this;
	}


	/**
	 * @return Class_NoticeUnimarc
	 */
  public function getNoticeUnimarc() {
		if (null == $this->_notice_unimarc)
			$this->_notice_unimarc = new Class_NoticeUnimarc();
		return $this->_notice_unimarc;
	}
}

?>