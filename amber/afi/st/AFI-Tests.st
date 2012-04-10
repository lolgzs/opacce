Smalltalk current createPackage: 'AFI-Tests' properties: #{}!
TestCase subclass: #CycleTest
	instanceVariableNames: ''
	category: 'AFI-Tests'!

!CycleTest methodsFor: 'tests'!

testCycleWithTwoElements
	|cycle|
	cycle := Cycle with: #('one' 'two').
	self assert: 'one' equals: cycle next.
	self assert: 'two' equals: cycle next.
	self assert: 'one' equals: cycle next.
	self assert: 'two' equals: cycle next.
!

testCycleWithFourElements
	|cycle|
	cycle := Cycle with: #('one' 'two' 'three' 'four').
	3 timesRepeat: [
		self assert: 'one' equals: cycle next.
		self assert: 'two' equals: cycle next.
		self assert: 'three' equals: cycle next.
		self assert: 'four' equals: cycle next ].
! !

TestCase subclass: #SouvignyBibleTest
	instanceVariableNames: 'bible'
	category: 'AFI-Tests'!

!SouvignyBibleTest methodsFor: 'running'!

setUp
	bible := SouvignyBible new
! !

!SouvignyBibleTest methodsFor: 'tests'!

testFolioOneShouldReturnPageOne
	self assert: 1 equals: (bible parseFolioNo: '1')
!

testFolio150VShouldReturnPage306
	self assert: 306 equals:(bible parseFolioNo: '150v').
!

testFolio151RShouldReturnPage307
	self assert: 307 equals:(bible parseFolioNo: '151r').
! !

TestCase subclass: #BibNumAlbumTintinTest
	instanceVariableNames: 'bible album container ajax'
	category: 'AFI-Tests'!

!BibNumAlbumTintinTest methodsFor: 'json'!

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
                                                          					"original":"bib-numerique/get-resource/id/1.jpg"},
                                                          
                                                          				{	"id":13,
                                                          					"titre": "Haddock",
												"foliono": "XX",
                                                          					"link_to":"",
                                                          					"description":"Captain living in Moulinsard",
                                                          					"thumbnail":"userfiles/album/2/thumbs/media/2.jpg",
                                                          					"navigator_thumbnail":"userfiles/album/2/thumbs/media/2_small.jpg",
                                                          					"original":"bib-numerique/get-resource/id/2.jpg" }                                                        
                                                          				]
				} }'.
! !

!BibNumAlbumTintinTest methodsFor: 'running'!

setUp
	window location hash: ' '.
	ajax := AMockWrapper on: Ajax new.
	ajax onMessage: 'send' answer: ajax.

	container := '<div></div>' asJQuery width: 500.

	album := BibNumAlbum new 
		url: '/bib-numerique/album/id/2.json';
		scriptsRoot: 'http://localhost/afi-opac3/amber/afi/souvigny/';
		ajax: ajax;
		container: container; 
		load.

	(ajax options at: 'success')  value: (jQuery parseJSON: self tintinJSON).
!

tearDown
	container remove.
! !

!BibNumAlbumTintinTest methodsFor: 'tests'!

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

testBookletPluginShouldBeLoaded
	self assert: ('body' asJQuery at: 'booklet') notNil
!

testBookletJSShouldBeLoadedOnce
	self assert: 1 equals: ( 'head' asJQuery find: 'script[src*="booklet.1.2.0.min.js"]') length
!

testShouldContainsFourPages
	"2 pages + covers"
	self assert: 4 equals: (container find: '.b-page') length
!

testFirstPageImageShouldLinkToOneDotJpg
	self assert: 0 < (container find: 'img[src*="userfiles/album/2/thumbs/media/1.jpg"]')  length
!

testPageDescShouldContainsCreatedIn1929ByHerge
	self assert: 'Created in 1929 by Herge' equals: (container find: '.page-desc') text
!

testPageWidthShouldBe165
	"resize to its container width (500)  - margins (170)  / 2 pages"  
	self assert: 165 equals: (container find: '.b-page')  width
!

testPageHeightShouldBe124
	"the width of page (165) *  height of first image in json (300) / width of first image in json (400)"  
	self assert: 124 equals: (container find: '.b-page')  height
!

testFirstPageZoomedImageShouldLinkToOneDotJpg
	(container find: '.b-zoom-magnify a') click.
	self assert: 0 < (container find: '.iviewer img[src*="bib-numerique/get-resource/id/1.jpg"]')  length
!

testChapterSelectorShouldContainsLinkToFirstPage
	self assert: 'Origins'  equals: (container find: '.b-selector-chapter #selector-page-1')   text
!

testChapterSelectorInitialHeightShouldBeZero
	self assert: 0  equals: (container find: '.b-selector-chapter>ul')   height
!

testFirstPageImageFolioNumberShouldBeTwelveR
	self assert: '12R' equals: (container find: '.b-counter + .b-counter')  text
!

testSecondPageImageFolioNumberShouldBeXX
	self assert: 'XX' equals: album pages last foliono
!

testAnchorToDownloadPDFShouldBePresent
	self assert: 'http://localhost/pdf/2'  equals: ((container find: 'div.b-download-book a') attr: 'href' )
!

testNextPageShouldSetMenuTextToFin
	(container find: '.book') booklet: 'next'.
	self assert: 'Fin' equals: (container find: '.b-current') text
!

testFirstPageNavigatorThumbnailShouldBeOneSmallDotJpg
	self assert: 'userfiles/album/2/thumbs/media/1_small.jpg' equals: album pages first navigatorThumbnailURL
! !

