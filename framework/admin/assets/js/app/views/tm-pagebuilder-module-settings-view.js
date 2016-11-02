( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.ModuleSettingsView = Backbone.View.extend( {

    className : 'tm_pb_module_settings',

    initialize : function() {
      if ( ! $( TM_PageBuilder_Layout.generateTemplateName( this.attributes['data-module_type'] ) ).length ) {
        this.attributes['data-no_template'] = 'no_template';
        return;
      }

      this.template = _.template( $( TM_PageBuilder_Layout.generateTemplateName( this.attributes['data-module_type'] ) ).html() );
      this.listenTo( TM_PageBuilder_Events, 'tm-modal-view-removed', this.removeModule );
      this.listenTo( TM_PageBuilder_Events, 'tm-advanced-module:saved', this.renderMap );
    },

    events : {
    },

    render : function() {
      var $this_el = this.$el,
        content = '',
        this_module_cid = this.model.attributes.cid,
        $content_textarea,
        $content_textarea_container,
        $content_textarea_option,
        advanced_mode = false,
        view,
        $color_picker,
        $color_picker_alpha,
        $upload_button,
        $video_image_button,
        $gallery_button,
        $time_picker,
        $icon_font_list,
        $validation_element,
        $tm_affect_fields,
        $tm_form_validation,
        $icon_font_options = [ "tm_pb_font_icon", "tm_pb_button_one_icon", "tm_pb_button_two_icon", "tm_pb_button_icon" ];

      // Replace encoded double quotes with normal quotes,
      // escaping is applied in modules templates
      _.each( this.model.attributes, function( value, key, list ) {
        if ( typeof value === 'string' && key !== 'tm_pb_content_new' && -1 === $.inArray( key, $icon_font_options ) ) {
          return list[ key ] = value.replace( /%22/g, '"' );
        }
      } );

      this.$el.html( this.template( this.model.attributes ) );

      $content_textarea = this.$el.find( '#tm_pb_content_new' );
      $color_picker = this.$el.find('.tm-pb-color-picker-hex');
      $color_picker_alpha = this.$el.find('.tm-builder-color-picker-alpha');
      $upload_button = this.$el.find('.tm-pb-upload-button');
      $video_image_button = this.$el.find('.tm-pb-video-image-button');
      $gallery_button = this.$el.find('.tm-pb-gallery-button');
      $time_picker = this.$el.find('.tm-pb-date-time-picker');
      $icon_font_list = this.$el.find('.tm_font_icon');
      $validation_element = $this_el.find('.tm-validate-number');
      $tm_form_validation = $this_el.find('form.validate');

      // validation
      if ( $tm_form_validation.length ) {
        tm_builder_debug_message('validation enabled');
        $tm_form_validation.validate({
          debug: true
        });
      }

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
          },
          clear: function() {
            $(this).val( tm_pb_options.invalid_color );
            $(this).closest( '.tm-pb-option-container' ).find( '.tm-pb-main-setting' ).val( '' );
          }
        });

        $color_picker.each( function() {
          var $this = $(this),
            default_color = $this.data('default-color') || '',
            $reset_button = $this.closest( '.tm-pb-option-container' ).find( '.tm-pb-reset-setting' );

          if ( ! $reset_button.length ) {
            return true;
          }

          if ( default_color !== $this.val() ) {
            $reset_button.addClass( 'tm-pb-reset-icon-visible' );
          }
        } );
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

      if ( $upload_button.length ) {
        tm_pb_activate_upload( $upload_button );
      }

      if ( $video_image_button.length ) {
        tm_pb_generate_video_image( $video_image_button );
      }

      if ( $gallery_button.length ) {
        tm_pb_activate_gallery( $gallery_button );
      }

      if ( $time_picker.length ) {
        $time_picker.datetimepicker();
      }

      if( $validation_element.length ){
        $validation_element.keyup( function() {
          var $this_el = $( this );

          if ( $this_el.val() < 0 || ( !$.isNumeric( $this_el.val() ) && $this_el.val() !== '' ) ) {
            $this_el.val( 0 );
          }

          if ( $this_el.val() > 100 ) {
            $this_el.val( 100 );
          }

          if ( $this_el.val() !=='' ) {
            $this_el.val( Math.round( $this_el.val() ) );
          }
        });
      }

      if ( $icon_font_list.length ) {
        var that = this;
        $icon_font_list.each( function() {
          var $this_icon_list     = $( this ),
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

      if ( $content_textarea.length ) {
        $content_textarea_option = $content_textarea.closest( '.tm-pb-option' );

        if ( $content_textarea_option.hasClass( 'tm-pb-option-advanced-module' ) )
          advanced_mode = true;

        if ( ! advanced_mode ) {
          $content_textarea_container = $content_textarea.closest( '.tm-pb-option-container' );

          content = $content_textarea.html();

          $content_textarea.remove();

          $content_textarea_container.prepend( tm_pb_content_html );

          setTimeout( function() {
            if ( typeof window.switchEditors !== 'undefined' ) {
              window.switchEditors.go( 'tm_pb_content_new', tm_get_editor_mode() );
            }

            tm_pb_set_content( 'tm_pb_content_new', content );

            window.wpActiveEditor = 'tm_pb_content_new';
          }, 100 );
        } else {
          var view_cid = TM_PageBuilder_Layout.generateNewId();
          this.view_cid = view_cid;

          $content_textarea_option.hide();

          $content_textarea.attr( 'id', 'tm_pb_content_main' );

          view = new TM_PageBuilder.AdvancedModuleSettingsView( {
            model : this,
            el : this.$el.find( '.tm-pb-option-advanced-module-settings' ),
            attributes : {
              cid : view_cid
            }
          } );

          TM_PageBuilder_Layout.addView( view_cid, view );

          $content_textarea_option.before( view.render() );

          if ( $content_textarea.html() !== '' ) {
            view.generateAdvancedSortableItems( $content_textarea.html(), this.$el.find( '.tm-pb-option-advanced-module-settings' ).data( 'module_type' ) );
            TM_PageBuilder_Events.trigger( 'tm-advanced-module:updated_order', this.$el );
          }
        }
      }

      this.renderMap();

      tm_pb_init_main_settings( this.$el, this_module_cid );

      if ( ! advanced_mode ) {
        setTimeout( function() {
          $this_el.find('select, input, textarea, radio').filter(':eq(0)').focus();
        }, 1 );
      }

      return this;
    },

    removeModule : function() {
      // remove Module settings, when modal window is closed or saved

      this.remove();
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

    renderMap: function() {
      var this_el = this,
        $map = this.$el.find('.tm-pb-map');

      if ( $map.length ) {
        view_cid = this.view_cid;

        var $address = this.$el.find('.tm_pb_address'),
          $address_lat = this.$el.find('.tm_pb_address_lat'),
          $address_lng = this.$el.find('.tm_pb_address_lng'),
          $find_address = this.$el.find('.tm_pb_find_address'),
          $zoom_level = this.$el.find('.tm_pb_zoom_level'),
          geocoder = new google.maps.Geocoder(),
          markers = {};
        var geocode_address = function() {
          var address = $address.val();
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
                update_center( location );
              } else {
                alert( tm_pb_options.map_pin_address_invalid );
              }
            } else {
              alert( tm_pb_options.geocode_error + ': ' + status);
            }
          });
        }

        var update_center = function( LatLng ) {
          $map.map.setCenter( LatLng );
        }

        var update_zoom = function () {
          $map.map.setZoom( parseInt( $zoom_level.val() ) );
        }

        $address.on('blur', geocode_address );
        $find_address.on('click', function(e){
          e.preventDefault();
        });

        $zoom_level.on('blur', update_zoom );

        setTimeout( function() {
          $map.map = new google.maps.Map( $map[0], {
            zoom: parseInt( $zoom_level.val() ),
            mapTypeId: google.maps.MapTypeId.ROADMAP
          });

          if ( '' != $address_lat.val() && '' != $address_lng.val() ) {
            update_center( new google.maps.LatLng( $address_lat.val(), $address_lng.val() ) );
          }

          if ( '' != $zoom_level ) {
            update_zoom();
          }

          setTimeout( function() {
            var map_pins = TM_PageBuilder_Layout.getChildViews( view_cid );
            var bounds = new google.maps.LatLngBounds();
            if ( _.size( map_pins ) ) {
              _.each( map_pins, function( map_pin, key ) {

                // Skip current map pin if it has no lat or lng, as it will trigger maximum call stack exceeded
                if ( _.isUndefined( map_pin.model.get('tm_pb_pin_address_lat') ) || _.isUndefined( map_pin.model.get('tm_pb_pin_address_lng') ) ) {
                  return;
                }

                var position = new google.maps.LatLng( parseFloat( map_pin.model.get('tm_pb_pin_address_lat') ) , parseFloat( map_pin.model.get('tm_pb_pin_address_lng') ) );

                markers[key] = new google.maps.Marker({
                  map: $map.map,
                  position: position,
                  title: map_pin.model.get('tm_pb_title'),
                  icon: { url: tm_pb_options.images_uri + '/marker.png', size: new google.maps.Size( 46, 43 ), anchor: new google.maps.Point( 16, 43 ) },
                  shape: { coord: [1, 1, 46, 43], type: 'rect' }
                });

                bounds.extend( position );
              });

              if ( ! _.isUndefined( $map.map.getBounds() ) && ! _.isNull( $map.map.getBounds() ) ) {
                bounds.extend( $map.map.getBounds().getNorthEast() );
                bounds.extend( $map.map.getBounds().getSouthWest() );
              }
              $map.map.fitBounds( bounds );
            }
          }, 500 );

          google.maps.event.addListener( $map.map, 'center_changed', function() {
            var center = $map.map.getCenter();
            $address_lat.val( center.lat() );
            $address_lng.val( center.lng() );
          });

          google.maps.event.addListener( $map.map, 'zoom_changed', function() {
            var zoom_level = $map.map.getZoom();
            $zoom_level.val( zoom_level );
          });

        }, 200 );
      }
    }

  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, window.wp.Backbone ) );
