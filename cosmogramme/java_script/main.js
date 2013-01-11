//////////////////////////////////////////////////////////////////////////////////////
// Java script global
/////////////////////////////////////////////////////////////////////////////////////

//---------------------------------------------------------------------------------
// Contracter / deployer un bloc
//---------------------------------------------------------------------------------
function contracter_bloc(sIdObjet)
{
	visible = document.getElementById(sIdObjet).style.display;
	oImg=document.getElementById("I" + sIdObjet);
	if( visible == "block" )
	{
		if(oImg != null) oImg.setAttribute("src", sUrlImg + "plus.gif");
		document.getElementById(sIdObjet).style.display="none";
		return false;
	}
	else
	{
		if(oImg != null) oImg.setAttribute("src",sUrlImg + "moins.gif");
		document.getElementById(sIdObjet).style.display="block";
	}
}

//---------------------------------------------------------------------------------
// Affiche le pavé adequat en fonction du type et du format de fichier
//---------------------------------------------------------------------------------
function activerFormat(sIdProfil)
{
	// Recup type fichier et format
	oContainer=document.getElementById("profil" + sIdProfil);
	oCombosType=new Array();
	var listItems=oContainer.getElementsByTagName('SELECT');
	for(var no=0;no<listItems.length;no++)
	{
		sId=listItems[no].id;
		if(sId=="type_fichier") var sTypeFichier=listItems[no].value;
		if(sId=="format") var sFormat=listItems[no].value;
	}
	// Recup objets div du detail
	var listItems=oContainer.getElementsByTagName('DIV');
	oBlocUnimarc=new Array();
	for(var no=0;no<listItems.length;no++)
	{
		sId=listItems[no].id;
		if(sId.substr(0,8)=="unimarc_") oBlocUnimarc[oBlocUnimarc.length]=listItems[no];
		if(sId=="ascii") var oBlocAscii=listItems[no];
		if(sId=="fmt_xml") var oBlocXml=listItems[no];
		if(sId.substr(0,11)=="combo_type_") oCombosType[oCombosType.length]=listItems[no];
	}

	// Identifiants des objets a afficher
	if(sFormat == "0" || sFormat=="6")
	{
		if(sTypeFichier == "0") sIdBlocFormat="unimarc_0";
		else sIdBlocFormat="unimarc_1";
		oBlocAscii.style.display="none";
		oBlocXml.style.display="none";
		sIdComboType="";
	}
	else
	{
		sIdBlocFormat="";
		if(sFormat==4)
		{
			oXmlPasSupporte=document.getElementById("xml_pas_supporte");
			oXmlAbonne=document.getElementById("xml_abonne");
			if(sTypeFichier != 1)
			{
				oXmlPasSupporte.style.display="block";
				oXmlAbonne.style.display="none";
			}
			else
			{
				oXmlPasSupporte.style.display="none";
				oXmlAbonne.style.display="block";
			}
			oBlocXml.style.display="block";
			oBlocAscii.style.display="none";

		}
		else
		{
			oBlocAscii.style.display="block";
			oBlocXml.style.display="none";
			sIdComboType="combo_type_" + sTypeFichier;
		}
	}
	// Afficher
	for(i=0; i<oBlocUnimarc.length; i++) if(oBlocUnimarc[i].id==sIdBlocFormat)oBlocUnimarc[i].style.display="block"; else oBlocUnimarc[i].style.display="none";
	for(i=0; i<oCombosType.length; i++) if(oCombosType[i].id==sIdComboType)oCombosType[i].style.display="block"; else oCombosType[i].style.display="none";
}

//---------------------------------------------------------------------------------
// Sélection des champs dans les profils de données
//---------------------------------------------------------------------------------
function selectChamp(sIdChamp, sValeur)
{
	oChamp=document.getElementById(sIdChamp);
	oAff=document.getElementById("A" + sIdChamp);
	sChamp=oChamp.value;
	if(sChamp.length > 0)
	{
		if(sValeur == "")
		{
			nPos=sChamp.lastIndexOf(";",sChamp.length);
			if(nPos < 0) sChamp="";
			else sChamp=sChamp.substring(0,nPos);
		}
		else if(sValeur !="NULL")
		{
			nPos=sChamp.indexOf(sValeur,0);
			if(nPos >= 0) sValeur=""; 
			else sChamp = sChamp + ";";
		}
		else 
		{
			sChamp = sChamp + ";";
		}
	}
	sChamp=sChamp + sValeur;
	oChamp.value=sChamp;
	oAff.innerHTML=sChamp;
}

//---------------------------------------------------------------------------------
// Affichage du bloc des parametres (config des integration programmees)
//---------------------------------------------------------------------------------
function activerBlocCommBib(sIdBib,sType)
{
	sIdSelect="comm_" + sIdBib + "_" + sType;
	for(var i=0; i<100; i++)
	{
		sId="comm_" + sIdBib + "_" + i;
		oContainer = document.getElementById(sId);
		if(!oContainer) break;
		if(sId == sIdSelect) sDisplay="block"; else sDisplay="none";
		oContainer.style.display=sDisplay;
	}
}