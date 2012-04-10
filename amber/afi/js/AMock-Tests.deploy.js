smalltalk.addClass('AMockOnStringAmberTest', smalltalk.TestCase, ['mock', 'stringAdded'], 'AMock-Tests');
smalltalk.addMethod(
'_setUp',
smalltalk.method({
selector: 'setUp',
fn: function (){
var self=this;
self['@mock']=smalltalk.send((smalltalk.AMockWrapper || AMockWrapper), "_on_", ["Amber"]);
(function($rec){smalltalk.send($rec, "_onMessage_answer_", ["asUppercase", "Smalltalk"]);smalltalk.send($rec, "_onMessage_withArgument_answer_", [unescape("%2C"), " is buggy", "Amber is cool"]);smalltalk.send($rec, "_onMessage_withArgument_answer_", [unescape("%2C"), " is slow", "Amber is fast"]);return smalltalk.send($rec, "_onMessage_do_", ["add:", (function(aString){self['@stringAdded']=aString;return self['@mock'];})]);})(self['@mock']);
return self;}
}),
smalltalk.AMockOnStringAmberTest);

smalltalk.addMethod(
'_testAsUppercaseShouldAnswerSmalltalk',
smalltalk.method({
selector: 'testAsUppercaseShouldAnswerSmalltalk',
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", ["Smalltalk", smalltalk.send(self['@mock'], "_asUppercase", [])]);
return self;}
}),
smalltalk.AMockOnStringAmberTest);

smalltalk.addMethod(
'_testAsLowercaseShouldAnswerAmberLowercased',
smalltalk.method({
selector: 'testAsLowercaseShouldAnswerAmberLowercased',
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", ["amber", smalltalk.send(self['@mock'], "_asLowercase", [])]);
return self;}
}),
smalltalk.AMockOnStringAmberTest);

smalltalk.addMethod(
'_testConcatWithIsBuggyAnswersAmberIsCool',
smalltalk.method({
selector: 'testConcatWithIsBuggyAnswersAmberIsCool',
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", ["Amber is cool", smalltalk.send(self['@mock'], "__comma", [" is buggy"])]);
return self;}
}),
smalltalk.AMockOnStringAmberTest);

smalltalk.addMethod(
'_testConcatWithIsSlowAnswersAmberIsFast',
smalltalk.method({
selector: 'testConcatWithIsSlowAnswersAmberIsFast',
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", ["Amber is fast", smalltalk.send(self['@mock'], "__comma", [" is slow"])]);
return self;}
}),
smalltalk.AMockOnStringAmberTest);

smalltalk.addMethod(
'_testConcatWithIsNiceAnswersAmberIsNice',
smalltalk.method({
selector: 'testConcatWithIsNiceAnswersAmberIsNice',
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", ["Amber is nice", smalltalk.send(self['@mock'], "__comma", [" is nice"])]);
return self;}
}),
smalltalk.AMockOnStringAmberTest);

smalltalk.addMethod(
'_testAddRulesShouldExecuteGivenBlock',
smalltalk.method({
selector: 'testAddRulesShouldExecuteGivenBlock',
fn: function (){
var self=this;
smalltalk.send(self['@mock'], "_add_", ["rules"]);
smalltalk.send(self, "_assert_equals_", ["rules", self['@stringAdded']]);
return self;}
}),
smalltalk.AMockOnStringAmberTest);

smalltalk.addMethod(
'_testAddSmalltalkShouldAnswerTheMock',
smalltalk.method({
selector: 'testAddSmalltalkShouldAnswerTheMock',
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", [self['@mock'], smalltalk.send(self['@mock'], "_add_", ["Smalltalk"])]);
return self;}
}),
smalltalk.AMockOnStringAmberTest);



