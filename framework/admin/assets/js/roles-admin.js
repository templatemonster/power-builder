(function($){
	$( document ).ready( function() {
		var $container             = $( '.tm_pb_roles_options_container' ),
			$yes_no_button_wrapper = $container.find( '.tm_pb_yes_no_button_wrapper' ),
			$yes_no_button         = $container.find( '.tm_pb_yes_no_button' ),
			$yes_no_select         = $container.find( 'select' ),
			$body                  = $( 'body' );

		$yes_no_button_wrapper.each( function() {
			var $this_el = $( this ),
				$this_switcher = $this_el.find( '.tm_pb_yes_no_button' ),
				selected_value = $this_el.find( 'select' ).val();

			if ( 'on' === selected_value ) {
				$this_switcher.removeClass( 'tm_pb_off_state' );
				$this_switcher.addClass( 'tm_pb_on_state' );
			} else {
				$this_switcher.removeClass( 'tm_pb_on_state' );
				$this_switcher.addClass( 'tm_pb_off_state' );
			}
		});

		$yes_no_button.click( function() {
			var $this_el = $( this ),
				$this_select = $this_el.closest( '.tm_pb_yes_no_button_wrapper' ).find( 'select' );

			if ( $this_el.hasClass( 'tm_pb_off_state') ) {
				$this_el.removeClass( 'tm_pb_off_state' );
				$this_el.addClass( 'tm_pb_on_state' );
				$this_select.val( 'on' );
			} else {
				$this_el.removeClass( 'tm_pb_on_state' );
				$this_el.addClass( 'tm_pb_off_state' );
				$this_select.val( 'off' );
			}

			$this_select.trigger( 'change' );
		});

		$yes_no_select.change( function() {
			var $this_el = $( this ),
				$this_switcher = $this_el.closest( '.tm_pb_yes_no_button_wrapper' ).find( '.tm_pb_yes_no_button' ),
				new_value = $this_el.val();

			if ( 'on' === new_value ) {
				$this_switcher.removeClass( 'tm_pb_off_state' );
				$this_switcher.addClass( 'tm_pb_on_state' );
			} else {
				$this_switcher.removeClass( 'tm_pb_on_state' );
				$this_switcher.addClass( 'tm_pb_off_state' );
			}

		});

		$( '.tm-pb-layout-buttons:not(.tm-pb-layout-buttons-reset)' ).click( function() {
			var $clicked_tab = $( this ),
				open_tab = $clicked_tab.data( 'open_tab' );

			$( '.tm_pb_roles_options_container.active-container' ).css( { 'display' : 'block', 'opacity' : 1 } ).stop( true, true ).animate( { opacity : 0 }, 300, function() {
				var $this_container = $( this );
				$this_container.css( 'display', 'none' );
				$this_container.removeClass( 'active-container' );
				$( '.' + open_tab ).addClass( 'active-container' ).css( { 'display' : 'block', 'opacity' : 0 } ).stop( true, true ).animate( { opacity : 1 }, 300 );
			});

			$( '.tm-pb-layout-buttons' ).removeClass( 'tm_pb_roles_active_menu' );

			$clicked_tab.addClass( 'tm_pb_roles_active_menu' );
		});

		$( '#tm_pb_save_roles' ).click( function() {
			var $all_options = $( '.tm_pb_roles_container_all' ).find( 'form' ),
				all_options_array = {},
				options_combined = '';

			$all_options.each( function() {
				var this_form = $( this ),
					form_id = this_form.data( 'role_id' ),
					form_settings = this_form.serialize();

				all_options_array[form_id] = form_settings;
			});

			options_combined = JSON.stringify( all_options_array );

			$.ajax({
				type: 'POST',
				url: tm_pb_roles_options.ajaxurl,
				dataType: 'json',
				data: {
					action : 'tm_pb_save_role_settings',
					tm_pb_options_all : options_combined,
					tm_pb_save_roles_nonce : tm_pb_roles_options.tm_roles_nonce
				},
				beforeSend: function ( xhr ){
					$( '#tm_pb_loading_animation' ).removeClass( 'tm_pb_hide_loading' );
					$( '#tm_pb_success_animation' ).removeClass( 'tm_pb_active_success' );
					$( '#tm_pb_loading_animation' ).show();
				},
				success: function( data ){
					$( '#tm_pb_loading_animation' ).addClass( 'tm_pb_hide_loading' );
					$( '#tm_pb_success_animation' ).addClass( 'tm_pb_active_success' ).show();

					setTimeout( function(){
						$( '#tm_pb_success_animation' ).fadeToggle();
						$( '#tm_pb_loading_animation' ).fadeToggle();
					}, 1000 );
				}
			});

			return false;
		} );

		$( '.tm_pb_toggle_all' ).click( function() {
			var $options_section = $( this ).closest( '.tm_pb_roles_section_container' ),
				$toggles = $options_section.find( '.tm-pb-main-setting' ),
				on_buttons_count = 0,
				off_buttons_count = 0;

			$toggles.each( function() {
				if ( 'on' === $( this ).val() ) {
					on_buttons_count++;
				} else {
					off_buttons_count++;
				}
			});

			if ( on_buttons_count >= off_buttons_count ) {
				$toggles.val( 'off' );
			} else {
				$toggles.val( 'on' );
			}

			$toggles.change();
		});

		$( '.tm-pb-layout-buttons-reset' ).click( function() {
			var $confirm_modal =
				"<div class='tm_pb_modal_overlay' data-action='reset_roles'>\
					<div class='tm_pb_prompt_modal'>\
					<h3>" + tm_pb_roles_options.modal_title + "</h3>\
					<p>" + tm_pb_roles_options.modal_message + "</p>\
						<a href='#' class='tm_pb_prompt_dont_proceed tm-pb-modal-close'>\
							<span>" + tm_pb_roles_options.modal_no + "<span>\
						</span></span></a>\
						<div class='tm_pb_prompt_buttons'>\
							<a href='#' class='tm_pb_prompt_proceed'>" + tm_pb_roles_options.modal_yes + "</a>\
						</div>\
					</div>\
				</div>";

			$( 'body' ).append( $confirm_modal );

			return false;
		});

		$( 'body' ).on( 'click', '.tm-pb-modal-close', function() {
			tm_pb_close_modal( $( this ) );
		});

		$( 'body' ).on( 'click', '.tm_pb_prompt_proceed', function() {
			var $all_toggles = $( '.tm-pb-main-setting' );

			$all_toggles.val( 'on' );
			$all_toggles.change();

			tm_pb_close_modal( $( this ) );
		});

		$body.append( '<div id="tm_pb_loading_animation"></div>' );
		$body.append( '<div id="tm_pb_success_animation"></div>' );

		$( '#tm_pb_loading_animation' ).hide();
		$( '#tm_pb_success_animation' ).hide();

		function tm_pb_close_modal( $button ) {
			var $modal_overlay = $button.closest( '.tm_pb_modal_overlay' );

			// add class to apply the closing animation to modal
			$modal_overlay.addClass( 'tm_pb_modal_closing' );

			//remove the modal with overlay when animation complete
			setTimeout( function() {
				$modal_overlay.remove();
			}, 600 );
		}
	});
})(jQuery)