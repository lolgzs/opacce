smalltalk.addPackage('AFI', {});
smalltalk.addClass('AbstractBookNavigatorWidget', smalltalk.Widget, ['book', 'announcer'], 'AFI');
smalltalk.addMethod(
"_announcePageChange_",
smalltalk.method({
selector: "announcePageChange:",
fn: function (aPage){
var self=this;
smalltalk.send(smalltalk.send(self, "_announcer", []), "_announce_", [smalltalk.send((smalltalk.PageChangeAnnouncement || PageChangeAnnouncement), "_page_", [aPage])]);
return self;}
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_announcer",
smalltalk.method({
selector: "announcer",
fn: function (){
var self=this;
return (($receiver = self['@announcer']) == nil || $receiver == undefined) ? (function(){return (self['@announcer']=smalltalk.send((smalltalk.Announcer || Announcer), "_new", []));})() : $receiver;
return self;}
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_book_",
smalltalk.method({
selector: "book:",
fn: function (aBook){
var self=this;
(self['@book']=aBook);
return self;}
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_highlightPage_",
smalltalk.method({
selector: "highlightPage:",
fn: function (aPage){
var self=this;

return self;}
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_onPageChangeDo_",
smalltalk.method({
selector: "onPageChangeDo:",
fn: function (aBlockWithArg){
var self=this;
smalltalk.send(smalltalk.send(self, "_announcer", []), "_on_do_", [(smalltalk.PageChangeAnnouncement || PageChangeAnnouncement), (function(aPageChangeAnnouncement){return smalltalk.send(aBlockWithArg, "_value_", [smalltalk.send(aPageChangeAnnouncement, "_page", [])]);})]);
return self;}
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_renderOn_",
smalltalk.method({
selector: "renderOn:",
fn: function (html){
var self=this;
smalltalk.send(self, "_subclassResponsibility", []);
return self;}
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_style",
smalltalk.method({
selector: "style",
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(unescape("%0A%0A.b-navigator%20%7B%0A%09height%3A%20500px%3B%0A%20%09width%3A%20"), "__comma", [smalltalk.send(self, "_width", [])]), "__comma", [unescape("px%3B%0A%09overflow-x%3A%20hidden%3B%0A%09overflow-y%3A%20hidden%3B%0A%09border%3A%202px%20solid%20%23666%3B%0A%7D%0A%0A.b-navigator%3Ediv%20%7B%0A%09text-align%3A%20center%3B%0A%09border-bottom%3A%201px%20solid%20%23666%3B%0A%09background-color%3A%20%23666%3B%0A%09font-size%3A%201.1em%3B%0A%7D%0A%0A.b-navigator%3Einput%20%7B%0A%09width%3A%20100%25%3B%0A%09border%3A%201px%20solid%20%23666%3B%0A%09margin%3A%200px%3B%0A%7D%0A%0A.b-navigator%20ul%20%7B%0A%09height%3A%2090%25%3B%0A%09overflow-x%3A%20hidden%3B%0A%09overflow-y%3A%20auto%3B%0A%09margin%3A%200px%3B%0A%7D%0A")]);
return self;}
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_width",
smalltalk.method({
selector: "width",
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(self, "_class", []), "_width", []);
return self;}
}),
smalltalk.AbstractBookNavigatorWidget);


smalltalk.addMethod(
"_width",
smalltalk.method({
selector: "width",
fn: function (){
var self=this;
return (160);
return self;}
}),
smalltalk.AbstractBookNavigatorWidget.klass);


smalltalk.addClass('BookBookmarkNavigatorWidget', smalltalk.AbstractBookNavigatorWidget, ['bookmarkList'], 'AFI');
smalltalk.addMethod(
"_highlightPage_",
smalltalk.method({
selector: "highlightPage:",
fn: function (aPage){
var self=this;
var pageTitle=nil;
var listItemIndex=nil;
smalltalk.send(smalltalk.send(self['@bookmarkList'], "_find_", ["li"]), "_removeClass_", ["selected"]);
(pageTitle=smalltalk.send(smalltalk.send(aPage, "_title", []), "_ifEmpty_", [(function(){return smalltalk.send(smalltalk.send(self['@book'], "_pageAt_ifAbsent_", [((($receiver = smalltalk.send(aPage, "_pageNo", [])).klass === smalltalk.Number) ? $receiver -(1) : smalltalk.send($receiver, "__minus", [(1)])), (function(){return aPage;})]), "_title", []);})]));
((($receiver = smalltalk.send(pageTitle, "_isEmpty", [])).klass === smalltalk.Boolean) ? (! $receiver ? (function(){return smalltalk.send(smalltalk.send(self['@bookmarkList'], "_find_", [smalltalk.send(smalltalk.send(unescape("li%3Acontains%28%22"), "__comma", [pageTitle]), "__comma", [unescape("%22%29")])]), "_addClass_", ["selected"]);})() : nil) : smalltalk.send($receiver, "_ifFalse_", [(function(){return smalltalk.send(smalltalk.send(self['@bookmarkList'], "_find_", [smalltalk.send(smalltalk.send(unescape("li%3Acontains%28%22"), "__comma", [pageTitle]), "__comma", [unescape("%22%29")])]), "_addClass_", ["selected"]);})]));
return self;}
}),
smalltalk.BookBookmarkNavigatorWidget);

smalltalk.addMethod(
"_renderOn_",
smalltalk.method({
selector: "renderOn:",
fn: function (html){
var self=this;
smalltalk.send(html, "_style_", [smalltalk.send(self, "_style", [])]);
(function($rec){smalltalk.send($rec, "_class_", [unescape("b-navigator-bookmark%20b-navigator")]);return smalltalk.send($rec, "_with_", [(function(){var bookmarkSearchInput=nil;
smalltalk.send(html, "_div_", ["Signets"]);(bookmarkSearchInput=smalltalk.send(smalltalk.send(html, "_input", []), "_asJQuery", []));(self['@bookmarkList']=smalltalk.send(smalltalk.send(html, "_ul_", [(function(){return smalltalk.send(self, "_renderPagesOn_", [html]);})]), "_asJQuery", []));return smalltalk.send((smalltalk.ListFilter || ListFilter), "_filter_withInput_", [self['@bookmarkList'], bookmarkSearchInput]);})]);})(smalltalk.send(html, "_div", []));
return self;}
}),
smalltalk.BookBookmarkNavigatorWidget);

smalltalk.addMethod(
"_renderPagesOn_",
smalltalk.method({
selector: "renderPagesOn:",
fn: function (html){
var self=this;
smalltalk.send(smalltalk.send(self['@book'], "_pagesWithTitle", []), "_do_", [(function(aPage){return (function($rec){smalltalk.send($rec, "_with_", [smalltalk.send(aPage, "_title", [])]);return smalltalk.send($rec, "_onClick_", [(function(){return smalltalk.send(self, "_announcePageChange_", [aPage]);})]);})(smalltalk.send(html, "_li", []));})]);
return self;}
}),
smalltalk.BookBookmarkNavigatorWidget);

smalltalk.addMethod(
"_style",
smalltalk.method({
selector: "style",
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(self, "_style", [], smalltalk.AbstractBookNavigatorWidget), "__comma", [unescape("%0A.b-navigator-bookmark%20%7B%0A%09border-top-right-radius%3A%2010px%3B%0A%09border-bottom-right-radius%3A%2010px%3B%0A%09border-left%3A%200px%3B%0A%09margin-left%3A%200px%3B%0A%09margin-right%3A%2010px%3B%0A%09float%3A%20left%3B%0A%7D%0A%0A.b-navigator-bookmark%20ul%20%7B%0A%09list-style%3A%20square%3B%0A%09padding%3A%200px%2010px%200px%2015px%3B%0A%7D%0A%0A.b-navigator-bookmark%20li%20%7B%0A%09margin%3A%205px%3B%0A%09padding%3A%200px%3B%0A%09text-align%3A%20left%3B%0A%09cursor%3A%20pointer%3B%0A%09-webkit-transition%3A%20all%200.3s%3B%0A%09-moz-transition%3A%20all%200.3s%3B%0A%7D%0A%0A.b-navigator-bookmark%20li.selected%20%7B%0A%09text-decoration%3A%20underline%0A%7D%0A%0A.b-navigator-bookmark%20li%3Ahover%20%7B%0A%09color%3A%20%23aaa%3B%0A%7D")]);
return self;}
}),
smalltalk.BookBookmarkNavigatorWidget);



smalltalk.addClass('BookThumbnailNavigatorWidget', smalltalk.AbstractBookNavigatorWidget, ['bookmarkList'], 'AFI');
smalltalk.addMethod(
"_highlightPage_",
smalltalk.method({
selector: "highlightPage:",
fn: function (aPage){
var self=this;
var thumbnail=nil;
var listItemIndex=nil;
(listItemIndex=smalltalk.send((0), "_max_", [((($receiver = smalltalk.send(aPage, "_pageNo", [])).klass === smalltalk.Number) ? $receiver -(2) : smalltalk.send($receiver, "__minus", [(2)]))]));
(thumbnail=smalltalk.send(smalltalk.send(self['@bookmarkList'], "_find_", ["li"]), "_get_", [listItemIndex]));
smalltalk.send(self['@bookmarkList'], "_scrollTop_", [((($receiver = smalltalk.send(thumbnail, "_offsetTop", [])).klass === smalltalk.Number) ? $receiver -((($receiver = smalltalk.send(self['@bookmarkList'], "_height", [])).klass === smalltalk.Number) ? $receiver /(2) : smalltalk.send($receiver, "__slash", [(2)])) : smalltalk.send($receiver, "__minus", [((($receiver = smalltalk.send(self['@bookmarkList'], "_height", [])).klass === smalltalk.Number) ? $receiver /(2) : smalltalk.send($receiver, "__slash", [(2)]))]))]);
smalltalk.send(smalltalk.send(self['@bookmarkList'], "_find_", ["li"]), "_removeClass_", ["selected"]);
smalltalk.send(smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery_", [thumbnail]), "_addClass_", ["selected"]);
return self;}
}),
smalltalk.BookThumbnailNavigatorWidget);

smalltalk.addMethod(
"_renderOn_",
smalltalk.method({
selector: "renderOn:",
fn: function (html){
var self=this;
smalltalk.send(html, "_style_", [smalltalk.send(self, "_style", [])]);
(function($rec){smalltalk.send($rec, "_class_", [unescape("b-navigator-thumbnail%20%20b-navigator")]);return smalltalk.send($rec, "_with_", [(function(){var bookmarkSearchInput=nil;
smalltalk.send(html, "_div_", ["Folios"]);(bookmarkSearchInput=smalltalk.send(smalltalk.send(html, "_input", []), "_asJQuery", []));(self['@bookmarkList']=(function($rec){smalltalk.send($rec, "_with_", [(function(){return smalltalk.send(self, "_renderPagesOn_", [html]);})]);return smalltalk.send($rec, "_asJQuery", []);})(smalltalk.send(html, "_ul", [])));return smalltalk.send((smalltalk.ListFilter || ListFilter), "_filter_withInput_", [self['@bookmarkList'], bookmarkSearchInput]);})]);})(smalltalk.send(html, "_div", []));
return self;}
}),
smalltalk.BookThumbnailNavigatorWidget);

smalltalk.addMethod(
"_renderPagesOn_",
smalltalk.method({
selector: "renderPagesOn:",
fn: function (html){
var self=this;
var cycle=nil;
(cycle=smalltalk.send((smalltalk.Cycle || Cycle), "_with_", [["odd", "even"]]));
smalltalk.send(smalltalk.send(self['@book'], "_pages", []), "_do_", [(function(aPage){return (function($rec){smalltalk.send($rec, "_class_", [smalltalk.send(cycle, "_next", [])]);smalltalk.send($rec, "_with_", [(function(){return smalltalk.send(html, "_div_", [(function(){smalltalk.send(html, "_div_", [smalltalk.send(aPage, "_foliono", [])]);return smalltalk.send(smalltalk.send(html, "_img", []), "_src_", [smalltalk.send(aPage, "_navigatorThumbnailURL", [])]);})]);})]);return smalltalk.send($rec, "_onClick_", [(function(){return smalltalk.send(self, "_announcePageChange_", [aPage]);})]);})(smalltalk.send(html, "_li", []));})]);
return self;}
}),
smalltalk.BookThumbnailNavigatorWidget);

smalltalk.addMethod(
"_style",
smalltalk.method({
selector: "style",
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(self, "_style", [], smalltalk.AbstractBookNavigatorWidget), "__comma", [unescape("%0A.b-navigator-thumbnail%20%7B%0A%09border-top-left-radius%3A%2010px%3B%0A%09border-bottom-left-radius%3A%2010px%3B%0A%09border-right%3A%200px%3B%0A%09margin-left%3A%2010px%3B%0A%09margin-right%3A%200px%3B%0A%09float%3A%20right%3B%0A%7D%0A%0A.b-navigator-thumbnail%20ul%20%7B%0A%09list-style%3A%20none%3B%0A%09padding%3A%200px%3B%0A%09float%3A%20right%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%20%7B%0A%09float%3A%20left%3B%0A%09margin%3A%205px%3B%0A%09display%3A%20block%3B%0A%09overflow%3A%20hidden%3B%0A%09height%3A%2070px%3B%0A%09width%3A%2050px%3B%0A%09text-align%3A%20center%3B%0A%09cursor%3A%20pointer%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ediv%3Ediv%7B%0A%09display%3A%20none%3B%0A%09position%3A%20relative%3B%0A%09z-index%3A%202%3B%0A%09background-color%3A%20black%3B%0A%09font-weight%3A%20bold%3B%0A%09font-size%3A%200.9em%3B%0A%7D%0A%0A%0A.b-navigator-thumbnail%20li.selected%20img%2C%0A.b-navigator-thumbnail%20li.selected%20+%20li.odd%20img%2C%0A.b-navigator-thumbnail%20.filtered%20li%20img%2C%0A.b-navigator-thumbnail%20li%3Ahover%20img%20%7B%0A%09opacity%3A%201%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ahover%20%7B%0A%09overflow%3A%20visible%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ahover%3Ediv%3Ediv%7B%0A%09display%3A%20block%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ediv%20%7B%0A%09width%3A%2050px%3B%0A%20%20%09-webkit-transition%3A%20all%200.1s%20ease-out%3B%0A%20%09-moz-transition%3A%20all%200.1s%20ease-out%3B%0A%7D%0A%0A%0A.b-navigator-thumbnail%20li%3Ahover%3Ediv%20%7B%0A%20%20%20width%3A%20100px%3B%0A%20%20%20position%3A%20relative%3B%0A%20%20%20box-shadow%3A%200px%200px%2020px%20black%3B%0A%20%20%20z-index%3A%2030%3B%0A%7D%0A%0A%0A.b-navigator-thumbnail%20li.odd%3Ahover%3Ediv%20%7B%0A%20%20%20margin-left%3A%20-40px%3B%0A%7D%0A%0A.b-navigator-thumbnail%20ul%20li%3Afirst-child%3Ahover%3Ediv%20%7B%0A%20%20%20margin-right%3A%20-40px%3B%0A%20%20%20margin-left%3A%200px%3B%0A%20%20%20margin-top%3A%200px%3B%0A%7D%0A%0A.b-navigator-thumbnail%20.filtered%20li.odd%3Ahover%3Ediv%20%7B%0A%20%20%20margin-left%3A%200px%3B%0A%7D%0A%0A.b-navigator-thumbnail%20.filtered%20li%20%7B%0A%20%20%20width%3A%20100%25%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%20img%20%7B%0A%20%20%09width%3A%20100%25%3B%0A%09display%3A%20block%3B%0A%09opacity%3A%200.6%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Afirst-child%20+%20li%7B%0A%09clear%3A%20left%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%20img%20%7B%0A%09cursor%3A%20pointer%3B%0A%7D%0A")]);
return self;}
}),
smalltalk.BookThumbnailNavigatorWidget);



smalltalk.addClass('BibNumAlbum', smalltalk.Object, ['container', 'ajax', 'url', 'scriptsRoot', 'bookWidget'], 'AFI');
smalltalk.addMethod(
"_ajax",
smalltalk.method({
selector: "ajax",
fn: function (){
var self=this;
return (($receiver = self['@ajax']) == nil || $receiver == undefined) ? (function(){return self['@ajax']=smalltalk.send((smalltalk.Ajax || Ajax), "_url_", [smalltalk.send(self, "_url", [])]);})() : $receiver;
return self;}
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_ajax_",
smalltalk.method({
selector: "ajax:",
fn: function (anAjax){
var self=this;
self['@ajax']=anAjax;
return self;}
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_container",
smalltalk.method({
selector: "container",
fn: function (){
var self=this;
return self['@container'];
return self;}
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_container_",
smalltalk.method({
selector: "container:",
fn: function (aJQuery){
var self=this;
self['@container']=aJQuery;
return self;}
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_load",
smalltalk.method({
selector: "load",
fn: function (){
var self=this;
(self['@bookWidget']=(function($rec){smalltalk.send($rec, "_loader_", [(function($rec){smalltalk.send($rec, "_ajax_", [smalltalk.send(self, "_ajax", [])]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send((smalltalk.BibNumLoader || BibNumLoader), "_new", []))]);smalltalk.send($rec, "_scriptsRoot_", [smalltalk.send(self, "_scriptsRoot", [])]);return smalltalk.send($rec, "_appendToJQuery_", [smalltalk.send(self, "_container", [])]);})(smalltalk.send((smalltalk.BookWidget || BookWidget), "_new", [])));
return self;}
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_pages",
smalltalk.method({
selector: "pages",
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(self['@bookWidget'], "_book", []), "_pages", []);
return self;}
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_scriptsRoot",
smalltalk.method({
selector: "scriptsRoot",
fn: function (){
var self=this;
return (($receiver = self['@scriptsRoot']) == nil || $receiver == undefined) ? (function(){return self['@scriptsRoot']="";})() : $receiver;
return self;}
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_scriptsRoot_",
smalltalk.method({
selector: "scriptsRoot:",
fn: function (anUrl){
var self=this;
self['@scriptsRoot']=anUrl;
return self;}
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_url",
smalltalk.method({
selector: "url",
fn: function (){
var self=this;
return self['@url'];
return self;}
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_url_",
smalltalk.method({
selector: "url:",
fn: function (aString){
var self=this;
self['@url']=aString;
return self;}
}),
smalltalk.BibNumAlbum);


smalltalk.addMethod(
"_load_in_",
smalltalk.method({
selector: "load:in:",
fn: function (anURLForJSON, aJQuerySelector){
var self=this;
return (function($rec){smalltalk.send($rec, "_url_", [anURLForJSON]);smalltalk.send($rec, "_container_", [smalltalk.send(aJQuerySelector, "_asJQuery", [])]);return smalltalk.send($rec, "_load", []);})(smalltalk.send(self, "_new", []));
return self;}
}),
smalltalk.BibNumAlbum.klass);

smalltalk.addMethod(
"_load_in_scriptsRoot_",
smalltalk.method({
selector: "load:in:scriptsRoot:",
fn: function (anURLForJSON, aJQuerySelector, anURL){
var self=this;
return (function($rec){smalltalk.send($rec, "_url_", [anURLForJSON]);smalltalk.send($rec, "_container_", [smalltalk.send(aJQuerySelector, "_asJQuery", [])]);smalltalk.send($rec, "_scriptsRoot_", [anURL]);return smalltalk.send($rec, "_load", []);})(smalltalk.send(self, "_new", []));
return self;}
}),
smalltalk.BibNumAlbum.klass);


smalltalk.addClass('BibNumLoader', smalltalk.Object, ['ajax'], 'AFI');
smalltalk.addMethod(
"_abort",
smalltalk.method({
selector: "abort",
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(self, "_ajax", []), "_abort", []);
return self;}
}),
smalltalk.BibNumLoader);

smalltalk.addMethod(
"_ajax",
smalltalk.method({
selector: "ajax",
fn: function (){
var self=this;
return (($receiver = self['@ajax']) == nil || $receiver == undefined) ? (function(){return self['@ajax']=smalltalk.send((smalltalk.Ajax || Ajax), "_new", []);})() : $receiver;
return self;}
}),
smalltalk.BibNumLoader);

smalltalk.addMethod(
"_ajax_",
smalltalk.method({
selector: "ajax:",
fn: function (anAjax){
var self=this;
self['@ajax']=anAjax;
return self;}
}),
smalltalk.BibNumLoader);

smalltalk.addMethod(
"_buildBookFromJSon_",
smalltalk.method({
selector: "buildBookFromJSon:",
fn: function (aJSONObjectOrString){
var self=this;
var book=nil;
var album=nil;
(album=smalltalk.send(((($receiver = smalltalk.send(aJSONObjectOrString, "_isString", [])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return smalltalk.send(smalltalk.send((typeof window == 'undefined' ? nil : window), "_JSON", []), "_parse_", [aJSONObjectOrString]);})() : (function(){return aJSONObjectOrString;})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return smalltalk.send(smalltalk.send((typeof window == 'undefined' ? nil : window), "_JSON", []), "_parse_", [aJSONObjectOrString]);}), (function(){return aJSONObjectOrString;})])), "_album", []));
(book=(function($rec){smalltalk.send($rec, "_title_", [smalltalk.send(album, "_at_", ["titre"])]);smalltalk.send($rec, "_width_", [smalltalk.send(album, "_at_", ["width"])]);smalltalk.send($rec, "_height_", [smalltalk.send(album, "_at_", ["height"])]);smalltalk.send($rec, "_downloadUrl_", [smalltalk.send(album, "_at_", ["download_url"])]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send((smalltalk.Book || Book), "_new", [])));
smalltalk.send(smalltalk.send(album, "_ressources", []), "_do_", [(function(aRessource){return (function($rec){smalltalk.send($rec, "_title_", [smalltalk.send(aRessource, "_at_", ["titre"])]);smalltalk.send($rec, "_description_", [smalltalk.send(aRessource, "_at_", ["description"])]);smalltalk.send($rec, "_thumbnailURL_", [smalltalk.send(aRessource, "_at_", ["thumbnail"])]);smalltalk.send($rec, "_fullImageURL_", [smalltalk.send(aRessource, "_at_", ["original"])]);smalltalk.send($rec, "_foliono_", [smalltalk.send(aRessource, "_at_", ["foliono"])]);smalltalk.send($rec, "_navigatorThumbnailURL_", [smalltalk.send(aRessource, "_at_", ["navigator_thumbnail"])]);return smalltalk.send($rec, "_downloadURL_", [smalltalk.send(aRessource, "_at_", ["download"])]);})(smalltalk.send(book, "_newPage", []));})]);
return book;
return self;}
}),
smalltalk.BibNumLoader);

smalltalk.addMethod(
"_loadBookFromJSONOnSuccess_",
smalltalk.method({
selector: "loadBookFromJSONOnSuccess:",
fn: function (aBlock){
var self=this;
(function($rec){smalltalk.send($rec, "_onSuccessDo_", [(function(data){var book=nil;
(book=smalltalk.send(self, "_buildBookFromJSon_", [data]));return smalltalk.send(aBlock, "_value_", [book]);})]);return smalltalk.send($rec, "_send", []);})(smalltalk.send(self, "_ajax", []));
return self;}
}),
smalltalk.BibNumLoader);



smalltalk.addClass('SouvignyLoader', smalltalk.BibNumLoader, ['pages', 'links', 'book'], 'AFI');
smalltalk.addMethod(
"_baseURL",
smalltalk.method({
selector: "baseURL",
fn: function (){
var self=this;
return unescape("souvigny/B031906101_MS_001/");
return self;}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_book",
smalltalk.method({
selector: "book",
fn: function (){
var self=this;
return (($receiver = self['@book']) == nil || $receiver == undefined) ? (function(){return self['@book']=(function($rec){smalltalk.send($rec, "_width_", [(390)]);smalltalk.send($rec, "_height_", [(594)]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send(smalltalk.send(smalltalk.send(self, "_class", []), "_bookClass", []), "_new", []));})() : $receiver;
return self;}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_buildBookFromHTML_",
smalltalk.method({
selector: "buildBookFromHTML:",
fn: function (aHTMLString){
var self=this;
var anchors=nil;
anchors=smalltalk.send(smalltalk.send(aHTMLString, "_asJQuery", []), "_find_", [unescape("li%20a%5Bhref%24%3D%22jpg%22%5D")]);
smalltalk.send(anchors, "_each_", [(function(index, element){var fileName=nil;
fileName=smalltalk.send(smalltalk.send((smalltalk.JQuery || JQuery), "_fromElement_", [element]), "_attr_", ["href"]);return (function($rec){smalltalk.send($rec, "_fullImageURL_", [smalltalk.send(smalltalk.send(self, "_fullImagesURL", []), "__comma", [fileName])]);return smalltalk.send($rec, "_thumbnailURL_", [smalltalk.send(smalltalk.send(self, "_thumbsURL", []), "__comma", [fileName])]);})(smalltalk.send(smalltalk.send(self, "_book", []), "_newPage", []));})]);
return self;}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_buildBookFromJSON_",
smalltalk.method({
selector: "buildBookFromJSON:",
fn: function (anArray){
var self=this;
smalltalk.send(anArray, "_do_", [(function(fileName){return (function($rec){smalltalk.send($rec, "_fullImageURL_", [smalltalk.send(smalltalk.send(self, "_fullImagesURL", []), "__comma", [fileName])]);return smalltalk.send($rec, "_thumbnailURL_", [smalltalk.send(smalltalk.send(self, "_thumbsURL", []), "__comma", [fileName])]);})(smalltalk.send(smalltalk.send(self, "_book", []), "_newPage", []));})]);
return self;}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_fullImagesURL",
smalltalk.method({
selector: "fullImagesURL",
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(self, "_baseURL", []), "__comma", [unescape("big/")]);
return self;}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_initMetadata_",
smalltalk.method({
selector: "initMetadata:",
fn: function (anArray){
var self=this;
smalltalk.send(anArray, "_do_", [(function(aJSObject){var pageNo=nil;
var page=nil;
pageNo=aJSObject.pageNo;return (($receiver = pageNo) != nil && $receiver != undefined) ? (function(){page=smalltalk.send(smalltalk.send(self, "_book", []), "_pageAtFolio_", [pageNo]);return (($receiver = page) != nil && $receiver != undefined) ? (function(){return smalltalk.send(page, "_initMetadata_", [aJSObject]);})() : nil;})() : nil;})]);
return self;}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_links",
smalltalk.method({
selector: "links",
fn: function (){
var self=this;
return (($receiver = self['@links']) == nil || $receiver == undefined) ? (function(){return self['@links']=smalltalk.send((smalltalk.Dictionary || Dictionary), "_new", []);})() : $receiver;
return self;}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_loadBookFromJSONOnSuccess_",
smalltalk.method({
selector: "loadBookFromJSONOnSuccess:",
fn: function (aBlock){
var self=this;
(function($rec){smalltalk.send($rec, "_onSuccessDo_", [(function(data){smalltalk.send(self, "_buildBookFromJSON_", [data]);return smalltalk.send(self, "_onMetadataLoadedDo_", [(function(){return smalltalk.send(aBlock, "_value_", [smalltalk.send(self, "_book", [])]);})]);})]);return smalltalk.send($rec, "_send", []);})(smalltalk.send(smalltalk.send(self, "_ajax", []), "_url_", [smalltalk.send(self, "_thumbsJSONURL", [])]));
return self;}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_loadBookOnSuccess_",
smalltalk.method({
selector: "loadBookOnSuccess:",
fn: function (aBlock){
var self=this;
(function($rec){smalltalk.send($rec, "_onSuccessDo_", [(function(data){smalltalk.send(self, "_buildBookFromHTML_", [data]);return smalltalk.send(self, "_onMetadataLoadedDo_", [(function(){return smalltalk.send(aBlock, "_value_", [smalltalk.send(self, "_book", [])]);})]);})]);return smalltalk.send($rec, "_send", []);})(smalltalk.send((smalltalk.Ajax || Ajax), "_url_", [smalltalk.send(self, "_thumbsURL", [])]));
return self;}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_metadataURL",
smalltalk.method({
selector: "metadataURL",
fn: function (){
var self=this;
return unescape("souvigny/souvigny.json");
return self;}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_onMetadataLoadedDo_",
smalltalk.method({
selector: "onMetadataLoadedDo:",
fn: function (aBlock){
var self=this;
(function($rec){smalltalk.send($rec, "_onSuccessDo_", [(function(data){smalltalk.send(self, "_initMetadata_", [data]);return smalltalk.send(aBlock, "_value", []);})]);return smalltalk.send($rec, "_send", []);})(smalltalk.send((smalltalk.Ajax || Ajax), "_url_", [smalltalk.send(self, "_metadataURL", [])]));
return self;}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_parsePageNo_",
smalltalk.method({
selector: "parsePageNo:",
fn: function (aString){
var self=this;
return ((($receiver = smalltalk.send(aString, "_includesSubString_", ["r"])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]));})() : (function(){return ((($receiver = smalltalk.send(aString, "_includesSubString_", ["v"])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)]));})() : (function(){return aString;})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)]));}), (function(){return aString;})]));})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]));}), (function(){return ((($receiver = smalltalk.send(aString, "_includesSubString_", ["v"])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)]));})() : (function(){return aString;})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)]));}), (function(){return aString;})]));})]));
return self;}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_thumbsJSONURL",
smalltalk.method({
selector: "thumbsJSONURL",
fn: function (){
var self=this;
return unescape("souvigny/thumbs.json");
return self;}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_thumbsURL",
smalltalk.method({
selector: "thumbsURL",
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(self, "_baseURL", []), "__comma", [unescape("thumbs/")]);
return self;}
}),
smalltalk.SouvignyLoader);


smalltalk.addMethod(
"_bookClass",
smalltalk.method({
selector: "bookClass",
fn: function (){
var self=this;
return (smalltalk.SouvignyBible || SouvignyBible);
return self;}
}),
smalltalk.SouvignyLoader.klass);


smalltalk.addClass('Book', smalltalk.Object, ['pages', 'title', 'width', 'height', 'downloadUrl'], 'AFI');
smalltalk.addMethod(
"_addPage_",
smalltalk.method({
selector: "addPage:",
fn: function (aPage){
var self=this;
smalltalk.send(smalltalk.send(self, "_pages", []), "_add_", [aPage]);
smalltalk.send(aPage, "_book_", [self]);
return aPage;
return self;}
}),
smalltalk.Book);

smalltalk.addMethod(
"_downloadUrl",
smalltalk.method({
selector: "downloadUrl",
fn: function (){
var self=this;
return (($receiver = self['@downloadUrl']) == nil || $receiver == undefined) ? (function(){return (self['@downloadUrl']="");})() : $receiver;
return self;}
}),
smalltalk.Book);

smalltalk.addMethod(
"_downloadUrl_",
smalltalk.method({
selector: "downloadUrl:",
fn: function (anUrl){
var self=this;
(self['@downloadUrl']=anUrl);
return self;}
}),
smalltalk.Book);

smalltalk.addMethod(
"_height",
smalltalk.method({
selector: "height",
fn: function (){
var self=this;
return (($receiver = self['@height']) == nil || $receiver == undefined) ? (function(){return self['@height']=(400);})() : $receiver;
return self;}
}),
smalltalk.Book);

smalltalk.addMethod(
"_height_",
smalltalk.method({
selector: "height:",
fn: function (anInteger){
var self=this;
self['@height']=anInteger;
return self;}
}),
smalltalk.Book);

smalltalk.addMethod(
"_newPage",
smalltalk.method({
selector: "newPage",
fn: function (){
var self=this;
return smalltalk.send(self, "_addPage_", [smalltalk.send(smalltalk.send(smalltalk.send(self, "_class", []), "_pageClass", []), "_new", [])]);
return self;}
}),
smalltalk.Book);

smalltalk.addMethod(
"_pageAt_",
smalltalk.method({
selector: "pageAt:",
fn: function (aNumber){
var self=this;
return smalltalk.send(smalltalk.send(self, "_pages", []), "_at_", [aNumber]);
return self;}
}),
smalltalk.Book);

smalltalk.addMethod(
"_pageAt_do_",
smalltalk.method({
selector: "pageAt:do:",
fn: function (pageNo, aBlockWithArg){
var self=this;
var page=nil;
page=smalltalk.send(smalltalk.send(self, "_pages", []), "_at_ifAbsent_", [pageNo, (function(){return nil;})]);
(($receiver = page) != nil && $receiver != undefined) ? (function(){return smalltalk.send(aBlockWithArg, "_value_", [page]);})() : nil;
return self;}
}),
smalltalk.Book);

smalltalk.addMethod(
"_pageAt_ifAbsent_",
smalltalk.method({
selector: "pageAt:ifAbsent:",
fn: function (aNumber, aBlock){
var self=this;
return smalltalk.send(smalltalk.send(self, "_pages", []), "_at_ifAbsent_", [aNumber, aBlock]);
return self;}
}),
smalltalk.Book);

smalltalk.addMethod(
"_pageNo_",
smalltalk.method({
selector: "pageNo:",
fn: function (aPage){
var self=this;
return smalltalk.send(smalltalk.send(self, "_pages", []), "_indexOf_", [aPage]);
return self;}
}),
smalltalk.Book);

smalltalk.addMethod(
"_pages",
smalltalk.method({
selector: "pages",
fn: function (){
var self=this;
return (($receiver = self['@pages']) == nil || $receiver == undefined) ? (function(){return self['@pages']=smalltalk.send((smalltalk.Array || Array), "_new", []);})() : $receiver;
return self;}
}),
smalltalk.Book);

smalltalk.addMethod(
"_pagesNo_do_",
smalltalk.method({
selector: "pagesNo:do:",
fn: function (anArray, aBlockWithArg){
var self=this;
smalltalk.send(anArray, "_do_", [(function(pageNo){return smalltalk.send(self, "_pageAt_do_", [pageNo, aBlockWithArg]);})]);
return self;}
}),
smalltalk.Book);

smalltalk.addMethod(
"_pagesNo_to_do_",
smalltalk.method({
selector: "pagesNo:to:do:",
fn: function (start, end, aBlockWithArg){
var self=this;
smalltalk.send(start, "_to_do_", [end, (function(pageNo){return smalltalk.send(self, "_pageAt_do_", [pageNo, aBlockWithArg]);})]);
return self;}
}),
smalltalk.Book);

smalltalk.addMethod(
"_pagesWithTitle",
smalltalk.method({
selector: "pagesWithTitle",
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(self, "_pages", []), "_reject_", [(function(aPage){return smalltalk.send(smalltalk.send(aPage, "_title", []), "_isEmpty", []);})]);
return self;}
}),
smalltalk.Book);

smalltalk.addMethod(
"_reset",
smalltalk.method({
selector: "reset",
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(self, "_pages", []), "_do_", [(function(aPage){return smalltalk.send(aPage, "_reset", []);})]);
return self;}
}),
smalltalk.Book);

smalltalk.addMethod(
"_size",
smalltalk.method({
selector: "size",
fn: function (){
var self=this;
return smalltalk.send(self['@pages'], "_size", []);
return self;}
}),
smalltalk.Book);

smalltalk.addMethod(
"_title",
smalltalk.method({
selector: "title",
fn: function (){
var self=this;
return self['@title'];
return self;}
}),
smalltalk.Book);

smalltalk.addMethod(
"_title_",
smalltalk.method({
selector: "title:",
fn: function (aString){
var self=this;
self['@title']=aString;
return self;}
}),
smalltalk.Book);

smalltalk.addMethod(
"_width",
smalltalk.method({
selector: "width",
fn: function (){
var self=this;
return (($receiver = self['@width']) == nil || $receiver == undefined) ? (function(){return self['@width']=(300);})() : $receiver;
return self;}
}),
smalltalk.Book);

smalltalk.addMethod(
"_width_",
smalltalk.method({
selector: "width:",
fn: function (anInteger){
var self=this;
self['@width']=anInteger;
return self;}
}),
smalltalk.Book);


smalltalk.addMethod(
"_pageClass",
smalltalk.method({
selector: "pageClass",
fn: function (){
var self=this;
return (smalltalk.Page || Page);
return self;}
}),
smalltalk.Book.klass);


smalltalk.addClass('SouvignyBible', smalltalk.Book, [], 'AFI');
smalltalk.addMethod(
"_pageAtFolio_",
smalltalk.method({
selector: "pageAtFolio:",
fn: function (aString){
var self=this;
return smalltalk.send(smalltalk.send(self, "_pages", []), "_at_ifAbsent_", [smalltalk.send(self, "_parseFolioNo_", [aString]), (function(){return nil;})]);
return self;}
}),
smalltalk.SouvignyBible);

smalltalk.addMethod(
"_parseFolioNo_",
smalltalk.method({
selector: "parseFolioNo:",
fn: function (aString){
var self=this;
return ((($receiver = smalltalk.send(aString, "_includesSubString_", ["r"])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(5) : smalltalk.send($receiver, "__plus", [(5)]));})() : (function(){return ((($receiver = smalltalk.send(aString, "_includesSubString_", ["v"])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return ((($receiver = ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)]))).klass === smalltalk.Number) ? $receiver +(5) : smalltalk.send($receiver, "__plus", [(5)]));})() : (function(){return smalltalk.send(aString, "_asNumber", []);})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return ((($receiver = ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)]))).klass === smalltalk.Number) ? $receiver +(5) : smalltalk.send($receiver, "__plus", [(5)]));}), (function(){return smalltalk.send(aString, "_asNumber", []);})]));})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(5) : smalltalk.send($receiver, "__plus", [(5)]));}), (function(){return ((($receiver = smalltalk.send(aString, "_includesSubString_", ["v"])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return ((($receiver = ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)]))).klass === smalltalk.Number) ? $receiver +(5) : smalltalk.send($receiver, "__plus", [(5)]));})() : (function(){return smalltalk.send(aString, "_asNumber", []);})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return ((($receiver = ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)]))).klass === smalltalk.Number) ? $receiver +(5) : smalltalk.send($receiver, "__plus", [(5)]));}), (function(){return smalltalk.send(aString, "_asNumber", []);})]));})]));
return self;}
}),
smalltalk.SouvignyBible);

smalltalk.addMethod(
"_title",
smalltalk.method({
selector: "title",
fn: function (){
var self=this;
return "Bible de Souvigny";
return self;}
}),
smalltalk.SouvignyBible);


smalltalk.addMethod(
"_pageClass",
smalltalk.method({
selector: "pageClass",
fn: function (){
var self=this;
return (smalltalk.SouvignyPage || SouvignyPage);
return self;}
}),
smalltalk.SouvignyBible.klass);


smalltalk.addClass('BookWidget', smalltalk.Widget, ['book', 'currentPageNo', 'pageZoomBrush', 'pageZoomWidget', 'zoomLeftPageAnchor', 'zoomRightPageAnchor', 'pageDescriptionsBrush', 'loader', 'scriptsRoot', 'bookContainer', 'width', 'rootBrush', 'menuJQuery', 'isFullscreen', 'downloadBrush', 'leftFolioBrush', 'rightFolioBrush', 'announcer'], 'AFI');
smalltalk.addMethod(
"_afterPageChange_",
smalltalk.method({
selector: "afterPageChange:",
fn: function (data){
var self=this;
smalltalk.send(self, "_updateFolioNumbers", []);
smalltalk.send(self, "_openDescriptions", []);
smalltalk.send(self, "_announcePageChange_", [smalltalk.send(self, "_currentPage", [])]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_announcePageChange_",
smalltalk.method({
selector: "announcePageChange:",
fn: function (aPage){
var self=this;
smalltalk.send(smalltalk.send(self, "_announcer", []), "_announce_", [smalltalk.send((smalltalk.PageChangeAnnouncement || PageChangeAnnouncement), "_page_", [aPage])]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_announcer",
smalltalk.method({
selector: "announcer",
fn: function (){
var self=this;
return (($receiver = self['@announcer']) == nil || $receiver == undefined) ? (function(){return (self['@announcer']=smalltalk.send((smalltalk.Announcer || Announcer), "_new", []));})() : $receiver;
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_beforePageChange_",
smalltalk.method({
selector: "beforePageChange:",
fn: function (data){
var self=this;
smalltalk.send(self,"_closeDescriptions",[]);
smalltalk.send(self,"_openPageNo_",[smalltalk.send(smalltalk.send(data,"_curr",[]),"__plus",[(1)])]);
smalltalk.send(self,"_closeZoom",[]);
return self}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_book",
smalltalk.method({
selector: "book",
fn: function (){
var self=this;
return self['@book'];
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_bookStyle",
smalltalk.method({
selector: "bookStyle",
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(unescape("%0A%09%09%09.bk-widget%20.booklet%20%7B%20margin-bottom%3A%2020px%09%7D%09%09%09%0A%0A%09%09%09.bib-num-album%20%7B%20%20padding%3A%2010px%20%7D%0A%0A%09%09%09.bk-widget%20.b-counter%20%7B%0A%09%09%09%20%20margin-bottom%3A%20-20px%3B%0A%09%09%09%20%20margin-top%3A%2020px%3B%0A%09%09%09%20%20width%3A%20140px%3B%0A%09%09%09%20%20text-align%3A%20left%3B%0A%09%09%09%20%20bottom%3A%200px%3B%0A%09%09%09%20%20background-color%3A%20transparent%3B%0A%09%09%09%20%20font-weight%3A%20bold%3B%0A%09%09%09%20%20font-size%3A%201.1em%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-counter%20+%20.b-counter%20%7B%0A%09%09%09%20%20right%3A%200px%3B%0A%09%09%09%20%20text-align%3A%20right%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.loading%20%7B%20%20text-align%3Acenter%09%7D%0A%09%09%09%0A%09%09%09.bk-widget%20.booklet%20.b-wrap-right%20%7B%0A%09%09%09%20%20background-color%3A%20transparent%3B%0A%09%09%09%20%20padding%3A%200px%3B%20%21important%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-wrap-left%20%7B%0A%09%09%09%20%20background-color%3A%20transparent%3B%0A%09%09%09%20%20padding%3A%200px%3B%20%21important%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-page-cover%20%7B%20%20background-color%3A%20transparent%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20%7B%0A%09%09%09%20%20font-size%3A%201.4em%3B%0A%09%09%09%20%20font-weight%3A%20bold%3B%0A%09%09%09%20%20width%3A%20820px%3B%0A%09%09%09%20%20margin%3A%200%20auto%3B%0A%09%09%09%20%20height%3A%2060px%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20%7B%0A%09%09%09%20%20width%3A%20600px%3B%0A%09%09%09%20%20text-align%3A%20left%3B%0A%09%09%09%20%20height%3A%2060px%3B%0A%09%09%09%20%20float%3Anone%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20.b-current%20%7B%0A%09%09%09%20%20height%3A%20auto%3B%0A%09%09%09%20%20text-align%3A%20left%3B%0A%09%09%09%20%20background%3A%20url%28"), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/menu_off.png%29%20no-repeat%2015px%20center%3B%0A%09%09%09%20%20padding-left%3A%2045px%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%3Ahover%20.b-current%20%7B%0A%09%09%09%20%20background-image%3A%20url%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/menu_on.png%29%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20%7B%20color%3A%20black%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20a%20%7B%20color%3A%20inherit%3B%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%3Ahover%20%7Bcolor%3A%20black%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%3Ahover%20ul%20%7B%20box-shadow%3A%202px%202px%2040px%20rgba%282%2C2%2C0%2C0.8%29%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20ul%20%7B%0A%09%09%09%20%20width%3A%20584px%3B%0A%09%09%09%20%20top%3A%20auto%3B%0A%09%09%09%20%20max-height%3A%20600px%3B%0A%09%09%09%20%20overflow-y%3A%20auto%20%21important%3B%0A%09%09%09%20%20background-color%3A%20white%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20ul%20li%20%7B%20font-size%3A%201.2em%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20ul%20li%20a%20%7B%20height%3A%20auto%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20ul%20li%20a%20.b-text%20%7B%20float%3A%20none%3B%20%7D%0A%0A%09%09%09.bk-widget%20button%20%7Bfloat%3A%20left%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow%20div%20%7B%0A%09%09%09%09-webkit-transition%3A%20all%200.3s%3B%0A%09%09%09%09-moz-transition%3A%20all%200.3s%3B%0A%09%09%09%09-o-transition%3A%20all%200.3s%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow-next%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-next_black.png%29%3B%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow-next%3Ahover%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-next.png%29%3B%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow-prev%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-prev_black.png%29%3B%20%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow-prev%3Ahover%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-prev.png%29%3B%7D%0A%0A%09%09%09.bk-widget%20.b-counter%20+%20.b-counter%20%7Bfloat%3A%20right%3B%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-next%20div%20%7Bbackground-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-next_black-small.png%29%3B%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-next%3Ahover%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-next-small.png%29%3B%20%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-prev%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-prev_black-small.png%29%3B%20%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-prev%3Ahover%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-prev-small.png%29%3B%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-prev%20%7B%20left%3A%20-25px%20%7D%0A%09%09%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-next%20%7B%20right%3A%20-25px%20%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow%20%7B%20width%3A%2025px%20%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow%20%20div%20%7B%20top%3A%2036%25%20%7D%0A%0A%09%09%09.clear%20%7B%20%0A%09%09%09%09clear%3A%20both%3B%0A%09%09%09%09height%3A%200px%20%21important%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-wrap%20%7B%0A%09%09%09%09cursor%3A%20-moz-zoom-in%3B%0A%09%09%09%09cursor%3A%20-webkit-zoom-in%3B%0A%09%09%09%7D%0A")]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_bookletOptions",
smalltalk.method({
selector: "bookletOptions",
fn: function (){
var self=this;
return (function($rec){smalltalk.send($rec, "_at_put_", ["pageSelector", false]);smalltalk.send($rec, "_at_put_", ["chapterSelector", smalltalk.send(self['@isFullscreen'], "_not", [])]);smalltalk.send($rec, "_at_put_", ["menu", self['@menuJQuery']]);smalltalk.send($rec, "_at_put_", ["tabs", false]);smalltalk.send($rec, "_at_put_", ["keyboard", false]);smalltalk.send($rec, "_at_put_", ["arrows", true]);smalltalk.send($rec, "_at_put_", ["closed", true]);smalltalk.send($rec, "_at_put_", ["covers", true]);smalltalk.send($rec, "_at_put_", ["autoCenter", true]);smalltalk.send($rec, "_at_put_", ["pagePadding", (0)]);smalltalk.send($rec, "_at_put_", ["shadows", true]);smalltalk.send($rec, "_at_put_", ["width", smalltalk.send(self, "_width", [])]);smalltalk.send($rec, "_at_put_", ["height", smalltalk.send(self, "_height", [])]);smalltalk.send($rec, "_at_put_", ["manual", false]);smalltalk.send($rec, "_at_put_", ["pageNumbers", false]);smalltalk.send($rec, "_at_put_", ["overlays", false]);smalltalk.send($rec, "_at_put_", ["hovers", false]);smalltalk.send($rec, "_at_put_", ["arrowsHide", false]);smalltalk.send($rec, "_at_put_", ["closedFrontTitle", smalltalk.send(self['@book'], "_title", [])]);smalltalk.send($rec, "_at_put_", ["closedFrontChapter", smalltalk.send(self['@book'], "_title", [])]);smalltalk.send($rec, "_at_put_", ["closedBackTitle", "Fin"]);smalltalk.send($rec, "_at_put_", ["closedBackChapter", "Fin"]);smalltalk.send($rec, "_at_put_", ["previousPageTitle", unescape("Pr%E9c%E9dent")]);smalltalk.send($rec, "_at_put_", ["nextPageTitle", "Suivant"]);smalltalk.send($rec, "_at_put_", ["before", (function(data){return smalltalk.send(self, "_beforePageChange_", [data]);})]);smalltalk.send($rec, "_at_put_", ["after", (function(data){return smalltalk.send(self, "_afterPageChange_", [data]);})]);smalltalk.send($rec, "_at_put_", ["hash", smalltalk.send(smalltalk.send(self, "_isJQueryMobile", []), "_not", [])]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send((smalltalk.HashedCollection || HashedCollection), "_new", []));
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_clear",
smalltalk.method({
selector: "clear",
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(unescape(".bk-widget"), "_asJQuery", []), "_remove", []);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_closeDescriptions",
smalltalk.method({
selector: "closeDescriptions",
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(self['@pageDescriptionsBrush'], "_asJQuery", []), "_fadeOut", []);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_closeZoom",
smalltalk.method({
selector: "closeZoom",
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(unescape(".b-arrow"), "_asJQuery", []), "_show", []);
smalltalk.send(smalltalk.send(self['@pageZoomBrush'], "_asJQuery", []), "_fadeOut_do_", ["slow", (function(){self['@pageZoomWidget']=nil;smalltalk.send(self['@pageZoomBrush'], "_empty", []);(function($rec){smalltalk.send($rec, "_removeClass_", ["active"]);return smalltalk.send($rec, "_show", []);})(self['@zoomLeftPageAnchor']);(function($rec){smalltalk.send($rec, "_removeClass_", ["active"]);return smalltalk.send($rec, "_show", []);})(self['@zoomRightPageAnchor']);((($receiver = smalltalk.send(smalltalk.send(self, "_currentPageNo", []), "__eq", [(1)])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return smalltalk.send(self['@zoomLeftPageAnchor'], "_hide", []);})() : nil) : smalltalk.send($receiver, "_ifTrue_", [(function(){return smalltalk.send(self['@zoomLeftPageAnchor'], "_hide", []);})]));return ((($receiver = ((($receiver = smalltalk.send(self, "_currentPageNo", [])).klass === smalltalk.Number) ? $receiver >smalltalk.send(self['@book'], "_size", []) : smalltalk.send($receiver, "__gt", [smalltalk.send(self['@book'], "_size", [])]))).klass === smalltalk.Boolean) ? ($receiver ? (function(){return smalltalk.send(self['@zoomRightPageAnchor'], "_hide", []);})() : nil) : smalltalk.send($receiver, "_ifTrue_", [(function(){return smalltalk.send(self['@zoomRightPageAnchor'], "_hide", []);})]));})]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_closeZoomOr_",
smalltalk.method({
selector: "closeZoomOr:",
fn: function (aBlock){
var self=this;
smalltalk.send(self['@pageZoomWidget'], "_ifNil_ifNotNil_", [aBlock, (function(){smalltalk.send(self, "_closeZoom", []);return smalltalk.send(self, "_openDescriptions", []);})]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_currentPage",
smalltalk.method({
selector: "currentPage",
fn: function (){
var self=this;
return smalltalk.send(self['@book'], "_pageAt_ifAbsent_", [smalltalk.send(self, "_currentPageNo", []), (function(){return smalltalk.send(smalltalk.send(self['@book'], "_pages", []), "_last", []);})]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_currentPageNo",
smalltalk.method({
selector: "currentPageNo",
fn: function (){
var self=this;
return (($receiver = self['@currentPageNo']) == nil || $receiver == undefined) ? (function(){return self['@currentPageNo']=(1);})() : $receiver;
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_fullScreenStyle",
smalltalk.method({
selector: "fullScreenStyle",
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(unescape("%0A%09body.fullscreen%20%7B%0A%09%09overflow%3A%20hidden%3B%0A%09%7D%0A%0A%0A%09.fullscreen.bk-widget%20%7B%0A%09%09position%3A%20fixed%3B%0A%09%09width%3A%20100%25%3B%0A%09%09height%3A%20100%25%3B%0A%09%09z-index%3A%20200%3B%0A%09%09top%3A%200%3B%0A%09%09left%3A%200%3B%0A%09%09overflow-y%3A%20auto%3B%0A%09%7D%0A%0A%09.fullscreen.bk-widget%20.b-menu%20%7B%0A%09%09height%3A%200px%3B%0A%09%7D%0A%0A%09.fullscreen.bk-widget%2C%0A%09.fullscreen.bk-widget%20.b-menu%20.b-selector%2C%0A%09.fullscreen.bk-widget%20.b-menu%20.b-selector%20ul%2C%0A%09.fullscreen.bk-widget%20.b-counter%20%7B%09%0A%09%09color%3A%20white%3B%0A%09%09background-color%3A%20black%3B%0A%09%7D%0A%0A%09.fullscreen%20.b-zoom-fullscreen%20%7B%0A%09%09position%3A%20absolute%3B%0A%09%09right%3A%200px%3B%0A%09%7D%0A%0A%09.fullscreen.bk-widget%20.b-download-book%20a%20%7B%0A%09%09position%3A%20absolute%3B%0A%09%09right%3A%2060px%3B%0A%09%7D%0A%0A%09.fullscreen%20.b-zoom-fullscreen%20a%20%7B%0A%09%09background%3A%20url%28"), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/unexpand_black.png%29%20no-repeat%3B%0A%09%7D%0A%0A%09.fullscreen%20.b-zoom-fullscreen%20a%3Ahover%20%7B%0A%09%09background%3A%20url%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/unexpand_white.png%29%20no-repeat%3B%0A%09%7D%0A%0A%09.fullscreen%20h1.title%20%7B%0A%09%09font-size%3A%202em%3B%0A%09%09color%3A%20white%3B%0A%09%09border-bottom%3A%200px%3B%0A%09%09margin%3A%205px%200px%200px%200px%3B%0A%09%09text-align%3A%20center%3B%0A%09%7D%0A%0A%09.fullscreen%20%0A")]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_goToPageNo_",
smalltalk.method({
selector: "goToPageNo:",
fn: function (pageNo){
var self=this;
smalltalk.send(smalltalk.send(self['@bookContainer'], "_asJQuery", []), "_booklet_", [pageNo]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_height",
smalltalk.method({
selector: "height",
fn: function (){
var self=this;
return smalltalk.send(((($receiver = ((($receiver = ((($receiver = smalltalk.send(self['@book'], "_height", [])).klass === smalltalk.Number) ? $receiver *smalltalk.send(self, "_width", []) : smalltalk.send($receiver, "__star", [smalltalk.send(self, "_width", [])]))).klass === smalltalk.Number) ? $receiver /smalltalk.send(self['@book'], "_width", []) : smalltalk.send($receiver, "__slash", [smalltalk.send(self['@book'], "_width", [])]))).klass === smalltalk.Number) ? $receiver /(2) : smalltalk.send($receiver, "__slash", [(2)])), "_rounded", []);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_ifIE_ifNotIE_",
smalltalk.method({
selector: "ifIE:ifNotIE:",
fn: function (aBlock, anotherBlock){
var self=this;
smalltalk.send(smalltalk.send(self, "_isIE", []), "_ifTrue_ifFalse_", [aBlock, anotherBlock]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_initialize",
smalltalk.method({
selector: "initialize",
fn: function (){
var self=this;
smalltalk.send(self, "_initialize", [], smalltalk.Widget);
(self['@isFullscreen']=false);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_isContainerSmall",
smalltalk.method({
selector: "isContainerSmall",
fn: function (){
var self=this;
return ((($receiver = smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_width", [])).klass === smalltalk.Number) ? $receiver <(500) : smalltalk.send($receiver, "__lt", [(500)]));
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_isIE",
smalltalk.method({
selector: "isIE",
fn: function (){
var self=this;
var ie=nil;
ie=jQuery.browser.msie;
return smalltalk.send(ie, "_notNil", []);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_isJQueryMobile",
smalltalk.method({
selector: "isJQueryMobile",
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery", []), "_at_", ["jqmData"]), "_isNil", []), "_not", []);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_isRunInTestCase",
smalltalk.method({
selector: "isRunInTestCase",
fn: function (){
var self=this;
return smalltalk.send(self, "_isTestCaseInContext_", [(smalltalk.getThisContext())]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_isTestCaseInContext_",
smalltalk.method({
selector: "isTestCaseInContext:",
fn: function (aContext){
var self=this;
return (($receiver = smalltalk.send(aContext, "_home", [])) == nil || $receiver == undefined) ? (function(){return false;})() : (function(){return smalltalk.send(smalltalk.send(smalltalk.send(aContext, "_receiver", []), "_isKindOf_", [(smalltalk.TestCase || TestCase)]), "_or_", [(function(){return smalltalk.send(self, "_isTestCaseInContext_", [smalltalk.send(aContext, "_home", [])]);})]);})();
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_leftPage",
smalltalk.method({
selector: "leftPage",
fn: function (){
var self=this;
return smalltalk.send(self['@book'], "_pageAt_ifAbsent_", [((($receiver = self['@currentPageNo']).klass === smalltalk.Number) ? $receiver -(1) : smalltalk.send($receiver, "__minus", [(1)])), (function(){return smalltalk.send((smalltalk.Page || Page), "_new", []);})]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_loadBookThenRenderOn_",
smalltalk.method({
selector: "loadBookThenRenderOn:",
fn: function (bookBrush){
var self=this;
var renderBlock=nil;
(renderBlock=(function(aBook){return smalltalk.send(self, "_renderBook_on_", [aBook, bookBrush]);}));
(($receiver = self['@book']) == nil || $receiver == undefined) ? (function(){return smalltalk.send(smalltalk.send(self, "_loader", []), "_loadBookFromJSONOnSuccess_", [renderBlock]);})() : (function(){smalltalk.send(self['@book'], "_reset", []);return smalltalk.send(renderBlock, "_value_", [self['@book']]);})();
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_loader",
smalltalk.method({
selector: "loader",
fn: function (){
var self=this;
return (($receiver = self['@loader']) == nil || $receiver == undefined) ? (function(){return self['@loader']=smalltalk.send((smalltalk.SouvignyLoader || SouvignyLoader), "_new", []);})() : $receiver;
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_loader_",
smalltalk.method({
selector: "loader:",
fn: function (aBibNumLoader){
var self=this;
self['@loader']=aBibNumLoader;
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_navigatorWidth",
smalltalk.method({
selector: "navigatorWidth",
fn: function (){
var self=this;
return smalltalk.send((smalltalk.AbstractBookNavigatorWidget || AbstractBookNavigatorWidget), "_width", []);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_onPageChangeDo_",
smalltalk.method({
selector: "onPageChangeDo:",
fn: function (aBlockWithArg){
var self=this;
smalltalk.send(smalltalk.send(self, "_announcer", []), "_on_do_", [(smalltalk.PageChangeAnnouncement || PageChangeAnnouncement), (function(aPageChangeAnnouncement){return smalltalk.send(aBlockWithArg, "_value_", [smalltalk.send(aPageChangeAnnouncement, "_page", [])]);})]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_openDescriptions",
smalltalk.method({
selector: "openDescriptions",
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(self['@pageDescriptionsBrush'], "_asJQuery", []), "_hide", []);
smalltalk.send(self['@pageDescriptionsBrush'], "_contents_", [(function(html){smalltalk.send(smalltalk.send(smalltalk.send(html, "_div", []), "_asJQuery", []), "_html_", [smalltalk.send(smalltalk.send(self, "_leftPage", []), "_description", [])]);return smalltalk.send(smalltalk.send(smalltalk.send(html, "_div", []), "_asJQuery", []), "_html_", [smalltalk.send(smalltalk.send(self, "_rightPage", []), "_description", [])]);})]);
smalltalk.send(smalltalk.send(self['@pageDescriptionsBrush'], "_asJQuery", []), "_fadeIn", []);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_openPage_",
smalltalk.method({
selector: "openPage:",
fn: function (aPage){
var self=this;
smalltalk.send(self, "_goToPageNo_", [smalltalk.send(aPage, "_pageNo", [])]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_openPageNo_",
smalltalk.method({
selector: "openPageNo:",
fn: function (anInteger){
var self=this;
(self['@currentPageNo']=anInteger);
smalltalk.send(self['@book'], "_pagesNo_do_", [[((($receiver = anInteger).klass === smalltalk.Number) ? $receiver -(1) : smalltalk.send($receiver, "__minus", [(1)])),anInteger], (function(aPage){return smalltalk.send(aPage, "_renderWidth_height_", [smalltalk.send(((($receiver = smalltalk.send(self, "_width", [])).klass === smalltalk.Number) ? $receiver /(2) : smalltalk.send($receiver, "__slash", [(2)])), "_rounded", []), smalltalk.send(self, "_height", [])]);})]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_reloadWidget",
smalltalk.method({
selector: "reloadWidget",
fn: function (){
var self=this;
smalltalk.send(self['@rootBrush'], "_contents_", [(function(html){return smalltalk.send(self, "_renderWidgetOn_", [html]);})]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderBook_on_",
smalltalk.method({
selector: "renderBook:on:",
fn: function (aBook, aBrush){
var self=this;
(self['@book']=aBook);
smalltalk.send(aBrush, "_contents_", [(function(html){return smalltalk.send(smalltalk.send(aBook, "_pages", []), "_do_", [(function(aPage){return smalltalk.send(aPage, "_brush_", [(function($rec){smalltalk.send($rec, "_rel_", [smalltalk.send(aPage, "_title", [])]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send(html, "_div", []))]);})]);})]);
((($receiver = smalltalk.send(self, "_isContainerSmall", [])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_addClass_", ["small"]);})() : nil) : smalltalk.send($receiver, "_ifTrue_", [(function(){return smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_addClass_", ["small"]);})]));
(($receiver = smalltalk.send(smalltalk.send(self['@bookContainer'], "_asJQuery", []), "_at_", ["booklet"])) != nil && $receiver != undefined) ? (function(){return smalltalk.send(smalltalk.send(self['@bookContainer'], "_asJQuery", []), "_booklet_", [smalltalk.send(self, "_bookletOptions", [])]);})() : nil;
smalltalk.send(smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_find_", [unescape(".b-wrap-left")]), "_click_", [(function(){return smalltalk.send(self, "_zoomLeftPage", []);})]);
smalltalk.send(smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_find_", [unescape(".b-wrap-right%2C%20.b-page-cover")]), "_click_", [(function(){return smalltalk.send(self, "_zoomRightPage", []);})]);
smalltalk.send(smalltalk.send(self['@book'], "_downloadUrl", []), "_ifNotEmpty_", [(function(){return smalltalk.send(self['@downloadBrush'], "_contents_", [(function(html){return smalltalk.send(smalltalk.send(html, "_a", []), "_href_", [smalltalk.send(aBook, "_downloadUrl", [])]);})]);})]);
((($receiver = self['@isFullscreen']).klass === smalltalk.Boolean) ? ($receiver ? (function(){return (function($rec){smalltalk.send($rec, "_renderBookNavigator", []);return smalltalk.send($rec, "_renderBookTitle", []);})(self);})() : nil) : smalltalk.send($receiver, "_ifTrue_", [(function(){return (function($rec){smalltalk.send($rec, "_renderBookNavigator", []);return smalltalk.send($rec, "_renderBookTitle", []);})(self);})]));
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderBookMenuOn_",
smalltalk.method({
selector: "renderBookMenuOn:",
fn: function (html){
var self=this;
(self['@menuJQuery']=(function($rec){smalltalk.send($rec, "_class_", [unescape("book-menu")]);return smalltalk.send($rec, "_asJQuery", []);})(smalltalk.send(html, "_div", [])));
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderBookNavigator",
smalltalk.method({
selector: "renderBookNavigator",
fn: function (){
var self=this;
var navigatorDiv=nil;
(navigatorDiv=smalltalk.send(unescape("%3Cdiv%3E%3C/div%3E"), "_asJQuery", []));
smalltalk.send(navigatorDiv, "_insertAfter_", [self['@menuJQuery']]);
smalltalk.send([(smalltalk.BookBookmarkNavigatorWidget || BookBookmarkNavigatorWidget),(smalltalk.BookThumbnailNavigatorWidget || BookThumbnailNavigatorWidget)], "_do_", [(function(aNavigatorClass){var navigator=nil;
(navigator=(function($rec){smalltalk.send($rec, "_book_", [self['@book']]);smalltalk.send($rec, "_appendToJQuery_", [navigatorDiv]);smalltalk.send($rec, "_onPageChangeDo_", [(function(aPage){return smalltalk.send(self, "_openPage_", [aPage]);})]);smalltalk.send($rec, "_highlightPage_", [smalltalk.send(self, "_currentPage", [])]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send(aNavigatorClass, "_new", [])));return smalltalk.send(self, "_onPageChangeDo_", [(function(aPage){return smalltalk.send(navigator, "_highlightPage_", [aPage]);})]);})]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderBookOn_",
smalltalk.method({
selector: "renderBookOn:",
fn: function (html){
var self=this;
(self['@bookContainer']=smalltalk.send(html, "_div", []));
(function($rec){smalltalk.send($rec, "_class_", ["book"]);return smalltalk.send($rec, "_with_", [(function(){var bookBrush=nil;
(self['@leftFolioBrush']=smalltalk.send(smalltalk.send(html, "_div", []), "_class_", [unescape("b-counter")]));(self['@rightFolioBrush']=smalltalk.send(smalltalk.send(html, "_div", []), "_class_", [unescape("b-counter")]));(bookBrush=(function($rec){smalltalk.send($rec, "_class_", [unescape("b-load")]);smalltalk.send($rec, "_with_", [(function(){return (function($rec){smalltalk.send($rec, "_class_", ["loading"]);return smalltalk.send($rec, "_with_", [(function(){return smalltalk.send(smalltalk.send(html, "_img", []), "_src_", [smalltalk.send(smalltalk.send(self, "_scriptsRoot", []), "__comma", [unescape("images/ajax-loader.gif")])]);})]);})(smalltalk.send(html, "_div", []));})]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send(html, "_div", [])));return smalltalk.send(self, "_loadBookThenRenderOn_", [bookBrush]);})]);})(self['@bookContainer']);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderBookTitle",
smalltalk.method({
selector: "renderBookTitle",
fn: function (){
var self=this;
var titleDiv=nil;
(titleDiv=smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(unescape("%3Ch1%20class%3D%22title%22%3E"), "__comma", [smalltalk.send(self['@book'], "_title", [])]), "__comma", [unescape("%20%28%20")]), "__comma", [smalltalk.send(self['@book'], "_size", [])]), "__comma", [unescape("%20pages%20%29%20%3C/h1%3E")]), "_asJQuery", []));
smalltalk.send(titleDiv, "_insertBefore_", [self['@menuJQuery']]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderDevToolsOn_",
smalltalk.method({
selector: "renderDevToolsOn:",
fn: function (html){
var self=this;
((($receiver = smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send((smalltalk.Smalltalk || Smalltalk), "_current", []), "_at_", ["Browser"]), "_notNil", []), "_and_", [(function(){return smalltalk.send(smalltalk.send(self, "_isRunInTestCase", []), "_not", []);})])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return (function($rec){smalltalk.send($rec, "_addButton_action_", ["Reload booklet", (function(){return smalltalk.send(self, "_reloadWidget", []);})]);smalltalk.send($rec, "_addButton_action_", ["Inspect booklet", (function(){return smalltalk.send(self, "_inspect", []);})]);return smalltalk.send($rec, "_addButton_action_", ["Toggle fullscreen", (function(){return smalltalk.send(self, "_toggleFullscreen", []);})]);})((smalltalk.AFIIDETools || AFIIDETools));})() : nil) : smalltalk.send($receiver, "_ifTrue_", [(function(){return (function($rec){smalltalk.send($rec, "_addButton_action_", ["Reload booklet", (function(){return smalltalk.send(self, "_reloadWidget", []);})]);smalltalk.send($rec, "_addButton_action_", ["Inspect booklet", (function(){return smalltalk.send(self, "_inspect", []);})]);return smalltalk.send($rec, "_addButton_action_", ["Toggle fullscreen", (function(){return smalltalk.send(self, "_toggleFullscreen", []);})]);})((smalltalk.AFIIDETools || AFIIDETools));})]));
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderDownloadBookOn_",
smalltalk.method({
selector: "renderDownloadBookOn:",
fn: function (html){
var self=this;
(self['@downloadBrush']=smalltalk.send(smalltalk.send(html, "_div", []), "_class_", [unescape("b-download-book")]));
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderFullscreenControlsOn_",
smalltalk.method({
selector: "renderFullscreenControlsOn:",
fn: function (html){
var self=this;
(function($rec){smalltalk.send($rec, "_class_", [unescape("b-zoom-fullscreen")]);return smalltalk.send($rec, "_with_", [(function(){return smalltalk.send(smalltalk.send(html, "_a", []), "_onClick_", [(function(){return smalltalk.send(self, "_toggleFullscreen", []);})]);})]);})(smalltalk.send(html, "_div", []));
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderOn_",
smalltalk.method({
selector: "renderOn:",
fn: function (html){
var self=this;
smalltalk.send(self, "_renderDevToolsOn_", [html]);
(self['@rootBrush']=smalltalk.send(html, "_root", []));
smalltalk.send(self, "_renderWidgetOn_", [html]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderPage_class_on_",
smalltalk.method({
selector: "renderPage:class:on:",
fn: function (aPage, aCssClass, html){
var self=this;
smalltalk.send((function($rec){smalltalk.send($rec, "_class_", [aCssClass]);smalltalk.send($rec, "_with_", [(function(){return self['@pageZoomWidget']=(function($rec){smalltalk.send($rec, "_page_", [aPage]);smalltalk.send($rec, "_renderOn_", [html]);smalltalk.send($rec, "_onCloseDo_", [(function(){return (function($rec){smalltalk.send($rec, "_closeZoom", []);return smalltalk.send($rec, "_openDescriptions", []);})(self);})]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send((smalltalk.PageWidget || PageWidget), "_new", []));})]);return smalltalk.send($rec, "_asJQuery", []);})(smalltalk.send(html, "_div", [])), "_fadeIn_", ["slow"]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderPageDescriptionOn_",
smalltalk.method({
selector: "renderPageDescriptionOn:",
fn: function (html){
var self=this;
(self['@pageDescriptionsBrush']=(function($rec){smalltalk.send($rec, "_class_", [unescape("page-desc")]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send(html, "_div", [])));
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderScripts",
smalltalk.method({
selector: "renderScripts",
fn: function (){
var self=this;
var head=nil;
(head=smalltalk.send("head", "_asJQuery", []));
((($receiver = smalltalk.send(smalltalk.send(smalltalk.send(head, "_find_", [unescape("script%5Bsrc*%3D%22booklet%22%5D")]), "_length", []), "__eq", [(0)])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return smalltalk.send(self, "_renderScriptsOn_", [smalltalk.send((smalltalk.HTMLCanvas || HTMLCanvas), "_onJQuery_", [head])]);})() : nil) : smalltalk.send($receiver, "_ifTrue_", [(function(){return smalltalk.send(self, "_renderScriptsOn_", [smalltalk.send((smalltalk.HTMLCanvas || HTMLCanvas), "_onJQuery_", [head])]);})]));
((($receiver = self['@isFullscreen']).klass === smalltalk.Boolean) ? ($receiver ? (function(){return smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_addClass_", ["fullscreen"]);})() : (function(){return smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_removeClass_", ["fullscreen"]);})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_addClass_", ["fullscreen"]);}), (function(){return smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_removeClass_", ["fullscreen"]);})]));
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderScriptsOn_",
smalltalk.method({
selector: "renderScriptsOn:",
fn: function (html){
var self=this;
smalltalk.send([unescape("booklet/jquery.booklet.1.2.0.css"), unescape("iviewer/jquery.iviewer.css")], "_do_", [(function(anUrl){return (function($rec){smalltalk.send($rec, "_href_", [smalltalk.send(smalltalk.send(self, "_scriptsRoot", []), "__comma", [anUrl])]);smalltalk.send($rec, "_type_", [unescape("text/css")]);return smalltalk.send($rec, "_rel_", ["stylesheet"]);})(smalltalk.send(html, "_link", []));})]);
(function($rec){smalltalk.send($rec, "_type_", [unescape("text/css")]);return smalltalk.send($rec, "_with_", [smalltalk.send(self, "_style", [])]);})(smalltalk.send(html, "_style", []));
(($receiver = smalltalk.send((typeof jQuery == 'undefined' ? nil : jQuery), "_at_", ["ui"])) == nil || $receiver == undefined) ? (function(){return (function($rec){smalltalk.send($rec, "_type_", [unescape("text/javascript")]);return smalltalk.send($rec, "_src_", [unescape("http%3A//ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js")]);})(smalltalk.send(html, "_script", []));})() : $receiver;
smalltalk.send([unescape("booklet/jquery.easing.1.3.js"), unescape("booklet/jquery.booklet.1.2.0.min.js"), unescape("iviewer/jquery.iviewer.min.js"), unescape("iviewer/jquery.mousewheel.min.js")], "_do_", [(function(anUrl){return (function($rec){smalltalk.send($rec, "_type_", [unescape("text/javascript")]);return smalltalk.send($rec, "_src_", [smalltalk.send(smalltalk.send(self, "_scriptsRoot", []), "__comma", [anUrl])]);})(smalltalk.send(html, "_script", []));})]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderWidgetOn_",
smalltalk.method({
selector: "renderWidgetOn:",
fn: function (html){
var self=this;
(function($rec){smalltalk.send($rec, "_class_", [smalltalk.send(self, "_widgetClass", [])]);return smalltalk.send($rec, "_with_", [(function(){return (function($rec){smalltalk.send($rec, "_renderScripts", []);smalltalk.send($rec, "_renderFullscreenControlsOn_", [html]);smalltalk.send($rec, "_renderDownloadBookOn_", [html]);smalltalk.send($rec, "_renderBookMenuOn_", [html]);smalltalk.send($rec, "_renderZoomControlsOn_", [html]);smalltalk.send($rec, "_renderBookOn_", [html]);return smalltalk.send($rec, "_renderPageDescriptionOn_", [html]);})(self);})]);})(smalltalk.send(html, "_div", []));
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderZoomControlsOn_",
smalltalk.method({
selector: "renderZoomControlsOn:",
fn: function (html){
var self=this;
(function($rec){smalltalk.send($rec, "_class_", [unescape("b-zoom-magnify")]);return smalltalk.send($rec, "_with_", [(function(){(self['@zoomLeftPageAnchor']=smalltalk.send(smalltalk.send(smalltalk.send(html, "_a", []), "_onClick_", [(function(){return smalltalk.send(self, "_zoomLeftPage", []);})]), "_asJQuery", []));smalltalk.send(self['@zoomLeftPageAnchor'], "_hide", []);(self['@zoomRightPageAnchor']=smalltalk.send(smalltalk.send(smalltalk.send(html, "_a", []), "_onClick_", [(function(){return smalltalk.send(self, "_zoomRightPage", []);})]), "_asJQuery", []));smalltalk.send(self['@zoomRightPageAnchor'], "_hide", []);return (self['@pageZoomBrush']=(function($rec){smalltalk.send($rec, "_class_", [unescape("b-zoom")]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send(html, "_div", [])));})]);})(smalltalk.send(html, "_div", []));
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_reset",
smalltalk.method({
selector: "reset",
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(self, "_clear", []), "_show", []);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_rightPage",
smalltalk.method({
selector: "rightPage",
fn: function (){
var self=this;
return smalltalk.send(self['@book'], "_pageAt_ifAbsent_", [self['@currentPageNo'], (function(){return smalltalk.send((smalltalk.Page || Page), "_new", []);})]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_scriptsRoot",
smalltalk.method({
selector: "scriptsRoot",
fn: function (){
var self=this;
return (($receiver = self['@scriptsRoot']) == nil || $receiver == undefined) ? (function(){return self['@scriptsRoot']="";})() : $receiver;
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_scriptsRoot_",
smalltalk.method({
selector: "scriptsRoot:",
fn: function (anUrl){
var self=this;
self['@scriptsRoot']=anUrl;
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_show",
smalltalk.method({
selector: "show",
fn: function (){
var self=this;
smalltalk.send(self, "_appendToJQuery_", [smalltalk.send(unescape(".bib-num-album"), "_asJQuery", [])]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_style",
smalltalk.method({
selector: "style",
fn: function (){
var self=this;
return smalltalk.send((smalltalk.String || String), "_streamContents_", [(function(aStream){return (function($rec){smalltalk.send($rec, "_nextPutAll_", [smalltalk.send(self, "_bookStyle", [])]);smalltalk.send($rec, "_nextPutAll_", [smalltalk.send(self, "_zoomControlsStyle", [])]);return smalltalk.send($rec, "_nextPutAll_", [smalltalk.send(self, "_fullScreenStyle", [])]);})(aStream);})]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_toggleFullscreen",
smalltalk.method({
selector: "toggleFullscreen",
fn: function (){
var self=this;
(self['@isFullscreen']=smalltalk.send(self['@isFullscreen'], "_not", []));
smalltalk.send(smalltalk.send(self, "_loader", []), "_abort", []);
smalltalk.send(self, "_reloadWidget", []);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_updateFolioNumbers",
smalltalk.method({
selector: "updateFolioNumbers",
fn: function (){
var self=this;
smalltalk.send(self['@leftFolioBrush'], "_contents_", [smalltalk.send(smalltalk.send(self, "_leftPage", []), "_foliono", [])]);
smalltalk.send(self['@rightFolioBrush'], "_contents_", [smalltalk.send(smalltalk.send(self, "_rightPage", []), "_foliono", [])]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_widgetClass",
smalltalk.method({
selector: "widgetClass",
fn: function (){
var self=this;
return ((($receiver = self['@isFullscreen']).klass === smalltalk.Boolean) ? ($receiver ? (function(){return unescape("fullscreen%20bk-widget");})() : (function(){return unescape("bk-widget");})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return unescape("fullscreen%20bk-widget");}), (function(){return unescape("bk-widget");})]));
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_width",
smalltalk.method({
selector: "width",
fn: function (){
var self=this;
return ((($receiver = ((($receiver = self['@isFullscreen']).klass === smalltalk.Boolean) ? ($receiver ? (function(){return smalltalk.send(((($receiver = smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_width", [])).klass === smalltalk.Number) ? $receiver -(2) * smalltalk.send(self, "_navigatorWidth", []) : smalltalk.send($receiver, "__minus", [(2) * smalltalk.send(self, "_navigatorWidth", [])])), "_min_", [(900)]);})() : (function(){return smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_width", []);})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return smalltalk.send(((($receiver = smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_width", [])).klass === smalltalk.Number) ? $receiver -(2) * smalltalk.send(self, "_navigatorWidth", []) : smalltalk.send($receiver, "__minus", [(2) * smalltalk.send(self, "_navigatorWidth", [])])), "_min_", [(900)]);}), (function(){return smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_width", []);})]))).klass === smalltalk.Number) ? $receiver -(2) * smalltalk.send(self, "_zoomControlWidth", []) : smalltalk.send($receiver, "__minus", [(2) * smalltalk.send(self, "_zoomControlWidth", [])]));
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_zoomControlWidth",
smalltalk.method({
selector: "zoomControlWidth",
fn: function (){
var self=this;
return ((($receiver = smalltalk.send(self, "_isContainerSmall", [])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return (30);})() : (function(){return (85);})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return (30);}), (function(){return (85);})]));
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_zoomControlsStyle",
smalltalk.method({
selector: "zoomControlsStyle",
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(unescape("%0A%09%09%09.b-zoom%20%7B%0A%09%09%09%20%20position%3A%20fixed%3B%0A%09%09%09%20%20top%3A%200px%3B%0A%09%09%09%20%20left%3A%200px%3B%0A%09%09%09%20%20width%3A%20100%25%3B%0A%09%09%09%20%20height%3A%20100%25%3B%0A%09%09%09%20%20display%3A%20none%3B%0A%09%09%09%20%20z-index%3A%20200%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom%20.page-desc%20%7B%0A%09%09%09%20%20margin%3A%200px%205px%3B%0A%09%09%09%20%20width%3A%20auto%3B%0A%09%09%09%20%20color%3A%20white%3B%0A%09%09%09%20%20width%3A%2045%25%3B%0A%09%09%09%20%20padding-right%3A%2020px%3B%0A%09%09%09%20%20height%3A%2095%25%3B%0A%09%09%09%20%20max-width%3Aauto%3B%0A%09%09%09%20%20overflow-y%3A%20auto%3B%0A%09%09%09%20%20display%3A%20block%3B%0A%09%09%09%20%20float%3A%20left%3B%0A%09%09%09%20%20font-size%3A%201.3em%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom-magnify%20%7B%0A%09%09%09%20%20margin%3A%200px%20auto%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom-magnify%20a%2C%0A%09%09%09.b-zoom-fullscreen%20a%20%7B%0A%09%09%09%09display%3A%20block%3B%0A%09%09%09%09width%3A%2048px%3B%0A%09%09%09%09height%3A%2048px%3B%0A%09%09%09%09z-index%3A%2020%3B%0A%09%09%09%09position%3A%20relative%3B%0A%09%09%09%09cursor%3A%20pointer%3B%0A%09%09%09%7D%0A%09%09%09%0A%09%09%09.b-zoom-fullscreen%20%7Bfloat%3A%20right%7D%0A%0A%09%09%09.b-zoom-fullscreen%20a%20%7B%0A%09%09%09%09background%3A%20url%28"), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/expand_black.png%29%20no-repeat%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom-fullscreen%20a%3Ahover%20%7B%0A%09%09%09%09background%3A%20url%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/expand_white.png%29%20no-repeat%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-download-book%20a%20%7B%0A%09%09%09%09float%3A%20right%3B%0A%09%09%09%09display%3A%20block%3B%0A%09%09%09%09width%3A%2073px%3B%0A%09%09%09%09height%3A%2036px%3B%0A%09%09%09%09margin-right%3A%205px%3B%0A%09%09%09%09margin-top%3A%206px%3B%0A%09%09%09%09z-index%3A%2020%3B%0A%09%09%09%09position%3A%20relative%3B%0A%09%09%09%09cursor%3A%20pointer%3B%0A%09%09%09%09background%3A%20url%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/download_pdf_black.png%29%20no-repeat%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-download-book%20a%3Ahover%20%7B%0A%09%09%09%09background%3A%20url%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/download_pdf_white.png%29%20no-repeat%3B%0A%09%09%09%7D%0A%0A%09%09%09.small%3E.bk-widget%20.b-zoom-magnify%20a%20%7B%0A%09%09%09%09background-image%3A%20none%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom-magnify%20a%20%7B%0A%09%09%09%09background%3A%20url%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/magnify_black.png%29%20no-repeat%3B%0A%09%09%09%7D%0A%09%09%09%0A%09%09%09.b-zoom-magnify%20a%3Ahover%20%7B%0A%09%09%09%09background-image%3A%20url%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/magnify_white.png%29%3B%0A%09%09%09%7D%0A%0A%09%09%09.small%3E.bk-widget%20.b-zoom-magnify%20a%3Ahover%20%7B%0A%09%09%09%09background-image%3A%20none%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom-magnify%20a%20%7B%0A%09%09%09%09float%3A%20left%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom-magnify%20a%20+%20a%20%7B%0A%09%09%09%09float%3A%20right%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom%20%3E%20div%20%7B%0A%09%09%09%20%20position%3A%20relative%3B%0A%09%09%09%20%20z-index%3A%2030%3B%0A%0A%09%09%09%20%20background-color%3A%20rgb%2810%2C10%2C10%29%3B%0A%09%09%09%20%20border%3A%2010px%20solid%20rgb%2850%2C50%2C50%29%3B%0A%0A%09%09%09%20%20background-color%3A%20rgba%2810%2C10%2C10%2C0.8%29%3B%0A%09%09%09%20%20border%3A%2010px%20solid%20rgba%2850%2C50%2C50%2C0.8%29%3B%0A%0A%09%09%09%20%20border-radius%3A%2010px%3B%0A%09%09%09%20%20display%3Anone%3B%0A%09%09%09%20%20padding%3A%201px%3B%0A%09%09%09%20%20height%3A%20100%25%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom%20%3E%20div%20%3E%20div%20%7B%0A%09%09%09%20%20overflow%3A%20scroll%3B%0A%09%09%09%20%20border-radius%3A%2010px%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom%20.iviewer%20%7B%0A%09%09%09%09height%3A%20100%25%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom%20.iviewer_with_text%20%7B%0A%09%09%09%20%20float%3A%20left%3B%0A%09%09%09%20%20width%3A%2050%25%3B%0A%09%09%09%20%20margin-right%3A%205px%3B%0A%09%09%09%7D%0A%0A%09%09%09.iviewer%20%7B%0A%09%09%09%20%20backround-color%3A%20black%3B%0A%09%09%09%7D%0A%0A%09%09%09.iviewer_cursor%20%7B%0A%09%09%09%20%20cursor%3A%20move%3B%0A%09%09%09%7D%0A%0A%09%09%09.controls%20div.iviewer_common%20%7B%0A%09%09%09%20%20position%3A%20static%20%21important%3B%09%09%0A%09%09%09%20%20margin%3A%205px%20auto%3B%0A%09%09%09%20%20background-color%3A%20transparent%3B%0A%09%09%09%7D%0A%0A%09%09%09.controls%20div.iviewer_common%3Ahover%20%7B%0A%09%09%09%09background-color%3A%20white%3B%0A%09%09%09%7D%0A%0A%09%09%09.iviewer_zoom_close%20%7B%0A%09%09%09%20%20background%3A%20url%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/close_black28.png%29%3B%0A%09%09%09%7D%0A")]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_zoomLeftPage",
smalltalk.method({
selector: "zoomLeftPage",
fn: function (){
var self=this;
smalltalk.send(self, "_closeZoomOr_", [(function(){smalltalk.send(self, "_zoomPageNo_withClass_", [((($receiver = self['@currentPageNo']).klass === smalltalk.Number) ? $receiver -(1) : smalltalk.send($receiver, "__minus", [(1)])), unescape("b-left")]);return smalltalk.send(self['@zoomLeftPageAnchor'], "_addClass_", ["active"]);})]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_zoomPageNo_withClass_",
smalltalk.method({
selector: "zoomPageNo:withClass:",
fn: function (anInteger, aCssClass){
var self=this;
smalltalk.send(self, "_closeDescriptions", []);
smalltalk.send(self['@zoomLeftPageAnchor'], "_hide", []);
smalltalk.send(self['@zoomRightPageAnchor'], "_hide", []);
smalltalk.send(smalltalk.send(unescape(".b-arrow"), "_asJQuery", []), "_hide", []);
smalltalk.send(self['@book'], "_pageAt_do_", [anInteger, (function(aPage){smalltalk.send(self['@pageZoomBrush'], "_contents_", [(function(html){return smalltalk.send(self, "_renderPage_class_on_", [aPage, aCssClass, html]);})]);return smalltalk.send(smalltalk.send(self['@pageZoomBrush'], "_asJQuery", []), "_show", []);})]);
return self;}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_zoomRightPage",
smalltalk.method({
selector: "zoomRightPage",
fn: function (){
var self=this;
smalltalk.send(self, "_closeZoomOr_", [(function(){smalltalk.send(self, "_zoomPageNo_withClass_", [self['@currentPageNo'], unescape("b-right")]);return smalltalk.send(self['@zoomRightPageAnchor'], "_addClass_", ["active"]);})]);
return self;}
}),
smalltalk.BookWidget);


smalltalk.addMethod(
"_open",
smalltalk.method({
selector: "open",
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(self, "_new", []), "_show", []);
return self;}
}),
smalltalk.BookWidget.klass);

smalltalk.addMethod(
"_reset",
smalltalk.method({
selector: "reset",
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(self, "_new", []), "_reset", []);
return self;}
}),
smalltalk.BookWidget.klass);


smalltalk.addClass('Cycle', smalltalk.Object, ['elements', 'counter'], 'AFI');
smalltalk.addMethod(
"_elements_",
smalltalk.method({
selector: "elements:",
fn: function (anArray){
var self=this;
(self['@elements']=anArray);
return self;}
}),
smalltalk.Cycle);

smalltalk.addMethod(
"_initialize",
smalltalk.method({
selector: "initialize",
fn: function (){
var self=this;
(self['@counter']=(-1));
return self;}
}),
smalltalk.Cycle);

smalltalk.addMethod(
"_next",
smalltalk.method({
selector: "next",
fn: function (){
var self=this;
(self['@counter']=((($receiver = self['@counter']).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)])));
return smalltalk.send(self['@elements'], "_at_", [((($receiver = smalltalk.send(self['@counter'], "_\\\\", [smalltalk.send(self['@elements'], "_size", [])])).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)]))]);
return self;}
}),
smalltalk.Cycle);


smalltalk.addMethod(
"_with_",
smalltalk.method({
selector: "with:",
fn: function (anArray){
var self=this;
return smalltalk.send(smalltalk.send(self, "_new", []), "_elements_", [anArray]);
return self;}
}),
smalltalk.Cycle.klass);


smalltalk.addClass('ListFilter', smalltalk.Object, ['book', 'announcer', 'jqueryInput', 'jqueryList'], 'AFI');
smalltalk.addMethod(
"_filter_withInput_",
smalltalk.method({
selector: "filter:withInput:",
fn: function (aJQueryList, aJQueryInput){
var self=this;
(self['@jqueryList']=aJQueryList);
(self['@jqueryInput']=aJQueryInput);
smalltalk.send(self['@jqueryInput'], "_keyup_", [(function(){return smalltalk.send(self, "_filterListWithInputString", []);})]);
return self;}
}),
smalltalk.ListFilter);

smalltalk.addMethod(
"_filterListWithInputString",
smalltalk.method({
selector: "filterListWithInputString",
fn: function (){
var self=this;
var searchString=nil;
var regExp=nil;
var matches=nil;
var items=nil;
(searchString=smalltalk.send(self['@jqueryInput'], "_val", []));
(regExp=new RegExp(searchString, 'i'));
(items=smalltalk.send(self['@jqueryList'], "_find_", ["li"]));
(matches=smalltalk.send(items, "_filter_", [(function(anInteger){return regExp.test($(this).text());})]));
smalltalk.send(items, "_hide", []);
smalltalk.send(matches, "_show", []);
((($receiver = smalltalk.send(searchString, "_isEmpty", [])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return smalltalk.send(self['@jqueryList'], "_removeClass_", ["filtered"]);})() : (function(){return smalltalk.send(self['@jqueryList'], "_addClass_", ["filtered"]);})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return smalltalk.send(self['@jqueryList'], "_removeClass_", ["filtered"]);}), (function(){return smalltalk.send(self['@jqueryList'], "_addClass_", ["filtered"]);})]));
return self;}
}),
smalltalk.ListFilter);


smalltalk.addMethod(
"_filter_withInput_",
smalltalk.method({
selector: "filter:withInput:",
fn: function (aJQueryList, aJQueryInput){
var self=this;
return smalltalk.send(smalltalk.send(self, "_new", []), "_filter_withInput_", [aJQueryList, aJQueryInput]);
return self;}
}),
smalltalk.ListFilter.klass);


smalltalk.addClass('Page', smalltalk.Object, ['brush', 'fullImageURL', 'thumbnailURL', 'description', 'title', 'rendered', 'foliono', 'navigatorThumbnailURL', 'book', 'downloadURL'], 'AFI');
smalltalk.addMethod(
"_book",
smalltalk.method({
selector: "book",
fn: function (){
var self=this;
return self['@book'];
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_book_",
smalltalk.method({
selector: "book:",
fn: function (aBook){
var self=this;
(self['@book']=aBook);
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_brush",
smalltalk.method({
selector: "brush",
fn: function (){
var self=this;
return self['@brush'];
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_brush_",
smalltalk.method({
selector: "brush:",
fn: function (aBrush){
var self=this;
self['@brush']=aBrush;
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_description",
smalltalk.method({
selector: "description",
fn: function (){
var self=this;
return (($receiver = self['@description']) == nil || $receiver == undefined) ? (function(){return self['@description']="";})() : $receiver;
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_description_",
smalltalk.method({
selector: "description:",
fn: function (aString){
var self=this;
self['@description']=aString;
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_downloadURL",
smalltalk.method({
selector: "downloadURL",
fn: function (){
var self=this;
return (($receiver = self['@downloadURL']) == nil || $receiver == undefined) ? (function(){return (self['@downloadURL']="");})() : $receiver;
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_downloadURL_",
smalltalk.method({
selector: "downloadURL:",
fn: function (aString){
var self=this;
(self['@downloadURL']=aString);
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_foliono",
smalltalk.method({
selector: "foliono",
fn: function (){
var self=this;
return (($receiver = self['@foliono']) == nil || $receiver == undefined) ? (function(){return (self['@foliono']="");})() : $receiver;
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_foliono_",
smalltalk.method({
selector: "foliono:",
fn: function (aString){
var self=this;
(self['@foliono']=aString);
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_fullImageURL",
smalltalk.method({
selector: "fullImageURL",
fn: function (){
var self=this;
return (($receiver = self['@fullImageURL']) == nil || $receiver == undefined) ? (function(){return self['@fullImageURL']="";})() : $receiver;
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_fullImageURL_",
smalltalk.method({
selector: "fullImageURL:",
fn: function (aString){
var self=this;
self['@fullImageURL']=aString;
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_initMetadata_",
smalltalk.method({
selector: "initMetadata:",
fn: function (aJSObject){
var self=this;
self['@description']=aJSObject.description;
self['@title']=aJSObject.book;
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_isRendered",
smalltalk.method({
selector: "isRendered",
fn: function (){
var self=this;
return (($receiver = self['@rendered']) == nil || $receiver == undefined) ? (function(){return self['@rendered']=false;})() : $receiver;
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_navigatorThumbnailURL",
smalltalk.method({
selector: "navigatorThumbnailURL",
fn: function (){
var self=this;
return (($receiver = self['@navigatorThumbnailURL']) == nil || $receiver == undefined) ? (function(){return (self['@navigatorThumbnailURL']="");})() : $receiver;
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_navigatorThumbnailURL_",
smalltalk.method({
selector: "navigatorThumbnailURL:",
fn: function (aString){
var self=this;
(self['@navigatorThumbnailURL']=aString);
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_pageNo",
smalltalk.method({
selector: "pageNo",
fn: function (){
var self=this;
return smalltalk.send(self['@book'], "_pageNo_", [self]);
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_printString",
smalltalk.method({
selector: "printString",
fn: function (){
var self=this;
return smalltalk.send((smalltalk.String || String), "_streamContents_", [(function(aStream){return (function($rec){smalltalk.send($rec, "_nextPutAll_", [smalltalk.send(self, "_printString", [], smalltalk.Object)]);smalltalk.send($rec, "_nextPutAll_", [unescape("%28")]);smalltalk.send($rec, "_nextPutAll_", [smalltalk.send(self, "_title", [])]);return smalltalk.send($rec, "_nextPutAll_", [unescape("%29")]);})(aStream);})]);
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_render",
smalltalk.method({
selector: "render",
fn: function (){
var self=this;
smalltalk.send(self, "_renderWidth_height_", [smalltalk.send(self, "_width", []), smalltalk.send(self, "_height", [])]);
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_renderWidth_height_",
smalltalk.method({
selector: "renderWidth:height:",
fn: function (width, height){
var self=this;
((($receiver = smalltalk.send(self, "_isRendered", [])).klass === smalltalk.Boolean) ? (! $receiver ? (function(){(self['@rendered']=true);return smalltalk.send(self['@brush'], "_contents_", [(function(html){return (function($rec){smalltalk.send($rec, "_width_", [width]);smalltalk.send($rec, "_height_", [height]);return smalltalk.send($rec, "_src_", [self['@thumbnailURL']]);})(smalltalk.send(html, "_img", []));})]);})() : nil) : smalltalk.send($receiver, "_ifFalse_", [(function(){(self['@rendered']=true);return smalltalk.send(self['@brush'], "_contents_", [(function(html){return (function($rec){smalltalk.send($rec, "_width_", [width]);smalltalk.send($rec, "_height_", [height]);return smalltalk.send($rec, "_src_", [self['@thumbnailURL']]);})(smalltalk.send(html, "_img", []));})]);})]));
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_reset",
smalltalk.method({
selector: "reset",
fn: function (){
var self=this;
return (self['@rendered']=false);
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_thumbnailURL",
smalltalk.method({
selector: "thumbnailURL",
fn: function (){
var self=this;
return (($receiver = self['@thumbnailURL']) == nil || $receiver == undefined) ? (function(){return self['@thumbnailURL']="";})() : $receiver;
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_thumbnailURL_",
smalltalk.method({
selector: "thumbnailURL:",
fn: function (aString){
var self=this;
self['@thumbnailURL']=aString;
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_title",
smalltalk.method({
selector: "title",
fn: function (){
var self=this;
return (($receiver = self['@title']) == nil || $receiver == undefined) ? (function(){return self['@title']="";})() : $receiver;
return self;}
}),
smalltalk.Page);

smalltalk.addMethod(
"_title_",
smalltalk.method({
selector: "title:",
fn: function (aString){
var self=this;
self['@title']=aString;
return self;}
}),
smalltalk.Page);



smalltalk.addClass('SouvignyPage', smalltalk.Page, ['book', 'icon', 'letter', 'subject'], 'AFI');
smalltalk.addMethod(
"_initMetadata_",
smalltalk.method({
selector: "initMetadata:",
fn: function (aJSObject){
var self=this;
self['@book']=aJSObject.book;
self['@icon']=aJSObject.icon;
self['@letter']=aJSObject.letter;
self['@subject']=aJSObject.subject;
self['@description']=aJSObject.description;
self['@title']=smalltalk.send((smalltalk.String || String), "_streamContents_", [(function(aStream){smalltalk.send(aStream, "_nextPutAll_", [self['@book']]);smalltalk.send(self['@icon'], "_ifNotEmpty_", [(function(){return smalltalk.send(aStream, "_nextPutAll_", [smalltalk.send(unescape("%20-%20"), "__comma", [self['@icon']])]);})]);return smalltalk.send(self['@subject'], "_ifNotEmpty_", [(function(){return smalltalk.send(aStream, "_nextPutAll_", [smalltalk.send(unescape("%20-%20"), "__comma", [self['@subject']])]);})]);})]);
return self;}
}),
smalltalk.SouvignyPage);



smalltalk.addClass('PageChangeAnnouncement', smalltalk.Object, ['page'], 'AFI');
smalltalk.addMethod(
"_page",
smalltalk.method({
selector: "page",
fn: function (){
var self=this;
return self['@page'];
return self;}
}),
smalltalk.PageChangeAnnouncement);

smalltalk.addMethod(
"_page_",
smalltalk.method({
selector: "page:",
fn: function (aPage){
var self=this;
(self['@page']=aPage);
return self;}
}),
smalltalk.PageChangeAnnouncement);


smalltalk.addMethod(
"_page_",
smalltalk.method({
selector: "page:",
fn: function (aPage){
var self=this;
return smalltalk.send(smalltalk.send(self, "_new", []), "_page_", [aPage]);
return self;}
}),
smalltalk.PageChangeAnnouncement.klass);


smalltalk.addClass('PageWidget', smalltalk.Widget, ['page', 'inControl', 'outControl', 'fitControl', 'statusControl', 'zeroControl', 'closeControl', 'closeBlock', 'rotateRightControl', 'rotation', 'downloadImageControl'], 'AFI');
smalltalk.addMethod(
"_close",
smalltalk.method({
selector: "close",
fn: function (){
var self=this;
smalltalk.send(self['@closeBlock'], "_value", []);
return self;}
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_initIViewer_",
smalltalk.method({
selector: "initIViewer:",
fn: function (aViewer){
var self=this;
smalltalk.send(self['@inControl'], "_onClick_", [(function(){return aViewer.zoom_by(1);})]);
smalltalk.send(self['@outControl'], "_onClick_", [(function(){return aViewer.zoom_by(-1);})]);
smalltalk.send(self['@fitControl'], "_onClick_", [(function(){return smalltalk.send(aViewer, "_fit", []);})]);
smalltalk.send(self['@zeroControl'], "_onClick_", [(function(){return aViewer.set_zoom(100);})]);
smalltalk.send(self['@rotateRightControl'], "_onClick_", [(function(){return smalltalk.send(self, "_rotateRight", []);})]);
smalltalk.send(self['@downloadImageControl'], "_onClick_", [(function(){return smalltalk.send((typeof window == 'undefined' ? nil : window), "_open_", [smalltalk.send(self['@page'], "_downloadURL", [])]);})]);
return self;}
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_onCloseDo_",
smalltalk.method({
selector: "onCloseDo:",
fn: function (aBlock){
var self=this;
self['@closeBlock']=aBlock;
return self;}
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_page_",
smalltalk.method({
selector: "page:",
fn: function (aPage){
var self=this;
self['@page']=aPage;
return self;}
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_renderControlsOn_",
smalltalk.method({
selector: "renderControlsOn:",
fn: function (html){
var self=this;
var addControl=nil;
(function($rec){smalltalk.send($rec, "_class_", ["controls"]);return smalltalk.send($rec, "_with_", [(function(){(addControl=(function(name){return (function($rec){smalltalk.send($rec, "_class_", [smalltalk.send(smalltalk.send("iviewer_zoom_", "__comma", [name]), "__comma", [" iviewer_common iviewer_button"])]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send(html, "_div", []));}));(self['@closeControl']=smalltalk.send(addControl, "_value_", ["close"]));smalltalk.send(self['@closeControl'], "_onClick_", [(function(){return smalltalk.send(self, "_close", []);})]);(self['@inControl']=smalltalk.send(addControl, "_value_", ["in"]));(self['@outControl']=smalltalk.send(addControl, "_value_", ["out"]));(self['@zeroControl']=smalltalk.send(addControl, "_value_", ["zero"]));(self['@fitControl']=smalltalk.send(addControl, "_value_", ["fit"]));(self['@statusControl']=smalltalk.send(addControl, "_value_", ["status"]));(self['@rotateRightControl']=smalltalk.send(addControl, "_value_", ["rotate_right"]));return (self['@downloadImageControl']=smalltalk.send(addControl, "_value_", ["download_image"]));})]);})(smalltalk.send(html, "_div", []));
return self;}
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_renderOn_",
smalltalk.method({
selector: "renderOn:",
fn: function (html){
var self=this;
var iViewer=nil;
smalltalk.send(html, "_style_", [smalltalk.send(self, "_style", [])]);
smalltalk.send(self, "_renderControlsOn_", [html]);
(iViewer=(function($rec){smalltalk.send($rec, "_class_", ["iviewer"]);return smalltalk.send($rec, "_asJQuery", []);})(smalltalk.send(html, "_div", [])));
smalltalk.send(smalltalk.send(self['@page'], "_description", []), "_ifNotEmpty_", [(function(){return smalltalk.send(iViewer, "_addClass_", ["iviewer_with_text"]);})]);
smalltalk.send(iViewer, "_iviewer_", [(function($rec){smalltalk.send($rec, "_at_put_", ["src", smalltalk.send(self['@page'], "_fullImageURL", [])]);smalltalk.send($rec, "_at_put_", ["zoom", "fit"]);smalltalk.send($rec, "_at_put_", ["zoom_min", (10)]);smalltalk.send($rec, "_at_put_", ["zoom_max", (400)]);smalltalk.send($rec, "_at_put_", ["ui_disabled", true]);smalltalk.send($rec, "_at_put_", ["initCallback", (function(aViewer){return smalltalk.send(self, "_initIViewer_", [aViewer]);})]);smalltalk.send($rec, "_at_put_", ["onZoom", (function(aString){return smalltalk.send(self, "_updateZoomStatus_", [aString]);})]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send((smalltalk.HashedCollection || HashedCollection), "_new", []))]);
smalltalk.send((function($rec){smalltalk.send($rec, "_class_", [unescape("page-desc")]);return smalltalk.send($rec, "_asJQuery", []);})(smalltalk.send(html, "_div", [])), "_html_", [smalltalk.send(self['@page'], "_description", [])]);
smalltalk.send(smalltalk.send(html, "_div", []), "_class_", ["clear"]);
return self;}
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_rotateRight",
smalltalk.method({
selector: "rotateRight",
fn: function (){
var self=this;
var rotationDeg=nil;
(self['@rotation']=((($receiver = (($receiver = self['@rotation']) == nil || $receiver == undefined) ? (function(){return (0);})() : $receiver).klass === smalltalk.Number) ? $receiver +(90) : smalltalk.send($receiver, "__plus", [(90)])));
(rotationDeg=smalltalk.send(smalltalk.send(unescape("rotate%28"), "__comma", [smalltalk.send(self['@rotation'], "_asString", [])]), "__comma", [unescape("deg%29")]));
(function($rec){smalltalk.send($rec, "_css_value_", [unescape("-ms-transform"), rotationDeg]);smalltalk.send($rec, "_css_value_", [unescape("-o-transform"), rotationDeg]);smalltalk.send($rec, "_css_value_", [unescape("-moz-transform"), rotationDeg]);return smalltalk.send($rec, "_css_value_", [unescape("-webkit-transform"), rotationDeg]);})(smalltalk.send(".iviewer img", "_asJQuery", []));
return self;}
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_style",
smalltalk.method({
selector: "style",
fn: function (){
var self=this;
return unescape("%09.b-zoom%20.controls%20%7B%0A%09%09%09%20%20height%3A%20auto%3B%0A%09%09%09%20%20padding%3A%204px%3B%0A%09%09%09%20%20margin%3A%200%204px%3B%0A%09%09%09%20%20background-color%3A%20rgb%28200%2C200%2C200%29%3B%0A%09%09%09%20%20background-color%3A%20rgba%28200%2C200%2C200%2C0.8%29%3B%0A%09%09%09%20%20overflow%3A%20hidden%3B%0A%09%09%09%20%20float%3A%20right%3B%0A%09%09%09%20%20position%3A%20absolute%3B%0A%09%09%09%20%20*position%3A%20relative%3B%0A%09%09%09%20%20z-index%3A%201%3B%0A%09%09%09%20%20text-align%3A%20center%3B%0A%09%09%09%20%20width%3A%2042px%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20right%3A%200px%3B%0A%09%09%09%7D%0A");
return self;}
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_updateZoomStatus_",
smalltalk.method({
selector: "updateZoomStatus:",
fn: function (newZoom){
var self=this;
smalltalk.send(self['@statusControl'], "_contents_", [smalltalk.send("x", "__comma", [smalltalk.send(((($receiver = newZoom).klass === smalltalk.Number) ? $receiver /(100) : smalltalk.send($receiver, "__slash", [(100)])), "_printShowingDecimalPlaces_", [(1)])])]);
return self;}
}),
smalltalk.PageWidget);



