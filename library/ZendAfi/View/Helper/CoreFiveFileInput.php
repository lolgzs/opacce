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
// OPAC3 :	Sélection d'une image avec Core Five File Manager
//////////////////////////////////////////////////////////////////////////////////////////
class ZendAfi_View_Helper_CoreFiveFileInput extends ZendAfi_View_Helper_BaseHelper {
	/*
	 * $name: l'attribut name du tag input
	 * $value: l'url par défaut du fichier
	 * $folder: le répertoire de usersfiles sur lequel pointer
	 */
	public function CoreFiveFileInput($name, $value, $folder, $type = 'Images') {
		$banniere_dir = USERFILESPATH."/$folder/";
		if (!is_dir($banniere_dir))
			mkdir($banniere_dir);

		// Dernière mise à jour de Core Five: type Files / Flash plus utilisés.
		if ($type == 'Images') 
			$type = 'type=Images';
		else
			$type = '';

		$c5_url = CKBASEURL."core_five_filemanager/index.html?".$type."&ServerPath=".USERFILESURL."$folder/";

		$html = "<script type='text/javascript'>".
			         "function openFileManagerFor_$name(){".
                "SetUrl = function(data){\$(\"input[name='$name']\").attr('value',data);};".
                "window.open(\"$c5_url\")".
               "}".
            "</script>";

		$html .= sprintf("<input type='text' name='$name' value='$value' size='50'><input type='button' value='%s' onclick='openFileManagerFor_$name()'>",
										 $this->translate()->_('Explorer le serveur'));
		return $html;
	}
}