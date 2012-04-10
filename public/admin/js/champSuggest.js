/////////////////////////// EVENT HANDLER ////////////////////////////////////////////////////////////////

var oChampSaisie=new Array();
var nCurrentChampSaisie=-1;
var bOverListeSuggestion=false;

function initForm()
{
	var oInputs = getElementsByClassName('suggest', 'input');
	for(var no=0;no<oInputs.length;no++)
	{
		index=oChampSaisie.length;
		oChampSaisie[index]=new Array();
		oChampSaisie[index]['handle']=oInputs[no];
		oChampSaisie[index]['chercheType']=oInputs[no].chercheType;
		oChampSaisie[index]['last_value']="";
		oChampSaisie[index]['liste_suggestion']=creerListeSuggestion();
		oInputs[no].setAttribute("index",index);
		oInputs[no].onkeyup = afficheListeSuggestion;
		oInputs[no].onfocus = focusEvent;
		oInputs[no].onblur = focusEvent;
	}
}
function creerListeSuggestion()
{
	oNew=document.createElement("div");
	oNew.setAttribute("className","liste_suggestion");
	oNew.style.position="absolute";
	document.getElementById('bloc').appendChild(oNew);
	return oNew;
}
function afficheListeSuggestion(e)
{
	if (!e) var e = window.event;
	// Recup de la touche tapée
	var code;
	if (e.keyCode) code = e.keyCode;
	else if (e.which) code = e.which;
	
	index=this.getAttribute("index");
	if(oChampSaisie[index]['last_value'] == this.value) return true;
	nCurrentChampSaisie=index;
	oListeSuggestion=oChampSaisie[index]['liste_suggestion'];
	oChampSaisie[index]['last_value'] = this.value;
	if(this.value==''){oListeSuggestion.style.display='none'; return true;}
	oListeSuggestion.style.left=getLeftPos(this);
	oListeSuggestion.style.top=getTopPos(this) + this.offsetHeight;
	chercheType = oChampSaisie[index]['chercheType'];
	suggestAjax(this, this.value, oListeSuggestion, chercheType);
	//alert(e.type); // type d'évenement
}
function setValueListeSuggestion(oObj)
{
	var sValue=oObj.firstChild.nodeValue;
	oChampSaisie[nCurrentChampSaisie]['handle'].value = sValue;
	select_ligne_table(oChampSaisie[nCurrentChampSaisie]['handle']);
	oListeSuggestion=oChampSaisie[nCurrentChampSaisie]['liste_suggestion'];
	oListeSuggestion.style.display='none';
	bOverListeSuggestion=false;
}
function focusEvent(e)
{
	if (!e) var e = window.event;
	if(e.type == 'blur')
	{
		if(bOverListeSuggestion == true) return true;
		bOverListeSuggestion=false;
		if(nCurrentChampSaisie >=0)
		{
			oListeSuggestion=oChampSaisie[nCurrentChampSaisie]['liste_suggestion'];
			oListeSuggestion.style.display='none';
		}
	}
	select_ligne_table(this);
}