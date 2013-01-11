Smalltalk current createPackage: 'AFI-OPAC-Tests' properties: #{}!
TestCase subclass: #AFICssEditorTest
	instanceVariableNames: 'styleTag cssTextArea'
	package: 'AFI-OPAC-Tests'!

!AFICssEditorTest methodsFor: 'not yet classified'!

setUp
	AFICssEditor close; open; open.
	(window jQuery:'#profil_css_editor') remove.

	styleTag := window jQuery: '<style id="profil_css_editor"> body {} </style>'.
	styleTag appendTo: (window jQuery: 'body').
!

testCssEditorShouldContainsProfilCssContent
	styleTag html: 'body {font-size: 14px}'.
	AFICssEditor current refreshContents.

	cssTextArea := (window jQuery: '.source .CodeMirror textarea:contains("body {font-size: 14px}")').
	self assert: cssTextArea length equals: 1
!

testPageShouldContainsOnlyOneCssEditor
	self assert: (window jQuery: '.mtab span:contains("Editeur CSS")') length equals: 1
!

testStyleTagShouldBeUpdatedWhenEditorContentChange
	"AFICssEditor current contents: 'body {font-size: 12px}'."
	self assert: (window jQuery: '.source .CodeMirror textarea:contains("body {font-size: 12px}")') length equals: 1
! !

