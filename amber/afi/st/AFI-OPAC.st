Smalltalk current createPackage: 'AFI-OPAC' properties: #{}!
TabWidget subclass: #AFICssEditor
	instanceVariableNames: 'sourceArea styleTag'
	package: 'AFI-OPAC'!

!AFICssEditor methodsFor: 'not yet classified'!

contents: aString
	sourceArea val: aString
!

label
    ^ 'Editeur CSS'
!

refreshContents
	self contents: self styleTag html
!

renderBoxOn: html
    sourceArea := AFISourceArea new.
    sourceArea renderOn: html.

    self refreshContents.

    sourceArea onChange: [self updateStyleTag]
!

styleTag
	^ styleTag ifNil: [
		styleTag := window jQuery: '<style id="profil_css_editor"> body {} </style>'.
		styleTag appendTo: (window jQuery: 'body').
	]
!

updateStyleTag
	self styleTag html: sourceArea val
! !

AFICssEditor class instanceVariableNames: 'current'!

!AFICssEditor class methodsFor: 'not yet classified'!

close
	current
		 ifNotNil: [	current close.
					current := nil ].
!

current
	^current ifNil: [current := self new]
!

initialize
	AFIIDETools default addButton: 'Editeur CSS' action: [self open]
!

open
	current
		 ifNil: [	current := self new.
				current open ].
	^ current
! !

SourceArea subclass: #AFISourceArea
	instanceVariableNames: 'onChangeBlock'
	package: 'AFI-OPAC'!

!AFISourceArea methodsFor: 'not yet classified'!

editorChanged
	onChangeBlock ifNotNil: [onChangeBlock value]
!

onChange: aBlock
	onChangeBlock := aBlock
!

setEditorOn: aTextarea
	|params|
	params := #{ 'theme' -> 'jtalk'.
                		'lineNumbers' -> true.
                		'enterMode' -> 'flat'.
                		'matchBrackets' -> true.
                		'electricChars' -> false.
				'onChange' -> [:editor :data | self editorChanged]	}.

	<self['@editor'] = CodeMirror.fromTextArea(aTextarea,  params);
	 console.log(params)>
! !

