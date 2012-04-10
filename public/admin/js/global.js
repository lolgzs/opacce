// Simplification du getElementById
function getId(sId)
{
	return document.getElementById(sId);
}

// Pour le treemenu
function show(sId)
{
	oMenu = document.getElementById(sId);
	if (oMenu.style.display=='none') oMenu.style.display='block';
	else oMenu.style.display='none';
}

//////////////////////////////////////////////////////////////////////////////////////
// Fonctions standards
/////////////////////////////////////////////////////////////////////////////////////
var bProcessing=0;
var oPopupMessage;

function getTopPos(oObjet)
{	
	var returnValue = oObjet.offsetTop;
	while((oObjet = oObjet.offsetParent) != null) returnValue += oObjet.offsetTop;
	return returnValue;
}

function getLeftPos(oObjet)
{
	var returnValue = oObjet.offsetLeft;
	while((oObjet = oObjet.offsetParent) != null) returnValue += oObjet.offsetLeft;
	return returnValue;
}

function select_ligne_table( maLigne )
{
	sClasse=maLigne.getAttribute("class");
	if(!sClasse) sClasse=maLigne.getAttribute("className");
	nLen=sClasse.length -5;
	if(sClasse.substring(nLen,100) == "_over") sClasse=sClasse.substring(0,nLen);else sClasse=sClasse.concat('_over');
	maLigne.setAttribute("className", sClasse);
	maLigne.setAttribute("class", sClasse);
}
// Pour bug du replace
function remplace( sCherche, sRemplace, sTexte)
{
	while(sTexte.indexOf(sCherche,0) >=0) sTexte=sTexte.replace(sCherche,sRemplace);
	return sTexte;
}

///////////////////////////////////////////////////////////////////////////////
// CONTROLE DE SAISIE
///////////////////////////////////////////////////////////////////////////////

function controle_saisie(oForm)
{
	for(i=0; i< oForm.elements.length; i++)
	{
		oElem=oForm.elements[i];
		if( oElem.getAttribute("obligatoire")=="1")
		{
			if(remplace(" ","",oElem.value) == "")
			{
				alert("Cette donnée doit être renseignée !");
				oElem.focus();
				return false;
			}
		}
	}
	oForm.submit();
}

///////////////////////////////////////////////////////////////////////////////
// BOITES SURGISSANTES
///////////////////////////////////////////////////////////////////////////////

function centrer_popup(oPopup)
{
	oPopup.style.left=document.body.scrollLeft + Math.floor((screen.width - oPopup.clientWidth)/2);
	oPopup.style.top=document.body.scrollTop + Math.floor((screen.height - oPopup.clientHeight)/2)-100;
}

function scroll_popup(sId,nPos,nStopY)
{
	oPopup=document.getElementById(sId);
	if(!nPos) nY=screen.height; else nY=nPos-20;
	oPopup.style.top=nY;
	if(oPopup.style.display != "block")
	{
		oPopup.style.display="block";
		nLargeur=oPopup.clientWidth;
		oPopup.style.left=(screen.width-nLargeur)/2;
	}
		
	if(nY > nStopY) setTimeout("scroll_popup('"+sId+"',"+nY+","+nStopY+")",5);
}
	
function fermer_scroll_popup(sId,nPos)
{
	oPopup=document.getElementById(sId);
	if(!nPos) nY=oPopup.style.top.replace("px",""); else nY=nPos+20;
	oPopup.style.top=nY;
	if(nY > screen.height) oPopup.style.display="none";
	else setTimeout("fermer_scroll_popup('"+sId+"',"+nY+")",5);
}
	
function popup_gauche(sId,nPos)
{
	oBoite=document.getElementById(sId);
	if(!nPos) 
	{
		oBoite.style.top=document.body.scrollTop;
		oBoite.style.display="block";
		nX=-(oBoite.clientWidth);
	}
	else nX=nPos+10; 
	oBoite.style.left=nX;
	if(nX < 0) setTimeout("popup_gauche('"+sId+"',"+nX+")",5);
}
function fermer_popup_gauche(sId,nPos)
{
	oBoite=document.getElementById(sId);
	nX=nPos-10;
	oBoite.style.left=nX;
	if(nX > -(oBoite.clientWidth)) setTimeout("fermer_popup_gauche('"+sId+"',"+nX+")",5);
	else oBoite.style.display="none";
}

function afficher_section(objetNotice)
{
	visible = document.getElementById(objetNotice).style.display;
	oImg=document.getElementById("I" + objetNotice);
	if( visible == "block" ) 
	{
		if(oImg != null) oImg.setAttribute("src", imagesUrl+ "plus_carre.gif");
		document.getElementById(objetNotice).style.display="none";
		return false;
	}
	else
	{
		if(oImg != null) oImg.setAttribute("src", imagesUrl+ "moins_carre.gif");
		oNotice=document.getElementById(objetNotice);
		oNotice.style.display="block";
		return true;
	}
}
	
function getElementsByClassName(strClass, strTag, objContElm) {
	strTag = strTag || "*";
	objContElm = objContElm || document;
	var objColl = objContElm.getElementsByTagName(strTag);
	if (!objColl.length &&	strTag == "*" &&	objContElm.all) objColl = objContElm.all;
	var arr = new Array();
	var delim = strClass.indexOf('|') != -1	 ? '|' : ' ';
	var arrClass = strClass.split(delim);
	for (var i = 0, j = objColl.length; i < j; i++) {
		var arrObjClass = objColl[i].className.split(' ');
		if (delim == ' ' && arrClass.length > arrObjClass.length) continue;
		var c = 0;
			comparisonLoop:
			for (var k = 0, l = arrObjClass.length; k < l; k++) {
				for (var m = 0, n = arrClass.length; m < n; m++) {
					if (arrClass[m] == arrObjClass[k]) c++;
					if ((delim == '|' && c == 1) || (delim == ' ' && c == arrClass.length)) {
						arr.push(objColl[i]);
						break comparisonLoop;
					}
				}
			}
	}
	return arr;
}


function formSelectToggleVisibilityForElement(eventSourceSelector, objectToShowSelector, visibleForValues) {
	if (!(visibleForValues instanceof Array)) visibleForValues = [visibleForValues];
	toggleVisibilityForElement(eventSourceSelector, 
														 objectToShowSelector, 
														 function(element) {return ($.inArray(element.val(),visibleForValues) >= 0)} );
}



function checkBoxToggleVisibilityForElement(eventSourceSelector, objectToShowSelector, visibleWhenChecked) {
	toggleVisibilityForElement(eventSourceSelector, 
														 objectToShowSelector, 
														 function(element) {
															 return visibleWhenChecked == ($(element).attr('checked') == 'checked');
														 });
}



function toggleVisibilityForElement(eventSourceSelector, objectToShowSelector, testingAlgorithm) {
	$("document").ready(function(){
		var objectToShow = $(objectToShowSelector);
		
		var toggleVisibility = function(element) {
			if (element.length == 0) return;
			if (testingAlgorithm(element))
				objectToShow.fadeIn();
			else 
				objectToShow.fadeOut();
		}

		toggleVisibility($(eventSourceSelector));
		toggleVisibility($(eventSourceSelector+':checked'));
		$(eventSourceSelector).change(function(event){
			toggleVisibility($(event.target));
		});
	});
}



function showCmsAvis(idCmsAvis) {

	avis = document.getElementById(idCmsAvis);
	avis.style.display = 'block';
}

function hideCmsAvis(idCmsAvis) {

	avis = document.getElementById(idCmsAvis);
	avis.style.display = 'none';
}


/* Boîte pour changer le visuel, options d'accessibilité */
function initAccessibilityOptions() {
		var container = $("div#header");
		if (container.length==0) return;

		var open_accessibility = $("<div id='open_accessibility'></div>").
				appendTo(container).
				click(onOpenAccessibilityClick).
				wrap(
						$('<div></div'). //workaround pour calice, bannière pose problème
								css('height', 0).
								css('float', 'right'));

				

		createAccessibilityDialog();

		/* Restaure le CSS sélectionné précédemment*/
		if($.cookie("accessibility_css")) {
				$("link#accessibility_stylesheet").attr("href",$.cookie("accessibility_css"));
		}
}


function onOpenAccessibilityClick(event) {
		$('#accessibility_dialog').dialog('open');
}

function onAccessibilityDialogOpen(event, ui) {
		$('div.ui-widget-overlay').click(function(event) {
				$('#accessibility_dialog').dialog('close');
		});
}


function createAccessibilityDialog() {
		var dialog = $("<div id='accessibility_dialog' title='Style'><ul></ul></div>").
				appendTo($("#open_accessibility")).
				dialog({
						position: ['right', 'top'],
						resizable: false,
						autoOpen: false,
						modal: true,
						open: onAccessibilityDialogOpen});

		var css_list = dialog.children('ul');

		$('link[rel="alternate stylesheet"]').each(function(index, element){
				$('<li>'+$(element).attr('title')+'</li>').
						appendTo(css_list).
						data('css', $(element).attr('href')).
						css('cursor', 'pointer').
						addClass('ui-widget-content');
		});

		css_list.selectable({
				selected: function(event, ui){
						var css = $(event.target).children('.ui-selected').data('css');
						$("link#accessibility_stylesheet").attr("href", css);
						$.cookie("accessibility_css",css, {expires: 365, path: '/'});
						return false;
				}
		});
}


function openIFrameDialog(url, title) {
	var iframe = $('<iframe style="padding: 0px"></iframe>');
	iframe.dialog({title: title,	modal: true, width: 600, height: 400 });
	iframe.attr("src", url).width(600).height(400);
}