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
class AmberController extends Zend_Controller_Action {
	use Trait_StaticFileWriter;

	public function init() {
		$this->getHelper('ViewRenderer')->setNoRender();
	}


	protected function _writeContents($commit_path, $contents) {
		self::getFileWriter()->putContents($commit_path, $contents);
	}


	protected function commitTo($commit_subpath) {
		if (Class_ScriptLoader::getInstance()->isAmberModeDeploy())
			return $this;

		if (!Class_Users::getIdentity()->isSuperAdmin())
			return $this;

		$request_uri = $this->_request->getRequestUri();
		$filename = array_last(explode('/', $request_uri));
		$contents = $this->_request->getRawBody();

		$commit_path = './amber/';
		if ($this->isAFIPackage($filename))
			$commit_path .= 'afi/';
		else
			$commit_path .= 'src/';

		$commit_path .= $commit_subpath.'/'.$filename;
		
		$this->_writeContents($commit_path, $contents);

		return $this;
	}


	public function isAFIPackage($filename) {
		return (0 === strpos($filename, 'A'));
	}


	public function commitjsAction() {
		$this->commitTo('js');
	}


	public function commitstAction() {
		$this->commitTo('st');
	}
}
?>