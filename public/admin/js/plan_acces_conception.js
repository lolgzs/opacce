//////////////////////////////////////////////////////////////////////////////////////
// PLAN d'ACCES GOOGLE MAP en mode CONCEPTION
/////////////////////////////////////////////////////////////////////////////////////
var map = false;
var oCouches = new Array();
var hIcone = new Array();
var inputName = '';
var inputValue = '';
var globalNewLayer = "** nouvelle couche **";
var globalLastLayer = "Vous venez de supprimer la dernière couche de la carte. Une nouvelle couche va être créée.";
var globalDeleteLayer = "Etes-vous sur de vouloir supprimer cette couche ?"
var globalNewPoint = "** nouveau point **";
var globalLastPoint = "Etes-vous sur de vouloir supprimer ce point ?";
var globalLastPointLayer = "Vous venez de supprimer de dernier point de cette couche. Un nouveau point va être créé au centre de la couche.";
var globalNewInfo = "** nouvelle info **";
var globalNoAddress = "Impossible de trouver cette adresse";
var globalHtmlIcones = "";

//---------------------------------------------------------------------------------
// Initialisation de la carte
//---------------------------------------------------------------------------------
function init_carte()
{
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
            if (overlay) afficher_bulle(overlay);
        });
        // Afficher la première couche ou en creer une s'il n'y en a pas
        if(!oCouches.length) creer_couche();
        else
        {
            // Creer les numéros de couches dans la combo
            oSelect=document.getElementById("couche");
            for(i=1;i<oCouches.length; i++)	oSelect.options[oSelect.options.length]=new Option(i,i);
            afficher_couche(1);
        }
    }
}

// Controle clef google et redirection si pas ok
function controle_clef()
{
    if(!map)window.location.replace(baseUrl+"/admin/planaccess/clefgooglefailed");
}

//---------------------------------------------------------------------------------
// AFFICHAGE
//---------------------------------------------------------------------------------

// Afficher une couche
function afficher_couche(nCouche)
{
    // Effacer la couche existante
    map.clearOverlays();
    reset_combo("point");
    for(i=1;i<oCouches[nCouche].points.length; i++) oSelect.options[oSelect.options.length]=new Option(i,i);
    reset_combo("info");
    for(i=1;i<oCouches[nCouche].points[1].infos.length; i++) oSelect.options[oSelect.options.length]=new Option(i,i);
	
    // Afficher la nouvelle couche
    map.setCenter(new GLatLng(parseFloat(oCouches[nCouche].longitude), parseFloat(oCouches[nCouche].latitude)), parseInt(oCouches[nCouche].echelle));
    for(i=1; i<oCouches[nCouche].points.length; i++)
    {
        afficher_marker(nCouche,i,oCouches[nCouche].points[i].longitude,oCouches[nCouche].points[i].latitude,oCouches[nCouche].points[i].icone);
    }
    afficher_champs(nCouche,1,1);
}

// Affichage des points 
function afficher_marker(nCouche, nPoint, nLongitude, nLatitude, sIcone)
{
    var point=new GLatLng(parseFloat(nLongitude), parseFloat(nLatitude));
    var oMarker=new GMarker(point,{
        icon:hIcone[sIcone],
        draggable:true
    });
    oCouches[nCouche].points[nPoint].handle=oMarker;
    GEvent.addListener(oMarker, "dragstart", function() {
        document.getElementById("point").value=nPoint;
        afficher_point();
    });
    GEvent.addListener(oMarker, "dragend", function() {
        deplacer_point(oCouches[nCouche].points[nPoint].handle.getPoint());
    });
    map.addOverlay(oMarker);
}

// Affichage bulle d'infos
function afficher_bulle(oSelect)
{

    reprendre_valeurs();
	
    for(nCouche=1; nCouche<oCouches.length; nCouche++)
    {
        for(nPoint=1; nPoint<oCouches[nCouche].points.length; nPoint++)
        {
            if(oSelect == oCouches[nCouche].points[nPoint].handle)
            {
                document.getElementById("point").value=nPoint;
                afficher_point();
				
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

                //oSelect.openInfoWindowHtml(oCouches[nCouche].points[nPoint].html,{maxWidth:500});
                oSelect.openInfoWindowHtml(html,{
                    maxWidth:500
                });
                return;
            }
        }
    }
}

//---------------------------------------------------------------------------------
// INTERFACE  DONNEES et HTML 
//---------------------------------------------------------------------------------

function afficher_champs(nCouche,nPoint,nInfo)
{
    document.getElementById("couche").value=nCouche;
    document.getElementById("couche_titre").value=oCouches[nCouche].titre;
    document.getElementById("couche_long").value=oCouches[nCouche].longitude;
    document.getElementById("couche_lat").value=oCouches[nCouche].latitude;
    document.getElementById("couche_echelle").value=oCouches[nCouche].echelle;
	
    document.getElementById("point").value=nPoint;
    document.getElementById("point_titre").value=oCouches[nCouche].points[nPoint].titre;
    document.getElementById("point_long").value=oCouches[nCouche].points[nPoint].longitude;
    document.getElementById("point_lat").value=oCouches[nCouche].points[nPoint].latitude;
    document.getElementById("point_icone").value=oCouches[nCouche].points[nPoint].icone;
    document.getElementById("point_adresse").value=oCouches[nCouche].points[nPoint].adresse;
    document.getElementById("point_ville").value=oCouches[nCouche].points[nPoint].ville;
    document.getElementById("point_pays").value=oCouches[nCouche].points[nPoint].pays;
    document.getElementById("point_photo").value=oCouches[nCouche].points[nPoint].photo;
	
    document.getElementById("info").value=nInfo;
    document.getElementById("info_titre").value=remplace("<br>","\n",oCouches[nCouche].points[nPoint].infos[nInfo].titre);
    document.getElementById("info_texte").value=remplace("<br>","\n",oCouches[nCouche].points[nPoint].infos[nInfo].texte);
}

function reprendre_valeurs()
{
    nCouche=parseInt(document.getElementById("couche").value);
    oCouches[nCouche].titre=document.getElementById("couche_titre").value;
    oCouches[nCouche].longitude=document.getElementById("couche_long").value;
    oCouches[nCouche].latitude=document.getElementById("couche_lat").value;
    oCouches[nCouche].echelle=document.getElementById("couche_echelle").value;
	
    nPoint=parseInt(document.getElementById("point").value);
    oCouches[nCouche].points[nPoint].titre=document.getElementById("point_titre").value;
    oCouches[nCouche].points[nPoint].longitude=document.getElementById("point_long").value;
    oCouches[nCouche].points[nPoint].latitude=document.getElementById("point_lat").value;
    oCouches[nCouche].points[nPoint].icone=document.getElementById("point_icone").value;
    oCouches[nCouche].points[nPoint].adresse=document.getElementById("point_adresse").value;
    oCouches[nCouche].points[nPoint].ville=document.getElementById("point_ville").value;
    oCouches[nCouche].points[nPoint].pays=document.getElementById("point_pays").value;
    oCouches[nCouche].points[nPoint].photo=document.getElementById("point_photo").value;
	
    nInfo=parseInt(document.getElementById("info").value);
    oCouches[nCouche].points[nPoint].infos[nInfo].titre=document.getElementById("info_titre").value;
    oCouches[nCouche].points[nPoint].infos[nInfo].texte=remplace("\n","<br>",document.getElementById("info_texte").value);
}

// Validation de la carte
function valider_carte()
{
    var sData="";
    var sSep="$";
    for(nCouche=1;nCouche<oCouches.length; nCouche++)
    {
        sData += "[COUCHE]" +sSep;
        sData += "titre=" + oCouches[nCouche].titre +sSep;
        sData += "longitude=" + oCouches[nCouche].longitude +sSep;
        sData += "latitude=" + oCouches[nCouche].latitude +sSep;
        sData += "echelle=" + oCouches[nCouche].echelle +sSep;
        for(nPoint=1;nPoint< oCouches[nCouche].points.length; nPoint++)
        {
            oPoint=oCouches[nCouche].points[nPoint];
            sData += "[POINT]" +sSep;
            sData += "titre=" + oPoint.titre +sSep;
            sData += "longitude=" + oPoint.longitude +sSep;
            sData += "latitude=" + oPoint.latitude +sSep;
            sData += "icone=" + oPoint.icone +sSep;
            sData += "adresse=" + oPoint.adresse +sSep;
            sData += "ville=" + oPoint.ville +sSep;
            sData += "pays=" + oPoint.pays +sSep;
            sData += "photo=" + oPoint.photo +sSep;
            for(nInfo=1;nInfo< oCouches[nCouche].points[nPoint].infos.length; nInfo++)
            {
                oInfo=oPoint.infos[nInfo];
                sData += "[INFO]" +sSep;
                sData += "titre=" + oInfo.titre +sSep;
                sData += "texte=" + oInfo.texte +sSep;
            }
        }
    }
    document.getElementById("champ_data").value=sData;
    document.forms[0].submit();
}

//---------------------------------------------------------------------------------
// COUCHES
//---------------------------------------------------------------------------------

// Creer une couche
function creer_couche()
{
    // Couche
    nCouche=oCouches.length;
    if(nCouche==0) nCouche=1;
    oSelect=document.getElementById("couche");
    oSelect.options[oSelect.options.length]=new Option(nCouche,nCouche);
    oSelect.value=nCouche;
    oCouches[nCouche] = new Object();
    oCouches[nCouche].points=new Array();
    oCouches[nCouche].titre=globalNewLayer;
	
    // Reset du combo des points
    reset_combo("point");
    oCouches[nCouche].longitude=document.getElementById("couche_long").value;
    oCouches[nCouche].latitude=document.getElementById("couche_lat").value;
    oCouches[nCouche].echelle=document.getElementById("couche_echelle").value;
	
    // Creer le premier point
    creer_point();
    afficher_couche(nCouche);
}

// Supprimer une couche
function supprimer_couche()
{
    if(!confirm(globalDeleteLayer)) return;
    // Effacer du combo-box
    nCouche=parseInt(document.getElementById("couche").value);
    reset_combo("couche");
    for(i=1;i<oCouches.length-1; i++) oSelect.options[oSelect.options.length]=new Option(i,i);
	
    // Décaler la matrice
    oCouches.splice([nCouche],1);
    if(oCouches.length==1)
    {
        alert(globalLastLayer);
        creer_couche();
    }
    else afficher_couche(parseInt(document.getElementById("couche").value));
}

//---------------------------------------------------------------------------------
// POINTS
//---------------------------------------------------------------------------------

// Afficher un point
function afficher_point()
{
    nCouche=document.getElementById("couche").value;
    nPoint=document.getElementById("point").value;
    reset_combo("info");
    for(i=1;i<oCouches[nCouche].points[nPoint].infos.length; i++) oSelect.options[oSelect.options.length]=new Option(i,i);
    afficher_champs(nCouche,nPoint,1);
    // Recentrer la carte si point est en dehors
    var point=oCouches[nCouche].points[nPoint].handle.getPoint();
    if(!map.getBounds().contains(point))map.panTo(point);
}

// Creer un point
function creer_point()
{
    nCouche=parseInt(document.getElementById("couche").value);
    nPoint=oCouches[nCouche].points.length;
    if(nPoint==0) nPoint=1;
    oSelect=document.getElementById("point");
    oSelect.options[oSelect.length]=new Option(nPoint,nPoint);
    oSelect.value=nPoint;
    oCouches[nCouche].points[nPoint] = new Object();
    oCouches[nCouche].points[nPoint].infos = new Array();
    oCouches[nCouche].points[nPoint].titre=globalNewPoint;
    oCouches[nCouche].points[nPoint].longitude=oCouches[nCouche].longitude;
    oCouches[nCouche].points[nPoint].latitude=oCouches[nCouche].latitude;
    oCouches[nCouche].points[nPoint].icone=0;
    oCouches[nCouche].points[nPoint].adresse='';
    oCouches[nCouche].points[nPoint].ville='';
    oCouches[nCouche].points[nPoint].pays='';
    oCouches[nCouche].points[nPoint].photo='';
    afficher_marker(nCouche,nPoint,oCouches[nCouche].points[nPoint].longitude,oCouches[nCouche].points[nPoint].latitude,oCouches[nCouche].points[nPoint].icone);
	
    // Creer la première info
    reset_combo("info");
    creer_info();
    afficher_point();
}

// Supprimer un point
function supprimer_point()
{
    if(!confirm(globalLastPoint)) return;
    // Effacer du combo-box
    nCouche=parseInt(document.getElementById("couche").value);
    oSelect=document.getElementById("point");
    nPoint=parseInt(oSelect.value);
    map.removeOverlay(oCouches[nCouche].points[nPoint].handle);
    reset_combo("point");
    for(i=1;i<oCouches[nCouche].points.length-1; i++) oSelect.options[oSelect.options.length]=new Option(i,i);
	
    // Décaler la matrice
    oCouches[nCouche].points.splice([nPoint],1);
    if(oCouches[nCouche].points.length==1)
    {
        alert(globalLastPointLayer);
        creer_point();
    }
    else afficher_point();
}

// Deplacer un point
function deplacer_point(point)
{
    nCouche=document.getElementById("couche").value;
    nPoint=document.getElementById("point").value;
    oCouches[nCouche].points[nPoint].handle.setPoint(point);
    document.getElementById("point_long").value=point.y;
    document.getElementById("point_lat").value=point.x;
    reprendre_valeurs();
}

//---------------------------------------------------------------------------------
// INFOS
//---------------------------------------------------------------------------------

function afficher_info()
{
    nCouche=document.getElementById("couche").value;
    nPoint=document.getElementById("point").value;
    nInfo=document.getElementById("info").value;
    afficher_champs(nCouche,nPoint,nInfo);
}

function creer_info()
{
    nCouche=parseInt(document.getElementById("couche").value);
    nPoint=parseInt(document.getElementById("point").value);
    nInfo=oCouches[nCouche].points[nPoint].infos.length;
    if(nInfo==0) nInfo=1;
    oSelect=document.getElementById("info");
    oSelect.options[oSelect.length]=new Option(nInfo,nInfo);
    oSelect.value=nInfo;
    oCouches[nCouche].points[nPoint].infos[nInfo] = new Object();
    oCouches[nCouche].points[nPoint].infos[nInfo].titre=globalNewInfo;
    oCouches[nCouche].points[nPoint].infos[nInfo].texte='';
    afficher_info();
}

function supprimer_info()
{
    // Effacer du combo-box
    nCouche=parseInt(document.getElementById("couche").value);
    nPoint=parseInt(document.getElementById("point").value);
    oSelect=document.getElementById("info");
    nInfo=parseInt(oSelect.value);
    reset_combo("info");
    for(i=1;i<oCouches[nCouche].points[nPoint].infos.length-1; i++) oSelect.options[oSelect.options.length]=new Option(i,i);
	
    // Décaler la matrice
    oCouches[nCouche].points[nPoint].infos.splice([nInfo],1);
    if(oCouches[nCouche].points[nPoint].infos.length==1) creer_info();
    else afficher_info();
}

function copier_adresse_info()
{
    sAdresse=document.getElementById("point_adresse").value;
    sVille=document.getElementById("point_ville").value;
    sPays=document.getElementById("point_pays").value;
    document.getElementById("info_titre").value="Adresse";
    document.getElementById("info_texte").value=sAdresse+"\n"+sVille+"\n"+sPays;
    reprendre_valeurs();
}

//---------------------------------------------------------------------------------
// FONCTIONS COMPLEMENTAIRES
//---------------------------------------------------------------------------------

// Création des icones
function creer_icone(sFic)
{
    oIcone = new GIcon();
    oIcone.image = sFic;
    oIcone.shadow = sFic.replace(".png","s.png");
    oIcone.iconSize = new GSize(32,32);
    oIcone.shadowSize = new GSize(59,32);
    oIcone.iconAnchor = new GPoint(15,37);
    oIcone.infoWindowAnchor = new GPoint(14,6);
    return oIcone;
}

// Remplacer une icone
function changer_icone()
{
    nCouche=document.getElementById("couche").value;
    nPoint=document.getElementById("point").value;
    nIcone=parseInt(document.getElementById("point_icone").value);
    oCouches[nCouche].points[nPoint].icone=nIcone;
    map.removeOverlay(oCouches[nCouche].points[nPoint].handle);
    afficher_marker(nCouche,nPoint,oCouches[nCouche].points[nPoint].longitude,oCouches[nCouche].points[nPoint].latitude,nIcone);
}

// Fixer le point de centrage
function point_centrage()
{
    oCentre=map.getCenter();
    document.getElementById("couche_long").value=oCentre.lat();
    document.getElementById("couche_lat").value=oCentre.lng();
    document.getElementById("couche_echelle").value=map.getZoom();
    reprendre_valeurs();
}

function cherche_adresse() 
{
    // Recup adresse
    sAdresse=document.getElementById("point_adresse").value;
    sAdresse = sAdresse + ', ' + document.getElementById("point_ville").value;
    sAdresse = sAdresse + ', ' + document.getElementById("point_pays").value;
    ;
    // Afficher
    geocoder.getLatLng( sAdresse, function(point)
    {
        if (!point) alert(globalNoAddress);
        else
        {
            // Rectifier l'échelle si on est centré sur la france
            if(document.getElementById("couche_echelle").value == 5)
            {
                document.getElementById("couche_echelle").value=15;
                map.setZoom(15);
            }
            if(!map.getBounds().contains(point))
            {
                document.getElementById("couche_long").value=point.y;
                document.getElementById("couche_lat").value=point.x;
            }
            map.panTo(point);
            deplacer_point(point);
        }
    } );
}

// Reset options d'un combo
function reset_combo(sId)
{
    oSelect=document.getElementById(sId);
    nNombre=oSelect.options.length;
    for(i=0;i<nNombre; i++) oSelect.options[0]=null;
}

function setPlanAccessTranslateText( translateNewLayer, translateLastLayer, translateNewPoint, translateLastPoint, translateLastPointLayer, translateNewInfo, translateNoAddress, translateDeleteLayer) 
{
    globalNewLayer = translateNewLayer;
    globalLastLayer = translateLastLayer;
    globalNewPoint = translateNewPoint;
    globalLastPoint = translateLastPoint;
    globalLastPointLayer = translateLastPointLayer;
    globalNewInfo = translateNewInfo;
    globalNoAddress = translateNoAddress;
    globalDeleteLayer = translateDeleteLayer;
}

function setHtmlIcones( htmlIcones)
{
    globalHtmlIcones = htmlIcones;
}

function setInputName(varInputName) {
    inputName = varInputName;
}

function setInputValue(varInputValue) {
    inputValue = varInputValue;
}

function selectImage() {
    el = document.getElementById(inputName);
    el.value = inputValue;
    inputName = '';
    inputValue = '';
}

// File Picker modification for FCK Editor v2.0 - www.fckeditor.net
// by: Pete Forde <pete@unspace.ca> @ Unspace Interactive

var urlobj;

function BrowseServer(obj)
{
    urlobj = obj;
	
    OpenServerBrowser(    
				ckBaseUrl + 'core_five_filemanager/index.html?Type=Image&ServerPath=' + userFilesUrl + 'image/',
        screen.width * 0.8,
        screen.height * 0.8);
}

function OpenServerBrowser( url, width, height )
{
    var iLeft = (screen.width  - width) / 2 ;
    var iTop  = (screen.height - height) / 2 ;

    var sOptions = "toolbar=no,status=no,resizable=yes,dependent=yes" ;
    sOptions += ",width=" + width ;
    sOptions += ",height=" + height ;
    sOptions += ",left=" + iLeft ;
    sOptions += ",top=" + iTop ;

    var oWindow = window.open( url, "BrowseWindow", sOptions ) ;
}

function SetUrl( url, width, height, alt )
{
    document.getElementById(urlobj).value = url ;
    oWindow = null;
    reprendre_valeurs();
}

function ClearPhoto(obj)
{
    document.getElementById(obj).value = '';
    reprendre_valeurs();
}

// Init de la carte au load
$(document).ready(init_carte);
$(document).ready(controle_clef);
$(document).unload(GUnload);