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
class TableBlog extends Zend_Db_Table_Abstract
{
	protected $_name = 'blog';
}
class TableAlert extends Zend_Db_Table_Abstract
{
	protected $_name = 'blog_alerter';
}


class Class_Blog
{
	var $sql;
    var $_tableblog;
    var $_tablealert;
    var $_today;
    
    public function __construct()
    {
        $this->sql = Zend_Registry::get('sql');
        $this->_tableblog = new TableBlog();
        $this->_tablealert = new TableAlert();
        $class_date = new Class_Date();
        $this->_today = $class_date->DateTimeDuJour();
    }
    
    // Tous les cmt depuis un avis 
    public function getAllCmtByIdAvis($id_notice)
    {
        $sqlStmt = "select * from blog Where ID_NOTICE=$id_notice order by DATE_CMT DESC";
        $stmt = $this->sql->query($sqlStmt);
        $data = $stmt->fetchAll();
        return($data);
    }
    
    
    // Nb cmt sur avis
    public function getNombreCmtSurAvis($id_notice,$cms_ou_notice ="notice")
    {
       if($cms_ou_notice == "notice")$sqlStmt = $this->sql->query("select count(*) from blog Where ID_NOTICE=$id_notice ");
       if($cms_ou_notice == "cms") $sqlStmt = $this->sql->query("select count(*) from blog Where ID_CMS=$id_notice ");
       $data = $sqlStmt->fetchAll(); 
       return($data[0]["count(*)"]); 
    }
    
    // Rend le contenu avec les espace saisie
    public function cmsIt($contenu,$limit = 0)
    {
        $txt_avis = urlencode($contenu);
        $contenu_avis = str_replace('%0D%0A','<br />',$txt_avis );
        if($limit == 0)return(urldecode($contenu_avis));
        else
        {
            $txt = urldecode($contenu_avis);
            if(strlen($txt) > $limit)
            {
                $pos_end = strpos($txt,' ',$limit);
                $texte_cut = substr($txt,0,$pos_end);
                return($texte_cut.' [...]');
            }
            else return($txt);
        }
    }
    
//----------------------------------------------------------------------
// Alert
//----------------------------------------------------------------------    
    public function getTexteAlert($id_avis,$type)
    {
        $sqlStmt = $this->sql->query("select count(*) from blog_alerter Where ID_AVIS='$id_avis'");
        $data = $sqlStmt->fetchAll();  
        if($data[0]["count(*)"] == 0) {
            return('<a href="'.BASE_URL.'/blog/alert?id_avis='.$id_avis.'&type='.$type.'">Alerter</a>');
        }
        else  {
					$translate = Zend_Registry::get('translate');
					return ($translate->_('(ce commentaire a été signalé aux modérateurs)'));
        }
    }
    
    public function alertThis($id_avis,$type)
    {
        $data["ID_ALERT"]='';
        $data["ID_AVIS"]=$id_avis;
        $data["TYPE"]=$type;
        $data["DATE_ALERT"]=$this->_today;
        
        $this->_tablealert->insert($data);
    }
    
    public function getAllAlertes($type="cmt")
    {
        if($type=="cmt")
        {
            $sqlStmt = $this->sql->query("select * from blog_alerter WHERE type='cmt' order by DATE_ALERT");
            $alertes = $sqlStmt->fetchAll();
            
            foreach($alertes as $alerte)
            {
                $id_cmt_cut = explode('-',$alerte["ID_AVIS"]);
                $all_id.= $id_cmt_cut[2].',';
            }
            $all_id = substr($all_id,0,-1);
            if($all_id)
            {
                $sqlStmt = $this->sql->query("select * from blog WHERE ID_CMT IN($all_id)");
                $cmt = $sqlStmt->fetchAll();
                return($cmt);
            }
        }
    }
    
    public function modererAlertCommentaire($action,$id_notice,$id_user,$id_cmt,$contenu)
    {
        $id_avis = $id_notice.'-'.$id_user.'-'.$id_cmt;
        if($action=="1")
        {
            try{
            sqlExecute("update blog set CMT='$contenu' where ID_CMT='$id_cmt'");
            sqlExecute("delete from blog_alerter where ID_AVIS='$id_avis'");
            }catch (Exception $e){ get($e->getMessage());}
        }
		elseif($action=="2")
        {
            try{
            sqlExecute("delete from blog where ID_CMT=$id_cmt");
            sqlExecute("delete from blog_alerter where ID_AVIS='$id_avis'");
            }catch (Exception $e){ get($e->getMessage());}
        }
    }
    
//----------------------------------------------------------------------
// Admin
//----------------------------------------------------------------------
    

    public function addCmt($data)
    {
        if($this->_tableblog == null ){get(' ');}
        try
        {
            $this->_tableblog->insert($data);
        }
        catch (Exception $e)
        {
            logErrorMessage('Class: Class_Blog; Function: addCmt' . NL . $e->getMessage());
        }
    }
    
}