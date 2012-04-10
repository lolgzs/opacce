smalltalk.addClass('AMockOnStringAmberTest', smalltalk.TestCase, ['mock', 'stringAdded'], 'AMock-Tests');
smalltalk.addMethod(
unescape('_setUp'),
smalltalk.method({
selector: unescape('setUp'),
category: 'running',
fn: function (){
var self=this;
self['@mock']=smalltalk.send((smalltalk.AMockWrapper || AMockWrapper), "_on_", ["Amber"]);
(function($rec){smalltalk.send($rec, "_onMessage_answer_", ["asUppercase", "Smalltalk"]);smalltalk.send($rec, "_onMessage_withArgument_answer_", [unescape("%2C"), " is buggy", "Amber is cool"]);smalltalk.send($rec, "_onMessage_withArgument_answer_", [unescape("%2C"), " is slow", "Amber is fast"]);return smalltalk.send($rec, "_onMessage_do_", ["add:", (function(aString){self['@stringAdded']=aString;return self['@mock'];})]);})(self['@mock']);
return self;},
args: [],
source: unescape('setUp%0A%09mock%20%3A%3D%20AMockWrapper%20on%3A%20%27Amber%27.%20%0A%09%0A%09mock%20%0A%09%09onMessage%3A%20%23asUppercase%20answer%3A%20%27Smalltalk%27%3B%0A%09%09onMessage%3A%20%27%2C%27%20withArgument%3A%20%27%20is%20buggy%27%20answer%3A%20%27Amber%20is%20cool%27%3B%0A%09%09onMessage%3A%20%27%2C%27%20withArgument%3A%20%27%20is%20slow%27%20answer%3A%20%27Amber%20is%20fast%27%3B%0A%09%09onMessage%3A%20%27add%3A%27%20do%3A%20%5B%3AaString%7C%20%20stringAdded%20%3A%3D%20aString.%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09%20%20mock%09%09%09%09%09%5D'),
messageSends: ["on:", "onMessage:answer:", "onMessage:withArgument:answer:", "onMessage:do:"],
referencedClasses: [smalltalk.AMockWrapper]
}),
smalltalk.AMockOnStringAmberTest);

smalltalk.addMethod(
unescape('_testAsUppercaseShouldAnswerSmalltalk'),
smalltalk.method({
selector: unescape('testAsUppercaseShouldAnswerSmalltalk'),
category: 'tests',
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", ["Smalltalk", smalltalk.send(self['@mock'], "_asUppercase", [])]);
return self;},
args: [],
source: unescape('testAsUppercaseShouldAnswerSmalltalk%0A%09self%20assert%3A%20%27Smalltalk%27%20equals%3A%20mock%20asUppercase%0A%09'),
messageSends: ["assert:equals:", "asUppercase"],
referencedClasses: []
}),
smalltalk.AMockOnStringAmberTest);

smalltalk.addMethod(
unescape('_testAsLowercaseShouldAnswerAmberLowercased'),
smalltalk.method({
selector: unescape('testAsLowercaseShouldAnswerAmberLowercased'),
category: 'tests',
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", ["amber", smalltalk.send(self['@mock'], "_asLowercase", [])]);
return self;},
args: [],
source: unescape('testAsLowercaseShouldAnswerAmberLowercased%0A%09self%20assert%3A%20%27amber%27%20equals%3A%20mock%20asLowercase%0A%09'),
messageSends: ["assert:equals:", "asLowercase"],
referencedClasses: []
}),
smalltalk.AMockOnStringAmberTest);

smalltalk.addMethod(
unescape('_testConcatWithIsBuggyAnswersAmberIsCool'),
smalltalk.method({
selector: unescape('testConcatWithIsBuggyAnswersAmberIsCool'),
category: 'tests',
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", ["Amber is cool", smalltalk.send(self['@mock'], "__comma", [" is buggy"])]);
return self;},
args: [],
source: unescape('testConcatWithIsBuggyAnswersAmberIsCool%0A%09self%20assert%3A%20%27Amber%20is%20cool%27%20equals%3A%20mock%20%2C%27%20is%20buggy%27'),
messageSends: ["assert:equals:", unescape("%2C")],
referencedClasses: []
}),
smalltalk.AMockOnStringAmberTest);

smalltalk.addMethod(
unescape('_testConcatWithIsSlowAnswersAmberIsFast'),
smalltalk.method({
selector: unescape('testConcatWithIsSlowAnswersAmberIsFast'),
category: 'tests',
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", ["Amber is fast", smalltalk.send(self['@mock'], "__comma", [" is slow"])]);
return self;},
args: [],
source: unescape('testConcatWithIsSlowAnswersAmberIsFast%0A%09self%20assert%3A%20%27Amber%20is%20fast%27%20equals%3A%20mock%20%2C%27%20is%20slow%27'),
messageSends: ["assert:equals:", unescape("%2C")],
referencedClasses: []
}),
smalltalk.AMockOnStringAmberTest);

smalltalk.addMethod(
unescape('_testConcatWithIsNiceAnswersAmberIsNice'),
smalltalk.method({
selector: unescape('testConcatWithIsNiceAnswersAmberIsNice'),
category: 'tests',
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", ["Amber is nice", smalltalk.send(self['@mock'], "__comma", [" is nice"])]);
return self;},
args: [],
source: unescape('testConcatWithIsNiceAnswersAmberIsNice%0A%09self%20assert%3A%20%27Amber%20is%20nice%27%20equals%3A%20mock%20%2C%27%20is%20nice%27'),
messageSends: ["assert:equals:", unescape("%2C")],
referencedClasses: []
}),
smalltalk.AMockOnStringAmberTest);

smalltalk.addMethod(
unescape('_testAddRulesShouldExecuteGivenBlock'),
smalltalk.method({
selector: unescape('testAddRulesShouldExecuteGivenBlock'),
category: 'tests',
fn: function (){
var self=this;
smalltalk.send(self['@mock'], "_add_", ["rules"]);
smalltalk.send(self, "_assert_equals_", ["rules", self['@stringAdded']]);
return self;},
args: [],
source: unescape('testAddRulesShouldExecuteGivenBlock%0A%20%20mock%20add%3A%20%27rules%27.%0A%20%20self%20assert%3A%20%27rules%27%20equals%3A%20stringAdded%20'),
messageSends: ["add:", "assert:equals:"],
referencedClasses: []
}),
smalltalk.AMockOnStringAmberTest);

smalltalk.addMethod(
unescape('_testAddSmalltalkShouldAnswerTheMock'),
smalltalk.method({
selector: unescape('testAddSmalltalkShouldAnswerTheMock'),
category: 'tests',
fn: function (){
var self=this;
smalltalk.send(self, "_assert_equals_", [self['@mock'], smalltalk.send(self['@mock'], "_add_", ["Smalltalk"])]);
return self;},
args: [],
source: unescape('testAddSmalltalkShouldAnswerTheMock%0A%20%20self%20assert%3A%20mock%20equals%3A%20%28mock%20add%3A%20%27Smalltalk%27%29.'),
messageSends: ["assert:equals:", "add:"],
referencedClasses: []
}),
smalltalk.AMockOnStringAmberTest);



