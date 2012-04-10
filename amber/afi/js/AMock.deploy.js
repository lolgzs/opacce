smalltalk.addClass('AMockWrapper', smalltalk.Object, ['wrappedObject', 'methodMocks'], 'AMock');
smalltalk.addMethod(
'_wrap_',
smalltalk.method({
selector: 'wrap:',
fn: function (anObject){
var self=this;
self['@wrappedObject']=anObject;
return self;}
}),
smalltalk.AMockWrapper);

smalltalk.addMethod(
'_doesNotUnderstand_',
smalltalk.method({
selector: 'doesNotUnderstand:',
fn: function (aMessage){
var self=this;
return smalltalk.send(self, "_methodMockAnswerForMessage_ifNone_", [aMessage, (function(){return smalltalk.send(self['@wrappedObject'], "_perform_withArguments_", [smalltalk.send(aMessage, "_selector", []), smalltalk.send(aMessage, "_arguments", [])]);})]);
return self;}
}),
smalltalk.AMockWrapper);

smalltalk.addMethod(
'_addMethodMock_',
smalltalk.method({
selector: 'addMethodMock:',
fn: function (anAMethodMock){
var self=this;
smalltalk.send(anAMethodMock, "_mockWrapper_", [self]);
smalltalk.send(smalltalk.send(self, "_methodMocks", []), "_add_", [anAMethodMock]);
return self;}
}),
smalltalk.AMockWrapper);

smalltalk.addMethod(
'_methodMocks',
smalltalk.method({
selector: 'methodMocks',
fn: function (){
var self=this;
return (($receiver = self['@methodMocks']) == nil || $receiver == undefined) ? (function(){return self['@methodMocks']=smalltalk.send((smalltalk.Array || Array), "_new", []);})() : $receiver;
return self;}
}),
smalltalk.AMockWrapper);

smalltalk.addMethod(
'_methodMockAnswerForMessage_ifNone_',
smalltalk.method({
selector: 'methodMockAnswerForMessage:ifNone:',
fn: function (aMessage, aBlock){
var self=this;
var methodMocksForSelector=nil;
var methodMock=nil;
methodMocksForSelector=smalltalk.send(smalltalk.send(self, "_methodMocks", []), "_select_", [(function(aMethodMock){return smalltalk.send(smalltalk.send(aMethodMock, "_selector", []), "__eq", [smalltalk.send(aMessage, "_selector", [])]);})]);
methodMock=smalltalk.send(methodMocksForSelector, "_detect_ifNone_", [(function(aMethodMock){return smalltalk.send(smalltalk.send(aMessage, "_arguments", []), "__eq", [smalltalk.send(aMethodMock, "_arguments", [])]);}), (function(){return smalltalk.send(methodMocksForSelector, "_detect_ifNone_", [(function(aMethodMock){return smalltalk.send(smalltalk.send(aMethodMock, "_arguments", []), "_isEmpty", []);}), (function(){return nil;})]);})]);
return (($receiver = methodMock) == nil || $receiver == undefined) ? (function(){return smalltalk.send(aBlock, "_value", []);})() : (function(){return smalltalk.send(methodMock, "_valueWithArguments_", [smalltalk.send(aMessage, "_arguments", [])]);})();
return self;}
}),
smalltalk.AMockWrapper);

smalltalk.addMethod(
'_onMessage_answer_',
smalltalk.method({
selector: 'onMessage:answer:',
fn: function (aSelector, anObject){
var self=this;
smalltalk.send(self, "_addMethodMock_", [smalltalk.send((smalltalk.AMethodMock || AMethodMock), "_selector_answer_", [aSelector, anObject])]);
return self;}
}),
smalltalk.AMockWrapper);

smalltalk.addMethod(
'_onMessage_withArgument_answer_',
smalltalk.method({
selector: 'onMessage:withArgument:answer:',
fn: function (aSelector, anArgument, anObject){
var self=this;
smalltalk.send(self, "_addMethodMock_", [smalltalk.send((smalltalk.AMethodMock || AMethodMock), "_selector_argument_answer_", [aSelector, anArgument, anObject])]);
return self;}
}),
smalltalk.AMockWrapper);

smalltalk.addMethod(
'_onMessage_do_',
smalltalk.method({
selector: 'onMessage:do:',
fn: function (aSelector, aBlock){
var self=this;
smalltalk.send(self, "_addMethodMock_", [smalltalk.send((smalltalk.AMethodMock || AMethodMock), "_selector_action_", [aSelector, aBlock])]);
return self;}
}),
smalltalk.AMockWrapper);


smalltalk.addMethod(
'_on_',
smalltalk.method({
selector: 'on:',
fn: function (anObject){
var self=this;
return smalltalk.send(smalltalk.send(self, "_new", []), "_wrap_", [anObject]);
return self;}
}),
smalltalk.AMockWrapper.klass);


smalltalk.addClass('AMethodMock', smalltalk.Object, ['selector', 'answer', 'arguments', 'action', 'mockWrapper'], 'AMock');
smalltalk.addMethod(
'_selector_',
smalltalk.method({
selector: 'selector:',
fn: function (aSelector){
var self=this;
self['@selector']=aSelector;
return self;}
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
'_selector',
smalltalk.method({
selector: 'selector',
fn: function (){
var self=this;
return self['@selector'];
return self;}
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
'_answer_',
smalltalk.method({
selector: 'answer:',
fn: function (anObject){
var self=this;
self['@answer']=anObject;
return self;}
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
'_answer',
smalltalk.method({
selector: 'answer',
fn: function (){
var self=this;
return (($receiver = self['@answer']) == nil || $receiver == undefined) ? (function(){return self['@answer']=self['@mockWrapper'];})() : $receiver;
return self;}
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
'_arguments',
smalltalk.method({
selector: 'arguments',
fn: function (){
var self=this;
return (($receiver = self['@arguments']) == nil || $receiver == undefined) ? (function(){return self['@arguments']=smalltalk.send((smalltalk.Array || Array), "_new", []);})() : $receiver;
return self;}
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
'_addArgument_',
smalltalk.method({
selector: 'addArgument:',
fn: function (anObject){
var self=this;
smalltalk.send(smalltalk.send(self, "_arguments", []), "_add_", [anObject]);
return self;}
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
'_addAllArguments_',
smalltalk.method({
selector: 'addAllArguments:',
fn: function (aCollection){
var self=this;
smalltalk.send(smalltalk.send(self, "_arguments", []), "_addAll_", [aCollection]);
return self;}
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
'_action_',
smalltalk.method({
selector: 'action:',
fn: function (aBlock){
var self=this;
self['@action']=aBlock;
return self;}
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
'_action',
smalltalk.method({
selector: 'action',
fn: function (){
var self=this;
return self['@action'];
return self;}
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
'_mockWrapper_',
smalltalk.method({
selector: 'mockWrapper:',
fn: function (anAMockWrapper){
var self=this;
self['@mockWrapper']=anAMockWrapper;
return self;}
}),
smalltalk.AMethodMock);

smalltalk.addMethod(
'_valueWithArguments_',
smalltalk.method({
selector: 'valueWithArguments:',
fn: function (aCollection){
var self=this;
return (($receiver = self['@action']) == nil || $receiver == undefined) ? (function(){return smalltalk.send(self, "_answer", []);})() : (function(){return smalltalk.send(self['@action'], "_valueWithPossibleArguments_", [aCollection]);})();
return self;}
}),
smalltalk.AMethodMock);


smalltalk.addMethod(
'_selector_answer_',
smalltalk.method({
selector: 'selector:answer:',
fn: function (aSelector, anObject){
var self=this;
return (function($rec){smalltalk.send($rec, "_selector_", [aSelector]);return smalltalk.send($rec, "_answer_", [anObject]);})(smalltalk.send(self, "_new", []));
return self;}
}),
smalltalk.AMethodMock.klass);

smalltalk.addMethod(
'_selector_argument_answer_',
smalltalk.method({
selector: 'selector:argument:answer:',
fn: function (aSelector, anArgument, anObject){
var self=this;
return (function($rec){smalltalk.send($rec, "_selector_", [aSelector]);smalltalk.send($rec, "_addArgument_", [anArgument]);return smalltalk.send($rec, "_answer_", [anObject]);})(smalltalk.send(self, "_new", []));
return self;}
}),
smalltalk.AMethodMock.klass);

smalltalk.addMethod(
'_selector_action_',
smalltalk.method({
selector: 'selector:action:',
fn: function (aSelector, aBlock){
var self=this;
return (function($rec){smalltalk.send($rec, "_selector_", [aSelector]);return smalltalk.send($rec, "_action_", [aBlock]);})(smalltalk.send(self, "_new", []));
return self;}
}),
smalltalk.AMethodMock.klass);


