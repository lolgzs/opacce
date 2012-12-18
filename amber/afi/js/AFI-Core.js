smalltalk.addPackage('AFI-Core', {});
smalltalk.addClass('AFIBootstrap', smalltalk.Object, [], 'AFI-Core');
smalltalk.addMethod(
"_boot",
smalltalk.method({
selector: "boot",
category: 'initialize',
fn: function () {
    var self = this;
    ($receiver = smalltalk.send(smalltalk.send(smalltalk.Smalltalk || Smalltalk, "_current", []), "_at_", ["Browser"])) != nil &&
        $receiver != undefined ? function () {return smalltalk.send(self, "_onReady_", [function () {return smalltalk.send(self, "_renderTools", []);}]);}() : nil;
    return self;
},
args: [],
source: "boot\x0a\x09(Smalltalk current at: #Browser) \x0a\x09\x09ifNotNil: [   self onReady: [ self renderTools ] ]",
messageSends: ["ifNotNil:", "at:", "current", "onReady:", "renderTools"],
referencedClasses: ["BlockClosure"]
}),
smalltalk.AFIBootstrap);

smalltalk.addMethod(
"_onReady_",
smalltalk.method({
selector: "onReady:",
category: 'jquery',
fn: function (aBlock) {
    var self = this;
    smalltalk.send(smalltalk.send("document", "_asJQuery", []), "_ready_", [aBlock]);
    return self;
},
args: ["aBlock"],
source: "onReady: aBlock\x0a\x09'document' asJQuery ready: aBlock",
messageSends: ["ready:", "asJQuery"],
referencedClasses: []
}),
smalltalk.AFIBootstrap);

smalltalk.addMethod(
"_renderTools",
smalltalk.method({
selector: "renderTools",
category: 'rendering',
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(smalltalk.AFIIDETools || AFIIDETools, "_default", []), "_appendToJQuery_", [smalltalk.send("body", "_asJQuery", [])]);
    return self;
},
args: [],
source: "renderTools\x0a\x09AFIIDETools default appendToJQuery: 'body' asJQuery\x09",
messageSends: ["appendToJQuery:", "default", "asJQuery"],
referencedClasses: ["AFIIDETools"]
}),
smalltalk.AFIBootstrap);


smalltalk.addMethod(
"_initialize",
smalltalk.method({
selector: "initialize",
category: 'initialize-release',
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_new", []), "_boot", []);
    return self;
},
args: [],
source: "initialize\x0a\x09self new boot.",
messageSends: ["boot", "new"],
referencedClasses: []
}),
smalltalk.AFIBootstrap.klass);


smalltalk.addClass('AFIIDETools', smalltalk.Widget, ['toolsBrush'], 'AFI-Core');
smalltalk.addMethod(
"_addButton_action_",
smalltalk.method({
selector: "addButton:action:",
category: 'rendering',
fn: function (aString, aBlock) {
    var self = this;
    smalltalk.send(self['@toolsBrush'], "_appendBlock_", [function (html) {return function ($rec) {smalltalk.send($rec, "_onClick_", [aBlock]);return smalltalk.send($rec, "_with_", [aString]);}(smalltalk.send(html, "_button", []));}]);
    return self;
},
args: ["aString", "aBlock"],
source: "addButton: aString action: aBlock\x0a\x09toolsBrush appendBlock: [:html|\x09html button\x0a              \x09\x09\x09\x09\x09\x09\x09\x09\x09onClick: aBlock;\x0a                           \x09\x09\x09\x09\x09\x09\x09with: aString ]",
messageSends: ["appendBlock:", "onClick:", "with:", "button"],
referencedClasses: []
}),
smalltalk.AFIIDETools);

smalltalk.addMethod(
"_renderOn_",
smalltalk.method({
selector: "renderOn:",
category: 'rendering',
fn: function (html) {
    var self = this;
    self['@toolsBrush'] = smalltalk.send(smalltalk.send(html, "_div", []), "_style_", [unescape("position%3A%20fixed%3B%20top%3A%200px%3B%20z-index%3A500")]);
    smalltalk.send(self, "_addButton_action_", ["Amber IDE", function () {return smalltalk.send(smalltalk.Browser || Browser, "_open", []);}]);
    return self;
},
args: ["html"],
source: "renderOn: html\x0a\x09toolsBrush := html div\x09style: 'position: fixed; top: 0px; z-index:500'.\x0a\x0a\x09self addButton: 'Amber IDE' action: [Browser open].",
messageSends: ["style:", "div", "addButton:action:", "open"],
referencedClasses: ["Browser"]
}),
smalltalk.AFIIDETools);


smalltalk.AFIIDETools.klass.iVarNames = ['default'];
smalltalk.addMethod(
"_addButton_action_",
smalltalk.method({
selector: "addButton:action:",
category: 'buttons',
fn: function (aString, aBlock) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_default", []), "_addButton_action_", [aString, aBlock]);
    return self;
},
args: ["aString", "aBlock"],
source: "addButton: aString action: aBlock\x0a\x09self default addButton: aString action: aBlock",
messageSends: ["addButton:action:", "default"],
referencedClasses: []
}),
smalltalk.AFIIDETools.klass);

smalltalk.addMethod(
"_default",
smalltalk.method({
selector: "default",
category: 'instance creation',
fn: function () {
    var self = this;
    return ($receiver = self['@default']) == nil || $receiver == undefined ? function () {return self['@default'] = smalltalk.send(self, "_new", []);}() : $receiver;
    return self;
},
args: [],
source: "default\x0a\x09^ default ifNil: [default := self new]",
messageSends: ["ifNil:", "new"],
referencedClasses: []
}),
smalltalk.AFIIDETools.klass);


smalltalk.addClass('Ajax', smalltalk.Object, ['url', 'settings', 'options', 'ajaxRequest'], 'AFI-Core');
smalltalk.addMethod(
"_abort",
smalltalk.method({
selector: "abort",
category: 'actions',
fn: function () {
    var self = this;
    ($receiver = self['@ajaxRequest']) != nil && $receiver != undefined ? function () {return smalltalk.send(self['@ajaxRequest'], "_abort", []);}() : nil;
    return self;
},
args: [],
source: "abort\x0a\x09ajaxRequest ifNotNil: [ajaxRequest abort]",
messageSends: ["ifNotNil:", "abort"],
referencedClasses: []
}),
smalltalk.Ajax);

smalltalk.addMethod(
"_onCompleteDo_",
smalltalk.method({
selector: "onCompleteDo:",
category: 'callback',
fn: function (aBlock) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_options", []), "_at_put_", ["complete", aBlock]);
    return self;
},
args: ["aBlock"],
source: "onCompleteDo: aBlock\x0a\x09\x22A block to be called when the request finishes (after success and error callbacks are executed). Block arguments: jqXHR, textStatus\x22\x0a\x09self options at: 'complete' put: aBlock",
messageSends: ["at:put:", "options"],
referencedClasses: []
}),
smalltalk.Ajax);

smalltalk.addMethod(
"_onErrorDo_",
smalltalk.method({
selector: "onErrorDo:",
category: 'callback',
fn: function (aBlock) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_options", []), "_at_put_", ["error", aBlock]);
    return self;
},
args: ["aBlock"],
source: "onErrorDo: aBlock\x0a\x09\x22A block to be called if the request fails.Block arguments: jqXHR, textStatus, errorThrown\x22\x0a\x09self options at: 'error' put: aBlock",
messageSends: ["at:put:", "options"],
referencedClasses: []
}),
smalltalk.Ajax);

smalltalk.addMethod(
"_onSuccessDo_",
smalltalk.method({
selector: "onSuccessDo:",
category: 'callback',
fn: function (aBlock) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_options", []), "_at_put_", ["success", aBlock]);
    return self;
},
args: ["aBlock"],
source: "onSuccessDo: aBlock\x0a\x09\x22Set action to execute when Ajax request is successful. Pass received data as block argument. Block arguments: data, textStatus, jqXHR\x22\x0a\x09self options at: 'success' put: aBlock",
messageSends: ["at:put:", "options"],
referencedClasses: []
}),
smalltalk.Ajax);

smalltalk.addMethod(
"_options",
smalltalk.method({
selector: "options",
category: 'accessing',
fn: function () {
    var self = this;
    return ($receiver = self['@options']) == nil || $receiver == undefined ? function () {return self['@options'] = smalltalk.send(smalltalk.HashedCollection || HashedCollection, "_new", []);}() : $receiver;
    return self;
},
args: [],
source: "options\x0a\x09^ options ifNil: [options := HashedCollection new ]",
messageSends: ["ifNil:", "new"],
referencedClasses: ["HashedCollection"]
}),
smalltalk.Ajax);

smalltalk.addMethod(
"_send",
smalltalk.method({
selector: "send",
category: 'actions',
fn: function () {
    var self = this;
    self['@ajaxRequest'] = smalltalk.send(typeof jQuery == "undefined" ? nil : jQuery, "_ajax_options_", [self['@url'], self['@options']]);
    return self;
},
args: [],
source: "send\x0a\x09ajaxRequest := jQuery ajax: url options: options.",
messageSends: ["ajax:options:"],
referencedClasses: []
}),
smalltalk.Ajax);

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
smalltalk.Ajax);


smalltalk.Ajax.klass.iVarNames = ['opacBaseUrl','idProfil'];
smalltalk.addMethod(
"_controller_action_",
smalltalk.method({
selector: "controller:action:",
category: 'initialize',
fn: function (controllerName, actionName) {
    var self = this;
    return smalltalk.send(self, "_module_controller_action_", ["opac", controllerName, actionName]);
    return self;
},
args: ["controllerName", "actionName"],
source: "controller:controllerName action:actionName\x0a\x09^ self module: 'opac' controller: controllerName action: actionName",
messageSends: ["module:controller:action:"],
referencedClasses: []
}),
smalltalk.Ajax.klass);

smalltalk.addMethod(
"_module_controller_action_",
smalltalk.method({
selector: "module:controller:action:",
category: 'initialize',
fn: function (moduleName, controllerName, actionName) {
    var self = this;
    return smalltalk.send(self, "_url_", [smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(typeof baseUrl == "undefined" ? nil : baseUrl, "__comma", [unescape("/")]), "__comma", [moduleName]), "__comma", [unescape("/")]), "__comma", [controllerName]), "__comma", [unescape("/")]), "__comma", [actionName]), "__comma", [unescape("/id_profil/")]), "__comma", [self['@idProfil']])]);
    return self;
},
args: ["moduleName", "controllerName", "actionName"],
source: "module:moduleName controller:controllerName action:actionName\x0a\x09^ self url: baseUrl,'/',moduleName,'/',controllerName,'/',actionName,'/id_profil/',idProfil",
messageSends: ["url:", ","],
referencedClasses: []
}),
smalltalk.Ajax.klass);

smalltalk.addMethod(
"_opacBaseUrl_idProfil_",
smalltalk.method({
selector: "opacBaseUrl:idProfil:",
category: 'accessor',
fn: function (baseUrlString, idProfilString) {
    var self = this;
    self['@opacBaseUrl'] = baseUrlString;
    self['@idProfil'] = idProfilString;
    return self;
},
args: ["baseUrlString", "idProfilString"],
source: "opacBaseUrl: baseUrlString idProfil: idProfilString  \x0a\x09opacBaseUrl := baseUrlString.\x0a\x09idProfil := idProfilString.",
messageSends: [],
referencedClasses: []
}),
smalltalk.Ajax.klass);

smalltalk.addMethod(
"_url_",
smalltalk.method({
selector: "url:",
category: 'initialize',
fn: function (aString) {
    var self = this;
    return function ($rec) {smalltalk.send($rec, "_url_", [aString]);return smalltalk.send($rec, "_yourself", []);}(smalltalk.send(self, "_new", []));
    return self;
},
args: ["aString"],
source: "url: aString\x0a\x09^ self new \x0a\x09\x09url: aString;\x0a\x09\x09yourself",
messageSends: ["url:", "yourself", "new"],
referencedClasses: []
}),
smalltalk.Ajax.klass);


