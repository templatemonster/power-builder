( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.AdvancedModuleSettingEditViewContainer = Backbone.View.extend( {
    className : 'tm_pb_modal_settings_container',

    initialize : function() {
      this.template = _.template( $( '#tm-builder-advanced-setting-edit' ).html() );

      this.model = this.options.view.model;

      this.listenTo( TM_PageBuilder_Events, 'tm-modal-view-removed', this.removeView );
    },

    events : {
      'click .tm-pb-modal-save' : 'saveSettings',
      'click .tm-pb-modal-close' : 'removeView'
    },

    is_latlng : function( address ) {
      var latlng = address.split( ',' ),
        lat = ! _.isUndefined( latlng[0] ) ? parseFloat( latlng[0] ) : false,
        lng = ! _.isUndefined( latlng[1] ) ? parseFloat( latlng[1] ) : false;

      if ( lat && ! _.isNaN( lat ) && lng && ! _.isNaN( lng ) ) {
        return new google.maps.LatLng( lat, lng );
      }

      return false;
    },

    render : function() {
      var this_module_cid = this.model.attributes.cid,
        view,
        $color_picker,
        $upload_button,
        $video_image_button,
        $map,
        $social_network_picker,
        $icon_font_list,
        this_el = this;

      this.$el.html( this.template() );

      this.$el.addClass( 'tm_pb_modal_settings_container_step2' );

      if ( this.model.get( 'created' ) !== 'auto' || this.attributes['show_settings_clicked'] ) {
        view = new TM_PageBuilder.AdvancedModuleSettingEditView( { view : this } );

        this.$el.append( view.render().el );

        this.child_view = view;
      }

      TM_PageBuilder.Events.trigger( 'tm-advanced-module-settings:render', this );

      $color_picker = this.$el.find('.tm-pb-color-picker-hex');

      $color_picker_alpha = this.$el.find('.tm-builder-color-picker-alpha');

      if ( $color_picker.length ) {
        $color_picker.wpColorPicker({
          defaultColor : $color_picker.data('default-color'),
          change       : function( event, ui ) {
            var $this_el      = $(this),
              $reset_button = $this_el.closest( '.tm-pb-option-container' ).find( '.tm-pb-reset-setting' ),
              $custom_color_container = $this_el.closest( '.tm-pb-custom-color-container' ),
              current_value = $this_el.val(),
              default_value;

            if ( $custom_color_container.length ) {
              $custom_color_container.find( '.tm-pb-custom-color-picker' ).val( ui.color.toString() );
            }

            if ( ! $reset_button.length ) {
              return;
            }

            default_value = tm_pb_get_default_setting_value( $this_el );

            if ( current_value !== default_value ) {
              $reset_button.addClass( 'tm-pb-reset-icon-visible' );
            } else {
              $reset_button.removeClass( 'tm-pb-reset-icon-visible' );
            }
          }
        });
      }

      if ( $color_picker_alpha.length ) {
        $color_picker_alpha.each(function(){
          var $this_color_picker_alpha = $(this),
            color_picker_alpha_val = $this_color_picker_alpha.data('value').split('|'),
            color_picker_alpha_hex = color_picker_alpha_val[0] || '#444444',
            color_picker_alpha_opacity = color_picker_alpha_val[2] || 1.0;

          $this_color_picker_alpha.attr('data-opacity', color_picker_alpha_opacity );
          $this_color_picker_alpha.val( color_picker_alpha_hex );

          $this_color_picker_alpha.minicolors({
            control: 'hue',
            defaultValue: $(this).data('default-color') || '',
            opacity: true,
            changeDelay: 200,
            show: function() {
              $this_color_picker_alpha.minicolors('opacity', $this_color_picker_alpha.data('opacity') );
            },
            change: function(hex, opacity) {
              if( !hex ) {
                return;
              }

              var rgba_object = $this_color_picker_alpha.minicolors('rgbObject'),
                $field = $( $this_color_picker_alpha.data('field') ),
                values = [],
                values_string;

              values.push( hex );
              values.push( rgba_object.r + ', ' + rgba_object.g + ', ' + rgba_object.b );
              values.push( opacity );

              values_string = values.join('|');

              if ( $field.length ) {
                $field.val( values_string );
              }
            },
            theme: 'bootstrap'
          });
        });
      }

      $upload_button = this.$el.find('.tm-pb-upload-button');

      if ( $upload_button.length ) {
        tm_pb_activate_upload( $upload_button );
      }

      $video_image_button = this.$el.find('.tm-pb-video-image-button');

      if ( $video_image_button.length ) {
        tm_pb_generate_video_image( $video_image_button );
      }

      $map = this.$el.find('.tm-pb-map');

      if ( $map.length ) {
        var map,
          marker,
          $address = this.$el.find('.tm_pb_pin_address'),
          $address_lat = this.$el.find('.tm_pb_pin_address_lat'),
          $address_lng = this.$el.find('.tm_pb_pin_address_lng'),
          $find_address = this.$el.find('.tm_pb_find_address'),
          $zoom_level = this.$el.find('.tm_pb_zoom_level'),
          geocoder = new google.maps.Geocoder();
        var geocode_address = function() {
          var address = $address.val().trim();
          if ( address.length <= 0 ) {
            return;
          }
          geocoder.geocode( { 'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
              var result            = results[0],
                location          = result.geometry.location,
                address_is_latlng = this_el.is_latlng( address );

              // If user passes valid lat lng instead of address, override geocode with given lat & lng
              if ( address_is_latlng ) {
                location = address_is_latlng;
              }

              if ( ! isNaN( location.lat() ) && ! isNaN( location.lng() ) ) {
                $address.val( result.formatted_address);
                $address_lat.val(location.lat());
                $address_lng.val(location.lng());
                update_map( location );
              } else {
                alert( tm_pb_options.map_pin_address_invalid );
              }
            } else {
              alert( tm_pb_options.geocode_error + ': ' + status);
            }
          });
        }

        var update_map = function( LatLng ) {
          marker.setPosition( LatLng );
          map.setCenter( LatLng );
        }

        $address.on('change', geocode_address );
        $find_address.on('click', function(e){
          e.preventDefault();
        });

        setTimeout( function() {
          map = new google.maps.Map( $map[0], {
            zoom: parseInt( $zoom_level.val() ),
            mapTypeId: google.maps.MapTypeId.ROADMAP
          });

          marker = new google.maps.Marker({
            map: map,
            draggable: true,
            icon: { url: tm_pb_options.images_uri + '/marker.png', size: new google.maps.Size( 46, 43 ), anchor: new google.maps.Point( 16, 43 ) },
            shape: { coord: [1, 1, 46, 43], type: 'rect' },
          });

          google.maps.event.addListener(marker, 'dragend', function() {
            var drag_position = marker.getPosition();
            $address_lat.val(drag_position.lat());
            $address_lng.val(drag_position.lng());

            update_map(drag_position);

            latlng = new google.maps.LatLng( drag_position.lat(), drag_position.lng() );
            geocoder.geocode({'latLng': latlng }, function(results, status) {
              if (status == google.maps.GeocoderStatus.OK) {
                if ( results[0] ) {
                  $address.val( results[0].formatted_address );
                } else {
                  alert( tm_pb_options.no_results );
                }
              } else {
                alert( tm_pb_options.geocode_error_2 + ': ' + status);
              }
            });

          });

          if ( '' != $address_lat.val() && '' != $address_lng.val() ) {
            update_map( new google.maps.LatLng( $address_lat.val(), $address_lng.val() ) );
          }
        }, 200 );
      }

      $gallery_button = this.$el.find('.tm-pb-gallery-button');

      if ( $gallery_button.length ) {
        tm_pb_activate_gallery( $gallery_button );
      }

      $social_network_picker = this.$el.find('.tm-pb-social-network');

      if ( $social_network_picker.length ) {
        var $color_reset = this.$el.find('.reset-default-color'),
          $social_network_icon_color = this.$el.find('#tm_pb_bg_color');
        if ( $color_reset.length ){
          $color_reset.click(function(){
            $main_settings = $color_reset.parents('.tm-pb-main-settings');
            $social_network_picker = $main_settings.find('.tm-pb-social-network');
            $social_network_icon_color = $main_settings.find('#tm_pb_bg_color');
            if ( $social_network_icon_color.length ) {
              $social_network_icon_color.wpColorPicker('color', $social_network_picker.find( 'option:selected' ).data('color') );
              $color_reset.css( 'display', 'none' );
            }
          });
        }

        $social_network_picker.on('input', function(){
          $main_settings = $social_network_picker.parents('.tm-pb-main-settings');

          if ( $social_network_picker.val().length ) {
            var $social_network_title = $main_settings.find('#tm_pb_content_new'),
              $social_network_icon_color = $main_settings.find('#tm_pb_bg_color');

            if ( $social_network_title.length ) {
              $social_network_title.val( $social_network_picker.val() );
            }

            if ( $social_network_icon_color.length ) {
              $social_network_icon_color.wpColorPicker('color', $social_network_picker.find( 'option:selected' ).data('color') );
            }
          }
        });

        if ( $social_network_icon_color.val() !== $social_network_picker.find( 'option:selected' ).data('color') ) {
          $color_reset.css( 'display', 'inline' );
        }

      }

      $icon_font_list = this.$el.find('.tm_font_icon');

      if ( $icon_font_list.length ) {
        var that = this;
        $icon_font_list.each( function() {
          var $this_icon_list = $( this ),
            $icon_font_field    = $this_icon_list.siblings('.tm-pb-font-icon'),
            current_symbol_val  = $.trim( $icon_font_field.val() ),
            $icon_font_symbols  = $this_icon_list.find( 'li' ),
            active_symbol_class = 'tm_active',
            $current_symbol,
            top_offset,
            icon_index_number;

          function tm_pb_icon_font_init() {
            if ( current_symbol_val !== '' ) {
              // font icon index is used now in the following format: %%index_number%%
              if ( current_symbol_val.search( /^%%/ ) !== -1 ) {
                icon_index_number = parseInt( current_symbol_val.replace( /%/g, '' ) );
                $current_symbol   = $this_icon_list.find( 'li' ).eq( icon_index_number );
              } else {
                $current_symbol = $this_icon_list.find( 'li[data-icon="' + current_symbol_val + '"]' );
              }

              $current_symbol.addClass( active_symbol_class );

              if ( $this_icon_list.is( ':visible' ) ) {
                setTimeout( function() {
                  top_offset = $current_symbol.offset().top - $this_icon_list.offset().top;

                  if ( top_offset > 0 ) {
                    $this_icon_list.animate( { scrollTop : top_offset }, 0 );
                  }
                }, 110 );
              }
            }
          }
          tm_pb_icon_font_init();

          that.$el.find( '.tm-pb-options-tabs-links' ).on( 'tm_pb_main_tab:changed', tm_pb_icon_font_init );

          $icon_font_symbols.click( function() {
            var $this_element = $(this),
                this_symbol   = $this_element.data( 'icon' );

            if ( $this_element.hasClass( active_symbol_class ) ) {
              return false;
            }

            $this_element.siblings( '.' + active_symbol_class ).removeClass( active_symbol_class ).end().addClass( active_symbol_class );
            $icon_font_field.val( this_symbol ).trigger('change');
          } );
        });
      }

      tm_pb_set_child_defaults( this.$el, this_module_cid );
      tm_pb_init_main_settings( this.$el, this_module_cid );

      return this;
    },

    removeView : function( event ) {
      if ( event ) event.preventDefault();

      // remove advanced tab WYSIWYG, only if the close button is clicked
      if ( this.$el.find( '#tm_pb_content_new' ) && event )
        tm_pb_tinymce_remove_control( 'tm_pb_content_new' );

      tm_pb_hide_active_color_picker( this );

      if ( this.child_view )
        this.child_view.remove();

      this.remove();
    },

    saveSettings : function( event ) {
      var attributes = {},
        this_model_defaults = this.model.get( 'module_defaults' ) || '';

      event.preventDefault();

      this.$( 'input, select, textarea' ).each( function() {
        var $this_el = $(this),
          id = $this_el.attr('id'),
          setting_value;
          /*checked_values = [],
          name = $this_el.is('#tm_pb_content_main') ? 'tm_pb_content_new' : $this_el.attr('id');*/

        if ( typeof id === 'undefined' || ( -1 !== id.indexOf( 'qt_' ) && 'button' === $this_el.attr( 'type' ) ) ) {
          // settings should have an ID and shouldn't be a Quick Tag button from the tinyMCE in order to be saved
          return true;
        }

        id = $this_el.attr('id').replace( 'data.', '' );

        setting_value = $this_el.is('#tm_pb_content_new')
          ? tm_pb_get_content( 'tm_pb_content_new' )
          : $this_el.val();

        // do not save the default values into module attributes
        if ( '' !== this_model_defaults && typeof this_model_defaults[id] !== 'undefined' && this_model_defaults[id] === setting_value ) {
          return true;
        }

        attributes[ id ] = setting_value;
      } );

      // Check if this is map module's pin view
      if ( ! _.isUndefined( attributes.tm_pb_pin_address ) && ! _.isUndefined( attributes.tm_pb_pin_address_lat ) && ! _.isUndefined( attributes.tm_pb_pin_address_lng ) ) {
        // None of tm_pb_pin_address, tm_pb_pin_address_lat, and tm_pb_pin_address_lng fields can be empty
        // If one of them is empty, it'll trigger Uncaught RangeError: Maximum call stack size exceeded message
        if ( attributes.tm_pb_pin_address === '' || attributes.tm_pb_pin_address_lat === '' || attributes.tm_pb_pin_address_lng === '' ) {
          alert( tm_pb_options.map_pin_address_error );
          return;
        }
      }

      this.model.set( attributes, { silent : true } );

      TM_PageBuilder_Events.trigger( 'tm-advanced-module:updated' );
      TM_PageBuilder_Events.trigger( 'tm-advanced-module:saved' );

      tm_pb_tinymce_remove_control( 'tm_pb_content_new' );

      this.removeView();
    }
  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, window.wp.Backbone ) );
