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

class MicrobibFixtures {
	public static function xmlInfosExemplaires5204() {
		return '<?xml version = "1.0" encoding="UTF-8" standalone="yes"?>
<Exemplaires>
	<xsd:schema id="Exemplaires" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
		<xsd:element name="Exemplaires" msdata:IsDataSet="true">
			<xsd:complexType>
				<xsd:choice maxOccurs="unbounded">
					<xsd:element name="Exemplaire" minOccurs="0" maxOccurs="unbounded">
						<xsd:complexType>
							<xsd:sequence>
								<xsd:element name="code_barre">
									<xsd:simpleType>
										<xsd:restriction base="xsd:string">
											<xsd:maxLength value="14"/>
										</xsd:restriction>
									</xsd:simpleType>
								</xsd:element>
								<xsd:element name="disponible" type="xsd:boolean"/>
								<xsd:element name="piege">
									<xsd:simpleType>
										<xsd:restriction base="xsd:string">
											<xsd:maxLength value="30"/>
										</xsd:restriction>
									</xsd:simpleType>
								</xsd:element>
								<xsd:element name="date_retour" type="xsd:date"/>
								<xsd:element name="reservable" type="xsd:boolean"/>
							</xsd:sequence>
						</xsd:complexType>
					</xsd:element>
				</xsd:choice>
				<xsd:anyAttribute namespace="http://www.w3.org/XML/1998/namespace" processContents="lax"/>
			</xsd:complexType>
		</xsd:element>
	</xsd:schema>
	<Exemplaire>
		<code_barre>0006260194</code_barre>
		<disponible>true</disponible>
		<piege/>
		<date_retour/>
		<reservable>false</reservable>
	</Exemplaire>
	<Exemplaire>
		<code_barre>99999999</code_barre>
		<disponible>false</disponible>
		<piege>Retour prévu le 31/03/2012</piege>
		<date_retour>2012-03-31</date_retour>
		<reservable>true</reservable>
	</Exemplaire>
</Exemplaires>';
	} 


	public static function xmlInfosAbonne9999() {
		return '<?xml version = "1.0" encoding="UTF-8" standalone="yes"?>
<abonne>
	<xsd:schema id="abonne" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
		<xsd:element name="abonne" msdata:IsDataSet="true">
			<xsd:complexType>
				<xsd:choice maxOccurs="unbounded">
					<xsd:element name="abonne" minOccurs="0" maxOccurs="unbounded">
						<xsd:complexType>
							<xsd:sequence>
								<xsd:element name="date_inscr" type="xsd:date"/>
								<xsd:element name="date_expir" type="xsd:date"/>
								<xsd:element name="adresse">
									<xsd:simpleType>
										<xsd:restriction base="xsd:string">
											<xsd:maxLength value="76"/>
										</xsd:restriction>
									</xsd:simpleType>
								</xsd:element>
								<xsd:element name="telephone">
									<xsd:simpleType>
										<xsd:restriction base="xsd:string">
											<xsd:maxLength value="14"/>
										</xsd:restriction>
									</xsd:simpleType>
								</xsd:element>
								<xsd:element name="email">
									<xsd:simpleType>
										<xsd:restriction base="xsd:string">
											<xsd:maxLength value="45"/>
										</xsd:restriction>
									</xsd:simpleType>
								</xsd:element>
							</xsd:sequence>
						</xsd:complexType>
					</xsd:element>
					<xsd:element name="Liste_Prets" minOccurs="0" maxOccurs="unbounded">
						<xsd:complexType>
							<xsd:sequence>
								<xsd:element name="titre">
									<xsd:simpleType>
										<xsd:restriction base="xsd:string">
											<xsd:maxLength value="100"/>
										</xsd:restriction>
									</xsd:simpleType>
								</xsd:element>
								<xsd:element name="auteur">
									<xsd:simpleType>
										<xsd:restriction base="xsd:string">
											<xsd:maxLength value="60"/>
										</xsd:restriction>
									</xsd:simpleType>
								</xsd:element>
								<xsd:element name="code_barre">
									<xsd:simpleType>
										<xsd:restriction base="xsd:string">
											<xsd:maxLength value="14"/>
										</xsd:restriction>
									</xsd:simpleType>
								</xsd:element>
								<xsd:element name="date_retour" type="xsd:date"/>
							</xsd:sequence>
						</xsd:complexType>
					</xsd:element>
					<xsd:element name="Liste_Reservations" minOccurs="0" maxOccurs="unbounded">
						<xsd:complexType>
							<xsd:sequence>
								<xsd:element name="titre">
									<xsd:simpleType>
										<xsd:restriction base="xsd:string">
											<xsd:maxLength value="100"/>
										</xsd:restriction>
									</xsd:simpleType>
								</xsd:element>
								<xsd:element name="auteur">
									<xsd:simpleType>
										<xsd:restriction base="xsd:string">
											<xsd:maxLength value="60"/>
										</xsd:restriction>
									</xsd:simpleType>
								</xsd:element>
								<xsd:element name="code_barre">
									<xsd:simpleType>
										<xsd:restriction base="xsd:string">
											<xsd:maxLength value="14"/>
										</xsd:restriction>
									</xsd:simpleType>
								</xsd:element>
								<xsd:element name="posee_le" type="xsd:date"/>
								<xsd:element name="expire_le" type="xsd:date"/>
							</xsd:sequence>
						</xsd:complexType>
					</xsd:element>
				</xsd:choice>
				<xsd:anyAttribute namespace="http://www.w3.org/XML/1998/namespace" processContents="lax"/>
			</xsd:complexType>
		</xsd:element>
	</xsd:schema>
	<abonne>
		<date_inscr>2005-03-16</date_inscr>
		<date_expir>2011-11-23</date_expir>
		<adresse>ST PIERRE EN VAUX 49350 SAINT GEOR</adresse>
		<telephone>02 41 80 60 19</telephone>
		<email/>
	</abonne>
	<Liste_Prets>
		<titre>ABSURDUS DELIRIUM</titre>
		<auteur>BIGART T.P.</auteur>
		<code_barre>1805660020</code_barre>
		<date_retour>2012-02-03</date_retour>
	</Liste_Prets>
	<Liste_Prets>
		<titre>AVENTURES DE HUCKLEBERRY FINN (LES)</titre>
		<auteur>MATTOTTI LORENZO</auteur>
		<code_barre>0234020194</code_barre>
		<date_retour>2012-02-03</date_retour>
	</Liste_Prets>
	<Liste_Prets>
		<titre>OUI MAIS IL NE BAT QUE POUR VOUS</titre>
		<auteur>PRALONG ISABELLE</auteur>
		<code_barre>0235030194</code_barre>
		<date_retour>2012-02-03</date_retour>
	</Liste_Prets>
	<Liste_Prets>
		<titre>ROI CASSÉ (LE)</titre>
		<auteur>DUMONTHEUIL NICOLAS</auteur>
		<code_barre>0200650194</code_barre>
		<date_retour>2012-01-25</date_retour>
	</Liste_Prets>
	<Liste_Prets>
		<titre>DES SALOPES ET DES ANGES</titre>
		<auteur>BENACQUISTA TONINO</auteur>
		<code_barre>0234770194</code_barre>
		<date_retour>2012-02-03</date_retour>
	</Liste_Prets>
  <Liste_Reservations>
		<titre>ABSURDUS DELIRIUM</titre>
		<auteur>BIGART T.P.</auteur>
		<code_barre>1805660020</code_barre>
		<posee_le>2012-02-28</posee_le>
		<expire_le/>
		<disponible>false</disponible>
	</Liste_Reservations>
	<Liste_Reservations>
		<titre>COMMUNAUTÉ DE L\'ANNEAU : LE SEIGNEUR DES ANNEAUX 1 (LA)</titre>
		<auteur>JACKSON PETER</auteur>
		<code_barre>0183050194</code_barre>
		<posee_le>2012-12-11</posee_le>
		<expire_le/>
		<disponible>true</disponible>
	</Liste_Reservations>
</abonne>';
	}



	public static function xmlAjoutReservationOK() {
		return '<?xml version = "1.0" encoding="UTF-8" standalone="yes"?>
<Reservation>
	<xsd:schema id="Reservation" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
		<xsd:element name="Reservation" msdata:IsDataSet="true">
			<xsd:complexType>
				<xsd:choice maxOccurs="unbounded">
					<xsd:element name="Reservation" minOccurs="0" maxOccurs="unbounded">
						<xsd:complexType>
							<xsd:sequence>
								<xsd:element name="reponse">
									<xsd:simpleType>
										<xsd:restriction base="xsd:string">
											<xsd:maxLength value="100"/>
										</xsd:restriction>
									</xsd:simpleType>
								</xsd:element>
							</xsd:sequence>
						</xsd:complexType>
					</xsd:element>
				</xsd:choice>
				<xsd:anyAttribute namespace="http://www.w3.org/XML/1998/namespace" processContents="lax"/>
			</xsd:complexType>
		</xsd:element>
	</xsd:schema>
	<Reservation>
		<reponse>Ok</reponse>
	</Reservation>
</Reservation>';
	}



	public static function xmlAjoutReservationError() {
		return '<?xml version = "1.0" encoding="UTF-8" standalone="yes"?>
<Reservation>
	<xsd:schema id="Reservation" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
		<xsd:element name="Reservation" msdata:IsDataSet="true">
			<xsd:complexType>
				<xsd:choice maxOccurs="unbounded">
					<xsd:element name="Reservation" minOccurs="0" maxOccurs="unbounded">
						<xsd:complexType>
							<xsd:sequence>
								<xsd:element name="reponse">
									<xsd:simpleType>
										<xsd:restriction base="xsd:string">
											<xsd:maxLength value="100"/>
										</xsd:restriction>
									</xsd:simpleType>
								</xsd:element>
							</xsd:sequence>
						</xsd:complexType>
					</xsd:element>
				</xsd:choice>
				<xsd:anyAttribute namespace="http://www.w3.org/XML/1998/namespace" processContents="lax"/>
			</xsd:complexType>
		</xsd:element>
	</xsd:schema>
	<Reservation>
		<reponse>Impossible de valider votre demande</reponse>
	</Reservation>
</Reservation>';
	}


	public static function xmlProlongePretOk() {
		return '<?xml version = "1.0" encoding="UTF-8" standalone="yes"?>
<Pret>
	<xsd:schema id="Pret" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
		<xsd:element name="Pret" msdata:IsDataSet="true">
			<xsd:complexType>
				<xsd:choice maxOccurs="unbounded">
					<xsd:element name="Pret" minOccurs="0" maxOccurs="unbounded">
						<xsd:complexType>
							<xsd:sequence>
								<xsd:element name="reponse">
									<xsd:simpleType>
										<xsd:restriction base="xsd:string">
											<xsd:maxLength value="100"/>
										</xsd:restriction>
									</xsd:simpleType>
								</xsd:element>
								<xsd:element name="date_prolo" type="xsd:date"/>
								<xsd:element name="libelle">
									<xsd:simpleType>
										<xsd:restriction base="xsd:string">
											<xsd:maxLength value="100"/>
										</xsd:restriction>
									</xsd:simpleType>
								</xsd:element>
							</xsd:sequence>
						</xsd:complexType>
					</xsd:element>
				</xsd:choice>
				<xsd:anyAttribute namespace="http://www.w3.org/XML/1998/namespace" processContents="lax"/>
			</xsd:complexType>
		</xsd:element>
	</xsd:schema>
	<Pret>
		<reponse>Ok</reponse>
		<date_prolo>2012-03-31</date_prolo>
		<libelle>L\'emprunt a été prolongé de 15 jour(s)</libelle>
	</Pret>
</Pret>';
	}



	public static function xmlProlongePretError() {
		return '<?xml version = "1.0" encoding="UTF-8" standalone="yes"?>
<Pret>
	<xsd:schema id="Pret" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
		<xsd:element name="Pret" msdata:IsDataSet="true">
			<xsd:complexType>
				<xsd:choice maxOccurs="unbounded">
					<xsd:element name="Pret" minOccurs="0" maxOccurs="unbounded">
						<xsd:complexType>
							<xsd:sequence>
								<xsd:element name="reponse">
									<xsd:simpleType>
										<xsd:restriction base="xsd:string">
											<xsd:maxLength value="100"/>
										</xsd:restriction>
									</xsd:simpleType>
								</xsd:element>
								<xsd:element name="date_prolo" type="xsd:date"/>
								<xsd:element name="libelle">
									<xsd:simpleType>
										<xsd:restriction base="xsd:string">
											<xsd:maxLength value="100"/>
										</xsd:restriction>
									</xsd:simpleType>
								</xsd:element>
							</xsd:sequence>
						</xsd:complexType>
					</xsd:element>
				</xsd:choice>
				<xsd:anyAttribute namespace="http://www.w3.org/XML/1998/namespace" processContents="lax"/>
			</xsd:complexType>
		</xsd:element>
	</xsd:schema>
	<Pret>
		<reponse>3_Impossible de prolonger de prêt...</reponse>
		<date_prolo/>
		<libelle/>
	</Pret>
</Pret>';
	}


	public static function xmlAnnuleReservationOk() {
		return '<?xml version = "1.0" encoding="UTF-8" standalone="yes"?>
<Reservation>
	<xsd:schema id="Reservation" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
		<xsd:element name="Reservation" msdata:IsDataSet="true">
			<xsd:complexType>
				<xsd:choice maxOccurs="unbounded">
					<xsd:element name="Reservation" minOccurs="0" maxOccurs="unbounded">
						<xsd:complexType>
							<xsd:sequence>
								<xsd:element name="reponse">
									<xsd:simpleType>
										<xsd:restriction base="xsd:string">
											<xsd:maxLength value="100"/>
										</xsd:restriction>
									</xsd:simpleType>
								</xsd:element>
							</xsd:sequence>
						</xsd:complexType>
					</xsd:element>
				</xsd:choice>
				<xsd:anyAttribute namespace="http://www.w3.org/XML/1998/namespace" processContents="lax"/>
			</xsd:complexType>
		</xsd:element>
	</xsd:schema>
	<Reservation>
		<reponse>Ok</reponse>
	</Reservation>
</Reservation>';
	}



	public static function xmlAnnuleReservationError() {
		return '<?xml version = "1.0" encoding="UTF-8" standalone="yes"?>
<Reservation>
	<xsd:schema id="Reservation" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:msdata="urn:schemas-microsoft-com:xml-msdata">
		<xsd:element name="Reservation" msdata:IsDataSet="true">
			<xsd:complexType>
				<xsd:choice maxOccurs="unbounded">
					<xsd:element name="Reservation" minOccurs="0" maxOccurs="unbounded">
						<xsd:complexType>
							<xsd:sequence>
								<xsd:element name="reponse">
									<xsd:simpleType>
										<xsd:restriction base="xsd:string">
											<xsd:maxLength value="100"/>
										</xsd:restriction>
									</xsd:simpleType>
								</xsd:element>
							</xsd:sequence>
						</xsd:complexType>
					</xsd:element>
				</xsd:choice>
				<xsd:anyAttribute namespace="http://www.w3.org/XML/1998/namespace" processContents="lax"/>
			</xsd:complexType>
		</xsd:element>
	</xsd:schema>
	<Reservation>
		<reponse>4_Impossible d\'annuler cette réservation...</reponse>
	</Reservation>
</Reservation>';
	}
}

?>