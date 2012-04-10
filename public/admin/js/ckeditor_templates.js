
CKEDITOR.addTemplates( 'default',
{

	// The name of sub folder which hold the shortcut preview images of the templates.
	imagesPath : CKEDITOR.getUrl( CKEDITOR.plugins.getPath( 'templates' ) + 'templates/images/' ),

	templates:[

	{
		title:'Image et titre',
		image:'template1.gif',
		description:'Une image principale entourée par du texte.',
		html:'<h3>\n\
			<img style="margin-right: 10px" height="100" width="100" align="left"/>\n\
			Titre</h3>\n\
			<p>Le texte de l\'article</p>'
	},


	{
		title:'Deux colonnes',
		image:'template2.gif',
		description:'Article sur deux colonnes, avec titre et texte.',
		html:'\
		<table cellspacing="0" cellpadding="0" style="width:100%" border="0">\n\
			<tr>\n\
				<td style="width:50%">\n\
					<h3>Premier titre</h3>\n\
				</td>\n\
				<td></td>\n\
				<td style="width:50%">\n\
					<h3>Second titre</h3>\n\
				</td>\n\
			</tr>\n\
			<tr>\n\
				<td>Texte de la première colonne.</td>\n\
				<td></td>\n\
				<td>Texte de la seconde colonne.</td>\n\
			</tr>\n\
		</table>\n\
		<p>Plus de texte ici.</p>'
	},


	{
		title:'Texte et tableau',
		image:'template3.gif',
		description:'Un titre, du texte et un tableau.',
		html:'\
		<div style="width: 80%">\n\
			<h3>Titre de l\'article.</h3>\n\
			<table cellspacing="0" cellpadding="0" style="float:right;table-layout:fixed;width:250px" border="1">\n\
						<caption style="border:solid 1px black;white-space:nowrap;">\n\
							<strong>Titre du tableau.</strong>\n\
						</caption>\n\
				<tr>\n\
					<td>&nbsp;</td>\n\
					<td>&nbsp;</td>\n\
					<td>&nbsp;</td>\n\
				</tr>\n\
				<tr>\n\
					<td>&nbsp;</td>\n\
					<td>&nbsp;</td>\n\
					<td>&nbsp;</td>\n\
				</tr>\n\
				<tr>\n\
					<td>&nbsp;</td>\n\
					<td>&nbsp;</td>\n\
					<td>&nbsp;</td>\n\
				</tr>\n\
			</table>\n\
			<p>Texte de l\'article</p>\n\
		</div>'
	}]
});