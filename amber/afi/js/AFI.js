smalltalk.addPackage('AFI', {});
smalltalk.addClass('AbstractBookNavigatorWidget', smalltalk.Widget, ['book', 'announcer'], 'AFI');
smalltalk.addMethod(
unescape('_announcePageChange_'),
smalltalk.method({
selector: unescape('announcePageChange%3A'),
category: 'announcement',
fn: function (aPage){
var self=this;
smalltalk.send(smalltalk.send(self, "_announcer", []), "_announce_", [smalltalk.send((smalltalk.PageChangeAnnouncement || PageChangeAnnouncement), "_page_", [aPage])]);
return self;},
args: ["aPage"],
source: unescape('announcePageChange%3A%20aPage%0A%09self%20announcer%20announce%3A%20%28PageChangeAnnouncement%20page%3A%20aPage%29'),
messageSends: ["announce:", "announcer", "page:"],
referencedClasses: ["PageChangeAnnouncement"]
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
unescape('_announcer'),
smalltalk.method({
selector: unescape('announcer'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@announcer']) == nil || $receiver == undefined) ? (function(){return (self['@announcer']=smalltalk.send((smalltalk.Announcer || Announcer), "_new", []));})() : $receiver;
return self;},
args: [],
source: unescape('announcer%0A%09%5E%20announcer%20ifNil%3A%20%5Bannouncer%20%3A%3D%20Announcer%20new%5D'),
messageSends: ["ifNil:", "new"],
referencedClasses: ["Announcer"]
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
unescape('_book_'),
smalltalk.method({
selector: unescape('book%3A'),
category: 'accessing',
fn: function (aBook){
var self=this;
(self['@book']=aBook);
return self;},
args: ["aBook"],
source: unescape('book%3A%20aBook%0A%09book%20%3A%3D%20aBook'),
messageSends: [],
referencedClasses: []
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
unescape('_highlightPage_'),
smalltalk.method({
selector: unescape('highlightPage%3A'),
category: 'actions',
fn: function (aPage){
var self=this;

return self;},
args: ["aPage"],
source: unescape('highlightPage%3A%20aPage'),
messageSends: [],
referencedClasses: []
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
unescape('_onPageChangeDo_'),
smalltalk.method({
selector: unescape('onPageChangeDo%3A'),
category: 'announcement',
fn: function (aBlockWithArg){
var self=this;
smalltalk.send(smalltalk.send(self, "_announcer", []), "_on_do_", [(smalltalk.PageChangeAnnouncement || PageChangeAnnouncement), (function(aPageChangeAnnouncement){return smalltalk.send(aBlockWithArg, "_value_", [smalltalk.send(aPageChangeAnnouncement, "_page", [])]);})]);
return self;},
args: ["aBlockWithArg"],
source: unescape('onPageChangeDo%3A%20aBlockWithArg%0A%09self%20announcer%20%0A%09%09on%3A%20PageChangeAnnouncement%20%0A%09%09do%3A%20%5B%3AaPageChangeAnnouncement%7C%20%0A%09%09%09aBlockWithArg%20value%3A%20aPageChangeAnnouncement%20page%5D'),
messageSends: ["on:do:", "announcer", "value:", "page"],
referencedClasses: ["PageChangeAnnouncement"]
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
unescape('_renderOn_'),
smalltalk.method({
selector: unescape('renderOn%3A'),
category: 'rendering',
fn: function (html){
var self=this;
smalltalk.send(self, "_subclassResponsibility", []);
return self;},
args: ["html"],
source: unescape('renderOn%3A%20html%0A%09self%20subclassResponsibility'),
messageSends: ["subclassResponsibility"],
referencedClasses: []
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
unescape('_style'),
smalltalk.method({
selector: unescape('style'),
category: 'accessing',
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(unescape("%0A%0A.b-navigator%20%7B%0A%09height%3A%20500px%3B%0A%20%09width%3A%20"), "__comma", [smalltalk.send(self, "_width", [])]), "__comma", [unescape("px%3B%0A%09overflow-x%3A%20hidden%3B%0A%09overflow-y%3A%20hidden%3B%0A%09border%3A%202px%20solid%20%23666%3B%0A%7D%0A%0A.b-navigator%3Ediv%20%7B%0A%09text-align%3A%20center%3B%0A%09border-bottom%3A%201px%20solid%20%23666%3B%0A%09background-color%3A%20%23666%3B%0A%09font-size%3A%201.1em%3B%0A%7D%0A%0A.b-navigator%3Einput%20%7B%0A%09width%3A%20100%25%3B%0A%09border%3A%201px%20solid%20%23666%3B%0A%09margin%3A%200px%3B%0A%7D%0A%0A.b-navigator%20ul%20%7B%0A%09height%3A%2090%25%3B%0A%09overflow-x%3A%20hidden%3B%0A%09overflow-y%3A%20auto%3B%0A%09margin%3A%200px%3B%0A%7D%0A")]);
return self;},
args: [],
source: unescape('style%0A%09%5E%20%27%0A%0A.b-navigator%20%7B%0A%09height%3A%20500px%3B%0A%20%09width%3A%20%27%2C%20self%20width%2C%20%27px%3B%0A%09overflow-x%3A%20hidden%3B%0A%09overflow-y%3A%20hidden%3B%0A%09border%3A%202px%20solid%20%23666%3B%0A%7D%0A%0A.b-navigator%3Ediv%20%7B%0A%09text-align%3A%20center%3B%0A%09border-bottom%3A%201px%20solid%20%23666%3B%0A%09background-color%3A%20%23666%3B%0A%09font-size%3A%201.1em%3B%0A%7D%0A%0A.b-navigator%3Einput%20%7B%0A%09width%3A%20100%25%3B%0A%09border%3A%201px%20solid%20%23666%3B%0A%09margin%3A%200px%3B%0A%7D%0A%0A.b-navigator%20ul%20%7B%0A%09height%3A%2090%25%3B%0A%09overflow-x%3A%20hidden%3B%0A%09overflow-y%3A%20auto%3B%0A%09margin%3A%200px%3B%0A%7D%0A%27'),
messageSends: [unescape("%2C"), "width"],
referencedClasses: []
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
unescape('_width'),
smalltalk.method({
selector: unescape('width'),
category: 'accessing',
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(self, "_class", []), "_width", []);
return self;},
args: [],
source: unescape('width%0A%09%5E%20self%20class%20width'),
messageSends: ["width", "class"],
referencedClasses: []
}),
smalltalk.AbstractBookNavigatorWidget);


smalltalk.addMethod(
unescape('_width'),
smalltalk.method({
selector: unescape('width'),
category: 'accessing',
fn: function (){
var self=this;
return (160);
return self;},
args: [],
source: unescape('width%0A%09%5E%20160'),
messageSends: [],
referencedClasses: []
}),
smalltalk.AbstractBookNavigatorWidget.klass);


smalltalk.addClass('BookBookmarkNavigatorWidget', smalltalk.AbstractBookNavigatorWidget, ['bookmarkList'], 'AFI');
smalltalk.addMethod(
unescape('_highlightPage_'),
smalltalk.method({
selector: unescape('highlightPage%3A'),
category: 'actions',
fn: function (aPage){
var self=this;
var pageTitle=nil;
var listItemIndex=nil;
smalltalk.send(smalltalk.send(self['@bookmarkList'], "_find_", ["li"]), "_removeClass_", ["selected"]);
(pageTitle=smalltalk.send(smalltalk.send(aPage, "_title", []), "_ifEmpty_", [(function(){return smalltalk.send(smalltalk.send(self['@book'], "_pageAt_ifAbsent_", [((($receiver = smalltalk.send(aPage, "_pageNo", [])).klass === smalltalk.Number) ? $receiver -(1) : smalltalk.send($receiver, "__minus", [(1)])), (function(){return aPage;})]), "_title", []);})]));
((($receiver = smalltalk.send(pageTitle, "_isEmpty", [])).klass === smalltalk.Boolean) ? (! $receiver ? (function(){return smalltalk.send(smalltalk.send(self['@bookmarkList'], "_find_", [smalltalk.send(smalltalk.send(unescape("li%3Acontains%28%22"), "__comma", [pageTitle]), "__comma", [unescape("%22%29")])]), "_addClass_", ["selected"]);})() : nil) : smalltalk.send($receiver, "_ifFalse_", [(function(){return smalltalk.send(smalltalk.send(self['@bookmarkList'], "_find_", [smalltalk.send(smalltalk.send(unescape("li%3Acontains%28%22"), "__comma", [pageTitle]), "__comma", [unescape("%22%29")])]), "_addClass_", ["selected"]);})]));
return self;},
args: ["aPage"],
source: unescape('highlightPage%3A%20aPage%0A%09%7CpageTitle%20listItemIndex%7C%0A%09%28bookmarkList%20find%3A%20%27li%27%29%20removeClass%3A%20%27selected%27.%0A%0A%09pageTitle%20%3A%3D%20aPage%20title%20ifEmpty%3A%20%5B%20%28book%20pageAt%3A%20%28aPage%20pageNo%20-%201%29%20ifAbsent%3A%20%5BaPage%5D%29%20title%5D.%0A%0A%09pageTitle%20isEmpty%20ifFalse%3A%20%5B%0A%09%09%28bookmarkList%20find%3A%20%27li%3Acontains%28%22%27%2C%20pageTitle%2C%20%27%22%29%27%29%20addClass%3A%20%27selected%27.%0A%09%5D%20'),
messageSends: ["removeClass:", "find:", "ifEmpty:", "title", "pageAt:ifAbsent:", unescape("-"), "pageNo", "ifFalse:", "isEmpty", "addClass:", unescape("%2C")],
referencedClasses: []
}),
smalltalk.BookBookmarkNavigatorWidget);

smalltalk.addMethod(
unescape('_renderOn_'),
smalltalk.method({
selector: unescape('renderOn%3A'),
category: 'rendering',
fn: function (html){
var self=this;
smalltalk.send(html, "_style_", [smalltalk.send(self, "_style", [])]);
(function($rec){smalltalk.send($rec, "_class_", [unescape("b-navigator-bookmark%20b-navigator")]);return smalltalk.send($rec, "_with_", [(function(){var bookmarkSearchInput=nil;
smalltalk.send(html, "_div_", ["Signets"]);(bookmarkSearchInput=smalltalk.send(smalltalk.send(html, "_input", []), "_asJQuery", []));(self['@bookmarkList']=smalltalk.send(smalltalk.send(html, "_ul_", [(function(){return smalltalk.send(self, "_renderPagesOn_", [html]);})]), "_asJQuery", []));return smalltalk.send((smalltalk.ListFilter || ListFilter), "_filter_withInput_", [self['@bookmarkList'], bookmarkSearchInput]);})]);})(smalltalk.send(html, "_div", []));
return self;},
args: ["html"],
source: unescape('renderOn%3A%20html%0A%09html%20style%3A%20self%20style.%0A%09html%20div%20%0A%09%09class%3A%20%27b-navigator-bookmark%20b-navigator%27%3B%0A%09%09with%3A%20%5B%20%7CbookmarkSearchInput%20%7C%0A%09%09%09html%20div%3A%20%27Signets%27.%0A%0A%09%09%09bookmarkSearchInput%20%3A%3D%20html%20input%20asJQuery.%0A%09%09%09bookmarkList%20%3A%3D%20%28html%20ul%3A%20%5Bself%20renderPagesOn%3A%20html%20%5D%29%20asJQuery.%0A%0A%09%09%09ListFilter%20filter%3A%20bookmarkList%20withInput%3A%20bookmarkSearchInput.%0A%09%09%5D'),
messageSends: ["style:", "style", "class:", "with:", "div:", "asJQuery", "input", "ul:", "renderPagesOn:", "filter:withInput:", "div"],
referencedClasses: ["ListFilter"]
}),
smalltalk.BookBookmarkNavigatorWidget);

smalltalk.addMethod(
unescape('_renderPagesOn_'),
smalltalk.method({
selector: unescape('renderPagesOn%3A'),
category: 'rendering',
fn: function (html){
var self=this;
smalltalk.send(smalltalk.send(self['@book'], "_pagesWithTitle", []), "_do_", [(function(aPage){return (function($rec){smalltalk.send($rec, "_with_", [smalltalk.send(aPage, "_title", [])]);return smalltalk.send($rec, "_onClick_", [(function(){return smalltalk.send(self, "_announcePageChange_", [aPage]);})]);})(smalltalk.send(html, "_li", []));})]);
return self;},
args: ["html"],
source: unescape('renderPagesOn%3A%20html%0A%09book%20pagesWithTitle%20do%3A%20%5B%3AaPage%7C%0A%09%09html%20li%0A%09%09%09with%3A%20aPage%20title%3B%0A%09%09%09onClick%3A%20%5Bself%20announcePageChange%3A%20aPage%5D%0A%09%5D'),
messageSends: ["do:", "pagesWithTitle", "with:", "title", "onClick:", "announcePageChange:", "li"],
referencedClasses: []
}),
smalltalk.BookBookmarkNavigatorWidget);

smalltalk.addMethod(
unescape('_style'),
smalltalk.method({
selector: unescape('style'),
category: 'accessing',
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(self, "_style", [], smalltalk.AbstractBookNavigatorWidget), "__comma", [unescape("%0A.b-navigator-bookmark%20%7B%0A%09border-top-right-radius%3A%2010px%3B%0A%09border-bottom-right-radius%3A%2010px%3B%0A%09border-left%3A%200px%3B%0A%09margin-left%3A%200px%3B%0A%09margin-right%3A%2010px%3B%0A%09float%3A%20left%3B%0A%7D%0A%0A.b-navigator-bookmark%20ul%20%7B%0A%09list-style%3A%20square%3B%0A%09padding%3A%200px%2010px%200px%2015px%3B%0A%7D%0A%0A.b-navigator-bookmark%20li%20%7B%0A%09margin%3A%205px%3B%0A%09padding%3A%200px%3B%0A%09text-align%3A%20left%3B%0A%09cursor%3A%20pointer%3B%0A%09-webkit-transition%3A%20all%200.3s%3B%0A%09-moz-transition%3A%20all%200.3s%3B%0A%7D%0A%0A.b-navigator-bookmark%20li.selected%20%7B%0A%09text-decoration%3A%20underline%0A%7D%0A%0A.b-navigator-bookmark%20li%3Ahover%20%7B%0A%09color%3A%20%23aaa%3B%0A%7D")]);
return self;},
args: [],
source: unescape('style%0A%09%5E%20super%20style%2C%20%27%0A.b-navigator-bookmark%20%7B%0A%09border-top-right-radius%3A%2010px%3B%0A%09border-bottom-right-radius%3A%2010px%3B%0A%09border-left%3A%200px%3B%0A%09margin-left%3A%200px%3B%0A%09margin-right%3A%2010px%3B%0A%09float%3A%20left%3B%0A%7D%0A%0A.b-navigator-bookmark%20ul%20%7B%0A%09list-style%3A%20square%3B%0A%09padding%3A%200px%2010px%200px%2015px%3B%0A%7D%0A%0A.b-navigator-bookmark%20li%20%7B%0A%09margin%3A%205px%3B%0A%09padding%3A%200px%3B%0A%09text-align%3A%20left%3B%0A%09cursor%3A%20pointer%3B%0A%09-webkit-transition%3A%20all%200.3s%3B%0A%09-moz-transition%3A%20all%200.3s%3B%0A%7D%0A%0A.b-navigator-bookmark%20li.selected%20%7B%0A%09text-decoration%3A%20underline%0A%7D%0A%0A.b-navigator-bookmark%20li%3Ahover%20%7B%0A%09color%3A%20%23aaa%3B%0A%7D%27'),
messageSends: [unescape("%2C"), "style"],
referencedClasses: []
}),
smalltalk.BookBookmarkNavigatorWidget);



smalltalk.addClass('BookThumbnailNavigatorWidget', smalltalk.AbstractBookNavigatorWidget, ['bookmarkList'], 'AFI');
smalltalk.addMethod(
unescape('_highlightPage_'),
smalltalk.method({
selector: unescape('highlightPage%3A'),
category: 'actions',
fn: function (aPage){
var self=this;
var thumbnail=nil;
var listItemIndex=nil;
(listItemIndex=smalltalk.send((0), "_max_", [((($receiver = smalltalk.send(aPage, "_pageNo", [])).klass === smalltalk.Number) ? $receiver -(2) : smalltalk.send($receiver, "__minus", [(2)]))]));
(thumbnail=smalltalk.send(smalltalk.send(self['@bookmarkList'], "_find_", ["li"]), "_get_", [listItemIndex]));
smalltalk.send(self['@bookmarkList'], "_scrollTop_", [((($receiver = smalltalk.send(thumbnail, "_offsetTop", [])).klass === smalltalk.Number) ? $receiver -((($receiver = smalltalk.send(self['@bookmarkList'], "_height", [])).klass === smalltalk.Number) ? $receiver /(2) : smalltalk.send($receiver, "__slash", [(2)])) : smalltalk.send($receiver, "__minus", [((($receiver = smalltalk.send(self['@bookmarkList'], "_height", [])).klass === smalltalk.Number) ? $receiver /(2) : smalltalk.send($receiver, "__slash", [(2)]))]))]);
smalltalk.send(smalltalk.send(self['@bookmarkList'], "_find_", ["li"]), "_removeClass_", ["selected"]);
smalltalk.send(smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery_", [thumbnail]), "_addClass_", ["selected"]);
return self;},
args: ["aPage"],
source: unescape('highlightPage%3A%20aPage%0A%09%7Cthumbnail%20listItemIndex%7C%0A%20%20%20%20%20%20%20%20listItemIndex%20%3A%3D%200%20max%3A%20%28aPage%20pageNo%20-%202%29.%0A%20%20%20%20%20%20%20%20thumbnail%20%3A%3D%20%28bookmarkList%20find%3A%20%27li%27%29%20get%3A%20listItemIndex.%0A%09bookmarkList%20scrollTop%3A%20%28thumbnail%20offsetTop%20-%20%28bookmarkList%20height%20/%202%29%29.%0A%09%28bookmarkList%20find%3A%20%27li%27%29%20removeClass%3A%20%27selected%27.%0A%09%28window%20jQuery%3A%20thumbnail%29%20addClass%3A%20%27selected%27.'),
messageSends: ["max:", unescape("-"), "pageNo", "get:", "find:", "scrollTop:", "offsetTop", unescape("/"), "height", "removeClass:", "addClass:", "jQuery:"],
referencedClasses: []
}),
smalltalk.BookThumbnailNavigatorWidget);

smalltalk.addMethod(
unescape('_renderOn_'),
smalltalk.method({
selector: unescape('renderOn%3A'),
category: 'rendering',
fn: function (html){
var self=this;
smalltalk.send(html, "_style_", [smalltalk.send(self, "_style", [])]);
(function($rec){smalltalk.send($rec, "_class_", [unescape("b-navigator-thumbnail%20%20b-navigator")]);return smalltalk.send($rec, "_with_", [(function(){var bookmarkSearchInput=nil;
smalltalk.send(html, "_div_", ["Folios"]);(bookmarkSearchInput=smalltalk.send(smalltalk.send(html, "_input", []), "_asJQuery", []));(self['@bookmarkList']=(function($rec){smalltalk.send($rec, "_with_", [(function(){return smalltalk.send(self, "_renderPagesOn_", [html]);})]);return smalltalk.send($rec, "_asJQuery", []);})(smalltalk.send(html, "_ul", [])));return smalltalk.send((smalltalk.ListFilter || ListFilter), "_filter_withInput_", [self['@bookmarkList'], bookmarkSearchInput]);})]);})(smalltalk.send(html, "_div", []));
return self;},
args: ["html"],
source: unescape('renderOn%3A%20html%0A%09html%20style%3A%20self%20style.%0A%09html%20div%20%0A%09%09class%3A%20%27b-navigator-thumbnail%20%20b-navigator%27%3B%0A%09%09with%3A%20%5B%20%7CbookmarkSearchInput%7C%0A%09%09%09html%20div%3A%20%27Folios%27.%0A%0A%09%09%09bookmarkSearchInput%20%3A%3D%20html%20input%20asJQuery.%0A%09%09%09bookmarkList%20%3A%3D%20html%20ul%20%0A%09%09%09%09%09%09%09%09with%3A%20%5Bself%20renderPagesOn%3A%20html%20%5D%3B%20%0A%09%09%09%09%09%09%09%09asJQuery.%0A%0A%09%09%09ListFilter%20filter%3A%20bookmarkList%20withInput%3A%20bookmarkSearchInput.%0A%09%5D'),
messageSends: ["style:", "style", "class:", "with:", "div:", "asJQuery", "input", "renderPagesOn:", "ul", "filter:withInput:", "div"],
referencedClasses: ["ListFilter"]
}),
smalltalk.BookThumbnailNavigatorWidget);

smalltalk.addMethod(
unescape('_renderPagesOn_'),
smalltalk.method({
selector: unescape('renderPagesOn%3A'),
category: 'rendering',
fn: function (html){
var self=this;
var cycle=nil;
(cycle=smalltalk.send((smalltalk.Cycle || Cycle), "_with_", [["odd", "even"]]));
smalltalk.send(smalltalk.send(self['@book'], "_pages", []), "_do_", [(function(aPage){return (function($rec){smalltalk.send($rec, "_class_", [smalltalk.send(cycle, "_next", [])]);smalltalk.send($rec, "_with_", [(function(){return smalltalk.send(html, "_div_", [(function(){smalltalk.send(html, "_div_", [smalltalk.send(aPage, "_foliono", [])]);return smalltalk.send(smalltalk.send(html, "_img", []), "_src_", [smalltalk.send(aPage, "_navigatorThumbnailURL", [])]);})]);})]);return smalltalk.send($rec, "_onClick_", [(function(){return smalltalk.send(self, "_announcePageChange_", [aPage]);})]);})(smalltalk.send(html, "_li", []));})]);
return self;},
args: ["html"],
source: unescape('renderPagesOn%3A%20html%0A%09%7Ccycle%7C%0A%09cycle%20%3A%3D%20Cycle%20with%3A%20%23%28%27odd%27%20%27even%27%29.%0A%0A%09book%20pages%20do%3A%20%5B%3AaPage%7C%0A%09%09html%20li%0A%09%09%09class%3A%20cycle%20next%3B%0A%09%09%09with%3A%20%5B%20%09html%20div%3A%20%5B%09html%20div%3A%20aPage%20foliono.%0A%09%09%09%09%09%09%20%09%09html%20img%20src%3A%20aPage%20navigatorThumbnailURL%5D%20%5D%3B%0A%09%09%09onClick%3A%20%5Bself%20announcePageChange%3A%20aPage%5D%0A%09%5D'),
messageSends: ["with:", "do:", "pages", "class:", "next", "div:", "foliono", "src:", "img", "navigatorThumbnailURL", "onClick:", "announcePageChange:", "li"],
referencedClasses: ["Cycle"]
}),
smalltalk.BookThumbnailNavigatorWidget);

smalltalk.addMethod(
unescape('_style'),
smalltalk.method({
selector: unescape('style'),
category: 'css',
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(self, "_style", [], smalltalk.AbstractBookNavigatorWidget), "__comma", [unescape("%0A.b-navigator-thumbnail%20%7B%0A%09border-top-left-radius%3A%2010px%3B%0A%09border-bottom-left-radius%3A%2010px%3B%0A%09border-right%3A%200px%3B%0A%09margin-left%3A%2010px%3B%0A%09margin-right%3A%200px%3B%0A%09float%3A%20right%3B%0A%7D%0A%0A.b-navigator-thumbnail%20ul%20%7B%0A%09list-style%3A%20none%3B%0A%09padding%3A%200px%3B%0A%09float%3A%20right%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%20%7B%0A%09float%3A%20left%3B%0A%09margin%3A%205px%3B%0A%09display%3A%20block%3B%0A%09overflow%3A%20hidden%3B%0A%09height%3A%2070px%3B%0A%09width%3A%2050px%3B%0A%09text-align%3A%20center%3B%0A%09cursor%3A%20pointer%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ediv%3Ediv%7B%0A%09display%3A%20none%3B%0A%09position%3A%20relative%3B%0A%09z-index%3A%202%3B%0A%09background-color%3A%20black%3B%0A%09font-weight%3A%20bold%3B%0A%09font-size%3A%200.9em%3B%0A%7D%0A%0A%0A.b-navigator-thumbnail%20li.selected%20img%2C%0A.b-navigator-thumbnail%20li.selected%20+%20li.odd%20img%2C%0A.b-navigator-thumbnail%20.filtered%20li%20img%2C%0A.b-navigator-thumbnail%20li%3Ahover%20img%20%7B%0A%09opacity%3A%201%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ahover%20%7B%0A%09overflow%3A%20visible%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ahover%3Ediv%3Ediv%7B%0A%09display%3A%20block%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ediv%20%7B%0A%09width%3A%2050px%3B%0A%20%20%09-webkit-transition%3A%20all%200.1s%20ease-out%3B%0A%20%09-moz-transition%3A%20all%200.1s%20ease-out%3B%0A%7D%0A%0A%0A.b-navigator-thumbnail%20li%3Ahover%3Ediv%20%7B%0A%20%20%20width%3A%20100px%3B%0A%20%20%20position%3A%20relative%3B%0A%20%20%20box-shadow%3A%200px%200px%2020px%20black%3B%0A%20%20%20z-index%3A%2030%3B%0A%7D%0A%0A%0A.b-navigator-thumbnail%20li.odd%3Ahover%3Ediv%20%7B%0A%20%20%20margin-left%3A%20-40px%3B%0A%7D%0A%0A.b-navigator-thumbnail%20ul%20li%3Afirst-child%3Ahover%3Ediv%20%7B%0A%20%20%20margin-right%3A%20-40px%3B%0A%20%20%20margin-left%3A%200px%3B%0A%20%20%20margin-top%3A%200px%3B%0A%7D%0A%0A.b-navigator-thumbnail%20.filtered%20li.odd%3Ahover%3Ediv%20%7B%0A%20%20%20margin-left%3A%200px%3B%0A%7D%0A%0A.b-navigator-thumbnail%20.filtered%20li%20%7B%0A%20%20%20width%3A%20100%25%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%20img%20%7B%0A%20%20%09width%3A%20100%25%3B%0A%09display%3A%20block%3B%0A%09opacity%3A%200.6%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Afirst-child%20+%20li%7B%0A%09clear%3A%20left%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%20img%20%7B%0A%09cursor%3A%20pointer%3B%0A%7D%0A")]);
return self;},
args: [],
source: unescape('style%0A%09%5E%20super%20style%2C%20%27%0A.b-navigator-thumbnail%20%7B%0A%09border-top-left-radius%3A%2010px%3B%0A%09border-bottom-left-radius%3A%2010px%3B%0A%09border-right%3A%200px%3B%0A%09margin-left%3A%2010px%3B%0A%09margin-right%3A%200px%3B%0A%09float%3A%20right%3B%0A%7D%0A%0A.b-navigator-thumbnail%20ul%20%7B%0A%09list-style%3A%20none%3B%0A%09padding%3A%200px%3B%0A%09float%3A%20right%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%20%7B%0A%09float%3A%20left%3B%0A%09margin%3A%205px%3B%0A%09display%3A%20block%3B%0A%09overflow%3A%20hidden%3B%0A%09height%3A%2070px%3B%0A%09width%3A%2050px%3B%0A%09text-align%3A%20center%3B%0A%09cursor%3A%20pointer%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ediv%3Ediv%7B%0A%09display%3A%20none%3B%0A%09position%3A%20relative%3B%0A%09z-index%3A%202%3B%0A%09background-color%3A%20black%3B%0A%09font-weight%3A%20bold%3B%0A%09font-size%3A%200.9em%3B%0A%7D%0A%0A%0A.b-navigator-thumbnail%20li.selected%20img%2C%0A.b-navigator-thumbnail%20li.selected%20+%20li.odd%20img%2C%0A.b-navigator-thumbnail%20.filtered%20li%20img%2C%0A.b-navigator-thumbnail%20li%3Ahover%20img%20%7B%0A%09opacity%3A%201%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ahover%20%7B%0A%09overflow%3A%20visible%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ahover%3Ediv%3Ediv%7B%0A%09display%3A%20block%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ediv%20%7B%0A%09width%3A%2050px%3B%0A%20%20%09-webkit-transition%3A%20all%200.1s%20ease-out%3B%0A%20%09-moz-transition%3A%20all%200.1s%20ease-out%3B%0A%7D%0A%0A%0A.b-navigator-thumbnail%20li%3Ahover%3Ediv%20%7B%0A%20%20%20width%3A%20100px%3B%0A%20%20%20position%3A%20relative%3B%0A%20%20%20box-shadow%3A%200px%200px%2020px%20black%3B%0A%20%20%20z-index%3A%2030%3B%0A%7D%0A%0A%0A.b-navigator-thumbnail%20li.odd%3Ahover%3Ediv%20%7B%0A%20%20%20margin-left%3A%20-40px%3B%0A%7D%0A%0A.b-navigator-thumbnail%20ul%20li%3Afirst-child%3Ahover%3Ediv%20%7B%0A%20%20%20margin-right%3A%20-40px%3B%0A%20%20%20margin-left%3A%200px%3B%0A%20%20%20margin-top%3A%200px%3B%0A%7D%0A%0A.b-navigator-thumbnail%20.filtered%20li.odd%3Ahover%3Ediv%20%7B%0A%20%20%20margin-left%3A%200px%3B%0A%7D%0A%0A.b-navigator-thumbnail%20.filtered%20li%20%7B%0A%20%20%20width%3A%20100%25%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%20img%20%7B%0A%20%20%09width%3A%20100%25%3B%0A%09display%3A%20block%3B%0A%09opacity%3A%200.6%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Afirst-child%20+%20li%7B%0A%09clear%3A%20left%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%20img%20%7B%0A%09cursor%3A%20pointer%3B%0A%7D%0A%27'),
messageSends: [unescape("%2C"), "style"],
referencedClasses: []
}),
smalltalk.BookThumbnailNavigatorWidget);



smalltalk.addClass('Ajax', smalltalk.Object, ['url', 'settings', 'options', 'ajaxRequest'], 'AFI');
smalltalk.addMethod(
unescape('_abort'),
smalltalk.method({
selector: unescape('abort'),
category: 'actions',
fn: function (){
var self=this;
(($receiver = self['@ajaxRequest']) != nil && $receiver != undefined) ? (function(){return smalltalk.send(self['@ajaxRequest'], "_abort", []);})() : nil;
return self;},
args: [],
source: unescape('abort%0A%09ajaxRequest%20ifNotNil%3A%20%5BajaxRequest%20abort%5D'),
messageSends: ["ifNotNil:", "abort"],
referencedClasses: []
}),
smalltalk.Ajax);

smalltalk.addMethod(
unescape('_onCompleteDo_'),
smalltalk.method({
selector: unescape('onCompleteDo%3A'),
category: 'callback',
fn: function (aBlock){
var self=this;
smalltalk.send(smalltalk.send(self, "_options", []), "_at_put_", ["complete", aBlock]);
return self;},
args: ["aBlock"],
source: unescape('onCompleteDo%3A%20aBlock%0A%09%22A%20block%20to%20be%20called%20when%20the%20request%20finishes%20%28after%20success%20and%20error%20callbacks%20are%20executed%29.%20Block%20arguments%3A%20jqXHR%2C%20textStatus%22%0A%09self%20options%20at%3A%20%27complete%27%20put%3A%20aBlock'),
messageSends: ["at:put:", "options"],
referencedClasses: []
}),
smalltalk.Ajax);

smalltalk.addMethod(
unescape('_onErrorDo_'),
smalltalk.method({
selector: unescape('onErrorDo%3A'),
category: 'callback',
fn: function (aBlock){
var self=this;
smalltalk.send(smalltalk.send(self, "_options", []), "_at_put_", ["error", aBlock]);
return self;},
args: ["aBlock"],
source: unescape('onErrorDo%3A%20aBlock%0A%09%22A%20block%20to%20be%20called%20if%20the%20request%20fails.Block%20arguments%3A%20jqXHR%2C%20textStatus%2C%20errorThrown%22%0A%09self%20options%20at%3A%20%27error%27%20put%3A%20aBlock'),
messageSends: ["at:put:", "options"],
referencedClasses: []
}),
smalltalk.Ajax);

smalltalk.addMethod(
unescape('_onSuccessDo_'),
smalltalk.method({
selector: unescape('onSuccessDo%3A'),
category: 'callback',
fn: function (aBlock){
var self=this;
smalltalk.send(smalltalk.send(self, "_options", []), "_at_put_", ["success", aBlock]);
return self;},
args: ["aBlock"],
source: unescape('onSuccessDo%3A%20aBlock%0A%09%22Set%20action%20to%20execute%20when%20Ajax%20request%20is%20successful.%20Pass%20received%20data%20as%20block%20argument.%20Block%20arguments%3A%20data%2C%20textStatus%2C%20jqXHR%22%0A%09self%20options%20at%3A%20%27success%27%20put%3A%20aBlock'),
messageSends: ["at:put:", "options"],
referencedClasses: []
}),
smalltalk.Ajax);

smalltalk.addMethod(
unescape('_options'),
smalltalk.method({
selector: unescape('options'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@options']) == nil || $receiver == undefined) ? (function(){return self['@options']=smalltalk.send((smalltalk.HashedCollection || HashedCollection), "_new", []);})() : $receiver;
return self;},
args: [],
source: unescape('options%0A%09%5E%20options%20ifNil%3A%20%5Boptions%20%3A%3D%20HashedCollection%20new%20%5D'),
messageSends: ["ifNil:", "new"],
referencedClasses: ["HashedCollection"]
}),
smalltalk.Ajax);

smalltalk.addMethod(
unescape('_send'),
smalltalk.method({
selector: unescape('send'),
category: 'actions',
fn: function (){
var self=this;
(self['@ajaxRequest']=smalltalk.send((typeof jQuery == 'undefined' ? nil : jQuery), "_ajax_options_", [self['@url'], self['@options']]));
return self;},
args: [],
source: unescape('send%0A%09ajaxRequest%20%3A%3D%20jQuery%20ajax%3A%20url%20options%3A%20options.'),
messageSends: ["ajax:options:"],
referencedClasses: []
}),
smalltalk.Ajax);

smalltalk.addMethod(
unescape('_url_'),
smalltalk.method({
selector: unescape('url%3A'),
category: 'accessing',
fn: function (aString){
var self=this;
self['@url']=aString;
return self;},
args: ["aString"],
source: unescape('url%3A%20aString%0A%09url%20%3A%3D%20aString'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Ajax);


smalltalk.addMethod(
unescape('_url_'),
smalltalk.method({
selector: unescape('url%3A'),
category: 'initialize',
fn: function (aString){
var self=this;
return (function($rec){smalltalk.send($rec, "_url_", [aString]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send(self, "_new", []));
return self;},
args: ["aString"],
source: unescape('url%3A%20aString%0A%09%5E%20self%20new%20%0A%09%09url%3A%20aString%3B%0A%09%09yourself'),
messageSends: ["url:", "yourself", "new"],
referencedClasses: []
}),
smalltalk.Ajax.klass);


smalltalk.addClass('BibNumAlbum', smalltalk.Object, ['container', 'ajax', 'url', 'scriptsRoot', 'bookWidget'], 'AFI');
smalltalk.addMethod(
unescape('_ajax'),
smalltalk.method({
selector: unescape('ajax'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@ajax']) == nil || $receiver == undefined) ? (function(){return self['@ajax']=smalltalk.send((smalltalk.Ajax || Ajax), "_url_", [smalltalk.send(self, "_url", [])]);})() : $receiver;
return self;},
args: [],
source: unescape('ajax%0A%09%5E%20ajax%20ifNil%3A%20%5Bajax%20%3A%3D%20Ajax%20url%3A%20self%20url%5D'),
messageSends: ["ifNil:", "url:", "url"],
referencedClasses: ["Ajax"]
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
unescape('_ajax_'),
smalltalk.method({
selector: unescape('ajax%3A'),
category: 'accessing',
fn: function (anAjax){
var self=this;
self['@ajax']=anAjax;
return self;},
args: ["anAjax"],
source: unescape('ajax%3A%20anAjax%0A%09ajax%20%3A%3D%20anAjax'),
messageSends: [],
referencedClasses: []
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
unescape('_container'),
smalltalk.method({
selector: unescape('container'),
category: 'accessing',
fn: function (){
var self=this;
return self['@container'];
return self;},
args: [],
source: unescape('container%0A%09%5E%20container'),
messageSends: [],
referencedClasses: []
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
unescape('_container_'),
smalltalk.method({
selector: unescape('container%3A'),
category: 'accessing',
fn: function (aJQuery){
var self=this;
self['@container']=aJQuery;
return self;},
args: ["aJQuery"],
source: unescape('container%3A%20aJQuery%0A%09container%20%3A%3D%20aJQuery'),
messageSends: [],
referencedClasses: []
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
unescape('_load'),
smalltalk.method({
selector: unescape('load'),
category: 'loading',
fn: function (){
var self=this;
(self['@bookWidget']=(function($rec){smalltalk.send($rec, "_loader_", [(function($rec){smalltalk.send($rec, "_ajax_", [smalltalk.send(self, "_ajax", [])]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send((smalltalk.BibNumLoader || BibNumLoader), "_new", []))]);smalltalk.send($rec, "_scriptsRoot_", [smalltalk.send(self, "_scriptsRoot", [])]);return smalltalk.send($rec, "_appendToJQuery_", [smalltalk.send(self, "_container", [])]);})(smalltalk.send((smalltalk.BookWidget || BookWidget), "_new", [])));
return self;},
args: [],
source: unescape('load%0A%09bookWidget%20%3A%3D%20BookWidget%20new%20%0A%09%09%09%09%09loader%3A%20%28BibNumLoader%20new%0A%20%20%20%20%20%20%20%20%20%20%09%09%09%09%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09ajax%3A%20self%20ajax%3B%0A%20%20%20%20%20%20%20%20%20%09%09%20%20%20%20%20%20%20%20%20%20%20%20%09%09%20%20%20%09%09yourself%29%3B%0A%20%20%20%20%20%20%20%09%09%09%09%20%20%20%20%20%20%20%20scriptsRoot%3A%20self%20scriptsRoot%3B%0A%09%09%09%09%09appendToJQuery%3A%20self%20container'),
messageSends: ["loader:", "ajax:", "ajax", "yourself", "new", "scriptsRoot:", "scriptsRoot", "appendToJQuery:", "container"],
referencedClasses: ["BibNumLoader", "BookWidget"]
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
unescape('_pages'),
smalltalk.method({
selector: unescape('pages'),
category: 'accessing',
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(self['@bookWidget'], "_book", []), "_pages", []);
return self;},
args: [],
source: unescape('pages%0A%09%5E%20bookWidget%20book%20pages'),
messageSends: ["pages", "book"],
referencedClasses: []
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
unescape('_scriptsRoot'),
smalltalk.method({
selector: unescape('scriptsRoot'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@scriptsRoot']) == nil || $receiver == undefined) ? (function(){return self['@scriptsRoot']="";})() : $receiver;
return self;},
args: [],
source: unescape('scriptsRoot%0A%09%5E%20scriptsRoot%20ifNil%3A%20%5BscriptsRoot%20%3A%3D%20%27%27%5D%0A%09'),
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
unescape('_scriptsRoot_'),
smalltalk.method({
selector: unescape('scriptsRoot%3A'),
category: 'accessing',
fn: function (anUrl){
var self=this;
self['@scriptsRoot']=anUrl;
return self;},
args: ["anUrl"],
source: unescape('scriptsRoot%3A%20anUrl%0A%09scriptsRoot%20%3A%3D%20anUrl%0A%09'),
messageSends: [],
referencedClasses: []
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
unescape('_url'),
smalltalk.method({
selector: unescape('url'),
category: 'accessing',
fn: function (){
var self=this;
return self['@url'];
return self;},
args: [],
source: unescape('url%0A%09%5E%20url'),
messageSends: [],
referencedClasses: []
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
unescape('_url_'),
smalltalk.method({
selector: unescape('url%3A'),
category: 'accessing',
fn: function (aString){
var self=this;
self['@url']=aString;
return self;},
args: ["aString"],
source: unescape('url%3A%20aString%0A%09url%20%3A%3D%20aString'),
messageSends: [],
referencedClasses: []
}),
smalltalk.BibNumAlbum);


smalltalk.addMethod(
unescape('_load_in_'),
smalltalk.method({
selector: unescape('load%3Ain%3A'),
category: 'instance creation',
fn: function (anURLForJSON, aJQuerySelector){
var self=this;
return (function($rec){smalltalk.send($rec, "_url_", [anURLForJSON]);smalltalk.send($rec, "_container_", [smalltalk.send(aJQuerySelector, "_asJQuery", [])]);return smalltalk.send($rec, "_load", []);})(smalltalk.send(self, "_new", []));
return self;},
args: ["anURLForJSON", "aJQuerySelector"],
source: unescape('load%3A%20anURLForJSON%20%20in%3A%20aJQuerySelector%0A%09%5E%20%20self%20new%0A%09%09url%3A%20anURLForJSON%3B%0A%09%09container%3A%20aJQuerySelector%20asJQuery%3B%20%0A%09%09load.'),
messageSends: ["url:", "container:", "asJQuery", "load", "new"],
referencedClasses: []
}),
smalltalk.BibNumAlbum.klass);

smalltalk.addMethod(
unescape('_load_in_scriptsRoot_'),
smalltalk.method({
selector: unescape('load%3Ain%3AscriptsRoot%3A'),
category: 'instance creation',
fn: function (anURLForJSON, aJQuerySelector, anURL){
var self=this;
return (function($rec){smalltalk.send($rec, "_url_", [anURLForJSON]);smalltalk.send($rec, "_container_", [smalltalk.send(aJQuerySelector, "_asJQuery", [])]);smalltalk.send($rec, "_scriptsRoot_", [anURL]);return smalltalk.send($rec, "_load", []);})(smalltalk.send(self, "_new", []));
return self;},
args: ["anURLForJSON", "aJQuerySelector", "anURL"],
source: unescape('load%3A%20anURLForJSON%20%20in%3A%20aJQuerySelector%20scriptsRoot%3A%20anURL%0A%09%5E%20%20self%20new%0A%09%09url%3A%20anURLForJSON%3B%0A%09%09container%3A%20aJQuerySelector%20asJQuery%3B%20%0A%09%09scriptsRoot%3A%20anURL%3B%0A%09%09load.'),
messageSends: ["url:", "container:", "asJQuery", "scriptsRoot:", "load", "new"],
referencedClasses: []
}),
smalltalk.BibNumAlbum.klass);


smalltalk.addClass('BibNumLoader', smalltalk.Object, ['ajax'], 'AFI');
smalltalk.addMethod(
unescape('_abort'),
smalltalk.method({
selector: unescape('abort'),
category: 'loading',
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(self, "_ajax", []), "_abort", []);
return self;},
args: [],
source: unescape('abort%0A%09self%20ajax%20abort'),
messageSends: ["abort", "ajax"],
referencedClasses: []
}),
smalltalk.BibNumLoader);

smalltalk.addMethod(
unescape('_ajax'),
smalltalk.method({
selector: unescape('ajax'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@ajax']) == nil || $receiver == undefined) ? (function(){return self['@ajax']=smalltalk.send((smalltalk.Ajax || Ajax), "_new", []);})() : $receiver;
return self;},
args: [],
source: unescape('ajax%0A%09%5E%20ajax%20ifNil%3A%20%5Bajax%20%3A%3D%20Ajax%20new%5D'),
messageSends: ["ifNil:", "new"],
referencedClasses: ["Ajax"]
}),
smalltalk.BibNumLoader);

smalltalk.addMethod(
unescape('_ajax_'),
smalltalk.method({
selector: unescape('ajax%3A'),
category: 'accessing',
fn: function (anAjax){
var self=this;
self['@ajax']=anAjax;
return self;},
args: ["anAjax"],
source: unescape('ajax%3A%20anAjax%0A%09ajax%20%3A%3D%20anAjax'),
messageSends: [],
referencedClasses: []
}),
smalltalk.BibNumLoader);

smalltalk.addMethod(
unescape('_buildBookFromJSon_'),
smalltalk.method({
selector: unescape('buildBookFromJSon%3A'),
category: 'loading',
fn: function (aJSONObject){
var self=this;
var book=nil;
var album=nil;
(album=smalltalk.send(aJSONObject, "_album", []));
(book=(function($rec){smalltalk.send($rec, "_title_", [smalltalk.send(album, "_titre", [])]);smalltalk.send($rec, "_width_", [smalltalk.send(album, "_width", [])]);smalltalk.send($rec, "_height_", [smalltalk.send(album, "_height", [])]);smalltalk.send($rec, "_downloadUrl_", [smalltalk.send(album, "_at_", ["download_url"])]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send((smalltalk.Book || Book), "_new", [])));
smalltalk.send(smalltalk.send(album, "_ressources", []), "_do_", [(function(aRessource){return (function($rec){smalltalk.send($rec, "_title_", [smalltalk.send(aRessource, "_titre", [])]);smalltalk.send($rec, "_description_", [smalltalk.send(aRessource, "_description", [])]);smalltalk.send($rec, "_thumbnailURL_", [smalltalk.send(aRessource, "_thumbnail", [])]);smalltalk.send($rec, "_fullImageURL_", [smalltalk.send(aRessource, "_original", [])]);smalltalk.send($rec, "_foliono_", [smalltalk.send(aRessource, "_foliono", [])]);return smalltalk.send($rec, "_navigatorThumbnailURL_", [smalltalk.send(aRessource, "_at_", ["navigator_thumbnail"])]);})(smalltalk.send(book, "_newPage", []));})]);
return book;
return self;},
args: ["aJSONObject"],
source: unescape('buildBookFromJSon%3A%20aJSONObject%0A%09%7Cbook%20album%7C%0A%09album%20%3A%3D%20aJSONObject%20album.%0A%09book%20%3A%3D%20Book%20new%0A%09%09%09%09title%3A%20album%20titre%3B%0A%09%09%09%09width%3A%20album%20width%3B%0A%09%09%09%09height%3A%20album%20height%3B%0A%09%09%09%09downloadUrl%3A%20%28album%20at%3A%20%27download_url%27%29%3B%0A%09%09%09%09yourself.%0A%09album%20ressources%20do%3A%20%5B%3AaRessource%7C%20%0A%20%20%20%20%20%20%20%20%09%09%09%09%09book%20newPage%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09title%3A%20aRessource%20titre%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09description%3A%20aRessource%20description%3B%0A%09%09%09%09%09%09%09thumbnailURL%3A%20aRessource%20thumbnail%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09fullImageURL%3A%20aRessource%20original%3B%0A%09%09%09%09%09%09%09foliono%3A%20aRessource%20foliono%3B%0A%09%09%09%09%09%09%09navigatorThumbnailURL%3A%20%28aRessource%20at%3A%20%27navigator_thumbnail%27%29%5D.%0A%09%5E%20book%0A%0A%09'),
messageSends: ["album", "title:", "titre", "width:", "width", "height:", "height", "downloadUrl:", "at:", "yourself", "new", "do:", "ressources", "description:", "description", "thumbnailURL:", "thumbnail", "fullImageURL:", "original", "foliono:", "foliono", "navigatorThumbnailURL:", "newPage"],
referencedClasses: ["Book"]
}),
smalltalk.BibNumLoader);

smalltalk.addMethod(
unescape('_loadBookFromJSONOnSuccess_'),
smalltalk.method({
selector: unescape('loadBookFromJSONOnSuccess%3A'),
category: 'loading',
fn: function (aBlock){
var self=this;
(function($rec){smalltalk.send($rec, "_onSuccessDo_", [(function(data){var book=nil;
book=smalltalk.send(self, "_buildBookFromJSon_", [data]);return smalltalk.send(aBlock, "_value_", [book]);})]);return smalltalk.send($rec, "_send", []);})(smalltalk.send(self, "_ajax", []));
return self;},
args: ["aBlock"],
source: unescape('loadBookFromJSONOnSuccess%3A%20aBlock%0A%09self%20ajax%0A%09%09onSuccessDo%3A%20%5B%3Adata%7C%20%7Cbook%7C%0A%09%09%09%09%09%09%09book%20%3A%3D%20self%20buildBookFromJSon%3A%20data.%0A%09%09%09%09%09%09%09aBlock%20value%3A%20book%5D%3B%0A%09%09send.'),
messageSends: ["onSuccessDo:", "buildBookFromJSon:", "value:", "send", "ajax"],
referencedClasses: []
}),
smalltalk.BibNumLoader);



smalltalk.addClass('SouvignyLoader', smalltalk.BibNumLoader, ['pages', 'links', 'book'], 'AFI');
smalltalk.SouvignyLoader.comment=unescape('I%27m%20a%20loader%20dedicated%20to%20the%20Bible%20de%20Souvigny')
smalltalk.addMethod(
unescape('_baseURL'),
smalltalk.method({
selector: unescape('baseURL'),
category: 'accessing',
fn: function (){
var self=this;
return unescape("souvigny/B031906101_MS_001/");
return self;},
args: [],
source: unescape('baseURL%0A%09%5E%20%27souvigny/B031906101_MS_001/%27'),
messageSends: [],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
unescape('_book'),
smalltalk.method({
selector: unescape('book'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@book']) == nil || $receiver == undefined) ? (function(){return self['@book']=(function($rec){smalltalk.send($rec, "_width_", [(390)]);smalltalk.send($rec, "_height_", [(594)]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send(smalltalk.send(smalltalk.send(self, "_class", []), "_bookClass", []), "_new", []));})() : $receiver;
return self;},
args: [],
source: unescape('book%0A%09%5E%20book%20ifNil%3A%20%5Bbook%20%3A%3D%20self%20class%20bookClass%20new%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09width%3A%20390%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09height%3A%20594%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09yourself%09%09%09%09%5D'),
messageSends: ["ifNil:", "width:", "height:", "yourself", "new", "bookClass", "class"],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
unescape('_buildBookFromHTML_'),
smalltalk.method({
selector: unescape('buildBookFromHTML%3A'),
category: 'page creation',
fn: function (aHTMLString){
var self=this;
var anchors=nil;
anchors=smalltalk.send(smalltalk.send(aHTMLString, "_asJQuery", []), "_find_", [unescape("li%20a%5Bhref%24%3D%22jpg%22%5D")]);
smalltalk.send(anchors, "_each_", [(function(index, element){var fileName=nil;
fileName=smalltalk.send(smalltalk.send((smalltalk.JQuery || JQuery), "_fromElement_", [element]), "_attr_", ["href"]);return (function($rec){smalltalk.send($rec, "_fullImageURL_", [smalltalk.send(smalltalk.send(self, "_fullImagesURL", []), "__comma", [fileName])]);return smalltalk.send($rec, "_thumbnailURL_", [smalltalk.send(smalltalk.send(self, "_thumbsURL", []), "__comma", [fileName])]);})(smalltalk.send(smalltalk.send(self, "_book", []), "_newPage", []));})]);
return self;},
args: ["aHTMLString"],
source: unescape('buildBookFromHTML%3A%20aHTMLString%0A%09%7Canchors%7C%0A%09anchors%20%3A%3D%20%28aHTMLString%20asJQuery%20find%3A%27li%20a%5Bhref%24%3D%22jpg%22%5D%27%29.%0A%09anchors%20each%3A%20%5B%3Aindex%20%3Aelement%7C%20%7CfileName%7C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20fileName%20%3A%3D%20%28JQuery%20fromElement%3A%20element%29%20attr%3A%20%27href%27.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20self%20book%20newPage%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09fullImageURL%3A%20self%20fullImagesURL%2C%20fileName%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09thumbnailURL%3A%20self%20thumbsURL%2C%20fileName.%0A%20%20%20%20%20%20%20%20%5D.'),
messageSends: ["find:", "asJQuery", "each:", "attr:", "fromElement:", "fullImageURL:", unescape("%2C"), "fullImagesURL", "thumbnailURL:", "thumbsURL", "newPage", "book"],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
unescape('_buildBookFromJSON_'),
smalltalk.method({
selector: unescape('buildBookFromJSON%3A'),
category: 'page creation',
fn: function (anArray){
var self=this;
smalltalk.send(anArray, "_do_", [(function(fileName){return (function($rec){smalltalk.send($rec, "_fullImageURL_", [smalltalk.send(smalltalk.send(self, "_fullImagesURL", []), "__comma", [fileName])]);return smalltalk.send($rec, "_thumbnailURL_", [smalltalk.send(smalltalk.send(self, "_thumbsURL", []), "__comma", [fileName])]);})(smalltalk.send(smalltalk.send(self, "_book", []), "_newPage", []));})]);
return self;},
args: ["anArray"],
source: unescape('buildBookFromJSON%3A%20anArray%0A%09anArray%20do%3A%20%5B%3AfileName%7C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20self%20book%20newPage%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09fullImageURL%3A%20self%20fullImagesURL%2C%20fileName%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09thumbnailURL%3A%20self%20thumbsURL%2C%20fileName.%0A%20%20%20%20%20%20%20%20%5D.'),
messageSends: ["do:", "fullImageURL:", unescape("%2C"), "fullImagesURL", "thumbnailURL:", "thumbsURL", "newPage", "book"],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
unescape('_fullImagesURL'),
smalltalk.method({
selector: unescape('fullImagesURL'),
category: 'accessing',
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(self, "_baseURL", []), "__comma", [unescape("big/")]);
return self;},
args: [],
source: unescape('fullImagesURL%0A%09%5E%20self%20baseURL%2C%20%27big/%27'),
messageSends: [unescape("%2C"), "baseURL"],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
unescape('_initMetadata_'),
smalltalk.method({
selector: unescape('initMetadata%3A'),
category: 'page creation',
fn: function (anArray){
var self=this;
smalltalk.send(anArray, "_do_", [(function(aJSObject){var pageNo=nil;
var page=nil;
pageNo=aJSObject.pageNo;return (($receiver = pageNo) != nil && $receiver != undefined) ? (function(){page=smalltalk.send(smalltalk.send(self, "_book", []), "_pageAtFolio_", [pageNo]);return (($receiver = page) != nil && $receiver != undefined) ? (function(){return smalltalk.send(page, "_initMetadata_", [aJSObject]);})() : nil;})() : nil;})]);
return self;},
args: ["anArray"],
source: unescape('initMetadata%3A%20anArray%0A%09anArray%20do%3A%20%5B%3AaJSObject%7C%20%7CpageNo%20page%7C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20pageNo%20%3A%3D%20%3CaJSObject.pageNo%3E.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20pageNo%20ifNotNil%3A%20%5B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09page%20%3A%3D%20self%20book%20pageAtFolio%3A%20pageNo.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09page%20ifNotNil%3A%20%5Bpage%20initMetadata%3A%20aJSObject%5D%20%5D.%0A%20%20%20%20%20%20%20%20%5D'),
messageSends: ["do:", "ifNotNil:", "pageAtFolio:", "book", "initMetadata:"],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
unescape('_links'),
smalltalk.method({
selector: unescape('links'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@links']) == nil || $receiver == undefined) ? (function(){return self['@links']=smalltalk.send((smalltalk.Dictionary || Dictionary), "_new", []);})() : $receiver;
return self;},
args: [],
source: unescape('links%0A%09%5E%20links%20ifNil%3A%20%5Blinks%20%3A%3D%20Dictionary%20new%5D'),
messageSends: ["ifNil:", "new"],
referencedClasses: ["Dictionary"]
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
unescape('_loadBookFromJSONOnSuccess_'),
smalltalk.method({
selector: unescape('loadBookFromJSONOnSuccess%3A'),
category: 'loading',
fn: function (aBlock){
var self=this;
(function($rec){smalltalk.send($rec, "_onSuccessDo_", [(function(data){smalltalk.send(self, "_buildBookFromJSON_", [data]);return smalltalk.send(self, "_onMetadataLoadedDo_", [(function(){return smalltalk.send(aBlock, "_value_", [smalltalk.send(self, "_book", [])]);})]);})]);return smalltalk.send($rec, "_send", []);})(smalltalk.send(smalltalk.send(self, "_ajax", []), "_url_", [smalltalk.send(self, "_thumbsJSONURL", [])]));
return self;},
args: ["aBlock"],
source: unescape('loadBookFromJSONOnSuccess%3A%20aBlock%0A%09%28self%20ajax%20url%3A%20self%20thumbsJSONURL%29%20%0A%09%09onSuccessDo%3A%20%5B%3Adata%7C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09self%20buildBookFromJSON%3A%20data.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09self%20onMetadataLoadedDo%3A%20%5B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20aBlock%20value%3A%20self%20book%5D%5D%3B%20%0A%09%09send.'),
messageSends: ["onSuccessDo:", "buildBookFromJSON:", "onMetadataLoadedDo:", "value:", "book", "send", "url:", "ajax", "thumbsJSONURL"],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
unescape('_loadBookOnSuccess_'),
smalltalk.method({
selector: unescape('loadBookOnSuccess%3A'),
category: 'loading',
fn: function (aBlock){
var self=this;
(function($rec){smalltalk.send($rec, "_onSuccessDo_", [(function(data){smalltalk.send(self, "_buildBookFromHTML_", [data]);return smalltalk.send(self, "_onMetadataLoadedDo_", [(function(){return smalltalk.send(aBlock, "_value_", [smalltalk.send(self, "_book", [])]);})]);})]);return smalltalk.send($rec, "_send", []);})(smalltalk.send((smalltalk.Ajax || Ajax), "_url_", [smalltalk.send(self, "_thumbsURL", [])]));
return self;},
args: ["aBlock"],
source: unescape('loadBookOnSuccess%3A%20aBlock%0A%09%28Ajax%20url%3A%20self%20thumbsURL%29%20%0A%09%09onSuccessDo%3A%20%5B%3Adata%7C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09self%20buildBookFromHTML%3A%20data.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09self%20onMetadataLoadedDo%3A%20%5B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20aBlock%20value%3A%20self%20book%5D%5D%3B%20%0A%09%09send.'),
messageSends: ["onSuccessDo:", "buildBookFromHTML:", "onMetadataLoadedDo:", "value:", "book", "send", "url:", "thumbsURL"],
referencedClasses: ["Ajax"]
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
unescape('_metadataURL'),
smalltalk.method({
selector: unescape('metadataURL'),
category: 'accessing',
fn: function (){
var self=this;
return unescape("souvigny/souvigny.json");
return self;},
args: [],
source: unescape('metadataURL%0A%09%5E%20%27souvigny/souvigny.json%27.'),
messageSends: [],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
unescape('_onMetadataLoadedDo_'),
smalltalk.method({
selector: unescape('onMetadataLoadedDo%3A'),
category: 'loading',
fn: function (aBlock){
var self=this;
(function($rec){smalltalk.send($rec, "_onSuccessDo_", [(function(data){smalltalk.send(self, "_initMetadata_", [data]);return smalltalk.send(aBlock, "_value", []);})]);return smalltalk.send($rec, "_send", []);})(smalltalk.send((smalltalk.Ajax || Ajax), "_url_", [smalltalk.send(self, "_metadataURL", [])]));
return self;},
args: ["aBlock"],
source: unescape('onMetadataLoadedDo%3A%20aBlock%0A%09%28Ajax%20url%3A%20self%20metadataURL%29%0A%09%09onSuccessDo%3A%20%5B%3Adata%7C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09self%20initMetadata%3A%20data.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09aBlock%20value%5D%3B%0A%09%09send'),
messageSends: ["onSuccessDo:", "initMetadata:", "value", "send", "url:", "metadataURL"],
referencedClasses: ["Ajax"]
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
unescape('_parsePageNo_'),
smalltalk.method({
selector: unescape('parsePageNo%3A'),
category: 'accessing',
fn: function (aString){
var self=this;
return ((($receiver = smalltalk.send(aString, "_includesSubString_", ["r"])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]));})() : (function(){return ((($receiver = smalltalk.send(aString, "_includesSubString_", ["v"])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)]));})() : (function(){return aString;})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)]));}), (function(){return aString;})]));})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]));}), (function(){return ((($receiver = smalltalk.send(aString, "_includesSubString_", ["v"])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)]));})() : (function(){return aString;})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)]));}), (function(){return aString;})]));})]));
return self;},
args: ["aString"],
source: unescape('parsePageNo%3A%20aString%0A%09%5E%20%20%28aString%20includesSubString%3A%20%27r%27%29%20%0A%09%09%09ifTrue%3A%20%5BaString%20allButLast%20asNumber%20*%202%5D%0A%09%09%09ifFalse%3A%20%5B%20%20%28aString%20includesSubString%3A%20%27v%27%29%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09ifTrue%3A%20%5BaString%20allButLast%20asNumber%20*%202%20+%201%5D%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09ifFalse%3A%20%5BaString%5D%20%5D'),
messageSends: ["ifTrue:ifFalse:", "includesSubString:", unescape("*"), "asNumber", "allButLast", unescape("+")],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
unescape('_thumbsJSONURL'),
smalltalk.method({
selector: unescape('thumbsJSONURL'),
category: 'accessing',
fn: function (){
var self=this;
return unescape("souvigny/thumbs.json");
return self;},
args: [],
source: unescape('thumbsJSONURL%0A%09%5E%20%27souvigny/thumbs.json%27'),
messageSends: [],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
unescape('_thumbsURL'),
smalltalk.method({
selector: unescape('thumbsURL'),
category: 'accessing',
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(self, "_baseURL", []), "__comma", [unescape("thumbs/")]);
return self;},
args: [],
source: unescape('thumbsURL%0A%09%5E%20self%20baseURL%2C%20%27thumbs/%27'),
messageSends: [unescape("%2C"), "baseURL"],
referencedClasses: []
}),
smalltalk.SouvignyLoader);


smalltalk.addMethod(
unescape('_bookClass'),
smalltalk.method({
selector: unescape('bookClass'),
category: 'accessing',
fn: function (){
var self=this;
return (smalltalk.SouvignyBible || SouvignyBible);
return self;},
args: [],
source: unescape('bookClass%0A%09%5ESouvignyBible'),
messageSends: [],
referencedClasses: ["SouvignyBible"]
}),
smalltalk.SouvignyLoader.klass);


smalltalk.addClass('Book', smalltalk.Object, ['pages', 'title', 'width', 'height', 'downloadUrl'], 'AFI');
smalltalk.addMethod(
unescape('_addPage_'),
smalltalk.method({
selector: unescape('addPage%3A'),
category: 'adding',
fn: function (aPage){
var self=this;
smalltalk.send(smalltalk.send(self, "_pages", []), "_add_", [aPage]);
smalltalk.send(aPage, "_book_", [self]);
return aPage;
return self;},
args: ["aPage"],
source: unescape('addPage%3A%20aPage%0A%09self%20pages%20add%3A%20aPage.%0A%09aPage%20book%3A%20self.%0A%09%5E%20aPage'),
messageSends: ["add:", "pages", "book:"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
unescape('_downloadUrl'),
smalltalk.method({
selector: unescape('downloadUrl'),
category: 'accessing',
fn: function (){
var self=this;
return self['@downloadUrl'];
return self;},
args: [],
source: unescape('downloadUrl%0A%09%5E%20downloadUrl'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
unescape('_downloadUrl_'),
smalltalk.method({
selector: unescape('downloadUrl%3A'),
category: 'accessing',
fn: function (anUrl){
var self=this;
(self['@downloadUrl']=anUrl);
return self;},
args: ["anUrl"],
source: unescape('downloadUrl%3A%20anUrl%0A%09downloadUrl%20%3A%3D%20anUrl'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
unescape('_height'),
smalltalk.method({
selector: unescape('height'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@height']) == nil || $receiver == undefined) ? (function(){return self['@height']=(400);})() : $receiver;
return self;},
args: [],
source: unescape('height%0A%09%5E%20height%20ifNil%3A%20%5Bheight%20%3A%3D%20400%5D'),
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
unescape('_height_'),
smalltalk.method({
selector: unescape('height%3A'),
category: 'accessing',
fn: function (anInteger){
var self=this;
self['@height']=anInteger;
return self;},
args: ["anInteger"],
source: unescape('height%3A%20anInteger%0A%09height%20%3A%3D%20anInteger'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
unescape('_newPage'),
smalltalk.method({
selector: unescape('newPage'),
category: 'adding',
fn: function (){
var self=this;
return smalltalk.send(self, "_addPage_", [smalltalk.send(smalltalk.send(smalltalk.send(self, "_class", []), "_pageClass", []), "_new", [])]);
return self;},
args: [],
source: unescape('newPage%0A%09%5E%20self%20addPage%3A%20%28self%20class%20pageClass%20new%29'),
messageSends: ["addPage:", "new", "pageClass", "class"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
unescape('_pageAt_'),
smalltalk.method({
selector: unescape('pageAt%3A'),
category: 'accessing',
fn: function (aNumber){
var self=this;
return smalltalk.send(smalltalk.send(self, "_pages", []), "_at_", [aNumber]);
return self;},
args: ["aNumber"],
source: unescape('pageAt%3A%20aNumber%0A%09%5E%20self%20pages%20at%3A%20aNumber'),
messageSends: ["at:", "pages"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
unescape('_pageAt_do_'),
smalltalk.method({
selector: unescape('pageAt%3Ado%3A'),
category: 'enumerating',
fn: function (pageNo, aBlockWithArg){
var self=this;
var page=nil;
page=smalltalk.send(smalltalk.send(self, "_pages", []), "_at_ifAbsent_", [pageNo, (function(){return nil;})]);
(($receiver = page) != nil && $receiver != undefined) ? (function(){return smalltalk.send(aBlockWithArg, "_value_", [page]);})() : nil;
return self;},
args: ["pageNo", "aBlockWithArg"],
source: unescape('pageAt%3A%20pageNo%20do%3A%20aBlockWithArg%0A%09%7Cpage%7C%0A%09page%20%3A%3D%20self%20pages%20at%3A%20pageNo%20ifAbsent%3A%20%5Bnil%5D.%0A%20%20%20%20%20%20%20%20page%20ifNotNil%3A%20%5BaBlockWithArg%20value%3A%20page%5D.'),
messageSends: ["at:ifAbsent:", "pages", "ifNotNil:", "value:"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
unescape('_pageAt_ifAbsent_'),
smalltalk.method({
selector: unescape('pageAt%3AifAbsent%3A'),
category: 'accessing',
fn: function (aNumber, aBlock){
var self=this;
return smalltalk.send(smalltalk.send(self, "_pages", []), "_at_ifAbsent_", [aNumber, aBlock]);
return self;},
args: ["aNumber", "aBlock"],
source: unescape('pageAt%3A%20aNumber%20ifAbsent%3A%20aBlock%0A%09%5E%20self%20pages%20at%3A%20aNumber%20ifAbsent%3A%20aBlock'),
messageSends: ["at:ifAbsent:", "pages"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
unescape('_pageNo_'),
smalltalk.method({
selector: unescape('pageNo%3A'),
category: 'accessing',
fn: function (aPage){
var self=this;
return smalltalk.send(smalltalk.send(self, "_pages", []), "_indexOf_", [aPage]);
return self;},
args: ["aPage"],
source: unescape('pageNo%3A%20aPage%0A%09%5E%20self%20pages%20indexOf%3A%20aPage'),
messageSends: ["indexOf:", "pages"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
unescape('_pages'),
smalltalk.method({
selector: unescape('pages'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@pages']) == nil || $receiver == undefined) ? (function(){return self['@pages']=smalltalk.send((smalltalk.Array || Array), "_new", []);})() : $receiver;
return self;},
args: [],
source: unescape('pages%0A%09%5E%20pages%20ifNil%3A%20%5Bpages%20%3A%3D%20Array%20new%5D'),
messageSends: ["ifNil:", "new"],
referencedClasses: ["BlockClosure"]
}),
smalltalk.Book);

smalltalk.addMethod(
unescape('_pagesNo_do_'),
smalltalk.method({
selector: unescape('pagesNo%3Ado%3A'),
category: 'enumerating',
fn: function (anArray, aBlockWithArg){
var self=this;
smalltalk.send(anArray, "_do_", [(function(pageNo){return smalltalk.send(self, "_pageAt_do_", [pageNo, aBlockWithArg]);})]);
return self;},
args: ["anArray", "aBlockWithArg"],
source: unescape('pagesNo%3A%20%20anArray%20do%3A%20aBlockWithArg%0A%09anArray%20do%3A%20%5B%3ApageNo%7C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20self%20pageAt%3A%20pageNo%20do%3A%20aBlockWithArg%0A%20%20%20%20%20%20%20%20%20%5D.'),
messageSends: ["do:", "pageAt:do:"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
unescape('_pagesNo_to_do_'),
smalltalk.method({
selector: unescape('pagesNo%3Ato%3Ado%3A'),
category: 'enumerating',
fn: function (start, end, aBlockWithArg){
var self=this;
smalltalk.send(start, "_to_do_", [end, (function(pageNo){return smalltalk.send(self, "_pageAt_do_", [pageNo, aBlockWithArg]);})]);
return self;},
args: ["start", "end", "aBlockWithArg"],
source: unescape('pagesNo%3A%20start%20to%3A%20end%20do%3A%20aBlockWithArg%0A%09start%20to%3A%20end%20do%3A%20%5B%3ApageNo%7C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20self%20pageAt%3A%20pageNo%20do%3A%20aBlockWithArg%0A%20%20%20%20%20%20%20%20%20%5D.'),
messageSends: ["to:do:", "pageAt:do:"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
unescape('_pagesWithTitle'),
smalltalk.method({
selector: unescape('pagesWithTitle'),
category: 'accessing',
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(self, "_pages", []), "_reject_", [(function(aPage){return smalltalk.send(smalltalk.send(aPage, "_title", []), "_isEmpty", []);})]);
return self;},
args: [],
source: unescape('pagesWithTitle%0A%09%5E%20self%20pages%20reject%3A%20%5B%3AaPage%20%7C%20aPage%20title%20isEmpty%5D%20'),
messageSends: ["reject:", "pages", "isEmpty", "title"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
unescape('_reset'),
smalltalk.method({
selector: unescape('reset'),
category: 'reset',
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(self, "_pages", []), "_do_", [(function(aPage){return smalltalk.send(aPage, "_reset", []);})]);
return self;},
args: [],
source: unescape('reset%0A%09self%20pages%20do%3A%20%5B%3AaPage%7C%20aPage%20reset%5D'),
messageSends: ["do:", "pages", "reset"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
unescape('_size'),
smalltalk.method({
selector: unescape('size'),
category: 'accessing',
fn: function (){
var self=this;
return smalltalk.send(self['@pages'], "_size", []);
return self;},
args: [],
source: unescape('size%0A%09%5E%20pages%20size'),
messageSends: ["size"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
unescape('_title'),
smalltalk.method({
selector: unescape('title'),
category: 'accessing',
fn: function (){
var self=this;
return self['@title'];
return self;},
args: [],
source: unescape('title%0A%09%5E%20title'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
unescape('_title_'),
smalltalk.method({
selector: unescape('title%3A'),
category: 'accessing',
fn: function (aString){
var self=this;
self['@title']=aString;
return self;},
args: ["aString"],
source: unescape('title%3A%20aString%0A%09title%20%3A%3D%20aString'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
unescape('_width'),
smalltalk.method({
selector: unescape('width'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@width']) == nil || $receiver == undefined) ? (function(){return self['@width']=(300);})() : $receiver;
return self;},
args: [],
source: unescape('width%0A%09%5E%20width%20ifNil%3A%20%5Bwidth%20%3A%3D%20300%5D'),
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
unescape('_width_'),
smalltalk.method({
selector: unescape('width%3A'),
category: 'accessing',
fn: function (anInteger){
var self=this;
self['@width']=anInteger;
return self;},
args: ["anInteger"],
source: unescape('width%3A%20anInteger%0A%09width%20%3A%3D%20anInteger'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Book);


smalltalk.addMethod(
unescape('_pageClass'),
smalltalk.method({
selector: unescape('pageClass'),
category: 'accessing',
fn: function (){
var self=this;
return (smalltalk.Page || Page);
return self;},
args: [],
source: unescape('pageClass%0A%09%5E%20Page'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Book.klass);


smalltalk.addClass('SouvignyBible', smalltalk.Book, [], 'AFI');
smalltalk.addMethod(
unescape('_pageAtFolio_'),
smalltalk.method({
selector: unescape('pageAtFolio%3A'),
category: 'accessing',
fn: function (aString){
var self=this;
return smalltalk.send(smalltalk.send(self, "_pages", []), "_at_ifAbsent_", [smalltalk.send(self, "_parseFolioNo_", [aString]), (function(){return nil;})]);
return self;},
args: ["aString"],
source: unescape('pageAtFolio%3A%20aString%0A%09%5E%20self%20pages%20at%3A%20%28self%20parseFolioNo%3A%20aString%29%20ifAbsent%3A%20%5Bnil%5D.'),
messageSends: ["at:ifAbsent:", "pages", "parseFolioNo:"],
referencedClasses: []
}),
smalltalk.SouvignyBible);

smalltalk.addMethod(
unescape('_parseFolioNo_'),
smalltalk.method({
selector: unescape('parseFolioNo%3A'),
category: 'accessing',
fn: function (aString){
var self=this;
return ((($receiver = smalltalk.send(aString, "_includesSubString_", ["r"])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(5) : smalltalk.send($receiver, "__plus", [(5)]));})() : (function(){return ((($receiver = smalltalk.send(aString, "_includesSubString_", ["v"])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return ((($receiver = ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)]))).klass === smalltalk.Number) ? $receiver +(5) : smalltalk.send($receiver, "__plus", [(5)]));})() : (function(){return smalltalk.send(aString, "_asNumber", []);})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return ((($receiver = ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)]))).klass === smalltalk.Number) ? $receiver +(5) : smalltalk.send($receiver, "__plus", [(5)]));}), (function(){return smalltalk.send(aString, "_asNumber", []);})]));})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(5) : smalltalk.send($receiver, "__plus", [(5)]));}), (function(){return ((($receiver = smalltalk.send(aString, "_includesSubString_", ["v"])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return ((($receiver = ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)]))).klass === smalltalk.Number) ? $receiver +(5) : smalltalk.send($receiver, "__plus", [(5)]));})() : (function(){return smalltalk.send(aString, "_asNumber", []);})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return ((($receiver = ((($receiver = ((($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number) ? $receiver *(2) : smalltalk.send($receiver, "__star", [(2)]))).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)]))).klass === smalltalk.Number) ? $receiver +(5) : smalltalk.send($receiver, "__plus", [(5)]));}), (function(){return smalltalk.send(aString, "_asNumber", []);})]));})]));
return self;},
args: ["aString"],
source: unescape('parseFolioNo%3A%20aString%0A%09%22Folios%20are%20numbered%2032r%2032v%20as%20page%2032%20recto%2C%20page%2032%20verso.%20Excepted%203%20first%20folios%20%286%20pages%29%22%0A%09%5E%28aString%20includesSubString%3A%20%27r%27%29%20%0A%09%09%09ifTrue%3A%20%5BaString%20allButLast%20asNumber%20*%202%20+%205%5D%0A%09%09%09ifFalse%3A%20%5B%20%20%28aString%20includesSubString%3A%20%27v%27%29%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09ifTrue%3A%20%5BaString%20allButLast%20asNumber%20*%202%20+%201%20+%205%5D%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09ifFalse%3A%20%5BaString%20asNumber%5D%20%5D.'),
messageSends: ["ifTrue:ifFalse:", "includesSubString:", unescape("+"), unescape("*"), "asNumber", "allButLast"],
referencedClasses: []
}),
smalltalk.SouvignyBible);

smalltalk.addMethod(
unescape('_title'),
smalltalk.method({
selector: unescape('title'),
category: 'accessing',
fn: function (){
var self=this;
return "Bible de Souvigny";
return self;},
args: [],
source: unescape('title%0A%09%5E%20%27Bible%20de%20Souvigny%27'),
messageSends: [],
referencedClasses: []
}),
smalltalk.SouvignyBible);


smalltalk.addMethod(
unescape('_pageClass'),
smalltalk.method({
selector: unescape('pageClass'),
category: 'accessing',
fn: function (){
var self=this;
return (smalltalk.SouvignyPage || SouvignyPage);
return self;},
args: [],
source: unescape('pageClass%0A%09%5E%20SouvignyPage'),
messageSends: [],
referencedClasses: []
}),
smalltalk.SouvignyBible.klass);


smalltalk.addClass('BookWidget', smalltalk.Widget, ['book', 'currentPageNo', 'pageZoomBrush', 'pageZoomWidget', 'zoomLeftPageAnchor', 'zoomRightPageAnchor', 'pageDescriptionsBrush', 'loader', 'scriptsRoot', 'bookContainer', 'width', 'rootBrush', 'menuJQuery', 'isFullscreen', 'downloadBrush', 'leftFolioBrush', 'rightFolioBrush', 'announcer'], 'AFI');
smalltalk.addMethod(
unescape('_afterPageChange_'),
smalltalk.method({
selector: unescape('afterPageChange%3A'),
category: 'callbacks',
fn: function (data){
var self=this;
smalltalk.send(self, "_updateFolioNumbers", []);
smalltalk.send(self, "_openDescriptions", []);
smalltalk.send(self, "_announcePageChange_", [smalltalk.send(self, "_currentPage", [])]);
return self;},
args: ["data"],
source: unescape('afterPageChange%3A%20data%0A%09self%20updateFolioNumbers.%0A%09self%20openDescriptions.%0A%09self%20announcePageChange%3A%20self%20currentPage.'),
messageSends: ["updateFolioNumbers", "openDescriptions", "announcePageChange:", "currentPage"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_announcePageChange_'),
smalltalk.method({
selector: unescape('announcePageChange%3A'),
category: 'announcements',
fn: function (aPage){
var self=this;
smalltalk.send(smalltalk.send(self, "_announcer", []), "_announce_", [smalltalk.send((smalltalk.PageChangeAnnouncement || PageChangeAnnouncement), "_page_", [aPage])]);
return self;},
args: ["aPage"],
source: unescape('announcePageChange%3A%20aPage%0A%09self%20announcer%20announce%3A%20%28PageChangeAnnouncement%20page%3A%20aPage%29'),
messageSends: ["announce:", "announcer", "page:"],
referencedClasses: ["PageChangeAnnouncement"]
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_announcer'),
smalltalk.method({
selector: unescape('announcer'),
category: 'announcements',
fn: function (){
var self=this;
return (($receiver = self['@announcer']) == nil || $receiver == undefined) ? (function(){return (self['@announcer']=smalltalk.send((smalltalk.Announcer || Announcer), "_new", []));})() : $receiver;
return self;},
args: [],
source: unescape('announcer%0A%09%5E%20announcer%20ifNil%3A%20%5Bannouncer%20%3A%3D%20Announcer%20new%5D'),
messageSends: ["ifNil:", "new"],
referencedClasses: ["Announcer"]
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_beforePageChange_'),
smalltalk.method({
selector: unescape('beforePageChange%3A'),
category: 'callbacks',
fn: function (data){
var self=this;
smalltalk.send(self, "_closeDescriptions", []);
smalltalk.send(self, "_openPageNo_", [((($receiver = smalltalk.send(data, "_basicAt_", ["curr"])).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)]))]);
smalltalk.send(self, "_closeZoom", []);
return self;},
args: ["data"],
source: unescape('beforePageChange%3Adata%0A%09self%20closeDescriptions.%0A%09self%20openPageNo%3A%20%28data%20basicAt%3A%20%27curr%27%29%20+%201.%0A%09self%20closeZoom.'),
messageSends: ["closeDescriptions", "openPageNo:", unescape("+"), "basicAt:", "closeZoom"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_book'),
smalltalk.method({
selector: unescape('book'),
category: 'accessing',
fn: function (){
var self=this;
return self['@book'];
return self;},
args: [],
source: unescape('book%0A%09%5E%20book'),
messageSends: [],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_bookStyle'),
smalltalk.method({
selector: unescape('bookStyle'),
category: 'css',
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(unescape("%0A%09%09%09.bk-widget%20.booklet%20%7B%20margin-bottom%3A%2020px%09%7D%09%09%09%0A%0A%09%09%09.bib-num-album%20%7B%20%20padding%3A%2010px%20%7D%0A%0A%09%09%09.bk-widget%20.b-counter%20%7B%0A%09%09%09%20%20margin-bottom%3A%20-20px%3B%0A%09%09%09%20%20margin-top%3A%2020px%3B%0A%09%09%09%20%20width%3A%20140px%3B%0A%09%09%09%20%20text-align%3A%20left%3B%0A%09%09%09%20%20bottom%3A%200px%3B%0A%09%09%09%20%20background-color%3A%20transparent%3B%0A%09%09%09%20%20font-weight%3A%20bold%3B%0A%09%09%09%20%20font-size%3A%201.1em%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-counter%20+%20.b-counter%20%7B%0A%09%09%09%20%20right%3A%200px%3B%0A%09%09%09%20%20text-align%3A%20right%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.loading%20%7B%20%20text-align%3Acenter%09%7D%0A%09%09%09%0A%09%09%09.bk-widget%20.booklet%20.b-wrap-right%20%7B%0A%09%09%09%20%20background-color%3A%20transparent%3B%0A%09%09%09%20%20padding%3A%200px%3B%20%21important%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-wrap-left%20%7B%0A%09%09%09%20%20background-color%3A%20transparent%3B%0A%09%09%09%20%20padding%3A%200px%3B%20%21important%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-page-cover%20%7B%20%20background-color%3A%20transparent%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20%7B%0A%09%09%09%20%20font-size%3A%201.4em%3B%0A%09%09%09%20%20font-weight%3A%20bold%3B%0A%09%09%09%20%20width%3A%20820px%3B%0A%09%09%09%20%20margin%3A%200%20auto%3B%0A%09%09%09%20%20height%3A%2060px%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20%7B%0A%09%09%09%20%20width%3A%20600px%3B%0A%09%09%09%20%20text-align%3A%20left%3B%0A%09%09%09%20%20height%3A%2060px%3B%0A%09%09%09%20%20float%3Anone%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20.b-current%20%7B%0A%09%09%09%20%20height%3A%20auto%3B%0A%09%09%09%20%20text-align%3A%20left%3B%0A%09%09%09%20%20background%3A%20url%28"), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/menu_off.png%29%20no-repeat%2015px%20center%3B%0A%09%09%09%20%20padding-left%3A%2045px%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%3Ahover%20.b-current%20%7B%0A%09%09%09%20%20background-image%3A%20url%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/menu_on.png%29%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20%7B%20color%3A%20black%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20a%20%7B%20color%3A%20inherit%3B%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%3Ahover%20%7Bcolor%3A%20black%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%3Ahover%20ul%20%7B%20box-shadow%3A%202px%202px%2040px%20rgba%282%2C2%2C0%2C0.8%29%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20ul%20%7B%0A%09%09%09%20%20width%3A%20584px%3B%0A%09%09%09%20%20top%3A%20auto%3B%0A%09%09%09%20%20max-height%3A%20600px%3B%0A%09%09%09%20%20overflow-y%3A%20auto%20%21important%3B%0A%09%09%09%20%20background-color%3A%20white%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20ul%20li%20%7B%20font-size%3A%201.2em%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20ul%20li%20a%20%7B%20height%3A%20auto%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20ul%20li%20a%20.b-text%20%7B%20float%3A%20none%3B%20%7D%0A%0A%09%09%09.bk-widget%20button%20%7Bfloat%3A%20left%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow%20div%20%7B%0A%09%09%09%09-webkit-transition%3A%20all%200.3s%3B%0A%09%09%09%09-moz-transition%3A%20all%200.3s%3B%0A%09%09%09%09-o-transition%3A%20all%200.3s%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow-next%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-next_black.png%29%3B%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow-next%3Ahover%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-next.png%29%3B%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow-prev%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-prev_black.png%29%3B%20%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow-prev%3Ahover%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-prev.png%29%3B%7D%0A%0A%09%09%09.bk-widget%20.b-counter%20+%20.b-counter%20%7Bfloat%3A%20right%3B%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-next%20div%20%7Bbackground-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-next_black-small.png%29%3B%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-next%3Ahover%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-next-small.png%29%3B%20%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-prev%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-prev_black-small.png%29%3B%20%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-prev%3Ahover%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-prev-small.png%29%3B%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-prev%20%7B%20left%3A%20-25px%20%7D%0A%09%09%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-next%20%7B%20right%3A%20-25px%20%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow%20%7B%20width%3A%2025px%20%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow%20%20div%20%7B%20top%3A%2036%25%20%7D%0A%0A%09%09%09.clear%20%7B%20%0A%09%09%09%09clear%3A%20both%3B%0A%09%09%09%09height%3A%200px%20%21important%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-wrap%20%7B%0A%09%09%09%09cursor%3A%20-moz-zoom-in%3B%0A%09%09%09%09cursor%3A%20-webkit-zoom-in%3B%0A%09%09%09%7D%0A")]);
return self;},
args: [],
source: unescape('bookStyle%0A%09%5E%20%27%0A%09%09%09.bk-widget%20.booklet%20%7B%20margin-bottom%3A%2020px%09%7D%09%09%09%0A%0A%09%09%09.bib-num-album%20%7B%20%20padding%3A%2010px%20%7D%0A%0A%09%09%09.bk-widget%20.b-counter%20%7B%0A%09%09%09%20%20margin-bottom%3A%20-20px%3B%0A%09%09%09%20%20margin-top%3A%2020px%3B%0A%09%09%09%20%20width%3A%20140px%3B%0A%09%09%09%20%20text-align%3A%20left%3B%0A%09%09%09%20%20bottom%3A%200px%3B%0A%09%09%09%20%20background-color%3A%20transparent%3B%0A%09%09%09%20%20font-weight%3A%20bold%3B%0A%09%09%09%20%20font-size%3A%201.1em%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-counter%20+%20.b-counter%20%7B%0A%09%09%09%20%20right%3A%200px%3B%0A%09%09%09%20%20text-align%3A%20right%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.loading%20%7B%20%20text-align%3Acenter%09%7D%0A%09%09%09%0A%09%09%09.bk-widget%20.booklet%20.b-wrap-right%20%7B%0A%09%09%09%20%20background-color%3A%20transparent%3B%0A%09%09%09%20%20padding%3A%200px%3B%20%21important%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-wrap-left%20%7B%0A%09%09%09%20%20background-color%3A%20transparent%3B%0A%09%09%09%20%20padding%3A%200px%3B%20%21important%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-page-cover%20%7B%20%20background-color%3A%20transparent%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20%7B%0A%09%09%09%20%20font-size%3A%201.4em%3B%0A%09%09%09%20%20font-weight%3A%20bold%3B%0A%09%09%09%20%20width%3A%20820px%3B%0A%09%09%09%20%20margin%3A%200%20auto%3B%0A%09%09%09%20%20height%3A%2060px%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20%7B%0A%09%09%09%20%20width%3A%20600px%3B%0A%09%09%09%20%20text-align%3A%20left%3B%0A%09%09%09%20%20height%3A%2060px%3B%0A%09%09%09%20%20float%3Anone%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20.b-current%20%7B%0A%09%09%09%20%20height%3A%20auto%3B%0A%09%09%09%20%20text-align%3A%20left%3B%0A%09%09%09%20%20background%3A%20url%28%27%2C%20self%20scriptsRoot%2C%20%27images/menu_off.png%29%20no-repeat%2015px%20center%3B%0A%09%09%09%20%20padding-left%3A%2045px%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%3Ahover%20.b-current%20%7B%0A%09%09%09%20%20background-image%3A%20url%28%27%2C%20self%20scriptsRoot%2C%20%27images/menu_on.png%29%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20%7B%20color%3A%20black%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20a%20%7B%20color%3A%20inherit%3B%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%3Ahover%20%7Bcolor%3A%20black%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%3Ahover%20ul%20%7B%20box-shadow%3A%202px%202px%2040px%20rgba%282%2C2%2C0%2C0.8%29%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20ul%20%7B%0A%09%09%09%20%20width%3A%20584px%3B%0A%09%09%09%20%20top%3A%20auto%3B%0A%09%09%09%20%20max-height%3A%20600px%3B%0A%09%09%09%20%20overflow-y%3A%20auto%20%21important%3B%0A%09%09%09%20%20background-color%3A%20white%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20ul%20li%20%7B%20font-size%3A%201.2em%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20ul%20li%20a%20%7B%20height%3A%20auto%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20ul%20li%20a%20.b-text%20%7B%20float%3A%20none%3B%20%7D%0A%0A%09%09%09.bk-widget%20button%20%7Bfloat%3A%20left%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow%20div%20%7B%0A%09%09%09%09-webkit-transition%3A%20all%200.3s%3B%0A%09%09%09%09-moz-transition%3A%20all%200.3s%3B%0A%09%09%09%09-o-transition%3A%20all%200.3s%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow-next%20div%20%7B%20background-image%3Aurl%28%27%2C%20self%20scriptsRoot%2C%20%27booklet/images/arrow-next_black.png%29%3B%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow-next%3Ahover%20div%20%7B%20background-image%3Aurl%28%27%2C%20self%20scriptsRoot%2C%20%27booklet/images/arrow-next.png%29%3B%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow-prev%20div%20%7B%20background-image%3Aurl%28%27%2C%20self%20scriptsRoot%2C%20%27booklet/images/arrow-prev_black.png%29%3B%20%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow-prev%3Ahover%20div%20%7B%20background-image%3Aurl%28%27%2C%20self%20scriptsRoot%2C%20%27booklet/images/arrow-prev.png%29%3B%7D%0A%0A%09%09%09.bk-widget%20.b-counter%20+%20.b-counter%20%7Bfloat%3A%20right%3B%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-next%20div%20%7Bbackground-image%3Aurl%28%27%2C%20self%20scriptsRoot%2C%20%27booklet/images/arrow-next_black-small.png%29%3B%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-next%3Ahover%20div%20%7B%20background-image%3Aurl%28%27%2C%20self%20scriptsRoot%2C%20%27booklet/images/arrow-next-small.png%29%3B%20%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-prev%20div%20%7B%20background-image%3Aurl%28%27%2C%20self%20scriptsRoot%2C%20%27booklet/images/arrow-prev_black-small.png%29%3B%20%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-prev%3Ahover%20div%20%7B%20background-image%3Aurl%28%27%2C%20self%20scriptsRoot%2C%20%27booklet/images/arrow-prev-small.png%29%3B%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-prev%20%7B%20left%3A%20-25px%20%7D%0A%09%09%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-next%20%7B%20right%3A%20-25px%20%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow%20%7B%20width%3A%2025px%20%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow%20%20div%20%7B%20top%3A%2036%25%20%7D%0A%0A%09%09%09.clear%20%7B%20%0A%09%09%09%09clear%3A%20both%3B%0A%09%09%09%09height%3A%200px%20%21important%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-wrap%20%7B%0A%09%09%09%09cursor%3A%20-moz-zoom-in%3B%0A%09%09%09%09cursor%3A%20-webkit-zoom-in%3B%0A%09%09%09%7D%0A%27'),
messageSends: [unescape("%2C"), "scriptsRoot"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_bookletOptions'),
smalltalk.method({
selector: unescape('bookletOptions'),
category: 'accessing',
fn: function (){
var self=this;
return (function($rec){smalltalk.send($rec, "_at_put_", ["pageSelector", false]);smalltalk.send($rec, "_at_put_", ["chapterSelector", smalltalk.send(self['@isFullscreen'], "_not", [])]);smalltalk.send($rec, "_at_put_", ["menu", self['@menuJQuery']]);smalltalk.send($rec, "_at_put_", ["tabs", false]);smalltalk.send($rec, "_at_put_", ["keyboard", false]);smalltalk.send($rec, "_at_put_", ["arrows", true]);smalltalk.send($rec, "_at_put_", ["hash", true]);smalltalk.send($rec, "_at_put_", ["closed", true]);smalltalk.send($rec, "_at_put_", ["covers", true]);smalltalk.send($rec, "_at_put_", ["autoCenter", true]);smalltalk.send($rec, "_at_put_", ["pagePadding", (0)]);smalltalk.send($rec, "_at_put_", ["shadows", true]);smalltalk.send($rec, "_at_put_", ["width", smalltalk.send(self, "_width", [])]);smalltalk.send($rec, "_at_put_", ["height", smalltalk.send(self, "_height", [])]);smalltalk.send($rec, "_at_put_", ["manual", false]);smalltalk.send($rec, "_at_put_", ["pageNumbers", false]);smalltalk.send($rec, "_at_put_", ["overlays", false]);smalltalk.send($rec, "_at_put_", ["hovers", false]);smalltalk.send($rec, "_at_put_", ["arrowsHide", false]);smalltalk.send($rec, "_at_put_", ["closedFrontTitle", smalltalk.send(self['@book'], "_title", [])]);smalltalk.send($rec, "_at_put_", ["closedFrontChapter", smalltalk.send(self['@book'], "_title", [])]);smalltalk.send($rec, "_at_put_", ["closedBackTitle", "Fin"]);smalltalk.send($rec, "_at_put_", ["closedBackChapter", "Fin"]);smalltalk.send($rec, "_at_put_", ["previousPageTitle", unescape("Pr%E9c%E9dent")]);smalltalk.send($rec, "_at_put_", ["nextPageTitle", "Suivant"]);smalltalk.send($rec, "_at_put_", ["before", (function(data){return smalltalk.send(self, "_beforePageChange_", [data]);})]);smalltalk.send($rec, "_at_put_", ["after", (function(data){return smalltalk.send(self, "_afterPageChange_", [data]);})]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send((smalltalk.HashedCollection || HashedCollection), "_new", []));
return self;},
args: [],
source: unescape('bookletOptions%0A%09%5E%20HashedCollection%20new%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20at%3A%20%27pageSelector%27%20put%3Afalse%3B%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20at%3A%20%27chapterSelector%27%20put%3A%20isFullscreen%20not%3B%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20at%3A%20%27menu%27%20put%3A%20menuJQuery%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20at%3A%20%27tabs%27%20put%3A%20false%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20at%3A%20%27keyboard%27%20put%3A%20false%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20at%3A%20%27arrows%27%20put%3A%20true%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20at%3A%20%27hash%27%20put%3A%20true%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20at%3A%20%27closed%27%20put%3A%20true%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20at%3A%20%27covers%27%20put%3A%20true%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20at%3A%20%27autoCenter%27%20put%3A%20true%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20at%3A%20%27pagePadding%27%20put%3A%200%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20at%3A%20%27shadows%27%20put%3A%20true%3B%0A%09%09%09%20%20%20%20%20%20%20at%3A%20%27width%27%20put%3A%20self%20width%3B%0A%09%09%09%20%20%20%20%20%20%20at%3A%20%27height%27%20put%3A%20self%20height%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20at%3A%20%27manual%27%20put%3A%20false%3B%0A%09%09%09%20%20%20%20%20%20%20at%3A%20%27pageNumbers%27%20put%3A%20false%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20at%3A%20%27overlays%27%20put%3A%20false%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20at%3A%20%27hovers%27%20put%3A%20false%3B%0A%09%09%09%20%20%20%20%20%20%20at%3A%20%27arrowsHide%27%20put%3A%20false%3B%0A%09%09%09%20%20%20%20%20%20%20at%3A%20%27closedFrontTitle%27%20put%3A%20book%20title%3B%0A%09%09%09%20%20%20%20%20%20%20at%3A%20%27closedFrontChapter%27%20put%3A%20book%20title%3B%0A%09%09%09%20%20%20%20%20%20%20at%3A%20%27closedBackTitle%27%20put%3A%20%27Fin%27%3B%0A%09%09%09%20%20%20%20%20%20%20at%3A%20%27closedBackChapter%27%20put%3A%20%27Fin%27%3B%0A%09%09%09%20%20%20%20%20%20%20at%3A%20%27previousPageTitle%27%20put%3A%20%27Pr%E9c%E9dent%27%3B%0A%09%09%09%20%20%20%20%20%20%20at%3A%20%27nextPageTitle%27%20put%3A%20%27Suivant%27%3B%0A%09%09%09%20%20%20%20%20%20%20at%3A%20%27before%27%20put%3A%20%5B%3Adata%7C%20self%20beforePageChange%3Adata%5D%3B%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20at%3A%20%27after%27%20put%3A%20%5B%3Adata%7C%20%20self%20afterPageChange%3A%20data%5D%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20yourself'),
messageSends: ["at:put:", "not", "width", "height", "title", "beforePageChange:", "afterPageChange:", "yourself", "new"],
referencedClasses: ["HashedCollection"]
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_clear'),
smalltalk.method({
selector: unescape('clear'),
category: 'show',
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(unescape(".bk-widget"), "_asJQuery", []), "_remove", []);
return self;},
args: [],
source: unescape('clear%0A%09%27.bk-widget%27%20asJQuery%20remove.'),
messageSends: ["remove", "asJQuery"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_closeDescriptions'),
smalltalk.method({
selector: unescape('closeDescriptions'),
category: 'descriptions',
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(self['@pageDescriptionsBrush'], "_asJQuery", []), "_fadeOut", []);
return self;},
args: [],
source: unescape('closeDescriptions%0A%09pageDescriptionsBrush%20asJQuery%20fadeOut.'),
messageSends: ["fadeOut", "asJQuery"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_closeZoom'),
smalltalk.method({
selector: unescape('closeZoom'),
category: 'zoom',
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(unescape(".b-arrow"), "_asJQuery", []), "_show", []);
smalltalk.send(smalltalk.send(self['@pageZoomBrush'], "_asJQuery", []), "_fadeOut_do_", ["slow", (function(){self['@pageZoomWidget']=nil;smalltalk.send(self['@pageZoomBrush'], "_empty", []);(function($rec){smalltalk.send($rec, "_removeClass_", ["active"]);return smalltalk.send($rec, "_show", []);})(self['@zoomLeftPageAnchor']);(function($rec){smalltalk.send($rec, "_removeClass_", ["active"]);return smalltalk.send($rec, "_show", []);})(self['@zoomRightPageAnchor']);((($receiver = smalltalk.send(smalltalk.send(self, "_currentPageNo", []), "__eq", [(1)])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return smalltalk.send(self['@zoomLeftPageAnchor'], "_hide", []);})() : nil) : smalltalk.send($receiver, "_ifTrue_", [(function(){return smalltalk.send(self['@zoomLeftPageAnchor'], "_hide", []);})]));return ((($receiver = ((($receiver = smalltalk.send(self, "_currentPageNo", [])).klass === smalltalk.Number) ? $receiver >smalltalk.send(self['@book'], "_size", []) : smalltalk.send($receiver, "__gt", [smalltalk.send(self['@book'], "_size", [])]))).klass === smalltalk.Boolean) ? ($receiver ? (function(){return smalltalk.send(self['@zoomRightPageAnchor'], "_hide", []);})() : nil) : smalltalk.send($receiver, "_ifTrue_", [(function(){return smalltalk.send(self['@zoomRightPageAnchor'], "_hide", []);})]));})]);
return self;},
args: [],
source: unescape('closeZoom%0A%09%27.b-arrow%27%20asJQuery%20show.%0A%0A%09pageZoomBrush%20asJQuery%20%0A%09%09fadeOut%3A%20%27slow%27%20do%3A%20%5B%0A%09%09%09pageZoomWidget%20%3A%3D%20nil.%0A%09%09%09pageZoomBrush%20empty.%0A%09%09%09%22pageZoomBrush%20asJQuery%20show.%22%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%09%09%09zoomLeftPageAnchor%20%0A%09%09%09%09removeClass%3A%20%27active%27%3B%0A%09%09%09%09show.%0A%0A%09%09%09zoomRightPageAnchor%20%0A%09%09%09%09removeClass%3A%20%27active%27%3B%0A%09%09%09%09show.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09self%20currentPageNo%20%3D%201%20ifTrue%3A%20%5BzoomLeftPageAnchor%20hide%5D.%0A%09%09%09self%20currentPageNo%20%3E%20book%20size%20ifTrue%3A%20%5BzoomRightPageAnchor%20hide%5D.%0A%20%20%20%20%20%20%20%20%5D'),
messageSends: ["show", "asJQuery", "fadeOut:do:", "empty", "removeClass:", "ifTrue:", unescape("%3D"), "currentPageNo", "hide", unescape("%3E"), "size"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_closeZoomOr_'),
smalltalk.method({
selector: unescape('closeZoomOr%3A'),
category: 'zoom',
fn: function (aBlock){
var self=this;
smalltalk.send(self['@pageZoomWidget'], "_ifNil_ifNotNil_", [aBlock, (function(){smalltalk.send(self, "_closeZoom", []);return smalltalk.send(self, "_openDescriptions", []);})]);
return self;},
args: ["aBlock"],
source: unescape('closeZoomOr%3A%20aBlock%0A%09pageZoomWidget%20ifNil%3A%20aBlock%20ifNotNil%3A%20%5B%20%09self%20closeZoom.%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09self%20openDescriptions%5D'),
messageSends: ["ifNil:ifNotNil:", "closeZoom", "openDescriptions"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_currentPage'),
smalltalk.method({
selector: unescape('currentPage'),
category: 'accessing',
fn: function (){
var self=this;
return smalltalk.send(self['@book'], "_pageAt_ifAbsent_", [smalltalk.send(self, "_currentPageNo", []), (function(){return smalltalk.send(smalltalk.send(self['@book'], "_pages", []), "_last", []);})]);
return self;},
args: [],
source: unescape('currentPage%0A%09%5E%20book%20pageAt%3A%20self%20currentPageNo%20ifAbsent%3A%20%5Bbook%20pages%20last%5D'),
messageSends: ["pageAt:ifAbsent:", "currentPageNo", "last", "pages"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_currentPageNo'),
smalltalk.method({
selector: unescape('currentPageNo'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@currentPageNo']) == nil || $receiver == undefined) ? (function(){return self['@currentPageNo']=(1);})() : $receiver;
return self;},
args: [],
source: unescape('currentPageNo%0A%09%5E%20currentPageNo%20ifNil%3A%20%5BcurrentPageNo%20%3A%3D%201%5D'),
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_fullScreenStyle'),
smalltalk.method({
selector: unescape('fullScreenStyle'),
category: 'css',
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(unescape("%0A%09body.fullscreen%20%7B%0A%09%09overflow%3A%20hidden%3B%0A%09%7D%0A%0A%0A%09.fullscreen.bk-widget%20%7B%0A%09%09position%3A%20fixed%3B%0A%09%09width%3A%20100%25%3B%0A%09%09height%3A%20100%25%3B%0A%09%09z-index%3A%20200%3B%0A%09%09top%3A%200%3B%0A%09%09left%3A%200%3B%0A%09%09overflow-y%3A%20auto%3B%0A%09%7D%0A%0A%09.fullscreen.bk-widget%20.b-menu%20%7B%0A%09%09height%3A%2045px%3B%0A%09%7D%0A%0A%09.fullscreen.bk-widget%2C%0A%09.fullscreen.bk-widget%20.b-menu%20.b-selector%2C%0A%09.fullscreen.bk-widget%20.b-menu%20.b-selector%20ul%2C%0A%09.fullscreen.bk-widget%20.b-counter%20%7B%09%0A%09%09color%3A%20white%3B%0A%09%09background-color%3A%20black%3B%0A%09%7D%0A%0A%09.fullscreen%20.b-zoom-fullscreen%20%7B%0A%09%09position%3A%20absolute%3B%0A%09%09right%3A%200px%3B%0A%09%7D%0A%0A%09.fullscreen.bk-widget%20.b-download-book%20a%20%7B%0A%09%09position%3A%20absolute%3B%0A%09%09right%3A%2060px%3B%0A%09%7D%0A%0A%09.fullscreen%20.b-zoom-fullscreen%20a%20%7B%0A%09%09background%3A%20url%28"), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/unexpand_black.png%29%20no-repeat%3B%0A%09%7D%0A%0A%09.fullscreen%20.b-zoom-fullscreen%20a%3Ahover%20%7B%0A%09%09background%3A%20url%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/unexpand_white.png%29%20no-repeat%3B%0A%09%7D%0A")]);
return self;},
args: [],
source: unescape('fullScreenStyle%0A%09%5E%20%27%0A%09body.fullscreen%20%7B%0A%09%09overflow%3A%20hidden%3B%0A%09%7D%0A%0A%0A%09.fullscreen.bk-widget%20%7B%0A%09%09position%3A%20fixed%3B%0A%09%09width%3A%20100%25%3B%0A%09%09height%3A%20100%25%3B%0A%09%09z-index%3A%20200%3B%0A%09%09top%3A%200%3B%0A%09%09left%3A%200%3B%0A%09%09overflow-y%3A%20auto%3B%0A%09%7D%0A%0A%09.fullscreen.bk-widget%20.b-menu%20%7B%0A%09%09height%3A%2045px%3B%0A%09%7D%0A%0A%09.fullscreen.bk-widget%2C%0A%09.fullscreen.bk-widget%20.b-menu%20.b-selector%2C%0A%09.fullscreen.bk-widget%20.b-menu%20.b-selector%20ul%2C%0A%09.fullscreen.bk-widget%20.b-counter%20%7B%09%0A%09%09color%3A%20white%3B%0A%09%09background-color%3A%20black%3B%0A%09%7D%0A%0A%09.fullscreen%20.b-zoom-fullscreen%20%7B%0A%09%09position%3A%20absolute%3B%0A%09%09right%3A%200px%3B%0A%09%7D%0A%0A%09.fullscreen.bk-widget%20.b-download-book%20a%20%7B%0A%09%09position%3A%20absolute%3B%0A%09%09right%3A%2060px%3B%0A%09%7D%0A%0A%09.fullscreen%20.b-zoom-fullscreen%20a%20%7B%0A%09%09background%3A%20url%28%27%2C%20self%20scriptsRoot%2C%20%27images/unexpand_black.png%29%20no-repeat%3B%0A%09%7D%0A%0A%09.fullscreen%20.b-zoom-fullscreen%20a%3Ahover%20%7B%0A%09%09background%3A%20url%28%27%2C%20self%20scriptsRoot%2C%20%27images/unexpand_white.png%29%20no-repeat%3B%0A%09%7D%0A%27'),
messageSends: [unescape("%2C"), "scriptsRoot"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_goToPageNo_'),
smalltalk.method({
selector: unescape('goToPageNo%3A'),
category: 'callbacks',
fn: function (pageNo){
var self=this;
smalltalk.send(smalltalk.send(self['@bookContainer'], "_asJQuery", []), "_booklet_", [pageNo]);
return self;},
args: ["pageNo"],
source: unescape('goToPageNo%3A%20pageNo%0A%09bookContainer%20asJQuery%20booklet%3A%20%20%28pageNo%20%29.'),
messageSends: ["booklet:", "asJQuery"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_height'),
smalltalk.method({
selector: unescape('height'),
category: 'accessing',
fn: function (){
var self=this;
return smalltalk.send(((($receiver = ((($receiver = ((($receiver = smalltalk.send(self['@book'], "_height", [])).klass === smalltalk.Number) ? $receiver *smalltalk.send(self, "_width", []) : smalltalk.send($receiver, "__star", [smalltalk.send(self, "_width", [])]))).klass === smalltalk.Number) ? $receiver /smalltalk.send(self['@book'], "_width", []) : smalltalk.send($receiver, "__slash", [smalltalk.send(self['@book'], "_width", [])]))).klass === smalltalk.Number) ? $receiver /(2) : smalltalk.send($receiver, "__slash", [(2)])), "_rounded", []);
return self;},
args: [],
source: unescape('height%0A%09%5E%20%28%28book%20height%20*%20self%20width%20/%20book%20width%29%20/%202%29%20rounded'),
messageSends: ["rounded", unescape("/"), unescape("*"), "height", "width"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_ifIE_ifNotIE_'),
smalltalk.method({
selector: unescape('ifIE%3AifNotIE%3A'),
category: 'testing',
fn: function (aBlock, anotherBlock){
var self=this;
smalltalk.send(smalltalk.send(self, "_isIE", []), "_ifTrue_ifFalse_", [aBlock, anotherBlock]);
return self;},
args: ["aBlock", "anotherBlock"],
source: unescape('ifIE%3A%20aBlock%20ifNotIE%3A%20anotherBlock%0A%09self%20isIE%20ifTrue%3A%20aBlock%20ifFalse%3A%20anotherBlock'),
messageSends: ["ifTrue:ifFalse:", "isIE"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_initialize'),
smalltalk.method({
selector: unescape('initialize'),
category: 'initialize',
fn: function (){
var self=this;
smalltalk.send(self, "_initialize", [], smalltalk.Widget);
(self['@isFullscreen']=false);
return self;},
args: [],
source: unescape('initialize%0A%09super%20initialize.%0A%09isFullscreen%20%3A%3D%20false.'),
messageSends: ["initialize"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_isContainerSmall'),
smalltalk.method({
selector: unescape('isContainerSmall'),
category: 'testing',
fn: function (){
var self=this;
return ((($receiver = smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_width", [])).klass === smalltalk.Number) ? $receiver <(500) : smalltalk.send($receiver, "__lt", [(500)]));
return self;},
args: [],
source: unescape('isContainerSmall%0A%09%5E%20rootBrush%20asJQuery%20width%20%3C%20500'),
messageSends: [unescape("%3C"), "width", "asJQuery"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_isIE'),
smalltalk.method({
selector: unescape('isIE'),
category: 'testing',
fn: function (){
var self=this;
var ie=nil;
ie=jQuery.browser.msie;
return smalltalk.send(ie, "_notNil", []);
return self;},
args: [],
source: unescape('isIE%0A%09%7Cie%7C%0A%09ie%20%3A%3D%20%3CjQuery.browser.msie%3E.%0A%09%5E%20ie%20notNil.'),
messageSends: ["notNil"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_isRunInTestCase'),
smalltalk.method({
selector: unescape('isRunInTestCase'),
category: 'testing',
fn: function (){
var self=this;
return smalltalk.send(self, "_isTestCaseInContext_", [(smalltalk.getThisContext())]);
return self;},
args: [],
source: unescape('isRunInTestCase%0A%09%5E%20self%20isTestCaseInContext%3A%20thisContext%20'),
messageSends: ["isTestCaseInContext:"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_isTestCaseInContext_'),
smalltalk.method({
selector: unescape('isTestCaseInContext%3A'),
category: 'testing',
fn: function (aContext){
var self=this;
return (($receiver = smalltalk.send(aContext, "_home", [])) == nil || $receiver == undefined) ? (function(){return false;})() : (function(){return smalltalk.send(smalltalk.send(smalltalk.send(aContext, "_receiver", []), "_isKindOf_", [(smalltalk.TestCase || TestCase)]), "_or_", [(function(){return smalltalk.send(self, "_isTestCaseInContext_", [smalltalk.send(aContext, "_home", [])]);})]);})();
return self;},
args: ["aContext"],
source: unescape('isTestCaseInContext%3A%20aContext%20%0A%09%5E%20aContext%20home%20%0A%09%09ifNil%3A%20%5Bfalse%5D%0A%09%09ifNotNil%3A%20%5B%20%28aContext%20receiver%20isKindOf%3A%20TestCase%29%20or%3A%20%5B%20self%20isTestCaseInContext%3A%20aContext%20home%5D%5D.'),
messageSends: ["ifNil:ifNotNil:", "home", "or:", "isKindOf:", "receiver", "isTestCaseInContext:"],
referencedClasses: ["TestCase"]
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_leftPage'),
smalltalk.method({
selector: unescape('leftPage'),
category: 'accessing',
fn: function (){
var self=this;
return smalltalk.send(self['@book'], "_pageAt_ifAbsent_", [((($receiver = self['@currentPageNo']).klass === smalltalk.Number) ? $receiver -(1) : smalltalk.send($receiver, "__minus", [(1)])), (function(){return smalltalk.send((smalltalk.Page || Page), "_new", []);})]);
return self;},
args: [],
source: unescape('leftPage%0A%09%5E%20book%20pageAt%3A%20%28currentPageNo%20-%201%29%20ifAbsent%3A%20%5BPage%20new%5D.'),
messageSends: ["pageAt:ifAbsent:", unescape("-"), "new"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_loadBookThenRenderOn_'),
smalltalk.method({
selector: unescape('loadBookThenRenderOn%3A'),
category: 'loading',
fn: function (bookBrush){
var self=this;
var renderBlock=nil;
(renderBlock=(function(aBook){return smalltalk.send(self, "_renderBook_on_", [aBook, bookBrush]);}));
(($receiver = self['@book']) == nil || $receiver == undefined) ? (function(){return smalltalk.send(smalltalk.send(self, "_loader", []), "_loadBookFromJSONOnSuccess_", [renderBlock]);})() : (function(){smalltalk.send(self['@book'], "_reset", []);return smalltalk.send(renderBlock, "_value_", [self['@book']]);})();
return self;},
args: ["bookBrush"],
source: unescape('loadBookThenRenderOn%3A%20bookBrush%0A%09%7CrenderBlock%7C%0A%09renderBlock%20%3A%3D%20%5B%3AaBook%7C%20self%20renderBook%3AaBook%20on%3A%20bookBrush%5D.%0A%09book%20%0A%09%09ifNil%3A%20%5Bself%20loader%20loadBookFromJSONOnSuccess%3A%20renderBlock%5D%0A%09%09ifNotNil%3A%20%5B%09book%20reset.%09%09%09%09%09%0A%09%09%09%09%09renderBlock%20value%3A%20book%5D.'),
messageSends: ["renderBook:on:", "ifNil:ifNotNil:", "loadBookFromJSONOnSuccess:", "loader", "reset", "value:"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_loader'),
smalltalk.method({
selector: unescape('loader'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@loader']) == nil || $receiver == undefined) ? (function(){return self['@loader']=smalltalk.send((smalltalk.SouvignyLoader || SouvignyLoader), "_new", []);})() : $receiver;
return self;},
args: [],
source: unescape('loader%0A%09%5E%20loader%20ifNil%3A%20%5Bloader%20%3A%3D%20SouvignyLoader%20new%5D'),
messageSends: ["ifNil:", "new"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_loader_'),
smalltalk.method({
selector: unescape('loader%3A'),
category: 'accessing',
fn: function (aBibNumLoader){
var self=this;
self['@loader']=aBibNumLoader;
return self;},
args: ["aBibNumLoader"],
source: unescape('loader%3A%20aBibNumLoader%0A%09loader%20%3A%3D%20aBibNumLoader'),
messageSends: [],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_navigatorWidth'),
smalltalk.method({
selector: unescape('navigatorWidth'),
category: 'accessing',
fn: function (){
var self=this;
return smalltalk.send((smalltalk.AbstractBookNavigatorWidget || AbstractBookNavigatorWidget), "_width", []);
return self;},
args: [],
source: unescape('navigatorWidth%0A%09%5E%20AbstractBookNavigatorWidget%20width'),
messageSends: ["width"],
referencedClasses: ["AbstractBookNavigatorWidget"]
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_onPageChangeDo_'),
smalltalk.method({
selector: unescape('onPageChangeDo%3A'),
category: 'announcements',
fn: function (aBlockWithArg){
var self=this;
smalltalk.send(smalltalk.send(self, "_announcer", []), "_on_do_", [(smalltalk.PageChangeAnnouncement || PageChangeAnnouncement), (function(aPageChangeAnnouncement){return smalltalk.send(aBlockWithArg, "_value_", [smalltalk.send(aPageChangeAnnouncement, "_page", [])]);})]);
return self;},
args: ["aBlockWithArg"],
source: unescape('onPageChangeDo%3A%20aBlockWithArg%0A%09self%20announcer%20%0A%09%09on%3A%20PageChangeAnnouncement%20%0A%09%09do%3A%20%5B%3AaPageChangeAnnouncement%7C%20%0A%09%09%09aBlockWithArg%20value%3A%20aPageChangeAnnouncement%20page%5D'),
messageSends: ["on:do:", "announcer", "value:", "page"],
referencedClasses: ["PageChangeAnnouncement"]
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_openDescriptions'),
smalltalk.method({
selector: unescape('openDescriptions'),
category: 'descriptions',
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(self['@pageDescriptionsBrush'], "_asJQuery", []), "_hide", []);
smalltalk.send(self['@pageDescriptionsBrush'], "_contents_", [(function(html){smalltalk.send(smalltalk.send(smalltalk.send(html, "_div", []), "_asJQuery", []), "_html_", [smalltalk.send(smalltalk.send(self, "_leftPage", []), "_description", [])]);return smalltalk.send(smalltalk.send(smalltalk.send(html, "_div", []), "_asJQuery", []), "_html_", [smalltalk.send(smalltalk.send(self, "_rightPage", []), "_description", [])]);})]);
smalltalk.send(smalltalk.send(self['@pageDescriptionsBrush'], "_asJQuery", []), "_fadeIn", []);
return self;},
args: [],
source: unescape('openDescriptions%0A%09pageDescriptionsBrush%20asJQuery%20hide.%0A%09pageDescriptionsBrush%20contents%3A%20%5B%3Ahtml%7C%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%28html%20div%20asJQuery%29%20html%3A%20self%20leftPage%20description.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%28html%20div%20asJQuery%29%20html%3A%20self%20rightPage%20description.%0A%20%20%20%20%20%20%20%20%5D.%0A%09pageDescriptionsBrush%20asJQuery%20fadeIn.'),
messageSends: ["hide", "asJQuery", "contents:", "html:", "div", "description", "leftPage", "rightPage", "fadeIn"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_openPage_'),
smalltalk.method({
selector: unescape('openPage%3A'),
category: 'callbacks',
fn: function (aPage){
var self=this;
smalltalk.send(self, "_goToPageNo_", [smalltalk.send(aPage, "_pageNo", [])]);
return self;},
args: ["aPage"],
source: unescape('openPage%3A%20aPage%0A%09self%20goToPageNo%3A%20aPage%20pageNo.'),
messageSends: ["goToPageNo:", "pageNo"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_openPageNo_'),
smalltalk.method({
selector: unescape('openPageNo%3A'),
category: 'callbacks',
fn: function (anInteger){
var self=this;
(self['@currentPageNo']=anInteger);
smalltalk.send(self['@book'], "_pagesNo_do_", [[((($receiver = anInteger).klass === smalltalk.Number) ? $receiver -(1) : smalltalk.send($receiver, "__minus", [(1)])),anInteger], (function(aPage){return smalltalk.send(aPage, "_renderWidth_height_", [smalltalk.send(((($receiver = smalltalk.send(self, "_width", [])).klass === smalltalk.Number) ? $receiver /(2) : smalltalk.send($receiver, "__slash", [(2)])), "_rounded", []), smalltalk.send(self, "_height", [])]);})]);
return self;},
args: ["anInteger"],
source: unescape('openPageNo%3A%20anInteger%0A%09currentPageNo%20%3A%3D%20anInteger.%0A%09book%20%0A%09%09pagesNo%3A%20%7BanInteger%20-%201.%20anInteger%7D%20%0A%09%09do%3A%20%5B%3AaPage%7C%20aPage%20renderWidth%3A%20%28self%20width%20/%202%29%20rounded%20height%3A%20self%20height%5D.'),
messageSends: ["pagesNo:do:", unescape("-"), "renderWidth:height:", "rounded", unescape("/"), "width", "height"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_reloadWidget'),
smalltalk.method({
selector: unescape('reloadWidget'),
category: 'callbacks',
fn: function (){
var self=this;
smalltalk.send(self['@rootBrush'], "_contents_", [(function(html){return smalltalk.send(self, "_renderWidgetOn_", [html]);})]);
return self;},
args: [],
source: unescape('reloadWidget%0A%09rootBrush%20contents%3A%20%5B%3Ahtml%7C%20self%20renderWidgetOn%3A%20html%5D.'),
messageSends: ["contents:", "renderWidgetOn:"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_renderBook_on_'),
smalltalk.method({
selector: unescape('renderBook%3Aon%3A'),
category: 'rendering',
fn: function (aBook, aBrush){
var self=this;
(self['@book']=aBook);
smalltalk.send(aBrush, "_contents_", [(function(html){return smalltalk.send(smalltalk.send(aBook, "_pages", []), "_do_", [(function(aPage){return smalltalk.send(aPage, "_brush_", [(function($rec){smalltalk.send($rec, "_rel_", [smalltalk.send(aPage, "_title", [])]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send(html, "_div", []))]);})]);})]);
((($receiver = smalltalk.send(self, "_isContainerSmall", [])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_addClass_", ["small"]);})() : nil) : smalltalk.send($receiver, "_ifTrue_", [(function(){return smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_addClass_", ["small"]);})]));
(($receiver = smalltalk.send(smalltalk.send(self['@bookContainer'], "_asJQuery", []), "_at_", ["booklet"])) != nil && $receiver != undefined) ? (function(){return smalltalk.send(smalltalk.send(self['@bookContainer'], "_asJQuery", []), "_booklet_", [smalltalk.send(self, "_bookletOptions", [])]);})() : nil;
smalltalk.send(smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_find_", [unescape(".b-wrap-left")]), "_click_", [(function(){return smalltalk.send(self, "_zoomLeftPage", []);})]);
smalltalk.send(smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_find_", [unescape(".b-wrap-right%2C%20.b-page-cover")]), "_click_", [(function(){return smalltalk.send(self, "_zoomRightPage", []);})]);
smalltalk.send(smalltalk.send(self['@book'], "_downloadUrl", []), "_ifNotEmpty_", [(function(){return smalltalk.send(self['@downloadBrush'], "_contents_", [(function(html){return smalltalk.send(smalltalk.send(html, "_a", []), "_href_", [smalltalk.send(aBook, "_downloadUrl", [])]);})]);})]);
((($receiver = self['@isFullscreen']).klass === smalltalk.Boolean) ? ($receiver ? (function(){return smalltalk.send(self, "_renderBookNavigator", []);})() : nil) : smalltalk.send($receiver, "_ifTrue_", [(function(){return smalltalk.send(self, "_renderBookNavigator", []);})]));
return self;},
args: ["aBook", "aBrush"],
source: unescape('renderBook%3A%20aBook%20on%3A%20aBrush%0A%09book%20%3A%3D%20aBook.%0A%0A%09aBrush%20contents%3A%20%5B%3Ahtml%7C%0A%09%09aBook%20pages%20do%3A%20%5B%3AaPage%7C%20%09aPage%20brush%3A%20%28html%20div%0A%20%20%20%20%20%20%20%20%09%09%20%20%20%20%20%20%20%20%20%09%09%09%09%09%09%09%09rel%3A%20aPage%20title%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%09%09%09yourself%29%20%20%5D%09%5D.%0A%09%0A%09self%20isContainerSmall%20ifTrue%3A%20%5BrootBrush%20asJQuery%20addClass%3A%20%27small%27%5D.%0A%09%28bookContainer%20asJQuery%20at%3A%20%27booklet%27%29%20ifNotNil%3A%20%5B%20bookContainer%20asJQuery%20booklet%3A%20%28self%20bookletOptions%29%20%5D.%0A%09%28rootBrush%20asJQuery%20find%3A%20%27.b-wrap-left%27%29%20click%3A%20%5Bself%20zoomLeftPage%5D.%0A%09%28rootBrush%20asJQuery%20find%3A%20%27.b-wrap-right%2C%20.b-page-cover%27%29%20click%3A%20%5Bself%20zoomRightPage%5D.%0A%09%0A%09book%20downloadUrl%20ifNotEmpty%3A%20%5BdownloadBrush%20contents%3A%20%5B%3Ahtml%7C%20html%20a%20href%3A%20aBook%20downloadUrl%5D%5D.%0A%09%0A%09isFullscreen%20ifTrue%3A%20%5Bself%20renderBookNavigator%5D'),
messageSends: ["contents:", "do:", "pages", "brush:", "rel:", "title", "yourself", "div", "ifTrue:", "isContainerSmall", "addClass:", "asJQuery", "ifNotNil:", "at:", "booklet:", "bookletOptions", "click:", "find:", "zoomLeftPage", "zoomRightPage", "ifNotEmpty:", "downloadUrl", "href:", "a", "renderBookNavigator"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_renderBookMenuOn_'),
smalltalk.method({
selector: unescape('renderBookMenuOn%3A'),
category: 'rendering',
fn: function (html){
var self=this;
(self['@menuJQuery']=(function($rec){smalltalk.send($rec, "_class_", [unescape("book-menu")]);return smalltalk.send($rec, "_asJQuery", []);})(smalltalk.send(html, "_div", [])));
return self;},
args: ["html"],
source: unescape('renderBookMenuOn%3A%20html%0A%09menuJQuery%20%3A%3D%20html%20div%0A%09%09class%3A%20%27book-menu%27%3B%0A%09%09asJQuery.'),
messageSends: ["class:", "asJQuery", "div"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_renderBookNavigator'),
smalltalk.method({
selector: unescape('renderBookNavigator'),
category: 'rendering',
fn: function (){
var self=this;
var navigatorDiv=nil;
(navigatorDiv=smalltalk.send(unescape("%3Cdiv%3E%3C/div%3E"), "_asJQuery", []));
smalltalk.send(navigatorDiv, "_insertAfter_", [self['@menuJQuery']]);
smalltalk.send([(smalltalk.BookBookmarkNavigatorWidget || BookBookmarkNavigatorWidget),(smalltalk.BookThumbnailNavigatorWidget || BookThumbnailNavigatorWidget)], "_do_", [(function(aNavigatorClass){var navigator=nil;
(navigator=(function($rec){smalltalk.send($rec, "_book_", [self['@book']]);smalltalk.send($rec, "_appendToJQuery_", [navigatorDiv]);smalltalk.send($rec, "_onPageChangeDo_", [(function(aPage){return smalltalk.send(self, "_openPage_", [aPage]);})]);smalltalk.send($rec, "_highlightPage_", [smalltalk.send(self, "_currentPage", [])]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send(aNavigatorClass, "_new", [])));return smalltalk.send(self, "_onPageChangeDo_", [(function(aPage){return smalltalk.send(navigator, "_highlightPage_", [aPage]);})]);})]);
return self;},
args: [],
source: unescape('renderBookNavigator%0A%09%7CnavigatorDiv%7C%0A%09navigatorDiv%20%3A%3D%20%27%3Cdiv%3E%3C/div%3E%27%20asJQuery.%0A%09navigatorDiv%20insertAfter%3A%20menuJQuery.%0A%0A%09%7BBookBookmarkNavigatorWidget.%20BookThumbnailNavigatorWidget%7D%20do%3A%20%5B%3AaNavigatorClass%7C%20%7Cnavigator%7C%0A%09%09navigator%20%3A%3D%20aNavigatorClass%20new%0A%09%09%09%09%09%09book%3A%20book%3B%0A%09%09%09%09%09%09appendToJQuery%3A%20navigatorDiv%3B%0A%09%09%09%09%09%09onPageChangeDo%3A%20%5B%3AaPage%7C%20self%20openPage%3A%20aPage%5D%3B%0A%09%09%09%09%09%09highlightPage%3A%20self%20currentPage%3B%0A%09%09%09%09%09%09yourself.%0A%0A%09%09self%20onPageChangeDo%3A%20%5B%3AaPage%7C%20%20navigator%20highlightPage%3A%20aPage%5D.%0A%09%5D'),
messageSends: ["asJQuery", "insertAfter:", "do:", "book:", "appendToJQuery:", "onPageChangeDo:", "openPage:", "highlightPage:", "currentPage", "yourself", "new"],
referencedClasses: ["BookBookmarkNavigatorWidget", "BookThumbnailNavigatorWidget"]
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_renderBookOn_'),
smalltalk.method({
selector: unescape('renderBookOn%3A'),
category: 'rendering',
fn: function (html){
var self=this;
(self['@bookContainer']=smalltalk.send(html, "_div", []));
(function($rec){smalltalk.send($rec, "_class_", ["book"]);return smalltalk.send($rec, "_with_", [(function(){var bookBrush=nil;
(self['@leftFolioBrush']=smalltalk.send(smalltalk.send(html, "_div", []), "_class_", [unescape("b-counter")]));(self['@rightFolioBrush']=smalltalk.send(smalltalk.send(html, "_div", []), "_class_", [unescape("b-counter")]));(bookBrush=(function($rec){smalltalk.send($rec, "_class_", [unescape("b-load")]);smalltalk.send($rec, "_with_", [(function(){return (function($rec){smalltalk.send($rec, "_class_", ["loading"]);return smalltalk.send($rec, "_with_", [(function(){return smalltalk.send(smalltalk.send(html, "_img", []), "_src_", [smalltalk.send(smalltalk.send(self, "_scriptsRoot", []), "__comma", [unescape("images/ajax-loader.gif")])]);})]);})(smalltalk.send(html, "_div", []));})]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send(html, "_div", [])));return smalltalk.send(self, "_loadBookThenRenderOn_", [bookBrush]);})]);})(self['@bookContainer']);
return self;},
args: ["html"],
source: unescape('renderBookOn%3A%20html%0A%09bookContainer%20%3A%3D%20html%20div.%0A%09bookContainer%0A%09%20%20%20%20class%3A%20%27book%27%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20with%3A%20%5B%09%7CbookBrush%7C%0A%09%09%09leftFolioBrush%20%3A%3D%20html%20div%20class%3A%20%27b-counter%27.%0A%09%09%09rightFolioBrush%20%3A%3D%20html%20div%20class%3A%20%27b-counter%27.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09bookBrush%20%3A%3D%20html%20div%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09class%3A%20%27b-load%27%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09with%3A%20%5Bhtml%20div%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09class%3A%20%27loading%27%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09with%3A%20%5B%20html%20img%20src%3A%20self%20scriptsRoot%2C%20%27images/ajax-loader.gif%27%5D%20%5D%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09yourself.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09self%20loadBookThenRenderOn%3A%20bookBrush%5D'),
messageSends: ["div", "class:", "with:", "src:", "img", unescape("%2C"), "scriptsRoot", "yourself", "loadBookThenRenderOn:"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_renderDevToolsOn_'),
smalltalk.method({
selector: unescape('renderDevToolsOn%3A'),
category: 'rendering',
fn: function (html){
var self=this;
((($receiver = smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send((smalltalk.Smalltalk || Smalltalk), "_current", []), "_at_", ["Browser"]), "_notNil", []), "_and_", [(function(){return smalltalk.send(smalltalk.send(self, "_isRunInTestCase", []), "_not", []);})])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return (function($rec){smalltalk.send($rec, "_addButton_action_", ["Reload booklet", (function(){return smalltalk.send(self, "_reloadWidget", []);})]);smalltalk.send($rec, "_addButton_action_", ["Inspect booklet", (function(){return smalltalk.send(self, "_inspect", []);})]);return smalltalk.send($rec, "_addButton_action_", ["Toggle fullscreen", (function(){return smalltalk.send(self, "_toggleFullscreen", []);})]);})((smalltalk.AFIIDETools || AFIIDETools));})() : nil) : smalltalk.send($receiver, "_ifTrue_", [(function(){return (function($rec){smalltalk.send($rec, "_addButton_action_", ["Reload booklet", (function(){return smalltalk.send(self, "_reloadWidget", []);})]);smalltalk.send($rec, "_addButton_action_", ["Inspect booklet", (function(){return smalltalk.send(self, "_inspect", []);})]);return smalltalk.send($rec, "_addButton_action_", ["Toggle fullscreen", (function(){return smalltalk.send(self, "_toggleFullscreen", []);})]);})((smalltalk.AFIIDETools || AFIIDETools));})]));
return self;},
args: ["html"],
source: unescape('renderDevToolsOn%3A%20html%0A%09%28%28Smalltalk%20current%20at%3A%20%27Browser%27%29%20notNil%20and%3A%20%5Bself%20isRunInTestCase%20not%5D%29%20ifTrue%3A%0A%09%09%20%5B%20%20%20AFIIDETools%20%0A%09%09%09%09%09addButton%3A%20%27Reload%20booklet%27%20action%3A%20%5B%20self%20reloadWidget%20%5D%3B%0A%09%09%09%09%09addButton%3A%20%27Inspect%20booklet%27%20action%3A%20%5B%20self%20inspect%20%5D%3B%0A%09%09%09%09%09addButton%3A%20%27Toggle%20fullscreen%27%20action%3A%20%5B%20self%20toggleFullscreen%20%5D%09%5D'),
messageSends: ["ifTrue:", "and:", "notNil", "at:", "current", "not", "isRunInTestCase", "addButton:action:", "reloadWidget", "inspect", "toggleFullscreen"],
referencedClasses: ["Smalltalk", "AFIIDETools"]
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_renderDownloadBookOn_'),
smalltalk.method({
selector: unescape('renderDownloadBookOn%3A'),
category: 'rendering',
fn: function (html){
var self=this;
(self['@downloadBrush']=smalltalk.send(smalltalk.send(html, "_div", []), "_class_", [unescape("b-download-book")]));
return self;},
args: ["html"],
source: unescape('renderDownloadBookOn%3A%20html%0A%09downloadBrush%20%3A%3D%20html%20div%20class%3A%20%27b-download-book%27'),
messageSends: ["class:", "div"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_renderFullscreenControlsOn_'),
smalltalk.method({
selector: unescape('renderFullscreenControlsOn%3A'),
category: 'rendering',
fn: function (html){
var self=this;
(function($rec){smalltalk.send($rec, "_class_", [unescape("b-zoom-fullscreen")]);return smalltalk.send($rec, "_with_", [(function(){return smalltalk.send(smalltalk.send(html, "_a", []), "_onClick_", [(function(){return smalltalk.send(self, "_toggleFullscreen", []);})]);})]);})(smalltalk.send(html, "_div", []));
return self;},
args: ["html"],
source: unescape('renderFullscreenControlsOn%3A%20html%0A%09html%20div%20%0A%09%09class%3A%20%27b-zoom-fullscreen%27%3B%0A%09%09with%3A%20%5B%20html%20a%20onClick%3A%20%5Bself%20toggleFullscreen%5D%20%5D.'),
messageSends: ["class:", "with:", "onClick:", "a", "toggleFullscreen", "div"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_renderOn_'),
smalltalk.method({
selector: unescape('renderOn%3A'),
category: 'rendering',
fn: function (html){
var self=this;
smalltalk.send(self, "_renderDevToolsOn_", [html]);
(self['@rootBrush']=smalltalk.send(html, "_root", []));
smalltalk.send(self, "_renderWidgetOn_", [html]);
return self;},
args: ["html"],
source: unescape('renderOn%3A%20html%0A%20%20%20%20%20%20%20%20self%20renderDevToolsOn%3A%20html.%0A%09rootBrush%20%3A%3D%20html%20root.%0A%09self%20renderWidgetOn%3A%20html.%0A%09%22window%20at%3A%27onresize%27%20put%3A%20%5Bself%20reloadWidget%5D.%22'),
messageSends: ["renderDevToolsOn:", "root", "renderWidgetOn:"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_renderPage_class_on_'),
smalltalk.method({
selector: unescape('renderPage%3Aclass%3Aon%3A'),
category: 'zoom',
fn: function (aPage, aCssClass, html){
var self=this;
smalltalk.send((function($rec){smalltalk.send($rec, "_class_", [aCssClass]);smalltalk.send($rec, "_with_", [(function(){return self['@pageZoomWidget']=(function($rec){smalltalk.send($rec, "_page_", [aPage]);smalltalk.send($rec, "_renderOn_", [html]);smalltalk.send($rec, "_onCloseDo_", [(function(){return (function($rec){smalltalk.send($rec, "_closeZoom", []);return smalltalk.send($rec, "_openDescriptions", []);})(self);})]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send((smalltalk.PageWidget || PageWidget), "_new", []));})]);return smalltalk.send($rec, "_asJQuery", []);})(smalltalk.send(html, "_div", [])), "_fadeIn_", ["slow"]);
return self;},
args: ["aPage", "aCssClass", "html"],
source: unescape('renderPage%3A%20aPage%20class%3A%20aCssClass%20on%3A%20html%0A%09%28html%20div%0A%09%09class%3A%20aCssClass%3B%0A%09%09with%3A%5B%09pageZoomWidget%20%3A%3D%20PageWidget%20new%0A%09%09%09%09%09%09%09%09%09%09page%3A%20aPage%3B%0A%09%09%09%09%09%09%09%09%09%09renderOn%3A%20html%3B%0A%09%09%09%09%09%09%09%09%09%09onCloseDo%3A%20%5B%09self%20%0A%09%09%09%09%09%09%09%09%09%09%09%09%09%09closeZoom%3B%20%0A%09%09%09%09%09%09%09%09%09%09%09%09%09%09openDescriptions%5D%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09yourself%20%09%09%09%09%09%09%09%09%5D%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20asJQuery%29%20fadeIn%3A%20%27slow%27.%20'),
messageSends: ["fadeIn:", "class:", "with:", "page:", "renderOn:", "onCloseDo:", "closeZoom", "openDescriptions", "yourself", "new", "asJQuery", "div"],
referencedClasses: ["PageWidget"]
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_renderPageDescriptionOn_'),
smalltalk.method({
selector: unescape('renderPageDescriptionOn%3A'),
category: 'rendering',
fn: function (html){
var self=this;
(self['@pageDescriptionsBrush']=(function($rec){smalltalk.send($rec, "_class_", [unescape("page-desc")]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send(html, "_div", [])));
return self;},
args: ["html"],
source: unescape('renderPageDescriptionOn%3A%20html%20%09%0A%09pageDescriptionsBrush%20%3A%3D%20html%20div%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20class%3A%20%27page-desc%27%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%09yourself.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20'),
messageSends: ["class:", "yourself", "div"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_renderScripts'),
smalltalk.method({
selector: unescape('renderScripts'),
category: 'rendering',
fn: function (){
var self=this;
var head=nil;
(head=smalltalk.send("head", "_asJQuery", []));
((($receiver = smalltalk.send(smalltalk.send(smalltalk.send(head, "_find_", [unescape("script%5Bsrc*%3D%22booklet%22%5D")]), "_length", []), "__eq", [(0)])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return smalltalk.send(self, "_renderScriptsOn_", [smalltalk.send((smalltalk.HTMLCanvas || HTMLCanvas), "_onJQuery_", [head])]);})() : nil) : smalltalk.send($receiver, "_ifTrue_", [(function(){return smalltalk.send(self, "_renderScriptsOn_", [smalltalk.send((smalltalk.HTMLCanvas || HTMLCanvas), "_onJQuery_", [head])]);})]));
((($receiver = self['@isFullscreen']).klass === smalltalk.Boolean) ? ($receiver ? (function(){return smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_addClass_", ["fullscreen"]);})() : (function(){return smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_removeClass_", ["fullscreen"]);})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_addClass_", ["fullscreen"]);}), (function(){return smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_removeClass_", ["fullscreen"]);})]));
return self;},
args: [],
source: unescape('renderScripts%0A%09%7Chead%7C%0A%09head%20%3A%3D%20%27head%27%20asJQuery.%0A%09%28head%20find%3A%20%27script%5Bsrc*%3D%22booklet%22%5D%27%29%20length%20%3D%200%20ifTrue%3A%20%5B%0A%09%09self%20renderScriptsOn%3A%20%28HTMLCanvas%20onJQuery%3A%20head%29.%0A%20%20%20%20%20%20%20%20%20%20%5D.%0A%09isFullscreen%20%0A%09%09ifTrue%3A%20%5B%27body%27%20asJQuery%20addClass%3A%20%27fullscreen%27%5D%20%0A%09%09ifFalse%3A%20%5B%27body%27%20asJQuery%20removeClass%3A%20%27fullscreen%27%5D.'),
messageSends: ["asJQuery", "ifTrue:", unescape("%3D"), "length", "find:", "renderScriptsOn:", "onJQuery:", "ifTrue:ifFalse:", "addClass:", "removeClass:"],
referencedClasses: ["HTMLCanvas"]
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_renderScriptsOn_'),
smalltalk.method({
selector: unescape('renderScriptsOn%3A'),
category: 'rendering',
fn: function (html){
var self=this;
smalltalk.send([unescape("booklet/jquery.booklet.1.2.0.css"), unescape("iviewer/jquery.iviewer.css")], "_do_", [(function(anUrl){return (function($rec){smalltalk.send($rec, "_href_", [smalltalk.send(smalltalk.send(self, "_scriptsRoot", []), "__comma", [anUrl])]);smalltalk.send($rec, "_type_", [unescape("text/css")]);return smalltalk.send($rec, "_rel_", ["stylesheet"]);})(smalltalk.send(html, "_link", []));})]);
(function($rec){smalltalk.send($rec, "_type_", [unescape("text/css")]);return smalltalk.send($rec, "_with_", [smalltalk.send(self, "_style", [])]);})(smalltalk.send(html, "_style", []));
(($receiver = smalltalk.send((typeof jQuery == 'undefined' ? nil : jQuery), "_at_", ["ui"])) == nil || $receiver == undefined) ? (function(){return (function($rec){smalltalk.send($rec, "_type_", [unescape("text/javascript")]);return smalltalk.send($rec, "_src_", [unescape("http%3A//ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js")]);})(smalltalk.send(html, "_script", []));})() : $receiver;
smalltalk.send([unescape("booklet/jquery.easing.1.3.js"), unescape("booklet/jquery.booklet.1.2.0.min.js"), unescape("iviewer/jquery.iviewer.min.js"), unescape("iviewer/jquery.mousewheel.min.js")], "_do_", [(function(anUrl){return (function($rec){smalltalk.send($rec, "_type_", [unescape("text/javascript")]);return smalltalk.send($rec, "_src_", [smalltalk.send(smalltalk.send(self, "_scriptsRoot", []), "__comma", [anUrl])]);})(smalltalk.send(html, "_script", []));})]);
return self;},
args: ["html"],
source: unescape('renderScriptsOn%3A%20html%0A%09%23%28%20%09%27booklet/jquery.booklet.1.2.0.css%27%0A%20%20%20%20%20%20%20%20%20%20%09%27iviewer/jquery.iviewer.css%27%20%29%20do%3A%20%5B%3AanUrl%7C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09html%20link%0A%09%09%09%09%09%09%09%09%09%09%09href%3A%20self%20scriptsRoot%2C%20anUrl%3B%0A%09%09%09%09%09%09%09%09%09%09%09type%3A%27text/css%27%3B%0A%09%09%09%09%09%09%09%09%09%09%09rel%3A%27stylesheet%27%09%5D.%0A%09html%20style%0A%09%09type%3A%20%27text/css%27%3B%0A%09%09with%3A%20self%20style.%0A%0A%09%28jQuery%20at%3A%20%27ui%27%29%20ifNil%3A%20%5B%20html%20script%0A%20%20%20%20%20%20%20%20%20%09%09%09%09%20%20%20%20%09type%3A%20%27text/javascript%27%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09src%3A%20%09%27http%3A//ajax.googleapis.com/ajax/libs/jqueryui/1.8.9/jquery-ui.min.js%27%20%5D.%20%0A%0A%09%23%28%09%27booklet/jquery.easing.1.3.js%27%0A%09%20%20%09%27booklet/jquery.booklet.1.2.0.min.js%27%20%0A%20%20%20%20%20%20%20%20%20%20%09%27iviewer/jquery.iviewer.min.js%27%0A%20%20%20%20%20%20%20%20%20%20%09%27iviewer/jquery.mousewheel.min.js%27%0A%20%20%20%20%20%20%20%20%20%09%29%20do%3A%20%5B%3AanUrl%7C%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20html%20script%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09type%3A%20%27text/javascript%27%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20src%3A%20self%20scriptsRoot%2C%20anUrl%5D'),
messageSends: ["do:", "href:", unescape("%2C"), "scriptsRoot", "type:", "rel:", "link", "with:", "style", "ifNil:", "at:", "src:", "script"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_renderWidgetOn_'),
smalltalk.method({
selector: unescape('renderWidgetOn%3A'),
category: 'rendering',
fn: function (html){
var self=this;
(function($rec){smalltalk.send($rec, "_class_", [smalltalk.send(self, "_widgetClass", [])]);return smalltalk.send($rec, "_with_", [(function(){return (function($rec){smalltalk.send($rec, "_renderScripts", []);smalltalk.send($rec, "_renderFullscreenControlsOn_", [html]);smalltalk.send($rec, "_renderDownloadBookOn_", [html]);smalltalk.send($rec, "_renderBookMenuOn_", [html]);smalltalk.send($rec, "_renderZoomControlsOn_", [html]);smalltalk.send($rec, "_renderBookOn_", [html]);return smalltalk.send($rec, "_renderPageDescriptionOn_", [html]);})(self);})]);})(smalltalk.send(html, "_div", []));
return self;},
args: ["html"],
source: unescape('renderWidgetOn%3A%20html%0A%09html%20div%0A%09%09class%3A%20self%20widgetClass%3B%20%0A%09%09with%3A%20%5B%09self%20%0A%09%09%09%09%09renderScripts%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09renderFullscreenControlsOn%3A%20html%3B%0A%09%09%09%09%09renderDownloadBookOn%3A%20html%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09renderBookMenuOn%3A%20html%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09renderZoomControlsOn%3A%20html%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09renderBookOn%3A%20html%3B%0A%09%09%09%09%09renderPageDescriptionOn%3A%20html%20%09%5D.'),
messageSends: ["class:", "widgetClass", "with:", "renderScripts", "renderFullscreenControlsOn:", "renderDownloadBookOn:", "renderBookMenuOn:", "renderZoomControlsOn:", "renderBookOn:", "renderPageDescriptionOn:", "div"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_renderZoomControlsOn_'),
smalltalk.method({
selector: unescape('renderZoomControlsOn%3A'),
category: 'rendering',
fn: function (html){
var self=this;
(function($rec){smalltalk.send($rec, "_class_", [unescape("b-zoom-magnify")]);return smalltalk.send($rec, "_with_", [(function(){(self['@zoomLeftPageAnchor']=smalltalk.send(smalltalk.send(smalltalk.send(html, "_a", []), "_onClick_", [(function(){return smalltalk.send(self, "_zoomLeftPage", []);})]), "_asJQuery", []));smalltalk.send(self['@zoomLeftPageAnchor'], "_hide", []);(self['@zoomRightPageAnchor']=smalltalk.send(smalltalk.send(smalltalk.send(html, "_a", []), "_onClick_", [(function(){return smalltalk.send(self, "_zoomRightPage", []);})]), "_asJQuery", []));smalltalk.send(self['@zoomRightPageAnchor'], "_hide", []);return (self['@pageZoomBrush']=(function($rec){smalltalk.send($rec, "_class_", [unescape("b-zoom")]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send(html, "_div", [])));})]);})(smalltalk.send(html, "_div", []));
return self;},
args: ["html"],
source: unescape('renderZoomControlsOn%3A%20html%0A%09html%20div%0A%09%09class%3A%20%27b-zoom-magnify%27%3B%0A%09%09with%3A%20%5B%20%09zoomLeftPageAnchor%20%3A%3D%20%28html%20a%20onClick%3A%20%5Bself%20zoomLeftPage%5D%29%20asJQuery.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09zoomLeftPageAnchor%20hide.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09zoomRightPageAnchor%20%3A%3D%20%28html%20a%20onClick%3A%20%5Bself%20zoomRightPage%5D%29%20asJQuery.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09zoomRightPageAnchor%20hide.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20pageZoomBrush%20%3A%3D%20html%20div%20%0A%09%09%09%09%09%09class%3A%20%27b-zoom%27%3B%0A%09%09%09%09%09%09yourself.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%5D.'),
messageSends: ["class:", "with:", "asJQuery", "onClick:", "a", "zoomLeftPage", "hide", "zoomRightPage", "yourself", "div"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_reset'),
smalltalk.method({
selector: unescape('reset'),
category: 'show',
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(self, "_clear", []), "_show", []);
return self;},
args: [],
source: unescape('reset%0A%09self%20clear%20show.'),
messageSends: ["show", "clear"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_rightPage'),
smalltalk.method({
selector: unescape('rightPage'),
category: 'accessing',
fn: function (){
var self=this;
return smalltalk.send(self['@book'], "_pageAt_ifAbsent_", [self['@currentPageNo'], (function(){return smalltalk.send((smalltalk.Page || Page), "_new", []);})]);
return self;},
args: [],
source: unescape('rightPage%0A%09%5E%20book%20pageAt%3A%20currentPageNo%20ifAbsent%3A%20%5BPage%20new%5D.'),
messageSends: ["pageAt:ifAbsent:", "new"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_scriptsRoot'),
smalltalk.method({
selector: unescape('scriptsRoot'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@scriptsRoot']) == nil || $receiver == undefined) ? (function(){return self['@scriptsRoot']="";})() : $receiver;
return self;},
args: [],
source: unescape('scriptsRoot%0A%09%5E%20scriptsRoot%20ifNil%3A%20%5BscriptsRoot%20%3A%3D%20%27%27%5D'),
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_scriptsRoot_'),
smalltalk.method({
selector: unescape('scriptsRoot%3A'),
category: 'accessing',
fn: function (anUrl){
var self=this;
self['@scriptsRoot']=anUrl;
return self;},
args: ["anUrl"],
source: unescape('scriptsRoot%3A%20anUrl%0A%09scriptsRoot%20%3A%3D%20anUrl'),
messageSends: [],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_show'),
smalltalk.method({
selector: unescape('show'),
category: 'show',
fn: function (){
var self=this;
smalltalk.send(self, "_appendToJQuery_", [smalltalk.send(unescape(".bib-num-album"), "_asJQuery", [])]);
return self;},
args: [],
source: unescape('show%0A%09self%20appendToJQuery%3A%20%27.bib-num-album%27%20asJQuery'),
messageSends: ["appendToJQuery:", "asJQuery"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_style'),
smalltalk.method({
selector: unescape('style'),
category: 'css',
fn: function (){
var self=this;
return smalltalk.send((smalltalk.String || String), "_streamContents_", [(function(aStream){return (function($rec){smalltalk.send($rec, "_nextPutAll_", [smalltalk.send(self, "_bookStyle", [])]);smalltalk.send($rec, "_nextPutAll_", [smalltalk.send(self, "_zoomControlsStyle", [])]);return smalltalk.send($rec, "_nextPutAll_", [smalltalk.send(self, "_fullScreenStyle", [])]);})(aStream);})]);
return self;},
args: [],
source: unescape('style%0A%09%5E%20String%20streamContents%3A%20%5B%3AaStream%7C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09aStream%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09nextPutAll%3A%20self%20bookStyle%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09nextPutAll%3A%20self%20zoomControlsStyle%3B%0A%09%09%09%09%09%09nextPutAll%3A%20self%20fullScreenStyle%0A%20%20%20%20%20%20%20%20%20%20%5D'),
messageSends: ["streamContents:", "nextPutAll:", "bookStyle", "zoomControlsStyle", "fullScreenStyle"],
referencedClasses: ["String"]
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_toggleFullscreen'),
smalltalk.method({
selector: unescape('toggleFullscreen'),
category: 'callbacks',
fn: function (){
var self=this;
(self['@isFullscreen']=smalltalk.send(self['@isFullscreen'], "_not", []));
smalltalk.send(smalltalk.send(self, "_loader", []), "_abort", []);
smalltalk.send(self, "_reloadWidget", []);
return self;},
args: [],
source: unescape('toggleFullscreen%0A%09isFullscreen%20%3A%3D%20isFullscreen%20not.%0A%09self%20loader%20abort.%0A%09self%20reloadWidget.'),
messageSends: ["not", "abort", "loader", "reloadWidget"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_updateFolioNumbers'),
smalltalk.method({
selector: unescape('updateFolioNumbers'),
category: 'descriptions',
fn: function (){
var self=this;
smalltalk.send(self['@leftFolioBrush'], "_contents_", [smalltalk.send(smalltalk.send(self, "_leftPage", []), "_foliono", [])]);
smalltalk.send(self['@rightFolioBrush'], "_contents_", [smalltalk.send(smalltalk.send(self, "_rightPage", []), "_foliono", [])]);
return self;},
args: [],
source: unescape('updateFolioNumbers%0A%09leftFolioBrush%20contents%3A%20self%20leftPage%20foliono.%0A%09rightFolioBrush%20contents%3A%20self%20rightPage%20foliono.'),
messageSends: ["contents:", "foliono", "leftPage", "rightPage"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_widgetClass'),
smalltalk.method({
selector: unescape('widgetClass'),
category: 'accessing',
fn: function (){
var self=this;
return ((($receiver = self['@isFullscreen']).klass === smalltalk.Boolean) ? ($receiver ? (function(){return unescape("fullscreen%20bk-widget");})() : (function(){return unescape("bk-widget");})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return unescape("fullscreen%20bk-widget");}), (function(){return unescape("bk-widget");})]));
return self;},
args: [],
source: unescape('widgetClass%0A%09%5E%20isFullscreen%20%0A%09%09ifTrue%3A%20%5B%27fullscreen%20bk-widget%27%5D%20%0A%09%09ifFalse%3A%20%5B%27bk-widget%27%5D'),
messageSends: ["ifTrue:ifFalse:"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_width'),
smalltalk.method({
selector: unescape('width'),
category: 'accessing',
fn: function (){
var self=this;
return ((($receiver = ((($receiver = self['@isFullscreen']).klass === smalltalk.Boolean) ? ($receiver ? (function(){return smalltalk.send(((($receiver = smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_width", [])).klass === smalltalk.Number) ? $receiver -(2) * smalltalk.send(self, "_navigatorWidth", []) : smalltalk.send($receiver, "__minus", [(2) * smalltalk.send(self, "_navigatorWidth", [])])), "_min_", [(900)]);})() : (function(){return smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_width", []);})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return smalltalk.send(((($receiver = smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_width", [])).klass === smalltalk.Number) ? $receiver -(2) * smalltalk.send(self, "_navigatorWidth", []) : smalltalk.send($receiver, "__minus", [(2) * smalltalk.send(self, "_navigatorWidth", [])])), "_min_", [(900)]);}), (function(){return smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_width", []);})]))).klass === smalltalk.Number) ? $receiver -(2) * smalltalk.send(self, "_zoomControlWidth", []) : smalltalk.send($receiver, "__minus", [(2) * smalltalk.send(self, "_zoomControlWidth", [])]));
return self;},
args: [],
source: unescape('width%0A%09%5E%20%28isFullscreen%20%0A%09%09%09ifTrue%3A%20%5B%28%27body%27%20asJQuery%20width%20-%20%282%20*%20self%20navigatorWidth%29%29%20min%3A%20900%5D%20%0A%09%09%09ifFalse%3A%20%5BrootBrush%20asJQuery%20width%5D%29%20%20-%20%282%20*%20self%20zoomControlWidth%29'),
messageSends: [unescape("-"), "ifTrue:ifFalse:", "min:", "width", "asJQuery", unescape("*"), "navigatorWidth", "zoomControlWidth"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_zoomControlWidth'),
smalltalk.method({
selector: unescape('zoomControlWidth'),
category: 'accessing',
fn: function (){
var self=this;
return ((($receiver = smalltalk.send(self, "_isContainerSmall", [])).klass === smalltalk.Boolean) ? ($receiver ? (function(){return (30);})() : (function(){return (85);})()) : smalltalk.send($receiver, "_ifTrue_ifFalse_", [(function(){return (30);}), (function(){return (85);})]));
return self;},
args: [],
source: unescape('zoomControlWidth%0A%09%5E%20%20self%20isContainerSmall%20ifTrue%3A%20%5B30%5D%20ifFalse%3A%20%5B85%5D.'),
messageSends: ["ifTrue:ifFalse:", "isContainerSmall"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_zoomControlsStyle'),
smalltalk.method({
selector: unescape('zoomControlsStyle'),
category: 'css',
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(unescape("%0A%09%09%09.b-zoom%20%7B%0A%09%09%09%20%20position%3A%20fixed%3B%0A%09%09%09%20%20top%3A%200px%3B%0A%09%09%09%20%20left%3A%200px%3B%0A%09%09%09%20%20width%3A%20100%25%3B%0A%09%09%09%20%20height%3A%20100%25%3B%0A%09%09%09%20%20display%3A%20none%3B%0A%09%09%09%20%20z-index%3A%20200%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom%20.page-desc%20%7B%0A%09%09%09%20%20margin%3A%200px%205px%3B%0A%09%09%09%20%20width%3A%20auto%3B%0A%09%09%09%20%20color%3A%20white%3B%0A%09%09%09%20%20width%3A%2045%25%3B%0A%09%09%09%20%20padding-right%3A%2020px%3B%0A%09%09%09%20%20height%3A%2095%25%3B%0A%09%09%09%20%20max-width%3Aauto%3B%0A%09%09%09%20%20overflow-y%3A%20auto%3B%0A%09%09%09%20%20display%3A%20block%3B%0A%09%09%09%20%20float%3A%20left%3B%0A%09%09%09%20%20font-size%3A%201.3em%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom-magnify%20%7B%0A%09%09%09%20%20margin%3A%200px%20auto%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom-magnify%20a%2C%0A%09%09%09.b-zoom-fullscreen%20a%20%7B%0A%09%09%09%09display%3A%20block%3B%0A%09%09%09%09width%3A%2048px%3B%0A%09%09%09%09height%3A%2048px%3B%0A%09%09%09%09z-index%3A%2020%3B%0A%09%09%09%09position%3A%20relative%3B%0A%09%09%09%09cursor%3A%20pointer%3B%0A%09%09%09%7D%0A%09%09%09%0A%09%09%09.b-zoom-fullscreen%20%7Bfloat%3A%20right%7D%0A%0A%09%09%09.b-zoom-fullscreen%20a%20%7B%0A%09%09%09%09background%3A%20url%28"), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/expand_black.png%29%20no-repeat%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom-fullscreen%20a%3Ahover%20%7B%0A%09%09%09%09background%3A%20url%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/expand_white.png%29%20no-repeat%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-download-book%20a%20%7B%0A%09%09%09%09float%3A%20right%3B%0A%09%09%09%09display%3A%20block%3B%0A%09%09%09%09width%3A%2073px%3B%0A%09%09%09%09height%3A%2036px%3B%0A%09%09%09%09margin-right%3A%205px%3B%0A%09%09%09%09margin-top%3A%206px%3B%0A%09%09%09%09z-index%3A%2020%3B%0A%09%09%09%09position%3A%20relative%3B%0A%09%09%09%09cursor%3A%20pointer%3B%0A%09%09%09%09background%3A%20url%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/download_black.png%29%20no-repeat%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-download-book%20a%3Ahover%20%7B%0A%09%09%09%09background%3A%20url%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/download_white.png%29%20no-repeat%3B%0A%09%09%09%7D%0A%0A%09%09%09.small%3E.bk-widget%20.b-zoom-magnify%20a%20%7B%0A%09%09%09%09background-image%3A%20none%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom-magnify%20a%20%7B%0A%09%09%09%09background%3A%20url%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/magnify_black.png%29%20no-repeat%3B%0A%09%09%09%7D%0A%09%09%09%0A%09%09%09.b-zoom-magnify%20a%3Ahover%20%7B%0A%09%09%09%09background-image%3A%20url%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/magnify_white.png%29%3B%0A%09%09%09%7D%0A%0A%09%09%09.small%3E.bk-widget%20.b-zoom-magnify%20a%3Ahover%20%7B%0A%09%09%09%09background-image%3A%20none%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom-magnify%20a%20%7B%0A%09%09%09%09float%3A%20left%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom-magnify%20a%20+%20a%20%7B%0A%09%09%09%09float%3A%20right%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom%20%3E%20div%20%7B%0A%09%09%09%20%20position%3A%20relative%3B%0A%09%09%09%20%20z-index%3A%2030%3B%0A%0A%09%09%09%20%20background-color%3A%20rgb%2810%2C10%2C10%29%3B%0A%09%09%09%20%20border%3A%2010px%20solid%20rgb%2850%2C50%2C50%29%3B%0A%0A%09%09%09%20%20background-color%3A%20rgba%2810%2C10%2C10%2C0.8%29%3B%0A%09%09%09%20%20border%3A%2010px%20solid%20rgba%2850%2C50%2C50%2C0.8%29%3B%0A%0A%09%09%09%20%20border-radius%3A%2010px%3B%0A%09%09%09%20%20display%3Anone%3B%0A%09%09%09%20%20padding%3A%201px%3B%0A%09%09%09%20%20height%3A%20100%25%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom%20%3E%20div%20%3E%20div%20%7B%0A%09%09%09%20%20overflow%3A%20scroll%3B%0A%09%09%09%20%20border-radius%3A%2010px%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom%20.iviewer%20%7B%0A%09%09%09%09height%3A%20100%25%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom%20.iviewer_with_text%20%7B%0A%09%09%09%20%20float%3A%20left%3B%0A%09%09%09%20%20width%3A%2050%25%3B%0A%09%09%09%20%20margin-right%3A%205px%3B%0A%09%09%09%7D%0A%0A%09%09%09.iviewer%20%7B%0A%09%09%09%20%20backround-color%3A%20black%3B%0A%09%09%09%7D%0A%0A%09%09%09.iviewer_cursor%20%7B%0A%09%09%09%20%20cursor%3A%20move%3B%0A%09%09%09%7D%0A%0A%09%09%09.controls%20div.iviewer_common%20%7B%0A%09%09%09%20%20position%3A%20static%20%21important%3B%09%09%0A%09%09%09%20%20margin%3A%205px%20auto%3B%0A%09%09%09%20%20background-color%3A%20transparent%3B%0A%09%09%09%7D%0A%0A%09%09%09.controls%20div.iviewer_common%3Ahover%20%7B%0A%09%09%09%09background-color%3A%20white%3B%0A%09%09%09%7D%0A%0A%09%09%09.iviewer_zoom_close%20%7B%0A%09%09%09%20%20background%3A%20url%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/close_black28.png%29%3B%0A%09%09%09%7D%0A")]);
return self;},
args: [],
source: unescape('zoomControlsStyle%0A%09%5E%20%27%0A%09%09%09.b-zoom%20%7B%0A%09%09%09%20%20position%3A%20fixed%3B%0A%09%09%09%20%20top%3A%200px%3B%0A%09%09%09%20%20left%3A%200px%3B%0A%09%09%09%20%20width%3A%20100%25%3B%0A%09%09%09%20%20height%3A%20100%25%3B%0A%09%09%09%20%20display%3A%20none%3B%0A%09%09%09%20%20z-index%3A%20200%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom%20.page-desc%20%7B%0A%09%09%09%20%20margin%3A%200px%205px%3B%0A%09%09%09%20%20width%3A%20auto%3B%0A%09%09%09%20%20color%3A%20white%3B%0A%09%09%09%20%20width%3A%2045%25%3B%0A%09%09%09%20%20padding-right%3A%2020px%3B%0A%09%09%09%20%20height%3A%2095%25%3B%0A%09%09%09%20%20max-width%3Aauto%3B%0A%09%09%09%20%20overflow-y%3A%20auto%3B%0A%09%09%09%20%20display%3A%20block%3B%0A%09%09%09%20%20float%3A%20left%3B%0A%09%09%09%20%20font-size%3A%201.3em%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom-magnify%20%7B%0A%09%09%09%20%20margin%3A%200px%20auto%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom-magnify%20a%2C%0A%09%09%09.b-zoom-fullscreen%20a%20%7B%0A%09%09%09%09display%3A%20block%3B%0A%09%09%09%09width%3A%2048px%3B%0A%09%09%09%09height%3A%2048px%3B%0A%09%09%09%09z-index%3A%2020%3B%0A%09%09%09%09position%3A%20relative%3B%0A%09%09%09%09cursor%3A%20pointer%3B%0A%09%09%09%7D%0A%09%09%09%0A%09%09%09.b-zoom-fullscreen%20%7Bfloat%3A%20right%7D%0A%0A%09%09%09.b-zoom-fullscreen%20a%20%7B%0A%09%09%09%09background%3A%20url%28%27%2C%20self%20scriptsRoot%2C%20%27images/expand_black.png%29%20no-repeat%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom-fullscreen%20a%3Ahover%20%7B%0A%09%09%09%09background%3A%20url%28%27%2C%20self%20scriptsRoot%2C%20%27images/expand_white.png%29%20no-repeat%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-download-book%20a%20%7B%0A%09%09%09%09float%3A%20right%3B%0A%09%09%09%09display%3A%20block%3B%0A%09%09%09%09width%3A%2073px%3B%0A%09%09%09%09height%3A%2036px%3B%0A%09%09%09%09margin-right%3A%205px%3B%0A%09%09%09%09margin-top%3A%206px%3B%0A%09%09%09%09z-index%3A%2020%3B%0A%09%09%09%09position%3A%20relative%3B%0A%09%09%09%09cursor%3A%20pointer%3B%0A%09%09%09%09background%3A%20url%28%27%2C%20self%20scriptsRoot%2C%20%27images/download_black.png%29%20no-repeat%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-download-book%20a%3Ahover%20%7B%0A%09%09%09%09background%3A%20url%28%27%2C%20self%20scriptsRoot%2C%20%27images/download_white.png%29%20no-repeat%3B%0A%09%09%09%7D%0A%0A%09%09%09.small%3E.bk-widget%20.b-zoom-magnify%20a%20%7B%0A%09%09%09%09background-image%3A%20none%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom-magnify%20a%20%7B%0A%09%09%09%09background%3A%20url%28%27%2C%20self%20scriptsRoot%2C%20%27images/magnify_black.png%29%20no-repeat%3B%0A%09%09%09%7D%0A%09%09%09%0A%09%09%09.b-zoom-magnify%20a%3Ahover%20%7B%0A%09%09%09%09background-image%3A%20url%28%27%2C%20self%20scriptsRoot%2C%20%27images/magnify_white.png%29%3B%0A%09%09%09%7D%0A%0A%09%09%09.small%3E.bk-widget%20.b-zoom-magnify%20a%3Ahover%20%7B%0A%09%09%09%09background-image%3A%20none%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom-magnify%20a%20%7B%0A%09%09%09%09float%3A%20left%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom-magnify%20a%20+%20a%20%7B%0A%09%09%09%09float%3A%20right%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom%20%3E%20div%20%7B%0A%09%09%09%20%20position%3A%20relative%3B%0A%09%09%09%20%20z-index%3A%2030%3B%0A%0A%09%09%09%20%20background-color%3A%20rgb%2810%2C10%2C10%29%3B%0A%09%09%09%20%20border%3A%2010px%20solid%20rgb%2850%2C50%2C50%29%3B%0A%0A%09%09%09%20%20background-color%3A%20rgba%2810%2C10%2C10%2C0.8%29%3B%0A%09%09%09%20%20border%3A%2010px%20solid%20rgba%2850%2C50%2C50%2C0.8%29%3B%0A%0A%09%09%09%20%20border-radius%3A%2010px%3B%0A%09%09%09%20%20display%3Anone%3B%0A%09%09%09%20%20padding%3A%201px%3B%0A%09%09%09%20%20height%3A%20100%25%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom%20%3E%20div%20%3E%20div%20%7B%0A%09%09%09%20%20overflow%3A%20scroll%3B%0A%09%09%09%20%20border-radius%3A%2010px%3B%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom%20.iviewer%20%7B%0A%09%09%09%09height%3A%20100%25%0A%09%09%09%7D%0A%0A%09%09%09.b-zoom%20.iviewer_with_text%20%7B%0A%09%09%09%20%20float%3A%20left%3B%0A%09%09%09%20%20width%3A%2050%25%3B%0A%09%09%09%20%20margin-right%3A%205px%3B%0A%09%09%09%7D%0A%0A%09%09%09.iviewer%20%7B%0A%09%09%09%20%20backround-color%3A%20black%3B%0A%09%09%09%7D%0A%0A%09%09%09.iviewer_cursor%20%7B%0A%09%09%09%20%20cursor%3A%20move%3B%0A%09%09%09%7D%0A%0A%09%09%09.controls%20div.iviewer_common%20%7B%0A%09%09%09%20%20position%3A%20static%20%21important%3B%09%09%0A%09%09%09%20%20margin%3A%205px%20auto%3B%0A%09%09%09%20%20background-color%3A%20transparent%3B%0A%09%09%09%7D%0A%0A%09%09%09.controls%20div.iviewer_common%3Ahover%20%7B%0A%09%09%09%09background-color%3A%20white%3B%0A%09%09%09%7D%0A%0A%09%09%09.iviewer_zoom_close%20%7B%0A%09%09%09%20%20background%3A%20url%28%27%2C%20self%20scriptsRoot%2C%20%27images/close_black28.png%29%3B%0A%09%09%09%7D%0A%27'),
messageSends: [unescape("%2C"), "scriptsRoot"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_zoomLeftPage'),
smalltalk.method({
selector: unescape('zoomLeftPage'),
category: 'zoom',
fn: function (){
var self=this;
smalltalk.send(self, "_closeZoomOr_", [(function(){smalltalk.send(self, "_zoomPageNo_withClass_", [((($receiver = self['@currentPageNo']).klass === smalltalk.Number) ? $receiver -(1) : smalltalk.send($receiver, "__minus", [(1)])), unescape("b-left")]);return smalltalk.send(self['@zoomLeftPageAnchor'], "_addClass_", ["active"]);})]);
return self;},
args: [],
source: unescape('zoomLeftPage%0A%09self%20closeZoomOr%3A%20%5B%0A%20%20%20%20%20%20%20%20%20%20%09self%20zoomPageNo%3A%20currentPageNo%20-%201%20withClass%3A%20%27b-left%27.%0A%20%20%20%20%20%20%20%20%20%20%09zoomLeftPageAnchor%20addClass%3A%20%27active%27.%0A%20%20%20%20%20%20%20%20%5D.'),
messageSends: ["closeZoomOr:", "zoomPageNo:withClass:", unescape("-"), "addClass:"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_zoomPageNo_withClass_'),
smalltalk.method({
selector: unescape('zoomPageNo%3AwithClass%3A'),
category: 'zoom',
fn: function (anInteger, aCssClass){
var self=this;
smalltalk.send(self, "_closeDescriptions", []);
smalltalk.send(self['@zoomLeftPageAnchor'], "_hide", []);
smalltalk.send(self['@zoomRightPageAnchor'], "_hide", []);
smalltalk.send(smalltalk.send(unescape(".b-arrow"), "_asJQuery", []), "_hide", []);
smalltalk.send(self['@book'], "_pageAt_do_", [anInteger, (function(aPage){smalltalk.send(self['@pageZoomBrush'], "_contents_", [(function(html){return smalltalk.send(self, "_renderPage_class_on_", [aPage, aCssClass, html]);})]);return smalltalk.send(smalltalk.send(self['@pageZoomBrush'], "_asJQuery", []), "_show", []);})]);
return self;},
args: ["anInteger", "aCssClass"],
source: unescape('zoomPageNo%3A%20anInteger%20withClass%3A%20aCssClass%0A%09self%20closeDescriptions.%0A%0A%09zoomLeftPageAnchor%20hide.%0A%20%20%20%20%20%20%20%20zoomRightPageAnchor%20hide.%0A%09%27.b-arrow%27%20asJQuery%20hide.%0A%0A%09book%20pageAt%3A%20anInteger%20do%3A%20%5B%3AaPage%7C%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09pageZoomBrush%20contents%3A%20%5B%3Ahtml%7C%20%20self%20renderPage%3A%20aPage%20class%3A%20aCssClass%20on%3A%20html%20%5D.%0A%09%09%09%09%09%09%09%09pageZoomBrush%20asJQuery%20show.%09%09%09%09%09%09%09%09%09%09%09%09%20%09%5D.'),
messageSends: ["closeDescriptions", "hide", "asJQuery", "pageAt:do:", "contents:", "renderPage:class:on:", "show"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
unescape('_zoomRightPage'),
smalltalk.method({
selector: unescape('zoomRightPage'),
category: 'zoom',
fn: function (){
var self=this;
smalltalk.send(self, "_closeZoomOr_", [(function(){smalltalk.send(self, "_zoomPageNo_withClass_", [self['@currentPageNo'], unescape("b-right")]);return smalltalk.send(self['@zoomRightPageAnchor'], "_addClass_", ["active"]);})]);
return self;},
args: [],
source: unescape('zoomRightPage%0A%09self%20closeZoomOr%3A%20%5B%0A%20%20%20%20%20%20%20%20%20%20%09self%20zoomPageNo%3A%20currentPageNo%20withClass%3A%20%27b-right%27.%0A%20%20%20%20%20%20%20%20%20%20%09zoomRightPageAnchor%20addClass%3A%20%27active%27.%0A%20%20%20%20%20%20%20%20%5D.'),
messageSends: ["closeZoomOr:", "zoomPageNo:withClass:", "addClass:"],
referencedClasses: []
}),
smalltalk.BookWidget);


smalltalk.addMethod(
unescape('_open'),
smalltalk.method({
selector: unescape('open'),
category: 'initialize release',
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(self, "_new", []), "_show", []);
return self;},
args: [],
source: unescape('open%0A%09%5E%20self%20new%20show.'),
messageSends: ["show", "new"],
referencedClasses: []
}),
smalltalk.BookWidget.klass);

smalltalk.addMethod(
unescape('_reset'),
smalltalk.method({
selector: unescape('reset'),
category: 'initialize release',
fn: function (){
var self=this;
return smalltalk.send(smalltalk.send(self, "_new", []), "_reset", []);
return self;},
args: [],
source: unescape('reset%0A%09%5E%20self%20new%20reset.'),
messageSends: ["reset", "new"],
referencedClasses: []
}),
smalltalk.BookWidget.klass);


smalltalk.addClass('Cycle', smalltalk.Object, ['elements', 'counter'], 'AFI');
smalltalk.addMethod(
unescape('_elements_'),
smalltalk.method({
selector: unescape('elements%3A'),
category: 'accessing',
fn: function (anArray){
var self=this;
(self['@elements']=anArray);
return self;},
args: ["anArray"],
source: unescape('elements%3A%20anArray%0A%09elements%20%3A%3D%20anArray'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Cycle);

smalltalk.addMethod(
unescape('_initialize'),
smalltalk.method({
selector: unescape('initialize'),
category: 'initialize',
fn: function (){
var self=this;
(self['@counter']=(-1));
return self;},
args: [],
source: unescape('initialize%0A%09counter%20%3A%3D%20-1'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Cycle);

smalltalk.addMethod(
unescape('_next'),
smalltalk.method({
selector: unescape('next'),
category: 'accessing',
fn: function (){
var self=this;
(self['@counter']=((($receiver = self['@counter']).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)])));
return smalltalk.send(self['@elements'], "_at_", [((($receiver = smalltalk.send(self['@counter'], "_\\\\", [smalltalk.send(self['@elements'], "_size", [])])).klass === smalltalk.Number) ? $receiver +(1) : smalltalk.send($receiver, "__plus", [(1)]))]);
return self;},
args: [],
source: unescape('next%0A%09counter%20%3A%3D%20counter%20+%201.%0A%09%5Eelements%20at%3A%20%28counter%20%5C%5C%20elements%20size%29%20+%201.'),
messageSends: [unescape("+"), "at:", unescape("%5C%5C%5C%5C"), "size"],
referencedClasses: []
}),
smalltalk.Cycle);


smalltalk.addMethod(
unescape('_with_'),
smalltalk.method({
selector: unescape('with%3A'),
category: 'instance creation',
fn: function (anArray){
var self=this;
return smalltalk.send(smalltalk.send(self, "_new", []), "_elements_", [anArray]);
return self;},
args: ["anArray"],
source: unescape('with%3A%20anArray%0A%09%5E%20self%20new%20elements%3A%20anArray'),
messageSends: ["elements:", "new"],
referencedClasses: []
}),
smalltalk.Cycle.klass);


smalltalk.addClass('ListFilter', smalltalk.Object, ['book', 'announcer', 'jqueryInput', 'jqueryList'], 'AFI');
smalltalk.addMethod(
unescape('_filter_withInput_'),
smalltalk.method({
selector: unescape('filter%3AwithInput%3A'),
category: 'initialization',
fn: function (aJQueryList, aJQueryInput){
var self=this;
(self['@jqueryList']=aJQueryList);
(self['@jqueryInput']=aJQueryInput);
smalltalk.send(self['@jqueryInput'], "_keyup_", [(function(){return smalltalk.send(self, "_filterListWithInputString", []);})]);
return self;},
args: ["aJQueryList", "aJQueryInput"],
source: unescape('filter%3A%20aJQueryList%20withInput%3A%20aJQueryInput%0A%09jqueryList%20%3A%3D%20aJQueryList.%0A%09jqueryInput%20%3A%3D%20aJQueryInput.%0A%09jqueryInput%20keyup%3A%20%5Bself%20filterListWithInputString%5D.'),
messageSends: ["keyup:", "filterListWithInputString"],
referencedClasses: []
}),
smalltalk.ListFilter);

smalltalk.addMethod(
unescape('_filterListWithInputString'),
smalltalk.method({
selector: unescape('filterListWithInputString'),
category: 'callback',
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
return self;},
args: [],
source: unescape('filterListWithInputString%0A%09%7CsearchString%20regExp%20matches%20items%7C%0A%09searchString%20%3A%3D%20jqueryInput%20val.%0A%09regExp%20%3A%3D%20%3Cnew%20RegExp%28searchString%2C%20%27i%27%29%3E.%0A%09items%20%3A%3D%20jqueryList%20find%3A%20%27li%27.%0A%09matches%20%3A%3D%20items%20filter%3A%20%5B%3AanInteger%7C%20%3CregExp.test%28%24%28this%29.text%28%29%29%3E%5D.%0A%09items%20hide.%0A%09matches%20show.%0A%0A%09searchString%20isEmpty%20ifTrue%3A%20%5BjqueryList%20removeClass%3A%20%27filtered%27%5D%20ifFalse%3A%20%5BjqueryList%20addClass%3A%20%27filtered%27%5D.'),
messageSends: ["val", "find:", "filter:", "hide", "show", "ifTrue:ifFalse:", "isEmpty", "removeClass:", "addClass:"],
referencedClasses: []
}),
smalltalk.ListFilter);


smalltalk.addMethod(
unescape('_filter_withInput_'),
smalltalk.method({
selector: unescape('filter%3AwithInput%3A'),
category: 'instance creation',
fn: function (aJQueryList, aJQueryInput){
var self=this;
return smalltalk.send(smalltalk.send(self, "_new", []), "_filter_withInput_", [aJQueryList, aJQueryInput]);
return self;},
args: ["aJQueryList", "aJQueryInput"],
source: unescape('filter%3A%20aJQueryList%20withInput%3A%20aJQueryInput%0A%09%5E%20self%20new%20filter%3A%20aJQueryList%20withInput%3A%20aJQueryInput'),
messageSends: ["filter:withInput:", "new"],
referencedClasses: []
}),
smalltalk.ListFilter.klass);


smalltalk.addClass('Page', smalltalk.Object, ['brush', 'fullImageURL', 'thumbnailURL', 'description', 'title', 'rendered', 'foliono', 'navigatorThumbnailURL', 'book'], 'AFI');
smalltalk.addMethod(
unescape('_book'),
smalltalk.method({
selector: unescape('book'),
category: 'accessing',
fn: function (){
var self=this;
return self['@book'];
return self;},
args: [],
source: unescape('book%0A%09%5E%20book'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_book_'),
smalltalk.method({
selector: unescape('book%3A'),
category: 'accessing',
fn: function (aBook){
var self=this;
(self['@book']=aBook);
return self;},
args: ["aBook"],
source: unescape('book%3A%20aBook%0A%09book%20%3A%3D%20aBook'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_brush'),
smalltalk.method({
selector: unescape('brush'),
category: 'accessing',
fn: function (){
var self=this;
return self['@brush'];
return self;},
args: [],
source: unescape('brush%0A%09%5E%20brush'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_brush_'),
smalltalk.method({
selector: unescape('brush%3A'),
category: 'accessing',
fn: function (aBrush){
var self=this;
self['@brush']=aBrush;
return self;},
args: ["aBrush"],
source: unescape('brush%3A%20aBrush%0A%09brush%20%3A%3D%20aBrush'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_description'),
smalltalk.method({
selector: unescape('description'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@description']) == nil || $receiver == undefined) ? (function(){return self['@description']="";})() : $receiver;
return self;},
args: [],
source: unescape('description%0A%09%5E%20description%20ifNil%3A%20%5Bdescription%20%3A%3D%20%27%27%5D'),
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_description_'),
smalltalk.method({
selector: unescape('description%3A'),
category: 'accessing',
fn: function (aString){
var self=this;
self['@description']=aString;
return self;},
args: ["aString"],
source: unescape('description%3A%20aString%0A%09description%20%3A%3D%20aString'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_foliono'),
smalltalk.method({
selector: unescape('foliono'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@foliono']) == nil || $receiver == undefined) ? (function(){return (self['@foliono']="");})() : $receiver;
return self;},
args: [],
source: unescape('foliono%0A%09%5E%20foliono%20ifNil%3A%20%5Bfoliono%20%3A%3D%20%27%27%5D'),
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_foliono_'),
smalltalk.method({
selector: unescape('foliono%3A'),
category: 'accessing',
fn: function (aString){
var self=this;
(self['@foliono']=aString);
return self;},
args: ["aString"],
source: unescape('foliono%3A%20aString%0A%09foliono%20%3A%3D%20aString'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_fullImageURL'),
smalltalk.method({
selector: unescape('fullImageURL'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@fullImageURL']) == nil || $receiver == undefined) ? (function(){return self['@fullImageURL']="";})() : $receiver;
return self;},
args: [],
source: unescape('fullImageURL%0A%09%5E%20fullImageURL%20ifNil%3A%20%5BfullImageURL%20%3A%3D%20%27%27%5D'),
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_fullImageURL_'),
smalltalk.method({
selector: unescape('fullImageURL%3A'),
category: 'accessing',
fn: function (aString){
var self=this;
self['@fullImageURL']=aString;
return self;},
args: ["aString"],
source: unescape('fullImageURL%3A%20aString%0A%09fullImageURL%20%3A%3D%20aString'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_initMetadata_'),
smalltalk.method({
selector: unescape('initMetadata%3A'),
category: 'accessing',
fn: function (aJSObject){
var self=this;
self['@description']=aJSObject.description;
self['@title']=aJSObject.book;
return self;},
args: ["aJSObject"],
source: unescape('initMetadata%3A%20aJSObject%0A%09description%20%3A%3D%20%3CaJSObject.description%3E.%0A%09title%20%3A%3D%20%3CaJSObject.book%3E.'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_isRendered'),
smalltalk.method({
selector: unescape('isRendered'),
category: 'testing',
fn: function (){
var self=this;
return (($receiver = self['@rendered']) == nil || $receiver == undefined) ? (function(){return self['@rendered']=false;})() : $receiver;
return self;},
args: [],
source: unescape('isRendered%0A%09%5E%20rendered%20ifNil%3A%20%5Brendered%20%3A%3D%20false%5D'),
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_navigatorThumbnailURL'),
smalltalk.method({
selector: unescape('navigatorThumbnailURL'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@navigatorThumbnailURL']) == nil || $receiver == undefined) ? (function(){return (self['@navigatorThumbnailURL']="");})() : $receiver;
return self;},
args: [],
source: unescape('navigatorThumbnailURL%0A%09%5E%20navigatorThumbnailURL%20ifNil%3A%20%5BnavigatorThumbnailURL%20%3A%3D%20%27%27%5D.'),
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_navigatorThumbnailURL_'),
smalltalk.method({
selector: unescape('navigatorThumbnailURL%3A'),
category: 'accessing',
fn: function (aString){
var self=this;
(self['@navigatorThumbnailURL']=aString);
return self;},
args: ["aString"],
source: unescape('navigatorThumbnailURL%3A%20aString%0A%09navigatorThumbnailURL%20%3A%3D%20aString'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_pageNo'),
smalltalk.method({
selector: unescape('pageNo'),
category: 'accessing',
fn: function (){
var self=this;
return smalltalk.send(self['@book'], "_pageNo_", [self]);
return self;},
args: [],
source: unescape('pageNo%0A%09%5E%20book%20pageNo%3A%20self'),
messageSends: ["pageNo:"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_printString'),
smalltalk.method({
selector: unescape('printString'),
category: 'printing',
fn: function (){
var self=this;
return smalltalk.send((smalltalk.String || String), "_streamContents_", [(function(aStream){return (function($rec){smalltalk.send($rec, "_nextPutAll_", [smalltalk.send(self, "_printString", [], smalltalk.Object)]);smalltalk.send($rec, "_nextPutAll_", [unescape("%28")]);smalltalk.send($rec, "_nextPutAll_", [smalltalk.send(self, "_title", [])]);return smalltalk.send($rec, "_nextPutAll_", [unescape("%29")]);})(aStream);})]);
return self;},
args: [],
source: unescape('printString%0A%09%5E%20String%20streamContents%3A%20%5B%3AaStream%7C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20aStream%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09nextPutAll%3A%20super%20printString%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09nextPutAll%3A%20%27%28%27%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09nextPutAll%3A%20self%20title%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09nextPutAll%3A%27%29%27.%0A%20%20%20%20%20%20%20%20%5D.'),
messageSends: ["streamContents:", "nextPutAll:", "printString", "title"],
referencedClasses: ["BlockClosure"]
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_render'),
smalltalk.method({
selector: unescape('render'),
category: 'rendering',
fn: function (){
var self=this;
smalltalk.send(self, "_renderWidth_height_", [smalltalk.send(self, "_width", []), smalltalk.send(self, "_height", [])]);
return self;},
args: [],
source: unescape('render%0A%09self%20renderWidth%3A%20self%20width%20height%3A%20self%20height.'),
messageSends: ["renderWidth:height:", "width", "height"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_renderWidth_height_'),
smalltalk.method({
selector: unescape('renderWidth%3Aheight%3A'),
category: 'rendering',
fn: function (width, height){
var self=this;
((($receiver = smalltalk.send(self, "_isRendered", [])).klass === smalltalk.Boolean) ? (! $receiver ? (function(){(self['@rendered']=true);return smalltalk.send(self['@brush'], "_contents_", [(function(html){return (function($rec){smalltalk.send($rec, "_width_", [width]);smalltalk.send($rec, "_height_", [height]);return smalltalk.send($rec, "_src_", [self['@thumbnailURL']]);})(smalltalk.send(html, "_img", []));})]);})() : nil) : smalltalk.send($receiver, "_ifFalse_", [(function(){(self['@rendered']=true);return smalltalk.send(self['@brush'], "_contents_", [(function(html){return (function($rec){smalltalk.send($rec, "_width_", [width]);smalltalk.send($rec, "_height_", [height]);return smalltalk.send($rec, "_src_", [self['@thumbnailURL']]);})(smalltalk.send(html, "_img", []));})]);})]));
return self;},
args: ["width", "height"],
source: unescape('renderWidth%3A%20width%20height%3A%20height%0A%09self%20isRendered%20ifFalse%3A%20%5B%0A%20%20%20%20%20%20%20%20%20%20%09rendered%20%3A%3D%20true.%0A%09%09brush%20contents%3A%20%5B%3Ahtml%7C%20html%20img%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09width%3A%20%20width%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09height%3A%20%20height%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09src%3A%20thumbnailURL%5D.%0A%20%20%20%20%20%20%20%20%20%20%5D.'),
messageSends: ["ifFalse:", "isRendered", "contents:", "width:", "height:", "src:", "img"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_reset'),
smalltalk.method({
selector: unescape('reset'),
category: 'reset',
fn: function (){
var self=this;
return (self['@rendered']=false);
return self;},
args: [],
source: unescape('reset%0A%09%5E%20rendered%20%3A%3D%20false'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_thumbnailURL'),
smalltalk.method({
selector: unescape('thumbnailURL'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@thumbnailURL']) == nil || $receiver == undefined) ? (function(){return self['@thumbnailURL']="";})() : $receiver;
return self;},
args: [],
source: unescape('thumbnailURL%0A%09%5E%20thumbnailURL%20ifNil%3A%20%5BthumbnailURL%20%3A%3D%20%27%27%5D.'),
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_thumbnailURL_'),
smalltalk.method({
selector: unescape('thumbnailURL%3A'),
category: 'accessing',
fn: function (aString){
var self=this;
self['@thumbnailURL']=aString;
return self;},
args: ["aString"],
source: unescape('thumbnailURL%3A%20aString%0A%09thumbnailURL%20%3A%3D%20aString'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_title'),
smalltalk.method({
selector: unescape('title'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@title']) == nil || $receiver == undefined) ? (function(){return self['@title']="";})() : $receiver;
return self;},
args: [],
source: unescape('title%0A%09%5E%20title%20ifNil%3A%20%5Btitle%20%3A%3D%20%27%27%5D'),
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
unescape('_title_'),
smalltalk.method({
selector: unescape('title%3A'),
category: 'accessing',
fn: function (aString){
var self=this;
self['@title']=aString;
return self;},
args: ["aString"],
source: unescape('title%3A%20aString%0A%09title%20%3A%3D%20aString'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);



smalltalk.addClass('SouvignyPage', smalltalk.Page, ['book', 'icon', 'letter', 'subject'], 'AFI');
smalltalk.addMethod(
unescape('_initMetadata_'),
smalltalk.method({
selector: unescape('initMetadata%3A'),
category: 'accessing',
fn: function (aJSObject){
var self=this;
self['@book']=aJSObject.book;
self['@icon']=aJSObject.icon;
self['@letter']=aJSObject.letter;
self['@subject']=aJSObject.subject;
self['@description']=aJSObject.description;
self['@title']=smalltalk.send((smalltalk.String || String), "_streamContents_", [(function(aStream){smalltalk.send(aStream, "_nextPutAll_", [self['@book']]);smalltalk.send(self['@icon'], "_ifNotEmpty_", [(function(){return smalltalk.send(aStream, "_nextPutAll_", [smalltalk.send(unescape("%20-%20"), "__comma", [self['@icon']])]);})]);return smalltalk.send(self['@subject'], "_ifNotEmpty_", [(function(){return smalltalk.send(aStream, "_nextPutAll_", [smalltalk.send(unescape("%20-%20"), "__comma", [self['@subject']])]);})]);})]);
return self;},
args: ["aJSObject"],
source: unescape('initMetadata%3A%20aJSObject%0A%09book%20%3A%3D%20%3CaJSObject.book%3E.%0A%09icon%20%3A%3D%20%3CaJSObject.icon%3E.%0A%09letter%20%3A%3D%20%3CaJSObject.letter%3E.%0A%09subject%20%3A%3D%20%3CaJSObject.subject%3E.%0A%09description%20%3A%3D%20%3CaJSObject.description%3E.%0A%20%09%0A%09title%20%3A%3D%20String%20streamContents%3A%20%5B%3AaStream%7C%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20aStream%20nextPutAll%3A%20book.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%20%20icon%20ifNotEmpty%3A%20%5BaStream%20nextPutAll%3A%20%27%20-%20%27%2C%20icon%5D.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%20%20subject%20ifNotEmpty%3A%20%5BaStream%20nextPutAll%3A%20%27%20-%20%27%2C%20subject%5D.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%5D'),
messageSends: ["streamContents:", "nextPutAll:", "ifNotEmpty:", unescape("%2C")],
referencedClasses: ["BlockClosure"]
}),
smalltalk.SouvignyPage);



smalltalk.addClass('PageChangeAnnouncement', smalltalk.Object, ['page'], 'AFI');
smalltalk.addMethod(
unescape('_page'),
smalltalk.method({
selector: unescape('page'),
category: 'accessing',
fn: function (){
var self=this;
return self['@page'];
return self;},
args: [],
source: unescape('page%0A%09%5E%20page'),
messageSends: [],
referencedClasses: []
}),
smalltalk.PageChangeAnnouncement);

smalltalk.addMethod(
unescape('_page_'),
smalltalk.method({
selector: unescape('page%3A'),
category: 'accessing',
fn: function (aPage){
var self=this;
(self['@page']=aPage);
return self;},
args: ["aPage"],
source: unescape('page%3A%20aPage%0A%09page%20%3A%3D%20aPage'),
messageSends: [],
referencedClasses: []
}),
smalltalk.PageChangeAnnouncement);


smalltalk.addMethod(
unescape('_page_'),
smalltalk.method({
selector: unescape('page%3A'),
category: 'instance creation',
fn: function (aPage){
var self=this;
return smalltalk.send(smalltalk.send(self, "_new", []), "_page_", [aPage]);
return self;},
args: ["aPage"],
source: unescape('page%3A%20aPage%0A%09%5E%20self%20new%20page%3A%20aPage'),
messageSends: ["page:", "new"],
referencedClasses: []
}),
smalltalk.PageChangeAnnouncement.klass);


smalltalk.addClass('PageWidget', smalltalk.Widget, ['page', 'inControl', 'outControl', 'fitControl', 'statusControl', 'zeroControl', 'closeControl', 'closeBlock', 'rotateRightControl', 'rotation'], 'AFI');
smalltalk.PageWidget.comment=unescape('I%20display%20a%20full%20page%20with%20zoom%20controller%20and%20description')
smalltalk.addMethod(
unescape('_close'),
smalltalk.method({
selector: unescape('close'),
category: 'callback',
fn: function (){
var self=this;
smalltalk.send(self['@closeBlock'], "_value", []);
return self;},
args: [],
source: unescape('close%0A%09closeBlock%20value.'),
messageSends: ["value"],
referencedClasses: []
}),
smalltalk.PageWidget);

smalltalk.addMethod(
unescape('_initIViewer_'),
smalltalk.method({
selector: unescape('initIViewer%3A'),
category: 'callback',
fn: function (aViewer){
var self=this;
smalltalk.send(self['@inControl'], "_onClick_", [(function(){return aViewer.zoom_by(1);})]);
smalltalk.send(self['@outControl'], "_onClick_", [(function(){return aViewer.zoom_by(-1);})]);
smalltalk.send(self['@fitControl'], "_onClick_", [(function(){return smalltalk.send(aViewer, "_fit", []);})]);
smalltalk.send(self['@zeroControl'], "_onClick_", [(function(){return aViewer.set_zoom(100);})]);
smalltalk.send(self['@rotateRightControl'], "_onClick_", [(function(){return smalltalk.send(self, "_rotateRight", []);})]);
return self;},
args: ["aViewer"],
source: unescape('initIViewer%3A%20aViewer%0A%09inControl%20onClick%3A%20%5B%3CaViewer.zoom_by%281%29%3E%5D.%0A%09outControl%20onClick%3A%20%5B%3CaViewer.zoom_by%28-1%29%3E%5D.%0A%09fitControl%20onClick%3A%20%5BaViewer%20fit%5D.%0A%09zeroControl%20onClick%3A%20%5B%3CaViewer.set_zoom%28100%29%3E%5D.%0A%09rotateRightControl%20onClick%3A%20%5Bself%20rotateRight%5D.'),
messageSends: ["onClick:", "fit", "rotateRight"],
referencedClasses: []
}),
smalltalk.PageWidget);

smalltalk.addMethod(
unescape('_onCloseDo_'),
smalltalk.method({
selector: unescape('onCloseDo%3A'),
category: 'accessing',
fn: function (aBlock){
var self=this;
self['@closeBlock']=aBlock;
return self;},
args: ["aBlock"],
source: unescape('onCloseDo%3A%20aBlock%0A%09closeBlock%20%3A%3D%20aBlock'),
messageSends: [],
referencedClasses: []
}),
smalltalk.PageWidget);

smalltalk.addMethod(
unescape('_page_'),
smalltalk.method({
selector: unescape('page%3A'),
category: 'accessing',
fn: function (aPage){
var self=this;
self['@page']=aPage;
return self;},
args: ["aPage"],
source: unescape('page%3A%20aPage%0A%09page%20%3A%3D%20aPage.'),
messageSends: [],
referencedClasses: []
}),
smalltalk.PageWidget);

smalltalk.addMethod(
unescape('_renderControlsOn_'),
smalltalk.method({
selector: unescape('renderControlsOn%3A'),
category: 'rendering',
fn: function (html){
var self=this;
var addControl=nil;
(function($rec){smalltalk.send($rec, "_class_", ["controls"]);return smalltalk.send($rec, "_with_", [(function(){(addControl=(function(name){return (function($rec){smalltalk.send($rec, "_class_", [smalltalk.send(smalltalk.send("iviewer_zoom_", "__comma", [name]), "__comma", [" iviewer_common iviewer_button"])]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send(html, "_div", []));}));(self['@closeControl']=smalltalk.send(addControl, "_value_", ["close"]));smalltalk.send(self['@closeControl'], "_onClick_", [(function(){return smalltalk.send(self, "_close", []);})]);(self['@inControl']=smalltalk.send(addControl, "_value_", ["in"]));(self['@outControl']=smalltalk.send(addControl, "_value_", ["out"]));(self['@zeroControl']=smalltalk.send(addControl, "_value_", ["zero"]));(self['@fitControl']=smalltalk.send(addControl, "_value_", ["fit"]));(self['@statusControl']=smalltalk.send(addControl, "_value_", ["status"]));return (self['@rotateRightControl']=smalltalk.send(addControl, "_value_", ["rotate_right"]));})]);})(smalltalk.send(html, "_div", []));
return self;},
args: ["html"],
source: unescape('renderControlsOn%3A%20html%0A%09%7CaddControl%7C%0A%09html%20div%20%0A%09%09class%3A%20%27controls%27%3B%0A%09%09with%3A%20%5B%0A%20%20%20%20%20%20%20%20%20%20%09%09addControl%20%3A%3D%20%20%5B%3Aname%7C%20html%20div%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09class%3A%20%27iviewer_zoom_%27%2C%20name%2C%20%27%20iviewer_common%20iviewer_button%27%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09yourself%5D.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09closeControl%20%3A%3D%20addControl%20value%3A%20%27close%27.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09closeControl%20onClick%3A%20%5Bself%20close%5D.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09inControl%20%3A%3D%20addControl%20value%3A%20%27in%27.%0A%20%20%20%20%20%20%20%20%20%20%09%09outControl%20%3A%3D%20addControl%20value%3A%20%27out%27.%0A%20%20%20%20%20%20%20%20%20%20%09%09zeroControl%20%3A%3D%20addControl%20value%3A%20%27zero%27.%0A%20%20%20%20%20%20%20%20%20%20%09%09fitControl%20%3A%3D%20addControl%20value%3A%20%27fit%27.%0A%20%20%20%20%20%20%20%20%20%20%09%09statusControl%20%3A%3D%20addControl%20value%3A%20%27status%27.%0A%20%20%20%20%20%20%20%20%20%20%09%09rotateRightControl%20%3A%3D%20addControl%20value%3A%20%27rotate_right%27.%0A%20%20%20%20%20%20%20%20%5D.'),
messageSends: ["class:", "with:", unescape("%2C"), "yourself", "div", "value:", "onClick:", "close"],
referencedClasses: []
}),
smalltalk.PageWidget);

smalltalk.addMethod(
unescape('_renderOn_'),
smalltalk.method({
selector: unescape('renderOn%3A'),
category: 'rendering',
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
return self;},
args: ["html"],
source: unescape('renderOn%3A%20html%0A%09%7CiViewer%7C%0A%09html%20style%3A%20self%20style.%0A%09self%20renderControlsOn%3A%20html.%0A%09iViewer%20%3A%3D%20html%20div%20%0A%09%09class%3A%20%27iviewer%27%3B%0A%09%09asJQuery.%0A%09%0A%09page%20description%20ifNotEmpty%3A%20%5BiViewer%20addClass%3A%20%27iviewer_with_text%27%5D.%0A%0A%09iViewer%20iviewer%3A%20%28HashedCollection%20new%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09at%3A%20%27src%27%20put%3A%20page%20fullImageURL%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09at%3A%20%27zoom%27%20put%3A%20%27fit%27%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09at%3A%20%27zoom_min%27%20put%3A%2010%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09at%3A%20%27zoom_max%27%20put%3A%20400%3B%0A%09%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09at%3A%20%27ui_disabled%27%20put%3A%20true%3B%0A%20%20%20%20%20%20%20%20%09%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09at%3A%20%27initCallback%27%20put%3A%20%5B%3AaViewer%7C%20self%20initIViewer%3A%20aViewer%5D%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09at%3A%20%27onZoom%27%20put%3A%20%5B%3AaString%7C%20self%20updateZoomStatus%3A%20aString%5D%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09yourself%29.%0A%0A%20%20%20%20%20%20%20%20%28html%20div%20class%3A%20%27page-desc%27%3B%20%20asJQuery%29%20html%3A%20page%20description.%0A%09html%20div%20class%3A%20%27clear%27.'),
messageSends: ["style:", "style", "renderControlsOn:", "class:", "asJQuery", "div", "ifNotEmpty:", "description", "addClass:", "iviewer:", "at:put:", "fullImageURL", "initIViewer:", "updateZoomStatus:", "yourself", "new", "html:"],
referencedClasses: ["HashedCollection"]
}),
smalltalk.PageWidget);

smalltalk.addMethod(
unescape('_rotateRight'),
smalltalk.method({
selector: unescape('rotateRight'),
category: 'callback',
fn: function (){
var self=this;
var rotationDeg=nil;
(self['@rotation']=((($receiver = (($receiver = self['@rotation']) == nil || $receiver == undefined) ? (function(){return (0);})() : $receiver).klass === smalltalk.Number) ? $receiver +(90) : smalltalk.send($receiver, "__plus", [(90)])));
(rotationDeg=smalltalk.send(smalltalk.send(unescape("rotate%28"), "__comma", [smalltalk.send(self['@rotation'], "_asString", [])]), "__comma", [unescape("deg%29")]));
(function($rec){smalltalk.send($rec, "_css_value_", [unescape("-ms-transform"), rotationDeg]);smalltalk.send($rec, "_css_value_", [unescape("-o-transform"), rotationDeg]);smalltalk.send($rec, "_css_value_", [unescape("-moz-transform"), rotationDeg]);return smalltalk.send($rec, "_css_value_", [unescape("-webkit-transform"), rotationDeg]);})(smalltalk.send(".iviewer img", "_asJQuery", []));
return self;},
args: [],
source: unescape('rotateRight%0A%09%7CrotationDeg%7C%0A%09rotation%20%3A%3D%20%28rotation%20ifNil%3A%5B0%5D%29%20+%2090%20.%0A%09rotationDeg%20%3A%3D%20%27rotate%28%27%2Crotation%20asString%2C%20%27deg%29%27.%0A%09%27.iviewer%20img%27%20asJQuery%0A%09%09css%3A%20%27-ms-transform%27%20value%3A%20rotationDeg%3B%0A%09%09css%3A%20%27-o-transform%27%20value%3A%20rotationDeg%3B%0A%09%09css%3A%20%27-moz-transform%27%20value%3A%20rotationDeg%3B%0A%09%09css%3A%20%27-webkit-transform%27%20value%3A%20rotationDeg'),
messageSends: [unescape("+"), "ifNil:", unescape("%2C"), "asString", "css:value:", "asJQuery"],
referencedClasses: []
}),
smalltalk.PageWidget);

smalltalk.addMethod(
unescape('_style'),
smalltalk.method({
selector: unescape('style'),
category: 'css',
fn: function (){
var self=this;
return unescape("%09.b-zoom%20.controls%20%7B%0A%09%09%09%20%20height%3A%20auto%3B%0A%09%09%09%20%20padding%3A%204px%3B%0A%09%09%09%20%20margin%3A%200%204px%3B%0A%09%09%09%20%20background-color%3A%20rgb%28200%2C200%2C200%29%3B%0A%09%09%09%20%20background-color%3A%20rgba%28200%2C200%2C200%2C0.8%29%3B%0A%09%09%09%20%20overflow%3A%20hidden%3B%0A%09%09%09%20%20float%3A%20right%3B%0A%09%09%09%20%20position%3A%20absolute%3B%0A%09%09%09%20%20*position%3A%20relative%3B%0A%09%09%09%20%20z-index%3A%201%3B%0A%09%09%09%20%20text-align%3A%20center%3B%0A%09%09%09%20%20width%3A%2042px%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20right%3A%200px%3B%0A%09%09%09%7D%0A");
return self;},
args: [],
source: unescape('style%0A%09%5E%20%27%09.b-zoom%20.controls%20%7B%0A%09%09%09%20%20height%3A%20auto%3B%0A%09%09%09%20%20padding%3A%204px%3B%0A%09%09%09%20%20margin%3A%200%204px%3B%0A%09%09%09%20%20background-color%3A%20rgb%28200%2C200%2C200%29%3B%0A%09%09%09%20%20background-color%3A%20rgba%28200%2C200%2C200%2C0.8%29%3B%0A%09%09%09%20%20overflow%3A%20hidden%3B%0A%09%09%09%20%20float%3A%20right%3B%0A%09%09%09%20%20position%3A%20absolute%3B%0A%09%09%09%20%20*position%3A%20relative%3B%0A%09%09%09%20%20z-index%3A%201%3B%0A%09%09%09%20%20text-align%3A%20center%3B%0A%09%09%09%20%20width%3A%2042px%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20right%3A%200px%3B%0A%09%09%09%7D%0A%27'),
messageSends: [],
referencedClasses: []
}),
smalltalk.PageWidget);

smalltalk.addMethod(
unescape('_updateZoomStatus_'),
smalltalk.method({
selector: unescape('updateZoomStatus%3A'),
category: 'callback',
fn: function (newZoom){
var self=this;
smalltalk.send(self['@statusControl'], "_contents_", [smalltalk.send("x", "__comma", [smalltalk.send(((($receiver = newZoom).klass === smalltalk.Number) ? $receiver /(100) : smalltalk.send($receiver, "__slash", [(100)])), "_printShowingDecimalPlaces_", [(1)])])]);
return self;},
args: ["newZoom"],
source: unescape('updateZoomStatus%3A%20newZoom%0A%09statusControl%20contents%3A%20%27x%27%2C%20%28newZoom%20/%20100%20%20printShowingDecimalPlaces%3A%201%29.'),
messageSends: ["contents:", unescape("%2C"), "printShowingDecimalPlaces:", unescape("/")],
referencedClasses: []
}),
smalltalk.PageWidget);



