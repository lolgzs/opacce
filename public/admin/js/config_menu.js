/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC 3 - Constitution du menu
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		

//--------------------------------------------------------------------------	
// Variables initialisees dans menusMaj.phtml
//--------------------------------------------------------------------------
var nIdMax=0;
var sPathIco;
var nIdProfil;
var nIdBib;
var sModules = new Array();

//--------------------------------------------------------------------------	
// Initialisation : attribution d'identifiants uniques
//--------------------------------------------------------------------------
function initMenus()
{
    initModules();
	  menu_container=getId("menu_container")

    var listItems = menu_container.getElementsByTagName('DIV');
    for(var no=0;no<listItems.length;no++)
    {
        if(listItems[no].id=="module")
        {
            nIdMax++;
            listItems[no].id=nIdMax;
        }
    }
}

//--------------------------------------------------------------------------	
// Mise a jour des proprietes
//--------------------------------------------------------------------------	
function majProprietes(oBouton,sUrl)
{
    oMenu=getDivMenu(oBouton);
    sTypeMenu=oMenu.getAttribute("type_menu");
    sModule=sModules[sTypeMenu];
    if(!sModule || !sModule['action']) sModule=sModules['vide'];

    // Completer l'url avec l'id_module et les proprietes
    sUrl+=sModule['action'];
    sUrl+='?id_profil='+ nIdProfil;
		sUrl+='&id_bib=' + nIdBib;
    sUrl+='&type_menu='+ sTypeMenu;
    sUrl+='&id_module=' + oMenu.id;
    sUrl+='&libelle=' + oMenu.getAttribute("libelle");
    sUrl+='&picto=' + oMenu.getAttribute("picto");
    sUrl+='&preferences=' + oMenu.getAttribute("preferences");
    showPopWin(sUrl,parseInt(sModule['popup_width']),parseInt(sModule['popup_height']),null);
}

//--------------------------------------------------------------------------	
// Retour de mise a jour des proprietes
//--------------------------------------------------------------------------
function retourMajProprietes(nIdModule,sLibelle,sPicto,sPreferences)
{
    setFlagMaj(true);
    oMenu=getId(nIdModule);
    oMenu.setAttribute("libelle",sLibelle);
    oMenu.setAttribute("picto",sPicto);
    oMenu.setAttribute("preferences",sPreferences);
	
    oSpans=oMenu.getElementsByTagName('SPAN');
    for(i=0; i < oSpans.length; i++) if(oSpans[i].id=="libelle") break;
    oSpans[i].innerHTML=sLibelle;
	
    oPictos=oMenu.getElementsByTagName('IMG');
    for(i=0; i < oPictos.length; i++) if(oPictos[i].id=="picto") break;
    oPictos[i].src=sPathIco + sPicto;
}

//----------------------------------------------------------------------------
// Rend le noeud parent de type div
//----------------------------------------------------------------------------
function getDivMenu(oObjet)
{
    oParent=oObjet;
    while(true)
    {
        oParent=oParent.parentNode;
        if(!oParent) {
            alert("Erreur javascript : config_menu - Fonction : getDivMenu");
            return;
        }
        if(oParent.nodeName =="DIV") break;
    }
    return oParent;
}

//----------------------------------------------------------------------------
// Ajout d'une entree de menu
//----------------------------------------------------------------------------
function addMenu()
{
    setFlagMaj(true);
    oContainer=getId("menu_container");
    oMenu=getNewMenu("menu_vide");
    oContainer.appendChild(oMenu);
}
//----------------------------------------------------------------------------
// Ajout d'une entree de sous-menu
//----------------------------------------------------------------------------
function addSousMenu(oObjet)
{
    setFlagMaj(true);
    oContainer=oObjet.parentNode;
    oMenu=getNewMenu("sous_menu_vide");
    oContainer.appendChild(oMenu);
}
//----------------------------------------------------------------------------
// Creation d'un nouveau menu ou sous_menu
//----------------------------------------------------------------------------
function getNewMenu(sModele)
{
    oMenuVide=getId(sModele);
    oMenu=document.createElement('div');
    oMenu.style.display="block";
    nIdMax++;
    oMenu.id=nIdMax;
    oMenu.setAttribute("type_menu",oMenuVide.getAttribute("type_menu"));
    oMenu.setAttribute("picto",oMenuVide.getAttribute("picto"));
    oMenu.setAttribute("libelle",oMenuVide.getAttribute("libelle"));
    oMenu.setAttribute("preferences",oMenuVide.getAttribute("preferences"));
    oMenu.innerHTML=oMenuVide.innerHTML;
    return oMenu;
}

//----------------------------------------------------------------------------
// Supression d'une entree de menu
//----------------------------------------------------------------------------
function deleteMenu(oBouton)
{
    setFlagMaj(true);
    oMenu=getDivMenu(oBouton);
    oNode=oMenu.parentNode;
    oNode.removeChild(oMenu);
}

//----------------------------------------------------------------------------
// Monter une entree de menu
//----------------------------------------------------------------------------
function monterMenu(oBouton)
{
    oMenu=getDivMenu(oBouton);
    oPrecedent=oMenu.previousSibling;
    if(!oPrecedent.id) return;
    swapMenus(oMenu,oPrecedent);
    setFlagMaj(true);
}

//----------------------------------------------------------------------------
// Descendre une entree de menu
//----------------------------------------------------------------------------
function descendreMenu(oBouton)
{
    oMenu=getDivMenu(oBouton);
    oSuivant=oMenu.nextSibling;
    if(!oSuivant.id) return;
    swapMenus(oMenu,oSuivant);
    setFlagMaj(true);
}

//----------------------------------------------------------------------------
// Swapper 2 entrees de menu
//----------------------------------------------------------------------------
function swapMenus(oMenu1,oMenu2)
{
    oSaveMenu1=oMenu1.cloneNode(true);
    oSaveMenu2=oMenu2.cloneNode(true);
    oContainer=oMenu1.parentNode;
    oContainer.replaceChild(oSaveMenu2,oMenu1);
    oContainer.replaceChild(oSaveMenu1,oMenu2);
}

//----------------------------------------------------------------------------
// Supression d'une entree de menu
//----------------------------------------------------------------------------
function setTypeMenu(oCombo)
{
    setFlagMaj(true);
    oMenu=getDivMenu(oCombo);
    sLibelle=oCombo.options[oCombo.selectedIndex].text;
    oMenu.setAttribute("type_menu",oCombo.value);
    oMenu.setAttribute("preferences","");
	
    // Maj du libelle
    oSpans=oMenu.getElementsByTagName('SPAN');
    for(i=0; i < oSpans.length; i++) if(oSpans[i].id=="libelle") break;

		//on ne change le libelle que si il n'a jamais été changé
		var currentLibelle = oSpans[i].innerHTML;
		if (currentLibelle == getId("menu_vide").getAttribute("libelle") ||
				currentLibelle == getId("sous_menu_vide").getAttribute("libelle")) {
				oMenu.setAttribute("libelle",sLibelle);
				oSpans[i].innerHTML=sLibelle;
		}
	
    // Affichage bloc des sous-menus
    oContainer=oMenu.firstChild;
    while(oContainer != null)
    {
        oContainer=oContainer.nextSibling;
        if(!oContainer || !oContainer.getAttribute) continue;
        if(oContainer.id == "sous_menu") break;
    }
    if(oCombo.value =="MENU") sDisplay="block"; else sDisplay="none";
    oContainer.style.display=sDisplay;
}

//--------------------------------------------------------------------------
// Recup des donnees
//--------------------------------------------------------------------------
function saveMenu()
{
    setFlagMaj(false);
    // Entrées principales
    oMenu=menu_container.firstChild;
    var nIndexMenu=0;
    while(oMenu != null)
    {
        oMenu=oMenu.nextSibling;
        if(!oMenu || !oMenu.getAttribute) continue;
        sTypeMenu=oMenu.getAttribute("type_menu");
        if(!sTypeMenu) continue;
		
        nIndexMenu++;
        sName="menu_properties[" + nIndexMenu + "]";
        getItemMenu(sName,oMenu);
		
        // Sous-menus
        if(sTypeMenu != "MENU") continue;
		
        // Cherche le container
        oContainer=oMenu.firstChild;
        while(oContainer != null)
        {
            oContainer=oContainer.nextSibling;
            if(!oContainer || !oContainer.getAttribute) continue;
            if(oContainer.id == "sous_menu") break;
        }

        // Parser les entrees
        var nIndexSousMenu=0;
        oSousMenu=oContainer.firstChild;
        while(oSousMenu != null)
        {
            oSousMenu=oSousMenu.nextSibling;
            if(!oSousMenu || !oSousMenu.getAttribute) continue;
            sTypeMenu=oSousMenu.getAttribute("type_menu");
            if(!sTypeMenu) continue;
			
            nIndexSousMenu++;
            sName="sous_menu_properties[" + nIndexMenu + "][" + nIndexSousMenu + "]";
            getItemMenu(sName,oSousMenu);
        }
    }
}

function getItemMenu(sName,oDiv)
{
    // On cree un element <input hidden> pour renvoyer au php
    oProperty=document.createElement('input');
    oProperty.setAttribute('type','hidden');
    oProperty.name=sName;
    oProperty.value ="type_menu=" + oDiv.getAttribute("type_menu") + ";";
    oProperty.value +="libelle=" + oDiv.getAttribute("libelle") + ";";
    oProperty.value +="picto=" + oDiv.getAttribute("picto") + ";";
    oProperty.value +="preferences=" + oDiv.getAttribute("preferences");
		
    oForm=getId("form_menu");
    oForm.appendChild(oProperty);
}

// Initialiser les objets au form load
$(document).ready(initMenus);