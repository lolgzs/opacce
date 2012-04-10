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

class VSmartFixtures {
	public static function xmlBorrowerEvelyne() {
		return
			'<VubisSmart>
				 <Header>
						 <Function>GetBorrower</Function>
						 <ErrorCode>0</ErrorCode>
				 </Header>
				 <Version>20101216</Version>
				 <Borrower>
						 <General>
								 <MetaInstitution>RES</MetaInstitution>
								 <OriginalBarcode>04051972</OriginalBarcode>
								 <ActualBarcode>10102003</ActualBarcode>
								 <LastName>SERVIER</LastName>
								 <FirstName>Evelyne</FirstName>
								 <Gender />
								 <BirthDate>04/05/1972</BirthDate>
								 <BorrowerCategoryCode>ABIB</BorrowerCategoryCode>
								 <RegistrationDate>03/02/2011</RegistrationDate>
								 <LastRenewalDate>03/02/2011</LastRenewalDate>
								 <ExpiryDate>03/02/2012</ExpiryDate>
								 <TotalAmountDue>0</TotalAmountDue>
						 </General>
						 <Addresses>
								 <Address>
										 <Street>92 rue colonel rochebrune</Street>
										 <PostalCode>92380</PostalCode>
										 <Town>Gennetines</Town>
										 <HomePhone>0147957326</HomePhone>
										 <MobilePhone>0607049098</MobilePhone>
								 </Address>
								 <Address />
								 <Address />
								 <Address />
								 <Address />
								 <Address />
								 <Address />
								 <Address />
								 <Address />
								 <Address />
						 </Addresses>
						 <Loans>
								 <Loan>
										 <ItemBarcode>10102003</ItemBarcode>
										 <Title>notice test 3 pour le por</Title>
										 <MaterialType>LFA</MaterialType>
										 <LoanLocation>RES/MCFC</LoanLocation>
										 <LoanDate>17/02/2011</LoanDate>
										 <DueDate>19/02/2011</DueDate>
										 <Renewals>0</Renewals>
								 </Loan>
								 <Loan>
										 <ItemBarcode>0078010148</ItemBarcode>
										 <Title>ANTHOLOGIE DE LA LITTERAT</Title>
										 <MaterialType>NEM</MaterialType>
										 <LoanLocation>RES/MCFC</LoanLocation>
										 <LoanDate>03/02/2011</LoanDate>
										 <DueDate>05/02/2011</DueDate>
										 <Renewals>0</Renewals>
								 </Loan>
								 <Loan>
										 <ItemBarcode>04051972</ItemBarcode>
										 <Title>notice test 2 pour le por</Title>
										 <MaterialType>LFA</MaterialType>
										 <LoanLocation>RES/MTIL</LoanLocation>
										 <LoanDate>17/02/2011</LoanDate>
										 <DueDate>22/02/2030</DueDate>
										 <Renewals>1</Renewals>
								 </Loan>
						 </Loans>
						 <Reservations />
						 <StackRequests />
				 </Borrower>
		 </VubisSmart>';
	}










	public static function xmlBorrowerFranck() {
		return
		 '<VubisSmart>
			  <Header>
						<Function>GetBorrower</Function>
						<ErrorCode>0</ErrorCode>
				</Header>
				<Version>20101216</Version>
				<Borrower>
						<General>
								<MetaInstitution>RES</MetaInstitution>
								<OriginalBarcode>30101964</OriginalBarcode>
								<ActualBarcode>30101964</ActualBarcode>
								<LastName>HANOTIN</LastName>
								<FirstName>Franck</FirstName>
								<Gender />
								<BorrowerCategoryCode>AAGG</BorrowerCategoryCode>
								<RegistrationDate>03/02/2011</RegistrationDate>
								<LastRenewalDate>03/02/2011</LastRenewalDate>
								<ExpiryDate>03/02/2012</ExpiryDate>
								<TotalAmountDue>0</TotalAmountDue>
						</General>
						<Addresses>
								<Address>
										<Street>92 rue colonel rochebrune</Street>
										<PostalCode>92380</PostalCode>
										<HomePhone>0147957325</HomePhone>
								</Address>
								<Address />
								<Address />
								<Address />
								<Address />
								<Address />
								<Address />
								<Address />
								<Address />
								<Address />
						</Addresses>
						<Loans />
						<Reservations>
							<Reservation>
								<ReservationNumber>5</ReservationNumber>
								<Items>
									<Item>
										<ItemBarcode>32272073521608</ItemBarcode>
										<Title>1001 phrases pour bien pa</Title>
										<MaterialType>LIA</MaterialType>
										<ReservationLocation>BPVP/AVER</ReservationLocation>
										<PickupLocation>BPVP/AVER</PickupLocation>
										<RequestDateTime>04/02/2009 11:18:40</RequestDateTime>
										<!-- <ExpiryDate>18/02/2009</ExpiryDate> -->
										<!-- <TrappedDateTime>18/02/2009 00:00:00</TrappedDateTime> -->
										<PlaceInQueue>4</PlaceInQueue>
									</Item>
								</Items>
							</Reservation>
							<Reservation>
								<ReservationNumber>6</ReservationNumber>
								<Items>
									<Item>
										<ItemBarcode>32272073523224</ItemBarcode>
										<Title>Espagnol, grammaire</Title>
										<MaterialType>LIA</MaterialType>
										<ReservationLocation>BPVP/YZLM</ReservationLocation>
										<PickupLocation>BPVP/YZLM</PickupLocation>
										<RequestDateTime>04/02/2009 11:20:07</RequestDateTime>
										<ExpiryDate>19/02/2009</ExpiryDate>
										<TrappedDateTime>19/02/2009 00:00:00</TrappedDateTime>
										<PlaceInQueue>1</PlaceInQueue>
									</Item>
								</Items>
							</Reservation>
						</Reservations>
						<StackRequests />
				</Borrower>
		</VubisSmart>';
	}


	public static function xmlNoticeAnthologie() {
		return
		'<zs:searchRetrieveResponse xmlns:zs="http://www.loc.gov/zing/srw/">
		   <zs:version>2010.10.14</zs:version>
				 <zs:numberOfRecords>1</zs:numberOfRecords>
				 <zs:resultSetId>1498_1</zs:resultSetId>
				 <zs:records>
						 <zs:record format="UniMarc/B" type="Bibliographic">
								 <zs:recordSchema>info:marcXchange</zs:recordSchema>
								 <zs:recordPacking>xml</zs:recordPacking>
								 <zs:recordData>
										 <leader>00000nam##2200000###450#</leader>
										 <controlfield tag="001">1/47918</controlfield>
										 <!-- <controlfield tag="005">20110217145136.0</controlfield> -->
										 <datafield tag="039" ind1=" " ind2=" ">
												 <subfield code="a">0078010148</subfield>
										 </datafield>
										 <datafield tag="073" ind1=" " ind2="0">
												 <subfield code="a">2850562311</subfield>
										 </datafield>
										 <datafield tag="200" ind1="1" ind2=" ">
												 <subfield code="a">ANTHOLOGIE DE LA LITTERATURE DE SCIENCE</subfield>
										 </datafield>
										 <datafield tag="801" ind1=" " ind2="0">
												 <subfield code="b">plivbd_11jan2011.xls</subfield>
										 </datafield>
										 <datafield tag="852">
												 <subfield code="a">RES</subfield>
												 <subfield code="b">MCFC</subfield>
												 <subfield code="c">MCPR</subfield>
												 <subfield code="e">20110131</subfield>
												 <subfield code="p">0078010148</subfield>
												 <subfield code="q">0078010148</subfield>
												 <subfield code="t">NEM</subfield>
												 <subfield code="y">4</subfield>
												 <subfield code="x">20110205</subfield>
												 <subfield code="y">8</subfield>
										 </datafield>
								 </zs:recordData>
								 <zs:recordPosition>1</zs:recordPosition>
						 </zs:record>
				 </zs:records>
     </zs:searchRetrieveResponse>';
	}



	public static function xmlNoticeHarryPotter() {
			return
				 '<zs:searchRetrieveResponse xmlns:zs="http://www.loc.gov/zing/srw/">
						 <zs:version>2010.10.14</zs:version>
						 <zs:numberOfRecords>1</zs:numberOfRecords>
						 <zs:resultSetId />
						 <zs:records>
								 <zs:record format="UniMarc/B" type="Bibliographic">
										 <zs:recordSchema>info:marcXchange</zs:recordSchema>
										 <zs:recordPacking>xml</zs:recordPacking>
										 <zs:recordData>
												 <leader>00000nam##22######i#450#</leader>
												 <controlfield tag="001">2/3066</controlfield>
												 <controlfield tag="005">20110301081211.0</controlfield>
												 <datafield tag="039" ind1=" " ind2=" ">
														 <subfield code="a">1032650148</subfield>
												 </datafield>
												 <datafield tag="073" ind1=" " ind2="0">
														 <subfield code="a">9782070615377</subfield>
												 </datafield>
												 <datafield tag="200" ind1="1" ind2=" ">
														 <subfield code="a">HARRY POTTER : 7 : ET LES RELIQUES DE LA</subfield>
														 <subfield code="f">ROWLING J.K.</subfield>
												 </datafield>
												 <datafield tag="801" ind1=" " ind2="0">
														 <subfield code="b">plivbd_11jan2011.xls</subfield>
												 </datafield>
												 <datafield tag="801" ind1=" " ind2="0">
														 <subfield code="b">06-Adultes-LivEmp-Romans-Bin01a07.txt</subfield>
												 </datafield>
												 <datafield tag="801" ind1=" " ind2="0">
														 <subfield code="b">28-Jeunesse-LivEmp-Romans-Bin01a07.txt</subfield>
												 </datafield>
												 <datafield tag="852">
														 <subfield code="a">RES</subfield>
														 <subfield code="b">MCFC</subfield>
														 <subfield code="c">MCRPT</subfield>
														 <subfield code="e">20110301</subfield>
														 <subfield code="h">J</subfield>
														 <subfield code="p">1042770148</subfield>
														 <subfield code="q">1042770148</subfield>
														 <subfield code="t">LFJ</subfield>
														 <subfield code="y">0</subfield>
												 </datafield>
												 <datafield tag="852">
														 <subfield code="a">RES</subfield>
														 <subfield code="b">MCFC</subfield>
														 <subfield code="c">MCRPT</subfield>
														 <subfield code="e">20110301</subfield>
														 <subfield code="h">R</subfield>
														 <subfield code="p">1032650148</subfield>
														 <subfield code="q">1032650148</subfield>
														 <subfield code="t">LFA</subfield>
														 <subfield code="y">4</subfield>
														 <subfield code="x">20110310</subfield>
												 </datafield>
												 <datafield tag="852">
														 <subfield code="a">RES</subfield>
														 <subfield code="b">MCFC</subfield>
														 <subfield code="c">MCRPT</subfield>
														 <subfield code="e">20110301</subfield>
														 <subfield code="h">S</subfield>
														 <subfield code="p">650148</subfield>
														 <subfield code="q">650148</subfield>
														 <subfield code="t">LFS</subfield>
														 <subfield code="y">999</subfield>
												 </datafield>
												 <datafield tag="852">
														 <subfield code="a">RES</subfield>
														 <subfield code="b">MCFC</subfield>
														 <subfield code="c">MCRPT</subfield>
														 <subfield code="e">20110301</subfield>
														 <subfield code="h">R</subfield>
														 <subfield code="p">123</subfield>
														 <subfield code="q">123</subfield>
														 <subfield code="t">LFA</subfield>
														 <subfield code="y">0</subfield>
												 </datafield>
										 </zs:recordData>
										 <zs:recordPosition>-1</zs:recordPosition>
								 </zs:record>
						 </zs:records>
				 </zs:searchRetrieveResponse>';
	}
}
