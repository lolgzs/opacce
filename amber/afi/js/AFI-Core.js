smalltalk.addPackage('AFI-Core', {});
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


smalltalk.addClass('AFIIDETools', smalltalk.Widget, ['toolsBrush'], 'AFI-Core');
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


smalltalk.AFIIDETools.klass.iVarNames = ['default'];
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


smalltalk.addClass('Ajax', smalltalk.Object, ['url', 'settings', 'options', 'ajaxRequest'], 'AFI-Core');
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


smalltalk.Ajax.klass.iVarNames = ['opacBaseUrl'];
smalltalk.addMethod(
unescape('_controller_action_'),
smalltalk.method({
selector: unescape('controller%3Aaction%3A'),
category: 'initialize',
fn: function (controllerName, actionName){
var self=this;
return smalltalk.send(self, "_module_controller_action_", ["opac", controllerName, actionName]);
return self;},
args: ["controllerName", "actionName"],
source: unescape('controller%3AcontrollerName%20action%3AactionName%0A%09%5E%20self%20module%3A%20%27opac%27%20controller%3A%20controllerName%20action%3A%20actionName'),
messageSends: ["module:controller:action:"],
referencedClasses: []
}),
smalltalk.Ajax.klass);

smalltalk.addMethod(
unescape('_module_controller_action_'),
smalltalk.method({
selector: unescape('module%3Acontroller%3Aaction%3A'),
category: 'initialize',
fn: function (moduleName, controllerName, actionName){
var self=this;
return smalltalk.send(self, "_url_", [smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send(smalltalk.send((typeof baseUrl == 'undefined' ? nil : baseUrl), "__comma", [unescape("/")]), "__comma", [moduleName]), "__comma", [unescape("/")]), "__comma", [controllerName]), "__comma", [unescape("/")]), "__comma", [actionName])]);
return self;},
args: ["moduleName", "controllerName", "actionName"],
source: unescape('module%3AmoduleName%20controller%3AcontrollerName%20action%3AactionName%0A%09%5E%20self%20url%3A%20baseUrl%2C%27/%27%2CmoduleName%2C%27/%27%2CcontrollerName%2C%27/%27%2CactionName'),
messageSends: ["url:", unescape("%2C")],
referencedClasses: []
}),
smalltalk.Ajax.klass);

smalltalk.addMethod(
unescape('_opacBaseUrl_'),
smalltalk.method({
selector: unescape('opacBaseUrl%3A'),
category: 'accessor',
fn: function (aString){
var self=this;
(self['@opacBaseUrl']=aString);
return self;},
args: ["aString"],
source: unescape('opacBaseUrl%3A%20aString%0A%09opacBaseUrl%20%3A%3D%20aString'),
messageSends: [],
referencedClasses: []
}),
smalltalk.Ajax.klass);

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


