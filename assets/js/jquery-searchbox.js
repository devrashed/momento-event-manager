(function($) {
	
		$.fn.searchBox = function(opts) {


		var settings = $.extend({}, $.fn.searchBox.defaults, opts);
		
		var init = function (obj) {

			var self = $(obj),
				parent = self.closest('div,tr'),
				searchWord = ''; 
			

			self.before('<input type="text" class="refineText formTextbox" />');
			var refineText = parent.find('.refineText');
			if (settings.mode === MODE.NORMAL) {
				refineText.attr('readonly', 'readonly');
			}
			

			var selectedOption = self.find('option:selected');
			if(selectedOption){
				refineText.val(selectedOption.text());
				if (selectedOption.val() === '') {
					if (settings.mode === MODE.TAG) {
						refineText.val("");
					}
				}
			}


			var visibleTarget =self.find('option').map(function(i, e) {
				return '<li data-selected="off" data-searchval="' + $(e).val() + '"><span>' + $(e).text() + '</span></li>';
			}).get();
			self.after($('<ul class="searchBoxElement"></ul>').hide());

			var refineTextWidth = (settings.elementWidth) ? settings.elementWidth : self.width();
			refineText.css('width', refineTextWidth);
			parent.find('.searchBoxElement').css('width', refineTextWidth);

			
			self.hide();

			
			var changeSearchBoxElement = function() {
				if (searchWord !== '') {
					var matcher = new RegExp(searchWord.replace(/\\/g, '\\\\'), "i");
					var filterTarget = $(visibleTarget.join()); 
					filterTarget = filterTarget.filter(function(){
						return $(this).text().match(matcher);
					});
					parent.find('.searchBoxElement').empty();
					parent.find('.searchBoxElement').html(filterTarget);
					parent.find('.searchBoxElement').show();
				} else {
					parent.find('.searchBoxElement').empty();
					parent.find('.searchBoxElement').html(visibleTarget.slice(0, settings.optionMaxSize).join(''));
					parent.find('.searchBoxElement').show();
				}
				
				
				var selectedOption = self.find('option:selected');
				if(selectedOption){
					parent.find('.searchBoxElement').find('li').removeClass('selected');
					parent.find('.searchBoxElement').find('li[data-searchval="' + selectedOption.val() + '"]').addClass('selected');
				}
				
				
				parent.find('.searchBoxElement').find('li').click(function(e){
					e.preventDefault();
					
					var li = $(this),
						searchval = li.data('searchval');
					self.val(searchval).change();
					parent.find('li').attr('data-selected', 'off');
					li.attr('data-selected', 'on');
				});

			};


			refineText.keyup(function(e){
				searchWord = $(this).val();
				changeSearchBoxElement();
			});


			self.change(function(){
				 
				var selectedOption = $(this).find('option:selected');
				searchWord = selectedOption.text();
				refineText.val(selectedOption.text());

				if (settings.selectCallback) {
					settings.selectCallback({
						selectVal: selectedOption.attr('value'),
						selectLabel: selectedOption.text()
					});
				}
			});

		
			refineText.click(function(e) {
				e.preventDefault();

				if (settings.mode === MODE.NORMAL) {
					searchWord = '';
				} else if (settings.mode === MODE.INPUT) {
					refineText.val('');
					searchWord = '';
				} else if (settings.mode === MODE.TAG) {
					var selectedOption = self.find('option:selected');
					if (selectedOption.val() === '') {
						refineText.val('');
						searchWord = '';
					}
				}

				parent.find('.searchBoxElement').hide();
				changeSearchBoxElement();
				
			});
			

			$(document).click(function(e){
				if($(e.target).hasClass('refineText')){
					return;
				}
				parent.find('.searchBoxElement').hide();
				if (settings.mode !== MODE.TAG) {
					var selectedOption = self.find('option:selected');
					searchWord = selectedOption.text();
					refineText.val(selectedOption.text());
				}
			});

		}

		$(this).each(function (){
			init(this);
		});

		return this;
	}
	
	var MODE = {
		NORMAL: 0, 
		INPUT: 1, 
		TAG: 2 
	};

	$.fn.searchBox.defaults = {
		selectCallback: null, 
		elementWidth: null, 
		optionMaxSize: 100, 
		mode: MODE.INPUT 
	};

})(jQuery);
