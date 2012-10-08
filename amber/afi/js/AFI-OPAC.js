smalltalk.addPackage('AFI-OPAC', {});
smalltalk.addClass('AFICssEditor', smalltalk.TabWidget, ['sourceArea', 'styleTag'], 'AFI-OPAC');
smalltalk.addMethod(
unescape('_commit'),
smalltalk.method({
selector: unescape('commit'),
category: 'actions',
fn: function (){
var self=this;
var ajax=nil;
(ajax=smalltalk.send((smalltalk.Ajax || Ajax), "_module_controller_action_", ["admin", "profil", unescape("upload-css")]));
(function($rec){smalltalk.send($rec, "_at_put_", ["type", "PUT"]);smalltalk.send($rec, "_at_put_", ["data", smalltalk.send(self, "_contents", [])]);return smalltalk.send($rec, "_at_put_", ["contentType", unescape("text/css%3Bcharset%3DUTF-8")]);})(smalltalk.send(ajax, "_options", []));
(function($rec){smalltalk.send($rec, "_onErrorDo_", [(function(){return smalltalk.send((typeof window == 'undefined' ? nil : window), "_alert_", ["Erreur lors de la sauvegarde: "]);})]);smalltalk.send($rec, "_onSuccessDo_", [(function(){return smalltalk.send((typeof window == 'undefined' ? nil : window), "_alert_", [unescape("Fichier%20CSS%20sauvegard%E9")]);})]);return smalltalk.send($rec, "_send", []);})(ajax);
return self;},
args: [],
source: unescape('commit%0A%09%7Cajax%7C%20%0A%09ajax%20%3A%3D%20Ajax%20module%3A%20%27admin%27%20controller%3A%20%27profil%27%20action%3A%20%27upload-css%27.%0A%09ajax%20options%20%0A%09%09%09at%3A%20%27type%27%20put%3A%20%27PUT%27%3B%0A%09%09%09at%3A%20%27data%27%20put%3A%20self%20contents%3B%0A%09%09%09at%3A%20%27contentType%27%20put%3A%20%27text/css%3Bcharset%3DUTF-8%27.%0A%09ajax%20%0A%09%09onErrorDo%3A%20%5Bwindow%20alert%3A%20%27Erreur%20lors%20de%20la%20sauvegarde%3A%20%27%20%5D%3B%0A%09%09onSuccessDo%3A%20%5Bwindow%20alert%3A%20%27Fichier%20CSS%20sauvegard%E9%27%20%5D%3B%0A%09%09send'),
messageSends: ["module:controller:action:", "at:put:", "contents", "options", "onErrorDo:", "alert:", "onSuccessDo:", "send"],
referencedClasses: ["Ajax"]
}),
smalltalk.AFICssEditor);

smalltalk.addMethod(
unescape('_contents'),
smalltalk.method({
selector: unescape('contents'),
category: 'accessor',
fn: function (){
var self=this;
return smalltalk.send(self['@sourceArea'], "_val", []);
return self;},
args: [],
source: unescape('contents%0A%09%5E%20sourceArea%20val'),
messageSends: ["val"],
referencedClasses: []
}),
smalltalk.AFICssEditor);

smalltalk.addMethod(
unescape('_contents_'),
smalltalk.method({
selector: unescape('contents%3A'),
category: 'accessor',
fn: function (aString){
var self=this;
smalltalk.send(self['@sourceArea'], "_val_", [aString]);
return self;},
args: ["aString"],
source: unescape('contents%3A%20aString%0A%09sourceArea%20val%3A%20aString'),
messageSends: ["val:"],
referencedClasses: []
}),
smalltalk.AFICssEditor);

smalltalk.addMethod(
unescape('_label'),
smalltalk.method({
selector: unescape('label'),
category: 'accessor',
fn: function (){
var self=this;
return "Editeur CSS";
return self;},
args: [],
source: unescape('label%0A%20%20%20%20%5E%20%27Editeur%20CSS%27'),
messageSends: [],
referencedClasses: []
}),
smalltalk.AFICssEditor);

smalltalk.addMethod(
unescape('_loadCss'),
smalltalk.method({
selector: unescape('loadCss'),
category: 'actions',
fn: function (){
var self=this;
var cssUrl=nil;
(cssUrl=smalltalk.send(smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery_", [unescape("%23profil_css")]), "_attr_", ["href"]));
(($receiver = cssUrl) != nil && $receiver != undefined) ? (function(){return (function($rec){smalltalk.send($rec, "_onSuccessDo_", [(function(data){return smalltalk.send(self, "_contents_", [data]);})]);return smalltalk.send($rec, "_send", []);})(smalltalk.send((smalltalk.Ajax || Ajax), "_url_", [cssUrl]));})() : nil;
return self;},
args: [],
source: unescape('loadCss%0A%09%7CcssUrl%7C%0A%09cssUrl%20%3A%3D%20%28window%20jQuery%3A%20%27%23profil_css%27%29%20attr%3A%20%27href%27.%0A%09cssUrl%20ifNotNil%3A%20%5B%09%28Ajax%20url%3A%20cssUrl%29%20%0A%09%09%09%09%09%09onSuccessDo%3A%20%5B%3Adata%20%7C%20self%20contents%3A%20data%20%5D%3B%0A%09%09%09%09%09%09send.%20%5D.'),
messageSends: ["attr:", "jQuery:", "ifNotNil:", "onSuccessDo:", "contents:", "send", "url:"],
referencedClasses: ["Ajax"]
}),
smalltalk.AFICssEditor);

smalltalk.addMethod(
unescape('_refreshContents'),
smalltalk.method({
selector: unescape('refreshContents'),
category: 'callbacks',
fn: function (){
var self=this;
smalltalk.send(self, "_contents_", [smalltalk.send(smalltalk.send(self, "_styleTag", []), "_html", [])]);
return self;},
args: [],
source: unescape('refreshContents%0A%09self%20contents%3A%20self%20styleTag%20html'),
messageSends: ["contents:", "html", "styleTag"],
referencedClasses: []
}),
smalltalk.AFICssEditor);

smalltalk.addMethod(
unescape('_renderBoxOn_'),
smalltalk.method({
selector: unescape('renderBoxOn%3A'),
category: 'rendering',
fn: function (html){
var self=this;
(self['@sourceArea']=smalltalk.send((smalltalk.AFISourceArea || AFISourceArea), "_new", []));
smalltalk.send(self['@sourceArea'], "_renderOn_", [html]);
(function($rec){smalltalk.send($rec, "_loadCss", []);return smalltalk.send($rec, "_refreshContents", []);})(self);
smalltalk.send(self['@sourceArea'], "_onChange_", [(function(){return smalltalk.send(self, "_updateStyleTag", []);})]);
return self;},
args: ["html"],
source: unescape('renderBoxOn%3A%20html%0A%20%20%20%20sourceArea%20%3A%3D%20AFISourceArea%20new.%0A%20%20%20%20sourceArea%20renderOn%3A%20html.%0A%0A%20%20%20%20self%20loadCss%3B%20refreshContents.%0A%0A%20%20%20%20sourceArea%20onChange%3A%20%5Bself%20updateStyleTag%5D'),
messageSends: ["new", "renderOn:", "loadCss", "refreshContents", "onChange:", "updateStyleTag"],
referencedClasses: ["AFISourceArea"]
}),
smalltalk.AFICssEditor);

smalltalk.addMethod(
unescape('_renderButtonsOn_'),
smalltalk.method({
selector: unescape('renderButtonsOn%3A'),
category: 'rendering',
fn: function (html){
var self=this;
(function($rec){smalltalk.send($rec, "_with_", [unescape("R%E9initialiser")]);return smalltalk.send($rec, "_onClick_", [(function(){return smalltalk.send(self, "_loadCss", []);})]);})(smalltalk.send(html, "_button", []));
(function($rec){smalltalk.send($rec, "_with_", ["Sauvegarder"]);return smalltalk.send($rec, "_onClick_", [(function(){return smalltalk.send(self, "_commit", []);})]);})(smalltalk.send(html, "_button", []));
return self;},
args: ["html"],
source: unescape('renderButtonsOn%3A%20html%0A%20%20%20%20html%20button%0A%09with%3A%20%27R%E9initialiser%27%3B%0A%09onClick%3A%20%5Bself%20loadCss%5D.%0A%20%20%20%20html%20button%0A%09with%3A%20%27Sauvegarder%27%3B%0A%09onClick%3A%20%5Bself%20commit%5D.'),
messageSends: ["with:", "onClick:", "loadCss", "button", "commit"],
referencedClasses: []
}),
smalltalk.AFICssEditor);

smalltalk.addMethod(
unescape('_styleTag'),
smalltalk.method({
selector: unescape('styleTag'),
category: 'accessor',
fn: function (){
var self=this;
return (($receiver = self['@styleTag']) == nil || $receiver == undefined) ? (function(){(self['@styleTag']=smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery_", [unescape("%3Cstyle%20id%3D%22profil_css_editor%22%3E%20body%20%7B%7D%20%3C/style%3E")]));return smalltalk.send(self['@styleTag'], "_appendTo_", [smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery_", ["body"])]);})() : $receiver;
return self;},
args: [],
source: unescape('styleTag%0A%09%5E%20styleTag%20ifNil%3A%20%5B%0A%09%09styleTag%20%3A%3D%20window%20jQuery%3A%20%27%3Cstyle%20id%3D%22profil_css_editor%22%3E%20body%20%7B%7D%20%3C/style%3E%27.%0A%09%09styleTag%20appendTo%3A%20%28window%20jQuery%3A%20%27body%27%29.%0A%09%5D'),
messageSends: ["ifNil:", "jQuery:", "appendTo:"],
referencedClasses: []
}),
smalltalk.AFICssEditor);

smalltalk.addMethod(
unescape('_updateStyleTag'),
smalltalk.method({
selector: unescape('updateStyleTag'),
category: 'callbacks',
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(self, "_styleTag", []), "_html_", [smalltalk.send(self['@sourceArea'], "_val", [])]);
return self;},
args: [],
source: unescape('updateStyleTag%0A%09self%20styleTag%20html%3A%20sourceArea%20val'),
messageSends: ["html:", "styleTag", "val"],
referencedClasses: []
}),
smalltalk.AFICssEditor);


smalltalk.AFICssEditor.klass.iVarNames = ['current'];
smalltalk.addMethod(
unescape('_close'),
smalltalk.method({
selector: unescape('close'),
category: 'not yet classified',
fn: function (){
var self=this;
(($receiver = self['@current']) != nil && $receiver != undefined) ? (function(){smalltalk.send(self['@current'], "_close", []);return (self['@current']=nil);})() : nil;
return self;},
args: [],
source: unescape('close%0A%09current%0A%09%09%20ifNotNil%3A%20%5B%09current%20close.%0A%09%09%09%09%09current%20%3A%3D%20nil%20%5D.'),
messageSends: ["ifNotNil:", "close"],
referencedClasses: []
}),
smalltalk.AFICssEditor.klass);

smalltalk.addMethod(
unescape('_current'),
smalltalk.method({
selector: unescape('current'),
category: 'not yet classified',
fn: function (){
var self=this;
return (($receiver = self['@current']) == nil || $receiver == undefined) ? (function(){return (self['@current']=smalltalk.send(self, "_new", []));})() : $receiver;
return self;},
args: [],
source: unescape('current%0A%09%5Ecurrent%20ifNil%3A%20%5Bcurrent%20%3A%3D%20self%20new%5D'),
messageSends: ["ifNil:", "new"],
referencedClasses: []
}),
smalltalk.AFICssEditor.klass);

smalltalk.addMethod(
unescape('_initialize'),
smalltalk.method({
selector: unescape('initialize'),
category: 'not yet classified',
fn: function (){
var self=this;
smalltalk.send(smalltalk.send((smalltalk.AFIIDETools || AFIIDETools), "_default", []), "_addButton_action_", ["Editeur CSS", (function(){smalltalk.send(smalltalk.send(smalltalk.send((smalltalk.TabManager || TabManager), "_current", []), "_tabs", []), "_do_", [(function(aTab){return smalltalk.send(smalltalk.send((smalltalk.TabManager || TabManager), "_current", []), "_removeTab_", [aTab]);})]);return smalltalk.send(smalltalk.send(self, "_current", []), "_open", []);})]);
return self;},
args: [],
source: unescape('initialize%0A%09AFIIDETools%20default%20addButton%3A%20%27Editeur%20CSS%27%20action%3A%20%5B%0A%09%09%09TabManager%20current%20tabs%20do%3A%20%5B%3AaTab%7C%20TabManager%20current%20removeTab%3A%20aTab%5D.%0A%09%09%09self%20current%20open%5D.'),
messageSends: ["addButton:action:", "default", "do:", "tabs", "current", "removeTab:", "open"],
referencedClasses: ["AFIIDETools", "TabManager"]
}),
smalltalk.AFICssEditor.klass);

smalltalk.addMethod(
unescape('_open'),
smalltalk.method({
selector: unescape('open'),
category: 'not yet classified',
fn: function (){
var self=this;
(($receiver = self['@current']) == nil || $receiver == undefined) ? (function(){(self['@current']=smalltalk.send(self, "_new", []));return smalltalk.send(self['@current'], "_open", []);})() : $receiver;
return self['@current'];
return self;},
args: [],
source: unescape('open%0A%09current%0A%09%09%20ifNil%3A%20%5B%09current%20%3A%3D%20self%20new.%0A%09%09%09%09current%20open%20%5D.%0A%09%5E%20current'),
messageSends: ["ifNil:", "new", "open"],
referencedClasses: []
}),
smalltalk.AFICssEditor.klass);


smalltalk.addClass('AFISourceArea', smalltalk.SourceArea, ['onChangeBlock'], 'AFI-OPAC');
smalltalk.addMethod(
unescape('_editorChanged'),
smalltalk.method({
selector: unescape('editorChanged'),
category: 'not yet classified',
fn: function (){
var self=this;
(($receiver = self['@onChangeBlock']) != nil && $receiver != undefined) ? (function(){return smalltalk.send(self['@onChangeBlock'], "_value", []);})() : nil;
return self;},
args: [],
source: unescape('editorChanged%0A%09onChangeBlock%20ifNotNil%3A%20%5BonChangeBlock%20value%5D'),
messageSends: ["ifNotNil:", "value"],
referencedClasses: []
}),
smalltalk.AFISourceArea);

smalltalk.addMethod(
unescape('_onChange_'),
smalltalk.method({
selector: unescape('onChange%3A'),
category: 'not yet classified',
fn: function (aBlock){
var self=this;
(self['@onChangeBlock']=aBlock);
return self;},
args: ["aBlock"],
source: unescape('onChange%3A%20aBlock%0A%09onChangeBlock%20%3A%3D%20aBlock'),
messageSends: [],
referencedClasses: []
}),
smalltalk.AFISourceArea);

smalltalk.addMethod(
unescape('_setEditorOn_'),
smalltalk.method({
selector: unescape('setEditorOn%3A'),
category: 'not yet classified',
fn: function (aTextarea){
var self=this;
var params=nil;
(params=smalltalk.HashedCollection._fromPairs_([smalltalk.send("theme", "__minus_gt", ["jtalk"]),smalltalk.send("lineNumbers", "__minus_gt", [true]),smalltalk.send("enterMode", "__minus_gt", ["flat"]),smalltalk.send("matchBrackets", "__minus_gt", [true]),smalltalk.send("electricChars", "__minus_gt", [false]),smalltalk.send("onChange", "__minus_gt", [(function(editor, data){return smalltalk.send(self, "_editorChanged", []);})])]));
self['@editor'] = CodeMirror.fromTextArea(aTextarea,  params);
	 console.log(params);
return self;},
args: ["aTextarea"],
source: unescape('setEditorOn%3A%20aTextarea%0A%09%7Cparams%7C%0A%09params%20%3A%3D%20%23%7B%20%27theme%27%20-%3E%20%27jtalk%27.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%27lineNumbers%27%20-%3E%20true.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%27enterMode%27%20-%3E%20%27flat%27.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%27matchBrackets%27%20-%3E%20true.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%27electricChars%27%20-%3E%20false.%0A%09%09%09%09%27onChange%27%20-%3E%20%5B%3Aeditor%20%3Adata%20%7C%20self%20editorChanged%5D%09%7D.%0A%0A%09%3Cself%5B%27@editor%27%5D%20%3D%20CodeMirror.fromTextArea%28aTextarea%2C%20%20params%29%3B%0A%09%20console.log%28params%29%3E'),
messageSends: [unescape("-%3E"), "editorChanged"],
referencedClasses: []
}),
smalltalk.AFISourceArea);



