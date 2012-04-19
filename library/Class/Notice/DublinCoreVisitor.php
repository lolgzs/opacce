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

class Class_Notice_DublinCoreVisitor {
	protected $_xml;

	public function visit($notice) {
		$this->_xml = '<oai_dc:dc xmlns:dc="http://purl.org/dc/elements/1.1/"
									 xmlns:oai_dc="http://www.openarchives.org/OAI/2.0/oai_dc/">';

		$notice->acceptVisitor($this);
		$this->_xml .= '</oai_dc:dc>';
		
	}


	public function xml() {
		return $this->_xml;
	}


	public function visitClefAlpha($clef) {
		$this->addTag('identifier', sprintf('http://%s%s/recherche/notice/%s',
																				$_SERVER['SERVER_NAME'],
																				BASE_URL,
																				$clef));
	}


	public function visitTitre($titre) {
		$this->addTag('title', $titre);
	}


	public function addTag($tag, $content) {
		$this->_xml .= sprintf('<dc:%1$s>%2$s</dc:%1$s>',
													 $tag, $content);
	}
}

?>