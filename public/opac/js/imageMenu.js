/**************************************************************

	Script	: Image Menu
	Version	: 2.2
	Authors	: Samuel Birch
	Desc	: 
	Licence	: Open Source MIT Licence

**************************************************************/

// Afficher les sous_menus
function ImageMenu_afficher(sMode,nIdMenu)
{
	if(sMode=="on") $("vmenu" + nIdMenu).style.display="block";
	if(sMode=="off") $("vmenu" + nIdMenu).style.display="none";
}

function ma_fonction_open(e,nIndex)
{
	// Se declenche quand on clique l'image, pas utile pour l'instant mais si on le retire ca marche plus
}

var ImageMenu = new Class({
	
	getOptions: function(){
		return {
			onOpen: false,
			onClose: Class.empty,
			openWidth: 200,
			transition: Fx.Transitions.quadOut,
			duration: 400,
			open: null,
			border: 0
		};
	},

	initialize: function(elements, options)
	{
		this.setOptions(this.getOptions(), options);
		
		this.elements = $$(elements);
		
		this.widths = {};
		this.widths.closed = this.elements[0].getStyle('width').toInt();
		this.widths.openSelected = this.options.openWidth;
		this.widths.openOthers = Math.round(((this.widths.closed*this.elements.length) - (this.widths.openSelected+this.options.border)) / (this.elements.length-1))
		
		
		this.fx = new Fx.Elements(this.elements, {wait: false, duration: this.options.duration, transition: this.options.transition});
		
		this.elements.each(function(el,i){
			el.addEvent('mouseenter', function(e){
				new Event(e).stop();
				this.reset(i);
				ImageMenu_afficher("on",i);
			}.bind(this));
			
			el.addEvent('mouseleave', function(e){
				new Event(e).stop();
				this.reset(this.options.open);
				ImageMenu_afficher("off",i);
			}.bind(this));
			
			var obj = this;
			
			el.addEvent('click', function(e){

				if(obj.options.onOpen){
					new Event(e).stop();
					if(obj.options.open == i){
						obj.options.open = null;
						obj.options.onClose(this.href, i);
					}else{
						obj.options.open = i;
						obj.options.onOpen(this.href, i); 
					}		
				}			
			})
			
		}.bind(this));
		
		if(this.options.open){
			if($type(this.options.open) == 'number'){
				this.reset(this.options.open);
			}else{
				this.elements.each(function(el,i){
					if(el.id == this.options.open){
						this.reset(i);
					}
				},this);
			}
		}
		
	},
	
	reset: function(num){
		if($type(num) == 'number'){
			var width = this.widths.openOthers;
			if(num+1 == this.elements.length){
				width += this.options.border;
			}
		}else{
			var width = this.widths.closed;
		}
		
		var obj = {};
		this.elements.each(function(el,i){
			var w = width;
			if(i == this.elements.length-1){
				w = width+5
			}
			obj[i] = {'width': w};
		}.bind(this));
		
		if($type(num) == 'number'){
			obj[num] = {'width': this.widths.openSelected};
		}
				
		this.fx.start(obj);
	}
	
});

ImageMenu.implement(new Options);
ImageMenu.implement(new Events);

window.addEvent('domready', function(){var myMenu = new ImageMenu($$('#imageMenu a'),{openWidth:240, border:2, onOpen:function(e,i){ma_fonction_open(e,i);}});});