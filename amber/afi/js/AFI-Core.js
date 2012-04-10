smalltalk.addPackage('AFI-Core', {});
smalltalk.addClass('AFIIDETools', smalltalk.Widget, ['toolsBrush'], 'AFI-Core');
smalltalk.addMethod(
unescape('_renderOn_'),
smalltalk.method({
selector: unescape('renderOn%3A'),
category: 'rendering',
fn: function (html){
var self=this;
self['@toolsBrush']=smalltalk.send(smalltalk.send(html, "_div", []), "_style_", [unescape("position%3A%20fixed%3B%20top%3A%200px%3B%20z-index%3A500")]);
smalltalk.send(self, "_addButton_action_", ["Amber IDE", (function(){return smalltalk.send((smalltalk.Browser || Browser), "_open", []);})]);
return self;},
args: ["html"],
source: unescape('renderOn%3A%20html%0A%09toolsBrush%20%3A%3D%20html%20div%09style%3A%20%27position%3A%20fixed%3B%20top%3A%200px%3B%20z-index%3A500%27.%0A%0A%09self%20addButton%3A%20%27Amber%20IDE%27%20action%3A%20%5BBrowser%20open%5D.'),
messageSends: ["style:", "div", "addButton:action:", "open"],
referencedClasses: ["Browser"]
}),
smalltalk.AFIIDETools);

smalltalk.addMethod(
unescape('_addButton_action_'),
smalltalk.method({
selector: unescape('addButton%3Aaction%3A'),
category: 'rendering',
fn: function (aString, aBlock){
var self=this;
smalltalk.send(self['@toolsBrush'], "_appendBlock_", [(function(html){return (function($rec){smalltalk.send($rec, "_onClick_", [aBlock]);return smalltalk.send($rec, "_with_", [aString]);})(smalltalk.send(html, "_button", []));})]);
return self;},
args: ["aString", "aBlock"],
source: unescape('addButton%3A%20aString%20action%3A%20aBlock%0A%09toolsBrush%20appendBlock%3A%20%5B%3Ahtml%7C%09html%20button%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%09%09%09%09onClick%3A%20aBlock%3B%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%09%09with%3A%20aString%20%5D'),
messageSends: ["appendBlock:", "onClick:", "with:", "button"],
referencedClasses: []
}),
smalltalk.AFIIDETools);


smalltalk.AFIIDETools.klass.iVarNames = ['default'];
smalltalk.addMethod(
unescape('_default'),
smalltalk.method({
selector: unescape('default'),
category: 'instance creation',
fn: function (){
var self=this;
return (($receiver = self['@default']) == nil || $receiver == undefined) ? (function(){return self['@default']=smalltalk.send(self, "_new", []);})() : $receiver;
return self;},
args: [],
source: unescape('default%0A%09%5E%20default%20ifNil%3A%20%5Bdefault%20%3A%3D%20self%20new%5D'),
messageSends: ["ifNil:", "new"],
referencedClasses: []
}),
smalltalk.AFIIDETools.klass);

smalltalk.addMethod(
unescape('_addButton_action_'),
smalltalk.method({
selector: unescape('addButton%3Aaction%3A'),
category: 'buttons',
fn: function (aString, aBlock){
var self=this;
smalltalk.send(smalltalk.send(self, "_default", []), "_addButton_action_", [aString, aBlock]);
return self;},
args: ["aString", "aBlock"],
source: unescape('addButton%3A%20aString%20action%3A%20aBlock%0A%09self%20default%20addButton%3A%20aString%20action%3A%20aBlock'),
messageSends: ["addButton:action:", "default"],
referencedClasses: []
}),
smalltalk.AFIIDETools.klass);


smalltalk.addClass('AFIBootstrap', smalltalk.Object, [], 'AFI-Core');
smalltalk.addMethod(
unescape('_boot'),
smalltalk.method({
selector: unescape('boot'),
category: 'initialize',
fn: function (){
var self=this;
(($receiver = smalltalk.send(smalltalk.send((smalltalk.Smalltalk || Smalltalk), "_current", []), "_at_", ["Browser"])) != nil && $receiver != undefined) ? (function(){return smalltalk.send(self, "_onReady_", [(function(){return smalltalk.send(self, "_renderTools", []);})]);})() : nil;
return self;},
args: [],
source: unescape('boot%0A%09%28Smalltalk%20current%20at%3A%20%23Browser%29%20%0A%09%09ifNotNil%3A%20%5B%20%20%20self%20onReady%3A%20%5B%20self%20renderTools%20%5D%20%5D'),
messageSends: ["ifNotNil:", "at:", "current", "onReady:", "renderTools"],
referencedClasses: ["BlockClosure"]
}),
smalltalk.AFIBootstrap);

smalltalk.addMethod(
unescape('_onReady_'),
smalltalk.method({
selector: unescape('onReady%3A'),
category: 'jquery',
fn: function (aBlock){
var self=this;
smalltalk.send(smalltalk.send("document", "_asJQuery", []), "_ready_", [aBlock]);
return self;},
args: ["aBlock"],
source: unescape('onReady%3A%20aBlock%0A%09%27document%27%20asJQuery%20ready%3A%20aBlock'),
messageSends: ["ready:", "asJQuery"],
referencedClasses: []
}),
smalltalk.AFIBootstrap);

smalltalk.addMethod(
unescape('_renderTools'),
smalltalk.method({
selector: unescape('renderTools'),
category: 'rendering',
fn: function (){
var self=this;
smalltalk.send(smalltalk.send((smalltalk.AFIIDETools || AFIIDETools), "_default", []), "_appendToJQuery_", [smalltalk.send("body", "_asJQuery", [])]);
return self;},
args: [],
source: unescape('renderTools%0A%09AFIIDETools%20default%20appendToJQuery%3A%20%27body%27%20asJQuery%09'),
messageSends: ["appendToJQuery:", "default", "asJQuery"],
referencedClasses: ["AFIIDETools"]
}),
smalltalk.AFIBootstrap);


smalltalk.addMethod(
unescape('_initialize'),
smalltalk.method({
selector: unescape('initialize'),
category: 'initialize-release',
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(self, "_new", []), "_boot", []);
return self;},
args: [],
source: unescape('initialize%0A%09self%20new%20boot.'),
messageSends: ["boot", "new"],
referencedClasses: []
}),
smalltalk.AFIBootstrap.klass);


