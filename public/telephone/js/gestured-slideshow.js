/**
 * original work from Osvaldas Valutis, www.osvaldas.info
 */
$(document).ready(function() {
	(function($) {
		$.fn.gesturedSlideshow = function() {
			var stack = this,
			moveOrigin,
			hasMoved,
			film,
			filmPos,
			filmWidth,
			maxOffset,
			containerWidth,

			init = function() {
				var itemWidth = 0;
				var width = 0;
				stack.find('ul').each(function() {
					$(this).find('li').each(function() { 
						itemWidth = $(this).outerWidth()
							+ parseInt($(this).css('margin-left')) 
							+ parseInt($(this).css('margin-right'));
						width += itemWidth;
					});					
					$(this).width(width);
				});
				
				filmWidth = width;
				maxOffset = filmWidth - itemWidth;

				stack
					.bind('touchstart',	start)
					.bind('touchmove', move)
					.bind('touchend', end);
			},

			start = function(e) {
				containerWidth = $(this).width(); 
				film = $(this).find('ul'); 
				impulse = e.originalEvent.touches[0].pageX;
				e.preventDefault();
				hasMoved = false;

				if (containerWidth > filmWidth)
					return false;

				moveOrigin = impulse;
				filmPos = parseInt(film.css('left'));
				film.stop(true, true);
			},

			move = function(e) {
				hasMoved = true;
				if (containerWidth > filmWidth)
					return false;

				impulse = e.originalEvent.touches[0].pageX; 
				destination = filmPos + (impulse - moveOrigin);
				if (0 < destination)
					destination = 0;

				if (destination < -maxOffset) 
					destination = -maxOffset;

				film
					.stop(true,true)
					.css({'left' : destination + 'px'});
			},

			end = function(e) {
				if (!hasMoved) {
					anchor = $(e.target).parent();
					$(location).attr('href', anchor.attr('href'));
					return;
				}
					
				if (containerWidth > filmWidth) 
					return false;
				film.stop(true, true);
			};

			init();
		};
	})(jQuery);
});