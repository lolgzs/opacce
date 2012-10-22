<?PHP
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
 * exemple des possibilités:
 *
 * $all_nl = Class_Newsletter::getLoader()->findAll();
 *
 * $nl = Class_Newsletter::getLoader()->find(3);
 * $nl->getTitre();
 * $nl->setContenu('youpi');
 * $nl->save();
 * $nl->delete();
 *
 * $nl = new Class_Newsletter();
 * $nl->setTitre('Nouveautés');
 * $nl->save();
 *
 * Mocking pour les tests:
 *
 * $mock_row =  new Zend_Db_Table_Rowset(
 *                           array('data' => array('titre' => 'test',
 *                                                  'contenu' => 'enfin des tests')));
 * $mock_tbl = this->getMock('MockTable', array('find'));
 * $mock_tbl
 *   ->expects($this->once())
 *   ->metchod('find')
 *   ->with(3)
 *   ->will($this->return($mock_row))
 *
 * Class_Newsletter::getLoader()->setTable($mock_tbl);
 *
 * $nl = Class_Newsletter::getLoader()->find(3);
 * $this->assertEquals('test', $nl->getTitre());
 *
 */

class Class_Newsletter extends Storm_Model_Abstract {
	protected $_table_name = 'newsletters';
	protected $_has_many = ['subscriptions' => ['model' => 'Class_NewsletterSubscription',
																							'role' => 'newsletter',
																							'dependents' => 'delete'],
													'users' => ['through' => 'subscriptions',
																			'unique' => true]];
	protected $_notices_finder;

	public function send() {
		$this
			->generateMail()
			->send();

		$this
			->setLastDistributionDate(strftime("%Y-%m-%d %H:%M:%S"))
			->save();
	}


	public function sendTo($destinataire) {
		$mail = $this->_newMail();
		$mail->addTo($destinataire);
		$mail->send();
	}


	public function getExpediteur() {
		if ($this->isAttributeEmpty('expediteur'))
			return $this->_getMailPortail();
		return $this->_get('expediteur');
	}


	protected function _newMail() {
		$notices = $this->getNotices();

		$mail = new ZendAfi_Mail('utf8');
		$mail
			->setSubject($this->getTitre())
			->setBodyText($this->_getBodyText($notices))
			->setBodyHTML($this->_getBodyHTML($notices))
			->setFrom($this->getExpediteur());
		return $mail;
	}


	public function generateMail() {
		$mail = $this->_newMail();
		$mail->addTo($this->getExpediteur());

		$users = $this->getUsers();
		foreach ($users as $user) {
			if ($recipient = $user->getMail())
				$mail->addBcc($recipient);
		}

		return $mail;
	}


	protected function _getMailPortail() {
		$portail = Class_Profil::getLoader()->find(1);
		return $portail->getMailSite();
	}


	public function getNotices() {
		if (!$this->getIdPanier() and !$this->getIdCatalogue())
			return array();

		$preferences = array( 'id_catalogue' => $this->getIdCatalogue(),
													'id_panier' => $this->getIdPanier(),
													'nb_notices' => $this->getNbNotices(),
													'only_img' => false,
													'aleatoire' => 0,
													'tri' => 1);
		return Class_Notice::getLoader()->getNoticesFromPreferences($preferences);
	}


	protected function _htmlToText($html) {
		return strip_tags(preg_replace('/<br[^>]*>/i', "\n", $html));
	}



	protected function _getBodyText($notices) {
		$lines = array($this->_htmlToText($this->getContenu()));

		foreach($notices as $notice) {
			$url_notice = sprintf('http://%s/recherche/viewnotice/id/%d',
                            $_SERVER['SERVER_NAME'].BASE_URL,
														$notice->getId());

			$lines []= '- '.$this->_getTitleForNotice($notice);
			$lines []= $notice->getResume();
			$lines []= "Lien: $url_notice";
			$lines []= "\n";

		}

		return implode("\n", $lines);
	}

	/**
	 * @param array $notices
	 * @return string
	 */
	protected function _getBodyHTML($notices) {
		$view = new ZendAfi_Controller_Action_Helper_View();

		$html = $this->getContenu();

		foreach($notices as $notice) {
			$title = $this->_getTitleForNotice($notice);
			$vignette = $notice->getUrlVignette();
			$resume = $notice->getResume();

			$anchor_notice = $view->getHelper('tagAnchor')->baseURL(
					$view->tagImg(
							$vignette,
							array('style' => 'float:left;width:50px;vertical-align:top;padding:5px',
											'alt' => 'vignette')
					) . $title,
					'recherche',
					'viewnotice',
					array('id' => $notice->getId())
			);

			$html.=
				'<div style="padding:5px">' .
				  $anchor_notice .
				  '<div>' . $resume . '</div>' .
				  '<div style="clear:both"></div>'.
				'</div>';
		}

		return $html;
		
	}


	protected function _getTitleForNotice($notice) {
		$title = $notice->getTitrePrincipal();

		$infos = array();
		if ($auteur = $notice->getAuteurPrincipal()) $infos []= $auteur;
		if ($annee = $notice->getAnnee())	$infos []= $annee;
		if (!empty($infos))	$title .= ' ('.implode(', ', $infos).')';

		return $title;
	}

}

?>