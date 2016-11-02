( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.BlockModuleView = Backbone.View.extend( {

    className : function() {
      var className = 'tm_pb_module_block';

      if ( typeof this.model.attributes.className !== 'undefined' ) {
        className += this.model.attributes.className;
      }

      return className;
    },

    template : _.template( $( '#tm-builder-block-module-template' ).html() ),

    initialize : function() {
      this.listenTo( this.model, 'change:admin_label', this.renameModule );
      this.listenTo( this.model, 'change:tm_pb_disabled', this.toggleDisabledClass );
      this.listenTo( this.model, 'change:tm_pb_global_module', this.removeGlobal );
    },

    events : {
      'click .tm-pb-settings' : 'showSettings',
      'click .tm-pb-clone-module' : 'cloneModule',
      'click .tm-pb-remove-module' : 'removeModule',
      'click .tm-pb-unlock' : 'unlockModule',
      'contextmenu' : 'showRightClickOptions',
      'click' : 'hideRightClickOptions',
    },

    render : function() {
      var parent_views = TM_PageBuilder_Layout.getParentViews( this.model.get( 'parent' ) );

      this.$el.html( this.template( this.model.attributes ) );

      if ( typeof this.model.attributes.tm_pb_global_module !== 'undefined' || ( typeof this.model.attributes.tm_pb_template_type !== 'undefined' && 'module' === this.model.attributes.tm_pb_template_type && 'global' === tm_pb_options.is_global_template ) ) {
        this.$el.addClass( 'tm_pb_global' );
      }

      if ( typeof this.model.get( 'tm_pb_locked' ) !== 'undefined' && this.model.get( 'tm_pb_locked' ) === 'on' ) {
        _.each( parent_views, function( parent ) {
          parent.$el.addClass( 'tm_pb_children_locked' );
        } );
      }

      if ( typeof this.model.get( 'tm_pb_parent_locked' ) !== 'undefined' && this.model.get( 'tm_pb_parent_locked' ) === 'on' ) {
        this.$el.addClass( 'tm_pb_parent_locked' );
      }

      if ( TM_PageBuilder_Layout.isModuleFullwidth( this.model.get( 'module_type' ) ) )
        this.$el.addClass( 'tm_pb_fullwidth_module' );

      if ( typeof this.model.get( 'pasted_module' ) !== 'undefined' && this.model.get( 'pasted_module' ) ) {
        tm_pb_handle_clone_class( this.$el );
      }

      return this;
    },

    cloneModule : function( event ) {
      var global_module_cid = '',
        clone_module,
        view_settings = {
          model      : this.model,
          view       : this.$el,
          view_event : event
        };

      event.preventDefault();

      if ( this.isModuleLocked() ) {
        return;
      }

      if ( typeof this.model.get( 'tm_pb_global_module' ) !== 'undefined' ) {
        global_module_cid = this.model.get( 'cid' );
      }

      clone_module = new TM_PageBuilder.RightClickOptionsView( view_settings, true );

      // Enable history saving and set meta for history
      TM_PageBuilder_App.allowHistorySaving( 'cloned', 'module', this.model.get( 'admin_label' ) );

      clone_module.copy( event );

      clone_module.pasteAfter( event );

      if ( '' !== global_module_cid ) {
        tm_pb_update_global_template( global_module_cid );
      }
    },

    renameModule : function() {
      this.$( '.tm-pb-module-title' ).html( this.model.get( 'admin_label' ) );
    },

    removeGlobal : function() {
      if ( this.isModuleLocked() ) {
        return;
      }

      if ( typeof this.model.get( 'tm_pb_global_module' ) === 'undefined' ) {
        this.$el.removeClass( 'tm_pb_global' );
      }
    },

    toggleDisabledClass : function() {
      if ( typeof this.model.get( 'tm_pb_disabled' ) !== 'undefined' && 'on' === this.model.get( 'tm_pb_disabled' ) ) {
        this.$el.addClass( 'tm_pb_disabled' );
      } else {
        this.$el.removeClass( 'tm_pb_disabled' );
      }
    },

    showSettings : function( event ) {
      var that = this,
        modal_view,
        view_settings = {
          model : this.model,
          collection : this.collection,
          attributes : {
            'data-open_view' : 'module_settings'
          },
          triggered_by_right_click : this.triggered_by_right_click,
          do_preview : this.do_preview
        };

      if ( typeof event !== 'undefined' ) {
        event.preventDefault();
      }

      if ( this.isModuleLocked() ) {
        return;
      }

      if ( typeof this.model.get( 'tm_pb_global_module' ) !== 'undefined' && '' !== this.model.get( 'tm_pb_global_module' ) ) {
        tm_builder_get_global_module( view_settings );

        // Set marker variable to undefined after being used to prevent unwanted preview
        this.triggered_by_right_click = undefined;
        this.do_preview = undefined;
      } else {
        modal_view = new TM_PageBuilder.ModalView( view_settings );
        if ( false === modal_view.render() ) {
          setTimeout( function() {
            that.showSettings();
          }, 500 );

          TM_PageBuilder_Events.trigger( 'tm-pb-loading:started' );

          return;
        }

        TM_PageBuilder_Events.trigger( 'tm-pb-loading:ended' );

        $('body').append( modal_view.render().el );
      }

      // set initial active tab for partially saved module templates.
      tm_pb_open_current_tab();

      if ( ( typeof this.model.get( 'tm_pb_global_parent' ) !== 'undefined' && '' !== this.model.get( 'tm_pb_global_parent' ) ) || ( TM_PageBuilder_Layout.getView( this.model.get('cid') ).$el.closest( '.tm_pb_global' ).length ) ) {
        $( '.tm_pb_modal_settings_container' ).addClass( 'tm_pb_saved_global_modal' );

        var saved_tabs = [ 'general', 'advanced', 'custom_css' ];
        _.each( saved_tabs, function( tab_name ) {
          $( '.tm_pb_options_tab_' + tab_name ).addClass( 'tm_pb_saved_global_tab' );
        });
      }
    },

    removeModule : function( event ) {
      var global_module_cid = '';

      if ( this.isModuleLocked() ) {
        return;
      }

      if ( event ) {
        event.preventDefault();

        if ( ( this.$el.closest( '.tm_pb_section.tm_pb_global' ).length || this.$el.closest( '.tm_pb_row.tm_pb_global' ).length ) && '' === tm_pb_options.template_post_id ) {
          global_module_cid = this.model.get( 'global_parent_cid' );
        }
      }

      this.model.destroy();

      // Enable history saving and set meta for history
      TM_PageBuilder_App.allowHistorySaving( 'removed', 'module', this.model.get( 'admin_label' ) );

      TM_PageBuilder_Layout.removeView( this.model.get('cid') );

      this.remove();

      // if single module is removed from the builder
      if ( event ) {
        TM_PageBuilder_Events.trigger( 'tm-module:removed' );
      }

      if ( '' !== global_module_cid ) {
        tm_pb_update_global_template( global_module_cid );
      }
    },

    unlockModule : function( event ) {
      event.preventDefault();

      var this_el = this,
        $parent = this_el.$el.closest('.tm_pb_module_block'),
        request = tm_pb_user_lock_permissions(),
        parent_views;

      request.done( function ( response ) {
        if ( true === response ) {
          $parent.removeClass('tm_pb_locked');

          // Add attribute to shortcode
          this_el.options.model.attributes.tm_pb_locked = 'off';

          parent_views = TM_PageBuilder_Layout.getParentViews( this_el.model.get('parent') );

          _.each( parent_views, function( view, key ) {
            if ( ! TM_PageBuilder_Layout.isChildrenLocked( view.model.get( 'cid' ) ) ) {
              view.$el.removeClass('tm_pb_children_locked');
            }
          } );

          // Enable history saving and set meta for history
          TM_PageBuilder_App.allowHistorySaving( 'unlocked', 'module', this_el.options.model.get( 'admin_label' ) );

          // Rebuild shortcodes
          TM_PageBuilder_App.saveAsShortcode();
        } else {
          alert( tm_pb_options.locked_module_permission_alert );
        }
      });
    },

    isModuleLocked : function() {
      if ( 'on' === this.model.get( 'tm_pb_locked' ) || 'on' === this.model.get( 'tm_pb_parent_locked' ) ) {
        return true;
      }

      return false;
    },

    showRightClickOptions : function( event ) {
      event.preventDefault();

      var tm_right_click_options_view,
        view_settings = {
          model      : this.model,
          view       : this.$el,
          view_event : event
        };

      tm_right_click_options_view = new TM_PageBuilder.RightClickOptionsView( view_settings );
    },

    hideRightClickOptions : function( event ) {
      event.preventDefault();

      tm_pb_close_all_right_click_options();
    }
  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, window.wp.Backbone ) );
