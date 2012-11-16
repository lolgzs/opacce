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

class ArticleLoader extends Storm_Model_Loader {
	/** @var Zend_Db_Table_Select */
	protected $_select;
	protected $_sort_order;
	protected $_nb_analyse;
	protected $_nb_aff;
	protected $_id_articles;
	protected $_id_categories;
	protected $_limit;
	/** @var int */
	protected $_status;
	/** @var bool */
	protected $_events_only;
	/** @var bool */
	protected $_published;

	/**
	 * @return ArticleLoader
	 */
	protected function _selectArticles() {
		$this->_select = $this
			->getTable()
			->select('cms_article.*')
			->setIntegrityCheck(false)
			->from('cms_article');

		return $this;
	}


	protected function _publishedNow() {
		if (!$this->_published)
			return $this;

		$this->_select
			->where('(DEBUT IS NULL) OR (DEBUT <= CURDATE())')
			->where('(FIN IS NULL) OR (FIN >= CURDATE())');

		return $this;
	}


	/**
	 * @param array $id_categories
	 * @return array
	 */
	protected function _mergeCategorieIdsWithSousCategories($id_categories) {
		$all_cat_ids = array();
		foreach($id_categories as $id_cat) {
			if (!$root_cat = Class_ArticleCategorie::getLoader()->find($id_cat))
				continue;

			$all_cat_ids []= $id_cat;

			$sub_cats = $root_cat->getRecursiveSousCategories();
			foreach ($sub_cats as $cat)
				$all_cat_ids []= $cat->getId();
		}

		return array_unique($all_cat_ids);
	}


	/**
	 * @param array $id_articles
	 * @param array $id_categories
	 * @return ArticleLoader
	 */
	protected function _whereSelectionIn($id_articles, $id_categories) {
		$conditions = array();

		if ($id_articles)
			$conditions[] = sprintf('%s in (%s)',
															$this->getIdField(),
															implode(',', $id_articles));

		if ($id_categories)
			$conditions[] = sprintf('`cms_article`.ID_CAT in (%s)',
															implode(',', $id_categories));

		if ($conditions)
			$this->_select->where(implode(' OR ', $conditions));

		return $this;
	}


	/**
	 * @param string $event_date
	 * @return ArticleLoader
	 */
	protected function _whereEventDateIn($event_date) {
		if ($this->_events_only) {
			$this->_select->where('EVENTS_DEBUT IS NOT NULL');
			$this->_select->where('EVENTS_FIN IS NOT NULL');
		}

		if (
			!$event_date
			|| (7 > strlen($event_date))
		) {
			return $this;
		}

		if (10 == strlen($event_date)) {
			$this->_select->where('EVENTS_DEBUT <= ?', $event_date);
			$this->_select->where('EVENTS_FIN >= ?', $event_date);

		} else {
			$this->_select->where('left(EVENTS_DEBUT,7) <= ?', $event_date);
			$this->_select->where('left(EVENTS_FIN,7) >= ?', $event_date);

		}

		return $this;
	}


	/**
	 * @param string $event_date
	 * @return ArticleLoader
	 */
	protected function _whereEventStartAfter($event_date) {
		if (!$event_date || (7 > strlen($event_date)))
			return $this;

		$this->_select->where('EVENTS_DEBUT IS NOT NULL');
		$this->_select->where('EVENTS_FIN IS NOT NULL');

		$field = (10 == strlen($event_date)) ? 'EVENTS_DEBUT' : 'left(EVENTS_DEBUT,7)';
		$this->_select->where("$field > ?", $event_date);

		return $this;
	}


	/**
	 * @return ArticleLoader
	 */
	protected function _orderAndLimit() {
		if (!$this->_has_selection) {
			$this->_select->order('DATE_CREATION DESC');
			$this->_select->limit($this->_limit);

		} else {
			if ($this->_id_categories)
				$this->_select->order(sprintf("FIELD(`cms_article`.ID_CAT, %s)", implode(',', $this->_id_categories)));

			if ($this->_id_articles)
				$this->_select->order(sprintf("FIELD(ID_ARTICLE, %s)", implode(',', $this->_id_articles)));

		}

		return $this;
	}


	/**
	 * @return ArticleLoader
	 */
	protected function _filterByLangue() {
		if (!$this->_has_selection) {
			if ($this->_langue) {
				$this->_select->where('LANGUE=?', $this->_langue);
			} else {
				$this->_select->where('PARENT_ID=?', 0);
			}
		}

		return $this;
	}


	/**
	 * @return Zend_Db_Table_Select
	 */
	protected function _getSelect() {
		return $this->_select;
	}

	/**
	 * @param array $articles
	 * @return array
	 */
	protected function _sortArticles($articles) {
		if ($this->_sort_order == 'Random') {
			shuffle($articles);
		} else {
			$sort_function = 'sortBy'.$this->_sort_order;
			if (method_exists('Class_Article', $sort_function))
				usort($articles, 'Class_Article::'.$sort_function);
		}

		return $articles;
	}


	/**
	 * @param array $preferences
	 * @return array
	 */
	protected function _byIdBib($id_bib) {
		if ((0 === $id_bib) or (null === $id_bib))
			return $this;

		$this->_select
			->join('cms_categorie',
						 'cms_categorie.ID_CAT = cms_article.ID_CAT',
						 array())
			->where('cms_categorie.ID_SITE=?', $id_bib);
		return $this;
	}


	/**
	 * @return ArticleLoader
	 */
	protected function _filterByStatus() {
		if (null === $this->_status) {
			return $this;
		}

		$this->_select->where('STATUS = ?', $this->_status);

		return $this;
	}


	/**
	 * @param array $preferences
	 * @return array
	 */
	public function getArticlesByPreferences($preferences) {
		$defaults = array(
				'id_categorie' => '', // catégories d'article, ex: 12-2-8-1-89
				'id_items' => '', // liste d'articles, ex: 39-28-7
				'display_order' => '', // tri, cf. méthodes Class_Article::sortByXXX, Random, Selection
				'nb_analyse' => 0, // afficher nb_aff articles (aléatoires) parmis nb_analyse articles ramenés sur un critère
				'nb_aff' => null, // nb d'article à retourner
				'langue' => null, // que les traductions de cette langue
				'event_date' => null, // que les articles dont les dates de début et/ou de fin inclue cette date
				'event_start_after' => null, // que les articles dont l'évènement commence après cette date
				'id_bib' => null, // filtre par cette bibliothèque
				'status' => null, // filtre par cet état de workflow cf. Class_Article::STATUS_XXX
				'events_only' => false, // filtre que les évènements,
				'published' => true, // seulement les articles dont les date de debut / fin incluent le jour en cours
		);

		$preferences = array_merge($defaults, $preferences);

		$this->_sort_order = $preferences['display_order'];
		$this->_nb_aff = (int)$preferences['nb_aff'];
		$this->_nb_analyse = (int)$preferences['nb_analyse'];
		$this->_id_articles = array_filter(explode('-', $preferences['id_items']));
		$this->_id_categories = $this->_mergeCategorieIdsWithSousCategories(array_filter(explode('-', $preferences['id_categorie'])));
		$this->_has_selection = (count($this->_id_articles)>0 or count($this->_id_categories)>0);
		$this->_limit = $this->_sort_order == 'Random' ? $this->_nb_analyse : $this->_nb_aff;
		$this->_langue = $preferences['langue'];
		$this->_event_date = $preferences['event_date'];
		$this->_event_start_after = $preferences['event_start_after'];
		$this->_id_bib = $preferences['id_bib'];
		$this->_status = $preferences['status'];
		$this->_events_only = (bool)$preferences['events_only'];
		$this->_published = (bool)$preferences['published'];


		$select = $this
			->_selectArticles()
			->_publishedNow()
			->_byIdBib($this->_id_bib)
			->_whereSelectionIn($this->_id_articles, $this->_id_categories)
			->_whereEventDateIn($this->_event_date)
			->_whereEventStartAfter($this->_event_start_after)
			->_filterByLangue()
			->_filterByStatus()
			->_orderAndLimit()
			->_getSelect();


		$articles = $this->_sortArticles($this->findAll($select));
		if (
			($this->_sort_order == 'Selection')
			or !$this->_nb_aff
		)
			return $articles;

		return array_slice($articles, 0, $this->_nb_aff);
	}


	/**
	 * @param array $articles
	 * @return array
	 */
	public static function groupByBib(array $articles) {
		$grouped = array();

		foreach ($articles as $article) {
			$label = ($bib = $article->getBib())
									? $bib->getLibelle()
									: '';

			if (array_key_exists($label, $grouped)) {
				$grouped[$label][] = $article;
			} else {
				$grouped[$label] = array($article);
			}

		}

		return $grouped;
	}


	/**
	 * @param array $articles
	 * @return array
	 */
	public function groupByLibelleCategorie(array $articles) {
		$grouped = array();

		foreach ($articles as $article) {
			$libelle_cat = $article->getCategorie()->getLibelle();

			if (!array_key_exists($libelle_cat, $grouped))
				$grouped[$libelle_cat] = array();

			$grouped[$libelle_cat][] = $article;
		}

		return $grouped;
	}


	/**
	 * Retourne les traductions des articles valides et pour la langue courant
	 * @param array
	 * @return array
	 */
	public function filterByLocaleAndWorkflow($articles) {
		return Class_Article::filterByLocaleAndWorkflow($articles);
	}
}


class Class_Article extends Storm_Model_Abstract {
	const END_TAG='{FIN}';

	const STATUS_DRAFT = 1;
	const STATUS_VALIDATION_PENDING = 2;
	const STATUS_VALIDATED = 3;
	const STATUS_ARCHIVED = 4;
	const TITLE_MAX_LENGTH = 200;

	/**
	 * @var array
	 */
	protected static $_knownStatus = array(
		self::STATUS_DRAFT => 'Brouillon',
		self::STATUS_VALIDATION_PENDING => 'À valider',
		self::STATUS_VALIDATED => 'Validé',
		self::STATUS_ARCHIVED => 'Archivé',
	);

	protected $_loader_class = 'ArticleLoader';
	protected $_table_name = 'cms_article';
	protected $_table_primary = 'ID_ARTICLE';

	protected $_has_many = ['traductions' => ['model' => 'Class_Article',
																						'role' => 'article_original',
																						'dependents' => 'delete'],

													'avis_users' => ['model' => 'Class_Avis',
																					 'role' => 'article',
																					 'dependents' => 'delete',
																					 'order' => 'date_avis desc'],

													'formulaires' => ['model' => 'Class_Formulaire',
																						'role' => 'article',
																						'order' => 'date_creation desc']
	];

	protected $_belongs_to = ['categorie' => ['model' => 'Class_ArticleCategorie',
																						'referenced_in' => 'id_cat'],

														'article_original' => ['model' => 'Class_Article',
																									 'referenced_in' => 'parent_id'],

														'bib' => ['through' => 'categorie'],

														'lieu' => ['model' => 'Class_Lieu',
																			 'referenced_in' => 'id_lieu'] ];


	protected $_overrided_attributes = ['id',
																			'parent_id',
																			'article_original',
																			'langue',
																			'titre',
																			'description',
																			'contenu'];

	protected $_default_attribute_values = [
																					'titre' => '',
																					'description' => '',
																					'contenu' => '',
																					'debut' => '',
																					'fin' => '',
																					'avis' => false,
																					'tags' => '',
																					'events_debut' => '',
																					'events_fin' => '',
																					'indexation' => 1,
																					'cacher_titre' => 0,
																					'date_maj' => '',
																					'date_creation' => '',
																					'status' => self::STATUS_DRAFT,
																					'id_lieu' => 0
																				];

	/**
	 * @return ArticleLoader
	 */
	public static function getLoader() {
		return self::getLoaderFor(__CLASS__);
	}


	/**
	 * Ne retourne que les traductions des articles donnés
	 * pour la langue courante.
	 * @param array
	 * @return array
	 */
	public static function getTraductionsCurrentLocale($originaux) {
		if (!Class_AdminVar::isTranslationEnabled())
			return $originaux;

		$langue = Zend_Registry::get('translate')->getLocale();
		$traductions = array();

		foreach($originaux as $article) {
			if ($article->isTraductionExists($langue))
				$traductions[] = $article->getTraductionLangue($langue);
		}

		return $traductions;
	}


	/**
	 * Ne retourne que les articles valides (si workflow actif)
	 * @param array
	 * @return array
	 */
	public  static function filterByWorkflow($articles) {
		if (!Class_AdminVar::isWorkflowEnabled())
			return $articles;

		return array_filter($articles, array(__CLASS__, 'isStatusValidated'));
	}


	/**
	 *
	 * @param Class_Article $article
	 * @return boolean true si l'article a le status validé
	 */
	public static function isStatusValidated($article) {
		$validated = ($article->getStatus() == self::STATUS_VALIDATED);
		return $validated;
	}


	/**
	 * Retourne les traductions des articles valides et pour la langue courant
	 * @param array
	 * @return array
	 */
	public static function filterByLocaleAndWorkflow($articles) {
		return self::filterByWorkflow(self::getTraductionsCurrentLocale($articles));
	}


	public static function sortByEventDebut($article1, $article2) {
		if (0 === $evt_cmp = strcmp($article1->getEventsDebut(), $article2->getEventsDebut()))
				return strcmp($article1->getEventsFin(), $article2->getEventsFin());
		return $evt_cmp;
	}


	public static function sortByDebutPublicationDesc($article1, $article2) {
		return strcmp($article2->getDebut(), $article1->getDebut());
	}


	public static function sortByDateCreationDesc($article1, $article2) {
		return strcmp($article2->getDateCreation(), $article1->getDateCreation());
	}


	public function __construct() {
		$this->_default_attribute_values['langue'] = Class_AdminVar::getDefaultLanguage();
	}

	/**
	 * @return string
	 */
	public function getLangue() {
		if (!$langue = parent::_get('langue'))
			$langue = $this->getDefaultValueForAttribute('langue');
		return $langue;
	}

	/**
	 * @param string $langue
	 * @return bool
	 */
	public function isLangue($langue) {
		return strtolower($this->getLangue()) == strtolower($langue);
	}

	/**
	 * @param string $langue
	 * @return Class_Article
	 */
	public function getTraductionLangue($langue) {
		if ($original = $this->getArticleOriginal())
			return $original->getTraductionLangue($langue);

		$traductions = $this->getTraductions();

		foreach ($traductions as $traduction) {
			if ($traduction->isLangue($langue))
				return $traduction;
		}

		return $this;
	}

	/**
	 * @param string $langue
	 * @return bool
	 */
	public function isTraductionExists($langue) {
		return ($langue == $this->getTraductionLangue($langue)->getLangue());
	}

	/**
	 * @param string $langue
	 * @return Class_Article
	 */
	public function getOrCreateTraductionLangue($langue) {
		if ($this->isTraductionExists($langue))
			return $this->getTraductionLangue($langue);

		$original = $this->isTraduction() ? $this->getArticleOriginal() : $this;

		return $this->getLoader()
			->newInstance()
			->setArticleOriginal($original)
			->setLangue($langue)
			->setTitre($original->getTitre())
			->setDescription($original->getDescription())
			->setContenu($original->getContenu());
	}

	/**
	 * @return bool
	 */
	public function isTraduction() {
		$is_trad = $this->hasArticleOriginal();
		return $is_trad;
	}

	/**
	 * @param string $field
	 * @return bool
	 */
	public function shouldOverrideAttribute($field) {
		return in_array($field, $this->_overrided_attributes);
	}

	/**
	 * Prends l'attribut de l'article original s'il y'en a un
	 *
	 * @param string $field
	 * @return mixed
	 */
	public function _get($field) {
		if ($this->shouldOverrideAttribute($field))
			return parent::_get($field);

		if ($this->isTraduction())
			return $this->getArticleOriginal()->_get($field);

		return parent::_get($field);
	}

	/**
	 * @param string $field
	 * @param mixed $value
	 * @return Class_Article
	 */
	public function _set($field, $value) {
		if ($this->shouldOverrideAttribute($field))
			return parent::_set($field, $value);

		if ($this->isTraduction()) {
			return $this->getArticleOriginal()->_set($field, $value);
		} else {
			return parent::_set($field, $value);
		}
	}


	protected function _updateDateMaj() {
		$date = new Class_Date();
		$this->setDateMaj($date->DateTimeDuJour());
	}


	public function beforeSave() {
		$this->_updateDateMaj();
		if ($this->isNew())
			$this->setDateCreation($this->getDateMaj());
	}

	/**
	 * @param string $str_date
	 * @return bool
	 */
	public function isValidDate($str_date) {
		if (empty($str_date))
			return true;

		try {
			new Zend_Date($str_date, Zend_Date::ISO_8601);
			return true;
		} catch(Exception $e) {
			return (strptime($str_date, '%d/%m/%Y') != 0);
		}
		return false;
	}

	/**
	 * @return Class_Article
	 */
	public function validate() {
		$this->check(Class_Date::isEndDateAfterStartDate($this->getDebut(), $this->getFin()),
								 "La date de début de publication doit être plus récente que la date de fin");

		$this->check(Class_Date::isEndDateAfterStartDate($this->getEventsDebut(), $this->getEventsFin()),
								 "La date de début d'évènement doit être plus récente que la date de fin");

		$this->check($this->getTitre(), "Vous devez compléter le champ 'Titre'");

		$this->check(strlen_utf8($this->getTitre()) <= self::TITLE_MAX_LENGTH,
								 sprintf("Le champ 'Titre' doit être inférieur à %d caractères", 
												 self::TITLE_MAX_LENGTH));

		$this->check($this->getContenu(), "Vous devez compléter le champ 'Contenu'");


		$this->check($this->isValidDate($this->getDebut()), "La date de 'Début' n'est pas valide");
		$this->check($this->isValidDate($this->getFin()), "La date de 'Fin' n'est pas valide");
		$this->check($this->isValidDate($this->getEventsDebut()), "La date de 'Début évènement' n'est pas valide");
		$this->check($this->isValidDate($this->getEventsFin()), "La date de 'Fin évènement' n'est pas valide");
		return $this;
	}

	/**
	 * @return bool
	 */
	public function hasSummary() {
		return
			(strpos($this->getContenu(), self::END_TAG) !== false)
			|| $this->hasDescription();
	}

	/**
	 * @return string
	 */
	public function getSummary() {
		if (!$this->hasSummary())
			return $this->contenu;

		if ($this->hasDescription())
			return $this->description;

		$head_and_content = explode(self::END_TAG, $this->getContenu());
		return (trim($head_and_content[0]));
	}


	/**
	 * @return string
	 */
	public function getFullContent() {
		$content = str_replace(self::END_TAG, '', $this->getContenu());

		if ($this->hasDescription() && $content=='')
			$content = $this->getDescription();

		return $content;
	}


	/**
	 * @return string
	 */
	public function getContenu() {
		$contenu = parent::_get('contenu');

		if (preg_match('/(<form[^>]+)action=[\"\']http/', $contenu))
			return $contenu;

		$replaced_form = preg_replace(['/(<form[^>]+)action=[\"\'][^\"\']+\"? /',
																	 '/(<form )/'],
																	['$1 ', 
																	 '$1action="'.BASE_URL.'/formulaire/add/id_article/'.$this->getId().'" '],
																	$contenu);

		$typesubmit = 'type=[\'\"](?:submit|button)[\'\"]';
		$namesubmit = 'name=[\"\'][^\"\']+[\'\"]';
		$otherattributes = '[^>]+';
		$inputtag = '<input';
		return preg_replace([ '/('.$inputtag.$otherattributes.')('.$typesubmit.$otherattributes.')'.$namesubmit.'/',
													'/('.$inputtag.$otherattributes.')'.$namesubmit.'('.$otherattributes.$typesubmit.')/',
													'/('.$inputtag.$otherattributes.')'.$typesubmit.'('.$otherattributes.')\/>/' ],
												[ '$1$2',
													'$1$2',
													'$1$2type="submit"/>'],
												$replaced_form);
												
	}


	/**
	 * @return string
	 */
	public function getUrl() {
		return array('controller' => 'cms',
								  'action' => 'articleview',
								  'id' => $this->getId());
		/* return sprintf('%s/cms/articleview/id/%s', */
		/* 							 BASE_URL, */
		/* 							 $this->getId()); */
	}

	/**
	 * @return string
	 */
	public function getFirstImageURL() {
		$matches = array();
		if (preg_match('/< *img[^>]*src *= *["\']?([^"\'>]*)/i', $this->getFullContent(), $matches) > 0)
			return $matches[1];

		return '';
	}

	/**
	 * @param int $value
	 * @return Class_Article
	 */
	public function setStatus($value) {
		if (
			Class_AdminVar::isWorkflowEnabled()
			&& (array_key_exists((int)$value, self::$_knownStatus))
		) {
			return $this->_set('status', (int)$value);
		}

		return $this;
	}


	/**
	 * @return bool
	 */
	public function isVisible() {
		return ($this->isVisibleByDates() && $this->isVisibleByWorkflow());

	}

	/**
	 * @return bool
	 */
	public function isNotVisible() {
		return !$this->isVisible();
	}

	/**
	 * @return bool
	 */
	public function isVisibleByWorkflow() {
		return (self::STATUS_VALIDATED == $this->getStatus());
	}

	/**
	 * @return bool
	 */
	public function isVisibleByDates() {
		$start = (string)$this->getDebut();
		$end = (string)$this->getFin();

		if (('' == $start) && ('' == $end)) {
			return true;
		}

		$now = time();

		$start = ('' == $start) ? $now : strtotime($start);
		$end = ('' == $end) ? $now : strtotime($end);

		return (($now >= $start) && ($now <= $end));
	}

	public function beVisible() {
		$date = new DateTime();

		$this->setDebut($date->format('Y-m-d'))
					->setFin(null)
					->save();
	}

	public function beInvisible() {
		$date = new DateTime();
		$date->modify('-1 day');

		$this->setDebut(null)
					->setFin($date->format('Y-m-d'))
					->save();
	}

	/**
	 * @return Class_Article
	 */
	public function beDraft() {
		$this->setStatus(self::STATUS_DRAFT);
		return $this;
	}

	/**
	 * @return Class_Article
	 */
	public function beValidationPending() {
		$this->setStatus(self::STATUS_VALIDATION_PENDING);
		return $this;
	}

	/**
	 * @return Class_Article
	 */
	public function beValidated() {
		$this->setStatus(self::STATUS_VALIDATED);
		return $this;
	}

	/**
	 * @return Class_Article
	 */
	public function beArchived() {
		$this->setStatus(self::STATUS_ARCHIVED);
		return $this;
	}


	/**
	 * @return array
	 */
	public static function getKnownStatus() {
		return self::$_knownStatus;
	}


	/**
	 * @return int
	 */
	public function getStatus() {
		if (!Class_AdminVar::isWorkflowEnabled()) {
			return self::STATUS_VALIDATED;
		}

		return $this->_get('status');
	}


	/**
	 * @return String
	 */
	public function toJSON() {
		return json_encode(array("id" => $this->getId(),
														 "label" => htmlspecialchars($this->getTitre())));
	}


	/**
	 * @return String
	 */
	public function getBibLibelle() {
		return $this->getCategorie()->getBib()->getLibelle();
	}


	public function getRank() {
		return Class_CmsRank::getLoader()->findFirstBy(array('id_cms' => $this->getId()));
	}
}


?>