( function ( $, TM_PageBuilder, Backbone, tm_builder ) {
	window.tm_pb_get_content = function tm_pb_get_content( textarea_id, fix_shortcodes ) {
		var content,
			fix_shortcodes = typeof fix_shortcodes !== 'undefined' ? fix_shortcodes : false;

		if ( typeof window.tinyMCE !== 'undefined' && window.tinyMCE.get( textarea_id ) && ! window.tinyMCE.get( textarea_id ).isHidden() ) {
			content = window.tinyMCE.get( textarea_id ).getContent();
		} else {
			content = $( '#' + textarea_id ).val();
		}

		if ( fix_shortcodes && typeof window.tinyMCE !== 'undefined' ) {
			content = content.replace( /<p>\[/g, '[' );
			content = content.replace( /\]<\/p>/g, ']' );
		}

		return content.trim();
	}

	$( document ).ready( function() {

		window.TM_PageBuilder_Events = _.extend( {}, Backbone.Events );
		window.TM_PageBuilder_Layout = new TM_PageBuilder.Layout;
		window.TM_PageBuilder_Modules = new TM_PageBuilder.Modules;
		window.TM_PageBuilder_Histories = new TM_PageBuilder.Histories;
		window.TM_PageBuilder_App = new TM_PageBuilder.AppView( {
			model : TM_PageBuilder.Module,
			collection : TM_PageBuilder_Modules,
			history : TM_PageBuilder_Histories
		} );
		window.TM_PageBuilder_Visualize_Histories = new TM_PageBuilder.visualizeHistoriesView;

		var $tm_pb_content = $( '#tm_pb_hidden_editor' ),
			tm_pb_content_html = $tm_pb_content.html(),
			tm_pb_file_frame,
			$toggle_builder_button = $('#tm_pb_toggle_builder'),
			$toggle_builder_button_wrapper = $('.tm_pb_toggle_builder_wrapper'),
			$builder = $( '#tm_pb_layout' ),
			$tm_pb_old_content = $('#tm_pb_old_content'),
			$post_format_wrapper = $('#formatdiv'),
			$use_builder_custom_field = $( '#tm_pb_use_builder' ),
			$main_editor_wrapper = $( '#tm_pb_main_editor_wrap' ),
			$tm_pb_setting = $( '.tm_pb_page_setting' ),
			$tm_pb_layout_settings = $( '.tm_pb_page_layout_settings' ),
			$tm_pb_templates_cache = [],
			tm_pb_globals_loaded = 0,
			tm_pb_processed_yoast_content = false,
			tm_builder_template_options = {
				tabs: {},
				padding: {},
				yes_no_button: {},
				font_buttons: {}
			};

		TM_PageBuilder.Events = TM_PageBuilder_Events;
		window.tm_builder_template_options = tm_builder_template_options;
		window.tm_pb_globals_requested = 0;

		// Explicitly define ERB-style template delimiters to prevent
		// template delimiters being overwritten by 3rd party plugin
		_.templateSettings = {
			evaluate   : /<%([\s\S]+?)%>/g,
			interpolate: /<%=([\s\S]+?)%>/g,
			escape     : /<%-([\s\S]+?)%>/g
		};

		/*window.tm_pb_append_templates = function tm_pb_append_templates( start_from ) {
			$.ajax({
				type: "POST",
				dataType: 'json',
				url: tm_pb_options.ajaxurl,
				data: {
					action : 'et_pb_get_backbone_templates',
					et_post_type : tm_pb_options.post_type,
					et_admin_load_nonce : tm_pb_options.tm_admin_load_nonce,
					et_templates_start_from : start_from
				},
				success: function( data ) {
					//append retrieved templates to body
					$( 'body' ).append( data.templates );
				}
			});
		}*/

		/**
		 * Close and remove right click options
		 */
		window.tm_pb_close_all_right_click_options = function tm_pb_close_all_right_click_options() {
			// Remove right click options UI
			$('#tm-builder-right-click-controls').remove();

			// Remove builder overlay (right/left click anywhere outside builder to close right click options UI)
			$('#tm_pb_layout_right_click_overlay').remove();
		}

		/**
		 * @param  {Object} $upload_button
		 */
		window.tm_pb_activate_upload = function tm_pb_activate_upload( $upload_button ) {
			$upload_button.click( function( event ) {
				var $this_el = $(this);

				event.preventDefault();

				tm_pb_file_frame = wp.media.frames.tm_pb_file_frame = wp.media({
					title: $this_el.data( 'choose' ),
					library: {
						type: $this_el.data( 'type' )
					},
					button: {
						text: $this_el.data( 'update' ),
					},
					multiple: false
				});

				tm_pb_file_frame.on( 'select', function() {
					var attachment = tm_pb_file_frame.state().get('selection').first().toJSON();

					$this_el.siblings( '.tm-pb-upload-field' ).val( attachment.url );

					tm_pb_generate_preview_image( $this_el );
				});

				tm_pb_file_frame.open();
			} );

			$upload_button.siblings( '.tm-pb-upload-field' ).on( 'input', function() {
				tm_pb_generate_preview_image( $(this).siblings( '.tm-pb-upload-button' ) );
			} );

			$upload_button.siblings( '.tm-pb-upload-field' ).each( function() {
				tm_pb_generate_preview_image( $(this).siblings( '.tm-pb-upload-button' ) );
			} );
		}

		/**
		 * @param  {Object} $gallery_button
		 */
		window.tm_pb_activate_gallery = function tm_pb_activate_gallery( $gallery_button ) {
			$gallery_button.click( function( event ) {
				var $this_el = $(this)
					$gallery_ids = $gallery_button.closest( '.tm-pb-option' ).siblings( '.tm-pb-option-gallery_ids' ).find( '.tm-pb-gallery-ids-field' ),
					$gallery_orderby = $gallery_button.closest( '.tm-pb-option' ).siblings( '.tm-pb-option-gallery_orderby' ).find( '.tm-pb-gallery-ids-field' );

				event.preventDefault();

				// Check if the `wp.media.gallery` API exists.
				if ( typeof wp === 'undefined' || ! wp.media || ! wp.media.gallery )
					return;

				var gallery_ids = $gallery_ids.val().length ? ' ids="' + $gallery_ids.val() + '"' : '',
					gallery_orderby = $gallery_orderby.val().length ? ' orderby="' + $gallery_orderby.val() + '"' : '',
					gallery_shortcode = '[gallery' + gallery_ids + gallery_orderby + ']';

				tm_pb_file_frame = wp.media.frames.tm_pb_file_frame = wp.media.gallery.edit( gallery_shortcode );

				if ( !gallery_ids ) {
					tm_pb_file_frame.setState('gallery-library');
				}

				/**
				 * Remove the 'Columns' and 'Link To' unneeded settings
				 * @access private
				 * @param  {Object} $el
				 */
				function remove_unneeded_gallery_settings( $el ) {
					setTimeout(function(){
						$el.find( '.gallery-settings' ).find( 'label.setting' ).each(function() {
							if ( $(this).find( '.link-to, .columns, .size' ).length ) {
								$(this).remove();
							} else {
								if ( $(this).has( 'input[type=checkbox]' ).length ) {
									$(this).children( 'input[type=checkbox]' ).css( 'margin', '11px 5px' );
								}
							}
						});
					}, 10 );
				}
				// Remove initial unneeded settings
				remove_unneeded_gallery_settings( tm_pb_file_frame.$el );
				// Remove unneeded settings upon re-viewing edit view
				tm_pb_file_frame.on( 'content:render:browse', function( browser ){
					remove_unneeded_gallery_settings( browser.$el );
				});

				tm_pb_file_frame.state( 'gallery-edit' ).on( 'update', function( selection ) {

					var shortcode_atts = wp.media.gallery.shortcode( selection ).attrs.named;
					if ( shortcode_atts.ids ) {
						$gallery_ids.val( shortcode_atts.ids );
					}

					if ( shortcode_atts.orderby ) {
						$gallery_orderby.val( shortcode_atts.orderby );
					} else {
						$gallery_orderby.val( '' );
					}
				});
			});
		}

		/**
		 * @param  {Object} $video_image_button
		 */
		window.tm_pb_generate_video_image = function tm_pb_generate_video_image( $video_image_button ) {
			$video_image_button.click( function( event ) {
				var $this_el = $(this),
					$upload_field = $( '#tm_pb_src.tm-pb-upload-field' ),
					video_url = $upload_field.val().trim();

				event.preventDefault();

				$.ajax( {
					type: "POST",
					url: tm_pb_options.ajaxurl,
					data:
					{
						action : 'tm_pb_video_get_oembed_thumbnail',
						tm_admin_load_nonce : tm_pb_options.tm_admin_load_nonce,
						tm_video_url : video_url
					},
					success: function( response ) {
						if ( response.length ) {
							$('#tm_pb_image_src').val( response ).trigger('input');
						} else {
							$this_el.after( '<div class="tm-pb-error">' + tm_pb_options.video_module_image_error + '</div>' );
							$this_el.siblings('.tm-pb-error').delay(5000).fadeOut(800);
						}
					}
				} );
			} );
		}

		/**
		 * @param  {Object} $upload_button
		 */
		window.tm_pb_generate_preview_image = function tm_pb_generate_preview_image( $upload_button ){
			var $upload_field = $upload_button.siblings( '.tm-pb-upload-field' ),
				$preview = $upload_field.siblings( '.tm-pb-upload-preview' ),
				image_url = $upload_field.val().trim();

			if ( $upload_button.data( 'type' ) !== 'image' ) return;

			if ( image_url === '' ) {
				if ( $preview.length ) $preview.remove();

				return;
			}

			if ( ! $preview.length ) {
				$upload_button.siblings('.description').before( '<div class="tm-pb-upload-preview">' + '<strong class="tm-pb-upload-preview-title">' + tm_pb_options.preview_image + '</strong>' + '<img src="" width="408" /></div>' );
				$preview = $upload_field.siblings( '.tm-pb-upload-preview' );
			}

			$preview.find( 'img' ).attr( 'src', image_url );
		}

		/**
		 * Deactivate builder
		 */
		window.tm_pb_deactivate_builder = function tm_pb_deactivate_builder() {
			var $body = $( 'body' ),
				page_position = 0;

			tm_pb_set_content( 'content', $tm_pb_old_content.val() );

			window.wpActiveEditor = 'content';
			$use_builder_custom_field.val( 'off' );
			$builder.hide();
			$toggle_builder_button.text( $toggle_builder_button.data( 'builder' ) ).toggleClass( 'tm_pb_builder_is_used' );
			$main_editor_wrapper.toggleClass( 'tm_pb_hidden' );

			tm_pb_show_layout_settings();

			page_position = $body.scrollTop();
			$body.scrollTop( page_position + 1 );

			TM_PageBuilder_Events.trigger( 'tm-deactivate-builder' );

			//trigger window resize event to trigger tinyMCE editor toolbar sizes recalculation.
			$( window ).trigger( 'resize' );
		}

		/**
		 * Create modal
		 * @param  {String}        action
		 * @param  {String|Object} cid_or_element
		 * @param  {Number}        module_width
		 * @param  {String}        columns_layout
		 */
		window.tm_pb_create_prompt_modal = function tm_pb_create_prompt_modal( action, cid_or_element, module_width, columns_layout ) {
			var on_top_class = '',
				on_top_both_actions_class = '',
				$modal,
				modal_interface,
				modal_content,
				modal_attributes = {},
				current_view,
				parent_view = '',
				$global_children,
				has_global = 'no_globals';

			if ( -1 !== $.inArray( action, [ 'save_template', 'reset_advanced_settings' ] ) ) {
				on_top_class = ' tm_modal_on_top';
			}

			if ( 'reset_advanced_settings' === action ) {
				on_top_both_actions_class = ' tm_modal_on_top_both_actions';
			}

			$modal = $( '<div class="tm_pb_modal_overlay' + on_top_class + on_top_both_actions_class + '" data-action="' + action + '"></div>' );
			modal_interface = $( '#tm-builder-prompt-modal-' + action );

			if ( modal_interface.length ) {
				modal_interface = modal_interface.html();
			} else {
				modal_interface = $( '#tm-builder-prompt-modal' ).html();
			}

			modal_content = _.template( $( '#tm-builder-prompt-modal-' + action + '-text' ).html() );

			if ( 'save_template' === action ) {
				current_view = TM_PageBuilder_Layout.getView( cid_or_element.model.get( 'cid' ) );

				if ( 'undefined' !== typeof current_view.model.get( 'parent' ) ) {
					parent_view = TM_PageBuilder_Layout.getView( current_view.model.get( 'parent' ) );
				}

				$global_children = current_view.$el.find( '.tm_pb_global' );

				if ( $global_children.length ) {
					has_global = 'has_global';
				}

				modal_attributes.is_global = typeof current_view.model.get( 'tm_pb_global_module' ) !== 'undefined' && '' !== current_view.model.get( 'tm_pb_global_module' ) ? 'global' : 'regular';
				modal_attributes.is_global_child = '' !== parent_view && ( ( typeof parent_view.model.get( 'tm_pb_global_module' ) !== 'undefined' && '' !== parent_view.model.get( 'tm_pb_global_module' ) ) || ( typeof parent_view.model.get( 'global_parent_cid' ) !== 'undefined' && '' !== parent_view.model.get( 'global_parent_cid' ) ) ) ? 'global' : 'regular';
				modal_attributes.module_type = current_view.model.get( 'type' );
			}

			$modal.append( modal_interface );

			$modal.find( '.tm_pb_prompt_modal' ).prepend( modal_content( modal_attributes ) );

			$( 'body' ).append( $modal );

			setTimeout( function() {
				$modal.find('select, input, textarea, radio').filter(':eq(0)').focus();
			}, 1 );

			if ( 'rename_admin_label' === action ) {
				var admin_label = $modal.find( 'input#tm_pb_new_admin_label' ),
					current_view = TM_PageBuilder_Layout.getView( cid_or_element ),
					current_admin_label = current_view.model.get( 'admin_label' ).trim();

				if ( current_admin_label !== '' ) {
					admin_label.val( current_admin_label );
				}
			}

			$( '.tm_pb_modal_overlay .tm_pb_prompt_proceed' ).click( function( event ) {
				event.preventDefault();

				var $prompt_modal = $(this).closest( '.tm_pb_modal_overlay' );

				switch( $prompt_modal.data( 'action' ).trim() ){
					case 'deactivate_builder' :
						tm_pb_deactivate_builder();
						break;
					case 'clear_layout' :
						TM_PageBuilder_App.removeAllSections( true );
						break;

					case 'rename_admin_label' :
						var admin_label = $prompt_modal.find( '#tm_pb_new_admin_label' ).val().trim(),
							current_view = TM_PageBuilder_Layout.getView( cid_or_element );

						// TODO: Decide if we want to allow blank admin labels
						if ( admin_label == '' ) {
							$prompt_modal.find( '#tm_pb_new_admin_label' ).focus()

							return;
						}

						current_view.model.set( 'admin_label', admin_label, { silent : true } );
						current_view.renameModule();

						// Enable history saving and set meta for history
						TM_PageBuilder_App.allowHistorySaving( 'renamed', 'module', admin_label );

						tm_reinitialize_builder_layout();

						break;
					case 'reset_advanced_settings' :
						cid_or_element.each( function() {
							tm_pb_reset_element_settings( $(this) );
						} );
						break;
					case 'save_layout' :
						var layout_name = $prompt_modal.find( '#tm_pb_new_layout_name' ).val().trim();

						if ( layout_name == '' ) {
							$prompt_modal.find( '#tm_pb_new_layout_name' ).focus()

							return;
						}

						$.ajax( {
							type: "POST",
							url: tm_pb_options.ajaxurl,
							data:
							{
								action : 'tm_pb_save_layout',
								tm_admin_load_nonce : tm_pb_options.tm_admin_load_nonce,
								tm_layout_name : layout_name,
								tm_layout_content : tm_pb_get_content( 'content' ),
								tm_layout_type : 'layout',
								tm_post_type : tm_pb_options.post_type
							},
							success: function( data ) {
							}
						} );

						break;
					case 'save_template' :
						var template_name                = $prompt_modal.find( '#tm_pb_new_template_name' ).val().trim(),
							layout_scope                 = $prompt_modal.find( $( '#tm_pb_template_global' ) ).is( ':checked' ) ? 'global' : 'not_global',
							$module_settings_container   = $( '.tm_pb_module_settings' ),
							module_type                  = $module_settings_container.data( 'module_type' ),
							module_icon                  = $module_settings_container.data( 'module_icon' ),
							layout_type                  = ( 'section' === module_type || 'row' === module_type ) ? module_type : 'module',
							module_width_upd             = typeof module_width !== 'undefined' ? module_width : 'regular',
							module_cid                   = cid_or_element.model.get( 'cid' ),
							template_shortcode           = '',
							selected_tabs                = '',
							selected_cats                = '',
							new_cat                      = $prompt_modal.find( '#tm_pb_new_cat_name' ).val(),
							ignore_global                = typeof has_global !== 'undefined' && 'has_global' === has_global && 'global' === layout_scope ? 'ignore_global' : 'include_global',
							ignore_saved_tabs            = 'ignore_global' === ignore_global ? 'ignore_global_tabs' : '',
							$modal_settings_container    = $( '.tm_pb_modal_settings_container' ),
							$modal_overlay               = $( '.tm_pb_modal_overlay' );

							layout_type = 'row_inner' === module_type ? 'row' : layout_type;

						if ( template_name == '' ) {
							$prompt_modal.find( '#tm_pb_new_template_name' ).focus();

							return;
						}

						if ( $( '.tm_pb_select_module_tabs' ).length ) {
							if ( ! $( '.tm_pb_select_module_tabs input' ).is( ':checked' ) ) {
								$( '.tm_pb_error_message_save_template' ).css( "display", "block" );
								return;
							} else {
								selected_tabs = '';

								$( '.tm_pb_select_module_tabs input' ).each( function() {
									var this_input = $( this );

									if ( this_input.is( ':checked' ) ) {
										selected_tabs += '' !== selected_tabs ? ',' + this_input.val() : this_input.val();
									}

								});

								selected_tabs = 'general,advanced,css' === selected_tabs ? 'all' : selected_tabs;
							}

							if ( 'all' !== selected_tabs ) {
								var selected_tabs_selector = '',
									selected_tabs_array = selected_tabs.split(','),
									existing_attributes = cid_or_element.model.attributes;

								_.each( selected_tabs_array, function ( tab ) {
									switch ( tab ) {
										case 'general' :
											selected_tabs_selector += '.tm-pb-options-tab-general input, .tm-pb-options-tab-general select, .tm-pb-options-tab-general textarea';
											break;
										case 'advanced' :
											selected_tabs_selector += '' !== selected_tabs_selector ? ',' : '';
											selected_tabs_selector += '.tm-pb-options-tab-advanced input, .tm-pb-options-tab-advanced select, .tm-pb-options-tab-advanced textarea';
											break;
										case 'css' :
											selected_tabs_selector += '' !== selected_tabs_selector ? ',' : '';
											selected_tabs_selector += '.tm-pb-options-tab-custom_css input, .tm-pb-options-tab-custom_css select, .tm-pb-options-tab-custom_css textarea';
											break;
									}
								});

								_.each( existing_attributes, function( value, key ) {
									if ( -1 !== key.indexOf( 'tm_pb_' ) ) {
										cid_or_element.model.unset( key, { silent : true } );
									}
								} );
							}

							cid_or_element.model.set( 'tm_pb_saved_tabs', selected_tabs, { silent : true } );
						}

						if ( $( '.layout_cats_container input' ).is( ':checked' ) ) {

							$( '.layout_cats_container input' ).each( function() {
								var this_input = $( this );

								if ( this_input.is( ':checked' ) ) {
									selected_cats += '' !== selected_cats ? ',' + this_input.val() : this_input.val();
								}
							});

						}

						cid_or_element.performSaving( selected_tabs_selector );

						template_shortcode = TM_PageBuilder_App.generateCompleteShortcode( module_cid, layout_type, ignore_global, ignore_saved_tabs );

						if ( 'row_inner' === module_type ) {
							template_shortcode = template_shortcode.replace( /tm_pb_row_inner/g, 'tm_pb_row' );
							template_shortcode = template_shortcode.replace( /tm_pb_column_inner/g, 'tm_pb_column' );
						}

						// save all the settings after template was generated.
						if ( 'all' !== selected_tabs ) {
							cid_or_element.performSaving();
						}

						$modal_settings_container.addClass( 'tm_pb_modal_closing' );
						$modal_overlay.addClass( 'tm_pb_overlay_closing' );

						setTimeout( function() {
							$modal_settings_container.remove();
							$modal_overlay.remove();
							$( 'body' ).removeClass( 'tm_pb_stop_scroll' );
						}, 600 );

						$.ajax( {
							type: "POST",
							url: tm_pb_options.ajaxurl,
							dataType: 'json',
							data:
							{
								action : 'tm_pb_save_layout',
								tm_admin_load_nonce : tm_pb_options.tm_admin_load_nonce,
								tm_layout_name : template_name,
								tm_layout_content : template_shortcode,
								tm_layout_scope : layout_scope,
								tm_layout_type : layout_type,
								tm_module_width : module_width_upd,
								tm_columns_layout : columns_layout,
								tm_selected_tabs : selected_tabs,
								tm_module_type : module_type,
								tm_module_icon: module_icon,
								tm_layout_cats : selected_cats,
								tm_layout_new_cat : new_cat,
								tm_post_type : tm_pb_options.post_type,
							},
							beforeSend: function( data ) {
								//show overlay which blocks the entire screen to avoid js errors if user starts editing the module immediately after saving
								if ( 'global' === layout_scope ) {
									if ( ! $( 'body' ).find( '.tm_pb_global_loading_overlay' ).length ) {
										$( 'body' ).append( '<div class="tm_pb_global_loading_overlay"></div>' );
									}
								}
							},
							success : function( data ) {
								if ( 'global' === layout_scope ) {
									var model = TM_PageBuilder_App.collection.find( function( model ) {
										return model.get( 'cid' ) == module_cid;
									} );

									model.set( 'tm_pb_global_module', data.post_id );

									if ( 'ignore_global' === ignore_global ) {
										if ( $global_children.length ) {
											$global_children.each( function() {
												var child_cid = $( this ).data( 'cid' );

												if ( typeof child_cid !== 'undefined' && '' !== child_cid ) {
													var child_model = TM_PageBuilder_App.collection.find( function( model ) {
														return model.get( 'cid' ) == child_cid;
													} );

													child_model.unset( 'tm_pb_global_module' );
													child_model.unset( 'tm_pb_saved_tabs' );
												}
											});
										}
									}

									tm_reinitialize_builder_layout();

									setTimeout( function(){
										$( 'body' ).find( '.tm_pb_global_loading_overlay' ).remove();
									}, 650 );
								}
							}
						} );
						break;
				}

				tm_pb_close_modal( $( this ) );
			} );

			$( '.tm_pb_modal_overlay .tm_pb_prompt_dont_proceed' ).click( function( event ) {
				event.preventDefault();

				tm_pb_close_modal( $( this ) );
			} );
		}

		/**
		 * @param  {Object} $element
		 */
		window.tm_pb_handle_clone_class = function tm_pb_handle_clone_class( $element ) {
			$element.addClass( 'tm_pb_animate_clone' );

			setTimeout( function() {
				if ( $element.length ) {
					$element.removeClass( 'tm_pb_animate_clone' );
				}
			}, 500 );
		}

		/**
		 * @param  {Object} $this_button
		 */
		window.tm_pb_close_modal = function tm_pb_close_modal( $this_button ) {
			var $modal_overlay = $this_button.closest( '.tm_pb_modal_overlay' );

			$modal_overlay.addClass( 'tm_pb_modal_closing' );

			setTimeout( function() {
				$modal_overlay.remove();
			}, 600 );
		}

		window.tm_pb_close_modal_view = function tm_pb_close_modal_view( that, trigger_event ) {
			that.removeOverlay();

			$( '.tm_pb_modal_settings_container' ).addClass( 'tm_pb_modal_closing' );

			setTimeout( function() {
				that.remove();

				if ( 'trigger_event' === trigger_event ) {
					TM_PageBuilder_Events.trigger( 'tm-modal-view-removed' );
				}
			}, 600 );
		}

		window.tm_pb_hide_layout_settings = function tm_pb_hide_layout_settings(){
			if ( $tm_pb_setting.filter( ':visible' ).length > 1 ){
				$tm_pb_layout_settings.find('.tm_pb_page_layout_settings').hide();
				$tm_pb_layout_settings.find('.tm_pb_side_nav_settings').show();
			}
			else{
				if ( 'post' !== tm_pb_options.post_type ) {
					$tm_pb_layout_settings.closest( '#tm_settings_meta_box' ).find('.tm_pb_page_layout_settings').hide();
				}

				$tm_pb_layout_settings.closest( '#tm_settings_meta_box' ).find('.tm_pb_side_nav_settings').show();
				$tm_pb_layout_settings.closest( '#tm_settings_meta_box' ).find('.tm_pb_single_title').show();
			}

			// On post, hide post format UI and layout settings if pagebuilder is activated
			if ( $post_format_wrapper.length ) {
				$post_format_wrapper.hide();

				var active_post_format = $post_format_wrapper.find( 'input[type="radio"]:checked').val();
				$( '.tm_divi_format_setting.tm_divi_' + active_post_format + '_settings' ).hide();
			}

			// Show project navigation option when builder enabled
			if ( 'project' === tm_pb_options.post_type ) {
				$tm_pb_layout_settings.closest( '#tm_settings_meta_box' ).find( '.tm_pb_project_nav' ).show();
			}
		}

		window.tm_pb_show_layout_settings = function tm_pb_show_layout_settings(){
			$tm_pb_layout_settings.show().closest( '#tm_settings_meta_box' ).show();
			$tm_pb_layout_settings.closest( '#tm_settings_meta_box' ).find('.tm_pb_side_nav_settings').hide();
			$tm_pb_layout_settings.closest( '#tm_settings_meta_box' ).find('.tm_pb_single_title').hide();

			// On post, show post format UI and layout settings if pagebuilder is deactivated
			if ( $post_format_wrapper.length ) {
				$post_format_wrapper.show();

				var active_post_format = $post_format_wrapper.find( 'input[type="radio"]:checked').val();
				$( '.tm_divi_format_setting.tm_divi_' + active_post_format + '_settings' ).show();
			}

			// Hide project navigation option when builder disabled
			if ( 'project' === tm_pb_options.post_type ) {
				$tm_pb_layout_settings.closest( '#tm_settings_meta_box' ).find( '.tm_pb_project_nav' ).hide();
			}

		}

		window.tm_get_editor_mode = function tm_get_editor_mode() {
			var tm_editor_mode = 'tinymce';

			if ( 'html' === getUserSetting( 'editor' ) ) {
				tm_editor_mode = 'html';
			}

			return tm_editor_mode;
		}

		window.tm_pb_is_editor_in_visual_mode = function tm_pb_is_editor_in_visual_mode( id ) {
			var is_editor_in_visual_mode = !! ( typeof window.tinyMCE !== 'undefined' && window.tinyMCE.get( id ) && ! window.tinyMCE.get( id ).isHidden() );

			return is_editor_in_visual_mode;
		}

		window.tm_pb_set_content = function tm_pb_set_content( textarea_id, content, current_action ) {
			var current_action                = current_action || '',
				main_editor_in_visual_mode    = tm_pb_is_editor_in_visual_mode( 'content' ),
				current_editor_in_visual_mode = tm_pb_is_editor_in_visual_mode( textarea_id );

			if ( typeof window.tinyMCE !== 'undefined' && window.tinyMCE.get( textarea_id ) && current_editor_in_visual_mode ) {
				var editor = window.tinyMCE.get( textarea_id );

				editor.setContent( $.trim( content ), { format : 'html'  } );
			} else {
				$( '#' + textarea_id ).val( $.trim( content ) );
			}

			// generate quick tag buttons for the editor in Text mode
			( typeof tinyMCEPreInit.mceInit[textarea_id] !== "undefined" ) ? quicktags( { id : textarea_id } ) : quicktags( tinyMCEPreInit.qtInit[textarea_id] );
			QTags._buttonsInit();

			// Enabling publish button + removes disable_publish mark
			if ( ! wp.heartbeat || ! wp.heartbeat.hasConnectionError() ) {
				$('#publish').removeClass( 'disabled' );

				delete TM_PageBuilder_App.disable_publish;
			}
		}

		window.tm_pb_tinymce_remove_control = function tm_pb_tinymce_remove_control( textarea_id ) {
			if ( typeof window.tinyMCE !== 'undefined' ) {
				window.tinyMCE.execCommand( 'mceRemoveEditor', false, textarea_id );

				if ( typeof window.tinyMCE.get( textarea_id ) !== 'undefined' ) {
					window.tinyMCE.remove( '#' + textarea_id );
				}
			}
		}

		window.tm_pb_update_affected_fields = function tm_pb_update_affected_fields( $affected_fields ) {
			if ( $affected_fields.length ) {
				$affected_fields.each( function() {
					$(this).trigger( 'change' );
				} );
			}
		}

		window.tm_pb_custom_color_remove = function tm_pb_custom_color_remove( $element ) {
			var $this_el = $element,
				$color_picker_container = $this_el.closest( '.tm-pb-custom-color-container' ),
				$color_choose_button = $color_picker_container.siblings( '.tm-pb-choose-custom-color-button' ),
				$hidden_color_input = $color_picker_container.find( '.tm-pb-custom-color-picker' ),
				hidden_class = 'tm_pb_hidden';

			$color_choose_button.removeClass( hidden_class );
			$color_picker_container.addClass( hidden_class );

			$hidden_color_input.val( '' );

			return false;
		}

		// set default values for the responsive options.
		// Tablet default inherits Desktop value, Phone default inherits the Tablet value.
		window.tm_pb_update_mobile_defaults = function tm_pb_update_mobile_defaults( $this_el, range_input_value ) {
			var this_device = typeof $this_el.data( 'device' ) === 'undefined' ? 'all' : $this_el.data( 'device' );

			if ( 'all' === this_device || 'phone' === this_device ) {
				return;
			}

			var this_value = typeof range_input_value !== 'undefined' ? range_input_value : $this_el.val(),
				is_range_field = $this_el.hasClass( 'tm-pb-range-input' ) || $this_el.hasClass( 'tm-pb-range' ),
				is_margin_field = $this_el.hasClass( 'tm_custom_margin_main' ),
				field_class = is_range_field ? '.tm-pb-range-input' : '.tm-pb-main-setting',
				$laptop_field = $this_el.siblings( field_class + '.tm_pb_setting_mobile_laptop' ),
				$tablet_field = $this_el.siblings( field_class + '.tm_pb_setting_mobile_tablet' ),
				$phone_field = $this_el.siblings( field_class + '.tm_pb_setting_mobile_phone' ),
				laptop_default = typeof $laptop_field.data( 'default' ) === 'undefined' ? '' : $laptop_field.data( 'default' ),
				tablet_default = typeof $tablet_field.data( 'default' ) === 'undefined' ? '' : $tablet_field.data( 'default' ),
				phone_default = typeof $phone_field.data( 'default' ) === 'undefined' ? '' : $phone_field.data( 'default' ),
				range_value = _.isNaN( parseFloat( this_value ) ) ? 0 : parseFloat( this_value ),
				check_phone_default = false,
				check_tablet_default = false,
				$laptop_range,
				$tablet_range,
				$phone_range;

			if ( is_range_field ) {
				$laptop_range = $this_el.siblings( '.tm-pb-range.tm_pb_setting_mobile_laptop' );
				$tablet_range = $this_el.siblings( '.tm-pb-range.tm_pb_setting_mobile_tablet' );
				$phone_range = $this_el.siblings( '.tm-pb-range.tm_pb_setting_mobile_phone' );
			} else if ( ! $this_el.hasClass( 'tm_custom_margin_main' ) ) {
				this_value = tm_pb_sanitize_input_unit_value( this_value, false, '' );
			}

			if ( 'desktop' === this_device ) {
				if ( 'no' === $laptop_field.data( 'has_saved_value' ) && $laptop_field.val() === tablet_default ) {
					$laptop_field.val( this_value ).change();
					check_tablet_default = true;

					// update range value if needed
					if ( is_range_field ) {
						//$laptop_range.val( range_value );
					}
				}

				$laptop_field.data( 'default', this_value );

				// update range value if needed
				if ( is_range_field ) {
					//$laptop_range.data( 'default', range_value );
				}

				if ( is_margin_field ) {
					tm_pb_process_custom_margin_field( $laptop_field );
				}
			} else {
				check_tablet_default = true;
			}

			if ( check_tablet_default ) {
				if ( 'no' === $tablet_field.data( 'has_saved_value' ) && $tablet_field.val() === tablet_default ) {
					$tablet_field.val( this_value ).change();
					check_phone_default = true;

					// update range value if needed
					if ( is_range_field ) {
						//$tablet_range.val( range_value );
					}
				}

				$tablet_field.data( 'default', this_value );

				// update range value if needed
				if ( is_range_field ) {
					//$tablet_range.data( 'default', range_value );
				}

				if ( is_margin_field ) {
					tm_pb_process_custom_margin_field( $tablet_field );
				}
			} else {
				check_phone_default = true;
			}

			// adjust default settings for the phone
			if ( check_phone_default ) {
				if ( 'no' === $phone_field.data( 'has_saved_value' ) && $phone_field.val() === phone_default ) {
					$phone_field.val( this_value ).change();

					// update range value if needed
					if ( is_range_field ) {
						//$phone_range.val( range_value );
					}

					if ( is_margin_field ) {
						tm_pb_process_custom_margin_field( $phone_field );
					}
				}

				$phone_field.data( 'default', this_value );

				// update range value if needed
				if ( is_range_field ) {
					//$phone_range.data( 'default', range_value );
				}
			}
		}

		window.tm_pb_update_reset_button = function tm_pb_update_reset_button( $option_container ) {
			var current_option = $option_container.find( '.tm-pb-main-setting.tm_pb_setting_mobile_active' ),
				option_value = current_option.val() + '',
				is_range_option  = current_option.hasClass( 'tm-pb-range' ),
				option_default = typeof current_option.data( 'default' ) === 'undefined' ? '' : current_option.data( 'default' ) + '',
				$reset_button = $option_container.find( '.tm-pb-reset-setting' ),
				option_default_processed = is_range_option && '' !== option_default ? parseFloat( option_default ) + '' : option_default;

			if ( option_value !== option_default_processed ) {
				$reset_button.addClass( 'tm-pb-reset-icon-visible' );
			} else {
				$reset_button.removeClass( 'tm-pb-reset-icon-visible' );
			}
		}

		window.tm_pb_open_responsive_tab = function tm_pb_open_responsive_tab( $option_container, selected_tab ) {
			$option_container.find( '.tm_pb_setting_mobile' ).removeClass( 'tm_pb_setting_mobile_active' );
			$option_container.find( '.tm_pb_setting_mobile_' + selected_tab ).addClass( 'tm_pb_setting_mobile_active' );
			$option_container.find( '.tm_pb_mobile_settings_tab' ).removeClass( 'tm_pb_mobile_settings_active_tab' );
			$option_container.find( '.tm_pb_mobile_settings_tab[data-settings_tab="' + selected_tab + '"]' ).addClass( 'tm_pb_mobile_settings_active_tab' );

			tm_pb_update_reset_button( $option_container );
		}

		// check the advanced settings and update defaults based on the current settings of the parent module
		window.tm_pb_set_child_defaults = function tm_pb_set_child_defaults( $container, module_cid ) {
			var $advanced_tab          = $container.find( '.tm-pb-options-tab-advanced' ),
				$advanced_tab_settings = $advanced_tab.find( '.tm-pb-main-setting' ),
				$parent_container      = $( '.tm_pb_modal_settings_container:not(.tm_pb_modal_settings_container_step2)'),
				$parent_container_adv  = $parent_container.find( '.tm-pb-options-tab-advanced' ),
				current_module         = TM_PageBuilder_Modules.findWhere( { cid : module_cid } );

			if ( $advanced_tab.length ) {
				$advanced_tab_settings.each( function() {
					var $this_option = $( this ),
						$option_main_input,
						option_id;

					// process only range options
					if ( $this_option.hasClass( 'tm-pb-range' ) ) {
						$option_main_input = $this_option.siblings( '.tm-pb-range-input' );

						$option_main_input.each( function() {
							var $current_option = $( this ),
								option_id = $current_option.attr( 'id' ),
								current_device = typeof $current_option.data( 'device' ) !== 'undefined' ? $current_option.data( 'device' ) : 'all',
								option_parent = $( '#' + option_id );

							if ( option_parent.length ) {
								// check whether module already has module_defaults, otherwise set it to empty array
								current_module.attributes['module_defaults'] = current_module.attributes['module_defaults'] || [];
								// update 'module_defaults' to avoid saving the default values into database
								current_module.attributes['module_defaults'][ option_id ] = option_parent.val();
								// update default attribute in the option settings to display the correct value in builder
								if ( 'all' !== current_device ) {
									var $mobile_option = $current_option.siblings( '.tm-pb-main-setting.tm_pb_setting_mobile_' + current_device );

									$mobile_option.data( 'default_inherited', option_parent.val() );
									$mobile_option.data( 'default', option_parent.val() );
								}
								$current_option.data( 'default_inherited', option_parent.val() );
								$current_option.data( 'default', option_parent.val() );
							}
						} );
					}
				} );
			}
		}

		window.tm_pb_init_main_settings = function tm_pb_init_main_settings( $container, this_module_cid ) {
			var $main_tabs                = $container.find( '.tm-pb-options-tabs-links' ),
				$settings_tab             = $container.find( '.tm-pb-options-tab' ),

				$tm_affect_fields         = $container.find( '.tm-pb-affects' ),

				$main_custom_margin_field = $container.find( '.tm_custom_margin_main' ),
				$custom_margin_fields     = $container.find( '.tm_custom_margin' ),

				$font_select              = $container.find( 'select.tm-pb-font-select' ),
				$font_style_fields        = $container.find( '.tm_builder_font_style' ),

				$range_field              = $container.find( '.tm-pb-range' ),
				$range_input              = $container.find( '.tm-pb-range-input' ),

				$advanced_tab             = $container.find( '.tm-pb-options-tab-advanced' ),
				$advanced_tab_settings    = $advanced_tab.find( '.tm-pb-main-setting' ),

				$general_tab              = $container.find( '.tm-pb-options-tab-general' ),
				$general_tab_settings     = $general_tab.find( '.tm-pb-main-setting' ),

				tabs_settings             = [ $general_tab_settings, $advanced_tab_settings ],

				$custom_color_picker        = $container.find( '.tm-pb-custom-color-picker' ),
				$custom_color_choose_button = $container.find( '.tm-pb-choose-custom-color-button' ),

				$yes_no_button_wrapper = $container.find( '.tm_pb_yes_no_button_wrapper' ),
				$yes_no_button         = $container.find( '.tm_pb_yes_no_button' ),
				$yes_no_select         = $container.find( 'select' ),
				$validate_unit_field   = $container.find( '.tm-pb-validate-unit' ),
				$transparent_bg_option = $container.find( '#tm_pb_transparent_background' ),

				$regular_input = $container.find( 'input.regular-text.tm_pb_setting_mobile' ),
				hidden_class = 'tm_pb_hidden',

				$custom_css_option = $container.find( '.tm-pb-options-tab-custom_css .tm-pb-option' )

				$mobile_settings_toggle = $container.find( '.tm-pb-mobile-settings-toggle' ),
				$mobile_settings_tabs   = $container.find( '.tm_pb_mobile_settings_tabs' ),

				$checkboxes_set = $container.find( '.tm_pb_checkboxes_wrapper' ),
				$checkbox       = $checkboxes_set.find( 'input[type="checkbox"]' );

			if ( $mobile_settings_tabs.length ) {
				$mobile_settings_tabs.each( function() {
					var $this_tabs = $( this ),
						$this_option_container = $this_tabs.closest( '.tm-pb-option' ),
						last_edited_field = $this_option_container.find( '.tm_pb_mobile_last_edited_field' ).val(),
						$mobile_fields = $this_option_container.find( '.tm_pb_setting_mobile' );

					// update defaults for the mobile settings
					if ( $mobile_fields.length ) {
						$mobile_fields.each( function() {
							var $this_field = $( this ),
								this_device = $this_field.data( 'device' ),
								has_saved_value = 'desktop' !== this_device && typeof $this_field.data( 'has_saved_value' ) !== 'undefined' ? $this_field.data( 'has_saved_value' ) : 'no',
								input_type = $this_field.attr( 'type' ),
								new_default;

							if ( 'laptop' === this_device ) {
								new_default = $this_field.siblings( 'input[type="' + input_type + '"].tm_pb_setting_mobile_desktop' ).val();
							} else if ( 'tablet' === this_device ) {
								new_default = $this_field.siblings( 'input[type="' + input_type + '"].tm_pb_setting_mobile_laptop' ).val();
							} else if ( 'phone' === this_device ) {
								new_default = $this_field.siblings( 'input[type="' + input_type + '"].tm_pb_setting_mobile_tablet' ).val();
							}

							// no need to update anything for desktop
							if ( 'desktop' === this_device ) {
								return;
							}

							if ( 'no' === has_saved_value ) {
								$this_field.val( new_default );
							}

							$this_field.data( 'default', new_default );
						});
					}

					if ( typeof last_edited_field !== 'undefined' && '' !== last_edited_field ) {
						last_edited_options = last_edited_field.split( '|' );

						if ( typeof last_edited_options[0] === 'undefined' || 'on' !== last_edited_options[0] ) {
							return;
						}

						$this_option_container.find( '.tm-pb-mobile-settings-toggle' ).addClass( 'tm-pb-mobile-icon-visible tm-pb-mobile-settings-active' );
						$this_option_container.toggleClass( 'tm_pb_has_mobile_settings' );


						if ( typeof last_edited_options[1] !== 'undefined' && '' !== last_edited_options[1] ) {
							tm_pb_open_responsive_tab( $this_option_container, last_edited_options[1] );
						}
					}
				});
			}

			$mobile_settings_toggle.click( function() {
				var $this_toggle = $( this ),
					$this_option_container = $this_toggle.closest( '.tm-pb-option' ),
					$last_edited_field = $this_option_container.find( '.tm_pb_mobile_last_edited_field' ),
					last_edited_field_val = $last_edited_field.val(),
					last_edited_options = '' !== last_edited_field_val ? last_edited_field_val.split( '|' ) : [],
					active_tab = typeof last_edited_options[1] !== 'undefined' && '' !== last_edited_options[1] ? last_edited_options[1] : 'desktop',
					$reset_button = $this_option_container.find( '.tm-pb-reset-setting' );

				$this_toggle.toggleClass( 'tm-pb-mobile-settings-active' );
				$this_option_container.toggleClass( 'tm_pb_has_mobile_settings' );

				// Set the last edited tab or desktop tab
				tm_pb_open_responsive_tab( $this_option_container, active_tab );

				// Add tm_pb_animate_options class to apply css animation and remove it after 500ms
				$this_option_container.addClass( 'tm_pb_animate_options' );
				setTimeout( function() {
					$this_option_container.removeClass( 'tm_pb_animate_options' );
				}, 500 );

				if ( $this_option_container.hasClass( 'tm_pb_has_mobile_settings' ) ) {
					$reset_button.data( 'device', active_tab );
					last_edited_options[0] = 'on';
				} else {
					$reset_button.data( 'device', 'all' );
					last_edited_options[0] = 'off';
					tm_pb_open_responsive_tab( $this_option_container, 'desktop' );
				}

				last_edited_options[1] = typeof last_edited_options[1] !== 'undefined' ? last_edited_options[1] : '';

				$last_edited_field.val( last_edited_options[0] + '|' + last_edited_options[1] );

				return false;
			});

			$mobile_settings_tabs.find( 'a' ).click( function() {
				var $this_button = $( this ),
					$option_container = $this_button.closest( '.tm-pb-option-container' ),
					selected_tab = $this_button.data( 'settings_tab' ),
					$last_edited_field = $option_container.find( '.tm_pb_mobile_last_edited_field' );

				$this_button.closest( '.tm_pb_mobile_settings_tabs' ).find( 'a' ).removeClass( 'tm_pb_mobile_settings_active_tab' );
				$this_button.addClass( 'tm_pb_mobile_settings_active_tab' );

				$option_container.find( '.tm_pb_setting_mobile' ).removeClass( 'tm_pb_setting_mobile_active' );
				$option_container.find( '.tm_pb_setting_mobile_' + selected_tab ).addClass( 'tm_pb_setting_mobile_active' );

				$option_container.find( '.tm-pb-reset-setting' ).data( 'device', selected_tab );

				$last_edited_field.val( 'on|' + selected_tab );

				tm_pb_update_reset_button( $option_container );

				return false;
			});

			if ( $checkboxes_set.length ) {
				$checkboxes_set.each( function() {
					var $this_container = $( this ),
						value = $this_container.find( 'input.tm-pb-main-setting' ).val(),
						checkboxes = $this_container.find( 'input[type="checkbox"]' ),
						values_array,
						i;

					if ( '' !== value ) {
						values_array = value.split( '|' );
						i = 0;

						checkboxes.each( function() {
							if ( 'on' === values_array[ i ] ) {
								var $this_checkbox = $( this );
								$this_checkbox.prop( 'checked', true );
							}
							i++;
						});
					}

				});
			}

			$checkbox.click( function() {
				var $this_checkbox = $( this ),
					current_checkbox_class = $( this ).attr( 'class' ),
					$this_container = $this_checkbox.closest( '.tm_pb_checkboxes_wrapper' ),
					$disabled_option_field = $this_container.find( '.tm_pb_disabled_option' ),
					$all_checkboxes = $this_container.find( 'input[type="checkbox"]' ),
					$value_field = $this_container.find( 'input.tm-pb-main-setting' ),
					new_value = true === $this_checkbox.prop( 'checked' ) ? 'on' : 'off',
					i = 0,
					empty_values_array = [],
					checkbox_order,
					values_array;

					$all_checkboxes.each( function() {
						if ( $( this ).hasClass( current_checkbox_class ) ) {
							checkbox_order = i;
						}
						i++;
						empty_values_array.push( '' );
					});

					if ( '' !== $value_field.val() ) {
						values_array = $value_field.val().split( '|' );
					} else {
						values_array = empty_values_array;
					}

					values_array[ checkbox_order ] = new_value;

					$value_field.val( values_array.join( '|' ) );

					// need to check additional option for 'disable_on'
					if ( $disabled_option_field.length ) {
						if ( 'on' === values_array[0] && 'on' === values_array[1] && 'on' === values_array[2] ) {
							$disabled_option_field.val( 'on' );
						} else {
							$disabled_option_field.val( 'off' );
						}
					}
			});

			if ( typeof window.switchEditors !== 'undefined' ) {
				$container.find( '.wp-switch-editor' ).click( function() {
					var $this_el = $(this),
						editor_mode;

					editor_mode = $this_el.hasClass( 'switch-tmce' ) ? 'tinymce' : 'html';

					if ( editor_mode === 'tinymce' ) {
						tm_pb_maybe_apply_wpautop_to_models();
					}

					window.switchEditors.go( 'content', editor_mode );
				} );
			}

			// fix the issue with disapperaing line breaks in visual editor
			tm_pb_maybe_apply_wpautop_to_models();

			$regular_input.on( 'input change' , function() {
				tm_pb_update_mobile_defaults( $( this ) );
			});

			$custom_color_picker.each( function() {
				var $this_color_picker      = $(this),
					this_color_picker_value = $this_color_picker.val(),
					$container              = $this_color_picker.closest( '.tm-pb-custom-color-container' ),
					$choose_color_button    = $container.siblings( '.tm-pb-choose-custom-color-button' ),
					$main_color_picker      = $container.find( '.tm-pb-color-picker-hex' );

				if ( '' === this_color_picker_value ) {
					return true;
				}

				$container.removeClass( hidden_class );
				$choose_color_button.addClass( hidden_class );

				$main_color_picker.wpColorPicker( 'color', this_color_picker_value );
			} );

			$custom_color_choose_button.click( function() {
				var $this_el = $(this),
					$color_picker_container = $this_el.siblings( '.tm-pb-custom-color-container' ),
					$color_picker = $color_picker_container.find( '.tm-pb-color-picker-hex' ),
					$hidden_color_input = $color_picker_container.find( '.tm-pb-custom-color-picker' );

				$this_el.addClass( hidden_class );
				$color_picker_container.removeClass( hidden_class );

				$hidden_color_input.val( $color_picker.wpColorPicker( 'color' ) );

				return false;
			} );

			// calculate the value for transparent bg option if plugin activated
			if ( $transparent_bg_option.length && tm_pb_options.is_plugin_used ) {
				var is_default_value = typeof $transparent_bg_option.data( 'default' ) !== 'undefined' && 'default' === $transparent_bg_option.data( 'default' ) ? true : false,
					bg_color_option_value = $container.find( '#tm_pb_background_color' ).val();

				// default value for the option should be yes if custom color is not defined
				if ( is_default_value && '' === bg_color_option_value ) {
					$transparent_bg_option.val( 'on' );
					$transparent_bg_option.trigger( 'change' );
				}
			}

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

			$main_tabs.find( 'li a' ).click( function() {
				var $this_el              = $(this),
					tab_index             = $this_el.closest( 'li' ).index(),
					$links_container      = $this_el.closest( 'ul' ),
					$tabs                 = $links_container.siblings( '.tm-pb-options-tabs' ),
					active_link_class     = 'tm-pb-options-tabs-links-active',
					$active_tab_link      = $links_container.find( '.' + active_link_class ),
					active_tab_link_index = $active_tab_link.index(),
					$current_tab          = $tabs.find( '.tm-pb-options-tab' ).eq( active_tab_link_index ),
					$next_tab             = $tabs.find( '.tm-pb-options-tab' ).eq( tab_index ),
					fade_speed            = 300;

				if ( active_tab_link_index !== tab_index ) {
					$next_tab.css( { 'display' : 'none', opacity : 0 } );

					$current_tab.css( { 'display' : 'block', 'opacity' : 1 } ).stop( true, true ).animate( { opacity : 0 }, fade_speed, function(){
						$(this).css( 'display', 'none' );

						$next_tab.css( { 'display' : 'block', 'opacity' : 0 } ).stop( true, true ).animate( { opacity : 1 }, fade_speed, function() {
							var $this = $(this);

							//tm_pb_update_affected_fields( $tm_affect_fields );

							if ( ! $this.find( '.tm-pb-option:visible' ).length ) {
								$this.append( '<p class="tm-pb-all-options-hidden">' + tm_pb_options.all_tab_options_hidden + '<p>' );
							} else {
								$('.tm-pb-all-options-hidden').remove();
							}

							$main_tabs.trigger( 'tm_pb_main_tab:changed' );
						} );
					} );

					$active_tab_link.removeClass( active_link_class );

					$links_container.find( 'li' ).eq( tab_index ).addClass( active_link_class );

					// always scroll to the top when tab opened
					$( '.tm-pb-options-tabs' ).animate( { scrollTop :  0 }, 400, 'swing' );
				}

				return false;
			} );

			$settings_tab.each( function() {
				var $this_tab          = $(this),
					$toggles           = $this_tab.find( '.tm-pb-options-toggle-enabled' ),
					open_class         = 'tm-pb-option-toggle-content-open',
					closed_class       = 'tm-pb-option-toggle-content-closed',
					content_area_class = 'tm-pb-option-toggle-content',
					animation_speed    = 300;

				$toggles.find( 'h3' ).click( function() {
					var $this_el                  = $(this),
						$content_area             = $this_el.siblings( '.' + content_area_class ),
						$container                = $this_el.closest( '.tm-pb-options-toggle-container' ),
						$open_toggle              = $toggles.filter( '.' + open_class ),
						$open_toggle_content_area = $open_toggle.find( '.' + content_area_class );

					if ( $container.hasClass( open_class ) ) {
						return;
					}

					$open_toggle.removeClass( open_class ).addClass( closed_class );
					$open_toggle_content_area.slideToggle( animation_speed );

					$container.removeClass( closed_class ).addClass( open_class );
					$content_area.slideToggle( animation_speed, function() {
						tm_pb_update_affected_fields( $tm_affect_fields );
					} );
				} );
			} );

			if ( $main_custom_margin_field.length ) {
				$main_custom_margin_field.each( function() {
					tm_pb_process_custom_margin_field( $( this ) );
				});

				$main_custom_margin_field.on( 'tm_main_custom_margin:change', function() {
					tm_pb_process_custom_margin_field( $(this) );
				} );
			}

			$custom_margin_fields.change( function() {
				var $this_el    = $(this),
					this_device = typeof $this_el.data( 'device' ) !== 'undefined' ? $this_el.data( 'device' ) : 'all',
					$container  = $this_el.closest( '.tm_custom_margin_padding' ),
					$main_container = $container.closest( '.tm-pb-option-container' ),
					$mobile_toggle = $main_container.find( '.tm-pb-mobile-settings-toggle' ),
					$main_field = 'all' === this_device ? $container.find( '.tm_custom_margin_main' ) : $container.find( '.tm_custom_margin_main.tm_pb_setting_mobile_' + this_device ),
					fields_selector = 'all' === this_device ? '.tm_custom_margin' : '.tm_custom_margin.tm_pb_setting_mobile_' + this_device,
					margin      = '';

				$container.find( fields_selector ).each( function() {
					margin += $.trim( tm_pb_sanitize_input_unit_value( $(this).val(), $(this).hasClass( 'auto_important' ) ) ) + '|';
				} );

				margin = margin.slice( 0, -1 );

				if ( '|||' === margin ) {
					margin = '';
				} else {
					$mobile_toggle.addClass( 'tm-pb-mobile-icon-visible' );
				}

				$main_field.val( margin ).trigger( 'tm_pb_setting:change' );

				tm_pb_update_mobile_defaults( $main_field );
			} );

			$font_style_fields.click( function() {
				var $this_el = $(this);

				$this_el.toggleClass( 'tm_font_style_active' );

				$font_select.trigger( 'change' );

				return false;
			} );

			$font_select.change( function() {
				var $this_el           = $(this),
					$main_option       = $this_el.siblings( 'input.tm-pb-font-select' ),
					$style_options     = $this_el.siblings( '.tm_builder_font_styles' ),
					$bold_option       = $style_options.find( '.tm_builder_bold_font' ),
					$italic_option     = $style_options.find( '.tm_builder_italic_font' ),
					$uppercase_option  = $style_options.find( '.tm_builder_uppercase_font' ),
					$underline_option  = $style_options.find( '.tm_builder_underline_font' ),
					style_active_class = 'tm_font_style_active',
					font_name          = $this_el.val(),
					result             = '';

				result += font_name !== 'default' ? $.trim( font_name ) : '';

				result += '|';

				if ( $bold_option.hasClass( style_active_class ) ) {
					result += 'on';
				}

				result += '|';

				if ( $italic_option.hasClass( style_active_class ) ) {
					result += 'on';
				}

				result += '|';

				if ( $uppercase_option.hasClass( style_active_class ) ) {
					result += 'on';
				}

				result += '|';

				if ( $underline_option.hasClass( style_active_class ) ) {
					result += 'on';
				}

				$main_option.val( result ).trigger( 'change' );
			} );

			$font_select.each( function() {
				tm_pb_setup_font_setting( $(this), false );
			} );

			$range_field.on( 'input change', function() {
				var $this_el          = $(this),
					this_device       = typeof $this_el.data( 'device' ) === 'undefined' ? 'all' : $this_el.data( 'device' ),
					range_value       = $this_el.val(),
					$range_input      = 'all' === this_device ? $this_el.siblings( '.tm-pb-range-input' ) : $this_el.siblings( '.tm-pb-range-input.tm_pb_setting_mobile_' + this_device ),
					initial_value_set = $range_input.data( 'initial_value_set' ) || false,
					range_input_value = tm_pb_sanitize_input_unit_value( $.trim( $range_input.val() ), false, 'no_default_unit' ),
					number,
					length;

				if ( range_input_value === '' && ! initial_value_set ) {
					$this_el.val( 0 );
					$range_input.data( 'initial_value_set', true );

					return;
				}

				number = parseFloat( range_input_value );

				range_input_value += '';

				length = $.trim( range_input_value.replace( number, '' ) );

				if ( length !== '' ) {
					range_value += length;
				}

				$range_input.val( range_value );

				//tm_pb_update_mobile_defaults( $this_el, range_value );

			} );

			if ( $range_field.length ) {
				$range_field.each( function() {
					var $this_el          = $(this),
						this_device       = typeof $this_el.data( 'device' ) === 'undefined' ? 'all' : $this_el.data( 'device' ),
						default_value     = typeof $this_el.data( 'default_inherited' ) !== 'undefined' ? $.trim( $this_el.data( 'default_inherited' ) ) : $.trim( $this_el.data( 'default' ) ),
						$range_input      = 'all' === this_device ? $this_el.siblings( '.tm-pb-range-input' ) : $this_el.siblings( '.tm-pb-range-input.tm_pb_setting_mobile_' + this_device ),
						range_input_value = $.trim( $range_input.val() );

					if ( range_input_value === '' ) {
						if ( default_value !== '' ) {
							$range_input.val( default_value );

							default_value = parseFloat( default_value ) || 0;
						}

						$this_el.val( default_value );
					}

					// Define defaults for tablet and phone settings on load
					if ( 'laptop' === this_device ) {
						var $desktop_field = $this_el.siblings( '.tm-pb-range-input.tm_pb_setting_mobile_desktop' ),
							new_laptop_default = $desktop_field.val();

						$this_el.data( 'default', parseFloat( new_laptop_default ) );
						$range_input.data( 'default', new_laptop_default );

					} else if ( 'tablet' === this_device ) {
						var $desktop_field = $this_el.siblings( '.tm-pb-range-input.tm_pb_setting_mobile_laptop' ),
							new_tablet_default = $desktop_field.val();

						$this_el.data( 'default', parseFloat( new_tablet_default ) );
						$range_input.data( 'default', new_tablet_default );

					} else if ( 'phone' === this_device ) {
						var $tablet_field = $this_el.siblings( '.tm-pb-range-input.tm_pb_setting_mobile_tablet' ),
							new_phone_default = $tablet_field.val();

						$this_el.data( 'default', parseFloat( new_phone_default ) );
						$range_input.data( 'default', new_phone_default );
					}

				} );
			}

			$range_input.on( 'keyup change', function() {
				var $this_el      = $(this),
					this_device   = typeof $this_el.data( 'device' ) === 'undefined' ? 'all' : $this_el.data( 'device' ),
					this_value    = $this_el.val(),
					$range_slider = 'all' === this_device ? $this_el.siblings( '.tm-pb-range' ) : $this_el.siblings( '.tm-pb-range.tm_pb_setting_mobile_' + this_device ),
					slider_value;

				slider_value = parseFloat( this_value ) || 0;

				$range_slider.val( slider_value ).trigger( 'tm_pb_setting:change' );

				//tm_pb_update_mobile_defaults( $this_el );
			} );

			if ( $validate_unit_field.length ) {
				$validate_unit_field.each( function() {
					var $this_el = $(this),
						value    = tm_pb_sanitize_input_unit_value( $.trim( $this_el.val() ) );

					$this_el.val( value );
				} );
			}

			tabs_settings.forEach( function( $single_tab ) {
				if ( $single_tab.length ) {
					$single_tab.on( 'change tm_pb_setting:change tm_main_custom_margin:change', function() {
						var $this_el         = $(this),
							this_device       = typeof $this_el.data( 'device' ) === 'undefined' ? 'all' : $this_el.data( 'device' ),
							$option_container = $this_el.closest( '.tm-pb-option-container' ),
							$reset_button    = $option_container.find( '.tm-pb-reset-setting' ),
							is_range_option  = $this_el.hasClass( 'tm-pb-range' ),
							$current_element = is_range_option && 'all' === this_device ? $this_el.siblings( '.tm-pb-range-input' ) : $this_el,
							$current_element = is_range_option && 'all' !== this_device ? $this_el.siblings( '.tm-pb-range-input.tm_pb_setting_mobile_' + this_device ) : $current_element,
							default_value    = tm_pb_get_default_setting_value( $current_element ),
							current_value    = $current_element.val(),
							is_global        = $current_element.data( 'global' );
							$mobile_toggle   = $option_container.find( '.tm-pb-mobile-settings-toggle' );

						if ( 'undefined' === typeof is_global ) {
							is_global = 0;
						}

						if ( $current_element.hasClass( 'tm_pb_setting_mobile' ) && ! $current_element.hasClass( 'tm_pb_setting_mobile_active' ) ) {
							// make the mobile toggle icon visible if any option is not default
							if ( ( current_value !== default_value && ! is_range_option ) || ( is_range_option && current_value !== default_value + 'px' && current_value !== default_value ) ) {
								$mobile_toggle.addClass( 'tm-pb-mobile-icon-visible' );
							}

							// do not proceed if mobile settings are not opened and we're processing mobile field
							return;
						}

						if ( $current_element.is( 'select' ) && default_value === '' && $current_element.prop( 'selectedIndex' ) === 0 ) {
							$reset_button.removeClass( 'tm-pb-reset-icon-visible' );

							return;
						}

						// range option default value can be defined without units, so compare current value with default and default + 'px' for range option
						if ( ( current_value !== default_value && ! is_range_option ) || ( is_range_option && current_value !== default_value + 'px' && current_value !== default_value ) || is_global ) {
							setTimeout( function() {
								$reset_button.addClass( 'tm-pb-reset-icon-visible' );
							}, 50 );

							$mobile_toggle.addClass( 'tm-pb-mobile-icon-visible' );
						} else {
							$reset_button.removeClass( 'tm-pb-reset-icon-visible' );
							if ( ! $mobile_toggle.hasClass( 'tm-pb-mobile-settings-active' ) ) {
								$mobile_toggle.removeClass( 'tm-pb-mobile-icon-visible' );
							}
						}
					} );

					$single_tab.trigger( 'change' );

					$container.find( '.tm-pb-main-settings .tm_pb_options_tab_advanced a' ).append( '<span class="tm-pb-reset-settings"></span>' );

					$container.find( '.tm-pb-reset-settings' ).on( 'click', function() {
						tm_pb_create_prompt_modal( 'reset_advanced_settings', $single_tab );
					} );
				}
			} );

			$container.find( '.tm-pb-reset-setting' ).on( 'click', function() {
				tm_pb_reset_element_settings( $(this) );
			} );


			if ( $tm_affect_fields.length ) {
				$tm_affect_fields.change( function() {
					var $this_field         = $(this), // this field value affects another field visibility
						new_field_value     = $this_field.val(),
						new_field_value_number = parseInt( new_field_value ),
						$affected_fields     = $( $this_field.data( 'affects' ) ),
						this_field_tab_index = $this_field.closest( '.tm-pb-options-tab' ).index();

					$affected_fields.each( function() {

						var $affected_field          = $(this),
							$affected_container      = $affected_field.closest( '.tm-pb-option' ),
							is_text_trigger          = ( ! $this_field.hasClass('tm-pb-trigger') ) && 'text' === $this_field.attr( 'type' ) && typeof show_if_not === 'undefined' && typeof show_if === 'undefined', // need to know if trigger is text field
							show_if                  = $affected_container.data( 'depends_show_if' ) || 'on',
							show_if_not              = is_text_trigger ? '' : $affected_container.data( 'depends_show_if_not' ),
							show                     = show_if === new_field_value || ( typeof show_if_not !== 'undefined' && show_if_not !== new_field_value ),
							affected_field_tab_index = $affected_field.closest( '.tm-pb-options-tab' ).index(),
							$dependant_fields        = $affected_container.find( '.tm-pb-affects' ); // affected field might affect some other fields as well

						// make sure hidden text fields do not break the visibility of option
						if ( is_text_trigger && ! $this_field.is( ':visible' ) ) {
							return;
						}

						// if the affected field should be displayed, but the field that affects it is not visible, don't show the affected field ( it only can happen on settings page load )
						if ( this_field_tab_index === affected_field_tab_index && show && ! $this_field.is( ':visible' ) && ( ! $this_field.hasClass('tm-pb-trigger') ) ) {
							show = false;
						}

						// shows or hides the affected field container
						$affected_container.toggle( show ).addClass( 'tm_pb_animate_affected' );

						setTimeout( function() {
							$affected_container.removeClass( 'tm_pb_animate_affected' );
						}, 500 );

						// if the affected field affects other fields, find out if we need to hide/show them
						if ( $dependant_fields.length ) {
							var $inner_affected_elements = $( $dependant_fields.data( 'affects' ) );

							if ( ! $affected_container.is( ':visible' ) ) {
								// if the main affected field is hidden, hide all fields it affects

								$inner_affected_elements.each( function() {
									$(this).closest( '.tm-pb-option' ).hide();
								} );
							} else {
								// if the main affected field is displayed, trigger the change event for all fields it affects

								$affected_field.trigger( 'change' );
							}
						}
					} );
				} );

				// trigger change event for all dependant ( affected ) fields to show on settings page load
				setTimeout( function() {
					// make all settings visible to properly enable all affected fields
					$settings_tab.css( { 'display' : 'block' } );

					tm_pb_update_affected_fields( $tm_affect_fields );

					// After all affected fields is being processed return all tabs to the initial state
					$settings_tab.css( { 'display' : 'none' } );
					tm_pb_open_current_tab();
				}, 100 );
			}

			// update the unique class for opened module when custom css tab opened
			$container.find( '.tm-pb-options-tabs-links' ).on( 'tm_pb_main_tab:changed', function() {
				var $custom_css_tab = $( '.tm-pb-options-tabs-links' ).find( '.tm_pb_options_tab_custom_css' ),
					$module_order_placeholder = $( '.tm-pb-options-tab-custom_css' ).find( '.tm_pb_module_order_placeholder' ),
					opened_module,
					module_order;

				if ( $custom_css_tab.hasClass( 'tm-pb-options-tabs-links-active' ) ) {
					var opened_module = TM_PageBuilder_Modules.findWhere( { cid : this_module_cid } );

					module_order = typeof opened_module.attributes.module_order !== 'undefined' ? opened_module.attributes.module_order : '';

					// replace empty placeholders with module order value if any
					if ( $module_order_placeholder.length ) {
						$module_order_placeholder.replaceWith( module_order );
					}
				}
			});

			// show/hide css selector field for the custom css options on focus
			if ( $custom_css_option.length ) {
				$custom_css_option.focusin( function() {
					var $this = $( this ),
						$this_main_container = $this.closest( '.tm-pb-option' ),
						$css_selector_holder = $this_main_container.find( 'label > span' ),
						$other_inputs_selectors = $this_main_container.siblings().find( 'label > span' );

					// show the css selector span for option with focus
					if ( $css_selector_holder.length ) {
						$css_selector_holder.removeClass( 'tm_pb_hidden_css_selector' );
						$css_selector_holder.css( { 'display' : 'inline-block' } );
						$css_selector_holder.addClass( 'tm_pb_visible_css_selector' );
					}

					// hide the css selector span for other options
					if ( $other_inputs_selectors.length ) {
						$other_inputs_selectors.removeClass( 'tm_pb_visible_css_selector' );
						$other_inputs_selectors.addClass( 'tm_pb_hidden_css_selector' );

						setTimeout( function() {
							$other_inputs_selectors.css( { 'display' : 'none' } );
							$other_inputs_selectors.removeClass( 'tm_pb_hidden_css_selector' );
						}, 200 );
					}
				});
			}
		}

		window.tm_pb_get_default_setting_value = function tm_pb_get_default_setting_value( $element ) {
			var default_data_name = $element.hasClass( 'tm-pb-color-picker-hex' ) ? 'default-color' : 'default',
				default_value;

			// need to check for 'undefined' type instead of $element.data( default_data_name ) || '' because default value maybe 0
			default_value = typeof $element.data( default_data_name ) !== 'undefined' ? $element.data( default_data_name ) : '';
			// convert any type to string
			default_value = default_value + '';

			return default_value;
		}

		/*
		 * Reset icon or a setting field can be used as $element
		 */
		window.tm_pb_reset_element_settings = function tm_pb_reset_element_settings( $element ) {
			var $this_el          = $element,
				$option_container = $this_el.closest( '.tm-pb-option-container' ),
				$main_container   = $option_container.closest( '.tm-pb-option' ),
				this_device       = typeof $this_el.data( 'device' ) === 'undefined' || ! $main_container.hasClass( 'tm_pb_has_mobile_settings' ) ? 'all' : $this_el.data( 'device' ),
				$main_setting     = 'all' === this_device ? $option_container.find( '.tm-pb-main-setting' ) : $option_container.find( '.tm-pb-main-setting.tm_pb_setting_mobile_' + this_device ),
				default_value     = tm_pb_get_default_setting_value( $main_setting );

			if ( $main_setting.is( 'select' ) && default_value === '' ) {
				$main_setting.prop( 'selectedIndex', 0 ).trigger( 'change' );

				return;
			}

			if ( $main_setting.hasClass( 'tm-pb-custom-color-picker' ) ) {
				tm_pb_custom_color_remove( $this_el );

				return;
			}

			if ( $main_setting.hasClass( 'tm-pb-color-picker-hex' ) ) {
				$main_setting.wpColorPicker( 'color', default_value );

				if ( default_value === '' ) {
					$main_setting.siblings('.wp-picker-clear').trigger('click');
				}

				if ( ! $this_el.hasClass( 'tm-pb-reset-setting' ) ) {
					$this_el = $option_container.find( '.tm-pb-reset-setting' );
				}

				$this_el.hide();

				return;
			}

			if ( $main_setting.hasClass( 'tm-pb-font-select' ) ) {
				tm_pb_setup_font_setting( $main_setting, true );
			}

			if ( $main_setting.hasClass( 'tm-pb-range' ) ) {
				$main_setting = 'all' === this_device ? $this_el.siblings( '.tm-pb-range-input' ) : $this_el.siblings( '.tm-pb-range-input.tm_pb_setting_mobile_' + this_device );
				default_value = tm_pb_get_default_setting_value( $main_setting );
			}

			$main_setting.val( default_value );

			$main_setting.data( 'has_saved_value', 'no' );

			if ( $main_setting.hasClass( 'tm_custom_margin_main' ) ) {
				$main_setting.trigger( 'tm_main_custom_margin:change' );
			} else {
				$main_setting.trigger( 'change' );
			}
		}

		window.tm_pb_sanitize_input_unit_value = function tm_pb_sanitize_input_unit_value( value, auto_important, default_unit ) {
			var value = typeof value === 'undefined' ? '' : value,
				valid_one_char_units  = [ "%" ],
				valid_two_chars_units = [ "em", "px", "cm", "mm", "in", "pt", "pc", "ex", "vh", "vw" ],
				important             = "!important",
				important_length      = important.length,
				has_important         = false,
				value_length          = value.length,
				auto_important       = _.isUndefined( auto_important ) ? false : auto_important,
				unit_value,
				result;

			if ( value === '' ) {
				return '';
			}

			// check for !important
			if ( value.substr( ( 0 - important_length ), important_length ) === important ) {
				has_important = true;
				value_length = value_length - important_length;
				value = value.substr( 0, value_length ).trim();
			}

			if ( $.inArray( value.substr( -1, 1 ), valid_one_char_units ) !== -1 ) {
				unit_value = parseFloat( value ) + "%";

				// Re-add !important tag
				if ( has_important && ! auto_important ) {
					unit_value = unit_value + ' ' + important;
				}

				return unit_value;
			}

			if ( $.inArray( value.substr( -2, 2 ), valid_two_chars_units ) !== -1 ) {
				var unit_value = parseFloat( value ) + value.substr( -2, 2 );

				// Re-add !important tag
				if ( has_important && ! auto_important ) {
					unit_value = unit_value + ' ' + important;
				}

				return unit_value;
			}

			if( isNaN( parseFloat( value ) ) ) {
				return '';
			}

			result = parseFloat( value );
			if ( _.isUndefined( default_unit ) || 'no_default_unit' !== default_unit ) {
				result += 'px';
			}

			// Return and automatically append px (default value)
			return result;
		}

		window.tm_pb_process_custom_margin_field = function tm_pb_process_custom_margin_field( $element ) {
			var $this_field      = $element,
				this_device      = typeof $this_field.data( 'device' ) !== 'undefined' ? $this_field.data( 'device' ) : 'all',
				this_field_value = $this_field.val(),
				$container       = $this_field.closest( '.tm_custom_margin_padding' ),
				$main_container  = $container.closest( '.tm-pb-option-container' ),
				$mobile_toggle   = $main_container.find( '.tm-pb-mobile-settings-toggle' ),
				$margin_fields   = 'all' === this_device ? $container.find( '.tm_custom_margin' ) : $container.find( '.tm_custom_margin.tm_pb_setting_mobile_' + this_device ),
				show_mobile      = false,
				i = 0,
				margins;

			tm_pb_update_mobile_defaults( $element );

			if ( this_field_value !== '' && ! _.isUndefined( this_field_value ) ) {
				margins = this_field_value.split( '|' );

				// if we have more fields than saved values, then add missing ones considering that saved values are top and bottom padding/margin
				if ( $margin_fields.length > margins.length ) {
					// fill the 2nd and 4th positions with empty values
					margins.splice( 1, 0, '' );
					margins.push( '' );
				}

				$margin_fields.each( function() {
					var $this_field = $(this),
						field_index = $margin_fields.index( $this_field ),
						auto_important  = $this_field.hasClass( 'auto_important' ),
						corner_value = tm_pb_sanitize_input_unit_value( margins[ field_index ], auto_important );

					$this_field.val( corner_value );

					if ( '' !== corner_value ) {
						show_mobile = true;
					}
				} );

				if ( show_mobile ) {
					$mobile_toggle.addClass( 'tm-pb-mobile-icon-visible' );
				}
			} else {
				$margin_fields.each( function() {
					$(this).val( '' );
				} );
			}
		}

		window.tm_pb_setup_font_setting = function tm_pb_setup_font_setting( $element, reset ) {
			var $this_el           = $element,
				$container         = $this_el.parent('.tm-pb-option-container'),
				$main_option       = $container.find( 'input.tm-pb-font-select' ),
				$select_option     = $container.find( 'select.tm-pb-font-select' ),
				$style_options     = $container.find( '.tm_builder_font_styles' ),
				$bold_option       = $style_options.find( '.tm_builder_bold_font' ),
				$italic_option     = $style_options.find( '.tm_builder_italic_font' ),
				$uppercase_option  = $style_options.find( '.tm_builder_uppercase_font' ),
				$underline_option  = $style_options.find( '.tm_builder_underline_font' ),
				style_active_class = 'tm_font_style_active',
				font_value         = $.trim( $main_option.val() ),
				font_values;

			if ( reset ) {
				font_value = $.trim( $main_option.attr('data-default') );
			}

			if ( font_value !== '' ) {
				font_values = font_value.split( '|' );

				if ( font_values[0] !== '' ) {
					$select_option.val( font_values[0] );
				} else {
					$select_option.prop( 'selectedIndex', 0 );
				}

				if ( font_values[1] === 'on' ) {
					$bold_option.addClass( style_active_class );
				} else {
					$bold_option.removeClass( style_active_class );
				}

				if ( font_values[2] === 'on' ) {
					$italic_option.addClass( style_active_class );
				} else {
					$italic_option.removeClass( style_active_class );
				}

				if ( font_values[3] === 'on' ) {
					$uppercase_option.addClass( style_active_class );
				} else {
					$uppercase_option.removeClass( style_active_class );
				}

				if ( font_values[4] === 'on' ) {
					$underline_option.addClass( style_active_class );
				} else {
					$underline_option.removeClass( style_active_class );
				}
			} else {
				$select_option.prop( 'selectedIndex', 0 );
				$bold_option.removeClass( style_active_class );
				$italic_option.removeClass( style_active_class );
				$uppercase_option.removeClass( style_active_class );
				$underline_option.removeClass( style_active_class );
			}
		}

		window.tm_pb_hide_active_color_picker = function tm_pb_hide_active_color_picker( container ) {
			container.$( '.tm-pb-color-picker-hex:visible' ).each( function(){
				$(this).closest( '.wp-picker-container' ).find( '.wp-color-result' ).trigger( 'click' );
			} );
		}

		window.tm_builder_debug_message = function tm_builder_debug_message() {
			if ( tm_pb_options.debug && window.console ) {
				if ( 2 === arguments.length ) {
					console.log( arguments[0], arguments[1] );
				} else {
					console.log( arguments[0] );
				}
			}
		}

		window.tm_reinitialize_builder_layout = function tm_reinitialize_builder_layout() {
			TM_PageBuilder_App.saveAsShortcode();

			setTimeout( function(){
				var $builder_container = $( '#tm_pb_layout' ),
					builder_height     = $builder_container.innerHeight();

				$builder_container.css( { 'height' : builder_height } );

				content = tm_pb_get_content( 'content', true );

				TM_PageBuilder_App.removeAllSections();

				TM_PageBuilder_App.$el.find( '.tm_pb_section' ).remove();

				TM_PageBuilder_App.createLayoutFromContent( tm_prepare_template_content( content ), '', '', { is_reinit : 'reinit' } );

				$builder_container.css( { 'height' : 'auto' } );
			}, 600 );
		}

		window.tm_prepare_template_content = function tm_prepare_template_content( content ) {
			if ( -1 !== content.indexOf( '[tm_pb_' ) ) {
				if  ( -1 === content.indexOf( 'tm_pb_row' ) && -1 === content.indexOf( 'tm_pb_section' ) ) {
					if ( -1 === content.indexOf( 'tm_pb_fullwidth' ) ) {
						var saved_tabs = /(\\?")(.*?)\1/.exec( content );
						content = '[tm_pb_section template_type="module" skip_module="true"][tm_pb_row template_type="module" skip_module="true"][tm_pb_column type="4_4" saved_tabs="' + saved_tabs[2] + '"]' + content + '[/tm_pb_column][/tm_pb_row][/tm_pb_section]';
					} else {
						var saved_tabs = /(\\?")(.*?)\1/.exec( content );
						content = '[tm_pb_section fullwidth="on" template_type="module" skip_module="true" saved_tabs="' + saved_tabs[2] + '"]' + content + '[/tm_pb_section]';
					}
				} else if ( -1 === content.indexOf( 'tm_pb_section' ) ) {
					content = '[tm_pb_section template_type="row" skip_module="true"]' + content + '[/tm_pb_section]';
				}
			}

			return content;
		}

		window.generate_templates_view = function generate_templates_view( include_global, is_global, layout_type, append_to, module_width, specialty_cols, selected_category, previous_result ) {
			var is_global = '' === is_global ? 'not_global' : is_global;
			if ( typeof $tm_pb_templates_cache[layout_type + '_' + is_global + '_' + module_width + '_' + specialty_cols] !== 'undefined' ) {
				var templates_collection = new TM_PageBuilder.SavedTemplates( $tm_pb_templates_cache[layout_type + '_' + is_global + '_' + module_width + '_' + specialty_cols] ),
					templates_view = new TM_PageBuilder.TemplatesView( { collection: templates_collection, category: selected_category } );

				append_to.append( templates_view.render().el );

				if ( 'include_global' === include_global && 'not_global' === is_global ) {
					generate_templates_view( 'include_global', 'global', layout_type, append_to, module_width, specialty_cols, selected_category );
				} else {
					TM_PageBuilder_Events.trigger( 'tm-pb-loading:ended' );
					append_to.prepend( tm_pb_generate_layouts_filter( selected_category ) );
					$( '#tm_pb_select_category' ).data( 'attr', { include_global : include_global, is_global : '', layout_type : layout_type, append_to : append_to, module_width : module_width, specialty_cols : specialty_cols } );
				}
			} else {
				$.ajax( {
					type: "POST",
					url: tm_pb_options.ajaxurl,
					dataType: 'json',
					data:
					{
						action : 'tm_pb_get_saved_templates',
						tm_admin_load_nonce : tm_pb_options.tm_admin_load_nonce,
						tm_is_global : is_global,
						tm_post_type : tm_pb_options.post_type,
						tm_layout_type : layout_type,
						tm_module_width : module_width,
						tm_specialty_columns : specialty_cols
					},
					beforeSend : function() {
						TM_PageBuilder_Events.trigger( 'tm-pb-loading:started' );
					},
					complete : function() {
						if ( 'include_global' !== include_global || ( 'include_global' === include_global && 'global' === is_global )  ) {
							TM_PageBuilder_Events.trigger( 'tm-pb-loading:ended' );
							append_to.prepend( tm_pb_generate_layouts_filter( selected_category ) );
							$( '#tm_pb_select_category' ).data( 'attr', { include_global : include_global, is_global : '', layout_type : layout_type, append_to : append_to, module_width : module_width, specialty_cols : specialty_cols } );
						}
					},
					success: function( data ) {
						var request_result = '';

						if ( typeof data.error !== 'undefined' ) {
							//show error message only for global section or when global section wasn't included
							if ( ( 'include_global' === include_global && 'global' === is_global && 'success' !== previous_result ) || 'include_global' !== include_global ) {
								append_to.append( '<ul><li>' + data.error + '</li></ul>');
								request_result = 'fail';
							}
						} else {
							var templates_collection = new TM_PageBuilder.SavedTemplates( data ),
								templates_view = new TM_PageBuilder.TemplatesView( { collection: templates_collection } );

							$tm_pb_templates_cache[layout_type + '_' + is_global + '_' + module_width + '_' + specialty_cols] = data;
							append_to.append( templates_view.render().el );
							request_result = 'success';
						}

						if ( 'include_global' === include_global && 'not_global' === is_global ) {
							generate_templates_view( 'include_global', 'global', layout_type, append_to, module_width, specialty_cols, selected_category, request_result );
						}
					}
				} );
			}
		}

		window.tm_pb_generate_layouts_filter = function tm_pb_generate_layouts_filter( selected_category ) {
			var all_cats        = $.parseJSON( tm_pb_options.layout_categories ),
				$cats_selector  = '<select id="tm_pb_select_category">',
				selected_option = 'all' === selected_category || '' === selected_category ? ' selected' : '';

				$cats_selector += '<option value="all"' + selected_option + '>' + tm_pb_options.all_cat_text + '</option>';

				if( ! $.isEmptyObject( all_cats ) ) {

					$.each( all_cats, function( i, single_cat ) {
						if ( ! $.isEmptyObject( single_cat ) ) {
							selected_option = selected_category === single_cat.slug ? ' selected' : '';
							$cats_selector += '<option value="' + single_cat.slug + '"' + selected_option + '>' + single_cat.name + '</option>';
						}
					});
				}

				$cats_selector += '</select>';

				return $cats_selector;
		}

		// function to load saved layouts, it works differently than loading saved rows, sections and modules, so we need a separate function
		window.tm_load_saved_layouts = function tm_load_saved_layouts( layout_type, container_class, $this_el, post_type ) {
			if ( typeof $tm_pb_templates_cache[layout_type + '_layouts'] !== 'undefined' ) {
				$this_el.find( '.tm-pb-main-settings.' + container_class ).append( $tm_pb_templates_cache[layout_type + '_layouts'] );
			} else {
				$.ajax( {
					type: "POST",
					url: tm_pb_options.ajaxurl,
					data:
					{
						action : 'tm_pb_show_all_layouts',
						tm_layouts_built_for_post_type: post_type,
						tm_admin_load_nonce : tm_pb_options.tm_admin_load_nonce,
						tm_load_layouts_type : layout_type //'predefined' or not predefined
					},
					beforeSend : function() {
						TM_PageBuilder_Events.trigger( 'tm-pb-loading:started' );
					},
					complete : function() {
						TM_PageBuilder_Events.trigger( 'tm-pb-loading:ended' );
					},
					success: function( data ){
						$this_el.find( '.tm-pb-main-settings.' + container_class ).append( data );
						$tm_pb_templates_cache[layout_type + '_layouts'] = data;
					}
				} );
			}
		}

		window.tm_handle_templates_switching = function tm_handle_templates_switching( $clicked_button, module_type, module_width ) {
			if ( ! $clicked_button.hasClass( 'tm-pb-options-tabs-links-active' ) ) {
				var specialty_columns = typeof $clicked_button.closest( '.tm-pb-options-tabs-links' ).data( 'specialty_columns' ) !== 'undefined' ? $clicked_button.closest( '.tm-pb-options-tabs-links' ).data( 'specialty_columns' ) : 0;
				$( '.tm-pb-options-tabs-links li' ).removeClass( 'tm-pb-options-tabs-links-active' );
				$clicked_button.addClass( 'tm-pb-options-tabs-links-active' );

				$( '.tm-pb-main-settings.active-container' ).css( { 'display' : 'block', 'opacity' : 1 } ).stop( true, true ).animate( { opacity : 0 }, 300, function(){
					$( this ).css( 'display', 'none' );
					$( this ).removeClass( 'active-container' );
					$( '.' + $clicked_button.data( 'open_tab' ) ).addClass( 'active-container' ).css( { 'display' : 'block', 'opacity' : 0 } ).stop( true, true ).animate( { opacity : 1 }, 300 );
				});

				if ( typeof $clicked_button.data( 'content_loaded' ) === 'undefined' && ! $clicked_button.hasClass( 'tm-pb-new-module' ) && 'layout' !== module_type ) {
					var include_global = $clicked_button.closest( '.tm_pb_modal_settings' ).hasClass( 'tm_pb_no_global' ) ? 'no_global' : 'include_global';
					generate_templates_view( include_global, '', module_type, $( '.' + $clicked_button.data( 'open_tab' ) ), module_width, specialty_columns, 'all' );
					$clicked_button.data( 'content_loaded', 'true' );
				}
			}
		}

		window.tm_pb_maybe_apply_wpautop_to_models = function tm_pb_maybe_apply_wpautop_to_models() {
			if ( typeof window.switchEditors === 'undefined' ) {
				return;
			}

			_.each( TM_PageBuilder_App.collection.models, function( model ) {
				var model_content = model.get( 'tm_pb_content_new' ),
					key,
					skip_modules = [
						'tm_pb_social_media_follow',
						'tm_pb_social_media_follow_network',
						'tm_pb_counters',
						'tm_pb_counter',
						'tm_pb_slider',
						'tm_pb_slide'
					]
					fix_p_in = [
						'tm_pb_tabs',
						'tm_pb_pricing_tables',
						'tm_pb_slider',
						'tm_pb_slide'
					];

				for ( key in skip_modules ) {
					if ( skip_modules[ key ] == model.get( 'module_type' ) ) {
						return;
					}
				}

				if ( typeof model_content !== 'undefined' && model_content.search( "\n" ) !== -1 ) {
					model_content = window.switchEditors.wpautop( model_content.replace( /<p> <\/p>/g, "<p>&nbsp;</p>" ) );

					for ( module_key in skip_modules ) {
						if ( fix_p_in[ module_key ] == model.get( 'module_type' ) ) {
							model_content = model_content.replace( /<p>[\s]?\[/g, '[' );
							model_content = model_content.replace( /\][\s]?<\/p>/g, ']' );
						}
					}

					model.set( 'tm_pb_content_new', model_content, { silent : true } );
				}

			} );
		}

		window.tm_add_template_meta = function tm_add_template_meta( custom_field_name, value ) {
			var current_post_id = tm_pb_options.template_post_id;
			$.ajax( {
					type: "POST",
					url: tm_pb_options.ajaxurl,
					data:
					{
						action : 'tm_pb_add_template_meta',
						tm_admin_load_nonce : tm_pb_options.tm_admin_load_nonce,
						tm_meta_value : value,
						tm_custom_field : custom_field_name,
						tm_post_id : current_post_id
					}
			} );
		}

		window.tm_builder_get_global_module = function tm_builder_get_global_module( view_settings ) {
			var modal_view,
				shortcode_atts;

			$.ajax( {
				type: "POST",
				url: tm_pb_options.ajaxurl,
				dataType: 'json',
				data:
				{
					action : 'tm_pb_get_global_module',
					tm_admin_load_nonce : tm_pb_options.tm_admin_load_nonce,
					tm_global_id : view_settings.model.get( 'tm_pb_global_module' )
				},
				beforeSend : function() {
					TM_PageBuilder_Events.trigger( 'tm-pb-loading:started' );
				},
				complete : function() {
					TM_PageBuilder_Events.trigger( 'tm-pb-loading:ended' );
				},
				success: function( data ){
					if ( data.error ) {
						// if global template not found, then make module not global.
						view_settings.model.unset( 'tm_pb_global_module' );
						view_settings.model.unset( 'tm_pb_saved_tabs' );
					} else {
						var tm_pb_shortcodes_tags = TM_PageBuilder_App.getShortCodeParentTags(),
							reg_exp = window.wp.shortcode.regexp( tm_pb_shortcodes_tags ),
							inner_reg_exp = TM_PageBuilder_App.wp_regexp_not_global( tm_pb_shortcodes_tags ),
							matches = data.shortcode.match( reg_exp );

						_.each( matches, function ( shortcode ) {
							var shortcode_element = shortcode.match( inner_reg_exp ),
								shortcode_name = shortcode_element[2],
								shortcode_attributes = shortcode_element[3] !== ''
									? window.wp.shortcode.attrs( shortcode_element[3] )
									: '',
								shortcode_content = shortcode_element[5],
								module_settings,
								found_inner_shortcodes = typeof shortcode_content !== 'undefined' && shortcode_content !== '' && shortcode_content.match( reg_exp ),
								saved_tabs = shortcode_attributes['named']['saved_tabs'] || view_settings.model.get('tm_pb_saved_tabs') || '',
								ignore_admin_label = 'all' !== saved_tabs && -1 === saved_tabs.indexOf( 'general' ); // we should load Admin Label only if General tab is synced

								if ( _.isObject( shortcode_attributes['named'] ) ) {
									for ( var key in shortcode_attributes['named'] ) {
										if ( 'template_type' !== key && ( 'admin_label' !== key || ( 'admin_label' === key && ! ignore_admin_label ) ) ) {
											var prefixed_key = key !== 'admin_label' ? 'tm_pb_' + key : key;

											if ( '' !== key ) {
												view_settings.model.set( prefixed_key, shortcode_attributes['named'][key], { silent : true } );
											}
										}
									}
								}

								if ( '' !== saved_tabs && ( 'general' === saved_tabs || 'all' === saved_tabs ) ) {
									view_settings.model.set( 'tm_pb_content_new', shortcode_content, { silent : true } );
								}
						} );
					}

					modal_view = new TM_PageBuilder.ModalView( view_settings );
					$( 'body' ).append( modal_view.render().el );

					var saved_tabs = view_settings.model.get( 'tm_pb_saved_tabs' );

					if ( typeof saved_tabs !== 'undefined' ) {
						saved_tabs = 'all' === saved_tabs ? [ 'general', 'advanced', 'css' ] : saved_tabs.split( ',' );
						_.each( saved_tabs, function( tab_name ) {
							tab_name = 'css' === tab_name ? 'custom_css' : tab_name;
							$( '.tm_pb_options_tab_' + tab_name ).addClass( 'tm_pb_saved_global_tab' );
						});
						$( '.tm_pb_modal_settings_container' ).addClass( 'tm_pb_saved_global_modal' );
					}
				}
			} );
		}

		window.tm_pb_load_global_row = function tm_pb_load_global_row( post_id, module_cid ) {
			if ( ! $( 'body' ).find( '.tm_pb_global_loading_overlay' ).length ) {
				$( 'body' ).append( '<div class="tm_pb_global_loading_overlay"></div>' );
			}
			$.ajax( {
				type: "POST",
				url: tm_pb_options.ajaxurl,
				dataType: 'json',
				data:
				{
					action : 'tm_pb_get_global_module',
					tm_admin_load_nonce : tm_pb_options.tm_admin_load_nonce,
					tm_global_id : post_id
				},
				success: function( data ){
					if ( data.error ) {
						// if global template not found, then make module and all child modules not global.
						var this_view = TM_PageBuilder_Layout.getView( module_cid ),
							$child_elements = this_view.$el.find( '[data-cid]' );
						this_view.model.unset( 'tm_pb_global_module' );

						if ( $child_elements.length ) {
							$child_elements.each( function() {
								var $this_child = $( this ),
									child_cid = $this_child.data( 'cid' );
								if ( typeof child_cid !== 'undefined' && '' !== child_cid ) {
									var child_view = TM_PageBuilder_Layout.getView( child_cid );
									if ( typeof child_view !== 'undefined' ) {
										child_view.model.unset( 'tm_pb_global_parent' );
									}
								}
							});
						}
					} else {
						TM_PageBuilder_App.createLayoutFromContent( data.shortcode, '', '', { ignore_template_tag : 'ignore_template', current_row_cid : module_cid, global_id : post_id, is_reinit : 'reinit' } );
					}

					tm_pb_globals_loaded++;

					//make sure all global modules have been processed and reinitialize the layout
					if ( window.tm_pb_globals_requested === tm_pb_globals_loaded ) {
						tm_reinitialize_builder_layout();

						setTimeout( function(){
							$( 'body' ).find( '.tm_pb_global_loading_overlay' ).remove();
						}, 650 );
					}
				}
			} );
		}

		window.tm_pb_update_global_template = function tm_pb_update_global_template( global_module_cid ) {
			var global_module_view           = TM_PageBuilder_Layout.getView( global_module_cid ),
				post_id                      = global_module_view.model.get( 'tm_pb_global_module' ),
				layout_type                  = global_module_view.model.get( 'type' );
				layout_type_updated          = 'row_inner' === layout_type ? 'row' : layout_type,
				template_shortcode           = TM_PageBuilder_App.generateCompleteShortcode( global_module_cid, layout_type_updated, 'ignore_global' );

				if ( 'row_inner' === layout_type ) {
					template_shortcode = template_shortcode.replace( /tm_pb_row_inner/g, 'tm_pb_row' );
					template_shortcode = template_shortcode.replace( /tm_pb_column_inner/g, 'tm_pb_column' );
				}

			$.ajax( {
				type: "POST",
				url: tm_pb_options.ajaxurl,
				data:
				{
					action : 'tm_pb_update_layout',
					tm_admin_load_nonce : tm_pb_options.tm_admin_load_nonce,
					tm_layout_content : template_shortcode,
					tm_template_post_id : post_id,
				}
			} );
		}

		window.tm_pb_open_current_tab = function tm_pb_open_current_tab() {
			var $container = $( '.tm_pb_modal_settings_container' );

			if ( $( '.tm_pb_modal_settings_container' ).hasClass( 'tm_pb_hide_general_tab' ) ) {
				$container.find( '.tm-pb-options-tabs-links li' ).removeClass( 'tm-pb-options-tabs-links-active' );
				$container.find( '.tm-pb-options-tabs .tm-pb-options-tab' ).css( { 'display' : 'none', opacity : 0 } );

				if ( $container.hasClass( 'tm_pb_hide_advanced_tab' ) ) {
					$container.find( '.tm-pb-options-tabs-links li.tm_pb_options_tab_custom_css' ).addClass( 'tm-pb-options-tabs-links-active' );
					$container.find( '.tm-pb-options-tabs .tm-pb-options-tab.tm-pb-options-tab-custom_css' ).css( { 'display' : 'block', opacity : 1 } );
				} else {
					$container.find( '.tm-pb-options-tabs-links li.tm_pb_options_tab_advanced' ).addClass( 'tm-pb-options-tabs-links-active' );
					$container.find( '.tm-pb-options-tabs .tm-pb-options-tab.tm-pb-options-tab-advanced' ).css( { 'display' : 'block', opacity : 1 } );
				}
			} else {
				$container.find( '.tm-pb-options-tabs .tm-pb-options-tab.tm-pb-options-tab-general' ).css( { 'display' : 'block', opacity : 1 } );
			}
		}

		/**
		* Check if current user has permission to lock/unlock content
		*/
		window.tm_pb_user_lock_permissions = function tm_pb_user_lock_permissions() {
			var permissions = $.ajax( {
				type: "POST",
				url: tm_pb_options.ajaxurl,
				dataType: 'json',
				data:
				{
					action : 'tm_pb_current_user_can_lock',
					tm_admin_load_nonce : tm_pb_options.tm_admin_load_nonce
				},
				beforeSend : function() {
					TM_PageBuilder_Events.trigger( 'tm-pb-loading:started' );
				},
				complete : function() {
					TM_PageBuilder_Events.trigger( 'tm-pb-loading:ended' );
				},
			} );

			return permissions;
		}

		/**
		* Check for localStorage support
		*/
		window.tm_pb_has_storage_support = function tm_pb_has_storage_support() {
			try {
				return 'localStorage' in window && window.localStorage !== null;
			} catch (e) {
				return false;
			}
		}

		/**
		 * Check whether the Yoast SEO plugin is active
		 */
		window.tm_pb_is_yoast_seo_active = function tm_pb_is_yoast_seo_active() {
			if ( typeof YoastSEO !== 'undefined' && typeof YoastSEO === 'object' ) {
				return true;
			}

			return false;
		}

		// hook for necessary adv form field logic for tabbed posts module
		window.adv_setting_form_category_select_update_hidden = function adv_setting_form_category_select_update_hidden( that ) {
			$select_field = that.$el.find('#tm_pb_category_id');
			$hidden_name_field = that.$el.find('#tm_pb_category_name');

			if ( $select_field.length && $hidden_name_field.length ) {
				category_name = $select_field.find('option:selected').text().trim();
				$hidden_name_field.val( category_name );

				$select_field.on('change', function() {
					category_name = $(this).find('option:selected').text().trim();
					$hidden_name_field.val( category_name );
				});
			}
		}

		// Adjust the height of tinymce iframe when fullscreen mode enabled from the Divi builder
		window.tm_pb_adjust_fullscreen_mode = function tm_pb_adjust_fullscreen_mode() {
			var $modal_container = $( '.tm_pb_modal_settings_container' );

			// if fullscreen mode enabled then calculate and apply correct height
			if ( $modal_container.find( 'div.mce-fullscreen' ).length ) {
				setTimeout( function() {
					var modal_height = $modal_container.innerHeight(),
						toolbar_height = $modal_container.find( '.mce-toolbar-grp' ).innerHeight();

					$modal_container.find( 'iframe' ).height( modal_height - toolbar_height );
				}, 100 );
			}
		}

		/**
		* Clipboard mechanism. Clipboard is only capable of handling one copied content at the onetime
		* @todo add fallback support
		*/
		window.TM_PB_Clipboard = {
		  key : 'tm_pb_clipboard_',
		  set : function( type, content ) {
		    if ( tm_pb_has_storage_support() ) {
		      // Save the type of copied content
		      localStorage.setItem( this.key + 'type', type );

		      // Save the copied content
		      localStorage.setItem( this.key + 'content', content );
		    } else {
		      alert( tm_pb_options.localstorage_unavailability_alert );
		    }
		  },
		  get : function( type ) {
		    if ( tm_pb_has_storage_support() ) {
		      // Get saved type and content
		      var saved_type =  localStorage.getItem( this.key + 'type' ),
		        saved_content = localStorage.getItem( this.key + 'content' );

		      // Check for the compatibility of saved data and paste destination
		      // Return value if the supplied type equal with saved value, or if the getter doesn't care about the content's type
		      if ( typeof type === 'undefined' || type === saved_type ) {
		        return saved_content;
		      } else {
		        return false;
		      }
		    } else {
		      alert( tm_pb_options.localstorage_unavailability_alert );
		    }
		  }
		};

		//
		// ------ END FUNCTIONS ------
		//

		// run et_pb_append_templates as many times as needed
		/*for (var i = 0; i < Math.ceil( tm_pb_options.tm_builder_modules_count / tm_pb_options.tm_builder_templates_amount ); i++) {
			tm_pb_append_templates( i * tm_pb_options.tm_builder_templates_amount );
		};*/

		$('body').on( 'click contextmenu', '#tm_pb_layout_right_click_overlay', function( event ){
			event.preventDefault();

			tm_pb_close_all_right_click_options();
		});

		// Remove $tm_pb_content ?
		$tm_pb_content.remove();

		// button can be disabled, therefore use the button wrapper to determine whether to display builder or not
		if ( $toggle_builder_button_wrapper.hasClass( 'tm_pb_builder_is_used' ) ) {
			$builder.show();

			tm_pb_hide_layout_settings();
		}

		$toggle_builder_button.click( function( event ) {
			event.preventDefault();

			var $this_el = $(this),
				is_builder_used = $this_el.hasClass( 'tm_pb_builder_is_used' ),
				content;

			if ( is_builder_used ) {
				tm_pb_create_prompt_modal( 'deactivate_builder' );
			} else {
				content = tm_pb_get_content( 'content' );
				$tm_pb_old_content.val( content );

				// Re-initialize the app ?
				TM_PageBuilder_App.reInitialize();

				$use_builder_custom_field.val( 'on' );
				$builder.show();
				$this_el.text( $this_el.data( 'editor' ) );
				$main_editor_wrapper.toggleClass( 'tm_pb_hidden' );
				$this_el.toggleClass( 'tm_pb_builder_is_used' );

				TM_PageBuilder_Events.trigger( 'tm-activate-builder' );
				tm_pb_hide_layout_settings();
			}
		} );

		/**
		* Builder hotkeys
		*/
		$(window).keydown( function( event ){

			// do not override default hotkeys inside input fields
			if ( typeof event.target !== 'undefined' && $( event.target ).is( 'input, textarea' ) ) {
				return;
			}

			if ( event.keyCode === 90 &&
					 event.metaKey &&
					 event.shiftKey &&
				 ! event.altKey ||
				   event.keyCode === 90 &&
					 event.ctrlKey &&
					 event.shiftKey &&
				 ! event.altKey ) {
				// Redo
				event.preventDefault();

				TM_PageBuilder_App.redo( event );

				return false;
			} else if ( event.keyCode === 90 &&
				          event.metaKey &&
								! event.altKey ||
								  event.keyCode === 90 &&
									event.ctrlKey &&
								! event.altKey ) {
				// Undo
				event.preventDefault();

				TM_PageBuilder_App.undo( event );

				return false;
			}
		});

		// set the correct content for Yoast SEO plugin if it's activated
		if ( tm_pb_is_yoast_seo_active() ) {
			var TM_PB_Yoast_Content = function() {
				YoastSEO.app.registerPlugin( 'TM_PB_Yoast_Content', { status: 'ready' } );

				/**
				 * @param modification    {string}    The name of the filter
				 * @param callable        {function}  The callable
				 * @param pluginName      {string}    The plugin that is registering the modification.
				 * @param priority        {number}    (optional) Used to specify the order in which the callables
				 *                                    associated with a particular filter are called. Lower numbers
				 *                                    correspond with earlier execution.
				 */
				YoastSEO.app.registerModification( 'content', this.tm_pb_update_content, 'TM_PB_Yoast_Content', 5 );
			}

			/**
			 * Return the content processed by do_shortcode()
			 */
			TM_PB_Yoast_Content.prototype.tm_pb_update_content = function( data ) {
				var final_content = tm_pb_processed_yoast_content || tm_pb_options.yoast_content;

				return final_content;
			};

			new TM_PB_Yoast_Content();
		}

		TM_PageBuilder.Events.on('tm-advanced-module-settings:render', adv_setting_form_category_select_update_hidden );

		tm_builder = {
				fonts_template: function() {
					var template = $('#tm-builder-google-fonts-options-items').html();

					return template;
				},
				font_icon_list_template: function(){
					var template = $('#tm-builder-font-icon-list-items').html();

					return template;
				},
				font_down_icon_list_template: function(){
					var template = $('#tm-builder-font-down-icon-list-items').html();

					return template;
				},
				font_social_icon_list_template: function(){
					var template = $('#tm-builder-font-social-icon-list-items').html();

					return template;
				},
				preview_tabs_output: function(){
					var template = $('#tm-builder-preview-icons-template').html();

					return template;
				},
				options_tabs_output: function( options ){
					var template = _.template( $('#tm-builder-options-tabs-links-template').html() ),
						template_processed;

					window.tm_builder_template_options['tabs']['options'] = $.extend( {}, options );

					template_processed = template( window.tm_builder_template_options.tabs );

					return template_processed;
				},
				mobile_tabs_output: function(){
					var template = $('#tm-builder-mobile-options-tabs-template').html();

					return template;
				},
				options_padding_output: function( options ){
					var template = _.template( $('#tm-builder-padding-inputs-template').html() ),
						template_processed;

					window.tm_builder_template_options['padding']['options'] = $.extend( {}, options );

					template_processed = template( window.tm_builder_template_options.padding );

					return template_processed;
				},
				options_yes_no_button_output: function( options ){
					var template = _.template( $('#tm-builder-yes-no-button-template').html() ),
						template_processed;

					window.tm_builder_template_options['yes_no_button']['options'] = $.extend( {}, options );

					template_processed = template( window.tm_builder_template_options.yes_no_button );

					return template_processed;
				},
				options_font_buttons_output: function( options ){
					var template = _.template( $('#tm-builder-font-buttons-option-template').html() ),
						template_processed;

					window.tm_builder_template_options['font_buttons']['options'] = $.extend( {}, options );

					template_processed = template( window.tm_builder_template_options.font_buttons );

					return template_processed;
				}
			};

			$.extend( window.tm_builder, tm_builder );

			// recalculate sizes of tinymce iframe when Fullscreen button clicked
			$( 'body' ).on( 'click', '.tm_pb_module_settings .mce-i-fullscreen', function() {
				tm_pb_adjust_fullscreen_mode();
			});

			// recalculate sizes of tinymce iframe when window resized
			$( window ).resize( function() {
				tm_pb_adjust_fullscreen_mode();
			});

			// handle Escape and Enter buttons in the builder
			$( document ).keydown( function(e) {
				// Do nothing if focus is not in the Settings Container and no Prompt Modal opened
				if ( ! $( '.tm_pb_modal_settings_container' ).is( ':focus' ) && ! $( '.tm_pb_modal_settings_container *' ).is( ':focus' ) && ! $( '.tm_pb_prompt_modal' ).is( ':visible' ) ) {
					//return;
				}

				var $save_button = $( '.tm-pb-modal-save' ),
					$proceed_button = $( '.tm_pb_prompt_proceed' ),
					$close_button = $( '.tm-pb-modal-close' ),
					$builder_buttons = $( '#tm_pb_main_container a, #tm_pb_toggle_builder' );

				switch( e.which ) {
					// Enter button handling
					case 13 :
						// do nothing if focus is in the textarea or in the map address field so enter will work as expected
						if ( $( '.tm-pb-option-container textarea, #tm_pb_address, #tm_pb_pin_address' ).is( ':focus' ) ) {
							return;
						}
						//remove focus from the builder buttons to avoid unexpected behavior
						$builder_buttons.blur();

						if ( $save_button.length || $proceed_button.length ) {
							// it's possible that proceed button displayed above the save, we need to click only proceed button in that case
							if ( $proceed_button.length ) {
								$proceed_button.click();
							} else {
								// it's possible that there are 2 Modals appear on top of each other, save the one which is on top
								if ( typeof $save_button[1] !== 'undefined' ) {
									$save_button[1].click();
								} else {
									$save_button.click();
								}
							}
						}
						break;
					// Escape button handling
					case 27 :
						// click close button if it exist on the screen
						if ( $close_button.length ) {
							// it's possible that there are 2 Modals appear on top of each other, close the one which is on top
							if ( typeof $close_button[1] !== 'undefined' ) {
								$close_button[1].click();
							} else {
								$close_button.click();
							}
						}
						break;
				}
			});

			// Fixing fullscreen editor inside builder ModalView height in Firefox. Firefox is too fast in calculating modal weight
			// Its height calculation ends up incorrect. Performing delayed resize trigger fixes the issue
			$('body.wp-admin').on( 'click', '.tm-pb-modal-container .mce-widget.mce-btn[aria-label="Fullscreen"] button', function() {
				setTimeout( function() {
					$(window).trigger( 'resize' );
				}, 50 );
			} );

			// hide cache notice and update option so it won't be displayed again
			$( '.tm_pb_hide_cache_notice' ).click( function() {
				$( this ).closest( '.update-nag' ).hide();

				$.ajax({
					type: "POST",
					url: tm_pb_options.ajaxurl,
					data: {
						action : 'tm_pb_hide_cache_notice',
						tm_admin_load_nonce : tm_pb_options.tm_admin_load_nonce
					}
				});
			});

			// Export globals
			window.tm_builder = tm_builder;
			window.TM_PageBuilder = TM_PageBuilder;
			window.tm_pb_content_html = tm_pb_content_html;

	} );

} ( jQuery, ( function ( window, TM_PageBuilder ) {

	if ( 'undefined' !== window.TM_PageBuilder ) {
		TM_PageBuilder = window.TM_PageBuilder;
	}

	return TM_PageBuilder;
} ( window, {} ) ), Backbone, {} ) );
