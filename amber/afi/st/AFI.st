Smalltalk current createPackage: 'AFI' properties: #{}!
Widget subclass: #AbstractBookNavigatorWidget
	instanceVariableNames: 'book announcer'
	package: 'AFI'!

!AbstractBookNavigatorWidget methodsFor: 'accessing'!

announcer
	^ announcer ifNil: [announcer := Announcer new]
!

book: aBook
	book := aBook
!

style
	^ '

.b-navigator {
	height: 500px;
 	width: ', self width, 'px;
	overflow-x: hidden;
	overflow-y: hidden;
	border: 2px solid #666;
}

.b-navigator>div {
	text-align: center;
	border-bottom: 1px solid #666;
	background-color: #666;
	font-size: 1.1em;
}

.b-navigator>input {
	width: 100%;
	border: 1px solid #666;
	margin: 0px;
}

.b-navigator ul {
	height: 90%;
	overflow-x: hidden;
	overflow-y: auto;
	margin: 0px;
}
'
!

width
	^ self class width
! !

!AbstractBookNavigatorWidget methodsFor: 'actions'!

highlightPage: aPage
! !

!AbstractBookNavigatorWidget methodsFor: 'announcement'!

announcePageChange: aPage
	self announcer announce: (PageChangeAnnouncement page: aPage)
!

onPageChangeDo: aBlockWithArg
	self announcer 
		on: PageChangeAnnouncement 
		do: [:aPageChangeAnnouncement| 
			aBlockWithArg value: aPageChangeAnnouncement page]
! !

!AbstractBookNavigatorWidget methodsFor: 'rendering'!

renderOn: html
	self subclassResponsibility
! !

!AbstractBookNavigatorWidget class methodsFor: 'accessing'!

width
	^ 160
! !

AbstractBookNavigatorWidget subclass: #BookBookmarkNavigatorWidget
	instanceVariableNames: 'bookmarkList'
	package: 'AFI'!

!BookBookmarkNavigatorWidget methodsFor: 'accessing'!

style
	^ super style, '
.b-navigator-bookmark {
	border-top-right-radius: 10px;
	border-bottom-right-radius: 10px;
	border-left: 0px;
	margin-left: 0px;
	margin-right: 10px;
	float: left;
}

.b-navigator-bookmark ul {
	list-style: square;
	padding: 0px 10px 0px 15px;
}

.b-navigator-bookmark li {
	margin: 5px;
	padding: 0px;
	text-align: left;
	cursor: pointer;
	-webkit-transition: all 0.3s;
	-moz-transition: all 0.3s;
}

.b-navigator-bookmark li.selected {
	text-decoration: underline
}

.b-navigator-bookmark li:hover {
	color: #aaa;
}'
! !

!BookBookmarkNavigatorWidget methodsFor: 'actions'!

highlightPage: aPage
	|pageTitle listItemIndex|
	(bookmarkList find: 'li') removeClass: 'selected'.

	pageTitle := aPage title ifEmpty: [ (book pageAt: (aPage pageNo - 1) ifAbsent: [aPage]) title].

	pageTitle isEmpty ifFalse: [
		(bookmarkList find: 'li:contains("', pageTitle, '")') addClass: 'selected'.
	]
! !

!BookBookmarkNavigatorWidget methodsFor: 'rendering'!

renderOn: html
	html style: self style.
	html div 
		class: 'b-navigator-bookmark b-navigator';
		with: [ |bookmarkSearchInput |
			html div: 'Signets'.

			bookmarkSearchInput := html input asJQuery.
			bookmarkList := (html ul: [self renderPagesOn: html ]) asJQuery.

			ListFilter filter: bookmarkList withInput: bookmarkSearchInput.
		]
!

renderPagesOn: html
	book pagesWithTitle do: [:aPage|
		html li
			with: aPage title;
			onClick: [self announcePageChange: aPage]
	]
! !

AbstractBookNavigatorWidget subclass: #BookThumbnailNavigatorWidget
	instanceVariableNames: 'bookmarkList'
	package: 'AFI'!

!BookThumbnailNavigatorWidget methodsFor: 'actions'!

highlightPage: aPage
	|thumbnail listItemIndex|
        listItemIndex := 0 max: (aPage pageNo - 2).
        thumbnail := (bookmarkList find: 'li') get: listItemIndex.
	bookmarkList scrollTop: (thumbnail offsetTop - (bookmarkList height / 2)).
	(bookmarkList find: 'li') removeClass: 'selected'.
	(window jQuery: thumbnail) addClass: 'selected'.
! !

!BookThumbnailNavigatorWidget methodsFor: 'css'!

style
	^ super style, '
.b-navigator-thumbnail {
	border-top-left-radius: 10px;
	border-bottom-left-radius: 10px;
	border-right: 0px;
	margin-left: 10px;
	margin-right: 0px;
	float: right;
}

.b-navigator-thumbnail ul {
	list-style: none;
	padding: 0px;
	float: right;
}

.b-navigator-thumbnail li {
	float: left;
	margin: 5px;
	display: block;
	overflow: hidden;
	height: 70px;
	width: 50px;
	text-align: center;
	cursor: pointer;
}

.b-navigator-thumbnail li>div>div{
	display: none;
	position: relative;
	z-index: 2;
	background-color: black;
	font-weight: bold;
	font-size: 0.9em;
}


.b-navigator-thumbnail li.selected img,
.b-navigator-thumbnail li.selected + li.odd img,
.b-navigator-thumbnail .filtered li img,
.b-navigator-thumbnail li:hover img {
	opacity: 1;
}

.b-navigator-thumbnail li:hover {
	overflow: visible;
}

.b-navigator-thumbnail li:hover>div>div{
	display: block;
}

.b-navigator-thumbnail li>div {
	width: 50px;
  	-webkit-transition: all 0.1s ease-out;
 	-moz-transition: all 0.1s ease-out;
}


.b-navigator-thumbnail li:hover>div {
   width: 100px;
   position: relative;
   box-shadow: 0px 0px 20px black;
   z-index: 30;
}


.b-navigator-thumbnail li.odd:hover>div {
   margin-left: -40px;
}

.b-navigator-thumbnail ul li:first-child:hover>div {
   margin-right: -40px;
   margin-left: 0px;
   margin-top: 0px;
}

.b-navigator-thumbnail .filtered li.odd:hover>div {
   margin-left: 0px;
}

.b-navigator-thumbnail .filtered li {
   width: 100%;
}

.b-navigator-thumbnail li img {
  	width: 100%;
	display: block;
	opacity: 0.6;
}

.b-navigator-thumbnail li:first-child + li{
	clear: left;
}

.b-navigator-thumbnail li img {
	cursor: pointer;
}
'
! !

!BookThumbnailNavigatorWidget methodsFor: 'rendering'!

renderOn: html
	html style: self style.
	html div 
		class: 'b-navigator-thumbnail  b-navigator';
		with: [ |bookmarkSearchInput|
			html div: 'Folios'.

			bookmarkSearchInput := html input asJQuery.
			bookmarkList := html ul 
								with: [self renderPagesOn: html ]; 
								asJQuery.

			ListFilter filter: bookmarkList withInput: bookmarkSearchInput.
	]
!

renderPagesOn: html
	|cycle|
	cycle := Cycle with: #('odd' 'even').

	book pages do: [:aPage|
		html li
			class: cycle next;
			with: [ 	html div: [	html div: aPage foliono.
						 		html img src: aPage navigatorThumbnailURL] ];
			onClick: [self announcePageChange: aPage]
	]
! !

Object subclass: #Ajax
	instanceVariableNames: 'url settings options ajaxRequest'
	package: 'AFI'!

!Ajax methodsFor: 'accessing'!

options
	^ options ifNil: [options := HashedCollection new ]
!

url: aString
	url := aString
! !

!Ajax methodsFor: 'actions'!

abort
	ajaxRequest ifNotNil: [ajaxRequest abort]
!

send
	ajaxRequest := jQuery ajax: url options: options.
! !

!Ajax methodsFor: 'callback'!

onCompleteDo: aBlock
	"A block to be called when the request finishes (after success and error callbacks are executed). Block arguments: jqXHR, textStatus"
	self options at: 'complete' put: aBlock
!

onErrorDo: aBlock
	"A block to be called if the request fails.Block arguments: jqXHR, textStatus, errorThrown"
	self options at: 'error' put: aBlock
!

onSuccessDo: aBlock
	"Set action to execute when Ajax request is successful. Pass received data as block argument. Block arguments: data, textStatus, jqXHR"
	self options at: 'success' put: aBlock
! !

!Ajax class methodsFor: 'initialize'!

url: aString
	^ self new 
		url: aString;
		yourself
! !

Object subclass: #BibNumAlbum
	instanceVariableNames: 'container ajax url scriptsRoot bookWidget'
	package: 'AFI'!

!BibNumAlbum methodsFor: 'accessing'!

ajax
	^ ajax ifNil: [ajax := Ajax url: self url]
!

ajax: anAjax
	ajax := anAjax
!

container
	^ container
!

container: aJQuery
	container := aJQuery
!

pages
	^ bookWidget book pages
!

scriptsRoot
	^ scriptsRoot ifNil: [scriptsRoot := '']
!

scriptsRoot: anUrl
	scriptsRoot := anUrl
!

url
	^ url
!

url: aString
	url := aString
! !

!BibNumAlbum methodsFor: 'loading'!

load
	bookWidget := BookWidget new 
					loader: (BibNumLoader new
          				              		ajax: self ajax;
         		            		   		yourself);
       				        scriptsRoot: self scriptsRoot;
					appendToJQuery: self container
! !

!BibNumAlbum class methodsFor: 'instance creation'!

load: anURLForJSON  in: aJQuerySelector
	^  self new
		url: anURLForJSON;
		container: aJQuerySelector asJQuery; 
		load.
!

load: anURLForJSON  in: aJQuerySelector scriptsRoot: anURL
	^  self new
		url: anURLForJSON;
		container: aJQuerySelector asJQuery; 
		scriptsRoot: anURL;
		load.
! !

Object subclass: #BibNumLoader
	instanceVariableNames: 'ajax'
	package: 'AFI'!

!BibNumLoader methodsFor: 'accessing'!

ajax
	^ ajax ifNil: [ajax := Ajax new]
!

ajax: anAjax
	ajax := anAjax
! !

!BibNumLoader methodsFor: 'loading'!

abort
	self ajax abort
!

buildBookFromJSon: aJSONObject
	|book album|
	album := aJSONObject album.
	book := Book new
				title: album titre;
				width: album width;
				height: album height;
				downloadUrl: (album at: 'download_url');
				yourself.
	album ressources do: [:aRessource| 
        					book newPage
                                			title: aRessource titre;
                              				description: aRessource description;
							thumbnailURL: aRessource thumbnail;
                             				fullImageURL: aRessource original;
							foliono: aRessource foliono;
							navigatorThumbnailURL: (aRessource at: 'navigator_thumbnail')].
	^ book
!

loadBookFromJSONOnSuccess: aBlock
	self ajax
		onSuccessDo: [:data| |book|
							book := self buildBookFromJSon: data.
							aBlock value: book];
		send.
! !

BibNumLoader subclass: #SouvignyLoader
	instanceVariableNames: 'pages links book'
	package: 'AFI'!
!SouvignyLoader commentStamp!
I'm a loader dedicated to the Bible de Souvigny!

!SouvignyLoader methodsFor: 'accessing'!

baseURL
	^ 'souvigny/B031906101_MS_001/'
!

book
	^ book ifNil: [book := self class bookClass new
                      					width: 390;
                      					height: 594;
                      					yourself				]
!

fullImagesURL
	^ self baseURL, 'big/'
!

links
	^ links ifNil: [links := Dictionary new]
!

metadataURL
	^ 'souvigny/souvigny.json'.
!

parsePageNo: aString
	^  (aString includesSubString: 'r') 
			ifTrue: [aString allButLast asNumber * 2]
			ifFalse: [  (aString includesSubString: 'v') 
                                 		ifTrue: [aString allButLast asNumber * 2 + 1]
                                 		ifFalse: [aString] ]
!

thumbsJSONURL
	^ 'souvigny/thumbs.json'
!

thumbsURL
	^ self baseURL, 'thumbs/'
! !

!SouvignyLoader methodsFor: 'loading'!

loadBookFromJSONOnSuccess: aBlock
	(self ajax url: self thumbsJSONURL) 
		onSuccessDo: [:data|
                              		self buildBookFromJSON: data.
                              		self onMetadataLoadedDo: [
                                          aBlock value: self book]]; 
		send.
!

loadBookOnSuccess: aBlock
	(Ajax url: self thumbsURL) 
		onSuccessDo: [:data|
                              		self buildBookFromHTML: data.
                              		self onMetadataLoadedDo: [
                                          aBlock value: self book]]; 
		send.
!

onMetadataLoadedDo: aBlock
	(Ajax url: self metadataURL)
		onSuccessDo: [:data|
                              		self initMetadata: data.
                              		aBlock value];
		send
! !

!SouvignyLoader methodsFor: 'page creation'!

buildBookFromHTML: aHTMLString
	|anchors|
	anchors := (aHTMLString asJQuery find:'li a[href$="jpg"]').
	anchors each: [:index :element| |fileName|
                       fileName := (JQuery fromElement: element) attr: 'href'.
                       self book newPage
                       			fullImageURL: self fullImagesURL, fileName;
                       			thumbnailURL: self thumbsURL, fileName.
        ].
!

buildBookFromJSON: anArray
	anArray do: [:fileName|
                       self book newPage
                       			fullImageURL: self fullImagesURL, fileName;
                       			thumbnailURL: self thumbsURL, fileName.
        ].
!

initMetadata: anArray
	anArray do: [:aJSObject| |pageNo page|
                     pageNo := <aJSObject.pageNo>.
                     pageNo ifNotNil: [
                   	page := self book pageAtFolio: pageNo.
                     	page ifNotNil: [page initMetadata: aJSObject] ].
        ]
! !

!SouvignyLoader class methodsFor: 'accessing'!

bookClass
	^SouvignyBible
! !

Object subclass: #Book
	instanceVariableNames: 'pages title width height downloadUrl'
	package: 'AFI'!

!Book methodsFor: 'accessing'!

downloadUrl
	^ downloadUrl
!

downloadUrl: anUrl
	downloadUrl := anUrl
!

height
	^ height ifNil: [height := 400]
!

height: anInteger
	height := anInteger
!

pageAt: aNumber
	^ self pages at: aNumber
!

pageAt: aNumber ifAbsent: aBlock
	^ self pages at: aNumber ifAbsent: aBlock
!

pageNo: aPage
	^ self pages indexOf: aPage
!

pages
	^ pages ifNil: [pages := Array new]
!

pagesWithTitle
	^ self pages reject: [:aPage | aPage title isEmpty]
!

size
	^ pages size
!

title
	^ title
!

title: aString
	title := aString
!

width
	^ width ifNil: [width := 300]
!

width: anInteger
	width := anInteger
! !

!Book methodsFor: 'adding'!

addPage: aPage
	self pages add: aPage.
	aPage book: self.
	^ aPage
!

newPage
	^ self addPage: (self class pageClass new)
! !

!Book methodsFor: 'enumerating'!

pageAt: pageNo do: aBlockWithArg
	|page|
	page := self pages at: pageNo ifAbsent: [nil].
        page ifNotNil: [aBlockWithArg value: page].
!

pagesNo:  anArray do: aBlockWithArg
	anArray do: [:pageNo|
                 self pageAt: pageNo do: aBlockWithArg
         ].
!

pagesNo: start to: end do: aBlockWithArg
	start to: end do: [:pageNo|
                 self pageAt: pageNo do: aBlockWithArg
         ].
! !

!Book methodsFor: 'reset'!

reset
	self pages do: [:aPage| aPage reset]
! !

!Book class methodsFor: 'accessing'!

pageClass
	^ Page
! !

Book subclass: #SouvignyBible
	instanceVariableNames: ''
	package: 'AFI'!

!SouvignyBible methodsFor: 'accessing'!

pageAtFolio: aString
	^ self pages at: (self parseFolioNo: aString) ifAbsent: [nil].
!

parseFolioNo: aString
	"Folios are numbered 32r 32v as page 32 recto, page 32 verso. Excepted 3 first folios (6 pages)"
	^(aString includesSubString: 'r') 
			ifTrue: [aString allButLast asNumber * 2 + 5]
			ifFalse: [  (aString includesSubString: 'v') 
                                 		ifTrue: [aString allButLast asNumber * 2 + 1 + 5]
                                 		ifFalse: [aString asNumber] ].
!

title
	^ 'Bible de Souvigny'
! !

!SouvignyBible class methodsFor: 'accessing'!

pageClass
	^ SouvignyPage
! !

Widget subclass: #BookWidget
	instanceVariableNames: 'book currentPageNo pageZoomBrush pageZoomWidget zoomLeftPageAnchor zoomRightPageAnchor pageDescriptionsBrush loader scriptsRoot bookContainer width rootBrush menuJQuery isFullscreen downloadBrush leftFolioBrush rightFolioBrush announcer'
	package: 'AFI'!

!BookWidget methodsFor: 'accessing'!

book
	^ book
!

bookletOptions
	^ HashedCollection new 
                               at: 'pageSelector' put:false; 
                               at: 'chapterSelector' put: isFullscreen not; 
                               at: 'menu' put: menuJQuery;
                               at: 'tabs' put: false;
                               at: 'keyboard' put: false;
                               at: 'arrows' put: true;
                               at: 'hash' put: true;
                               at: 'closed' put: true;
                               at: 'covers' put: true;
                               at: 'autoCenter' put: true;
                               at: 'pagePadding' put: 0;
                               at: 'shadows' put: true;
			       at: 'width' put: self width;
			       at: 'height' put: self height;
                               at: 'manual' put: false;
			       at: 'pageNumbers' put: false;
                               at: 'overlays' put: false;
                               at: 'hovers' put: false;
			       at: 'arrowsHide' put: false;
			       at: 'closedFrontTitle' put: book title;
			       at: 'closedFrontChapter' put: book title;
			       at: 'closedBackTitle' put: 'Fin';
			       at: 'closedBackChapter' put: 'Fin';
			       at: 'previousPageTitle' put: 'Précédent';
			       at: 'nextPageTitle' put: 'Suivant';
			       at: 'before' put: [:data| self beforePageChange:data]; 
                               at: 'after' put: [:data|  self afterPageChange: data];
                               yourself
!

currentPage
	^ book pageAt: self currentPageNo ifAbsent: [book pages last]
!

currentPageNo
	^ currentPageNo ifNil: [currentPageNo := 1]
!

height
	^ ((book height * self width / book width) / 2) rounded
!

leftPage
	^ book pageAt: (currentPageNo - 1) ifAbsent: [Page new].
!

loader
	^ loader ifNil: [loader := SouvignyLoader new]
!

loader: aBibNumLoader
	loader := aBibNumLoader
!

navigatorWidth
	^ AbstractBookNavigatorWidget width
!

rightPage
	^ book pageAt: currentPageNo ifAbsent: [Page new].
!

scriptsRoot
	^ scriptsRoot ifNil: [scriptsRoot := '']
!

scriptsRoot: anUrl
	scriptsRoot := anUrl
!

widgetClass
	^ isFullscreen 
		ifTrue: ['fullscreen bk-widget'] 
		ifFalse: ['bk-widget']
!

width
	^ (isFullscreen 
			ifTrue: [('body' asJQuery width - (2 * self navigatorWidth)) min: 900] 
			ifFalse: [rootBrush asJQuery width])  - (2 * self zoomControlWidth)
!

zoomControlWidth
	^  self isContainerSmall ifTrue: [30] ifFalse: [85].
! !

!BookWidget methodsFor: 'announcements'!

announcePageChange: aPage
	self announcer announce: (PageChangeAnnouncement page: aPage)
!

announcer
	^ announcer ifNil: [announcer := Announcer new]
!

onPageChangeDo: aBlockWithArg
	self announcer 
		on: PageChangeAnnouncement 
		do: [:aPageChangeAnnouncement| 
			aBlockWithArg value: aPageChangeAnnouncement page]
! !

!BookWidget methodsFor: 'callbacks'!

afterPageChange: data
	self updateFolioNumbers.
	self openDescriptions.
	self announcePageChange: self currentPage.
!

beforePageChange:data
	self closeDescriptions.
	self openPageNo: (data basicAt: 'curr') + 1.
	self closeZoom.
!

goToPageNo: pageNo
	bookContainer asJQuery booklet:  (pageNo ).
!

openPage: aPage
	self goToPageNo: aPage pageNo.
!

openPageNo: anInteger
	currentPageNo := anInteger.
	book 
		pagesNo: {anInteger - 1. anInteger} 
		do: [:aPage| aPage renderWidth: (self width / 2) rounded height: self height].
!

reloadWidget
	rootBrush contents: [:html| self renderWidgetOn: html].
!

toggleFullscreen
	isFullscreen := isFullscreen not.
	self loader abort.
	self reloadWidget.
! !

!BookWidget methodsFor: 'css'!

bookStyle
	^ '
			.bk-widget .booklet { margin-bottom: 20px	}			

			.bib-num-album {  padding: 10px }

			.bk-widget .b-counter {
			  margin-bottom: -20px;
			  margin-top: 20px;
			  width: 140px;
			  text-align: left;
			  bottom: 0px;
			  background-color: transparent;
			  font-weight: bold;
			  font-size: 1.1em;
			}

			.bk-widget .b-counter + .b-counter {
			  right: 0px;
			  text-align: right;
			}

			.bk-widget .loading {  text-align:center	}
			
			.bk-widget .booklet .b-wrap-right {
			  background-color: transparent;
			  padding: 0px; !!important;
			}

			.bk-widget .booklet .b-wrap-left {
			  background-color: transparent;
			  padding: 0px; !!important;
			}

			.bk-widget .booklet .b-page-cover {  background-color: transparent; }

			.bk-widget .b-menu {
			  font-size: 1.4em;
			  font-weight: bold;
			  width: 820px;
			  margin: 0 auto;
			  height: 60px;
			}

			.bk-widget .b-menu .b-selector {
			  width: 600px;
			  text-align: left;
			  height: 60px;
			  float:none;
			}

			.bk-widget .b-menu .b-selector .b-current {
			  height: auto;
			  text-align: left;
			  background: url(', self scriptsRoot, 'images/menu_off.png) no-repeat 15px center;
			  padding-left: 45px;
			}

			.bk-widget .b-menu .b-selector:hover .b-current {
			  background-image: url(', self scriptsRoot, 'images/menu_on.png);
			}

			.bk-widget .b-menu .b-selector { color: black; }

			.bk-widget .b-menu .b-selector a { color: inherit;}

			.bk-widget .b-menu .b-selector:hover {color: black; }

			.bk-widget .b-menu .b-selector:hover ul { box-shadow: 2px 2px 40px rgba(2,2,0,0.8); }

			.bk-widget .b-menu .b-selector ul {
			  width: 584px;
			  top: auto;
			  max-height: 600px;
			  overflow-y: auto !!important;
			  background-color: white;
			}

			.bk-widget .b-menu .b-selector ul li { font-size: 1.2em; }

			.bk-widget .b-menu .b-selector ul li a { height: auto; }

			.bk-widget .b-menu .b-selector ul li a .b-text { float: none; }

			.bk-widget button {float: left}

			.bk-widget .booklet .b-arrow div {
				-webkit-transition: all 0.3s;
				-moz-transition: all 0.3s;
				-o-transition: all 0.3s;
			}

			.bk-widget .booklet .b-arrow-next div { background-image:url(', self scriptsRoot, 'booklet/images/arrow-next_black.png);}

			.bk-widget .booklet .b-arrow-next:hover div { background-image:url(', self scriptsRoot, 'booklet/images/arrow-next.png);}

			.bk-widget .booklet .b-arrow-prev div { background-image:url(', self scriptsRoot, 'booklet/images/arrow-prev_black.png); }

			.bk-widget .booklet .b-arrow-prev:hover div { background-image:url(', self scriptsRoot, 'booklet/images/arrow-prev.png);}

			.bk-widget .b-counter + .b-counter {float: right;}

			.small>.bk-widget .booklet .b-arrow-next div {background-image:url(', self scriptsRoot, 'booklet/images/arrow-next_black-small.png);}

			.small>.bk-widget .booklet .b-arrow-next:hover div { background-image:url(', self scriptsRoot, 'booklet/images/arrow-next-small.png); }

			.small>.bk-widget .booklet .b-arrow-prev div { background-image:url(', self scriptsRoot, 'booklet/images/arrow-prev_black-small.png); }

			.small>.bk-widget .booklet .b-arrow-prev:hover div { background-image:url(', self scriptsRoot, 'booklet/images/arrow-prev-small.png);}

			.small>.bk-widget .booklet .b-arrow-prev { left: -25px }
		
			.small>.bk-widget .booklet .b-arrow-next { right: -25px }

			.small>.bk-widget .booklet .b-arrow { width: 25px }

			.small>.bk-widget .booklet .b-arrow  div { top: 36% }

			.clear { 
				clear: both;
				height: 0px !!important;
			}

			.bk-widget .b-wrap {
				cursor: -moz-zoom-in;
				cursor: -webkit-zoom-in;
			}
'
!

fullScreenStyle
	^ '
	body.fullscreen {
		overflow: hidden;
	}


	.fullscreen.bk-widget {
		position: fixed;
		width: 100%;
		height: 100%;
		z-index: 200;
		top: 0;
		left: 0;
		overflow-y: auto;
	}

	.fullscreen.bk-widget .b-menu {
		height: 45px;
	}

	.fullscreen.bk-widget,
	.fullscreen.bk-widget .b-menu .b-selector,
	.fullscreen.bk-widget .b-menu .b-selector ul,
	.fullscreen.bk-widget .b-counter {	
		color: white;
		background-color: black;
	}

	.fullscreen .b-zoom-fullscreen {
		position: absolute;
		right: 0px;
	}

	.fullscreen.bk-widget .b-download-book a {
		position: absolute;
		right: 60px;
	}

	.fullscreen .b-zoom-fullscreen a {
		background: url(', self scriptsRoot, 'images/unexpand_black.png) no-repeat;
	}

	.fullscreen .b-zoom-fullscreen a:hover {
		background: url(', self scriptsRoot, 'images/unexpand_white.png) no-repeat;
	}
'
!

style
	^ String streamContents: [:aStream|
                                  	aStream
                                  		nextPutAll: self bookStyle;
                                  		nextPutAll: self zoomControlsStyle;
						nextPutAll: self fullScreenStyle
          ]
!

zoomControlsStyle
	^ '
			.b-zoom {
			  position: fixed;
			  top: 0px;
			  left: 0px;
			  width: 100%;
			  height: 100%;
			  display: none;
			  z-index: 200;
			}

			.b-zoom .page-desc {
			  margin: 0px 5px;
			  width: auto;
			  color: white;
			  width: 45%;
			  padding-right: 20px;
			  height: 95%;
			  max-width:auto;
			  overflow-y: auto;
			  display: block;
			  float: left;
			  font-size: 1.3em;
			}

			.b-zoom-magnify {
			  margin: 0px auto;
			}

			.b-zoom-magnify a,
			.b-zoom-fullscreen a {
				display: block;
				width: 48px;
				height: 48px;
				z-index: 20;
				position: relative;
				cursor: pointer;
			}
			
			.b-zoom-fullscreen {float: right}

			.b-zoom-fullscreen a {
				background: url(', self scriptsRoot, 'images/expand_black.png) no-repeat;
			}

			.b-zoom-fullscreen a:hover {
				background: url(', self scriptsRoot, 'images/expand_white.png) no-repeat;
			}

			.b-download-book a {
				float: right;
				display: block;
				width: 73px;
				height: 36px;
				margin-right: 5px;
				margin-top: 6px;
				z-index: 20;
				position: relative;
				cursor: pointer;
				background: url(', self scriptsRoot, 'images/download_black.png) no-repeat;
			}

			.b-download-book a:hover {
				background: url(', self scriptsRoot, 'images/download_white.png) no-repeat;
			}

			.small>.bk-widget .b-zoom-magnify a {
				background-image: none;
			}

			.b-zoom-magnify a {
				background: url(', self scriptsRoot, 'images/magnify_black.png) no-repeat;
			}
			
			.b-zoom-magnify a:hover {
				background-image: url(', self scriptsRoot, 'images/magnify_white.png);
			}

			.small>.bk-widget .b-zoom-magnify a:hover {
				background-image: none;
			}

			.b-zoom-magnify a {
				float: left;
			}

			.b-zoom-magnify a + a {
				float: right;
			}

			.b-zoom > div {
			  position: relative;
			  z-index: 30;

			  background-color: rgb(10,10,10);
			  border: 10px solid rgb(50,50,50);

			  background-color: rgba(10,10,10,0.8);
			  border: 10px solid rgba(50,50,50,0.8);

			  border-radius: 10px;
			  display:none;
			  padding: 1px;
			  height: 100%;
			}

			.b-zoom > div > div {
			  overflow: scroll;
			  border-radius: 10px;
			}

			.b-zoom .iviewer {
				height: 100%
			}

			.b-zoom .iviewer_with_text {
			  float: left;
			  width: 50%;
			  margin-right: 5px;
			}

			.iviewer {
			  backround-color: black;
			}

			.iviewer_cursor {
			  cursor: move;
			}

			.controls div.iviewer_common {
			  position: static !!important;		
			  margin: 5px auto;
			  background-color: transparent;
			}

			.controls div.iviewer_common:hover {
				background-color: white;
			}

			.iviewer_zoom_close {
			  background: url(', self scriptsRoot, 'images/close_black28.png);
			}
'
! !

!BookWidget methodsFor: 'descriptions'!

closeDescriptions
	pageDescriptionsBrush asJQuery fadeOut.
!

openDescriptions
	pageDescriptionsBrush asJQuery hide.
	pageDescriptionsBrush contents: [:html| 
               		(html div asJQuery) html: self leftPage description.
               		(html div asJQuery) html: self rightPage description.
        ].
	pageDescriptionsBrush asJQuery fadeIn.
!

updateFolioNumbers
	leftFolioBrush contents: self leftPage foliono.
	rightFolioBrush contents: self rightPage foliono.
! !

!BookWidget methodsFor: 'initialize'!

initialize
	super initialize.
	isFullscreen := false.
! !

!BookWidget methodsFor: 'loading'!

loadBookThenRenderOn: bookBrush
	|renderBlock|
	renderBlock := [:aBook| self renderBook:aBook on: bookBrush].
	book 
		ifNil: [self loader loadBookFromJSONOnSuccess: renderBlock]
		ifNotNil: [	book reset.					
					renderBlock value: book].
! !

!BookWidget methodsFor: 'rendering'!

renderBook: aBook on: aBrush
	book := aBook.

	aBrush contents: [:html|
		aBook pages do: [:aPage| 	aPage brush: (html div
        		         								rel: aPage title;
                                 								yourself)  ]	].
	
	self isContainerSmall ifTrue: [rootBrush asJQuery addClass: 'small'].
	(bookContainer asJQuery at: 'booklet') ifNotNil: [ bookContainer asJQuery booklet: (self bookletOptions) ].
	(rootBrush asJQuery find: '.b-wrap-left') click: [self zoomLeftPage].
	(rootBrush asJQuery find: '.b-wrap-right, .b-page-cover') click: [self zoomRightPage].
	
	book downloadUrl ifNotEmpty: [downloadBrush contents: [:html| html a href: aBook downloadUrl]].
	
	isFullscreen ifTrue: [self renderBookNavigator]
!

renderBookMenuOn: html
	menuJQuery := html div
		class: 'book-menu';
		asJQuery.
!

renderBookNavigator
	|navigatorDiv|
	navigatorDiv := '<div></div>' asJQuery.
	navigatorDiv insertAfter: menuJQuery.

	{BookBookmarkNavigatorWidget. BookThumbnailNavigatorWidget} do: [:aNavigatorClass| |navigator|
		navigator := aNavigatorClass new
						book: book;
						appendToJQuery: navigatorDiv;
						onPageChangeDo: [:aPage| self openPage: aPage];
						highlightPage: self currentPage;
						yourself.

		self onPageChangeDo: [:aPage|  navigator highlightPage: aPage].
	]
!

renderBookOn: html
	bookContainer := html div.
	bookContainer
	    class: 'book';
            with: [	|bookBrush|
			leftFolioBrush := html div class: 'b-counter'.
			rightFolioBrush := html div class: 'b-counter'.
                   	bookBrush := html div 
                   		class: 'b-load';
                   		with: [html div
                                          	class: 'loading';
                                          	with: [ html img src: self scriptsRoot, 'images/ajax-loader.gif'] ];
                   		yourself.
                   
                   	self loadBookThenRenderOn: bookBrush]
!

renderDevToolsOn: html
	((Smalltalk current at: 'Browser') notNil and: [self isRunInTestCase not]) ifTrue:
		 [   AFIIDETools 
					addButton: 'Reload booklet' action: [ self reloadWidget ];
					addButton: 'Inspect booklet' action: [ self inspect ];
					addButton: 'Toggle fullscreen' action: [ self toggleFullscreen ]	]
!

renderDownloadBookOn: html
	downloadBrush := html div class: 'b-download-book'
!

renderFullscreenControlsOn: html
	html div 
		class: 'b-zoom-fullscreen';
		with: [ html a onClick: [self toggleFullscreen] ].
!

renderOn: html
        self renderDevToolsOn: html.
	rootBrush := html root.
	self renderWidgetOn: html.
	"window at:'onresize' put: [self reloadWidget]."
!

renderPageDescriptionOn: html 	
	pageDescriptionsBrush := html div 
                                                               class: 'page-desc';
                       						yourself.
!

renderScripts
	|head|
	head := 'head' asJQuery.
	(head find: 'script[src*="booklet"]') length = 0 ifTrue: [
		self renderScriptsOn: (HTMLCanvas onJQuery: head).
          ].
	isFullscreen 
		ifTrue: ['body' asJQuery addClass: 'fullscreen'] 
		ifFalse: ['body' asJQuery removeClass: 'fullscreen'].
!

renderScriptsOn: html
	#( 	'booklet/jquery.booklet.1.2.0.css'
          	'iviewer/jquery.iviewer.css' ) do: [:anUrl|
                                                   				html link
											href: self scriptsRoot, anUrl;
											type:'text/css';
											rel:'stylesheet'	].
	html style
		type: 'text/css';
		with: self style.

	(jQuery at: 'ui') ifNil: [ html script
         				    	type: 'text/javascript';
                				src: 	'http://ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js' ]. 

	#(	'booklet/jquery.easing.1.3.js'
	  	'booklet/jquery.booklet.1.2.0.min.js' 
          	'iviewer/jquery.iviewer.min.js'
          	'iviewer/jquery.mousewheel.min.js'
         	) do: [:anUrl| 
                           html script
                           	type: 'text/javascript';
                                src: self scriptsRoot, anUrl]
!

renderWidgetOn: html
	html div
		class: self widgetClass; 
		with: [	self 
					renderScripts;
                       			renderFullscreenControlsOn: html;
					renderDownloadBookOn: html;
                  			renderBookMenuOn: html;
                       			renderZoomControlsOn: html;
                  			renderBookOn: html;
					renderPageDescriptionOn: html 	].
!

renderZoomControlsOn: html
	html div
		class: 'b-zoom-magnify';
		with: [ 	zoomLeftPageAnchor := (html a onClick: [self zoomLeftPage]) asJQuery.
                       		zoomLeftPageAnchor hide.
                       
                         	zoomRightPageAnchor := (html a onClick: [self zoomRightPage]) asJQuery.
                       		zoomRightPageAnchor hide.
                                pageZoomBrush := html div 
						class: 'b-zoom';
						yourself.
                ].
! !

!BookWidget methodsFor: 'show'!

clear
	'.bk-widget' asJQuery remove.
!

reset
	self clear show.
!

show
	self appendToJQuery: '.bib-num-album' asJQuery
! !

!BookWidget methodsFor: 'testing'!

ifIE: aBlock ifNotIE: anotherBlock
	self isIE ifTrue: aBlock ifFalse: anotherBlock
!

isContainerSmall
	^ rootBrush asJQuery width < 500
!

isIE
	|ie|
	ie := <jQuery.browser.msie>.
	^ ie notNil.
!

isRunInTestCase
	^ self isTestCaseInContext: thisContext
!

isTestCaseInContext: aContext 
	^ aContext home 
		ifNil: [false]
		ifNotNil: [ (aContext receiver isKindOf: TestCase) or: [ self isTestCaseInContext: aContext home]].
! !

!BookWidget methodsFor: 'zoom'!

closeZoom
	'.b-arrow' asJQuery show.

	pageZoomBrush asJQuery 
		fadeOut: 'slow' do: [
			pageZoomWidget := nil.
			pageZoomBrush empty.
			"pageZoomBrush asJQuery show."
                  
			zoomLeftPageAnchor 
				removeClass: 'active';
				show.

			zoomRightPageAnchor 
				removeClass: 'active';
				show.
                  
                 	self currentPageNo = 1 ifTrue: [zoomLeftPageAnchor hide].
			self currentPageNo > book size ifTrue: [zoomRightPageAnchor hide].
        ]
!

closeZoomOr: aBlock
	pageZoomWidget ifNil: aBlock ifNotNil: [ 	self closeZoom. 
                                                					self openDescriptions]
!

renderPage: aPage class: aCssClass on: html
	(html div
		class: aCssClass;
		with:[	pageZoomWidget := PageWidget new
										page: aPage;
										renderOn: html;
										onCloseDo: [	self 
														closeZoom; 
														openDescriptions];
                                                                               	yourself 								];
                asJQuery) fadeIn: 'slow'.
!

zoomLeftPage
	self closeZoomOr: [
          	self zoomPageNo: currentPageNo - 1 withClass: 'b-left'.
          	zoomLeftPageAnchor addClass: 'active'.
        ].
!

zoomPageNo: anInteger withClass: aCssClass
	self closeDescriptions.

	zoomLeftPageAnchor hide.
        zoomRightPageAnchor hide.
	'.b-arrow' asJQuery hide.

	book pageAt: anInteger do: [:aPage| 
                                           			pageZoomBrush contents: [:html|  self renderPage: aPage class: aCssClass on: html ].
								pageZoomBrush asJQuery show.												 	].
!

zoomRightPage
	self closeZoomOr: [
          	self zoomPageNo: currentPageNo withClass: 'b-right'.
          	zoomRightPageAnchor addClass: 'active'.
        ].
! !

!BookWidget class methodsFor: 'initialize release'!

open
	^ self new show.
!

reset
	^ self new reset.
! !

Object subclass: #Cycle
	instanceVariableNames: 'elements counter'
	package: 'AFI'!

!Cycle methodsFor: 'accessing'!

elements: anArray
	elements := anArray
!

next
	counter := counter + 1.
	^elements at: (counter \\ elements size) + 1.
! !

!Cycle methodsFor: 'initialize'!

initialize
	counter := -1
! !

!Cycle class methodsFor: 'instance creation'!

with: anArray
	^ self new elements: anArray
! !

Object subclass: #ListFilter
	instanceVariableNames: 'book announcer jqueryInput jqueryList'
	package: 'AFI'!

!ListFilter methodsFor: 'callback'!

filterListWithInputString
	|searchString regExp matches items|
	searchString := jqueryInput val.
	regExp := <new RegExp(searchString, 'i')>.
	items := jqueryList find: 'li'.
	matches := items filter: [:anInteger| <regExp.test($(this).text())>].
	items hide.
	matches show.

	searchString isEmpty ifTrue: [jqueryList removeClass: 'filtered'] ifFalse: [jqueryList addClass: 'filtered'].
! !

!ListFilter methodsFor: 'initialization'!

filter: aJQueryList withInput: aJQueryInput
	jqueryList := aJQueryList.
	jqueryInput := aJQueryInput.
	jqueryInput keyup: [self filterListWithInputString].
! !

!ListFilter class methodsFor: 'instance creation'!

filter: aJQueryList withInput: aJQueryInput
	^ self new filter: aJQueryList withInput: aJQueryInput
! !

Object subclass: #Page
	instanceVariableNames: 'brush fullImageURL thumbnailURL description title rendered foliono navigatorThumbnailURL book'
	package: 'AFI'!

!Page methodsFor: 'accessing'!

book
	^ book
!

book: aBook
	book := aBook
!

brush
	^ brush
!

brush: aBrush
	brush := aBrush
!

description
	^ description ifNil: [description := '']
!

description: aString
	description := aString
!

foliono
	^ foliono ifNil: [foliono := '']
!

foliono: aString
	foliono := aString
!

fullImageURL
	^ fullImageURL ifNil: [fullImageURL := '']
!

fullImageURL: aString
	fullImageURL := aString
!

initMetadata: aJSObject
	description := <aJSObject.description>.
	title := <aJSObject.book>.
!

navigatorThumbnailURL
	^ navigatorThumbnailURL ifNil: [navigatorThumbnailURL := ''].
!

navigatorThumbnailURL: aString
	navigatorThumbnailURL := aString
!

pageNo
	^ book pageNo: self
!

thumbnailURL
	^ thumbnailURL ifNil: [thumbnailURL := ''].
!

thumbnailURL: aString
	thumbnailURL := aString
!

title
	^ title ifNil: [title := '']
!

title: aString
	title := aString
! !

!Page methodsFor: 'printing'!

printString
	^ String streamContents: [:aStream|
                                  aStream
                                  	nextPutAll: super printString;
                                  	nextPutAll: '(';
                                  	nextPutAll: self title;
                                  	nextPutAll:')'.
        ].
! !

!Page methodsFor: 'rendering'!

render
	self renderWidth: self width height: self height.
!

renderWidth: width height: height
	self isRendered ifFalse: [
          	rendered := true.
		brush contents: [:html| html img
                                 				width:  width;
                                 				height:  height;
                                 				src: thumbnailURL].
          ].
! !

!Page methodsFor: 'reset'!

reset
	^ rendered := false
! !

!Page methodsFor: 'testing'!

isRendered
	^ rendered ifNil: [rendered := false]
! !

Page subclass: #SouvignyPage
	instanceVariableNames: 'book icon letter subject'
	package: 'AFI'!

!SouvignyPage methodsFor: 'accessing'!

initMetadata: aJSObject
	book := <aJSObject.book>.
	icon := <aJSObject.icon>.
	letter := <aJSObject.letter>.
	subject := <aJSObject.subject>.
	description := <aJSObject.description>.
 	
	title := String streamContents: [:aStream| 
                                                         aStream nextPutAll: book.
                                                         	  icon ifNotEmpty: [aStream nextPutAll: ' - ', icon].
                                                         	  subject ifNotEmpty: [aStream nextPutAll: ' - ', subject].
                                                     	]
! !

Object subclass: #PageChangeAnnouncement
	instanceVariableNames: 'page'
	package: 'AFI'!

!PageChangeAnnouncement methodsFor: 'accessing'!

page
	^ page
!

page: aPage
	page := aPage
! !

!PageChangeAnnouncement class methodsFor: 'instance creation'!

page: aPage
	^ self new page: aPage
! !

Widget subclass: #PageWidget
	instanceVariableNames: 'page inControl outControl fitControl statusControl zeroControl closeControl closeBlock rotateRightControl rotation'
	package: 'AFI'!
!PageWidget commentStamp!
I display a full page with zoom controller and description!

!PageWidget methodsFor: 'accessing'!

onCloseDo: aBlock
	closeBlock := aBlock
!

page: aPage
	page := aPage.
! !

!PageWidget methodsFor: 'callback'!

close
	closeBlock value.
!

initIViewer: aViewer
	inControl onClick: [<aViewer.zoom_by(1)>].
	outControl onClick: [<aViewer.zoom_by(-1)>].
	fitControl onClick: [aViewer fit].
	zeroControl onClick: [<aViewer.set_zoom(100)>].
	rotateRightControl onClick: [self rotateRight].
!

rotateRight
	|rotationDeg|
	rotation := (rotation ifNil:[0]) + 90 .
	rotationDeg := 'rotate(',rotation asString, 'deg)'.
	'.iviewer img' asJQuery
		css: '-ms-transform' value: rotationDeg;
		css: '-o-transform' value: rotationDeg;
		css: '-moz-transform' value: rotationDeg;
		css: '-webkit-transform' value: rotationDeg
!

updateZoomStatus: newZoom
	statusControl contents: 'x', (newZoom / 100  printShowingDecimalPlaces: 1).
! !

!PageWidget methodsFor: 'css'!

style
	^ '	.b-zoom .controls {
			  height: auto;
			  padding: 4px;
			  margin: 0 4px;
			  background-color: rgb(200,200,200);
			  background-color: rgba(200,200,200,0.8);
			  overflow: hidden;
			  float: right;
			  position: absolute;
			  *position: relative;
			  z-index: 1;
			  text-align: center;
			  width: 42px;
                          right: 0px;
			}
'
! !

!PageWidget methodsFor: 'rendering'!

renderControlsOn: html
	|addControl|
	html div 
		class: 'controls';
		with: [
          		addControl :=  [:name| html div 
                                			class: 'iviewer_zoom_', name, ' iviewer_common iviewer_button';
                               				yourself].
                  	closeControl := addControl value: 'close'.
                  	closeControl onClick: [self close].
               		inControl := addControl value: 'in'.
          		outControl := addControl value: 'out'.
          		zeroControl := addControl value: 'zero'.
          		fitControl := addControl value: 'fit'.
          		statusControl := addControl value: 'status'.
          		rotateRightControl := addControl value: 'rotate_right'.
        ].
!

renderOn: html
	|iViewer|
	html style: self style.
	self renderControlsOn: html.
	iViewer := html div 
		class: 'iviewer';
		asJQuery.
	
	page description ifNotEmpty: [iViewer addClass: 'iviewer_with_text'].

	iViewer iviewer: (HashedCollection new
                                               			at: 'src' put: page fullImageURL;
                              					at: 'zoom' put: 'fit';
                         					at: 'zoom_min' put: 10;
                        					at: 'zoom_max' put: 400;
	                     					at: 'ui_disabled' put: true;
        	              					at: 'initCallback' put: [:aViewer| self initIViewer: aViewer];
                            					at: 'onZoom' put: [:aString| self updateZoomStatus: aString];
                              					yourself).

        (html div class: 'page-desc';  asJQuery) html: page description.
	html div class: 'clear'.
! !

