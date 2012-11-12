smalltalk.addPackage('AFI-Tests', {});
smalltalk.addClass('BibNumAlbumTestCase', smalltalk.TestCase, ['ajax', 'container', 'album'], 'AFI-Tests');
smalltalk.addMethod(
"_setUp",
smalltalk.method({
selector: "setUp",
fn: function (){
var self=this;
var $1,$2;
smalltalk.send(smalltalk.send(window,"_location",[]),"_hash_",[" "]);
self["@ajax"]=smalltalk.send((smalltalk.AMockWrapper || AMockWrapper),"_on_",[smalltalk.send((smalltalk.Ajax || Ajax),"_new",[])]);
smalltalk.send(self["@ajax"],"_onMessage_answer_",["send",self["@ajax"]]);
self["@container"]=smalltalk.send(smalltalk.send("<div></div>","_asJQuery",[]),"_width_",[(500)]);
$1=smalltalk.send((smalltalk.BibNumAlbum || BibNumAlbum),"_new",[]);
smalltalk.send($1,"_ajax_",[self["@ajax"]]);
$2=smalltalk.send($1,"_container_",[self["@container"]]);
self["@album"]=$2;
return self}
}),
smalltalk.BibNumAlbumTestCase);

smalltalk.addMethod(
"_tearDown",
smalltalk.method({
selector: "tearDown",
fn: function (){
var self=this;
smalltalk.send(self['@container'], "_remove", []);
return self;}
}),
smalltalk.BibNumAlbumTestCase);



smalltalk.addClass('BibNumAlbumDonjonTest', smalltalk.BibNumAlbumTestCase, ['ajax', 'container', 'album'], 'AFI-Tests');
smalltalk.addMethod(
"_donjonJSON",
smalltalk.method({
selector: "donjonJSON",
fn: function (){
var self=this;
return unescape("%7B%22album%22%3A%20%7B%09%22id%22%3A4%2C%20%0A%09%09%09%09%09%22titre%22%3A%22Donjon%20Zenith%22%2C%20%0A%09%09%09%09%09%22description%22%3A%22Une%20bonne%20bagarre%22%2C%0A%09%09%09%09%09%22width%22%3A%20200%2C%0A%09%09%09%09%09%22height%22%3A%2050%2C%0A%09%09%09%09%09%22ressources%22%3A%5B%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%7B%09%22id%22%3A1%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22titre%22%3A%20%22Donjon%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22link_to%22%3A%22%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22thumbnail%22%3A%22userfiles/album/4/thumbs/media/1.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22navigator_thumbnail%22%3A%22userfiles/album/4/thumbs/media/1_small.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22original%22%3A%22bib-numerique/get-resource/id/4.jpg%22%7D%0A%09%09%09%09%5D%0A%09%09%7D%20%7D");
return self;}
}),
smalltalk.BibNumAlbumDonjonTest);

smalltalk.addMethod(
"_setUp",
smalltalk.method({
selector: "setUp",
fn: function (){
var self=this;
smalltalk.send(self, "_setUp", [], smalltalk.BibNumAlbumTestCase);
(function($rec){smalltalk.send($rec, "_url_", ["donjon.json"]);return smalltalk.send($rec, "_load", []);})(self['@album']);
smalltalk.send(smalltalk.send(smalltalk.send(self['@ajax'], "_options", []), "_at_", ["success"]), "_value_", [smalltalk.send(self, "_donjonJSON", [])]);
return self;}
}),
smalltalk.BibNumAlbumDonjonTest);

smalltalk.addMethod(
"_testAnchorToDownloadPDFShouldNotBePresent",
smalltalk.method({
selector: "testAnchorToDownloadPDFShouldNotBePresent",
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", [(0), smalltalk.send(smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape("div.b-download-book%20a")]), "_at_", ["size"]), "_value", [])]);
return self;}
}),
smalltalk.BibNumAlbumDonjonTest);



smalltalk.addClass('BibNumAlbumTintinTestCase', smalltalk.BibNumAlbumTestCase, [], 'AFI-Tests');
smalltalk.addMethod(
"_setUp",
smalltalk.method({
selector: "setUp",
fn: function (){
var self=this;
var $1;
smalltalk.send(self,"_setUp",[],smalltalk.BibNumAlbumTestCase);
smalltalk.send(self["@album"],"_url_",["/bib-numerique/album/id/2.json"]);
smalltalk.send(self["@album"],"_scriptsRoot_",["http://localhost/afi-opac3/amber/afi/souvigny/"]);
$1=smalltalk.send(self["@album"],"_load",[]);
smalltalk.send(smalltalk.send(smalltalk.send(self["@ajax"],"_options",[]),"_at_",["success"]),"_value_",[smalltalk.send(jQuery,"_parseJSON_",[smalltalk.send(self,"_tintinJSON",[])])]);
return self}
}),
smalltalk.BibNumAlbumTintinTestCase);



smalltalk.addClass('BibNumAlbumTintinBookletTest', smalltalk.BibNumAlbumTintinTestCase, [], 'AFI-Tests');
smalltalk.addMethod(
"_testAnchorToDownloadPDFShouldBePresent",
smalltalk.method({
selector: "testAnchorToDownloadPDFShouldBePresent",
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [unescape("http%3A//localhost/pdf/2"), smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape("div.b-download-book%20a")]), "_attr_", ["href"])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testBookletPluginShouldBeLoaded",
smalltalk.method({
selector: "testBookletPluginShouldBeLoaded",
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_", [smalltalk.send(smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_at_", ["booklet"]), "_notNil", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testChapterSelectorInitialHeightShouldBeZero",
smalltalk.method({
selector: "testChapterSelectorInitialHeightShouldBeZero",
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [0, smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-selector-chapter%3Eul")]), "_height", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testChapterSelectorShouldContainsLinkToFirstPage",
smalltalk.method({
selector: "testChapterSelectorShouldContainsLinkToFirstPage",
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", ["Origins", smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-selector-chapter%20%23selector-page-1")]), "_text", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testDivBkWidgetShouldBePresent",
smalltalk.method({
selector: "testDivBkWidgetShouldBePresent",
fn: function () {
    var self = this;
    var bkWidget = nil;
    bkWidget = smalltalk.send(self['@container'], "_children_", [unescape(".bk-widget")]);
    smalltalk.send(self, "_assert_", [0 < smalltalk.send(bkWidget, "_length", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testDivBookShouldBePresent",
smalltalk.method({
selector: "testDivBookShouldBePresent",
fn: function () {
    var self = this;
    var book = nil;
    book = smalltalk.send(self['@container'], "_find_", [".book"]);
    smalltalk.send(self, "_assert_", [0 < smalltalk.send(book, "_length", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testFirstPageDownloadURLShouldLinkToDownloadResourceAction",
smalltalk.method({
selector: "testFirstPageDownloadURLShouldLinkToDownloadResourceAction",
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", [unescape("bib-numerique/download-resource/id/1"), smalltalk.send(smalltalk.send(smalltalk.send(self['@album'], "_pages", []), "_first", []), "_downloadURL", [])]);
return self;}
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testFirstPageImageFolioNumberShouldBeTwelveR",
smalltalk.method({
selector: "testFirstPageImageFolioNumberShouldBeTwelveR",
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", ["12R", smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-counter%20+%20.b-counter")]), "_text", [])]);
return self;}
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testFirstPageImageShouldLinkToOneDotJpg",
smalltalk.method({
selector: "testFirstPageImageShouldLinkToOneDotJpg",
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_", [0 < smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape("img%5Bsrc*%3D%22userfiles/album/2/thumbs/media/1.jpg%22%5D")]), "_length", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testFirstPageNavigatorThumbnailShouldBeOneSmallDotJpg",
smalltalk.method({
selector: "testFirstPageNavigatorThumbnailShouldBeOneSmallDotJpg",
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", [unescape("userfiles/album/2/thumbs/media/1_small.jpg"), smalltalk.send(smalltalk.send(smalltalk.send(self['@album'], "_pages", []), "_first", []), "_navigatorThumbnailURL", [])]);
return self;}
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testFirstPageZoomedImageShouldLinkToOneDotJpg",
smalltalk.method({
selector: "testFirstPageZoomedImageShouldLinkToOneDotJpg",
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-zoom-magnify%20a")]), "_click", []);
    smalltalk.send(self, "_assert_", [0 < smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".iviewer%20img%5Bsrc*%3D%22bib-numerique/get-resource/id/1.jpg%22%5D")]), "_length", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testNextPageShouldSetMenuTextToFin",
smalltalk.method({
selector: "testNextPageShouldSetMenuTextToFin",
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(self['@container'], "_find_", [".book"]), "_booklet_", ["next"]);
smalltalk.send(self, "_assert_equals_", ["Fin", smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-current")]), "_text", [])]);
return self;}
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testPageDescShouldContainsCreatedIn1929ByHerge",
smalltalk.method({
selector: "testPageDescShouldContainsCreatedIn1929ByHerge",
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", ["Created in 1929 by Herge", smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".page-desc")]), "_text", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testPageHeightShouldBe124",
smalltalk.method({
selector: "testPageHeightShouldBe124",
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [124, smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-page")]), "_height", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testPageWidthShouldBe165",
smalltalk.method({
selector: "testPageWidthShouldBe165",
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [165, smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-page")]), "_width", [])]);
    return self;
}
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testSecondPageImageFolioNumberShouldBeXX",
smalltalk.method({
selector: "testSecondPageImageFolioNumberShouldBeXX",
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", ["XX", smalltalk.send(smalltalk.send(smalltalk.send(self['@album'], "_pages", []), "_last", []), "_foliono", [])]);
return self;}
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_testShouldContainsFourPages",
smalltalk.method({
selector: "testShouldContainsFourPages",
fn: function (){
var self=this;
smalltalk.send(self,"_assert_equals_",[(4),smalltalk.send(smalltalk.send(self["@container"],"_find_",[".b-page"]),"_length",[])]);
return self}
}),
smalltalk.BibNumAlbumTintinBookletTest);

smalltalk.addMethod(
"_tintinJSON",
smalltalk.method({
selector: "tintinJSON",
fn: function (){
var self=this;
return unescape("%7B%22album%22%3A%20%7B%09%22id%22%3A2%2C%20%0A%09%09%09%09%09%22titre%22%3A%22Tintin%20et%20Milou%22%2C%20%0A%09%09%09%09%09%22description%22%3A%22The%20real%20story%20of%20Tintin%22%2C%0A%09%09%09%09%09%22width%22%3A%20400%2C%0A%09%09%09%09%09%22height%22%3A%20300%2C%0A%09%09%09%09%09%22download_url%22%3A%20%22http%3A//localhost/pdf/2%22%2C%0A%09%09%09%09%09%22ressources%22%3A%5B%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%7B%09%22id%22%3A12%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22titre%22%3A%20%22Origins%22%2C%0A%09%09%09%09%09%09%09%09%09%09%09%09%22foliono%22%3A%20%2212R%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22link_to%22%3A%22%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22description%22%3A%22Created%20in%201929%20by%20Herge%22%20%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22thumbnail%22%3A%22userfiles/album/2/thumbs/media/1.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22navigator_thumbnail%22%3A%22userfiles/album/2/thumbs/media/1_small.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22original%22%3A%22bib-numerique/get-resource/id/1.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22download%22%3A%22bib-numerique/download-resource/id/1%22%7D%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%7B%09%22id%22%3A13%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22titre%22%3A%20%22Haddock%22%2C%0A%09%09%09%09%09%09%09%09%09%09%09%09%22foliono%22%3A%20%22XX%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22link_to%22%3A%22%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22description%22%3A%22Captain%20living%20in%20Moulinsard%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22thumbnail%22%3A%22userfiles/album/2/thumbs/media/2.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22navigator_thumbnail%22%3A%22userfiles/album/2/thumbs/media/2_small.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22original%22%3A%22bib-numerique/get-resource/id/2.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22download%22%3A%22bib-numerique/download-resource/id/2%22%20%7D%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%5D%0A%09%09%09%09%7D%20%7D");
return self;}
}),
smalltalk.BibNumAlbumTintinBookletTest);



smalltalk.addClass('BibNumAlbumTintinMonopageTest', smalltalk.BibNumAlbumTintinTestCase, [], 'AFI-Tests');
smalltalk.addMethod(
"_testBookletShouldNotBeLoaded",
smalltalk.method({
selector: "testBookletShouldNotBeLoaded",
fn: function (){
var self=this;
smalltalk.send(self,"_assert_equals_",[(0),smalltalk.send(smalltalk.send(self["@container"],"_find_",[".b-load"]),"_length",[])]);
return self}
}),
smalltalk.BibNumAlbumTintinMonopageTest);

smalltalk.addMethod(
"_testFirstPageImageShouldLinkToOneDotJpg",
smalltalk.method({
selector: "testFirstPageImageShouldLinkToOneDotJpg",
fn: function (){
var self=this;
smalltalk.send(self,"_assert_",[smalltalk.send((0),"__lt",[smalltalk.send(smalltalk.send(self["@container"],"_find_",["img[src*=\x22userfiles/album/2/thumbs/media/1.jpg\x22]"]),"_length",[])])]);
return self}
}),
smalltalk.BibNumAlbumTintinMonopageTest);

smalltalk.addMethod(
"_tintinJSON",
smalltalk.method({
selector: "tintinJSON",
fn: function (){
var self=this;
return "{\x22album\x22: {\x09\x22id\x22:2, \x0a\x09\x09\x09\x09\x09\x22titre\x22:\x22Tintin et Milou\x22, \x0a\x09\x09\x09\x09\x09\x22description\x22:\x22The real story of Tintin\x22,\x0a\x09\x09\x09\x09\x09\x22width\x22: 400,\x0a\x09\x09\x09\x09\x09\x22height\x22: 300,\x0a\x09\x09\x09\x09\x09\x22download_url\x22: \x22http://localhost/pdf/2\x22,\x0a                    \x22player\x22: \x22BookMonoWidget\x22,\x0a\x09\x09\x09\x09\x09\x22ressources\x22:[ \x0a                                                          \x09\x09\x09\x09{\x09\x22id\x22:12,\x0a                                                          \x09\x09\x09\x09\x09\x22titre\x22: \x22Origins\x22,\x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x22foliono\x22: \x2212R\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22link_to\x22:\x22\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22description\x22:\x22Created in 1929 by Herge\x22 ,\x0a                                                          \x09\x09\x09\x09\x09\x22thumbnail\x22:\x22userfiles/album/2/thumbs/media/1.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22navigator_thumbnail\x22:\x22userfiles/album/2/thumbs/media/1_small.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22original\x22:\x22bib-numerique/get-resource/id/1.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22download\x22:\x22bib-numerique/download-resource/id/1\x22},\x0a                                                          \x0a                                                          \x09\x09\x09\x09{\x09\x22id\x22:13,\x0a                                                          \x09\x09\x09\x09\x09\x22titre\x22: \x22Haddock\x22,\x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x22foliono\x22: \x22XX\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22link_to\x22:\x22\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22description\x22:\x22Captain living in Moulinsard\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22thumbnail\x22:\x22userfiles/album/2/thumbs/media/2.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22navigator_thumbnail\x22:\x22userfiles/album/2/thumbs/media/2_small.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22original\x22:\x22bib-numerique/get-resource/id/2.jpg\x22,\x0a                                                          \x09\x09\x09\x09\x09\x22download\x22:\x22bib-numerique/download-resource/id/2\x22 }                                                        \x0a                                                          \x09\x09\x09\x09]\x0a\x09\x09\x09\x09} }";
}
}),
smalltalk.BibNumAlbumTintinMonopageTest);



smalltalk.addClass('CycleTest', smalltalk.TestCase, [], 'AFI-Tests');
smalltalk.addMethod(
"_testCycleWithFourElements",
smalltalk.method({
selector: "testCycleWithFourElements",
fn: function (){
var self=this;
var cycle=nil;
(cycle=smalltalk.send((smalltalk.Cycle || Cycle), "_with_", [["one", "two", "three", "four"]]));
smalltalk.send((3), "_timesRepeat_", [(function(){smalltalk.send(self, "_assert_equals_", ["one", smalltalk.send(cycle, "_next", [])]);smalltalk.send(self, "_assert_equals_", ["two", smalltalk.send(cycle, "_next", [])]);smalltalk.send(self, "_assert_equals_", ["three", smalltalk.send(cycle, "_next", [])]);return smalltalk.send(self, "_assert_equals_", ["four", smalltalk.send(cycle, "_next", [])]);})]);
return self;}
}),
smalltalk.CycleTest);

smalltalk.addMethod(
"_testCycleWithTwoElements",
smalltalk.method({
selector: "testCycleWithTwoElements",
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



smalltalk.addClass('SouvignyBibleTest', smalltalk.TestCase, ['bible'], 'AFI-Tests');
smalltalk.addMethod(
"_setUp",
smalltalk.method({
selector: "setUp",
fn: function () {
    var self = this;
    self['@bible'] = smalltalk.send(smalltalk.SouvignyBible || SouvignyBible, "_new", []);
    return self;
}
}),
smalltalk.SouvignyBibleTest);

smalltalk.addMethod(
"_testFolio150VShouldReturnPage306",
smalltalk.method({
selector: "testFolio150VShouldReturnPage306",
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [306, smalltalk.send(self['@bible'], "_parseFolioNo_", ["150v"])]);
    return self;
}
}),
smalltalk.SouvignyBibleTest);

smalltalk.addMethod(
"_testFolio151RShouldReturnPage307",
smalltalk.method({
selector: "testFolio151RShouldReturnPage307",
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [307, smalltalk.send(self['@bible'], "_parseFolioNo_", ["151r"])]);
    return self;
}
}),
smalltalk.SouvignyBibleTest);

smalltalk.addMethod(
"_testFolioOneShouldReturnPageOne",
smalltalk.method({
selector: "testFolioOneShouldReturnPageOne",
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [1, smalltalk.send(self['@bible'], "_parseFolioNo_", ["1"])]);
    return self;
}
}),
smalltalk.SouvignyBibleTest);



