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
////////////////////////////////////////////////////////////////////////
// OPAC3 :       CALCUL DES INDEX
///////////////////////////////////////////////////////////////////////

class Class_Indexation
{
	private $articles;							// Articles rejetes
	private $inclu;									// Mots inclus
	private $exclu;									// Mots vides
	private $pluriel;								// Règles des pluriels
	private $tableMaj;							// Table de transco pour majuscules
	
// Contructeur (init les règles d'indexation)
	function __construct()
	{
		// Lire formes rejetées
		$this->articles=array("L'","LE ","LA ","LES ","UN ","UNE ");
		$this->inclu=array("AN","AS","OR","U2","AI","LU","XO");
		$this->exclu = array("LES","DES","MES","TES","ENTRE","CHEZ","SES","LEUR","MON","ENTRE","POUR","ELLE","ILS","COMME","DANS","EUX","CEUX","MAIS","MEME","SANS",
		"TOME","VERS","VOUS","CECI","CES","ETC","PARCE","QUE","QUEL","QUELLE","QUELS","QUELLES","PAS","QUI","QUOI","VOS","AFIN","CECI","CELA","LUI","PAR","PUIS","SOI");
		
		// Pluriels
		$this->pluriel=array(
			array("AIL","AULX"),
			array("AVAL","AVALS"),
			array("BAIL","BAUX"),
			array("BAL","BALS"),
			array("BANAL","BANALS"),
			array("BANCAL","BANCALS"),
			array("BIJOU","BIJOUX"),
			array("BLEU","BLEUS"),
			array("CAILLOU","CAILLOUX"),
			array("CAL","CALS"),
			array("CARNAVAL","CARNAVALS"),
			array("CEREMONIAL","CEREMONIALS"),
			array("CHACAL","CHACALS"),
			array("CHORAL","CHORALS"),
			array("CHOU","CHOUX"),
			array("CORAIL","CORAUX"),
			array("DETAIL","DETAILS"),
			array("EMAIL","EMAUX"),
			array("EMEU","EMEUS"),
			array("ETAL","ETALS"),
			array("FATAL","FATALS"),
			array("FESTIVAL","FESTIVALS"),
			array("GEMMAIL","GEMMAUX"),
			array("GENOU","GENOUX"),
			array("HIBOU","HIBOUX"),
			array("JOUJOU","JOUJOUX"),
			array("LANDAU","LANDAUX"),
			array("NATAL","NATALS"),
			array("OEIL","YEUX"),
			array("PAL","PALS"),
			array("PNEU","PNEUS"),
			array("POU","POUX"),
			array("RECITAL","RECITALS"),
			array("REGAL","REGALS"),
			array("SARRAU","SARRAUS"),
			array("SOUPIRAIL","SOUPIRAUX"),
			array("TONAL","TONALS"),
			array("TRAVAIL","TRAVAUX"),
			array("VAL","VALS"),
			array("VENTAIL","VENTAUX"),
			array("VIRGINAL","VIRGINALS"),
			array("VITRAIL","VITRAUX"),
			array("*EAU","*EAUX"),
			array("*AL","*AUX"),
			array("*EU","*EUX"),
			array("*AU","*AUX")
			);
	
		// Init table ascii pour majuscules
		$this->tableMaj = str_repeat( " ", 41 ) 
			. "*     0123456789       "
			. "ABCDEFGHIJKLMNOPQRSTUVWXYZ      "
			. "ABCDEFGHIJKLMNOPQRSTUVWXYZ      "
			. str_repeat( " ", 63)
			."AAAAAAACEEEEIIII NOOOOO  UUUUY  AAAAAAACEEEEIIII NOOOOO  UUUUY Y";
	}
	// Indexation d'un titre
	public function codeAlphaTitre($titre)
	{
		$titre=$this->alphaMaj($titre);
		foreach($this->articles as $article)
		{
			$lg=strlen($article);
			if(strLeft($titre, $lg)==$article) {$titre=strMid($titre,$lg,256); break;}
		}
		$titre=$this->alphaMaj($titre);
		return $titre;
	}
	
	// Rend une suite de mots complete pour les formes plurielles 
	public function getExpressionRecherche($mot)
	{
		$mot=trim($mot);
		if(!$mot) return false;

		// Pluriel
		$etoile='';
		if(strRight($mot,1) == "*") {$etoile="*";$mot=strLeft($mot, strlen($mot)-1);}
		$m=$this->getPluriel($mot);
		$m[2]=$this->phonetix($m[0]);
		$ret="(".$m[0].$etoile." ".$m[1]." ".$m[2].")";
		return trim($ret);
	}
	
	// Transformation en majuscules
	public function alphaMaj( $chaine )
	{
		$chaine=utf8_decode($chaine);
		$new="";
		for( $i=0; $i < strLen($chaine); $i++)
		{
			$car = $chaine[$i];
			$ascii = ord($car);
			$car = $this->tableMaj[$ascii -1];
			$new = $new . $car;
		}
		return trim($new);
	}
	
// Remet les articles entre parenthèses au début du titre
	public function setArticleDebut($titre)
	{
		if(substr($titre,-1) !=")") return $titre;
		foreach($this->articles as $article)
		{
			$compare="(".trim($article).")";
			$lg=strlen($compare);
			if(strtoUpper(substr($titre,-$lg))==$compare)
			{
				$deb=substr($titre,-$lg);
				$deb=str_replace("(","",$article);
				$deb=str_replace(")","",$article);
				if(substr($deb,-1)!="'") $deb.=" ";
				$new=$deb.trim(substr($titre,0,(strlen($titre)-$lg)));
				return $new;
			}
		}
		return $titre;
	}
	
// Decoupe une expression en mots en tenant compte des exclusions / inclusions
	public function getMots( $chaine )
	{
		$new=array();
		$chaine = str_replace( ".", "", $chaine);
		$chaine = trim($this->alphaMaj($chaine));
		$mot = explode( " ", $chaine );
		$index = 0;
		for( $i = 0; $i < count($mot); $i++)
		{
			// Retirer mots de 2 lettres sauf les inclus et les nombres
			if( strLen($mot[$i]) < 3 And intVal($mot[$i])==false)
			{
				if( in_array( $mot[$i], $this->inclu) == false) continue;
			}
			// Retirer mots vides
			if( in_array($mot[$i], $this->exclu ) == true ) continue;
			// On garde le mot
			$new[$index++] = $mot[$i];
		}
		return $new;	
	}
// Rend une chaine de mots dedoublonnes et filtres 
	public function getFulltext($data)
	{
		$new = '';
		if(gettype($data) != "array") $data=array($data);
		foreach($data as $chaine)
		{
			$mots=$this->getMots($chaine);
			foreach($mots as $mot)
			{
				$mot=" ".$mot." ";
				if(strpos($new,$mot) === false ) $new.=$mot;
			}
		}
		return trim($new);
	}
	
// Rend le mot au singulier et au pluriel
	public function getPluriel( $mot )
	{
		if( strToUpper($mot) != $mot ) $mot=$this->alphaMaj($mot);
		if(!trim($mot)) return false;
		foreach($this->pluriel as $regle)
		{
			if(strLeft($regle[0],1) != "*")
			{
				if($mot==$regle[0] or $mot==$regle[1]) return $regle;
				else continue;
			}
			$regle[0]=str_replace("*","",$regle[0]);
			$regle[1]=str_replace("*","",$regle[1]);
			$pattern_singulier = "(".$regle[0]."$)";
			$pattern_pluriel="(".$regle[1]."$)";

			$pluriel=preg_replace($pattern_singulier, $regle[1], $mot);
			$singulier=preg_replace($pattern_pluriel, $regle[0], $mot);
			if($singulier != $mot or $pluriel != $mot) break;
		}
		// Si inchangé on ajoute le S
		if($singulier == $pluriel)
		{
			if(strRight($mot,1)=="S") {$pluriel=$singulier; $singulier=strLeft($singulier,strlen($singulier)-1);}
			else $pluriel=$singulier."S";
		}
		return array($singulier,$pluriel);
	}
	
	// Compare deux expression et rend true si tous les mots correspondent
	function compare_expression($texte1,$texte2)
	{
		$mots_vides=";THE;";
		$texte1 = convertFromUtf8($texte1);
		$texte2 = convertFromUtf8($texte2);
		// Decouper par mots
		$chaine = trim($this->AlphaMaj($texte1));
		$mot1 = explode( " ", $chaine );
		$chaine = trim($this->AlphaMaj($texte2));
		$mot2 = explode( " ", $chaine );

		// Comparer
		for($i=0;$i<count($mot1); $i++)
		{
			if(!trim($mot1[$i])) continue;
			if(strPos($mots_vides,";".$mot1[$i].";") !== false) continue;
			if(! in_array($mot1[$i],$mot2)) return false;
		}
		return true;
	}

	// ---------------------------------------------------
// Othographe approchante
// ---------------------------------------------------
	function phonetix($sIn)
	{
		if(strlen($sIn)<4 ) return false;
		$accents = array('É' => 'E', 'È' => 'E', 'Ë' => 'E', 'Ê' => 'E','Á' => 'A', 'À' => 'A', 'Ä' => 'A', 'Â' => 'A',
				'Å' => 'A', 'Ã' => 'A', 'Æ' => 'E','Ï' => 'I', 'Î' => 'I', 'Ì' => 'I', 'Í' => 'I',
				'Ô' => 'O', 'Ö' => 'O', 'Ò' => 'O', 'Ó' => 'O', 'Õ' => 'O', 'Ø' => 'O', 'Œ' => 'OEU',
				'Ú' => 'U', 'Ù' => 'U', 'Û' => 'U', 'Ü' => 'U','Ñ' => 'N', 'Ç' => 'S', '¿' => 'E');

		$min2maj = array('é' => 'É', 'è' => 'È', 'ë' => 'Ë', 'ê' => 'Ê','á' => 'Á', 'â' => 'Â', 'à' => 'À', 'Ä' => 'A',
				'Â' => 'A', 'å' => 'Å', 'ã' => 'Ã', 'æ' => 'Æ',	'ï' => 'Ï', 'î' => 'Î', 'ì' => 'Ì', 'í' => 'Í',
				'ô' => 'Ô', 'ö' => 'Ö', 'ò' => 'Ò', 'ó' => 'Ó','õ' => 'Õ', 'ø' => 'Ø', 'œ' => 'Œ',
				'ú' => 'Ú', 'ù' => 'Ù', 'û' => 'Û', 'ü' => 'Ü','ç' => 'Ç', 'ñ' => 'Ñ', 'ß' => 'S');


		//$sIn = utf8_decode($sIn);						// Selon votre implémentation, vous aurez besoin de décoder ce qui arrive pour les caractères spéciaux
		$sIn = strtr( $sIn, $min2maj); 					// minuscules accentuées ou composées en majuscules simples
		$sIn = strtr( $sIn, $accents); 					// majuscules accentuées ou composées en majuscules simples
		$sIn = strtoupper( $sIn );     					// on passe tout le reste en majuscules
		$sIn = preg_replace( '`[^A-Z]`', '', $sIn ); 	// on garde uniquement les lettres de A à Z

		$sBack=$sIn;									// on sauve le code (utilisé pour les mots très courts)

		$sIn = preg_replace( '`O[O]+`', 'OU', $sIn ); 	// pré traitement OO... -> OU
		$sIn = preg_replace( '`SAOU`', 'SOU', $sIn ); 	// pré traitement SAOU -> SOU
		$sIn = preg_replace( '`OES`', 'OS', $sIn ); 	// pré traitement OES -> OS
		$sIn = preg_replace( '`CCH`', 'K', $sIn ); 		// pré traitement CCH -> K
		$sIn = preg_replace( '`CC([IYE])`', 'KS$1', $sIn ); // CCI CCY CCE
		$sIn = preg_replace( '`(.)\1`', '$1', $sIn ); 	// supression des répétitions

		// quelques cas particuliers
		if ($sIn=="CD") return($sIn);
		if ($sIn=="BD") return($sIn);
		if ($sIn=="BV") return($sIn);
		if ($sIn=="TABAC") return("TABA");
		if ($sIn=="FEU") return("FE");
		if ($sIn=="FE") return($sIn);
		if ($sIn=="FER") return($sIn);
		if ($sIn=="FIEF") return($sIn);
		if ($sIn=="FJORD") return($sIn);
		if ($sIn=="GOAL") return("GOL");
		if ($sIn=="FLEAU") return("FLEO");
		if ($sIn=="HIER") return("IER");
		if ($sIn=="HEU") return("E");
		if ($sIn=="HE") return("E");
		if ($sIn=="OS") return($sIn);
		if ($sIn=="RIZ") return("RI");
		if ($sIn=="RAZ") return("RA");

		// pré-traitements
		$sIn = preg_replace( '`OIN[GT]$`', 'OIN', $sIn );									// terminaisons OING -> OIN
		$sIn = preg_replace( '`E[RS]$`', 'E', $sIn ); 										// supression des terminaisons infinitifs et participes pluriels
		$sIn = preg_replace( '`(C|CH)OEU`', 'KE', $sIn ); 									// pré traitement OEU -> EU
		$sIn = preg_replace( '`MOEU`', 'ME', $sIn ); 										// pré traitement OEU -> EU
		$sIn = preg_replace( '`OE([UI]+)([BCDFGHJKLMNPQRSTVWXZ])`', 'E$1$2', $sIn ); 		// pré traitement OEU OEI -> E
		$sIn = preg_replace( '`^GEN[TS]$`', 'JAN', $sIn );									// pré traitement GEN -> JAN
		$sIn = preg_replace( '`CUEI`', 'KEI', $sIn ); 										// pré traitement accueil
		$sIn = preg_replace( '`([^AEIOUYC])AE([BCDFGHJKLMNPQRSTVWXZ])`', '$1E$2', $sIn ); 	// pré traitement AE -> E
		$sIn = preg_replace( '`AE([QS])`', 'E$1', $sIn ); 									// pré traitement AE -> E
		$sIn = preg_replace( '`AIE([BCDFGJKLMNPQRSTVWXZ])`', 'AI$1', $sIn );				// pré-traitement AIE(consonne) -> AI
		$sIn = preg_replace( '`ANIEM`', 'ANIM', $sIn ); 									// pré traitement NIEM -> NIM
		$sIn = preg_replace( '`(DRA|TRO|IRO)P$`', '$1', $sIn ); 							// P terminal muet
		$sIn = preg_replace( '`(LOM)B$`', '$1', $sIn ); 									// B terminal muet
		$sIn = preg_replace( '`(RON|POR)C$`', '$1', $sIn ); 								// C terminal muet
		$sIn = preg_replace( '`PECT$`', 'PET', $sIn ); 										// C terminal muet
		$sIn = preg_replace( '`ECUL$`', 'CU', $sIn ); 										// L terminal muet
		$sIn = preg_replace( '`(CHA|CA|E)M(P|PS)$`', '$1N', $sIn ); 		 				// P ou PS terminal muet
		$sIn = preg_replace( '`(TAN|RAN)G$`', '$1', $sIn ); 			 					// G terminal muet


		// sons YEUX
		$sIn = preg_replace( '`([^VO])ILAG`', '$1IAJ', $sIn );
		$sIn = preg_replace( '`([^TRH])UIL(AR|E)(.+)`', '$1UI$2$3', $sIn );
		$sIn = preg_replace( '`([G])UIL([AEO])`', '$1UI$2', $sIn );
		$sIn = preg_replace( '`([NSPM])AIL([AEO])`', '$1AI$2', $sIn );
		$convMIn  = array("DILAI","DILON","DILER","DILEM","RILON","TAILE","GAILET","AILAI","AILAR",
		"OUILA","EILAI","EILAR","EILER","EILEM","REILET","EILET","AILOL" );
		$convMOut = array( "DIAI", "DION","DIER", "DIEM", "RION", "TAIE", "GAIET", "AIAI", "AIAR",
		"OUIA", "AIAI", "AIAR", "AIER", "AIEM",  "RAIET", "EIET", "AIOL" );
		$sIn = str_replace( $convMIn, $convMOut, $sIn );
		$sIn = preg_replace( '`([^AEIOUY])(SC|S)IEM([EA])`', '$1$2IAM$3', $sIn ); 	// IEM -> IAM
		$sIn = preg_replace( '`^(SC|S)IEM([EA])`', '$1IAM$2', $sIn ); 				// IEM -> IAM

		// MP MB -> NP NB
		$convMIn  = array( 'OMB', 'AMB', 'OMP', 'AMP', 'IMB', 'EMP','GEMB','EMB', 'UMBL','CIEN');
		$convMOut = array( 'ONB', 'ANB', 'ONP', 'ANP', 'INB', 'ANP','JANB','ANB', 'INBL','SIAN');
		$sIn = str_replace( $convMIn, $convMOut, $sIn );

		// Sons en K
		$sIn = preg_replace( '`^ECHO$`', 'EKO', $sIn ); 	// cas particulier écho
		$sIn = preg_replace( '`^ECEUR`', 'EKEUR', $sIn ); 	// cas particulier écœuré
		// Choléra Chœur mais pas chocolat!
		$sIn = preg_replace( '`^CH(OG+|OL+|OR+|EU+|ARIS|M+|IRO|ONDR)`', 'K$1', $sIn ); 				//En début de mot
		$sIn = preg_replace( '`(YN|RI)CH(OG+|OL+|OC+|OP+|OM+|ARIS|M+|IRO|ONDR)`', '$1K$2', $sIn ); 	//Ou devant une consonne
		$sIn = preg_replace( '`CHS`', 'CH', $sIn );
		$sIn = preg_replace( '`CH(AIQ)`', 'K$1', $sIn );
		$sIn = preg_replace( '`^ECHO([^UIPY])`', 'EKO$1', $sIn );
		$sIn = preg_replace( '`ISCH(I|E)`', 'ISK$1', $sIn );
		$sIn = preg_replace( '`^ICHT`', 'IKT', $sIn );
		$sIn = preg_replace( '`ORCHID`', 'ORKID', $sIn );
		$sIn = preg_replace( '`ONCHIO`', 'ONKIO', $sIn );
		$sIn = preg_replace( '`ACHIA`', 'AKIA', $sIn );			// retouche ACHIA -> AKIA
		$sIn = preg_replace( '`([^C])ANICH`', '$1ANIK', $sIn );	// ANICH -> ANIK 	1/2
		$sIn = preg_replace( '`OMANIK`', 'OMANICH', $sIn ); 	// cas particulier 	2/2
		$sIn = preg_replace( '`ACHY([^D])`', 'AKI$1', $sIn );
		$sIn = preg_replace( '`([AEIOU])C([BDFGJKLMNPQRTVWXZ])`', '$1K$2', $sIn ); // voyelle, C, consonne sauf H
		$convPrIn  = array('EUCHA','YCHIA','YCHA','YCHO','YCHED','ACHEO','RCHEO','RCHES',
		'ECHN','OCHTO','CHORA','CHONDR','CHORE','MACHM','BRONCHO','LICHOS','LICHOC');
		$convPrOut = array('EKA',  'IKIA', 'IKA', 'IKO',  'IKED','AKEO','RKEO',  'RKES',
		'EKN',  'OKTO', 'KORA', 'KONDR' ,'KORE' ,'MAKM', 'BRONKO', 'LIKOS', 'LIKOC');
		$sIn = str_replace( $convPrIn, $convPrOut, $sIn );

		// Weuh (perfectible)
		$convPrIn  = array( 'WA','WO', 'WI','WHI','WHY','WHA','WHO');
		$convPrOut = array( 'OI', 'O','OUI','OUI','OUI','OUA', 'OU');
		$sIn = str_replace( $convPrIn, $convPrOut, $sIn );

		// Gueu, Gneu, Jeu et quelques autres
		$convPrIn  = array( 'GNES','GNET','GNER','GNE',  'GI', 'GNI','GNA','GNOU','GNUR','GY','OUGAIN',
		'AGEOL', 'AGEOT','GEOLO','GEOM','GEOP','GEOG','GEOS','GEORG','GEOR','NGEOT','UGEOT','GEOT','GEOD','GEOC','GEO','GEA','GE',
		'QU', 'Q',  'CY', 'CI', 'CN','ICM','CEAT','CE',
		'CR', 'CO', 'CUEI','CU', 'VENCA','CA', 'CS','CLEN','CL', 'CZ', 'CTIQ',
		'CTIF','CTIC','CTIS','CTIL','CTIO','CTI', 'CTU', 'CTE','CTO','CTR','CT', 'PH', 'TH',
		'OW', 'LH', 'RDL', 'CHLO', 'CHR', 'PTIA');
		$convPrOut = array( 'NIES','NIET','NIER', 'NE',  'JI',  'NI','NIA','NIOU','NIUR','JI','OUGIN',
		'AJOL',  'AJOT','JEOLO','JEOM','JEOP','JEOG','JEOS','JORJ','JEOR','NJOT','UJOT','JEOT','JEOD','JEOC', 'JO','JA' ,'JE',
		'K', 'K',  'SI', 'SI', 'KN','IKM', 'SAT','SE',
		'KR', 'KO', 'KEI','KU', 'VANSA','KA', 'KS','KLAN','KL', 'KZ', 'KTIK',
		'KTIF','KTIS','KTIS','KTIL','KSIO','KTI', 'KTU', 'KTE','KTO','KTR','KT', 'F',  'T',
		'OU',  'L',  'RL',  'KLO',  'KR', 'PSIA');
		$sIn = str_replace( $convPrIn, $convPrOut, $sIn );

		$sIn = preg_replace( '`GU([^RLMBSTPZN])`', 'G$1', $sIn ); // Gueu !
		$sIn = preg_replace( '`GNO([MLTNRKG])`', 'NIO$1', $sIn ); // GNO ! Tout sauf S pour gnos
		$sIn = preg_replace( '`GNO([MLTNRKG])`', 'NIO$1', $sIn ); // bis -> gnognotte! Si quelqu'un sait le faire en une seule regexp...


		// TI -> SI v2.0
		$convPrIn  = array( 'BUTIE','BUTIA','BATIA','ANTIEL','RETION','ENTIEL','ENTIAL','ENTIO','ENTIAI','UJETION','ATIEM','PETIEN',
		'CETIE','OFETIE','IPETI','LBUTION','BLUTION','LETION','LATION','SATIET');
		$convPrOut = array( 'BUSIE','BUSIA','BASIA','ANSIEL','RESION','ENSIEL','ENSIAL','ENSIO','ENSIAI','UJESION','ASIAM','PESIEN',
		'CESIE','OFESIE','IPESI','LBUSION','BLUSION','LESION','LASION','SASIET');
		$sIn = str_replace( $convPrIn, $convPrOut, $sIn );
		$sIn = preg_replace( '`(.+)ANTI(AL|O)`', '$1ANSI$2', $sIn ); // sauf antialcoolique, antialbumine, antialarmer, ...
		$sIn = preg_replace( '`(.+)INUTI([^V])`', '$1INUSI$2', $sIn ); // sauf inutilité, inutilement, diminutive, ...
		$sIn = preg_replace( '`([^O])UTIEN`', '$1USIEN', $sIn ); // sauf soutien, ...
		$sIn = preg_replace( '`([^DE])RATI[E]$`', '$1RASI$2', $sIn ); // sauf xxxxxcratique, ...
		// TIEN TION -> SIEN SION v3.1
		$sIn = preg_replace( '`([^SNEU]|KU|KO|RU|LU|BU|TU|AU)T(IEN|ION)`', '$1S$2', $sIn );


		// H muet
		$sIn = preg_replace( '`([^CS])H`', '$1', $sIn ); 	// H muet
		$sIn = str_replace( "ESH", "ES", $sIn );			// H muet
		$sIn = str_replace( "NSH", "NS", $sIn );			// H muet
		$sIn = str_replace( "SH", "CH", $sIn );				// ou pas!

		// NASALES
		$convNasIn  = array( 'OMT','IMB', 'IMP','UMD','TIENT','RIENT','DIENT','IEN',
		'YMU','YMO','YMA','YME', 'YMI','YMN','YM', 'AHO','FAIM','DAIM','SAIM','EIN','AINS');
		$convNasOut = array( 'ONT','INB', 'INP','OND','TIANT','RIANT','DIANT', 'IN',
		'IMU','IMO','IMA','IME', 'IMI','IMN','IN',  'AO', 'FIN','DIN', 'SIN','AIN','INS');
		$sIn = str_replace( $convNasIn, $convNasOut, $sIn );
		// AIN -> IN v2.0
		$sIn = preg_replace( '`AIN$`', 'IN', $sIn );
		$sIn = preg_replace( '`AIN([BTDK])`', 'IN$1', $sIn );
		// UN -> IN
		$sIn = preg_replace( '`([^O])UND`', '$1IND', $sIn ); // aucun mot français ne commence par UND!
		$sIn = preg_replace( '`([JTVLFMRPSBD])UN([^IAE])`', '$1IN$2', $sIn );
		$sIn = preg_replace( '`([JTVLFMRPSBD])UN$`', '$1IN', $sIn );
		$sIn = preg_replace( '`RFUM$`', 'RFIN', $sIn );
		$sIn = preg_replace( '`LUMB`', 'LINB', $sIn );
		// EN -> AN
		$sIn = preg_replace( '`([^BCDFGHJKLMNPQRSTVWXZ])EN`', '$1AN', $sIn );
		$sIn = preg_replace( '`([VTLJMRPDSBFKNG])EN([BRCTDKZSVN])`', '$1AN$2', $sIn ); // deux fois pour les motifs recouvrants malentendu, pendentif, ...
		$sIn = preg_replace( '`([VTLJMRPDSBFKNG])EN([BRCTDKZSVN])`', '$1AN$2', $sIn ); // si quelqu'un sait faire avec une seule regexp!
		$sIn = preg_replace( '`^EN([BCDFGHJKLNPQRSTVXZ]|CH|IV|ORG|OB|UI|UA|UY)`', 'AN$1', $sIn );
		$sIn = preg_replace( '`(^[JRVTH])EN([DRTFGSVJMP])`', '$1AN$2', $sIn );
		$sIn = preg_replace( '`SEN([ST])`', 'SAN$1', $sIn );
		$sIn = preg_replace( '`^DESENIV`', 'DESANIV', $sIn );
		$sIn = preg_replace( '`([^M])EN(UI)`', '$1AN$2', $sIn );
		$sIn = preg_replace( '`(.+[JTVLFMRPSBD])EN([JLFDSTG])`', '$1AN$2', $sIn );
		// EI -> AI
		$sIn = preg_replace( '`([VSBSTNRLPM])E[IY]([ACDFRJLGZ])`', '$1AI$2', $sIn );

		// Histoire d'Ô
		$convNasIn  = array( 'EAU', 'EU',  'Y', 'EOI', 'JEA','OIEM','OUANJ','OUA','OUENJ');
		$convNasOut = array(   'O',  'E',  'I',  'OI', 'JA' ,'OIM' ,'OUENJ', 'OI','OUANJ');
		$sIn = str_replace( $convNasIn, $convNasOut, $sIn );
		$sIn = preg_replace( '`AU([^E])`', 'O$1', $sIn ); // AU sans E qui suit

		// Les retouches!
		$sIn = preg_replace( '`^BENJ`', 'BINJ', $sIn );				// retouche BENJ -> BINJ
		$sIn = preg_replace( '`RTIEL`', 'RSIEL', $sIn );			// retouche RTIEL -> RSIEL
		$sIn = preg_replace( '`PINK`', 'PONK', $sIn );				// retouche PINK -> PONK
		$sIn = preg_replace( '`KIND`', 'KOND', $sIn );				// retouche KIND -> KOND
		$sIn = preg_replace( '`KUM(N|P)`', 'KON$1', $sIn );			// retouche KUMN KUMP
		$sIn = preg_replace( '`LKOU`', 'LKO', $sIn );				// retouche LKOU -> LKO
		$sIn = preg_replace( '`EDBE`', 'EBE', $sIn );				// retouche EDBE pied-bœuf
		$sIn = preg_replace( '`ARCM`', 'ARKM', $sIn );				// retouche SCH -> CH
		$sIn = preg_replace( '`SCH`', 'CH', $sIn );					// retouche SCH -> CH
		$sIn = preg_replace( '`^OINI`', 'ONI', $sIn );				// retouche début OINI -> ONI
		$sIn = preg_replace( '`([^NDCGRHKO])APT`', '$1AT', $sIn );	// retouche APT -> AT
		$sIn = preg_replace( '`([L]|KON)PT`', '$1T', $sIn );		// retouche LPT -> LT
		$sIn = preg_replace( '`OTB`', 'OB', $sIn );					// retouche OTB -> OB (hautbois)
		$sIn = preg_replace( '`IXA`', 'ISA', $sIn );				// retouche IXA -> ISA
		$sIn = preg_replace( '`TG`', 'G', $sIn );					// retouche TG -> G
		$sIn = preg_replace( '`^TZ`', 'TS', $sIn );					// retouche début TZ -> TS
		$sIn = preg_replace( '`PTIE`', 'TIE', $sIn );				// retouche PTIE -> TIE
		$sIn = preg_replace( '`GT`', 'T', $sIn );					// retouche GT -> T
		$sIn = str_replace( "ANKIEM", "ANKILEM", $sIn );			// retouche tranquillement
		$sIn = preg_replace( "`(LO|RE)KEMAN`", "$1KAMAN", $sIn );	// KEMAN -> KAMAN
		$sIn = preg_replace( '`NT(B|M)`', 'N$1', $sIn );			// retouche TB -> B  TM -> M
		$sIn = preg_replace( '`GSU`', 'SU', $sIn );					// retouche GS -> SU
		$sIn = preg_replace( '`ESD`', 'ED', $sIn );					// retouche ESD -> ED
		$sIn = preg_replace( '`LESKEL`','LEKEL', $sIn );			// retouche LESQUEL -> LEKEL
		$sIn = preg_replace( '`CK`', 'K', $sIn );					// retouche CK -> K

		// Terminaisons
		$sIn = preg_replace( '`USIL$`', 'USI', $sIn ); 				// terminaisons USIL -> USI
		$sIn = preg_replace( '`X$|[TD]S$|[DS]$`', '', $sIn );		// terminaisons TS DS LS X T D S...  v2.0
		$sIn = preg_replace( '`([^KL]+)T$`', '$1', $sIn );			// sauf KT LT terminal
		$sIn = preg_replace( '`^[H]`', '', $sIn );					// H pseudo muet en début de mot, je sais, ce n'est pas une terminaison
		$sBack2=$sIn;												// on sauve le code (utilisé pour les mots très courts)
		$sIn = preg_replace( '`TIL$`', 'TI', $sIn );				// terminaisons TIL -> TI
		$sIn = preg_replace( '`LC$`', 'LK', $sIn );					// terminaisons LC -> LK
		$sIn = preg_replace( '`L[E]?[S]?$`', 'L', $sIn );			// terminaisons LE LES -> L
		$sIn = preg_replace( '`(.+)N[E]?[S]?$`', '$1N', $sIn );		// terminaisons NE NES -> N
		$sIn = preg_replace( '`EZ$`', 'E', $sIn );					// terminaisons EZ -> E
		$sIn = preg_replace( '`OIG$`', 'OI', $sIn );				// terminaisons OIG -> OI
		$sIn = preg_replace( '`OUP$`', 'OU', $sIn );				// terminaisons OUP -> OU
		$sIn = preg_replace( '`([^R])OM$`', '$1ON', $sIn );			// terminaisons OM -> ON sauf ROM
		$sIn = preg_replace( '`LOP$`', 'LO', $sIn );				// terminaisons LOP -> LO
		$sIn = preg_replace( '`NTANP$`', 'NTAN', $sIn );			// terminaisons NTANP -> NTAN
		$sIn = preg_replace( '`TUN$`', 'TIN', $sIn );				// terminaisons TUN -> TIN
		$sIn = preg_replace( '`AU$`', 'O', $sIn );					// terminaisons AU -> O
		$sIn = preg_replace( '`EI$`', 'AI', $sIn );					// terminaisons EI -> AI
		$sIn = preg_replace( '`R[DG]$`', 'R', $sIn );				// terminaisons RD RG -> R
		$sIn = preg_replace( '`ANC$`', 'AN', $sIn );				// terminaisons ANC -> AN
		$sIn = preg_replace( '`KROC$`', 'KRO', $sIn );				// terminaisons C muet de CROC, ESCROC
		$sIn = preg_replace( '`HOUC$`', 'HOU', $sIn );				// terminaisons C muet de CAOUTCHOUC
		$sIn = preg_replace( '`OMAC$`', 'OMA', $sIn );				// terminaisons C muet de ESTOMAC (mais pas HAMAC)
		$sIn = preg_replace( '`([J])O([NU])[CG]$`', '$1O$2', $sIn );// terminaisons C et G muet de OUC ONC OUG
		$sIn = preg_replace( '`([^GTR])([AO])NG$`', '$1$2N', $sIn );// terminaisons G muet ANG ONG sauf GANG GONG TANG TONG
		$sIn = preg_replace( '`UC$`', 'UK', $sIn );					// terminaisons UC -> UK
		$sIn = preg_replace( '`AING$`', 'IN', $sIn );				// terminaisons AING -> IN
		$sIn = preg_replace( '`([EISOARN])C$`', '$1K', $sIn );		// terminaisons C -> K
		$sIn = preg_replace( '`([ABD-MO-Z]+)[EH]+$`', '$1', $sIn );	// terminaisons E ou H sauf pour C et N
		$sIn = preg_replace( '`EN$`', 'AN', $sIn );					// terminaisons EN -> AN (difficile à faire avant sans avoir des soucis) Et encore, c'est pas top!
		$sIn = preg_replace( '`(NJ)EN$`', '$1AN', $sIn );			// terminaisons EN -> AN
		$sIn = preg_replace( '`^PAIEM`', 'PAIM', $sIn ); 			// PAIE -> PAI
		$sIn = preg_replace( '`([^NTB])EF$`', '\1', $sIn );			// F muet en fin de mot
		$sIn = preg_replace( '`(.)\1`', '$1', $sIn ); 				// supression des répétitions (suite à certains remplacements)

		// cas particuliers, bah au final, je n'en ai qu'un ici
		$convPartIn  = array( 'FUEL');
		$convPartOut = array( 'FIOUL');
		$sIn = str_replace( $convPartIn, $convPartOut, $sIn );

		// Ce sera le seul code retourné à une seule lettre!
		if ($sIn=='O') return($sIn);

		// seconde chance sur les mots courts qui ont souffert de la simplification
		if (strlen($sIn)<2)
		{
			// Sigles ou abréviations
			if (preg_match("`[BCDFGHJKLMNPQRSTVWXYZ][BCDFGHJKLMNPQRSTVWXYZ][BCDFGHJKLMNPQRSTVWXYZ][BCDFGHJKLMNPQRSTVWXYZ]*`",$sBack))
				return($sBack);

			if (preg_match("`[RFMLVSPJDF][AEIOU]`",$sBack))
			{
				if (strlen($sBack)==3)
					return(substr($sBack,0,2));// mots de trois lettres supposés simples
				if (strlen($sBack)==4)
					return(substr($sBack,0,3));// mots de quatre lettres supposés simples
			}

			if (strlen($sBack2)>1) return $sBack2;
		}
		if (strlen($sIn)>1)
		{
			return substr($sIn,0,16); // Je limite à 16 caractères mais vous faites comme vous voulez!
		}
		else return '';
		}
}
?>