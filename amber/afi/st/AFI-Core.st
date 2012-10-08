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

Object subclass: #Ajax
	instanceVariableNames: 'url settings options ajaxRequest'
	package: 'AFI-Core'!

!Ajax methodsFor: 'accessing'!

options
	^ options ifNil: [options := HashedCollection new ]
!

url: aString
	url := aString
! !

!Ajax methodsFor: 'actions'!

abort
	ajaxRequest ifNotNil: [ajaxRequest abort]
!

send
	ajaxRequest := jQuery ajax: url options: options.
! !

!Ajax methodsFor: 'callback'!

onCompleteDo: aBlock
	"A block to be called when the request finishes (after success and error callbacks are executed). Block arguments: jqXHR, textStatus"
	self options at: 'complete' put: aBlock
!

onErrorDo: aBlock
	"A block to be called if the request fails.Block arguments: jqXHR, textStatus, errorThrown"
	self options at: 'error' put: aBlock
!

onSuccessDo: aBlock
	"Set action to execute when Ajax request is successful. Pass received data as block argument. Block arguments: data, textStatus, jqXHR"
	self options at: 'success' put: aBlock
! !

!Ajax class methodsFor: 'initialize'!

url: aString
	^ self new 
		url: aString;
		yourself
! !

