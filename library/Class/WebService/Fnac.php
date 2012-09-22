<?php
/**
 * Copyright (c) 2012, Agence FranÃ§aise Informatique (AFI). All rights reserved.
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

class Class_WebService_Fnac extends Class_WebService_Abstract {
	private $url = 'http://www3.fnac.com/advanced/book.do?isbn=';

	public function getResumes($notice) {
		if (!$service = $notice->getIsbnOrEan())
			return array();

		if ($resume = $this->getResume($service))
			return array(array('source' => 'Editeur',
												 'texte' => $resume));
		return array();
	}

//------------------------------------------------------------------------------------------------------
// Résumé de l'editeur
//------------------------------------------------------------------------------------------------------	
	public function getResume($isbn) {
		if(!$isbn) return false;
		$isbn=str_replace("-","",$isbn);

		$data = self::getHttpClient()->open_url($this->url.$isbn);
		$url_lire_la_suite = $this->getUrlLireLaSuite($data);

		$suite = self::getHttpClient()->open_url($url_lire_la_suite);
		return strip_tags($this->extractResumeFromHTML($suite));
	}


	public function getUrlLireLaSuite($data) {
		$pos=striPos($data,"resume");
		if(!$pos) 
			return array();

		$pos = strPos($data,"a href=\"",$pos)+8;
		$posfin = strPos($data,"\"",$pos);
		return substr($data,$pos,($posfin-$pos));
	}


	public function extractResumeFromHTML($html) {
		if (!$pos = striPos($html, "laSuite bigLaSuite"))
				return $this->extractLireLaSuiteDivFromHTML($html);

		$pos = striPos($html, "img", $pos);
		$pos = striPos($html, ">", $pos) + 1;

		$posfin = strPos($html, "<p>", $pos);
		$posfin2 = strPos($html, "</div>", $pos);
		$posfin = $posfin < $posfin2 ? $posfin : $posfin2;

		$resume = substr($html, $pos, ($posfin-$pos));
		return trim($resume);
	}


	public function extractLireLaSuiteDivFromHTML($html) {
		$start_string = "lireLaSuite mrg_v_sm";
		if (!$pos = striPos($html, $start_string))
			return '';

		$pos = $pos + strlen($start_string)+2;

		$posfin = strPos($html, "</", $pos);

		$resume = substr($html, $pos, ($posfin - $pos));

		return trim($resume);
	}
}