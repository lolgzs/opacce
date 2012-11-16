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
class Class_Moderer {
	private $_session = null;	// Session
	private $sql;
	private $_moderation_avis;

	public function __construct() {
		$this->_session = Zend_Registry::get('session');
		$this->sql = Zend_Registry::get('sql');
	}


	public function getAllAvisCmsAModerer($abon_ou_bib = 0) {
		$sqlStmt = sprintf("Select * from cms_avis Where STATUT=0 AND ABON_OU_BIB=%d order by DATE_AVIS DESC",
											 $abon_ou_bib);
		return $this->sql->fetchAll($sqlStmt);
	}



	public function modererUserNonValid($action, $id_user) {
		if ('2' == $action)
			sqlExecute('delete from bib_admin_users_non_valid where ID_USER=' . $id_user);
	}


	public function modererAvisCms($action, $id_user, $id_news) {
		if ('1' == $action) 
			sqlExecute('update cms_avis set STATUT=1 where ID_USER=' . $id_user .' and ID_CMS=' . $id_news);
		elseif ('2' == $action)
			sqlExecute('delete from cms_avis where ID_USER=' . $id_user . ' and ID_CMS=' . $id_news);

		$avis = $this->sql->fetchAll('select * from cms_avis where ID_USER=' . $id_user . ' and ID_CMS='. $id_news);
		if (1 == $avis[0]['STATUT']) {
			$role_level = fetchOne('select ROLE_LEVEL from bib_admin_users where ID_USER=' . $id_user);
			$class_avis = new Class_Avis();
			$class_avis->maj_note_cms($id_news, (($role_level < 3) ? 0 : 1));
		}
	}


	public function getAllTagsAModerer() {
		$liste = fetchAll("select * from codif_tags where a_moderer > ''");
		if (empty($liste)) 
			return array();
		
		foreach ($liste as $tag) {
			$notices = explode(';', $tag['a_moderer']);
			foreach ($notices as $id_notice) {
				if (!$id_notice)
					continue;
				$r['id_tag'] = $tag['id_tag'];
				$r['libelle'] = $tag['libelle'];
				$r['id_notice'] = $id_notice;
				$tags[] = $r;
			}
		}
		return $tags;
	}


	public function modererTag($action, $id_tag, $id_notice)	{
		$cls_tag = new Class_TagNotice();

		if ('1' == $action) {
			$cls_tag->validerTagNotice($id_tag, $id_notice);
			return;
		}
				
		if ('2' == $action) {
			$cls_tag->supprimerTagNotice($id_tag, $id_notice);
			return;
		}
		
		return false;
	}

	
	public function getModerationStats() {
		$translate = Zend_Registry::get('translate');

		if (!isset($this->_moderation_stats)) {
			$moderations = ['avis_notices' => ['label' => $translate->_('Avis sur les notices'),
					                               'url' => BASE_URL . '/admin/modo/avisnotice',
					                               'count' => fetchOne('select count(*) from notices_avis where STATUT = 0')],
				              'avis_articles' => ['label' => $translate->_('Avis sur les articles'),
												                  'url' => BASE_URL . '/admin/modo/aviscms',
												                  'count' => fetchOne('select count(*) from cms_avis where STATUT = 0')],
				              'tags_notices' => ['label' => $translate->_('Tags sur les notices'),
												                 'url' => BASE_URL . '/admin/modo/tagnotice',
												                 'count' => fetchOne('select count(*) from codif_tags where a_moderer > \'\'')],
					            'demandes_inscription' => ['label' => $translate->_('Demandes d\'inscription'),
												                         'url' => BASE_URL . '/admin/modo/membreview',
												                         'count' => fetchOne('select count(*) from bib_admin_users_non_valid')],
					            'suggestions_achat' => ['label' => $translate->_('Suggestions d\'achat'),
										                          'url' => BASE_URL . '/admin/modo/suggestion-achat',
												                      'count' => Class_SuggestionAchat::count()],
			               ];
			if (Class_AdminVar::isCmsFormulairesEnabled()) {
				$moderations['formulaires'] =['label' => $translate->_('Formulaires'),
																			'url' => BASE_URL . '/admin/modo/formulaires',
																			'count' => Class_Formulaire::count()];
			}

			$this->_moderation_stats = $moderations;
		}

		return $this->_moderation_stats;
	}
}