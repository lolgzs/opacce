// OPAC3 : Sélection des bibliothèques
function envoie(sUrl) 
{
	var chaine="", toutEstCoche=true;
	for (var i=0;i<document.selection.length;i++) 
	{
		if (document.selection[i].checked==true) 
		{
			if(chaine > '') chaine=chaine+",";
			chaine=chaine+document.selection[i].name;
		}
		else toutEstCoche=false;	
	}  
	if(toutEstCoche==true | chaine == "") chaine="TOUT";
	document.location=sUrl + chaine;
}

function selectall()
{
	elm=document.selection.length;
	for(var i=0;i<elm;i++) document.selection[i].checked="checked";
}
	
function deselectall()
{
	elm=document.selection.length;
	for(var i=0;i<elm;i++) document.selection[i].checked="";
}
