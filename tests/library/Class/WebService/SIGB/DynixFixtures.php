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


	public static function loginUserManuLarcinetXml() {
		return 
			'<LoginUserResponse xmlns="http://schemas.sirsidynix.com/symws/security">
			  <userID>0917036</userID>
		    <sessionToken>497e6380-69fb-4850-b552-40dede41f0b5</sessionToken>
		  </LoginUserResponse>';
	}


	public static function manuLarcinetAccoutInfoXml() {
		return '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>
<ns3:LookupMyAccountInfoResponse xmlns:ns2="http://schemas.sirsidynix.com/symws/common" xmlns:ns3="http://schemas.sirsidynix.com/symws/patron">
	<ns3:patronInfo>
		<ns3:userKey>159</ns3:userKey>
		<ns3:userID>0917036</ns3:userID>
		<ns3:alternativeID></ns3:alternativeID>
		<ns3:webAuthID></ns3:webAuthID>
		<ns3:groupID></ns3:groupID>
		<ns3:displayName>LARCINET Manu</ns3:displayName>
		<ns3:birthDate>1962-01-22</ns3:birthDate>
		<ns3:patronLibraryID>CRETHAB</ns3:patronLibraryID>
		<ns3:patronLibraryDescription>Créteil : Habette</ns3:patronLibraryDescription>
		<ns3:department>94</ns3:department>
		<ns3:preferredLanguage>fr</ns3:preferredLanguage>
	</ns3:patronInfo>
	<ns3:patronCheckoutInfo>
		<ns3:titleKey>363377</ns3:titleKey>
		<ns3:itemID>00406882</ns3:itemID>
		<ns3:callNumber>F TRU</ns3:callNumber>
		<ns3:copyNumber>1</ns3:copyNumber>
		<ns3:pieces>1</ns3:pieces>
		<ns3:title>Chico &amp; Rita [Images animées]</ns3:title>
		<ns3:author>Trueba, Fernando</ns3:author>
		<ns3:checkoutLibraryID>CRETMAC</ns3:checkoutLibraryID>
		<ns3:checkoutLibraryDescription>Créteil : Maison des Arts</ns3:checkoutLibraryDescription>
		<ns3:itemLibraryID>CRETDOY</ns3:itemLibraryID>
		<ns3:itemLibraryDescription>Créteil : Doyen</ns3:itemLibraryDescription>
		<ns3:itemTypeID>1VIDEOF</ns3:itemTypeID>
		<ns3:itemTypeDescription>Vidéos de fiction</ns3:itemTypeDescription>	
		<ns3:checkoutDate xsi:type="xs:dateTime" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">2012-09-12T10:28:00+02:00</ns3:checkoutDate>
		<ns3:dueDate xsi:type="xs:dateTime" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">2012-10-17T23:59:00+02:00</ns3:dueDate>
		<ns3:recallNoticesSent>0</ns3:recallNoticesSent>
		<lastRenewedDate xsi:type="xs:dateTime" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">2012-09-26T12:44:00+02:00</lastRenewedDate>
		<ns3:renewals>2</ns3:renewals>
		<ns3:unseenRenewals>0</ns3:unseenRenewals>
		<ns3:renewalsRemaining>0</ns3:renewalsRemaining>
		<ns3:unseenRenewalsRemainingUnlimited>true</ns3:unseenRenewalsRemainingUnlimited>
		<ns3:overdue>false</ns3:overdue>
		<ns3:overdueNoticesSent>0</ns3:overdueNoticesSent>
	</ns3:patronCheckoutInfo>
	<ns3:patronCheckoutInfo>
		<ns3:titleKey>366596</ns3:titleKey>
		<ns3:itemID>00593153</ns3:itemID>
		<ns3:callNumber>099.2 KAT</ns3:callNumber>
		<ns3:copyNumber>1</ns3:copyNumber>
		<ns3:pieces>1</ns3:pieces>
		<ns3:title>Katerine, Francis et [enr. CD] ses peintres : CD 2, 52 reprises dans l\'espace</ns3:title>
		<ns3:author>Katerine. chant.</ns3:author>
		<ns3:checkoutLibraryID>CRETMAC</ns3:checkoutLibraryID>
		<ns3:checkoutLibraryDescription>Créteil : Maison des Arts</ns3:checkoutLibraryDescription>
		<ns3:itemLibraryID>ALFMEDA</ns3:itemLibraryID>
		<ns3:itemLibraryDescription>Alfortville : Pôle culturel</ns3:itemLibraryDescription>
		<ns3:itemTypeID>1IMP</ns3:itemTypeID>
		<ns3:itemTypeDescription>Imprimés</ns3:itemTypeDescription>
		<ns3:checkoutDate xsi:type="xs:dateTime" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">2012-10-01T13:26:00+02:00</ns3:checkoutDate>
		<ns3:dueDate xsi:type="xs:dateTime" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">2012-10-24T23:59:00+02:00</ns3:dueDate>
		<ns3:recallNoticesSent>0</ns3:recallNoticesSent>
		<ns3:renewals>0</ns3:renewals>
		<ns3:unseenRenewals>0</ns3:unseenRenewals>
		<ns3:renewalsRemaining>1</ns3:renewalsRemaining>
		<ns3:unseenRenewalsRemainingUnlimited>true</ns3:unseenRenewalsRemainingUnlimited>
		<ns3:overdue>false</ns3:overdue>
		<ns3:overdueNoticesSent>0</ns3:overdueNoticesSent>
	</ns3:patronCheckoutInfo>
	<ns3:patronCheckoutInfo>
		<ns3:titleKey>231595</ns3:titleKey>
		<ns3:itemID>39410001449012</ns3:itemID>
		<ns3:callNumber>2.STU</ns3:callNumber>
		<ns3:copyNumber>1</ns3:copyNumber>
		<ns3:pieces>1</ns3:pieces>
		<ns3:title>Stupeflip [CD]</ns3:title>
		<ns3:author>Stupeflip</ns3:author>
		<ns3:checkoutLibraryID>CRETMAC</ns3:checkoutLibraryID>
		<ns3:checkoutLibraryDescription>Créteil : Maison des Arts</ns3:checkoutLibraryDescription>
		<ns3:itemLibraryID>ALFMEDA</ns3:itemLibraryID>
		<ns3:itemLibraryDescription>Alfortville : Pôle culturel</ns3:itemLibraryDescription>
		<ns3:itemTypeID>1MUS</ns3:itemTypeID>
		<ns3:itemTypeDescription>Musique (CD et K7)</ns3:itemTypeDescription>
		<ns3:checkoutDate xsi:type="xs:dateTime" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">2012-10-01T13:26:00+02:00</ns3:checkoutDate>
		<ns3:dueDate xsi:type="xs:dateTime" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">2012-10-24T23:59:00+02:00</ns3:dueDate>
		<ns3:recallNoticesSent>0</ns3:recallNoticesSent>
		<ns3:renewals>0</ns3:renewals>
		<ns3:unseenRenewals>0</ns3:unseenRenewals>
		<ns3:renewalsRemaining>1</ns3:renewalsRemaining>
		<ns3:unseenRenewalsRemainingUnlimited>true</ns3:unseenRenewalsRemainingUnlimited>
		<ns3:overdue>false</ns3:overdue>
		<ns3:overdueNoticesSent>0</ns3:overdueNoticesSent>
	</ns3:patronCheckoutInfo>
	<ns3:patronHoldInfo>
		<ns3:holdKey>160540</ns3:holdKey>
		<ns3:titleKey>348899</ns3:titleKey>
		<ns3:itemID>00577705</ns3:itemID>
		<ns3:callNumber>2.STU 80</ns3:callNumber>
		<ns3:displayableCallNumber>2.STU 80</ns3:displayableCallNumber>
		<ns3:blanketHoldCopiesNeeded>0</ns3:blanketHoldCopiesNeeded>
		<ns3:blanketHoldCopiesReceived>0</ns3:blanketHoldCopiesReceived>
		<ns3:holdStatus>2</ns3:holdStatus>
		<ns3:itemTypeID>1MUS</ns3:itemTypeID>
		<ns3:itemTypeDescription>Musique (CD et K7)</ns3:itemTypeDescription>
		<ns3:title>The hypnoflip invasion [enr. CD]</ns3:title>
		<ns3:author>Stupeflip</ns3:author>
		<ns3:itemLibraryID>ALFMEDA</ns3:itemLibraryID>
		<ns3:itemLibraryDescription>Alfortville : Pôle culturel</ns3:itemLibraryDescription>
		<ns3:pickupLibraryID>CRETMAC</ns3:pickupLibraryID>
		<ns3:pickupLibraryDescription>Créteil : Maison des Arts</ns3:pickupLibraryDescription>
		<ns3:placedDate>2012-09-28</ns3:placedDate>
		<ns3:reserve>false</ns3:reserve>
		<ns3:recallStatus>STANDARD</ns3:recallStatus>
		<ns3:available>false</ns3:available>
		<ns3:intransit>false</ns3:intransit>
		<ns3:queuePosition>1</ns3:queuePosition>
		<ns3:queueLength>1</ns3:queueLength>
		<ns3:holdPlacedWithOverride>false</ns3:holdPlacedWithOverride>
		<ns3:orderLibraryID>ALFMEDA</ns3:orderLibraryID>
		<ns3:orderLibraryDescription>Alfortville : Pôle culturel</ns3:orderLibraryDescription>
	</ns3:patronHoldInfo>
	<ns3:patronHoldInfo>
		<ns3:holdKey>160543</ns3:holdKey>
		<ns3:titleKey>101964</ns3:titleKey>
		<ns3:itemID>00312977</ns3:itemID>
		<ns3:callNumber>2 STU</ns3:callNumber>
		<ns3:displayableCallNumber>2 STU</ns3:displayableCallNumber>
		<ns3:blanketHoldCopiesNeeded>0</ns3:blanketHoldCopiesNeeded>
		<ns3:blanketHoldCopiesReceived>0</ns3:blanketHoldCopiesReceived>
		<ns3:holdStatus>2</ns3:holdStatus>
		<ns3:itemTypeID>1MUS</ns3:itemTypeID>
		<ns3:itemTypeDescription>Musique (CD et K7)</ns3:itemTypeDescription>
		<ns3:title>Le Crou ne mourra [enr. sonore] jamais ; Stupeflip ; Présentation du Crou...[etc.]</ns3:title>
		<ns3:author>Stupeflip</ns3:author>
		<ns3:itemLibraryID>CRETBUS</ns3:itemLibraryID>
		<ns3:itemLibraryDescription>Créteil : Bibliobus</ns3:itemLibraryDescription>
		<ns3:pickupLibraryID>CRETMAC</ns3:pickupLibraryID>
		<ns3:pickupLibraryDescription>Créteil : Maison des Arts</ns3:pickupLibraryDescription>
		<ns3:placedDate>2012-09-28</ns3:placedDate>
		<ns3:reserve>false</ns3:reserve>
		<ns3:recallStatus>STANDARD</ns3:recallStatus>
		<ns3:available>true</ns3:available>
		<ns3:intransit>false</ns3:intransit>
		<ns3:queuePosition>2</ns3:queuePosition>
		<ns3:queueLength>2</ns3:queueLength>
		<ns3:holdPlacedWithOverride>false</ns3:holdPlacedWithOverride>
		<ns3:orderLibraryID>ALFMEDA</ns3:orderLibraryID>
		<ns3:orderLibraryDescription>Alfortville : Pôle culturel</ns3:orderLibraryDescription>
	</ns3:patronHoldInfo>
</ns3:LookupMyAccountInfoResponse>';
	}
}


?>

