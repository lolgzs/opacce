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
class Admin_IndexController extends Zend_Controller_Action {
	public function indexAction()	{
		$this->view->titre = 'Accueil';

		// Activation - désactivation du site
		if (null !== ($setSiteOk = $this->_getParam('setsiteok'))) {
			if ('false' == $setSiteOk) Class_AdminVar::set('SITE_OK', '0');
			if ('true' == $setSiteOk) Class_AdminVar::set('SITE_OK', '1');
		}

		// Statut du site
		if (1 == Class_AdminVar::get('SITE_OK')) {
			$this->view->etat_site="Le site est en ligne";
			$this->view->lien_site="Rendre le site indisponible";
			$this->view->href_site="false";

		} else {
			$this->view->etat_site="Le site est indisponible";
			$this->view->lien_site="Remettre le site en ligne";
			$this->view->href_site="true";
		}

		$status_babelio = Class_WebService_Babelio::getActivationStatus();
		$this->view->status_babelio = $status_babelio['enabled'] ? 'Activé' : 'Désactivé';
		if (null !== $expiration_date = $status_babelio['expire_at'])
			$this->view->status_babelio .= ', expiration le '.$expiration_date->toString("d MMMM yyyy");
		$this->view->show_babelio_info = !$status_babelio['enabled'] || $expiration_date !== null;
	}

	public function adminvarAction()	{
		$existing_variables = Class_AdminVar::getLoader()->findAll();
		$existing_clefs = array();
		foreach ($existing_variables as $var)
			$existing_clefs[] = $var->getId();

		// creer les variables manquantes
		foreach (Class_AdminVar::getKnownVars() as $name) {
			if (!in_array($name, $existing_clefs)) {
				$existing_variables[] = Class_AdminVar::set($name, '');
			}
		}

		$this->view->titre = 'Gestion des variables';
		$this->view->vars = $existing_variables;

	}



	public function shouldEncodeVar($cle) {
		return in_array($cle->getId(), 
										array("REGISTER_OK", "RESA_CONDITION","TEXTE_MAIL_RESA",
													"USER_VALIDATED", "USER_NON_VALIDATED"));
	}


	public function adminvareditAction() {
		$id = $this->_getParam('cle');
		$cle = Class_AdminVar::getLoader()->find($id);

		if ($this->_request->isPost())	{
			$filter = new Zend_Filter_StripTags();
			$new_valeur = $this->_request->getPost('valeur');

			if ($this->shouldEncodeVar($cle)) {
				$cle->setValeur(urlencode($new_valeur));

			} else if ($cle->getId() == 'GOOGLE_ANALYTICS') {
				$cle->setValeur(addslashes($new_valeur));

			} else {
				$cle->setValeur(trim($filter->filter($new_valeur)));
			}

			$cle->save();
			$this->_redirect('admin/index/adminvar');
		}

		if ($this->shouldEncodeVar($cle))
			$this->view->var_valeur	= urldecode($cle->getValeur());
		else
			$this->view->var_valeur	= $cle->getValeur();

		$this->view->var_cle		= $cle->getId();
		$this->view->tuto				= $this->_getAdminVarHelpFor($cle->getId());
		$this->view->titre			= 'Modifier la variable: '.$cle->getId();
	}


	public function changelocaleAction() {
		$locale= $this->_getParam('locale', 0);
		if ($locale){
			$session = Zend_Registry::get('session');
			$session->locale = $locale;
		}

		$this->_redirect('admin');
	}

	public function clearcacheAction() {
		Zend_Registry::get('cache')->clean(Zend_Cache::CLEANING_MODE_ALL);
		$this->_redirect('admin/index/adminvar');
	}

	/**
	 * @param string $name
	 * @return string
	 */
	private function _getAdminVarHelpFor($name) {
		$help = array(
			'AVIS_MAX_SAISIE'           => 'Nombre de caractères maximum autorisé à saisir dans les avis.',
			'AVIS_MIN_SAISIE'           => 'Nombre de caractères minimum autorisé à saisir dans les avis.',
			'BLOG_MAX_NB_CARAC'         => "Nombre de caractères maximum à afficher dans le bloc critiques.",
			'PCDM4_LIB'									=> "Libellé affichage pour la PCDM4",
			'DEWEY_LIB'									=> "Libellé affichage pour la Dewey",
			'NB_AFFICH_AVIS_PAR_AUTEUR' => "Nombre d'avis maximum à afficher par utilisateur.",
			'CLEF_GOOGLE_MAP'           => 'Clef d\'activation pour le plan d\'accès google map. <a target="_blank" href="http://code.google.com/apis/maps/signup.html">Obtenir la clé google map</a>',
			'MODO_AVIS'                 => 'Modération des avis des lecteurs.<br /> 0 = Validation a priori<br /> 1 = Validation a posteriori.',
			'MODO_AVIS_BIBLIO'          => 'Modération des avis des bibliothèquaires.<br /> 0 = Validation a priori<br /> 1 = Validation a posteriori.',
			'AVIS_BIB_SEULEMENT'        => '0 = Les lecteurs peuvent donner leur avis. <br /> 1 = Seuls les bibliothèquaires peuvent donner leur avis',
			'MODO_BLOG'                 => '0 = Ne requiert pas d\'identification pour saisir des  commentaires. <br /> 1 = Requiert l\'identification pour saisir des commentaires.',
			'REGISTER_OK'               => 'Texte visible par l\'internaute après son inscription.',
			'RESA_CONDITION'            => 'Texte visible après l\'envoi d\'e-mail de demande de réservation.',
			'SITE_OK'                   => '0 = Site en maintenance. <br /> 1 = Site ouvert.',
			'ID_BIBLIOSURF'             => 'Nom de la bibliothèque chez bibliosurf (en minuscules)',
			'GOOGLE_ANALYTICS'          => 'Code Google Analytics',
			'ID_READ_SPEAKER'           => 'Numéro de client Read Speaker <a target="_blank" href="http://webreader.readspeaker.com">http://webreader.readspeaker.com</a>',
			'BLUGA_API_KEY'             => 'Clé API Bluga Webthumb <a target="_blank" href="http://webthumb.bluga.net/home">http://webthumb.bluga.net/home</a>',
			'AIDE_FICHE_ABONNE'         => "Texte d'aide affiché dans la fiche abonné",
			'INTERDIRE_ENREG_UTIL'      => "Ne pas autoriser l'enregistrement d'utilisateurs depuis la boite de connexion",
			'LANGUES'                   => "Liste des codes langue utilisées en plus du français séparées par des ;. Exemple: en;ro;es",
			'WORKFLOW'									=> 'Activer ou désactiver la gestion des validations des articles<br />1 = Activé, Autre valeur = désactivé',
			'BIBNUM'									  => 'Activer ou désactiver la bibliothèque numérique<br />1 = Activé, Autre valeur = désactivé',
			'FORMATIONS'							  => 'Activer ou désactiver le module formation<br />1 = Activé, Autre valeur = désactivé',
			'CACHE_ACTIF'               => implode(
																			'<br/>',
																			array(
																				'Activer le cache des boîtes (meilleure performance mais mise à jour toutes les ' . ((int)Zend_Registry::get('cache')->getOption('lifetime')) / 60 . 'mn)',
																				'0 = inactif',
																				'1 = actif',
																				sprintf(
																					'<a href="%s" >Vider le cache</a>',
																					$this->view->url(array('action' => 'clearcache'))
																				)
																			)
																	 ),
			'VODECLIC_KEY'              => 'Clé de sécurité Vodeclic',
			'VODECLIC_ID'               => 'Identifiant partenaire Vodeclic',
			'OAI_SERVER'                => 'Activation du serveur OAI'
		);

		if (!array_key_exists($name, $help)) {
			return '';
		}

		return $help[$name];
	}

}