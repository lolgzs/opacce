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
	protected $_builder;
	protected $_identifier;
	protected $_date;
	protected $_globalSetSpec;


	public function __construct() {
		$this->_builder = new Class_Xml_Oai_DublinCoreBuilder();
	}


	public function visit($notice) {
		$this->_xml = '';
		$this->_identifier = null;
		$this->_date = null;
		$notice->acceptVisitor($this);
	}


	public function xml() {
		return $this->_builder->oai_dc(
			$this->_builder->identifier($this->_identifier)
			. $this->_xml);
	}


	public function visitClefAlpha($clef) {
		$this->_identifier = sprintf('oai:%s:%s',
																 $_SERVER['SERVER_NAME'], $clef);
	}


	/**
	 * @param $titres array
	 */
	public function visitTitres($titres) {
		if (!is_array($titres) || empty($titres))
			return;
		
		$this->_xml .= $this->_builder->title($this->cdata(strip_tags(implode('. ', $titres))));
	}


	/**
	 * @param $auteurs array
	 */
	public function visitAuteurs($auteurs) {
		$this->_visitAuteurWithClosure($auteurs, function ($data) {
			return $this->_builder->creator($this->cdata($data));
		});
	}


	/**
	 * @param $auteurs array
	 */
	public function visitContributeurs($auteurs) {
		$this->_visitAuteurWithClosure($auteurs, function ($data) {
			return $this->_builder->contributor($this->cdata($data));
		});
	}


	protected function _visitAuteurWithClosure($auteurs, $closure) {
		if (!is_array($auteurs) || empty($auteurs))
			return;

		foreach ($auteurs as $auteur) {
			$parts = explode('|', $auteur);
			$this->_xml .= $closure(implode(', ', $parts));
		}
	}


	public function visitResume($resume) {
		if ($resume) 
			$this->_xml .= $this->_builder->description($this->cdata($resume));
	}


	public function visitDateMaj($dateMaj) {
		$this->_date = substr($dateMaj, 0, 10);
	}


	public function visitAnnee($annee) {
		if ($annee)
			$this->_xml .= $this->_builder->date($annee);
	}


	public function visitMatiere($matiere) {
		$this->_xml .= $this->_builder->subject($this->cdata($matiere));
	}


	public function visitEditeur($editeur) {
		if ($editeur)
			$this->_xml .= $this->_builder->publisher($this->cdata($editeur));
	}


	public function visitLangues($langues) {
		if (!is_array($langues)) 
			return;

		foreach ($langues as $langue)
			$this->_xml .= $this->_builder->language($langue);
	}


	public function visitTypeDoc($id) {
		if (!$id)
			return;

		if ($type = Class_TypeDoc::find($id))
			$this->_xml .= $this->_builder->type($this->cdata($type->getLabel()));

		if (in_array($id, [Class_TypeDoc::LIVRE_NUM, Class_TypeDoc::DIAPORAMA])) 
			$this->visitFormat('image/jpeg');
	}


	public function visitFormats($datas) {
		if (!is_array($datas) || empty($datas))
			return;

		foreach ($datas as $data)
			$this->visitFormat($data);
	}

	
	public function visitFormat($data) {
		if (!$data)
			return;
		$this->_xml .= $this->_builder->format($this->cdata($data));
	}


	public function visitVignette($url) {
		if (!$url or 'NO' == $url )
			return;

		$this->_xml .= $this->_builder->relation($this->cdata('vignette : ' . $url));
	}


	public function visitIsbn($isbn) {
		if (!$isbn)
			return;
		$this->_xml .= $this->_builder->identifier($isbn);
	}


	public function visitEan($ean) {
		if (!$ean)
			return;
		$this->_xml .= $this->_builder->identifier($ean);
	}


	/**
	 * @param $album Class_Album
	 */
	public function visitAlbum($album) {
		$this->_xml .= $this->_builder->rights((null === $album) ? '' : $album->getDroits());
	}


	/**
	 * @param $source array
	 */
	public function visitSource($source) {
		if (!is_array($source) || empty($source))
			return;

		$this->_xml .= $this->_builder->source($this->cdata(implode(', ', $source)));
	}


	public function getIdentifier() {
		return $this->_identifier;
	}


	public function getDate() {
		return $this->_date;
	}


	public function cdata($value) {
		return $this->_builder->cdata($value);
	}


	public function setGlobalSetSpec($spec) {
		$this->_globalSetSpec = $spec;
		return $this;
	}


	public function getGlobalSetSpec() {
		return $this->_globalSetSpec;
	}
}
?>