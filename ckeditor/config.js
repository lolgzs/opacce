/*
Copyright (c) 2010, AFI
For licensing, see LICENSE.html or http://ckeditor.com/license
*/

(function()
{
	// Insert tag {FIN} in the document
	var insertEndTagCmd = {
		exec : function( editor )
		{
			var doc = editor.document.$;
			doc.execCommand( 'inserthtml', false, '{FIN}' );
			return true;
		}
	};

	var pluginName = 'insertendtag';

	// Register the plugin.
	CKEDITOR.plugins.add( pluginName, {
		init : function( editor )
		{
			var command = editor.addCommand( pluginName, insertEndTagCmd );

			editor.ui.addButton( 'InsertEndTag',
			{
				label : 'Fin de résumé',
				command : pluginName
			});
		}

	})
	})();

CKEDITOR.editorConfig = function( config )
{
	/* Documentation des options:
	 *  http://docs.cksource.com/ckeditor_api/symbols/CKEDITOR.config.html
	 */
	config.language = 'fr';
	config.uiColor = '#F0F0F0';
	config.resize_enabled = false;
	config.toolbarCanCollapse = false;
	config.extraPlugins = 'insertendtag';
	config.skin = 'kama';
	config.removePlugins = 'elementspath';
	config.templates_replaceContent = false;
	config.bodyClass = 'boiteMilieu ckeditor_content';


	// Toolbar par défaut:
	//	config.toolbar_Full =
	//[
	//    ['Source','-','Save','NewPage','Preview','-','Templates'],
	//    ['Cut','Copy','Paste','PasteText','PasteFromWord','-','Print', 'SpellChecker', 'Scayt'],
	//    ['Undo','Redo','-','Find','Replace','-','SelectAll','RemoveFormat'],
	//    ['Form', 'Checkbox', 'Radio', 'TextField', 'Textarea', 'Select', 'Button', 'ImageButton', 'HiddenField'],
	//    '/',
	//    ['Bold','Italic','Underline','Strike','-','Subscript','Superscript'],
	//    ['NumberedList','BulletedList','-','Outdent','Indent','Blockquote'],
	//    ['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
	//    ['Link','Unlink','Anchor'],
	//    ['Image','Flash','Table','HorizontalRule','Smiley','SpecialChar','PageBreak'],
	//    '/',
	//    ['Styles','Format','Font','FontSize'],
	//    ['TextColor','BGColor'],
	//    ['Maximize', 'ShowBlocks','-','About']
	//];


	config.toolbar_Full = [
  ['Preview', 'Templates', 'Source','Maximize'],
	['Cut','Copy','Paste'],
	['Undo','Redo','-','SelectAll','RemoveFormat'],
	['Link','Unlink','Anchor'],
	['Image','Flash','Table','HorizontalRule'],
	'/',
	['Styles','FontSize','TextColor','BGColor'],
	['Bold','Italic','Underline','Strike'],
	['NumberedList','BulletedList','-','Outdent','Indent'],
	['JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock'],
	];
};


CKEDITOR.on( 'dialogDefinition', function( ev )
{
	var dialogName = ev.data.name;
	var dialogDefinition = ev.data.definition;

	var tabsToRemove = {
			"image": ['Link', 'Upload'],
			"flash": ['Upload', 'properties'],
			"link" : ['upload']};

	for (var i in tabsToRemove[dialogName])
	{
			dialogDefinition.removeContents(tabsToRemove[dialogName][i]);
	}

	if (dialogName == 'templates')
	{
			var contents = dialogDefinition.getContents('selectTpl');
 			var insertCheckBox = contents.get('chkInsertOpt');
			insertCheckBox.style = "display:none";
			dialogDefinition.minHeight = 280;
	}
});

CKEDITOR.addStylesSet('default',[
{	name:'Normal', element:'p' },
{	name:'Titre 1', element:'h1' },
{	name:'Titre 2',	element:'h2' },
{	name:'Titre 3',	element:'h3' },
{	name:'Titre 4',	element:'h4' },
{	name:'Grand',	element:'big' },
{	name:'Petit',	element:'small' },
{	name:'Machine à écrire',	element:'tt' },
{	name:'Code informatique',	element:'code' },
{	name:'Texte supprimé', element:'del' },
{	name:'Texte inséré', element:'ins' },
{	name:'Citation', element:'cite' },
{
	name:'Ecriture droite à gauche',
	element:'span',
	attributes:{
		dir:'rtl'
	}
},
{
	name:'Ecriture gauche à droite',
	element:'span',
	attributes:{
		dir:'ltr'
	}
},
{
	name:'Image à Gauche',
	element:'img',
	attributes:{
		style:'padding: 5px; margin-right: 5px',
		border:'2',
		align:'left'
	}
},
{
	name:'Image à Droite',
	element:'img',
	attributes:{
		style:'padding: 5px; margin-left: 5px',	
		border:'2',
		align:'right'
	}
}
]);

