smalltalk.addPackage('AFI-OPAC-Tests', {});
smalltalk.addClass('AFICssEditorTest', smalltalk.TestCase, ['styleTag', 'cssTextArea'], 'AFI-OPAC-Tests');
smalltalk.addMethod(
unescape('_setUp'),
smalltalk.method({
selector: unescape('setUp'),
category: 'not yet classified',
fn: function (){
var self=this;
(function($rec){smalltalk.send($rec, "_close", []);smalltalk.send($rec, "_open", []);return smalltalk.send($rec, "_open", []);})((smalltalk.AFICssEditor || AFICssEditor));
smalltalk.send(smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery_", [unescape("%23profil_css_editor")]), "_remove", []);
(self['@styleTag']=smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery_", [unescape("%3Cstyle%20id%3D%22profil_css_editor%22%3E%20body%20%7B%7D%20%3C/style%3E")]));
smalltalk.send(self['@styleTag'], "_appendTo_", [smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery_", ["body"])]);
return self;},
args: [],
source: unescape('setUp%0A%09AFICssEditor%20close%3B%20open%3B%20open.%0A%09%28window%20jQuery%3A%27%23profil_css_editor%27%29%20remove.%0A%0A%09styleTag%20%3A%3D%20window%20jQuery%3A%20%27%3Cstyle%20id%3D%22profil_css_editor%22%3E%20body%20%7B%7D%20%3C/style%3E%27.%0A%09styleTag%20appendTo%3A%20%28window%20jQuery%3A%20%27body%27%29.'),
messageSends: ["close", "open", "remove", "jQuery:", "appendTo:"],
referencedClasses: ["AFICssEditor"]
}),
smalltalk.AFICssEditorTest);

smalltalk.addMethod(
unescape('_testCssEditorShouldContainsProfilCssContent'),
smalltalk.method({
selector: unescape('testCssEditorShouldContainsProfilCssContent'),
category: 'not yet classified',
fn: function (){
var self=this;
smalltalk.send(self['@styleTag'], "_html_", [unescape("body%20%7Bfont-size%3A%2014px%7D")]);
smalltalk.send(smalltalk.send((smalltalk.AFICssEditor || AFICssEditor), "_current", []), "_refreshContents", []);
(self['@cssTextArea']=smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery_", [unescape(".source%20.CodeMirror%20textarea%3Acontains%28%22body%20%7Bfont-size%3A%2014px%7D%22%29")]));
smalltalk.send(self, "_assert_equals_", [smalltalk.send(self['@cssTextArea'], "_length", []), (1)]);
return self;},
args: [],
source: unescape('testCssEditorShouldContainsProfilCssContent%0A%09styleTag%20html%3A%20%27body%20%7Bfont-size%3A%2014px%7D%27.%0A%09AFICssEditor%20current%20refreshContents.%0A%0A%09cssTextArea%20%3A%3D%20%28window%20jQuery%3A%20%27.source%20.CodeMirror%20textarea%3Acontains%28%22body%20%7Bfont-size%3A%2014px%7D%22%29%27%29.%0A%09self%20assert%3A%20cssTextArea%20length%20equals%3A%201'),
messageSends: ["html:", "refreshContents", "current", "jQuery:", "assert:equals:", "length"],
referencedClasses: ["AFICssEditor"]
}),
smalltalk.AFICssEditorTest);

smalltalk.addMethod(
unescape('_testPageShouldContainsOnlyOneCssEditor'),
smalltalk.method({
selector: unescape('testPageShouldContainsOnlyOneCssEditor'),
category: 'not yet classified',
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", [smalltalk.send(smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery_", [unescape(".mtab%20span%3Acontains%28%22Editeur%20CSS%22%29")]), "_length", []), (1)]);
return self;},
args: [],
source: unescape('testPageShouldContainsOnlyOneCssEditor%0A%09self%20assert%3A%20%28window%20jQuery%3A%20%27.mtab%20span%3Acontains%28%22Editeur%20CSS%22%29%27%29%20length%20equals%3A%201'),
messageSends: ["assert:equals:", "length", "jQuery:"],
referencedClasses: []
}),
smalltalk.AFICssEditorTest);

smalltalk.addMethod(
unescape('_testStyleTagShouldBeUpdatedWhenEditorContentChange'),
smalltalk.method({
selector: unescape('testStyleTagShouldBeUpdatedWhenEditorContentChange'),
category: 'not yet classified',
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", [smalltalk.send(smalltalk.send((typeof window == 'undefined' ? nil : window), "_jQuery_", [unescape(".source%20.CodeMirror%20textarea%3Acontains%28%22body%20%7Bfont-size%3A%2012px%7D%22%29")]), "_length", []), (1)]);
return self;},
args: [],
source: unescape('testStyleTagShouldBeUpdatedWhenEditorContentChange%0A%09%22AFICssEditor%20current%20contents%3A%20%27body%20%7Bfont-size%3A%2012px%7D%27.%22%0A%09self%20assert%3A%20%28window%20jQuery%3A%20%27.source%20.CodeMirror%20textarea%3Acontains%28%22body%20%7Bfont-size%3A%2012px%7D%22%29%27%29%20length%20equals%3A%201'),
messageSends: ["assert:equals:", "length", "jQuery:"],
referencedClasses: []
}),
smalltalk.AFICssEditorTest);



