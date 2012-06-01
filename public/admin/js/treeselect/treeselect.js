(function($) {
		
$.widget("ui.treeselect", {
		options: {
				datas: []
		},


		_create: function(){
				var self=this;
				this.element.addClass('ui-widget ui-helper-reset');
				this.container = $("<div class='ui-treeselect ui-widget-content ui-corner-all'></div>").
						appendTo(this.element);

				this.searchInput = 
						$("<div class='ui-treeselect-search'>"+
								 "<span>Rechercher: </span><input type='textfield' size='50'></input>"+
							"</div>").
						appendTo(this.container).
						find('input').keyup(function(event){
										self._onSearchInputChange($(this).val());
								}).end();

				this.itemsTree = ($("<div class='ui-treeselect-items-tree' />")).
						appendTo(this.container);


				this.selectedItems = 
						$("<div class='ui-treeselect-selected-items'>"+
								 "<div class='ui-treeselect-selected-items-inner ui-widget-content ui-corner-all'>"+
										 "<ul></ul></div></div>").
						appendTo(this.container).
						find("ul");

				this._createDataTree();
				this._initialize();
		},


		_initialize: function(){
				this._initSelection();
				this._initAccordion();
				this._initSortable();
		},


		_destroy: function() {
				$.Widget.prototype.destroy.apply(this, arguments);
				this.container.remove(this.container.children());
		},


		_addCategoriesTo: function(categories, parent) {
				var self = this;

				if (categories == undefined) return;

				if (this.mkli == undefined)
						this.mkli = function(catOrItem, parent, type){
								return	$("<li class='ui-treeselect-"+type+"'>"+
														"<input type='checkbox' />" +
														"<a class='ui-state-default' href='#'>" +
																"<span class='ui-treeselect-icon' />"+
																catOrItem.label+
														"</a>" +
													"</li>").
										data("label", catOrItem.label).
										data("type", type).
										data("elid", catOrItem.id).
										appendTo(parent).
										children('a').hover(
												function (event) {
														self._onMouseEntersLI($(event.target).parent());
												},
												function (event) {
														self._onMouseExitsLI($(event.target).parent());
												}).end();
						};

				var self = this;
				$.each(categories, function(index, cat) {
						var cat_li = self.mkli(cat, parent, 'category');

						if ((cat.categories.length + cat.items.length) == 0) {
								cat_li.addClass('ui-treeselect-empty-category');
								return;
						}
								

						var cat_ul = $("<ul></ul>").appendTo(cat_li);
						self._addCategoriesTo(cat.categories, cat_ul);

						$.each(cat.items, function(index, item){
								self.mkli(item, cat_ul, 'item').
										children('a');
						});
				});
		},


		_onMouseEntersLI: function(li) {
				var anchor = li.children('a');

				anchor.data('on_mouse_enter_classes', anchor[0].className);
				anchor.
						addClass("ui-state-hover").
						removeClass("ui-state-default").
						removeClass("ui-state-highlight").
						removeClass("ui-state-active");

				if (li.parents('.ui-treeselect-selected-items').length == 0) return;

				var hint = 
						$('<div class="hint ui-widget ui-corner-all ui-state-active" style="display:none"/>').
						hide().
						appendTo(li);

				hint.delay(1000).fadeIn('fast');
				
				var sourceLI = this._findConnectedIn(li, this.itemsTree);

				this._getAnchorTreeFor(sourceLI).
						slice(0, -1).
						each(function(index, element){
								hint = $("<li>"+$(element).text()+"</li>").
										appendTo($("<ul></ul>")).
										appendTo(hint);
						});
		},


		_onMouseExitsLI: function(li) {
				var anchor = 	li.children('a');

				anchor.
						addClass(anchor.data('on_mouse_enter_classes')).
						removeData('on_mouse_enter_classes').
						removeClass("ui-state-hover");


				this.selectedItems.find('.hint').remove();
		},


		_createDataTree: function(){
				var self = this;
				$.each(this.options.datas, function(index, cat) {
						$("<h3><a href='#'>"+cat.label+"</a></h3>").
								appendTo(self.itemsTree);

						var ul = $("<div style='padding:0px' class='cat_"+cat.id+"'></div>").
								appendTo(self.itemsTree).
								append("<ul></ul>").
								children()[0];
						
						self._addCategoriesTo(cat.categories, ul);
				});
		},

				
		_initAccordion: function(){		
				this.itemsTree.accordion();
		},


		_initSortable: function(){
				var self=this;
				this.selectedItems.
						addClass("sortable").
						sortable({
								cursor:'crosshair', 
								opacity: 0.8,
								start: function(event, ui) {$(this).find('.hint').remove();}
						});

				this._adaptSelectedItemSize();
		},


		_adaptSelectedItemSize: function(){
				var inner = this.selectedItems.closest('.ui-treeselect-selected-items-inner');
				var outer = inner.parent();
				inner.height(this.itemsTree.height() - (outer.height() - inner.height()));
				inner.css('min-height', inner.height() + 'px');
		},

		
		_initSelection: function(){
				var self=this;

				this.itemsTree.find('li').each(
						function(index, li){
								$(li).data('sid', index);});
				
				this.itemsTree.change(
						function(event){
								self._onSelectionChange($(event.target).parent());
								event.stopPropagation();});

				this.itemsTree.find('li>ul').css("display","none");
				this.itemsTree.click(
						function(event){
								var $target = $(event.target);
								if ($target.is("a>*")) $target = $target.parent();
								if ($target.is("a")) {
										event.stopPropagation();
										event.preventDefault();
										$target.siblings('ul').find('ul').hide();
										$target.siblings('ul').toggle('fast', function(){
												self._turnHighlightOff();
										});}})
		},


		_findConnectedIn: function(li, items) {
				var sid = li.data('sid');
			  return items.find('li').filter(function(index) { 
					return $(this).data('sid') == sid; 
				});
		},


		_deselect: function(li) {
				this._findConnectedIn(li, this.selectedItems).
						hide('fast', function(){
								$(this).remove()});

				li.children('input').removeAttr('checked');
				li.find("li input").removeAttr('disabled');
				li.children('a').removeClass('ui-state-active');
		},


		_setInactive: function(li) {
				li.children('a').removeClass('ui-state-active');

				parent = li.parent().parent();
				if (parent.length == 0) return;

				if (parent.find('li>input:checked').length==0) {
						this._setInactive(parent);
				}
		},


		_select: function(li) {
				var self = this;

				li.children('input').attr('checked', 'checked');

				var selectedLI = $("<li>"+
															"<a href='#'><span class='ui-treeselect-icon' />" + 
															 $(li).data("label") + 
														 "</a></li>").
						appendTo(self.selectedItems).
						data(li.data()).
						children('a').
						addClass('ui-state-default').
						addClass(li[0].className).
						hover(
								function (event) {
										self._onMouseEntersLI($(event.target).parent());
								},
								function (event) {
										self._onMouseExitsLI($(event.target).parent());
								}
						).end().
						click(
								function(event){
										event.preventDefault();
										if ($(event.target).is('a'))
												self._highlightSelected($(event.target).parent());
								});

				$("<div class='remove_button'/>").
						appendTo(selectedLI).
						click(function(event){
								self._deselect(
										self._findConnectedIn($(event.target).parent(), 
																					self.itemsTree));
						});
						
				li.parents('li').andSelf().children('a').addClass('ui-state-active');

				this._disableChildren(li);
		},


		_disableChildren: function(li) {
				var self = this;
				li.find("li>input").each( 
						function(index, checkbox) {
								self._deselect($(checkbox).parent());
								$(checkbox).
										attr('disabled', true).
										removeAttr('checked'); });
		},


		_onSelectionChange: function(li) {
				this._turnHighlightOff();
				if (li.children('input').is(':checked')) {
						this._select(li);
				}	else {
						this._deselect(li);
						this._setInactive(li);
				}
				
				this.selectedItems.sortable('refresh');
		},

		_turnHighlightOff: function() {
				this.container.find('.ui-state-highlight').
						removeClass("ui-state-highlight").
						filter('li a, h3').
						addClass("ui-state-default");
		},


		_hideAll: function() {
				this._turnHighlightOff();
				this.itemsTree.find('ul ul:visible').hide();
		},


		_getAnchorTreeFor: function(li) {
				var parents = li.parentsUntil('.ui-treeselect-items-tree, .ui-treeselect-selected-items');
				var litree = parents.andSelf().children('a');
				return litree.add(parents.last().prev());
		},


		_highlightItems: function(lis) {
				this._hideAll();
				lis.parents("ul").show();
				
				var self = this;
				lis.each(function(index, li) {
						self._getAnchorTreeFor($(li)).
								addClass("ui-state-highlight").
								removeClass("ui-state-default");
				});
		},


		_highlightSelected: function(li) {
				var itemToHighlight = this._findConnectedIn(li, this.itemsTree);
				
				this._highlightItems(itemToHighlight.add(li));

				itemToHighlight.
						parents(".ui-accordion-content").prev().click();
		},


		_onSearchInputChange: function(searchText) {
				if (searchText=="") {
						this._hideAll();
						return;
				}

				var re = new RegExp(searchText, 'i');
				var matches = this.itemsTree.find('li, h3').filter(function() {
						return re.test($(this).text());
				});
				this._highlightItems(matches);
		},


		_selectByIdAndType: function(ids, type) {
				var self = this;
				jQuery.each(ids, function(index, id) { 
					self.itemsTree.find('li').each(function(index, li) {
						var listitem = $(li);
						if ((listitem.data('elid') == id) && (listitem.data('type') == type))
							self._select($(li));
					});
				});
		},


		_getSelectedDataByType: function(type) {
				var datas = [];
				this.selectedItems.find('li').each(function(index,li){
					if ($(li).data('type') == type) {
						datas.push({"id": $(li).data('elid'), 
												"label": $(li).data('label')});
					}
				});
				return datas;
		},

		selectItems: function(ids) {
				this._selectByIdAndType(ids, 'item');
		},

		selectCategories: function(ids) {
				this._selectByIdAndType(ids, 'category');
		},


		readSelection: function(callback) {
				callback(
						this._getSelectedDataByType('item'),
						this._getSelectedDataByType('category'));
		},

		toggleVisibility: function(visible) {
				if (visible) {
						var self = this;
						this.element.show('fast',
															function() {
																	self.itemsTree.children('div').height(200);
																	self._adaptSelectedItemSize();});
				} else {
						this.element.hide('fast');
				}
		}
})})(jQuery);