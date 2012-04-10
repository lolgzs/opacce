TestCase subclass: #AMockOnStringAmberTest
	instanceVariableNames: 'mock stringAdded'
	category: 'AMock-Tests'!

!AMockOnStringAmberTest methodsFor: 'running'!

setUp
	mock := AMockWrapper on: 'Amber'. 
	
	mock 
		onMessage: #asUppercase answer: 'Smalltalk';
		onMessage: ',' withArgument: ' is buggy' answer: 'Amber is cool';
		onMessage: ',' withArgument: ' is slow' answer: 'Amber is fast';
		onMessage: 'add:' do: [:aString|  stringAdded := aString.
                                      					  mock					]
! !

!AMockOnStringAmberTest methodsFor: 'tests'!

testAsUppercaseShouldAnswerSmalltalk
	self assert: 'Smalltalk' equals: mock asUppercase
!

testAsLowercaseShouldAnswerAmberLowercased
	self assert: 'amber' equals: mock asLowercase
!

testConcatWithIsBuggyAnswersAmberIsCool
	self assert: 'Amber is cool' equals: mock ,' is buggy'
!

testConcatWithIsSlowAnswersAmberIsFast
	self assert: 'Amber is fast' equals: mock ,' is slow'
!

testConcatWithIsNiceAnswersAmberIsNice
	self assert: 'Amber is nice' equals: mock ,' is nice'
!

testAddRulesShouldExecuteGivenBlock
  mock add: 'rules'.
  self assert: 'rules' equals: stringAdded
!

testAddSmalltalkShouldAnswerTheMock
  self assert: mock equals: (mock add: 'Smalltalk').
! !

