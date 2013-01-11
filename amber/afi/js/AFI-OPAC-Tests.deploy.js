smalltalk.addPackage('AFI-OPAC-Tests', {});
smalltalk.addClass('AFICssEditorTest', smalltalk.TestCase, ['styleTag', 'cssTextArea'], 'AFI-OPAC-Tests');
smalltalk.addMethod(
unescape('_setUp'),
smalltalk.method({
selector: unescape('setUp'),
fn: function (){
var self=this;
(function($rec){smalltalk.send($rec, "_close", []);smalltalk.send($rec, "_open", []);return smalltalk.send($rec, "_open", []);})((smalltalk.AFICssEditor || AFICssEditor));
smalltalk.send(smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery_", [unescape("%23profil_css_editor")]), "_remove", []);
(self['@styleTag']=smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery_", [unescape("%3Cstyle%20id%3D%22profil_css_editor%22%3E%20body%20%7B%7D%20%3C/style%3E")]));
smalltalk.send(self['@styleTag'], "_appendTo_", [smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery_", ["body"])]);
return self;}
}),
smalltalk.AFICssEditorTest);

smalltalk.addMethod(
unescape('_testCssEditorShouldContainsProfilCssContent'),
smalltalk.method({
selector: unescape('testCssEditorShouldContainsProfilCssContent'),
fn: function (){
var self=this;
smalltalk.send(self['@styleTag'], "_html_", [unescape("body%20%7Bfont-size%3A%2014px%7D")]);
smalltalk.send(smalltalk.send((smalltalk.AFICssEditor || AFICssEditor), "_current", []), "_refreshContents", []);
(self['@cssTextArea']=smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery_", [unescape(".source%20.CodeMirror%20textarea%3Acontains%28%22body%20%7Bfont-size%3A%2014px%7D%22%29")]));
smalltalk.send(self, "_assert_equals_", [smalltalk.send(self['@cssTextArea'], "_length", []), (1)]);
return self;}
}),
smalltalk.AFICssEditorTest);

smalltalk.addMethod(
unescape('_testPageShouldContainsOnlyOneCssEditor'),
smalltalk.method({
selector: unescape('testPageShouldContainsOnlyOneCssEditor'),
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", [smalltalk.send(smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery_", [unescape(".mtab%20span%3Acontains%28%22Editeur%20CSS%22%29")]), "_length", []), (1)]);
return self;}
}),
smalltalk.AFICssEditorTest);

smalltalk.addMethod(
unescape('_testStyleTagShouldBeUpdatedWhenEditorContentChange'),
smalltalk.method({
selector: unescape('testStyleTagShouldBeUpdatedWhenEditorContentChange'),
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", [smalltalk.send(smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery_", [unescape(".source%20.CodeMirror%20textarea%3Acontains%28%22body%20%7Bfont-size%3A%2012px%7D%22%29")]), "_length", []), (1)]);
return self;}
}),
smalltalk.AFICssEditorTest);



