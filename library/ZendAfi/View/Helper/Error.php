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

class ZendAfi_View_Helper_Error extends Zend_View_Helper_Abstract {
	/**
	 * @param $error_handler string
   * @return string
	 */
	public function error($error_handler) {
		if (null == $error_handler)
			return '';

		return '<h2>N\'hésitez pas à contacter notre support</h2>
				<div class="panel">
				Par courriel (de préférence) : <a href="mailto:hotline@afi-sa.fr">hotline@afi-sa.fr</a><br>
				Par téléphone : 01.60.17.12.34</div>
				<a href="javascript:$(\'#error_tech\').toggle();">Voir le détail technique de l\'erreur &gt;&gt;</a>
				<div class="panel" id="error_tech" style="display:none;overflow:auto;">
						<strong>Erreur :</strong> '. $this->view->escape($error_handler->exception->getMessage()) . '<br>
						<strong>Date :</strong> '. date('c') .'<br>
						<strong>Pile d\'appel :</strong> <pre><code>'
				. $this->view->escape($error_handler->exception->getTraceAsString()) . '</code></pre><br>
						<strong>Base de données : </strong> '
				. $this->view->escape(array_at('dbname',
						Zend_Db_Table::getDefaultAdapter()->getConfig())) .'<br>
						<strong>Version : </strong> '. $this->view->escape(VERSION_PERGAME) .'<br>
				</div>';
	}
}
?>