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
class ZendAfi_View_Helper_IconeSupport extends Zend_View_Helper_HtmlElement {
	protected $_filename_by_support = array(
																					1 => 'famille_livre_small.png',
																					2 => 'famille_periodique_small.png',
																					3 => 'son_s.png',
																					4 => 'famille_video_small.png',
																					5 => 'mul_s.png',
																					6 => 'support_6.gif',
																					7 => 'img_s.png',
																					8 => 'per_s_dep.png',
																					9 => 'support_9.gif',
																					10=> 'support_10.gif'
																					);
	
	public function iconeSupport($type_doc_id) {
		return sprintf('<img class="icone_support" src="%s" alt="%s" border="0" />',
									 $this->imageForSupport($type_doc_id),
									 $this->view->_('Support'));
	}


	public function pathInSkin($filename) {
		return PATH_SKIN.'/images/supports/'.$filename;
	}

	public function urlInSkin($filename) {
		return URL_IMG.'supports/'.$filename;
	}
	
	public function pathInAdmin($filename) {
		return PATH_ADMIN_SUPPORTS.$filename;
	}

	public function urlInAdmin($filename) {
		return URL_ADMIN_IMG.'supports/'.$filename;
	}


	public function imageForSupport($id) {
		//keep format support_xxx_.gif for backward compatibility
		$filename = 'support_'.$id.'.gif';
	
		if ($this->fileExists($this->pathInSkin($filename)))
			return $this->urlInSkin($filename);
		
		//then back to new icons
		$filename = $this->getFileNameBySupport($id);
		if ($this->fileExists($this->pathInSkin($filename)))
			return $this->urlInSkin($filename);

		return $this->urlInAdmin($filename);
	}


	public function getFilenameBySupport($id) {
		if (array_key_exists($id, $this->_filename_by_support))
			return $this->_filename_by_support[$id];

		if ($id >= Class_TypeDoc::LIVRE_NUM) //ressources numeriques
			return 'mls_s.png';

		return 'aut_s.png';
	}


	public function fileExists($filename) {
		return file_exists($filename);
	}
}