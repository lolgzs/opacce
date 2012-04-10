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
//  OPAC3: Paniers de notices 
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class PanierNoticeLoader extends Storm_Model_Loader {
	public function findAllBelongsToAdmin() {
		$select = $this
			->getTable()
			->select('notices_paniers.*')
			->setIntegrityCheck(false)
			->from('notices_paniers')
			->join('bib_admin_users',
						 'notices_paniers.id_user = bib_admin_users.id_user')
			->where('bib_admin_users.ROLE_LEVEL > ?', 3)
			->order('notices_paniers.libelle');
		return $this->findAll($select);
	}
}


class Class_PanierNotice extends Storm_Model_Abstract {
	protected $_loader_class = 'PanierNoticeLoader';
	protected $_table_name = 'notices_paniers';
	protected $_table_primary = 'ID';
	protected $_belongs_to = array('user' => array('model' => 'Class_Users',
																								 'referenced_in' => 'id_user'));


	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	public function getClesNotices() {
		$cles = array();

		$notices_field = $this->_get('notices');
		if (empty($notices_field)) return $cles;

		foreach(explode(";", $notices_field) as $cle) {
			if (empty($cle)) continue;

			$cles []= $cle;
		}
		
		return $cles;
	}


	public function getNotices() {
		$cles = $this->getClesNotices();
		return Class_Notice::getLoader()->findAllBy(array('clef_alpha' => $cles));
	}


	public function getNoticesOnlyVignettes($only_vignettes) {
		$notices = $this->getNotices();
		if (!$only_vignettes) return $notices;

		return array_filter_by_method($notices, 'hasVignette', array());
	}


//------------------------------------------------------------------------------------------------------  
// Liste des paniers pour 1 abonné
//------------------------------------------------------------------------------------------------------  
	public function getListeAbonne($id_user) {
		$paniers = $this->getLoader()->findAllBy(array('id_user' => $id_user));
		$listeAbonne  = array();
		foreach($paniers as $panier) 
			$listeAbonne []= array('ID_USER' => $id_user,
														 'ID_PANIER' => $panier->getIdPanier(),
														 'LIBELLE' => $panier->getLibelle(),
														 'DATE_MAJ' => $panier->getDateMaj(),
														 'nombre' => count($panier->getClesNotices()));
		return $listeAbonne;
	}

//------------------------------------------------------------------------------------------------------  
// Créér un panier
//------------------------------------------------------------------------------------------------------  
	public function creerPanier($id_user)
	{
		$sql = Zend_Registry::get('sql');
		$nb = $sql->fetchOne("select max(ID_PANIER) from notices_paniers where ID_USER=$id_user");
		$id_panier=$nb+1;
		$libelle="Panier no ".$id_panier;
		$date=date("Y-m-d");
		$req="insert into notices_paniers(ID_USER,ID_PANIER,NOTICES,LIBELLE,DATE_MAJ) Values($id_user,$id_panier,'','$libelle','$date')";
		$sql->execute($req);
		return $id_panier;
	}
	
//------------------------------------------------------------------------------------------------------  
// Supprimer un panier
//------------------------------------------------------------------------------------------------------  
	public function supprimerPanier($id_user,$id_panier)
	{
		$sql = Zend_Registry::get('sql');
		$req="delete from notices_paniers where ID_USER=$id_user and ID_PANIER=$id_panier";
		$sql->execute($req);
	}

//------------------------------------------------------------------------------------------------------  
// Ajout notice dans panier
//------------------------------------------------------------------------------------------------------  
	public function ajouterNotice($id_user,$id_panier, $id_notice)
	{
		// Clef_alpha
		$clef_alpha=fetchOne("select clef_alpha from notices where id_notice=$id_notice");

		// Verif si deja dans panier
		$notices=fetchOne("select NOTICES from notices_paniers where ID_USER = $id_user and ID_PANIER=$id_panier");
		if($notices)
		{
			if(strPos($notices,";".$clef_alpha.";") !== false) return false;
			$notices.=$clef_alpha.";";
		}
		else $notices=";".$clef_alpha.";";
		$req="Update notices_paniers set NOTICES='$notices' where ID_USER=$id_user and ID_PANIER=$id_panier";
		sqlExecute($req);
		return true;
	}
	
//------------------------------------------------------------------------------------------------------  
// Suppression de notice dans panier
//------------------------------------------------------------------------------------------------------  
	public function supprimerNotice($id_user,$id_panier, $id_notice)
	{
		$clef_alpha=fetchOne("select clef_alpha from notices where id_notice=$id_notice");
		$notices=fetchOne("select NOTICES from notices_paniers where ID_USER = $id_user and ID_PANIER=$id_panier");
		$notices=str_replace($clef_alpha.";","",$notices);
		if($notices == ";") $notices="";
		$req="Update notices_paniers set NOTICES='$notices' where ID_USER=$id_user and ID_PANIER=$id_panier";
		sqlExecute($req);
	}

//------------------------------------------------------------------------------------------------------  
// Liste des notices d'1 panier
//------------------------------------------------------------------------------------------------------  
	public function getPanier($id_user,$id_panier)
	{
		$oNotice=new Class_Notice();
		$enreg=fetchEnreg("select * from notices_paniers where ID_USER = $id_user and ID_PANIER=$id_panier");
		if(!$enreg["NOTICES"])
		{ 
			$enreg["nombre"]=0;
			return $enreg;
		}
		$items=explode(";",$enreg["NOTICES"]);
		foreach($items as $clef_alpha)
		{
			if(!$clef_alpha) continue;
			$notice=$oNotice->getNoticeByClefAlpha($clef_alpha,"TA");
			if(!$notice) continue;
			$nombre++;
			$item["id_notice"]=$notice["id_notice"];
			$item["titre"]=$notice["T"];
			$item["auteur"]=$notice["A"];
			$item["type_doc"]=$notice["type_doc"];
			$notices[]=$item;
		}
		$enreg["NOTICES"]=$notices;
		$enreg["nombre"]=$nombre;
		return $enreg;
	}
	
//------------------------------------------------------------------------------------------------------  
// MAJ libellé d'1 panier
//------------------------------------------------------------------------------------------------------  
	public function majTitre($id_user,$id_panier,$titre)	{
		$sql = Zend_Registry::get('sql');
		$titre=substr($sql->quote($titre),0,50);
		$req="update notices_paniers set LIBELLE=$titre where ID_PANIER=$id_panier and ID_USER=$id_user";
		$sql->execute($req);
	}

//------------------------------------------------------------------------------------------------------  
// Export paniers
//------------------------------------------------------------------------------------------------------ 
	public function exportPaniers($id_user,$id_panier)
	{
		// Controle de la presence du repertoire
		$sep=chr(9);
		$nom_fic=$id_user.$id_panier.".txt";
		$ret["fic_unimarc"]=BASE_URL."/temp/panierunimarc".$nom_fic;
		$ret["fic_liste"]=BASE_URL."/temp/panier".$nom_fic;
		$rep=getcwd()."/temp/";

		if(file_exists($rep) == false)
		{
			$ret["erreur"]="Impossible d'acceder au dossier : ".$rep;
			return $ret;
		}
		$fic_unimarc=fopen($rep."panierunimarc".$nom_fic,"wb");
		$fic_liste=fopen($rep."panier".$nom_fic,"wb");
		
		// Get des notices
		$unimarc = new Class_NoticeUnimarc();
		
		$oNotice=new Class_Notice();
		$enreg=fetchEnreg("select * from notices_paniers where ID_USER = $id_user and ID_PANIER=$id_panier");
		if(!$enreg["NOTICES"])
		{ 
			$ret["erreur"]="Ce panier est vide";
			return $ret;
		}
		$ret["libelle"]=$enreg["LIBELLE"];
		$items=explode(";",$enreg["NOTICES"]);
		foreach($items as $clef_alpha)
		{
			if(!$clef_alpha) continue;
			$nombre++;
			$notice=$oNotice->getNoticeByClefAlpha($clef_alpha,"TAUECN");
			if(!$notice) continue;

			$unimarc->setNotice($unimarc->ISO_encode($notice["U"]));
			fwrite($fic_unimarc, $unimarc->update());
			$enreg=Class_Codification::getLibelleFacette("T".$notice["type_doc"]).$sep;
			$enreg.=$notice["T"].$sep;
			$enreg.=$notice["A"].$sep;
			$enreg.=$notice["E"].$sep;
			$enreg.=$notice["C"].$sep;
			$enreg.=$notice["N"].chr(10);
			fwrite($fic_liste,utf8_decode($enreg));
		}
		$ret["nombre"]=$nombre;
		fclose($fic_unimarc);
		fclose($fic_liste);
		return $ret;
	}

	//-------------------------------------------------------------------------------
	// liste des paniers pour une combo
	//-------------------------------------------------------------------------------
	static function getPaniersForCombo() {
		$liste = array('');

		$user=Zend_Auth::getInstance()->getIdentity();
		$paniers=fetchAll("select ID_PANIER,LIBELLE from notices_paniers where ID_USER=".$user->ID_USER);

		if(!$paniers) 
			return $liste;

		foreach($paniers as $panier) $liste[$panier["ID_PANIER"]]=$panier["LIBELLE"];
		return $liste;
	}

	static function getPaniersForComboMenu() {
		$liste = array('');
		$paniers = self::getLoader()->findAllBelongsToAdmin();

		foreach($paniers as $panier) $liste[$panier->getId()]=$panier->getLibelle().' - '.$panier->getUser()->getNomAff();
		return $liste;
	}


//-------------------------------------------------------------------------------
// Encodage iso pour les paniers unimarc
//-------------------------------------------------------------------------------
private function ISO_encode($chaine) 
{
		if(!$chaine) return "";
		$char_table['À'] = chr(0xc1).chr(0x41);
		$char_table['Á'] = chr(0xc2).chr(0x41);
		$char_table['Â'] = chr(0xc3).chr(0x41);
		$char_table['Ã'] = chr(0xc4).chr(0x41);
		$char_table['Ä'] = chr(0xc9).chr(0x41);
		$char_table['Å'] = chr(0xca).chr(0x41);
		$char_table['Å'] = chr(0xca).chr(0x41);
		$char_table['Ç'] = chr(0xd0).chr(0x43); 
		$char_table['È'] = chr(0xc1).chr(0x45);
		$char_table['É'] = chr(0xc2).chr(0x45);
		$char_table['Ê'] = chr(0xc3).chr(0x45);
		$char_table['Ë'] = chr(0xc8).chr(0x45);
		$char_table['Ì'] = chr(0xc1).chr(0x49);
		$char_table['Í'] = chr(0xc2).chr(0x49);
		$char_table['Î'] = chr(0xc3).chr(0x49);
		$char_table['Ï'] = chr(0xc8).chr(0x49);
		$char_table['Ñ'] = chr(0xc4).chr(0x4e);
		$char_table['Ò'] = chr(0xc1).chr(0x4f);
		$char_table['Ó'] = chr(0xc2).chr(0x4f);
		$char_table['Ô'] = chr(0xc3).chr(0x4f);
		$char_table['Õ'] = chr(0xc4).chr(0x4f);
		$char_table['Ö'] = chr(0xc9).chr(0x4f);
		$char_table['Ù'] = chr(0xc1).chr(0x55);
		$char_table['Ú'] = chr(0xc2).chr(0x55);
		$char_table['Û'] = chr(0xc3).chr(0x55);
		$char_table['Ý'] = chr(0xc2).chr(0x59);
		$char_table['à'] = chr(0xc1).chr(0x61);
		$char_table['á'] = chr(0xc2).chr(0x61);
		$char_table['â'] = chr(0xc3).chr(0x61);
		$char_table['ã'] = chr(0xc4).chr(0x61);
		$char_table['ä'] = chr(0xc9).chr(0x61);
		$char_table['å'] = chr(0xca).chr(0x61);
		$char_table['ç'] = chr(0xd0).chr(0x63);
		$char_table['è'] = chr(0xc1).chr(0x65);
		$char_table['é'] = chr(0xc2).chr(0x65);
		$char_table['ê'] = chr(0xc3).chr(0x65);
		$char_table['ë'] = chr(0xc8).chr(0x65);
		$char_table['ñ'] = chr(0xc4).chr(0x6e);
		$char_table['ì'] = chr(0xc1).chr(0x69);
		$char_table['í'] = chr(0xc2).chr(0x69);
		$char_table['î'] = chr(0xc3).chr(0x69);
		$char_table['ï'] = chr(0xc8).chr(0x69);
		$char_table['ò'] = chr(0xc1).chr(0x6f);
		$char_table['ó'] = chr(0xc2).chr(0x6f);
		$char_table['ô'] = chr(0xc3).chr(0x6f);
		$char_table['õ'] = chr(0xc4).chr(0x6f);
		$char_table['ö'] = chr(0xc9).chr(0x6f);
		$char_table['ù'] = chr(0xc1).chr(0x75);
		$char_table['ú'] = chr(0xc2).chr(0x75);
		$char_table['û'] = chr(0xc3).chr(0x75);
		$char_table['ü'] = chr(0xc9).chr(0x75);
		$char_table['ý'] = chr(0xc2).chr(0x79);
		$char_table['ÿ'] = chr(0xc8).chr(0x79);
		$char_table['Æ'] = chr(0xe1);
		$char_table['Ø'] = chr(0xe9);
		$char_table['þ'] = chr(0xec);
		$char_table['æ'] = chr(0xf1);
		$char_table['ð'] = chr(0xf3);
		$char_table['ø'] = chr(0xf9);
		$char_table['ß'] = chr(0xfb);

 		while(list($char, $value) = each($char_table))
			$chaine = preg_replace("/$char/", $value, $chaine);

		return $chaine;

	}	 
}