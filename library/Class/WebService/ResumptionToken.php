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

/*
 * Représente un token qui permets de reprendre le download
 * de données (OAI) là ou on en était
 */

class Class_WebService_ResumptionToken {
	public function getToken() {
		return $this->_token;
	}

	public function setToken($token) {
		$this->_token = $token;
		return $this;
	}

	public function getListSize() {
		return $this->_list_size;
	}

	public function setListSize($size) {
		$this->_list_size = $size;
		return $this;
	}

	public function getCursor() {
		return $this->_cursor;
	}

	public function setCursor($cursor) {
		$this->_cursor = $cursor;
		return $this;
	}

	public function toJSON() {
		return json_encode(array(
														 "token" => $this->getToken(), 
														 "list_size" => $this->getListSize(),
														 "cursor" => $this->getCursor()));
	}
}

?>