smalltalk.addPackage('AFI', {});
smalltalk.addClass('AbstractBookNavigatorWidget', smalltalk.Widget, ['book', 'announcer'], 'AFI');
smalltalk.addMethod(
"_announcePageChange_",
smalltalk.method({
selector: "announcePageChange:",
fn: function (aPage) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_announcer", []), "_announce_", [smalltalk.send(smalltalk.PageChangeAnnouncement || PageChangeAnnouncement, "_page_", [aPage])]);
    return self;
}
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_announcer",
smalltalk.method({
selector: "announcer",
fn: function () {
    var self = this;
    return ($receiver = self['@announcer']) == nil || $receiver == undefined ? function () {return self['@announcer'] = smalltalk.send(smalltalk.Announcer || Announcer, "_new", []);}() : $receiver;
    return self;
}
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_book_",
smalltalk.method({
selector: "book:",
fn: function (aBook) {
    var self = this;
    self['@book'] = aBook;
    return self;
}
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_highlightPage_",
smalltalk.method({
selector: "highlightPage:",
fn: function (aPage) {
    var self = this;
    return self;
}
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_onPageChangeDo_",
smalltalk.method({
selector: "onPageChangeDo:",
fn: function (aBlockWithArg) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_announcer", []), "_on_do_", [smalltalk.PageChangeAnnouncement || PageChangeAnnouncement, function (aPageChangeAnnouncement) {return smalltalk.send(aBlockWithArg, "_value_", [smalltalk.send(aPageChangeAnnouncement, "_page", [])]);}]);
    return self;
}
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_renderOn_",
smalltalk.method({
selector: "renderOn:",
fn: function (html) {
    var self = this;
    smalltalk.send(self, "_subclassResponsibility", []);
    return self;
}
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_style",
smalltalk.method({
selector: "style",
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(unescape("%0A%0A.b-navigator%20%7B%0A%09height%3A%20500px%3B%0A%20%09width%3A%20"), "__comma", [smalltalk.send(self, "_width", [])]), "__comma", [unescape("px%3B%0A%09overflow-x%3A%20hidden%3B%0A%09overflow-y%3A%20hidden%3B%0A%09border%3A%202px%20solid%20%23666%3B%0A%7D%0A%0A.b-navigator%3Ediv%20%7B%0A%09text-align%3A%20center%3B%0A%09border-bottom%3A%201px%20solid%20%23666%3B%0A%09background-color%3A%20%23666%3B%0A%09font-size%3A%201.1em%3B%0A%7D%0A%0A.b-navigator%3Einput%20%7B%0A%09width%3A%20100%25%3B%0A%09border%3A%201px%20solid%20%23666%3B%0A%09margin%3A%200px%3B%0A%7D%0A%0A.b-navigator%20ul%20%7B%0A%09height%3A%2090%25%3B%0A%09overflow-x%3A%20hidden%3B%0A%09overflow-y%3A%20auto%3B%0A%09margin%3A%200px%3B%0A%7D%0A")]);
    return self;
}
}),
smalltalk.AbstractBookNavigatorWidget);

smalltalk.addMethod(
"_width",
smalltalk.method({
selector: "width",
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_class", []), "_width", []);
    return self;
}
}),
smalltalk.AbstractBookNavigatorWidget);


smalltalk.addMethod(
"_width",
smalltalk.method({
selector: "width",
fn: function () {
    var self = this;
    return 160;
    return self;
}
}),
smalltalk.AbstractBookNavigatorWidget.klass);


smalltalk.addClass('BookBookmarkNavigatorWidget', smalltalk.AbstractBookNavigatorWidget, ['bookmarkList'], 'AFI');
smalltalk.addMethod(
"_highlightPage_",
smalltalk.method({
selector: "highlightPage:",
fn: function (aPage) {
    var self = this;
    var pageTitle = nil;
    var listItemIndex = nil;
    smalltalk.send(smalltalk.send(self['@bookmarkList'], "_find_", ["li"]), "_removeClass_", ["selected"]);
    pageTitle = smalltalk.send(smalltalk.send(aPage, "_title", []), "_ifEmpty_", [function () {return smalltalk.send(smalltalk.send(self['@book'], "_pageAt_ifAbsent_", [($receiver = smalltalk.send(aPage, "_pageNo", [])).klass === smalltalk.Number ? $receiver - 1 : smalltalk.send($receiver, "__minus", [1]), function () {return aPage;}]), "_title", []);}]);
    ($receiver = smalltalk.send(pageTitle, "_isEmpty", [])).klass === smalltalk.Boolean ? !$receiver ? function () {return smalltalk.send(smalltalk.send(self['@bookmarkList'], "_find_", [smalltalk.send(smalltalk.send(unescape("li%3Acontains%28%22"), "__comma", [pageTitle]), "__comma", [unescape("%22%29")])]), "_addClass_", ["selected"]);}() : nil : smalltalk.send($receiver, "_ifFalse_", [function () {return smalltalk.send(smalltalk.send(self['@bookmarkList'], "_find_", [smalltalk.send(smalltalk.send(unescape("li%3Acontains%28%22"), "__comma", [pageTitle]), "__comma", [unescape("%22%29")])]), "_addClass_", ["selected"]);}]);
    return self;
}
}),
smalltalk.BookBookmarkNavigatorWidget);

smalltalk.addMethod(
"_renderOn_",
smalltalk.method({
selector: "renderOn:",
fn: function (html) {
    var self = this;
    smalltalk.send(html, "_style_", [smalltalk.send(self, "_style", [])]);
    (function ($rec) {smalltalk.send($rec, "_class_", [unescape("b-navigator-bookmark%20b-navigator")]);return smalltalk.send($rec, "_with_", [function () {var bookmarkSearchInput = nil;smalltalk.send(html, "_div_", ["Signets"]);bookmarkSearchInput = smalltalk.send(smalltalk.send(html, "_input", []), "_asJQuery", []);self['@bookmarkList'] = smalltalk.send(smalltalk.send(html, "_ul_", [function () {return smalltalk.send(self, "_renderPagesOn_", [html]);}]), "_asJQuery", []);return smalltalk.send(smalltalk.ListFilter || ListFilter, "_filter_withInput_", [self['@bookmarkList'], bookmarkSearchInput]);}]);}(smalltalk.send(html, "_div", [])));
    return self;
}
}),
smalltalk.BookBookmarkNavigatorWidget);

smalltalk.addMethod(
"_renderPagesOn_",
smalltalk.method({
selector: "renderPagesOn:",
fn: function (html) {
    var self = this;
    smalltalk.send(smalltalk.send(self['@book'], "_pagesWithTitle", []), "_do_", [function (aPage) {return function ($rec) {smalltalk.send($rec, "_with_", [smalltalk.send(aPage, "_title", [])]);return smalltalk.send($rec, "_onClick_", [function () {return smalltalk.send(self, "_announcePageChange_", [aPage]);}]);}(smalltalk.send(html, "_li", []));}]);
    return self;
}
}),
smalltalk.BookBookmarkNavigatorWidget);

smalltalk.addMethod(
"_style",
smalltalk.method({
selector: "style",
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_style", [], smalltalk.AbstractBookNavigatorWidget), "__comma", [unescape("%0A.b-navigator-bookmark%20%7B%0A%09border-top-right-radius%3A%2010px%3B%0A%09border-bottom-right-radius%3A%2010px%3B%0A%09border-left%3A%200px%3B%0A%09margin-left%3A%200px%3B%0A%09margin-right%3A%2010px%3B%0A%09float%3A%20left%3B%0A%7D%0A%0A.b-navigator-bookmark%20ul%20%7B%0A%09list-style%3A%20square%3B%0A%09padding%3A%200px%2010px%200px%2015px%3B%0A%7D%0A%0A.b-navigator-bookmark%20li%20%7B%0A%09margin%3A%205px%3B%0A%09padding%3A%200px%3B%0A%09text-align%3A%20left%3B%0A%09cursor%3A%20pointer%3B%0A%09-webkit-transition%3A%20all%200.3s%3B%0A%09-moz-transition%3A%20all%200.3s%3B%0A%7D%0A%0A.b-navigator-bookmark%20li.selected%20%7B%0A%09text-decoration%3A%20underline%0A%7D%0A%0A.b-navigator-bookmark%20li%3Ahover%20%7B%0A%09color%3A%20%23aaa%3B%0A%7D")]);
    return self;
}
}),
smalltalk.BookBookmarkNavigatorWidget);



smalltalk.addClass('BookThumbnailNavigatorWidget', smalltalk.AbstractBookNavigatorWidget, ['bookmarkList'], 'AFI');
smalltalk.addMethod(
"_highlightPage_",
smalltalk.method({
selector: "highlightPage:",
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
}
}),
smalltalk.BookThumbnailNavigatorWidget);

smalltalk.addMethod(
"_renderOn_",
smalltalk.method({
selector: "renderOn:",
fn: function (html) {
    var self = this;
    smalltalk.send(html, "_style_", [smalltalk.send(self, "_style", [])]);
    (function ($rec) {smalltalk.send($rec, "_class_", [unescape("b-navigator-thumbnail%20%20b-navigator")]);return smalltalk.send($rec, "_with_", [function () {var bookmarkSearchInput = nil;smalltalk.send(html, "_div_", ["Folios"]);bookmarkSearchInput = smalltalk.send(smalltalk.send(html, "_input", []), "_asJQuery", []);self['@bookmarkList'] = function ($rec) {smalltalk.send($rec, "_with_", [function () {return smalltalk.send(self, "_renderPagesOn_", [html]);}]);return smalltalk.send($rec, "_asJQuery", []);}(smalltalk.send(html, "_ul", []));return smalltalk.send(smalltalk.ListFilter || ListFilter, "_filter_withInput_", [self['@bookmarkList'], bookmarkSearchInput]);}]);}(smalltalk.send(html, "_div", [])));
    return self;
}
}),
smalltalk.BookThumbnailNavigatorWidget);

smalltalk.addMethod(
"_renderPagesOn_",
smalltalk.method({
selector: "renderPagesOn:",
fn: function (html) {
    var self = this;
    var cycle = nil;
    cycle = smalltalk.send(smalltalk.Cycle || Cycle, "_with_", [["odd", "even"]]);
    smalltalk.send(smalltalk.send(self['@book'], "_pages", []), "_do_", [function (aPage) {return function ($rec) {smalltalk.send($rec, "_class_", [smalltalk.send(cycle, "_next", [])]);smalltalk.send($rec, "_with_", [function () {return smalltalk.send(html, "_div_", [function () {smalltalk.send(html, "_div_", [smalltalk.send(aPage, "_foliono", [])]);return smalltalk.send(smalltalk.send(html, "_img", []), "_src_", [smalltalk.send(aPage, "_navigatorThumbnailURL", [])]);}]);}]);return smalltalk.send($rec, "_onClick_", [function () {return smalltalk.send(self, "_announcePageChange_", [aPage]);}]);}(smalltalk.send(html, "_li", []));}]);
    return self;
}
}),
smalltalk.BookThumbnailNavigatorWidget);

smalltalk.addMethod(
"_style",
smalltalk.method({
selector: "style",
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_style", [], smalltalk.AbstractBookNavigatorWidget), "__comma", [unescape("%0A.b-navigator-thumbnail%20%7B%0A%09border-top-left-radius%3A%2010px%3B%0A%09border-bottom-left-radius%3A%2010px%3B%0A%09border-right%3A%200px%3B%0A%09margin-left%3A%2010px%3B%0A%09margin-right%3A%200px%3B%0A%09float%3A%20right%3B%0A%7D%0A%0A.b-navigator-thumbnail%20ul%20%7B%0A%09list-style%3A%20none%3B%0A%09padding%3A%200px%3B%0A%09float%3A%20right%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%20%7B%0A%09float%3A%20left%3B%0A%09margin%3A%205px%3B%0A%09display%3A%20block%3B%0A%09overflow%3A%20hidden%3B%0A%09height%3A%2070px%3B%0A%09width%3A%2050px%3B%0A%09text-align%3A%20center%3B%0A%09cursor%3A%20pointer%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ediv%3Ediv%7B%0A%09display%3A%20none%3B%0A%09position%3A%20relative%3B%0A%09z-index%3A%202%3B%0A%09background-color%3A%20black%3B%0A%09font-weight%3A%20bold%3B%0A%09font-size%3A%200.9em%3B%0A%7D%0A%0A%0A.b-navigator-thumbnail%20li.selected%20img%2C%0A.b-navigator-thumbnail%20li.selected%20+%20li.odd%20img%2C%0A.b-navigator-thumbnail%20.filtered%20li%20img%2C%0A.b-navigator-thumbnail%20li%3Ahover%20img%20%7B%0A%09opacity%3A%201%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ahover%20%7B%0A%09overflow%3A%20visible%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ahover%3Ediv%3Ediv%7B%0A%09display%3A%20block%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Ediv%20%7B%0A%09width%3A%2050px%3B%0A%20%20%09-webkit-transition%3A%20all%200.1s%20ease-out%3B%0A%20%09-moz-transition%3A%20all%200.1s%20ease-out%3B%0A%7D%0A%0A%0A.b-navigator-thumbnail%20li%3Ahover%3Ediv%20%7B%0A%20%20%20width%3A%20100px%3B%0A%20%20%20position%3A%20relative%3B%0A%20%20%20box-shadow%3A%200px%200px%2020px%20black%3B%0A%20%20%20z-index%3A%2030%3B%0A%7D%0A%0A%0A.b-navigator-thumbnail%20li.odd%3Ahover%3Ediv%20%7B%0A%20%20%20margin-left%3A%20-40px%3B%0A%7D%0A%0A.b-navigator-thumbnail%20ul%20li%3Afirst-child%3Ahover%3Ediv%20%7B%0A%20%20%20margin-right%3A%20-40px%3B%0A%20%20%20margin-left%3A%200px%3B%0A%20%20%20margin-top%3A%200px%3B%0A%7D%0A%0A.b-navigator-thumbnail%20.filtered%20li.odd%3Ahover%3Ediv%20%7B%0A%20%20%20margin-left%3A%200px%3B%0A%7D%0A%0A.b-navigator-thumbnail%20.filtered%20li%20%7B%0A%20%20%20width%3A%20100%25%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%20img%20%7B%0A%20%20%09width%3A%20100%25%3B%0A%09display%3A%20block%3B%0A%09opacity%3A%200.6%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%3Afirst-child%20+%20li%7B%0A%09clear%3A%20left%3B%0A%7D%0A%0A.b-navigator-thumbnail%20li%20img%20%7B%0A%09cursor%3A%20pointer%3B%0A%7D%0A")]);
    return self;
}
}),
smalltalk.BookThumbnailNavigatorWidget);



smalltalk.addClass('AbstractBookWidget', smalltalk.Widget, ['announcer', 'currentPageNo', 'book', 'scriptsRoot', 'rootBrush', 'isFullscreen', 'downloadBrush', 'menuJQuery', 'pageZoomWidget', 'pageZoomBrush', 'pageDescriptionsBrush', 'bookContainer', 'loader', 'folioBrush'], 'AFI');
smalltalk.addMethod(
"_afterPageChange_",
smalltalk.method({
selector: "afterPageChange:",
fn: function (data) {
    var self = this;
    smalltalk.send(self, "_updateFolioNumbers", []);
    smalltalk.send(self, "_openDescriptions", []);
    smalltalk.send(self, "_announcePageChange_", [smalltalk.send(self, "_currentPage", [])]);
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_announcer",
smalltalk.method({
selector: "announcer",
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
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_book",
smalltalk.method({
selector: "book",
fn: function () {
    var self = this;
    return self['@book'];
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_book_",
smalltalk.method({
selector: "book:",
fn: function (aBook) {
    var self = this;
    self['@book'] = aBook;
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_bookStyle",
smalltalk.method({
selector: "bookStyle",
fn: function () {
    var self = this;
    var $1;
    $1 = smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send("\n    \n\t\t\t.bk-widget  .b-arrow div {\n\t\t\t\t-webkit-transition: all 0.3s;\n\t\t\t\t-moz-transition: all 0.3s;\n\t\t\t\t-o-transition: all 0.3s;\n\t\t\t}\n\n\t\t\t.bk-widget  .b-arrow-next div { background-image:url(", "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["booklet/images/arrow-next_black.png);}\n\n\t\t\t.bk-widget  .b-arrow-next:hover div { background-image:url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["booklet/images/arrow-next.png);}\n\n\t\t\t.bk-widget  .b-arrow-prev div { background-image:url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["booklet/images/arrow-prev_black.png); }\n\n\t\t\t.bk-widget  .b-arrow-prev:hover div { background-image:url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["booklet/images/arrow-prev.png);}\n\n\t\t\t.bk-widget .b-counter + .b-counter {float: right;}\n\n\t\t\t.small>.bk-widget  .b-arrow-next div {background-image:url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["booklet/images/arrow-next_black-small.png);}\n\n\t\t\t.small>.bk-widget  .b-arrow-next:hover div { background-image:url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["booklet/images/arrow-next-small.png); }\n\n\t\t\t.small>.bk-widget  .b-arrow-prev div { background-image:url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["booklet/images/arrow-prev_black-small.png); }\n\n\t\t\t.small>.bk-widget  .b-arrow-prev:hover div { background-image:url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["booklet/images/arrow-prev-small.png);}\n\n\t\t\t.small>.bk-widget  .b-arrow-prev { left: -25px }\n\t\t\n\t\t\t.small>.bk-widget  .b-arrow-next { right: -25px }\n\n\t\t\t.small>.bk-widget  .b-arrow { width: 25px }\n\n\t\t\t.small>.bk-widget  .b-arrow  div { top: 36% }\n\t\t\n\n\t\n"]);
    return $1;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_closeDescriptions",
smalltalk.method({
selector: "closeDescriptions",
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(self['@pageDescriptionsBrush'], "_asJQuery", []), "_fadeOut", []);
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_currentPage",
smalltalk.method({
selector: "currentPage",
fn: function () {
    var self = this;
    var $1;
    $1 = smalltalk.send(self['@book'], "_pageAt_ifAbsent_", [smalltalk.send(self, "_currentPageNo", []), function () {return smalltalk.send(smalltalk.send(self['@book'], "_pages", []), "_last", []);}]);
    return $1;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_currentPageNo",
smalltalk.method({
selector: "currentPageNo",
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
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_fullScreenStyle",
smalltalk.method({
selector: "fullScreenStyle",
fn: function () {
    var self = this;
    var $1;
    $1 = smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send("\n\tbody.fullscreen {\n\t\toverflow: hidden;\n\t}\n\n\n\t.fullscreen.bk-widget {\n\t\tposition: fixed;\n\t\twidth: 100%;\n\t\theight: 100%;\n\t\tz-index: 200;\n\t\ttop: 0;\n\t\tleft: 0;\n\t\toverflow-y: auto;\n\t}\n\n\t.fullscreen.bk-widget .b-menu {\n\t\theight: 0px;\n\t}\n\n\t.fullscreen.bk-widget,\n\t.fullscreen.bk-widget .b-menu .b-selector,\n\t.fullscreen.bk-widget .b-menu .b-selector ul,\n\t.fullscreen.bk-widget .b-counter {\t\n\t\tcolor: white;\n\t\tbackground-color: black;\n\t}\n\n\t.fullscreen .b-zoom-fullscreen {\n\t\tposition: absolute;\n\t\tright: 0px;\n\t}\n\n\t.fullscreen.bk-widget .b-download-book a {\n\t\tposition: absolute;\n\t\tright: 60px;\n\t}\n\n\t.fullscreen .b-zoom-fullscreen a {\n\t\tbackground: url(", "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/unexpand_black.png) no-repeat;\n\t}\n\n\t.fullscreen .b-zoom-fullscreen a:hover {\n\t\tbackground: url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/unexpand_white.png) no-repeat;\n\t}\n\n\t.fullscreen h1.title {\n\t\tfont-size: 2em;\n\t\tcolor: white;\n\t\tborder-bottom: 0px;\n\t\tmargin: 5px 0px 0px 0px;\n\t\ttext-align: center;\n\t}\n\n\t.fullscreen \n"]);
    return $1;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_height",
smalltalk.method({
selector: "height",
fn: function () {
    var self = this;
    var $1;
    $1 = smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(self['@book'], "_height", []), "__star", [smalltalk.send(self, "_width", [])]), "__slash", [smalltalk.send(self['@book'], "_width", [])]), "__slash", [2]), "_rounded", []);
    return $1;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_initialize",
smalltalk.method({
selector: "initialize",
fn: function () {
    var self = this;
    smalltalk.send(self, "_initialize", [], smalltalk.Widget);
    self['@isFullscreen'] = false;
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_isContainerSmall",
smalltalk.method({
selector: "isContainerSmall",
fn: function () {
    var self = this;
    var $1;
    $1 = smalltalk.send(smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_width", []), "__lt", [500]);
    return $1;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_isRunInTestCase",
smalltalk.method({
selector: "isRunInTestCase",
fn: function () {
    var self = this;
    var $1;
    $1 = smalltalk.send(self, "_isTestCaseInContext_", [smalltalk.getThisContext()]);
    return $1;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_isTestCaseInContext_",
smalltalk.method({
selector: "isTestCaseInContext:",
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
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_loadBook",
smalltalk.method({
selector: "loadBook",
fn: function () {
    var self = this;
    smalltalk.send(self, "_subclassResponsibility", []);
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_loadCSS_",
smalltalk.method({
selector: "loadCSS:",
fn: function (anUrl) {
    var self = this;
    var $1, $2;
    $1 = smalltalk.send(smalltalk.send(smalltalk.HTMLCanvas || HTMLCanvas, "_onJQuery_", [smalltalk.send("head", "_asJQuery", [])]), "_link", []);
    smalltalk.send($1, "_href_", [smalltalk.send(smalltalk.send(self, "_scriptsRoot", []), "__comma", [anUrl])]);
    smalltalk.send($1, "_type_", ["text/css"]);
    $2 = smalltalk.send($1, "_rel_", ["stylesheet"]);
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_loadIViewerJS",
smalltalk.method({
selector: "loadIViewerJS",
fn: function () {
    var self = this;
    var $1;
    smalltalk.send(self, "_loadCSS_", ["iviewer/jquery.iviewer.css"]);
    smalltalk.send(self, "_loadJS_", ["iviewer/jquery.iviewer.min.js"]);
    $1 = smalltalk.send(self, "_loadJS_", ["iviewer/jquery.mousewheel.min.js"]);
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_loadJS_",
smalltalk.method({
selector: "loadJS:",
fn: function (anUrl) {
    var self = this;
    smalltalk.send(jQuery, "_ajax_", [smalltalk.HashedCollection._fromPairs_([smalltalk.send("dataType", "__minus_gt", ["script"]), smalltalk.send("url", "__minus_gt", [smalltalk.send(smalltalk.send(self, "_scriptsRoot", []), "__comma", [anUrl])]), smalltalk.send("cache", "__minus_gt", [true])])]);
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_loader",
smalltalk.method({
selector: "loader",
fn: function () {
    var self = this;
    return self['@loader'];
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_loader_",
smalltalk.method({
selector: "loader:",
fn: function (aBibNumLoader) {
    var self = this;
    self['@loader'] = aBibNumLoader;
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_navigatorWidth",
smalltalk.method({
selector: "navigatorWidth",
fn: function () {
    var self = this;
    var $1;
    $1 = smalltalk.send(smalltalk.AbstractBookNavigatorWidget ||
        AbstractBookNavigatorWidget, "_width", []);
    return $1;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_onPageChangeDo_",
smalltalk.method({
selector: "onPageChangeDo:",
fn: function (aBlockWithArg) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_announcer", []), "_on_do_", [smalltalk.PageChangeAnnouncement || PageChangeAnnouncement, function (aPageChangeAnnouncement) {return smalltalk.send(aBlockWithArg, "_value_", [smalltalk.send(aPageChangeAnnouncement, "_page", [])]);}]);
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_openPage_",
smalltalk.method({
selector: "openPage:",
fn: function (aPage) {
    var self = this;
    smalltalk.send(self, "_subclassResponsibility", []);
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_reloadWidget",
smalltalk.method({
selector: "reloadWidget",
fn: function () {
    var self = this;
    smalltalk.send(self['@book'], "_reset", []);
    smalltalk.send(self['@rootBrush'], "_contents_", [function (html) {return smalltalk.send(self, "_renderWidgetOn_", [html]);}]);
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderBook_on_",
smalltalk.method({
selector: "renderBook:on:",
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
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderBookMenuOn_",
smalltalk.method({
selector: "renderBookMenuOn:",
fn: function (html) {
    var self = this;
    var $1, $2;
    $1 = smalltalk.send(html, "_div", []);
    smalltalk.send($1, "_class_", ["book-menu"]);
    $2 = smalltalk.send($1, "_asJQuery", []);
    self['@menuJQuery'] = $2;
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderBookNavigator",
smalltalk.method({
selector: "renderBookNavigator",
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
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderBookOn_",
smalltalk.method({
selector: "renderBookOn:",
fn: function (html) {
    var self = this;
    smalltalk.send(self, "_subclassResponsibility", []);
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderBookTitle",
smalltalk.method({
selector: "renderBookTitle",
fn: function () {
    var self = this;
    var titleDiv;
    titleDiv = smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send("<h1 class=\"title\">", "__comma", [smalltalk.send(self['@book'], "_title", [])]), "__comma", [" ( "]), "__comma", [smalltalk.send(self['@book'], "_size", [])]), "__comma", [" pages ) </h1>"]), "_asJQuery", []);
    smalltalk.send(titleDiv, "_insertBefore_", [self['@menuJQuery']]);
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderDevToolsOn_",
smalltalk.method({
selector: "renderDevToolsOn:",
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
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderDownloadBookOn_",
smalltalk.method({
selector: "renderDownloadBookOn:",
fn: function (html) {
    var self = this;
    self['@downloadBrush'] = smalltalk.send(smalltalk.send(html, "_div", []), "_class_", ["b-download-book"]);
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderFullscreenControlsOn_",
smalltalk.method({
selector: "renderFullscreenControlsOn:",
fn: function (html) {
    var self = this;
    var $1, $2;
    $1 = smalltalk.send(html, "_div", []);
    smalltalk.send($1, "_class_", ["b-zoom-fullscreen"]);
    $2 = smalltalk.send($1, "_with_", [function () {return smalltalk.send(smalltalk.send(html, "_a", []), "_onClick_", [function () {return smalltalk.send(self, "_toggleFullscreen", []);}]);}]);
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderOn_",
smalltalk.method({
selector: "renderOn:",
fn: function (html) {
    var self = this;
    smalltalk.send(self, "_renderDevToolsOn_", [html]);
    self['@rootBrush'] = smalltalk.send(html, "_root", []);
    smalltalk.send(self, "_renderWidgetOn_", [html]);
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderPage_class_on_",
smalltalk.method({
selector: "renderPage:class:on:",
fn: function (aPage, aCssClass, html) {
    var self = this;
    var $1, $2, $3, $4, $5;
    $1 = smalltalk.send(html, "_div", []);
    smalltalk.send($1, "_class_", [aCssClass]);
    smalltalk.send($1, "_with_", [function () {$2 = smalltalk.send(smalltalk.PageWidget || PageWidget, "_new", []);smalltalk.send($2, "_page_", [aPage]);smalltalk.send($2, "_renderOn_", [html]);smalltalk.send($2, "_onCloseDo_", [function () {smalltalk.send(self, "_closeZoom", []);$3 = smalltalk.send(self, "_openDescriptions", []);return $3;}]);$4 = smalltalk.send($2, "_yourself", []);self['@pageZoomWidget'] = $4;return self['@pageZoomWidget'];}]);
    $5 = smalltalk.send($1, "_asJQuery", []);
    smalltalk.send($5, "_fadeIn_", ["slow"]);
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderPageDescriptionOn_",
smalltalk.method({
selector: "renderPageDescriptionOn:",
fn: function (html) {
    var self = this;
    var $1, $2;
    $1 = smalltalk.send(html, "_div", []);
    smalltalk.send($1, "_class_", ["page-desc"]);
    $2 = smalltalk.send($1, "_yourself", []);
    self['@pageDescriptionsBrush'] = $2;
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderWidgetOn_",
smalltalk.method({
selector: "renderWidgetOn:",
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
    smalltalk.send(self, "_loadBook", []);
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_renderZoomControlsOn_",
smalltalk.method({
selector: "renderZoomControlsOn:",
fn: function (html) {
    var self = this;
    var $1, $3, $4, $2;
    $1 = smalltalk.send(html, "_div", []);
    smalltalk.send($1, "_class_", ["b-zoom-magnify"]);
    $2 = smalltalk.send($1, "_with_", [function () {self['@zoomLeftPageAnchor'] = smalltalk.send(smalltalk.send(smalltalk.send(html, "_a", []), "_onClick_", [function () {return smalltalk.send(self, "_zoomLeftPage", []);}]), "_asJQuery", []);self['@zoomLeftPageAnchor'];smalltalk.send(self['@zoomLeftPageAnchor'], "_hide", []);self['@zoomRightPageAnchor'] = smalltalk.send(smalltalk.send(smalltalk.send(html, "_a", []), "_onClick_", [function () {return smalltalk.send(self, "_zoomRightPage", []);}]), "_asJQuery", []);self['@zoomRightPageAnchor'];smalltalk.send(self['@zoomRightPageAnchor'], "_hide", []);$3 = smalltalk.send(html, "_div", []);smalltalk.send($3, "_class_", ["b-zoom"]);$4 = smalltalk.send($3, "_yourself", []);self['@pageZoomBrush'] = $4;return self['@pageZoomBrush'];}]);
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_scriptsRoot",
smalltalk.method({
selector: "scriptsRoot",
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
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_scriptsRoot_",
smalltalk.method({
selector: "scriptsRoot:",
fn: function (anUrl) {
    var self = this;
    self['@scriptsRoot'] = anUrl;
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_style",
smalltalk.method({
selector: "style",
fn: function () {
    var self = this;
    var $2, $1;
    $1 = smalltalk.send(smalltalk.String || String, "_streamContents_", [function (aStream) {smalltalk.send(aStream, "_nextPutAll_", [smalltalk.send(self, "_bookStyle", [])]);smalltalk.send(aStream, "_nextPutAll_", [smalltalk.send(self, "_zoomControlsStyle", [])]);$2 = smalltalk.send(aStream, "_nextPutAll_", [smalltalk.send(self, "_fullScreenStyle", [])]);return $2;}]);
    return $1;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_toggleFullscreen",
smalltalk.method({
selector: "toggleFullscreen",
fn: function () {
    var self = this;
    self['@isFullscreen'] = smalltalk.send(self['@isFullscreen'], "_not", []);
    smalltalk.send(self, "_reloadWidget", []);
    return self;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_widgetClass",
smalltalk.method({
selector: "widgetClass",
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
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_width",
smalltalk.method({
selector: "width",
fn: function () {
    var self = this;
    var $2, $1;
    if (smalltalk.assert(self['@isFullscreen'])) {
        $2 = smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_width", []), "__minus", [smalltalk.send(2, "__star", [smalltalk.send(self, "_navigatorWidth", [])])]), "_min_", [900]);
    } else {
        $2 = smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_width", []);
    }
    $1 = smalltalk.send($2, "__minus", [smalltalk.send(2, "__star", [smalltalk.send(self, "_zoomControlWidth", [])])]);
    return $1;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_zoomControlWidth",
smalltalk.method({
selector: "zoomControlWidth",
fn: function () {
    var self = this;
    var $2, $1;
    $2 = smalltalk.send(self, "_isContainerSmall", []);
    if (smalltalk.assert($2)) {
        $1 = 30;
    } else {
        $1 = 85;
    }
    return $1;
}
}),
smalltalk.AbstractBookWidget);

smalltalk.addMethod(
"_zoomControlsStyle",
smalltalk.method({
selector: "zoomControlsStyle",
fn: function () {
    var self = this;
    var $1;
    $1 = smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send("\n\t\t\t.b-zoom {\n\t\t\t  position: fixed;\n\t\t\t  top: 0px;\n\t\t\t  left: 0px;\n\t\t\t  width: 100%;\n\t\t\t  height: 100%;\n\t\t\t  display: none;\n\t\t\t  z-index: 200;\n\t\t\t}\n\n\t\t\t.b-zoom .page-desc {\n\t\t\t  margin: 0px 5px;\n\t\t\t  width: auto;\n\t\t\t  color: white;\n\t\t\t  width: 45%;\n\t\t\t  padding-right: 20px;\n\t\t\t  height: 95%;\n\t\t\t  max-width:auto;\n\t\t\t  overflow-y: auto;\n\t\t\t  display: block;\n\t\t\t  float: left;\n\t\t\t  font-size: 1.3em;\n\t\t\t}\n\n\t\t\t.b-zoom-magnify {\n\t\t\t  margin: 0px auto;\n\t\t\t}\n\n\t\t\t.b-zoom-magnify a,\n\t\t\t.b-zoom-fullscreen a {\n\t\t\t\tdisplay: block;\n\t\t\t\twidth: 48px;\n\t\t\t\theight: 48px;\n\t\t\t\tz-index: 20;\n\t\t\t\tposition: relative;\n\t\t\t\tcursor: pointer;\n\t\t\t}\n\t\t\t\n\t\t\t.b-zoom-fullscreen {float: right}\n\n\t\t\t.b-zoom-fullscreen a {\n\t\t\t\tbackground: url(", "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/expand_black.png) no-repeat;\n\t\t\t}\n\n\t\t\t.b-zoom-fullscreen a:hover {\n\t\t\t\tbackground: url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/expand_white.png) no-repeat;\n\t\t\t}\n\n\t\t\t.b-download-book a {\n\t\t\t\tfloat: right;\n\t\t\t\tdisplay: block;\n\t\t\t\twidth: 73px;\n\t\t\t\theight: 36px;\n\t\t\t\tmargin-right: 5px;\n\t\t\t\tmargin-top: 6px;\n\t\t\t\tz-index: 20;\n\t\t\t\tposition: relative;\n\t\t\t\tcursor: pointer;\n\t\t\t\tbackground: url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/download_pdf_black.png) no-repeat;\n\t\t\t}\n\n\t\t\t.b-download-book a:hover {\n\t\t\t\tbackground: url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/download_pdf_white.png) no-repeat;\n\t\t\t}\n\n\t\t\t.small>.bk-widget .b-zoom-magnify a {\n\t\t\t\tbackground-image: none;\n\t\t\t}\n\n\t\t\t.b-zoom-magnify a {\n\t\t\t\tbackground: url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/magnify_black.png) no-repeat;\n\t\t\t}\n\t\t\t\n\t\t\t.b-zoom-magnify a:hover {\n\t\t\t\tbackground-image: url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/magnify_white.png);\n\t\t\t}\n\n\t\t\t.small>.bk-widget .b-zoom-magnify a:hover {\n\t\t\t\tbackground-image: none;\n\t\t\t}\n\n\t\t\t.b-zoom-magnify a {\n\t\t\t\tfloat: left;\n\t\t\t}\n\n\t\t\t.b-zoom-magnify a + a {\n\t\t\t\tfloat: right;\n\t\t\t}\n\n\t\t\t.b-zoom > div {\n\t\t\t  position: relative;\n\t\t\t  z-index: 30;\n\n\t\t\t  background-color: rgb(10,10,10);\n\t\t\t  border: 10px solid rgb(50,50,50);\n\n\t\t\t  background-color: rgba(10,10,10,0.8);\n\t\t\t  border: 10px solid rgba(50,50,50,0.8);\n\n\t\t\t  border-radius: 10px;\n\t\t\t  display:none;\n\t\t\t  padding: 1px;\n\t\t\t  height: 100%;\n\t\t\t}\n\n\t\t\t.b-zoom > div > div {\n\t\t\t  overflow: scroll;\n\t\t\t  border-radius: 10px;\n\t\t\t}\n\n\t\t\t.b-zoom .iviewer {\n\t\t\t\theight: 100%\n\t\t\t}\n\n\t\t\t.b-zoom .iviewer_with_text {\n\t\t\t  float: left;\n\t\t\t  width: 50%;\n\t\t\t  margin-right: 5px;\n\t\t\t}\n\n\t\t\t.iviewer {\n\t\t\t  backround-color: black;\n\t\t\t}\n\n\t\t\t.iviewer_cursor {\n\t\t\t  cursor: move;\n\t\t\t}\n\n\t\t\t.controls div.iviewer_common {\n\t\t\t  position: static !important;\t\t\n\t\t\t  margin: 5px auto;\n\t\t\t  background-color: transparent;\n\t\t\t}\n\n\t\t\t.controls div.iviewer_common:hover {\n\t\t\t\tbackground-color: white;\n\t\t\t}\n\n\t\t\t.iviewer_zoom_close {\n\t\t\t  background: url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/close_black28.png);\n\t\t\t}\n"]);
    return $1;
}
}),
smalltalk.AbstractBookWidget);



smalltalk.addClass('BookMonoWidget', smalltalk.AbstractBookWidget, ['zoomPageAnchor', 'bookBrush', 'currentPage'], 'AFI');
smalltalk.addMethod(
"_bookStyle",
smalltalk.method({
selector: "bookStyle",
fn: function () {
    var self = this;
    var $1;
    $1 = smalltalk.send(smalltalk.send(self, "_bookStyle", [], smalltalk.AbstractBookWidget), "__comma", ["\n    .pages img {\n    \t\tmargin: 10px auto; \n            display: block\n      }\n      \n       .BookMonoWidget  .b-navigator-thumbnail {\n      \t\twidth: 130px;\n      }\n       \n       .BookMonoWidget .b-navigator-thumbnail ul {\n       \t\tfloat: none;\n            width: 100%;\n       }\n       \n      .BookMonoWidget .b-navigator-thumbnail li {\n      \t\theight: auto;\n            float: none;\n            display: block;\n            margin: 10px auto;\n      }\n      \n      .BookMonoWidget .b-navigator-thumbnail li:hover {\n      \twidth: auto;\n      }\n      \n      .BookMonoWidget .b-navigator-thumbnail li.odd:hover>div,\n      .BookMonoWidget .b-navigator-thumbnail li.even:hover>div,\n \t  .BookMonoWidget .b-navigator-thumbnail ul li:first-child:hover>div,\n      .BookMonoWidget .b-navigator-thumbnail li:hover {\n   \t\t\tmargin: 0px auto;\n      }\n     .bk-widget  .b-arrow-prev div{\n    height: 170px;\n    float: left;\n    top: 25%;\n    width: 74px;\n    }\n     .bk-widget  .b-arrow-next div{\n    height: 170px;\n    float: right;\n    top: 25%;\n    width: 74px;\n    }\n   "]);
    return $1;
}
}),
smalltalk.BookMonoWidget);

smalltalk.addMethod(
"_closeZoom",
smalltalk.method({
selector: "closeZoom",
fn: function () {
    var self = this;
    var $1;
    smalltalk.send(smalltalk.send(".b-arrow", "_asJQuery", []), "_show", []);
    smalltalk.send(smalltalk.send(self['@pageZoomBrush'], "_asJQuery", []), "_fadeOut_do_", ["slow", function () {self['@pageZoomWidget'] = nil;self['@pageZoomWidget'];smalltalk.send(self['@pageZoomBrush'], "_empty", []);smalltalk.send(self['@zoomPageAnchor'], "_removeClass_", ["active"]);$1 = smalltalk.send(self['@zoomPageAnchor'], "_show", []);return $1;}]);
    return self;
}
}),
smalltalk.BookMonoWidget);

smalltalk.addMethod(
"_goToNextPage",
smalltalk.method({
selector: "goToNextPage",
fn: function () {
    var self = this;
    self['@currentPage'] = smalltalk.send(self['@currentPage'], "_nextPage", []);
    smalltalk.send(self, "_renderCurrentPage", []);
    return self;
}
}),
smalltalk.BookMonoWidget);

smalltalk.addMethod(
"_goToPreviousPage",
smalltalk.method({
selector: "goToPreviousPage",
fn: function () {
    var self = this;
    self['@currentPage'] = smalltalk.send(self['@currentPage'], "_previousPage", []);
    smalltalk.send(self, "_renderCurrentPage", []);
    return self;
}
}),
smalltalk.BookMonoWidget);

smalltalk.addMethod(
"_loadBook",
smalltalk.method({
selector: "loadBook",
fn: function () {
    var self = this;
    smalltalk.send(self, "_renderBook_on_", [self['@book'], self['@bookBrush']]);
    if (($receiver = self['@currentPage']) == nil || $receiver == undefined) {
        self['@currentPage'] = smalltalk.send(smalltalk.send(self['@book'], "_pages", []), "_first", []);
        self['@currentPage'];
    } else {
        self['@currentPage'];
    }
    smalltalk.send(self, "_renderCurrentPage", []);
    return self;
}
}),
smalltalk.BookMonoWidget);

smalltalk.addMethod(
"_openDescriptions",
smalltalk.method({
selector: "openDescriptions",
fn: function () {
    var self = this;
    smalltalk.send(console, "_log_", ["open description"]);
    smalltalk.send(smalltalk.send(self['@pageDescriptionsBrush'], "_asJQuery", []), "_hide", []);
    smalltalk.send(self['@pageDescriptionsBrush'], "_contents_", [function (html) {return smalltalk.send(smalltalk.send(smalltalk.send(html, "_div", []), "_asJQuery", []), "_html_", [smalltalk.send(self['@currentPage'], "_description", [])]);}]);
    smalltalk.send(smalltalk.send(self['@pageDescriptionsBrush'], "_asJQuery", []), "_fadeIn", []);
    return self;
}
}),
smalltalk.BookMonoWidget);

smalltalk.addMethod(
"_openPage_",
smalltalk.method({
selector: "openPage:",
fn: function (aPage) {
    var self = this;
    smalltalk.send(self['@bookBrush'], "_contents_", [function (html) {return smalltalk.send(smalltalk.send(html, "_img", []), "_src_", [smalltalk.send(aPage, "_thumbnailURL", [])]);}]);
    return self;
}
}),
smalltalk.BookMonoWidget);

smalltalk.addMethod(
"_renderBookOn_",
smalltalk.method({
selector: "renderBookOn:",
fn: function (html) {
    var self = this;
    var $1, $2, $3, $4;
    smalltalk.send(self, "_loadIViewerJS", []);
    $1 = smalltalk.send(html, "_div", []);
    smalltalk.send($1, "_class_", ["b-arrow-prev"]);
    $2 = smalltalk.send($1, "_with_", [function () {return smalltalk.send(smalltalk.send(html, "_div", []), "_onClick_", [function () {return smalltalk.send(self, "_goToPreviousPage", []);}]);}]);
    $3 = smalltalk.send(html, "_div", []);
    smalltalk.send($3, "_class_", ["b-arrow-next"]);
    $4 = smalltalk.send($3, "_with_", [function () {return smalltalk.send(smalltalk.send(html, "_div", []), "_onClick_", [function () {return smalltalk.send(self, "_goToNextPage", []);}]);}]);
    self['@bookBrush'] = smalltalk.send(smalltalk.send(html, "_div", []), "_class_", ["pages"]);
    smalltalk.send(self['@bookBrush'], "_onClick_", [function () {return smalltalk.send(self, "_zoomPage", []);}]);
    self['@folioBrush'] = smalltalk.send(smalltalk.send(html, "_div", []), "_class_", ["b-counter"]);
    return self;
}
}),
smalltalk.BookMonoWidget);

smalltalk.addMethod(
"_renderCurrentPage",
smalltalk.method({
selector: "renderCurrentPage",
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(smalltalk.send(self['@bookBrush'], "_asJQuery", []), "_find_", ["img"]), "_hide", []);
    smalltalk.send(self['@currentPage'], "_renderWidth_height_", [smalltalk.send(smalltalk.send(smalltalk.send(self, "_width", []), "__slash", [2]), "_rounded", []), smalltalk.send(self, "_height", [])]);
    smalltalk.send(self, "_openDescriptions", []);
    smalltalk.send(self, "_updateFolioNumbers", []);
    return self;
}
}),
smalltalk.BookMonoWidget);

smalltalk.addMethod(
"_renderZoomControlsOn_",
smalltalk.method({
selector: "renderZoomControlsOn:",
fn: function (html) {
    var self = this;
    var $1, $3, $4, $2;
    $1 = smalltalk.send(html, "_div", []);
    smalltalk.send($1, "_class_", ["b-zoom-magnify"]);
    $2 = smalltalk.send($1, "_with_", [function () {self['@zoomPageAnchor'] = smalltalk.send(smalltalk.send(smalltalk.send(html, "_a", []), "_onClick_", [function () {return smalltalk.send(self, "_zoomPage", []);}]), "_asJQuery", []);self['@zoomPageAnchor'];$3 = smalltalk.send(html, "_div", []);smalltalk.send($3, "_class_", ["b-zoom"]);$4 = smalltalk.send($3, "_yourself", []);self['@pageZoomBrush'] = $4;return self['@pageZoomBrush'];}]);
    return self;
}
}),
smalltalk.BookMonoWidget);

smalltalk.addMethod(
"_updateFolioNumbers",
smalltalk.method({
selector: "updateFolioNumbers",
fn: function () {
    var self = this;
    smalltalk.send(self['@folioBrush'], "_contents_", [smalltalk.send(self['@currentPage'], "_foliono", [])]);
    return self;
}
}),
smalltalk.BookMonoWidget);

smalltalk.addMethod(
"_zoomPage",
smalltalk.method({
selector: "zoomPage",
fn: function () {
    var self = this;
    smalltalk.send(self, "_closeDescriptions", []);
    smalltalk.send(self['@zoomPageAnchor'], "_hide", []);
    smalltalk.send(smalltalk.send(".b-arrow", "_asJQuery", []), "_hide", []);
    smalltalk.send(self['@pageZoomBrush'], "_contents_", [function (html) {return smalltalk.send(self, "_renderPage_class_on_", [self['@currentPage'], "b-left", html]);}]);
    smalltalk.send(smalltalk.send(self['@pageZoomBrush'], "_asJQuery", []), "_show", []);
    return self;
}
}),
smalltalk.BookMonoWidget);



smalltalk.addClass('BookWidget', smalltalk.AbstractBookWidget, ['width', 'bookBrush', 'leftFolioBrush', 'rightFolioBrush', 'zoomLeftPageAnchor', 'zoomRightPageAnchor'], 'AFI');
smalltalk.addMethod(
"_afterPageChange_",
smalltalk.method({
selector: "afterPageChange:",
fn: function (data) {
    var self = this;
    smalltalk.send(self, "_updateFolioNumbers", []);
    smalltalk.send(self, "_openDescriptions", []);
    smalltalk.send(self, "_announcePageChange_", [smalltalk.send(self, "_currentPage", [])]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_announcePageChange_",
smalltalk.method({
selector: "announcePageChange:",
fn: function (aPage) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_announcer", []), "_announce_", [smalltalk.send(smalltalk.PageChangeAnnouncement || PageChangeAnnouncement, "_page_", [aPage])]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_beforePageChange_",
smalltalk.method({
selector: "beforePageChange:",
fn: function (data) {
    var self = this;
    smalltalk.send(self, "_closeDescriptions", []);
    smalltalk.send(self, "_openPageNo_", [smalltalk.send(smalltalk.send(data, "_at_", ["curr"]), "__plus", [1])]);
    smalltalk.send(self, "_closeZoom", []);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_bookStyle",
smalltalk.method({
selector: "bookStyle",
fn: function () {
    var self = this;
    var $1;
    $1 = smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send("\n\t\t\t.bk-widget .booklet { margin-bottom: 20px\t}\t\t\t\n\n\t\t\t.bib-num-album {  padding: 10px }\n\n\t\t\t.bk-widget .b-counter {\n\t\t\t  margin-bottom: -20px;\n\t\t\t  margin-top: 20px;\n\t\t\t  width: 140px;\n\t\t\t  text-align: left;\n\t\t\t  bottom: 0px;\n\t\t\t  background-color: transparent;\n\t\t\t  font-weight: bold;\n\t\t\t  font-size: 1.1em;\n\t\t\t}\n\n\t\t\t.bk-widget .b-counter + .b-counter {\n\t\t\t  right: 0px;\n\t\t\t  text-align: right;\n\t\t\t}\n\n\t\t\t.bk-widget .loading {  text-align:center\t}\n\t\t\t\n\t\t\t.bk-widget .booklet .b-wrap-right {\n\t\t\t  background-color: transparent;\n\t\t\t  padding: 0px; !important;\n\t\t\t}\n\n\t\t\t.bk-widget .booklet .b-wrap-left {\n\t\t\t  background-color: transparent;\n\t\t\t  padding: 0px; !important;\n\t\t\t}\n\n\t\t\t.bk-widget .booklet .b-page-cover {  background-color: transparent; }\n\n\t\t\t.bk-widget .b-menu {\n\t\t\t  font-size: 1.4em;\n\t\t\t  font-weight: bold;\n\t\t\t  width: 820px;\n\t\t\t  margin: 0 auto;\n\t\t\t  height: 60px;\n\t\t\t}\n\n\t\t\t.bk-widget .b-menu .b-selector {\n\t\t\t  width: 600px;\n\t\t\t  text-align: left;\n\t\t\t  height: 60px;\n\t\t\t  float:none;\n\t\t\t}\n\n\t\t\t.bk-widget .b-menu .b-selector .b-current {\n\t\t\t  height: auto;\n\t\t\t  text-align: left;\n\t\t\t  background: url(", "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/menu_off.png) no-repeat 15px center;\n\t\t\t  padding-left: 45px;\n\t\t\t}\n\n\t\t\t.bk-widget .b-menu .b-selector:hover .b-current {\n\t\t\t  background-image: url("]), "__comma", [smalltalk.send(self, "_scriptsRoot", [])]), "__comma", ["images/menu_on.png);\n\t\t\t}\n\n\t\t\t.bk-widget .b-menu .b-selector { color: black; }\n\n\t\t\t.bk-widget .b-menu .b-selector a { color: inherit;}\n\n\t\t\t.bk-widget .b-menu .b-selector:hover {color: black; }\n\n\t\t\t.bk-widget .b-menu .b-selector:hover ul { box-shadow: 2px 2px 40px rgba(2,2,0,0.8); }\n\n\t\t\t.bk-widget .b-menu .b-selector ul {\n\t\t\t  width: 584px;\n\t\t\t  top: auto;\n\t\t\t  max-height: 600px;\n\t\t\t  overflow-y: auto !important;\n\t\t\t  background-color: white;\n\t\t\t}\n\n\t\t\t.bk-widget .b-menu .b-selector ul li { font-size: 1.2em; }\n\n\t\t\t.bk-widget .b-menu .b-selector ul li a { height: auto; }\n\n\t\t\t.bk-widget .b-menu .b-selector ul li a .b-text { float: none; }\n\n\t\t\t.bk-widget button {float: left}\n\n\t\t\t.clear { \n\t\t\t\tclear: both;\n\t\t\t\theight: 0px !important;\n\t\t\t}\n\n\t\t\t.bk-widget .b-wrap {\n\t\t\t\tcursor: -moz-zoom-in;\n\t\t\t\tcursor: -webkit-zoom-in;\n\t\t\t}\n"]);
    return $1;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_bookletOptions",
smalltalk.method({
selector: "bookletOptions",
fn: function () {
    var self = this;
    return function ($rec) {smalltalk.send($rec, "_at_put_", ["pageSelector", false]);smalltalk.send($rec, "_at_put_", ["chapterSelector", smalltalk.send(self['@isFullscreen'], "_not", [])]);smalltalk.send($rec, "_at_put_", ["menu", self['@menuJQuery']]);smalltalk.send($rec, "_at_put_", ["tabs", false]);smalltalk.send($rec, "_at_put_", ["keyboard", false]);smalltalk.send($rec, "_at_put_", ["arrows", true]);smalltalk.send($rec, "_at_put_", ["closed", true]);smalltalk.send($rec, "_at_put_", ["covers", true]);smalltalk.send($rec, "_at_put_", ["autoCenter", true]);smalltalk.send($rec, "_at_put_", ["pagePadding", 0]);smalltalk.send($rec, "_at_put_", ["shadows", true]);smalltalk.send($rec, "_at_put_", ["width", smalltalk.send(self, "_width", [])]);smalltalk.send($rec, "_at_put_", ["height", smalltalk.send(self, "_height", [])]);smalltalk.send($rec, "_at_put_", ["manual", false]);smalltalk.send($rec, "_at_put_", ["pageNumbers", false]);smalltalk.send($rec, "_at_put_", ["overlays", false]);smalltalk.send($rec, "_at_put_", ["hovers", false]);smalltalk.send($rec, "_at_put_", ["arrowsHide", false]);smalltalk.send($rec, "_at_put_", ["closedFrontTitle", smalltalk.send(self['@book'], "_title", [])]);smalltalk.send($rec, "_at_put_", ["closedFrontChapter", smalltalk.send(self['@book'], "_title", [])]);smalltalk.send($rec, "_at_put_", ["closedBackTitle", "Fin"]);smalltalk.send($rec, "_at_put_", ["closedBackChapter", "Fin"]);smalltalk.send($rec, "_at_put_", ["previousPageTitle", unescape("Pr%E9c%E9dent")]);smalltalk.send($rec, "_at_put_", ["nextPageTitle", "Suivant"]);smalltalk.send($rec, "_at_put_", ["before", function (data) {return smalltalk.send(self, "_beforePageChange_", [data]);}]);smalltalk.send($rec, "_at_put_", ["after", function (data) {return smalltalk.send(self, "_afterPageChange_", [data]);}]);smalltalk.send($rec, "_at_put_", ["hash", smalltalk.send(smalltalk.send(self, "_isJQueryMobile", []), "_not", [])]);return smalltalk.send($rec, "_yourself", []);}(smalltalk.send(smalltalk.HashedCollection || HashedCollection, "_new", []));
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_clear",
smalltalk.method({
selector: "clear",
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(unescape(".bk-widget"), "_asJQuery", []), "_remove", []);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_closeZoom",
smalltalk.method({
selector: "closeZoom",
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(unescape(".b-arrow"), "_asJQuery", []), "_show", []);
    smalltalk.send(smalltalk.send(self['@pageZoomBrush'], "_asJQuery", []), "_fadeOut_do_", ["slow", function () {self['@pageZoomWidget'] = nil;smalltalk.send(self['@pageZoomBrush'], "_empty", []);(function ($rec) {smalltalk.send($rec, "_removeClass_", ["active"]);return smalltalk.send($rec, "_show", []);}(self['@zoomLeftPageAnchor']));(function ($rec) {smalltalk.send($rec, "_removeClass_", ["active"]);return smalltalk.send($rec, "_show", []);}(self['@zoomRightPageAnchor']));($receiver = smalltalk.send(smalltalk.send(self, "_currentPageNo", []), "__eq", [1])).klass === smalltalk.Boolean ? $receiver ? function () {return smalltalk.send(self['@zoomLeftPageAnchor'], "_hide", []);}() : nil : smalltalk.send($receiver, "_ifTrue_", [function () {return smalltalk.send(self['@zoomLeftPageAnchor'], "_hide", []);}]);return ($receiver = ($receiver = smalltalk.send(self, "_currentPageNo", [])).klass === smalltalk.Number ? $receiver > smalltalk.send(self['@book'], "_size", []) : smalltalk.send($receiver, "__gt", [smalltalk.send(self['@book'], "_size", [])])).klass === smalltalk.Boolean ? $receiver ? function () {return smalltalk.send(self['@zoomRightPageAnchor'], "_hide", []);}() : nil : smalltalk.send($receiver, "_ifTrue_", [function () {return smalltalk.send(self['@zoomRightPageAnchor'], "_hide", []);}]);}]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_closeZoomOr_",
smalltalk.method({
selector: "closeZoomOr:",
fn: function (aBlock) {
    var self = this;
    smalltalk.send(self['@pageZoomWidget'], "_ifNil_ifNotNil_", [aBlock, function () {smalltalk.send(self, "_closeZoom", []);return smalltalk.send(self, "_openDescriptions", []);}]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_goToPageNo_",
smalltalk.method({
selector: "goToPageNo:",
fn: function (pageNo) {
    var self = this;
    smalltalk.send(smalltalk.send(self['@bookContainer'], "_asJQuery", []), "_booklet_", [pageNo]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_ifIE_ifNotIE_",
smalltalk.method({
selector: "ifIE:ifNotIE:",
fn: function (aBlock, anotherBlock) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_isIE", []), "_ifTrue_ifFalse_", [aBlock, anotherBlock]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_isIE",
smalltalk.method({
selector: "isIE",
fn: function () {
    var self = this;
    var ie = nil;
    ie = jQuery.browser.msie;
    return smalltalk.send(ie, "_notNil", []);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_isJQueryMobile",
smalltalk.method({
selector: "isJQueryMobile",
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(typeof window == "undefined" ? nil : window, "_jQuery", []), "_at_", ["jqmData"]), "_isNil", []), "_not", []);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_leftPage",
smalltalk.method({
selector: "leftPage",
fn: function () {
    var self = this;
    return smalltalk.send(self['@book'], "_pageAt_ifAbsent_", [($receiver = self['@currentPageNo']).klass === smalltalk.Number ? $receiver - 1 : smalltalk.send($receiver, "__minus", [1]), function () {return smalltalk.send(smalltalk.Page || Page, "_new", []);}]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_loadBook",
smalltalk.method({
selector: "loadBook",
fn: function () {
    var self = this;
    smalltalk.send(self, "_loadBookThenRenderOn_", [self['@bookBrush']]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_loadBookThenRenderOn_",
smalltalk.method({
selector: "loadBookThenRenderOn:",
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
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_loadBookletJSThen_",
smalltalk.method({
selector: "loadBookletJSThen:",
fn: function (aBlock) {
    var self = this;
    var $1;
    $1 = smalltalk.send(smalltalk.send(window, "_jQuery", []), "_at_", ["booklet"]);
    smalltalk.send($1, "_ifNil_ifNotNil_", [function () {return smalltalk.send(self, "_renderScriptsOn_Then_", [smalltalk.send(smalltalk.HTMLCanvas || HTMLCanvas, "_onJQuery_", [smalltalk.send("head", "_asJQuery", [])]), aBlock]);}, aBlock]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_loader",
smalltalk.method({
selector: "loader",
fn: function () {
    var self = this;
    return ($receiver = self['@loader']) == nil || $receiver == undefined ? function () {return self['@loader'] = smalltalk.send(smalltalk.SouvignyLoader || SouvignyLoader, "_new", []);}() : $receiver;
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_openDescriptions",
smalltalk.method({
selector: "openDescriptions",
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(self['@pageDescriptionsBrush'], "_asJQuery", []), "_hide", []);
    smalltalk.send(self['@pageDescriptionsBrush'], "_contents_", [function (html) {smalltalk.send(smalltalk.send(smalltalk.send(html, "_div", []), "_asJQuery", []), "_html_", [smalltalk.send(smalltalk.send(self, "_leftPage", []), "_description", [])]);return smalltalk.send(smalltalk.send(smalltalk.send(html, "_div", []), "_asJQuery", []), "_html_", [smalltalk.send(smalltalk.send(self, "_rightPage", []), "_description", [])]);}]);
    smalltalk.send(smalltalk.send(self['@pageDescriptionsBrush'], "_asJQuery", []), "_fadeIn", []);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_openPage_",
smalltalk.method({
selector: "openPage:",
fn: function (aPage) {
    var self = this;
    smalltalk.send(self, "_goToPageNo_", [smalltalk.send(aPage, "_pageNo", [])]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_openPageNo_",
smalltalk.method({
selector: "openPageNo:",
fn: function (anInteger) {
    var self = this;
    self['@currentPageNo'] = anInteger;
    smalltalk.send(self['@book'], "_pagesNo_do_", [[($receiver = anInteger).klass === smalltalk.Number ? $receiver - 1 : smalltalk.send($receiver, "__minus", [1]), anInteger], function (aPage) {return smalltalk.send(aPage, "_renderWidth_height_", [smalltalk.send(($receiver = smalltalk.send(self, "_width", [])).klass === smalltalk.Number ? $receiver / 2 : smalltalk.send($receiver, "__slash", [2]), "_rounded", []), smalltalk.send(self, "_height", [])]);}]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderBook_on_",
smalltalk.method({
selector: "renderBook:on:",
fn: function (aBook, aBrush) {
    var self = this;
    smalltalk.send(self, "_renderBook_on_", [aBook, aBrush], smalltalk.AbstractBookWidget);
    smalltalk.send(self, "_loadBookletJSThen_", [function () {smalltalk.send(smalltalk.send(self['@bookContainer'], "_asJQuery", []), "_booklet_", [smalltalk.send(self, "_bookletOptions", [])]);smalltalk.send(smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_find_", [".b-wrap-left"]), "_click_", [function () {return smalltalk.send(self, "_zoomLeftPage", []);}]);return smalltalk.send(smalltalk.send(smalltalk.send(self['@rootBrush'], "_asJQuery", []), "_find_", [".b-wrap-right, .b-page-cover"]), "_click_", [function () {return smalltalk.send(self, "_zoomRightPage", []);}]);}]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderBookOn_",
smalltalk.method({
selector: "renderBookOn:",
fn: function (html) {
    var self = this;
    var $2, $3, $4, $5, $1;
    self['@bookContainer'] = smalltalk.send(html, "_div", []);
    smalltalk.send(self['@bookContainer'], "_class_", ["book"]);
    $1 = smalltalk.send(self['@bookContainer'], "_with_", [function () {self['@leftFolioBrush'] = smalltalk.send(smalltalk.send(html, "_div", []), "_class_", ["b-counter"]);self['@leftFolioBrush'];self['@rightFolioBrush'] = smalltalk.send(smalltalk.send(html, "_div", []), "_class_", ["b-counter"]);self['@rightFolioBrush'];$2 = smalltalk.send(html, "_div", []);smalltalk.send($2, "_class_", ["b-load"]);smalltalk.send($2, "_with_", [function () {$3 = smalltalk.send(html, "_div", []);smalltalk.send($3, "_class_", ["loading"]);$4 = smalltalk.send($3, "_with_", [function () {return smalltalk.send(smalltalk.send(html, "_img", []), "_src_", [smalltalk.send(smalltalk.send(self, "_scriptsRoot", []), "__comma", ["images/ajax-loader.gif"])]);}]);return $4;}]);$5 = smalltalk.send($2, "_yourself", []);self['@bookBrush'] = $5;return self['@bookBrush'];}]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderScriptsOn_Then_",
smalltalk.method({
selector: "renderScriptsOn:Then:",
fn: function (html, aBlock) {
    var self = this;
    smalltalk.send(self, "_loadCSS_", ["booklet/jquery.booklet.1.2.0.css"]);
    smalltalk.send(jQuery, "_ajax_", [smalltalk.HashedCollection._fromPairs_([smalltalk.send("dataType", "__minus_gt", ["script"]), smalltalk.send("url", "__minus_gt", [smalltalk.send(smalltalk.send(self, "_scriptsRoot", []), "__comma", ["booklet/jquery.booklet.1.2.0.min.js"])]), smalltalk.send("cache", "__minus_gt", [true]), smalltalk.send("success", "__minus_gt", [aBlock])])]);
    smalltalk.send(self, "_loadIViewerJS", []);
    smalltalk.send(self, "_loadJS_", ["booklet/jquery.easing.1.3.js"]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_renderZoomControlsOn_",
smalltalk.method({
selector: "renderZoomControlsOn:",
fn: function (html) {
    var self = this;
    var $1, $3, $4, $2;
    $1 = smalltalk.send(html, "_div", []);
    smalltalk.send($1, "_class_", ["b-zoom-magnify"]);
    $2 = smalltalk.send($1, "_with_", [function () {self['@zoomLeftPageAnchor'] = smalltalk.send(smalltalk.send(smalltalk.send(html, "_a", []), "_onClick_", [function () {return smalltalk.send(self, "_zoomLeftPage", []);}]), "_asJQuery", []);self['@zoomLeftPageAnchor'];smalltalk.send(self['@zoomLeftPageAnchor'], "_hide", []);self['@zoomRightPageAnchor'] = smalltalk.send(smalltalk.send(smalltalk.send(html, "_a", []), "_onClick_", [function () {return smalltalk.send(self, "_zoomRightPage", []);}]), "_asJQuery", []);self['@zoomRightPageAnchor'];smalltalk.send(self['@zoomRightPageAnchor'], "_hide", []);$3 = smalltalk.send(html, "_div", []);smalltalk.send($3, "_class_", ["b-zoom"]);$4 = smalltalk.send($3, "_yourself", []);self['@pageZoomBrush'] = $4;return self['@pageZoomBrush'];}]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_reset",
smalltalk.method({
selector: "reset",
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_clear", []), "_show", []);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_rightPage",
smalltalk.method({
selector: "rightPage",
fn: function () {
    var self = this;
    return smalltalk.send(self['@book'], "_pageAt_ifAbsent_", [self['@currentPageNo'], function () {return smalltalk.send(smalltalk.Page || Page, "_new", []);}]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_show",
smalltalk.method({
selector: "show",
fn: function () {
    var self = this;
    smalltalk.send(self, "_appendToJQuery_", [smalltalk.send(unescape(".bib-num-album"), "_asJQuery", [])]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_updateFolioNumbers",
smalltalk.method({
selector: "updateFolioNumbers",
fn: function () {
    var self = this;
    smalltalk.send(self['@leftFolioBrush'], "_contents_", [smalltalk.send(smalltalk.send(self, "_leftPage", []), "_foliono", [])]);
    smalltalk.send(self['@rightFolioBrush'], "_contents_", [smalltalk.send(smalltalk.send(self, "_rightPage", []), "_foliono", [])]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_zoomLeftPage",
smalltalk.method({
selector: "zoomLeftPage",
fn: function () {
    var self = this;
    smalltalk.send(self, "_closeZoomOr_", [function () {smalltalk.send(self, "_zoomPageNo_withClass_", [($receiver = self['@currentPageNo']).klass === smalltalk.Number ? $receiver - 1 : smalltalk.send($receiver, "__minus", [1]), unescape("b-left")]);return smalltalk.send(self['@zoomLeftPageAnchor'], "_addClass_", ["active"]);}]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_zoomPageNo_withClass_",
smalltalk.method({
selector: "zoomPageNo:withClass:",
fn: function (anInteger, aCssClass) {
    var self = this;
    smalltalk.send(self, "_closeDescriptions", []);
    smalltalk.send(self['@zoomLeftPageAnchor'], "_hide", []);
    smalltalk.send(self['@zoomRightPageAnchor'], "_hide", []);
    smalltalk.send(smalltalk.send(unescape(".b-arrow"), "_asJQuery", []), "_hide", []);
    smalltalk.send(self['@book'], "_pageAt_do_", [anInteger, function (aPage) {smalltalk.send(self['@pageZoomBrush'], "_contents_", [function (html) {return smalltalk.send(self, "_renderPage_class_on_", [aPage, aCssClass, html]);}]);return smalltalk.send(smalltalk.send(self['@pageZoomBrush'], "_asJQuery", []), "_show", []);}]);
    return self;
}
}),
smalltalk.BookWidget);

smalltalk.addMethod(
"_zoomRightPage",
smalltalk.method({
selector: "zoomRightPage",
fn: function () {
    var self = this;
    smalltalk.send(self, "_closeZoomOr_", [function () {smalltalk.send(self, "_zoomPageNo_withClass_", [self['@currentPageNo'], unescape("b-right")]);return smalltalk.send(self['@zoomRightPageAnchor'], "_addClass_", ["active"]);}]);
    return self;
}
}),
smalltalk.BookWidget);


smalltalk.addMethod(
"_open",
smalltalk.method({
selector: "open",
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_new", []), "_show", []);
    return self;
}
}),
smalltalk.BookWidget.klass);

smalltalk.addMethod(
"_reset",
smalltalk.method({
selector: "reset",
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_new", []), "_reset", []);
    return self;
}
}),
smalltalk.BookWidget.klass);


smalltalk.addClass('BibNumAlbum', smalltalk.Object, ['container', 'ajax', 'url', 'scriptsRoot', 'bookWidget'], 'AFI');
smalltalk.addMethod(
"_ajax",
smalltalk.method({
selector: "ajax",
fn: function () {
    var self = this;
    return ($receiver = self['@ajax']) == nil || $receiver == undefined ? function () {return self['@ajax'] = smalltalk.send(smalltalk.Ajax || Ajax, "_url_", [smalltalk.send(self, "_url", [])]);}() : $receiver;
    return self;
}
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_ajax_",
smalltalk.method({
selector: "ajax:",
fn: function (anAjax) {
    var self = this;
    self['@ajax'] = anAjax;
    return self;
}
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_container",
smalltalk.method({
selector: "container",
fn: function () {
    var self = this;
    return self['@container'];
    return self;
}
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_container_",
smalltalk.method({
selector: "container:",
fn: function (aJQuery) {
    var self = this;
    self['@container'] = aJQuery;
    return self;
}
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_load",
smalltalk.method({
selector: "load",
fn: function () {
    var self = this;
    var $1, $2, $3;
    var loader;
    loader = smalltalk.send(smalltalk.BibNumLoader || BibNumLoader, "_ajax_", [smalltalk.send(self, "_ajax", [])]);
    smalltalk.send(loader, "_loadBookFromJSONOnSuccess_", [function (aBook, playerClassName) {var playerClass;$1 = smalltalk.send(smalltalk, "_at_", [playerClassName]);if (($receiver = $1) == nil || $receiver == undefined) {playerClass = smalltalk.BookWidget || BookWidget;} else {playerClass = $1;}$2 = smalltalk.send(playerClass, "_new", []);smalltalk.send($2, "_book_", [aBook]);smalltalk.send($2, "_scriptsRoot_", [smalltalk.send(self, "_scriptsRoot", [])]);$3 = smalltalk.send($2, "_appendToJQuery_", [smalltalk.send(self, "_container", [])]);self['@bookWidget'] = $3;return self['@bookWidget'];}]);
    return self;
}
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_pages",
smalltalk.method({
selector: "pages",
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(self['@bookWidget'], "_book", []), "_pages", []);
    return self;
}
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_scriptsRoot",
smalltalk.method({
selector: "scriptsRoot",
fn: function () {
    var self = this;
    return ($receiver = self['@scriptsRoot']) == nil || $receiver == undefined ? function () {return self['@scriptsRoot'] = "";}() : $receiver;
    return self;
}
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_scriptsRoot_",
smalltalk.method({
selector: "scriptsRoot:",
fn: function (anUrl) {
    var self = this;
    self['@scriptsRoot'] = anUrl;
    return self;
}
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_url",
smalltalk.method({
selector: "url",
fn: function () {
    var self = this;
    return self['@url'];
    return self;
}
}),
smalltalk.BibNumAlbum);

smalltalk.addMethod(
"_url_",
smalltalk.method({
selector: "url:",
fn: function (aString) {
    var self = this;
    self['@url'] = aString;
    return self;
}
}),
smalltalk.BibNumAlbum);


smalltalk.addMethod(
"_load_in_",
smalltalk.method({
selector: "load:in:",
fn: function (anURLForJSON, aJQuerySelector) {
    var self = this;
    return function ($rec) {smalltalk.send($rec, "_url_", [anURLForJSON]);smalltalk.send($rec, "_container_", [smalltalk.send(aJQuerySelector, "_asJQuery", [])]);return smalltalk.send($rec, "_load", []);}(smalltalk.send(self, "_new", []));
    return self;
}
}),
smalltalk.BibNumAlbum.klass);

smalltalk.addMethod(
"_load_in_scriptsRoot_",
smalltalk.method({
selector: "load:in:scriptsRoot:",
fn: function (anURLForJSON, aJQuerySelector, anURL) {
    var self = this;
    return function ($rec) {smalltalk.send($rec, "_url_", [anURLForJSON]);smalltalk.send($rec, "_container_", [smalltalk.send(aJQuerySelector, "_asJQuery", [])]);smalltalk.send($rec, "_scriptsRoot_", [anURL]);return smalltalk.send($rec, "_load", []);}(smalltalk.send(self, "_new", []));
    return self;
}
}),
smalltalk.BibNumAlbum.klass);


smalltalk.addClass('BibNumLoader', smalltalk.Object, ['ajax'], 'AFI');
smalltalk.addMethod(
"_abort",
smalltalk.method({
selector: "abort",
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_ajax", []), "_abort", []);
    return self;
}
}),
smalltalk.BibNumLoader);

smalltalk.addMethod(
"_ajax",
smalltalk.method({
selector: "ajax",
fn: function () {
    var self = this;
    return ($receiver = self['@ajax']) == nil || $receiver == undefined ? function () {return self['@ajax'] = smalltalk.send(smalltalk.Ajax || Ajax, "_new", []);}() : $receiver;
    return self;
}
}),
smalltalk.BibNumLoader);

smalltalk.addMethod(
"_ajax_",
smalltalk.method({
selector: "ajax:",
fn: function (anAjax) {
    var self = this;
    self['@ajax'] = anAjax;
    return self;
}
}),
smalltalk.BibNumLoader);

smalltalk.addMethod(
"_buildBookFromJSon_",
smalltalk.method({
selector: "buildBookFromJSon:",
fn: function (aJSONObjectOrString) {
    var self = this;
    var book = nil;
    var album = nil;
    album = smalltalk.send(($receiver = smalltalk.send(aJSONObjectOrString, "_isString", [])).klass === smalltalk.Boolean ? $receiver ? function () {return smalltalk.send(smalltalk.send(typeof window == "undefined" ? nil : window, "_JSON", []), "_parse_", [aJSONObjectOrString]);}() : function () {return aJSONObjectOrString;}() : smalltalk.send($receiver, "_ifTrue_ifFalse_", [function () {return smalltalk.send(smalltalk.send(typeof window == "undefined" ? nil : window, "_JSON", []), "_parse_", [aJSONObjectOrString]);}, function () {return aJSONObjectOrString;}]), "_album", []);
    book = function ($rec) {smalltalk.send($rec, "_title_", [smalltalk.send(album, "_at_", ["titre"])]);smalltalk.send($rec, "_width_", [smalltalk.send(album, "_at_", ["width"])]);smalltalk.send($rec, "_height_", [smalltalk.send(album, "_at_", ["height"])]);smalltalk.send($rec, "_downloadUrl_", [smalltalk.send(album, "_at_", ["download_url"])]);return smalltalk.send($rec, "_yourself", []);}(smalltalk.send(smalltalk.Book || Book, "_new", []));
    smalltalk.send(smalltalk.send(album, "_ressources", []), "_do_", [function (aRessource) {return function ($rec) {smalltalk.send($rec, "_title_", [smalltalk.send(aRessource, "_at_", ["titre"])]);smalltalk.send($rec, "_description_", [smalltalk.send(aRessource, "_at_", ["description"])]);smalltalk.send($rec, "_thumbnailURL_", [smalltalk.send(aRessource, "_at_", ["thumbnail"])]);smalltalk.send($rec, "_fullImageURL_", [smalltalk.send(aRessource, "_at_", ["original"])]);smalltalk.send($rec, "_foliono_", [smalltalk.send(aRessource, "_at_", ["foliono"])]);smalltalk.send($rec, "_navigatorThumbnailURL_", [smalltalk.send(aRessource, "_at_", ["navigator_thumbnail"])]);return smalltalk.send($rec, "_downloadURL_", [smalltalk.send(aRessource, "_at_", ["download"])]);}(smalltalk.send(book, "_newPage", []));}]);
    return book;
    return self;
}
}),
smalltalk.BibNumLoader);

smalltalk.addMethod(
"_loadBookFromJSONOnSuccess_",
smalltalk.method({
selector: "loadBookFromJSONOnSuccess:",
fn: function (aBlock) {
    var self = this;
    var $1, $2;
    $1 = smalltalk.send(self, "_ajax", []);
    smalltalk.send($1, "_onSuccessDo_", [function (data) {var book;book = smalltalk.send(self, "_buildBookFromJSon_", [data]);return smalltalk.send(aBlock, "_value_value_", [book, smalltalk.send(smalltalk.send(smalltalk.send(data, "_at_", ["album"]), "_at_", ["player"]), "_asString", [])]);}]);
    $2 = smalltalk.send($1, "_send", []);
    return self;
}
}),
smalltalk.BibNumLoader);


smalltalk.addMethod(
"_ajax_",
smalltalk.method({
selector: "ajax:",
fn: function (anAjax) {
    var self = this;
    var $1;
    $1 = smalltalk.send(smalltalk.send(self, "_new", []), "_ajax_", [anAjax]);
    return $1;
}
}),
smalltalk.BibNumLoader.klass);


smalltalk.addClass('SouvignyLoader', smalltalk.BibNumLoader, ['pages', 'links', 'book'], 'AFI');
smalltalk.addMethod(
"_baseURL",
smalltalk.method({
selector: "baseURL",
fn: function () {
    var self = this;
    return unescape("souvigny/B031906101_MS_001/");
    return self;
}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_book",
smalltalk.method({
selector: "book",
fn: function () {
    var self = this;
    return ($receiver = self['@book']) == nil || $receiver == undefined ? function () {return self['@book'] = function ($rec) {smalltalk.send($rec, "_width_", [390]);smalltalk.send($rec, "_height_", [594]);return smalltalk.send($rec, "_yourself", []);}(smalltalk.send(smalltalk.send(smalltalk.send(self, "_class", []), "_bookClass", []), "_new", []));}() : $receiver;
    return self;
}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_buildBookFromHTML_",
smalltalk.method({
selector: "buildBookFromHTML:",
fn: function (aHTMLString) {
    var self = this;
    var anchors = nil;
    anchors = smalltalk.send(smalltalk.send(aHTMLString, "_asJQuery", []), "_find_", [unescape("li%20a%5Bhref%24%3D%22jpg%22%5D")]);
    smalltalk.send(anchors, "_each_", [function (index, element) {var fileName = nil;fileName = smalltalk.send(smalltalk.send(smalltalk.JQuery || JQuery, "_fromElement_", [element]), "_attr_", ["href"]);return function ($rec) {smalltalk.send($rec, "_fullImageURL_", [smalltalk.send(smalltalk.send(self, "_fullImagesURL", []), "__comma", [fileName])]);return smalltalk.send($rec, "_thumbnailURL_", [smalltalk.send(smalltalk.send(self, "_thumbsURL", []), "__comma", [fileName])]);}(smalltalk.send(smalltalk.send(self, "_book", []), "_newPage", []));}]);
    return self;
}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_buildBookFromJSON_",
smalltalk.method({
selector: "buildBookFromJSON:",
fn: function (anArray) {
    var self = this;
    smalltalk.send(anArray, "_do_", [function (fileName) {return function ($rec) {smalltalk.send($rec, "_fullImageURL_", [smalltalk.send(smalltalk.send(self, "_fullImagesURL", []), "__comma", [fileName])]);return smalltalk.send($rec, "_thumbnailURL_", [smalltalk.send(smalltalk.send(self, "_thumbsURL", []), "__comma", [fileName])]);}(smalltalk.send(smalltalk.send(self, "_book", []), "_newPage", []));}]);
    return self;
}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_fullImagesURL",
smalltalk.method({
selector: "fullImagesURL",
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_baseURL", []), "__comma", [unescape("big/")]);
    return self;
}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_initMetadata_",
smalltalk.method({
selector: "initMetadata:",
fn: function (anArray) {
    var self = this;
    smalltalk.send(anArray, "_do_", [function (aJSObject) {var pageNo = nil;var page = nil;pageNo = aJSObject.pageNo;return ($receiver = pageNo) != nil && $receiver != undefined ? function () {page = smalltalk.send(smalltalk.send(self, "_book", []), "_pageAtFolio_", [pageNo]);return ($receiver = page) != nil && $receiver != undefined ? function () {return smalltalk.send(page, "_initMetadata_", [aJSObject]);}() : nil;}() : nil;}]);
    return self;
}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_links",
smalltalk.method({
selector: "links",
fn: function () {
    var self = this;
    return ($receiver = self['@links']) == nil || $receiver == undefined ? function () {return self['@links'] = smalltalk.send(smalltalk.Dictionary || Dictionary, "_new", []);}() : $receiver;
    return self;
}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_loadBookFromJSONOnSuccess_",
smalltalk.method({
selector: "loadBookFromJSONOnSuccess:",
fn: function (aBlock) {
    var self = this;
    (function ($rec) {smalltalk.send($rec, "_onSuccessDo_", [function (data) {smalltalk.send(self, "_buildBookFromJSON_", [data]);return smalltalk.send(self, "_onMetadataLoadedDo_", [function () {return smalltalk.send(aBlock, "_value_", [smalltalk.send(self, "_book", [])]);}]);}]);return smalltalk.send($rec, "_send", []);}(smalltalk.send(smalltalk.send(self, "_ajax", []), "_url_", [smalltalk.send(self, "_thumbsJSONURL", [])])));
    return self;
}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_loadBookOnSuccess_",
smalltalk.method({
selector: "loadBookOnSuccess:",
fn: function (aBlock) {
    var self = this;
    (function ($rec) {smalltalk.send($rec, "_onSuccessDo_", [function (data) {smalltalk.send(self, "_buildBookFromHTML_", [data]);return smalltalk.send(self, "_onMetadataLoadedDo_", [function () {return smalltalk.send(aBlock, "_value_", [smalltalk.send(self, "_book", [])]);}]);}]);return smalltalk.send($rec, "_send", []);}(smalltalk.send(smalltalk.Ajax || Ajax, "_url_", [smalltalk.send(self, "_thumbsURL", [])])));
    return self;
}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_metadataURL",
smalltalk.method({
selector: "metadataURL",
fn: function () {
    var self = this;
    return unescape("souvigny/souvigny.json");
    return self;
}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_onMetadataLoadedDo_",
smalltalk.method({
selector: "onMetadataLoadedDo:",
fn: function (aBlock) {
    var self = this;
    (function ($rec) {smalltalk.send($rec, "_onSuccessDo_", [function (data) {smalltalk.send(self, "_initMetadata_", [data]);return smalltalk.send(aBlock, "_value", []);}]);return smalltalk.send($rec, "_send", []);}(smalltalk.send(smalltalk.Ajax || Ajax, "_url_", [smalltalk.send(self, "_metadataURL", [])])));
    return self;
}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_parsePageNo_",
smalltalk.method({
selector: "parsePageNo:",
fn: function (aString) {
    var self = this;
    return ($receiver = smalltalk.send(aString, "_includesSubString_", ["r"])).klass === smalltalk.Boolean ? $receiver ? function () {return ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2]);}() : function () {return ($receiver = smalltalk.send(aString, "_includesSubString_", ["v"])).klass === smalltalk.Boolean ? $receiver ? function () {return ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1]);}() : function () {return aString;}() : smalltalk.send($receiver, "_ifTrue_ifFalse_", [function () {return ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1]);}, function () {return aString;}]);}() : smalltalk.send($receiver, "_ifTrue_ifFalse_", [function () {return ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2]);}, function () {return ($receiver = smalltalk.send(aString, "_includesSubString_", ["v"])).klass === smalltalk.Boolean ? $receiver ? function () {return ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1]);}() : function () {return aString;}() : smalltalk.send($receiver, "_ifTrue_ifFalse_", [function () {return ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1]);}, function () {return aString;}]);}]);
    return self;
}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_thumbsJSONURL",
smalltalk.method({
selector: "thumbsJSONURL",
fn: function () {
    var self = this;
    return unescape("souvigny/thumbs.json");
    return self;
}
}),
smalltalk.SouvignyLoader);

smalltalk.addMethod(
"_thumbsURL",
smalltalk.method({
selector: "thumbsURL",
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_baseURL", []), "__comma", [unescape("thumbs/")]);
    return self;
}
}),
smalltalk.SouvignyLoader);


smalltalk.addMethod(
"_bookClass",
smalltalk.method({
selector: "bookClass",
fn: function () {
    var self = this;
    return smalltalk.SouvignyBible || SouvignyBible;
    return self;
}
}),
smalltalk.SouvignyLoader.klass);


smalltalk.addClass('Book', smalltalk.Object, ['pages', 'title', 'width', 'height', 'downloadUrl'], 'AFI');
smalltalk.addMethod(
"_addPage_",
smalltalk.method({
selector: "addPage:",
fn: function (aPage) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_pages", []), "_add_", [aPage]);
    smalltalk.send(aPage, "_book_", [self]);
    return aPage;
    return self;
}
}),
smalltalk.Book);

smalltalk.addMethod(
"_downloadUrl",
smalltalk.method({
selector: "downloadUrl",
fn: function () {
    var self = this;
    return ($receiver = self['@downloadUrl']) == nil || $receiver == undefined ? function () {return self['@downloadUrl'] = "";}() : $receiver;
    return self;
}
}),
smalltalk.Book);

smalltalk.addMethod(
"_downloadUrl_",
smalltalk.method({
selector: "downloadUrl:",
fn: function (anUrl) {
    var self = this;
    self['@downloadUrl'] = anUrl;
    return self;
}
}),
smalltalk.Book);

smalltalk.addMethod(
"_height",
smalltalk.method({
selector: "height",
fn: function () {
    var self = this;
    return ($receiver = self['@height']) == nil || $receiver == undefined ? function () {return self['@height'] = 400;}() : $receiver;
    return self;
}
}),
smalltalk.Book);

smalltalk.addMethod(
"_height_",
smalltalk.method({
selector: "height:",
fn: function (anInteger) {
    var self = this;
    self['@height'] = anInteger;
    return self;
}
}),
smalltalk.Book);

smalltalk.addMethod(
"_newPage",
smalltalk.method({
selector: "newPage",
fn: function () {
    var self = this;
    return smalltalk.send(self, "_addPage_", [smalltalk.send(smalltalk.send(smalltalk.send(self, "_class", []), "_pageClass", []), "_new", [])]);
    return self;
}
}),
smalltalk.Book);

smalltalk.addMethod(
"_pageAt_",
smalltalk.method({
selector: "pageAt:",
fn: function (aNumber) {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_pages", []), "_at_", [aNumber]);
    return self;
}
}),
smalltalk.Book);

smalltalk.addMethod(
"_pageAt_do_",
smalltalk.method({
selector: "pageAt:do:",
fn: function (pageNo, aBlockWithArg) {
    var self = this;
    var page = nil;
    page = smalltalk.send(smalltalk.send(self, "_pages", []), "_at_ifAbsent_", [pageNo, function () {return nil;}]);
    ($receiver = page) != nil && $receiver != undefined ? function () {return smalltalk.send(aBlockWithArg, "_value_", [page]);}() : nil;
    return self;
}
}),
smalltalk.Book);

smalltalk.addMethod(
"_pageAt_ifAbsent_",
smalltalk.method({
selector: "pageAt:ifAbsent:",
fn: function (aNumber, aBlock) {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_pages", []), "_at_ifAbsent_", [aNumber, aBlock]);
    return self;
}
}),
smalltalk.Book);

smalltalk.addMethod(
"_pageNo_",
smalltalk.method({
selector: "pageNo:",
fn: function (aPage) {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_pages", []), "_indexOf_", [aPage]);
    return self;
}
}),
smalltalk.Book);

smalltalk.addMethod(
"_pages",
smalltalk.method({
selector: "pages",
fn: function () {
    var self = this;
    return ($receiver = self['@pages']) == nil || $receiver == undefined ? function () {return self['@pages'] = smalltalk.send(smalltalk.Array || Array, "_new", []);}() : $receiver;
    return self;
}
}),
smalltalk.Book);

smalltalk.addMethod(
"_pagesNo_do_",
smalltalk.method({
selector: "pagesNo:do:",
fn: function (anArray, aBlockWithArg) {
    var self = this;
    smalltalk.send(anArray, "_do_", [function (pageNo) {return smalltalk.send(self, "_pageAt_do_", [pageNo, aBlockWithArg]);}]);
    return self;
}
}),
smalltalk.Book);

smalltalk.addMethod(
"_pagesNo_to_do_",
smalltalk.method({
selector: "pagesNo:to:do:",
fn: function (start, end, aBlockWithArg) {
    var self = this;
    smalltalk.send(start, "_to_do_", [end, function (pageNo) {return smalltalk.send(self, "_pageAt_do_", [pageNo, aBlockWithArg]);}]);
    return self;
}
}),
smalltalk.Book);

smalltalk.addMethod(
"_pagesWithTitle",
smalltalk.method({
selector: "pagesWithTitle",
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_pages", []), "_reject_", [function (aPage) {return smalltalk.send(smalltalk.send(aPage, "_title", []), "_isEmpty", []);}]);
    return self;
}
}),
smalltalk.Book);

smalltalk.addMethod(
"_reset",
smalltalk.method({
selector: "reset",
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_pages", []), "_do_", [function (aPage) {return smalltalk.send(aPage, "_reset", []);}]);
    return self;
}
}),
smalltalk.Book);

smalltalk.addMethod(
"_size",
smalltalk.method({
selector: "size",
fn: function () {
    var self = this;
    return smalltalk.send(self['@pages'], "_size", []);
    return self;
}
}),
smalltalk.Book);

smalltalk.addMethod(
"_title",
smalltalk.method({
selector: "title",
fn: function () {
    var self = this;
    return self['@title'];
    return self;
}
}),
smalltalk.Book);

smalltalk.addMethod(
"_title_",
smalltalk.method({
selector: "title:",
fn: function (aString) {
    var self = this;
    self['@title'] = aString;
    return self;
}
}),
smalltalk.Book);

smalltalk.addMethod(
"_width",
smalltalk.method({
selector: "width",
fn: function () {
    var self = this;
    return ($receiver = self['@width']) == nil || $receiver == undefined ? function () {return self['@width'] = 300;}() : $receiver;
    return self;
}
}),
smalltalk.Book);

smalltalk.addMethod(
"_width_",
smalltalk.method({
selector: "width:",
fn: function (anInteger) {
    var self = this;
    self['@width'] = anInteger;
    return self;
}
}),
smalltalk.Book);


smalltalk.addMethod(
"_pageClass",
smalltalk.method({
selector: "pageClass",
fn: function () {
    var self = this;
    return smalltalk.Page || Page;
    return self;
}
}),
smalltalk.Book.klass);


smalltalk.addClass('SouvignyBible', smalltalk.Book, [], 'AFI');
smalltalk.addMethod(
"_pageAtFolio_",
smalltalk.method({
selector: "pageAtFolio:",
fn: function (aString) {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_pages", []), "_at_ifAbsent_", [smalltalk.send(self, "_parseFolioNo_", [aString]), function () {return nil;}]);
    return self;
}
}),
smalltalk.SouvignyBible);

smalltalk.addMethod(
"_parseFolioNo_",
smalltalk.method({
selector: "parseFolioNo:",
fn: function (aString) {
    var self = this;
    return ($receiver = smalltalk.send(aString, "_includesSubString_", ["r"])).klass === smalltalk.Boolean ? $receiver ? function () {return ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 5 : smalltalk.send($receiver, "__plus", [5]);}() : function () {return ($receiver = smalltalk.send(aString, "_includesSubString_", ["v"])).klass === smalltalk.Boolean ? $receiver ? function () {return ($receiver = ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1])).klass === smalltalk.Number ? $receiver + 5 : smalltalk.send($receiver, "__plus", [5]);}() : function () {return smalltalk.send(aString, "_asNumber", []);}() : smalltalk.send($receiver, "_ifTrue_ifFalse_", [function () {return ($receiver = ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1])).klass === smalltalk.Number ? $receiver + 5 : smalltalk.send($receiver, "__plus", [5]);}, function () {return smalltalk.send(aString, "_asNumber", []);}]);}() : smalltalk.send($receiver, "_ifTrue_ifFalse_", [function () {return ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 5 : smalltalk.send($receiver, "__plus", [5]);}, function () {return ($receiver = smalltalk.send(aString, "_includesSubString_", ["v"])).klass === smalltalk.Boolean ? $receiver ? function () {return ($receiver = ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1])).klass === smalltalk.Number ? $receiver + 5 : smalltalk.send($receiver, "__plus", [5]);}() : function () {return smalltalk.send(aString, "_asNumber", []);}() : smalltalk.send($receiver, "_ifTrue_ifFalse_", [function () {return ($receiver = ($receiver = ($receiver = smalltalk.send(smalltalk.send(aString, "_allButLast", []), "_asNumber", [])).klass === smalltalk.Number ? $receiver * 2 : smalltalk.send($receiver, "__star", [2])).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1])).klass === smalltalk.Number ? $receiver + 5 : smalltalk.send($receiver, "__plus", [5]);}, function () {return smalltalk.send(aString, "_asNumber", []);}]);}]);
    return self;
}
}),
smalltalk.SouvignyBible);

smalltalk.addMethod(
"_title",
smalltalk.method({
selector: "title",
fn: function () {
    var self = this;
    return "Bible de Souvigny";
    return self;
}
}),
smalltalk.SouvignyBible);


smalltalk.addMethod(
"_pageClass",
smalltalk.method({
selector: "pageClass",
fn: function () {
    var self = this;
    return smalltalk.SouvignyPage || SouvignyPage;
    return self;
}
}),
smalltalk.SouvignyBible.klass);


smalltalk.addClass('Cycle', smalltalk.Object, ['elements', 'counter'], 'AFI');
smalltalk.addMethod(
"_elements_",
smalltalk.method({
selector: "elements:",
fn: function (anArray) {
    var self = this;
    self['@elements'] = anArray;
    return self;
}
}),
smalltalk.Cycle);

smalltalk.addMethod(
"_initialize",
smalltalk.method({
selector: "initialize",
fn: function () {
    var self = this;
    self['@counter'] = -1;
    return self;
}
}),
smalltalk.Cycle);

smalltalk.addMethod(
"_next",
smalltalk.method({
selector: "next",
fn: function () {
    var self = this;
    self['@counter'] = ($receiver = self['@counter']).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1]);
    return smalltalk.send(self['@elements'], "_at_", [($receiver = smalltalk.send(self['@counter'], "_\\\\", [smalltalk.send(self['@elements'], "_size", [])])).klass === smalltalk.Number ? $receiver + 1 : smalltalk.send($receiver, "__plus", [1])]);
    return self;
}
}),
smalltalk.Cycle);


smalltalk.addMethod(
"_with_",
smalltalk.method({
selector: "with:",
fn: function (anArray) {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_new", []), "_elements_", [anArray]);
    return self;
}
}),
smalltalk.Cycle.klass);


smalltalk.addClass('ListFilter', smalltalk.Object, ['book', 'announcer', 'jqueryInput', 'jqueryList'], 'AFI');
smalltalk.addMethod(
"_filter_withInput_",
smalltalk.method({
selector: "filter:withInput:",
fn: function (aJQueryList, aJQueryInput) {
    var self = this;
    self['@jqueryList'] = aJQueryList;
    self['@jqueryInput'] = aJQueryInput;
    smalltalk.send(self['@jqueryInput'], "_keyup_", [function () {return smalltalk.send(self, "_filterListWithInputString", []);}]);
    return self;
}
}),
smalltalk.ListFilter);

smalltalk.addMethod(
"_filterListWithInputString",
smalltalk.method({
selector: "filterListWithInputString",
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
}
}),
smalltalk.ListFilter);


smalltalk.addMethod(
"_filter_withInput_",
smalltalk.method({
selector: "filter:withInput:",
fn: function (aJQueryList, aJQueryInput) {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_new", []), "_filter_withInput_", [aJQueryList, aJQueryInput]);
    return self;
}
}),
smalltalk.ListFilter.klass);


smalltalk.addClass('Page', smalltalk.Object, ['brush', 'imgBrush', 'fullImageURL', 'thumbnailURL', 'description', 'title', 'rendered', 'foliono', 'navigatorThumbnailURL', 'book', 'downloadURL'], 'AFI');
smalltalk.addMethod(
"_book",
smalltalk.method({
selector: "book",
fn: function () {
    var self = this;
    return self['@book'];
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_book_",
smalltalk.method({
selector: "book:",
fn: function (aBook) {
    var self = this;
    self['@book'] = aBook;
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_brush",
smalltalk.method({
selector: "brush",
fn: function () {
    var self = this;
    return self['@brush'];
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_brush_",
smalltalk.method({
selector: "brush:",
fn: function (aBrush) {
    var self = this;
    self['@brush'] = aBrush;
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_description",
smalltalk.method({
selector: "description",
fn: function () {
    var self = this;
    return ($receiver = self['@description']) == nil || $receiver == undefined ? function () {return self['@description'] = "";}() : $receiver;
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_description_",
smalltalk.method({
selector: "description:",
fn: function (aString) {
    var self = this;
    self['@description'] = aString;
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_downloadURL",
smalltalk.method({
selector: "downloadURL",
fn: function () {
    var self = this;
    return ($receiver = self['@downloadURL']) == nil || $receiver == undefined ? function () {return self['@downloadURL'] = "";}() : $receiver;
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_downloadURL_",
smalltalk.method({
selector: "downloadURL:",
fn: function (aString) {
    var self = this;
    self['@downloadURL'] = aString;
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_foliono",
smalltalk.method({
selector: "foliono",
fn: function () {
    var self = this;
    return ($receiver = self['@foliono']) == nil || $receiver == undefined ? function () {return self['@foliono'] = "";}() : $receiver;
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_foliono_",
smalltalk.method({
selector: "foliono:",
fn: function (aString) {
    var self = this;
    self['@foliono'] = aString;
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_fullImageURL",
smalltalk.method({
selector: "fullImageURL",
fn: function () {
    var self = this;
    return ($receiver = self['@fullImageURL']) == nil ||
        $receiver == undefined ? function () {return self['@fullImageURL'] = "";}() : $receiver;
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_fullImageURL_",
smalltalk.method({
selector: "fullImageURL:",
fn: function (aString) {
    var self = this;
    self['@fullImageURL'] = aString;
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_initMetadata_",
smalltalk.method({
selector: "initMetadata:",
fn: function (aJSObject) {
    var self = this;
    self['@description'] = aJSObject.description;
    self['@title'] = aJSObject.book;
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_isRendered",
smalltalk.method({
selector: "isRendered",
fn: function () {
    var self = this;
    return ($receiver = self['@rendered']) == nil || $receiver == undefined ? function () {return self['@rendered'] = false;}() : $receiver;
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_navigatorThumbnailURL",
smalltalk.method({
selector: "navigatorThumbnailURL",
fn: function () {
    var self = this;
    return ($receiver = self['@navigatorThumbnailURL']) == nil ||
        $receiver == undefined ? function () {return self['@navigatorThumbnailURL'] = "";}() : $receiver;
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_navigatorThumbnailURL_",
smalltalk.method({
selector: "navigatorThumbnailURL:",
fn: function (aString) {
    var self = this;
    self['@navigatorThumbnailURL'] = aString;
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_nextPage",
smalltalk.method({
selector: "nextPage",
fn: function () {
    var self = this;
    var $1;
    $1 = smalltalk.send(smalltalk.send(self['@book'], "_pages", []), "_at_ifAbsent_", [smalltalk.send(smalltalk.send(self, "_pageNo", []), "__plus", [1]), function () {return self;}]);
    return $1;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_pageNo",
smalltalk.method({
selector: "pageNo",
fn: function () {
    var self = this;
    return smalltalk.send(self['@book'], "_pageNo_", [self]);
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_previousPage",
smalltalk.method({
selector: "previousPage",
fn: function () {
    var self = this;
    var $1;
    $1 = smalltalk.send(smalltalk.send(self['@book'], "_pages", []), "_at_ifAbsent_", [smalltalk.send(smalltalk.send(self, "_pageNo", []), "__minus", [1]), function () {return self;}]);
    return $1;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_printString",
smalltalk.method({
selector: "printString",
fn: function () {
    var self = this;
    return smalltalk.send(smalltalk.String || String, "_streamContents_", [function (aStream) {return function ($rec) {smalltalk.send($rec, "_nextPutAll_", [smalltalk.send(self, "_printString", [], smalltalk.Object)]);smalltalk.send($rec, "_nextPutAll_", [unescape("%28")]);smalltalk.send($rec, "_nextPutAll_", [smalltalk.send(self, "_title", [])]);return smalltalk.send($rec, "_nextPutAll_", [unescape("%29")]);}(aStream);}]);
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_render",
smalltalk.method({
selector: "render",
fn: function () {
    var self = this;
    smalltalk.send(self, "_renderWidth_height_", [smalltalk.send(self, "_width", []), smalltalk.send(self, "_height", [])]);
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_renderWidth_height_",
smalltalk.method({
selector: "renderWidth:height:",
fn: function (width, height) {
    var self = this;
    var $1, $2, $3;
    $1 = smalltalk.send(self, "_isRendered", []);
    if (!smalltalk.assert($1)) {
        self['@rendered'] = true;
        self['@rendered'];
        smalltalk.send(self['@brush'], "_contents_", [function (html) {$2 = smalltalk.send(html, "_img", []);smalltalk.send($2, "_width_", [width]);smalltalk.send($2, "_height_", [height]);$3 = smalltalk.send($2, "_src_", [self['@thumbnailURL']]);self['@imgBrush'] = $3;return self['@imgBrush'];}]);
    }
    smalltalk.send(smalltalk.send(self['@imgBrush'], "_asJQuery", []), "_show", []);
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_reset",
smalltalk.method({
selector: "reset",
fn: function () {
    var self = this;
    return self['@rendered'] = false;
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_thumbnailURL",
smalltalk.method({
selector: "thumbnailURL",
fn: function () {
    var self = this;
    return ($receiver = self['@thumbnailURL']) == nil ||
        $receiver == undefined ? function () {return self['@thumbnailURL'] = "";}() : $receiver;
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_thumbnailURL_",
smalltalk.method({
selector: "thumbnailURL:",
fn: function (aString) {
    var self = this;
    self['@thumbnailURL'] = aString;
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_title",
smalltalk.method({
selector: "title",
fn: function () {
    var self = this;
    return ($receiver = self['@title']) == nil || $receiver == undefined ? function () {return self['@title'] = "";}() : $receiver;
    return self;
}
}),
smalltalk.Page);

smalltalk.addMethod(
"_title_",
smalltalk.method({
selector: "title:",
fn: function (aString) {
    var self = this;
    self['@title'] = aString;
    return self;
}
}),
smalltalk.Page);



smalltalk.addClass('SouvignyPage', smalltalk.Page, ['book', 'icon', 'letter', 'subject'], 'AFI');
smalltalk.addMethod(
"_initMetadata_",
smalltalk.method({
selector: "initMetadata:",
fn: function (aJSObject) {
    var self = this;
    self['@book'] = aJSObject.book;
    self['@icon'] = aJSObject.icon;
    self['@letter'] = aJSObject.letter;
    self['@subject'] = aJSObject.subject;
    self['@description'] = aJSObject.description;
    self['@title'] = smalltalk.send(smalltalk.String || String, "_streamContents_", [function (aStream) {smalltalk.send(aStream, "_nextPutAll_", [self['@book']]);smalltalk.send(self['@icon'], "_ifNotEmpty_", [function () {return smalltalk.send(aStream, "_nextPutAll_", [smalltalk.send(unescape("%20-%20"), "__comma", [self['@icon']])]);}]);return smalltalk.send(self['@subject'], "_ifNotEmpty_", [function () {return smalltalk.send(aStream, "_nextPutAll_", [smalltalk.send(unescape("%20-%20"), "__comma", [self['@subject']])]);}]);}]);
    return self;
}
}),
smalltalk.SouvignyPage);



smalltalk.addClass('PageChangeAnnouncement', smalltalk.Object, ['page'], 'AFI');
smalltalk.addMethod(
"_page",
smalltalk.method({
selector: "page",
fn: function () {
    var self = this;
    return self['@page'];
    return self;
}
}),
smalltalk.PageChangeAnnouncement);

smalltalk.addMethod(
"_page_",
smalltalk.method({
selector: "page:",
fn: function (aPage) {
    var self = this;
    self['@page'] = aPage;
    return self;
}
}),
smalltalk.PageChangeAnnouncement);


smalltalk.addMethod(
"_page_",
smalltalk.method({
selector: "page:",
fn: function (aPage) {
    var self = this;
    return smalltalk.send(smalltalk.send(self, "_new", []), "_page_", [aPage]);
    return self;
}
}),
smalltalk.PageChangeAnnouncement.klass);


smalltalk.addClass('PageWidget', smalltalk.Widget, ['page', 'inControl', 'outControl', 'fitControl', 'statusControl', 'zeroControl', 'closeControl', 'closeBlock', 'rotateRightControl', 'rotation', 'downloadImageControl'], 'AFI');
smalltalk.addMethod(
"_close",
smalltalk.method({
selector: "close",
fn: function () {
    var self = this;
    smalltalk.send(self['@closeBlock'], "_value", []);
    return self;
}
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_initIViewer_",
smalltalk.method({
selector: "initIViewer:",
fn: function (aViewer) {
    var self = this;
    smalltalk.send(self['@inControl'], "_onClick_", [function () {return aViewer.zoom_by(1);}]);
    smalltalk.send(self['@outControl'], "_onClick_", [function () {return aViewer.zoom_by(-1);}]);
    smalltalk.send(self['@fitControl'], "_onClick_", [function () {return smalltalk.send(aViewer, "_fit", []);}]);
    smalltalk.send(self['@zeroControl'], "_onClick_", [function () {return aViewer.set_zoom(100);}]);
    smalltalk.send(self['@rotateRightControl'], "_onClick_", [function () {return smalltalk.send(self, "_rotateRight", []);}]);
    smalltalk.send(self['@downloadImageControl'], "_onClick_", [function () {return smalltalk.send(typeof window == "undefined" ? nil : window, "_open_", [smalltalk.send(self['@page'], "_downloadURL", [])]);}]);
    return self;
}
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_onCloseDo_",
smalltalk.method({
selector: "onCloseDo:",
fn: function (aBlock) {
    var self = this;
    self['@closeBlock'] = aBlock;
    return self;
}
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_page_",
smalltalk.method({
selector: "page:",
fn: function (aPage) {
    var self = this;
    self['@page'] = aPage;
    return self;
}
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_renderControlsOn_",
smalltalk.method({
selector: "renderControlsOn:",
fn: function (html) {
    var self = this;
    var $1, $3, $4, $2;
    var addControl;
    $1 = smalltalk.send(html, "_div", []);
    smalltalk.send($1, "_class_", ["controls"]);
    $2 = smalltalk.send($1, "_with_", [function () {addControl = function (name, helpText) {$3 = smalltalk.send(html, "_div", []);smalltalk.send($3, "_with_", [function () {smalltalk.send(smalltalk.send(html, "_div", []), "_class_", [smalltalk.send(smalltalk.send("iviewer_zoom_", "__comma", [name]), "__comma", [" iviewer_common iviewer_button"])]);return smalltalk.send(html, "_div_", [helpText]);}]);$4 = smalltalk.send($3, "_yourself", []);return $4;};addControl;self['@closeControl'] = smalltalk.send(addControl, "_value_value_", ["close", "Fermer"]);self['@closeControl'];smalltalk.send(self['@closeControl'], "_onClick_", [function () {return smalltalk.send(self, "_close", []);}]);self['@inControl'] = smalltalk.send(addControl, "_value_value_", ["in", "Agrandir"]);self['@inControl'];self['@outControl'] = smalltalk.send(addControl, "_value_value_", ["out", "R\xE9duire"]);self['@outControl'];self['@zeroControl'] = smalltalk.send(addControl, "_value_value_", ["zero", "Taille originale"]);self['@zeroControl'];self['@fitControl'] = smalltalk.send(addControl, "_value_value_", ["fit", "Taille adapt\xE9e"]);self['@fitControl'];self['@rotateRightControl'] = smalltalk.send(addControl, "_value_value_", ["rotate_right", "Tourner"]);self['@rotateRightControl'];self['@downloadImageControl'] = smalltalk.send(addControl, "_value_value_", ["download_image", "T\xE9l\xE9charger"]);return self['@downloadImageControl'];}]);
    return self;
}
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_renderOn_",
smalltalk.method({
selector: "renderOn:",
fn: function (html) {
    var self = this;
    var $1, $2, $3, $4, $5, $6;
    var iViewer;
    smalltalk.send(html, "_style_", [smalltalk.send(self, "_style", [])]);
    smalltalk.send(self, "_renderControlsOn_", [html]);
    $1 = smalltalk.send(html, "_div", []);
    smalltalk.send($1, "_class_", ["iviewer"]);
    $2 = smalltalk.send($1, "_asJQuery", []);
    iViewer = $2;
    smalltalk.send(smalltalk.send(self['@page'], "_description", []), "_ifNotEmpty_", [function () {return smalltalk.send(iViewer, "_addClass_", ["iviewer_with_text"]);}]);
    $3 = smalltalk.send(smalltalk.HashedCollection || HashedCollection, "_new", []);
    smalltalk.send($3, "_at_put_", ["src", smalltalk.send(self['@page'], "_fullImageURL", [])]);
    smalltalk.send($3, "_at_put_", ["zoom", "fit"]);
    smalltalk.send($3, "_at_put_", ["zoom_min", 10]);
    smalltalk.send($3, "_at_put_", ["zoom_max", 400]);
    smalltalk.send($3, "_at_put_", ["ui_disabled", true]);
    smalltalk.send($3, "_at_put_", ["initCallback", function (aViewer) {return smalltalk.send(self, "_initIViewer_", [aViewer]);}]);
    $4 = smalltalk.send($3, "_yourself", []);
    smalltalk.send(iViewer, "_iviewer_", [$4]);
    $5 = smalltalk.send(html, "_div", []);
    smalltalk.send($5, "_class_", ["page-desc"]);
    $6 = smalltalk.send($5, "_asJQuery", []);
    smalltalk.send($6, "_html_", [smalltalk.send(self['@page'], "_description", [])]);
    smalltalk.send(smalltalk.send(html, "_div", []), "_class_", ["clear"]);
    return self;
}
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_rotateRight",
smalltalk.method({
selector: "rotateRight",
fn: function () {
    var self = this;
    var rotationDeg = nil;
    self['@rotation'] = ($receiver = ($receiver = self['@rotation']) == nil || $receiver == undefined ? function () {return 0;}() : $receiver).klass === smalltalk.Number ? $receiver + 90 : smalltalk.send($receiver, "__plus", [90]);
    rotationDeg = smalltalk.send(smalltalk.send(unescape("rotate%28"), "__comma", [smalltalk.send(self['@rotation'], "_asString", [])]), "__comma", [unescape("deg%29")]);
    (function ($rec) {smalltalk.send($rec, "_css_value_", [unescape("-ms-transform"), rotationDeg]);smalltalk.send($rec, "_css_value_", [unescape("-o-transform"), rotationDeg]);smalltalk.send($rec, "_css_value_", [unescape("-moz-transform"), rotationDeg]);return smalltalk.send($rec, "_css_value_", [unescape("-webkit-transform"), rotationDeg]);}(smalltalk.send(".iviewer img", "_asJQuery", [])));
    return self;
}
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_style",
smalltalk.method({
selector: "style",
fn: function () {
    var self = this;
    return "\t.b-zoom .controls {\n\t\t\t  height: auto;\n\t\t\t  padding: 4px;\n\t\t\t  margin: 0 4px;\n\t\t\t  background-color: rgb(200,200,200);\n\t\t\t  background-color: rgba(200,200,200,0.8);\n\t\t\t  overflow: hidden;\n\t\t\t  float: right;\n\t\t\t  position: absolute;\n\t\t\t  *position: relative;\n\t\t\t  z-index: 1;\n\t\t\t  text-align: center;\n\t\t\t  width: 114px;\n              right: 0px;\n\t\t\t}\n            \n            .b-zoom .controls>div {\n            \theight: 28px;\n                border-radius: 5px;\n            }\n            \n             .b-zoom .controls>div:hover {\n             \t\tbackground-color: rgba(250,250,250, 0.8);\n                    cursor: pointer;\n            }\n            \n            \n             .b-zoom .controls  .iviewer_button {\n             \tmargin: 0px 8px 0px 0px;\n                float: left;\n             }\n             \n             .b-zoom .controls  .iviewer_button + div{\n             \tmargin-top: 4px;\n                text-align: left;\n             }\n";
}
}),
smalltalk.PageWidget);

smalltalk.addMethod(
"_updateZoomStatus_",
smalltalk.method({
selector: "updateZoomStatus:",
fn: function (newZoom) {
    var self = this;
    smalltalk.send(self['@statusControl'], "_contents_", [smalltalk.send("x", "__comma", [smalltalk.send(($receiver = newZoom).klass === smalltalk.Number ? $receiver / 100 : smalltalk.send($receiver, "__slash", [100]), "_printShowingDecimalPlaces_", [1])])]);
    return self;
}
}),
smalltalk.PageWidget);



