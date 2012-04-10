//////////////////////////////////////////////////////////////////////////////////////
// PLAN d'ACCES GOOGLE MAP
/////////////////////////////////////////////////////////////////////////////////////
var map;
var oCouches = new Array();
var hIcone = new Array();

function init_carte()
{
	// Redimentionner le container de la carte
	oMap=getId("map");
	nLargeur=oMap.parentNode.clientWidth;
	oMap.style.width=nLargeur + "px";

	// Initialisations
	oCouches = initOCouches();
	hIcone = initHIcone();
	
	if (GBrowserIsCompatible())
	{
		map = new GMap2(document.getElementById("map"));
		// Controles de navigation
		map.addControl(new GLargeMapControl());
    map.addControl(new GMapTypeControl());
    // Pour recherches par adresses
    geocoder = new GClientGeocoder();
    //Gestion du click
    GEvent.addListener(map, "click", function(overlay, point) 
    {
    if(overlay) afficher_info(overlay);
    });
    // Afficher la première couche
    afficher_couche(1);
  }
}

// Afficher une couche
function afficher_couche(nCouche)
{	
	// Afficher les points
	map.clearOverlays();
	map.setCenter(new GLatLng(parseFloat(oCouches[nCouche].longitude), parseFloat(oCouches[nCouche].latitude)), oCouches[nCouche].echelle);
	for(i=1; i<oCouches[nCouche].points.length; i++)
	{
		afficher_marker(nCouche,i,oCouches[nCouche].points[i].longitude,oCouches[nCouche].points[i].latitude,oCouches[nCouche].points[i].icone);
	}
}

// Affichage des point remarquables
function afficher_marker(nCouche, nIndex, nLongitude, nLatitude, sIcone)
{
	var point=new GLatLng(parseFloat(nLongitude), parseFloat(nLatitude));
	oCouches[nCouche].points[nIndex].handle=new GMarker(point,{icon:hIcone[sIcone]});
	map.addOverlay(oCouches[nCouche].points[nIndex].handle);
}

// Affichage bulle d'infos
function afficher_info(oSelect)
{
	for(nCouche=1; nCouche<oCouches.length; nCouche++)
	{
		for(nPoint=1; nPoint<oCouches[nCouche].points.length; nPoint++)
		{
			if(oSelect == oCouches[nCouche].points[nPoint].handle)
			{ 
				
				html = '<table cellpadding="0" cellspacing="0"><tr><td class="map_entete" colspan="2">' + oCouches[nCouche].points[nPoint].titre + '</td></tr><tr>';

				if(oCouches[nCouche].points[nPoint].photo != '')
				{
					html = html + '<td valign="top"><img src="' + oCouches[nCouche].points[nPoint].photo + '" border="0"></td><td class="map">';
				}
					else html = html + '<td colspan="2" class="map">';
		
				for(nInfo=1;nInfo< oCouches[nCouche].points[nPoint].infos.length;nInfo++) {
    				html = html + '<span class="map_titre">' + oCouches[nCouche].points[nPoint].infos[nInfo].titre + '</span><br>' + oCouches[nCouche].points[nPoint].infos[nInfo].texte + '<br><br>';
 				 }
  
				html = html + '</td></tr></table>';
				oSelect.openInfoWindowHtml(html,{maxWidth:500});
				return;
			}
		}
	}
}
// Création des icones
function creer_icone(sFic)
{
	oIcone = new Object();
	oIcone.image = sFic;
	oIcone.shadow = sFic.replace(".png","s.png");
	oIcone.iconSize = new GSize(32,32);
	oIcone.shadowSize = new GSize(59, 32);
	oIcone.iconAnchor = new GPoint(9, 32);
	oIcone.infoWindowAnchor = new GPoint(14, 6);
	return oIcone;
}
