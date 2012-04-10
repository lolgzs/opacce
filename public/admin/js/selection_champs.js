////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 : Fonctions pour l'objet drag and drop champs
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Variables de travail valorisées dans le script php
var libelle_champ=new Array();				// Libellés des champs
var codes_champ=new Array();					// Codes des champs disponibles par rubriques
var current_id;

// Afficher rubrique
function afficherRubrique(sId)
{
	if(current_id)
	{ 
		document.getElementById(current_id + "_mainDiv").style.display="none";
		if(sId==current_id) {current_id=null; return;}
	}
	current_id=sId;
	oContainer=document.getElementById(sId + "_mainDiv");
	oContainer.style.display="block";
	dragDropObj = new DHTMLgoodies_dragDrop();
	for(i=0; i < codes_champ[sId].length; i++)
	{
		sChamp=sId + "_box_" + codes_champ[sId].substr(i,1);
		dragDropObj.addSource(sChamp,true,true,true,false,'onDragFunction');
	}
	dragDropObj.addTarget(sId + '_rightColumn','dropItems');	// Set <div id="rightColumn"> as a drop target. Call function dropItems on drop
	dragDropObj.addTarget(sId + '_leftColumn','dropItems'); // Set <div id="leftColumn"> as a drop target. Call function dropItems on drop
	dragDropObj.init();
}

// Drop item
function dropItems(idOfDraggedItem,targetId,x,y)
{
	if(targetId==current_id + '_rightColumn')
	{
		var obj = document.getElementById(idOfDraggedItem);
		if(obj.parentNode.id==current_id + '_dropBox')return;		
		document.getElementById(current_id + '_dropBox').appendChild(obj);	// Appending dragged element as child of target box
		supprimeChamp(obj);
	}
	if(targetId==current_id + '_leftColumn')
	{
		var obj = document.getElementById(idOfDraggedItem);
		if(obj.parentNode.id==current_id + '_dropContent')return;	
		document.getElementById(current_id + '_dropContent').appendChild(obj);	// Appending dragged element as child of target box
		addChamp(obj);
	}
	
}

// Debut du drag
function onDragFunction(cloneId,origId)
{
	var obj = document.getElementById(cloneId);
	obj.style.border='1px solid #F00';
}

// Ajouter un champ
function addChamp(obj)
{
	oHidden=document.getElementById(current_id + '_codes');
	sCodes=oHidden.value;
	sCodes = sCodes + obj.getAttribute('code');
	oHidden.value=sCodes;
	afficherLibelles();
}

// Supprimer un champ
function supprimeChamp(obj)
{
	oHidden=document.getElementById(current_id + '_codes');
	sCodes=oHidden.value;
	sCodes=sCodes.replace(obj.getAttribute('code'),'');
	oHidden.value=sCodes;
	afficherLibelles();
}

// Affichage des libelles
function afficherLibelles()
{
	var sTexte="";
	sCodes=document.getElementById(current_id + '_codes').value;
	for(i=0; i<sCodes.length; i++)
	{
		sCode=sCodes.substr(i,1);
		if(sTexte) sTexte += ", ";
		sTexte += libelle_champ[sCode];
	}
	document.getElementById(current_id + '_libelle').innerHTML=sTexte;
}