( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.RightClickOptionsView = Backbone.View.extend( {

    tagName : 'div',

    id : 'tm-builder-right-click-controls',

    template : _.template( $('#tm-builder-right-click-controls-template').html() ),

    events : {
      'click .tm-pb-right-click-rename' : 'rename',
      'click .tm-pb-right-click-save-to-library' : 'saveToLibrary',
      'click .tm-pb-right-click-undo' : 'undo',
      'click .tm-pb-right-click-redo' : 'redo',
      'click .tm-pb-right-click-disable' : 'disable',
      'click .tm_pb_disable_on_option' : 'disable_device',
      'click .tm-pb-right-click-lock' : 'lock',
      'click .tm-pb-right-click-collapse' : 'collapse',
      'click .tm-pb-right-click-copy' : 'copy',
      'click .tm-pb-right-click-paste-after' : 'pasteAfter',
      'click .tm-pb-right-click-paste-app' : 'pasteApp',
      'click .tm-pb-right-click-paste-column' : 'pasteColumn',
      'click .tm-pb-right-click-preview' : 'preview'
    },

    initialize : function( attributes, skip_render ) {
      var skip_render                       = _.isUndefined( skip_render ) ? false : skip_render,
        allowed_library_clipboard_content;

      this.type                             = this.options.model.attributes.type;
      this.tm_pb_has_storage_support        = tm_pb_has_storage_support();
      this.has_compatible_clipboard_content = TM_PB_Clipboard.get( this.getClipboardType() );
      this.history_noun                     = this.type === 'row_inner' ? 'row' : this.type;

      // Divi Library adjustment
      if ( tm_pb_options.is_divi_library === '1' && this.has_compatible_clipboard_content !== false ) {
        // There are four recognized layout type: layout, section, row, module
        switch( tm_pb_options.layout_type ) {
          case 'module' :
            allowed_library_clipboard_content = [];
            break;
          case 'row' :
            allowed_library_clipboard_content = ['module'];
            break;
          case 'section' :
            allowed_library_clipboard_content = ['module', 'row'];
            break;
          default :
            allowed_library_clipboard_content = ['module', 'row', 'section'];
            break;
        }

        // If current clipboard type isn't allowed, disable pasteAfter
        if ( $.inArray( this.type, allowed_library_clipboard_content ) == -1 ) {
          this.has_compatible_clipboard_content = false;
        }
      }

      // Enable right options control rendering to be skipped
      if ( skip_render === false ) {
        this.render();
      }
    },

    render : function() {
      var $parent = $( this.options.view ),
        $options_wrap = this.$el.html( this.template() ),
        view_offset = this.options.view.offset(),
        parent_offset_x = this.options.view_event.pageX - view_offset.left - 100,
        parent_offset_y = this.options.view_event.pageY - view_offset.top;

      // close other options, if there's any
      this.closeAllRightClickOptions();

      // Prevent recursive right click options
      if ( $( this.options.view_event.toElement ).is('#tm-builder-right-click-controls a')  ) {
        return;
      }

      // Don't display empty right click options
      if ( $options_wrap.find('li').length < 1 ) {
        return;
      }

      // Append options to the page
      $parent.append( $options_wrap );

      // Fixing options' position and animating it
      $options_wrap.find('.options').css({
        'top' : parent_offset_y,
        'left' : parent_offset_x,
        'margin-top': ( 0 - $options_wrap.find('.options').height() - 40 ),
      }).animate({
        'margin-top': ( 0 - $options_wrap.find('.options').height() - 10 ),
        'opacity' : 1
      }, 300 );

      // Add full screen page overlay (right/left click anywhere outside builder to close options)
      $('#tm_pb_layout').prepend('<div id="tm_pb_layout_right_click_overlay" />');
    },

    closeAllRightClickOptions : function() {
      tm_pb_close_all_right_click_options();

      return false;
    },

    rename : function( event ) {
      event.preventDefault();

      var $parent = this.$el.parent(),
        cid = this.options.model.attributes.cid;

      tm_pb_create_prompt_modal( 'rename_admin_label', cid );

      // close the click right options
      this.closeAllRightClickOptions();
    },

    saveToLibrary : function ( event ) {
      event.preventDefault();

      var model          = this.options.model,
        view_settings  = {
          model : model,
          collection : TM_PageBuilder_Modules,
          attributes : {
            'data-open_view' : 'module_settings'
          }
        };

      // Close right click options UI
      this.closeAllRightClickOptions();

      if ( this.type === 'app' ) {
        // Init save current page to library modal view
        tm_pb_create_prompt_modal( 'save_layout' );
      } else {
        // Init modal view
        modal_view = new TM_PageBuilder.ModalView( view_settings );

        // Append modal view
        $('body').append( modal_view.render().el );

        // set initial active tab for partially saved module templates.
        tm_pb_open_current_tab();

        // Init save template modal view
        modal_view.saveTemplate( event );
      }
    },

    undo : function( event ) {
      event.preventDefault();

      // Undoing...
      TM_PageBuilder_App.undo( event );

      // Close right click options UI
      this.closeAllRightClickOptions();
    },

    redo : function( event ) {
      event.preventDefault();

      // Redoing...
      TM_PageBuilder_App.redo( event );

      // Close right click options UI
      this.closeAllRightClickOptions();
    },

    disable : function( event ) {
      event.preventDefault();

      var $this_button = $( event.target ).hasClass( 'tm-pb-right-click-disable' ) ? $( event.target ) : $( event.target ).closest( 'a' ),
        this_options_container = $this_button.closest( 'li' ).find( 'span.tm_pb_disable_on_options' ),
        single_options = this_options_container.find( 'span.tm_pb_disable_on_option' ),
        is_all_disabled = typeof this.options.model.attributes.tm_pb_disabled !== 'undefined' && 'on' === this.options.model.attributes.tm_pb_disabled ? true : false,
        disabled_on = typeof this.options.model.attributes.tm_pb_disabled_on !== 'undefined' ? this.options.model.attributes.tm_pb_disabled_on : '',
        disabled_on_array,
        i,
        device;

      $this_button.addClass( 'tm_pb_right_click_hidden' );

      this_options_container.addClass( 'tm_pb_right_click_visible' );

      // backward compatibility with old option
      if ( is_all_disabled ) {
        single_options.addClass( 'tm_pb_disable_on_active' );
      } else if ( '' !== disabled_on ) {
        disabled_on_array = disabled_on.split('|');
        i = 0,
        device = 'phone';

        single_options.each( function() {
          var this_option = $( this );

          if ( this_option.hasClass( 'tm_pb_disable_on_' + device ) && 'on' === disabled_on_array[ i ] ) {
            this_option.addClass( 'tm_pb_disable_on_active' );
          }

          i++;
          device = 1 === i ? 'tablet' : 'desktop';
        } );
      }

      return false;
    },

    disable_device : function( event ) {
      var $this_button = $( event.target ),
        this_option = $( this ),
        new_option_state = $this_button.hasClass( 'tm_pb_disable_on_active' ) ? 'off' : 'on',
        disabled_on = typeof this.options.model.attributes.tm_pb_disabled_on !== 'undefined' ? this.options.model.attributes.tm_pb_disabled_on : '',
        $parent = this.$el.parent(),
        history_verb,
        disabled_on_array,
        option_index,
        history_addition;

      // determine which option should be updated, Phone, Tablet or Desktop.
      if ( $this_button.hasClass( 'tm_pb_disable_on_phone' ) ) {
        option_index = 0;
        history_addition = 'phone';
      } else if ( $this_button.hasClass( 'tm_pb_disable_on_tablet' ) ) {
        option_index = 1;
        history_addition = 'tablet';
      } else {
        option_index = 2;
        history_addition = 'desktop';
      }

      if ( '' !== disabled_on ) {
        disabled_on_array = disabled_on.split('|');
      } else {
        disabled_on_array = ['','',''];
      }

      disabled_on_array[ option_index ] = new_option_state;

      this.options.model.attributes.tm_pb_disabled_on = disabled_on_array[0] + '|' + disabled_on_array[1] + '|' + disabled_on_array[2];

      if ( 'on' === disabled_on_array[0] && 'on' === disabled_on_array[1] && 'on' === disabled_on_array[2] ) {
        parent_background_color = $parent.css('backgroundColor');

        $parent.addClass('tm_pb_disabled');

        // Add attribute to shortcode
        this.options.model.attributes.tm_pb_disabled = 'on';
        history_verb = 'disabled';
      } else {
        // toggle tm_pb_disabled class
        $parent.removeClass( 'tm_pb_disabled' );

        // Remove attribute to shortcode
        this.options.model.attributes.tm_pb_disabled = 'off';
        history_verb = 'off' === new_option_state ? 'enabled' : 'disabled';
      }

      $this_button.toggleClass( 'tm_pb_disable_on_active' );

      // Update global module
      this.updateGlobalModule();

      // Enable history saving and set meta for history
      TM_PageBuilder_App.allowHistorySaving( history_verb, this.history_noun, undefined, history_addition );

      // Rebuild shortcodes
      TM_PageBuilder_App.saveAsShortcode();

      return false;
    },

    lock : function( event ) {
      event.preventDefault();

      var $parent = this.$el.parent();

      // toggle tm_pb_locked class
      if ( $parent.hasClass('tm_pb_locked') ) {
        this.unlockItem();

        // Enable history saving and set meta for history
        TM_PageBuilder_App.allowHistorySaving( 'unlocked', this.history_noun );
      } else {
        this.lockItem();

        // Enable history saving and set meta for history
        TM_PageBuilder_App.allowHistorySaving( 'locked', this.history_noun );
      }

      // Update global module
      this.updateGlobalModule();

      // close the click right options
      this.closeAllRightClickOptions();

      // Rebuild shortcodes
      TM_PageBuilder_App.saveAsShortcode();
    },

    unlockItem : function() {
      var this_el = this,
        $parent = this_el.$el.parent(),
        request = tm_pb_user_lock_permissions(),
        children_views,
        parent_views;

      request.done( function ( response ) {
        if ( true === response ) {
          $parent.removeClass('tm_pb_locked');

          // Add attribute to shortcode
          this_el.options.model.attributes.tm_pb_locked = 'off';

          if ( 'module' !== this_el.options.model.get( 'type' ) ) {
            children_views = TM_PageBuilder_Layout.getChildrenViews( this_el.model.get('cid') );

            _.each( children_views, function( view, key ) {
              view.$el.removeClass('tm_pb_parent_locked');
              view.model.set( 'tm_pb_parent_locked', 'off', { silent : true } );
            } );
          }

          if ( 'section' !== this_el.options.model.get( 'type' ) ) {
            parent_views = TM_PageBuilder_Layout.getParentViews( this_el.model.get( 'parent' ) );

            _.each( parent_views, function( view, key ) {
              if ( ! TM_PageBuilder_Layout.isChildrenLocked( view.model.get( 'cid' ) ) ) {
                view.$el.removeClass('tm_pb_children_locked');
              }
            } );
          }

          // Enable history saving and set meta for history
          TM_PageBuilder_App.allowHistorySaving( 'unlocked', this_el.history_noun );

          // Rebuild shortcodes
          TM_PageBuilder_App.saveAsShortcode();
        } else {
          alert( tm_pb_options.locked_item_permission_alert );
        }
      });
    },

    lockItem : function() {
      var this_el = this,
        $parent = this_el.$el.parent(),
        request = tm_pb_user_lock_permissions(),
        children_views,
        parent_views;

      request.done( function ( response ) {
        if ( true === response ) {
          $parent.addClass('tm_pb_locked');

          // Add attribute to shortcode
          this_el.options.model.attributes.tm_pb_locked = 'on';

          if ( 'module' !== this_el.options.model.get( 'type' ) ) {
            children_views = TM_PageBuilder_Layout.getChildrenViews( this_el.model.get('cid') );

            _.each( children_views, function( view, key ) {
              view.$el.addClass('tm_pb_parent_locked');
              view.model.set( 'tm_pb_parent_locked', 'on', { silent : true } );
            } );
          }

          if ( 'section' !== this_el.options.model.get( 'type' ) ) {
            parent_views = TM_PageBuilder_Layout.getParentViews( this_el.model.get( 'parent' ) );

            _.each( parent_views, function( view, key ) {
              view.$el.addClass( 'tm_pb_children_locked' );
            } );
          }

          // Enable history saving and set meta for history
          TM_PageBuilder_App.allowHistorySaving( 'locked', this_el.history_noun );

          // Rebuild shortcodes
          TM_PageBuilder_App.saveAsShortcode();
        } else {
          alert( tm_pb_options.locked_item_permission_alert );
        }
      });
    },

    collapse : function( event ) {
      event.preventDefault();

      var $parent = this.$el.parent(),
        cid = this.options.model.attributes.cid,
        history_verb;

      $parent.toggleClass('tm_pb_collapsed');

      if ( $parent.hasClass('tm_pb_collapsed') ) {
        // Add attribute to shortcode
        this.options.model.attributes.tm_pb_collapsed = 'on';
        history_verb = 'collapsed';
      } else {
        // Add attribute to shortcode
        this.options.model.attributes.tm_pb_collapsed = 'off';
        history_verb = 'expanded';
      }

      // Update global module
      this.updateGlobalModule();

      // close the click right options
      this.closeAllRightClickOptions();

      // Enable history saving and set meta for history
      TM_PageBuilder_App.allowHistorySaving( history_verb, this.history_noun );

      // Rebuild shortcodes
      TM_PageBuilder_App.saveAsShortcode();
    },

    copy : function( event ) {
      event.preventDefault();

      var module_attributes = _.clone( this.model.attributes ),
        type              = module_attributes.type,
        clipboard_content;

      // Normalize row_inner as row. Specialty's section row is detected as row_inner
      // but selector-wise, there's no .tm_pb_row_inner. It uses the same .tm_pb_row
      if ( type === 'row_inner' ) {
        type = 'row';
      }

      // Delete circular structure element carried by default by specialty section's row inner
      if ( ! _.isUndefined( module_attributes.view ) ) {
        delete module_attributes.view;
      }

      // Delete appendAfter element, its leftover can cause misunderstanding on rendering UI
      if ( ! _.isUndefined( module_attributes.appendAfter ) ) {
        delete module_attributes.appendAfter;
      }

      // append childview's data to mobile_attributes for row and section
      if ( type === 'row' || type === 'section' ) {
        module_attributes.childviews = this.getChildViews( module_attributes.cid );
      }

      module_attributes.created = 'manually';

      // Set clipboard content
      clipboard_content = JSON.stringify( module_attributes );

      // Save content to clipboard
      TM_PB_Clipboard.set( this.getClipboardType(), clipboard_content );

      // close the click right options
      this.closeAllRightClickOptions();
    },

    pasteAfter : function( event, parent, clipboard_type, has_cloned_cid ) {
      event.preventDefault();

      var parent            = _.isUndefined( parent ) ? this.model.get( 'parent' ) : parent,
        clipboard_type    = _.isUndefined( clipboard_type ) ? this.getClipboardType() : clipboard_type,
        clipboard_content,
        has_cloned_cid    = _.isUndefined( has_cloned_cid ) ? true : has_cloned_cid;

      // Get clipboard content
      clipboard_content = TM_PB_Clipboard.get( clipboard_type );
      clipboard_content = JSON.parse( clipboard_content );

      if ( has_cloned_cid ) {
        clipboard_content.cloned_cid = this.model.get( 'cid' );
      }

      // Paste views recursively
      this.setPasteViews( clipboard_content, parent, 'main_parent' );

      // Trigger events
      TM_PageBuilder_Events.trigger( 'tm-advanced-module:updated' );
      TM_PageBuilder_Events.trigger( 'tm-advanced-module:saved' );

      // Update global module
      this.updateGlobalModule();

      // close the click right options
      this.closeAllRightClickOptions();

      // Enable history saving and set meta for history
      // pasteAfter can be used for clone, so only use copied if history verb being used is default
      if ( TM_PageBuilder_Visualize_Histories.verb === 'did' ) {
        TM_PageBuilder_App.allowHistorySaving( 'copied', this.history_noun );
      }

      // Rebuild shortcodes
      TM_PageBuilder_App.saveAsShortcode();
    },

    pasteApp : function( event ) {
      event.preventDefault();

      // Get last' section model
      var sections     = TM_PageBuilder_Modules.where({ 'type' : 'section' }),
        last_section = _.last( sections );

      // Set last section as this.model and this.options.model so setPasteViews() can parse the clipboard correctly
      this.model = last_section;
      this.options.model = last_section;

      // Paste Item
      this.pasteAfter( event, undefined, 'tm_pb_clipboard_section', false );
    },

    pasteColumn : function( event ) {
      event.preventDefault();

      var parent         = this.model.get( 'cid' ),
        clipboard_type = this.model.get('type') === 'section' ? 'tm_pb_clipboard_module_fullwidth' : 'tm_pb_clipboard_module';

      // Paste item
      this.pasteAfter( event, parent, clipboard_type, false );
    },

    getClipboardType : function() {
      var type              = this.model.attributes.type,
        module_type        = _.isUndefined( this.model.attributes.module_type ) ? this.model.attributes.type : this.model.attributes.module_type,
        clipboard_key     = 'tm_pb_clipboard_' + type,
        fullwidth_prefix  = 'tm_pb_fullwidth';

      // Added fullwidth prefix
      if ( module_type.substr( 0, fullwidth_prefix.length ) === fullwidth_prefix ) {
        clipboard_key += '_fullwidth';
      }

      return clipboard_key;
    },

    getChildViews : function( parent ) {
      var this_el = this,
        views = TM_PageBuilder_Modules.models,
        child_attributes,
        child_views = [];

      _.each( views, function( view, key ) {
        if ( view.attributes.parent === parent ) {
          child_attributes = view.attributes;

          // Delete circular structure element carried by default by specialty section's row inner
          if ( ! _.isUndefined( child_attributes.view ) ) {
            delete child_attributes.view;
          }

          // Delete appendAfter element, its leftover can cause misunderstanding on rendering UI
          if ( ! _.isUndefined( child_attributes.appendAfter ) ) {
            delete child_attributes.appendAfter;
          }

          child_attributes.created = 'manually';

          // Append grand child views, if there's any
          child_attributes.childviews = this_el.getChildViews( view.attributes.cid );
          child_views.push( child_attributes );
        }
      } );

      return child_views;
    },

    setPasteViews : function( view, parent, is_main_parent ) {
      var this_el    = this,
        cid        = TM_PageBuilder_Layout.generateNewId(),
        view_index = this.model.collection.indexOf( this.model ),
        childviews = ( ! _.isUndefined( view.childviews ) && _.isArray( view.childviews ) ) ? view.childviews : false,
        global_module_elements = [ 'tm_pb_global_parent', 'global_parent_cid' ];

      // Add newly generated cid and parent to the pasted view
      view.cid    = cid;
      view.parent = parent;

      if ( typeof is_main_parent !== 'undefined' && 'main_parent' === is_main_parent ) {
        view.pasted_module = true;
      } else {
        view.pasted_module = false;
      }

      // Set new global_parent_cid for pasted element
      if ( ! _.isUndefined( view.tm_pb_global_module ) && _.isUndefined( view.global_parent_cid ) && _.isUndefined( this.set_global_parent_cid ) ) {
        this.global_parent_cid = cid;
        this.set_global_parent_cid = true;
      }

      if ( ! _.isUndefined( view.global_parent_cid ) ) {
        view.global_parent_cid = this.global_parent_cid;
      }

      // If the view is pasted inside global module, inherit its global module child attributes
      _.each( global_module_elements, function( global_module_element ) {
        if ( ! _.isUndefined( this_el.options.model.get( global_module_element ) ) && _.isUndefined( view[ global_module_element ] ) ) {
          view[ global_module_element ] = this_el.options.model.get( global_module_element );
        }
      } );

      // Remove template type leftover. Template type is used by Divi Library to remove item's settings and clone button
      if ( ! _.isUndefined( view.tm_pb_template_type ) ) {
        delete view.tm_pb_template_type;
      }

      // Delete unused childviews
      delete view.childviews;

      // Add view to collections
      this.model.collection.add( view, { at : view_index } );

      // If current view has childviews (row & module), repeat the process above recursively
      if ( childviews ) {
        _.each( childviews, function( childview ){
          this_el.setPasteViews( childview, cid );
        });
      };
    },

    updateGlobalModule : function () {
      var global_module_cid;

      if ( ! _.isUndefined( this.options.model.get( 'tm_pb_global_module' ) ) ) {
        global_module_cid = this.options.model.get( 'cid' );
      } else if ( ! _.isUndefined( this.options.model.get( 'tm_pb_global_parent' ) ) ) {
        global_module_cid = this.options.model.get( 'global_parent_cid' );
      }

      if ( ! _.isUndefined( global_module_cid ) ) {
        tm_pb_update_global_template( global_module_cid );
      }
    },

    hasOption : function( option_name ) {
      var has_option = false;

      switch( option_name ) {
        case "rename" :
            if ( this.hasOptionSupport( [ "module", "section", "row_inner", "row" ] ) &&
               this.options.model.attributes.tm_pb_locked !== "on" ) {
              has_option = true;
            }
          break;
        case "save-to-library" :
            if ( this.hasOptionSupport( [ "app", "section", "row_inner", "row", "module" ] ) &&
               _.isUndefined( this.options.model.attributes.tm_pb_global_module ) &&
               _.isUndefined( this.options.model.attributes.tm_pb_global_parent ) &&
               this.options.model.attributes.tm_pb_locked !== "on" &&
               tm_pb_options.is_divi_library !== "1" ) {
              has_option = true;
            }
          break;
        case "undo" :
            if ( this.hasOptionSupport( [ "app", "section", "row_inner", "row", "column", "column_inner", "module" ] ) &&
               this.hasUndo() ) {
              has_option = true;
            }
          break;
        case "redo" :
            if ( this.hasOptionSupport( [ "app", "section", "row_inner", "row", "column", "column_inner", "module" ] ) &&
               this.hasRedo() ) {
              has_option = true;
            }
          break;
        case "disable" :
            if ( this.hasOptionSupport( [ "section", "row_inner", "row", "module" ] ) &&
               this.options.model.attributes.tm_pb_locked !== "on" &&
               this.hasDisabledParent() === false &&
               _.isUndefined( this.model.attributes.tm_pb_skip_module ) ) {
              has_option = true;
            }
          break;
        case "lock" :
            if ( this.hasOptionSupport( [ "section", "row_inner", "row", "module" ] ) &&
               _.isUndefined( this.model.attributes.tm_pb_skip_module ) ) {
              has_option = true;
            }
          break;
        case "collapse" :
            if ( this.hasOptionSupport( [ "section", "row_inner", "row" ] ) &&
               this.options.model.attributes.tm_pb_locked !== "on" &&
               _.isUndefined( this.model.attributes.tm_pb_skip_module ) ) {
              has_option = true;
            }
          break;
        case "copy" :
            if ( this.hasOptionSupport( [ "section", "row_inner", "row", "module" ] ) &&
               this.tm_pb_has_storage_support &&
               this.options.model.attributes.tm_pb_locked !== "on" &&
               _.isUndefined( this.model.attributes.tm_pb_skip_module ) ) {
              has_option = true;
            }
          break;
        case "paste-after" :
            if ( this.hasOptionSupport( [ "section", "row_inner", "row", "module" ] ) &&
               this.tm_pb_has_storage_support &&
               this.has_compatible_clipboard_content &&
               this.options.model.attributes.tm_pb_locked !== "on" ) {
              has_option = true;
            }
          break;
        case "paste-app" :
            if ( this.hasOptionSupport( [ "app" ] ) &&
               this.tm_pb_has_storage_support &&
               TM_PB_Clipboard.get( "tm_pb_clipboard_section" ) ) {
              has_option = true;
            }
          break;
        case "paste-column" :
            if ( ! _.isUndefined( this.model.attributes.is_insert_module ) &&
              ( ( ( this.type === "column" || this.type == "column_inner" ) && TM_PB_Clipboard.get( "tm_pb_clipboard_module" ) ) || ( this.type === "section" && TM_PB_Clipboard.get( "tm_pb_clipboard_module_fullwidth" ) ) ) &&
              this.tm_pb_has_storage_support ) {
              has_option = true;
            }
          break;
        case "preview" :
            if ( this.hasOptionSupport( [ "section", "row_inner", "row", "module" ] ) &&
              this.options.model.attributes.tm_pb_locked !== "on" ) {
              has_option = true;
            }
          break;
      }

      return has_option;
    },

    hasOptionSupport : function( whitelisted_types ) {
      if ( _.isUndefined( _.findWhere( whitelisted_types, this.type ) ) ) {
        return false;
      }

      return true;
    },

    hasUndo : function() {
      return TM_PageBuilder_App.hasUndo();
    },

    hasRedo : function() {
      return TM_PageBuilder_App.hasRedo();
    },

    hasDisabledParent : function() {
      var parent_view = TM_PageBuilder_Layout.getView( this.model.attributes.parent ),
        parent_views = {},
        has_disabled_parents = false;

      // Loop until parent_view is undefined (reaches section)
      while ( ! _.isUndefined( parent_view  ) ) {
        // Check whether current parent is disabled or not
        if ( ! _.isUndefined( parent_view.model.attributes.tm_pb_disabled ) && parent_view.model.attributes.tm_pb_disabled === "on" ) {
          has_disabled_parents = true;
        }

        // Append views to object
        parent_views[parent_view.model.attributes.cid] = parent_view;

        // Refresh parent_view for new loop
        parent_view = TM_PageBuilder_Layout.getView( parent_view.model.attributes.parent );
      }

      return has_disabled_parents;
    },

    preview : function( event ) {
      event.preventDefault();

      // Get item's view
      var view = TM_PageBuilder_Layout.getView( this.model.get( 'cid' ) );

      // Close all right click options
      this.closeAllRightClickOptions();

      // Tell view that it is initiated from right click options so it can tell modalView
      view.triggered_by_right_click = true;

      // Tell modal view that this instance is intended for previewing
      // This is specifically needed for global module
      view.do_preview = true;

      // Display ModalView
      view.showSettings( event );

      // Emulate preview clicking
      $('.tm-pb-modal-preview-template').trigger( 'click' );
    }
  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, window.wp.Backbone ) );
