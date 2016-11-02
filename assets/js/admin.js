(function($){
	$( document ).ready( function() {
		var $body = $( 'body' );

		$( '.tm_dashboard_authorize' ).click( function() {
			var $this_button = $( this ),
				$key_field = $this_button.closest( 'ul' ).find( '.api_option_key' ),
				$spinner = $this_button.closest( 'li' ).find( 'span.spinner' );

		});

		$( '#tm_pb_save_plugin' ).click( function() {
			var $loading_animation = $( '#tm_pb_loading_animation' ),
				$success_animation = $( '#tm_pb_success_animation' ),
				options_fromform;

			tinyMCE.triggerSave();
			options_fromform = $( '.' + dashboardSettings.plugin_class + ' #tm_dashboard_options' ).serialize();

			$.ajax({
				type: 'POST',
				url: builder_settings.ajaxurl,
				data: {
					action : 'tm_builder_save_settings',
					options : options_fromform,
					options_sub_title : '',
					save_settings_nonce : builder_settings.save_settings
				},
				beforeSend: function ( xhr ) {
					$loading_animation.removeClass( 'tm_pb_hide_loading' );
					$success_animation.removeClass( 'tm_pb_active_success' );
					$loading_animation.show();
				},
				success: function( data ) {
					$loading_animation.addClass( 'tm_pb_hide_loading' );
					$success_animation.addClass( 'tm_pb_active_success' ).show();

					setTimeout( function(){
						$success_animation.fadeToggle();
						$loading_animation.fadeToggle();
					}, 1000 );
				}
			});

			return false;
		});

		//$body.append( '<div id="tm_pb_loading_animation"></div>' );
		$body.append( '<div id="tm_pb_success_animation"></div>' );

		$( '#tm_pb_loading_animation' ).hide();
		$( '#tm_pb_success_animation' ).hide();
	});
})(jQuery)
