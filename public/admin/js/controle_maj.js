//////////////////////////////////////////////////
// OPAC3 : Controle de modif d'une form
/////////////////////////////////////////////////

//--------------------------------------------------------------------------	
// Rajouter une alerte au texte standard du dialogue
//--------------------------------------------------------------------------	
function controleMaj(event) {
		if (event != null) {
				event.preventDefault();
				event.stopPropagation();
		}
		return "ATTENTION : si vous quittez cette page, toutes vos modifications seront perdues.";
}

//--------------------------------------------------------------------------	
// Setter du controle de sortie
//--------------------------------------------------------------------------
function setFlagMaj(bMode)
{

		window.showModalDialog = function( sURL,vArguments, sFeatures)
		{
				if(retVal!=null) return retVal;
				modalWin = window.open(sURL, 'modal', sFeatures)
		}
		if(bMode==true) 
				window.onbeforeunload = controleMaj;
		else 
				window.onbeforeunload = null;//cancelEvent;
}

//--------------------------------------------------------------------------	
// Stopper l'event si on clique sur valider
//--------------------------------------------------------------------------
function cancelEvent(e) {
		if (!e) e = window.event;
		if (e.preventDefault) e.preventDefault();
		else return true;
}