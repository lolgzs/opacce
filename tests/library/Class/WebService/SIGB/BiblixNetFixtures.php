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

class BiblixNetFixtures {

	public static function xmlGetRecordsCenseAlouettes() {
		return '<?xml version="1.0" encoding="UTF-8"?>
	 <dlf:collection xmlns:dlf="http://diglib.org/ilsdi/1.1" xmlns:marcxml="http://www.loc.gov/MARC21/slim">
		 <dlf:record>
			 <dlf:bibliographic id="3">
				 <marcxml:leader>02337nam 2200169 4500</marcxml:leader>
				 <marcxml:record>
					 <marcxml:controlfield tag="005">19970905</marcxml:controlfield>
					 <marcxml:datafield tag="10" ind1=" " ind2=" ">
						 <marcxml:subfield code="b">Presses de la Cité</marcxml:subfield>
					 </marcxml:datafield>
					 <marcxml:datafield tag="100" ind1=" " ind2=" ">
						 <marcxml:subfield code="a">19970905d1997 m 0fre 0103</marcxml:subfield>
					 </marcxml:datafield>
					 <marcxml:datafield tag="102" ind1="0" ind2=" ">
						 <marcxml:subfield code="a">fre</marcxml:subfield>
					 </marcxml:datafield>
					 <marcxml:datafield tag="200" ind1="1" ind2=" ">
						 <marcxml:subfield code="a">La Cense aux Alouettes</marcxml:subfield>
						 <marcxml:subfield code="f">Marie-Paul Armand</marcxml:subfield>
					 </marcxml:datafield>
					 <marcxml:datafield tag="210" ind1=" " ind2=" ">
						 <marcxml:subfield code="a">Paris</marcxml:subfield>
						 <marcxml:subfield code="c">Presses de la Cité</marcxml:subfield>
						 <marcxml:subfield code="d">1997</marcxml:subfield>
					 </marcxml:datafield>
					 <marcxml:datafield tag="215" ind1=" " ind2=" ">
						 <marcxml:subfield code="a">366 p.</marcxml:subfield>
					 </marcxml:datafield>
					 <marcxml:datafield tag="225" ind1="2" ind2=" ">
						 <marcxml:subfield code="a">Presses de la Cité</marcxml:subfield>
					 </marcxml:datafield>
					 <marcxml:datafield tag="410" ind1=" " ind2="0">
						 <marcxml:subfield code="t">Presses de la Cité</marcxml:subfield>
					 </marcxml:datafield>
					 <marcxml:datafield tag="700" ind1=" " ind2="0">
						 <marcxml:subfield code="a">Armand</marcxml:subfield>
						 <marcxml:subfield code="b">Marie-Paul</marcxml:subfield>
						 <marcxml:subfield code="4">070</marcxml:subfield>
					 </marcxml:datafield>
					 <marcxml:datafield tag="801" ind1=" " ind2="1">
						 <marcxml:subfield code="a">FR</marcxml:subfield>
						 <marcxml:subfield code="b"/>
						 <marcxml:subfield code="c">19970905</marcxml:subfield>
						 <marcxml:subfield code="w">BiblixNet 2.0</marcxml:subfield>
					 </marcxml:datafield>
					 <marcxml:datafield tag="995" ind1=" " ind2=" ">
						 <marcxml:subfield code="b">Mediatheque</marcxml:subfield>
						 <marcxml:subfield code="f">1000025966311</marcxml:subfield>
						 <marcxml:subfield code="j">a</marcxml:subfield>
						 <marcxml:subfield code="k">R ARM C</marcxml:subfield>
						 <marcxml:subfield code="o">Disponible</marcxml:subfield>
						 <marcxml:subfield code="u">Bon auteur</marcxml:subfield>
					 </marcxml:datafield>
					 <marcxml:datafield tag="995" ind1=" " ind2=" ">
						 <marcxml:subfield code="b">Mediatheque</marcxml:subfield>
						 <marcxml:subfield code="f">1000025966323</marcxml:subfield>
						 <marcxml:subfield code="j">a</marcxml:subfield>
						 <marcxml:subfield code="k">R ARM C</marcxml:subfield>
						 <marcxml:subfield code="o">En reliure</marcxml:subfield>
					 </marcxml:datafield>
					 <marcxml:datafield tag="995" ind1=" " ind2=" ">
						 <marcxml:subfield code="b">Mediatheque</marcxml:subfield>
						 <marcxml:subfield code="f">1000025777323</marcxml:subfield>
						 <marcxml:subfield code="j">a</marcxml:subfield>
						 <marcxml:subfield code="k">R ARM C</marcxml:subfield>
						 <marcxml:subfield code="o">Sorti</marcxml:subfield>
						 <marcxml:subfield code="n">2012-04-19T00:00:00+02:00</marcxml:subfield>
					 </marcxml:datafield>
				 </marcxml:record>
			 </dlf:bibliographic>
		 </dlf:record>
	 </dlf:collection>';
	}



	/** @return string */
	public static function xmlGetPatronJustinTicou() {
		return '<?xml version="1.0" encoding="utf-8"?>
<GetPatronInfo>
  <patronId>34</patronId>
  <lastName>TICOU</lastName>
  <firstName>Justin</firstName>
  <loans>
    <loan>
      <itemId>196895</itemId>
      <barcode>1069005966314</barcode>
      <title>Béart en public</title>
			<dueDate>2011-05-04</dueDate>
    </loan>
    <loan>
      <itemId>107177</itemId>
      <barcode>1089005966245</barcode>
      <title>Les Finances publiques et la réforme budgétaire</title>
			<dueDate>2029-05-04</dueDate>
    </loan>
  </loans>
  <holds>
    <hold>
      <itemId>7307</itemId>
			<state>En attente</state>
			<priority>2</priority>
    </hold>
    <hold>
      <itemId>14586</itemId>
			<state>Disponible</state>
			<priority>3</priority>
    </hold>
  </holds>
</GetPatronInfo>';
	}


	/** @return string */
	public static function xmlHoldTitleSuccess() {
		return '<?xml version="1.0" encoding="UTF-8"?>
						<HoldTitle></HoldTitle>';
	}


	/** @return string */
	public static function xmlHoldTitleError() {
		return '<?xml version="1.0" encoding="UTF-8"?>
						<HoldTitle><code>Deja Reserve</code><code>NotHoldable</code></HoldTitle>';
	}


	/** @return string */
	public static function xmlCancelHoldSuccess() {
		return '<?xml version="1.0" encoding="UTF-8"?>
						<CancelHold></CancelHold>';
	}


	/** @return string */
	public static function xmlCancelHoldError() {
		return '<?xml version="1.0" encoding="UTF-8"?>
						<CancelHold><code>RecordNotFound</code></CancelHold>';
	}


	/** @return string */
	public static function xmlRenewLoanSuccess() {
		return '<?xml version="1.0" encoding="UTF-8"?>
						<RenewLoan></RenewLoan>';
	}


	/** @return string */
	public static function xmlRenewLoanError() {
		return '<?xml version="1.0" encoding="UTF-8"?>
						<RenewLoan><code>NotAuthorized</code></RenewLoan>';
	}
}
