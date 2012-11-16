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
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 : Gestion des utilisateurs
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class UsersLoader extends Storm_Model_Loader {
	public function findAllLike($search, $by_right = 0, $limit = 500) {
		$sql_template = 'select bib_admin_users.* from bib_admin_users ';

		if ($by_right)
			$sql_template .=
				'inner join user_group_memberships on user_group_memberships.user_id = bib_admin_users.id_user '.
				'inner join user_groups on user_group_memberships.user_group_id = user_groups.id  '.
				'where (user_groups.rights_token & %1$d = %1$d) and ';
		else
			$sql_template .= 'where ';

		$sql_template .=
				'(nom like \'%2$s\' or prenom like \'%2$s\' or login like \'%2$s\') '.
				'order by nom, prenom, login limit '.$limit;

		$like = '%'.strtolower($search).'%';

		$all_users = Class_Users::findAll(sprintf($sql_template, $by_right, $like));

		if (!$by_right || count($all_users) >= $limit)
			return $all_users;

		$groups = Class_UserGroup::findAllBy(['where' => sprintf('rights_token & %1$d = %1$d', $by_right),
																					'group_type' => Class_UserGroup::TYPE_DYNAMIC]);

		foreach($groups as $group) {
			$nb_users_to_fetch = $limit - count($all_users);
			if ($nb_users_to_fetch <= 0)
				break;

			$params = ['role_level' => $group->getRoleLevel(), 
								 'limit' => $nb_users_to_fetch,
								 'where' => sprintf('(nom like \'%1$s\' or prenom like \'%1$s\' or login like \'%1$s\')', $like),
								 'order' => ['nom','prenom', 'login']];
			$all_users = array_merge($all_users,
															 Class_Users::findAllBy($params));
		}

		return $all_users;
	}


	/**
	 * @return array
	 */
	public function findAllByRightDirigerFormation() {
		$users = array();

		$all_groups = Class_UserGroup::getLoader()->findAll();
		foreach ($all_groups as $group) {
			if ($group->hasRightDirigerFormation())
				$users = array_merge($users , $group->getUsers());
		}

		return $users;
	}


	/**
	 * @return Class_Users
	 */
	public function getIdentity() {
		if (!$user = ZendAfi_Auth::getInstance()->getIdentity())
			return null;

		return $this->find($user->ID_USER);
	}


	/**
	 * @return bool
	 */
	public function hasIdentity() {
		return null != $this->getIdentity();
	}


	public function isCurrentUserAdmin() {
		if (!$user = $this->getIdentity())
			return false;
		return $user->isAdmin();
	}


	public function isCurrentUserCanAccesBackend() {
		if (!$user = $this->getIdentity())
			return false;
		return $user->canAccessBackend();
	}


	/**
	 * @param Class_Article
	 * @return bool
	 */
	public function isCurrentUserCanEditArticle($article) {
		if (!$user = $this->getIdentity())
			return false;
		return $user->canEditArticle($article);
	}


	/**
	 * @param Class_Article
	 * @return bool
	 */
	public function isCurrentUserCanAccessAllBibs() {
		if (!$user = $this->getIdentity())
			return false;
		return $user->canAccessAllBibs();
	}
}


class Class_Users extends Storm_Model_Abstract {
	protected $_table_name = 'bib_admin_users';
	protected $_table_primary = 'ID_USER';
  protected $_loader_class = 'UsersLoader';
	protected $_has_many = [
		'subscriptions' => ['model' => 'Class_NewsletterSubscription',
												'role' => 'user',
												'dependents' => 'delete'],

		'newsletters' => ['through' => 'subscriptions'],

		'avis' => ['model' => 'Class_AvisNotice',
							 'role' => 'user',
							 'order' => 'date_avis desc'],

		'avis_articles' => ['model' => 'Class_Avis',
												'role' => 'auteur',
												'order' => 'date_avis desc'],

		'paniers' => ['model' => 'Class_PanierNotice',
									'role' => 'user'],

		'session_formation_inscriptions' => ['model' => 'Class_SessionFormationInscription',
																				 'role' => 'stagiaire'],

		'session_formations' => ['through' => 'session_formation_inscriptions'],

		'formations' => ['through' => 'session_formation_inscriptions'],

		'session_formation_interventions' => ['model' => 'Class_SessionFormationIntervention',
																					'role' => 'intervenant'],

		'session_interventions' => ['through' => 'session_formation_interventions'],

		'user_group_memberships' => ['model' => 'Class_UserGroupMembership',
																 'role' => 'user'],

		'user_groups' => ['through' => 'user_group_memberships'],

		'formulaires' => ['model' => 'Class_Formulaire',
											'role' => 'user',
											'order' => 'date_creation desc'],

	];


	protected $_belongs_to = array('bib' => array('model' => 'Class_Bib',
																								'referenced_in' => 'id_site'),
																 'int_bib' => array('model' => 'Class_IntBib',
																										'referenced_in' => 'id_site'),
																 'zone' => array('through' => 'bib'));

	protected $_default_attribute_values = array('id_site' => 0,
																							 'role' => 'invite',
																							 'role_level' => 0,
																							 'idabon' => '',
																							 'date_fin' => '',
																							 'naissance' => '',
																							 'date_debut' => 0,
																							 'telephone' => '',
																							 'mail' => '',
																							 'nom' => '',
																							 'prenom' => '',
																							 'adresse' => '',
																							 'code_postal' => '',
																							 'ville' => '',
																							 'id_sigb' => null
																							 );

	protected $_translate;
	protected $_fiche_sigb;


	public function __construct() {
		$this->_translate = Zend_Registry::get('translate');
	}


	public static function currentUserId() {
		if (!$user = self::getLoader()->getIdentity())
			return 0;

		return $user->getId();
	}


	public static function sortByNom($user1, $user2) {
		return (strtolower($user1->getNom()) > strtolower($user2->getNom()));
	}


	public function isAbonne() {
		return $this->getRoleLevel() == ZendAfi_Acl_AdminControllerRoles::ABONNE_SIGB;
	}


	public function isBibliothequaire() {
		return $this->getRoleLevel() >= ZendAfi_Acl_AdminControllerRoles::MODO_BIB;
	}


	public function getLibelleBib() {
		if (!$bib = $this->getBib())
			return '';
		return $bib->getLibelle();
	}


	/**
	 * @return bool
	 */
	public function isRedacteur() {
		return in_array(
			$this->getRoleLevel(),
			array(
				ZendAfi_Acl_AdminControllerRoles::MODO_BIB,
				ZendAfi_Acl_AdminControllerRoles::MODO_PORTAIL
			)
		);
	}


	/**
	 * @return bool
	 */
	public function isAdmin() {
		return $this->getRoleLevel() >= ZendAfi_Acl_AdminControllerRoles::ADMIN_PORTAIL;
	}


	/**
	 * @return bool
	 */
	public function isSuperAdmin() {
		return $this->getRoleLevel() >= ZendAfi_Acl_AdminControllerRoles::SUPER_ADMIN;
	}


	/**
	 * @return bool
	 */
	public function isAdminBib() {
		return $this->getRoleLevel() == ZendAfi_Acl_AdminControllerRoles::ADMIN_BIB;
	}


	/**
	 * @return bool
	 */
	public function canEditArticle($article) {
		if ($this->getRoleLevel() < ZendAfi_Acl_AdminControllerRoles::MODO_BIB)
			return false;

		if ($this->getRoleLevel() > ZendAfi_Acl_AdminControllerRoles::ADMIN_BIB)
			return true;

		if (!$article->hasCategorie())
			return false;

		return $this->getIdSite() == $article->getCategorie()->getIdSite();
	}


	/**
	 * @return bool
	 */
	public function canAccessAllBibs() {
		return ($this->getRoleLevel() > ZendAfi_Acl_AdminControllerRoles::ADMIN_BIB);
	}


	/**
	 * @return bool
	 */
	public function canAccessBackend() {
		return $this->getRoleLevel() >= ZendAfi_Acl_AdminControllerRoles::MODO_BIB;
	}


	/**
	 * @return bool
	 */
	public function isAbonnementValid() {
		if (! $this->isAbonne()) return false;
		if (! $this->hasDateFin()) return true;
		return ($this->getDateFin() >= date("Y-m-d"));
	}

	
	/**
	 * Retourne la Date au format YYYY/MM/DD
	 * @return string
	 */
	public function getDateNaissanceIso8601(){
		return str_replace("-", "/", $this->getNaissance());
	}

	
	/**
	 * @return int
	 */
	public function getAge(){
		if(!$date_naissance=$this->getDateNaissanceIso8601())
			return null;
		
		$arr1 = explode('/', $date_naissance);
    $arr2 = explode('/', $this->getToday());
		
    if(($arr1[1] < $arr2[1]) || (($arr1[1] == $arr2[1]) && ($arr1[2] <= $arr2[2])))
			return $arr2[0] - $arr1[0];

    return $arr2[0] - $arr1[0] - 1;
	}
	
	
	/**
	 * @return string
	 */
	public function getToday(){
		if ($today=$this->today)
			return $today;
		return date('Y/m/d');
	}
	
	
	/**
	 * @return array
	 */
	public function getGroupes(){
		$groupes=array();
		if ($this->getAge()>= 18)
			$groupes[]='adulte';
		else if($this->hasNaissance()) 
			$groupes[]='mineur';
		
		if ($this->hasIdabon()) 
			$groupes[]='abonne';
		$groupes[]=$this->getLibelleRole();
		return $groupes;
	}


	/**
	 * @return array
	 */
	public function getUserGroupsLabels() {
		$labels = $this->getGroupes();
		foreach ($this->getUserGroups() as $group)
			$labels[] = $group->getLibelle();
		return $labels;
	}


	/**
	 * @return array
	 */
	public function getUserGroups() {
		return array_merge(parent::_get('user_groups'), 
											 Class_UserGroup::findAllBy(['role_level' => $this->getRoleLevel(),
																									 'group_type' => Class_UserGroup::TYPE_DYNAMIC]));
	}

	
	/**
	 * @return string
	 */
	public function getLibelleRole(){
		return ZendAfi_Acl_AdminControllerRoles::getNomRole($this->getRoleLevel());
	}
	
	/**
	 * @return array
	 */
	public function getLastAvis() {
		return Class_AvisNotice::getLoader()->findAllBy(array(
																													'id_user' => $this->getId(),
																													'order' => 'date_avis desc',
																													'limit' =>  10));
	}


	/**
	 * @return int
	 */
	public function getRole() {
		return ZendAfi_Acl_AdminControllerRoles::getNomRole($this->getRoleLevel());
	}


	/**
	 * @return array
	 */
	public function getTitresNewsletters() {
		$newsletters = $this->getNewsletters();
		$titres = array();

		foreach($newsletters as $nl)
			$titres []= $nl->getTitre();

		return $titres;
	}



	/**
	 * @return array
	 */
	public function getRights() {
		$rights = array();
		$groups = $this->getUserGroups();
		foreach ($groups as $group) 
			$rights = array_merge($rights, $group->getRights());
		return array_values(array_unique($rights));
	}


	/**
	 * @return bool
	 */
	public function hasRightSuivreFormation() {
		return in_array(Class_UserGroup::RIGHT_SUIVRE_FORMATION, $this->getRights());
	}


	/**
	 * @return bool
	 */
	public function hasRightDirigerFormation() {
		return in_array(Class_UserGroup::RIGHT_DIRIGER_FORMATION, $this->getRights());
	}


	public function getFirstAvisByIdNotice($id_notice) {
		$notice = Class_Notice::getLoader()->find($id_notice);
		$avis = $notice->getAvisByUser($this);
		if (count($avis) > 0)
			return $avis[0];
		return null;
	}

	//------------------------------------------------------------------------------------------------------
	// Enreg user
	//------------------------------------------------------------------------------------------------------
	public function getUser($id_user)
	{
		if (!$id_user) return null;
		$enreg=fetchEnreg("select * from bib_admin_users where ID_USER=$id_user");
		return $enreg;
	}


	//------------------------------------------------------------------------------------------------------
	// Liste de users pour 1 zone ou une bib
	//------------------------------------------------------------------------------------------------------
	public function getUsers($id_zone,$id_site,$role_level,$recherche,$page)
	{
		if($id_site and $id_site !="ALL")
		{
			if($id_site=="PORTAIL") $id_site=0;
			$cond[]="id_site=$id_site";
		}
		elseif($id_zone and $id_zone !="ALL")
		{
			if($id_zone=="PORTAIL") $cond[]="ID_SITE=0";
			else
			{
				$bibs=fetchAll("select ID_SITE from bib_c_site where ID_ZONE=$id_zone");
				if(!$bibs) return false;
				foreach($bibs as $bib)
				{
					if($inSql) $inSql.=",";
					$inSql.=$bib["ID_SITE"];
				}
				$cond[]="ID_SITE in($inSql)";
			}
		}
		
		$recherche = array_merge(array('role' => '', 'login' => '', 'nom' => ''),
														 $recherche);

		if($recherche["role"]>"") $cond[]="ROLE_LEVEL=".$recherche["role"];
		else $cond[]="ROLE_LEVEL<=$role_level";
		if($recherche["login"]) $cond[]="LOGIN like '".addslashes($recherche["login"])."%'";
		if($recherche["nom"]) $cond[]="NOM like '".addslashes($recherche["nom"])."%'";
		$where=getWhereSql($cond);
		$req_comptage="select count(*) from bib_admin_users ".$where;
		$req_liste="select * from bib_admin_users ".$where." order by LOGIN ";

		// Retour
		$ret["nb_par_page"]=30;
		$ret["nombre"]=fetchOne($req_comptage);
		$ret["users"]=fetchAll($req_liste.getLimitSql($ret["nb_par_page"],$page));
		return $ret;
	}



	//------------------------------------------------------------------------------------------------------
	// Ecriture user
	//------------------------------------------------------------------------------------------------------
	public function updatePseudo($data_user, $pseudo) {
		$user = $this->getLoader()->find($data_user->ID_USER);
		if ($user == null) return false;

		return $user
			->setPseudo($pseudo)
			->save();
	}

	//------------------------------------------------------------------------------------------------------
	// Vérification pour eviter les doublons de login
	//------------------------------------------------------------------------------------------------------
	public function ifLoginExist($login) {
		$login = (trim($login));

		$login = $this->getLoader()->findFirstBy(array('login' => $login));
		return ($login != null);
	}


	/* Hook appelé sur save */
	public function validate() {
		$this->check($this->getLogin(), $this->_translate->_("Vous devez compléter le champ 'Identifiant'"));
		$this->check(strlen_utf8($this->getLogin()) <= 50, $this->_translate->_("Le champ 'Identifiant' doit être inférieur à 50 caractères"));

		if ($this->isNew()) {
			$this->check($this->ifLoginExist($this->getLogin()) == false,
									 $this->_translate->_("L'identifiant que vous avez choisi existe déjà."));
		}

		$this->check($this->getPassword(), $this->_translate->_("Vous devez compléter le champ 'Mot de passe'"));
		$this->check(strlen_utf8($this->getPassword()) <= 50, $this->_translate->_("Le champ 'Mot de passe' doit être inférieur à 50 caractères"));

		if ($this->getRoleLevel() > 1 and $this->getRoleLevel() < 5 and $this->getIdSite() == 0) {
			$cls_role= new ZendAfi_Acl_AdminControllerRoles();
			$this->addError($this->_translate->_("La bibliothèque est obligatoire pour le rôle : %s",
																					 $cls_role->getLibelleRole($this->getRoleLevel())));
		}

		if ($this->getRole()=="abonne_sigb" and !$this->getIdabon())
			$this->addError($this->_translate->_("Le numéro de carte est obligatoire pour les abonnés identifiés dans un sigb."));

		$this->check($this->hasRightSuivreFormation() or (count($this->getSessionFormations()) === 0),
								 $this->_translate->_('Vous n\'avez pas les droits suffisants pour suivre une formation'));

		$this->check($this->hasRightDirigerFormation() or (count($this->getSessionInterventions()) === 0),
								 $this->_translate->_('Vous n\'avez pas les droits suffisants pour diriger une formation'));
	}


	//------------------------------------------------------------------------------------------------------
	// Supprime un utilisateur
	//------------------------------------------------------------------------------------------------------
	public function deleteUser($id_user)
	{
		sqlExecute("delete from bib_admin_users where ID_USER=$id_user");
	}

	//------------------------------------------------------------------------------------------------------
	// Vérification d'adresse e-mail
	//------------------------------------------------------------------------------------------------------
	function verifMail($adresse) {
		$syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,5}$#';
		if (!preg_match($syntaxe,$adresse))
			return false;

		$ctrl1 = fetchOne("select count(*) from bib_admin_users_non_valid Where MAIL='$adresse'");
		$ctrl2 = fetchOne("select count(*) from bib_admin_users Where MAIL='$adresse'");
		return ($ctrl1 + $ctrl2 === 0);
	}

	//------------------------------------------------------------------------------------------------------
	// Enregistrement d'une demande d'inscription
	//------------------------------------------------------------------------------------------------------
	public function registerUser($data)
	{
		// Test champ valid
		extract($data);
		$test_login = $this->ifLoginExist($login);
		$test_mail = $this->verifMail($mail);

		$errors = array();
		if($test_login == 1)
			$errors []= $this->_translate->_("Cet identifiant existe déjà.");
		elseif(trim($login) =="")
			$errors []= $this->_translate->_("Vous n'avez pas saisi de login.");

		if($mdp != $mdp2)
			$errors []= $this->_translate->_("Vous n'avez pas saisi les mêmes mots de passe.");
		elseif(trim($mdp) =="")
			$errors []= $this->_translate->_("Le mot de passe est obligatoire.");

		if($test_mail == 0)
			$errors []= $this->_translate->_("L'adresse e-mail est invalide ou est déjà utilisée.");
		elseif(trim($test_mail) =="")
			$errors []= $this->_translate->_("Vous n'avez pas saisi d'E-mail.");

		if(trim($captcha) =="")
			$errors []= $this->_translate->_("Vous n'avez pas saisi le code anti-spam.");
		elseif($_SESSION['captcha_code'] != $captcha)
			$errors []= $this->_translate->_("Le code anti-spam est invalide.");

		$error = implode('<br/>', $errors);

		// Tout est ok : on ecrit
		if (!$error)
		{
			$enreg = array(
					'ID_USER' => '',
					'LOGIN' => $login,
					'PASSWORD' => $mdp,
					'MAIL' => $mail,
					'CLE' => $cle
				);
			sqlInsert("bib_admin_users_non_valid",$enreg);

			$profil = Class_Profil::getCurrentProfil();

			// Corps du mail
			$message_mail =$this->_translate->_("Bonjour,").BR;
			$message_mail.=$this->_translate->_("Vous avez fait une demande d'inscription sur le portail.").BR;
			$message_mail.=$this->_translate->_("Pour activer votre compte, merci de cliquer sur le lien suivant :").BR;
			$message_mail.=$this->_translate->_("Url d'activation : %s",
																					sprintf('<a href="http://'.$_SERVER["SERVER_NAME"].BASE_URL.'/opac/auth/activeuser?c='.$cle.'">%s</a>',
																									$this->_translate->_('Valider mon inscription'))).BR.BR;
			$message_mail.=$this->_translate->_("Si vous n'êtes pas à l'origine de cette demande d'inscription, merci de ne pas tenir compte de cet e-mail, et l'inscription ne sera pas activée.").BR;
			$message_mail.=$profil->getLibelle().BR.sprintf("<a href=http://".$_SERVER["SERVER_NAME"].BASE_URL.">%s</a>",
																											$this->_translate->_('Aller sur le portail'));

			// envoi du mail de confirmation
			$cls_mail=new Class_Mail();
			$erreur=$cls_mail->sendMail($profil->getLibelle(), $message_mail, $mail, "");
			if($erreur) 
				$ret["message_mail"]='<p class="error">'.$erreur.'</p>';
			else {
				$message_mail = getVar('REGISTER_OK') ? getVar('REGISTER_OK') : $this->_translate->_('Un mail viens de vous être envoyé pour confirmer votre inscription');
				$ret["message_mail"]=urldecode(str_replace('%0D%0A',BR,$message_mail));
			}
		}

		// Affichage des erreurs
		else $ret["error"]=$error;
		return $ret;
	}

	//------------------------------------------------------------------------------------------------------
	// Activation d'une demande d'inscription
	//------------------------------------------------------------------------------------------------------
	public function activerRegistration($cle)
	{
		if(!$cle) return false;
		$enreg=fetchEnreg("select LOGIN,PASSWORD,MAIL from bib_admin_users_non_valid where CLE='$cle'");
		if(!$enreg) return false;
		sqlExecute("delete from bib_admin_users_non_valid where CLE='$cle'");
		sqlInsert("bib_admin_users",$enreg);
	}

	//------------------------------------------------------------------------------------------------------
	// Liste des inscriptions en attente
	//------------------------------------------------------------------------------------------------------
	public function getUsersNonValid()
	{
		$users = fetchAll("Select * from bib_admin_users_non_valid order by DATE DESC");
		return($users);
	}

	//------------------------------------------------------------------------------------------------------
	// Mot de passe oublié
	//------------------------------------------------------------------------------------------------------
	function lostpass($user) {
		if(!trim($user)) 
			return array('error' => 1);

		$enreg=fetchEnreg("Select * from bib_admin_users where LOGIN='$user'");
		if (!$enreg) $enreg=fetchEnreg("Select * from bib_admin_users_non_valid where LOGIN='$user'");
		if (!$enreg["LOGIN"]) 
			return array('error' => 2);

		if (!$enreg["MAIL"]) 
			return array('error' => 4);
	
		// envoi du mail
		$message_mail = sprintf("%s\n\n",
														$this->_translate->_('Vous avez fait une demande de mot de passe sur le portail.'));
		$message_mail .= $this->_translate->_("Votre identifiant : %s\n", $enreg["LOGIN"]);
		$message_mail .= $this->_translate->_("Votre mot de passe : %s\nn", $enreg["PASSWORD"]);
		$message_mail .= sprintf("%s\n\n", $this->_translate->_('Bonne navigation sur le portail'));
		$mail = new Class_Mail();
		$erreur = $mail->sendMail(Class_Profil::getCurrentProfil()->getTitreSite(), $message_mail, $enreg["MAIL"]);

		if($erreur) 
			return array('message_mail' => '<p class="error">'.$erreur.'</p>');
			
		return array('message_mail' => $this->_translate->_("Un mail vient de vous être envoyé avec vos paramètres de connexion."));
	}

	//------------------------------------------------------------------------------------------------------
	// Nom abonné
	//------------------------------------------------------------------------------------------------------
	public function getNomAff($id_user = null, $complet = false) {
		if ($id_user != null) {
			if (null === $user = $this->getLoader()->find($id_user))
				return '';
		}	else {
			$user = $this;
		}
		 
		$pseudo = ($complet) ? 
			trim($user->PRENOM." ".$user->NOM):
			$user->PSEUDO;

		if ($pseudo) 
			return $pseudo;

		if ($user->PRENOM) 
			return $user->PRENOM;


		return $user->LOGIN;
	}


	/**
	 * @return string
	 */
	public function getNomComplet() {
		return $this->getNomAff(null, true);
	}


	/**
	 * Return the list of Class_WebService_SIGB_Emprunt for this user
	 * @return array
	 */
	public function getEmprunts() {
		return array_at('fiche', $this->getFicheSIGB())->getEmprunts();
	}


	/**
	 * Liste des Class_WebService_SIGB_Emprunt en retard
	 * @return array
	 */
	public function getEmpruntsRetard() {
		$emprunts = $this->getEmprunts();
		$retards = array();
		foreach ($emprunts as $emprunt) {
			if ($emprunt->enRetard())
				$retards[]=$emprunt;
		}
		return $retards;
	}


	/**
	 * @return int
	 */
	public function getNbEmprunts() {
		return count($this->getEmprunts());
	}


	/**
	 * @return int
	 */
	public function getNbEmpruntsRetard() {
		return count($this->getEmpruntsRetard());
	}


	/**
	 * Return the list of Class_WebService_SIGB_Reservation for this user
	 * @return array
	 */
	public function getReservations() {
		return array_at('fiche', $this->getFicheSIGB())->getReservations();
	}


	/**
	 * @return int
	 */
	public function getNbReservations() {
		return count($this->getReservations());
	}


	//------------------------------------------------------------------------------------------------------
	// Fiche abonné sigb
	//------------------------------------------------------------------------------------------------------
	public function getFicheSigb($user = null) {
		if ($user === null)
			$user = $this; // compatibilité

		if (isset($this->_fiche_sigb))
			return $this->_fiche_sigb;

		if (!$user->getIdabon())
			$ret = ["message" => $this->_translate->_("Vous devez vous connecter en tant qu'abonné de la bibliothèque pour obtenir plus d'informations.")];
		else
			$ret = (new Class_CommSigb())->ficheAbonne($user);

		if (!isset($ret['fiche']))
			$ret['fiche'] = Class_WebService_SIGB_Emprunteur::nullInstance();

		$ret["nom_aff"] = $this->getNomAff($user->ID_USER, true);

		$this->_fiche_sigb = $ret;
		return $ret;
	}


	public function getSIGBComm() {
		if (null == $int_bib = $this->getIntBib())
			return null;
		return $int_bib->getSIGBComm();
	}


	public function updateSIGBOnSave() {
		$this->getFicheSigb();
		return $this;
	}


	public function setFicheSIGB($fiche) {
		$this->_fiche_sigb = $fiche;
		return $this;
	}


	public function getFutureMultimediaHolds() {
		return Class_Multimedia_DeviceHold::getLoader()->getFutureHoldsOfUser($this);
	}


	public function setNom($nom) {
		if (null === $nom)
			$nom = '';
		return parent::_set('nom', $nom);
	}


	public function setPrenom($prenom) {
		if (null === $prenom)
			$prenom = '';
		return parent::_set('prenom', $prenom);
	}


	/**
	 * Hook AbstractModel::save
	 * Sauvegarde des données compte lecteur sur le SIGB
	 */
	public function afterSave() {
		if (!isset($this->_fiche_sigb)) return;

		$fiche = $this->_fiche_sigb;
		if ($fiche['type_comm'] != Class_IntBib::COM_OPSYS)
			return;

		$emprunteur = $fiche['fiche'];
		$emprunteur
			->setNom($this->getNom())
			->setPrenom($this->getPrenom())
			->setEMail($this->getMail())
			->setPassword($this->getPassword())
			->save();
	}


	/**
	 * @param $day string YYYY-MM-DD formatted
	 * @return string const error type @see Class_Multimedia_DeviceHold
	 */
	public function getMultimediaQuotaErrorForDay($day) {
		// chargements des quotas les plus avantageux pour l'utilisateur selon ses groupes
		$max_day = $max_week = $max_month = 0;
		$quotaNames = array('day', 'week', 'month');
		foreach ($this->getUserGroups() as $group) {
			foreach ($quotaNames as $name) {
				$quotaName = 'max_' . $name;
				$methodName = 'getMax' . ucfirst($name);
				$value = (int)$group->$methodName();
				if (${$quotaName} < $value)
					${$quotaName} = $value;
			}
		}

		// si un seul des quotas est resté à 0, on n'a aucun droit
		if (in_array(0, array($max_day, $max_week, $max_month)))
			return Class_Multimedia_DeviceHold::QUOTA_NONE;

		if ($max_day <= $this->getMultimediaHoldDurationForDay($day))
			return Class_Multimedia_DeviceHold::QUOTA_DAY;

		if ($max_week <= $this->getMultimediaHoldDurationForWeekOfDay($day))
			return Class_Multimedia_DeviceHold::QUOTA_WEEK;

		if ($max_month <= $this->getMultimediaHoldDurationForMonthOfDay($day))
			return Class_Multimedia_DeviceHold::QUOTA_MONTH;
	}


	/**
	 * @param $day string
	 * @return int minutes
	 */
	public function getMultimediaHoldDurationForDay($day) {
		$start = strtotime($day);
		$end = strtotime('+1 day', $start);

		return (int) Class_Multimedia_DeviceHold::getLoader()
				->getDurationForUserBetweenTimes($this, $start, $end);
	}


	/**
	 * @param $day string
	 * @return int minutes
	 */
	public function getMultimediaHoldDurationForWeekOfDay($day) {
		$start = strtotime('previous monday', strtotime($day));
		$end = strtotime('next monday', $start);

		return (int) Class_Multimedia_DeviceHold::getLoader()
				->getDurationForUserBetweenTimes($this, $start, $end);
	}


	/**
	 * @param $day string
	 * @return int minutes
	 */
	public function getMultimediaHoldDurationForMonthOfDay($day) {
		$start = strtotime('first day of this month', strtotime($day));
		$end = strtotime('first day of next month', $start);

		return (int) Class_Multimedia_DeviceHold::getLoader()
				->getDurationForUserBetweenTimes($this, $start, $end);
	}



	/**
	 * return Class_Users
	 */
	public function beAbonneSIGB() {
		return $this->changeRoleTo(ZendAfi_Acl_AdminControllerRoles::ABONNE_SIGB);
	}


	/**
	 * return Class_Users
	 */
	public function beInvite() {
		return $this->changeRoleTo(ZendAfi_Acl_AdminControllerRoles::INVITE);
	}


	/**
	 * return Class_Users
	 */
	public function beAdminPortail() {
		return $this->changeRoleTo(ZendAfi_Acl_AdminControllerRoles::ADMIN_PORTAIL);
	}


	/**
	 * return Class_Users
	 */
	public function changeRoleTo($role) {
		return $this
			->setRoleLevel($role)
			->setRole(ZendAfi_Acl_AdminControllerRoles::getNomRole($role));
	}


	
	/**
	 * return StdClass
	 */
	public function toStdClass() {
		$result = new StdClass();

		$fields = $this->toArray();
		foreach($fields as $field => $value) {
			$prop_name = strtoupper($field);
			$result->$prop_name = $value;
		}
		return $result;
	}
}