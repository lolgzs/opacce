//////////////////////////////////////////////////////////////////////////////////////
// OPAC3 : Resultats de recherches
/////////////////////////////////////////////////////////////////////////////////////
var sCache_notice = new Object();
var sPatience='<div align="center"><img src="'+imagesUrl+'patience.gif"></div>';
var sBlocInfos=new Object();

function deployer_contracter(sIdObjet)
{
	visible = document.getElementById(sIdObjet).style.display;
	oImg=document.getElementById("I" + sIdObjet);
	if( visible == "block" ) 
		{
			if(oImg != null) oImg.setAttribute("src", imagesUrl + "bouton/plus_carre.gif");
			document.getElementById(sIdObjet).style.display="none";
			return false;
		}
	else
		{
			if(oImg != null) oImg.setAttribute("src", imagesUrl + "bouton/moins_carre.gif");
			oConteneur=document.getElementById(sIdObjet);
			oConteneur.style.display="block";
			return true;
		}
}

function refreshOnglet(sIdOnglet)
{
	sBlocInfos=new Object();
	obj=document.getElementById(sIdOnglet);
	var sOnclick=obj.onclick;
	sOnclick=sOnclick.toString();
	sOnclick=sOnclick.replace("this.id","'" + sIdOnglet + "'");
	nPos=sOnclick.indexOf("{",0)+1;
	nPosFin=sOnclick.indexOf("}",0);
	sOnclick=sOnclick.substring(nPos,nPosFin);
	//On clique 2 fois pour fermer puis recharger
	eval(sOnclick + ";");
	eval(sOnclick + ";");
}

function getNoticeAjax(sIdNotice,sContainer,sTypeDoc)
{
	sUrl=baseUrl + '/opac/noticeajax/notice?id_notice='+ sIdNotice + "&type_doc=" + sTypeDoc;
	$('#' + sContainer).load(sUrl);
}

////////////////////////////////////////////////////////////////////////////////////
// Onglets et blocs ajax
///////////////////////////////////////////////////////////////////////////////////
function infos_onglet(sIdOngletCourant,sIsbn,sType,sCherche,nNiveau,nPage)
{

	$('.onglet_select').attr('className','onglet').removeClass('onglet_select');
	$('#'+sIdOngletCourant).attr('className','onglet_select').addClass('onglet_select');
	$('div.onglet').css('display','none');
	
	sIdRoot=sIdOngletCourant;
	nPos=sIdRoot.lastIndexOf("_");
	sIdRoot=sIdRoot.substr(0,nPos + 1);
	nIdNotice=sIdRoot.substr(3,nPos-1).replace("_onglet_","");
	sUrl=baseUrl + '/opac/noticeajax/'+ sType + '?isbn=' + sIsbn + '&onglet=' + sIdOngletCourant + '&page=' + nPage +'&id_notice='+ nIdNotice;
	if(sCherche) sUrl = sUrl + '&cherche=' + sCherche;
	var onglet = $('#' + sIdOngletCourant +'_contenu');
	onglet
		.css('display','block')
		.css('max-width', onglet.parent().width()+'px')
		.load(sUrl, function() { blocNoticeAfterLoad(sType, sIsbn, onglet); } );

}


if (undefined == window.blocNoticeAfterLoad) 
	window.blocNoticeAfterLoad = function (info, isbn, target) {};


function infos_bloc(sIdBloc,sIsbn,sType,sCherche,nNiveau,nPage)
{
	if(sIdBloc.substr(0,1) == "I" ) sIdBloc=sIdBloc.substr(1,sIdBloc.length);
	oImg=document.getElementById("I" + sIdBloc);
	oContenu=document.getElementById(sIdBloc + "_contenu");
	if(oContenu.style.display =="block" && !sCherche)
	{
		oImg.setAttribute("src", imagesUrl + "bouton/plus_carre.gif");
		oContenu.style.display="none";
		return;
	}

	oImg.setAttribute("src", imagesUrl + "bouton/moins_carre.gif");
	oContenu.style.display="block";
	nPos=sIdBloc.lastIndexOf("_");
	nIdNotice=sIdBloc.substr(5,nPos-5);
	sUrl=baseUrl + '/opac/noticeajax/'+ sType + '?isbn=' + sIsbn + '&onglet=' + sIdBloc + '&page=' + nPage +'&id_notice=N'+ nIdNotice;
	if(sCherche) sUrl = sUrl + '&cherche=' + sCherche;
	
	var bloc = $('#'+sIdBloc + '_contenu');
	bloc
		.css('max-width', bloc.parent().width()+'px')
		.load(sUrl, function() { blocNoticeAfterLoad(sType, sIsbn, bloc); });
}
	
function fermer_infos_notice(sId)
{
	$('#'+sId).attr('className','onglet').attr('class','onglet');
	$('#'+sId+'_contenu').css('display','none');
}

//////////////////////////////////////////////////////////////////////////////////
// Localiser exemplaire sur le plan
/////////////////////////////////////////////////////////////////////////////////
var saveImg="";
function localisationExemplaire(oImg,nIdBib,sCote,sCodeBarres)
{
	saveImg=$(oImg).attr('src');
	$(oImg).attr('src',imagesUrl+'patience.gif');
	sUrl=baseUrl+'/opac/noticeajax/localisation/id_bib/' + nIdBib + '?cote='+ sCote + '&code_barres=' + sCodeBarres;
	$.getJSON(sUrl, function(data)
	{
		if(data.erreur > '') {$(oImg).attr('src',saveImg); alert(data.erreur); return; }
		if(data.url == null) {localisationBulle(data.titre,data.description,data.photo); return; }
		$('#point_localisation > img').attr('src',data.animation);
		$('#point_localisation').click(function(){ localisationBulle(oImg,data.titre,data.description,data.photo)});
		$('#plan_localisation').html('<a href="'+data.url+'" rel="lightbox" title="'+data.description+'"><img id="img_plan" src="'+data.url+'" posX="'+data.posX+'" posY="'+data.posY+'"></a>');
		jQuery(function($) {	$("a[rel^='lightbox']").slimbox({onClose:function(){$('#point_localisation').css('display','none');$(oImg).attr('src',saveImg);}},null,null); });
		$('#img_plan').trigger('click');
		localisationLoaded();
	});
}
function localisationLoaded()
{
	if($('#lbBottomContainer').css('display')=='none')
	{
		window.setTimeout(localisationLoaded,1000);
		return;
	}
	$('#point_localisation').css('display','block');
	x=parseInt($('#img_plan').attr('posX'));
	y=parseInt($('#img_plan').attr('posY'));
	container=$('#lbImage').offset();
	x+= container.left;
	y+=container.top;
	$('#point_localisation')
	.css('display','block')
	.css('top',y+'px')
	.css('left',x+'px')
	.click (function(){$('#point_localisation').trigger('click')});
}
function localisationBulle(oImg,titre,description,image)
{
	// Ajouter le contenu
	if( image > '') image='<img src="'+image+'" style="margin-right:10px;float:left;width:150px">';
	$("#bulle_localisation").attr('title',titre).html(image+'<p>'+description+'</p>');

	// Afficher
	$("#bulle_localisation").dialog
	({
		modal:true,
		resizable: false,
		width: 300,
		zIndex: 11000,
		buttons: { "Fermer": function() { $(this).dialog("close"); } }
	});
	$(oImg).attr('src',saveImg);
}

////////////////////////////////////////////////////////////////////////////////////
// AFFICHAGE IMAGES
///////////////////////////////////////////////////////////////////////////////////

function afficher_image(sUrl)
{
    bProcessing=false;
    if(!sUrl)
    {
        alert("Grande image non disponible.");
        return;
    }
    afficher_fond_gris(true);
    oBoite=document.getElementById("img_boite");
    if(!oBoite)
    {
        oBoite=document.createElement("div");
        oBoite.setAttribute("id","img_boite");
        oBoite.setAttribute("class","notice_img");
        oBoite.setAttribute("className","notice_img");
        oBoite.setAttribute("style","display:block;position:absolute");
        document.body.appendChild(oBoite);
    }
    // Patience
    sHtml='<center><table style="margin-top:40px"><tr><td class="notice_patience" style="text-align:right;width:15px"><img src="' + imagesUrl + 'patience.gif"';
    sHtml+='border="0"></td><td><b>&nbsp;Veuillez patienter chargement en cours...</b></td></tr></table>';
    oBoite.innerHTML=sHtml;
    centrer_image(oBoite,300,100, false);

    // Charger image
    oImage = new Image();
    oImage.src=sUrl;
    oImage.onError = function()
    {
        oBoite.style.display="none";
        afficher_fond_gris(false);
        alert("Une erreur s'est produite au chargement de l'image.");
    };
    setTimeout( 'image_completed(oImage)', 100 );
}

function image_completed()
{
    if(oImage.complete == true)
    {
        sOnclick='document.getElementById(\'img_boite\').style.display=\'none\';afficher_fond_gris(false);';
        sHtml='<div style="height:30px;width:100%;text-align:right;margin-top:5px;"><a href="#" onclick="' + sOnclick + '">'
        sHtml+='&raquo&nbsp;Fermer l\'image&nbsp;&nbsp;</a></div>';
        sHtml+='<img border="0" id="img_image" style="margin:10px;margin-top:0px;border:1px solid;border-color:#bfbfbf;cursor:pointer;"';
        sHtml+=' src="' + oImage.src + '" onclick="' + sOnclick + '">';
        oBoite=document.getElementById('img_boite');
        oBoite.innerHTML=sHtml;
        centrer_image(oBoite,(oImage.width + 20) ,(oImage.height + 50), true);
    }
    else setTimeout( 'image_completed(oImage)', 100 );
}

function centrer_image(oBoite, nLargeur, nHauteur, bProgressif)
{
    oBoite.style.display="none";
    // Positionnement
    var left = parseInt((screen.availWidth/2) - (nLargeur/2));
    var top = parseInt((screen.availHeight/2) - (nHauteur/2));
    if(left < 0 ) left=0;
    if(top < 0) top=0;
    oBoite.style.left=left + "px";
    oBoite.style.top=document.documentElement.scrollTop + top + "px";
    oBoite.style.width=nLargeur + "px";
    oBoite.style.height=nHauteur + "px";
    // Si image plus grande que fonds gris on agrandit le fond gris
    if((nHauteur + top) > document.getElementById("img_fond").height)
    {
        nHauteurFond=nHauteur + parseInt(oBoite.style.top);
        document.getElementById("img_fond").style.height=nHauteurFond + "px";
    }
    if(bProgressif == false )
    {
        oBoite.style.display="block";
        return;
    }
	
    // Affichage progressif
    oBoite.style.opacity =0;
    oBoite.style.filter = 'alpha(opacity=1)';
    oBoite.style.display="block";
    for( var i = 0; i <= 100; i++ )
        setTimeout( 'setOpacity("img_boite",' + (i / 10) + ')' , 20 * i );
}

// Fond pour modal dialog 
function afficher_fond_gris(bMode)
{
    oFondGris=document.getElementById("fond_gris");
    if(!oFondGris)
    {
        oFondGris=document.createElement("div");
        oFondGris.setAttribute("id","fond_gris");
        document.body.appendChild(oFondGris);
    }
    oFondGris.style.display="none";
    if(bMode == false) return;
    oFondGris.style.position="absolute";
    oFondGris.style.opacity="0.4";
    oFondGris.style.filter = 'alpha(opacity="40")';
    oFondGris.style.top="0px";
    oFondGris.style.left="0px";
    oFondGris.style.width="100%";
    sHauteur=document.body.clientHeight + "px";
    sHtml='<img id="img_fond" src="' + imagesUrl + 'fond-gris.gif" width="100%" height="' + sHauteur + '">';
    oFondGris.innerHTML=sHtml;
    oFondGris.style.display="block";
    return;
}

function setOpacity( sIdBoite, value ) 
{
    oObj=document.getElementById(sIdBoite);
    oObj.style.opacity = value / 10;
    oObj.style.filter = 'alpha(opacity=' + value * 10 + ')';
}

//////////////////////////////////////////////////////////////////////////////////
// ECOUTE DOCS SONORES
/////////////////////////////////////////////////////////////////////////////////
var oLastFm;

function afficher_media(sIdObjet,sUrl,sType)
{
    $("div[rel='video']").html(sPatience).css('display','none');
		if(sUrl == "close") return;
		$('#'+sIdObjet).css('display','block');
		oObjet=document.getElementById(sIdObjet);
    if(sType == "real_audio")
    {
        sHtml='<embed src="'+ sUrl +'" width="200" height="36" loop="false" type="audio/x-pn-realaudio-plugin" controls="ControlPanel" autostart="true"></embed>';
    }
    if(sType == "last_fm")
    {
        //alert(sUrl);
        sElem=sUrl.split(';');
        sHtml='<embed id="lfmPlayer" height="221" width="300" align="middle" swliveconnect="true" name="lfmPlayer" allowfullscreen="true" allowscriptaccess="always" flashvars="lang=fr&lfmMode=playlist&FOD=true&resname='+ sElem[0] +'&restype=track&artist='+ sElem[1] +'&albumArt=&autostart=true" bgcolor="#fff" wmode="transparent" quality="high" menu="true" pluginspage="http://www.macromedia.com/go/getflashplayer" src="http://cdn.last.fm/webclient/s12n/s/5/lfmPlayer.swf" type="application/x-shockwave-flash"/>';
    }
    oObjet.innerHTML=sHtml;
}

//////////////////////////////////////////////////////////////////////////////////
// GOOGLE VIDEO
/////////////////////////////////////////////////////////////////////////////////

function chercher_videos(sId,sAuteur,sTitre)
{	
	$("div[rel='video']").html(sPatience).css('display','none');
	sUrl=baseUrl+'/opac/noticeajax/videomorceau?auteur='+sAuteur+'&titre='+sTitre;
	$('#'+sId).css('display','block');
	$.get(sUrl, function(data) { $('#'+sId).html(data); });

}

////////////////////////////////////////////////////////////////////////////////////
// Réservation de notice en ajax (pour comm sigb)
///////////////////////////////////////////////////////////////////////////////////
function reservationAjax(oImg,nIdBib,sIdOrigine, sCodeAnnexe)
{
	var sUrl=baseUrl+'/recherche/reservationajax?id_bib='+nIdBib+"&id_origine="+sIdOrigine+"&code_annexe="+sCodeAnnexe;
	var saveImg=$(oImg).attr('src');
	$(oImg).attr('src',imagesUrl+'patience.gif');
	$.getJSON(sUrl, function(data)	{
		$(oImg).attr('src',saveImg);

		if (data.indexOf('http') == 0)
			showPopWin(data, 500, 345, null);
		else 
			alert(data);
	});
}


var pickupImgCallback;
var pickupConfirmCallBack;
function reservationPickupAjax(oImg,nIdBib,sIdOrigine,sCodeAnnexe)
{
	var sUrl = baseUrl+'/recherche/reservation-pickup-ajax?id_bib='+nIdBib+"&id_origine="+sIdOrigine+"&code_annexe="+sCodeAnnexe;
	var saveImg = $(oImg).attr('src');
	pickupImgLoadingCallback = function() {
		$(oImg).attr('src', saveImg);
	};
	pickupConfirmCallBack = function(form) {
		reservationPickupAjaxCancel();
		var sCodeAnnexe = $(form).find('input:radio[name="code_annexe"]:checked').val();
		reservationAjax(oImg, nIdBib, sIdOrigine, sCodeAnnexe);
	};

	$(oImg).attr('src',imagesUrl+'patience.gif');
	showPopWin(sUrl, 500, 345, null);
}


function reservationPickupAjaxCancel() {
	pickupImgLoadingCallback();
	hidePopWin(false);
}


function reservationPickupAjaxConfirm(form) {
	pickupConfirmCallBack(form);
}
