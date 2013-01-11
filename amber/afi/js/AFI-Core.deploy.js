smalltalk.addPackage('AFI-Core', {});
smalltalk.addClass('AFIBootstrap', smalltalk.Object, [], 'AFI-Core');
smalltalk.addMethod(
"_boot",
smalltalk.method({
selector: "boot",
fn: function () {
    var self = this;
    ($receiver = smalltalk.send(smalltalk.send(smalltalk.Smalltalk || Smalltalk, "_current", []), "_at_", ["Browser"])) != nil &&
        $receiver != undefined ? function () {return smalltalk.send(self, "_onReady_", [function () {return smalltalk.send(self, "_renderTools", []);}]);}() : nil;
    return self;
}
}),
smalltalk.AFIBootstrap);

smalltalk.addMethod(
"_onReady_",
smalltalk.method({
selector: "onReady:",
fn: function (aBlock) {
    var self = this;
    smalltalk.send(smalltalk.send("document", "_asJQuery", []), "_ready_", [aBlock]);
    return self;
}
}),
smalltalk.AFIBootstrap);

smalltalk.addMethod(
"_renderTools",
smalltalk.method({
selector: "renderTools",
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(smalltalk.AFIIDETools || AFIIDETools, "_default", []), "_appendToJQuery_", [smalltalk.send("body", "_asJQuery", [])]);
    return self;
}
}),
smalltalk.AFIBootstrap);


smalltalk.addMethod(
"_initialize",
smalltalk.method({
selector: "initialize",
fn: function () {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_new", []), "_boot", []);
    return self;
}
}),
smalltalk.AFIBootstrap.klass);


smalltalk.addClass('AFIIDETools', smalltalk.Widget, ['toolsBrush'], 'AFI-Core');
smalltalk.addMethod(
"_addButton_action_",
smalltalk.method({
selector: "addButton:action:",
fn: function (aString, aBlock) {
    var self = this;
    smalltalk.send(self['@toolsBrush'], "_appendBlock_", [function (html) {return function ($rec) {smalltalk.send($rec, "_onClick_", [aBlock]);return smalltalk.send($rec, "_with_", [aString]);}(smalltalk.send(html, "_button", []));}]);
    return self;
}
}),
smalltalk.AFIIDETools);

smalltalk.addMethod(
"_renderOn_",
smalltalk.method({
selector: "renderOn:",
fn: function (html) {
    var self = this;
    self['@toolsBrush'] = smalltalk.send(smalltalk.send(html, "_div", []), "_style_", [unescape("position%3A%20fixed%3B%20top%3A%200px%3B%20z-index%3A500")]);
    smalltalk.send(self, "_addButton_action_", ["Amber IDE", function () {return smalltalk.send(smalltalk.Browser || Browser, "_open", []);}]);
    return self;
}
}),
smalltalk.AFIIDETools);


smalltalk.AFIIDETools.klass.iVarNames = ['default'];
smalltalk.addMethod(
"_addButton_action_",
smalltalk.method({
selector: "addButton:action:",
fn: function (aString, aBlock) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_default", []), "_addButton_action_", [aString, aBlock]);
    return self;
}
}),
smalltalk.AFIIDETools.klass);

smalltalk.addMethod(
"_default",
smalltalk.method({
selector: "default",
fn: function () {
    var self = this;
    return ($receiver = self['@default']) == nil || $receiver == undefined ? function () {return self['@default'] = smalltalk.send(self, "_new", []);}() : $receiver;
    return self;
}
}),
smalltalk.AFIIDETools.klass);


smalltalk.addClass('Ajax', smalltalk.Object, ['url', 'settings', 'options', 'ajaxRequest'], 'AFI-Core');
smalltalk.addMethod(
"_abort",
smalltalk.method({
selector: "abort",
fn: function () {
    var self = this;
    ($receiver = self['@ajaxRequest']) != nil && $receiver != undefined ? function () {return smalltalk.send(self['@ajaxRequest'], "_abort", []);}() : nil;
    return self;
}
}),
smalltalk.Ajax);

smalltalk.addMethod(
"_onCompleteDo_",
smalltalk.method({
selector: "onCompleteDo:",
fn: function (aBlock) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_options", []), "_at_put_", ["complete", aBlock]);
    return self;
}
}),
smalltalk.Ajax);

smalltalk.addMethod(
"_onErrorDo_",
smalltalk.method({
selector: "onErrorDo:",
fn: function (aBlock) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_options", []), "_at_put_", ["error", aBlock]);
    return self;
}
}),
smalltalk.Ajax);

smalltalk.addMethod(
"_onSuccessDo_",
smalltalk.method({
selector: "onSuccessDo:",
fn: function (aBlock) {
    var self = this;
    smalltalk.send(smalltalk.send(self, "_options", []), "_at_put_", ["success", aBlock]);
    return self;
}
}),
smalltalk.Ajax);

smalltalk.addMethod(
"_options",
smalltalk.method({
selector: "options",
fn: function () {
    var self = this;
    return ($receiver = self['@options']) == nil || $receiver == undefined ? function () {return self['@options'] = smalltalk.send(smalltalk.HashedCollection || HashedCollection, "_new", []);}() : $receiver;
    return self;
}
}),
smalltalk.Ajax);

smalltalk.addMethod(
"_send",
smalltalk.method({
selector: "send",
fn: function () {
    var self = this;
    self['@ajaxRequest'] = smalltalk.send(typeof jQuery == "undefined" ? nil : jQuery, "_ajax_options_", [self['@url'], self['@options']]);
    return self;
}
}),
smalltalk.Ajax);

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
smalltalk.Ajax);


smalltalk.Ajax.klass.iVarNames = ['opacBaseUrl','idProfil'];
smalltalk.addMethod(
"_controller_action_",
smalltalk.method({
selector: "controller:action:",
fn: function (controllerName, actionName) {
    var self = this;
    return smalltalk.send(self, "_module_controller_action_", ["opac", controllerName, actionName]);
    return self;
}
}),
smalltalk.Ajax.klass);

smalltalk.addMethod(
"_module_controller_action_",
smalltalk.method({
selector: "module:controller:action:",
fn: function (moduleName, controllerName, actionName) {
    var self = this;
    return smalltalk.send(self, "_url_", [smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(typeof baseUrl == "undefined" ? nil : baseUrl, "__comma", [unescape("/")]), "__comma", [moduleName]), "__comma", [unescape("/")]), "__comma", [controllerName]), "__comma", [unescape("/")]), "__comma", [actionName]), "__comma", [unescape("/id_profil/")]), "__comma", [self['@idProfil']])]);
    return self;
}
}),
smalltalk.Ajax.klass);

smalltalk.addMethod(
"_opacBaseUrl_idProfil_",
smalltalk.method({
selector: "opacBaseUrl:idProfil:",
fn: function (baseUrlString, idProfilString) {
    var self = this;
    self['@opacBaseUrl'] = baseUrlString;
    self['@idProfil'] = idProfilString;
    return self;
}
}),
smalltalk.Ajax.klass);

smalltalk.addMethod(
"_url_",
smalltalk.method({
selector: "url:",
fn: function (aString) {
    var self = this;
    return function ($rec) {smalltalk.send($rec, "_url_", [aString]);return smalltalk.send($rec, "_yourself", []);}(smalltalk.send(self, "_new", []));
    return self;
}
}),
smalltalk.Ajax.klass);


