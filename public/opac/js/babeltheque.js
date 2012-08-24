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
		$(this).prepend('<div id="BW_'+info+'"></div>');
	});
}


var insertBabelthequeISBNTag = function() {
	if (!$('input#BW_id_isbn'))
		$('body').append('<input type="hidden" id="BW_id_isbn" value="'+isbn+'"\>');
}


var blocNoticeAfterLoad = function (info, isbn, target) {
	var callback = $(window).attr(info + 'NoticeAfterLoad');
	if (undefined == callback)
		return;

	callback(target);
	loadBabelthequeScript();
}


var tagsNoticeAfterLoad = function(target) {
	target.babelthequeTag('etiquettes');
}


var similairesNoticeAfterLoad = function(target) {
	target.babelthequeTag('suggestions');
}


var avisNoticeAfterLoad = function(target) {
	$('<tr><td><ul class="notice_info"><li><div id="BW_notes"></div></li></ul></td></tr>')
	.insertAfter(target.find('table tr:nth-child(2)'));

	$('<tr><td><div id="BW_critiques"></div></td></tr>').appendTo(target.find('table'));

	$('<tr><td><div id="BW_critiques_pro"></div></td></tr>').appendTo(target.find('table'));
}


var videosNoticeAfterLoad = function(target) {
	target.babelthequeTag('videos');
}


var resumeNoticeAfterLoad = function(target) {
	target.babelthequeTag('citations');
}
