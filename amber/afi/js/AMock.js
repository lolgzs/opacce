smalltalk.addClass('AMockWrapper', smalltalk.Object, ['wrappedObject', 'methodMocks'], 'AMock');
smalltalk.addMethod(
unescape('_wrap_'),
smalltalk.method({
selector: unescape('wrap%3A'),
category: 'accessing',
fn: function (anObject){
var self=this;
self['@wrappedObject']=anObject;
return self;},
args: ["anObject"],
source: unescape('wrap%3A%20anObject%0A%09wrappedObject%20%3A%3D%20anObject'),
messageSends: [],
referencedClasses: []
}),
smalltalk.AMockWrapper);

smalltalk.addMethod(
unescape('_doesNotUnderstand_'),
smalltalk.method({
selector: unescape('doesNotUnderstand%3A'),
category: 'error handling',
fn: function (aMessage){
var self=this;
return smalltalk.send(self, "_methodMockAnswerForMessage_ifNone_", [aMessage, (function(){return smalltalk.send(self['@wrappedObject'], "_perform_withArguments_", [smalltalk.send(aMessage, "_selector", []), smalltalk.send(aMessage, "_arguments", [])]);})]);
return self;},
args: ["aMessage"],
source: unescape('doesNotUnderstand%3A%20aMessage%0A%09%5E%20%20self%20%0A%09%09methodMockAnswerForMessage%3A%20aMessage%0A%09%09ifNone%3A%20%5B%20wrappedObject%20perform%3A%20aMessage%20selector%20withArguments%3A%20aMessage%20arguments%20%5D'),
messageSends: ["methodMockAnswerForMessage:ifNone:", "perform:withArguments:", "selector", "arguments"],
referencedClasses: []
}),
smalltalk.AMockWrapper);

smalltalk.addMethod(
unescape('_addMethodMock_'),
smalltalk.method({
selector: unescape('addMethodMock%3A'),
category: 'method mocks',
fn: function (anAMethodMock){
var self=this;
smalltalk.send(anAMethodMock, "_mockWrapper_", [self]);
smalltalk.send(smalltalk.send(self, "_methodMocks", []), "_add_", [anAMethodMock]);
return self;},
args: ["anAMethodMock"],
source: unescape('addMethodMock%3A%20anAMethodMock%0A%09anAMethodMock%20mockWrapper%3A%20self.%0A%09self%20methodMocks%20add%3A%20anAMethodMock.'),
messageSends: ["mockWrapper:", "add:", "methodMocks"],
referencedClasses: []
}),
smalltalk.AMockWrapper);

smalltalk.addMethod(
unescape('_methodMocks'),
smalltalk.method({
selector: unescape('methodMocks'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@methodMocks']) == nil || $receiver == undefined) ? (function(){return self['@methodMocks']=smalltalk.send((smalltalk.Array || Array), "_new", []);})() : $receiver;
return self;},
args: [],
source: unescape('methodMocks%0A%09%5E%20methodMocks%20ifNil%3A%20%5BmethodMocks%20%3A%3D%20Array%20new%5D'),
messageSends: ["ifNil:", "new"],
referencedClasses: [smalltalk.Array]
}),
smalltalk.AMockWrapper);

smalltalk.addMethod(
unescape('_methodMockAnswerForMessage_ifNone_'),
smalltalk.method({
selector: unescape('methodMockAnswerForMessage%3AifNone%3A'),
category: 'error handling',
fn: function (aMessage, aBlock){
var self=this;
var methodMocksForSelector=nil;
var methodMock=nil;
methodMocksForSelector=smalltalk.send(smalltalk.send(self, "_methodMocks", []), "_select_", [(function(aMethodMock){return smalltalk.send(smalltalk.send(aMethodMock, "_selector", []), "__eq", [smalltalk.send(aMessage, "_selector", [])]);})]);
methodMock=smalltalk.send(methodMocksForSelector, "_detect_ifNone_", [(function(aMethodMock){return smalltalk.send(smalltalk.send(aMessage, "_arguments", []), "__eq", [smalltalk.send(aMethodMock, "_arguments", [])]);}), (function(){return smalltalk.send(methodMocksForSelector, "_detect_ifNone_", [(function(aMethodMock){return smalltalk.send(smalltalk.send(aMethodMock, "_arguments", []), "_isEmpty", []);}), (function(){return nil;})]);})]);
return (($receiver = methodMock) == nil || $receiver == undefined) ? (function(){return smalltalk.send(aBlock, "_value", []);})() : (function(){return smalltalk.send(methodMock, "_valueWithArguments_", [smalltalk.send(aMessage, "_arguments", [])]);})();
return self;},
args: ["aMessage", "aBlock"],
source: unescape('methodMockAnswerForMessage%3A%20aMessage%20ifNone%3A%20aBlock%0A%09%7CmethodMocksForSelector%20methodMock%7C%0A%09methodMocksForSelector%20%3A%3D%20self%20methodMocks%20select%3A%20%5B%3AaMethodMock%7C%20aMethodMock%20selector%20%3D%20aMessage%20selector%5D.%0A%0A%09methodMock%20%3A%3D%20methodMocksForSelector%20%0A%09%09%09%09%09%09detect%3A%20%5B%3AaMethodMock%7C%20%20aMessage%20arguments%20%3D%20aMethodMock%20arguments%5D%20%20%0A%20%20%20%20%20%20%20%20%20%20%20%09%09%09%09%09ifNone%3A%20%5BmethodMocksForSelector%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09detect%3A%20%5B%3AaMethodMock%7C%20aMethodMock%20arguments%20isEmpty%20%5D%20%0A%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%20%09%09%09ifNone%3A%20%5Bnil%5D%09%5D.%0A%0A%20%20%20%20%20%20%20%20%5E%20methodMock%20%0A%09%09ifNotNil%3A%20%5BmethodMock%20valueWithArguments%3A%20aMessage%20arguments%20%5D%20%0A%09%09ifNil%3A%20%5BaBlock%20value%5D.'),
messageSends: ["select:", "methodMocks", unescape("%3D"), "selector", "detect:ifNone:", "arguments", "isEmpty", "ifNotNil:ifNil:", "value", "valueWithArguments:"],
referencedClasses: []
}),
smalltalk.AMockWrapper);

smalltalk.addMethod(
unescape('_onMessage_answer_'),
smalltalk.method({
selector: unescape('onMessage%3Aanswer%3A'),
category: 'method mocks',
fn: function (aSelector, anObject){
var self=this;
smalltalk.send(self, "_addMethodMock_", [smalltalk.send((smalltalk.AMethodMock || AMethodMock), "_selector_answer_", [aSelector, anObject])]);
return self;},
args: ["aSelector", "anObject"],
source: unescape('onMessage%3A%20aSelector%20answer%3A%20anObject%0A%09self%20addMethodMock%3A%20%28AMethodMock%20selector%3A%20aSelector%20answer%3A%20anObject%29'),
messageSends: ["addMethodMock:", "selector:answer:"],
referencedClasses: [smalltalk.nil]
}),
smalltalk.AMockWrapper);

smalltalk.addMethod(
unescape('_onMessage_withArgument_answer_'),
smalltalk.method({
selector: unescape('onMessage%3AwithArgument%3Aanswer%3A'),
category: 'method mocks',
fn: function (aSelector, anArgument, anObject){
var self=this;
smalltalk.send(self, "_addMethodMock_", [smalltalk.send((smalltalk.AMethodMock || AMethodMock), "_selector_argument_answer_", [aSelector, anArgument, anObject])]);
return self;},
args: ["aSelector", "anArgument", "anObject"],
source: unescape('onMessage%3A%20aSelector%20withArgument%3A%20anArgument%20answer%3A%20anObject%0A%09self%20addMethodMock%3A%20%28AMethodMock%20selector%3A%20aSelector%20argument%3A%20anArgument%20answer%3A%20anObject%29'),
messageSends: ["addMethodMock:", "selector:argument:answer:"],
referencedClasses: [smalltalk.nil]
}),
smalltalk.AMockWrapper);

smalltalk.addMethod(
unescape('_onMessage_do_'),
smalltalk.method({
selector: unescape('onMessage%3Ado%3A'),
category: 'method mocks',
fn: function (aSelector, aBlock){
var self=this;
smalltalk.send(self, "_addMethodMock_", [smalltalk.send((smalltalk.AMethodMock || AMethodMock), "_selector_action_", [aSelector, aBlock])]);
return self;},
args: ["aSelector", "aBlock"],
source: unescape('onMessage%3A%20aSelector%20do%3A%20aBlock%0A%09self%20addMethodMock%3A%20%28AMethodMock%20selector%3A%20aSelector%20action%3A%20aBlock%29'),
messageSends: ["addMethodMock:", "selector:action:"],
referencedClasses: [smalltalk.nil]
}),
smalltalk.AMockWrapper);


smalltalk.addMethod(
unescape('_on_'),
smalltalk.method({
selector: unescape('on%3A'),
category: 'instance creation',
fn: function (anObject){
var self=this;
return smalltalk.send(smalltalk.send(self, "_new", []), "_wrap_", [anObject]);
return self;},
args: ["anObject"],
source: unescape('on%3A%20anObject%0A%09%5E%20self%20new%20wrap%3A%20anObject'),
messageSends: ["wrap:", "new"],
referencedClasses: []
}),
smalltalk.AMockWrapper.klass);


smalltalk.addClass('AMethodMock', smalltalk.Object, ['selector', 'answer', 'arguments', 'action', 'mockWrapper'], 'AMock');
smalltalk.addMethod(
unescape('_selector_'),
smalltalk.method({
selector: unescape('selector%3A'),
category: 'accessing',
fn: function (aSelector){
var self=this;
self['@selector']=aSelector;
return self;},
args: ["aSelector"],
source: unescape('selector%3A%20aSelector%0A%09selector%20%3A%3D%20aSelector'),
messageSends: [],
referencedClasses: []
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
unescape('_selector'),
smalltalk.method({
selector: unescape('selector'),
category: 'accessing',
fn: function (){
var self=this;
return self['@selector'];
return self;},
args: [],
source: unescape('selector%0A%09%5E%20selector'),
messageSends: [],
referencedClasses: []
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
unescape('_answer_'),
smalltalk.method({
selector: unescape('answer%3A'),
category: 'accessing',
fn: function (anObject){
var self=this;
self['@answer']=anObject;
return self;},
args: ["anObject"],
source: unescape('answer%3A%20anObject%0A%09answer%20%3A%3D%20anObject'),
messageSends: [],
referencedClasses: []
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
unescape('_answer'),
smalltalk.method({
selector: unescape('answer'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@answer']) == nil || $receiver == undefined) ? (function(){return self['@answer']=self['@mockWrapper'];})() : $receiver;
return self;},
args: [],
source: unescape('answer%0A%09%5E%20answer%20ifNil%3A%20%5Banswer%20%3A%3D%20mockWrapper%5D'),
messageSends: ["ifNil:"],
referencedClasses: []
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
unescape('_arguments'),
smalltalk.method({
selector: unescape('arguments'),
category: 'accessing',
fn: function (){
var self=this;
return (($receiver = self['@arguments']) == nil || $receiver == undefined) ? (function(){return self['@arguments']=smalltalk.send((smalltalk.Array || Array), "_new", []);})() : $receiver;
return self;},
args: [],
source: unescape('arguments%0A%09%5E%20arguments%20ifNil%3A%20%5Barguments%20%3A%3D%20Array%20new%5D'),
messageSends: ["ifNil:", "new"],
referencedClasses: [smalltalk.Array]
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
unescape('_addArgument_'),
smalltalk.method({
selector: unescape('addArgument%3A'),
category: 'accessing',
fn: function (anObject){
var self=this;
smalltalk.send(smalltalk.send(self, "_arguments", []), "_add_", [anObject]);
return self;},
args: ["anObject"],
source: unescape('addArgument%3A%20anObject%0A%09self%20arguments%20add%3A%20anObject'),
messageSends: ["add:", "arguments"],
referencedClasses: []
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
unescape('_addAllArguments_'),
smalltalk.method({
selector: unescape('addAllArguments%3A'),
category: 'accessing',
fn: function (aCollection){
var self=this;
smalltalk.send(smalltalk.send(self, "_arguments", []), "_addAll_", [aCollection]);
return self;},
args: ["aCollection"],
source: unescape('addAllArguments%3A%20aCollection%0A%09self%20arguments%20addAll%3A%20aCollection'),
messageSends: ["addAll:", "arguments"],
referencedClasses: []
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
unescape('_action_'),
smalltalk.method({
selector: unescape('action%3A'),
category: 'accessing',
fn: function (aBlock){
var self=this;
self['@action']=aBlock;
return self;},
args: ["aBlock"],
source: unescape('action%3A%20aBlock%0A%09action%20%3A%3D%20aBlock%20'),
messageSends: [],
referencedClasses: []
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
unescape('_action'),
smalltalk.method({
selector: unescape('action'),
category: 'accessing',
fn: function (){
var self=this;
return self['@action'];
return self;},
args: [],
source: unescape('action%0A%09%5E%20action'),
messageSends: [],
referencedClasses: []
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
unescape('_mockWrapper_'),
smalltalk.method({
selector: unescape('mockWrapper%3A'),
category: 'accessing',
fn: function (anAMockWrapper){
var self=this;
self['@mockWrapper']=anAMockWrapper;
return self;},
args: ["anAMockWrapper"],
source: unescape('mockWrapper%3A%20anAMockWrapper%0A%09mockWrapper%20%3A%3D%20anAMockWrapper'),
messageSends: [],
referencedClasses: []
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
unescape('_valueWithArguments_'),
smalltalk.method({
selector: unescape('valueWithArguments%3A'),
category: 'evaluate',
fn: function (aCollection){
var self=this;
return (($receiver = self['@action']) == nil || $receiver == undefined) ? (function(){return smalltalk.send(self, "_answer", []);})() : (function(){return smalltalk.send(self['@action'], "_valueWithPossibleArguments_", [aCollection]);})();
return self;},
args: ["aCollection"],
source: unescape('valueWithArguments%3A%20aCollection%0A%09%5E%20action%20%0A%09%09ifNotNil%3A%20%5B%20action%20valueWithPossibleArguments%3A%20aCollection%20%5D%20%0A%09%09ifNil%3A%20%5Bself%20answer%5D'),
messageSends: ["ifNotNil:ifNil:", "answer", "valueWithPossibleArguments:"],
referencedClasses: []
}),
smalltalk.AMethodMock);


smalltalk.addMethod(
unescape('_selector_answer_'),
smalltalk.method({
selector: unescape('selector%3Aanswer%3A'),
category: 'instance creation',
fn: function (aSelector, anObject){
var self=this;
return (function($rec){smalltalk.send($rec, "_selector_", [aSelector]);return smalltalk.send($rec, "_answer_", [anObject]);})(smalltalk.send(self, "_new", []));
return self;},
args: ["aSelector", "anObject"],
source: unescape('selector%3A%20aSelector%20answer%3A%20anObject%0A%09%5E%20self%20new%0A%09%09selector%3A%20aSelector%3B%0A%09%09answer%3A%20anObject'),
messageSends: ["selector:", "answer:", "new"],
referencedClasses: []
}),
smalltalk.AMethodMock.klass);

smalltalk.addMethod(
unescape('_selector_argument_answer_'),
smalltalk.method({
selector: unescape('selector%3Aargument%3Aanswer%3A'),
category: 'instance creation',
fn: function (aSelector, anArgument, anObject){
var self=this;
return (function($rec){smalltalk.send($rec, "_selector_", [aSelector]);smalltalk.send($rec, "_addArgument_", [anArgument]);return smalltalk.send($rec, "_answer_", [anObject]);})(smalltalk.send(self, "_new", []));
return self;},
args: ["aSelector", "anArgument", "anObject"],
source: unescape('selector%3A%20aSelector%20argument%3A%20anArgument%20answer%3A%20anObject%0A%09%5E%20self%20new%0A%09%09selector%3A%20aSelector%3B%0A%09%09addArgument%3A%20anArgument%3B%0A%09%09answer%3A%20anObject'),
messageSends: ["selector:", "addArgument:", "answer:", "new"],
referencedClasses: []
}),
smalltalk.AMethodMock.klass);

smalltalk.addMethod(
unescape('_selector_action_'),
smalltalk.method({
selector: unescape('selector%3Aaction%3A'),
category: 'instance creation',
fn: function (aSelector, aBlock){
var self=this;
return (function($rec){smalltalk.send($rec, "_selector_", [aSelector]);return smalltalk.send($rec, "_action_", [aBlock]);})(smalltalk.send(self, "_new", []));
return self;},
args: ["aSelector", "aBlock"],
source: unescape('selector%3A%20aSelector%20action%3A%20aBlock%0A%09%5E%20self%20new%0A%09%09selector%3A%20aSelector%3B%0A%09%09action%3A%20aBlock'),
messageSends: ["selector:", "action:", "new"],
referencedClasses: []
}),
smalltalk.AMethodMock.klass);


