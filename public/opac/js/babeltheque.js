(function() {
	var getJSQueryString = function(script) {
		var
		  scripts = document.getElementsByTagName('script'),
		  i, 
		  curScript;

		for (i = 0; i < scripts.length; ++i) {
			curScript = scripts[i];

			if (curScript.src.match(script)) {
				return  (curScript.src.match(/\?(.*)/) || [undefined])[1];
			}
		}
	}


	var getJSParams = function(script) {
		var 
		  qs = getJSQueryString(script),
		  search = /([^&=]+)=?([^&]*)/g,
		  urlParams = {};

		while (match = search.exec(qs))
      urlParams[match[1]] = match[2];

		return urlParams;
	}


	var getJSParamNamed = function (script, name) {
		return  getJSParams(script)[name];
	}


	var bwid = getJSParamNamed('babeltheque.js', 'bwid');

	window.loadBabelthequeScript = function() {
		insertBabelthequeISBNTag();
		$.getScript('http://www.babeltheque.com/bw_' + bwid +'.js');
	}
})();


$.fn.babelthequeTag=function(info) {
	return this.each(function() {
		$(this).append('<div id="BW_'+info+'"></div>');
	});
}


var insertBabelthequeISBNTag = function() {
	if (!$('input#BW_id_isbn'))
		$('body').append('<input type="hidden" id="BW_id_isbn" value="'+isbn+'"\>');
}


var blocNoticeAfterLoad = function (info, isbn, target) {
	if ("" == isbn)
		return;

	var callback = $(window).attr(info + 'NoticeAfterLoad');
	if (undefined == callback)
		return;

	callback(target);
	loadBabelthequeScript();
}


var tagsNoticeAfterLoad = function(target) {
	target.prepend('<div id="BW_etiquettes"></div>');
}


var avisNoticeAfterLoad = function(target) {
	$('<li class="notes_avis_babeltheque"><span id="BW_notes"></span>Babelthèque (<span id="BW_critiques"></span> évaluations, <span id="BW_critiques_pro"></span> critiques pro.)</li>')
	.insertAfter(target.find('table tr:nth-child(2) ul li:last-child'));
}


var videosNoticeAfterLoad = function(target) {
	target.find('td:contains("Aucune vidéo")').remove();
	target.prepend('<div id="BW_videos"></div>');
}


var resumeNoticeAfterLoad = function(target) {
	target.find('td:contains("Aucune information")').remove();
	target.babelthequeTag('citations');
}


var babelthequeNoticeAfterLoad = function(target) {
	target.empty().babelthequeTag('suggestions');
}
