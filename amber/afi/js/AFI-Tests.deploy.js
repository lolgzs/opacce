smalltalk.addPackage('AFI-Tests', {});
smalltalk.addClass('CycleTest', smalltalk.TestCase, [], 'AFI-Tests');
smalltalk.addMethod(
unescape('_testCycleWithTwoElements'),
smalltalk.method({
selector: unescape('testCycleWithTwoElements'),
fn: function (){
var self=this;
var cycle=nil;
(cycle=smalltalk.send((smalltalk.Cycle || Cycle), "_with_", [["one", "two"]]));
smalltalk.send(self, "_assert_equals_", ["one", smalltalk.send(cycle, "_next", [])]);
smalltalk.send(self, "_assert_equals_", ["two", smalltalk.send(cycle, "_next", [])]);
smalltalk.send(self, "_assert_equals_", ["one", smalltalk.send(cycle, "_next", [])]);
smalltalk.send(self, "_assert_equals_", ["two", smalltalk.send(cycle, "_next", [])]);
return self;}
}),
smalltalk.CycleTest);

smalltalk.addMethod(
unescape('_testCycleWithFourElements'),
smalltalk.method({
selector: unescape('testCycleWithFourElements'),
fn: function (){
var self=this;
var cycle=nil;
(cycle=smalltalk.send((smalltalk.Cycle || Cycle), "_with_", [["one", "two", "three", "four"]]));
smalltalk.send((3), "_timesRepeat_", [(function(){smalltalk.send(self, "_assert_equals_", ["one", smalltalk.send(cycle, "_next", [])]);smalltalk.send(self, "_assert_equals_", ["two", smalltalk.send(cycle, "_next", [])]);smalltalk.send(self, "_assert_equals_", ["three", smalltalk.send(cycle, "_next", [])]);return smalltalk.send(self, "_assert_equals_", ["four", smalltalk.send(cycle, "_next", [])]);})]);
return self;}
}),
smalltalk.CycleTest);



smalltalk.addClass('SouvignyBibleTest', smalltalk.TestCase, ['bible'], 'AFI-Tests');
smalltalk.addMethod(
unescape('_setUp'),
smalltalk.method({
selector: unescape('setUp'),
fn: function () {
    var self = this;
    self['@bible'] = smalltalk.send(smalltalk.SouvignyBible || SouvignyBible, "_new", []);
    return self;
}
}),
smalltalk.SouvignyBibleTest);

smalltalk.addMethod(
unescape('_testFolioOneShouldReturnPageOne'),
smalltalk.method({
selector: unescape('testFolioOneShouldReturnPageOne'),
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [1, smalltalk.send(self['@bible'], "_parseFolioNo_", ["1"])]);
    return self;
}
}),
smalltalk.SouvignyBibleTest);

smalltalk.addMethod(
unescape('_testFolio150VShouldReturnPage306'),
smalltalk.method({
selector: unescape('testFolio150VShouldReturnPage306'),
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [306, smalltalk.send(self['@bible'], "_parseFolioNo_", ["150v"])]);
    return self;
}
}),
smalltalk.SouvignyBibleTest);

smalltalk.addMethod(
unescape('_testFolio151RShouldReturnPage307'),
smalltalk.method({
selector: unescape('testFolio151RShouldReturnPage307'),
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [307, smalltalk.send(self['@bible'], "_parseFolioNo_", ["151r"])]);
    return self;
}
}),
smalltalk.SouvignyBibleTest);



smalltalk.addClass('BibNumAlbumTintinTest', smalltalk.TestCase, ['bible', 'album', 'container', 'ajax'], 'AFI-Tests');
smalltalk.addMethod(
unescape('_setUp'),
smalltalk.method({
selector: unescape('setUp'),
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(typeof window == "undefined" ? nil : window, "_location", []), "_hash_", [" "]);
    self['@ajax'] = smalltalk.send(smalltalk.AMockWrapper || AMockWrapper, "_on_", [smalltalk.send(smalltalk.Ajax || Ajax, "_new", [])]);
    smalltalk.send(self['@ajax'], "_onMessage_answer_", ["send", self['@ajax']]);
    self['@container'] = smalltalk.send(smalltalk.send(unescape("%3Cdiv%3E%3C/div%3E"), "_asJQuery", []), "_width_", [500]);
    self['@album'] = function ($rec) {smalltalk.send($rec, "_url_", [unescape("/bib-numerique/album/id/2.json")]);smalltalk.send($rec, "_scriptsRoot_", [unescape("http%3A//localhost/afi-opac3/amber/afi/souvigny/")]);smalltalk.send($rec, "_ajax_", [self['@ajax']]);smalltalk.send($rec, "_container_", [self['@container']]);return smalltalk.send($rec, "_load", []);}(smalltalk.send(smalltalk.BibNumAlbum || BibNumAlbum, "_new", []));
    smalltalk.send(smalltalk.send(smalltalk.send(self['@ajax'], "_options", []), "_at_", ["success"]), "_value_", [smalltalk.send(typeof jQuery == "undefined" ? nil : jQuery, "_parseJSON_", [smalltalk.send(self, "_tintinJSON", [])])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testDivBkWidgetShouldBePresent'),
smalltalk.method({
selector: unescape('testDivBkWidgetShouldBePresent'),
fn: function () {
    var self = this;
    var bkWidget = nil;
    bkWidget = smalltalk.send(self['@container'], "_children_", [unescape(".bk-widget")]);
    smalltalk.send(self, "_assert_", [0 < smalltalk.send(bkWidget, "_length", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testDivBookShouldBePresent'),
smalltalk.method({
selector: unescape('testDivBookShouldBePresent'),
fn: function () {
    var self = this;
    var book = nil;
    book = smalltalk.send(self['@container'], "_find_", [".book"]);
    smalltalk.send(self, "_assert_", [0 < smalltalk.send(book, "_length", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_tearDown'),
smalltalk.method({
selector: unescape('tearDown'),
fn: function () {
    var self = this;
    smalltalk.send(self['@container'], "_remove", []);
    return self;
}
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testBookletPluginShouldBeLoaded'),
smalltalk.method({
selector: unescape('testBookletPluginShouldBeLoaded'),
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_", [smalltalk.send(smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_at_", ["booklet"]), "_notNil", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testBookletJSShouldBeLoadedOnce'),
smalltalk.method({
selector: unescape('testBookletJSShouldBeLoadedOnce'),
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [1, smalltalk.send(smalltalk.send(smalltalk.send("head", "_asJQuery", []), "_find_", [unescape("script%5Bsrc*%3D%22booklet.1.2.0.min.js%22%5D")]), "_length", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_tintinJSON'),
smalltalk.method({
selector: unescape('tintinJSON'),
fn: function (){
var self=this;
return unescape("%7B%22album%22%3A%20%7B%09%22id%22%3A2%2C%20%0A%09%09%09%09%09%22titre%22%3A%22Tintin%20et%20Milou%22%2C%20%0A%09%09%09%09%09%22description%22%3A%22The%20real%20story%20of%20Tintin%22%2C%0A%09%09%09%09%09%22width%22%3A%20400%2C%0A%09%09%09%09%09%22height%22%3A%20300%2C%0A%09%09%09%09%09%22download_url%22%3A%20%22http%3A//localhost/pdf/2%22%2C%0A%09%09%09%09%09%22ressources%22%3A%5B%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%7B%09%22id%22%3A12%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22titre%22%3A%20%22Origins%22%2C%0A%09%09%09%09%09%09%09%09%09%09%09%09%22foliono%22%3A%20%2212R%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22link_to%22%3A%22%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22description%22%3A%22Created%20in%201929%20by%20Herge%22%20%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22thumbnail%22%3A%22userfiles/album/2/thumbs/media/1.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22navigator_thumbnail%22%3A%22userfiles/album/2/thumbs/media/1_small.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22original%22%3A%22bib-numerique/get-resource/id/1.jpg%22%7D%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%7B%09%22id%22%3A13%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22titre%22%3A%20%22Haddock%22%2C%0A%09%09%09%09%09%09%09%09%09%09%09%09%22foliono%22%3A%20%22XX%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22link_to%22%3A%22%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22description%22%3A%22Captain%20living%20in%20Moulinsard%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22thumbnail%22%3A%22userfiles/album/2/thumbs/media/2.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22navigator_thumbnail%22%3A%22userfiles/album/2/thumbs/media/2_small.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22original%22%3A%22bib-numerique/get-resource/id/2.jpg%22%20%7D%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%5D%0A%09%09%09%09%7D%20%7D");
return self;}
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testShouldContainsFourPages'),
smalltalk.method({
selector: unescape('testShouldContainsFourPages'),
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [4, smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-page")]), "_length", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testFirstPageImageShouldLinkToOneDotJpg'),
smalltalk.method({
selector: unescape('testFirstPageImageShouldLinkToOneDotJpg'),
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_", [0 < smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape("img%5Bsrc*%3D%22userfiles/album/2/thumbs/media/1.jpg%22%5D")]), "_length", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testPageDescShouldContainsCreatedIn1929ByHerge'),
smalltalk.method({
selector: unescape('testPageDescShouldContainsCreatedIn1929ByHerge'),
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", ["Created in 1929 by Herge", smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".page-desc")]), "_text", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testPageWidthShouldBe165'),
smalltalk.method({
selector: unescape('testPageWidthShouldBe165'),
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [165, smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-page")]), "_width", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testPageHeightShouldBe124'),
smalltalk.method({
selector: unescape('testPageHeightShouldBe124'),
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [124, smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-page")]), "_height", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testFirstPageZoomedImageShouldLinkToOneDotJpg'),
smalltalk.method({
selector: unescape('testFirstPageZoomedImageShouldLinkToOneDotJpg'),
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-zoom-magnify%20a")]), "_click", []);
    smalltalk.send(self, "_assert_", [0 < smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".iviewer%20img%5Bsrc*%3D%22bib-numerique/get-resource/id/1.jpg%22%5D")]), "_length", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testChapterSelectorShouldContainsLinkToFirstPage'),
smalltalk.method({
selector: unescape('testChapterSelectorShouldContainsLinkToFirstPage'),
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", ["Origins", smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-selector-chapter%20%23selector-page-1")]), "_text", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testChapterSelectorInitialHeightShouldBeZero'),
smalltalk.method({
selector: unescape('testChapterSelectorInitialHeightShouldBeZero'),
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [0, smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-selector-chapter%3Eul")]), "_height", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testFirstPageImageFolioNumberShouldBeTwelveR'),
smalltalk.method({
selector: unescape('testFirstPageImageFolioNumberShouldBeTwelveR'),
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", ["12R", smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-counter%20+%20.b-counter")]), "_text", [])]);
return self;}
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testSecondPageImageFolioNumberShouldBeXX'),
smalltalk.method({
selector: unescape('testSecondPageImageFolioNumberShouldBeXX'),
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", ["XX", smalltalk.send(smalltalk.send(smalltalk.send(self['@album'], "_pages", []), "_last", []), "_foliono", [])]);
return self;}
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testAnchorToDownloadPDFShouldBePresent'),
smalltalk.method({
selector: unescape('testAnchorToDownloadPDFShouldBePresent'),
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [unescape("http%3A//localhost/pdf/2"), smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape("div.b-download-book%20a")]), "_attr_", ["href"])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testNextPageShouldSetMenuTextToFin'),
smalltalk.method({
selector: unescape('testNextPageShouldSetMenuTextToFin'),
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(self['@container'], "_find_", [".book"]), "_booklet_", ["next"]);
smalltalk.send(self, "_assert_equals_", ["Fin", smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-current")]), "_text", [])]);
return self;}
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testFirstPageNavigatorThumbnailShouldBeOneSmallDotJpg'),
smalltalk.method({
selector: unescape('testFirstPageNavigatorThumbnailShouldBeOneSmallDotJpg'),
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", [unescape("userfiles/album/2/thumbs/media/1_small.jpg"), smalltalk.send(smalltalk.send(smalltalk.send(self['@album'], "_pages", []), "_first", []), "_navigatorThumbnailURL", [])]);
return self;}
}),
smalltalk.BibNumAlbumTintinTest);



