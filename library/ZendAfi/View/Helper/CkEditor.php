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

include_once(CKBASEPATH . "ckeditor.php");

class ZendAfi_View_Helper_CkEditor extends ZendAfi_View_Helper_BaseHelper
{
	/*
	 * @param $initial Initial content for the editor
	 */
	public function ckEditor($initial, $editorId, $showFileBrowser=true)
	{
		$config = array();
		if ($showFileBrowser) {
			$config['filebrowserBrowseUrl'] = CKBASEURL."core_five_filemanager/index.html?ServerPath=".USERFILESURL."file/";
			$config['filebrowserImageBrowseUrl'] = CKBASEURL."core_five_filemanager/index.html?type=Images&ServerPath=".USERFILESURL."image/";
			$config['filebrowserFlashBrowseUrl'] = CKBASEURL."core_five_filemanager/index.html?type=Flash&Connector=connectors/php/connector.php&ServerPath=".USERFILESURL."flash/";
		}
		$config['filebrowserUploadUrl'] = CKBASEURL."filemanager/upload/php/upload.php?ServerPath=".USERFILESURL;
		$config['filebrowserImageUploadUrl'] = CKBASEURL."filemanager/upload/php/upload.php?Type=Image&ServerPath=".USERFILESURL;
		$config['filebrowserFlashUploadUrl'] = CKBASEURL."filemanager/upload/php/upload.php?Type=Flash&ServerPath=".USERFILESURL;
		$config['imagesPath'] = URL_ADMIN_IMG."ckeditor_templates/";
		$config['templates_files'] = array(URL_ADMIN_JS."ckeditor_templates.js");
		$config['contentsCss'] = array(URL_CSS."global.css");

		$config['toolbar_Full'] = [
			['Preview', 'Templates', 'Source','Maximize'],
			['Cut','Copy','Paste'],
			['Undo','Redo','-','SelectAll','RemoveFormat'],
			['Link','Unlink','Anchor'],
			['Image','Flash','Table','HorizontalRule'],
			'/',
			['Styles','FontSize','TextColor','BGColor'],
			['Bold','Italic','Underline','Strike'],
			['NumberedList','BulletedList','-','Outdent','Indent'],
			['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
		];

		if (Class_AdminVar::isCmsFormulairesEnabled()) {
			$config['toolbar_Full'][]=['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'];
		}

		$oCKeditor = new CKeditor(CKBASEURL);
		$oCKeditor->returnOutput = true;
		return $oCKeditor->editor($editorId, $initial, $config);
	}
}