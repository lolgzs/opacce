smalltalk.addPackage('AFI', {});
smalltalk.addClass('AbstractBookNavigatorWidget', smalltalk.Widget, ['book', 'announcer'], 'AFI');
smalltalk.addMethod(
"_announcePageChange_",
smalltalk.method({
selector: "announcePageChange:",
category: 'announcement',
fn: function (aPage) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_announcer", []), "_announce_", [smalltalk.send(smalltalk.PageChangeAnnouncement || PageChangeAnnouncement, "_page_", [aPage])]);
    return self;
},
args: ["aPage"],
source: "announcePageChange: aPage\x0a\x09self announcer announce: (PageChangeAnnouncement page: aPage)",
messageSends: ["announce:", "announcer", "page:"],
referencedClasses: ["PageChangeAnnouncement"]
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_announcer",
smalltalk.method({
selector: "announcer",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = self['@announcer']) == nil || $receiver == undefined ? function () {return self['@announcer'] = smalltalk.send(smalltalk.Announcer || Announcer, "_new", []);}() : $receiver;
    return self;
},
args: [],
source: "announcer\x0a\x09^ announcer ifNil: [announcer := Announcer new]",
messageSends: ["ifNil:", "new"],
referencedClasses: ["Announcer"]
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_book_",
smalltalk.method({
selector: "book:",
category: 'accessing',
fn: function (aBook) {
    var self = this;
    self['@book'] = aBook;
    return self;
},
args: ["aBook"],
source: "book: aBook\x0a\x09book := aBook",
messageSends: [],
referencedClasses: []
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_highlightPage_",
smalltalk.method({
selector: "highlightPage:",
category: 'actions',
fn: function (aPage) {
    var self = this;
    return self;
},
args: ["aPage"],
source: "highlightPage: aPage",
messageSends: [],
referencedClasses: []
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_onPageChangeDo_",
smalltalk.method({
selector: "onPageChangeDo:",
category: 'announcement',
fn: function (aBlockWithArg) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_announcer", []), "_on_do_", [smalltalk.PageChangeAnnouncement || PageChangeAnnouncement, function (aPageChangeAnnouncement) {return smalltalk.send(aBlockWithArg, "_value_", [smalltalk.send(aPageChangeAnnouncement, "_page", [])]);}]);
    return self;
},
args: ["aBlockWithArg"],
source: "onPageChangeDo: aBlockWithArg\x0a\x09self announcer \x0a\x09\x09on: PageChangeAnnouncement \x0a\x09\x09do: [:aPageChangeAnnouncement| \x0a\x09\x09\x09aBlockWithArg value: aPageChangeAnnouncement page]",
messageSends: ["on:do:", "announcer", "value:", "page"],
referencedClasses: ["PageChangeAnnouncement"]
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_renderOn_",
smalltalk.method({
selector: "renderOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    smalltalk.send(self, "_subclassResponsibility", []);
    return self;
},
args: ["html"],
source: "renderOn: html\x0a\x09self subclassResponsibility",
messageSends: ["subclassResponsibility"],
referencedClasses: []
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_style",
smalltalk.method({
selector: "style",
category: 'accessing',
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(unescape("%0A%0A.b-navigator%20%7B%0A%09height%3A%20500px%3B%0A%20%09width%3A%20"), "__comma", [smalltalk.send(self, "_width", [])]), "__comma", [unescape("px%3B%0A%09overflow-x%3A%20hidden%3B%0A%09overflow-y%3A%20hidden%3B%0A%09border%3A%202px%20solid%20%23666%3B%0A%7D%0A%0A.b-navigator%3Ediv%20%7B%0A%09text-align%3A%20center%3B%0A%09border-bottom%3A%201px%20solid%20%23666%3B%0A%09background-color%3A%20%23666%3B%0A%09font-size%3A%201.1em%3B%0A%7D%0A%0A.b-navigator%3Einput%20%7B%0A%09width%3A%20100%25%3B%0A%09border%3A%201px%20solid%20%23666%3B%0A%09margin%3A%200px%3B%0A%7D%0A%0A.b-navigator%20ul%20%7B%0A%09height%3A%2090%25%3B%0A%09overflow-x%3A%20hidden%3B%0A%09overflow-y%3A%20auto%3B%0A%09margin%3A%200px%3B%0A%7D%0A")]);
    return self;
},
args: [],
source: "style\x0a\x09^ '\x0a\x0a.b-navigator {\x0a\x09height: 500px;\x0a \x09width: ', self width, 'px;\x0a\x09overflow-x: hidden;\x0a\x09overflow-y: hidden;\x0a\x09border: 2px solid #666;\x0a}\x0a\x0a.b-navigator>div {\x0a\x09text-align: center;\x0a\x09border-bottom: 1px solid #666;\x0a\x09background-color: #666;\x0a\x09font-size: 1.1em;\x0a}\x0a\x0a.b-navigator>input {\x0a\x09width: 100%;\x0a\x09border: 1px solid #666;\x0a\x09margin: 0px;\x0a}\x0a\x0a.b-navigator ul {\x0a\x09height: 90%;\x0a\x09overflow-x: hidden;\x0a\x09overflow-y: auto;\x0a\x09margin: 0px;\x0a}\x0a'",
messageSends: [",", "width"],
referencedClasses: []
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_width",
smalltalk.method({
selector: "width",
category: 'accessing',
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_class", []), "_width", []);
    return self;
},
args: [],
source: "width\x0a\x09^ self class width",
messageSends: ["width", "class"],
referencedClasses: []
}),
smalltalk.AbstractBookNavigatorWidget);


smalltalk.addMethod(
"_width",
smalltalk.method({
selector: "width",
category: 'accessing',
fn: function () {
    var self = this;
    return 160;
    return self;
},
args: [],
source: "width\x0a\x09^ 160",
messageSends: [],
referencedClasses: []
}),
smalltalk.AbstractBookNavigatorWidget.klass);


smalltalk.addClass('BookBookmarkNavigatorWidget', smalltalk.AbstractBookNavigatorWidget, ['bookmarkList'], 'AFI');
smalltalk.addMethod(
"_highlightPage_",
smalltalk.method({
selector: "highlightPage:",
category: 'actions',
fn: function (aPage) {
    var self = this;
    var pageTitle = nil;
    var listItemIndex = nil;
    smalltalk.send(smalltalk.send(self['@bookmarkList'], "_find_", ["li"]), "_removeClass_", ["selected"]);
    pageTitle = smalltalk.send(smalltalk.send(aPage, "_title", []), "_ifEmpty_", [function () {return smalltalk.send(smalltalk.send(self['@book'], "_pageAt_ifAbsent_", [($receiver = smalltalk.send(aPage, "_pageNo", [])).klass === smalltalk.Number ? $receiver - 1 : smalltalk.send($receiver, "__minus", [1]), function () {return aPage;}]), "_title", []);}]);
    ($receiver = smalltalk.send(pageTitle, "_isEmpty", [])).klass === smalltalk.Boolean ? !$receiver ? function () {return smalltalk.send(smalltalk.send(self['@bookmarkList'], "_find_", [smalltalk.send(smalltalk.send(unescape("li%3Acontains%28%22"), "__comma", [pageTitle]), "__comma", [unescape("%22%29")])]), "_addClass_", ["selected"]);}() : nil : smalltalk.send($receiver, "_ifFalse_", [function () {return smalltalk.send(smalltalk.send(self['@bookmarkList'], "_find_", [smalltalk.send(smalltalk.send(unescape("li%3Acontains%28%22"), "__comma", [pageTitle]), "__comma", [unescape("%22%29")])]), "_addClass_", ["selected"]);}]);
    return self;
},
args: ["aPage"],
source: "highlightPage: aPage\x0a\x09|pageTitle listItemIndex|\x0a\x09(bookmarkList find: 'li') removeClass: 'selected'.\x0a\x0a\x09pageTitle := aPage title ifEmpty: [ (book pageAt: (aPage pageNo - 1) ifAbsent: [aPage]) title].\x0a\x0a\x09pageTitle isEmpty ifFalse: [\x0a\x09\x09(bookmarkList find: 'li:contains(\x22', pageTitle, '\x22)') addClass: 'selected'.\x0a\x09] ",
messageSends: ["removeClass:", "find:", "ifEmpty:", "title", "pageAt:ifAbsent:", "-", "pageNo", "ifFalse:", "isEmpty", "addClass:", ","],
referencedClasses: []
}),
smalltalk.BookBookmarkNavigatorWidget);

smalltalk.addMethod(
"_renderOn_",
smalltalk.method({
selector: "renderOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    smalltalk.send(html, "_style_", [smalltalk.send(self, "_style", [])]);
    (function ($rec) {smalltalk.send($rec, "_class_", [unescape("b-navigator-bookmark%20b-navigator")]);return smalltalk.send($rec, "_with_", [function () {var bookmarkSearchInput = nil;smalltalk.send(html, "_div_", ["Signets"]);bookmarkSearchInput = smalltalk.send(smalltalk.send(html, "_input", []), "_asJQuery", []);self['@bookmarkList'] = smalltalk.send(smalltalk.send(html, "_ul_", [function () {return smalltalk.send(self, "_renderPagesOn_", [html]);}]), "_asJQuery", []);return smalltalk.send(smalltalk.ListFilter || ListFilter, "_filter_withInput_", [self['@bookmarkList'], bookmarkSearchInput]);}]);}(smalltalk.send(html, "_div", [])));
    return self;
},
args: ["html"],
source: "renderOn: html\x0a\x09html style: self style.\x0a\x09html div \x0a\x09\x09class: 'b-navigator-bookmark b-navigator';\x0a\x09\x09with: [ |bookmarkSearchInput |\x0a\x09\x09\x09html div: 'Signets'.\x0a\x0a\x09\x09\x09bookmarkSearchInput := html input asJQuery.\x0a\x09\x09\x09bookmarkList := (html ul: [self renderPagesOn: html ]) asJQuery.\x0a\x0a\x09\x09\x09ListFilter filter: bookmarkList withInput: bookmarkSearchInput.\x0a\x09\x09]",
messageSends: ["style:", "style", "class:", "with:", "div:", "asJQuery", "input", "ul:", "renderPagesOn:", "filter:withInput:", "div"],
referencedClasses: ["ListFilter"]
}),
smalltalk.BookBookmarkNavigatorWidget);

smalltalk.addMethod(
"_renderPagesOn_",
smalltalk.method({
selector: "renderPagesOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    smalltalk.send(smalltalk.send(self['@book'], "_pagesWithTitle", []), "_do_", [function (aPage) {return function ($rec) {smalltalk.send($rec, "_with_", [smalltalk.send(aPage, "_title", [])]);return smalltalk.send($rec, "_onClick_", [function () {return smalltalk.send(self, "_announcePageChange_", [aPage]);}]);}(smalltalk.send(html, "_li", []));}]);
    return self;
},
args: ["html"],
source: "renderPagesOn: html\x0a\x09book pagesWithTitle do: [:aPage|\x0a\x09\x09html li\x0a\x09\x09\x09with: aPage title;\x0a\x09\x09\x09onClick: [self announcePageChange: aPage]\x0a\x09]",
messageSends: ["do:", "pagesWithTitle", "with:", "title", "onClick:", "announcePageChange:", "li"],
referencedClasses: []
}),
smalltalk.BookBookmarkNavigatorWidget);

smalltalk.addMethod(
"_style",
smalltalk.method({
selector: "style",
category: 'accessing',
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_style", [], smalltalk.AbstractBookNavigatorWidget), "__comma", [unescape("%0A.b-navigator-bookmark%20%7B%0A%09border-top-right-radius%3A%2010px%3B%0A%09border-bottom-right-radius%3A%2010px%3B%0A%09border-left%3A%200px%3B%0A%09margin-left%3A%200px%3B%0A%09margin-right%3A%2010px%3B%0A%09float%3A%20left%3B%0A%7D%0A%0A.b-navigator-bookmark%20ul%20%7B%0A%09list-style%3A%20square%3B%0A%09padding%3A%200px%2010px%200px%2015px%3B%0A%7D%0A%0A.b-navigator-bookmark%20li%20%7B%0A%09margin%3A%205px%3B%0A%09padding%3A%200px%3B%0A%09text-align%3A%20left%3B%0A%09cursor%3A%20pointer%3B%0A%09-webkit-transition%3A%20all%200.3s%3B%0A%09-moz-transition%3A%20all%200.3s%3B%0A%7D%0A%0A.b-navigator-bookmark%20li.selected%20%7B%0A%09text-decoration%3A%20underline%0A%7D%0A%0A.b-navigator-bookmark%20li%3Ahover%20%7B%0A%09color%3A%20%23aaa%3B%0A%7D")]);
    return self;
},
args: [],
source: "style\x0a\x09^ super style, '\x0a.b-navigator-bookmark {\x0a\x09border-top-right-radius: 10px;\x0a\x09border-bottom-right-radius: 10px;\x0a\x09border-left: 0px;\x0a\x09margin-left: 0px;\x0a\x09margin-right: 10px;\x0a\x09float: left;\x0a}\x0a\x0a.b-navigator-bookmark ul {\x0a\x09list-style: square;\x0a\x09padding: 0px 10px 0px 15px;\x0a}\x0a\x0a.b-navigator-bookmark li {\x0a\x09margin: 5px;\x0a\x09padding: 0px;\x0a\x09text-align: left;\x0a\x09cursor: pointer;\x0a\x09-webkit-transition: all 0.3s;\x0a\x09-moz-transition: all 0.3s;\x0a}\x0a\x0a.b-navigator-bookmark li.selected {\x0a\x09text-decoration: underline\x0a}\x0a\x0a.b-navigator-bookmark li:hover {\x0a\x09color: #aaa;\x0a}'",
messageSends: [",", "style"],
referencedClasses: []
}),
smalltalk.BookBookmarkNavigatorWidget);



smalltalk.addClass('BookThumbnailNavigatorWidget', smalltalk.AbstractBookNavigatorWidget, ['bookmarkList'], 'AFI');
smalltalk.addMethod(
"_highlightPage_",
smalltalk.method({
selector: "highlightPage:",
category: 'actions',
fn: function (aPage) {
    var self = this;
    var thumbnail = nil;
    var listItemIndex = nil;
    listItemIndex = smalltalk.send(0, "_max_", [($receiver = smalltalk.send(aPage, "_pageNo", [])).klass === smalltalk.Number ? $receiver - 2 : smalltalk.send($receiver, "__minus", [2])]);
    thumbnail = smalltalk.send(smalltalk.send(self['@bookmarkList'], "_find_", ["li"]), "_get_", [listItemIndex]);
    smalltalk.send(self['@bookmarkList'], "_scrollTop_", [($receiver = smalltalk.send(thumbnail, "_offsetTop", [])).klass === smalltalk.Number ? $receiver - (($receiver = smalltalk.send(self['@bookmarkList'], "_height", [])).klass === smalltalk.Number ? $receiver / 2 : smalltalk.send($receiver, "__slash", [2])) : smalltalk.send($receiver, "__minus", [($receiver = smalltalk.send(self['@bookmarkList'], "_height", [])).klass === smalltalk.Number ? $receiver / 2 : smalltalk.send($receiver, "__slash", [2])])]);
    smalltalk.send(smalltalk.send(self['@bookmarkList'], "_find_", ["li"]), "_removeClass_", ["selected"]);
    smalltalk.send(smalltalk.send(typeof window == "undefined" ? nil : window, "_jQuery_", [thumbnail]), "_addClass_", ["selected"]);
    return self;
},
args: ["aPage"],
source: "highlightPage: aPage\x0a\x09|thumbnail listItemIndex|\x0a        listItemIndex := 0 max: (aPage pageNo - 2).\x0a        thumbnail := (bookmarkList find: 'li') get: listItemIndex.\x0a\x09bookmarkList scrollTop: (thumbnail offsetTop - (bookmarkList height / 2)).\x0a\x09(bookmarkList find: 'li') removeClass: 'selected'.\x0a\x09(window jQuery: thumbnail) addClass: 'selected'.",
messageSends: ["max:", "-", "pageNo", "get:", "find:", "scrollTop:", "offsetTop", "/", "height", "removeClass:", "addClass:", "jQuery:"],
referencedClasses: []
}),
smalltalk.BookThumbnailNavigatorWidget);

smalltalk.addMethod(
"_renderOn_",
smalltalk.method({
selector: "renderOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    smalltalk.send(html, "_style_", [smalltalk.send(self, "_style", [])]);
    (function ($rec) {smalltalk.send($rec, "_class_", [unescape("b-navigator-thumbnail%20%20b-navigator")]);return smalltalk.send($rec, "_with_", [function () {var bookmarkSearchInput = nil;smalltalk.send(html, "_div_", ["Folios"]);bookmarkSearchInput = smalltalk.send(smalltalk.send(html, "_input", []), "_asJQuery", []);self['@bookmarkList'] = function ($rec) {smalltalk.send($rec, "_with_", [function () {return smalltalk.send(self, "_renderPagesOn_", [html]);}]);return smalltalk.send($rec, "_asJQuery", []);}(smalltalk.send(html, "_ul", []));return smalltalk.send(smalltalk.ListFilter || ListFilter, "_filter_withInput_", [self['@bookmarkList'], bookmarkSearchInput]);}]);}(smalltalk.send(html, "_div", [])));
    return self;
},
args: ["html"],
source: "renderOn: html\x0a\x09html style: self style.\x0a\x09html div \x0a\x09\x09class: 'b-navigator-thumbnail  b-navigator';\x0a\x09\x09with: [ |bookmarkSearchInput|\x0a\x09\x09\x09html div: 'Folios'.\x0a\x0a\x09\x09\x09bookmarkSearchInput := html input asJQuery.\x0a\x09\x09\x09bookmarkList := html ul \x0a\x09\x09\x09\x09\x09\x09\x09\x09with: [self renderPagesOn: html ]; \x0a\x09\x09\x09\x09\x09\x09\x09\x09asJQuery.\x0a\x0a\x09\x09\x09ListFilter filter: bookmarkList withInput: bookmarkSearchInput.\x0a\x09]",
messageSends: ["style:", "style", "class:", "with:", "div:", "asJQuery", "input", "renderPagesOn:", "ul", "filter:withInput:", "div"],
referencedClasses: ["ListFilter"]
}),
smalltalk.BookThumbnailNavigatorWidget);

smalltalk.addMethod(
"_renderPagesOn_",
smalltalk.method({
selector: "renderPagesOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    var cycle = nil;
    cycle = smalltalk.send(smalltalk.Cycle || Cycle, "_with_", [["odd", "even"]]);
    smalltalk.send(smalltalk.send(self['@book'], "_pages", []), "_do_", [function (aPage) {return function ($rec) {smalltalk.send($rec, "_class_", [smalltalk.send(cycle, "_next", [])]);smalltalk.send($rec, "_with_", [function () {return smalltalk.send(html, "_div_", [function () {smalltalk.send(html, "_div_", [smalltalk.send(aPage, "_foliono", [])]);return smalltalk.send(smalltalk.send(html, "_img", []), "_src_", [smalltalk.send(aPage, "_navigatorThumbnailURL", [])]);}]);}]);return smalltalk.send($rec, "_onClick_", [function () {return smalltalk.send(self, "_announcePageChange_", [aPage]);}]);}(smalltalk.send(html, "_li", []));}]);
    return self;
},
args: ["html"],
source: "renderPagesOn: html\x0a\x09|cycle|\x0a\x09cycle := Cycle with: #('odd' 'even').\x0a\x0a\x09book pages do: [:aPage|\x0a\x09\x09html li\x0a\x09\x09\x09class: cycle next;\x0a\x09\x09\x09with: [ \x09html div: [\x09html div: aPage foliono.\x0a\x09\x09\x09\x09\x09\x09 \x09\x09html img src: aPage navigatorThumbnailURL] ];\x0a\x09\x09\x09onClick: [self announcePageChange: aPage]\x0a\x09]",
messageSends: ["with:", "do:", "pages", "class:", "next", "div:", "foliono", "src:", "img", "navigatorThumbnailURL", "onClick:", "announcePageChange:", "li"],
referencedClasses: ["Cycle"]
}),
smalltalk.BookThumbnailNavigatorWidget);

smalltalk.addMethod(
"_style",
smalltalk.method({
selector: "style",
category: 'css',
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_style", [], smalltalk.AbstractBookNavigatorWidget), "__comma", [unescape("%0A.b-navigator-thumbnail%20%7B%0A%09border-top-left-radius%3A%2010px%3B%0A%09border-bottom-left-radius%3A%2010px%3B%0A%09border-right%3A%200px%3B%0A%09margin-left%3A%2010px%3B%0A%09margin-right%3A%200px%3B%0A%09float%3A%20right%3B%0A%7D%0A%0A.b-navigator-thumbnail%20ul%20%7B%0A%09list-style%3A%20none%3B%0A%09padding%3A%200px%3B%0A%09float%3A%20right%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%20%7B%0A%09float%3A%20left%3B%0A%09margin%3A%205px%3B%0A%09display%3A%20block%3B%0A%09overflow%3A%20hidden%3B%0A%09height%3A%2070px%3B%0A%09width%3A%2050px%3B%0A%09text-align%3A%20center%3B%0A%09cursor%3A%20pointer%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ediv%3Ediv%7B%0A%09display%3A%20none%3B%0A%09position%3A%20relative%3B%0A%09z-index%3A%202%3B%0A%09background-color%3A%20black%3B%0A%09font-weight%3A%20bold%3B%0A%09font-size%3A%200.9em%3B%0A%7D%0A%0A%0A.b-navigator-thumbnail%20li.selected%20img%2C%0A.b-navigator-thumbnail%20li.selected%20+%20li.odd%20img%2C%0A.b-navigator-thumbnail%20.filtered%20li%20img%2C%0A.b-navigator-thumbnail%20li%3Ahover%20img%20%7B%0A%09opacity%3A%201%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ahover%20%7B%0A%09overflow%3A%20visible%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ahover%3Ediv%3Ediv%7B%0A%09display%3A%20block%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ediv%20%7B%0A%09width%3A%2050px%3B%0A%20%20%09-webkit-transition%3A%20all%200.1s%20ease-out%3B%0A%20%09-moz-transition%3A%20all%200.1s%20ease-out%3B%0A%7D%0A%0A%0A.b-navigator-thumbnail%20li%3Ahover%3Ediv%20%7B%0A%20%20%20width%3A%20100px%3B%0A%20%20%20position%3A%20relative%3B%0A%20%20%20box-shadow%3A%200px%200px%2020px%20black%3B%0A%20%20%20z-index%3A%2030%3B%0A%7D%0A%0A%0A.b-navigator-thumbnail%20li.odd%3Ahover%3Ediv%20%7B%0A%20%20%20margin-left%3A%20-40px%3B%0A%7D%0A%0A.b-navigator-thumbnail%20ul%20li%3Afirst-child%3Ahover%3Ediv%20%7B%0A%20%20%20margin-right%3A%20-40px%3B%0A%20%20%20margin-left%3A%200px%3B%0A%20%20%20margin-top%3A%200px%3B%0A%7D%0A%0A.b-navigator-thumbnail%20.filtered%20li.odd%3Ahover%3Ediv%20%7B%0A%20%20%20margin-left%3A%200px%3B%0A%7D%0A%0A.b-navigator-thumbnail%20.filtered%20li%20%7B%0A%20%20%20width%3A%20100%25%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%20img%20%7B%0A%20%20%09width%3A%20100%25%3B%0A%09display%3A%20block%3B%0A%09opacity%3A%200.6%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Afirst-child%20+%20li%7B%0A%09clear%3A%20left%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%20img%20%7B%0A%09cursor%3A%20pointer%3B%0A%7D%0A")]);
    return self;
},
args: [],
source: "style\x0a\x09^ super style, '\x0a.b-navigator-thumbnail {\x0a\x09border-top-left-radius: 10px;\x0a\x09border-bottom-left-radius: 10px;\x0a\x09border-right: 0px;\x0a\x09margin-left: 10px;\x0a\x09margin-right: 0px;\x0a\x09float: right;\x0a}\x0a\x0a.b-navigator-thumbnail ul {\x0a\x09list-style: none;\x0a\x09padding: 0px;\x0a\x09float: right;\x0a}\x0a\x0a.b-navigator-thumbnail li {\x0a\x09float: left;\x0a\x09margin: 5px;\x0a\x09display: block;\x0a\x09overflow: hidden;\x0a\x09height: 70px;\x0a\x09width: 50px;\x0a\x09text-align: center;\x0a\x09cursor: pointer;\x0a}\x0a\x0a.b-navigator-thumbnail li>div>div{\x0a\x09display: none;\x0a\x09position: relative;\x0a\x09z-index: 2;\x0a\x09background-color: black;\x0a\x09font-weight: bold;\x0a\x09font-size: 0.9em;\x0a}\x0a\x0a\x0a.b-navigator-thumbnail li.selected img,\x0a.b-navigator-thumbnail li.selected + li.odd img,\x0a.b-navigator-thumbnail .filtered li img,\x0a.b-navigator-thumbnail li:hover img {\x0a\x09opacity: 1;\x0a}\x0a\x0a.b-navigator-thumbnail li:hover {\x0a\x09overflow: visible;\x0a}\x0a\x0a.b-navigator-thumbnail li:hover>div>div{\x0a\x09display: block;\x0a}\x0a\x0a.b-navigator-thumbnail li>div {\x0a\x09width: 50px;\x0a  \x09-webkit-transition: all 0.1s ease-out;\x0a \x09-moz-transition: all 0.1s ease-out;\x0a}\x0a\x0a\x0a.b-navigator-thumbnail li:hover>div {\x0a   width: 100px;\x0a   position: relative;\x0a   box-shadow: 0px 0px 20px black;\x0a   z-index: 30;\x0a}\x0a\x0a\x0a.b-navigator-thumbnail li.odd:hover>div {\x0a   margin-left: -40px;\x0a}\x0a\x0a.b-navigator-thumbnail ul li:first-child:hover>div {\x0a   margin-right: -40px;\x0a   margin-left: 0px;\x0a   margin-top: 0px;\x0a}\x0a\x0a.b-navigator-thumbnail .filtered li.odd:hover>div {\x0a   margin-left: 0px;\x0a}\x0a\x0a.b-navigator-thumbnail .filtered li {\x0a   width: 100%;\x0a}\x0a\x0a.b-navigator-thumbnail li img {\x0a  \x09width: 100%;\x0a\x09display: block;\x0a\x09opacity: 0.6;\x0a}\x0a\x0a.b-navigator-thumbnail li:first-child + li{\x0a\x09clear: left;\x0a}\x0a\x0a.b-navigator-thumbnail li img {\x0a\x09cursor: pointer;\x0a}\x0a'",
messageSends: [",", "style"],
referencedClasses: []
}),
smalltalk.BookThumbnailNavigatorWidget);



smalltalk.addClass('AbstractBookWidget', smalltalk.Widget, ['announcer', 'currentPageNo', 'book', 'scriptsRoot', 'rootBrush', 'isFullscreen', 'downloadBrush', 'menuJQuery', 'pageZoomWidget', 'pageZoomBrush', 'pageDescriptionsBrush', 'bookContainer', 'loader'], 'AFI');
smalltalk.addMethod(
"_announcer",
smalltalk.method({
selector: "announcer",
category: 'announcements',
fn: function () {
    var self = this;
    var $1;
    if (($receiver = self['@announcer']) == nil || $receiver == undefined) {
        self['@announcer'] = smalltalk.send(smalltalk.Announcer || Announcer, "_new", []);
        $1 = self['@announcer'];
    } else {
        $1 = self['@announcer'];
    }
    return $1;
},
args: [],
source: "announcer\x0a\x09^ announcer ifNil: [announcer := Announcer new]",
messageSends: ["ifNil:", "new"],
referencedClasses: ["Announcer"]
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_book",
smalltalk.method({
selector: "book",
category: 'accessor',
fn: function () {
    var self = this;
    return self['@book'];
},
args: [],
source: "book\x0a\x09^ book",
messageSends: [],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_book_",
smalltalk.method({
selector: "book:",
category: 'accessor',
fn: function (aBook) {
    var self = this;
    self['@book'] = aBook;
    return self;
},
args: ["aBook"],
source: "book: aBook\x0a\x09book := aBook",
messageSends: [],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_bookStyle",
smalltalk.method({
selector: "bookStyle",
category: 'css',
fn: function () {
    var self = this;
    return "";
},
args: [],
source: "bookStyle\x0a\x09^ ''",
messageSends: [],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_closeDescriptions",
smalltalk.method({
selector: "closeDescriptions",
category: 'descriptions',
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(self['@pageDescriptionsBrush'], "_asJQuery", []), "_fadeOut", []);
    return self;
},
args: [],
source: "closeDescriptions\x0a\x09pageDescriptionsBrush asJQuery fadeOut.",
messageSends: ["fadeOut", "asJQuery"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_currentPage",
smalltalk.method({
selector: "currentPage",
category: 'accessor',
fn: function () {
    var self = this;
    var $1;
    $1 = smalltalk.send(self['@book'], "_pageAt_ifAbsent_", [smalltalk.send(self, "_currentPageNo", []), function () {return smalltalk.send(smalltalk.send(self['@book'], "_pages", []), "_last", []);}]);
    return $1;
},
args: [],
source: "currentPage\x0a\x09^ book pageAt: self currentPageNo ifAbsent: [book pages last]",
messageSends: ["pageAt:ifAbsent:", "currentPageNo", "last", "pages"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_currentPageNo",
smalltalk.method({
selector: "currentPageNo",
category: 'accessor',
fn: function () {
    var self = this;
    var $1;
    if (($receiver = self['@currentPageNo']) == nil ||
        $receiver == undefined) {
        self['@currentPageNo'] = 1;
        $1 = self['@currentPageNo'];
    } else {
        $1 = self['@currentPageNo'];
    }
    return $1;
},
args: [],
source: "currentPageNo\x0a\x09^ currentPageNo ifNil: [currentPageNo := 1]",
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_fullScreenStyle",
smalltalk.method({
selector: "fullScreenStyle",
category: 'css',
fn: function () {
    var self = this;
    var $1;
    $1 = smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send("\n\tbody.fullscreen {\n\t\toverflow: hidden;\n\t}\n\n\n\t.fullscreen.bk-widget {\n\t\tposition: fixed;\n\t\twidth: 100%;\n\t\theight: 100%;\n\t\tz-index: 200;\n\t\ttop: 0;\n\t\tleft: 0;\n\t\toverflow-y: auto;\n\t}\n\n\t.fullscreen.bk-widget .b-menu {\n\t\theight: 0px;\n\t}\n\n\t.fullscreen.bk-widget,\n\t.fullscreen.bk-widget .b-menu .b-selector,\n\t.fullscreen.bk-widget .b-menu .b-selector ul,\n\t.fullscreen.bk-widget .b-counter {\t\n\t\tcolor: white;\n\t\tbackground-color: black;\n\t}\n\n\t.fullscreen .b-zoom-fullscreen {\n\t\tposition: absolute;\n\t\tright: 0px;\n\t}\n\n\t.fullscreen.bk-widget .b-download-book a {\n\t\tposition: absolute;\n\t\tright: 60px;\n\t}\n\n\t.fullscreen .b-zoom-fullscreen a {\n\t\tbackground: url(", "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/unexpand_black.png) no-repeat;\n\t}\n\n\t.fullscreen .b-zoom-fullscreen a:hover {\n\t\tbackground: url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/unexpand_white.png) no-repeat;\n\t}\n\n\t.fullscreen h1.title {\n\t\tfont-size: 2em;\n\t\tcolor: white;\n\t\tborder-bottom: 0px;\n\t\tmargin: 5px 0px 0px 0px;\n\t\ttext-align: center;\n\t}\n\n\t.fullscreen \n"]);
    return $1;
},
args: [],
source: "fullScreenStyle\x0a\x09^ '\x0a\x09body.fullscreen {\x0a\x09\x09overflow: hidden;\x0a\x09}\x0a\x0a\x0a\x09.fullscreen.bk-widget {\x0a\x09\x09position: fixed;\x0a\x09\x09width: 100%;\x0a\x09\x09height: 100%;\x0a\x09\x09z-index: 200;\x0a\x09\x09top: 0;\x0a\x09\x09left: 0;\x0a\x09\x09overflow-y: auto;\x0a\x09}\x0a\x0a\x09.fullscreen.bk-widget .b-menu {\x0a\x09\x09height: 0px;\x0a\x09}\x0a\x0a\x09.fullscreen.bk-widget,\x0a\x09.fullscreen.bk-widget .b-menu .b-selector,\x0a\x09.fullscreen.bk-widget .b-menu .b-selector ul,\x0a\x09.fullscreen.bk-widget .b-counter {\x09\x0a\x09\x09color: white;\x0a\x09\x09background-color: black;\x0a\x09}\x0a\x0a\x09.fullscreen .b-zoom-fullscreen {\x0a\x09\x09position: absolute;\x0a\x09\x09right: 0px;\x0a\x09}\x0a\x0a\x09.fullscreen.bk-widget .b-download-book a {\x0a\x09\x09position: absolute;\x0a\x09\x09right: 60px;\x0a\x09}\x0a\x0a\x09.fullscreen .b-zoom-fullscreen a {\x0a\x09\x09background: url(', self scriptsRoot, 'images/unexpand_black.png) no-repeat;\x0a\x09}\x0a\x0a\x09.fullscreen .b-zoom-fullscreen a:hover {\x0a\x09\x09background: url(', self scriptsRoot, 'images/unexpand_white.png) no-repeat;\x0a\x09}\x0a\x0a\x09.fullscreen h1.title {\x0a\x09\x09font-size: 2em;\x0a\x09\x09color: white;\x0a\x09\x09border-bottom: 0px;\x0a\x09\x09margin: 5px 0px 0px 0px;\x0a\x09\x09text-align: center;\x0a\x09}\x0a\x0a\x09.fullscreen \x0a'",
messageSends: [",", "scriptsRoot"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_initialize",
smalltalk.method({
selector: "initialize",
category: 'initialize',
fn: function () {
    var self = this;
    smalltalk.send(self, "_initialize", [], smalltalk.Widget);
    self['@isFullscreen'] = false;
    return self;
},
args: [],
source: "initialize\x0a\x09super initialize.\x0a\x09isFullscreen := false.",
messageSends: ["initialize"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_isContainerSmall",
smalltalk.method({
selector: "isContainerSmall",
category: 'testing',
fn: function () {
    var self = this;
    var $1;
    $1 = smalltalk.send(smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_width", []), "__lt", [500]);
    return $1;
},
args: [],
source: "isContainerSmall\x0a\x09^ rootBrush asJQuery width < 500",
messageSends: ["<", "width", "asJQuery"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_isRunInTestCase",
smalltalk.method({
selector: "isRunInTestCase",
category: 'testing',
fn: function () {
    var self = this;
    var $1;
    $1 = smalltalk.send(self, "_isTestCaseInContext_", [smalltalk.getThisContext()]);
    return $1;
},
args: [],
source: "isRunInTestCase\x0a\x09^ self isTestCaseInContext: thisContext ",
messageSends: ["isTestCaseInContext:"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_isTestCaseInContext_",
smalltalk.method({
selector: "isTestCaseInContext:",
category: 'testing',
fn: function (aContext) {
    var self = this;
    var $2, $1;
    $2 = smalltalk.send(aContext, "_home", []);
    if (($receiver = $2) == nil || $receiver == undefined) {
        $1 = false;
    } else {
        $1 = smalltalk.send(smalltalk.send(smalltalk.send(aContext, "_receiver", []), "_isKindOf_", [smalltalk.TestCase || TestCase]), "_or_", [function () {return smalltalk.send(self, "_isTestCaseInContext_", [smalltalk.send(aContext, "_home", [])]);}]);
    }
    return $1;
},
args: ["aContext"],
source: "isTestCaseInContext: aContext \x0a\x09^ aContext home \x0a\x09\x09ifNil: [false]\x0a\x09\x09ifNotNil: [ (aContext receiver isKindOf: TestCase) or: [ self isTestCaseInContext: aContext home]].",
messageSends: ["ifNil:ifNotNil:", "or:", "isTestCaseInContext:", "home", "isKindOf:", "receiver"],
referencedClasses: ["TestCase"]
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_loadCSS_",
smalltalk.method({
selector: "loadCSS:",
category: 'external libs',
fn: function (anUrl) {
    var self = this;
    var $1, $2;
    $1 = smalltalk.send(smalltalk.send(smalltalk.HTMLCanvas || HTMLCanvas, "_onJQuery_", [smalltalk.send("head", "_asJQuery", [])]), "_link", []);
    smalltalk.send($1, "_href_", [smalltalk.send(smalltalk.send(self, "_scriptsRoot", []), "__comma", [anUrl])]);
    smalltalk.send($1, "_type_", ["text/css"]);
    $2 = smalltalk.send($1, "_rel_", ["stylesheet"]);
    return self;
},
args: ["anUrl"],
source: "loadCSS: anUrl  \x0a      (HTMLCanvas onJQuery: 'head' asJQuery)  link\x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09href: self scriptsRoot, anUrl;\x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09type:'text/css';\x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09rel:'stylesheet'",
messageSends: ["href:", ",", "scriptsRoot", "link", "onJQuery:", "asJQuery", "type:", "rel:"],
referencedClasses: ["HTMLCanvas"]
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_loadIViewerJS",
smalltalk.method({
selector: "loadIViewerJS",
category: 'external libs',
fn: function () {
    var self = this;
    var $1;
    smalltalk.send(self, "_loadCSS_", ["iviewer/jquery.iviewer.css"]);
    smalltalk.send(self, "_loadJS_", ["iviewer/jquery.iviewer.min.js"]);
    $1 = smalltalk.send(self, "_loadJS_", ["iviewer/jquery.mousewheel.min.js"]);
    return self;
},
args: [],
source: "loadIViewerJS   \x09\x0a      self\x0a      \x09loadCSS: 'iviewer/jquery.iviewer.css';\x0a        loadJS: 'iviewer/jquery.iviewer.min.js';\x0a        loadJS: 'iviewer/jquery.mousewheel.min.js'",
messageSends: ["loadCSS:", "loadJS:"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_loadJS_",
smalltalk.method({
selector: "loadJS:",
category: 'external libs',
fn: function (anUrl) {
    var self = this;
    smalltalk.send(jQuery, "_ajax_", [smalltalk.HashedCollection._fromPairs_([smalltalk.send("dataType", "__minus_gt", ["script"]), smalltalk.send("url", "__minus_gt", [smalltalk.send(smalltalk.send(self, "_scriptsRoot", []), "__comma", [anUrl])]), smalltalk.send("cache", "__minus_gt", [true])])]);
    return self;
},
args: ["anUrl"],
source: "loadJS: anUrl\x0a\x09 jQuery ajax: #{\x09\x0a     \x09\x09'dataType' -> 'script'.\x0a       \x09\x09'url' -> (self scriptsRoot, anUrl).\x0a            'cache' -> true\x0a     }",
messageSends: ["ajax:", "->", ",", "scriptsRoot"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_loader",
smalltalk.method({
selector: "loader",
category: 'accessor',
fn: function () {
    var self = this;
    return self['@loader'];
},
args: [],
source: "loader\x0a\x09^ loader",
messageSends: [],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_loader_",
smalltalk.method({
selector: "loader:",
category: 'accessor',
fn: function (aBibNumLoader) {
    var self = this;
    self['@loader'] = aBibNumLoader;
    return self;
},
args: ["aBibNumLoader"],
source: "loader: aBibNumLoader\x0a\x09loader := aBibNumLoader",
messageSends: [],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_onPageChangeDo_",
smalltalk.method({
selector: "onPageChangeDo:",
category: 'announcements',
fn: function (aBlockWithArg) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_announcer", []), "_on_do_", [smalltalk.PageChangeAnnouncement || PageChangeAnnouncement, function (aPageChangeAnnouncement) {return smalltalk.send(aBlockWithArg, "_value_", [smalltalk.send(aPageChangeAnnouncement, "_page", [])]);}]);
    return self;
},
args: ["aBlockWithArg"],
source: "onPageChangeDo: aBlockWithArg\x0a\x09self announcer \x0a\x09\x09on: PageChangeAnnouncement \x0a\x09\x09do: [:aPageChangeAnnouncement| \x0a\x09\x09\x09aBlockWithArg value: aPageChangeAnnouncement page]",
messageSends: ["on:do:", "value:", "page", "announcer"],
referencedClasses: ["PageChangeAnnouncement"]
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_openPage_",
smalltalk.method({
selector: "openPage:",
category: 'callbacks',
fn: function (aPage) {
    var self = this;
    smalltalk.send(self, "_subclassResponsibility", []);
    return self;
},
args: ["aPage"],
source: "openPage: aPage\x0a\x09self subclassResponsibility",
messageSends: ["subclassResponsibility"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_reloadWidget",
smalltalk.method({
selector: "reloadWidget",
category: 'callbacks',
fn: function () {
    var self = this;
    smalltalk.send(self['@rootBrush'], "_contents_", [function (html) {return smalltalk.send(self, "_renderWidgetOn_", [html]);}]);
    return self;
},
args: [],
source: "reloadWidget\x0a\x09rootBrush contents: [:html| self renderWidgetOn: html].",
messageSends: ["contents:", "renderWidgetOn:"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderBook_on_",
smalltalk.method({
selector: "renderBook:on:",
category: 'rendering',
fn: function (aBook, aBrush) {
    var self = this;
    var $1, $2, $3, $4;
    self['@book'] = aBook;
    smalltalk.send(aBrush, "_contents_", [function (html) {return smalltalk.send(smalltalk.send(aBook, "_pages", []), "_do_", [function (aPage) {$1 = smalltalk.send(html, "_div", []);smalltalk.send($1, "_rel_", [smalltalk.send(aPage, "_title", [])]);$2 = smalltalk.send($1, "_yourself", []);return smalltalk.send(aPage, "_brush_", [$2]);}]);}]);
    $3 = smalltalk.send(self, "_isContainerSmall", []);
    if (smalltalk.assert($3)) {
        smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_addClass_", ["small"]);
    }
    smalltalk.send(smalltalk.send(self['@book'], "_downloadUrl", []), "_ifNotEmpty_", [function () {return smalltalk.send(self['@downloadBrush'], "_contents_", [function (html) {return smalltalk.send(smalltalk.send(html, "_a", []), "_href_", [smalltalk.send(aBook, "_downloadUrl", [])]);}]);}]);
    if (smalltalk.assert(self['@isFullscreen'])) {
        smalltalk.send(self, "_renderBookNavigator", []);
        $4 = smalltalk.send(self, "_renderBookTitle", []);
    }
    return self;
},
args: ["aBook", "aBrush"],
source: "renderBook: aBook on: aBrush\x09\x0a    book := aBook.\x0a    \x0a\x09aBrush contents: [:html|\x0a\x09\x09aBook pages do: [:aPage| \x09aPage brush: (html div\x0a        \x09\x09         \x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09rel: aPage title;\x0a                                 \x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09yourself)  ]\x09].\x0a\x09\x0a\x09self isContainerSmall ifTrue: [rootBrush asJQuery addClass: 'small'].\x0a\x09\x0a\x09book downloadUrl ifNotEmpty: [downloadBrush contents: [:html| html a href: aBook downloadUrl]].\x0a\x09\x0a\x09isFullscreen ifTrue: [self renderBookNavigator; renderBookTitle].\x0a    \x0a    ",
messageSends: ["contents:", "do:", "brush:", "rel:", "title", "div", "yourself", "pages", "ifTrue:", "addClass:", "asJQuery", "isContainerSmall", "ifNotEmpty:", "href:", "downloadUrl", "a", "renderBookNavigator", "renderBookTitle"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderBookMenuOn_",
smalltalk.method({
selector: "renderBookMenuOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    var $1, $2;
    $1 = smalltalk.send(html, "_div", []);
    smalltalk.send($1, "_class_", ["book-menu"]);
    $2 = smalltalk.send($1, "_asJQuery", []);
    self['@menuJQuery'] = $2;
    return self;
},
args: ["html"],
source: "renderBookMenuOn: html\x0a\x09menuJQuery := html div\x0a\x09\x09class: 'book-menu';\x0a\x09\x09asJQuery.",
messageSends: ["class:", "div", "asJQuery"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderBookNavigator",
smalltalk.method({
selector: "renderBookNavigator",
category: 'rendering',
fn: function () {
    var self = this;
    var $1, $2;
    var navigatorDiv;
    navigatorDiv = smalltalk.send("<div></div>", "_asJQuery", []);
    smalltalk.send(navigatorDiv, "_insertAfter_", [self['@menuJQuery']]);
    smalltalk.send([smalltalk.BookBookmarkNavigatorWidget ||
        BookBookmarkNavigatorWidget, smalltalk.BookThumbnailNavigatorWidget ||
        BookThumbnailNavigatorWidget], "_do_", [function (aNavigatorClass) {var navigator;$1 = smalltalk.send(aNavigatorClass, "_new", []);smalltalk.send($1, "_book_", [self['@book']]);smalltalk.send($1, "_appendToJQuery_", [navigatorDiv]);smalltalk.send($1, "_onPageChangeDo_", [function (aPage) {return smalltalk.send(self, "_openPage_", [aPage]);}]);smalltalk.send($1, "_highlightPage_", [smalltalk.send(self, "_currentPage", [])]);$2 = smalltalk.send($1, "_yourself", []);navigator = $2;return smalltalk.send(self, "_onPageChangeDo_", [function (aPage) {return smalltalk.send(navigator, "_highlightPage_", [aPage]);}]);}]);
    return self;
},
args: [],
source: "renderBookNavigator\x0a\x09|navigatorDiv|\x0a\x09navigatorDiv := ('<div></div>') asJQuery.\x0a\x09navigatorDiv insertAfter: menuJQuery.\x0a\x0a\x09{BookBookmarkNavigatorWidget. BookThumbnailNavigatorWidget} do: [:aNavigatorClass| |navigator|\x0a\x09\x09navigator := aNavigatorClass new\x0a\x09\x09\x09\x09\x09\x09book: book;\x0a\x09\x09\x09\x09\x09\x09appendToJQuery: navigatorDiv;\x0a\x09\x09\x09\x09\x09\x09onPageChangeDo: [:aPage| self openPage: aPage];\x0a\x09\x09\x09\x09\x09\x09highlightPage: self currentPage;\x0a\x09\x09\x09\x09\x09\x09yourself.\x0a\x0a\x09\x09self onPageChangeDo: [:aPage|  navigator highlightPage: aPage].\x0a\x09]",
messageSends: ["asJQuery", "insertAfter:", "do:", "book:", "new", "appendToJQuery:", "onPageChangeDo:", "openPage:", "highlightPage:", "currentPage", "yourself"],
referencedClasses: ["BookBookmarkNavigatorWidget", "BookThumbnailNavigatorWidget"]
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderBookOn_",
smalltalk.method({
selector: "renderBookOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    smalltalk.send(self, "_subclassResponsibility", []);
    return self;
},
args: ["html"],
source: "renderBookOn: html\x0a\x09self subclassResponsibility",
messageSends: ["subclassResponsibility"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderBookTitle",
smalltalk.method({
selector: "renderBookTitle",
category: 'rendering',
fn: function () {
    var self = this;
    var titleDiv;
    titleDiv = smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send("<h1 class=\"title\">", "__comma", [smalltalk.send(self['@book'], "_title", [])]), "__comma", [" ( "]), "__comma", [smalltalk.send(self['@book'], "_size", [])]), "__comma", [" pages ) </h1>"]), "_asJQuery", []);
    smalltalk.send(titleDiv, "_insertBefore_", [self['@menuJQuery']]);
    return self;
},
args: [],
source: "renderBookTitle\x0a\x09|titleDiv|\x0a\x09titleDiv := ('<h1 class=\x22title\x22>', book title, ' ( ', book size ,' pages ) </h1>') asJQuery.\x0a\x09titleDiv insertBefore: menuJQuery.",
messageSends: ["asJQuery", ",", "size", "title", "insertBefore:"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderDevToolsOn_",
smalltalk.method({
selector: "renderDevToolsOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    var $1, $2;
    $1 = smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.Smalltalk || Smalltalk, "_current", []), "_at_", ["Browser"]), "_notNil", []), "_and_", [function () {return smalltalk.send(smalltalk.send(self, "_isRunInTestCase", []), "_not", []);}]);
    if (smalltalk.assert($1)) {
        smalltalk.send(smalltalk.AFIIDETools || AFIIDETools, "_addButton_action_", ["Reload booklet", function () {return smalltalk.send(self, "_reloadWidget", []);}]);
        smalltalk.send(smalltalk.AFIIDETools || AFIIDETools, "_addButton_action_", ["Inspect booklet", function () {return smalltalk.send(self, "_inspect", []);}]);
        $2 = smalltalk.send(smalltalk.AFIIDETools || AFIIDETools, "_addButton_action_", ["Toggle fullscreen", function () {return smalltalk.send(self, "_toggleFullscreen", []);}]);
    }
    return self;
},
args: ["html"],
source: "renderDevToolsOn: html\x0a\x09((Smalltalk current at: 'Browser') notNil and: [self isRunInTestCase not]) ifTrue:\x0a\x09\x09 [   AFIIDETools \x0a\x09\x09\x09\x09\x09addButton: 'Reload booklet' action: [ self reloadWidget ];\x0a\x09\x09\x09\x09\x09addButton: 'Inspect booklet' action: [ self inspect ];\x0a\x09\x09\x09\x09\x09addButton: 'Toggle fullscreen' action: [ self toggleFullscreen ]\x09]",
messageSends: ["ifTrue:", "addButton:action:", "reloadWidget", "inspect", "toggleFullscreen", "and:", "not", "isRunInTestCase", "notNil", "at:", "current"],
referencedClasses: ["AFIIDETools", "Smalltalk"]
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderDownloadBookOn_",
smalltalk.method({
selector: "renderDownloadBookOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    self['@downloadBrush'] = smalltalk.send(smalltalk.send(html, "_div", []), "_class_", ["b-download-book"]);
    return self;
},
args: ["html"],
source: "renderDownloadBookOn: html\x0a\x09downloadBrush := html div class: 'b-download-book'",
messageSends: ["class:", "div"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderFullscreenControlsOn_",
smalltalk.method({
selector: "renderFullscreenControlsOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    var $1, $2;
    $1 = smalltalk.send(html, "_div", []);
    smalltalk.send($1, "_class_", ["b-zoom-fullscreen"]);
    $2 = smalltalk.send($1, "_with_", [function () {return smalltalk.send(smalltalk.send(html, "_a", []), "_onClick_", [function () {return smalltalk.send(self, "_toggleFullscreen", []);}]);}]);
    return self;
},
args: ["html"],
source: "renderFullscreenControlsOn: html\x0a\x09html div \x0a\x09\x09class: 'b-zoom-fullscreen';\x0a\x09\x09with: [ html a onClick: [self toggleFullscreen] ].",
messageSends: ["class:", "div", "with:", "onClick:", "toggleFullscreen", "a"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderOn_",
smalltalk.method({
selector: "renderOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    smalltalk.send(self, "_renderDevToolsOn_", [html]);
    self['@rootBrush'] = smalltalk.send(html, "_root", []);
    smalltalk.send(self, "_renderWidgetOn_", [html]);
    return self;
},
args: ["html"],
source: "renderOn: html\x0a     self renderDevToolsOn: html.\x0a        \x0a\x09rootBrush := html root.\x0a        \x0a\x09self renderWidgetOn: html.",
messageSends: ["renderDevToolsOn:", "root", "renderWidgetOn:"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderPage_class_on_",
smalltalk.method({
selector: "renderPage:class:on:",
category: 'rendering',
fn: function (aPage, aCssClass, html) {
    var self = this;
    var $1, $2, $3, $4, $5;
    $1 = smalltalk.send(html, "_div", []);
    smalltalk.send($1, "_class_", [aCssClass]);
    smalltalk.send($1, "_with_", [function () {$2 = smalltalk.send(smalltalk.PageWidget || PageWidget, "_new", []);smalltalk.send($2, "_page_", [aPage]);smalltalk.send($2, "_renderOn_", [html]);smalltalk.send($2, "_onCloseDo_", [function () {smalltalk.send(self, "_closeZoom", []);$3 = smalltalk.send(self, "_openDescriptions", []);return $3;}]);$4 = smalltalk.send($2, "_yourself", []);self['@pageZoomWidget'] = $4;return self['@pageZoomWidget'];}]);
    $5 = smalltalk.send($1, "_asJQuery", []);
    smalltalk.send($5, "_fadeIn_", ["slow"]);
    return self;
},
args: ["aPage", "aCssClass", "html"],
source: "renderPage: aPage class: aCssClass on: html\x0a\x09(html div\x0a\x09\x09class: aCssClass;\x0a\x09\x09with:[\x09pageZoomWidget := PageWidget new\x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09page: aPage;\x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09renderOn: html;\x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09onCloseDo: [\x09self \x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09closeZoom; \x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09openDescriptions];\x0a                                                                               \x09yourself \x09\x09\x09\x09\x09\x09\x09\x09];\x0a                asJQuery) fadeIn: 'slow'. ",
messageSends: ["fadeIn:", "class:", "div", "with:", "page:", "new", "renderOn:", "onCloseDo:", "closeZoom", "openDescriptions", "yourself", "asJQuery"],
referencedClasses: ["PageWidget"]
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderPageDescriptionOn_",
smalltalk.method({
selector: "renderPageDescriptionOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    var $1, $2;
    $1 = smalltalk.send(html, "_div", []);
    smalltalk.send($1, "_class_", ["page-desc"]);
    $2 = smalltalk.send($1, "_yourself", []);
    self['@pageDescriptionsBrush'] = $2;
    return self;
},
args: ["html"],
source: "renderPageDescriptionOn: html \x09\x0a\x09pageDescriptionsBrush := html div \x0a                                                               class: 'page-desc';\x0a                       \x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09yourself.\x0a                      ",
messageSends: ["class:", "div", "yourself"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderWidgetOn_",
smalltalk.method({
selector: "renderWidgetOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    var $1, $2, $3, $5, $4;
    $1 = smalltalk.send(html, "_style", []);
    smalltalk.send($1, "_type_", ["text/css"]);
    $2 = smalltalk.send($1, "_with_", [smalltalk.send(self, "_style", [])]);
    $3 = smalltalk.send(html, "_div", []);
    smalltalk.send($3, "_class_", [smalltalk.send(self, "_widgetClass", [])]);
    $4 = smalltalk.send($3, "_with_", [function () {smalltalk.send(self, "_renderFullscreenControlsOn_", [html]);smalltalk.send(self, "_renderDownloadBookOn_", [html]);smalltalk.send(self, "_renderBookMenuOn_", [html]);smalltalk.send(self, "_renderZoomControlsOn_", [html]);smalltalk.send(self, "_renderBookOn_", [html]);$5 = smalltalk.send(self, "_renderPageDescriptionOn_", [html]);return $5;}]);
    if (smalltalk.assert(self['@isFullscreen'])) {
        smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_addClass_", ["fullscreen"]);
    } else {
        smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_removeClass_", ["fullscreen"]);
    }
    return self;
},
args: ["html"],
source: "renderWidgetOn: html\x0a\x09html style\x0a\x09\x09type: 'text/css';\x0a\x09\x09with: self style.\x0a        \x0a        \x0a\x09html div\x0a\x09\x09class: self widgetClass; \x0a\x09\x09with: [\x09self \x0a                    renderFullscreenControlsOn: html;\x0a\x09\x09\x09\x09\x09renderDownloadBookOn: html;\x0a                  \x09renderBookMenuOn: html;\x0a                    renderZoomControlsOn: html;\x0a                  \x09renderBookOn: html;\x0a\x09\x09\x09\x09\x09renderPageDescriptionOn: html].\x0a    \x0a\x09isFullscreen \x0a\x09\x09ifTrue: ['body' asJQuery addClass: 'fullscreen'] \x0a\x09\x09ifFalse: ['body' asJQuery removeClass: 'fullscreen'].",
messageSends: ["type:", "style", "with:", "class:", "widgetClass", "div", "renderFullscreenControlsOn:", "renderDownloadBookOn:", "renderBookMenuOn:", "renderZoomControlsOn:", "renderBookOn:", "renderPageDescriptionOn:", "ifTrue:ifFalse:", "addClass:", "asJQuery", "removeClass:"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderZoomControlsOn_",
smalltalk.method({
selector: "renderZoomControlsOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    var $1, $3, $4, $2;
    $1 = smalltalk.send(html, "_div", []);
    smalltalk.send($1, "_class_", ["b-zoom-magnify"]);
    $2 = smalltalk.send($1, "_with_", [function () {self['@zoomLeftPageAnchor'] = smalltalk.send(smalltalk.send(smalltalk.send(html, "_a", []), "_onClick_", [function () {return smalltalk.send(self, "_zoomLeftPage", []);}]), "_asJQuery", []);self['@zoomLeftPageAnchor'];smalltalk.send(self['@zoomLeftPageAnchor'], "_hide", []);self['@zoomRightPageAnchor'] = smalltalk.send(smalltalk.send(smalltalk.send(html, "_a", []), "_onClick_", [function () {return smalltalk.send(self, "_zoomRightPage", []);}]), "_asJQuery", []);self['@zoomRightPageAnchor'];smalltalk.send(self['@zoomRightPageAnchor'], "_hide", []);$3 = smalltalk.send(html, "_div", []);smalltalk.send($3, "_class_", ["b-zoom"]);$4 = smalltalk.send($3, "_yourself", []);self['@pageZoomBrush'] = $4;return self['@pageZoomBrush'];}]);
    return self;
},
args: ["html"],
source: "renderZoomControlsOn: html\x0a\x09html div\x0a\x09\x09class: 'b-zoom-magnify';\x0a\x09\x09with: [ \x09zoomLeftPageAnchor := (html a onClick: [self zoomLeftPage]) asJQuery.\x0a                       \x09\x09zoomLeftPageAnchor hide.\x0a                       \x0a                         \x09zoomRightPageAnchor := (html a onClick: [self zoomRightPage]) asJQuery.\x0a                       \x09\x09zoomRightPageAnchor hide.\x0a                                pageZoomBrush := html div \x0a\x09\x09\x09\x09\x09\x09class: 'b-zoom';\x0a\x09\x09\x09\x09\x09\x09yourself.\x0a                ].",
messageSends: ["class:", "div", "with:", "asJQuery", "onClick:", "zoomLeftPage", "a", "hide", "zoomRightPage", "yourself"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_scriptsRoot",
smalltalk.method({
selector: "scriptsRoot",
category: 'accessor',
fn: function () {
    var self = this;
    var $1;
    if (($receiver = self['@scriptsRoot']) == nil || $receiver == undefined) {
        self['@scriptsRoot'] = "";
        $1 = self['@scriptsRoot'];
    } else {
        $1 = self['@scriptsRoot'];
    }
    return $1;
},
args: [],
source: "scriptsRoot\x0a\x09^ scriptsRoot ifNil: [scriptsRoot := '']",
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_scriptsRoot_",
smalltalk.method({
selector: "scriptsRoot:",
category: 'accessor',
fn: function (anUrl) {
    var self = this;
    self['@scriptsRoot'] = anUrl;
    return self;
},
args: ["anUrl"],
source: "scriptsRoot: anUrl\x0a\x09scriptsRoot := anUrl",
messageSends: [],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_style",
smalltalk.method({
selector: "style",
category: 'css',
fn: function () {
    var self = this;
    var $2, $1;
    $1 = smalltalk.send(smalltalk.String || String, "_streamContents_", [function (aStream) {smalltalk.send(aStream, "_nextPutAll_", [smalltalk.send(self, "_bookStyle", [])]);smalltalk.send(aStream, "_nextPutAll_", [smalltalk.send(self, "_zoomControlsStyle", [])]);$2 = smalltalk.send(aStream, "_nextPutAll_", [smalltalk.send(self, "_fullScreenStyle", [])]);return $2;}]);
    return $1;
},
args: [],
source: "style\x0a\x09^ String streamContents: [:aStream|\x0a                                  \x09aStream\x0a                                  \x09\x09nextPutAll: self bookStyle;\x0a                                  \x09\x09nextPutAll: self zoomControlsStyle;\x0a\x09\x09\x09\x09\x09\x09nextPutAll: self fullScreenStyle\x0a          ]",
messageSends: ["streamContents:", "nextPutAll:", "bookStyle", "zoomControlsStyle", "fullScreenStyle"],
referencedClasses: ["String"]
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_toggleFullscreen",
smalltalk.method({
selector: "toggleFullscreen",
category: 'callbacks',
fn: function () {
    var self = this;
    self['@isFullscreen'] = smalltalk.send(self['@isFullscreen'], "_not", []);
    smalltalk.send(self, "_reloadWidget", []);
    return self;
},
args: [],
source: "toggleFullscreen\x0a\x09isFullscreen := isFullscreen not.\x0a\x09self reloadWidget.",
messageSends: ["not", "reloadWidget"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_widgetClass",
smalltalk.method({
selector: "widgetClass",
category: 'accessor',
fn: function () {
    var self = this;
    var $2, $1;
    if (smalltalk.assert(self['@isFullscreen'])) {
        $2 = " fullscreen bk-widget";
    } else {
        $2 = " bk-widget";
    }
    $1 = smalltalk.send(smalltalk.send(smalltalk.send(self, "_class", []), "_name", []), "__comma", [$2]);
    return $1;
},
args: [],
source: "widgetClass\x0a\x09^ self class name, (isFullscreen \x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09ifTrue: [' fullscreen bk-widget'] \x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09ifFalse: [' bk-widget'])",
messageSends: [",", "ifTrue:ifFalse:", "name", "class"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_zoomControlsStyle",
smalltalk.method({
selector: "zoomControlsStyle",
category: 'css',
fn: function () {
    var self = this;
    var $1;
    $1 = smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send("\n\t\t\t.b-zoom {\n\t\t\t  position: fixed;\n\t\t\t  top: 0px;\n\t\t\t  left: 0px;\n\t\t\t  width: 100%;\n\t\t\t  height: 100%;\n\t\t\t  display: none;\n\t\t\t  z-index: 200;\n\t\t\t}\n\n\t\t\t.b-zoom .page-desc {\n\t\t\t  margin: 0px 5px;\n\t\t\t  width: auto;\n\t\t\t  color: white;\n\t\t\t  width: 45%;\n\t\t\t  padding-right: 20px;\n\t\t\t  height: 95%;\n\t\t\t  max-width:auto;\n\t\t\t  overflow-y: auto;\n\t\t\t  display: block;\n\t\t\t  float: left;\n\t\t\t  font-size: 1.3em;\n\t\t\t}\n\n\t\t\t.b-zoom-magnify {\n\t\t\t  margin: 0px auto;\n\t\t\t}\n\n\t\t\t.b-zoom-magnify a,\n\t\t\t.b-zoom-fullscreen a {\n\t\t\t\tdisplay: block;\n\t\t\t\twidth: 48px;\n\t\t\t\theight: 48px;\n\t\t\t\tz-index: 20;\n\t\t\t\tposition: relative;\n\t\t\t\tcursor: pointer;\n\t\t\t}\n\t\t\t\n\t\t\t.b-zoom-fullscreen {float: right}\n\n\t\t\t.b-zoom-fullscreen a {\n\t\t\t\tbackground: url(", "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/expand_black.png) no-repeat;\n\t\t\t}\n\n\t\t\t.b-zoom-fullscreen a:hover {\n\t\t\t\tbackground: url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/expand_white.png) no-repeat;\n\t\t\t}\n\n\t\t\t.b-download-book a {\n\t\t\t\tfloat: right;\n\t\t\t\tdisplay: block;\n\t\t\t\twidth: 73px;\n\t\t\t\theight: 36px;\n\t\t\t\tmargin-right: 5px;\n\t\t\t\tmargin-top: 6px;\n\t\t\t\tz-index: 20;\n\t\t\t\tposition: relative;\n\t\t\t\tcursor: pointer;\n\t\t\t\tbackground: url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/download_pdf_black.png) no-repeat;\n\t\t\t}\n\n\t\t\t.b-download-book a:hover {\n\t\t\t\tbackground: url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/download_pdf_white.png) no-repeat;\n\t\t\t}\n\n\t\t\t.small>.bk-widget .b-zoom-magnify a {\n\t\t\t\tbackground-image: none;\n\t\t\t}\n\n\t\t\t.b-zoom-magnify a {\n\t\t\t\tbackground: url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/magnify_black.png) no-repeat;\n\t\t\t}\n\t\t\t\n\t\t\t.b-zoom-magnify a:hover {\n\t\t\t\tbackground-image: url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/magnify_white.png);\n\t\t\t}\n\n\t\t\t.small>.bk-widget .b-zoom-magnify a:hover {\n\t\t\t\tbackground-image: none;\n\t\t\t}\n\n\t\t\t.b-zoom-magnify a {\n\t\t\t\tfloat: left;\n\t\t\t}\n\n\t\t\t.b-zoom-magnify a + a {\n\t\t\t\tfloat: right;\n\t\t\t}\n\n\t\t\t.b-zoom > div {\n\t\t\t  position: relative;\n\t\t\t  z-index: 30;\n\n\t\t\t  background-color: rgb(10,10,10);\n\t\t\t  border: 10px solid rgb(50,50,50);\n\n\t\t\t  background-color: rgba(10,10,10,0.8);\n\t\t\t  border: 10px solid rgba(50,50,50,0.8);\n\n\t\t\t  border-radius: 10px;\n\t\t\t  display:none;\n\t\t\t  padding: 1px;\n\t\t\t  height: 100%;\n\t\t\t}\n\n\t\t\t.b-zoom > div > div {\n\t\t\t  overflow: scroll;\n\t\t\t  border-radius: 10px;\n\t\t\t}\n\n\t\t\t.b-zoom .iviewer {\n\t\t\t\theight: 100%\n\t\t\t}\n\n\t\t\t.b-zoom .iviewer_with_text {\n\t\t\t  float: left;\n\t\t\t  width: 50%;\n\t\t\t  margin-right: 5px;\n\t\t\t}\n\n\t\t\t.iviewer {\n\t\t\t  backround-color: black;\n\t\t\t}\n\n\t\t\t.iviewer_cursor {\n\t\t\t  cursor: move;\n\t\t\t}\n\n\t\t\t.controls div.iviewer_common {\n\t\t\t  position: static !important;\t\t\n\t\t\t  margin: 5px auto;\n\t\t\t  background-color: transparent;\n\t\t\t}\n\n\t\t\t.controls div.iviewer_common:hover {\n\t\t\t\tbackground-color: white;\n\t\t\t}\n\n\t\t\t.iviewer_zoom_close {\n\t\t\t  background: url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/close_black28.png);\n\t\t\t}\n"]);
    return $1;
},
args: [],
source: "zoomControlsStyle\x0a\x09^ '\x0a\x09\x09\x09.b-zoom {\x0a\x09\x09\x09  position: fixed;\x0a\x09\x09\x09  top: 0px;\x0a\x09\x09\x09  left: 0px;\x0a\x09\x09\x09  width: 100%;\x0a\x09\x09\x09  height: 100%;\x0a\x09\x09\x09  display: none;\x0a\x09\x09\x09  z-index: 200;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.b-zoom .page-desc {\x0a\x09\x09\x09  margin: 0px 5px;\x0a\x09\x09\x09  width: auto;\x0a\x09\x09\x09  color: white;\x0a\x09\x09\x09  width: 45%;\x0a\x09\x09\x09  padding-right: 20px;\x0a\x09\x09\x09  height: 95%;\x0a\x09\x09\x09  max-width:auto;\x0a\x09\x09\x09  overflow-y: auto;\x0a\x09\x09\x09  display: block;\x0a\x09\x09\x09  float: left;\x0a\x09\x09\x09  font-size: 1.3em;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.b-zoom-magnify {\x0a\x09\x09\x09  margin: 0px auto;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.b-zoom-magnify a,\x0a\x09\x09\x09.b-zoom-fullscreen a {\x0a\x09\x09\x09\x09display: block;\x0a\x09\x09\x09\x09width: 48px;\x0a\x09\x09\x09\x09height: 48px;\x0a\x09\x09\x09\x09z-index: 20;\x0a\x09\x09\x09\x09position: relative;\x0a\x09\x09\x09\x09cursor: pointer;\x0a\x09\x09\x09}\x0a\x09\x09\x09\x0a\x09\x09\x09.b-zoom-fullscreen {float: right}\x0a\x0a\x09\x09\x09.b-zoom-fullscreen a {\x0a\x09\x09\x09\x09background: url(', self scriptsRoot, 'images/expand_black.png) no-repeat;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.b-zoom-fullscreen a:hover {\x0a\x09\x09\x09\x09background: url(', self scriptsRoot, 'images/expand_white.png) no-repeat;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.b-download-book a {\x0a\x09\x09\x09\x09float: right;\x0a\x09\x09\x09\x09display: block;\x0a\x09\x09\x09\x09width: 73px;\x0a\x09\x09\x09\x09height: 36px;\x0a\x09\x09\x09\x09margin-right: 5px;\x0a\x09\x09\x09\x09margin-top: 6px;\x0a\x09\x09\x09\x09z-index: 20;\x0a\x09\x09\x09\x09position: relative;\x0a\x09\x09\x09\x09cursor: pointer;\x0a\x09\x09\x09\x09background: url(', self scriptsRoot, 'images/download_pdf_black.png) no-repeat;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.b-download-book a:hover {\x0a\x09\x09\x09\x09background: url(', self scriptsRoot, 'images/download_pdf_white.png) no-repeat;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.small>.bk-widget .b-zoom-magnify a {\x0a\x09\x09\x09\x09background-image: none;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.b-zoom-magnify a {\x0a\x09\x09\x09\x09background: url(', self scriptsRoot, 'images/magnify_black.png) no-repeat;\x0a\x09\x09\x09}\x0a\x09\x09\x09\x0a\x09\x09\x09.b-zoom-magnify a:hover {\x0a\x09\x09\x09\x09background-image: url(', self scriptsRoot, 'images/magnify_white.png);\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.small>.bk-widget .b-zoom-magnify a:hover {\x0a\x09\x09\x09\x09background-image: none;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.b-zoom-magnify a {\x0a\x09\x09\x09\x09float: left;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.b-zoom-magnify a + a {\x0a\x09\x09\x09\x09float: right;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.b-zoom > div {\x0a\x09\x09\x09  position: relative;\x0a\x09\x09\x09  z-index: 30;\x0a\x0a\x09\x09\x09  background-color: rgb(10,10,10);\x0a\x09\x09\x09  border: 10px solid rgb(50,50,50);\x0a\x0a\x09\x09\x09  background-color: rgba(10,10,10,0.8);\x0a\x09\x09\x09  border: 10px solid rgba(50,50,50,0.8);\x0a\x0a\x09\x09\x09  border-radius: 10px;\x0a\x09\x09\x09  display:none;\x0a\x09\x09\x09  padding: 1px;\x0a\x09\x09\x09  height: 100%;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.b-zoom > div > div {\x0a\x09\x09\x09  overflow: scroll;\x0a\x09\x09\x09  border-radius: 10px;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.b-zoom .iviewer {\x0a\x09\x09\x09\x09height: 100%\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.b-zoom .iviewer_with_text {\x0a\x09\x09\x09  float: left;\x0a\x09\x09\x09  width: 50%;\x0a\x09\x09\x09  margin-right: 5px;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.iviewer {\x0a\x09\x09\x09  backround-color: black;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.iviewer_cursor {\x0a\x09\x09\x09  cursor: move;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.controls div.iviewer_common {\x0a\x09\x09\x09  position: static !important;\x09\x09\x0a\x09\x09\x09  margin: 5px auto;\x0a\x09\x09\x09  background-color: transparent;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.controls div.iviewer_common:hover {\x0a\x09\x09\x09\x09background-color: white;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.iviewer_zoom_close {\x0a\x09\x09\x09  background: url(', self scriptsRoot, 'images/close_black28.png);\x0a\x09\x09\x09}\x0a'",
messageSends: [",", "scriptsRoot"],
referencedClasses: []
}),
smalltalk.AbstractBookWidget);



smalltalk.addClass('BookMonoWidget', smalltalk.AbstractBookWidget, ['zoomPageAnchor', 'bookBrush'], 'AFI');
smalltalk.addMethod(
"_bookStyle",
smalltalk.method({
selector: "bookStyle",
category: 'css',
fn: function () {
    var self = this;
    return "\n    .pages img {\n    \t\tmargin: 10px auto; \n            display: block\n      }\n      \n       .BookMonoWidget  .b-navigator-thumbnail {\n      \t\twidth: 130px;\n      }\n       \n       .BookMonoWidget .b-navigator-thumbnail ul {\n       \t\tfloat: none;\n            width: 100%;\n       }\n       \n      .BookMonoWidget .b-navigator-thumbnail li {\n      \t\theight: auto;\n            float: none;\n            display: block;\n            margin: 10px auto;\n      }\n      \n      .BookMonoWidget .b-navigator-thumbnail li:hover {\n      \twidth: auto;\n      }\n      \n      .BookMonoWidget .b-navigator-thumbnail li.odd:hover>div,\n      .BookMonoWidget .b-navigator-thumbnail li.even:hover>div,\n \t  .BookMonoWidget .b-navigator-thumbnail ul li:first-child:hover>div,\n      .BookMonoWidget .b-navigator-thumbnail li:hover {\n   \t\t\tmargin: 0px auto;\n      }\n   ";
},
args: [],
source: "bookStyle\x0a\x09^ '\x0a    .pages img {\x0a    \x09\x09margin: 10px auto; \x0a            display: block\x0a      }\x0a      \x0a       .BookMonoWidget  .b-navigator-thumbnail {\x0a      \x09\x09width: 130px;\x0a      }\x0a       \x0a       .BookMonoWidget .b-navigator-thumbnail ul {\x0a       \x09\x09float: none;\x0a            width: 100%;\x0a       }\x0a       \x0a      .BookMonoWidget .b-navigator-thumbnail li {\x0a      \x09\x09height: auto;\x0a            float: none;\x0a            display: block;\x0a            margin: 10px auto;\x0a      }\x0a      \x0a      .BookMonoWidget .b-navigator-thumbnail li:hover {\x0a      \x09width: auto;\x0a      }\x0a      \x0a      .BookMonoWidget .b-navigator-thumbnail li.odd:hover>div,\x0a      .BookMonoWidget .b-navigator-thumbnail li.even:hover>div,\x0a \x09  .BookMonoWidget .b-navigator-thumbnail ul li:first-child:hover>div,\x0a      .BookMonoWidget .b-navigator-thumbnail li:hover {\x0a   \x09\x09\x09margin: 0px auto;\x0a      }\x0a   '",
messageSends: [],
referencedClasses: []
}),
smalltalk.BookMonoWidget);

smalltalk.addMethod(
"_closeZoom",
smalltalk.method({
selector: "closeZoom",
category: 'callbacks',
fn: function () {
    var self = this;
    var $1;
    smalltalk.send(smalltalk.send(".b-arrow", "_asJQuery", []), "_show", []);
    smalltalk.send(smalltalk.send(self['@pageZoomBrush'], "_asJQuery", []), "_fadeOut_do_", ["slow", function () {self['@pageZoomWidget'] = nil;self['@pageZoomWidget'];smalltalk.send(self['@pageZoomBrush'], "_empty", []);smalltalk.send(self['@zoomPageAnchor'], "_removeClass_", ["active"]);$1 = smalltalk.send(self['@zoomPageAnchor'], "_show", []);return $1;}]);
    return self;
},
args: [],
source: "closeZoom\x0a\x09'.b-arrow' asJQuery show.\x0a\x0a\x09pageZoomBrush asJQuery \x0a\x09\x09fadeOut: 'slow' do: [\x0a\x09\x09\x09pageZoomWidget := nil.\x0a\x09\x09\x09pageZoomBrush empty.\x0a                  \x0a\x09\x09\x09zoomPageAnchor \x0a\x09\x09\x09\x09removeClass: 'active';\x0a\x09\x09\x09\x09show.\x0a        ]",
messageSends: ["show", "asJQuery", "fadeOut:do:", "empty", "removeClass:"],
referencedClasses: []
}),
smalltalk.BookMonoWidget);

smalltalk.addMethod(
"_openDescriptions",
smalltalk.method({
selector: "openDescriptions",
category: 'css',
fn: function () {
    var self = this;
    return self;
},
args: [],
source: "openDescriptions\x0a\x09",
messageSends: [],
referencedClasses: []
}),
smalltalk.BookMonoWidget);

smalltalk.addMethod(
"_openPage_",
smalltalk.method({
selector: "openPage:",
category: 'callbacks',
fn: function (aPage) {
    var self = this;
    smalltalk.send(self['@bookBrush'], "_contents_", [function (html) {return smalltalk.send(smalltalk.send(html, "_img", []), "_src_", [smalltalk.send(aPage, "_thumbnailURL", [])]);}]);
    return self;
},
args: ["aPage"],
source: "openPage: aPage\x0a\x09bookBrush contents: [:html|  html img src: aPage thumbnailURL]",
messageSends: ["contents:", "src:", "thumbnailURL", "img"],
referencedClasses: []
}),
smalltalk.BookMonoWidget);

smalltalk.addMethod(
"_renderBookOn_",
smalltalk.method({
selector: "renderBookOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    smalltalk.send(self, "_loadIViewerJS", []);
    self['@bookBrush'] = smalltalk.send(smalltalk.send(html, "_div", []), "_class_", ["pages"]);
    smalltalk.send(self, "_renderBook_on_", [self['@book'], self['@bookBrush']]);
    return self;
},
args: ["html"],
source: "renderBookOn: html\x0a\x09self loadIViewerJS.\x0a\x09self renderBook:book on: (bookBrush := (html div class: 'pages'))",
messageSends: ["loadIViewerJS", "renderBook:on:", "class:", "div"],
referencedClasses: []
}),
smalltalk.BookMonoWidget);

smalltalk.addMethod(
"_renderZoomControlsOn_",
smalltalk.method({
selector: "renderZoomControlsOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    var $1, $3, $4, $2;
    $1 = smalltalk.send(html, "_div", []);
    smalltalk.send($1, "_class_", ["b-zoom-magnify"]);
    $2 = smalltalk.send($1, "_with_", [function () {self['@zoomPageAnchor'] = smalltalk.send(smalltalk.send(smalltalk.send(html, "_a", []), "_onClick_", [function () {return smalltalk.send(self, "_zoomPage", []);}]), "_asJQuery", []);self['@zoomPageAnchor'];$3 = smalltalk.send(html, "_div", []);smalltalk.send($3, "_class_", ["b-zoom"]);$4 = smalltalk.send($3, "_yourself", []);self['@pageZoomBrush'] = $4;return self['@pageZoomBrush'];}]);
    return self;
},
args: ["html"],
source: "renderZoomControlsOn: html\x0a\x09html div\x0a\x09\x09class: 'b-zoom-magnify';\x0a\x09\x09with: [ \x09zoomPageAnchor := (html a onClick: [self zoomPage]) asJQuery.\x0a                        \x0a                        pageZoomBrush := html div \x0a\x09\x09\x09\x09\x09\x09\x09class: 'b-zoom';\x0a\x09\x09\x09\x09\x09\x09\x09yourself.\x0a                ].",
messageSends: ["class:", "div", "with:", "asJQuery", "onClick:", "zoomPage", "a", "yourself"],
referencedClasses: []
}),
smalltalk.BookMonoWidget);

smalltalk.addMethod(
"_zoomPage",
smalltalk.method({
selector: "zoomPage",
category: 'callbacks',
fn: function () {
    var self = this;
    smalltalk.send(self, "_closeDescriptions", []);
    smalltalk.send(self['@zoomPageAnchor'], "_hide", []);
    smalltalk.send(smalltalk.send(".b-arrow", "_asJQuery", []), "_hide", []);
    smalltalk.send(self['@book'], "_pageAt_do_", [smalltalk.send(self, "_currentPageNo", []), function (aPage) {smalltalk.send(self['@pageZoomBrush'], "_contents_", [function (html) {return smalltalk.send(self, "_renderPage_class_on_", [aPage, "b-left", html]);}]);return smalltalk.send(smalltalk.send(self['@pageZoomBrush'], "_asJQuery", []), "_show", []);}]);
    return self;
},
args: [],
source: "zoomPage\x0a\x09self closeDescriptions.\x0a\x0a\x09 zoomPageAnchor hide.\x0a\x09'.b-arrow' asJQuery hide.\x0a\x0a\x09book pageAt: (self currentPageNo) do: [:aPage| \x0a                                           \x09\x09\x09\x09\x09\x09\x09\x09\x09\x09pageZoomBrush contents: [:html|  self renderPage: aPage class: 'b-left' on: html ].\x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09pageZoomBrush asJQuery show.\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09 \x09].",
messageSends: ["closeDescriptions", "hide", "asJQuery", "pageAt:do:", "currentPageNo", "contents:", "renderPage:class:on:", "show"],
referencedClasses: []
}),
smalltalk.BookMonoWidget);



smalltalk.addClass('BookWidget', smalltalk.AbstractBookWidget, ['width', 'leftFolioBrush', 'rightFolioBrush', 'zoomLeftPageAnchor', 'zoomRightPageAnchor'], 'AFI');
smalltalk.addMethod(
"_afterPageChange_",
smalltalk.method({
selector: "afterPageChange:",
category: 'callbacks',
fn: function (data) {
    var self = this;
    smalltalk.send(self, "_updateFolioNumbers", []);
    smalltalk.send(self, "_openDescriptions", []);
    smalltalk.send(self, "_announcePageChange_", [smalltalk.send(self, "_currentPage", [])]);
    return self;
},
args: ["data"],
source: "afterPageChange: data\x0a\x09self updateFolioNumbers.\x0a\x09self openDescriptions.\x0a\x09self announcePageChange: self currentPage.",
messageSends: ["updateFolioNumbers", "openDescriptions", "announcePageChange:", "currentPage"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_announcePageChange_",
smalltalk.method({
selector: "announcePageChange:",
category: 'announcements',
fn: function (aPage) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_announcer", []), "_announce_", [smalltalk.send(smalltalk.PageChangeAnnouncement || PageChangeAnnouncement, "_page_", [aPage])]);
    return self;
},
args: ["aPage"],
source: "announcePageChange: aPage\x0a\x09self announcer announce: (PageChangeAnnouncement page: aPage)",
messageSends: ["announce:", "announcer", "page:"],
referencedClasses: ["PageChangeAnnouncement"]
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_beforePageChange_",
smalltalk.method({
selector: "beforePageChange:",
category: 'callbacks',
fn: function (data) {
    var self = this;
    smalltalk.send(self, "_closeDescriptions", []);
    smalltalk.send(self, "_openPageNo_", [smalltalk.send(smalltalk.send(data, "_at_", ["curr"]), "__plus", [1])]);
    smalltalk.send(self, "_closeZoom", []);
    return self;
},
args: ["data"],
source: "beforePageChange:data\x0a\x09self closeDescriptions.\x0a\x09self openPageNo: (data at: 'curr') + 1.\x0a\x09self closeZoom.",
messageSends: ["closeDescriptions", "openPageNo:", "+", "at:", "closeZoom"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_bookStyle",
smalltalk.method({
selector: "bookStyle",
category: 'css',
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(unescape("%0A%09%09%09.bk-widget%20.booklet%20%7B%20margin-bottom%3A%2020px%09%7D%09%09%09%0A%0A%09%09%09.bib-num-album%20%7B%20%20padding%3A%2010px%20%7D%0A%0A%09%09%09.bk-widget%20.b-counter%20%7B%0A%09%09%09%20%20margin-bottom%3A%20-20px%3B%0A%09%09%09%20%20margin-top%3A%2020px%3B%0A%09%09%09%20%20width%3A%20140px%3B%0A%09%09%09%20%20text-align%3A%20left%3B%0A%09%09%09%20%20bottom%3A%200px%3B%0A%09%09%09%20%20background-color%3A%20transparent%3B%0A%09%09%09%20%20font-weight%3A%20bold%3B%0A%09%09%09%20%20font-size%3A%201.1em%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-counter%20+%20.b-counter%20%7B%0A%09%09%09%20%20right%3A%200px%3B%0A%09%09%09%20%20text-align%3A%20right%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.loading%20%7B%20%20text-align%3Acenter%09%7D%0A%09%09%09%0A%09%09%09.bk-widget%20.booklet%20.b-wrap-right%20%7B%0A%09%09%09%20%20background-color%3A%20transparent%3B%0A%09%09%09%20%20padding%3A%200px%3B%20%21important%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-wrap-left%20%7B%0A%09%09%09%20%20background-color%3A%20transparent%3B%0A%09%09%09%20%20padding%3A%200px%3B%20%21important%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-page-cover%20%7B%20%20background-color%3A%20transparent%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20%7B%0A%09%09%09%20%20font-size%3A%201.4em%3B%0A%09%09%09%20%20font-weight%3A%20bold%3B%0A%09%09%09%20%20width%3A%20820px%3B%0A%09%09%09%20%20margin%3A%200%20auto%3B%0A%09%09%09%20%20height%3A%2060px%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20%7B%0A%09%09%09%20%20width%3A%20600px%3B%0A%09%09%09%20%20text-align%3A%20left%3B%0A%09%09%09%20%20height%3A%2060px%3B%0A%09%09%09%20%20float%3Anone%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20.b-current%20%7B%0A%09%09%09%20%20height%3A%20auto%3B%0A%09%09%09%20%20text-align%3A%20left%3B%0A%09%09%09%20%20background%3A%20url%28"), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/menu_off.png%29%20no-repeat%2015px%20center%3B%0A%09%09%09%20%20padding-left%3A%2045px%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%3Ahover%20.b-current%20%7B%0A%09%09%09%20%20background-image%3A%20url%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("images/menu_on.png%29%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20%7B%20color%3A%20black%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20a%20%7B%20color%3A%20inherit%3B%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%3Ahover%20%7Bcolor%3A%20black%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%3Ahover%20ul%20%7B%20box-shadow%3A%202px%202px%2040px%20rgba%282%2C2%2C0%2C0.8%29%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20ul%20%7B%0A%09%09%09%20%20width%3A%20584px%3B%0A%09%09%09%20%20top%3A%20auto%3B%0A%09%09%09%20%20max-height%3A%20600px%3B%0A%09%09%09%20%20overflow-y%3A%20auto%20%21important%3B%0A%09%09%09%20%20background-color%3A%20white%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20ul%20li%20%7B%20font-size%3A%201.2em%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20ul%20li%20a%20%7B%20height%3A%20auto%3B%20%7D%0A%0A%09%09%09.bk-widget%20.b-menu%20.b-selector%20ul%20li%20a%20.b-text%20%7B%20float%3A%20none%3B%20%7D%0A%0A%09%09%09.bk-widget%20button%20%7Bfloat%3A%20left%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow%20div%20%7B%0A%09%09%09%09-webkit-transition%3A%20all%200.3s%3B%0A%09%09%09%09-moz-transition%3A%20all%200.3s%3B%0A%09%09%09%09-o-transition%3A%20all%200.3s%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow-next%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-next_black.png%29%3B%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow-next%3Ahover%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-next.png%29%3B%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow-prev%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-prev_black.png%29%3B%20%7D%0A%0A%09%09%09.bk-widget%20.booklet%20.b-arrow-prev%3Ahover%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-prev.png%29%3B%7D%0A%0A%09%09%09.bk-widget%20.b-counter%20+%20.b-counter%20%7Bfloat%3A%20right%3B%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-next%20div%20%7Bbackground-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-next_black-small.png%29%3B%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-next%3Ahover%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-next-small.png%29%3B%20%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-prev%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-prev_black-small.png%29%3B%20%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-prev%3Ahover%20div%20%7B%20background-image%3Aurl%28")]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", [unescape("booklet/images/arrow-prev-small.png%29%3B%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-prev%20%7B%20left%3A%20-25px%20%7D%0A%09%09%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow-next%20%7B%20right%3A%20-25px%20%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow%20%7B%20width%3A%2025px%20%7D%0A%0A%09%09%09.small%3E.bk-widget%20.booklet%20.b-arrow%20%20div%20%7B%20top%3A%2036%25%20%7D%0A%0A%09%09%09.clear%20%7B%20%0A%09%09%09%09clear%3A%20both%3B%0A%09%09%09%09height%3A%200px%20%21important%3B%0A%09%09%09%7D%0A%0A%09%09%09.bk-widget%20.b-wrap%20%7B%0A%09%09%09%09cursor%3A%20-moz-zoom-in%3B%0A%09%09%09%09cursor%3A%20-webkit-zoom-in%3B%0A%09%09%09%7D%0A")]);
    return self;
},
args: [],
source: "bookStyle\x0a\x09^ '\x0a\x09\x09\x09.bk-widget .booklet { margin-bottom: 20px\x09}\x09\x09\x09\x0a\x0a\x09\x09\x09.bib-num-album {  padding: 10px }\x0a\x0a\x09\x09\x09.bk-widget .b-counter {\x0a\x09\x09\x09  margin-bottom: -20px;\x0a\x09\x09\x09  margin-top: 20px;\x0a\x09\x09\x09  width: 140px;\x0a\x09\x09\x09  text-align: left;\x0a\x09\x09\x09  bottom: 0px;\x0a\x09\x09\x09  background-color: transparent;\x0a\x09\x09\x09  font-weight: bold;\x0a\x09\x09\x09  font-size: 1.1em;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.bk-widget .b-counter + .b-counter {\x0a\x09\x09\x09  right: 0px;\x0a\x09\x09\x09  text-align: right;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.bk-widget .loading {  text-align:center\x09}\x0a\x09\x09\x09\x0a\x09\x09\x09.bk-widget .booklet .b-wrap-right {\x0a\x09\x09\x09  background-color: transparent;\x0a\x09\x09\x09  padding: 0px; !important;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.bk-widget .booklet .b-wrap-left {\x0a\x09\x09\x09  background-color: transparent;\x0a\x09\x09\x09  padding: 0px; !important;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.bk-widget .booklet .b-page-cover {  background-color: transparent; }\x0a\x0a\x09\x09\x09.bk-widget .b-menu {\x0a\x09\x09\x09  font-size: 1.4em;\x0a\x09\x09\x09  font-weight: bold;\x0a\x09\x09\x09  width: 820px;\x0a\x09\x09\x09  margin: 0 auto;\x0a\x09\x09\x09  height: 60px;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.bk-widget .b-menu .b-selector {\x0a\x09\x09\x09  width: 600px;\x0a\x09\x09\x09  text-align: left;\x0a\x09\x09\x09  height: 60px;\x0a\x09\x09\x09  float:none;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.bk-widget .b-menu .b-selector .b-current {\x0a\x09\x09\x09  height: auto;\x0a\x09\x09\x09  text-align: left;\x0a\x09\x09\x09  background: url(', self scriptsRoot, 'images/menu_off.png) no-repeat 15px center;\x0a\x09\x09\x09  padding-left: 45px;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.bk-widget .b-menu .b-selector:hover .b-current {\x0a\x09\x09\x09  background-image: url(', self scriptsRoot, 'images/menu_on.png);\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.bk-widget .b-menu .b-selector { color: black; }\x0a\x0a\x09\x09\x09.bk-widget .b-menu .b-selector a { color: inherit;}\x0a\x0a\x09\x09\x09.bk-widget .b-menu .b-selector:hover {color: black; }\x0a\x0a\x09\x09\x09.bk-widget .b-menu .b-selector:hover ul { box-shadow: 2px 2px 40px rgba(2,2,0,0.8); }\x0a\x0a\x09\x09\x09.bk-widget .b-menu .b-selector ul {\x0a\x09\x09\x09  width: 584px;\x0a\x09\x09\x09  top: auto;\x0a\x09\x09\x09  max-height: 600px;\x0a\x09\x09\x09  overflow-y: auto !important;\x0a\x09\x09\x09  background-color: white;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.bk-widget .b-menu .b-selector ul li { font-size: 1.2em; }\x0a\x0a\x09\x09\x09.bk-widget .b-menu .b-selector ul li a { height: auto; }\x0a\x0a\x09\x09\x09.bk-widget .b-menu .b-selector ul li a .b-text { float: none; }\x0a\x0a\x09\x09\x09.bk-widget button {float: left}\x0a\x0a\x09\x09\x09.bk-widget .booklet .b-arrow div {\x0a\x09\x09\x09\x09-webkit-transition: all 0.3s;\x0a\x09\x09\x09\x09-moz-transition: all 0.3s;\x0a\x09\x09\x09\x09-o-transition: all 0.3s;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.bk-widget .booklet .b-arrow-next div { background-image:url(', self scriptsRoot, 'booklet/images/arrow-next_black.png);}\x0a\x0a\x09\x09\x09.bk-widget .booklet .b-arrow-next:hover div { background-image:url(', self scriptsRoot, 'booklet/images/arrow-next.png);}\x0a\x0a\x09\x09\x09.bk-widget .booklet .b-arrow-prev div { background-image:url(', self scriptsRoot, 'booklet/images/arrow-prev_black.png); }\x0a\x0a\x09\x09\x09.bk-widget .booklet .b-arrow-prev:hover div { background-image:url(', self scriptsRoot, 'booklet/images/arrow-prev.png);}\x0a\x0a\x09\x09\x09.bk-widget .b-counter + .b-counter {float: right;}\x0a\x0a\x09\x09\x09.small>.bk-widget .booklet .b-arrow-next div {background-image:url(', self scriptsRoot, 'booklet/images/arrow-next_black-small.png);}\x0a\x0a\x09\x09\x09.small>.bk-widget .booklet .b-arrow-next:hover div { background-image:url(', self scriptsRoot, 'booklet/images/arrow-next-small.png); }\x0a\x0a\x09\x09\x09.small>.bk-widget .booklet .b-arrow-prev div { background-image:url(', self scriptsRoot, 'booklet/images/arrow-prev_black-small.png); }\x0a\x0a\x09\x09\x09.small>.bk-widget .booklet .b-arrow-prev:hover div { background-image:url(', self scriptsRoot, 'booklet/images/arrow-prev-small.png);}\x0a\x0a\x09\x09\x09.small>.bk-widget .booklet .b-arrow-prev { left: -25px }\x0a\x09\x09\x0a\x09\x09\x09.small>.bk-widget .booklet .b-arrow-next { right: -25px }\x0a\x0a\x09\x09\x09.small>.bk-widget .booklet .b-arrow { width: 25px }\x0a\x0a\x09\x09\x09.small>.bk-widget .booklet .b-arrow  div { top: 36% }\x0a\x0a\x09\x09\x09.clear { \x0a\x09\x09\x09\x09clear: both;\x0a\x09\x09\x09\x09height: 0px !important;\x0a\x09\x09\x09}\x0a\x0a\x09\x09\x09.bk-widget .b-wrap {\x0a\x09\x09\x09\x09cursor: -moz-zoom-in;\x0a\x09\x09\x09\x09cursor: -webkit-zoom-in;\x0a\x09\x09\x09}\x0a'",
messageSends: [",", "scriptsRoot"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_bookletOptions",
smalltalk.method({
selector: "bookletOptions",
category: 'accessing',
fn: function () {
    var self = this;
    return function ($rec) {smalltalk.send($rec, "_at_put_", ["pageSelector", false]);smalltalk.send($rec, "_at_put_", ["chapterSelector", smalltalk.send(self['@isFullscreen'], "_not", [])]);smalltalk.send($rec, "_at_put_", ["menu", self['@menuJQuery']]);smalltalk.send($rec, "_at_put_", ["tabs", false]);smalltalk.send($rec, "_at_put_", ["keyboard", false]);smalltalk.send($rec, "_at_put_", ["arrows", true]);smalltalk.send($rec, "_at_put_", ["closed", true]);smalltalk.send($rec, "_at_put_", ["covers", true]);smalltalk.send($rec, "_at_put_", ["autoCenter", true]);smalltalk.send($rec, "_at_put_", ["pagePadding", 0]);smalltalk.send($rec, "_at_put_", ["shadows", true]);smalltalk.send($rec, "_at_put_", ["width", smalltalk.send(self, "_width", [])]);smalltalk.send($rec, "_at_put_", ["height", smalltalk.send(self, "_height", [])]);smalltalk.send($rec, "_at_put_", ["manual", false]);smalltalk.send($rec, "_at_put_", ["pageNumbers", false]);smalltalk.send($rec, "_at_put_", ["overlays", false]);smalltalk.send($rec, "_at_put_", ["hovers", false]);smalltalk.send($rec, "_at_put_", ["arrowsHide", false]);smalltalk.send($rec, "_at_put_", ["closedFrontTitle", smalltalk.send(self['@book'], "_title", [])]);smalltalk.send($rec, "_at_put_", ["closedFrontChapter", smalltalk.send(self['@book'], "_title", [])]);smalltalk.send($rec, "_at_put_", ["closedBackTitle", "Fin"]);smalltalk.send($rec, "_at_put_", ["closedBackChapter", "Fin"]);smalltalk.send($rec, "_at_put_", ["previousPageTitle", unescape("Pr%E9c%E9dent")]);smalltalk.send($rec, "_at_put_", ["nextPageTitle", "Suivant"]);smalltalk.send($rec, "_at_put_", ["before", function (data) {return smalltalk.send(self, "_beforePageChange_", [data]);}]);smalltalk.send($rec, "_at_put_", ["after", function (data) {return smalltalk.send(self, "_afterPageChange_", [data]);}]);smalltalk.send($rec, "_at_put_", ["hash", smalltalk.send(smalltalk.send(self, "_isJQueryMobile", []), "_not", [])]);return smalltalk.send($rec, "_yourself", []);}(smalltalk.send(smalltalk.HashedCollection || HashedCollection, "_new", []));
    return self;
},
args: [],
source: "bookletOptions\x0a\x09^ HashedCollection new \x0a                               at: 'pageSelector' put:false; \x0a                               at: 'chapterSelector' put: isFullscreen not; \x0a                               at: 'menu' put: menuJQuery;\x0a                               at: 'tabs' put: false;\x0a                               at: 'keyboard' put: false;\x0a                               at: 'arrows' put: true;\x0a                               at: 'closed' put: true;\x0a                               at: 'covers' put: true;\x0a                               at: 'autoCenter' put: true;\x0a                               at: 'pagePadding' put: 0;\x0a                               at: 'shadows' put: true;\x0a\x09\x09\x09       at: 'width' put: self width;\x0a\x09\x09\x09       at: 'height' put: self height;\x0a                               at: 'manual' put: false;\x0a\x09\x09\x09       at: 'pageNumbers' put: false;\x0a                               at: 'overlays' put: false;\x0a                               at: 'hovers' put: false;\x0a\x09\x09\x09       at: 'arrowsHide' put: false;\x0a\x09\x09\x09       at: 'closedFrontTitle' put: book title;\x0a\x09\x09\x09       at: 'closedFrontChapter' put: book title;\x0a\x09\x09\x09       at: 'closedBackTitle' put: 'Fin';\x0a\x09\x09\x09       at: 'closedBackChapter' put: 'Fin';\x0a\x09\x09\x09       at: 'previousPageTitle' put: 'Précédent';\x0a\x09\x09\x09       at: 'nextPageTitle' put: 'Suivant';\x0a\x09\x09\x09       at: 'before' put: [:data| self beforePageChange:data]; \x0a                               at: 'after' put: [:data|  self afterPageChange: data];\x0a\x09\x09\x09       at: 'hash' put: self isJQueryMobile not;\x0a                               yourself",
messageSends: ["at:put:", "not", "width", "height", "title", "beforePageChange:", "afterPageChange:", "isJQueryMobile", "yourself", "new"],
referencedClasses: ["HashedCollection"]
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_clear",
smalltalk.method({
selector: "clear",
category: 'show',
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(unescape(".bk-widget"), "_asJQuery", []), "_remove", []);
    return self;
},
args: [],
source: "clear\x0a\x09'.bk-widget' asJQuery remove.",
messageSends: ["remove", "asJQuery"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_closeZoom",
smalltalk.method({
selector: "closeZoom",
category: 'zoom',
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(unescape(".b-arrow"), "_asJQuery", []), "_show", []);
    smalltalk.send(smalltalk.send(self['@pageZoomBrush'], "_asJQuery", []), "_fadeOut_do_", ["slow", function () {self['@pageZoomWidget'] = nil;smalltalk.send(self['@pageZoomBrush'], "_empty", []);(function ($rec) {smalltalk.send($rec, "_removeClass_", ["active"]);return smalltalk.send($rec, "_show", []);}(self['@zoomLeftPageAnchor']));(function ($rec) {smalltalk.send($rec, "_removeClass_", ["active"]);return smalltalk.send($rec, "_show", []);}(self['@zoomRightPageAnchor']));($receiver = smalltalk.send(smalltalk.send(self, "_currentPageNo", []), "__eq", [1])).klass === smalltalk.Boolean ? $receiver ? function () {return smalltalk.send(self['@zoomLeftPageAnchor'], "_hide", []);}() : nil : smalltalk.send($receiver, "_ifTrue_", [function () {return smalltalk.send(self['@zoomLeftPageAnchor'], "_hide", []);}]);return ($receiver = ($receiver = smalltalk.send(self, "_currentPageNo", [])).klass === smalltalk.Number ? $receiver > smalltalk.send(self['@book'], "_size", []) : smalltalk.send($receiver, "__gt", [smalltalk.send(self['@book'], "_size", [])])).klass === smalltalk.Boolean ? $receiver ? function () {return smalltalk.send(self['@zoomRightPageAnchor'], "_hide", []);}() : nil : smalltalk.send($receiver, "_ifTrue_", [function () {return smalltalk.send(self['@zoomRightPageAnchor'], "_hide", []);}]);}]);
    return self;
},
args: [],
source: "closeZoom\x0a\x09'.b-arrow' asJQuery show.\x0a\x0a\x09pageZoomBrush asJQuery \x0a\x09\x09fadeOut: 'slow' do: [\x0a\x09\x09\x09pageZoomWidget := nil.\x0a\x09\x09\x09pageZoomBrush empty.\x0a\x09\x09\x09\x22pageZoomBrush asJQuery show.\x22\x0a                  \x0a\x09\x09\x09zoomLeftPageAnchor \x0a\x09\x09\x09\x09removeClass: 'active';\x0a\x09\x09\x09\x09show.\x0a\x0a\x09\x09\x09zoomRightPageAnchor \x0a\x09\x09\x09\x09removeClass: 'active';\x0a\x09\x09\x09\x09show.\x0a                  \x0a                 \x09self currentPageNo = 1 ifTrue: [zoomLeftPageAnchor hide].\x0a\x09\x09\x09self currentPageNo > book size ifTrue: [zoomRightPageAnchor hide].\x0a        ]",
messageSends: ["show", "asJQuery", "fadeOut:do:", "empty", "removeClass:", "ifTrue:", "=", "currentPageNo", "hide", ">", "size"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_closeZoomOr_",
smalltalk.method({
selector: "closeZoomOr:",
category: 'zoom',
fn: function (aBlock) {
    var self = this;
    smalltalk.send(self['@pageZoomWidget'], "_ifNil_ifNotNil_", [aBlock, function () {smalltalk.send(self, "_closeZoom", []);return smalltalk.send(self, "_openDescriptions", []);}]);
    return self;
},
args: ["aBlock"],
source: "closeZoomOr: aBlock\x0a\x09pageZoomWidget ifNil: aBlock ifNotNil: [ \x09self closeZoom. \x0a                                                \x09\x09\x09\x09\x09self openDescriptions]",
messageSends: ["ifNil:ifNotNil:", "closeZoom", "openDescriptions"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_goToPageNo_",
smalltalk.method({
selector: "goToPageNo:",
category: 'callbacks',
fn: function (pageNo) {
    var self = this;
    smalltalk.send(smalltalk.send(self['@bookContainer'], "_asJQuery", []), "_booklet_", [pageNo]);
    return self;
},
args: ["pageNo"],
source: "goToPageNo: pageNo\x0a\x09bookContainer asJQuery booklet:  (pageNo ).",
messageSends: ["booklet:", "asJQuery"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_height",
smalltalk.method({
selector: "height",
category: 'accessing',
fn: function () {
    var self = this;
    return smalltalk.send(($receiver = ($receiver = ($receiver = smalltalk.send(self['@book'], "_height", [])).klass === smalltalk.Number ? $receiver * smalltalk.send(self, "_width", []) : smalltalk.send($receiver, "__star", [smalltalk.send(self, "_width", [])])).klass === smalltalk.Number ? $receiver / smalltalk.send(self['@book'], "_width", []) : smalltalk.send($receiver, "__slash", [smalltalk.send(self['@book'], "_width", [])])).klass === smalltalk.Number ? $receiver / 2 : smalltalk.send($receiver, "__slash", [2]), "_rounded", []);
    return self;
},
args: [],
source: "height\x0a\x09^ ((book height * self width / book width) / 2) rounded",
messageSends: ["rounded", "/", "*", "height", "width"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_ifIE_ifNotIE_",
smalltalk.method({
selector: "ifIE:ifNotIE:",
category: 'testing',
fn: function (aBlock, anotherBlock) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_isIE", []), "_ifTrue_ifFalse_", [aBlock, anotherBlock]);
    return self;
},
args: ["aBlock", "anotherBlock"],
source: "ifIE: aBlock ifNotIE: anotherBlock\x0a\x09self isIE ifTrue: aBlock ifFalse: anotherBlock",
messageSends: ["ifTrue:ifFalse:", "isIE"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_isIE",
smalltalk.method({
selector: "isIE",
category: 'testing',
fn: function () {
    var self = this;
    var ie = nil;
    ie = jQuery.browser.msie;
    return smalltalk.send(ie, "_notNil", []);
    return self;
},
args: [],
source: "isIE\x0a\x09|ie|\x0a\x09ie := <jQuery.browser.msie>.\x0a\x09^ ie notNil.",
messageSends: ["notNil"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_isJQueryMobile",
smalltalk.method({
selector: "isJQueryMobile",
category: 'testing',
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(typeof window == "undefined" ? nil : window, "_jQuery", []), "_at_", ["jqmData"]), "_isNil", []), "_not", []);
    return self;
},
args: [],
source: "isJQueryMobile\x0a\x09^ (window jQuery at: 'jqmData') isNil not",
messageSends: ["not", "isNil", "at:", "jQuery"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_leftPage",
smalltalk.method({
selector: "leftPage",
category: 'accessing',
fn: function () {
    var self = this;
    return smalltalk.send(self['@book'], "_pageAt_ifAbsent_", [($receiver = self['@currentPageNo']).klass === smalltalk.Number ? $receiver - 1 : smalltalk.send($receiver, "__minus", [1]), function () {return smalltalk.send(smalltalk.Page || Page, "_new", []);}]);
    return self;
},
args: [],
source: "leftPage\x0a\x09^ book pageAt: (currentPageNo - 1) ifAbsent: [Page new].",
messageSends: ["pageAt:ifAbsent:", "-", "new"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_loadBookThenRenderOn_",
smalltalk.method({
selector: "loadBookThenRenderOn:",
category: 'loading',
fn: function (bookBrush) {
    var self = this;
    var renderBlock;
    renderBlock = function (aBook) {return smalltalk.send(self, "_renderBook_on_", [aBook, bookBrush]);};
    if (($receiver = self['@book']) == nil || $receiver == undefined) {
        smalltalk.send(smalltalk.send(self, "_loader", []), "_loadBookFromJSONOnSuccess_", [renderBlock]);
    } else {
        smalltalk.send(self['@book'], "_reset", []);
        smalltalk.send(renderBlock, "_value_", [self['@book']]);
    }
    return self;
},
args: ["bookBrush"],
source: "loadBookThenRenderOn: bookBrush\x0a\x09|renderBlock|\x0a\x09renderBlock := [:aBook| self renderBook:aBook on: bookBrush].\x0a\x09book \x0a\x09\x09ifNil: [self loader loadBookFromJSONOnSuccess: renderBlock]\x0a\x09\x09ifNotNil: [\x09book reset.\x09\x09\x09\x09\x09\x0a\x09\x09\x09\x09\x09\x09  renderBlock value: book].",
messageSends: ["renderBook:on:", "ifNil:ifNotNil:", "loadBookFromJSONOnSuccess:", "loader", "reset", "value:"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_loadBookletJSThen_",
smalltalk.method({
selector: "loadBookletJSThen:",
category: 'external libs',
fn: function (aBlock) {
    var self = this;
    var $1;
    $1 = smalltalk.send(smalltalk.send(window, "_jQuery", []), "_at_", ["booklet"]);
    smalltalk.send($1, "_ifNil_ifNotNil_", [function () {return smalltalk.send(self, "_renderScriptsOn_Then_", [smalltalk.send(smalltalk.HTMLCanvas || HTMLCanvas, "_onJQuery_", [smalltalk.send("head", "_asJQuery", [])]), aBlock]);}, aBlock]);
    return self;
},
args: ["aBlock"],
source: "loadBookletJSThen: aBlock\x0a\x09(window jQuery at: 'booklet')\x0a    \x09ifNil: [ self renderScriptsOn: (HTMLCanvas onJQuery: 'head' asJQuery) Then: aBlock]\x0a        ifNotNil: aBlock",
messageSends: ["ifNil:ifNotNil:", "renderScriptsOn:Then:", "onJQuery:", "asJQuery", "at:", "jQuery"],
referencedClasses: ["HTMLCanvas"]
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_loader",
smalltalk.method({
selector: "loader",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = self['@loader']) == nil || $receiver == undefined ? function () {return self['@loader'] = smalltalk.send(smalltalk.SouvignyLoader || SouvignyLoader, "_new", []);}() : $receiver;
    return self;
},
args: [],
source: "loader\x0a\x09^ loader ifNil: [loader := SouvignyLoader new]",
messageSends: ["ifNil:", "new"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_navigatorWidth",
smalltalk.method({
selector: "navigatorWidth",
category: 'accessing',
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.AbstractBookNavigatorWidget ||
        AbstractBookNavigatorWidget, "_width", []);
    return self;
},
args: [],
source: "navigatorWidth\x0a\x09^ AbstractBookNavigatorWidget width",
messageSends: ["width"],
referencedClasses: ["AbstractBookNavigatorWidget"]
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_openDescriptions",
smalltalk.method({
selector: "openDescriptions",
category: 'descriptions',
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(self['@pageDescriptionsBrush'], "_asJQuery", []), "_hide", []);
    smalltalk.send(self['@pageDescriptionsBrush'], "_contents_", [function (html) {smalltalk.send(smalltalk.send(smalltalk.send(html, "_div", []), "_asJQuery", []), "_html_", [smalltalk.send(smalltalk.send(self, "_leftPage", []), "_description", [])]);return smalltalk.send(smalltalk.send(smalltalk.send(html, "_div", []), "_asJQuery", []), "_html_", [smalltalk.send(smalltalk.send(self, "_rightPage", []), "_description", [])]);}]);
    smalltalk.send(smalltalk.send(self['@pageDescriptionsBrush'], "_asJQuery", []), "_fadeIn", []);
    return self;
},
args: [],
source: "openDescriptions\x0a\x09pageDescriptionsBrush asJQuery hide.\x0a\x09pageDescriptionsBrush contents: [:html| \x0a               \x09\x09(html div asJQuery) html: self leftPage description.\x0a               \x09\x09(html div asJQuery) html: self rightPage description.\x0a        ].\x0a\x09pageDescriptionsBrush asJQuery fadeIn.",
messageSends: ["hide", "asJQuery", "contents:", "html:", "div", "description", "leftPage", "rightPage", "fadeIn"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_openPage_",
smalltalk.method({
selector: "openPage:",
category: 'callbacks',
fn: function (aPage) {
    var self = this;
    smalltalk.send(self, "_goToPageNo_", [smalltalk.send(aPage, "_pageNo", [])]);
    return self;
},
args: ["aPage"],
source: "openPage: aPage\x0a\x09self goToPageNo: aPage pageNo",
messageSends: ["goToPageNo:", "pageNo"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_openPageNo_",
smalltalk.method({
selector: "openPageNo:",
category: 'callbacks',
fn: function (anInteger) {
    var self = this;
    self['@currentPageNo'] = anInteger;
    smalltalk.send(self['@book'], "_pagesNo_do_", [[($receiver = anInteger).klass === smalltalk.Number ? $receiver - 1 : smalltalk.send($receiver, "__minus", [1]), anInteger], function (aPage) {return smalltalk.send(aPage, "_renderWidth_height_", [smalltalk.send(($receiver = smalltalk.send(self, "_width", [])).klass === smalltalk.Number ? $receiver / 2 : smalltalk.send($receiver, "__slash", [2]), "_rounded", []), smalltalk.send(self, "_height", [])]);}]);
    return self;
},
args: ["anInteger"],
source: "openPageNo: anInteger\x0a\x09currentPageNo := anInteger.\x0a\x09book \x0a\x09\x09pagesNo: {anInteger - 1. anInteger} \x0a\x09\x09do: [:aPage| aPage renderWidth: (self width / 2) rounded height: self height].",
messageSends: ["pagesNo:do:", "-", "renderWidth:height:", "rounded", "/", "width", "height"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderBook_on_",
smalltalk.method({
selector: "renderBook:on:",
category: 'rendering',
fn: function (aBook, aBrush) {
    var self = this;
    smalltalk.send(self, "_renderBook_on_", [aBook, aBrush], smalltalk.AbstractBookWidget);
    smalltalk.send(self, "_loadBookletJSThen_", [function () {smalltalk.send(smalltalk.send(self['@bookContainer'], "_asJQuery", []), "_booklet_", [smalltalk.send(self, "_bookletOptions", [])]);smalltalk.send(smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_find_", [".b-wrap-left"]), "_click_", [function () {return smalltalk.send(self, "_zoomLeftPage", []);}]);return smalltalk.send(smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_find_", [".b-wrap-right, .b-page-cover"]), "_click_", [function () {return smalltalk.send(self, "_zoomRightPage", []);}]);}]);
    return self;
},
args: ["aBook", "aBrush"],
source: "renderBook: aBook on: aBrush\x0a\x09super renderBook: aBook on: aBrush.\x0a   \x09self loadBookletJSThen: [ \x0a    \x09\x09bookContainer asJQuery booklet: (self bookletOptions).\x0a\x09\x09\x09(rootBrush asJQuery find: '.b-wrap-left') click: [self zoomLeftPage].\x0a\x09\x09\x09(rootBrush asJQuery find: '.b-wrap-right, .b-page-cover') click: [self zoomRightPage].\x0a     ].",
messageSends: ["renderBook:on:", "loadBookletJSThen:", "booklet:", "bookletOptions", "asJQuery", "click:", "zoomLeftPage", "find:", "zoomRightPage"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderBookOn_",
smalltalk.method({
selector: "renderBookOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    var $2, $3, $4, $5, $1;
    self['@bookContainer'] = smalltalk.send(html, "_div", []);
    smalltalk.send(self['@bookContainer'], "_class_", ["book"]);
    $1 = smalltalk.send(self['@bookContainer'], "_with_", [function () {var bookBrush;self['@leftFolioBrush'] = smalltalk.send(smalltalk.send(html, "_div", []), "_class_", ["b-counter"]);self['@leftFolioBrush'];self['@rightFolioBrush'] = smalltalk.send(smalltalk.send(html, "_div", []), "_class_", ["b-counter"]);self['@rightFolioBrush'];$2 = smalltalk.send(html, "_div", []);smalltalk.send($2, "_class_", ["b-load"]);smalltalk.send($2, "_with_", [function () {$3 = smalltalk.send(html, "_div", []);smalltalk.send($3, "_class_", ["loading"]);$4 = smalltalk.send($3, "_with_", [function () {return smalltalk.send(smalltalk.send(html, "_img", []), "_src_", [smalltalk.send(smalltalk.send(self, "_scriptsRoot", []), "__comma", ["images/ajax-loader.gif"])]);}]);return $4;}]);$5 = smalltalk.send($2, "_yourself", []);bookBrush = $5;return smalltalk.send(self, "_loadBookThenRenderOn_", [bookBrush]);}]);
    return self;
},
args: ["html"],
source: "renderBookOn: html\x0a\x09bookContainer := html div.\x0a\x09bookContainer\x0a\x09    class: 'book';\x0a            with: [\x09|bookBrush|\x0a\x09\x09\x09\x09\x09\x09leftFolioBrush := html div class: 'b-counter'.\x0a\x09\x09\x09\x09\x09\x09rightFolioBrush := html div class: 'b-counter'.\x0a       \x09            \x09bookBrush := html div \x0a            \x09       \x09\x09class: 'b-load';\x0a             \x09      \x09\x09with: [html div\x0a                      \x09                    \x09class: 'loading';\x0a                  \x09                        \x09with: [ html img src: self scriptsRoot, 'images/ajax-loader.gif'] ];\x0a                   \x09\x09\x09yourself.\x0a                   \x0a                   \x09\x09\x09self loadBookThenRenderOn: bookBrush]",
messageSends: ["div", "class:", "with:", "src:", ",", "scriptsRoot", "img", "yourself", "loadBookThenRenderOn:"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderScriptsOn_Then_",
smalltalk.method({
selector: "renderScriptsOn:Then:",
category: 'external libs',
fn: function (html, aBlock) {
    var self = this;
    smalltalk.send(self, "_loadCSS_", ["booklet/jquery.booklet.1.2.0.css"]);
    smalltalk.send(jQuery, "_ajax_", [smalltalk.HashedCollection._fromPairs_([smalltalk.send("dataType", "__minus_gt", ["script"]), smalltalk.send("url", "__minus_gt", [smalltalk.send(smalltalk.send(self, "_scriptsRoot", []), "__comma", ["booklet/jquery.booklet.1.2.0.min.js"])]), smalltalk.send("cache", "__minus_gt", [true]), smalltalk.send("success", "__minus_gt", [aBlock])])]);
    smalltalk.send(self, "_loadIViewerJS", []);
    smalltalk.send(self, "_loadJS_", ["booklet/jquery.easing.1.3.js"]);
    return self;
},
args: ["html", "aBlock"],
source: "renderScriptsOn: html Then: aBlock\x0a\x09self loadCSS: 'booklet/jquery.booklet.1.2.0.css'.\x0a     \x0a\x09jQuery ajax: #{\x09  'dataType' -> 'script'. \x0a    \x09\x09\x09\x09\x09\x09\x09\x09'url' -> (self scriptsRoot, 'booklet/jquery.booklet.1.2.0.min.js').\x0a                                    'cache' -> true.\x0a                                    'success' -> aBlock\x0a                                }.\x0a                                \x0a\x09self loadIViewerJS.\x0a\x09self loadJS: 'booklet/jquery.easing.1.3.js'.",
messageSends: ["loadCSS:", "ajax:", "->", ",", "scriptsRoot", "loadIViewerJS", "loadJS:"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderZoomControlsOn_",
smalltalk.method({
selector: "renderZoomControlsOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    var $1, $3, $4, $2;
    $1 = smalltalk.send(html, "_div", []);
    smalltalk.send($1, "_class_", ["b-zoom-magnify"]);
    $2 = smalltalk.send($1, "_with_", [function () {self['@zoomLeftPageAnchor'] = smalltalk.send(smalltalk.send(smalltalk.send(html, "_a", []), "_onClick_", [function () {return smalltalk.send(self, "_zoomLeftPage", []);}]), "_asJQuery", []);self['@zoomLeftPageAnchor'];smalltalk.send(self['@zoomLeftPageAnchor'], "_hide", []);self['@zoomRightPageAnchor'] = smalltalk.send(smalltalk.send(smalltalk.send(html, "_a", []), "_onClick_", [function () {return smalltalk.send(self, "_zoomRightPage", []);}]), "_asJQuery", []);self['@zoomRightPageAnchor'];smalltalk.send(self['@zoomRightPageAnchor'], "_hide", []);$3 = smalltalk.send(html, "_div", []);smalltalk.send($3, "_class_", ["b-zoom"]);$4 = smalltalk.send($3, "_yourself", []);self['@pageZoomBrush'] = $4;return self['@pageZoomBrush'];}]);
    return self;
},
args: ["html"],
source: "renderZoomControlsOn: html\x0a\x09html div\x0a\x09\x09class: 'b-zoom-magnify';\x0a\x09\x09with: [ \x09zoomLeftPageAnchor := (html a onClick: [self zoomLeftPage]) asJQuery.\x0a                       \x09\x09zoomLeftPageAnchor hide.\x0a                       \x0a                         \x09zoomRightPageAnchor := (html a onClick: [self zoomRightPage]) asJQuery.\x0a                       \x09\x09zoomRightPageAnchor hide.\x0a                                pageZoomBrush := html div \x0a\x09\x09\x09\x09\x09\x09class: 'b-zoom';\x0a\x09\x09\x09\x09\x09\x09yourself.\x0a                ].",
messageSends: ["class:", "div", "with:", "asJQuery", "onClick:", "zoomLeftPage", "a", "hide", "zoomRightPage", "yourself"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_reset",
smalltalk.method({
selector: "reset",
category: 'show',
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_clear", []), "_show", []);
    return self;
},
args: [],
source: "reset\x0a\x09self clear show.",
messageSends: ["show", "clear"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_rightPage",
smalltalk.method({
selector: "rightPage",
category: 'accessing',
fn: function () {
    var self = this;
    return smalltalk.send(self['@book'], "_pageAt_ifAbsent_", [self['@currentPageNo'], function () {return smalltalk.send(smalltalk.Page || Page, "_new", []);}]);
    return self;
},
args: [],
source: "rightPage\x0a\x09^ book pageAt: currentPageNo ifAbsent: [Page new].",
messageSends: ["pageAt:ifAbsent:", "new"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_show",
smalltalk.method({
selector: "show",
category: 'show',
fn: function () {
    var self = this;
    smalltalk.send(self, "_appendToJQuery_", [smalltalk.send(unescape(".bib-num-album"), "_asJQuery", [])]);
    return self;
},
args: [],
source: "show\x0a\x09self appendToJQuery: '.bib-num-album' asJQuery",
messageSends: ["appendToJQuery:", "asJQuery"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_updateFolioNumbers",
smalltalk.method({
selector: "updateFolioNumbers",
category: 'descriptions',
fn: function () {
    var self = this;
    smalltalk.send(self['@leftFolioBrush'], "_contents_", [smalltalk.send(smalltalk.send(self, "_leftPage", []), "_foliono", [])]);
    smalltalk.send(self['@rightFolioBrush'], "_contents_", [smalltalk.send(smalltalk.send(self, "_rightPage", []), "_foliono", [])]);
    return self;
},
args: [],
source: "updateFolioNumbers\x0a\x09leftFolioBrush contents: self leftPage foliono.\x0a\x09rightFolioBrush contents: self rightPage foliono.",
messageSends: ["contents:", "foliono", "leftPage", "rightPage"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_width",
smalltalk.method({
selector: "width",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = ($receiver = self['@isFullscreen']).klass === smalltalk.Boolean ? $receiver ? function () {return smalltalk.send(($receiver = smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_width", [])).klass === smalltalk.Number ? $receiver - 2 * smalltalk.send(self, "_navigatorWidth", []) : smalltalk.send($receiver, "__minus", [2 * smalltalk.send(self, "_navigatorWidth", [])]), "_min_", [900]);}() : function () {return smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_width", []);}() : smalltalk.send($receiver, "_ifTrue_ifFalse_", [function () {return smalltalk.send(($receiver = smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_width", [])).klass === smalltalk.Number ? $receiver - 2 * smalltalk.send(self, "_navigatorWidth", []) : smalltalk.send($receiver, "__minus", [2 * smalltalk.send(self, "_navigatorWidth", [])]), "_min_", [900]);}, function () {return smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_width", []);}])).klass === smalltalk.Number ? $receiver - 2 * smalltalk.send(self, "_zoomControlWidth", []) : smalltalk.send($receiver, "__minus", [2 * smalltalk.send(self, "_zoomControlWidth", [])]);
    return self;
},
args: [],
source: "width\x0a\x09^ (isFullscreen \x0a\x09\x09\x09ifTrue: [('body' asJQuery width - (2 * self navigatorWidth)) min: 900] \x0a\x09\x09\x09ifFalse: [rootBrush asJQuery width])  - (2 * self zoomControlWidth)",
messageSends: ["-", "ifTrue:ifFalse:", "min:", "width", "asJQuery", "*", "navigatorWidth", "zoomControlWidth"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_zoomControlWidth",
smalltalk.method({
selector: "zoomControlWidth",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = smalltalk.send(self, "_isContainerSmall", [])).klass === smalltalk.Boolean ? $receiver ? function () {return 30;}() : function () {return 85;}() : smalltalk.send($receiver, "_ifTrue_ifFalse_", [function () {return 30;}, function () {return 85;}]);
    return self;
},
args: [],
source: "zoomControlWidth\x0a\x09^  self isContainerSmall ifTrue: [30] ifFalse: [85].",
messageSends: ["ifTrue:ifFalse:", "isContainerSmall"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_zoomLeftPage",
smalltalk.method({
selector: "zoomLeftPage",
category: 'zoom',
fn: function () {
    var self = this;
    smalltalk.send(self, "_closeZoomOr_", [function () {smalltalk.send(self, "_zoomPageNo_withClass_", [($receiver = self['@currentPageNo']).klass === smalltalk.Number ? $receiver - 1 : smalltalk.send($receiver, "__minus", [1]), unescape("b-left")]);return smalltalk.send(self['@zoomLeftPageAnchor'], "_addClass_", ["active"]);}]);
    return self;
},
args: [],
source: "zoomLeftPage\x0a\x09self closeZoomOr: [\x0a          \x09self zoomPageNo: currentPageNo - 1 withClass: 'b-left'.\x0a          \x09zoomLeftPageAnchor addClass: 'active'.\x0a        ].",
messageSends: ["closeZoomOr:", "zoomPageNo:withClass:", "-", "addClass:"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_zoomPageNo_withClass_",
smalltalk.method({
selector: "zoomPageNo:withClass:",
category: 'zoom',
fn: function (anInteger, aCssClass) {
    var self = this;
    smalltalk.send(self, "_closeDescriptions", []);
    smalltalk.send(self['@zoomLeftPageAnchor'], "_hide", []);
    smalltalk.send(self['@zoomRightPageAnchor'], "_hide", []);
    smalltalk.send(smalltalk.send(unescape(".b-arrow"), "_asJQuery", []), "_hide", []);
    smalltalk.send(self['@book'], "_pageAt_do_", [anInteger, function (aPage) {smalltalk.send(self['@pageZoomBrush'], "_contents_", [function (html) {return smalltalk.send(self, "_renderPage_class_on_", [aPage, aCssClass, html]);}]);return smalltalk.send(smalltalk.send(self['@pageZoomBrush'], "_asJQuery", []), "_show", []);}]);
    return self;
},
args: ["anInteger", "aCssClass"],
source: "zoomPageNo: anInteger withClass: aCssClass\x0a\x09self closeDescriptions.\x0a\x0a\x09zoomLeftPageAnchor hide.\x0a        zoomRightPageAnchor hide.\x0a\x09'.b-arrow' asJQuery hide.\x0a\x0a\x09book pageAt: anInteger do: [:aPage| \x0a                                           \x09\x09\x09pageZoomBrush contents: [:html|  self renderPage: aPage class: aCssClass on: html ].\x0a\x09\x09\x09\x09\x09\x09\x09\x09pageZoomBrush asJQuery show.\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09 \x09].",
messageSends: ["closeDescriptions", "hide", "asJQuery", "pageAt:do:", "contents:", "renderPage:class:on:", "show"],
referencedClasses: []
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_zoomRightPage",
smalltalk.method({
selector: "zoomRightPage",
category: 'zoom',
fn: function () {
    var self = this;
    smalltalk.send(self, "_closeZoomOr_", [function () {smalltalk.send(self, "_zoomPageNo_withClass_", [self['@currentPageNo'], unescape("b-right")]);return smalltalk.send(self['@zoomRightPageAnchor'], "_addClass_", ["active"]);}]);
    return self;
},
args: [],
source: "zoomRightPage\x0a\x09self closeZoomOr: [\x0a          \x09self zoomPageNo: currentPageNo withClass: 'b-right'.\x0a          \x09zoomRightPageAnchor addClass: 'active'.\x0a        ].",
messageSends: ["closeZoomOr:", "zoomPageNo:withClass:", "addClass:"],
referencedClasses: []
}),
smalltalk.BookWidget);


smalltalk.addMethod(
"_open",
smalltalk.method({
selector: "open",
category: 'initialize release',
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_new", []), "_show", []);
    return self;
},
args: [],
source: "open\x0a\x09^ self new show.",
messageSends: ["show", "new"],
referencedClasses: []
}),
smalltalk.BookWidget.klass);

smalltalk.addMethod(
"_reset",
smalltalk.method({
selector: "reset",
category: 'initialize release',
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_new", []), "_reset", []);
    return self;
},
args: [],
source: "reset\x0a\x09^ self new reset.",
messageSends: ["reset", "new"],
referencedClasses: []
}),
smalltalk.BookWidget.klass);


smalltalk.addClass('BibNumAlbum', smalltalk.Object, ['container', 'ajax', 'url', 'scriptsRoot', 'bookWidget'], 'AFI');
smalltalk.addMethod(
"_ajax",
smalltalk.method({
selector: "ajax",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = self['@ajax']) == nil || $receiver == undefined ? function () {return self['@ajax'] = smalltalk.send(smalltalk.Ajax || Ajax, "_url_", [smalltalk.send(self, "_url", [])]);}() : $receiver;
    return self;
},
args: [],
source: "ajax\x0a\x09^ ajax ifNil: [ajax := Ajax url: self url]",
messageSends: ["ifNil:", "url:", "url"],
referencedClasses: ["Ajax"]
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_ajax_",
smalltalk.method({
selector: "ajax:",
category: 'accessing',
fn: function (anAjax) {
    var self = this;
    self['@ajax'] = anAjax;
    return self;
},
args: ["anAjax"],
source: "ajax: anAjax\x0a\x09ajax := anAjax",
messageSends: [],
referencedClasses: []
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_container",
smalltalk.method({
selector: "container",
category: 'accessing',
fn: function () {
    var self = this;
    return self['@container'];
    return self;
},
args: [],
source: "container\x0a\x09^ container",
messageSends: [],
referencedClasses: []
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_container_",
smalltalk.method({
selector: "container:",
category: 'accessing',
fn: function (aJQuery) {
    var self = this;
    self['@container'] = aJQuery;
    return self;
},
args: ["aJQuery"],
source: "container: aJQuery\x0a\x09container := aJQuery",
messageSends: [],
referencedClasses: []
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_load",
smalltalk.method({
selector: "load",
category: 'loading',
fn: function () {
    var self = this;
    var $1, $2, $3;
    var loader;
    loader = smalltalk.send(smalltalk.BibNumLoader || BibNumLoader, "_ajax_", [smalltalk.send(self, "_ajax", [])]);
    smalltalk.send(loader, "_loadBookFromJSONOnSuccess_", [function (aBook, playerClassName) {var playerClass;$1 = smalltalk.send(smalltalk, "_at_", [playerClassName]);if (($receiver = $1) == nil || $receiver == undefined) {playerClass = smalltalk.BookWidget || BookWidget;} else {playerClass = $1;}$2 = smalltalk.send(playerClass, "_new", []);smalltalk.send($2, "_book_", [aBook]);smalltalk.send($2, "_scriptsRoot_", [smalltalk.send(self, "_scriptsRoot", [])]);$3 = smalltalk.send($2, "_appendToJQuery_", [smalltalk.send(self, "_container", [])]);self['@bookWidget'] = $3;return self['@bookWidget'];}]);
    return self;
},
args: [],
source: "load\x0a\x09|loader|\x0a\x09loader := BibNumLoader ajax: self ajax.\x0a    loader loadBookFromJSONOnSuccess: [:aBook :playerClassName| |playerClass|\x0a    \x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09playerClass := (smalltalk at: playerClassName) ifNil: [BookWidget].\x0a    \x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09bookWidget := playerClass new \x0a    \x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09book: aBook;\x0a       \x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09       \x09\x09\x09\x09\x09\x09\x09scriptsRoot: self scriptsRoot;\x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09\x09appendToJQuery: self container]",
messageSends: ["ajax:", "ajax", "loadBookFromJSONOnSuccess:", "ifNil:", "at:", "book:", "new", "scriptsRoot:", "scriptsRoot", "appendToJQuery:", "container"],
referencedClasses: ["BibNumLoader", "BookWidget"]
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_pages",
smalltalk.method({
selector: "pages",
category: 'accessing',
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(self['@bookWidget'], "_book", []), "_pages", []);
    return self;
},
args: [],
source: "pages\x0a\x09^ bookWidget book pages",
messageSends: ["pages", "book"],
referencedClasses: []
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_scriptsRoot",
smalltalk.method({
selector: "scriptsRoot",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = self['@scriptsRoot']) == nil || $receiver == undefined ? function () {return self['@scriptsRoot'] = "";}() : $receiver;
    return self;
},
args: [],
source: "scriptsRoot\x0a\x09^ scriptsRoot ifNil: [scriptsRoot := '']\x0a\x09",
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_scriptsRoot_",
smalltalk.method({
selector: "scriptsRoot:",
category: 'accessing',
fn: function (anUrl) {
    var self = this;
    self['@scriptsRoot'] = anUrl;
    return self;
},
args: ["anUrl"],
source: "scriptsRoot: anUrl\x0a\x09scriptsRoot := anUrl\x0a\x09",
messageSends: [],
referencedClasses: []
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_url",
smalltalk.method({
selector: "url",
category: 'accessing',
fn: function () {
    var self = this;
    return self['@url'];
    return self;
},
args: [],
source: "url\x0a\x09^ url",
messageSends: [],
referencedClasses: []
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_url_",
smalltalk.method({
selector: "url:",
category: 'accessing',
fn: function (aString) {
    var self = this;
    self['@url'] = aString;
    return self;
},
args: ["aString"],
source: "url: aString\x0a\x09url := aString",
messageSends: [],
referencedClasses: []
}),
smalltalk.BibNumAlbum);


smalltalk.addMethod(
"_load_in_",
smalltalk.method({
selector: "load:in:",
category: 'instance creation',
fn: function (anURLForJSON, aJQuerySelector) {
    var self = this;
    return function ($rec) {smalltalk.send($rec, "_url_", [anURLForJSON]);smalltalk.send($rec, "_container_", [smalltalk.send(aJQuerySelector, "_asJQuery", [])]);return smalltalk.send($rec, "_load", []);}(smalltalk.send(self, "_new", []));
    return self;
},
args: ["anURLForJSON", "aJQuerySelector"],
source: "load: anURLForJSON  in: aJQuerySelector\x0a\x09^  self new\x0a\x09\x09url: anURLForJSON;\x0a\x09\x09container: aJQuerySelector asJQuery; \x0a\x09\x09load.",
messageSends: ["url:", "container:", "asJQuery", "load", "new"],
referencedClasses: []
}),
smalltalk.BibNumAlbum.klass);

smalltalk.addMethod(
"_load_in_scriptsRoot_",
smalltalk.method({
selector: "load:in:scriptsRoot:",
category: 'instance creation',
fn: function (anURLForJSON, aJQuerySelector, anURL) {
    var self = this;
    return function ($rec) {smalltalk.send($rec, "_url_", [anURLForJSON]);smalltalk.send($rec, "_container_", [smalltalk.send(aJQuerySelector, "_asJQuery", [])]);smalltalk.send($rec, "_scriptsRoot_", [anURL]);return smalltalk.send($rec, "_load", []);}(smalltalk.send(self, "_new", []));
    return self;
},
args: ["anURLForJSON", "aJQuerySelector", "anURL"],
source: "load: anURLForJSON  in: aJQuerySelector scriptsRoot: anURL\x0a\x09^  self new\x0a\x09\x09url: anURLForJSON;\x0a\x09\x09container: aJQuerySelector asJQuery; \x0a\x09\x09scriptsRoot: anURL;\x0a\x09\x09load.",
messageSends: ["url:", "container:", "asJQuery", "scriptsRoot:", "load", "new"],
referencedClasses: []
}),
smalltalk.BibNumAlbum.klass);


smalltalk.addClass('BibNumLoader', smalltalk.Object, ['ajax'], 'AFI');
smalltalk.addMethod(
"_abort",
smalltalk.method({
selector: "abort",
category: 'loading',
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_ajax", []), "_abort", []);
    return self;
},
args: [],
source: "abort\x0a\x09self ajax abort",
messageSends: ["abort", "ajax"],
referencedClasses: []
}),
smalltalk.BibNumLoader);

smalltalk.addMethod(
"_ajax",
smalltalk.method({
selector: "ajax",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = self['@ajax']) == nil || $receiver == undefined ? function () {return self['@ajax'] = smalltalk.send(smalltalk.Ajax || Ajax, "_new", []);}() : $receiver;
    return self;
},
args: [],
source: "ajax\x0a\x09^ ajax ifNil: [ajax := Ajax new]",
messageSends: ["ifNil:", "new"],
referencedClasses: ["Ajax"]
}),
smalltalk.BibNumLoader);

smalltalk.addMethod(
"_ajax_",
smalltalk.method({
selector: "ajax:",
category: 'accessing',
fn: function (anAjax) {
    var self = this;
    self['@ajax'] = anAjax;
    return self;
},
args: ["anAjax"],
source: "ajax: anAjax\x0a\x09ajax := anAjax",
messageSends: [],
referencedClasses: []
}),
smalltalk.BibNumLoader);

smalltalk.addMethod(
"_buildBookFromJSon_",
smalltalk.method({
selector: "buildBookFromJSon:",
category: 'loading',
fn: function (aJSONObjectOrString) {
    var self = this;
    var book = nil;
    var album = nil;
    album = smalltalk.send(($receiver = smalltalk.send(aJSONObjectOrString, "_isString", [])).klass === smalltalk.Boolean ? $receiver ? function () {return smalltalk.send(smalltalk.send(typeof window == "undefined" ? nil : window, "_JSON", []), "_parse_", [aJSONObjectOrString]);}() : function () {return aJSONObjectOrString;}() : smalltalk.send($receiver, "_ifTrue_ifFalse_", [function () {return smalltalk.send(smalltalk.send(typeof window == "undefined" ? nil : window, "_JSON", []), "_parse_", [aJSONObjectOrString]);}, function () {return aJSONObjectOrString;}]), "_album", []);
    book = function ($rec) {smalltalk.send($rec, "_title_", [smalltalk.send(album, "_at_", ["titre"])]);smalltalk.send($rec, "_width_", [smalltalk.send(album, "_at_", ["width"])]);smalltalk.send($rec, "_height_", [smalltalk.send(album, "_at_", ["height"])]);smalltalk.send($rec, "_downloadUrl_", [smalltalk.send(album, "_at_", ["download_url"])]);return smalltalk.send($rec, "_yourself", []);}(smalltalk.send(smalltalk.Book || Book, "_new", []));
    smalltalk.send(smalltalk.send(album, "_ressources", []), "_do_", [function (aRessource) {return function ($rec) {smalltalk.send($rec, "_title_", [smalltalk.send(aRessource, "_at_", ["titre"])]);smalltalk.send($rec, "_description_", [smalltalk.send(aRessource, "_at_", ["description"])]);smalltalk.send($rec, "_thumbnailURL_", [smalltalk.send(aRessource, "_at_", ["thumbnail"])]);smalltalk.send($rec, "_fullImageURL_", [smalltalk.send(aRessource, "_at_", ["original"])]);smalltalk.send($rec, "_foliono_", [smalltalk.send(aRessource, "_at_", ["foliono"])]);smalltalk.send($rec, "_navigatorThumbnailURL_", [smalltalk.send(aRessource, "_at_", ["navigator_thumbnail"])]);return smalltalk.send($rec, "_downloadURL_", [smalltalk.send(aRessource, "_at_", ["download"])]);}(smalltalk.send(book, "_newPage", []));}]);
    return book;
    return self;
},
args: ["aJSONObjectOrString"],
source: "buildBookFromJSon: aJSONObjectOrString\x0a\x09|book album|\x0a\x09album := (aJSONObjectOrString isString \x0a\x09\x09\x09\x09\x09ifTrue: [window JSON parse: aJSONObjectOrString] \x0a\x09\x09\x09\x09\x09ifFalse: [aJSONObjectOrString]) album.\x0a\x09book := Book new\x0a\x09\x09\x09\x09title: (album at: 'titre');\x0a\x09\x09\x09\x09width: (album at: 'width');\x0a\x09\x09\x09\x09height: (album at: 'height');\x0a\x09\x09\x09\x09downloadUrl: (album at: 'download_url');\x0a\x09\x09\x09\x09yourself.\x0a\x09album ressources do: [:aRessource| \x0a        \x09\x09\x09\x09\x09book newPage\x0a                                \x09\x09\x09title: (aRessource at: 'titre');\x0a                              \x09\x09\x09\x09description: (aRessource at: 'description');\x0a\x09\x09\x09\x09\x09\x09\x09thumbnailURL: (aRessource at: 'thumbnail');\x0a                             \x09\x09\x09\x09fullImageURL: (aRessource at: 'original');\x0a\x09\x09\x09\x09\x09\x09\x09foliono: (aRessource at: 'foliono');\x0a\x09\x09\x09\x09\x09\x09\x09navigatorThumbnailURL: (aRessource at: 'navigator_thumbnail');\x0a\x09\x09\x09\x09\x09\x09\x09downloadURL: (aRessource at: 'download')].\x0a\x09^ book\x0a\x0a\x09",
messageSends: ["album", "ifTrue:ifFalse:", "isString", "parse:", "JSON", "title:", "at:", "width:", "height:", "downloadUrl:", "yourself", "new", "do:", "ressources", "description:", "thumbnailURL:", "fullImageURL:", "foliono:", "navigatorThumbnailURL:", "downloadURL:", "newPage"],
referencedClasses: ["Book"]
}),
smalltalk.BibNumLoader);

smalltalk.addMethod(
"_loadBookFromJSONOnSuccess_",
smalltalk.method({
selector: "loadBookFromJSONOnSuccess:",
category: 'loading',
fn: function (aBlock) {
    var self = this;
    var $1, $2;
    $1 = smalltalk.send(self, "_ajax", []);
    smalltalk.send($1, "_onSuccessDo_", [function (data) {var book;book = smalltalk.send(self, "_buildBookFromJSon_", [data]);return smalltalk.send(aBlock, "_value_value_", [book, smalltalk.send(smalltalk.send(smalltalk.send(data, "_at_", ["album"]), "_at_", ["player"]), "_asString", [])]);}]);
    $2 = smalltalk.send($1, "_send", []);
    return self;
},
args: ["aBlock"],
source: "loadBookFromJSONOnSuccess: aBlock\x0a\x09self ajax\x0a\x09\x09onSuccessDo: [:data| |book|\x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09book := self buildBookFromJSon: data.\x0a\x09\x09\x09\x09\x09\x09\x09\x09\x09aBlock value: book value: ((data at: 'album') at: 'player') asString];\x0a\x09\x09send.",
messageSends: ["onSuccessDo:", "buildBookFromJSon:", "value:value:", "asString", "at:", "ajax", "send"],
referencedClasses: []
}),
smalltalk.BibNumLoader);


smalltalk.addMethod(
"_ajax_",
smalltalk.method({
selector: "ajax:",
category: 'initialize',
fn: function (anAjax) {
    var self = this;
    var $1;
    $1 = smalltalk.send(smalltalk.send(self, "_new", []), "_ajax_", [anAjax]);
    return $1;
},
args: ["anAjax"],
source: "ajax: anAjax\x0a\x09^ self new ajax: anAjax",
messageSends: ["ajax:", "new"],
referencedClasses: []
}),
smalltalk.BibNumLoader.klass);


smalltalk.addClass('SouvignyLoader', smalltalk.BibNumLoader, ['pages', 'links', 'book'], 'AFI');
smalltalk.SouvignyLoader.comment="I'm a loader dedicated to the Bible de Souvigny"
smalltalk.addMethod(
"_baseURL",
smalltalk.method({
selector: "baseURL",
category: 'accessing',
fn: function () {
    var self = this;
    return unescape("souvigny/B031906101_MS_001/");
    return self;
},
args: [],
source: "baseURL\x0a\x09^ 'souvigny/B031906101_MS_001/'",
messageSends: [],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_book",
smalltalk.method({
selector: "book",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = self['@book']) == nil || $receiver == undefined ? function () {return self['@book'] = function ($rec) {smalltalk.send($rec, "_width_", [390]);smalltalk.send($rec, "_height_", [594]);return smalltalk.send($rec, "_yourself", []);}(smalltalk.send(smalltalk.send(smalltalk.send(self, "_class", []), "_bookClass", []), "_new", []));}() : $receiver;
    return self;
},
args: [],
source: "book\x0a\x09^ book ifNil: [book := self class bookClass new\x0a                      \x09\x09\x09\x09\x09width: 390;\x0a                      \x09\x09\x09\x09\x09height: 594;\x0a                      \x09\x09\x09\x09\x09yourself\x09\x09\x09\x09]",
messageSends: ["ifNil:", "width:", "height:", "yourself", "new", "bookClass", "class"],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_buildBookFromHTML_",
smalltalk.method({
selector: "buildBookFromHTML:",
category: 'page creation',
fn: function (aHTMLString) {
    var self = this;
    var anchors = nil;
    anchors = smalltalk.send(smalltalk.send(aHTMLString, "_asJQuery", []), "_find_", [unescape("li%20a%5Bhref%24%3D%22jpg%22%5D")]);
    smalltalk.send(anchors, "_each_", [function (index, element) {var fileName = nil;fileName = smalltalk.send(smalltalk.send(smalltalk.JQuery || JQuery, "_fromElement_", [element]), "_attr_", ["href"]);return function ($rec) {smalltalk.send($rec, "_fullImageURL_", [smalltalk.send(smalltalk.send(self, "_fullImagesURL", []), "__comma", [fileName])]);return smalltalk.send($rec, "_thumbnailURL_", [smalltalk.send(smalltalk.send(self, "_thumbsURL", []), "__comma", [fileName])]);}(smalltalk.send(smalltalk.send(self, "_book", []), "_newPage", []));}]);
    return self;
},
args: ["aHTMLString"],
source: "buildBookFromHTML: aHTMLString\x0a\x09|anchors|\x0a\x09anchors := (aHTMLString asJQuery find:'li a[href$=\x22jpg\x22]').\x0a\x09anchors each: [:index :element| |fileName|\x0a                       fileName := (JQuery fromElement: element) attr: 'href'.\x0a                       self book newPage\x0a                       \x09\x09\x09fullImageURL: self fullImagesURL, fileName;\x0a                       \x09\x09\x09thumbnailURL: self thumbsURL, fileName.\x0a        ].",
messageSends: ["find:", "asJQuery", "each:", "attr:", "fromElement:", "fullImageURL:", ",", "fullImagesURL", "thumbnailURL:", "thumbsURL", "newPage", "book"],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_buildBookFromJSON_",
smalltalk.method({
selector: "buildBookFromJSON:",
category: 'page creation',
fn: function (anArray) {
    var self = this;
    smalltalk.send(anArray, "_do_", [function (fileName) {return function ($rec) {smalltalk.send($rec, "_fullImageURL_", [smalltalk.send(smalltalk.send(self, "_fullImagesURL", []), "__comma", [fileName])]);return smalltalk.send($rec, "_thumbnailURL_", [smalltalk.send(smalltalk.send(self, "_thumbsURL", []), "__comma", [fileName])]);}(smalltalk.send(smalltalk.send(self, "_book", []), "_newPage", []));}]);
    return self;
},
args: ["anArray"],
source: "buildBookFromJSON: anArray\x0a\x09anArray do: [:fileName|\x0a                       self book newPage\x0a                       \x09\x09\x09fullImageURL: self fullImagesURL, fileName;\x0a                       \x09\x09\x09thumbnailURL: self thumbsURL, fileName.\x0a        ].",
messageSends: ["do:", "fullImageURL:", ",", "fullImagesURL", "thumbnailURL:", "thumbsURL", "newPage", "book"],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_fullImagesURL",
smalltalk.method({
selector: "fullImagesURL",
category: 'accessing',
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_baseURL", []), "__comma", [unescape("big/")]);
    return self;
},
args: [],
source: "fullImagesURL\x0a\x09^ self baseURL, 'big/'",
messageSends: [",", "baseURL"],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_initMetadata_",
smalltalk.method({
selector: "initMetadata:",
category: 'page creation',
fn: function (anArray) {
    var self = this;
    smalltalk.send(anArray, "_do_", [function (aJSObject) {var pageNo = nil;var page = nil;pageNo = aJSObject.pageNo;return ($receiver = pageNo) != nil && $receiver != undefined ? function () {page = smalltalk.send(smalltalk.send(self, "_book", []), "_pageAtFolio_", [pageNo]);return ($receiver = page) != nil && $receiver != undefined ? function () {return smalltalk.send(page, "_initMetadata_", [aJSObject]);}() : nil;}() : nil;}]);
    return self;
},
args: ["anArray"],
source: "initMetadata: anArray\x0a\x09anArray do: [:aJSObject| |pageNo page|\x0a                     pageNo := <aJSObject.pageNo>.\x0a                     pageNo ifNotNil: [\x0a                   \x09page := self book pageAtFolio: pageNo.\x0a                     \x09page ifNotNil: [page initMetadata: aJSObject] ].\x0a        ]",
messageSends: ["do:", "ifNotNil:", "pageAtFolio:", "book", "initMetadata:"],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_links",
smalltalk.method({
selector: "links",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = self['@links']) == nil || $receiver == undefined ? function () {return self['@links'] = smalltalk.send(smalltalk.Dictionary || Dictionary, "_new", []);}() : $receiver;
    return self;
},
args: [],
source: "links\x0a\x09^ links ifNil: [links := Dictionary new]",
messageSends: ["ifNil:", "new"],
referencedClasses: ["Dictionary"]
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_loadBookFromJSONOnSuccess_",
smalltalk.method({
selector: "loadBookFromJSONOnSuccess:",
category: 'loading',
fn: function (aBlock) {
    var self = this;
    (function ($rec) {smalltalk.send($rec, "_onSuccessDo_", [function (data) {smalltalk.send(self, "_buildBookFromJSON_", [data]);return smalltalk.send(self, "_onMetadataLoadedDo_", [function () {return smalltalk.send(aBlock, "_value_", [smalltalk.send(self, "_book", [])]);}]);}]);return smalltalk.send($rec, "_send", []);}(smalltalk.send(smalltalk.send(self, "_ajax", []), "_url_", [smalltalk.send(self, "_thumbsJSONURL", [])])));
    return self;
},
args: ["aBlock"],
source: "loadBookFromJSONOnSuccess: aBlock\x0a\x09(self ajax url: self thumbsJSONURL) \x0a\x09\x09onSuccessDo: [:data|\x0a                              \x09\x09self buildBookFromJSON: data.\x0a                              \x09\x09self onMetadataLoadedDo: [\x0a                                          aBlock value: self book]]; \x0a\x09\x09send.",
messageSends: ["onSuccessDo:", "buildBookFromJSON:", "onMetadataLoadedDo:", "value:", "book", "send", "url:", "ajax", "thumbsJSONURL"],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_loadBookOnSuccess_",
smalltalk.method({
selector: "loadBookOnSuccess:",
category: 'loading',
fn: function (aBlock) {
    var self = this;
    (function ($rec) {smalltalk.send($rec, "_onSuccessDo_", [function (data) {smalltalk.send(self, "_buildBookFromHTML_", [data]);return smalltalk.send(self, "_onMetadataLoadedDo_", [function () {return smalltalk.send(aBlock, "_value_", [smalltalk.send(self, "_book", [])]);}]);}]);return smalltalk.send($rec, "_send", []);}(smalltalk.send(smalltalk.Ajax || Ajax, "_url_", [smalltalk.send(self, "_thumbsURL", [])])));
    return self;
},
args: ["aBlock"],
source: "loadBookOnSuccess: aBlock\x0a\x09(Ajax url: self thumbsURL) \x0a\x09\x09onSuccessDo: [:data|\x0a                              \x09\x09self buildBookFromHTML: data.\x0a                              \x09\x09self onMetadataLoadedDo: [\x0a                                          aBlock value: self book]]; \x0a\x09\x09send.",
messageSends: ["onSuccessDo:", "buildBookFromHTML:", "onMetadataLoadedDo:", "value:", "book", "send", "url:", "thumbsURL"],
referencedClasses: ["Ajax"]
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_metadataURL",
smalltalk.method({
selector: "metadataURL",
category: 'accessing',
fn: function () {
    var self = this;
    return unescape("souvigny/souvigny.json");
    return self;
},
args: [],
source: "metadataURL\x0a\x09^ 'souvigny/souvigny.json'.",
messageSends: [],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_onMetadataLoadedDo_",
smalltalk.method({
selector: "onMetadataLoadedDo:",
category: 'loading',
fn: function (aBlock) {
    var self = this;
    (function ($rec) {smalltalk.send($rec, "_onSuccessDo_", [function (data) {smalltalk.send(self, "_initMetadata_", [data]);return smalltalk.send(aBlock, "_value", []);}]);return smalltalk.send($rec, "_send", []);}(smalltalk.send(smalltalk.Ajax || Ajax, "_url_", [smalltalk.send(self, "_metadataURL", [])])));
    return self;
},
args: ["aBlock"],
source: "onMetadataLoadedDo: aBlock\x0a\x09(Ajax url: self metadataURL)\x0a\x09\x09onSuccessDo: [:data|\x0a                              \x09\x09self initMetadata: data.\x0a                              \x09\x09aBlock value];\x0a\x09\x09send",
messageSends: ["onSuccessDo:", "initMetadata:", "value", "send", "url:", "metadataURL"],
referencedClasses: ["Ajax"]
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_parsePageNo_",
smalltalk.method({
selector: "parsePageNo:",
category: 'accessing',
fn: function (aString) {
    var self = this;
    return ($receiver = smalltalk.send(aString, "_includesSubString_", ["r"])).klass === smalltalk.Boolean ? $receiver ? function () {return ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2]);}() : function () {return ($receiver = smalltalk.send(aString, "_includesSubString_", ["v"])).klass === smalltalk.Boolean ? $receiver ? function () {return ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1]);}() : function () {return aString;}() : smalltalk.send($receiver, "_ifTrue_ifFalse_", [function () {return ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1]);}, function () {return aString;}]);}() : smalltalk.send($receiver, "_ifTrue_ifFalse_", [function () {return ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2]);}, function () {return ($receiver = smalltalk.send(aString, "_includesSubString_", ["v"])).klass === smalltalk.Boolean ? $receiver ? function () {return ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1]);}() : function () {return aString;}() : smalltalk.send($receiver, "_ifTrue_ifFalse_", [function () {return ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1]);}, function () {return aString;}]);}]);
    return self;
},
args: ["aString"],
source: "parsePageNo: aString\x0a\x09^  (aString includesSubString: 'r') \x0a\x09\x09\x09ifTrue: [aString allButLast asNumber * 2]\x0a\x09\x09\x09ifFalse: [  (aString includesSubString: 'v') \x0a                                 \x09\x09ifTrue: [aString allButLast asNumber * 2 + 1]\x0a                                 \x09\x09ifFalse: [aString] ]",
messageSends: ["ifTrue:ifFalse:", "includesSubString:", "*", "asNumber", "allButLast", "+"],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_thumbsJSONURL",
smalltalk.method({
selector: "thumbsJSONURL",
category: 'accessing',
fn: function () {
    var self = this;
    return unescape("souvigny/thumbs.json");
    return self;
},
args: [],
source: "thumbsJSONURL\x0a\x09^ 'souvigny/thumbs.json'",
messageSends: [],
referencedClasses: []
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_thumbsURL",
smalltalk.method({
selector: "thumbsURL",
category: 'accessing',
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_baseURL", []), "__comma", [unescape("thumbs/")]);
    return self;
},
args: [],
source: "thumbsURL\x0a\x09^ self baseURL, 'thumbs/'",
messageSends: [",", "baseURL"],
referencedClasses: []
}),
smalltalk.SouvignyLoader);


smalltalk.addMethod(
"_bookClass",
smalltalk.method({
selector: "bookClass",
category: 'accessing',
fn: function () {
    var self = this;
    return smalltalk.SouvignyBible || SouvignyBible;
    return self;
},
args: [],
source: "bookClass\x0a\x09^SouvignyBible",
messageSends: [],
referencedClasses: ["SouvignyBible"]
}),
smalltalk.SouvignyLoader.klass);


smalltalk.addClass('Book', smalltalk.Object, ['pages', 'title', 'width', 'height', 'downloadUrl'], 'AFI');
smalltalk.addMethod(
"_addPage_",
smalltalk.method({
selector: "addPage:",
category: 'adding',
fn: function (aPage) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_pages", []), "_add_", [aPage]);
    smalltalk.send(aPage, "_book_", [self]);
    return aPage;
    return self;
},
args: ["aPage"],
source: "addPage: aPage\x0a\x09self pages add: aPage.\x0a\x09aPage book: self.\x0a\x09^ aPage",
messageSends: ["add:", "pages", "book:"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
"_downloadUrl",
smalltalk.method({
selector: "downloadUrl",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = self['@downloadUrl']) == nil || $receiver == undefined ? function () {return self['@downloadUrl'] = "";}() : $receiver;
    return self;
},
args: [],
source: "downloadUrl\x0a\x09^ downloadUrl ifNil: [downloadUrl := '']",
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
"_downloadUrl_",
smalltalk.method({
selector: "downloadUrl:",
category: 'accessing',
fn: function (anUrl) {
    var self = this;
    self['@downloadUrl'] = anUrl;
    return self;
},
args: ["anUrl"],
source: "downloadUrl: anUrl\x0a\x09downloadUrl := anUrl",
messageSends: [],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
"_height",
smalltalk.method({
selector: "height",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = self['@height']) == nil || $receiver == undefined ? function () {return self['@height'] = 400;}() : $receiver;
    return self;
},
args: [],
source: "height\x0a\x09^ height ifNil: [height := 400]",
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
"_height_",
smalltalk.method({
selector: "height:",
category: 'accessing',
fn: function (anInteger) {
    var self = this;
    self['@height'] = anInteger;
    return self;
},
args: ["anInteger"],
source: "height: anInteger\x0a\x09height := anInteger",
messageSends: [],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
"_newPage",
smalltalk.method({
selector: "newPage",
category: 'adding',
fn: function () {
    var self = this;
    return smalltalk.send(self, "_addPage_", [smalltalk.send(smalltalk.send(smalltalk.send(self, "_class", []), "_pageClass", []), "_new", [])]);
    return self;
},
args: [],
source: "newPage\x0a\x09^ self addPage: (self class pageClass new)",
messageSends: ["addPage:", "new", "pageClass", "class"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
"_pageAt_",
smalltalk.method({
selector: "pageAt:",
category: 'accessing',
fn: function (aNumber) {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_pages", []), "_at_", [aNumber]);
    return self;
},
args: ["aNumber"],
source: "pageAt: aNumber\x0a\x09^ self pages at: aNumber",
messageSends: ["at:", "pages"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
"_pageAt_do_",
smalltalk.method({
selector: "pageAt:do:",
category: 'enumerating',
fn: function (pageNo, aBlockWithArg) {
    var self = this;
    var page = nil;
    page = smalltalk.send(smalltalk.send(self, "_pages", []), "_at_ifAbsent_", [pageNo, function () {return nil;}]);
    ($receiver = page) != nil && $receiver != undefined ? function () {return smalltalk.send(aBlockWithArg, "_value_", [page]);}() : nil;
    return self;
},
args: ["pageNo", "aBlockWithArg"],
source: "pageAt: pageNo do: aBlockWithArg\x0a\x09|page|\x0a\x09page := self pages at: pageNo ifAbsent: [nil].\x0a        page ifNotNil: [aBlockWithArg value: page].",
messageSends: ["at:ifAbsent:", "pages", "ifNotNil:", "value:"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
"_pageAt_ifAbsent_",
smalltalk.method({
selector: "pageAt:ifAbsent:",
category: 'accessing',
fn: function (aNumber, aBlock) {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_pages", []), "_at_ifAbsent_", [aNumber, aBlock]);
    return self;
},
args: ["aNumber", "aBlock"],
source: "pageAt: aNumber ifAbsent: aBlock\x0a\x09^ self pages at: aNumber ifAbsent: aBlock",
messageSends: ["at:ifAbsent:", "pages"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
"_pageNo_",
smalltalk.method({
selector: "pageNo:",
category: 'accessing',
fn: function (aPage) {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_pages", []), "_indexOf_", [aPage]);
    return self;
},
args: ["aPage"],
source: "pageNo: aPage\x0a\x09^ self pages indexOf: aPage",
messageSends: ["indexOf:", "pages"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
"_pages",
smalltalk.method({
selector: "pages",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = self['@pages']) == nil || $receiver == undefined ? function () {return self['@pages'] = smalltalk.send(smalltalk.Array || Array, "_new", []);}() : $receiver;
    return self;
},
args: [],
source: "pages\x0a\x09^ pages ifNil: [pages := Array new]",
messageSends: ["ifNil:", "new"],
referencedClasses: ["BlockClosure"]
}),
smalltalk.Book);

smalltalk.addMethod(
"_pagesNo_do_",
smalltalk.method({
selector: "pagesNo:do:",
category: 'enumerating',
fn: function (anArray, aBlockWithArg) {
    var self = this;
    smalltalk.send(anArray, "_do_", [function (pageNo) {return smalltalk.send(self, "_pageAt_do_", [pageNo, aBlockWithArg]);}]);
    return self;
},
args: ["anArray", "aBlockWithArg"],
source: "pagesNo:  anArray do: aBlockWithArg\x0a\x09anArray do: [:pageNo|\x0a                 self pageAt: pageNo do: aBlockWithArg\x0a         ].",
messageSends: ["do:", "pageAt:do:"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
"_pagesNo_to_do_",
smalltalk.method({
selector: "pagesNo:to:do:",
category: 'enumerating',
fn: function (start, end, aBlockWithArg) {
    var self = this;
    smalltalk.send(start, "_to_do_", [end, function (pageNo) {return smalltalk.send(self, "_pageAt_do_", [pageNo, aBlockWithArg]);}]);
    return self;
},
args: ["start", "end", "aBlockWithArg"],
source: "pagesNo: start to: end do: aBlockWithArg\x0a\x09start to: end do: [:pageNo|\x0a                 self pageAt: pageNo do: aBlockWithArg\x0a         ].",
messageSends: ["to:do:", "pageAt:do:"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
"_pagesWithTitle",
smalltalk.method({
selector: "pagesWithTitle",
category: 'accessing',
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_pages", []), "_reject_", [function (aPage) {return smalltalk.send(smalltalk.send(aPage, "_title", []), "_isEmpty", []);}]);
    return self;
},
args: [],
source: "pagesWithTitle\x0a\x09^ self pages reject: [:aPage | aPage title isEmpty] ",
messageSends: ["reject:", "pages", "isEmpty", "title"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
"_reset",
smalltalk.method({
selector: "reset",
category: 'reset',
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_pages", []), "_do_", [function (aPage) {return smalltalk.send(aPage, "_reset", []);}]);
    return self;
},
args: [],
source: "reset\x0a\x09self pages do: [:aPage| aPage reset]",
messageSends: ["do:", "pages", "reset"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
"_size",
smalltalk.method({
selector: "size",
category: 'accessing',
fn: function () {
    var self = this;
    return smalltalk.send(self['@pages'], "_size", []);
    return self;
},
args: [],
source: "size\x0a\x09^ pages size",
messageSends: ["size"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
"_title",
smalltalk.method({
selector: "title",
category: 'accessing',
fn: function () {
    var self = this;
    return self['@title'];
    return self;
},
args: [],
source: "title\x0a\x09^ title",
messageSends: [],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
"_title_",
smalltalk.method({
selector: "title:",
category: 'accessing',
fn: function (aString) {
    var self = this;
    self['@title'] = aString;
    return self;
},
args: ["aString"],
source: "title: aString\x0a\x09title := aString",
messageSends: [],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
"_width",
smalltalk.method({
selector: "width",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = self['@width']) == nil || $receiver == undefined ? function () {return self['@width'] = 300;}() : $receiver;
    return self;
},
args: [],
source: "width\x0a\x09^ width ifNil: [width := 300]",
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Book);

smalltalk.addMethod(
"_width_",
smalltalk.method({
selector: "width:",
category: 'accessing',
fn: function (anInteger) {
    var self = this;
    self['@width'] = anInteger;
    return self;
},
args: ["anInteger"],
source: "width: anInteger\x0a\x09width := anInteger",
messageSends: [],
referencedClasses: []
}),
smalltalk.Book);


smalltalk.addMethod(
"_pageClass",
smalltalk.method({
selector: "pageClass",
category: 'accessing',
fn: function () {
    var self = this;
    return smalltalk.Page || Page;
    return self;
},
args: [],
source: "pageClass\x0a\x09^ Page",
messageSends: [],
referencedClasses: []
}),
smalltalk.Book.klass);


smalltalk.addClass('SouvignyBible', smalltalk.Book, [], 'AFI');
smalltalk.addMethod(
"_pageAtFolio_",
smalltalk.method({
selector: "pageAtFolio:",
category: 'accessing',
fn: function (aString) {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_pages", []), "_at_ifAbsent_", [smalltalk.send(self, "_parseFolioNo_", [aString]), function () {return nil;}]);
    return self;
},
args: ["aString"],
source: "pageAtFolio: aString\x0a\x09^ self pages at: (self parseFolioNo: aString) ifAbsent: [nil].",
messageSends: ["at:ifAbsent:", "pages", "parseFolioNo:"],
referencedClasses: []
}),
smalltalk.SouvignyBible);

smalltalk.addMethod(
"_parseFolioNo_",
smalltalk.method({
selector: "parseFolioNo:",
category: 'accessing',
fn: function (aString) {
    var self = this;
    return ($receiver = smalltalk.send(aString, "_includesSubString_", ["r"])).klass === smalltalk.Boolean ? $receiver ? function () {return ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 5 : smalltalk.send($receiver, "__plus", [5]);}() : function () {return ($receiver = smalltalk.send(aString, "_includesSubString_", ["v"])).klass === smalltalk.Boolean ? $receiver ? function () {return ($receiver = ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1])).klass === smalltalk.Number ? $receiver + 5 : smalltalk.send($receiver, "__plus", [5]);}() : function () {return smalltalk.send(aString, "_asNumber", []);}() : smalltalk.send($receiver, "_ifTrue_ifFalse_", [function () {return ($receiver = ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1])).klass === smalltalk.Number ? $receiver + 5 : smalltalk.send($receiver, "__plus", [5]);}, function () {return smalltalk.send(aString, "_asNumber", []);}]);}() : smalltalk.send($receiver, "_ifTrue_ifFalse_", [function () {return ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 5 : smalltalk.send($receiver, "__plus", [5]);}, function () {return ($receiver = smalltalk.send(aString, "_includesSubString_", ["v"])).klass === smalltalk.Boolean ? $receiver ? function () {return ($receiver = ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1])).klass === smalltalk.Number ? $receiver + 5 : smalltalk.send($receiver, "__plus", [5]);}() : function () {return smalltalk.send(aString, "_asNumber", []);}() : smalltalk.send($receiver, "_ifTrue_ifFalse_", [function () {return ($receiver = ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1])).klass === smalltalk.Number ? $receiver + 5 : smalltalk.send($receiver, "__plus", [5]);}, function () {return smalltalk.send(aString, "_asNumber", []);}]);}]);
    return self;
},
args: ["aString"],
source: "parseFolioNo: aString\x0a\x09\x22Folios are numbered 32r 32v as page 32 recto, page 32 verso. Excepted 3 first folios (6 pages)\x22\x0a\x09^(aString includesSubString: 'r') \x0a\x09\x09\x09ifTrue: [aString allButLast asNumber * 2 + 5]\x0a\x09\x09\x09ifFalse: [  (aString includesSubString: 'v') \x0a                                 \x09\x09ifTrue: [aString allButLast asNumber * 2 + 1 + 5]\x0a                                 \x09\x09ifFalse: [aString asNumber] ].",
messageSends: ["ifTrue:ifFalse:", "includesSubString:", "+", "*", "asNumber", "allButLast"],
referencedClasses: []
}),
smalltalk.SouvignyBible);

smalltalk.addMethod(
"_title",
smalltalk.method({
selector: "title",
category: 'accessing',
fn: function () {
    var self = this;
    return "Bible de Souvigny";
    return self;
},
args: [],
source: "title\x0a\x09^ 'Bible de Souvigny'",
messageSends: [],
referencedClasses: []
}),
smalltalk.SouvignyBible);


smalltalk.addMethod(
"_pageClass",
smalltalk.method({
selector: "pageClass",
category: 'accessing',
fn: function () {
    var self = this;
    return smalltalk.SouvignyPage || SouvignyPage;
    return self;
},
args: [],
source: "pageClass\x0a\x09^ SouvignyPage",
messageSends: [],
referencedClasses: []
}),
smalltalk.SouvignyBible.klass);


smalltalk.addClass('Cycle', smalltalk.Object, ['elements', 'counter'], 'AFI');
smalltalk.addMethod(
"_elements_",
smalltalk.method({
selector: "elements:",
category: 'accessing',
fn: function (anArray) {
    var self = this;
    self['@elements'] = anArray;
    return self;
},
args: ["anArray"],
source: "elements: anArray\x0a\x09elements := anArray",
messageSends: [],
referencedClasses: []
}),
smalltalk.Cycle);

smalltalk.addMethod(
"_initialize",
smalltalk.method({
selector: "initialize",
category: 'initialize',
fn: function () {
    var self = this;
    self['@counter'] = -1;
    return self;
},
args: [],
source: "initialize\x0a\x09counter := -1",
messageSends: [],
referencedClasses: []
}),
smalltalk.Cycle);

smalltalk.addMethod(
"_next",
smalltalk.method({
selector: "next",
category: 'accessing',
fn: function () {
    var self = this;
    self['@counter'] = ($receiver = self['@counter']).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1]);
    return smalltalk.send(self['@elements'], "_at_", [($receiver = smalltalk.send(self['@counter'], "_\\\\", [smalltalk.send(self['@elements'], "_size", [])])).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1])]);
    return self;
},
args: [],
source: "next\x0a\x09counter := counter + 1.\x0a\x09^elements at: (counter \x5c\x5c elements size) + 1.",
messageSends: ["+", "at:", "\x5c\x5c\x5c\x5c", "size"],
referencedClasses: []
}),
smalltalk.Cycle);


smalltalk.addMethod(
"_with_",
smalltalk.method({
selector: "with:",
category: 'instance creation',
fn: function (anArray) {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_new", []), "_elements_", [anArray]);
    return self;
},
args: ["anArray"],
source: "with: anArray\x0a\x09^ self new elements: anArray",
messageSends: ["elements:", "new"],
referencedClasses: []
}),
smalltalk.Cycle.klass);


smalltalk.addClass('ListFilter', smalltalk.Object, ['book', 'announcer', 'jqueryInput', 'jqueryList'], 'AFI');
smalltalk.addMethod(
"_filter_withInput_",
smalltalk.method({
selector: "filter:withInput:",
category: 'initialization',
fn: function (aJQueryList, aJQueryInput) {
    var self = this;
    self['@jqueryList'] = aJQueryList;
    self['@jqueryInput'] = aJQueryInput;
    smalltalk.send(self['@jqueryInput'], "_keyup_", [function () {return smalltalk.send(self, "_filterListWithInputString", []);}]);
    return self;
},
args: ["aJQueryList", "aJQueryInput"],
source: "filter: aJQueryList withInput: aJQueryInput\x0a\x09jqueryList := aJQueryList.\x0a\x09jqueryInput := aJQueryInput.\x0a\x09jqueryInput keyup: [self filterListWithInputString].",
messageSends: ["keyup:", "filterListWithInputString"],
referencedClasses: []
}),
smalltalk.ListFilter);

smalltalk.addMethod(
"_filterListWithInputString",
smalltalk.method({
selector: "filterListWithInputString",
category: 'callback',
fn: function () {
    var self = this;
    var searchString = nil;
    var regExp = nil;
    var matches = nil;
    var items = nil;
    searchString = smalltalk.send(self['@jqueryInput'], "_val", []);
    regExp = new RegExp(searchString, "i");
    items = smalltalk.send(self['@jqueryList'], "_find_", ["li"]);
    matches = smalltalk.send(items, "_filter_", [function (anInteger) {return regExp.test($(this).text());}]);
    smalltalk.send(items, "_hide", []);
    smalltalk.send(matches, "_show", []);
    ($receiver = smalltalk.send(searchString, "_isEmpty", [])).klass === smalltalk.Boolean ? $receiver ? function () {return smalltalk.send(self['@jqueryList'], "_removeClass_", ["filtered"]);}() : function () {return smalltalk.send(self['@jqueryList'], "_addClass_", ["filtered"]);}() : smalltalk.send($receiver, "_ifTrue_ifFalse_", [function () {return smalltalk.send(self['@jqueryList'], "_removeClass_", ["filtered"]);}, function () {return smalltalk.send(self['@jqueryList'], "_addClass_", ["filtered"]);}]);
    return self;
},
args: [],
source: "filterListWithInputString\x0a\x09|searchString regExp matches items|\x0a\x09searchString := jqueryInput val.\x0a\x09regExp := <new RegExp(searchString, 'i')>.\x0a\x09items := jqueryList find: 'li'.\x0a\x09matches := items filter: [:anInteger| <regExp.test($(this).text())>].\x0a\x09items hide.\x0a\x09matches show.\x0a\x0a\x09searchString isEmpty ifTrue: [jqueryList removeClass: 'filtered'] ifFalse: [jqueryList addClass: 'filtered'].",
messageSends: ["val", "find:", "filter:", "hide", "show", "ifTrue:ifFalse:", "isEmpty", "removeClass:", "addClass:"],
referencedClasses: []
}),
smalltalk.ListFilter);


smalltalk.addMethod(
"_filter_withInput_",
smalltalk.method({
selector: "filter:withInput:",
category: 'instance creation',
fn: function (aJQueryList, aJQueryInput) {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_new", []), "_filter_withInput_", [aJQueryList, aJQueryInput]);
    return self;
},
args: ["aJQueryList", "aJQueryInput"],
source: "filter: aJQueryList withInput: aJQueryInput\x0a\x09^ self new filter: aJQueryList withInput: aJQueryInput",
messageSends: ["filter:withInput:", "new"],
referencedClasses: []
}),
smalltalk.ListFilter.klass);


smalltalk.addClass('Page', smalltalk.Object, ['brush', 'fullImageURL', 'thumbnailURL', 'description', 'title', 'rendered', 'foliono', 'navigatorThumbnailURL', 'book', 'downloadURL'], 'AFI');
smalltalk.addMethod(
"_book",
smalltalk.method({
selector: "book",
category: 'accessing',
fn: function () {
    var self = this;
    return self['@book'];
    return self;
},
args: [],
source: "book\x0a\x09^ book",
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_book_",
smalltalk.method({
selector: "book:",
category: 'accessing',
fn: function (aBook) {
    var self = this;
    self['@book'] = aBook;
    return self;
},
args: ["aBook"],
source: "book: aBook\x0a\x09book := aBook",
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_brush",
smalltalk.method({
selector: "brush",
category: 'accessing',
fn: function () {
    var self = this;
    return self['@brush'];
    return self;
},
args: [],
source: "brush\x0a\x09^ brush",
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_brush_",
smalltalk.method({
selector: "brush:",
category: 'accessing',
fn: function (aBrush) {
    var self = this;
    self['@brush'] = aBrush;
    return self;
},
args: ["aBrush"],
source: "brush: aBrush\x0a\x09brush := aBrush",
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_description",
smalltalk.method({
selector: "description",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = self['@description']) == nil || $receiver == undefined ? function () {return self['@description'] = "";}() : $receiver;
    return self;
},
args: [],
source: "description\x0a\x09^ description ifNil: [description := '']",
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_description_",
smalltalk.method({
selector: "description:",
category: 'accessing',
fn: function (aString) {
    var self = this;
    self['@description'] = aString;
    return self;
},
args: ["aString"],
source: "description: aString\x0a\x09description := aString",
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_downloadURL",
smalltalk.method({
selector: "downloadURL",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = self['@downloadURL']) == nil || $receiver == undefined ? function () {return self['@downloadURL'] = "";}() : $receiver;
    return self;
},
args: [],
source: "downloadURL\x0a\x09^ downloadURL ifNil: [downloadURL := '']",
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_downloadURL_",
smalltalk.method({
selector: "downloadURL:",
category: 'accessing',
fn: function (aString) {
    var self = this;
    self['@downloadURL'] = aString;
    return self;
},
args: ["aString"],
source: "downloadURL: aString\x0a\x09downloadURL := aString",
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_foliono",
smalltalk.method({
selector: "foliono",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = self['@foliono']) == nil || $receiver == undefined ? function () {return self['@foliono'] = "";}() : $receiver;
    return self;
},
args: [],
source: "foliono\x0a\x09^ foliono ifNil: [foliono := '']",
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_foliono_",
smalltalk.method({
selector: "foliono:",
category: 'accessing',
fn: function (aString) {
    var self = this;
    self['@foliono'] = aString;
    return self;
},
args: ["aString"],
source: "foliono: aString\x0a\x09foliono := aString",
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_fullImageURL",
smalltalk.method({
selector: "fullImageURL",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = self['@fullImageURL']) == nil ||
        $receiver == undefined ? function () {return self['@fullImageURL'] = "";}() : $receiver;
    return self;
},
args: [],
source: "fullImageURL\x0a\x09^ fullImageURL ifNil: [fullImageURL := '']",
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_fullImageURL_",
smalltalk.method({
selector: "fullImageURL:",
category: 'accessing',
fn: function (aString) {
    var self = this;
    self['@fullImageURL'] = aString;
    return self;
},
args: ["aString"],
source: "fullImageURL: aString\x0a\x09fullImageURL := aString",
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_initMetadata_",
smalltalk.method({
selector: "initMetadata:",
category: 'accessing',
fn: function (aJSObject) {
    var self = this;
    self['@description'] = aJSObject.description;
    self['@title'] = aJSObject.book;
    return self;
},
args: ["aJSObject"],
source: "initMetadata: aJSObject\x0a\x09description := <aJSObject.description>.\x0a\x09title := <aJSObject.book>.",
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_isRendered",
smalltalk.method({
selector: "isRendered",
category: 'testing',
fn: function () {
    var self = this;
    return ($receiver = self['@rendered']) == nil || $receiver == undefined ? function () {return self['@rendered'] = false;}() : $receiver;
    return self;
},
args: [],
source: "isRendered\x0a\x09^ rendered ifNil: [rendered := false]",
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_navigatorThumbnailURL",
smalltalk.method({
selector: "navigatorThumbnailURL",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = self['@navigatorThumbnailURL']) == nil ||
        $receiver == undefined ? function () {return self['@navigatorThumbnailURL'] = "";}() : $receiver;
    return self;
},
args: [],
source: "navigatorThumbnailURL\x0a\x09^ navigatorThumbnailURL ifNil: [navigatorThumbnailURL := ''].",
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_navigatorThumbnailURL_",
smalltalk.method({
selector: "navigatorThumbnailURL:",
category: 'accessing',
fn: function (aString) {
    var self = this;
    self['@navigatorThumbnailURL'] = aString;
    return self;
},
args: ["aString"],
source: "navigatorThumbnailURL: aString\x0a\x09navigatorThumbnailURL := aString",
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_pageNo",
smalltalk.method({
selector: "pageNo",
category: 'accessing',
fn: function () {
    var self = this;
    return smalltalk.send(self['@book'], "_pageNo_", [self]);
    return self;
},
args: [],
source: "pageNo\x0a\x09^ book pageNo: self",
messageSends: ["pageNo:"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_printString",
smalltalk.method({
selector: "printString",
category: 'printing',
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.String || String, "_streamContents_", [function (aStream) {return function ($rec) {smalltalk.send($rec, "_nextPutAll_", [smalltalk.send(self, "_printString", [], smalltalk.Object)]);smalltalk.send($rec, "_nextPutAll_", [unescape("%28")]);smalltalk.send($rec, "_nextPutAll_", [smalltalk.send(self, "_title", [])]);return smalltalk.send($rec, "_nextPutAll_", [unescape("%29")]);}(aStream);}]);
    return self;
},
args: [],
source: "printString\x0a\x09^ String streamContents: [:aStream|\x0a                                  aStream\x0a                                  \x09nextPutAll: super printString;\x0a                                  \x09nextPutAll: '(';\x0a                                  \x09nextPutAll: self title;\x0a                                  \x09nextPutAll:')'.\x0a        ].",
messageSends: ["streamContents:", "nextPutAll:", "printString", "title"],
referencedClasses: ["BlockClosure"]
}),
smalltalk.Page);

smalltalk.addMethod(
"_render",
smalltalk.method({
selector: "render",
category: 'rendering',
fn: function () {
    var self = this;
    smalltalk.send(self, "_renderWidth_height_", [smalltalk.send(self, "_width", []), smalltalk.send(self, "_height", [])]);
    return self;
},
args: [],
source: "render\x0a\x09self renderWidth: self width height: self height.",
messageSends: ["renderWidth:height:", "width", "height"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_renderWidth_height_",
smalltalk.method({
selector: "renderWidth:height:",
category: 'rendering',
fn: function (width, height) {
    var self = this;
    ($receiver = smalltalk.send(self, "_isRendered", [])).klass === smalltalk.Boolean ? !$receiver ? function () {self['@rendered'] = true;return smalltalk.send(self['@brush'], "_contents_", [function (html) {return function ($rec) {smalltalk.send($rec, "_width_", [width]);smalltalk.send($rec, "_height_", [height]);return smalltalk.send($rec, "_src_", [self['@thumbnailURL']]);}(smalltalk.send(html, "_img", []));}]);}() : nil : smalltalk.send($receiver, "_ifFalse_", [function () {self['@rendered'] = true;return smalltalk.send(self['@brush'], "_contents_", [function (html) {return function ($rec) {smalltalk.send($rec, "_width_", [width]);smalltalk.send($rec, "_height_", [height]);return smalltalk.send($rec, "_src_", [self['@thumbnailURL']]);}(smalltalk.send(html, "_img", []));}]);}]);
    return self;
},
args: ["width", "height"],
source: "renderWidth: width height: height\x0a\x09self isRendered ifFalse: [\x0a          \x09rendered := true.\x0a\x09\x09brush contents: [:html| html img\x0a                                 \x09\x09\x09\x09width:  width;\x0a                                 \x09\x09\x09\x09height:  height;\x0a                                 \x09\x09\x09\x09src: thumbnailURL].\x0a          ].",
messageSends: ["ifFalse:", "isRendered", "contents:", "width:", "height:", "src:", "img"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_reset",
smalltalk.method({
selector: "reset",
category: 'reset',
fn: function () {
    var self = this;
    return self['@rendered'] = false;
    return self;
},
args: [],
source: "reset\x0a\x09^ rendered := false",
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_thumbnailURL",
smalltalk.method({
selector: "thumbnailURL",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = self['@thumbnailURL']) == nil ||
        $receiver == undefined ? function () {return self['@thumbnailURL'] = "";}() : $receiver;
    return self;
},
args: [],
source: "thumbnailURL\x0a\x09^ thumbnailURL ifNil: [thumbnailURL := ''].",
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_thumbnailURL_",
smalltalk.method({
selector: "thumbnailURL:",
category: 'accessing',
fn: function (aString) {
    var self = this;
    self['@thumbnailURL'] = aString;
    return self;
},
args: ["aString"],
source: "thumbnailURL: aString\x0a\x09thumbnailURL := aString",
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_title",
smalltalk.method({
selector: "title",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = self['@title']) == nil || $receiver == undefined ? function () {return self['@title'] = "";}() : $receiver;
    return self;
},
args: [],
source: "title\x0a\x09^ title ifNil: [title := '']",
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.Page);

smalltalk.addMethod(
"_title_",
smalltalk.method({
selector: "title:",
category: 'accessing',
fn: function (aString) {
    var self = this;
    self['@title'] = aString;
    return self;
},
args: ["aString"],
source: "title: aString\x0a\x09title := aString",
messageSends: [],
referencedClasses: []
}),
smalltalk.Page);



smalltalk.addClass('SouvignyPage', smalltalk.Page, ['book', 'icon', 'letter', 'subject'], 'AFI');
smalltalk.addMethod(
"_initMetadata_",
smalltalk.method({
selector: "initMetadata:",
category: 'accessing',
fn: function (aJSObject) {
    var self = this;
    self['@book'] = aJSObject.book;
    self['@icon'] = aJSObject.icon;
    self['@letter'] = aJSObject.letter;
    self['@subject'] = aJSObject.subject;
    self['@description'] = aJSObject.description;
    self['@title'] = smalltalk.send(smalltalk.String || String, "_streamContents_", [function (aStream) {smalltalk.send(aStream, "_nextPutAll_", [self['@book']]);smalltalk.send(self['@icon'], "_ifNotEmpty_", [function () {return smalltalk.send(aStream, "_nextPutAll_", [smalltalk.send(unescape("%20-%20"), "__comma", [self['@icon']])]);}]);return smalltalk.send(self['@subject'], "_ifNotEmpty_", [function () {return smalltalk.send(aStream, "_nextPutAll_", [smalltalk.send(unescape("%20-%20"), "__comma", [self['@subject']])]);}]);}]);
    return self;
},
args: ["aJSObject"],
source: "initMetadata: aJSObject\x0a\x09book := <aJSObject.book>.\x0a\x09icon := <aJSObject.icon>.\x0a\x09letter := <aJSObject.letter>.\x0a\x09subject := <aJSObject.subject>.\x0a\x09description := <aJSObject.description>.\x0a \x09\x0a\x09title := String streamContents: [:aStream| \x0a                                                         aStream nextPutAll: book.\x0a                                                         \x09  icon ifNotEmpty: [aStream nextPutAll: ' - ', icon].\x0a                                                         \x09  subject ifNotEmpty: [aStream nextPutAll: ' - ', subject].\x0a                                                     \x09]",
messageSends: ["streamContents:", "nextPutAll:", "ifNotEmpty:", ","],
referencedClasses: ["BlockClosure"]
}),
smalltalk.SouvignyPage);



smalltalk.addClass('PageChangeAnnouncement', smalltalk.Object, ['page'], 'AFI');
smalltalk.addMethod(
"_page",
smalltalk.method({
selector: "page",
category: 'accessing',
fn: function () {
    var self = this;
    return self['@page'];
    return self;
},
args: [],
source: "page\x0a\x09^ page",
messageSends: [],
referencedClasses: []
}),
smalltalk.PageChangeAnnouncement);

smalltalk.addMethod(
"_page_",
smalltalk.method({
selector: "page:",
category: 'accessing',
fn: function (aPage) {
    var self = this;
    self['@page'] = aPage;
    return self;
},
args: ["aPage"],
source: "page: aPage\x0a\x09page := aPage",
messageSends: [],
referencedClasses: []
}),
smalltalk.PageChangeAnnouncement);


smalltalk.addMethod(
"_page_",
smalltalk.method({
selector: "page:",
category: 'instance creation',
fn: function (aPage) {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_new", []), "_page_", [aPage]);
    return self;
},
args: ["aPage"],
source: "page: aPage\x0a\x09^ self new page: aPage",
messageSends: ["page:", "new"],
referencedClasses: []
}),
smalltalk.PageChangeAnnouncement.klass);


smalltalk.addClass('PageWidget', smalltalk.Widget, ['page', 'inControl', 'outControl', 'fitControl', 'statusControl', 'zeroControl', 'closeControl', 'closeBlock', 'rotateRightControl', 'rotation', 'downloadImageControl'], 'AFI');
smalltalk.PageWidget.comment="I display a full page with zoom controller and description"
smalltalk.addMethod(
"_close",
smalltalk.method({
selector: "close",
category: 'callback',
fn: function () {
    var self = this;
    smalltalk.send(self['@closeBlock'], "_value", []);
    return self;
},
args: [],
source: "close\x0a\x09closeBlock value.",
messageSends: ["value"],
referencedClasses: []
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_initIViewer_",
smalltalk.method({
selector: "initIViewer:",
category: 'callback',
fn: function (aViewer) {
    var self = this;
    smalltalk.send(self['@inControl'], "_onClick_", [function () {return aViewer.zoom_by(1);}]);
    smalltalk.send(self['@outControl'], "_onClick_", [function () {return aViewer.zoom_by(-1);}]);
    smalltalk.send(self['@fitControl'], "_onClick_", [function () {return smalltalk.send(aViewer, "_fit", []);}]);
    smalltalk.send(self['@zeroControl'], "_onClick_", [function () {return aViewer.set_zoom(100);}]);
    smalltalk.send(self['@rotateRightControl'], "_onClick_", [function () {return smalltalk.send(self, "_rotateRight", []);}]);
    smalltalk.send(self['@downloadImageControl'], "_onClick_", [function () {return smalltalk.send(typeof window == "undefined" ? nil : window, "_open_", [smalltalk.send(self['@page'], "_downloadURL", [])]);}]);
    return self;
},
args: ["aViewer"],
source: "initIViewer: aViewer\x0a\x09inControl onClick: [<aViewer.zoom_by(1)>].\x0a\x09outControl onClick: [<aViewer.zoom_by(-1)>].\x0a\x09fitControl onClick: [aViewer fit].\x0a\x09zeroControl onClick: [<aViewer.set_zoom(100)>].\x0a\x09rotateRightControl onClick: [self rotateRight].\x0a\x09downloadImageControl onClick: [window open: page downloadURL].",
messageSends: ["onClick:", "fit", "rotateRight", "open:", "downloadURL"],
referencedClasses: []
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_onCloseDo_",
smalltalk.method({
selector: "onCloseDo:",
category: 'accessing',
fn: function (aBlock) {
    var self = this;
    self['@closeBlock'] = aBlock;
    return self;
},
args: ["aBlock"],
source: "onCloseDo: aBlock\x0a\x09closeBlock := aBlock",
messageSends: [],
referencedClasses: []
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_page_",
smalltalk.method({
selector: "page:",
category: 'accessing',
fn: function (aPage) {
    var self = this;
    self['@page'] = aPage;
    return self;
},
args: ["aPage"],
source: "page: aPage\x0a\x09page := aPage.",
messageSends: [],
referencedClasses: []
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_renderControlsOn_",
smalltalk.method({
selector: "renderControlsOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    var addControl = nil;
    (function ($rec) {smalltalk.send($rec, "_class_", ["controls"]);return smalltalk.send($rec, "_with_", [function () {addControl = function (name) {return function ($rec) {smalltalk.send($rec, "_class_", [smalltalk.send(smalltalk.send("iviewer_zoom_", "__comma", [name]), "__comma", [" iviewer_common iviewer_button"])]);return smalltalk.send($rec, "_yourself", []);}(smalltalk.send(html, "_div", []));};self['@closeControl'] = smalltalk.send(addControl, "_value_", ["close"]);smalltalk.send(self['@closeControl'], "_onClick_", [function () {return smalltalk.send(self, "_close", []);}]);self['@inControl'] = smalltalk.send(addControl, "_value_", ["in"]);self['@outControl'] = smalltalk.send(addControl, "_value_", ["out"]);self['@zeroControl'] = smalltalk.send(addControl, "_value_", ["zero"]);self['@fitControl'] = smalltalk.send(addControl, "_value_", ["fit"]);self['@statusControl'] = smalltalk.send(addControl, "_value_", ["status"]);self['@rotateRightControl'] = smalltalk.send(addControl, "_value_", ["rotate_right"]);return self['@downloadImageControl'] = smalltalk.send(addControl, "_value_", ["download_image"]);}]);}(smalltalk.send(html, "_div", [])));
    return self;
},
args: ["html"],
source: "renderControlsOn: html\x0a\x09|addControl|\x0a\x09html div \x0a\x09\x09class: 'controls';\x0a\x09\x09with: [\x0a          \x09\x09addControl :=  [:name| html div \x0a                                \x09\x09\x09class: 'iviewer_zoom_', name, ' iviewer_common iviewer_button';\x0a                               \x09\x09\x09\x09yourself].\x0a                  \x09closeControl := addControl value: 'close'.\x0a                  \x09closeControl onClick: [self close].\x0a               \x09\x09inControl := addControl value: 'in'.\x0a          \x09\x09outControl := addControl value: 'out'.\x0a          \x09\x09zeroControl := addControl value: 'zero'.\x0a          \x09\x09fitControl := addControl value: 'fit'.\x0a          \x09\x09statusControl := addControl value: 'status'.\x0a          \x09\x09rotateRightControl := addControl value: 'rotate_right'.\x0a          \x09\x09downloadImageControl := addControl value: 'download_image'.\x0a        ].",
messageSends: ["class:", "with:", ",", "yourself", "div", "value:", "onClick:", "close"],
referencedClasses: []
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_renderOn_",
smalltalk.method({
selector: "renderOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    var iViewer = nil;
    smalltalk.send(html, "_style_", [smalltalk.send(self, "_style", [])]);
    smalltalk.send(self, "_renderControlsOn_", [html]);
    iViewer = function ($rec) {smalltalk.send($rec, "_class_", ["iviewer"]);return smalltalk.send($rec, "_asJQuery", []);}(smalltalk.send(html, "_div", []));
    smalltalk.send(smalltalk.send(self['@page'], "_description", []), "_ifNotEmpty_", [function () {return smalltalk.send(iViewer, "_addClass_", ["iviewer_with_text"]);}]);
    smalltalk.send(iViewer, "_iviewer_", [function ($rec) {smalltalk.send($rec, "_at_put_", ["src", smalltalk.send(self['@page'], "_fullImageURL", [])]);smalltalk.send($rec, "_at_put_", ["zoom", "fit"]);smalltalk.send($rec, "_at_put_", ["zoom_min", 10]);smalltalk.send($rec, "_at_put_", ["zoom_max", 400]);smalltalk.send($rec, "_at_put_", ["ui_disabled", true]);smalltalk.send($rec, "_at_put_", ["initCallback", function (aViewer) {return smalltalk.send(self, "_initIViewer_", [aViewer]);}]);smalltalk.send($rec, "_at_put_", ["onZoom", function (aString) {return smalltalk.send(self, "_updateZoomStatus_", [aString]);}]);return smalltalk.send($rec, "_yourself", []);}(smalltalk.send(smalltalk.HashedCollection || HashedCollection, "_new", []))]);
    smalltalk.send(function ($rec) {smalltalk.send($rec, "_class_", [unescape("page-desc")]);return smalltalk.send($rec, "_asJQuery", []);}(smalltalk.send(html, "_div", [])), "_html_", [smalltalk.send(self['@page'], "_description", [])]);
    smalltalk.send(smalltalk.send(html, "_div", []), "_class_", ["clear"]);
    return self;
},
args: ["html"],
source: "renderOn: html\x0a\x09|iViewer|\x0a\x09html style: self style.\x0a\x09self renderControlsOn: html.\x0a\x09iViewer := html div \x0a\x09\x09class: 'iviewer';\x0a\x09\x09asJQuery.\x0a\x09\x0a\x09page description ifNotEmpty: [iViewer addClass: 'iviewer_with_text'].\x0a\x0a\x09iViewer iviewer: (HashedCollection new\x0a                                               \x09\x09\x09at: 'src' put: page fullImageURL;\x0a                              \x09\x09\x09\x09\x09at: 'zoom' put: 'fit';\x0a                         \x09\x09\x09\x09\x09at: 'zoom_min' put: 10;\x0a                        \x09\x09\x09\x09\x09at: 'zoom_max' put: 400;\x0a\x09                     \x09\x09\x09\x09\x09at: 'ui_disabled' put: true;\x0a        \x09              \x09\x09\x09\x09\x09at: 'initCallback' put: [:aViewer| self initIViewer: aViewer];\x0a                            \x09\x09\x09\x09\x09at: 'onZoom' put: [:aString| self updateZoomStatus: aString];\x0a                              \x09\x09\x09\x09\x09yourself).\x0a\x0a        (html div class: 'page-desc';  asJQuery) html: page description.\x0a\x09html div class: 'clear'.",
messageSends: ["style:", "style", "renderControlsOn:", "class:", "asJQuery", "div", "ifNotEmpty:", "description", "addClass:", "iviewer:", "at:put:", "fullImageURL", "initIViewer:", "updateZoomStatus:", "yourself", "new", "html:"],
referencedClasses: ["HashedCollection"]
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_rotateRight",
smalltalk.method({
selector: "rotateRight",
category: 'callback',
fn: function () {
    var self = this;
    var rotationDeg = nil;
    self['@rotation'] = ($receiver = ($receiver = self['@rotation']) == nil || $receiver == undefined ? function () {return 0;}() : $receiver).klass === smalltalk.Number ? $receiver + 90 : smalltalk.send($receiver, "__plus", [90]);
    rotationDeg = smalltalk.send(smalltalk.send(unescape("rotate%28"), "__comma", [smalltalk.send(self['@rotation'], "_asString", [])]), "__comma", [unescape("deg%29")]);
    (function ($rec) {smalltalk.send($rec, "_css_value_", [unescape("-ms-transform"), rotationDeg]);smalltalk.send($rec, "_css_value_", [unescape("-o-transform"), rotationDeg]);smalltalk.send($rec, "_css_value_", [unescape("-moz-transform"), rotationDeg]);return smalltalk.send($rec, "_css_value_", [unescape("-webkit-transform"), rotationDeg]);}(smalltalk.send(".iviewer img", "_asJQuery", [])));
    return self;
},
args: [],
source: "rotateRight\x0a\x09|rotationDeg|\x0a\x09rotation := (rotation ifNil:[0]) + 90 .\x0a\x09rotationDeg := 'rotate(',rotation asString, 'deg)'.\x0a\x09'.iviewer img' asJQuery\x0a\x09\x09css: '-ms-transform' value: rotationDeg;\x0a\x09\x09css: '-o-transform' value: rotationDeg;\x0a\x09\x09css: '-moz-transform' value: rotationDeg;\x0a\x09\x09css: '-webkit-transform' value: rotationDeg",
messageSends: ["+", "ifNil:", ",", "asString", "css:value:", "asJQuery"],
referencedClasses: []
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_style",
smalltalk.method({
selector: "style",
category: 'css',
fn: function () {
    var self = this;
    return unescape("%09.b-zoom%20.controls%20%7B%0A%09%09%09%20%20height%3A%20auto%3B%0A%09%09%09%20%20padding%3A%204px%3B%0A%09%09%09%20%20margin%3A%200%204px%3B%0A%09%09%09%20%20background-color%3A%20rgb%28200%2C200%2C200%29%3B%0A%09%09%09%20%20background-color%3A%20rgba%28200%2C200%2C200%2C0.8%29%3B%0A%09%09%09%20%20overflow%3A%20hidden%3B%0A%09%09%09%20%20float%3A%20right%3B%0A%09%09%09%20%20position%3A%20absolute%3B%0A%09%09%09%20%20*position%3A%20relative%3B%0A%09%09%09%20%20z-index%3A%201%3B%0A%09%09%09%20%20text-align%3A%20center%3B%0A%09%09%09%20%20width%3A%2042px%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20right%3A%200px%3B%0A%09%09%09%7D%0A");
    return self;
},
args: [],
source: "style\x0a\x09^ '\x09.b-zoom .controls {\x0a\x09\x09\x09  height: auto;\x0a\x09\x09\x09  padding: 4px;\x0a\x09\x09\x09  margin: 0 4px;\x0a\x09\x09\x09  background-color: rgb(200,200,200);\x0a\x09\x09\x09  background-color: rgba(200,200,200,0.8);\x0a\x09\x09\x09  overflow: hidden;\x0a\x09\x09\x09  float: right;\x0a\x09\x09\x09  position: absolute;\x0a\x09\x09\x09  *position: relative;\x0a\x09\x09\x09  z-index: 1;\x0a\x09\x09\x09  text-align: center;\x0a\x09\x09\x09  width: 42px;\x0a                          right: 0px;\x0a\x09\x09\x09}\x0a'",
messageSends: [],
referencedClasses: []
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_updateZoomStatus_",
smalltalk.method({
selector: "updateZoomStatus:",
category: 'callback',
fn: function (newZoom) {
    var self = this;
    smalltalk.send(self['@statusControl'], "_contents_", [smalltalk.send("x", "__comma", [smalltalk.send(($receiver = newZoom).klass === smalltalk.Number ? $receiver / 100 : smalltalk.send($receiver, "__slash", [100]), "_printShowingDecimalPlaces_", [1])])]);
    return self;
},
args: ["newZoom"],
source: "updateZoomStatus: newZoom\x0a\x09statusControl contents: 'x', (newZoom / 100  printShowingDecimalPlaces: 1).",
messageSends: ["contents:", ",", "printShowingDecimalPlaces:", "/"],
referencedClasses: []
}),
smalltalk.PageWidget);



