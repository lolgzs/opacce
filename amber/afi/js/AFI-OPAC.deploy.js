smalltalk.addPackage('AFI-OPAC', {});
smalltalk.addClass('AFICssEditor', smalltalk.TabWidget, ['sourceArea', 'styleTag'], 'AFI-OPAC');
smalltalk.addMethod(
unescape('_contents_'),
smalltalk.method({
selector: unescape('contents%3A'),
fn: function (aString){
var self=this;
smalltalk.send(self['@sourceArea'], "_val_", [aString]);
return self;}
}),
smalltalk.AFICssEditor);

smalltalk.addMethod(
unescape('_label'),
smalltalk.method({
selector: unescape('label'),
fn: function (){
var self=this;
return "Editeur CSS";
return self;}
}),
smalltalk.AFICssEditor);

smalltalk.addMethod(
unescape('_refreshContents'),
smalltalk.method({
selector: unescape('refreshContents'),
fn: function (){
var self=this;
smalltalk.send(self, "_contents_", [smalltalk.send(smalltalk.send(self, "_styleTag", []), "_html", [])]);
return self;}
}),
smalltalk.AFICssEditor);

smalltalk.addMethod(
unescape('_renderBoxOn_'),
smalltalk.method({
selector: unescape('renderBoxOn%3A'),
fn: function (html){
var self=this;
(self['@sourceArea']=smalltalk.send((smalltalk.AFISourceArea || AFISourceArea), "_new", []));
smalltalk.send(self['@sourceArea'], "_renderOn_", [html]);
smalltalk.send(self, "_refreshContents", []);
smalltalk.send(self['@sourceArea'], "_onChange_", [(function(){return smalltalk.send(self, "_updateStyleTag", []);})]);
return self;}
}),
smalltalk.AFICssEditor);

smalltalk.addMethod(
unescape('_styleTag'),
smalltalk.method({
selector: unescape('styleTag'),
fn: function (){
var self=this;
return (($receiver = self['@styleTag']) == nil || $receiver == undefined) ? (function(){(self['@styleTag']=smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery_", [unescape("%3Cstyle%20id%3D%22profil_css_editor%22%3E%20body%20%7B%7D%20%3C/style%3E")]));return smalltalk.send(self['@styleTag'], "_appendTo_", [smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery_", ["body"])]);})() : $receiver;
return self;}
}),
smalltalk.AFICssEditor);

smalltalk.addMethod(
unescape('_updateStyleTag'),
smalltalk.method({
selector: unescape('updateStyleTag'),
fn: function (){
var self=this;
smalltalk.send(smalltalk.send(self, "_styleTag", []), "_html_", [smalltalk.send(self['@sourceArea'], "_val", [])]);
return self;}
}),
smalltalk.AFICssEditor);


smalltalk.AFICssEditor.klass.iVarNames = ['current'];
smalltalk.addMethod(
unescape('_close'),
smalltalk.method({
selector: unescape('close'),
fn: function (){
var self=this;
(($receiver = self['@current']) != nil && $receiver != undefined) ? (function(){smalltalk.send(self['@current'], "_close", []);return (self['@current']=nil);})() : nil;
return self;}
}),
smalltalk.AFICssEditor.klass);

smalltalk.addMethod(
unescape('_current'),
smalltalk.method({
selector: unescape('current'),
fn: function (){
var self=this;
return (($receiver = self['@current']) == nil || $receiver == undefined) ? (function(){return (self['@current']=smalltalk.send(self, "_new", []));})() : $receiver;
return self;}
}),
smalltalk.AFICssEditor.klass);

smalltalk.addMethod(
unescape('_open'),
smalltalk.method({
selector: unescape('open'),
fn: function (){
var self=this;
(($receiver = self['@current']) == nil || $receiver == undefined) ? (function(){(self['@current']=smalltalk.send(self, "_new", []));return smalltalk.send(self['@current'], "_open", []);})() : $receiver;
return self['@current'];
return self;}
}),
smalltalk.AFICssEditor.klass);


smalltalk.addClass('AFISourceArea', smalltalk.SourceArea, ['onChangeBlock'], 'AFI-OPAC');
smalltalk.addMethod(
unescape('_editorChanged'),
smalltalk.method({
selector: unescape('editorChanged'),
fn: function (){
var self=this;
(($receiver = self['@onChangeBlock']) != nil && $receiver != undefined) ? (function(){return smalltalk.send(self['@onChangeBlock'], "_value", []);})() : nil;
return self;}
}),
smalltalk.AFISourceArea);

smalltalk.addMethod(
unescape('_onChange_'),
smalltalk.method({
selector: unescape('onChange%3A'),
fn: function (aBlock){
var self=this;
(self['@onChangeBlock']=aBlock);
return self;}
}),
smalltalk.AFISourceArea);

smalltalk.addMethod(
unescape('_setEditorOn_'),
smalltalk.method({
selector: unescape('setEditorOn%3A'),
fn: function (aTextarea){
var self=this;
var params=nil;
(params=smalltalk.HashedCollection._fromPairs_([smalltalk.send("theme", "__minus_gt", ["jtalk"]),smalltalk.send("lineNumbers", "__minus_gt", [true]),smalltalk.send("enterMode", "__minus_gt", ["flat"]),smalltalk.send("matchBrackets", "__minus_gt", [true]),smalltalk.send("electricChars", "__minus_gt", [false]),smalltalk.send("onChange", "__minus_gt", [(function(editor, data){return smalltalk.send(self, "_editorChanged", []);})])]));
self['@editor'] = CodeMirror.fromTextArea(aTextarea,  params);
	 console.log(params);
return self;}
}),
smalltalk.AFISourceArea);



