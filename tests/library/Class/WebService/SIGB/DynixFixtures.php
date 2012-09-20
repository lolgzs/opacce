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

class DynixFixtures {
	public static function xmlLookupTitleInfoLeCombatOrdinaire() {
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<LookupTitleInfoResponse xmlns="http://schemas.sirsidynix.com/symws/standard">
	<TitleInfo>
		<titleID>233823</titleID>
		<TitleAvailabilityInfo>
			<totalCopiesAvailable>0</totalCopiesAvailable>
			<totalResvCopiesAvailable>0</totalResvCopiesAvailable>
			<holdable>true</holdable>
		<bookable>false</bookable></TitleAvailabilityInfo>
		<CallInfo>
			<libraryID>ALFMEDA</libraryID>
			<classificationID>ASIS</classificationID>
			<callNumber>BD LAR</callNumber>
			<numberOfCopies>1</numberOfCopies>
			<ItemInfo>
				<itemID>39410001517933</itemID>
				<itemTypeID>1IMP</itemTypeID>
				<currentLocationID>CHECKEDOUT</currentLocationID>
				<homeLocationID>10ABD</homeLocationID>
				<dueDate>2012-09-18T00:00:00+02:00</dueDate>
				<chargeable>false</chargeable>
				<fixedTimeBooking>false</fixedTimeBooking>
			</ItemInfo>
		</CallInfo>
		<CallInfo>
			<libraryID>ALFAX1</libraryID>
			<classificationID>ASIS</classificationID>
			<callNumber>BD LAR</callNumber>
			<numberOfCopies>1</numberOfCopies>
			<ItemInfo>
				<itemID>39410001557343</itemID>
				<itemTypeID>1IMP</itemTypeID>
				<currentLocationID>INTRANSIT</currentLocationID>
				<homeLocationID>11ABD</homeLocationID>
				<transitSourceLibraryID>ALFAX1</transitSourceLibraryID>
				<transitDestinationLibraryID>ALFMEDA</transitDestinationLibraryID>
				<transitReason>HOLD</transitReason>
				<transitDate>2012-09-14T16:17:00+02:00</transitDate>
				<chargeable>true</chargeable>
				<numberOfHolds>1</numberOfHolds>
				<fixedTimeBooking>false</fixedTimeBooking>
			</ItemInfo>
		</CallInfo>
		<CallInfo>
			<libraryID>CRETMAC</libraryID>
			<classificationID>ASIS</classificationID>
			<callNumber>BD LAR</callNumber>
			<numberOfCopies>1</numberOfCopies>
			<ItemInfo>
				<itemID>00580317</itemID>
				<itemTypeID>1IMP</itemTypeID>
				<currentLocationID>11ABD</currentLocationID>
				<homeLocationID>11ABD</homeLocationID>
				<chargeable>true</chargeable>
				<fixedTimeBooking>false</fixedTimeBooking>
			</ItemInfo>
		</CallInfo>
	</TitleInfo>
</LookupTitleInfoResponse>';
	}


	public static function xmlLookupTitleInfoHarryPotter() {
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<LookupTitleInfoResponse xmlns="http://schemas.sirsidynix.com/symws/standard">
	<TitleInfo>
		<titleID>353917</titleID>
		<TitleAvailabilityInfo>
			<totalCopiesAvailable>3</totalCopiesAvailable>
			<libraryWithAvailableCopies>Créteil : Maison des Arts</libraryWithAvailableCopies>
			<libraryWithAvailableCopies>Créteil : Bibliobus</libraryWithAvailableCopies>
			<libraryWithAvailableCopies>Limeil-Brévannes</libraryWithAvailableCopies>
			<totalResvCopiesAvailable>0</totalResvCopiesAvailable>
			<locationOfFirstAvailableItem>02MFILM</locationOfFirstAvailableItem>
			<holdable>false</holdable>
			<bookable>false</bookable>
		</TitleAvailabilityInfo>
		<CallInfo>
			<libraryID>CRETMAC</libraryID>
			<classificationID>ASIS</classificationID>
			<callNumber>520 HAR</callNumber>
			<numberOfCopies>1</numberOfCopies>
			<ItemInfo>
				<itemID>00580322</itemID>
				<itemTypeID>1MUS</itemTypeID>
				<currentLocationID>02MFILM</currentLocationID>
				<homeLocationID>02MFILM</homeLocationID>
				<chargeable>true</chargeable>
				<fixedTimeBooking>false</fixedTimeBooking>
			</ItemInfo>
		</CallInfo>
		<CallInfo>
			<libraryID>CRETBUS</libraryID>
			<classificationID>ASIS</classificationID>
			<callNumber>520 HAR</callNumber>
			<numberOfCopies>1</numberOfCopies>
			<ItemInfo>
				<itemID>00580317</itemID>
				<itemTypeID>1MUS</itemTypeID>
				<currentLocationID>07MFILM</currentLocationID>
				<homeLocationID>07MFILM</homeLocationID>
				<chargeable>true</chargeable>
				<fixedTimeBooking>false</fixedTimeBooking>
			</ItemInfo>
		</CallInfo>
		<CallInfo>
			<libraryID>LB</libraryID>
			<classificationID>ASIS</classificationID>
			<callNumber>8.1 HAR</callNumber>
			<numberOfCopies>1</numberOfCopies>
			<ItemInfo>
				<itemID>00594173</itemID>
				<itemTypeID>1MUS</itemTypeID>
				<currentLocationID>09JENR</currentLocationID>
				<homeLocationID>09JENR</homeLocationID>
				<chargeable>true</chargeable>
				<fixedTimeBooking>false</fixedTimeBooking>
			</ItemInfo>
		</CallInfo>
	</TitleInfo>
		</LookupTitleInfoResponse>';
	}
}


?>