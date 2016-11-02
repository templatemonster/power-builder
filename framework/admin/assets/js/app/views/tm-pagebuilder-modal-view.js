( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.ModalView = Backbone.View.extend( {

    className : 'tm_pb_modal_settings_container',

    template : _.template( $('#tm-builder-modal-template').html() ),

    events : {
      'click .tm-pb-modal-save' : 'saveSettings',
      'click .tm-pb-modal-close' : 'closeModal',
      'click .tm-pb-modal-save-template' : 'saveTemplate',
      'change #tm_pb_select_category' : 'applyFilter'
    },

    initialize : function( attributes ) {
      this.listenTo( TM_PageBuilder_Events, 'tm-add:columns', this.removeView );

      // listen to module settings box that is created after the user selects new module to add
      this.listenTo( TM_PageBuilder_Events, 'tm-new_module:show_settings', this.removeView );

      this.listenTo( TM_PageBuilder_Events, 'tm-saved_layout:loaded', this.removeView );

      this.options = attributes;
    },

    render : function() {
      var view,
        view_settings = {
          model : this.model,
          collection : this.collection,
          view : this.options.view
        },
        fake_value = false;

      this.$el.attr( 'tabindex', 0 ); // set tabindex to make the div focusable

      // update the row view if it has been dragged into another column
      if ( typeof this.model !== 'undefined' && typeof this.model.get( 'view' ) !== 'undefined' && ( this.model.get( 'module_type' ) === 'row_inner' || this.model.get( 'module_type' ) === 'row' ) && this.model.get( 'parent' ) !== this.model.get( 'view' ).$el.data( 'cid' ) ) {
        this.model.set( 'view', TM_PageBuilder_Layout.getView( this.model.get( 'parent' ) ), { silent : true } );
      }

      if ( this.attributes['data-open_view'] === 'all_modules' && this.model.get( 'module_type' ) === 'section' && this.model.get( 'tm_pb_fullwidth' ) === 'on' ) {
        this.model.set( 'type', 'column', { silent : true } );
        fake_value = true;
      }

      if ( typeof this.model !== 'undefined' ) {
        var this_parent_view = TM_PageBuilder_Layout.getView( this.model.get( 'parent' ) ),
          this_template_type = typeof this.model.get( 'tm_pb_template_type' ) !== 'undefined' && 'module' === this.model.get( 'tm_pb_template_type' ) || typeof this.model.get( 'template_type' ) !== 'undefined' && 'module' === this.model.get( 'template_type' ),
          saved_tabs = typeof this.model.get( 'tm_pb_saved_tabs' ) !== 'undefined' && 'all' !== this.model.get( 'tm_pb_saved_tabs' ) || typeof this_parent_view !== 'undefined' && typeof this_parent_view.model.get( 'tm_pb_saved_tabs' ) !== 'undefined' && 'all' !== this_parent_view.model.get( 'tm_pb_saved_tabs' )

        if ( this.attributes['data-open_view'] === 'column_specialty_settings' ) {
          this.model.set( 'open_view', 'column_specialty_settings', { silent : true } );
        }

        this.$el.html( this.template( this.model.toJSON() ) );

        if ( this.attributes['data-open_view'] === 'column_specialty_settings' ) {
          this.model.unset( 'open_view', 'column_specialty_settings', { silent : true } );
        }

        if ( this_template_type && saved_tabs ) {
          var selected_tabs = typeof this.model.get( 'tm_pb_saved_tabs' ) !== 'undefined' ? this.model.get( 'tm_pb_saved_tabs' ) : this_parent_view.model.get( 'tm_pb_saved_tabs' ) ,
            selected_tabs_array = selected_tabs.split( ',' ),
            possible_tabs_array = [ 'general', 'advanced', 'css' ],
            css_class = '',
            start_from_tab = '';

          if ( selected_tabs_array[0] !== 'all' ) {
            _.each( possible_tabs_array, function ( tab ) {
              if ( -1 === $.inArray( tab, selected_tabs_array ) ) {
                css_class += ' tm_pb_hide_' + tab + '_tab';
              } else {
                start_from_tab = '' === start_from_tab ? tab : start_from_tab;
              }
            } );

            start_from_tab = 'css' === start_from_tab ? 'custom_css' : start_from_tab;

          }

          this.$el.addClass( css_class );

          if ( typeof this.model.get( 'tm_pb_saved_tabs' ) === 'undefined' ) {
            this.model.set( 'tm_pb_saved_tabs', selected_tabs, { silent : true } );
          }
        }
      }
      else
        this.$el.html( this.template() );

      if ( fake_value )
        this.model.set( 'type', 'section', { silent : true } );

      this.container = this.$('.tm-pb-modal-container');

      if ( this.attributes['data-open_view'] === 'column_settings' ) {
        view = new TM_PageBuilder.ColumnSettingsView( view_settings );
      } else if ( this.attributes['data-open_view'] === 'all_modules' ) {
        view_settings['attributes'] = {
          'data-parent_cid' : this.model.get( 'cid' )
        }

        view = new TM_PageBuilder.ModulesView( view_settings );
      } else if ( this.attributes['data-open_view'] === 'module_settings' ) {
        view_settings['attributes'] = {
          'data-module_type' : this.model.get( 'module_type' ),
          'data-module_icon' : this.model.get( 'module_icon' )
        }

        view_settings['view'] = this;

        view = new TM_PageBuilder.ModuleSettingsView( view_settings );
      } else if ( this.attributes['data-open_view'] === 'save_layout' ) {
        view = new TM_PageBuilder.SaveLayoutSettingsView( view_settings );
      } else if ( this.attributes['data-open_view'] === 'column_specialty_settings' ) {
        view = new TM_PageBuilder.ColumnSettingsView( view_settings );
      } else if ( this.attributes['data-open_view'] === 'saved_templates' ) {
        view = new TM_PageBuilder.TemplatesModal( { attributes: { 'data-parent_cid' : this.attributes['data-parent_cid'] } } );
      }

      // do not proceed and return false if no template for this module exist yet
      if ( typeof view.attributes !== 'undefined' && 'no_template' === view.attributes['data-no_template'] ) {
        return false;
      }

      this.container.append( view.render().el );

      if ( this.attributes['data-open_view'] === 'column_settings' ) {
        // if column settings layout was generated, remove open_view attribute from a row
        // the row module modal window shouldn't have this attribute attached
        this.model.unset( 'open_view', { silent : true } );
      }

      // show only modules that the current element can contain
      if ( this.attributes['data-open_view'] === 'all_modules' ) {
        if ( this.model.get( 'module_type' ) === 'section' && typeof( this.model.get( 'tm_pb_fullwidth' ) !== 'undefined' ) && this.model.get( 'tm_pb_fullwidth' ) === 'on' ) {
          $( view.render().el ).find( '.tm-pb-all-modules li:not(.tm_pb_fullwidth_only_module)' ).remove();
        } else {
          $( view.render().el ).find( 'li.tm_pb_fullwidth_only_module' ).remove();
        }
      }

      if ( $( '.tm_pb_modal_overlay' ).length ) {
        $( '.tm_pb_modal_overlay' ).remove();
        $( 'body' ).removeClass( 'tm_pb_stop_scroll' );
      }

      if ( $( 'body' ).hasClass( 'tm_pb_modal_fade_in' ) ) {
        $( 'body' ).append( '<div class="tm_pb_modal_overlay tm_pb_no_animation"></div>' );
      } else {
        $( 'body' ).append( '<div class="tm_pb_modal_overlay"></div>' );
      }

      $( 'body' ).addClass( 'tm_pb_stop_scroll' );

      return this;
    },

    closeModal : function( event ) {
      event.preventDefault();

      if ( $( '.tm_modal_on_top' ).length ) {
        $( '.tm_modal_on_top' ).remove();
      } else {

        if ( typeof this.model !== 'undefined' && this.model.get( 'type' ) === 'module' && this.$( '#tm_pb_content_new' ).length )
          tm_pb_tinymce_remove_control( 'tm_pb_content_new' );

        tm_pb_hide_active_color_picker( this );

        tm_pb_close_modal_view( this, 'trigger_event' );
      }
    },

    removeView : function() {
      if ( typeof this.model === 'undefined' || ( this.model.get( 'type' ) === 'row' || this.model.get( 'type' ) === 'column' || this.model.get( 'type' ) === 'row_inner' || this.model.get( 'type' ) === 'column_inner' || ( this.model.get( 'type' ) === 'section' && ( this.model.get( 'tm_pb_fullwidth' ) === 'on' || this.model.get( 'tm_pb_specialty' ) === 'on' ) ) ) ) {
        if ( typeof this.model !== 'undefined' && typeof this.model.get( 'type' ) !== 'undefined' && ( this.model.get( 'type' ) === 'column' || this.model.get( 'type' ) === 'column_inner' || ( this.model.get( 'type' ) === 'section' &&  this.model.get( 'tm_pb_fullwidth' ) === 'on' ) ) ) {
          var that = this,
            $opened_tab = $( that.el ).find( '.tm-pb-main-settings.active-container' );

          // if we're adding module from library, then close everything. Otherwise leave overlay in place and add specific classes
          if ( $opened_tab.hasClass( 'tm-pb-saved-modules-tab' ) ) {
            tm_pb_close_modal_view( that );
          } else {
            that.remove();

            $( 'body' ).addClass( 'tm_pb_modal_fade_in' );
            $( '.tm_pb_modal_overlay' ).addClass( 'tm_pb_no_animation' );
            setTimeout( function() {
              $( '.tm_pb_modal_settings_container' ).addClass( 'tm_pb_no_animation' );
              $( 'body' ).removeClass( 'tm_pb_modal_fade_in' );
            }, 500);
          }
        } else {
          tm_pb_close_modal_view( this );
        }
      } else {
        this.removeOverlay();
      }
    },

    saveSettings : function( event, close_modal ) {
      var that = this,
        global_module_cid = '',
        this_parent_view = typeof that.model.get( 'parent' ) !== 'undefined' ? TM_PageBuilder_Layout.getView( that.model.get( 'parent' ) ) : '',
        global_holder_view = '' !== this_parent_view && ( typeof that.model.get( 'tm_pb_global_module' ) === 'undefined' || '' === that.model.get( 'tm_pb_global_module' ) ) ? this_parent_view : TM_PageBuilder_Layout.getView( that.model.get( 'cid' ) ),
        update_template_only = false,
        close_modal = _.isUndefined( close_modal ) ? true : close_modal;

      event.preventDefault();

      // Disabling state and mark it. It takes a while for generating shortcode,
      // so ensure that user doesn't update the page before shortcode generation has completed
      $('#publish').addClass( 'disabled' );

      TM_PageBuilder_App.disable_publish = true;

      if ( ( typeof global_holder_view.model.get( 'global_parent_cid' ) !== 'undefined' && '' !== global_holder_view.model.get( 'global_parent_cid' ) ) || ( typeof global_holder_view.model.get( 'tm_pb_global_module' ) !== 'undefined' && '' !== global_holder_view.model.get( 'tm_pb_global_module' ) ) ) {
        global_module_cid = typeof global_holder_view.model.get( 'global_parent_cid' ) !== 'undefined' ? global_holder_view.model.get( 'global_parent_cid' ) : global_holder_view.model.get( 'cid' );
      }

      if ( ( typeof that.model.get( 'tm_pb_template_type' ) !== 'undefined' && 'module' === that.model.get( 'tm_pb_template_type' ) || '' !== global_module_cid ) && ( typeof that.model.get( 'tm_pb_saved_tabs' ) !== 'undefined' ) || ( '' !== this_parent_view && typeof this_parent_view.model.get( 'tm_pb_saved_tabs' ) !== 'undefined' ) ) {
        var selected_tabs_array    = typeof that.model.get( 'tm_pb_saved_tabs' ) === 'undefined' ? this_parent_view.model.get( 'tm_pb_saved_tabs' ).split( ',' ) : that.model.get( 'tm_pb_saved_tabs' ).split( ',' ),
          selected_tabs_selector = '',
          existing_attributes    = that.model.attributes;

        _.each( selected_tabs_array, function ( tab ) {
          switch ( tab ) {
            case 'general' :
              selected_tabs_selector += '' !== selected_tabs_selector ? ',' : '';
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
          if ( -1 !== key.indexOf( 'tm_pb_' ) && 'tm_pb_template_type' !== key && 'tm_pb_saved_tabs' !== key && 'tm_pb_global_module' !== key ) {
            that.model.unset( key, { silent : true } );
          }
        } );

        if ( typeof that.model.get( 'tm_pb_saved_tabs' ) === 'undefined' ) {
          that.model.set( 'tm_pb_saved_tabs', this_parent_view.model.get( 'tm_pb_saved_tabs' ) );
        }

        if ( typeof that.model.get( 'tm_pb_template_type' ) !== 'undefined' && 'module' === that.model.get( 'tm_pb_template_type' ) ) {
          update_template_only = true;
        }
      }

      that.performSaving( selected_tabs_selector );

      if ( '' !== global_module_cid ) {
        tm_pb_update_global_template( global_module_cid );
      }

      // update all module settings only if we're updating not partially saved template
      if ( false === update_template_only && typeof selected_tabs_selector !== 'undefined' ) {
        that.performSaving();
      }

      // Enable history saving and set meta for history
      TM_PageBuilder_App.allowHistorySaving( 'edited', that.model.get( 'type' ), that.model.get( 'admin_label' ) );

      // In some contexts, closing modal view isn't needed & only settings saving needed
      if ( ! close_modal ) {
        return;
      }

      tm_pb_tinymce_remove_control( 'tm_pb_content_new' );

      tm_pb_hide_active_color_picker( that );

      tm_pb_close_modal_view( that, 'trigger_event' );
    },

    performSaving : function( option_tabs_selector ) {
      var attributes = {},
        defaults   = {},
        options_selector = typeof option_tabs_selector !== 'undefined' && '' !== option_tabs_selector ? option_tabs_selector : 'input, select, textarea, #tm_pb_content_main';

      var $tm_form_validation;
      $tm_form_validation = $(this)[0].$el.find('form.validate');
      if ( $tm_form_validation.length ) {
        validator = $tm_form_validation.validate();
        if ( !validator.form() ) {
          tm_builder_debug_message('failed form validation');
          tm_builder_debug_message('failed elements: ');
          tm_builder_debug_message( validator.errorList );
          validator.focusInvalid();
          return;
        }
        tm_builder_debug_message('passed form validation');
      }

      TM_PageBuilder.Events.trigger( 'tm-modal-settings:save', this );

      this.$( options_selector ).each( function() {
        var $this_el = $(this),
          setting_value,
          checked_values = [],
          name = $this_el.is('#tm_pb_content_main') ? 'tm_pb_content_new' : $this_el.attr('id'),
          default_value = $this_el.data('default') || '',
          custom_css_option_value;

        // convert default value to string to make sure current and default values have the same type
        default_value = default_value + '';

        // name attribute is used in normal html checkboxes, use it instead of ID
        if ( $this_el.is( ':checkbox' ) ) {
          name = $this_el.attr('name');
        }

        if ( typeof name === 'undefined' || ( -1 !== name.indexOf( 'qt_' ) && 'button' === $this_el.attr( 'type' ) ) ) {
          // settings should have an ID and shouldn't be a Quick Tag button from the tinyMCE in order to be saved
          return true;
        }

        if ( $this_el.hasClass( 'tm-pb-helper-field' ) ) {
          // don't process helper fields
          return true;
        }

        // All checkbox values are saved at once on the next step, so if the attribute name
        // already exists, do nothing
        if ( $this_el.is( ':checkbox' ) && typeof attributes[name] !== 'undefined' ) {
          return true;
        }

        // Validate colorpicker - if invalid color given, return to default color
        if ( $this_el.hasClass( 'tm-pb-color-picker-hex' ) && new Color( $this_el.val() ).error ) {
          $this_el.val( $this_el.data( 'selected-value') );
        }

        // Process all checkboxex for the current setting at once
        if ( $this_el.is( ':checkbox' ) && typeof attributes[name] === 'undefined' ) {
          $this_el.closest( '.tm-pb-option-container' ).find( '[name="' + name + '"]:checked' ).each( function() {
            checked_values.push( $(this).val() );
          } );

          setting_value = checked_values.join( "," );
        } else if ( $this_el.is( '#tm_pb_content_main' ) ) {
          // Process main content

          setting_value = $this_el.html();

          // Replace temporary ^^ signs with double quotes
          setting_value = setting_value.replace( /\^\^/g, '%22' );
        } else if ( $this_el.closest( '.tm-pb-custom-css-option' ).length ) {
          // Custom CSS settings content should be modified before it is added to the shortcode attribute

          custom_css_option_value = $this_el.val();

          // replace new lines with || in Custom CSS settings
          setting_value = '' !== custom_css_option_value ? custom_css_option_value.replace( /\n/g, '\|\|' ) : '';
        } else if ( $this_el.hasClass( 'tm-pb-range-input' ) || $this_el.hasClass( 'tm-pb-validate-unit' ) ) {
          // Process range sliders. Sanitize for valid unit first
          var tm_validate_default_unit = $this_el.hasClass( 'tm-pb-range-input' ) ? 'no_default_unit' : '';
          setting_value = tm_pb_sanitize_input_unit_value( $this_el.val(), false, tm_validate_default_unit );
        } else if ( ! $this_el.is( ':checkbox' ) ) {
          // Process all other settings: inputs, textarea#tm_pb_content_new, range sliders etc.

          setting_value = $this_el.is('textarea#tm_pb_content_new')
            ? tm_pb_get_content( 'tm_pb_content_new' )
            : $this_el.val();

          if ( $this_el.hasClass( 'tm-pb-range-input' ) && setting_value === 'px' ) {
            setting_value = '';
          }
        }

        // if default value is set, add it to the defaults object
        if ( default_value !== '' ) {
          defaults[ name ] = default_value;
        }

        // save the attribute value
        attributes[name] = setting_value;

      } );

      // add defaults object
      attributes['module_defaults'] = defaults;

      // set model attributes
      this.model.set( attributes );
    },

    saveTemplate : function( event ) {
      var module_width = -1 !== this.model.get( 'module_type' ).indexOf( 'fullwidth' ) ? 'fullwidth' : 'regular',
        columns_layout = typeof this.model.get( 'columns_layout' ) !== 'undefined' ? this.model.get( 'columns_layout' ) : '0';
      event.preventDefault();

      tm_pb_create_prompt_modal( 'save_template', this, module_width, columns_layout );
    },

    removeOverlay : function() {
      var $overlay = $( '.tm_pb_modal_overlay' );
      if ( $overlay.length ) {

        $overlay.addClass( 'tm_pb_overlay_closing' );

        setTimeout( function() {
          $overlay.remove();

          $( 'body' ).removeClass( 'tm_pb_stop_scroll' );
        }, 600 );
      }

      // Check for existence of disable_publish element, don't do auto enable publish
      // if not necesarry. Example: opening Modal View, then close it without further action
      if ( ! _.isUndefined( TM_PageBuilder_App.disable_publish ) ) {
        var auto_enable_publishing = setTimeout( function() {

          // Check for disable_publish state, auto enable after three seconds
          // This means no tm_pb_set_content triggered
          if ( ! _.isUndefined( TM_PageBuilder_App.disable_publish ) ) {
            $('#publish').removeClass( 'disabled' );

            delete TM_PageBuilder_App.disable_publish;
          }
        }, 3000 );
      }
    },

    applyFilter : function( event ) {
      var $event_target = $(event.target),
        all_data = $event_target.data( 'attr' ),
        selected_category = $event_target.val();
      all_data.append_to.html( '' );
      generate_templates_view( all_data.include_global, '', all_data.layout_type, all_data.append_to, all_data.module_width, all_data.specialty_cols, selected_category );
    }

  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, window.wp.Backbone ) );
