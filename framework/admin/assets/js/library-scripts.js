(function($){
	$( document ).ready( function() {
		var url = window.location.href,
			tab_link = url.split( 'edit.php' )[1];

		if ( typeof tab_link !== 'undefined' ) {
			var $menu_items = $( '#toplevel_page_tm_divi_library' ).find( '.wp-submenu li' );
			$menu_items.removeClass( 'current' );
			$menu_items.find( 'a' ).each( function() {
				var $this_el = $( this ),
					this_href = $this_el.attr( 'href' ),
					full_tab_link = 'edit.php' + tab_link;
				if ( -1 !== full_tab_link.indexOf( this_href ) ) {
					$this_el.closest( 'li' ).addClass( 'current' );
				}
			});
			$( '#toplevel_page_tm_divi_library' ).removeClass( 'wp-not-current-submenu' ).addClass( 'wp-has-current-submenu' );
			$( 'a.toplevel_page_tm_divi_library' ).removeClass( 'wp-not-current-submenu' ).addClass( 'wp-has-current-submenu wp-menu-open' );
		}

		$( 'body' ).on( 'click', '.add-new-h2, a.page-title-action', function() {
			$( 'body' ).append( tm_pb_new_template_options.modal_output );
			return false;
		} );

		$( 'body' ).on( 'click', '.tm_pb_prompt_dont_proceed', function() {
			var $modal_overlay = $( this ).closest( '.tm_pb_modal_overlay' );

			// add class to apply the closing animation to modal
			$modal_overlay.addClass( 'tm_pb_modal_closing' );

			//remove the modal with overlay when animation complete
			setTimeout( function() {
				$modal_overlay.remove();
			}, 600 );
		} );

		$( 'body' ).on( 'change', '#new_template_type', function() {
			var selected_type = $( this ).val();

			if ( 'module' === selected_type || 'fullwidth_module' === selected_type ) {
				$( '.tm_module_tabs_options' ).css( 'display', 'block' );
			} else {
				$( '.tm_module_tabs_options' ).css( 'display', 'none' );
			}
		} );

		$( 'body' ).on( 'click', '.tm_pb_create_template:not(.clicked_button)', function() {
			var $this_button = $( this ),
				$this_form = $this_button.closest( '.tm_pb_prompt_modal' ),
				template_name = $this_form.find( '#tm_pb_new_template_name' ).val();

			if ( '' === template_name ) {
				$this_form.find( '#tm_pb_new_template_name' ).focus();
			} else {
				var	template_shortcode = '',
					layout_type = $this_form.find( '#new_template_type' ).val(),
					selected_tabs = '',
					selected_cats = '',
					fields_data = [];

				// push all the data from inputs into array
				$this_form.find('input, select').each( function() {
					var $this_input = $( this );

					if ( typeof $this_input.attr('id') !== 'undefined' && '' !== $this_input.val()) {
						// add only values from checked checkboxes
						if ( 'checkbox' === $this_input.attr('type') && !$this_input.is( ':checked' ) ) {
							return;
						}
						fields_data.push({
							'field_id': $this_input.attr('id'),
							'field_val': $this_input.val()
						});
					}
				});

				if ( 'module' === layout_type || 'fullwidth_module' === layout_type ) {
					if ( ! $( '.tm_module_tabs_options input' ).is( ':checked' ) ) {
						$( '.tm_pb_error_message_save_template' ).css( "display", "block" );
						return;
					} else {
						selected_tabs = '';

						$( '.tm_module_tabs_options input' ).each( function() {
							var this_input = $( this );

							if ( this_input.is( ':checked' ) ) {
								selected_tabs += '' !== selected_tabs ? ',' + this_input.val() : this_input.val();
							}

						});

						selected_tabs = 'general,advanced,css' === selected_tabs ? 'all' : selected_tabs;
					}
				}

				if ( $( '.layout_cats_container input' ).is( ':checked' ) ) {

					$( '.layout_cats_container input' ).each( function() {
						var this_input = $( this );

						if ( this_input.is( ':checked' ) ) {
							selected_cats += '' !== selected_cats ? ',' + this_input.val() : this_input.val();
						}
					});

				}

				// add processed data into array of values
				fields_data.push(
					{
						'field_id': 'selected_tabs',
						'field_val': selected_tabs
					},
					{
						'field_id': 'selected_cats',
						'field_val': selected_cats
					}
				);

				$this_button.addClass( 'clicked_button' );
				$this_button.closest( '.tm_pb_prompt_buttons' ).find( '.spinner' ).addClass( 'tm_pb_visible_spinner' );

				$.ajax( {
					type: "POST",
					url: tm_pb_new_template_options.ajaxurl,
					dataType: 'json',
					data:
					{
						action : 'tm_pb_add_new_layout',
						tm_admin_load_nonce : tm_pb_new_template_options.tm_admin_load_nonce,
						tm_layout_options : JSON.stringify(fields_data),
					},
					success: function( data ) {
						if ( typeof data !== 'undefined' && '' !== data ) {
							window.location.href = decodeURIComponent( unescape( data.edit_link ) );
						}
					}
				} );
			}
		} );

		$( '#tm_show_export_section' ).click( function() {
			var this_link = $( this ),
				max_height_value = this_link.hasClass( 'tm_pb_opened' ) ? '0' : '1000px';

			$( '.tm_pb_export_section' ).animate( { maxHeight: max_height_value }, 500 );
			this_link.toggleClass( 'tm_pb_opened' );
		});
	});
})(jQuery)