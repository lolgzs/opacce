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
// OPAC3: classe de Gestion des Avis sur les articles
///////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

class Class_Avis extends Storm_Model_Abstract {
	const AVIS_ABONNE = 0;
	const AVIS_BIBLIO = 1;

	protected $_table_name = 'cms_avis';
	protected $_belongs_to = array('auteur' => array('model' => 'Class_Users',
																									 'referenced_in' => 'id_user'),

																 'article' => array('model' => 'Class_Article',
																                 	  'referenced_in' => 'id_cms'));

	var $sql;	// Curseur sql
	var $_today;

	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}

	//------------------------------------------------------------------------------------------------------  
	// Constructeur
	//------------------------------------------------------------------------------------------------------  
	function __construct()
	{
		$this->sql = Zend_Registry::get('sql');
		$class_date = new Class_Date();
		$this->_today = $class_date->DateTimeDuJour();
	}

	//------------------------------------------------------------------------------------------------------  
	// html Avis pour le CMS pour les boites
	//------------------------------------------------------------------------------------------------------  
	function getCmsAvisById($id_user,$id_news)
	{
		// News
		$req_news = "Select * from cms_avis Where ID_CMS=$id_news AND ID_USER=$id_user";
		return($this->sql->fetchAll($req_news));
	}
    
	//------------------------------------------------------------------------------------------------------  
	// Ecrire 1 avis (update si existe déjà)
	//------------------------------------------------------------------------------------------------------  
	function ecrireCmsAvis($id_user,$role_level,$id_news,$note,$entete,$avis) 
	{
		$modo_avis_abo = getVar('MODO_AVIS'); // 0 apres / 1 avant de publier sur le site
		$modo_avis_bib = getVar('MODO_AVIS_BIBLIO'); // 0 apres / 1 avant de publier sur le site

		if($role_level < 3)
			{
				$abon_ou_bib=0;
				if($modo_avis_abo == 1){$statut = 0;}
				if($modo_avis_abo == 0){$statut = 1;}
			}
		else 
			{
				$abon_ou_bib=1;
				if($modo_avis_bib == 1) $statut=0;
				if($modo_avis_bib == 0) $statut=1;
			}

		try{
			$entete=strLeft($this->sql->quote($entete),100);
			$avis=$this->sql->quote($avis);
			$controle=fetchOne("select count(*) from cms_avis where ID_USER=$id_user and ID_CMS=$id_news");
			if(!$controle) 
				$req="Insert into cms_avis(ID_USER,ID_NOTICE,ID_CMS,DATE_AVIS,DATE_MOD,NOTE,ENTETE,AVIS,STATUT,ABON_OU_BIB) values($id_user,'',$id_news,'$this->_today','','$note',$entete,$avis,$statut,'$abon_ou_bib')";
			else 
				$req="Update cms_avis Set NOTE='$note',ENTETE=$entete,AVIS=$avis,STATUT=$statut,ABON_OU_BIB='$abon_ou_bib' Where ID_USER=$id_user and ID_CMS=$id_news";
			$stmt = $this->sql->query($req);
			
			if($modo_avis_abo == 0 && $role_level < 3) {$this->maj_note_cms($id_news,$abon_ou_bib);}
			if($modo_avis_bib == 0 && $role_level >= 3) {$this->maj_note_cms($id_news,$abon_ou_bib);}
            
		}catch (Exception $e){
			logErrorMessage('Class: Class_Avis; Function: ecrireCmsAvis' . NL . $req . NL . $e->getMessage());
			return false;
		}
	}

	//------------------------------------------------------------------------------------------------------  
	// Maj note et rang notice
	//------------------------------------------------------------------------------------------------------  
	function maj_note_cms($id_news,$abon_ou_bib)
	{
		$sqlStmt = "Select count(*), avg(NOTE) From cms_avis Where ID_CMS=$id_news and ABON_OU_BIB='$abon_ou_bib' AND STATUT=1";
		$data = $this->sql->fetchAll($sqlStmt);
		$note=round($data[0]["avg(NOTE)"],1);
		if
			(strlen($note)==1) $note.=".0";
		else	{
				$dec=strRight($note,1);
				if($dec<3) $dec="0"; elseif($dec<"8")$dec="5";else {$note+=1; $dec=0;}
				$note=strLeft($note,1).".".$dec;
			}

		$nombre=$data[0]["count(*)"];
		// UPDATE `opac3`.`cms_avis` SET `STATUT` = '0' WHERE `cms_avis`.`ID_CMS` =1;
		// Lire enreg rank
		$enreg=fetchEnreg("select * from cms_rank where ID_CMS=$id_news");
		$sqlStmt = "Delete from cms_rank Where ID_CMS=$id_news";
		$stmt = $this->sql->query($sqlStmt);
		if($abon_ou_bib==1)
			{
				$abon_nombre_avis=$enreg["ABON_NOMBRE_AVIS"];
				$abon_note=$enreg["ABON_NOTE"];
				$bib_nombre_avis=$nombre;
				$bib_note=$note;
			}
		else
			{
				$abon_nombre_avis=$nombre;
				$abon_note=$note;
				$bib_nombre_avis=$enreg["BIB_NOMBRE_AVIS"];
				$bib_note=$enreg["BIB_NOTE"];
			}
    
		if(!$abon_nombre_avis) $abon_nombre_avis=0;
		if(!$bib_nombre_avis) $bib_nombre_avis=0;
		//if(!$abon_nombre_avis and !$bib_nombre_avis) return false;
		$sqlStmt = "Insert Into cms_rank(ID_CMS,ABON_NOMBRE_AVIS,ABON_NOTE,BIB_NOMBRE_AVIS,BIB_NOTE) Values($id_news,$abon_nombre_avis,'$abon_note',$bib_nombre_avis,'$bib_note')";
		$stmt = $this->sql->query($sqlStmt);	
	}
    
	function HtmlCmsAvecAvis($id_news)
	{
		// News
		$req_news = "Select * from cms_article Where ID_ARTICLE=$id_news";
		$news = $this->sql->fetchAll($req_news);
        
		if($news[0]["AVIS"] == 1)
			{
				$sqlStmt = "Select * from cms_rank Where ID_CMS=$id_news";
				$ret = $this->sql->fetchAll($sqlStmt);

				$nb_avis = (int)$ret[0]["BIB_NOMBRE_AVIS"] + (int)$ret[0]["ABON_NOMBRE_AVIS"];
				if($nb_avis ==0) return ('<div align="right"><a href="'.BASE_URL.'/cms/articleview/id/'.$id_news.'">&raquo;aucun avis</a></div>');
				else return ('<div align="right"><a href="'.BASE_URL.'/cms/articleview/id/'.$id_news.'">&raquo; avis ('.$nb_avis.')</a></div>');
			}
		else
			{
				return(' ');
			}	
	}

    
	public function beWrittenByAbonne() {
		return $this->setAbonOuBib(self::AVIS_ABONNE);
	}


	public function beWrittenByBibliothecaire() {
		return $this->setAbonOuBib(self::AVIS_BIBLIO);
	}

}