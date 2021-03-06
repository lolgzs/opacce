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

class MockMailTransport extends Zend_Mail_Transport_Abstract {
	public $sent_mail = null;
	protected $_send_block;
	protected $_sent_mails = array();

	public function send(Zend_Mail $mail) {
		$this->sent_mail = $mail;
		$this->_sent_mails []= $mail;

		if (isset($this->_send_block)) {
			call_user_func($this->_send_block);
		}
	}

	public function onSendDo($block) {
		$this->_send_block = $block;
	}

	protected function _sendMail() {}

	public function getSentMails() {
		return $this->_sent_mails;
	}
}

?>