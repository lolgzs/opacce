smalltalk.addPackage('AFI-Tests', {});
smalltalk.addClass('CycleTest', smalltalk.TestCase, [], 'AFI-Tests');
smalltalk.addMethod(
unescape('_testCycleWithTwoElements'),
smalltalk.method({
selector: unescape('testCycleWithTwoElements'),
category: 'tests',
fn: function (){
var self=this;
var cycle=nil;
(cycle=smalltalk.send((smalltalk.Cycle || Cycle), "_with_", [["one", "two"]]));
smalltalk.send(self, "_assert_equals_", ["one", smalltalk.send(cycle, "_next", [])]);
smalltalk.send(self, "_assert_equals_", ["two", smalltalk.send(cycle, "_next", [])]);
smalltalk.send(self, "_assert_equals_", ["one", smalltalk.send(cycle, "_next", [])]);
smalltalk.send(self, "_assert_equals_", ["two", smalltalk.send(cycle, "_next", [])]);
return self;},
args: [],
source: unescape('testCycleWithTwoElements%0A%09%7Ccycle%7C%0A%09cycle%20%3A%3D%20Cycle%20with%3A%20%23%28%27one%27%20%27two%27%29.%0A%09self%20assert%3A%20%27one%27%20equals%3A%20cycle%20next.%0A%09self%20assert%3A%20%27two%27%20equals%3A%20cycle%20next.%0A%09self%20assert%3A%20%27one%27%20equals%3A%20cycle%20next.%0A%09self%20assert%3A%20%27two%27%20equals%3A%20cycle%20next.%0A%09'),
messageSends: ["with:", "assert:equals:", "next"],
referencedClasses: ["Cycle"]
}),
smalltalk.CycleTest);

smalltalk.addMethod(
unescape('_testCycleWithFourElements'),
smalltalk.method({
selector: unescape('testCycleWithFourElements'),
category: 'tests',
fn: function (){
var self=this;
var cycle=nil;
(cycle=smalltalk.send((smalltalk.Cycle || Cycle), "_with_", [["one", "two", "three", "four"]]));
smalltalk.send((3), "_timesRepeat_", [(function(){smalltalk.send(self, "_assert_equals_", ["one", smalltalk.send(cycle, "_next", [])]);smalltalk.send(self, "_assert_equals_", ["two", smalltalk.send(cycle, "_next", [])]);smalltalk.send(self, "_assert_equals_", ["three", smalltalk.send(cycle, "_next", [])]);return smalltalk.send(self, "_assert_equals_", ["four", smalltalk.send(cycle, "_next", [])]);})]);
return self;},
args: [],
source: unescape('testCycleWithFourElements%0A%09%7Ccycle%7C%0A%09cycle%20%3A%3D%20Cycle%20with%3A%20%23%28%27one%27%20%27two%27%20%27three%27%20%27four%27%29.%0A%093%20timesRepeat%3A%20%5B%0A%09%09self%20assert%3A%20%27one%27%20equals%3A%20cycle%20next.%0A%09%09self%20assert%3A%20%27two%27%20equals%3A%20cycle%20next.%0A%09%09self%20assert%3A%20%27three%27%20equals%3A%20cycle%20next.%0A%09%09self%20assert%3A%20%27four%27%20equals%3A%20cycle%20next%20%5D.%0A%09'),
messageSends: ["with:", "timesRepeat:", "assert:equals:", "next"],
referencedClasses: ["Cycle"]
}),
smalltalk.CycleTest);



smalltalk.addClass('SouvignyBibleTest', smalltalk.TestCase, ['bible'], 'AFI-Tests');
smalltalk.addMethod(
unescape('_setUp'),
smalltalk.method({
selector: unescape('setUp'),
category: 'running',
fn: function () {
    var self = this;
    self['@bible'] = smalltalk.send(smalltalk.SouvignyBible || SouvignyBible, "_new", []);
    return self;
},
args: [],
source: unescape('setUp%0A%09bible%20%3A%3D%20SouvignyBible%20new'),
messageSends: ["new"],
referencedClasses: []
}),
smalltalk.SouvignyBibleTest);

smalltalk.addMethod(
unescape('_testFolioOneShouldReturnPageOne'),
smalltalk.method({
selector: unescape('testFolioOneShouldReturnPageOne'),
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [1, smalltalk.send(self['@bible'], "_parseFolioNo_", ["1"])]);
    return self;
},
args: [],
source: unescape('testFolioOneShouldReturnPageOne%0A%09self%20assert%3A%201%20equals%3A%20%28bible%20parseFolioNo%3A%20%271%27%29'),
messageSends: ["assert:equals:", "parseFolioNo:"],
referencedClasses: []
}),
smalltalk.SouvignyBibleTest);

smalltalk.addMethod(
unescape('_testFolio150VShouldReturnPage306'),
smalltalk.method({
selector: unescape('testFolio150VShouldReturnPage306'),
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [306, smalltalk.send(self['@bible'], "_parseFolioNo_", ["150v"])]);
    return self;
},
args: [],
source: unescape('testFolio150VShouldReturnPage306%0A%09self%20assert%3A%20306%20equals%3A%28bible%20parseFolioNo%3A%20%27150v%27%29.'),
messageSends: ["assert:equals:", "parseFolioNo:"],
referencedClasses: []
}),
smalltalk.SouvignyBibleTest);

smalltalk.addMethod(
unescape('_testFolio151RShouldReturnPage307'),
smalltalk.method({
selector: unescape('testFolio151RShouldReturnPage307'),
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [307, smalltalk.send(self['@bible'], "_parseFolioNo_", ["151r"])]);
    return self;
},
args: [],
source: unescape('testFolio151RShouldReturnPage307%0A%09self%20assert%3A%20307%20equals%3A%28bible%20parseFolioNo%3A%20%27151r%27%29.'),
messageSends: ["assert:equals:", "parseFolioNo:"],
referencedClasses: []
}),
smalltalk.SouvignyBibleTest);



smalltalk.addClass('BibNumAlbumTintinTest', smalltalk.TestCase, ['bible', 'album', 'container', 'ajax'], 'AFI-Tests');
smalltalk.addMethod(
unescape('_setUp'),
smalltalk.method({
selector: unescape('setUp'),
category: 'running',
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(typeof window == "undefined" ? nil : window, "_location", []), "_hash_", [" "]);
    self['@ajax'] = smalltalk.send(smalltalk.AMockWrapper || AMockWrapper, "_on_", [smalltalk.send(smalltalk.Ajax || Ajax, "_new", [])]);
    smalltalk.send(self['@ajax'], "_onMessage_answer_", ["send", self['@ajax']]);
    self['@container'] = smalltalk.send(smalltalk.send(unescape("%3Cdiv%3E%3C/div%3E"), "_asJQuery", []), "_width_", [500]);
    self['@album'] = function ($rec) {smalltalk.send($rec, "_url_", [unescape("/bib-numerique/album/id/2.json")]);smalltalk.send($rec, "_scriptsRoot_", [unescape("http%3A//localhost/afi-opac3/amber/afi/souvigny/")]);smalltalk.send($rec, "_ajax_", [self['@ajax']]);smalltalk.send($rec, "_container_", [self['@container']]);return smalltalk.send($rec, "_load", []);}(smalltalk.send(smalltalk.BibNumAlbum || BibNumAlbum, "_new", []));
    smalltalk.send(smalltalk.send(smalltalk.send(self['@ajax'], "_options", []), "_at_", ["success"]), "_value_", [smalltalk.send(typeof jQuery == "undefined" ? nil : jQuery, "_parseJSON_", [smalltalk.send(self, "_tintinJSON", [])])]);
    return self;
},
args: [],
source: unescape('setUp%0A%09window%20location%20hash%3A%20%27%20%27.%0A%09ajax%20%3A%3D%20AMockWrapper%20on%3A%20Ajax%20new.%0A%09ajax%20onMessage%3A%20%27send%27%20answer%3A%20ajax.%0A%0A%09container%20%3A%3D%20%27%3Cdiv%3E%3C/div%3E%27%20asJQuery%20width%3A%20500.%0A%0A%09album%20%3A%3D%20BibNumAlbum%20new%20%0A%09%09url%3A%20%27/bib-numerique/album/id/2.json%27%3B%0A%09%09scriptsRoot%3A%20%27http%3A//localhost/afi-opac3/amber/afi/souvigny/%27%3B%0A%09%09ajax%3A%20ajax%3B%0A%09%09container%3A%20container%3B%20%0A%09%09load.%0A%0A%09%28ajax%20options%20at%3A%20%27success%27%29%20%20value%3A%20%28jQuery%20parseJSON%3A%20self%20tintinJSON%29.'),
messageSends: ["hash:", "location", "on:", "new", "onMessage:answer:", "width:", "asJQuery", "url:", "scriptsRoot:", "ajax:", "container:", "load", "value:", "at:", "options", "parseJSON:", "tintinJSON"],
referencedClasses: ["AMockWrapper", "Ajax", "BibNumAlbum"]
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testDivBkWidgetShouldBePresent'),
smalltalk.method({
selector: unescape('testDivBkWidgetShouldBePresent'),
category: 'tests',
fn: function () {
    var self = this;
    var bkWidget = nil;
    bkWidget = smalltalk.send(self['@container'], "_children_", [unescape(".bk-widget")]);
    smalltalk.send(self, "_assert_", [0 < smalltalk.send(bkWidget, "_length", [])]);
    return self;
},
args: [],
source: unescape('testDivBkWidgetShouldBePresent%0A%09%7CbkWidget%7C%0A%09bkWidget%20%3A%3D%20container%20children%3A%20%27.bk-widget%27.%0A%09self%20assert%3A%20%280%20%3C%20bkWidget%20length%29.'),
messageSends: ["children:", "assert:", unescape("%3C"), "length"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testDivBookShouldBePresent'),
smalltalk.method({
selector: unescape('testDivBookShouldBePresent'),
category: 'tests',
fn: function () {
    var self = this;
    var book = nil;
    book = smalltalk.send(self['@container'], "_find_", [".book"]);
    smalltalk.send(self, "_assert_", [0 < smalltalk.send(book, "_length", [])]);
    return self;
},
args: [],
source: unescape('testDivBookShouldBePresent%0A%09%7Cbook%7C%0A%09book%20%3A%3D%20container%20find%3A%20%27.book%27.%0A%09self%20assert%3A%20%280%20%3C%20book%20length%29.'),
messageSends: ["find:", "assert:", unescape("%3C"), "length"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_tearDown'),
smalltalk.method({
selector: unescape('tearDown'),
category: 'running',
fn: function () {
    var self = this;
    smalltalk.send(self['@container'], "_remove", []);
    return self;
},
args: [],
source: unescape('tearDown%0A%09container%20remove.'),
messageSends: ["remove"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testBookletPluginShouldBeLoaded'),
smalltalk.method({
selector: unescape('testBookletPluginShouldBeLoaded'),
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_", [smalltalk.send(smalltalk.send(smalltalk.send("body", "_asJQuery", []), "_at_", ["booklet"]), "_notNil", [])]);
    return self;
},
args: [],
source: unescape('testBookletPluginShouldBeLoaded%0A%09self%20assert%3A%20%28%27body%27%20asJQuery%20at%3A%20%27booklet%27%29%20notNil%20'),
messageSends: ["assert:", "notNil", "at:", "asJQuery"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testBookletJSShouldBeLoadedOnce'),
smalltalk.method({
selector: unescape('testBookletJSShouldBeLoadedOnce'),
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [1, smalltalk.send(smalltalk.send(smalltalk.send("head", "_asJQuery", []), "_find_", [unescape("script%5Bsrc*%3D%22booklet.1.2.0.min.js%22%5D")]), "_length", [])]);
    return self;
},
args: [],
source: unescape('testBookletJSShouldBeLoadedOnce%0A%09self%20assert%3A%201%20equals%3A%20%28%20%27head%27%20asJQuery%20find%3A%20%27script%5Bsrc*%3D%22booklet.1.2.0.min.js%22%5D%27%29%20length%0A%09'),
messageSends: ["assert:equals:", "length", "find:", "asJQuery"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_tintinJSON'),
smalltalk.method({
selector: unescape('tintinJSON'),
category: 'json',
fn: function (){
var self=this;
return unescape("%7B%22album%22%3A%20%7B%09%22id%22%3A2%2C%20%0A%09%09%09%09%09%22titre%22%3A%22Tintin%20et%20Milou%22%2C%20%0A%09%09%09%09%09%22description%22%3A%22The%20real%20story%20of%20Tintin%22%2C%0A%09%09%09%09%09%22width%22%3A%20400%2C%0A%09%09%09%09%09%22height%22%3A%20300%2C%0A%09%09%09%09%09%22download_url%22%3A%20%22http%3A//localhost/pdf/2%22%2C%0A%09%09%09%09%09%22ressources%22%3A%5B%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%7B%09%22id%22%3A12%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22titre%22%3A%20%22Origins%22%2C%0A%09%09%09%09%09%09%09%09%09%09%09%09%22foliono%22%3A%20%2212R%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22link_to%22%3A%22%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22description%22%3A%22Created%20in%201929%20by%20Herge%22%20%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22thumbnail%22%3A%22userfiles/album/2/thumbs/media/1.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22navigator_thumbnail%22%3A%22userfiles/album/2/thumbs/media/1_small.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22original%22%3A%22bib-numerique/get-resource/id/1.jpg%22%7D%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%7B%09%22id%22%3A13%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22titre%22%3A%20%22Haddock%22%2C%0A%09%09%09%09%09%09%09%09%09%09%09%09%22foliono%22%3A%20%22XX%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22link_to%22%3A%22%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22description%22%3A%22Captain%20living%20in%20Moulinsard%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22thumbnail%22%3A%22userfiles/album/2/thumbs/media/2.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22navigator_thumbnail%22%3A%22userfiles/album/2/thumbs/media/2_small.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22original%22%3A%22bib-numerique/get-resource/id/2.jpg%22%20%7D%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%5D%0A%09%09%09%09%7D%20%7D");
return self;},
args: [],
source: unescape('tintinJSON%0A%09%5E%20%20%20%27%7B%22album%22%3A%20%7B%09%22id%22%3A2%2C%20%0A%09%09%09%09%09%22titre%22%3A%22Tintin%20et%20Milou%22%2C%20%0A%09%09%09%09%09%22description%22%3A%22The%20real%20story%20of%20Tintin%22%2C%0A%09%09%09%09%09%22width%22%3A%20400%2C%0A%09%09%09%09%09%22height%22%3A%20300%2C%0A%09%09%09%09%09%22download_url%22%3A%20%22http%3A//localhost/pdf/2%22%2C%0A%09%09%09%09%09%22ressources%22%3A%5B%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%7B%09%22id%22%3A12%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22titre%22%3A%20%22Origins%22%2C%0A%09%09%09%09%09%09%09%09%09%09%09%09%22foliono%22%3A%20%2212R%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22link_to%22%3A%22%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22description%22%3A%22Created%20in%201929%20by%20Herge%22%20%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22thumbnail%22%3A%22userfiles/album/2/thumbs/media/1.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22navigator_thumbnail%22%3A%22userfiles/album/2/thumbs/media/1_small.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22original%22%3A%22bib-numerique/get-resource/id/1.jpg%22%7D%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%7B%09%22id%22%3A13%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22titre%22%3A%20%22Haddock%22%2C%0A%09%09%09%09%09%09%09%09%09%09%09%09%22foliono%22%3A%20%22XX%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22link_to%22%3A%22%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22description%22%3A%22Captain%20living%20in%20Moulinsard%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22thumbnail%22%3A%22userfiles/album/2/thumbs/media/2.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22navigator_thumbnail%22%3A%22userfiles/album/2/thumbs/media/2_small.jpg%22%2C%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%22original%22%3A%22bib-numerique/get-resource/id/2.jpg%22%20%7D%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%5D%0A%09%09%09%09%7D%20%7D%27.'),
messageSends: [],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testShouldContainsFourPages'),
smalltalk.method({
selector: unescape('testShouldContainsFourPages'),
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [4, smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-page")]), "_length", [])]);
    return self;
},
args: [],
source: unescape('testShouldContainsFourPages%0A%09%222%20pages%20+%20covers%22%0A%09self%20assert%3A%204%20equals%3A%20%28container%20find%3A%20%27.b-page%27%29%20length'),
messageSends: ["assert:equals:", "length", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testFirstPageImageShouldLinkToOneDotJpg'),
smalltalk.method({
selector: unescape('testFirstPageImageShouldLinkToOneDotJpg'),
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_", [0 < smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape("img%5Bsrc*%3D%22userfiles/album/2/thumbs/media/1.jpg%22%5D")]), "_length", [])]);
    return self;
},
args: [],
source: unescape('testFirstPageImageShouldLinkToOneDotJpg%0A%09self%20assert%3A%200%20%3C%20%28container%20find%3A%20%27img%5Bsrc*%3D%22userfiles/album/2/thumbs/media/1.jpg%22%5D%27%29%20%20length'),
messageSends: ["assert:", unescape("%3C"), "length", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testPageDescShouldContainsCreatedIn1929ByHerge'),
smalltalk.method({
selector: unescape('testPageDescShouldContainsCreatedIn1929ByHerge'),
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", ["Created in 1929 by Herge", smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".page-desc")]), "_text", [])]);
    return self;
},
args: [],
source: unescape('testPageDescShouldContainsCreatedIn1929ByHerge%0A%09self%20assert%3A%20%27Created%20in%201929%20by%20Herge%27%20equals%3A%20%28container%20find%3A%20%27.page-desc%27%29%20text'),
messageSends: ["assert:equals:", "text", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testPageWidthShouldBe165'),
smalltalk.method({
selector: unescape('testPageWidthShouldBe165'),
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [165, smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-page")]), "_width", [])]);
    return self;
},
args: [],
source: unescape('testPageWidthShouldBe165%0A%09%22resize%20to%20its%20container%20width%20%28500%29%20%20-%20margins%20%28170%29%20%20/%202%20pages%22%20%20%0A%09self%20assert%3A%20165%20equals%3A%20%28container%20find%3A%20%27.b-page%27%29%20%20width'),
messageSends: ["assert:equals:", "width", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testPageHeightShouldBe124'),
smalltalk.method({
selector: unescape('testPageHeightShouldBe124'),
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [124, smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-page")]), "_height", [])]);
    return self;
},
args: [],
source: unescape('testPageHeightShouldBe124%0A%09%22the%20width%20of%20page%20%28165%29%20*%20%20height%20of%20first%20image%20in%20json%20%28300%29%20/%20width%20of%20first%20image%20in%20json%20%28400%29%22%20%20%0A%09self%20assert%3A%20124%20equals%3A%20%28container%20find%3A%20%27.b-page%27%29%20%20height'),
messageSends: ["assert:equals:", "height", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testFirstPageZoomedImageShouldLinkToOneDotJpg'),
smalltalk.method({
selector: unescape('testFirstPageZoomedImageShouldLinkToOneDotJpg'),
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-zoom-magnify%20a")]), "_click", []);
    smalltalk.send(self, "_assert_", [0 < smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".iviewer%20img%5Bsrc*%3D%22bib-numerique/get-resource/id/1.jpg%22%5D")]), "_length", [])]);
    return self;
},
args: [],
source: unescape('testFirstPageZoomedImageShouldLinkToOneDotJpg%0A%09%28container%20find%3A%20%27.b-zoom-magnify%20a%27%29%20click.%0A%09self%20assert%3A%200%20%3C%20%28container%20find%3A%20%27.iviewer%20img%5Bsrc*%3D%22bib-numerique/get-resource/id/1.jpg%22%5D%27%29%20%20length'),
messageSends: ["click", "find:", "assert:", unescape("%3C"), "length"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testChapterSelectorShouldContainsLinkToFirstPage'),
smalltalk.method({
selector: unescape('testChapterSelectorShouldContainsLinkToFirstPage'),
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", ["Origins", smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-selector-chapter%20%23selector-page-1")]), "_text", [])]);
    return self;
},
args: [],
source: unescape('testChapterSelectorShouldContainsLinkToFirstPage%0A%09self%20assert%3A%20%27Origins%27%20%20equals%3A%20%28container%20find%3A%20%27.b-selector-chapter%20%23selector-page-1%27%29%20%20%20text%20'),
messageSends: ["assert:equals:", "text", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testChapterSelectorInitialHeightShouldBeZero'),
smalltalk.method({
selector: unescape('testChapterSelectorInitialHeightShouldBeZero'),
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [0, smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-selector-chapter%3Eul")]), "_height", [])]);
    return self;
},
args: [],
source: unescape('testChapterSelectorInitialHeightShouldBeZero%0A%09self%20assert%3A%200%20%20equals%3A%20%28container%20find%3A%20%27.b-selector-chapter%3Eul%27%29%20%20%20height%20'),
messageSends: ["assert:equals:", "height", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testFirstPageImageFolioNumberShouldBeTwelveR'),
smalltalk.method({
selector: unescape('testFirstPageImageFolioNumberShouldBeTwelveR'),
category: 'tests',
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", ["12R", smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-counter%20+%20.b-counter")]), "_text", [])]);
return self;},
args: [],
source: unescape('testFirstPageImageFolioNumberShouldBeTwelveR%0A%09self%20assert%3A%20%2712R%27%20equals%3A%20%28container%20find%3A%20%27.b-counter%20+%20.b-counter%27%29%20%20text'),
messageSends: ["assert:equals:", "text", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testSecondPageImageFolioNumberShouldBeXX'),
smalltalk.method({
selector: unescape('testSecondPageImageFolioNumberShouldBeXX'),
category: 'tests',
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", ["XX", smalltalk.send(smalltalk.send(smalltalk.send(self['@album'], "_pages", []), "_last", []), "_foliono", [])]);
return self;},
args: [],
source: unescape('testSecondPageImageFolioNumberShouldBeXX%0A%09self%20assert%3A%20%27XX%27%20equals%3A%20album%20pages%20last%20foliono'),
messageSends: ["assert:equals:", "foliono", "last", "pages"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testAnchorToDownloadPDFShouldBePresent'),
smalltalk.method({
selector: unescape('testAnchorToDownloadPDFShouldBePresent'),
category: 'tests',
fn: function () {
    var self = this;
    smalltalk.send(self, "_assert_equals_", [unescape("http%3A//localhost/pdf/2"), smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape("div.b-download-book%20a")]), "_attr_", ["href"])]);
    return self;
},
args: [],
source: unescape('testAnchorToDownloadPDFShouldBePresent%0A%09self%20assert%3A%20%27http%3A//localhost/pdf/2%27%20%20equals%3A%20%28%28container%20find%3A%20%27div.b-download-book%20a%27%29%20attr%3A%20%27href%27%20%29'),
messageSends: ["assert:equals:", "attr:", "find:"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testNextPageShouldSetMenuTextToFin'),
smalltalk.method({
selector: unescape('testNextPageShouldSetMenuTextToFin'),
category: 'tests',
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(self['@container'], "_find_", [".book"]), "_booklet_", ["next"]);
smalltalk.send(self, "_assert_equals_", ["Fin", smalltalk.send(smalltalk.send(self['@container'], "_find_", [unescape(".b-current")]), "_text", [])]);
return self;},
args: [],
source: unescape('testNextPageShouldSetMenuTextToFin%0A%09%28container%20find%3A%20%27.book%27%29%20booklet%3A%20%27next%27.%0A%09self%20assert%3A%20%27Fin%27%20equals%3A%20%28container%20find%3A%20%27.b-current%27%29%20text'),
messageSends: ["booklet:", "find:", "assert:equals:", "text"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTest);

smalltalk.addMethod(
unescape('_testFirstPageNavigatorThumbnailShouldBeOneSmallDotJpg'),
smalltalk.method({
selector: unescape('testFirstPageNavigatorThumbnailShouldBeOneSmallDotJpg'),
category: 'tests',
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", [unescape("userfiles/album/2/thumbs/media/1_small.jpg"), smalltalk.send(smalltalk.send(smalltalk.send(self['@album'], "_pages", []), "_first", []), "_navigatorThumbnailURL", [])]);
return self;},
args: [],
source: unescape('testFirstPageNavigatorThumbnailShouldBeOneSmallDotJpg%0A%09self%20assert%3A%20%27userfiles/album/2/thumbs/media/1_small.jpg%27%20equals%3A%20album%20pages%20first%20navigatorThumbnailURL%20'),
messageSends: ["assert:equals:", "navigatorThumbnailURL", "first", "pages"],
referencedClasses: []
}),
smalltalk.BibNumAlbumTintinTest);



