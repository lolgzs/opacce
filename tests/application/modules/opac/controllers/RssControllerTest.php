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
require_once 'AbstractControllerTestCase.php';

class MockZendHttpClient extends Zend_Http_Client {
	public function request($method = null) {
		return new Zend_Http_Response(200, array(), RssFixtures::lemondeRSS());
	}
}

class RssControllerViewRawRssTest extends AbstractControllerTestCase {
	public function setUp() {
		parent::setUp();

		Class_Rss::getLoader()
			->newInstanceWithId(15)
			->setIdCat(11)
			->setIdNotice(86546)
			->setTitre('Le monde')
			->setDescription('A la Une')
			->setUrl('http://rss.lemonde.fr/c/205/f/3050/index.rss')
			->setDateMaj('2010-04-01 10:47:58');

		$preferences = array(
												 'boite'				=> '',
												 'titre'				=> 'Fils Rss',
												 'type_aff'			=> '1',
												 'id_categorie'	=> '',
												 'id_items'			=> '15',
												 'nb_aff'				=> '2' );

		Class_Profil::getLoader()
			->newInstanceWithId(25)
			->setCfgAccueil(array(
														'modules' => array(
																							 '1' => array(
																														'division' => '1',
																														'type_module' => 'RSS',
																														'preferences' => $preferences))));


		
		$this->old_http_client = Zend_Registry::get('httpClient');
		Zend_Registry::set('httpClient', new MockZendHttpClient());

		$this->dispatch('rss/view-raw-rss/id_rss/15/id_profil/25/id_module/1');
	}

	
	public function tearDown() {
		Zend_Registry::set('httpClient', $this->old_http_client);
		parent::tearDown();
	}

	
	/** @test */
	function titleShouldContainsBlogDelinquanceEtc() {
		$this->assertXPathContentContains('//div[@class="rss-title"]/a', 
																			utf8_encode('Blog - Délinquance des mineurs : le septième rapport en sept ans'),
																			$this->_response->getBody());
	}


	/** @test */
	function titleShouldNotContainsLePremierTrainSolaire() {
		$this->assertNotXPathContentContains('//div[@class="rss-title"]/a', 
																			'Le premier train solaire roule en Belgique',
																			$this->_response->getBody());
	}


	/** @test */
	function dateShouldContains() {
		$this->assertXPathContentContains('//div[@class="rss-date"]',
																		  '07-06-2011',
																			$this->_response->getBody());
	}
}



class RssFixtures {
	public static function lemondeRSS() {
		return 
			'<?xml version=\'1.0\' encoding=\'UTF-8\'?>
			 <?xml-stylesheet type=\'text/xsl\' href=\'http://rss.lemonde.fr/xsl/fr/rss.xsl\'?>
			 <rss xmlns:itunes="http://www.itunes.com/dtds/podcast-1.0.dtd" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:taxo="http://purl.org/rss/1.0/modules/taxonomy/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#" version="2.0">
				 <channel>
					 <title>Le Monde.fr : à la Une
					 </title>
					 <link>http://www.lemonde.fr
					 </link>
					 <description>Toute l\'actualité au moment de la connexion
					 </description>
					 <language>en
					 </language>
					 <copyright>Copyright Le Monde.fr
					 </copyright>
					 <pubDate>Tue, 07 Jun 2011 09:16:52 GMT
					 </pubDate>
					 <lastBuildDate>Tue, 07 Jun 2011 09:16:52 GMT
					 </lastBuildDate>
					 <ttl>15
					 </ttl>
					 <image>
						 <title />
						 <url>http://medias.lemonde.fr/mmpub/img/lgo/lemondefr_rss.gif
						 </url>
						 <link>http://www.lemonde.fr
						 </link>
					 </image>
					 <item>
						 <title>Hirsch a "six minutes pour sauver le RSA"
						 </title>
						 <link>http://bigbrowser.blog.lemonde.fr/2011/06/07/speed-dating-hirsch-a-six-minutes-pour-sauver-le-rsa/#xtor=RSS-32280322#xtor=RSS-3208
						 </link>
						 <description>"Six minutes, c\'est rapide sur un sujet qui concerne, aujourd\'hui, près de deux millions de foyers et qui fait l\'objet de tant de contre-vérités", déplore Martin Hirsch.&lt;img width=\'1\' height=\'1\' src=\'http://rss.lemonde.fr/c/205/f/3050/s/15b495be/mf.gif\' border=\'0\'/&gt;&lt;br/&gt;&lt;br/&gt;&lt;a href="http://da.feedsportal.com/r/104471368109/u/192/f/3050/c/205/s/15b495be/a2.htm"&gt;&lt;img src="http://da.feedsportal.com/r/104471368109/u/192/f/3050/c/205/s/15b495be/a2.img" border="0"/&gt;&lt;/a&gt;
						 </description>
						 <enclosure url="http://s1.lemde.fr/image/2009/10/29/87x0/1260149_7_8929_six-minutes-c-est-rapide-sur-un-sujet-qui.jpg" length="2133" type="image/jpeg" />
						 <pubDate>Tue, 07 Jun 2011 09:14:02 GMT
						 </pubDate>
						 <guid isPermaLink="false">http://bigbrowser.blog.lemonde.fr/2011/06/07/speed-dating-hirsch-a-six-minutes-pour-sauver-le-rsa/#xtor=RSS-32280322#xtor=RSS-3208
						 </guid>
					 </item>
					 <item>
						 <title>Blog - Délinquance des mineurs : le septième rapport en sept ans
						 </title>
						 <link>http://insecurite.blog.lemonde.fr/2011/06/07/delinquance-des-mineurs-le-septieme-rapport-en-sept-ans/#xtor=RSS-3208
						 </link>
						 <description>On ne compte plus ces dernières années les rapports consacrés à la délinquance et à la justice des mineurs. Ou plutôt si, comptons-les pour voir. Nous avions déjà les rapports Bénisti (celui de 2011, pas le premier de 2004), Klarsfeld ... Continuer la lecture ?&lt;img width=\'1\' height=\'1\' src=\'http://rss.lemonde.fr/c/205/f/3050/s/15b495bf/mf.gif\' border=\'0\'/&gt;&lt;br/&gt;&lt;br/&gt;&lt;a href="http://da.feedsportal.com/r/104471368108/u/192/f/3050/c/205/s/15b495bf/a2.htm"&gt;&lt;img src="http://da.feedsportal.com/r/104471368108/u/192/f/3050/c/205/s/15b495bf/a2.img" border="0"/&gt;&lt;/a&gt;
						 </description>
						 <enclosure url="http://s2.lemde.fr/image/2011/06/07/87x0/1532863_7_41d9_yvan-lachaud-depute-nouveau-centre-est.jpg" length="2315" type="image/jpeg" />
						 <pubDate>Tue, 07 Jun 2011 09:01:19 GMT
						 </pubDate>
						 <guid isPermaLink="false">http://insecurite.blog.lemonde.fr/2011/06/07/delinquance-des-mineurs-le-septieme-rapport-en-sept-ans/#xtor=RSS-3208
						 </guid>
					 </item>
					 <item>
						 <title>Blog - Le premier train solaire roule en Belgique
						 </title>
						 <link>http://ecologie.blog.lemonde.fr/2011/06/07/le-premier-train-solaire-roule-en-belgique/#xtor=RSS-3208
						 </link>
						 <description>A bord, rien ne le distingue d\'un autre convoi. Mais à l\'extérieur, ce sont des wagons d\'un genre nouveau, seulement alimentés par les rayons du soleil et non à l\'électricité issue des centrales nucléaires ou au gaz. Pour la première ... Continuer la lecture ?&lt;img width=\'1\' height=\'1\' src=\'http://rss.lemonde.fr/c/205/f/3050/s/15b48416/mf.gif\' border=\'0\'/&gt;&lt;br/&gt;&lt;br/&gt;&lt;a href="http://da.feedsportal.com/r/104471364868/u/192/f/3050/c/205/s/15b48416/a2.htm"&gt;&lt;img src="http://da.feedsportal.com/r/104471364868/u/192/f/3050/c/205/s/15b48416/a2.img" border="0"/&gt;&lt;/a&gt;
						 </description>
						 <enclosure url="http://s1.lemde.fr/image/2011/06/07/87x0/1532842_7_fe11_des-panneaux-solaires-sont-installes-sur-le.jpg" length="2514" type="image/jpeg" />
						 <pubDate>Tue, 07 Jun 2011 08:27:33 GMT
						 </pubDate>
						 <guid isPermaLink="false">http://ecologie.blog.lemonde.fr/2011/06/07/le-premier-train-solaire-roule-en-belgique/#xtor=RSS-3208
						 </guid>
					 </item>
					 <item>
						 <title>Paris veut faire condamner la Syrie par l\'ONU
						 </title>
						 <link>http://www.lemonde.fr/proche-orient/article/2011/06/07/paris-veut-faire-condamner-la-syrie-par-l-onu_1532779_3218.html#xtor=RSS-3208
						 </link>
						 <description>Malgré la menace d\'un veto russe, Paris veut faire adopter un projet de résolution qui exige la fin immédiate des violences contre les manifestants.&lt;img width=\'1\' height=\'1\' src=\'http://rss.lemonde.fr/c/205/f/3050/s/15b48417/mf.gif\' border=\'0\'/&gt;&lt;br/&gt;&lt;br/&gt;&lt;a href="http://da.feedsportal.com/r/104471364867/u/192/f/3050/c/205/s/15b48417/a2.htm"&gt;&lt;img src="http://da.feedsportal.com/r/104471364867/u/192/f/3050/c/205/s/15b48417/a2.img" border="0"/&gt;&lt;/a&gt;
						 </description>
						 <enclosure url="http://s2.lemde.fr/image/2011/06/07/87x0/1532785_7_345a_manifestants-a-banias-face-aux-forces-de.jpg" length="2054" type="image/jpeg" />
						 <pubDate>Tue, 07 Jun 2011 08:20:15 GMT
						 </pubDate>
						 <guid isPermaLink="false">http://www.lemonde.fr/proche-orient/article/2011/06/07/paris-veut-faire-condamner-la-syrie-par-l-onu_1532779_3218.html#xtor=RSS-3208
						 </guid>
					 </item>
					 <item>
						 <title>Lisbonne veut rassurer les marchés en créant une autorité budgétaire
						 </title>
						 <link>http://www.lemonde.fr/europe/article/2011/06/07/lisbonne-veut-rassurer-les-marches-en-creant-une-autorite-budgetaire_1532774_3214.html#xtor=RSS-3208
						 </link>
						 <description>Le nouveau gouvernement portugais va créer une autorité budgétaire indépendante "aux pouvoirs larges" pour rassurer les marchés financiers, déclare le futur fremier ministre portugais, Pedro Passos Coelho, dans une interview au quotidien "Les Echos" de mardi.&lt;img width=\'1\' height=\'1\' src=\'http://rss.lemonde.fr/c/205/f/3050/s/15b48418/mf.gif\' border=\'0\'/&gt;&lt;br/&gt;&lt;br/&gt;&lt;a href="http://da.feedsportal.com/r/104471364866/u/192/f/3050/c/205/s/15b48418/a2.htm"&gt;&lt;img src="http://da.feedsportal.com/r/104471364866/u/192/f/3050/c/205/s/15b48418/a2.img" border="0"/&gt;&lt;/a&gt;
						 </description>
						 <pubDate>Tue, 07 Jun 2011 07:40:37 GMT
						 </pubDate>
						 <guid isPermaLink="false">http://www.lemonde.fr/europe/article/2011/06/07/lisbonne-veut-rassurer-les-marches-en-creant-une-autorite-budgetaire_1532774_3214.html#xtor=RSS-3208
						 </guid>
					 </item>
					 <item>
						 <title>Luxe : PPR se préparerait à une acquisition majeure
						 </title>
						 <link>http://www.lemonde.fr/economie/article/2011/06/07/luxe-ppr-se-preparerait-a-une-acquisition-majeure_1532770_3234.html#xtor=RSS-3208
						 </link>
						 <description>Selon "La Tribune" plusieurs "cibles" pourraient intéresser le groupe, dont Hugo Boss, Burberry, Ralph Lauren et Armani, mais l\'italien Prada semble le plus coivoité.&lt;img width=\'1\' height=\'1\' src=\'http://rss.lemonde.fr/c/205/f/3050/s/15b48419/mf.gif\' border=\'0\'/&gt;&lt;br/&gt;&lt;br/&gt;&lt;a href="http://da.feedsportal.com/r/104471364865/u/192/f/3050/c/205/s/15b48419/a2.htm"&gt;&lt;img src="http://da.feedsportal.com/r/104471364865/u/192/f/3050/c/205/s/15b48419/a2.img" border="0"/&gt;&lt;/a&gt;
						 </description>
						 <enclosure url="http://s1.lemde.fr/image/2011/06/07/87x0/1532772_7_f14e_francois-henri-pinault-lors-de-la-presentation.jpg" length="1951" type="image/jpeg" />
						 <pubDate>Tue, 07 Jun 2011 07:10:04 GMT
						 </pubDate>
						 <guid isPermaLink="false">http://www.lemonde.fr/economie/article/2011/06/07/luxe-ppr-se-preparerait-a-une-acquisition-majeure_1532770_3234.html#xtor=RSS-3208
						 </guid>
					 </item>
					 <item>
						 <title>Microsoft intègre la reconnaissance vocale à son système de jeu Kinect
						 </title>
						 <link>http://www.lemonde.fr/technologies/article/2011/06/07/microsoft-integre-la-reconnaissance-vocale-a-son-systeme-de-jeu-kinect_1532757_651865.html#ens_id=1514468&amp;#38;xtor=RSS-3208
						 </link>
						 <description>Microsoft a ouvert, lundi, le bal des conférences de presse du Salon E3 à Los Angeles. Un show millimétré, rythmé par les images des prochains jeux défilant sur des écrans géants, au cours duquel le constructeur a présenté les nouvelles fonctionnalités de son système Kinect.&lt;img width=\'1\' height=\'1\' src=\'http://rss.lemonde.fr/c/205/f/3050/s/15b3bbe8/mf.gif\' border=\'0\'/&gt;&lt;br/&gt;&lt;br/&gt;&lt;a href="http://da.feedsportal.com/r/104456619640/u/192/f/3050/c/205/s/15b3bbe8/a2.htm"&gt;&lt;img src="http://da.feedsportal.com/r/104456619640/u/192/f/3050/c/205/s/15b3bbe8/a2.img" border="0"/&gt;&lt;/a&gt;
						 </description>
						 <enclosure url="http://s2.lemde.fr/image/2011/06/07/87x0/1532765_7_53d1_presentation-du-jeu-star-wars-sur-kinect.jpg" length="2500" type="image/jpeg" />
						 <pubDate>Tue, 07 Jun 2011 07:04:13 GMT
						 </pubDate>
						 <guid isPermaLink="false">http://www.lemonde.fr/technologies/article/2011/06/07/microsoft-integre-la-reconnaissance-vocale-a-son-systeme-de-jeu-kinect_1532757_651865.html#ens_id=1514468&amp;#38;xtor=RSS-3208
						 </guid>
					 </item>
					 <item>
						 <title>Egalité des sexes : un rapport suggère d\'allonger le congé paternité
						 </title>
						 <link>http://www.lemonde.fr/societe/article/2011/06/07/egalite-des-sexes-un-rapport-suggere-d-allonger-le-conge-paternite_1532763_3224.html#xtor=RSS-3208
						 </link>
						 <description>Brigitte Grésy, inspectrice générale des affaires sociales, doit rendre mardi à Roselyne Bachelot, ministre des solidarités, son rapport sur "la participation des hommes aux responsabilités parentales".&lt;img width=\'1\' height=\'1\' src=\'http://rss.lemonde.fr/c/205/f/3050/s/15b3bbe2/mf.gif\' border=\'0\'/&gt;&lt;br/&gt;&lt;br/&gt;&lt;a href="http://da.feedsportal.com/r/104456619639/u/192/f/3050/c/205/s/15b3bbe2/a2.htm"&gt;&lt;img src="http://da.feedsportal.com/r/104456619639/u/192/f/3050/c/205/s/15b3bbe2/a2.img" border="0"/&gt;&lt;/a&gt;
						 </description>
						 <enclosure url="http://s2.lemde.fr/image/2011/03/23/87x0/1497164_7_d1d0_l-allongement-du-conge-de-paternite-permettrait.jpg" length="2391" type="image/jpeg" />
						 <pubDate>Tue, 07 Jun 2011 06:47:20 GMT
						 </pubDate>
						 <guid isPermaLink="false">http://www.lemonde.fr/societe/article/2011/06/07/egalite-des-sexes-un-rapport-suggere-d-allonger-le-conge-paternite_1532763_3224.html#xtor=RSS-3208
						 </guid>
					 </item>
					 <item>
						 <title>"Le mastère spécialisé apporte surtout la méthodologie d\'une école"
						 </title>
						 <link>http://www.lemonde.fr/orientation-scolaire/article/2011/06/07/le-mastere-specialise-apporte-surtout-la-methodologie-d-une-ecole_1531169_1473696.html#xtor=RSS-3208
						 </link>
						 <description>Eric Parlebas, président de la commission d\'accréditation des diplômes de la Conférence des grandes écoles, explique ce qu\'on peut attendre d\'un mastère spécialisé.&lt;img width=\'1\' height=\'1\' src=\'http://rss.lemonde.fr/c/205/f/3050/s/15b3a6c1/mf.gif\' border=\'0\'/&gt;&lt;br/&gt;&lt;br/&gt;&lt;a href="http://da.feedsportal.com/r/104471352231/u/192/f/3050/c/205/s/15b3a6c1/a2.htm"&gt;&lt;img src="http://da.feedsportal.com/r/104471352231/u/192/f/3050/c/205/s/15b3a6c1/a2.img" border="0"/&gt;&lt;/a&gt;
						 </description>
						 <enclosure url="http://s1.lemde.fr/image/2011/06/02/87x0/1531170_7_1da1_eric-parlebas.jpg" length="2048" type="image/jpeg" />
						 <pubDate>Tue, 07 Jun 2011 06:45:33 GMT
						 </pubDate>
						 <guid isPermaLink="false">http://www.lemonde.fr/orientation-scolaire/article/2011/06/07/le-mastere-specialise-apporte-surtout-la-methodologie-d-une-ecole_1531169_1473696.html#xtor=RSS-3208
						 </guid>
					 </item>
				 </channel>
			 </rss>';
	}
}