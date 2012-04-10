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

class KohaFixtures {
	public static function xmlGetRecordOneJardinEnfance() {
		return
			'<?xml version="1.0" encoding="ISO-8859-1" ?>
			<GetRecords>
				<record>
					<biblioitemnumber>1</biblioitemnumber>
					<isbn>9782862749198</isbn>
					<marcxml>
						<record
								xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
								xsi:schemaLocation="http://www.loc.gov/MARC21/slim http://www.loc.gov/ standards/marcxml/schema/MARC21slim.xsd"
								xmlns="http://www.loc.gov/MARC21/slim">
							<leader>00397nac a22001451u 4500</leader>
							<datafield tag="010" ind1=" " ind2=" ">
								<subfield code="a">9782862749198</subfield>
							</datafield>
							<datafield tag="090" ind1=" " ind2=" ">
								<subfield code="a">1</subfield>
							</datafield>
							<datafield tag="101" ind1=" " ind2=" ">
								<subfield code="a">fre</subfield>
							</datafield>
							<datafield tag="100" ind1=" " ind2=" ">
								<subfield code="a">20080725              frey50       </subfield>
							</datafield>
							<datafield tag="200" ind1=" " ind2=" ">
								<subfield code="a">Jardins d\'enfance</subfield>
								<subfield code="b">LITT</subfield>
								<subfield code="f">Abécassis, Eliette</subfield>
							</datafield>
							<datafield tag="210" ind1=" " ind2=" ">
								<subfield code="c">cherche midi éditeur</subfield>
								<subfield code="d">11/2001</subfield>
							</datafield>
							<datafield tag="215" ind1=" " ind2=" ">
								<subfield code="a">180</subfield>
							</datafield>
							<datafield tag="225" ind1=" " ind2=" ">
								<subfield code="a">nouvelles</subfield>
							</datafield>
							<datafield tag="995" ind1=" " ind2=" ">
								<subfield code="9">1</subfield>
								<subfield code="c">BIB</subfield>
								<subfield code="2">0</subfield>
								<subfield code="k">R ABE</subfield>
								<subfield code="o">0</subfield>
								<subfield code="e">Secteur Adulte</subfield>
								<subfield code="b">BIB</subfield>
								<subfield code="j">7786000200</subfield>
								<subfield code="q">a</subfield>
								<subfield code="r">2</subfield>
								<subfield code="s">Achats</subfield>
							</datafield>
							<controlfield tag="001">1</controlfield>
						</record>
					</marcxml>
					<publicationyear>2001</publicationyear>
					<collectiontitle>nouvelles</collectiontitle>
					<pages>180</pages>
					<issues>
					</issues>
					<itemtype>LITT</itemtype>
					<biblionumber>1</biblionumber>
					<timestamp>2008-09-03 18:43:19</timestamp>
					<cn_sort>_</cn_sort>
					<publishercode>cherche midi éditeur</publishercode>
					<reserves>
					</reserves>
					<items>
						<item>
							<biblioitemnumber>1</biblioitemnumber>
							<wthdrawn>0</wthdrawn>
							<holdingbranchname>Bibliothèque Jean Prunier</holdingbranchname>
							<notforloan>0</notforloan>
							<replacementpricedate>2008-08-20</replacementpricedate>
							<itemnumber>1</itemnumber>
							<location>Secteur Adulte</location>
							<itemcallnumber>R ABE</itemcallnumber>
							<date_due>2011-08-20</date_due>
							<itemlost>0</itemlost>
							<datelastseen>2008-08-20</datelastseen>
							<homebranch>BIB</homebranch>
							<homebranchname>Bibliothèque Jean Prunier</homebranchname>
							<biblionumber>1</biblionumber>
							<holdingbranch>BIB</holdingbranch>
							<timestamp>2008-08-20 17:15:51</timestamp>
							<damaged>0</damaged>
							<cn_sort>R_ABE</cn_sort>
							<dateaccessioned>2008-08-20</dateaccessioned>
						</item>
					</items>
				</record>
     </GetRecords>';
	}


	public static function xmlGetRecordHarryPotter() {
		return
				'<?xml version="1.0" encoding="UTF-8" ?>
				<GetRecords>
					<record>
						<biblioitemnumber>33233</biblioitemnumber>
						<isbn>2-07-051842-6</isbn>
						<marcxml>&lt;?xml version=&quot;1.0&quot; encoding=&quot;UTF-8&quot;?&gt;
&lt;record
    xmlns:xsi=&quot;http://www.w3.org/2001/XMLSchema-instance&quot;
    xsi:schemaLocation=&quot;http://www.loc.gov/MARC21/slim http://www.loc.gov/standards/marcxml/schema/MARC21slim.xsd&quot;
    xmlns=&quot;http://www.loc.gov/MARC21/slim&quot;&gt;

  &lt;leader&gt;01158     2200277   4500&lt;/leader&gt;
  &lt;datafield tag=&quot;010&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;a&quot;&gt;2-07-051842-6&lt;/subfield&gt;
    &lt;subfield code=&quot;d&quot;&gt;39 F&lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;035&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;a&quot;&gt;0223985&lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;101&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;a&quot;&gt;fre&lt;/subfield&gt;
    &lt;subfield code=&quot;c&quot;&gt;eng&lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;102&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;a&quot;&gt;fr&lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;105&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;a&quot;&gt;a|||z|||000ay&lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;100&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;a&quot;&gt;20110218              frey50       &lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;200&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;a&quot;&gt;Harry Potter à l\'école des sorciers&lt;/subfield&gt;
    &lt;subfield code=&quot;f&quot;&gt;J. K. Rowling&lt;/subfield&gt;
    &lt;subfield code=&quot;g&quot;&gt;trad. de l\'anglais par Jean-François Ménard&lt;/subfield&gt;
    &lt;subfield code=&quot;g&quot;&gt;ill. Emily Walcker&lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;210&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;a&quot;&gt;Paris&lt;/subfield&gt;
    &lt;subfield code=&quot;c&quot;&gt;Gallimard jeunesse&lt;/subfield&gt;
    &lt;subfield code=&quot;d&quot;&gt;1998&lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;215&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;a&quot;&gt;306 p.&lt;/subfield&gt;
    &lt;subfield code=&quot;c&quot;&gt;ill.&lt;/subfield&gt;
    &lt;subfield code=&quot;d&quot;&gt;18 cm&lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;225&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;a&quot;&gt;Folio junior&lt;/subfield&gt;
    &lt;subfield code=&quot;v&quot;&gt;899&lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;410&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;9&quot;&gt;747&lt;/subfield&gt;
    &lt;subfield code=&quot;t&quot;&gt;Folio junior&lt;/subfield&gt;
    &lt;subfield code=&quot;v&quot;&gt;899&lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;461&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;9&quot;&gt;109679&lt;/subfield&gt;
    &lt;subfield code=&quot;t&quot;&gt;Harry Potter&lt;/subfield&gt;
    &lt;subfield code=&quot;v&quot;&gt;1&lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;676&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;a&quot;&gt;823&lt;/subfield&gt;
    &lt;subfield code=&quot;v&quot;&gt;20a&lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;700&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;9&quot;&gt;8635
&lt;/subfield&gt;
    &lt;subfield code=&quot;a&quot;&gt;Rowling&lt;/subfield&gt;
    &lt;subfield code=&quot;b&quot;&gt;J. K.&lt;/subfield&gt;
    &lt;subfield code=&quot;f&quot;&gt;1967-....&lt;/subfield&gt;
    &lt;subfield code=&quot;4&quot;&gt;070&lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;702&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;9&quot;&gt;8637
&lt;/subfield&gt;
    &lt;subfield code=&quot;a&quot;&gt;Walcker&lt;/subfield&gt;
    &lt;subfield code=&quot;b&quot;&gt;Emily&lt;/subfield&gt;
    &lt;subfield code=&quot;f&quot;&gt;1972-....&lt;/subfield&gt;
    &lt;subfield code=&quot;4&quot;&gt;440&lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;702&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;9&quot;&gt;52339
&lt;/subfield&gt;
    &lt;subfield code=&quot;a&quot;&gt;Ménard&lt;/subfield&gt;
    &lt;subfield code=&quot;b&quot;&gt;Jean-François&lt;/subfield&gt;
    &lt;subfield code=&quot;c&quot;&gt;romancier pour la jeunesse&lt;/subfield&gt;
    &lt;subfield code=&quot;f&quot;&gt;1948-....&lt;/subfield&gt;
    &lt;subfield code=&quot;4&quot;&gt;730&lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;801&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;a&quot;&gt;fr&lt;/subfield&gt;
    &lt;subfield code=&quot;b&quot;&gt;BD Meuse&lt;/subfield&gt;
    &lt;subfield code=&quot;c&quot;&gt;19990118&lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;995&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;1&quot;&gt;0&lt;/subfield&gt;
    &lt;subfield code=&quot;2&quot;&gt;0&lt;/subfield&gt;
    &lt;subfield code=&quot;3&quot;&gt;0&lt;/subfield&gt;
    &lt;subfield code=&quot;9&quot;&gt;33028&lt;/subfield&gt;
    &lt;subfield code=&quot;b&quot;&gt;BDM&lt;/subfield&gt;
    &lt;subfield code=&quot;c&quot;&gt;BDM&lt;/subfield&gt;
    &lt;subfield code=&quot;f&quot;&gt;2661690090&lt;/subfield&gt;
    &lt;subfield code=&quot;h&quot;&gt;ROMJEUN&lt;/subfield&gt;
    &lt;subfield code=&quot;i&quot;&gt;richezmajuscule&lt;/subfield&gt;
    &lt;subfield code=&quot;j&quot;&gt;J&lt;/subfield&gt;
    &lt;subfield code=&quot;k&quot;&gt;JR ROW h&lt;/subfield&gt;
    &lt;subfield code=&quot;o&quot;&gt;0&lt;/subfield&gt;
    &lt;subfield code=&quot;r&quot;&gt;LIV&lt;/subfield&gt;
    &lt;subfield code=&quot;x&quot;&gt;1&lt;/subfield&gt;
    &lt;subfield code=&quot;z&quot;&gt;9&lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;995&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;1&quot;&gt;0&lt;/subfield&gt;
    &lt;subfield code=&quot;2&quot;&gt;0&lt;/subfield&gt;
    &lt;subfield code=&quot;3&quot;&gt;0&lt;/subfield&gt;
    &lt;subfield code=&quot;9&quot;&gt;33029&lt;/subfield&gt;
    &lt;subfield code=&quot;b&quot;&gt;BDM&lt;/subfield&gt;
    &lt;subfield code=&quot;c&quot;&gt;BDM&lt;/subfield&gt;
    &lt;subfield code=&quot;f&quot;&gt;2661680090&lt;/subfield&gt;
    &lt;subfield code=&quot;h&quot;&gt;ROMJEUN&lt;/subfield&gt;
    &lt;subfield code=&quot;i&quot;&gt;richezmajuscule&lt;/subfield&gt;
    &lt;subfield code=&quot;j&quot;&gt;J&lt;/subfield&gt;
    &lt;subfield code=&quot;k&quot;&gt;JR ROW h&lt;/subfield&gt;
    &lt;subfield code=&quot;o&quot;&gt;0&lt;/subfield&gt;
    &lt;subfield code=&quot;r&quot;&gt;LIV&lt;/subfield&gt;
    &lt;subfield code=&quot;x&quot;&gt;1&lt;/subfield&gt;
    &lt;subfield code=&quot;z&quot;&gt;10&lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;995&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;1&quot;&gt;0&lt;/subfield&gt;
    &lt;subfield code=&quot;2&quot;&gt;0&lt;/subfield&gt;
    &lt;subfield code=&quot;3&quot;&gt;0&lt;/subfield&gt;
    &lt;subfield code=&quot;9&quot;&gt;33030&lt;/subfield&gt;
    &lt;subfield code=&quot;b&quot;&gt;BDM&lt;/subfield&gt;
    &lt;subfield code=&quot;c&quot;&gt;BDM&lt;/subfield&gt;
    &lt;subfield code=&quot;f&quot;&gt;2661660090&lt;/subfield&gt;
    &lt;subfield code=&quot;h&quot;&gt;ROMJEUN&lt;/subfield&gt;
    &lt;subfield code=&quot;i&quot;&gt;richezmajuscule&lt;/subfield&gt;
    &lt;subfield code=&quot;j&quot;&gt;J&lt;/subfield&gt;
    &lt;subfield code=&quot;k&quot;&gt;JR ROW h&lt;/subfield&gt;
    &lt;subfield code=&quot;o&quot;&gt;0&lt;/subfield&gt;
    &lt;subfield code=&quot;r&quot;&gt;LIV&lt;/subfield&gt;
    &lt;subfield code=&quot;x&quot;&gt;1&lt;/subfield&gt;
    &lt;subfield code=&quot;z&quot;&gt;14&lt;/subfield&gt;
  &lt;/datafield&gt;
  &lt;datafield tag=&quot;999&quot; ind1=&quot; &quot; ind2=&quot; &quot;&gt;
    &lt;subfield code=&quot;9&quot;&gt;33233&lt;/subfield&gt;
    &lt;subfield code=&quot;a&quot;&gt;33233&lt;/subfield&gt;
  &lt;/datafield&gt;
&lt;/record&gt;
</marcxml>
						<publicationyear>1998</publicationyear>
						<collectiontitle>Folio junior</collectiontitle>
						<pages>306 p.</pages>
						<issues>
							<issue>
								<author>J. K. Rowling</author>
								<biblionumber>33233</biblionumber>
								<timestamp>2011-02-18 15:29:40</timestamp>
								<borrowernumber>84</borrowernumber>
								<itemnumber>33028</itemnumber>
								<date_due>2011-05-30</date_due>
								<barcode>2661690090</barcode>
								<surname>WISEPPE</surname>
								<issuedate>2009-11-26</issuedate>
								<cardnumber>10001560</cardnumber>
								<title>Harry Potter à l\'école des sorciers</title>
							</issue>
							<issue>
								<author>J. K. Rowling</author>
								<biblionumber>33233</biblionumber>
								<timestamp>2011-02-18 15:29:40</timestamp>
								<borrowernumber>42</borrowernumber>
								<itemnumber>33029</itemnumber>
								<date_due>2011-08-15</date_due>
								<barcode>2661680090</barcode>
								<surname>ARRANCY SUR CRUSNE (Mairie)</surname>
								<issuedate>2010-02-11</issuedate>
								<cardnumber>10000303</cardnumber>
								<title>Harry Potter à l\'école des sorciers</title>
							</issue>
						</issues>
						<size>18 cm</size>
						<biblionumber>33233</biblionumber>
						<timestamp>2011-02-18 16:15:22</timestamp>
						<cn_sort></cn_sort>
						<publishercode>Gallimard jeunesse</publishercode>
						<collectionvolume>899</collectionvolume>
						<reserves>
						</reserves>
						<items>
              <!-- 1 -->
							<item>
								<biblioitemnumber>33233</biblioitemnumber>
								<wthdrawn>0</wthdrawn>
								<holdingbranchname>Bibliothèque Départementale de la Meuse</holdingbranchname>
								<notforloan>0</notforloan>
								<borrowernumber>84</borrowernumber>
								<replacementpricedate>2011-02-18</replacementpricedate>
								<itemnumber>33028</itemnumber>
								<ccode>ROMJEUN</ccode>
								<itemcallnumber>JR ROW h</itemcallnumber>
								<date_due>2011-05-30</date_due>
								<barcode>2661690090</barcode>
								<itemlost>0</itemlost>
								<datelastseen>2011-02-18</datelastseen>
								<cardnumber>10001560</cardnumber>
								<homebranch>BDM</homebranch>
								<homebranchname>Bibliothèque Départementale de la Meuse</homebranchname>
								<biblionumber>33233</biblionumber>
								<holdingbranch>BDM</holdingbranch>
								<timestamp>2011-02-18 15:29:40</timestamp>
								<damaged>0</damaged>
								<cn_sort>JR_ROW_H</cn_sort>
								<dateaccessioned>2011-02-18</dateaccessioned>
								<itype>LIV</itype>
								<onloan>2011-05-30</onloan>
							</item>

              <!-- 2 -->
							<item>
								<biblioitemnumber>33233</biblioitemnumber>
								<wthdrawn>0</wthdrawn>
								<holdingbranchname>Bibliothèque Départementale de la Meuse</holdingbranchname>
								<notforloan>0</notforloan>
								<borrowernumber>42</borrowernumber>
								<replacementpricedate>2011-02-18</replacementpricedate>
								<itemnumber>33029</itemnumber>
								<ccode>ROMJEUN</ccode>
								<itemcallnumber>JR ROW h</itemcallnumber>
								<date_due>2011-08-15</date_due>
								<barcode>2661680090</barcode>
								<itemlost>0</itemlost>
								<datelastseen>2011-02-18</datelastseen>
								<cardnumber>10000303</cardnumber>
								<homebranch>BDM</homebranch>
								<homebranchname>Bibliothèque Départementale de la Meuse</homebranchname>
								<biblionumber>33233</biblionumber>
								<holdingbranch>BDM</holdingbranch>
								<timestamp>2011-02-18 15:29:40</timestamp>
								<damaged>0</damaged>
								<cn_sort>JR_ROW_H</cn_sort>
								<dateaccessioned>2011-02-18</dateaccessioned>
								<itype>LIV</itype>
								<onloan>2011-08-15</onloan>
							</item>

              <!-- 3 -->
							<item>
								<biblioitemnumber>33233</biblioitemnumber>
								<wthdrawn>0</wthdrawn>
								<holdingbranchname>Bibliothèque Départementale de la Meuse</holdingbranchname>
								<notforloan>0</notforloan>
								<replacementpricedate>2011-02-18</replacementpricedate>
								<itemnumber>33030</itemnumber>
								<ccode>ROMJEUN</ccode>
								<itemcallnumber>JR ROW h</itemcallnumber>
								<date_due></date_due>
								<barcode>2661660090</barcode>
								<itemlost>0</itemlost>
								<datelastseen>2011-02-18</datelastseen>
								<homebranch>BDM</homebranch>
								<homebranchname>Bibliothèque Départementale de la Meuse</homebranchname>
								<biblionumber>33233</biblionumber>
								<holdingbranch>BDM</holdingbranch>
								<timestamp>2011-02-18 14:24:01</timestamp>
								<damaged>0</damaged>
								<cn_sort>JR_ROW_H</cn_sort>
								<dateaccessioned>2011-02-18</dateaccessioned>
								<itype>LIV</itype>
							</item>

              <!-- 4 -->
							<item>
								<biblioitemnumber>33233</biblioitemnumber>
								<wthdrawn>1</wthdrawn>
								<holdingbranchname>Bibliothèque Départementale de la Meuse</holdingbranchname>
								<notforloan>0</notforloan>
								<replacementpricedate>2011-02-18</replacementpricedate>
								<itemnumber>33030</itemnumber>
								<ccode>ROMJEUN</ccode>
								<itemcallnumber>JR ROW h</itemcallnumber>
								<date_due></date_due>
								<barcode>2661660090</barcode>
								<itemlost>0</itemlost>
								<datelastseen>2011-02-18</datelastseen>
								<homebranch>BDM</homebranch>
								<homebranchname>Bibliothèque Départementale de la Meuse</homebranchname>
								<biblionumber>33233</biblionumber>
								<holdingbranch>BDM</holdingbranch>
								<timestamp>2011-02-18 14:24:01</timestamp>
								<damaged>0</damaged>
								<cn_sort>JR_ROW_H</cn_sort>
								<dateaccessioned>2011-02-18</dateaccessioned>
								<itype>LIV</itype>
							</item>

              <!-- 5 -->
							<item>
								<biblioitemnumber>33233</biblioitemnumber>
								<wthdrawn>0</wthdrawn>
								<holdingbranchname>Bibliothèque Départementale de la Meuse</holdingbranchname>
								<notforloan>0</notforloan>
								<replacementpricedate>2011-02-18</replacementpricedate>
								<itemnumber>33030</itemnumber>
								<ccode>ROMJEUN</ccode>
								<itemcallnumber>JR ROW h</itemcallnumber>
								<date_due></date_due>
								<barcode>2661660090</barcode>
								<itemlost>1</itemlost>
								<datelastseen>2011-02-18</datelastseen>
								<homebranch>BDM</homebranch>
								<homebranchname>Bibliothèque Départementale de la Meuse</homebranchname>
								<biblionumber>33233</biblionumber>
								<holdingbranch>BDM</holdingbranch>
								<timestamp>2011-02-18 14:24:01</timestamp>
								<damaged>0</damaged>
								<cn_sort>JR_ROW_H</cn_sort>
								<dateaccessioned>2011-02-18</dateaccessioned>
								<itype>LIV</itype>
							</item>

              <!-- 6 -->
							<item>
								<biblioitemnumber>33233</biblioitemnumber>
								<wthdrawn>0</wthdrawn>
								<holdingbranchname>Bibliothèque Départementale de la Meuse</holdingbranchname>
								<notforloan>5</notforloan>
								<replacementpricedate>2011-02-18</replacementpricedate>
								<itemnumber>33030</itemnumber>
								<ccode>ROMJEUN</ccode>
								<itemcallnumber>JR ROW h</itemcallnumber>
								<date_due></date_due>
								<barcode>2661660090</barcode>
								<itemlost>0</itemlost>
								<datelastseen>2011-02-18</datelastseen>
								<homebranch>BDM</homebranch>
								<homebranchname>Bibliothèque Départementale de la Meuse</homebranchname>
								<biblionumber>33233</biblionumber>
								<holdingbranch>BDM</holdingbranch>
								<timestamp>2011-02-18 14:24:01</timestamp>
								<damaged>1</damaged>
								<cn_sort>JR_ROW_H</cn_sort>
								<dateaccessioned>2011-02-18</dateaccessioned>
								<itype>LIV</itype>
							</item>

              <!-- 7 -->
							<item>
								<biblioitemnumber>33233</biblioitemnumber>
								<wthdrawn>0</wthdrawn>
								<holdingbranchname>Bibliothèque Départementale de la Meuse</holdingbranchname>
								<notforloan>2</notforloan>
								<replacementpricedate>2011-02-18</replacementpricedate>
								<itemnumber>33030</itemnumber>
								<ccode>ROMJEUN</ccode>
								<itemcallnumber>JR ROW h</itemcallnumber>
								<date_due></date_due>
								<barcode>2661660090</barcode>
								<itemlost>0</itemlost>
								<datelastseen>2011-02-18</datelastseen>
								<homebranch>BDM</homebranch>
								<homebranchname>Bibliothèque Départementale de la Meuse</homebranchname>
								<biblionumber>33233</biblionumber>
								<holdingbranch>BDM</holdingbranch>
								<timestamp>2011-02-18 14:24:01</timestamp>
								<damaged>0</damaged>
								<cn_sort>JR_ROW_H</cn_sort>
								<dateaccessioned>2011-02-18</dateaccessioned>
								<itype>LIV</itype>
							</item>


              <!-- 8 -->
							<item>
								<biblioitemnumber>33266</biblioitemnumber>
								<wthdrawn>0</wthdrawn>
								<holdingbranchname>Bibliothèque Départementale de la Meuse</holdingbranchname>
								<notforloan>4</notforloan>
								<replacementpricedate>2011-02-18</replacementpricedate>
								<itemnumber>33030</itemnumber>
								<ccode>ROMJEUN</ccode>
								<itemcallnumber>JR ROW h</itemcallnumber>
								<date_due></date_due>
								<barcode>2661660099</barcode>
								<itemlost>0</itemlost>
								<datelastseen>2011-02-18</datelastseen>
								<homebranch>BDM</homebranch>
								<homebranchname>Bibliothèque Départementale de la Meuse</homebranchname>
								<biblionumber>33233</biblionumber>
								<holdingbranch>BDM</holdingbranch>
								<timestamp>2011-02-18 14:24:01</timestamp>
								<damaged>0</damaged>
								<cn_sort>JR_ROW_H</cn_sort>
								<dateaccessioned>2011-02-18</dateaccessioned>
								<itype>LIV</itype>
							</item>
						</items>
					</record>
				</GetRecords>';
	}


	public static function xmlLookupPatronLaurent() {
		return '<?xml version="1.0" encoding="ISO-8859-1" ?>
				<LookupPatron>
					<id>572</id>
				</LookupPatron>';
	}


	public static function xmlGetPatronInfoLaurent() {
		return '<?xml version="1.0" encoding="UTF-8" ?>
				<GetPatronInfo>
					<category_type>A</category_type>
					<categorycode>INDIVIDU</categorycode>
					<contactnote></contactnote>
					<holds>
							<hold>
									<priority>2</priority>
									<reservenotes />
									<item>
											<isbn>2-84011-699-5</isbn>
											<itemnumber>24426</itemnumber>
											<ccode>GRCARACT</ccode>
											<serial>0</serial>
											<barcode>3512090090</barcode>
											<datelastseen>2011-02-18</datelastseen>
											<title>Harry Potter et la chambre des secrets</title>
											<pages>459 p.</pages>
											<author>J. K. Rowling</author>
											<size>25 cm</size>
											<timestamp>2011-02-18 16:14:00</timestamp>
											<publishercode>Feryane</publishercode>
											<datecreated>2011-02-18</datecreated>
											<dateaccessioned>2011-02-18</dateaccessioned>
											<itype>LIV</itype>
											<onloan>2011-12-10</onloan>
											<biblioitemnumber>27136</biblioitemnumber>
											<wthdrawn>0</wthdrawn>
											<notforloan>0</notforloan>
											<replacementpricedate>2011-02-18</replacementpricedate>
											<itemcallnumber>GR ROW h</itemcallnumber>
											<itemlost>0</itemlost>
											<publicationyear>2006</publicationyear>
											<homebranch>BDM</homebranch>
											<holdingbranch>BDM</holdingbranch>
											<biblionumber>27136</biblionumber>
											<damaged>0</damaged>
											<cn_sort />
											<frameworkcode />
									</item>
									<reservedate>2011-04-13</reservedate>
									<timestamp>2011-04-13 15:42:13</timestamp>
									<biblionumber>27136</biblionumber>
									<borrowernumber>572</borrowernumber>
									<reservenumber>234</reservenumber>
									<branchcode>BDM</branchcode>
									<itemnumber>24426</itemnumber>
									<branchname>BibliothÃ¨que DÃ©partementale de la Meuse</branchname>
									<constrainttype>a</constrainttype>
									<lowestPriority>0</lowestPriority>
									<title>Harry Potter et la chambre des secrets</title>
							</hold>
					</holds>
					<B_country></B_country>
					<borrowernumber>572</borrowernumber>
					<lost>0</lost>
					<branchcode>BDM</branchcode>
					<amountoutstanding>0</amountoutstanding>
					<description>Lecteur individuel</description>
					<loans>
					</loans>
					<title>Mr</title>
					<enrolmentperiod>12</enrolmentperiod>
					<country></country>
					<dateenrolled>2011-04-12</dateenrolled>
					<guarantorid>0</guarantorid>
					<borrowernotes></borrowernotes>
					<dateexpiry>2012-04-12</dateexpiry>
					<sort2></sort2>
					<firstname>laurent</firstname>
					<altcontactcountry></altcontactcountry>
					<gonenoaddress>0</gonenoaddress>
					<othernames></othernames>
					<dateofbirth>1978-02-17</dateofbirth>
					<B_address2></B_address2>
					<branchname>Bibliothèque Départementale de la Meuse</branchname>
					<surname>llaffont</surname>
					<gonenoaddresscomment></gonenoaddresscomment>
					<cardnumber>10002000</cardnumber>
					<opacnote></opacnote>
					<initials>ll</initials>
					<sort1>10</sort1>
					<sex></sex>
				</GetPatronInfo>';
	}



	public static function xmlLookupPatronJeanAndre() {
		return '<?xml version="1.0" encoding="UTF-8" ?>
				<LookupPatron>
					<id>419</id>
				</LookupPatron>';
	}



	public static function xmlGetPatronInfoJeanAndre() {
		return '<?xml version="1.0" encoding="UTF-8" ?>
				<GetPatronInfo>
					<category_type>A</category_type>
					<categorycode>ADUEXT</categorycode>
					<borrowernumber>419</borrowernumber>
					<lost>0</lost>
					<branchcode>BIB</branchcode>
					<amountoutstanding>6</amountoutstanding>
					<description>Adulte extérieur</description>
					<title>M</title>
					<enrolmentperiod>12</enrolmentperiod>
					<charges>6.00</charges>
					<dateenrolled>2009-03-04</dateenrolled>
					<borrowernotes></borrowernotes>
					<dateexpiry>2010-03-04</dateexpiry>
					<firstname>Jean-André</firstname>
					<gonenoaddress>0</gonenoaddress>
					<dateofbirth>1984-06-08</dateofbirth>
					<debarred>0</debarred>
					<branchname>Bibliothèque Jean Prunier</branchname>
					<surname>SANTONI</surname>
					<cardnumber>815</cardnumber>
					<initials>JAS</initials>
					<sort1>CSP5</sort1>
					<sex>M</sex>
					<loans>
						<loan>
							<lastreneweddate>2009-04-03</lastreneweddate>
							<isbn>2253003689</isbn>
							<borrowernumber>419</borrowernumber>
							<branchcode>BIB</branchcode>
							<itemnumber>4454</itemnumber>
							<date_due>2009-05-06</date_due>
							<barcode>4765476</barcode>
							<datelastseen>2008-08-23</datelastseen>
							<issuedate>2008-08-23</issuedate>
							<title>L\'Île au trésor</title>
							<itemtype>LITT</itemtype>
							<author>Robert Louis Stevenson</author>
							<timestamp>2009-04-03 14:46:10</timestamp>
							<publishercode>Librairie générale française</publishercode>
							<datecreated>2008-08-23</datecreated>
							<totalrenewals>11</totalrenewals>
							<dateaccessioned>2008-08-23</dateaccessioned>
							<onloan>2008-09-17</onloan>
							<biblioitemnumber>4483</biblioitemnumber>
							<wthdrawn>0</wthdrawn>
							<notforloan>0</notforloan>
							<replacementpricedate>2008-08-23</replacementpricedate>
							<itemcallnumber>RO STE</itemcallnumber>
							<location>Salle de lecture</location>
							<itemlost>0</itemlost>
							<publicationyear>1985</publicationyear>
							<issues>1</issues>
							<homebranch>BIB</homebranch>
							<holdingbranch>BIB</holdingbranch>
							<biblionumber>4483</biblionumber>
							<renewals>3</renewals>
							<damaged>0</damaged>
							<cn_sort>RO_STE</cn_sort>
							<frameworkcode></frameworkcode>
							<datelastborrowed>2008-08-23</datelastborrowed>
						</loan>
						<loan>
							<lastreneweddate>2009-03-17</lastreneweddate>
							<isbn>9782700017823</isbn>
							<borrowernumber>419</borrowernumber>
							<branchcode>BIB</branchcode>
							<itemnumber>4456</itemnumber>
							<date_due>2009-04-18</date_due>
							<barcode>2700017UUU</barcode>
							<datelastseen>2008-08-23</datelastseen>
							<issuedate>2008-08-23</issuedate>
							<title>La guitare en 10 leçons</title>
							<itemtype>LITT</itemtype>
							<author>Jon Buck</author>
							<timestamp>2009-03-17 16:48:14</timestamp>
							<publishercode>Gründ</publishercode>
							<datecreated>2008-08-23</datecreated>
							<totalrenewals>6</totalrenewals>
							<dateaccessioned>2008-08-23</dateaccessioned>
							<notes>La couv. porte en plus : "un guide simple et facile pour apprendre la guitare" | Glossaire. Index</notes>
							<onloan>2008-09-25</onloan>
							<biblioitemnumber>4486</biblioitemnumber>
							<wthdrawn>0</wthdrawn>
							<notforloan>0</notforloan>
							<replacementpricedate>2008-08-23</replacementpricedate>
							<itemcallnumber>787.87 BUC</itemcallnumber>
							<location>Salle de lecture</location>
							<itemlost>0</itemlost>
							<publicationyear>2007</publicationyear>
							<issues>1</issues>
							<homebranch>BIB</homebranch>
							<holdingbranch>BIB</holdingbranch>
							<biblionumber>4486</biblionumber>
							<renewals>3</renewals>
							<damaged>0</damaged>
							<cn_sort>78787_BUC</cn_sort>
							<volume>une méthode simple et facile pour apprendre la guitare</volume>
							<frameworkcode></frameworkcode>
							<datelastborrowed>2008-08-23</datelastborrowed>
						</loan>
		      </loans>
       </GetPatronInfo>';
	}
}

?>