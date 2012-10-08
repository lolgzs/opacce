smalltalk.addPackage('AFI-Core', {});
smalltalk.addClass('AFIBootstrap', smalltalk.Object, [], 'AFI-Core');
smalltalk.addMethod(
unescape('_boot'),
smalltalk.method({
selector: unescape('boot'),
fn: function (){
var self=this;
(($receiver = smalltalk.send(smalltalk.send((smalltalk.Smalltalk || Smalltalk), "_current", []), "_at_", ["Browser"])) != nil && $receiver != undefined) ? (function(){return smalltalk.send(self, "_onReady_", [(function(){return smalltalk.send(self, "_renderTools", []);})]);})() : nil;
return self;}
}),
smalltalk.AFIBootstrap);

smalltalk.addMethod(
unescape('_onReady_'),
smalltalk.method({
selector: unescape('onReady%3A'),
fn: function (aBlock){
var self=this;
smalltalk.send(smalltalk.send("document", "_asJQuery", []), "_ready_", [aBlock]);
return self;}
}),
smalltalk.AFIBootstrap);

smalltalk.addMethod(
unescape('_renderTools'),
smalltalk.method({
selector: unescape('renderTools'),
fn: function (){
var self=this;
smalltalk.send(smalltalk.send((smalltalk.AFIIDETools || AFIIDETools), "_default", []), "_appendToJQuery_", [smalltalk.send("body", "_asJQuery", [])]);
return self;}
}),
smalltalk.AFIBootstrap);


smalltalk.addMethod(
unescape('_initialize'),
smalltalk.method({
selector: unescape('initialize'),
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(self, "_new", []), "_boot", []);
return self;}
}),
smalltalk.AFIBootstrap.klass);


smalltalk.addClass('AFIIDETools', smalltalk.Widget, ['toolsBrush'], 'AFI-Core');
smalltalk.addMethod(
unescape('_addButton_action_'),
smalltalk.method({
selector: unescape('addButton%3Aaction%3A'),
fn: function (aString, aBlock){
var self=this;
smalltalk.send(self['@toolsBrush'], "_appendBlock_", [(function(html){return (function($rec){smalltalk.send($rec, "_onClick_", [aBlock]);return smalltalk.send($rec, "_with_", [aString]);})(smalltalk.send(html, "_button", []));})]);
return self;}
}),
smalltalk.AFIIDETools);

smalltalk.addMethod(
unescape('_renderOn_'),
smalltalk.method({
selector: unescape('renderOn%3A'),
fn: function (html){
var self=this;
self['@toolsBrush']=smalltalk.send(smalltalk.send(html, "_div", []), "_style_", [unescape("position%3A%20fixed%3B%20top%3A%200px%3B%20z-index%3A500")]);
smalltalk.send(self, "_addButton_action_", ["Amber IDE", (function(){return smalltalk.send((smalltalk.Browser || Browser), "_open", []);})]);
return self;}
}),
smalltalk.AFIIDETools);


smalltalk.AFIIDETools.klass.iVarNames = ['default'];
smalltalk.addMethod(
unescape('_addButton_action_'),
smalltalk.method({
selector: unescape('addButton%3Aaction%3A'),
fn: function (aString, aBlock){
var self=this;
smalltalk.send(smalltalk.send(self, "_default", []), "_addButton_action_", [aString, aBlock]);
return self;}
}),
smalltalk.AFIIDETools.klass);

smalltalk.addMethod(
unescape('_default'),
smalltalk.method({
selector: unescape('default'),
fn: function (){
var self=this;
return (($receiver = self['@default']) == nil || $receiver == undefined) ? (function(){return self['@default']=smalltalk.send(self, "_new", []);})() : $receiver;
return self;}
}),
smalltalk.AFIIDETools.klass);


smalltalk.addClass('Ajax', smalltalk.Object, ['url', 'settings', 'options', 'ajaxRequest'], 'AFI-Core');
smalltalk.addMethod(
unescape('_abort'),
smalltalk.method({
selector: unescape('abort'),
fn: function (){
var self=this;
(($receiver = self['@ajaxRequest']) != nil && $receiver != undefined) ? (function(){return smalltalk.send(self['@ajaxRequest'], "_abort", []);})() : nil;
return self;}
}),
smalltalk.Ajax);

smalltalk.addMethod(
unescape('_onCompleteDo_'),
smalltalk.method({
selector: unescape('onCompleteDo%3A'),
fn: function (aBlock){
var self=this;
smalltalk.send(smalltalk.send(self, "_options", []), "_at_put_", ["complete", aBlock]);
return self;}
}),
smalltalk.Ajax);

smalltalk.addMethod(
unescape('_onErrorDo_'),
smalltalk.method({
selector: unescape('onErrorDo%3A'),
fn: function (aBlock){
var self=this;
smalltalk.send(smalltalk.send(self, "_options", []), "_at_put_", ["error", aBlock]);
return self;}
}),
smalltalk.Ajax);

smalltalk.addMethod(
unescape('_onSuccessDo_'),
smalltalk.method({
selector: unescape('onSuccessDo%3A'),
fn: function (aBlock){
var self=this;
smalltalk.send(smalltalk.send(self, "_options", []), "_at_put_", ["success", aBlock]);
return self;}
}),
smalltalk.Ajax);

smalltalk.addMethod(
unescape('_options'),
smalltalk.method({
selector: unescape('options'),
fn: function (){
var self=this;
return (($receiver = self['@options']) == nil || $receiver == undefined) ? (function(){return self['@options']=smalltalk.send((smalltalk.HashedCollection || HashedCollection), "_new", []);})() : $receiver;
return self;}
}),
smalltalk.Ajax);

smalltalk.addMethod(
unescape('_send'),
smalltalk.method({
selector: unescape('send'),
fn: function (){
var self=this;
(self['@ajaxRequest']=smalltalk.send((typeof jQuery == 'undefined' ? nil : jQuery), "_ajax_options_", [self['@url'], self['@options']]));
return self;}
}),
smalltalk.Ajax);

smalltalk.addMethod(
unescape('_url_'),
smalltalk.method({
selector: unescape('url%3A'),
fn: function (aString){
var self=this;
self['@url']=aString;
return self;}
}),
smalltalk.Ajax);


smalltalk.Ajax.klass.iVarNames = ['opacBaseUrl'];
smalltalk.addMethod(
unescape('_controller_action_'),
smalltalk.method({
selector: unescape('controller%3Aaction%3A'),
fn: function (controllerName, actionName){
var self=this;
return smalltalk.send(self, "_module_controller_action_", ["opac", controllerName, actionName]);
return self;}
}),
smalltalk.Ajax.klass);

smalltalk.addMethod(
unescape('_module_controller_action_'),
smalltalk.method({
selector: unescape('module%3Acontroller%3Aaction%3A'),
fn: function (moduleName, controllerName, actionName){
var self=this;
return smalltalk.send(self, "_url_", [smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send((typeof baseUrl == 'undefined' ? nil : baseUrl), "__comma", [unescape("/")]), "__comma", [moduleName]), "__comma", [unescape("/")]), "__comma", [controllerName]), "__comma", [unescape("/")]), "__comma", [actionName])]);
return self;}
}),
smalltalk.Ajax.klass);

smalltalk.addMethod(
unescape('_opacBaseUrl_'),
smalltalk.method({
selector: unescape('opacBaseUrl%3A'),
fn: function (aString){
var self=this;
(self['@opacBaseUrl']=aString);
return self;}
}),
smalltalk.Ajax.klass);

smalltalk.addMethod(
unescape('_url_'),
smalltalk.method({
selector: unescape('url%3A'),
fn: function (aString){
var self=this;
return (function($rec){smalltalk.send($rec, "_url_", [aString]);return smalltalk.send($rec, "_yourself", []);})(smalltalk.send(self, "_new", []));
return self;}
}),
smalltalk.Ajax.klass);


