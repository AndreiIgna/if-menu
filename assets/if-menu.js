jQuery(function($) {


	// Show or hide conditions section
	$('body').on('change', '.menu-item-if-menu-enable', function() {
		$( this ).closest( '.if-menu-enable' ).next().toggle( $( this ).prop( 'checked' ) );

		if ( ! $( this ).prop( 'checked' ) ) {
			var firstCondition = $( this ).closest( '.if-menu-enable' ).next().find('p:first');
			firstCondition.find('.menu-item-if-menu-enable-next').val('false');
			firstCondition.nextAll().remove();
		}
	});


	// Show or hide conditions section for multiple rules
	$('body').on( 'change', '.menu-item-if-menu-enable-next', function() {
		var elCondition = $( this ).closest( '.if-menu-condition' );

		if ($(this).val() === 'false') {
			elCondition.nextAll().remove();
		} else if (!elCondition.next().length) {
			var newCondition = elCondition.clone().appendTo(elCondition.parent());
			newCondition.find('select').removeAttr('data-val').find('option:selected').removeAttr('selected');
			newCondition.find('.menu-item-if-menu-options, .select2').remove();
		}
	});


	// Check if menu extra fields are actually displayed
	var countMenuItems = $('#menu-to-edit li.menu-item').length;
	var countMenuItemsWithIfMenu = $('#menu-to-edit li.menu-item .if-menu-enable').length;
	if (countMenuItems === countMenuItemsWithIfMenu / 2) {
		$('<div class="notice notice-warning is-dismissible if-menu-notice"><p>' + IfMenu.duplicateErrorMessage + '</p></div>').insertAfter('.wp-header-end');
	} else if (countMenuItems !== countMenuItemsWithIfMenu) {
		$('<div class="notice error is-dismissible if-menu-notice"><p>' + IfMenu.conflictErrorMessage + '</p></div>').insertAfter('.wp-header-end');
	}


	// Store current value in data-val attribute (used for CSS styling)
	$('body').on('change', '.menu-item-if-menu-condition-type', function() {
		$(this).attr('data-val', $(this).val());
	});


	// Display multiple options
	$('.menu-item-if-menu-options').select2();
	$('body').on('change', '.menu-item-if-menu-condition', function() {
		var options = $(this).find('option:selected').data('options'),
			elCondition = $(this).closest('.if-menu-condition');

		elCondition.find('.menu-item-if-menu-options').select2('destroy').remove();

		if (options && !!IfMenu.plan && IfMenu.plan.plan === 'premium') {
			$('<select class="menu-item-if-menu-options" name="menu-item-if-menu-options[' + elCondition.data('menu-item-id') + '][' + elCondition.index() + '][]" style="width: 305px" multiple></select>')
				.appendTo(elCondition)
				.select2({data: $.map(options, function(value, index) {
					return {
						id:		index,
						text:	value
					};
				})});
		} else if (options && (!IfMenu.plan || IfMenu.plan.plan !== 'premium')) {
			$(this).find(':selected').removeAttr('selected');
			$(this).find(':first').attr('selected', true);
			$('.if-menu-dialog-premium').dialog({
				dialogClass:	'if-menu-dialog',
				draggable:		false,
				modal:			true,
				width:			450
			});
		}
	});


	// Store current value in data-val attribute (used for CSS styling)
	$('.if-menu-dialog-btn').click(function() {
		if ($(this).data('action') === 'get-premium') {
			window.onbeforeunload = function() {};
		}

		$(this).closest('.ui-dialog-content').dialog('close');
	});
	
	// Remove unselected if-menu rules from getting submitted to PHP/processed. If there are a lot of menu items and some of them don't have any 
	// settings atteched to them, PHP may throw an error saying that the max_input_vars has been exceeded or completely disregard if-menu rules 
	// for some menu items since we could post over 1000 inputs with most of them being empty.
	$('form#update-nav-menu').on('submit', function(event){
		var $menu_visible = $( '[name^="menu-item-if-menu-enable"], [name^="menu-item-if-menu-condition-type"], [name^="menu-item-if-menu-condition"], [name^="menu-item-if-menu-options"]' );
		$.each( $menu_visible, function( index, element ) {
			var $input = $(element);
			var input_value = $input.val();
			var input_is_checked = $input.prop('checked');
			var input_name = $input.prop('name');
			var input_value_menu_item_db_id = input_name.replace(/\D/g,'');

			if ('false' === input_value || false === input_value || ! input_is_checked) {
				$input.prop('disabled', true);
				// if the condition type is not set, then the if-menu setting should not be set for the menu item
				if ($.isNumeric(input_value_menu_item_db_id) && input_name.indexOf('menu-item-if-menu-condition-type') > -1) {
					var $input_if_menu_enable_field = $('input[name="menu-item-if-menu-enable[' + input_value_menu_item_db_id + '][]"]');

					if ( $input_if_menu_enable_field.length ) {
						$input_if_menu_enable_field.prop('disabled', true);
					}
				}
			}
		} );
	} );


});
