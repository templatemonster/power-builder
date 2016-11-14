( function ( $ ) {
	'use strict';

	window.TM_PageBuilder = {};

	var tm_is_loading_missing_modules = false;

	function tm_builder_load_backbone_templates( reload_template ) {

		// run tm_pb_append_templates as many times as needed
		var tm_pb_templates_count = 0,
			date_now              = new Date(),
			today_date            = date_now.getYear() + '_' + date_now.getMonth() + '_' + date_now.getDate(),
			tm_ls_prefix          = 'tm_pb_templates_',
			tm_ls_all_modules     = ( tm_pb_options['tm_builder_module_parent_shortcodes'] + '|' + tm_pb_options['tm_builder_module_child_shortcodes'] ).split( '|' ),
			product_version       = tm_pb_options.product_version,
			local_storage_buffer  = '',
			processed_modules_count = 0,
			reload_template = _.isUndefined( reload_template ) ? false : reload_template,
			missing_modules = {
				missing_modules_array: []
			},
			tm_pb_templates_interval;

		if ( ! reload_template ) {
			if ( ! $( 'script[src="' + tm_pb_options.builder_js_src + '"]' ).length ) {
				$( '.et-pb-cache-update' ).show();
			}

			$( 'body' ).on( 'click', '.tm_builder_increase_memory', function() {
				var $this_button = $(this);

				$.ajax({
					type: "POST",
					dataType: 'json',
					url: tm_pb_options.ajaxurl,
					data: {
						action : 'tm_pb_increase_memory_limit',
						tm_admin_load_nonce : tm_pb_options.tm_admin_load_nonce
					},
					success: function( data ) {
						if ( ! _.isUndefined( data.success ) ) {
							$this_button.addClass( 'tm_builder_modal_action_button_success' ).text( tm_pb_options.memory_limit_increased );
						} else {
							$this_button.addClass( 'tm_builder_modal_action_button_fail' ).prop( 'disabled', true ).text( tm_pb_options.memory_limit_not_increased );
						}
					}
				});

				return false;
			} );

			$( 'body' ).on( 'click', '.tm_pb_reload_builder', function() {
				location.reload();

				return false;
			} );

		}

		if ( tm_should_load_from_local_storage() ) {
			for ( var tm_ls_module_index in tm_ls_all_modules ) {
				var tm_ls_module_slug      = tm_ls_all_modules[ tm_ls_module_index ],
					tm_ls_template_slug    = tm_ls_prefix + tm_ls_module_slug,
					tm_ls_template_content = localStorage.getItem( tm_ls_template_slug );

				// count the processed modules
				processed_modules_count++;

				if ( _.isUndefined( tm_ls_template_content ) || _.isNull( tm_ls_template_content ) ) {
					missing_modules['missing_modules_array'].push( tm_ls_module_slug );
				} else {
					local_storage_buffer += localStorage.getItem( tm_ls_template_slug );
				}

				// perform ajax request if missing_modules_array length equals to the templates amount setting or if all the modules processed and we need to retrieve something
				if ( ! tm_is_loading_missing_modules && ( ( missing_modules['missing_modules_array'].length === parseInt( tm_pb_options.tm_builder_templates_amount ) ) || ( missing_modules['missing_modules_array'].length && ( tm_ls_all_modules.length === processed_modules_count ) ) ) ) {
					tm_is_loading_missing_modules = true;
					$.ajax({
						type: "POST",
						dataType: 'json',
						url: tm_pb_options.ajaxurl,
						data: {
							action : 'tm_pb_get_backbone_template',
							tm_post_type : tm_pb_options.post_type,
							tm_modules_slugs : JSON.stringify( missing_modules ),
							tm_admin_load_nonce : tm_pb_options.tm_admin_load_nonce
						},
						success: function( data ) {
							tm_is_loading_missing_modules = false;

							try {
								localStorage.setItem( tm_ls_prefix + data['slug'], data['template'] );
							} catch(e) {
								// do not use localStorage if it full or any other error occurs
							}

							$( 'body' ).append( data.template );
							if ( data.length ) {
								_.each( data, function( single_module ) {
									try {
										localStorage.setItem( tm_ls_prefix + single_module['slug'], single_module['template'] );
									} catch(e) {
										// do not use localStorage if it full or any other error occurs
									}

									$( 'body' ).append( single_module['template'] );
								} );
							}
						}
					});

					// reset the array of missing modules
					missing_modules['missing_modules_array'] = [];
				}

			}

			$( 'body' ).append( local_storage_buffer );

		} else {

			// run tm_pb_append_templates as many times as needed
			tm_pb_templates_interval = setInterval( function() {
				if ( tm_pb_templates_count === Math.ceil( tm_pb_options.tm_builder_modules_count/tm_pb_options.tm_builder_templates_amount ) ) {
					clearInterval( tm_pb_templates_interval );
					return false;
				}

				tm_pb_append_templates( tm_pb_templates_count * tm_pb_options.tm_builder_templates_amount );

				tm_pb_templates_count++;
			}, 800);

			tm_ls_set_transient();

		}

		function tm_builder_has_storage_support() {
			try {
				return 'localStorage' in window && window.localStorage !== null;
			} catch (e) {
				return false;
			}
		}

		function tm_ls_set_transient() {

			if ( ! tm_builder_has_storage_support() ) {
				return false;
			}

			try {
				localStorage.setItem( tm_ls_prefix + 'settings_date', today_date );
				localStorage.setItem( tm_ls_prefix + 'settings_product_version', product_version );
				localStorage.setItem( tm_ls_prefix + 'modules_count', tm_pb_options.modules_count );
			} catch(e) {
				// do not use localStorage if it full or any other error occurs
			}
		}

		function tm_should_load_from_local_storage() {

			if ( ! tm_builder_has_storage_support() ) {
				return false;
			}

			if ( ! _.isUndefined( tm_pb_options.debug ) && '1' == tm_pb_options.debug ) {
				return false;
			}

			if ( ! _.isUndefined( tm_pb_options.force_cache_purge ) && '1' == tm_pb_options.force_cache_purge ) {
				return false;
			}

			var tm_ls_settings_date = localStorage.getItem( tm_ls_prefix + 'settings_date' ),
				tm_ls_modules_count = localStorage.getItem( tm_ls_prefix + 'modules_count' ),
				tm_ls_settings_product_version = localStorage.getItem( tm_ls_prefix + 'settings_product_version' );

			if ( _.isUndefined( tm_ls_modules_count ) || tm_ls_modules_count !== tm_pb_options.modules_count ) {
				localStorage.removeItem( tm_ls_settings_date );
				localStorage.removeItem( tm_ls_modules_count );
				localStorage.removeItem( tm_ls_settings_product_version );
				tm_remove_ls_templates();
				return false;
			}

			if ( _.isUndefined( tm_ls_settings_date ) || _.isNull( tm_ls_settings_date ) ) {
				return false;
			}

			if ( _.isUndefined( tm_ls_settings_product_version ) || _.isNull( tm_ls_settings_product_version ) ) {
				return false;
			}

			if ( today_date != tm_ls_settings_date || product_version != tm_ls_settings_product_version ) {
				tm_remove_ls_templates();
				return false;
			}

			return true;
		}

		function tm_remove_ls_templates() {
			if ( ! tm_builder_has_storage_support() ) {
				return false;
			}

			var templates_prefix_re = /tm_pb_templates_*/i,
				found = null;

			for ( var prop in localStorage ) {
				if ( found = prop.match( templates_prefix_re ) ) {
					try {
						localStorage.removeItem( prop );
					} catch(e) {
						console.log( 'Clear cookie, please' );
					}
				}
			}
		}

		function tm_pb_append_templates( start_from ) {

			$.ajax({
				type: "POST",
				dataType: 'json',
				url: tm_pb_options.ajaxurl,
				data: {
					action : 'tm_pb_get_backbone_templates',
					tm_post_type : tm_pb_options.post_type,
					tm_admin_load_nonce : tm_pb_options.tm_admin_load_nonce,
					tm_templates_start_from : start_from
				},
				error: function() {
					var $failure_notice_template = $( '#et-builder-failure-notice-template' );

					if ( ! $failure_notice_template.length ) {
						return;
					}

					if ( $( '.tm_pb_failure_notification_modal' ).length ) {
						return;
					}

					if ( tm_builder_has_storage_support() ) {
						localStorage.removeItem( tm_ls_prefix + 'settings_date' );
						localStorage.removeItem( tm_ls_prefix + 'settings_product_version' );
					}

					$( 'body' ).addClass( 'tm_pb_stop_scroll' ).append( $failure_notice_template.html() );
				},
				success: function( data ) {
					//append retrieved templates to body
					for ( var name in data.templates ) {
						if ( tm_builder_has_storage_support() ) {
							try {
								localStorage.setItem( 'tm_pb_templates_' + name, data.templates[name] );
							} catch(e) {
								// do not use localStorage if it full or any other error occurs
							}
						}

						$( 'body' ).append( data.templates[name] );
					}
				}
			});
		}

	}
	tm_builder_load_backbone_templates();

	// Explicitly define ERB-style template delimiters to prevent
	// template delimiters being overwritten by 3rd party plugin
	_.templateSettings = {
		evaluate   : /<%([\s\S]+?)%>/g,
		interpolate: /<%=([\s\S]+?)%>/g,
		escape     : /<%-([\s\S]+?)%>/g
	};

} ( jQuery ) );
