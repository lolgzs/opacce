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


class DecodageUnimarcDVDLaJeuneFilleTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$mock_sql = $this->getMockBuilder('Class_Systeme_Sql')
                         			->disableOriginalConstructor()
                        			->getMock();
		Zend_Registry::set('sql', $mock_sql);
		$mock_sql
			->expects($this->any())
			->method('fetchOne');

		Class_CodifLangue::getLoader()
			->newInstanceWithId('bam')
			->setLibelle('');

		$this->dvd_jeune_fille = Class_Notice::getLoader()
			->newFromRow(array('id_notice' => 3,
												 'unimarc' => "01328ngm0 2200265   450 0010007000001000041000071010013000481020007000611150025000682000071000932100022001642150053001863000035002393000045002743300454003193450027007735100018008006060027008186060039008457000042008847020043009267020033009697020032010028010028010342247456  a20021213i20041975u  y0frey0103    ba0 abamjfre  aFR  ac086baz|zba    zz  c1 aLa jeune fillebDVDdDen MusofSouleymane Cisse, réal., scénario  cPathédcop. 2004  a1 DVD vidéo monoface zone 2 (1 h 26 min)ccoul.  aDate de sortie du film : 1975.  aFilm en bambara sous-titré en français  aSékou est renvoyé de l'usine parce qu'il a osé demander une augmentation. Chômeur, il sort avec Ténin, une jeune fille muette ; il ignore qu'elle est la fille de son ancien patron. Ténin, qui sera violée par Sékou lors d'une sortie entre jeunes, se retrouve enceinte et subit la colère de ses parents. Elle se trouve alors confrontée brutalement à la morale de sa famille et à la lâcheté de Sékou, qui refuse de reconnaiîre l'enfant.  b3388334509824d14.00 ?1 aDen Musozbam| 31070135aCinémayMali| 32243367aCinéma30076549yAfrique 131070144aCissébSouleymane43704690 132247457aCoulibalibDounamba Dani4590 132247458aDiabatebFanta4590 132247459aDiarrabOumou4590 0aFRbBNc20011120gAFNOR"));
	}


	public function testNotesSizeIsTwo() {
		$this->assertEquals(3, count($this->dvd_jeune_fille->getNotes()));
	}

	public function testNotesContainsDateDeSortie1975() {
		$this->assertContains("Date de sortie du film : 1975.", 
													$this->dvd_jeune_fille->getNotes());
	}

	public function testNotesContainsFilmEnBambara() {
		$this->assertContains("Film en bambara sous-titré en français",
													$this->dvd_jeune_fille->getNotes());
	}


	public function testTitrePrincipalIsLaJeuneFille() {
		$this->assertEquals("La jeune fille",
												$this->dvd_jeune_fille->getTitrePrincipal());
	}


	public function testAuteurPrincipalIsBarbetSchroeder() {
		$this->assertEquals("Souleymane Cissé",
												$this->dvd_jeune_fille->getAuteurPrincipal());
	}

	public function testGetAuteursReturnsFourAuteurs() {
		$this->assertEquals(4, count($this->dvd_jeune_fille->getAuteurs()));
	}

	public function testCoulibaliInAuteurs() {
		$auteurs = $this->dvd_jeune_fille->getAuteurs();
		$this->assertContains('Coulibali|Dounamba Dani', $auteurs);
	}


	public function testDiabateInAuteurs() {
		$this->assertContains('Diabate|Fanta', $this->dvd_jeune_fille->getAuteurs());
	}


	public function testDiarraInAuteurs() {
		$this->assertContains('Diarra|Oumou', $this->dvd_jeune_fille->getAuteurs());
	}


	public function testLanguesIsBambara() {
		$this->assertEquals('bam', $this->dvd_jeune_fille->getLangues());
	}


	public function testCollationIsOneDVD() {
		$this->assertEquals('1 DVD vidéo monoface zone 2 (1 h 26 min) ; coul.', 
												$this->dvd_jeune_fille->getCollation());
	}

	public function testEditeurIsPathe() {
		$this->assertEquals('Pathé',
												$this->dvd_jeune_fille->getEditeur());
	}

	public function testResume() {
		$this->assertEquals("Sékou est renvoyé de l'usine parce qu'il a osé demander une augmentation. Chômeur, il sort avec Ténin, une jeune fille muette ; il ignore qu'elle est la fille de son ancien patron. Ténin, qui sera violée par Sékou lors d'une sortie entre jeunes, se retrouve enceinte et subit la colère de ses parents. Elle se trouve alors confrontée brutalement à la morale de sa famille et à la lâcheté de Sékou, qui refuse de reconnaiîre l'enfant.",
												$this->dvd_jeune_fille->getResume());
	}
}




class DecodageUnimarcLivreCinemaDAnimationTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->livre_cinema = new Class_Notice();
		$this->livre_cinema->setUnimarc("01570nam0 2200325   450 0010007000000100033000070200017000400210027000571000041000841010008001251020007001331050018001401060006001582000106001642100075002702150044003452250023003893000125004123000020005373000137005573300265006943450018009594100051009775120027010286060033010556060060010886760012011487000045011608010039012052218529  a2-86642-370-4bbr.d8,95 EUR  aFRb00347575  aFRbDLE-20031204-51138  a20031107d2003    m  h0frey0103    ba| afre  aFR  ay   z   000y|  ar1 aCinéma d'animationbTexte impriméedessin animé, marionnettes, images de synthèsefBernard Génin  a[Paris]c\"Cahiers du cinéma\"cSCEREN-CNDPdcop. 2003gimpr. en Italie  a95 p.cill., couv. ill. en coul.d19 cm2 aLes petits cahiers  aLa couv. porte en plus : \"du crayon à l'ordinateur, pour ou contre Disney, Europe-Japon : le dessin animé aujourd'hui\"  aBibliogr. p. 93  aSCEREN = Services, cultures, éditions, ressources pour l'éducation nationale. CNDP = Centre national de documentation pédagogique  aPrésente un historique du cinéma d'animation, un survol des différentes productions nationales à travers le monde (Etats-Unis, Japon, France, Canada), les techniques du volume animé, l'image de synthèse, mais aussi l'oeuvre de Disney et le film d'auteur.  b9782866423704 032525826tLes Petits cahiers (Paris)x1633-90531 aLe cinéma d'animation| 31053394aAnimation (cinéma)| 31031625aDessins animés32195497xHistoire et critique  a791.431 |32547161aGéninbBernardf1946-....4070 0aFRbBNFc20031107gAFNOR2intermrc");
	}


	public function testAuteurPrincipalIsBernardGenin() {
		$this->assertEquals("Bernard Génin",
												$this->livre_cinema->getAuteurPrincipal());
	}

	public function testTitrePrincipalIsCinemaAnimation() {
		$this->assertEquals("Cinéma d'animation",
												$this->livre_cinema->getTitrePrincipal());
	}
}




class DecodageUnimarcLittleSenegalTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->little_senegal = new Class_Notice();
		$this->little_senegal->setUnimarc("01494ngm0 2200337   450 0010007000000710012000071000041000191010038000601020007000981150042001052000052001472100035001992150053002343000109002873000071003963050034004673300201005013450027007026060031007296060036007606060043007966060039008397000048008787020043009267020031009697020038010007020035010387020033010738010023011068010027011292371272|0aEDV1441  a20070320i20042001b-ey0frey0103    ba0 afreaengcfrecengjfrejengjger  aFR  ac093baz|zba||||zz||cb|||||||||||||||1 aLittle SénégalbDVDfRachid Bouchareb, réal.  cBlaq outcParamountdcop. 2004  a1 DVD vidéo monoface zone 2 (1 h 33 min)cCoul.  aVersion originale franco-anglaise, Version française, avec sous-titrage en français, anglais, allemand  aBonus : court-métrage \"Peut-être la mer\" (14 min), bande-annonce  aDate de sortie du film : 2001  aUn vieil Africain, guide à la maison des esclaves de l'île de Gorée, part à la rencontre des descendants de ses ancêtres à Harlem... Quête identitaire et exploration d'un fossé culturel...  b3333973136023d44,73 ?| 31047449aCinémayAlgérie| 32243366aCinémayFrancez1990-| 32163808aNoirs américainsxAu cinéma| 32243367aCinéma30076549yAfrique 132371273aBoucharebbRachidf1953-43704690 132371260aKouyatébSotiguif1936-4590 132371274aHopebSharon4590 131073585aZembRoschdyf1965-4590 132371277aLorellebOlivier4690 131089718aBoutellabSafy4230 0aFRbADAVc20070320 0aFRbBM Melunc20070510");
		Class_CodifLangue::getLoader()
			->newInstanceWithId('fre')->setLibelle('');
		Class_CodifLangue::getLoader()
			->newInstanceWithId('eng')->setLibelle('');
	}


	public function testLangues() {
		$this->assertEquals('fre, eng', $this->little_senegal->getLangues());
	}

	public function testEditeurIsBlaqOut() {
		$this->assertEquals('Blaq out', $this->little_senegal->getEditeur());
	}

	public function testResume() {
		$this->assertEquals("Un vieil Africain, guide à la maison des esclaves de l'île de Gorée, part à la rencontre des descendants de ses ancêtres à Harlem... Quête identitaire et exploration d'un fossé culturel...",
												$this->little_senegal->getResume());
	}

	public function testNotes() {
		$this->assertEquals(array('DVD',
															"Version originale franco-anglaise, Version française, avec sous-titrage en français, anglais, allemand",
															'Bonus : court-métrage "Peut-être la mer" (14 min), bande-annonce',
															'Date de sortie du film : 2001'),
												$this->little_senegal->getNotes());
	}
}


class UnimarcLindaLemayBlesseTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->blessee = new Class_Notice();
		$this->blessee->setUnimarc("01433nje0 22004331  450 00100080000001000250000807100310003310000410006410500910010520000490019621000230024521500200026830000240028830000350031233500770034746400160042446400320044046400290047246400310050146400180053246400150055046400360056546400300060146400240063146400180065546400320067346400160070546400150072146400150073646400170075146400230076846400260079146400340081746400260085167600060087768600140088370000370089793400650093400123297  a0825646794607d22,2300a2564679460bWarner2WARNER  a20101125             1frea01      ba  1[2010-11-25-00.00.00.000000][2010-11-25-00.00.00.000000][2010-11-25-00.00.00.000000][]1 aBlesséefLynda Lemay AbDC : Disque Compact  cWarner Musicd2010  a1 DC11elivret  aTextes des chansons  aContient une plage multimédia  ahttp://www.gamannecy.com/images/pochettes/201007/0825646794607_thumb.jpg1 aBlesséev11 aDebout sur les pissenlisv11 aJ'ai rencontré Mariev11 aLes Mûres Introductionv11 aLes Mûresv11 aJumellev11 aGros colons - gros blaireauxv11 aCa valait des millionsv11 aJe t'aime encorev11 aUn Golfeurv11 aMes plus belles vacancesv11 aAncêtrev11 aCharlotv11 aPoissonv11 aUne Mèrev11 aFarce d'oreillev11 aMa chaise en rotinv11 aUn Verre de n'importe quoiv11 aEntre deux paradisv1  10  a0 LEM 99710aLemaybLyndagAlto, Contralto5A  a<0><99><7><Créé par import UNIMARC le 25-11-2010><LEM><><>");
	}


	public function testTitrePrincipalIsBlessee() {
		$this->assertEquals('Blessée', $this->blessee->getTitrePrincipal());
	}


	public function testAuteurPrincipalIsLindaLemay() {
		$auteur = $this->blessee->getAuteurPrincipal();
		$this->assertEquals('Lynda Lemay', $auteur);
	}

	public function testUnimarcZone700IsLindaLemay() {
		$this->assertEquals(array('10aLemaybLyndagAlto, Contralto5A'),
												$this->blessee->getUnimarcZone('700'));
	}

	public function testUnimarcZone335IsGamannecy() {
		$this->assertEquals(array('  ahttp://www.gamannecy.com/images/pochettes/201007/0825646794607_thumb.jpg'),
												$this->blessee->getUnimarcZone('335'));
	}
}



class DecodageUnimarcHarryPotterTest extends PHPUnit_Framework_TestCase {
	public function setUp() {
		$this->potter = new Class_Notice();
		$this->potter->setUnimarc("00627nam0 22002291  450 00100080000001000180000802100070002610000410003310100130007410500390008720000690012621000360019521500290023122500230026067600060028368600100028970000290029983000270032883500060035593000140036193200220037500028922  a2-07-052818-9  aFR  a20010130         d   0frea01      ba1 afreceng  1[2001-01-30-00.00.00.000000][][][]1 aHarry Potter et le prisonnier d'AzkabanfJoanne Kathleen Rowling  aPariscGallimard jeunessed2000  a465 p.3465cill.d18 cm 2aFolio juniorv1006  10  aR ROW1 aRowlingbJoanne Kathleen  1A32A partir de 10 ans  aJ  aRomans4R  aSorcier-Sorcière");
	}


	public function testAuteurPrincipalIsJKRowling() {
		$this->assertEquals('Joanne Kathleen Rowling',
												$this->potter->getAuteurPrincipal());
	}


	public function testUnimarcZone700IsJKRowling() {
		$this->assertEquals(array('1 aRowlingbJoanne Kathleen'),
												$this->potter->getUnimarcZone('700'));
	}
}

?>