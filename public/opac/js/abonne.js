///////////////////////////////////////////////////////////////////////////////
// AIGUILLAGE ABONNE AVEC IDENTIFICATION
///////////////////////////////////////////////////////////////////////////////
var sUrlToAbonne;
var bAbonneConnected=0;

function fonction_abonne(nIdAbon,sUrl)
{
	if(nIdAbon > 0 || bAbonneConnected==true)
	{
		if (nIdAbon == 0)	sUrl = baseUrl + '/admin/';
		redirection_abonne(sUrl);
		return;
	}
	else
	{
		sUrlToAbonne=sUrl;
		sUrl=baseUrl + '/opac/auth/ajaxlogin';
		showPopWin(sUrl, 470, 290, null);
	}
}

function logout()
{
	document.location= baseUrl + '/auth/logout';
}

function abonne_ok(idAbon, varAbonneNom, varTradDeconnecter)
{
	//alert(sUrlToAbonne);
	bAbonneConnected=true;
	if ((varAbonneNom!=undefined) && (varAbonneNom!=''))
	{
		e = document.getElementById("barreNavNom");
		if (e!=undefined)	e.innerHTML = varAbonneNom;
	}
	e = document.getElementById("connecterMenu");
	if (e!=undefined)
	{
		e.innerHTML = varTradDeconnecter;
		e.onclick = logout;
	}
	
	e1 = document.getElementById("connecterMenuH");
	if (e1!=undefined)
	{
		e1.innerHTML = varTradDeconnecter;
		e1.onclick = logout;
	}
	if(sUrlToAbonne)fonction_abonne(idAbon,sUrlToAbonne);
}

function redirection_abonne( sAction )
{
	varLength = baseUrl.length;
	if( sAction.slice(0,varLength) == baseUrl) 
	{
		document.location.replace(sAction); 
		return;
	}
	else showPopWin(baseUrl + sAction, 500, 345, null);
}