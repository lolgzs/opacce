function requestRss(varIdRSS) {
	if (document.getElementById("popup_rss_"+varIdRSS) !== null) return;
	var sUrl = baseUrl + '/opac/rss/afficherrss/id_rss/' + varIdRSS;
	var show_anchor = $("#rss_a_"+varIdRSS);

	$("<div></div>").
				appendTo(show_anchor.parent()).
				css("display", "none").
				attr("id", "popup_rss_"+varIdRSS).
				load(sUrl,
						 function(){
								 $(this).show('slow');
								 show_anchor.toggle();
						 });
}

function loadRssByContentName(name, profil, module) {
	parts	= name.split('_');
	url		= baseUrl + '/opac/rss/view-raw-rss/id_rss/' + parts[parts.length - 1] + '/id_profil/' + profil + '/id_module/' + module;
	$(name).load(url);
}

function requestRssbyUrl(varUrlRSS) {
	sUrl= baseUrl + '/opac/rss/afficherrss?url_rss=' + escape(varUrlRSS);
	sId=insertRSSDiv("");
	$('#'+sId).load(sUrl);
}


function closeRSSDiv(idrss) {
	$("#popup_rss_"+idrss).
				hide('slow', function(){
						$(this).remove();
						$('#rss_a_'+idrss).fadeIn();
				});
}