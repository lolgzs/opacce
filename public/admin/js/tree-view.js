var treeViewFilterTimer;

$(document).ready(function(){
	$('a.containerTriggerer').click(treeViewObserveContainer);
	$('input.treeViewSearch').keyup(treeViewObserveSearch);
	$('div.treeViewSearchStatus a').click(treeViewObserveSearchStatus);
	$('div.treeViewSearchStatus a:first').click();
	$('.tree').accordion({'autoHeight':false});
	treeViewUpdateItemCount();
	treeViewOpenSelected();
});


function treeViewUpdateItemCount() {
	$('ul.root').children('li.categorie')
							.each(function(i, container) {treeViewCountItems(container);});
}

function treeViewCountItems(container) {
	// items non masqués directement sous ce conteneur
	var itemsCount = $(container).children('ul')
																.children('li.item')
																.filter(function(i){
																	return !$(this).hasClass('masked');
																})
																.length;

	// ajout des items sous des sous-conteneurs
	$(container).children('ul')
							.children('li.categorie')
							.each(function (i, subContainer) {
								itemsCount = itemsCount + treeViewCountItems(subContainer);
							})

	$(container).children('div.label')
							.find('span.count')
							.empty()
							.append('(' + itemsCount + ')');

	return itemsCount;

}

function treeViewObserveContainer(event) {
	event.preventDefault();
	$('#' + $(event.currentTarget).attr('rel')).toggle();
}

function treeViewObserveSearchStatus(event) {
	event.preventDefault();
	$('div.treeViewSearchStatus a').css('font-weight', 'normal');
	$(event.currentTarget).css('font-weight', 'bold');

	var status = $(event.currentTarget).attr('rel');

	$('li.item').show()
							.removeClass('masked');

	if ('status-all' == status) {
		treeViewUpdateItemCount();
		return;
	}

	$('li.item').hide()
							.addClass('masked');

	$('li.' + status).show()
										.removeClass('masked');

	treeViewUpdateItemCount();

}

function treeViewObserveSearch(event) {
	treeViewSetFilterDelay(200);
}

function treeViewSetFilterDelay(delay) {
	if (treeViewFilterTimer) {
		clearTimeout(treeViewFilterTimer);
	}

	treeViewFilterTimer = setTimeout(treeViewExecuteFilter, delay);
}

function treeViewExecuteFilter() {
	var search = $('input.treeViewSearch').val();

	treeViewRemoveHighlights();

	if ('' == search) {
		return;
	}

	var re = new RegExp(search, 'i');
	var matches = $('.tree li').find('div.label a, div.item-label').filter(function() {
			return re.test($(this).text());
	});

	treeViewHighlight(matches);
}

function treeViewRemoveHighlights() {
	$('.tree li div.label a, .tree li div.item-label').css('font-weight', 'normal');
	$('.treeView h3 a[href="#"]').css('font-weight', 'normal');
}

function treeViewHighlight(matches) {
	matches.each(function (i, item) {
		$(item).css('font-weight', 'bold');
		$(item).parentsUntil('ul.root')
					.children('div.label')
					.children('a')
					.css('font-weight', 'bold');

		$(item).parentsUntil('div.tree')
					.prev()
					.children('a')
					.css('font-weight', 'bold');
	});
}

function treeViewOpenSelected() {
	if (0 < treeViewSelectedCategory) {
		$('a[href$="admin/cms/catedit/id/' + treeViewSelectedCategory + '"]')
			.parentsUntil('ul.root')
			.children('div.label')
			.children('a')
			.each(function (i, item) {$('#' + $(item).attr('rel')).show()});

		$('a[href$="admin/cms/catedit/id/' + treeViewSelectedCategory + '"]')
			.parentsUntil('div.tree')
			.prev()
			.children('a')
			.click();
	}
}