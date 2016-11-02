( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.RowView = Backbone.View.extend( {
    className : 'tm_pb_row',

    template : _.template( $('#tm-builder-row-template').html() ),

    events : {
      'click .tm-pb-settings-row' : 'showSettings',
      'click .tm-pb-insert-column' : 'displayColumnsOptions',
      'click .tm-pb-clone-row' : 'cloneRow',
      'click .tm-pb-row-add' : 'addNewRow',
      'click .tm-pb-remove-row' : 'removeRow',
      'click .tm-pb-change-structure' : 'changeStructure',
      'click .tm-pb-expand' : 'expandRow',
      'contextmenu .tm-pb-row-add' : 'showRightClickOptions',
      'click.tm_pb_row > .tm-pb-controls .tm-pb-unlock' : 'unlockRow',
      'contextmenu.tm_pb_row > .tm-pb-controls' : 'showRightClickOptions',
      'contextmenu.tm_pb_row > .tm-pb-right-click-trigger-overlay' : 'showRightClickOptions',
      'contextmenu .tm-pb-column' : 'showRightClickOptions',
      'click.tm_pb_row > .tm-pb-controls' : 'hideRightClickOptions',
      'click.tm_pb_row > .tm-pb-right-click-trigger-overlay' : 'hideRightClickOptions',
      'click > .tm-pb-locked-overlay' : 'showRightClickOptions',
      'contextmenu > .tm-pb-locked-overlay' : 'showRightClickOptions',
    },

    initialize : function() {
      this.listenTo( TM_PageBuilder_Events, 'tm-add:columns', this.toggleInsertColumnButton );
      this.listenTo( this.model, 'change:admin_label', this.renameModule );
      this.listenTo( this.model, 'change:tm_pb_disabled', this.toggleDisabledClass );
    },

    render : function() {
      var parent_views = TM_PageBuilder_Layout.getParentViews( this.model.get( 'parent' ) );

      if ( typeof this.model.get( 'view' ) !== 'undefined' && typeof this.model.get( 'view' ).model.get( 'layout_specialty' ) !== 'undefined' ) {
        this.model.set( 'specialty_row', '1', { silent : true } );
      }

      this.$el.html( this.template( this.model.toJSON() ) );

      if ( typeof this.model.get( 'tm_pb_global_module' ) !== 'undefined' || ( typeof this.model.get( 'tm_pb_template_type' ) !== 'undefined' && 'row' === this.model.get( 'tm_pb_template_type' ) && 'global' === tm_pb_options.is_global_template ) ) {
        this.$el.addClass( 'tm_pb_global' );
      }

      if ( typeof this.model.get( 'tm_pb_disabled' ) !== 'undefined' && this.model.get( 'tm_pb_disabled' ) === 'on' ) {
        this.$el.addClass( 'tm_pb_disabled' );
      }

      if ( typeof this.model.get( 'tm_pb_locked' ) !== 'undefined' && this.model.get( 'tm_pb_locked' ) === 'on' ) {
        this.$el.addClass( 'tm_pb_locked' );

        _.each( parent_views, function( parent ) {
          parent.$el.addClass( 'tm_pb_children_locked' );
        } );
      }

      if ( typeof this.model.get( 'tm_pb_parent_locked' ) !== 'undefined' && this.model.get( 'tm_pb_parent_locked' ) === 'on' ) {
        this.$el.addClass( 'tm_pb_parent_locked' );
      }

      if ( typeof this.model.get( 'tm_pb_collapsed' ) !== 'undefined' && this.model.get( 'tm_pb_collapsed' ) === 'on' ) {
        this.$el.addClass( 'tm_pb_collapsed' );
      }

      if ( typeof this.model.get( 'pasted_module' ) !== 'undefined' && this.model.get( 'pasted_module' ) ) {
        tm_pb_handle_clone_class( this.$el );
      }

      return this;
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

      if ( this.isRowLocked() ) {
        return;
      }

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

      if ( ( typeof modal_view.model.get( 'tm_pb_global_module' ) !== 'undefined' && '' !== modal_view.model.get( 'tm_pb_global_module' ) ) || ( TM_PageBuilder_Layout.getView( modal_view.model.get('cid') ).$el.closest( '.tm_pb_global' ).length ) || ( typeof this.model.get( 'tm_pb_template_type' ) !== 'undefined' && 'row' === this.model.get( 'tm_pb_template_type' ) && 'global' === tm_pb_options.is_global_template ) ) {
        $( '.tm_pb_modal_settings_container' ).addClass( 'tm_pb_saved_global_modal' );

        var saved_tabs = [ 'general', 'advanced', 'custom_css' ];
        _.each( saved_tabs, function( tab_name ) {
          $( '.tm_pb_options_tab_' + tab_name ).addClass( 'tm_pb_saved_global_tab' );
        });
      }
    },

    displayColumnsOptions : function( event ) {
      if ( event ) {
        event.preventDefault();
      }

      if ( this.isRowLocked() ) {
        return;
      }

      var view,
        this_view = this;

      this.model.set( 'open_view', 'column_settings', { silent : true } );

      view = new TM_PageBuilder.ModalView( {
        model : this.model,
        collection : this.collection,
        attributes : {
          'data-open_view' : 'column_settings'
        },
        view : this_view
      } );

      $('body').append( view.render().el );

      this.toggleInsertColumnButton();
    },

    changeStructure : function( event ) {
      event.preventDefault();

      var view,
        this_view = this;

      if ( this.isRowLocked() ) {
        return;
      }

      this.model.set( 'change_structure', 'true', { silent : true } );

      this.model.set( 'open_view', 'column_settings', { silent : true } );

      TM_PageBuilder.Events = TM_PageBuilder_Events;
      view = new TM_PageBuilder.ModalView( {
        model : this.model,
        collection : this.collection,
        attributes : {
          'data-open_view' : 'column_settings'
        },
        view : this_view
      } );

      $('body').append( view.render().el );
    },

    expandRow : function( event ) {
      event.preventDefault();

      var $parent = this.$el.closest('.tm_pb_row');

      $parent.removeClass('tm_pb_collapsed');

      // Add attribute to shortcode
      this.options.model.attributes.tm_pb_collapsed = 'off';

      // Enable history saving and set meta for history
      TM_PageBuilder_App.allowHistorySaving( 'expanded', 'row' );

      // Rebuild shortcodes
      TM_PageBuilder_App.saveAsShortcode();
    },

    unlockRow : function( event ) {
      event.preventDefault();

      var this_el = this,
        $parent = this_el.$el.closest('.tm_pb_row'),
        request = tm_pb_user_lock_permissions(),
        children_views,
        parent_views;

      request.done( function ( response ) {
        if ( true === response ) {
          $parent.removeClass('tm_pb_locked');

          // Add attribute to shortcode
          this_el.options.model.attributes.tm_pb_locked = 'off';

          children_views = TM_PageBuilder_Layout.getChildrenViews( this_el.model.get('cid') );

          _.each( children_views, function( view, key ) {
            view.$el.removeClass('tm_pb_parent_locked');
            view.model.set( 'tm_pb_parent_locked', 'off', { silent : true } );
          } );

          parent_views = TM_PageBuilder_Layout.getParentViews( this_el.model.get('parent') );

          _.each( parent_views, function( view, key ) {
            if ( ! TM_PageBuilder_Layout.isChildrenLocked( view.model.get( 'cid' ) ) ) {
              view.$el.removeClass('tm_pb_children_locked');
            }
          } );

          // Enable history saving and set meta for history
          TM_PageBuilder_App.allowHistorySaving( 'unlocked', 'row' );

          // Rebuild shortcodes
          TM_PageBuilder_App.saveAsShortcode();
        } else {
          alert( tm_pb_options.locked_row_permission_alert );
        }
      });
    },

    toggleInsertColumnButton : function() {
      var model_id = this.model.get( 'cid' ),
        columnsInRow;

      // check if the current row has at least one column
      columnsInRow = this.collection.find( function( model ) {
        return ( model.get( 'type' ) === 'column' || model.get( 'type' ) === 'column_inner' ) && model.get( 'parent' ) === model_id;
      } );

      if ( ! _.isUndefined( columnsInRow ) ) {
        this.$( '.tm-pb-insert-column' ).hide();

        // show "change columns structure" icon, if current row's column layout is set
        this.$( '.tm-pb-change-structure' ).show();
      }
    },

    addNewRow : function( event ) {
      var $parent_section = this.$el.closest( '.tm-pb-section-content' ),
        $current_target = $( event.currentTarget ),
        parent_view_cid = $current_target.closest( '.tm-pb-column-specialty' ).length ? $current_target.closest( '.tm-pb-column-specialty' ).data( 'cid' ) : $parent_section.data( 'cid' ),
        parent_view = TM_PageBuilder_Layout.getView( parent_view_cid );

      event.preventDefault();

      tm_pb_close_all_right_click_options();

      if ( 'on' === this.model.get( 'tm_pb_parent_locked' ) ) {
        return;
      }

      // Enable history saving and set meta for history
      TM_PageBuilder_App.allowHistorySaving( 'added', 'row' );

      parent_view.addRow( this.$el );

    },

    cloneRow : function( event ) {
      var global_module_cid = '',
        parent_view = TM_PageBuilder_Layout.getView( this.model.get( 'parent' ) ),
        clone_row,
        view_settings = {
          model      : this.model,
          view       : this.$el,
          view_event : event
        };

      event.preventDefault();

      if ( this.isRowLocked() ) {
        return;
      }

      if ( this.$el.closest( '.tm_pb_section.tm_pb_global' ).length && typeof parent_view.model.get( 'tm_pb_template_type' ) === 'undefined' ) {
        global_module_cid = this.model.get( 'global_parent_cid' );
      }

      clone_row = new TM_PageBuilder.RightClickOptionsView( view_settings, true );

      // Enable history saving and set meta for history
      TM_PageBuilder_App.allowHistorySaving( 'cloned', 'row' );

      clone_row.copy( event );

      clone_row.pasteAfter( event );

      if ( '' !== global_module_cid ) {
        tm_pb_update_global_template( global_module_cid );
      }
    },

    removeRow : function( event, force ) {
      var columns,
        global_module_cid = '',
        parent_view = TM_PageBuilder_Layout.getView( this.model.get( 'parent' ) );

      if ( this.isRowLocked() || TM_PageBuilder_Layout.isChildrenLocked( this.model.get( 'cid' ) ) ) {
        return;
      }

      if ( event ) {
        event.preventDefault();

        // don't allow to remove a specialty section, even if there is only one row in it
        if ( this.$el.closest( '.tm-pb-column-specialty' ).length ) {
          event.stopPropagation();
        }

        if ( this.$el.closest( '.tm_pb_section.tm_pb_global' ).length && typeof parent_view.model.get( 'tm_pb_template_type' ) === 'undefined' ) {
          global_module_cid = this.model.get( 'global_parent_cid' );
        }
      }

      columns = TM_PageBuilder_Layout.getChildViews( this.model.get('cid') );

      _.each( columns, function( column ) {
        column.removeColumn();
      } );

      // if there is only one row in the section, don't remove it
      if ( TM_PageBuilder_Layout.get( 'forceRemove' ) || TM_PageBuilder_Layout.getNumberOf( 'row', this.model.get('parent') ) > 1 ) {
        this.model.destroy();

        TM_PageBuilder_Layout.removeView( this.model.get('cid') );

        this.remove();
      } else {
        this.$( '.tm-pb-insert-column' ).show();

        // hide "change columns structure" icon, column layout can be re-applied using "Insert column(s)" button
        this.$( '.tm-pb-change-structure' ).hide();
      }

      // Enable history saving and set meta for history
      TM_PageBuilder_App.allowHistorySaving( 'removed', 'row' );

      // trigger remove event if the row was removed manually ( using a button )
      if ( event ) {
        TM_PageBuilder_Events.trigger( 'tm-module:removed' );
      }

      if ( '' !== global_module_cid ) {
        tm_pb_update_global_template( global_module_cid );
      }
    },

    isRowLocked : function() {
      if ( 'on' === this.model.get( 'tm_pb_locked' ) || 'on' === this.model.get( 'tm_pb_parent_locked' ) ) {
        return true;
      }

      return false;
    },

    showRightClickOptions : function( event ) {
      event.preventDefault();
      var $event_target = $( event.target ),
        tm_right_click_options_view,
        view_settings;

      // Do nothing if Module or "Insert Module" clicked
      if ( $event_target.closest( '.tm-pb-insert-module' ).length || $event_target.hasClass( 'tm_pb_module_block' ) || $event_target.closest( '.tm_pb_module_block' ).length ) {
        return;
      }

      tm_right_click_options_view,
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
    },

    renameModule : function() {
      this.$( '.tm-pb-row-title' ).html( this.model.get( 'admin_label' ) );
    },

    toggleDisabledClass : function() {
      if ( typeof this.model.get( 'tm_pb_disabled' ) !== 'undefined' && 'on' === this.model.get( 'tm_pb_disabled' ) ) {
        this.$el.addClass( 'tm_pb_disabled' );
      } else {
        this.$el.removeClass( 'tm_pb_disabled' );
      }
    }
  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, window.wp.Backbone ) );
