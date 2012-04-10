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
class Admin_UploadController extends Zend_Controller_Action {
	public function multipleAction() {
		$this->view->headScript()
								->appendFile(JQUERY)
								->appendFile(URL_ADMIN_JS . 'multi_upload/fileuploader.js')
								->appendScript("
$(document).ready(function () {
	var uploader = new qq.FileUploader({
		'element': document.getElementById('file_uploader'),
		'action': '" . $this->view->url(array(
							'action' => 'multiple-process',
							'modelClass' => $this->_getParam('modelClass'),
							'modelId'		=> $this->_getParam('modelId'),
						)) . "',
		'debug': true
	});
});");

		$this->view->headLink()
								->appendStylesheet(URL_CSS . 'global.css')
								->appendStylesheet(URL_ADMIN_JS . 'multi_upload/fileuploader.css');

		$this->_helper->getHelper('viewRenderer')->setLayoutScript('empty.phtml');
	}


	public function multipleProcessAction() {
		if (
			$this->_request->isPost()
			&& (null !== ($fileName = $this->_getParam('qqfile')))
			&& (null !== ($modelClass = $this->_getParam('modelClass')))
			&& (0 < ($modelId = (int)$this->_getParam('modelId')))
		) {
			if (!$loader	= @call_user_func(array($this->_getParam('modelClass'), 'getLoader'))) {
				$this->_helper->json(array('success' => 'false', 'error' => 'Bad model loader'));
				return;
			}

			if (null === ($model = $loader->find($this->_getParam('modelId')))) {
				$this->_helper->json(array('success' => 'false', 'error' => 'No model'));
				return;
			}

			$this->_helper->json($model->addFile($this->_request));
			$model->save();
			return;
		}

		$this->_helper->json(array('success' => 'false'));
	}
}
?>