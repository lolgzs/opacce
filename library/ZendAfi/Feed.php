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
/**
 * Extend Zend_Feed to workaround a bug in Zend_Feed::import()
 * The original Zend_Feed::import() has a problem displaying Smart Quotes
 */
class ZendAfi_Feed extends Zend_Feed {
	/**
	 * Imports a feed located at $uri.
	 *
	 * @param  string $uri
	 * @throws Zend_Feed_Exception
	 * @return Zend_Feed_Abstract
	 */
	public static function import($uri) {
		$client = self::getHttpClient();
		$client->setUri($uri);
		$response = $client->request('GET');

		if ($response->getStatus() !== 200) {
			/**
			 * @see Zend_Feed_Exception
			 */
			//require_once 'Zend/Feed/Exception.php';
			throw new Zend_Feed_Exception('Feed failed to load, got response code '
																											. $response->getStatus());
		}

		return self::importString(self::escapeIsoChars($response->getBody()));
	}


	/**
	 * M.F. Added this block of code to deal with smart quotes
	 * @param string $content
	 * @return string
	 */
	public static function escapeIsoChars($content) {
		if (self::isUtf($content)) {
			return $content;
		}

		$search = array(chr(145), chr(146), chr(147), chr(148), chr(150), chr(151));
		$replace = array("'", "'", '"', '"', '-', '-');

		return str_replace($search, $replace, $content);
	}


	/**
	 * @param string $content
	 * @return bool
	 */
	public static function isUtf($content) {
		// search for xml prolog
		$matches = array();
		if (1 === preg_match('#<\\?xml [^>]*>#', $content, $matches)) {
			$encoding = array();
			if (1 === preg_match('#encoding="([^"]+)"#', $matches[0],	$encoding)) {
				return ('utf-8' == strtolower($encoding[1]));
			}
		}

		return true;
	}
}
