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
class ZendAfi_View_Helper_RenderDateRange extends Zend_View_Helper_HtmlElement {
	public function renderDateRange($date_debut, $date_fin) {
		$date_fin = $date_fin ? $date_fin : $date_debut;

		if ($date_debut == $date_fin) {
			$event_string = $this->view->humanDate($date_debut, 'dd MMMM Y');
		} else {
			$event_string = $this->view->_(
														'%s au %s',
														$this->view->humanDate($date_debut, 'dd MMMM Y'),
														$this->view->humanDate($date_fin, 'dd MMMM Y')
														);
		}
		return sprintf('<span>%s</span>', $event_string);
	}
}

?>