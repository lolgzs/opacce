<?php
require_once('classe_notice_integration.php');
require_once('classe_notice_marc21.php');

abstract class NoticeIntegrationAbstract extends PHPUnit_Framework_TestCase {
	protected  $notice_sgbd;
	public function expectVariable($index, $name, $value) {
		global $sql;
		$sql->expects($this->at($index))
				->method('fetchOne')
				->with("Select valeur from variables where clef='".$name."'")
				->will($this->returnValue($value));
		return $this;
	}

	public function setUp() {
		global $sql;
		$sql=$this->getMock('NullSql',['fetchOne',
																	 'execute',
																	 'fetchAll',
																	 'insert',
																	 'update',
																	 'fetchEnreg']);
		
		$sql->expects($this->any())
				->method('execute')
				->will($this->returnValue(true));


		VariableCache::getInstance()
		  ->setValeurCache(['filtrer_fulltext' => 1,
												'mode_doublon'=> 1,
												'tracer_accents_iso'=>1,
												'non_exportable'=> 'electre;decitre;gam;zebris',
												'controle_codes_barres'=> 0,
												'unimarc_zone_titre' => '200$a;461$t'])
			->setListeCache(['nature_docs'=> "1:Collection\r\n2:Dataset\r\n3:Event\r\n4:Image"]);

	
		$this->notice_sgbd=new notice_unimarc();
	}
}




class NoticeIntegrationNatureDocTest extends NoticeIntegrationAbstract {
	public function setUp() {
		parent::setUp();
		$notice_integration=new notice_integration();
		$ret = $notice_integration->traitePseudoNotice(100,
																												['titre'=>'Eloge de la fuite',
																												 'tags'=> 'domination,sociologie,biologie',
																												 'id_origine' => '666',

																												 'nature_doc' => '1;2']);
		$this->notice_sgbd->ouvrirNotice($ret['unimarc'],0);

	}


	/** @test */
	public function titreShouldBeElogeDeLaFuite() {
		$this->assertEquals('Eloge de la fuite',$this->notice_sgbd->get_subfield("200","a")[0]);		
	}

	/** @test */
	public function subfield200_b_ShouldContainsCollectionAndDataset() {
		$this->assertEquals(['Collection', 'Dataset'], $this->notice_sgbd->get_subfield('200', 'b'));
	}

}




abstract class NoticeIntegrationSacramentariumTestCase extends NoticeIntegrationAbstract {
	public function setUp() {
		parent::setUp();

		Codif_matiere::getInstance()->setCodif(['62115' => ['libelle' => 'Douzième siècle']]);

		$this->album_data = ['id'=>'144',
												 'cat_id'=>'30',
												 'notice_id'=>'99421',
												 'titre'=>'MS 14 - Sacramentarium ad usum Sylviniacensem',
												 'auteur'=>'',
												 'editeur'=>'',
												 'annee'=>'',
												 'description'=>'',
												 'tags'=>'',
												 'date_maj'=>'2012-10-22 16:49:57',
												 'fichier'=>'144_B031906101_MS_014_0033R.jpg',
												 'type_doc_id'=>'100',
												 'id_langue'=>'lat',
												 'genre'=>'',
												 'dewey'=>'',
												 'matiere'=>'62115',
												 'id_origine'=>'D09030160',
												 'cfg_thumbnails'=>'',
												 'a:9:{s:15:"thumbnail_width";s:3:"400";s:28:"thumbnail_left_page_crop_top";s:1:"0";s:30:"thumbnail_left_page_crop_right";s:2:"35";s:31:"thumbnail_left_page_crop_bottom";s:1:"0";s:29:"thumbnail_left_page_crop_left";s:1:"0";s:29:"thumbnail_right_page_crop_top";s:1:"0";s:31:"thumbnail_right_page_crop_right";s:1:"0";s:32:"thumbnail_right_page_crop_bottom";s:1:"0";s:30:"thumbnail_right_page_crop_left";s:2:"35";}',
												 'pdf'=>'',
												 'sous_titre'=>'Sacramentaire de Souvigny',
												 'cote'=>'MS 14',
												 'provenance'=>'Prieuré de Souvigny',
												 'notes'=>'a:3:{s:5:"305$a";s:12:"XIIe siècle";s:5:"200$b";s:9:"Parchemin";s:5:"316$a";s:12:"Reliure bois";}',
												 'url_origine'=>null,
												 'visible'=>'1',
												 'droits'=>'Domaine public',
												 'nature_doc'=>'',
												 'id_bib' => 1];

		$this->notice_integration = new notice_integration();

		$this->pseudo_notice = $this->notice_integration->traitePseudoNotice(100,
																																				 $this->album_data);
		$this->notice_sgbd->ouvrirNotice($this->pseudo_notice['unimarc'],0);
	}
}




class NoticeIntegrationSacramentariumParsingTest extends NoticeIntegrationSacramentariumTestCase {
	/** @test */
	public function subfield200_bShouldBeParchemin() {
		$this->assertEquals('Parchemin',$this->notice_sgbd->get_subfield("200","b")[0]);		
	}


	/** @test */
	public function matiereShouldBeDouziemeSiecle() {
		$this->assertEquals('Douzième siècle',$this->notice_sgbd->get_subfield("610","a")[0]);		
	}


	/** @test */
	public function titreShouldBeSacramentarium() {
		$this->assertEquals('MS 14 - Sacramentarium ad usum Sylviniacensem',$this->notice_sgbd->get_subfield("200","a")[0]);		
	}
}




class NoticeIntegrationLollipopGeneratedNoticeRecordTest extends NoticeIntegrationAbstract {
	public function setUp() {
		parent::setUp();

		Codif_langue::getInstance()->setCodif(['fre' => ['id_langue' => 'fre',
																										 'libelle' => 'français']]);


		$this->notice_integration = new notice_integration();
		$this->notice_integration->setParamsIntegration(1, 0, 1);
		$this->notice_integration->traiteNotice(file_get_contents(dirname(__FILE__)."/unimarc_lollipop.txt"));
		$this->notice_integration->traiteFacettes();
		$this->notice_data = $this->notice_integration->getNotice();
	}


	/** @test */
	public function facetteShouldContainsLangueFre() {
		$this->assertContains(' Lfre', $this->notice_data['facettes']);
	}


	/** @test */
	public function codeAlphaShouldBeLollipop() {
		$this->assertEquals('LOLLIPOP--NOSTLINGERC--ECOLEDESLOISIRS-1987-0', $this->notice_data['clef_alpha']);
	}


	/** @test */
	public function clefOeuvreShouldBeLollipop() {
		$this->assertEquals('LOLLIPOP--NOSTLINGERC-', $this->notice_data['clef_oeuvre']);
	}


	/** @test */
	public function noticeDbEnregTitresShouldBeLollipopAndLolipop() {
		$this->assertEquals('LOLLIPOP LOLIPOP', 
												$this->notice_integration->noticeToDBEnreg($this->notice_data)['titres']);
	}


	/** @test */
	public function noticeDbEnregEditeurShouldBeEcoleEkolLoisirsLoisir() {
		$this->assertEquals('ECOLE EKOL LOISIRS LOISIR', 
												$this->notice_integration->noticeToDBEnreg($this->notice_data)['editeur']);
	}
}




abstract class NoticeIntegrationMarc21ToUnimarcTest extends NoticeIntegrationAbstract {
	public function setUp() {
		parent::setUp();
		$this->notice_marc21 = new notice_marc21();
		$this->notice_marc21->ouvrirNotice(file_get_contents(dirname(__FILE__)."/marc21_etalon.txt"), 0);		
		$this->notice_sgbd->ouvrirNotice($this->notice_marc21->getFullRecord());
	}
	
	/** @test */
	public function zone461TInUnimarcShouldContainsTitres() {
		$this->assertEquals(['Titre général ;', 'titre general ;'], $this->notice_sgbd->get_subfield('461', 't'));
	}
	
		/** @test */
	public function zone461TInMarc21ShouldContainsTitres() {
		$this->assertEquals(['Titre général ;', 'titre general ;'], $this->notice_marc21->get_subfield('461', 't'));
	}
}



class NoticeIntegrationMarc21CoupCavalierToUnimarcTest extends NoticeIntegrationAbstract {
	public function setUp() {
		parent::setUp();
		$this->notice_marc21 = new notice_marc21();
		$this->notice_marc21->ouvrirNotice(file_get_contents(dirname(__FILE__)."/marc21_coup_cavalier.txt"), 0);		
		$this->notice_sgbd->ouvrirNotice($this->notice_marc21->getFullRecord());
	}
	
	
	/** @test */
	public function zone200AShouldBeLeCoupDuCavalier() {
		$this->assertEquals('Le coup du cavalier', $this->notice_sgbd->get_subfield('200', 'a')[0]);
	}


	/** @test */
	public function zone210CShouldBeEditeurMetailie() {
		$this->assertEquals('Métailié,', $this->notice_sgbd->get_subfield('210', 'c')[0]);
	}


	/** @test */
	public function getAllShouldReturnAllFields() {
		$all = $this->notice_sgbd->getAll();
		$this->assertEquals('Le coup du cavalier', $all['titre_princ']);
		$this->assertEquals([	['Longueur de la notice', 784],
													['Statut de la notice', 'n'],
													['Type de document', 'am'],
													['Niveau hiérarchique', 0],
													['Adresse des données', 181],
													['Niveau de catalogage' , '1']
												 ],

												$all['label']);
		$this->assertEquals('Quadruppani, Serge', $all['zones'][11]['champs'][0]['valeur']);
	}
}


?>
