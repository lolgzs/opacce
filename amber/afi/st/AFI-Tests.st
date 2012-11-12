Smalltalk current createPackage: 'AFI-Tests' properties: #{}!
TestCase subclass: #BibNumAlbumTestCase
	instanceVariableNames: 'ajax container album'
	package: 'AFI-Tests'!

!BibNumAlbumTestCase methodsFor: 'running'!

setUp
	window location hash: ' '.
	ajax := AMockWrapper on: Ajax new.
	ajax onMessage: 'send' answer: ajax.

	container := '<div></div>' asJQuery width: 500.

	album := BibNumAlbum new 
		ajax: ajax;
		container: container.
!

tearDown
	container remove.
! !

BibNumAlbumTestCase subclass: #BibNumAlbumDonjonTest
	instanceVariableNames: 'ajax container album'
	package: 'AFI-Tests'!

!BibNumAlbumDonjonTest methodsFor: 'accessing'!

donjonJSON
	^   '{"album": {	"id":4, 
					"titre":"Donjon Zenith", 
					"description":"Une bonne bagarre",
					"width": 200,
					"height": 50,
					"ressources":[ 
                                                          				{	"id":1,
                                                          					"titre": "Donjon",
                                                          					"link_to":"",
                                                          					"thumbnail":"userfiles/album/4/thumbs/media/1.jpg",
                                                          					"navigator_thumbnail":"userfiles/album/4/thumbs/media/1_small.jpg",
                                                          					"original":"bib-numerique/get-resource/id/4.jpg"}
				]
		} }'.
! !

!BibNumAlbumDonjonTest methodsFor: 'running'!

setUp
	super setUp.

	album
		url: 'donjon.json';
		load.

	(ajax options at: 'success')  value: self donjonJSON.
! !

!BibNumAlbumDonjonTest methodsFor: 'tests'!

testAnchorToDownloadPDFShouldNotBePresent
	self assert: 0 equals: ((container find: 'div.b-download-book a') at: 'size') value
! !

BibNumAlbumTestCase subclass: #BibNumAlbumTintinTestCase
	instanceVariableNames: ''
	package: 'AFI-Tests'!

!BibNumAlbumTintinTestCase methodsFor: 'running'!

setUp
	super setUp.

	album
		url: '/bib-numerique/album/id/2.json';
		scriptsRoot: 'http://localhost/afi-opac3/amber/afi/souvigny/';
		load.

	(ajax options at: 'success')  value: (jQuery parseJSON: self tintinJSON).
! !

BibNumAlbumTintinTestCase subclass: #BibNumAlbumTintinBookletTest
	instanceVariableNames: ''
	package: 'AFI-Tests'!

!BibNumAlbumTintinBookletTest methodsFor: 'json'!

tintinJSON
	^   '{"album": {	"id":2, 
					"titre":"Tintin et Milou", 
					"description":"The real story of Tintin",
					"width": 400,
					"height": 300,
					"download_url": "http://localhost/pdf/2",
					"ressources":[ 
                                                          				{	"id":12,
                                                          					"titre": "Origins",
												"foliono": "12R",
                                                          					"link_to":"",
                                                          					"description":"Created in 1929 by Herge" ,
                                                          					"thumbnail":"userfiles/album/2/thumbs/media/1.jpg",
                                                          					"navigator_thumbnail":"userfiles/album/2/thumbs/media/1_small.jpg",
                                                          					"original":"bib-numerique/get-resource/id/1.jpg",
                                                          					"download":"bib-numerique/download-resource/id/1"},
                                                          
                                                          				{	"id":13,
                                                          					"titre": "Haddock",
												"foliono": "XX",
                                                          					"link_to":"",
                                                          					"description":"Captain living in Moulinsard",
                                                          					"thumbnail":"userfiles/album/2/thumbs/media/2.jpg",
                                                          					"navigator_thumbnail":"userfiles/album/2/thumbs/media/2_small.jpg",
                                                          					"original":"bib-numerique/get-resource/id/2.jpg",
                                                          					"download":"bib-numerique/download-resource/id/2" }                                                        
                                                          				]
				} }'.
! !

!BibNumAlbumTintinBookletTest methodsFor: 'tests'!

testAnchorToDownloadPDFShouldBePresent
	self assert: 'http://localhost/pdf/2'  equals: ((container find: 'div.b-download-book a') attr: 'href' )
!

testBookletPluginShouldBeLoaded
	self assert: ('body' asJQuery at: 'booklet') notNil
!

testChapterSelectorInitialHeightShouldBeZero
	self assert: 0  equals: (container find: '.b-selector-chapter>ul')   height
!

testChapterSelectorShouldContainsLinkToFirstPage
	self assert: 'Origins'  equals: (container find: '.b-selector-chapter #selector-page-1')   text
!

testDivBkWidgetShouldBePresent
	|bkWidget|
	bkWidget := container children: '.bk-widget'.
	self assert: (0 < bkWidget length).
!

testDivBookShouldBePresent
	|book|
	book := container find: '.book'.
	self assert: (0 < book length).
!

testFirstPageDownloadURLShouldLinkToDownloadResourceAction
	self assert: 'bib-numerique/download-resource/id/1' equals: album pages first downloadURL
!

testFirstPageImageFolioNumberShouldBeTwelveR
	self assert: '12R' equals: (container find: '.b-counter + .b-counter')  text
!

testFirstPageImageShouldLinkToOneDotJpg
	self assert: 0 < (container find: 'img[src*="userfiles/album/2/thumbs/media/1.jpg"]')  length
!

testFirstPageNavigatorThumbnailShouldBeOneSmallDotJpg
	self assert: 'userfiles/album/2/thumbs/media/1_small.jpg' equals: album pages first navigatorThumbnailURL
!

testFirstPageZoomedImageShouldLinkToOneDotJpg
	(container find: '.b-zoom-magnify a') click.
	self assert: 0 < (container find: '.iviewer img[src*="bib-numerique/get-resource/id/1.jpg"]')  length
!

testNextPageShouldSetMenuTextToFin
	(container find: '.book') booklet: 'next'.
	self assert: 'Fin' equals: (container find: '.b-current') text
!

testPageDescShouldContainsCreatedIn1929ByHerge
	self assert: 'Created in 1929 by Herge' equals: (container find: '.page-desc') text
!

testPageHeightShouldBe124
	"the width of page (165) *  height of first image in json (300) / width of first image in json (400)"  
	self assert: 124 equals: (container find: '.b-page')  height
!

testPageWidthShouldBe165
	"resize to its container width (500)  - margins (170)  / 2 pages"  
	self assert: 165 equals: (container find: '.b-page')  width
!

testSecondPageImageFolioNumberShouldBeXX
	self assert: 'XX' equals: album pages last foliono
!

testShouldContainsFourPages
	"2 pages + covers"
	self assert: 4 equals: (container find: '.b-page') length
! !

BibNumAlbumTintinTestCase subclass: #BibNumAlbumTintinMonopageTest
	instanceVariableNames: ''
	package: 'AFI-Tests'!

!BibNumAlbumTintinMonopageTest methodsFor: 'json'!

tintinJSON
	^   '{"album": {	"id":2, 
					"titre":"Tintin et Milou", 
					"description":"The real story of Tintin",
					"width": 400,
					"height": 300,
					"download_url": "http://localhost/pdf/2",
                    "player": "BookMonoWidget",
					"ressources":[ 
                                                          				{	"id":12,
                                                          					"titre": "Origins",
												"foliono": "12R",
                                                          					"link_to":"",
                                                          					"description":"Created in 1929 by Herge" ,
                                                          					"thumbnail":"userfiles/album/2/thumbs/media/1.jpg",
                                                          					"navigator_thumbnail":"userfiles/album/2/thumbs/media/1_small.jpg",
                                                          					"original":"bib-numerique/get-resource/id/1.jpg",
                                                          					"download":"bib-numerique/download-resource/id/1"},
                                                          
                                                          				{	"id":13,
                                                          					"titre": "Haddock",
												"foliono": "XX",
                                                          					"link_to":"",
                                                          					"description":"Captain living in Moulinsard",
                                                          					"thumbnail":"userfiles/album/2/thumbs/media/2.jpg",
                                                          					"navigator_thumbnail":"userfiles/album/2/thumbs/media/2_small.jpg",
                                                          					"original":"bib-numerique/get-resource/id/2.jpg",
                                                          					"download":"bib-numerique/download-resource/id/2" }                                                        
                                                          				]
				} }'.
! !

!BibNumAlbumTintinMonopageTest methodsFor: 'tests'!

testBookletShouldNotBeLoaded
	self assert: 0 equals: ( container find: '.bk-widget') length
!

testFirstPageImageShouldLinkToOneDotJpg
	self assert: 0 < (container find: 'img[src*="userfiles/album/2/thumbs/media/1.jpg"]')  length
! !

TestCase subclass: #CycleTest
	instanceVariableNames: ''
	package: 'AFI-Tests'!

!CycleTest methodsFor: 'tests'!

testCycleWithFourElements
	|cycle|
	cycle := Cycle with: #('one' 'two' 'three' 'four').
	3 timesRepeat: [
		self assert: 'one' equals: cycle next.
		self assert: 'two' equals: cycle next.
		self assert: 'three' equals: cycle next.
		self assert: 'four' equals: cycle next ].
!

testCycleWithTwoElements
	|cycle|
	cycle := Cycle with: #('one' 'two').
	self assert: 'one' equals: cycle next.
	self assert: 'two' equals: cycle next.
	self assert: 'one' equals: cycle next.
	self assert: 'two' equals: cycle next.
! !

TestCase subclass: #SouvignyBibleTest
	instanceVariableNames: 'bible'
	package: 'AFI-Tests'!

!SouvignyBibleTest methodsFor: 'running'!

setUp
	bible := SouvignyBible new
! !

!SouvignyBibleTest methodsFor: 'tests'!

testFolio150VShouldReturnPage306
	self assert: 306 equals:(bible parseFolioNo: '150v').
!

testFolio151RShouldReturnPage307
	self assert: 307 equals:(bible parseFolioNo: '151r').
!

testFolioOneShouldReturnPageOne
	self assert: 1 equals: (bible parseFolioNo: '1')
! !

