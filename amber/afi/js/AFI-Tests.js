smalltalk.addPackage('AFI-Tests', {});
smalltalk.addClass('BibNumAlbumTestCase', smalltalk.TestCase, ['ajax', 'container', 'album'], 'AFI-Tests');
smalltalk.addMethod(
"_setUp",
smalltalk.method({
selector: "setUp",
category: 'running',
fn: function () {
    var self = this;
    var $1, $2;
    smalltalk.send(smalltalk.send(window, "_location", []), "_hash_", [" "]);
    self['@ajax'] = smalltalk.send(smalltalk.AMockWrapper || AMockWrapper, "_on_", [smalltalk.send(smalltalk.Ajax || Ajax, "_new", [])]);
    smalltalk.send(self['@ajax'], "_onMessage_answer_", ["send", self['@ajax']]);
    self['@container'] = smalltalk.send(smalltalk.send("<div></div>", "_asJQuery", []), "_width_", [500]);
    $1 = smalltalk.send(smalltalk.BibNumAlbum || BibNumAlbum, "_new", []);
    smalltalk.send($1, "_ajax_", [self['@ajax']]);
    smalltalk.send($1, "_scriptsRoot_", ["http://localhost/afi-opac3/amber/afi/souvigny/"]);
    $2 = smalltalk.send($1, "_container_", [self['@container']]);
    self['@album'] = $2;
    return self;
},
args: [],
source: "setUp\x0a\x09window location hash: ' '.\x0a\x09ajax := AMockWrapper on: Ajax new.\x0a\x09ajax onMessage: 'send' answer: ajax.\x0a\x0a\x09container := '<div></div>' asJQuery width: 500.\x0a\x0a\x09album := BibNumAlbum new \x0a\x09\x09ajax: ajax;\x0a\x09\x09scriptsRoot: 'http://localhost/afi-opac3/amber/afi/souvigny/';\x0a\x09\x09container: container.",
messageSends: ["hash:", "location", "on:", "new", "onMessage:answer:", "width:", "asJQuery", "ajax:", "scriptsRoot:", "container:"],
referencedClasses: ["Ajax", "AMockWrapper", "BibNumAlbum"]
}),
smalltalk.BibNumAlbumTestCase);

smalltalk.addMethod(
"_tearDown",
smalltalk.method({
selector: "tearDown",
category: 'running',
fn: function () {
    var self = this;
    smalltalk.send(self['@container'], "_remove", []);
    return self;
},
args: [],
source: "tearDown\x0a\x09container remove.",
messageSends: ["remove"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTestCase);



smalltalk.addClass('BibNumAlbumDonjonTest', smalltalk.BibNumAlbumTestCase, ['ajax', 'container', 'album'], 'AFI-Tests');
smalltalk.addMethod(
"_donjonJSON",
smalltalk.method({
selector: "donjonJSON",
category: 'accessing',
fn: function () {
    var self = this;
    return unescape("%7B%22album%22%3A%20%7B%09%22id%22%3A4%2C%20%0A%09%09%09%09%09%22titre%22%3A%22Donjon%20Zenith%22%2C%20%0A%09%09%09%09%09%22description%22%3A%22Une%20bonne%20bagarre%22%2C%0A%09%09%09%09%09%22width%22%3A%20200%2C%0A%09%09%09%09%09%22height%22%3A%2050%2C%0A%09%09%09%09%09%22ressources%22%3A%5B%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%7B%09%22id%22%3A1%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22titre%22%3A%20%22Donjon%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22link_to%22%3A%22%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22thumbnail%22%3A%22userfiles/album/4/thumbs/media/1.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22navigator_thumbnail%22%3A%22userfiles/album/4/thumbs/media/1_small.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22original%22%3A%22bib-numerique/get-resource/id/4.jpg%22%7D%0A%09%09%09%09%5D%0A%09%09%7D%20%7D");
    return self;
},
args: [],
source: "donjonJSON\x0a\x09^   '{\x22album\x22: {\x09\x22id\x22:4, \x0a\x09\x09\x09\x09\x09\x22titre\x22:\x22Donjon Zenith\x22, \x0a\x09\x09\x09\x09\x09\x22description\x22:\x22Une bonne bagarre\x22,\x0a\x09\x09\x09\x09\x09\x22width\x22: 200,\x0a\x09\x09\x09\x09\x09\x22height\x22: 50,\x0a\x09\x09\x09\x09\x09\x22ressources\x22:[ \x0a                                                          \x09\x09\x09\x09{\x09\x22id\x22:1,\x0a                                                          \x09\x09\x09\x09\x09\x22titre\x22: \x22Donjon\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22link_to\x22:\x22\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22thumbnail\x22:\x22userfiles/album/4/thumbs/media/1.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22navigator_thumbnail\x22:\x22userfiles/album/4/thumbs/media/1_small.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22original\x22:\x22bib-numerique/get-resource/id/4.jpg\x22}\x0a\x09\x09\x09\x09]\x0a\x09\x09} }'.",
messageSends: [],
referencedClasses: []
}),
smalltalk.BibNumAlbumDonjonTest);

smalltalk.addMethod(
"_setUp",
smalltalk.method({
selector: "setUp",
category: 'running',
fn: function () {
    var self = this;
    smalltalk.send(self, "_setUp", [], smalltalk.BibNumAlbumTestCase);
    (function ($rec) {smalltalk.send($rec, "_url_", ["donjon.json"]);return smalltalk.send($rec, "_load", []);}(self['@album']));
    smalltalk.send(smalltalk.send(smalltalk.send(self['@ajax'], "_options", []), "_at_", ["success"]), "_value_", [smalltalk.send(self, "_donjonJSON", [])]);
    return self;
},
args: [],
source: "setUp\x0a\x09super setUp.\x0a\x0a\x09album\x0a\x09\x09url: 'donjon.json';\x0a\x09\x09load.\x0a\x0a\x09(ajax options at: 'success')  value: self donjonJSON.",
messageSends: ["setUp", "url:", "load", "value:", "at:", "options", "donjonJSON"],
referencedClasses: []
}),
smalltalk.BibNumAlbumDonjonTest);

smalltalk.addMethod(
"_testAnchorToDownloadPDFShouldNotBePresent",
smalltalk.method({
selector: "testAnchorToDownloadPDFShouldNotBePresent",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [0, smalltalk.send(smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape("div.b-download-book%20a")]), "_at_", ["size"]), "_value", [])]);
    return self;
},
args: [],
source: "testAnchorToDownloadPDFShouldNotBePresent\x0a\x09self assert: 0 equals: ((container find: 'div.b-download-book a') at: 'size') value",
messageSends: ["assert:equals:", "value", "at:", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumDonjonTest);



smalltalk.addClass('BibNumAlbumTintinTestCase', smalltalk.BibNumAlbumTestCase, [], 'AFI-Tests');
smalltalk.addMethod(
"_setUp",
smalltalk.method({
selector: "setUp",
category: 'running',
fn: function () {
    var self = this;
    var $1;
    smalltalk.send(self, "_setUp", [], smalltalk.BibNumAlbumTestCase);
    smalltalk.send(self['@album'], "_url_", ["/bib-numerique/album/id/2.json"]);
    $1 = smalltalk.send(self['@album'], "_load", []);
    smalltalk.send(smalltalk.send(smalltalk.send(self['@ajax'], "_options", []), "_at_", ["success"]), "_value_", [smalltalk.send(jQuery, "_parseJSON_", [smalltalk.send(self, "_tintinJSON", [])])]);
    return self;
},
args: [],
source: "setUp\x0a\x09super setUp.\x0a\x0a\x09album\x0a\x09\x09url: '/bib-numerique/album/id/2.json';\x0a\x09\x09load.\x0a\x0a\x09(ajax options at: 'success')  value: (jQuery parseJSON: self tintinJSON).",
messageSends: ["setUp", "url:", "load", "value:", "parseJSON:", "tintinJSON", "at:", "options"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTestCase);



smalltalk.addClass('BibNumAlbumTintinBookletTest', smalltalk.BibNumAlbumTintinTestCase, [], 'AFI-Tests');
smalltalk.addMethod(
"_testAnchorToDownloadPDFShouldBePresent",
smalltalk.method({
selector: "testAnchorToDownloadPDFShouldBePresent",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [unescape("http%3A//localhost/pdf/2"), smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape("div.b-download-book%20a")]), "_attr_", ["href"])]);
    return self;
},
args: [],
source: "testAnchorToDownloadPDFShouldBePresent\x0a\x09self assert: 'http://localhost/pdf/2'  equals: ((container find: 'div.b-download-book a') attr: 'href' )",
messageSends: ["assert:equals:", "attr:", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testBookletPluginShouldBeLoaded",
smalltalk.method({
selector: "testBookletPluginShouldBeLoaded",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_", [smalltalk.send(smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_at_", ["booklet"]), "_notNil", [])]);
    return self;
},
args: [],
source: "testBookletPluginShouldBeLoaded\x0a\x09self assert: ('body' asJQuery at: 'booklet') notNil ",
messageSends: ["assert:", "notNil", "at:", "asJQuery"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testChapterSelectorInitialHeightShouldBeZero",
smalltalk.method({
selector: "testChapterSelectorInitialHeightShouldBeZero",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [0, smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-selector-chapter%3Eul")]), "_height", [])]);
    return self;
},
args: [],
source: "testChapterSelectorInitialHeightShouldBeZero\x0a\x09self assert: 0  equals: (container find: '.b-selector-chapter>ul')   height ",
messageSends: ["assert:equals:", "height", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testChapterSelectorShouldContainsLinkToFirstPage",
smalltalk.method({
selector: "testChapterSelectorShouldContainsLinkToFirstPage",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", ["Origins", smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-selector-chapter%20%23selector-page-1")]), "_text", [])]);
    return self;
},
args: [],
source: "testChapterSelectorShouldContainsLinkToFirstPage\x0a\x09self assert: 'Origins'  equals: (container find: '.b-selector-chapter #selector-page-1')   text ",
messageSends: ["assert:equals:", "text", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testDivBkWidgetShouldBePresent",
smalltalk.method({
selector: "testDivBkWidgetShouldBePresent",
category: 'tests',
fn: function () {
    var self = this;
    var bkWidget = nil;
    bkWidget = smalltalk.send(self['@container'], "_children_", [unescape(".bk-widget")]);
    smalltalk.send(self, "_assert_", [0 < smalltalk.send(bkWidget, "_length", [])]);
    return self;
},
args: [],
source: "testDivBkWidgetShouldBePresent\x0a\x09|bkWidget|\x0a\x09bkWidget := container children: '.bk-widget'.\x0a\x09self assert: (0 < bkWidget length).",
messageSends: ["children:", "assert:", "<", "length"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testDivBookShouldBePresent",
smalltalk.method({
selector: "testDivBookShouldBePresent",
category: 'tests',
fn: function () {
    var self = this;
    var book = nil;
    book = smalltalk.send(self['@container'], "_find_", [".book"]);
    smalltalk.send(self, "_assert_", [0 < smalltalk.send(book, "_length", [])]);
    return self;
},
args: [],
source: "testDivBookShouldBePresent\x0a\x09|book|\x0a\x09book := container find: '.book'.\x0a\x09self assert: (0 < book length).",
messageSends: ["find:", "assert:", "<", "length"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testFirstPageDownloadURLShouldLinkToDownloadResourceAction",
smalltalk.method({
selector: "testFirstPageDownloadURLShouldLinkToDownloadResourceAction",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [unescape("bib-numerique/download-resource/id/1"), smalltalk.send(smalltalk.send(smalltalk.send(self['@album'], "_pages", []), "_first", []), "_downloadURL", [])]);
    return self;
},
args: [],
source: "testFirstPageDownloadURLShouldLinkToDownloadResourceAction\x0a\x09self assert: 'bib-numerique/download-resource/id/1' equals: album pages first downloadURL ",
messageSends: ["assert:equals:", "downloadURL", "first", "pages"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testFirstPageImageFolioNumberShouldBeTwelveR",
smalltalk.method({
selector: "testFirstPageImageFolioNumberShouldBeTwelveR",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", ["12R", smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-counter%20+%20.b-counter")]), "_text", [])]);
    return self;
},
args: [],
source: "testFirstPageImageFolioNumberShouldBeTwelveR\x0a\x09self assert: '12R' equals: (container find: '.b-counter + .b-counter')  text",
messageSends: ["assert:equals:", "text", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testFirstPageImageShouldLinkToOneDotJpg",
smalltalk.method({
selector: "testFirstPageImageShouldLinkToOneDotJpg",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_", [0 < smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape("img%5Bsrc*%3D%22userfiles/album/2/thumbs/media/1.jpg%22%5D")]), "_length", [])]);
    return self;
},
args: [],
source: "testFirstPageImageShouldLinkToOneDotJpg\x0a\x09self assert: 0 < (container find: 'img[src*=\x22userfiles/album/2/thumbs/media/1.jpg\x22]')  length",
messageSends: ["assert:", "<", "length", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testFirstPageNavigatorThumbnailShouldBeOneSmallDotJpg",
smalltalk.method({
selector: "testFirstPageNavigatorThumbnailShouldBeOneSmallDotJpg",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [unescape("userfiles/album/2/thumbs/media/1_small.jpg"), smalltalk.send(smalltalk.send(smalltalk.send(self['@album'], "_pages", []), "_first", []), "_navigatorThumbnailURL", [])]);
    return self;
},
args: [],
source: "testFirstPageNavigatorThumbnailShouldBeOneSmallDotJpg\x0a\x09self assert: 'userfiles/album/2/thumbs/media/1_small.jpg' equals: album pages first navigatorThumbnailURL ",
messageSends: ["assert:equals:", "navigatorThumbnailURL", "first", "pages"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testFirstPageZoomedImageShouldLinkToOneDotJpg",
smalltalk.method({
selector: "testFirstPageZoomedImageShouldLinkToOneDotJpg",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-zoom-magnify%20a")]), "_click", []);
    smalltalk.send(self, "_assert_", [0 < smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".iviewer%20img%5Bsrc*%3D%22bib-numerique/get-resource/id/1.jpg%22%5D")]), "_length", [])]);
    return self;
},
args: [],
source: "testFirstPageZoomedImageShouldLinkToOneDotJpg\x0a\x09(container find: '.b-zoom-magnify a') click.\x0a\x09self assert: 0 < (container find: '.iviewer img[src*=\x22bib-numerique/get-resource/id/1.jpg\x22]')  length",
messageSends: ["click", "find:", "assert:", "<", "length"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testNextPageShouldSetMenuTextToFin",
smalltalk.method({
selector: "testNextPageShouldSetMenuTextToFin",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(self['@container'], "_find_", [".book"]), "_booklet_", ["next"]);
    smalltalk.send(self, "_assert_equals_", ["Fin", smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-current")]), "_text", [])]);
    return self;
},
args: [],
source: "testNextPageShouldSetMenuTextToFin\x0a\x09(container find: '.book') booklet: 'next'.\x0a\x09self assert: 'Fin' equals: (container find: '.b-current') text",
messageSends: ["booklet:", "find:", "assert:equals:", "text"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testPageDescShouldContainsCreatedIn1929ByHerge",
smalltalk.method({
selector: "testPageDescShouldContainsCreatedIn1929ByHerge",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", ["Created in 1929 by Herge", smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".page-desc")]), "_text", [])]);
    return self;
},
args: [],
source: "testPageDescShouldContainsCreatedIn1929ByHerge\x0a\x09self assert: 'Created in 1929 by Herge' equals: (container find: '.page-desc') text",
messageSends: ["assert:equals:", "text", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testPageHeightShouldBe124",
smalltalk.method({
selector: "testPageHeightShouldBe124",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [124, smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-page")]), "_height", [])]);
    return self;
},
args: [],
source: "testPageHeightShouldBe124\x0a\x09\x22the width of page (165) *  height of first image in json (300) / width of first image in json (400)\x22  \x0a\x09self assert: 124 equals: (container find: '.b-page')  height",
messageSends: ["assert:equals:", "height", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testPageWidthShouldBe165",
smalltalk.method({
selector: "testPageWidthShouldBe165",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [165, smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-page")]), "_width", [])]);
    return self;
},
args: [],
source: "testPageWidthShouldBe165\x0a\x09\x22resize to its container width (500)  - margins (170)  / 2 pages\x22  \x0a\x09self assert: 165 equals: (container find: '.b-page')  width",
messageSends: ["assert:equals:", "width", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testSecondPageImageFolioNumberShouldBeXX",
smalltalk.method({
selector: "testSecondPageImageFolioNumberShouldBeXX",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", ["XX", smalltalk.send(smalltalk.send(smalltalk.send(self['@album'], "_pages", []), "_last", []), "_foliono", [])]);
    return self;
},
args: [],
source: "testSecondPageImageFolioNumberShouldBeXX\x0a\x09self assert: 'XX' equals: album pages last foliono",
messageSends: ["assert:equals:", "foliono", "last", "pages"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testShouldContainsFourPages",
smalltalk.method({
selector: "testShouldContainsFourPages",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [4, smalltalk.send(smalltalk.send(self['@container'], "_find_", [".b-page"]), "_length", [])]);
    return self;
},
args: [],
source: "testShouldContainsFourPages\x0a\x09\x222 pages + covers\x22\x0a\x09self assert: 4 equals: (container find: '.b-page') length",
messageSends: ["assert:equals:", "length", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_tintinJSON",
smalltalk.method({
selector: "tintinJSON",
category: 'json',
fn: function () {
    var self = this;
    return unescape("%7B%22album%22%3A%20%7B%09%22id%22%3A2%2C%20%0A%09%09%09%09%09%22titre%22%3A%22Tintin%20et%20Milou%22%2C%20%0A%09%09%09%09%09%22description%22%3A%22The%20real%20story%20of%20Tintin%22%2C%0A%09%09%09%09%09%22width%22%3A%20400%2C%0A%09%09%09%09%09%22height%22%3A%20300%2C%0A%09%09%09%09%09%22download_url%22%3A%20%22http%3A//localhost/pdf/2%22%2C%0A%09%09%09%09%09%22ressources%22%3A%5B%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%7B%09%22id%22%3A12%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22titre%22%3A%20%22Origins%22%2C%0A%09%09%09%09%09%09%09%09%09%09%09%09%22foliono%22%3A%20%2212R%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22link_to%22%3A%22%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22description%22%3A%22Created%20in%201929%20by%20Herge%22%20%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22thumbnail%22%3A%22userfiles/album/2/thumbs/media/1.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22navigator_thumbnail%22%3A%22userfiles/album/2/thumbs/media/1_small.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22original%22%3A%22bib-numerique/get-resource/id/1.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22download%22%3A%22bib-numerique/download-resource/id/1%22%7D%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%7B%09%22id%22%3A13%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22titre%22%3A%20%22Haddock%22%2C%0A%09%09%09%09%09%09%09%09%09%09%09%09%22foliono%22%3A%20%22XX%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22link_to%22%3A%22%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22description%22%3A%22Captain%20living%20in%20Moulinsard%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22thumbnail%22%3A%22userfiles/album/2/thumbs/media/2.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22navigator_thumbnail%22%3A%22userfiles/album/2/thumbs/media/2_small.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22original%22%3A%22bib-numerique/get-resource/id/2.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22download%22%3A%22bib-numerique/download-resource/id/2%22%20%7D%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%5D%0A%09%09%09%09%7D%20%7D");
    return self;
},
args: [],
source: "tintinJSON\x0a\x09^   '{\x22album\x22: {\x09\x22id\x22:2, \x0a\x09\x09\x09\x09\x09\x22titre\x22:\x22Tintin et Milou\x22, \x0a\x09\x09\x09\x09\x09\x22description\x22:\x22The real story of Tintin\x22,\x0a\x09\x09\x09\x09\x09\x22width\x22: 400,\x0a\x09\x09\x09\x09\x09\x22height\x22: 300,\x0a\x09\x09\x09\x09\x09\x22download_url\x22: \x22http://localhost/pdf/2\x22,\x0a\x09\x09\x09\x09\x09\x22ressources\x22:[ \x0a                                                          \x09\x09\x09\x09{\x09\x22id\x22:12,\x0a                                                          \x09\x09\x09\x09\x09\x22titre\x22: \x22Origins\x22,\x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x22foliono\x22: \x2212R\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22link_to\x22:\x22\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22description\x22:\x22Created in 1929 by Herge\x22 ,\x0a                                                          \x09\x09\x09\x09\x09\x22thumbnail\x22:\x22userfiles/album/2/thumbs/media/1.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22navigator_thumbnail\x22:\x22userfiles/album/2/thumbs/media/1_small.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22original\x22:\x22bib-numerique/get-resource/id/1.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22download\x22:\x22bib-numerique/download-resource/id/1\x22},\x0a                                                          \x0a                                                          \x09\x09\x09\x09{\x09\x22id\x22:13,\x0a                                                          \x09\x09\x09\x09\x09\x22titre\x22: \x22Haddock\x22,\x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x22foliono\x22: \x22XX\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22link_to\x22:\x22\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22description\x22:\x22Captain living in Moulinsard\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22thumbnail\x22:\x22userfiles/album/2/thumbs/media/2.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22navigator_thumbnail\x22:\x22userfiles/album/2/thumbs/media/2_small.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22original\x22:\x22bib-numerique/get-resource/id/2.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22download\x22:\x22bib-numerique/download-resource/id/2\x22 }                                                        \x0a                                                          \x09\x09\x09\x09]\x0a\x09\x09\x09\x09} }'.",
messageSends: [],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinBookletTest);



smalltalk.addClass('BibNumAlbumTintinMonopageTest', smalltalk.BibNumAlbumTintinTestCase, [], 'AFI-Tests');
smalltalk.addMethod(
"_testBookMenuShouldContainsLIWithHaddock",
smalltalk.method({
selector: "testBookMenuShouldContainsLIWithHaddock",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_", [smalltalk.send(0, "__lt", [smalltalk.send(smalltalk.send(self['@container'], "_find_", [".book-menu ul li:contains(\"Haddock\")"]), "_length", [])])]);
    return self;
},
args: [],
source: "testBookMenuShouldContainsLIWithHaddock\x0a\x09self assert: 0 < (container find: '.book-menu ul li:contains(\x22Haddock\x22)')  length",
messageSends: ["assert:", "<", "length", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinMonopageTest);

smalltalk.addMethod(
"_testBookMenuShouldContainsLIWithOrigins",
smalltalk.method({
selector: "testBookMenuShouldContainsLIWithOrigins",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_", [smalltalk.send(0, "__lt", [smalltalk.send(smalltalk.send(self['@container'], "_find_", [".book-menu li:contains(\"Origins\")"]), "_length", [])])]);
    return self;
},
args: [],
source: "testBookMenuShouldContainsLIWithOrigins\x0a\x09self assert: 0 < (container find: '.book-menu li:contains(\x22Origins\x22)')  length",
messageSends: ["assert:", "<", "length", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinMonopageTest);

smalltalk.addMethod(
"_testBookletShouldNotBeLoaded",
smalltalk.method({
selector: "testBookletShouldNotBeLoaded",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [0, smalltalk.send(smalltalk.send(self['@container'], "_find_", [".b-load"]), "_length", [])]);
    return self;
},
args: [],
source: "testBookletShouldNotBeLoaded\x0a\x09self assert: 0 equals: ( container find: '.b-load') length\x0a\x09",
messageSends: ["assert:equals:", "length", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinMonopageTest);

smalltalk.addMethod(
"_testFirstPageImageShouldLinkToOneDotJpg",
smalltalk.method({
selector: "testFirstPageImageShouldLinkToOneDotJpg",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_", [smalltalk.send(0, "__lt", [smalltalk.send(smalltalk.send(self['@container'], "_find_", ["img[src*=\"userfiles/album/2/thumbs/media/1.jpg\"]"]), "_length", [])])]);
    return self;
},
args: [],
source: "testFirstPageImageShouldLinkToOneDotJpg\x0a\x09self assert: 0 < (container find: 'img[src*=\x22userfiles/album/2/thumbs/media/1.jpg\x22]')  length",
messageSends: ["assert:", "<", "length", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinMonopageTest);

smalltalk.addMethod(
"_tintinJSON",
smalltalk.method({
selector: "tintinJSON",
category: 'json',
fn: function () {
    var self = this;
    return "{\"album\": {\t\"id\":2, \n\t\t\t\t\t\"titre\":\"Tintin et Milou\", \n\t\t\t\t\t\"description\":\"The real story of Tintin\",\n\t\t\t\t\t\"width\": 400,\n\t\t\t\t\t\"height\": 300,\n\t\t\t\t\t\"download_url\": \"http://localhost/pdf/2\",\n                    \"player\": \"BookMonoWidget\",\n\t\t\t\t\t\"ressources\":[ \n                                                          \t\t\t\t{\t\"id\":12,\n                                                          \t\t\t\t\t\"titre\": \"Origins\",\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\"foliono\": \"12R\",\n                                                          \t\t\t\t\t\"link_to\":\"\",\n                                                          \t\t\t\t\t\"description\":\"Created in 1929 by Herge\" ,\n                                                          \t\t\t\t\t\"thumbnail\":\"userfiles/album/2/thumbs/media/1.jpg\",\n                                                          \t\t\t\t\t\"navigator_thumbnail\":\"userfiles/album/2/thumbs/media/1_small.jpg\",\n                                                          \t\t\t\t\t\"original\":\"bib-numerique/get-resource/id/1.jpg\",\n                                                          \t\t\t\t\t\"download\":\"bib-numerique/download-resource/id/1\"},\n                                                          \n                                                          \t\t\t\t{\t\"id\":13,\n                                                          \t\t\t\t\t\"titre\": \"Haddock\",\n\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\t\"foliono\": \"XX\",\n                                                          \t\t\t\t\t\"link_to\":\"\",\n                                                          \t\t\t\t\t\"description\":\"Captain living in Moulinsard\",\n                                                          \t\t\t\t\t\"thumbnail\":\"userfiles/album/2/thumbs/media/2.jpg\",\n                                                          \t\t\t\t\t\"navigator_thumbnail\":\"userfiles/album/2/thumbs/media/2_small.jpg\",\n                                                          \t\t\t\t\t\"original\":\"bib-numerique/get-resource/id/2.jpg\",\n                                                          \t\t\t\t\t\"download\":\"bib-numerique/download-resource/id/2\" }                                                        \n                                                          \t\t\t\t]\n\t\t\t\t} }";
},
args: [],
source: "tintinJSON\x0a\x09^   '{\x22album\x22: {\x09\x22id\x22:2, \x0a\x09\x09\x09\x09\x09\x22titre\x22:\x22Tintin et Milou\x22, \x0a\x09\x09\x09\x09\x09\x22description\x22:\x22The real story of Tintin\x22,\x0a\x09\x09\x09\x09\x09\x22width\x22: 400,\x0a\x09\x09\x09\x09\x09\x22height\x22: 300,\x0a\x09\x09\x09\x09\x09\x22download_url\x22: \x22http://localhost/pdf/2\x22,\x0a                    \x22player\x22: \x22BookMonoWidget\x22,\x0a\x09\x09\x09\x09\x09\x22ressources\x22:[ \x0a                                                          \x09\x09\x09\x09{\x09\x22id\x22:12,\x0a                                                          \x09\x09\x09\x09\x09\x22titre\x22: \x22Origins\x22,\x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x22foliono\x22: \x2212R\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22link_to\x22:\x22\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22description\x22:\x22Created in 1929 by Herge\x22 ,\x0a                                                          \x09\x09\x09\x09\x09\x22thumbnail\x22:\x22userfiles/album/2/thumbs/media/1.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22navigator_thumbnail\x22:\x22userfiles/album/2/thumbs/media/1_small.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22original\x22:\x22bib-numerique/get-resource/id/1.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22download\x22:\x22bib-numerique/download-resource/id/1\x22},\x0a                                                          \x0a                                                          \x09\x09\x09\x09{\x09\x22id\x22:13,\x0a                                                          \x09\x09\x09\x09\x09\x22titre\x22: \x22Haddock\x22,\x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x22foliono\x22: \x22XX\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22link_to\x22:\x22\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22description\x22:\x22Captain living in Moulinsard\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22thumbnail\x22:\x22userfiles/album/2/thumbs/media/2.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22navigator_thumbnail\x22:\x22userfiles/album/2/thumbs/media/2_small.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22original\x22:\x22bib-numerique/get-resource/id/2.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22download\x22:\x22bib-numerique/download-resource/id/2\x22 }                                                        \x0a                                                          \x09\x09\x09\x09]\x0a\x09\x09\x09\x09} }'.",
messageSends: [],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinMonopageTest);



smalltalk.addClass('CycleTest', smalltalk.TestCase, [], 'AFI-Tests');
smalltalk.addMethod(
"_testCycleWithFourElements",
smalltalk.method({
selector: "testCycleWithFourElements",
category: 'tests',
fn: function () {
    var self = this;
    var cycle = nil;
    cycle = smalltalk.send(smalltalk.Cycle || Cycle, "_with_", [["one", "two", "three", "four"]]);
    smalltalk.send(3, "_timesRepeat_", [function () {smalltalk.send(self, "_assert_equals_", ["one", smalltalk.send(cycle, "_next", [])]);smalltalk.send(self, "_assert_equals_", ["two", smalltalk.send(cycle, "_next", [])]);smalltalk.send(self, "_assert_equals_", ["three", smalltalk.send(cycle, "_next", [])]);return smalltalk.send(self, "_assert_equals_", ["four", smalltalk.send(cycle, "_next", [])]);}]);
    return self;
},
args: [],
source: "testCycleWithFourElements\x0a\x09|cycle|\x0a\x09cycle := Cycle with: #('one' 'two' 'three' 'four').\x0a\x093 timesRepeat: [\x0a\x09\x09self assert: 'one' equals: cycle next.\x0a\x09\x09self assert: 'two' equals: cycle next.\x0a\x09\x09self assert: 'three' equals: cycle next.\x0a\x09\x09self assert: 'four' equals: cycle next ].\x0a\x09",
messageSends: ["with:", "timesRepeat:", "assert:equals:", "next"],
referencedClasses: ["Cycle"]
}),
smalltalk.CycleTest);

smalltalk.addMethod(
"_testCycleWithTwoElements",
smalltalk.method({
selector: "testCycleWithTwoElements",
category: 'tests',
fn: function () {
    var self = this;
    var cycle = nil;
    cycle = smalltalk.send(smalltalk.Cycle || Cycle, "_with_", [["one", "two"]]);
    smalltalk.send(self, "_assert_equals_", ["one", smalltalk.send(cycle, "_next", [])]);
    smalltalk.send(self, "_assert_equals_", ["two", smalltalk.send(cycle, "_next", [])]);
    smalltalk.send(self, "_assert_equals_", ["one", smalltalk.send(cycle, "_next", [])]);
    smalltalk.send(self, "_assert_equals_", ["two", smalltalk.send(cycle, "_next", [])]);
    return self;
},
args: [],
source: "testCycleWithTwoElements\x0a\x09|cycle|\x0a\x09cycle := Cycle with: #('one' 'two').\x0a\x09self assert: 'one' equals: cycle next.\x0a\x09self assert: 'two' equals: cycle next.\x0a\x09self assert: 'one' equals: cycle next.\x0a\x09self assert: 'two' equals: cycle next.\x0a\x09",
messageSends: ["with:", "assert:equals:", "next"],
referencedClasses: ["Cycle"]
}),
smalltalk.CycleTest);



smalltalk.addClass('SouvignyBibleTest', smalltalk.TestCase, ['bible'], 'AFI-Tests');
smalltalk.addMethod(
"_setUp",
smalltalk.method({
selector: "setUp",
category: 'running',
fn: function () {
    var self = this;
    self['@bible'] = smalltalk.send(smalltalk.SouvignyBible || SouvignyBible, "_new", []);
    return self;
},
args: [],
source: "setUp\x0a\x09bible := SouvignyBible new",
messageSends: ["new"],
referencedClasses: []
}),
smalltalk.SouvignyBibleTest);

smalltalk.addMethod(
"_testFolio150VShouldReturnPage306",
smalltalk.method({
selector: "testFolio150VShouldReturnPage306",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [306, smalltalk.send(self['@bible'], "_parseFolioNo_", ["150v"])]);
    return self;
},
args: [],
source: "testFolio150VShouldReturnPage306\x0a\x09self assert: 306 equals:(bible parseFolioNo: '150v').",
messageSends: ["assert:equals:", "parseFolioNo:"],
referencedClasses: []
}),
smalltalk.SouvignyBibleTest);

smalltalk.addMethod(
"_testFolio151RShouldReturnPage307",
smalltalk.method({
selector: "testFolio151RShouldReturnPage307",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [307, smalltalk.send(self['@bible'], "_parseFolioNo_", ["151r"])]);
    return self;
},
args: [],
source: "testFolio151RShouldReturnPage307\x0a\x09self assert: 307 equals:(bible parseFolioNo: '151r').",
messageSends: ["assert:equals:", "parseFolioNo:"],
referencedClasses: []
}),
smalltalk.SouvignyBibleTest);

smalltalk.addMethod(
"_testFolioOneShouldReturnPageOne",
smalltalk.method({
selector: "testFolioOneShouldReturnPageOne",
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [1, smalltalk.send(self['@bible'], "_parseFolioNo_", ["1"])]);
    return self;
},
args: [],
source: "testFolioOneShouldReturnPageOne\x0a\x09self assert: 1 equals: (bible parseFolioNo: '1')",
messageSends: ["assert:equals:", "parseFolioNo:"],
referencedClasses: []
}),
smalltalk.SouvignyBibleTest);



