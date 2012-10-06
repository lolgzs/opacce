Smalltalk current createPackage: 'AFI-Core' properties: #{}!
Object subclass: #AFIBootstrap
	instanceVariableNames: ''
	package: 'AFI-Core'!

!AFIBootstrap methodsFor: 'initialize'!

boot
	(Smalltalk current at: #Browser) 
		ifNotNil: [   self onReady: [ self renderTools ] ]
! !

!AFIBootstrap methodsFor: 'jquery'!

onReady: aBlock
	'document' asJQuery ready: aBlock
! !

!AFIBootstrap methodsFor: 'rendering'!

renderTools
	AFIIDETools default appendToJQuery: 'body' asJQuery
! !

!AFIBootstrap class methodsFor: 'initialize-release'!

initialize
	self new boot.
! !

Widget subclass: #AFIIDETools
	instanceVariableNames: 'toolsBrush'
	package: 'AFI-Core'!

!AFIIDETools methodsFor: 'rendering'!

addButton: aString action: aBlock
	toolsBrush appendBlock: [:html|	html button
              									onClick: aBlock;
                           							with: aString ]
!

renderOn: html
	toolsBrush := html div	style: 'position: fixed; top: 0px; z-index:500'.

	self addButton: 'Amber IDE' action: [Browser open].
! !

AFIIDETools class instanceVariableNames: 'default'!

!AFIIDETools class methodsFor: 'buttons'!

addButton: aString action: aBlock
	self default addButton: aString action: aBlock
! !

!AFIIDETools class methodsFor: 'instance creation'!

default
	^ default ifNil: [default := self new]
! !

