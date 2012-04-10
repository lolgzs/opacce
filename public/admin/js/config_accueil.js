/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// OPAC 3 - Drag & drop page d'accueil config
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////		

// VARIABLES YOU COULD MODIFY
var boxSizeArray = [9,9,9,9,9,9];	// Array indicating how many items there is rooom for in the right column ULs

var arrow_offsetX = -5;	// Offset X - position of small arrow
var arrow_offsetY = 0;	// Offset Y - position of small arrow

var arrow_offsetX_firefox = -6;	// Firefox - offset X small arrow
var arrow_offsetY_firefox = -13; // Firefox - offset Y small arrow

var verticalSpaceBetweenListItems = 3;	// Pixels space between one <li> and next
var indicateDestionationByUseOfArrow = true;	// Display arrow to indicate where object will be dropped(false = use rectangle)

var cloneSourceItems = true;	// Items picked from main container will be cloned(i.e. "copy" instead of "cut").
var cloneAllowDuplicates = true;	// Allow multiple instances of an item inside a small box(example: drag Student 1 to team A twice
var indicateDestinationBox = false; // La petite fleche 
var nIdModuleMax=0  // Indice max pour affecter un id_module aux nouveaux objets

// VARIABLES YOU SHOULD NOT MODIFY
var dragDropTopContainer = false;
var dragTimer = -1;
var dragContentObj = false;
var contentToBeDragged = false;	// Reference to dragged <li>
var contentToBeDragged_src = false;	// Reference to parent of <li> before drag started
var contentToBeDragged_next = false; 	// Reference to next sibling of <li> to be dragged
var destinationObj = false;	// Reference to <UL> or <LI> where element is dropped.
var dragDropIndicator = false;	// Reference to small arrow indicating where items will be dropped
var ulPositionArray = new Array();
var mouseoverObj = false;	// Reference to highlighted DIV
var MSIE = navigator.userAgent.indexOf('MSIE')>=0?true:false;
var navigatorVersion = navigator.appVersion.replace(/.*?MSIE (\d\.\d).*/g,'$1')/1;
	
	function getTopPos(inputObj)
	{		
	  var returnValue = inputObj.offsetTop;
	  while((inputObj = inputObj.offsetParent) != null){
	  	if(inputObj.tagName!='HTML')returnValue += inputObj.offsetTop;
	  }
	  return returnValue;
	}
	
	function getLeftPos(inputObj)
	{
	  var returnValue = inputObj.offsetLeft;
	  while((inputObj = inputObj.offsetParent) != null){
	  	if(inputObj.tagName!='HTML')returnValue += inputObj.offsetLeft;
	  }
	  return returnValue;
	}
		
	function cancelEvent()
	{
		return false;
	}

//--------------------------------------------------------------------------	
// Mise a jour des proprietes
//--------------------------------------------------------------------------	
function majProprietes(oImg,sUrl,nLargeur,nHauteur)
{
	// Rechercher l'objet pere de type <li>
	oParent=oImg;
	while(true)
	{
		oParent=oParent.parentNode;
		if(!oParent) { alert("Erreur javascript : config_accueil - Fonction : majProprietes"); return; }
		if(oParent.nodeName =="LI") break;
	}
	// Completer l'url avec l'id_module et les proprietes
	sUrl+='&type_module='+ oParent.id +'&id_module=' + oParent.getAttribute("id_module") + '&proprietes=' + oParent.getAttribute("proprietes");
	showPopWin(sUrl,nLargeur,nHauteur,null);
}

//--------------------------------------------------------------------------	
// Retour de mise a jour des proprietes
//--------------------------------------------------------------------------	
function retourMajProprietes(nIdModule,sProprietes)
{
	setFlagMaj(true);
	var listItems = dragDropTopContainer.getElementsByTagName('LI');	
	for(var no=0;no<listItems.length;no++)
	{
		if(listItems[no].getAttribute("id_module") == nIdModule)
		{
			listItems[no].setAttribute("proprietes",sProprietes);
			return;
		}
	}
}

//--------------------------------------------------------------------------	
// Debut du drag
//--------------------------------------------------------------------------	
function initDrag(e)
{
	// Si click sur le bouton proprietes on laisse faire le onclick
	if(e.target.src)
	{ 
		return false;
	}
	if(document.all)e = event;
	var st = Math.max(document.body.scrollTop,document.documentElement.scrollTop);
	var sl = Math.max(document.body.scrollLeft,document.documentElement.scrollLeft);
	dragTimer = 0;
	dragContentObj.style.left = e.clientX + sl + 'px';
	dragContentObj.style.top = e.clientY + st + 'px';
	contentToBeDragged = this;
	contentToBeDragged_src = this.parentNode;
	contentToBeDragged_next = false;
	if(this.nextSibling)
	{
		contentToBeDragged_next = this.nextSibling;
		if(!this.tagName && contentToBeDragged_next.nextSibling)contentToBeDragged_next = contentToBeDragged_next.nextSibling;
	}
	timerDrag();
	return false;
}
	
function timerDrag()
{
	if(dragTimer>=0 && dragTimer<10){
		dragTimer++;
		setTimeout('timerDrag()',10);
		return;
	}
	if(dragTimer==10){

		if(cloneSourceItems && contentToBeDragged.parentNode.id=='allItems'){
			newItem = contentToBeDragged.cloneNode(true);
			newItem.onmousedown = contentToBeDragged.onmousedown;
			contentToBeDragged = newItem;
		}
		dragContentObj.style.display='block';
		dragContentObj.appendChild(contentToBeDragged);
	}
}
	
function moveDragContent(e)
{
	if(dragTimer<10)
	{
		if(contentToBeDragged)
		{
			if(contentToBeDragged_next) contentToBeDragged_src.insertBefore(contentToBeDragged,contentToBeDragged_next);
			else contentToBeDragged_src.appendChild(contentToBeDragged);
			}
			return;
		}
		if(document.all)e = event;
		var st = Math.max(document.body.scrollTop,document.documentElement.scrollTop);
		var sl = Math.max(document.body.scrollLeft,document.documentElement.scrollLeft);

		dragContentObj.style.left = e.clientX + sl + 'px';
		dragContentObj.style.top = e.clientY + st + 'px';

		if(mouseoverObj) mouseoverObj.className='';
		destinationObj = false;
		dragDropIndicator.style.display='none';
		if(indicateDestinationBox)indicateDestinationBox.style.display='none';
		var x = e.clientX + sl;
		var y = e.clientY + st;
		var width = dragContentObj.offsetWidth;
		var height = dragContentObj.offsetHeight;

		var tmpOffsetX = arrow_offsetX;
		var tmpOffsetY = arrow_offsetY;
		if(!document.all){
			tmpOffsetX = arrow_offsetX_firefox;
			tmpOffsetY = arrow_offsetY_firefox;
		}

		for(var no=0;no<ulPositionArray.length;no++){
			var ul_leftPos = ulPositionArray[no]['left'];
			var ul_topPos = ulPositionArray[no]['top'];
			var ul_height = ulPositionArray[no]['height'];
			var ul_width = ulPositionArray[no]['width'];

			if((x+width) > ul_leftPos && x<(ul_leftPos + ul_width) && (y+height)> ul_topPos && y<(ul_topPos + ul_height)){
				var noExisting = ulPositionArray[no]['obj'].getElementsByTagName('LI').length;
				if(indicateDestinationBox && indicateDestinationBox.parentNode==ulPositionArray[no]['obj'])noExisting--;
				if(noExisting<boxSizeArray[no-1] || no==0){
					dragDropIndicator.style.left = ul_leftPos + tmpOffsetX + 'px';
					var subLi = ulPositionArray[no]['obj'].getElementsByTagName('LI');

					var clonedItemAllreadyAdded = false;
					if(cloneSourceItems && !cloneAllowDuplicates){
						for(var liIndex=0;liIndex<subLi.length;liIndex++){
							if(contentToBeDragged.id == subLi[liIndex].id)clonedItemAllreadyAdded = true;
						}
						if(clonedItemAllreadyAdded)continue;
					}

					for(var liIndex=0;liIndex<subLi.length;liIndex++){
						var tmpTop = getTopPos(subLi[liIndex]);
						if(!indicateDestionationByUseOfArrow){
							if(y<tmpTop){
								destinationObj = subLi[liIndex];
								indicateDestinationBox.style.display='block';
								subLi[liIndex].parentNode.insertBefore(indicateDestinationBox,subLi[liIndex]);
								break;
							}
							}else{
								if(y<tmpTop){
									destinationObj = subLi[liIndex];
									dragDropIndicator.style.top = tmpTop + tmpOffsetY - Math.round(dragDropIndicator.clientHeight/2) + 'px';
									dragDropIndicator.style.display='block';
									break;
								}
							}
						}

						if(!indicateDestionationByUseOfArrow){
							if(indicateDestinationBox.style.display=='none'){
								indicateDestinationBox.style.display='block';
								ulPositionArray[no]['obj'].appendChild(indicateDestinationBox);
							}

							}else{
								if(subLi.length>0 && dragDropIndicator.style.display=='none'){
									dragDropIndicator.style.top = getTopPos(subLi[subLi.length-1]) + subLi[subLi.length-1].offsetHeight + tmpOffsetY + 'px';
									dragDropIndicator.style.display='block';
								}
								if(subLi.length==0){
									dragDropIndicator.style.top = ul_topPos + arrow_offsetY + 'px'
									dragDropIndicator.style.display='block';
								}
							}

							if(!destinationObj)destinationObj = ulPositionArray[no]['obj'];
							mouseoverObj = ulPositionArray[no]['obj'].parentNode;
							mouseoverObj.className='mouseover';
							return;
						}
					}
				}
			}
	
//--------------------------------------------------------------------------
// Drop element ou retour a l'envoyeur
//--------------------------------------------------------------------------	
function dragDropEnd(e)
{
	if(dragTimer==-1)return;
	if(dragTimer<10)
	{
		dragTimer = -1;
		return;
	}
	dragTimer = -1;
	if(document.all)e = event;
	if(cloneSourceItems && !destinationObj && contentToBeDragged_src.id.substr(0,3)!="box" || (destinationObj && destinationObj.id=='allItems') || (destinationObj && destinationObj.parentNode.id=='allItems'))
	{
		contentToBeDragged.parentNode.removeChild(contentToBeDragged);
	}
	else
	{
		if(destinationObj)
		{
			setFlagMaj(true);
			// Affecter un identifiant si nouvel element
			if(contentToBeDragged_src.id == "allItems")
			{
				nIdModuleMax++;
				contentToBeDragged.setAttribute("id_module",nIdModuleMax);
				contentToBeDragged.setAttribute("new_module",true);
        $(contentToBeDragged).mousedown(initDrag);
				
				// Afficher le picto pour les proprietes
				oImg=contentToBeDragged;
				while(true)
				{
					oImg=oImg.lastChild;
					if(!oImg) { alert("Erreur javascript : config_accueil - Fonction : dragDropEnd"); return; }
					if(oImg.nodeName == "IMG") break;
				}
				oImg.style.display="block";
			}
			if(destinationObj.tagName=='UL') destinationObj.appendChild(contentToBeDragged);
			else destinationObj.parentNode.insertBefore(contentToBeDragged,destinationObj);
			mouseoverObj.className='';
			destinationObj = false;
			dragDropIndicator.style.display='none';
			if(indicateDestinationBox)
			{
				indicateDestinationBox.style.display='none';
				document.body.appendChild(indicateDestinationBox);
			}
			contentToBeDragged = false;
			return;
		}
		if(contentToBeDragged_next) contentToBeDragged_src.insertBefore(contentToBeDragged,contentToBeDragged_next);
		else contentToBeDragged_src.appendChild(contentToBeDragged);
	}
	contentToBeDragged = false;
	dragDropIndicator.style.display='none';
	if(indicateDestinationBox)
	{
		indicateDestinationBox.style.display='none';
		document.body.appendChild(indicateDestinationBox);
	}
	mouseoverObj = false;
}

//--------------------------------------------------------------------------
// Recup des donnees (bas�e sur la balise UL)
//--------------------------------------------------------------------------
function saveDragDropNodes()
{
	var saveString = "";
	var uls = dragDropTopContainer.getElementsByTagName('UL');
	for(var no=0;no<uls.length;no++)
	{
		var lis = uls[no].getElementsByTagName('LI');
		for(var no2=0;no2<lis.length;no2++)
		{
			if(saveString.length>0)saveString = saveString + ";";
			if(uls[no].id !="allItems") {
					var id_module = lis[no2].getAttribute("id_module");
					if (lis[no2].getAttribute("new_module") == 'true') id_module = 'new';
					saveString = saveString + uls[no].id + '|' + id_module + "|" + lis[no2].id + "|" + lis[no2].getAttribute('proprietes');
			}
		}
	}
	document.getElementById('saveContent').innerHTML = saveString;
}

//--------------------------------------------------------------------------
// Initialisation des evenements 
//--------------------------------------------------------------------------	
function initDragDropScript()
{
	dragContentObj = document.getElementById('dragContent');
	dragDropIndicator = document.getElementById('dragDropIndicator');
	dragDropTopContainer = document.getElementById('dhtmlgoodies_dragDropContainer');
	document.documentElement.onselectstart = cancelEvent;
	
	// Get objets <li>
	var listItems = dragDropTopContainer.getElementsByTagName('LI');	
	var itemHeight = false;

	//pour bug IE 7 & 8
	$('#dhtmlgoodies_dragDropContainer LI').mousedown(initDrag);

	for(var no=0;no<listItems.length;no++)
	{
//		listItems[no].onmousedown = initDrag;
		listItems[no].onselectstart = cancelEvent;
		if(!itemHeight) itemHeight = listItems[no].offsetHeight;
		if(MSIE && navigatorVersion/1<6) listItems[no].style.cursor='hand';
		// Indice max
		if(listItems[no].getAttribute("id_module") > nIdModuleMax) nIdModuleMax=listItems[no].getAttribute("id_module");
	}
	var mainContainer = document.getElementById('dhtmlgoodies_mainContainer');
	var uls = mainContainer.getElementsByTagName('UL');
	var leftContainer = document.getElementById('dhtmlgoodies_listOfItems');
	var itemBox = leftContainer.getElementsByTagName('UL')[0];

	document.documentElement.onmousemove = moveDragContent;	// Mouse move event - moving draggable div
	document.documentElement.onmouseup = dragDropEnd;	// Mouse move event - moving draggable div

	var ulArray = dragDropTopContainer.getElementsByTagName('UL');
	for(var no=0;no<ulArray.length;no++)
	{
		ulPositionArray[no] = new Array();
		ulPositionArray[no]['left'] = getLeftPos(ulArray[no]);
		ulPositionArray[no]['top'] = getTopPos(ulArray[no]);
		ulPositionArray[no]['width'] = ulArray[no].offsetWidth;
		ulPositionArray[no]['height'] = ulArray[no].clientHeight;
		ulPositionArray[no]['obj'] = ulArray[no];
	}
	if(!indicateDestionationByUseOfArrow)
	{
		indicateDestinationBox = document.createElement('LI');
		indicateDestinationBox.id = 'indicateDestination';
		indicateDestinationBox.style.display='none';
		document.body.appendChild(indicateDestinationBox);
	}
}

// Initialiser les objets au form load
$(document).ready(initDragDropScript);