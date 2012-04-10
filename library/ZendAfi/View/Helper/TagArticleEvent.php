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
class ZendAfi_View_Helper_TagArticleEvent extends Zend_View_Helper_HtmlElement {
	/**
	 * @param Class_Article $article
	 * @return string
	 */
	public function tagArticleEvent($article) {
		if (!$time_start	= strtotime($article->getEventsDebut()))
			return '';

		if (!$time_end = strtotime($article->getEventsFin()))
			$time_end = $time_start;
		
		$month_start = strftime('%B', $time_start);
		$month_end = strftime('%B', $time_end);
		if ($month_start == $month_end)
			$month_start = '';

		$year_start = strftime('%Y', $time_start);
		$year_end = strftime('%Y', $time_end);
		if ($year_start == $year_end)
			$year_start = '';


		if ($time_start == $time_end) {
			$event_string = $this->view->_(
														'Le %s',
														trim(strftime('%d', $time_start) . ' ' . $month_end . ' ' . $year_end)
														);
		} else {
			$event_string = $this->view->_(
														'Du %s au %s',
														trim(strftime('%d', $time_start) . ' ' . $month_start . ' ' . $year_start),
														trim(strftime('%d', $time_end) . ' ' . $month_end . ' ' . $year_end)
														);
		}
		return sprintf('<span class="calendar_event_date">%s</span>', $event_string);
	}
}
?>