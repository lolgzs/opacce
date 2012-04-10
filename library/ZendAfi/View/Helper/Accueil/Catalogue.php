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
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 - Class_Module_Catalogue
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_Accueil_Catalogue extends ZendAfi_View_Helper_Accueil_Base
{

//---------------------------------------------------------------------
// Construction HTML 
//---------------------------------------------------------------------
    public function getHtml()
    {
			// Calcul requete en fonction des preferences
      switch($this->preferences["notices"])
      {
      	case 0: $req="select id_notice from notices";	break;
      	case 1: $req="select id_notice from notices order by nb_visu desc"; break;
      	case 2: $req="select id_notice from notices order by date_creation desc"; break;
      }
      $req.=" limit 0,".$this->preferences["nb_requete"];
      
      // Lire les notices
      $ids=fetchAll($req);
      if(count($ids) > 0)
      {
      	$notice=new Class_Notice();
      	foreach($ids as $lig)	$ret[]=$notice->getNotice($lig["id_notice"],"TA");
      }
       
      // Html avec le view helper
      if(!count($ret)) 
				$html["CONTENU"]=sprintf('<div>%s</div>', 
																 $this->translate()->_("Aucune notice n'a été trouvée"));
      else  {
      	$cls=new ZendAfi_View_Helper_ListeNotices();
       	$contenu.= $cls->listeNotices($ret,count($ret),1,$this->preferences);
      }
  
      // Valorisation du html accessible et retour
      $this->titre = $this->preferences["message"];
      $this->contenu=$contenu;
      
      return $this->getHtmlArray();
    }

}