//OPAC3 : Tags de sélection avec criteres qui s'ouvrent dans un bloc caché d'origine

//----------------------------------------------------------
// Ouvrir / fermer le bloc de saisie et permuter le picto
//----------------------------------------------------------
function ouvrirFermer(oImg,sId)
{
	sImg=oImg.src;
	if(sImg.indexOf("ouvrir") > 0 )
	{
		oImg.src=sImg.replace("ouvrir", "fermer");
		getId(sId).style.display="block";
	}
	else
	{
		oImg.src=sImg.replace("fermer", "ouvrir");
		getId(sId).style.display="none";
	}
}

//----------------------------------------------------------
// Recup. de valeurs des caces a cocher
//----------------------------------------------------------
function getCoches(sIdChamp)
{
	// Handles
	oChampCodes=getId(sIdChamp);
	oContainer=getId(sIdChamp +"_saisie");
	oChampAff=getId(sIdChamp +"_aff");
	
	// Get elements cochés
	oChampCodes.value="";
	sAff="";
	bToutEstCoche=true;
	var listItems = oContainer.getElementsByTagName('INPUT');
	for(var no=0;no<listItems.length;no++)
	{
		oCheckBox=listItems[no];
		if(oCheckBox.getAttribute("type") != "checkbox") continue;
		if(oCheckBox.checked == true)
		{
			sLibelle=oCheckBox.nextSibling.data;
			if(oChampCodes.value > "") oChampCodes.value += ";";
			oChampCodes.value+=oCheckBox.getAttribute("clef");
			if( sAff > "") sAff +=" ";
			sAff+="«" + sLibelle + "»";
		}
		else bToutEstCoche=false;
	}
	if(bToutEstCoche == true)
	{
		oChampCodes.value="";
		sAff="";
	}
	oChampAff.innerHTML=sAff;
	setFlagMaj(true);
}

//----------------------------------------------------------
// Tout cocher ou decocher
//----------------------------------------------------------
function selectAll(sIdChamp,bMode)
{
	// Handles
	oChampCodes=getId(sIdChamp);
	oContainer=getId(sIdChamp +"_saisie");
	oChampAff=getId(sIdChamp +"_aff");
	
	// Affecter les valeurs
	var listItems = oContainer.getElementsByTagName('INPUT');
	for(var no=0;no<listItems.length;no++)
	{
		oCheckBox=listItems[no];
		if(oCheckBox.getAttribute("type") != "checkbox") continue;
		oCheckBox.checked=bMode;
	}
	oChampCodes.value="";
	oChampAff.innerHTML="";
	setFlagMaj(true);
}

//----------------------------------------------------------
// Lancer recherche ajax
//----------------------------------------------------------
function getSuggest(sRubrique,sChamp,sValeur,nMinCars)
{
	if(sValeur.length < nMinCars)
	{ 
		getId(sChamp + "_liste").innerHTML="";
		return;
	}
	while(sValeur.indexOf(" ")>0)
	{
		sValeur=sValeur.replace(" ", "+");
	}
	sMode=getId("mode_" + sChamp).value;
	sUrl=baseUrl + "/admin/ajax/listesuggestion?type_autorite=" + sRubrique + "&id_champ=" + sChamp + "&mode=" + sMode +"&valeur=" + sValeur;
	$('#'+sChamp+'_liste').load(sUrl);
}

//----------------------------------------------------------
// Champs suggestion selection d'un item
//----------------------------------------------------------
function selectSuggest(sIdChamp, oItem)
{
	// Handles
	oChampCodes=getId(sIdChamp);
	oChampAff=getId(sIdChamp +"_aff");
	
	// Controle s'il est deja la
	sClef=oItem.getAttribute("clef");
	sCodes=oChampCodes.value;
	sControle=";" + sCodes + ";";
	if(sControle.indexOf(";" + sClef + ";") >=0 )
	{ 
		bRet=confirm("Cet élément a déjà été sélectionné.\n\nVoulez vous le supprimer ?");
		if(bRet==true)
		{
			sControle=sControle.replace(";" + sClef + ";", ";");
			if(sControle.substr(0,1)==";") sControle=sControle.substr(1);
			if(sControle.substr(-1)==";") sControle=sControle.substr(0,sControle.length-1);
			oChampCodes.value=sControle;
			sAff=oChampAff.innerHTML;
			sItemAff="«" + oItem.innerHTML + "»"
			sAff=sAff.replace(sItemAff, "");
			oChampAff.innerHTML=sAff;
		}
		return;
	}
	
	// Integration de 'item sélectionné
	if(sCodes > "") sCodes+=";";
	sCodes+=sClef;
	oChampCodes.value=sCodes;
	
	sAff=oChampAff.innerHTML;
	if( sAff > "") sAff +=" ";
	sAff+="«" + oItem.innerHTML + "»"
	oChampAff.innerHTML=sAff;
	setFlagMaj(true);
}

//----------------------------------------------------------
// Champs suggestion effacer toute la selection
//----------------------------------------------------------
function suggestClear(sIdChamp)
{
	oChampCodes=getId(sIdChamp);
	if(oChampCodes.value=="")
	{ 
		alert("Il n'y a aucun élément à effacer.");
		return;
	}
	if(confirm("Etes vour sûr de vouloir effacer tous les élément sélectionnés ?")==false) return;
	oChampAff=getId(sIdChamp +"_aff");
	oChampCodes.value="";
	oChampAff.innerHTML="";
	setFlagMaj(true);
}