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
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
//	 OPAC- 3																				Class_Calendar
//////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
class Class_Calendar {
	var $PREFIX					= "calendar_";
	var $CSS_PREFIX			= "calendar_";
	var $URL_PARAMETER	= "date";
	var $PRESERVE_URL		= true;
	var $FIRST_WEEK_DAY = 1;								// premier jour-> 0 = dimanche 1 = lundi
	var $LANGUAGE_CODE	= "fr";
	var $WEEK_DAYS;
	var $MONTH_HEADER = array('fr' => "%m %y");
	var $events = array();
	var $today;
	var $param;
	var $day;
	var $month;
	var $year;
	protected $id_module;
	protected $articles; //Liste des articles à afficher, chargés dans _loadArticles
	protected $_translate;
	protected $_article_event_helper;


	public function __construct($param_array) {
		$this->_translate = Zend_Registry::get('translate');
		$this->WEEK_DAYS = array($this->_translate->_("dim"),
														 $this->_translate->_("lun"),
														 $this->_translate->_("mar"),
														 $this->_translate->_("mer"),
														 $this->_translate->_("jeu"),
														 $this->_translate->_("ven"),
														 $this->_translate->_("sam"));

		$this->MONTHS = array($this->_translate->_('janvier'),
													$this->_translate->_('février'),
													$this->_translate->_('mars'),
													$this->_translate->_('avril'),
													$this->_translate->_('mai'),
													$this->_translate->_('juin'),
													$this->_translate->_('juillet'),
													$this->_translate->_('août'),
													$this->_translate->_('septembre'),
													$this->_translate->_('octobre'),
													$this->_translate->_('novembre'),
													$this->_translate->_('décembre'));

		// Month and year to display (gotten from URL)
		if (isset($param_array["DATE"]) && $param_array["DATE"] != "") {
			$date_array = explode('-', $param_array["DATE"]);
			$this->month = (int)$date_array[1];
			$this->year	 = (int)$date_array[0];
		}
		else {
			$this->month = gmdate("n");
			$this->year = gmdate("Y");
		}
		$param_array["DATE"] = sprintf('%4d-%02d', $this->year, $this->month);

		$this->param = $param_array;
		$this->today = date("dmY");
		$this->id_module = $param_array["ID_MODULE"];

		$this->_loadArticles();
	}


	/* Charge la liste des articles à afficher
	 * Paramètres utilisés:
	 * param["DATE"]: mois / année des articles
	 * param["ID_BIB"]: bibliothèque
	 * param["SELECT_ID_CAT"]: ne charger que les articles de cette catégorie (paramètre utilisateur),
	 *												 ou tous si vaut "all"
	 * param["ID_CAT"]: Liste des catégories autorisées (paramètre administrateur)
	 */
	protected function _loadArticles(){
		if (('all' !== $this->param["SELECT_ID_CAT"]) and ($this->param['DISPLAY_CAT_SELECT']))
			$id_cat = (int)$this->param['SELECT_ID_CAT'];
		else
			$id_cat = $this->param['ID_CAT'];

		if (0 == $id_cat)
			$id_cat = '';

		// Lire les news dans la bdd
		$articles = Class_Article::getLoader()->getArticlesByPreferences(array(
																	'display_order' => 'EventDebut',
																	'id_categorie' => $id_cat,
																	'event_date' => $this->param["DATE"],
																	'events_only' => true,
																	'published' => false));

		if ($id_bib = $this->param['ID_BIB']) {
			$this->articles = array();
			foreach($articles as $article)  {
				if (($id_bib == $article->getBib()->getId()))
					$this->articles []= $article;
			}
		} else {
			$this->articles = $articles;
		}

		$this->articles = Class_Article::filterByLocaleAndWorkflow($this->articles);

		if ($this->param["ALEATOIRE"] == 1)
			shuffle($this->articles);

		if (count($articles) < $this->param['NB_NEWS']) {
			$next_articles = Class_Article::getLoader()->getArticlesByPreferences(array(
																	'display_order' => 'EventDebut',
																	'id_categorie' => $id_cat,
																	'event_start_after' => $this->param["DATE"],
																	'events_only' => true,
																	'id_bib' => $this->param['ID_BIB'],
																	'limit' =>  $this->param['NB_NEWS'],
																	'published' => false));
			$this->articles = array_merge($this->articles,
																		Class_Article::filterByLocaleAndWorkflow($next_articles));
		}

		return $this;
	}


	/* liste des articles chargés, voir _loadArticles */
	protected function _getArticles() {
		return $this->articles;
	}


	/* Charge les catégories sélectionnées au niveau administrateur si ce n'est déjà fait
	 * et retourne la liste des paires id/libelle
	 */
	protected function _getAdminSelectedCategories() {
		if (isset($this->admin_selected_categories))
			return $this->admin_selected_categories;
		$this->admin_selected_categories = array();

		$ids = array_filter(explode('-', $this->param["ID_CAT"]));

		if (empty($ids)) {
			$this->admin_selected_categories = Class_ArticleCategorie::getLoader()
				->findAllBy(array('ID_CAT_MERE' => 0,
													'order'				=> 'LIBELLE'));
		} else {
			$this->admin_selected_categories = Class_ArticleCategorie::getLoader()
				->findAllBy(array('ID_CAT'	=> $ids,
													'order'		=> 'LIBELLE'));

		}


		return $this->admin_selected_categories;
	}


	/* Affiche la liste déroulante de sélection de la catégorie à
	 * afficher sur le calendrier (sélection utilisateur) ou toutes les catégories
	 *
	 * N'est affiché que si l'option administrateur est cochée param["DISPLAY_CAT_SELECT"]
	 */
	protected function rendSelectionCategories() {
		if ($this->param["DISPLAY_CAT_SELECT"]==null) return;

		$cats = $this->_getAdminSelectedCategories();

		$options = '<option value="all">Toutes</option>';
		foreach($cats as $categorie){
			$selected = ($categorie->getId()==$this->param["SELECT_ID_CAT"]) ? 'selected="selected"' : '';
			$options .= '<option '.$selected.' value="'.$categorie->getId().'">'.$categorie->getLibelle().'</option>';
		}

		$action_url=BASE_URL.'/opac/cms/calendar/';

		return sprintf('
			<form id="calendar_select_categorie"	method="get" action="%s">
			  <label for="select_id_categorie">%s:</label>
				<select id="select_id_categorie" name="select_id_categorie">%s</select>
				<input type="hidden" name="id_module" value="%d"></input>
			</form>',
		 $action_url,
		 $this->_translate->_('Catégorie'),
		 $options,
		 $this->id_module);
	}


	/* Retourne la catégorie de l'article ou la bibliothèque selon l'option administrateur
	 * $this->param["EVENT_INFO"]
	 */
	protected function _getArticleEventInfo($news){
		$label = 'Portail';
		$params = "b=0";

		if ($this->param["EVENT_INFO"]=="bib"){
			if ($bib = $news->getBib()) {
				$label = $bib->getVille();
				$params = 'b='.$bib->getId();
			}
		}

		else if ($this->param["EVENT_INFO"]=="cat") {
			$label = $news->getCategorie()->getLibelle();
			$params = 'cat='.$news->getIdCat();
		}

		else {
			$label = '';
			$params = '';
		}

		$anchor = '<a class="calendar_event_info" href="'.BASE_URL.'/opac/cms/articleviewbydate?'.
			$params.'" target="_parent">'.$label.'</a>';
		return $anchor;
	}


	protected function anEventStartThisDay($date, $events) {
		$day = (int)gmdate("j", $date);
		$month = (int)gmdate("m", $date);
		foreach($events as $event) {
			$event_debut = $this->filtreDateZend($event->getEventsDebut());
			if (($event_debut[1] == $month) && ($day == $event_debut[0]))
				return true;
		}
		return false;
	}

	protected function dayHasEvents($date, $events) {
		//TODO au prochain refactoring, si on construit un dictionnaire
		// jour -> [articles *], ça simplifie pas mal de code.
		// Pour construire le calendrier, il suffira de parcourir
		// le dictionnaire et virer tout ce code bizarre.
		$day = (int)gmdate("j", $date);
		$month = (int)gmdate("m", $date);

		foreach($events as $event) {
			$event_debut = $this->filtreDateZend($event->getEventsDebut());
			$event_fin = $this->filtreDateZend($event->getEventsFin());

			// Jour clickable
			if($event_debut[1] == $month && $event_fin[1] == $month) {
				if($day >= $event_debut[0] && $day <= $event_fin[0])
					return true;
			}

			elseif($event_debut[1] == $month && $event_fin[1] != $month) {
				if($day >= $event_debut[0] && $day <= 31)
					return true;
			}

			elseif($event_debut[1] != $month && $event_fin[1] == $month) {
				if($day >= 1 && $day <= $event_fin[0])
					return true;
			}

			elseif($event_debut[1] <= $month && $event_fin[1] >= $month) {
				if($day >= 1 && $day <= 31)
					return true;
			}
		}
		return false;
	}


	public function rendHTML() {
		$html = $this->rendSelectionCategories();

		$html.="<div><table class=\"calendar_main\">";
		$html.="	<tr class=\"calendar_title\">";
		$html.="		<td class=\"calendar_title_left_arrow\"></td>";
		$html.="		<td class=\"calendar_title_month\">
										<a href=\"".$this->getURL("LAST_MONTH")."\" class=\"calendar_title_month_clickable\">&laquo;&nbsp;</a>
										<a href=\"".$this->getURL("MONTH")."\" class=\"calendar_title_month_clickable\" target='_parent'>".$this->MONTHS[$this->month-1].strftime(" %Y", mktime(5,0,0, $this->month, 1, $this->year))."</a>
										<a href=\"".$this->getURL("NEXT_MONTH")."\" class=\"calendar_title_month_clickable\">&nbsp;&raquo;</a></td>";

		$html.="		<td class=\"calendar_title_right_arrow\"></td>";
		$html.="	</tr>";
		$html.="	<tr>";
		$html.="		<td colspan=\"3\">";
		$html.="			<table class=\"calendar_table\"	 cellpadding='1' cellspacing='1'>";
		$html.="				<tr>";

		///////////////////////////////////////////////////////////////////////////
		// HTML - Nom des jours
		///////////////////////////////////////////////////////////////////////////
		for ($counter = 0; $counter < 7; $counter++) {
			$html.="					<th>".$this->WEEK_DAYS[($this->FIRST_WEEK_DAY + $counter) % 7]."</th>";
		}
		$html.="				</tr>";


		$first_month_day = gmmktime(0, 0, 0, $this->month, 1, $this->year);
		$offset = (7 - ($this->FIRST_WEEK_DAY % 7 - gmdate("w", $first_month_day))) % 7;
		$current_day = $first_month_day - 3600 * 24 * $offset;
		$row_number = ceil((gmdate("t", $first_month_day) + $offset) / 7);

		$articles = $this->_getArticles();

		///////////////////////////////////////////////////////////////////////////
		// HTML - No des jours
		///////////////////////////////////////////////////////////////////////////
		for ($row = 1; $row <= $row_number; $row++) {
			// The first loop displays the rows
			$html.="				<tr>";

			for ($column = 1; $column <= 7; $column++) {
				// Day currently displayed
				$day = gmdate("j", $current_day);

				// If it is saturday or sunday, we use the "weekend" style
				if (gmdate("w", $current_day) == 6 || gmdate("w", $current_day) == 0) {
					$table_cell = "					<td class=\"calendar_weekend\">";
				}
				else {
					$table_cell = "					<td>";
				}

				// We display the current day
				$day_classes = array();

				if (gmdate("dmY", $current_day) == $this->today) {
					$day_classes []= "calendar_today_clickable";
					$today_click = '<b>'.$day.'</b>';
				} else {
					$today_click=$day;
					if (gmdate("n", $current_day) != $this->month) {
						$day_classes []= "calendar_other_month";
					}	else {
						$day_classes []= "calendar_day_non_clickable";
					}
				}
				///////////////////////////////////////////////////////////////////////////
				// HTML - News
				///////////////////////////////////////////////////////////////////////////
				if ($this->dayHasEvents($current_day, $articles))	$day_classes []= "day_clickable";
				if ($this->anEventStartThisDay($current_day, $articles)) $day_classes []= "calendar_day_event_start";

				$cell_classes = implode(' ', array_unique($day_classes));
				if (in_array('day_clickable', $day_classes)) {
					$table_cell .= "<a href=\"".$this->getURL('EVENTS',$day)."\" class='".$cell_classes."' target='_parent'>".$today_click."</a>";
				}	else {
					$table_cell .= "<span class=\"".$cell_classes."\">".$day."</span>";
				}

				// End of day cell
				$html.=$table_cell."</td>";
				// Next day
				$current_day += 3600 * 24 + 1;
			}
			$html.="				</tr>";
		}

		$html.="			</table>";
		$html.="		</td>";
		$html.="	</tr>";
		$html.="</table></div>";

		// Html sous le calendrier
		$html .= $this->htmlForEvents($articles);


		return sprintf("<div class='calendar'>%s</div>", $html);
	}



	protected function htmlForEvents($articles) {
		if ($this->param['DISPLAY_NEXT_EVENT']=='0')
			return '';

		$nb_news = $this->param["NB_NEWS"];
		$news_array = array_slice($articles, 0, $nb_news);

		$news_html = sprintf(
			"<div class='calendar_event_list'>".
				"<p align='center' style='font-size:13px;'>".
					"<b>%s</b>".
			"</p>", $this->_translate->_('Prochains rendez-vous'));

		$news_li_html = '';
		foreach($news_array as $news) {
			// Information à afficher: Catégorie ou Bibliothèque
			$event_info = $this->_getArticleEventInfo($news);

			// Filtre titre
			$libelle = $news->getTitre();
			$news_li_html.='<li>'.$this->formateDateZend($news).' '.
				$event_info.
				'<br />'.
				'<a class="calendar_event_title" href="'.BASE_URL.'/opac/cms/articleview/id/'.$news->getId().'" target="_parent">'.$libelle.'</a></li>';

		}
		if ($news_li_html)
			$news_li_html = sprintf("<ul>%s</ul>", $news_li_html);
		$news_html.=$news_li_html."</div>";

		return $news_html;
	}


	// Pour l'affichage
	function formateDateZend($news) {
		if (!isset($this->_article_event_helper)) {
			$this->_article_event_helper = new ZendAfi_View_Helper_TagArticleEvent();
			$this->_article_event_helper->setView(new ZendAfi_Controller_Action_Helper_View());
		}
		return $this->_article_event_helper->tagArticleEvent($news);
	}


	function getURL($type,$jour = "") {
		switch($type) {
			case "LAST_MONTH" :
				$url = BASE_URL.'/cms/calendar?date='.$this->getLastMonth($this->month, $this->year);
				break;

			case "MONTH" :
				if(strlen($this->month) == 1) $mois='0'.$this->month; else $mois = $this->month;
				$url = BASE_URL."/cms/articleviewbydate?d=".$this->year.'-'.$mois;
				break;

			case "NEXT_MONTH" :
				$url = BASE_URL.'/cms/calendar?date='.$this->getNextMonth($this->month, $this->year);
				break;

			case "EVENTS" :
				if(strlen($this->month) == 1) $mois='0'.$this->month; else $mois = $this->month;
				if(strlen($jour) == 1) $day='0'.$jour; else $day = $jour;
				$url = BASE_URL."/cms/articleviewbydate?d=".$this->year.'-'.$mois.'-'.$day;
				break;
		}
		return $url."&amp;".http_build_query(array(
																							 'id_module' => $this->id_module,
																							 'id_profil' => Class_Profil::getCurrentProfil()->getId(),
																							 'select_id_categorie' => $this->param["SELECT_ID_CAT"]));
	}

	function getLastMonth($month, $year) {
		if ($month == 1) {
			$new_month = "12";
			$new_year	 = $year - 1;
		} else {
			$new_month = (($month > 10)?"":"0").($month - 1);
			$new_year	 = $year;
		}

		return $new_year.'-'.$new_month;
	}

	function getNextMonth($month, $year) {
		if ($month == 12) {
			$new_month = "01";
			$new_year	 = $year + 1;
		} else {
			$new_month = (($month < 9)?"0":"").($month + 1);
			$new_year	 = $year;
		}

		return $new_year.'-'.$new_month;
	}

	// Rend array('0' => jour, '1' => mois, '2' => an) sans 0 pour le calendrier
	function filtreDateZend($zend) {
		$date_zend = explode('-',$zend);
		if (substr($date_zend[2],0,1) == 0)
			$day = substr($date_zend[2],1,1);
		else
			$day = substr($date_zend[2],0,2);

		if (substr($date_zend[1],0,1) == 0)
			$mois = substr($date_zend[1],1,2);
		else
			$mois = $date_zend[1];
		$date_events[0] = $day;
		$date_events[1] = $mois;
		$date_events[2] = $date_zend[0];
		return($date_events);
	}
}

?>
