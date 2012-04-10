Object subclass: #AMockWrapper
	instanceVariableNames: 'wrappedObject methodMocks'
	category: 'AMock'!

!AMockWrapper methodsFor: 'accessing'!

wrap: anObject
	wrappedObject := anObject
!

methodMocks
	^ methodMocks ifNil: [methodMocks := Array new]
! !

!AMockWrapper methodsFor: 'error handling'!

doesNotUnderstand: aMessage
	^  self 
		methodMockAnswerForMessage: aMessage
		ifNone: [ wrappedObject perform: aMessage selector withArguments: aMessage arguments ]
!

methodMockAnswerForMessage: aMessage ifNone: aBlock
	|methodMocksForSelector methodMock|
	methodMocksForSelector := self methodMocks select: [:aMethodMock| aMethodMock selector = aMessage selector].

	methodMock := methodMocksForSelector 
						detect: [:aMethodMock|  aMessage arguments = aMethodMock arguments]  
           					ifNone: [methodMocksForSelector
                                                         		detect: [:aMethodMock| aMethodMock arguments isEmpty ] 
                                                       			ifNone: [nil]	].

        ^ methodMock 
		ifNotNil: [methodMock valueWithArguments: aMessage arguments ] 
		ifNil: [aBlock value].
! !

!AMockWrapper methodsFor: 'method mocks'!

addMethodMock: anAMethodMock
	anAMethodMock mockWrapper: self.
	self methodMocks add: anAMethodMock.
!

onMessage: aSelector answer: anObject
	self addMethodMock: (AMethodMock selector: aSelector answer: anObject)
!

onMessage: aSelector withArgument: anArgument answer: anObject
	self addMethodMock: (AMethodMock selector: aSelector argument: anArgument answer: anObject)
!

onMessage: aSelector do: aBlock
	self addMethodMock: (AMethodMock selector: aSelector action: aBlock)
! !

!AMockWrapper class methodsFor: 'instance creation'!

on: anObject
	^ self new wrap: anObject
! !

Object subclass: #AMethodMock
	instanceVariableNames: 'selector answer arguments action mockWrapper'
	category: 'AMock'!

!AMethodMock methodsFor: 'accessing'!

selector: aSelector
	selector := aSelector
!

selector
	^ selector
!

answer: anObject
	answer := anObject
!

answer
	^ answer ifNil: [answer := mockWrapper]
!

arguments
	^ arguments ifNil: [arguments := Array new]
!

addArgument: anObject
	self arguments add: anObject
!

addAllArguments: aCollection
	self arguments addAll: aCollection
!

action: aBlock
	action := aBlock
!

action
	^ action
!

mockWrapper: anAMockWrapper
	mockWrapper := anAMockWrapper
! !

!AMethodMock methodsFor: 'evaluate'!

valueWithArguments: aCollection
	^ action 
		ifNotNil: [ action valueWithPossibleArguments: aCollection ] 
		ifNil: [self answer]
! !

!AMethodMock class methodsFor: 'instance creation'!

selector: aSelector answer: anObject
	^ self new
		selector: aSelector;
		answer: anObject
!

selector: aSelector argument: anArgument answer: anObject
	^ self new
		selector: aSelector;
		addArgument: anArgument;
		answer: anObject
!

selector: aSelector action: aBlock
	^ self new
		selector: aSelector;
		action: aBlock
! !

