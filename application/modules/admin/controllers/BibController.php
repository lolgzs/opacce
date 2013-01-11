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
//////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Controleur fiche bibliotheque 
//////////////////////////////////////////////////////////////////////////////////////////
class Admin_BibController extends Zend_Controller_Action
{
	private $_pathImg = "./public/admin/images/";
	private $id_zone;

	//------------------------------------------------------------------------------------------------------
	// Initialisation du controller
	//------------------------------------------------------------------------------------------------------
	function init()	{
		// Zone et bib du filtre (initialisé dans le plugin DefineUrls)
		$this->id_zone=$_SESSION["admin"]["filtre_localisation"]["id_zone"];
		$this->id_bib=$_SESSION["admin"]["filtre_localisation"]["id_bib"];
		
		// Variables de vue
		$this->view->id_zone=$this->id_zone;
		$this->view->id_bib=$this->id_bib;
	}

	//------------------------------------------------------------------------------------------------------
	// Liste des bibs
	//------------------------------------------------------------------------------------------------------
	function indexAction()
	{
		$this->view->titre = $this->view->_('Gestion des bibliothèques');
		// Retour accueil ou liste en fonction du role
		$user = ZendAfi_Auth::getInstance()->getIdentity();

		if ($user->ROLE_LEVEL < ZendAfi_Acl_AdminControllerRoles::ADMIN_BIB) 
			 $this->_redirect('admin/index');

		if ($user->ROLE_LEVEL == ZendAfi_Acl_AdminControllerRoles::ADMIN_BIB)
			$this->_redirect(sprintf('admin/bib/edit/id/%d', $user->ID_SITE));

		$this->view->bib_array = Class_Bib::getLoader()->findAllByIdZone($this->id_zone);
	}

	//------------------------------------------------------------------------------------------------------
	// Creation bib
	//------------------------------------------------------------------------------------------------------
	function addAction()
	{
		$this->view->titre = $this->view->_("Ajouter une bibliothèque");
 
		if ($this->_request->isPost()) 
			{
				$filter = new Zend_Filter_StripTags();
 
				$libelle = trim($filter->filter($this->_request->getPost('libelle')));
				$responsable = trim($filter->filter($this->_request->getPost('responsable')));
				$adresse = trim($filter->filter($this->_request->getPost('adresse')));
				$cp = trim($filter->filter($this->_request->getPost('cp')));
				$ville = trim($filter->filter($this->_request->getPost('ville')));
				$tel = trim($filter->filter($this->_request->getPost('tel')));
				$mail = trim($filter->filter($this->_request->getPost('mail')));
				$zone = (int)trim($filter->filter($this->_request->getPost('zone')));
				$carto = trim($filter->filter($this->_request->getPost('carto')));
				$inscription = trim($filter->filter($this->_request->getPost('inscription')));
				$pret = trim($filter->filter($this->_request->getPost('pret')));
				$fond = trim($filter->filter($this->_request->getPost('fond')));
				$procur = trim($filter->filter($this->_request->getPost('procur')));
				$annexe = trim($filter->filter($this->_request->getPost('annexe')));
				$statut = trim($filter->filter($this->_request->getPost('statut')));
				$url = trim($filter->filter($this->_request->getPost('url')));
				$horaire = trim($filter->filter($this->_request->getPost('horaire')));
				$photo = trim($filter->filter($this->_request->getPost('photo')));
 
				$data = array(
											'ID_SITE' => '',
											'LIBELLE' => $libelle,
											'RESPONSABLE' => $responsable,
											'ADRESSE' => $adresse,
											'CP' => $cp,
											'VILLE' => $ville,
											'TELEPHONE' => $tel,
											'MAIL' => $mail,
											'ID_ZONE' => $zone,
											'LIEN_CARTO' => $carto,
											'VISIBILITE' => $statut,
											'URL_WEB' => $url,
											'INSCRIPTION' => $inscription,
											'PRET' => $pret,
											'FOND' => $fond,
											'PROCURE' => $procur,
											'ANNEXE' => $annexe,
											'PHOTO' => $photo,
											'HORAIRE' => $horaire,
											'GOOGLE_MAP' => '',
											'INTERDIRE_RESA' => $this->_request->getPost('interdire_resa')
											);
 
				$bibClass = new Class_Bib();
				$errorMessage = $bibClass->addBib($data);
				if ($errorMessage == '') 
					{
						// Modif adresse photo
						if($photo)
							{
								$id_bib=fetchOne("select max(ID_SITE) from BIB_C_SITE");
								$img_temp=getcwd()."/userfiles/photobib/photoBib0.jpg";
								$img="/userfiles/photobib/photoBib".$id_bib.".jpg";
								$req="update BIB_C_SITE set PHOTO='$img' where ID_SITE=$id_bib";
								sqlExecute($req);
								rename($img_temp,getcwd().$img);
							}
						$this->_redirect('admin/bib?z='.$this->id_zone);
					}
				else
					{
						$this->view->message = $errorMessage;
						$this->view->ID_ZONE = $this->id_zone;
						$this->view->bib = new stdClass();
						$this->view->bib->LIBELLE = $libelle;
						$this->view->bib->RESPONSABLE = $responsable;
						$this->view->bib->ADRESSE = $adresse;
						$this->view->bib->CP = $cp;
						$this->view->bib->VILLE = $ville;
						$this->view->bib->TELEPHONE = $tel;
						$this->view->bib->MAIL = $mail;
						$this->view->bib->LIEN_CARTO = $carto;
						$this->view->bib->INSCRIPTION = $inscription;
						$this->view->bib->PRET = $pret;
						$this->view->bib->FOND = $fond;
						$this->view->bib->PROCURE = $procur;
						$this->view->bib->ANNEXE = $annexe;
						$this->view->bib->HORAIRE = $horaire;
						$this->view->bib->PHOTO = $photo;
						$this->view->bib->URL_WEB = $url;
						$this->view->bib->ID_SITE = null;
					}
			}
		else
			{
				$this->view->ID_ZONE = $this->id_zone;
				// supprime la photo temporaire
				$adresse_img=getcwd()."/userfiles/photobib/photoBib0.jpg";
				if(file_exists($adresse_img)) unlink($adresse_img);
			}
		// combo des zones
		$cls_zone = new Class_Zone();
		$this->view->combo_zone=$cls_zone->getComboZone($zone);

		$this->view->action = 'add';
	}
 
	//------------------------------------------------------------------------------------------------------
	// Modif bib
	//------------------------------------------------------------------------------------------------------
	function editAction() {
		$user = ZendAfi_Auth::getInstance()->getIdentity();

		if ($user->ROLE_LEVEL < ZendAfi_Acl_AdminControllerRoles::ADMIN_BIB) 
			$this->_redirect('admin/index');

		if ($this->_request->isPost()) 
			$id = (int)$this->_request->getPost('id_bib');
		else
			$id = (int)$this->_request->getParam('id', 0);				


		if ($user->ROLE_LEVEL == ZendAfi_Acl_AdminControllerRoles::ADMIN_BIB 
				and $user->ID_SITE != $id) {
			$this->_redirect('admin/index');
		}


		$bibClass = new Class_Bib();
		// Si c envoyé
		if ($this->_request->isPost()) {
			$filter = new Zend_Filter_StripTags();
			
			$libelle = trim($filter->filter($this->_request->getPost('libelle')));
			$responsable = trim($filter->filter($this->_request->getPost('responsable')));
			$adresse = trim($filter->filter($this->_request->getPost('adresse')));
			$cp = trim($filter->filter($this->_request->getPost('cp')));
			$ville = trim($filter->filter($this->_request->getPost('ville')));
			$tel = trim($filter->filter($this->_request->getPost('tel')));
			$mail = trim($filter->filter($this->_request->getPost('mail')));
			$zone = (int)trim($filter->filter($this->_request->getPost('zone')));
			$carto = trim($filter->filter($this->_request->getPost('carto')));
			$inscription = trim(urlencode($this->_request->getPost('inscription')));
			$pret = trim(urlencode($this->_request->getPost('pret')));
			$fond = trim(urlencode($this->_request->getPost('fond')));
			$procur = trim($filter->filter($this->_request->getPost('procur')));
			$annexe = trim(urlencode($this->_request->getPost('annexe')));
			$statut = trim($filter->filter($this->_request->getPost('statut')));
			$url = trim($filter->filter($this->_request->getPost('url')));
			$horaire = trim(urlencode($this->_request->getPost('horaire')));			
			$photo = trim($filter->filter($this->_request->getPost('photo')));
			
			if ($id !== false ) {
				$data = array(
											'ID_SITE' => $id,
											'LIBELLE' => $libelle,
											'RESPONSABLE' => $responsable,
											'ADRESSE' => $adresse,
											'CP' => $cp,
											'VILLE' => $ville,
											'TELEPHONE' => $tel,
											'MAIL' => $mail,
											'ID_ZONE' => $zone,
											'LIEN_CARTO' => $carto,
											'VISIBILITE' => $statut,
											'URL_WEB' => $url,
											'INSCRIPTION' => $inscription,
											'PRET' => $pret,
											'FOND' => $fond,
											'PROCURE' => $procur,
											'ANNEXE' => $annexe,
											'PHOTO' => $photo,
											'HORAIRE' => $horaire,
											'INTERDIRE_RESA' => $this->_request->getPost('interdire_resa')
											);
				
				$errorMessage = $bibClass->editBib($data, $id);
				
				// Redirection en fonction du role
				$redirect="admin";
				$user = ZendAfi_Auth::getInstance()->getIdentity();
				if ($user->ROLE_LEVEL > ZendAfi_Acl_AdminControllerRoles::MODO_PORTAIL) $redirect.="/bib?z=".$this->id_zone;
				
				if ($errorMessage == '') 
					$this->_redirect($redirect);
				else	{
					$bib = $bibClass->getBibById($id);
					if (is_string($bib) ) $this->_redirect('admin/error/database');
					else	{
						$this->view->bib = $bib;
						$this->view->message = $errorMessage;
					}
				}
			}
		}	else {
			if (!$bib = Class_Bib::getLoader()->find($id)) {
				$this->_redirect('admin/bib/index?z='.$this->id_zone);
				return;
			}
		}
		// combo des zones
		$cls_zone = new Class_Zone();
		$this->view->combo_zone=$cls_zone->getComboZone($bib->ID_ZONE);

		$this->view->titre = $this->view->_("Modifier la bibliothèque: %s", $bib->LIBELLE);
		$this->view->action = 'edit';
		$this->view->bib = $bib;
		$this->view->id = $id;
	}

	//------------------------------------------------------------------------------------------------------
	// Suppression bib
	//------------------------------------------------------------------------------------------------------
	function deleteAction()
	{
		$bibClass = new Class_Bib();
		$id = (int)$this->_request->getParam('id');
		if ($id > 0) 
			{
				$errorMessage = $bibClass->deleteBib($id);
				if ( $errorMessage == '') 
					{
						// supprimer la photo
						$img_temp=getcwd()."/userfiles/photobib/photoBib".$id.".jpg";
						unlink($img_temp);
						$this->_redirect('admin/bib/index');
					}
				else
					{
						$this->view->message = $errorMessage;
						$this->_redirect('admin/error/bib');	
					}
			}
		$this->_redirect('admin/bib?z='.$this->id_zone);
	}
 
	//------------------------------------------------------------------------------------------------------
	// Photo bib (affichage et upload)
	//------------------------------------------------------------------------------------------------------
	function photoAction()
	{
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setNoRender();
		
		// Parametres et adresse du fichier
		$id_bib = (int)$this->_request->getParam('id');
		$img="/userfiles/photobib/photoBib".$id_bib.".jpg";
		$url_img=BASE_URL."/admin/bib/getimage?id=".$id_bib;
		$adresse_img=getcwd().$img;
		if(! file_exists($adresse_img)) $url_img=BASE_URL."/userfiles/photobib/photoVide.jpg"; 

		// Debut html
		$html='<body style="background-color:#f0f0f0;overflow:hidden">';
		$html.='<link rel="stylesheet" type="text/css" media="screen" href="'.URL_ADMIN_CSS.'global.css" />';
		$html.='<div>';
		
		// Traitement de la photo
		if($_FILES["photo"])
			{
				$fic = $_FILES["photo"];
				$html.= $this->view->_("Fichier photo : %s", $fic["name"].BR);
				if($fic["error"] > 0)
					{
						if(!$fic["name"]) $erreur=$this->view->_("Vous devez sélectionner une photo en cliquant sur le bouton : parcourir");
						elseif(!$fic["size"]) $erreur=$this->view->_("Le fichier que vous avez sélectionné est vide.");
						else $erreur=$this->view->_('Erreur au téléchargement du fichier : L\'image que vous essayez de télécharger est trop volumineuse ou inaccessible.');
					}
				else
					{
						// Controles
						$ext=strToLower(strRight($fic["type"],3));
						$taille=(int)($fic["size"]/1024);
						if($ext !="jpg" and $ext != "peg") $erreur=$this->view->_("La photo que vous avez sélectionnée doit être de type : 'image/jpg' et pas de type : %s", $fic["type"]);
						elseif($taille > 100 ) $erreur=$this->view->_("La photo que vous avez sélectionnée est trop volumiseuse : %d ko", $taille);
						else
							{
								if(move_uploaded_file($fic['tmp_name'],$adresse_img))
									{ 
										$html.="<script>document.location.replace('".BASE_URL."/admin/bib/photo?id=".$id_bib."&upload=1')</script>"; 
									}
								else $erreur=$this->view->_("Erreur au transfert du fichier vers userfiles");
							}
					}
				if($erreur)
					{
						$html.=BR.'<span>'.$erreur.'</span>';
						$html.=BR.BR.'<center><input type="button" class="bouton" value="'.$this->view->_("Retour").'" onclick="document.location.replace(\''.BASE_URL.'/admin/bib/photo?id='.$id_bib.'\')">';
					}
			}
		else
			{
				if($_REQUEST["upload"]==1)
					{
						$html.='<script>window.top.document.getElementById("photo").value="'.$img.'";</script>';
					}
				$html.='<form name="form" action="'.BASE_URL.'/admin/bib/photo?id='.$id_bib.'" enctype="multipart/form-data" method="post">';
				$html.='<table><tr>';
				$html.='<td width="190px"><img src="'.$url_img.'" width="180px" height="140px"></td>';
				$html.='<td style=padding-left:10px"><span style="font-size:12px">'.$this->view->_('NB : l\'image doit être de type ".jpg", avoir une taille inférieure à 100 ko et des dimensions se rapprochant de 180 / 140 pixels').'</span><br/><br/>'; 
				$html.='<center><input type="file" name="photo" size="40" enctype="multipart/form-data">';
				$html.='<br/><br/><input type="submit" class="bouton" value="'.$this->view->_('Envoyer la photo sur le serveur').'"></td>';
				$html.='</tr></table></form>';
			}
		
		$html.='</div>';
		$html.='<script>document.body.setAttribute("bgColor ","#f0f0f0"))</script>';
		$this->getResponse()->setHeader('Content-Type', 'text/html;charset=utf-8');
		$this->getResponse()->setHeader('pragma', 'no-cache');
		$this->getResponse()->setBody($html);
	}
	
	//------------------------------------------------------------------------------------------------------
	// Photo bib (envoi contenu image en flux)
	//------------------------------------------------------------------------------------------------------
	function getimageAction()
	{
		$id_bib = (int)$this->_request->getParam('id');
		$img="/userfiles/photobib/photoBib".$id_bib.".jpg";
		$adresse_img=getcwd().$img;
		if(! file_exists($adresse_img)) $adresse_img=getcwd()."/userfiles/photobib/photoVide.jpg";
		
		$handle=fopen($adresse_img,"rb");
		$data=fread ($handle, filesize($adresse_img));
		fclose($handle);
		
		header("Content-type: image/jpg");
		header("pragma: no-cache");
		print($data);
		exit;
	}

	//------------------------------------------------------------------------------------------------------
	// Localisations	: liste
	//------------------------------------------------------------------------------------------------------
	function localisationsAction()
	{
		$cls_loc = new Class_Localisation();
		$id_bib = (int)$this->_request->getParam('id_bib');
		$this->view->id_bib=$id_bib;
		$this->view->nom_bib=fetchOne("select LIBELLE from bib_c_site where ID_SITE=$id_bib");
		$this->view->localisations=$cls_loc->getLocalisations($id_bib);
		$this->view->titre = $this->view->_("Localisations de la bibliothèque: %s", $this->view->nom_bib);
	}

	//------------------------------------------------------------------------------------------------------
	// Localisations	: creation / modif 
	//------------------------------------------------------------------------------------------------------
	function localisationsmajAction()
	{
		$cls_loc = new Class_Localisation();
		$id_localisation=(int)$this->_request->getParam('id_localisation');
		$id_bib = (int)$this->_request->getParam('id_bib');

		// Validation
		if ($this->_request->isPost())
			{
				$data=ZendAfi_Filters_Post::filterStatic($this->_request->getPost());
				if(!$data["LIBELLE"])$erreurs[]=$this->view->_("le libellé est obligatoire.");
				if($erreurs)
					{
						$this->view->erreurs=$erreurs;
						$id_localisation=0;
						$enreg=$data;
					}
				// Ecriture
				else
					{
						$data["ID_BIB"]=$id_bib;
						if(!$data["ANIMATION"]) $data["ANIMATION"]="etoile.gif";
						Class_AdminVar::set("animation",$data["ANIMATION"]);
						$cls_loc->ecrireLocalisation($id_localisation,$data);
						$this->_redirect('admin/bib/localisations?id_bib='.$id_bib);
					}
			}

		// Entree dans le formulaire
		else
			{
				if($this->_request->getParam('creation')==1)
					{
						$id_localisation=0;
						$enreg["LIBELLE"]=$this->view->_("** nouvelle localisation **");
						$enreg["ANIMATION"]=getVar("animation");
					}
				else
					{
						$id_localisation = (int)$this->_request->getParam('id_localisation');
						$enreg=$cls_loc->getLocalisations($id_bib,$id_localisation);
					}
			}
		
		// Plans
		$table_plans[0]="aucun";
		$plans=$cls_loc->getPlans($id_bib);
		if($plans)
			{
				foreach($plans as $plan) $table_plans[$plan["ID_PLAN"]]=$plan["LIBELLE"];
			}

		// Variables de vue
		if(!$enreg["ANIMATION"]) $enreg["ANIMATION"]="etoile.gif";
		$this->view->plans=$table_plans;
		$this->view->id_bib=$id_bib;
		$this->view->nom_bib=fetchOne("select LIBELLE from bib_c_site where ID_SITE=$id_bib");
		$this->view->id_localisation=$id_localisation;
		$this->view->localisation=$enreg;
		$this->view->titre = $this->view->_("Mise à jour de la localisation");
	}

	//------------------------------------------------------------------------------------------------------
	// Localisations	: suppression
	//------------------------------------------------------------------------------------------------------
	function localisationsdeleteAction()
	{
		$cls_loc = new Class_Localisation();
		$id_localisation=(int)$this->_request->getParam('id_localisation');
		$id_bib = (int)$this->_request->getParam('id_bib');
		$cls_loc->deleteLocalisation($id_localisation);
		$this->_redirect('admin/bib/localisations?id_bib='.$id_bib);
	}

	//------------------------------------------------------------------------------------------------------
	// Plans	: rend la balise image du plan (appel ajax)
	//------------------------------------------------------------------------------------------------------
	function ajaximageplanAction()
	{
		$cls_loc = new Class_Localisation();
		$id_plan=$this->_request->getParam('id_plan');
		$image=$cls_loc->getImagePlan($id_plan);
		if(!$image) $html='ERREUR';
		else
			{
				$html='<a id="ref_plan" href="'.$image["url"].'" rel="lightbox" title="">';
				$html.='<img id="img_plan" src="'.$image["url"].'">';
				$html.='</a>';
			}

		// Renvoyer la reponse
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setNoRender();
		print($html);
		exit;
	}
	
	//------------------------------------------------------------------------------------------------------
	// Plans	: liste
	//------------------------------------------------------------------------------------------------------
	function plansAction()
	{
		$cls_loc = new Class_Localisation();
		$id_bib = (int)$this->_request->getParam('id_bib');
		$this->view->id_bib=$id_bib;
		$this->view->nom_bib=fetchOne("select LIBELLE from bib_c_site where ID_SITE=$id_bib");
		$this->view->plans=$cls_loc->getPlans($id_bib);
		$this->view->titre = $this->view->_('Plans de la bibliothèque: %s', $this->view->nom_bib);
	}

	//------------------------------------------------------------------------------------------------------
	// Plans	: creation / modif 
	//------------------------------------------------------------------------------------------------------
	function plansmajAction()
	{
		$cls_loc = new Class_Localisation();
		$id_plan=(int)$this->_request->getParam('id_plan');
		$id_bib = (int)$this->_request->getParam('id_bib');

		// Validation
		if ($this->_request->isPost())
			{
				$data=ZendAfi_Filters_Post::filterStatic($this->_request->getPost());
				if(!$data["LIBELLE"])$erreurs[]=$this->view->_("le libellé est obligatoire.");
				if(!$data["IMAGE"]) $erreurs[]=$this->view->_("L'image du plan est obligatoire.");
				if($erreurs)
					{
						$this->view->erreurs=$erreurs;
						$id_plan=0;
						$enreg=$data;
					}
				// Ecriture
				else
					{
						$data["ID_BIB"]=$id_bib;
						$cls_loc->ecrirePlan($id_plan,$data);
						$this->_redirect('admin/bib/plans?id_bib='.$id_bib);
					}
			}

		// Entree dans le formulaire
		else	{
			$add_action = ($this->_request->getParam('creation')==1);
			if($add_action)	{
				$id_plan=0;
				$enreg["LIBELLE"]=$this->view->_("** nouveau plan **");
			}
			else	{
				$id_plan = (int)$this->_request->getParam('id_plan');
				$enreg=$cls_loc->getPlans($id_bib,$id_plan);
			}
		}

		$nom_bib=fetchOne("select LIBELLE from bib_c_site where ID_SITE=$id_bib");
		if ($add_action) 
			$this->view->titre = $this->view->_('Ajouter un plan de la bibliothèque: %s', $nom_bib);
		else
			$this->view->titre = $this->view->_('Modifier un plan de la bibliothèque: %s', $nom_bib);
		
		// Variables de vue
		$this->view->id_bib=$id_bib;
		$this->view->id_plan=$id_plan;
		$this->view->plan=$enreg;
	}
 
	//------------------------------------------------------------------------------------------------------
	// Plans	: suppression
	//------------------------------------------------------------------------------------------------------
	function plansdeleteAction()
	{
		$cls_loc = new Class_Localisation();
		$id_plan=(int)$this->_request->getParam('id_plan');
		$id_bib = (int)$this->_request->getParam('id_bib');
		$cls_loc->deletePlan($id_plan);
		$this->_redirect('admin/bib/plans?id_bib='.$id_bib);
	}

	//------------------------------------------------------------------------------------------------------
	// Plan d'acces - Création
	//------------------------------------------------------------------------------------------------------
	function planaccesAction()
	{
		$this->view->titre = $this->view->_("Constitution du plan d'accès");
		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setLayoutScript('sansMenuGauche.phtml');

		$id_bib = (int)$this->_request->getParam('id_bib');
		if($id_bib <= 0)	$this->_redirect('admin/bib');
		$this->_session->ID_SITE = $id_bib;
		$cle_plan_acces = getVar('CLEF_GOOGLE_MAP');

		// On teste si la cle est valide
		if(trim($cle_plan_acces) == "") $this->_redirect('admin/bib/planaccesscleffailed');

		// Lecture des parametres
		$class_bib = new Class_Bib();
		$bib = $class_bib->getBibById($id_bib);
		$data = ZendAfi_Filters_Serialize::unserialize($bib->GOOGLE_MAP);
		if(!$data) $data = $this->createDefaultPlanAccess($cle_plan_acces);

		// Création du javascript
		$init="";
		$id_couche = 1;
		foreach($data["COUCHE"] as $couche)
			{
				$root="oCouches[".$couche["ID_COUCHE"]."]";
				$init.=$root." = new Object();".NL;
				$init.=$root.".titre='".addslashes($couche["TITRE"])."';".NL;
				$init.=$root.".longitude=".$couche["LONGITUDE"].";".NL;
				$init.=$root.".latitude=".$couche["LATITUDE"].";".NL;
				$init.=$root.".echelle=".$couche["ECHELLE"].";".NL;
				$init.=$root.".points= new Array();".NL;

				$id_point = 1;
				foreach($data["COUCHE"][$id_couche]["POINT"] as $point)
					{
						$root1=$root.".points[".$id_point."]";
						$init.=$root1." = new Object();".NL;
						$init.=$root1.".titre='".addslashes($point["TITRE"])."';".NL;
						$init.=$root1.".longitude=".$point["LONGITUDE"].";".NL;
						$init.=$root1.".latitude=".$point["LATITUDE"].";".NL;
						$init.=$root1.".icone=".$point["ICONE"].";".NL;
						$init.=$root1.".adresse='".addslashes($point["ADRESSE"])."';".NL;
						$init.=$root1.".ville='".addslashes($point["VILLE"])."';".NL;
						$init.=$root1.".pays='".addslashes($point["PAYS"])."';".NL;
						$init.=$root1.".photo='".$point["PHOTO"]."';".NL;
						$init.=$root1.".infos= new Array();".NL;

						$id_info = 1;
						foreach($data["COUCHE"][$id_couche]["POINT"][$id_point]["INFO"] as $info)
							{
								$texte= urldecode($info["TEXTE"]);
								$texte = addslashes($texte);
								$root2=$root1.".infos[".$id_info."]";
								$init.=$root2." = new Object();".NL;
								$init.=$root2.".titre='".addslashes($info["TITRE"])."';".NL;
								$init.=$root2.".texte='".nl2br($texte)."';".NL;
								$id_info++;
							}
						$id_point++;
					}
				$id_couche++;
			}

		require_once("fonctions/file_system.php");

		// setup list of image icons
		$icones=parse_dossier($this->_pathImg."plan_acces");
		$html_icones = '';
		$hIcone = '';
		if($icones)
			foreach($icones as $ico)
				{
					if(strRight($ico[1],4)==".png" and strRight($ico[1],5) !="s.png") {
						$index=str_replace("icon","",$ico[1]);
						$index=str_replace(".png","",$index);
						$hIcone.="hIcone[".$index."]= creer_icone('".URL_ADMIN_IMG."plan_acces/".$ico[1]."');".NL;
						$html_icones.='<option value="'.$index.'">'.$index.'</option>'.NL;
					}
				}

		// Variables de vue
		$this->view->oCouches = $init;
		$this->view->hIcone = $hIcone;
		$this->view->htmlIcones = $html_icones;
		$this->_session->key_google = $cle_plan_acces;
		$this->view->googleKey = $cle_plan_acces;
		$this->view->id_bib =$id_bib;
	}

	//------------------------------------------------------------------------------------------------------
	// Plan d'acces - Sauvegarde
	//------------------------------------------------------------------------------------------------------
	function planaccesssaveAction()
	{
		$filter = new Zend_Filter_StripTags();
		$id_bib = (int)$this->_request->getPost('id_bib');
		$info_map = $this->_request->getPost('map_data');

		// Création de l'array
		$couches = explode('[COUCHE]',$info_map);
		unset($couches[0]);
		$id_couche = 1;
		foreach($couches as $couche)
			{
				$data["COUCHE"][$id_couche]["ID_COUCHE"] =$id_couche;
				$data["COUCHE"][$id_couche]["TITRE"]=addslashes($this->goParse($couche,'titre=','$longitude'));
				$data["COUCHE"][$id_couche]["LONGITUDE"]=$this->goParse($couche,'$longitude=','$latitude=');
				$data["COUCHE"][$id_couche]["LATITUDE"]=$this->goParse($couche,'$latitude=','$echelle=');
				$data["COUCHE"][$id_couche]["ECHELLE"]=$this->goParse($couche,'$echelle=','$');


				$points = explode('[POINT]',$couche);
				unset($points[0]);
				$id_point = 1;
				foreach($points as $point)
					{
						$data["COUCHE"][$id_couche]["POINT"][$id_point]["ID_COUCHE"]=$id_couche;
						$data["COUCHE"][$id_couche]["POINT"][$id_point]["ID_POINT"]=$id_point;
						$data["COUCHE"][$id_couche]["POINT"][$id_point]["TITRE"]=addslashes($this->goParse($point,'titre=','$longitude'));
						$data["COUCHE"][$id_couche]["POINT"][$id_point]["LONGITUDE"]=$this->goParse($point,'longitude=','$latitude');
						$data["COUCHE"][$id_couche]["POINT"][$id_point]["LATITUDE"]=$this->goParse($point,'$latitude=','$icone');
						$data["COUCHE"][$id_couche]["POINT"][$id_point]["ICONE"]=$this->goParse($point,'$icone=','$adresse');
						$data["COUCHE"][$id_couche]["POINT"][$id_point]["ADRESSE"]=addslashes($this->goParse($point,'$adresse=','$ville='));
						$data["COUCHE"][$id_couche]["POINT"][$id_point]["VILLE"]=addslashes($this->goParse($point,'$ville=','$pays='));
						$data["COUCHE"][$id_couche]["POINT"][$id_point]["PAYS"]=addslashes($this->goParse($point,'$pays=','$photo='));
						$data["COUCHE"][$id_couche]["POINT"][$id_point]["PHOTO"]=$this->goParse($point,'$photo=','$');

						$infos = explode('[INFO]',$point);
						unset($infos[0]);
						$id_info = 1;
						foreach($infos as $info)
							{
								$data["COUCHE"][$id_couche]["POINT"][$id_point]["INFO"][$id_info]["ID_POINT"]=$id_point;
								$data["COUCHE"][$id_couche]["POINT"][$id_point]["INFO"][$id_info]["ID_INFO"]=$id_info;
								$data["COUCHE"][$id_couche]["POINT"][$id_point]["INFO"][$id_info]["TITRE"]=addslashes($this->goParse($info,'$titre=','$texte='));
								$data["COUCHE"][$id_couche]["POINT"][$id_point]["INFO"][$id_info]["TEXTE"]= urlencode($this->goParse($info,'$texte=','$'));
								$id_info++;
							}
						$id_point++;
					}
				$id_couche++;
			}
		$data_bib["GOOGLE_MAP"] = ZendAfi_Filters_Serialize::serialize($data);
		$class_bib = new Class_Bib();
		$error = $class_bib->editBib($data_bib,$id_bib);
		if($error =="") $this->_redirect('admin/bib?z='.$this->id_zone);
		else $this->_redirect('admin/bib/planacces/id/'.$this->_session->ID_SITE);
	}
	//------------------------------------------------------------------------------------------------------
	// Plan d'acces - Icone
	//------------------------------------------------------------------------------------------------------		
	function planaccesiconeAction()
	{

		$viewRenderer = $this->getHelper('ViewRenderer');
		$viewRenderer->setLayoutScript('subModal.phtml');

		require_once("fonctions/file_system.php");
		// setup list of image icons
		$icones=parse_dossier($this->_pathImg."plan_acces");

		$availableImages = array();
		foreach($icones as $ico)
			{
				if(strRight($ico[1],4)==".png" and strRight($ico[1],5) !="s.png")
					{
						$index=str_replace("icon","",$ico[1]);
						$index=str_replace(".png","",$index);
						$availableImages[] = $index;
					}
			}

		$this->view->images = $availableImages;
	}

	function goParse($str,$deb,$fin)
	{
		$pos_deb = strpos($str,$deb);
		$nb_deb = strlen($deb);
		$deb = substr($str,($pos_deb + $nb_deb));

		$pos_fin = strpos($deb,$fin);
		$fin = substr($deb,0,$pos_fin);
		return($fin);
	}
	//------------------------------------------------------------------------------------------------------
	// Plan d'acces - Clef failed
	//------------------------------------------------------------------------------------------------------		
	function planaccesscleffailedAction()
	{
		$this->view->titre = $this->view->_("Impossible d'afficher la carte");
		$cle_plan_acces = getVar('CLEF_GOOGLE_MAP');
		if(empty($cle_plan_acces)){$this->view->clef = $this->view->_("Vous n'avez saisi aucune clef.");}
		else $this->view->clef = getVar('CLEF_GOOGLE_MAP');
	}
	//------------------------------------------------------------------------------------------------------
	// Plan d'acces - Création du plan d'acces par defaut
	//------------------------------------------------------------------------------------------------------			
	function createDefaultPlanAccess($clef_google)
	{
		$translate = Zend_Registry::get('translate');
		$data["CLE"]=$clef_google;
		$data["COUCHE"][1]["ID_COUCHE"] = 1;
		$data["COUCHE"][1]["TITRE"]=$translate->_("** nouvelle couche **");
		$data["COUCHE"][1]["LONGITUDE"]="48.829648";
		$data["COUCHE"][1]["LATITUDE"]="2.630609";
		$data["COUCHE"][1]["ECHELLE"]=15;
		$data["COUCHE"][1]["POINT"][1]["ID_COUCHE"]=1;
		$data["COUCHE"][1]["POINT"][1]["ID_POINT"]=1;
		$data["COUCHE"][1]["POINT"][1]["TITRE"]=$translate->_("** nouveau point **");
		$data["COUCHE"][1]["POINT"][1]["LONGITUDE"]="48.82548598904712";
		$data["COUCHE"][1]["POINT"][1]["LATITUDE"]="2.629852294921875";
		$data["COUCHE"][1]["POINT"][1]["ICONE"]=10;
		$data["COUCHE"][1]["POINT"][1]["ADRESSE"]="";
		$data["COUCHE"][1]["POINT"][1]["VILLE"]="";
		$data["COUCHE"][1]["POINT"][1]["PAYS"]="France";
		$data["COUCHE"][1]["POINT"][1]["PHOTO"]="";
		$data["COUCHE"][1]["POINT"][1]["INFO"][1]["ID_POINT"]=1;
		$data["COUCHE"][1]["POINT"][1]["INFO"][1]["ID_INFO"]=1;
		$data["COUCHE"][1]["POINT"][1]["INFO"][1]["TITRE"]=$translate->_("** nouvelle info **");
		$data["COUCHE"][1]["POINT"][1]["INFO"][1]["TEXTE"]= "";
		return($data);
	}



	function articlesAction() {
		$this->_helper->viewRenderer->setNoRender();
		$include_items = ! (bool)$this->_getParam('categories_only', false);
		$id_bib = (int)$this->_getParam('id_bib', 0);

		if ($id_bib)
			$bibs = array(Class_Bib::getLoader()->find($id_bib));
		else {
			$bibs = Class_Bib::getLoader()->findAllByWithPortail(array('order' => 'libelle'));
		}

		$jsons = array();
		foreach($bibs as $bib)
			$jsons []= $bib->articlesToJSON($include_items);

		$this->getResponse()->setHeader('Content-Type', 'application/json; charset=utf-8');
		$this->getResponse()->setBody('['.implode(',', $jsons).']');
	}


	/*
	 * Renvoie tous les items / catégories au format JSON.
	 * Utilisé pour l'objet jQuery TreeSelect
	 * type = cms | sito | rss
	 * id_bib : l'id de la bib, 0 signifie toutes les bibliothèques
	 * categories_only: si == 1, les items (articles, flux ou sites) ne seront pas chargées.
	 */
	function allitemsAction() {
		$this->_helper->viewRenderer->setNoRender();

		$id_bib = (int)$this->_request->getParam('id_bib', 0);
		$type = $this->_request->getParam('type');
		$do_load_items = (int)$this->_request->getParam('categories_only', 0) == 0;

		$class_bib = new Class_Bib();
		$cats_by_bib = $class_bib->buildBibTree($id_bib, $type, $do_load_items);
		
		$jsons = array();
		foreach($cats_by_bib as $bib)
			$jsons []= $bib->toJSON();

		$this->getResponse()->setHeader('Content-Type', 'application/json; charset=utf-8');
		$this->getResponse()->setBody('['.implode(',', $jsons).']');
	}
}