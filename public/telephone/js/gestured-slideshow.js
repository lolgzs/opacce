/**
 * original work from Osvaldas Valutis, www.osvaldas.info
 */
$(document).ready(function() {
	(function($) {
		$.fn.gesturedSlideshow = function() {
			var stack = this,
			inMotion,
			moveOrigin,
			hasMoved,
			ratio,
			film,
			filmPos,
			filmWidth,
			containerWidth,

			init = function() {
				stack.find('ul').each(function() {
					var width = 0;
					$(this).find('li').each( function() { 
						width += $(this).outerWidth()
							+ parseInt($(this).css('margin-left')) 
							+ parseInt($(this).css('margin-right'));});
					$(this).width(width);
				});

				stack
					.bind('touchstart',	start)
					.bind('touchmove', move)
					.bind('touchend', end);
			},

			start = function(e) {
				containerWidth = $(this).width(); 
				film = $(this).find('ul'); 
				filmWidth = film.width();

				impulse = e.originalEvent.touches[0].pageX; 
				e.preventDefault();

				if (containerWidth > filmWidth)
					return false;

				hasMoved = false;
				moveOrigin = impulse;
				filmPos = parseInt(film.css('left'));
				ratio	= filmWidth / containerWidth;
				film.stop(true, true);
			},

			move = function(e) {
				hasMoved = true;
				if (containerWidth > filmWidth)
					return false;

				impulse = e.originalEvent.touches[0].pageX; 
				destination = filmPos - ((impulse - moveOrigin) * ratio);
				if ((0 < destination) || (0 > containerWidth + destination) )
					return false;

				console.info('Moving to : ' + destination);
				film
					.stop(true,true)
					.css({'left' : destination + 'px'});
			},

			end = function(e) {
				if (hasMoved)
					e.preventDefault();
				if (containerWidth > filmWidth) 
					return false;
				film.stop(true, true);
			};

			init();
		};
	})(jQuery);
});