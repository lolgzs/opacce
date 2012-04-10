//////////////////////////////////////////////////////////////////////////////////////
// OPAC3 : Menus
/////////////////////////////////////////////////////////////////////////////////////

var menuWillOpen = null;
var menuTimer = null;
var menuClose = false;

function afficher_sous_menu(anchor)
{
		var li=$(anchor).parent('li');
		if (li.next().children().first().is('ul')) {
				li.next().toggle('fast');
				return;
		}

		li.children('ul').first().toggle('fast'); //compatibilité IE7
}

function menu_horizontal_mouse_over(node) {
	//$(node).parent().children(".over").removeClass("over").hide();
	var jnode = $(node);
	jnode.addClass('over');
	jnode.children("ul").css('min-width', jnode.width()+'px');
	menuWillOpen = node;
	menu_refresh_timer(200);
}

function menu_horizontal_mouse_out(node) {
	$(node).removeClass('over');
	menuClose = true;
	menuWillOpen = null;
	menu_refresh_timer(400);
}

function menu_execute_display() {
	if (menuClose) {
		$('#menu_horizontal').children('ul').children().each(function(i, node) {
			$(node).children('ul').hide();
		});

	}

	if (menuWillOpen) {
		if (0 < $(menuWillOpen).children("ul").length) {
			$(menuWillOpen).children("ul").show();
			var maxRight = $('div#header').offset().left + $('div#header').width();
			var offset = $(menuWillOpen).children("ul").offset()
			var actualRight = offset.left + $(menuWillOpen).children("ul").width();
			
			if (actualRight > maxRight) {
				var shift = actualRight - maxRight;
				$(menuWillOpen).children("ul").offset({'top':offset.top, 
																							 'left':offset.left-shift});
			}
		}

		menuWillOpen = null;

	}

}

function menu_refresh_timer(delay) {
	if (menuTimer) {
		clearTimeout(menuTimer);

	}

	menuTimer = setTimeout('menu_execute_display()', delay);

}