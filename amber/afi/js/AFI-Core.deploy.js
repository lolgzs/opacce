smalltalk.addPackage('AFI-Core', {});
smalltalk.addClass('AFIIDETools', smalltalk.Widget, ['toolsBrush'], 'AFI-Core');
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


smalltalk.AFIIDETools.klass.iVarNames = ['default'];
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


