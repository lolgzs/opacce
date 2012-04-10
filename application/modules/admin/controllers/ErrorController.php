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

class Admin_ErrorController extends Zend_Controller_Action
{

    function indexAction()
    {
        $this->view->title = "Erreur";
        $this->view->message = "Erreur";
        
    }
    
    function privilegesAction()
    {
        $this->view->title = "Erreur";
        $this->view->message = "Vous n'avez pas les privilèges appropriés";
    }
    
    function databaseAction()
    {	
        $this->view->title = "Erreur";
        $this->view->message = "Problème d'accès à la base de données";
    }
    
    function bibAction()
    {
        $this->view->title = "Erreur";
        $this->view->message = "Il y a encore des utilisateurs attachés à cette bibliothèque, suppression interdite";
    }
    
    function zoneAction()
    {
        $this->view->title = "Erreur";
        $this->view->message = "Il y a encore des bibliothèques attachées à ce territoire , suppression interdite";
    }
    
    function cmsAction()
    {
        $this->view->title = "Erreur";
        $this->view->message = "Il y a encore des objets attachées à cette catégorie , opération interdite";
    }
    
    function rssAction()
    {
        $this->view->title = "Erreur";
        $this->view->message = "Il y a encore des objets attachées à cette catégorie , opération interdite";
    }
}