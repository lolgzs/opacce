///////////////////////////////////////////////////////////////////////////////
// Retaille les images / flash .... des boîtes pour bien rentrer dans les
// divisions tout en gardant les proportions
///////////////////////////////////////////////////////////////////////////////
function resizeElement(element, maxw, keep_ratio) {
	var height = element.height();
	var width = element.width();

	if ((width > maxw) && (maxw > 0)){
		if (keep_ratio && (height>0)) {
			var newh = Math.round(height*(maxw/width));
			element.height(newh);
		}
		element.width(maxw);
	}
	element.css('display','inline');
}


function autoResizeTags(tags, keep_ratio){
	var children_selector = tags.join(',');

	$('.auto_resize').each(function(){
		var mywidth = $(this).width();
		var parent_width = $(this).parent('div').width();
		if (mywidth < parent_width)
			maxwidth = mywidth;
		else
			maxwidth = parent_width;

		$(this).find(children_selector).each(function(){
			resizeElement($(this), maxwidth, keep_ratio);
		});
		$(this).removeClass('auto_resize');
	});
}


// Active auto-resize pour tous les objets Flash, Images des elémenents
// avec la classe "auto_resize"
var resize_func = function(){
	autoResizeTags(new Array("embed", "object", "img"), true)}
if (typeof jQuery != "undefined") $(document).ready(resize_func);




//gestion de la navigation dans le calendrier
var ajaxify_calendars = function () {
	var month_link = $("a.calendar_title_month_clickable:first-child, a.calendar_title_month_clickable:last-child");
	month_link.click(function(event) {
		event.preventDefault();
		var url = $(this).attr('href');
		$(this).parents(".calendar").load(url, 
																			ajaxify_calendars);
	});

	$("form#calendar_select_categorie").change(function(event) {
		var url = $(this).attr('action');
		$(this).parents(".calendar").load(url, 
																			{'select_id_categorie':$(this).children('select').val(),
																			 'id_module':$(this).children('input').val()},
																			ajaxify_calendars);
	});
}




//Les liens qui référencent des sites externes doivent être ouverts dans un nouvel onglet
var setupAnchorsTarget = function() {
	var internalLink = new RegExp('/' + window.location.host + '/');
	$('a[href^="http://"]').each(function() {
		if (!internalLink.test($(this).attr('href')) && (undefined == this.onclick)  && (undefined == $(this).data('events') || undefined == $(this).data('events').click)) {
			if ($.browser.msie) { //Sinon IE n'envoie pas le HTTP REFERRER
				this.target = '_blank';
				return;
			}

			$(this).click(function(event) {
				event.preventDefault();
        event.stopPropagation();
        window.open(this.href, '_blank');
      });
		}
	});
}
