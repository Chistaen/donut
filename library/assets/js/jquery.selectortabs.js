/*!
 * SelectorTabs for CardTabs jQuery plugin
 * https://github.com/blekerfeld/cardtabs
 *
 * Released under the MIT license
 */

jQuery.fn.selectorTabs = function(options){

	// Getting the initial selector
	var selector = $(this).parents().map(function() { return this.tagName; }).get().reverse().concat([this.nodeName]).join(">");var id = $(this).attr("id");if (id) { selector += "#"+ id;}var classNames = $(this).attr("class");if (classNames) {selector += "." + $.trim(classNames).replace(/\s/gi, ".");}
	var selectorID = 'ts-' + Math.random().toString(36).substr(2, 5);
	var settings = $.extend({
        theme: '',
        afterclick: function(){
        	return;
        }
     }, options );

	// Initializing
	var htmlInner = $(this).html();
	var stack = $('<div />').addClass('card-tabs-bar selector-tabs-bar selectorTabs ' + selectorID);

	var activeValue = $(selector + " option:selected").attr('value');

	$(selector).addClass('selector-tabs-orginial');

	$(selector).children('option').each(function(){

		stack.append($('<a />').addClass('ts-option').attr('href', 'javascript:void();').attr('data-value', $(this).val()).append($(this).html()));
	});

	// Select the active value


	$(selector).hide().after(stack);
	$('.' + selectorID + " a[data-value='" + activeValue + "']").addClass('active');

	$('.' + selectorID +' .ts-option').click(function(){
		$(selector).val($(this).attr('data-value'));
		$('.' + selectorID + " a.active").removeClass('active');		
		$('.' + selectorID + " a[data-value='" + $(this).attr('data-value') + "']").addClass('active');		
		settings.afterclick();
	});

	return this;
};
