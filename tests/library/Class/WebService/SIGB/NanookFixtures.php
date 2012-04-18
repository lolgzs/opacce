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
class NanookFixtures {
	/** @return string */
	public static function xmlCancelHoldError() {
		return '<?xml version="1.0" encoding="UTF-8"?>
						<CancelHold>
							<error>NotCanceled</error>
						</CancelHold>';
	}

	/** @return string */
	public static function xmlCancelHoldSuccess() {
		return '<?xml version="1.0" encoding="UTF-8"?>
						<CancelHold></CancelHold>';
	}


	/** @return string */
	public static function xmlHoldTitleError() {
		return '<?xml version="1.0" encoding="UTF-8"?>
						<HoldTitle>
							<error>NotHoldable</error>
						</HoldTitle>';
	}


	/** @return string */
	public static function xmlHoldTitleSuccess() {
		return '<?xml version="1.0" encoding="UTF-8"?>
						<HoldTitle></HoldTitle>';
	}


	/** @return string */
	public static function xmlRenewLoanError() {
		return '<?xml version="1.0" encoding="UTF-8"?>
						<RenewLoan>
							<error>NotRenewable</error>
						</RenewLoan>';
	}


	/** @return string */
	public static function xmlRenewLoanSucces() {
		return '<?xml version="1.0" encoding="UTF-8"?>
						<RenewLoan></RenewLoan>';
	}


	/** @return string */
	public static function xmlGetPatronError() {
		return '<?xml version="1.0" encoding="UTF-8"?>
						<GetPatronInfo>
							<error>PatronNotFound</error>
						</GetPatronInfo>';
	}


	/** @return string */
	public static function xmlGetRecordLiliGrisbiAndCo() {
		return '<?xml version="1.0" encoding="UTF-8"?>
		<GetRecords>
				<record>
						<bibId>9842</bibId>
						<title>Lili, Grisbi et Compagnie</title>
						<items>
								<item>
										<barcode>L-007552</barcode>
										<itemId>10713</itemId>
										<available>1</available>
										<holdable>0</holdable>
										<visible>1</visible>
										<locationLabel>Annecy</locationLabel>
										<locationId>3</locationId>
                    <activityMessage>Se renseigner a l\'accueil</activityMessage>
								</item>
								<item>
										<barcode>L-072666</barcode>
										<itemId>10714</itemId>
										<available>0</available>
										<holdable>1</holdable>
										<visible>1</visible>
										<dueDate>2029-01-12</dueDate>
										<locationLabel>Cran-Gevrier</locationLabel>
										<locationId>2</locationId>
								</item>
						</items>
				</record>
		</GetRecords>';
	}


	/** @return string */
	public static function xmlGetRecordError() {
		return '<?xml version="1.0" encoding="UTF-8"?>
						<GetRecords>
							<error>RecordNotFound</error>
						</GetRecords>';
	}


	/** @return string */
	public static function xmlGetPatronChristelDelpeyroux() {
		return '<?xml version="1.0" encoding="utf-8"?>
<GetPatronInfo>
  <patronId>1</patronId>
  <lastName>DELPEYROUX</lastName>
  <firstName>Christel</firstName>
  <loans>
    <loan>
      <bibId>117661</bibId>
      <itemId>196895</itemId>
      <title>Béart en public</title>
      <author>Guy Béart</author>
      <locationLabel>Site Principal</locationLabel>
			<dueDate>2011-05-04</dueDate>
    </loan>
    <loan>
      <bibId>83413</bibId>
      <itemId>107177</itemId>
      <title>Les Finances publiques et la réforme budgétaire</title>
      <author></author>
      <locationLabel>Site Secondaire</locationLabel>
			<dueDate>2029-05-04</dueDate>
    </loan>
    <loan>
      <bibId>88200</bibId>
      <itemId>112956</itemId>
      <title>Finances publiques</title>
      <author>André Roux</author>
      <locationLabel>Site Principal</locationLabel>
			<dueDate>2029-06-04</dueDate>
    </loan>
  </loans>
  <holds>
    <hold>
      <bibId>7307</bibId>
      <itemId>7105</itemId>
      <title>Contes des quatre vents</title>
      <author>Natha Caputo</author>
      <locationLabel>Site Principal</locationLabel>
			<priority>1</priority>
    </hold>
    <hold>
      <bibId>12501</bibId>
      <itemId>14586</itemId>
      <title>Le Chant du lac</title>
      <author>Olympe Bhêly-Quénum</author>
      <locationLabel>Site Principal</locationLabel>
			<priority>49</priority>
      <available>1</available>
    </hold>
    <hold>
      <bibId>19954</bibId>
      <itemId>25732</itemId>
      <title>Contes et légendes du Québec</title>
      <author>Charles Le Blanc</author>
      <locationLabel>Site Principal</locationLabel>
    </hold>
  </holds>
</GetPatronInfo>';
	}


	/**
	 * @return string
	 */
	public static function htmlTomcatError() {
		return '<html>
			<head>
				<title>Apache Tomcat/6.0.32 - Rapport d\'erreur</title>
				<style><!--H1 {font-family:Tahoma,Arial,sans-serif;color:white;background-color:#525D76;font-size:22px;}
				H2 {font-family:Tahoma,Arial,sans-serif;color:white;background-color:#525D76;font-size:16px;}
				H3 {font-family:Tahoma,Arial,sans-serif;color:white;background-color:#525D76;font-size:14px;}
				BODY {font-family:Tahoma,Arial,sans-serif;color:black;background-color:white;}
				B {font-family:Tahoma,Arial,sans-serif;color:white;background-color:#525D76;}
				P {font-family:Tahoma,Arial,sans-serif;background:white;color:black;font-size:12px;}
				A {color : black;}A.name {color : black;}HR {color : #525D76;}--></style>
			</head>
			<body>
				<h1>Etat HTTP 404 - </h1>
				<HR size="1" noshade="noshade">
				<p><b>type</b> Rapport d\'état</p><p><b>message</b> <u></u></p>
				<p><b>description</b> <u>La ressource demandée () n\'est pas disponible.</u></p>
				<HR size="1" noshade="noshade">
				<h3>Apache Tomcat/6.0.32</h3>
			</body>
		</html>';
	}
}