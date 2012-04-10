////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC3 : Fonctions pour l'objet de selection des catégories CMS RSS et SITOTHEQUE
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

//--------------------------------------------------------------------------
// Ouvrir / fermer l'arbre
//--------------------------------------------------------------------------
function displayArbre()
{
	oArbre=getId('arbre');
	if(oArbre.style.display == "block") oArbre.style.display="none";
	else
	{ 
		oArbre.style.display="block";
		oArbre.style.left=getId("champ_selection").style.left;
		oArbre.style.top=parseInt(getId("champ_selection").style.top)+getId("champ_selection").clientHeight + "px";
		window.scrollTo(0,2000);
	}
}
 
//--------------------------------------------------------------------------
// Déployer / contracter l'arbre
//--------------------------------------------------------------------------
function clickArbre(oCategorie)
{
	oItem=oCategorie;
	while(true)
	{
		oItem=oItem.nextSibling;
		if(!oItem) break
		if(oItem.nodeName == "LI") return;
		if(oItem.id != "UL") continue;
		if(oItem.style.display=="block") oItem.style.display="none";
		else oItem.style.display="block";
		break;
	}
}

//--------------------------------------------------------------------------
// Sélection d'éléments tous niveaux
//--------------------------------------------------------------------------
function selectElement(oCheckBox)
{
	// Si categorie on modifie ses fils
	if(!oCheckBox.getAttribute("id_item"))
	{
		// On reclique pour le deploiement dans l'arbre
		oParent=oCheckBox;
		while(true)
		{
			oParent=oParent.parentNode;
			if(!oParent) { alert("Erreur javascript : selection categories - Fonction : select_element"); return; }
			if(oParent.nodeName=="LI") break;
		}
		clickArbre(oParent);
		
		// On modifie tous ses fils
		oItem=oParent;
		while(true)
		{
			var oContainerFils=null;
			oItem=oItem.nextSibling;
			if(!oItem) break
			if(oItem.id == "UL") { oContainerFils= oItem; break;}
		}
		if(oContainerFils)
		{
			var listItems = oContainerFils.getElementsByTagName('INPUT');	
			for(var no=0;no<listItems.length;no++)
			{
				if(listItems[no].getAttribute("type") != "checkbox") continue;
				listItems[no].checked=false;
				if(oCheckBox.checked == true) listItems[no].disabled="disabled";
				else listItems[no].disabled="";
			}
		}
	}
	// Recup des elements selectionnés dans l'input hidden
	sChampCategorie="";
	sChampItems="";
	sChampLibelles="";
	
	var arbre = document.getElementById('arbre');
	var listItems = arbre.getElementsByTagName('INPUT');
	for(var no=0;no<listItems.length;no++)
	{
		if(listItems[no].getAttribute("type") != "checkbox") continue;
		sIdCategorie=listItems[no].getAttribute("id_categorie");
		sIdItem=listItems[no].getAttribute("id_item");
		if(listItems[no].checked == true)
		{
			if(sIdItem > "") {sChampItems+=sIdItem + "-"; sChampLibelles += '[' + getId('item' + sIdItem).innerHTML + '] ';}
			else {sChampCategorie+=sIdCategorie + "-"; sChampLibelles += '[' +  getId('cat' + sIdCategorie).innerHTML + '] ';}
		}
	}
	getId("id_categorie").value=sChampCategorie;
	getId("id_items").value=sChampItems;
	getId("champ_selection").innerHTML=sChampLibelles;
}

