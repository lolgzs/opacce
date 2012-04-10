var valuesCache = {};

var i18nFilterTimer;

$(document).ready(function(){
	$('a.content_triggerer').click(i18nObserveClick);
	$('.i18n_field').focus(i18nObserveFocus);
	$('.i18n_field').blur(i18nObserveBlur);
	$('.i18n_field').keyup(i18nObserveKeyup);
	$('#i18n_filter').keyup(i18nObserveFilter);
	$($('a.content_triggerer')[0]).click();
});

function i18nObserveClick(event) {
	event.preventDefault();
	i18nShowContent($('#' + $(event.currentTarget).attr('rel')));
	i18nHighlightCurrentLang($(event.currentTarget));
}

function i18nShowContent(element) {
	$('div.i18n_content').hide();
	element.show();
}

function i18nHighlightCurrentLang(element) {
	$('a.content_triggerer').removeClass('selected');
	element.addClass('selected');

}

function i18nObserveFocus(event) {
	elem = $(event.currentTarget);
	valuesCache[elem.attr('name')] = elem.val();

}

function i18nObserveKeyup(event) {
	elem = $(event.currentTarget);
	if (elem.val() != valuesCache[elem.attr('name')]) {
		elem.css('border', '1px solid orange');
	}
}

function i18nObserveBlur(event) {
	elem = $(event.currentTarget);

	if (elem.val() != valuesCache[elem.attr('name')]) {
		parts = elem.attr('name').split('_');
		$.get(baseUrl + '/admin/i18n/update', {
			'lang': parts[0],
			'field': parts[1],
			'value': elem.val()
			});

	}

	elem.css('border', '1px solid #C8C8C8');

}

function i18nObserveFilter(event) {
	i18nSetFilterDelay(200);

}

function i18nSetFilterDelay(delay) {
	if (i18nFilterTimer) {
		clearTimeout(i18nFilterTimer);
	}

	i18nFilterTimer = setTimeout(i18nExecuteFilter, delay);
}

function i18nExecuteFilter() {
	if ('' != $('#i18n_filter').val()) {
		$('.i18n_field').parents('tr').hide();

		var re = new RegExp($('#i18n_filter').val(), 'i');

		$('.i18n_field').filter(function(index) {
			return re.test($(this).parents('tr').children('td.i18n_label').text());
		}).parents('tr').show();

	} else {
		$('.i18n_field').parents('tr').show();

	}

}