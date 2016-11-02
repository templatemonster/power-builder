( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.SectionView = Backbone.View.extend( {

    className : 'tm_pb_section',

    template : _.template( $('#tm-builder-section-template').html() ),

    events: {
      'click .tm-pb-settings-section' : 'showSettings',
      'click .tm-pb-clone-section' : 'cloneSection',
      'click .tm-pb-remove-section' : 'removeSection',
      'click .tm-pb-section-add-main' : 'addSection',
      'click .tm-pb-section-add-fullwidth' : 'addFullwidthSection',
      'click .tm-pb-section-add-specialty' : 'addSpecialtySection',
      'click .tm-pb-section-add-saved' : 'addSavedSection',
      'click .tm-pb-expand' : 'expandSection',
      'contextmenu .tm-pb-section-add' : 'showRightClickOptions',
      'click.tm_pb_section > .tm-pb-controls .tm-pb-unlock' : 'unlockSection',
      'contextmenu.tm_pb_section > .tm-pb-controls' : 'showRightClickOptions',
      'contextmenu.tm_pb_row > .tm-pb-right-click-trigger-overlay' : 'showRightClickOptions',
      'click.tm_pb_section > .tm-pb-controls' : 'hideRightClickOptions',
      'click.tm_pb_row > .tm-pb-right-click-trigger-overlay' : 'hideRightClickOptions',
      'click > .tm-pb-locked-overlay' : 'showRightClickOptions',
      'contextmenu > .tm-pb-locked-overlay' : 'showRightClickOptions',
    },

    initialize : function() {
      this.child_views = [];
      this.listenTo( this.model, 'change:admin_label', this.renameModule );
      this.listenTo( this.model, 'change:tm_pb_disabled', this.toggleDisabledClass );
    },

    render : function() {
      this.$el.html( this.template( this.model.toJSON() ) );

      if ( this.model.get( 'tm_pb_specialty' ) === 'on' ) {
        this.$el.addClass( 'tm_pb_section_specialty' );

        if ( this.model.get( 'tm_pb_specialty_placeholder' ) === 'true' ) {
          this.$el.addClass( 'tm_pb_section_placeholder' );
        }
      }

      if ( typeof this.model.get( 'tm_pb_global_module' ) !== 'undefined' || ( typeof this.model.get( 'tm_pb_template_type' ) !== 'undefined' && 'section' === this.model.get( 'tm_pb_template_type' ) && 'global' === tm_pb_options.is_global_template ) ) {
        this.$el.addClass( 'tm_pb_global' );
      }

      if ( typeof this.model.get( 'tm_pb_disabled' ) !== 'undefined' && this.model.get( 'tm_pb_disabled' ) === 'on' ) {
        this.$el.addClass( 'tm_pb_disabled' );
      }

      if ( typeof this.model.get( 'tm_pb_locked' ) !== 'undefined' && this.model.get( 'tm_pb_locked' ) === 'on' ) {
        this.$el.addClass( 'tm_pb_locked' );
      }

      if ( typeof this.model.get( 'tm_pb_collapsed' ) !== 'undefined' && this.model.get( 'tm_pb_collapsed' ) === 'on' ) {
        this.$el.addClass( 'tm_pb_collapsed' );
      }

      if ( typeof this.model.get( 'pasted_module' ) !== 'undefined' && this.model.get( 'pasted_module' ) ) {
        tm_pb_handle_clone_class( this.$el );
      }

      this.makeRowsSortable();

      return this;
    },

    showSettings : function( event ) {
      var that = this,
        $current_target = typeof event !== 'undefined' ? $( event.currentTarget ) : '',
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

      if ( this.isSectionLocked() ) {
        return;
      }

      if ( '' !== $current_target && $current_target.closest( '.tm_pb_section_specialty' ).length ) {
        var $specialty_section_columns = $current_target.closest( '.tm_pb_section_specialty' ).find( '.tm-pb-section-content > .tm-pb-column' ),
          columns_layout = '';

        if ( $specialty_section_columns.length ) {
          $specialty_section_columns.each( function() {
            columns_layout += '' === columns_layout ? '1_1' : ',1_1';
          });
        }

        view_settings.model.attributes.columns_layout = columns_layout;

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

      if ( ( typeof modal_view.model.get( 'tm_pb_global_module' ) !== 'undefined' && '' !== modal_view.model.get( 'tm_pb_global_module' ) ) || ( typeof this.model.get( 'tm_pb_template_type' ) !== 'undefined' && 'section' === this.model.get( 'tm_pb_template_type' ) && 'global' === tm_pb_options.is_global_template ) ) {
        $( '.tm_pb_modal_settings_container' ).addClass( 'tm_pb_saved_global_modal' );

        var saved_tabs = [ 'general', 'advanced', 'custom_css' ];
        _.each( saved_tabs, function( tab_name ) {
          $( '.tm_pb_options_tab_' + tab_name ).addClass( 'tm_pb_saved_global_tab' );
        });
      }

      if ( typeof this.model.get( 'tm_pb_specialty' ) === 'undefined' || 'on' !== this.model.get( 'tm_pb_specialty' ) ) {
        $( '.tm_pb_modal_settings_container' ).addClass( 'tm_pb_hide_advanced_tab' );
      }

      tm_pb_open_current_tab();
    },

    addSection : function( event ) {
      var module_id = TM_PageBuilder_Layout.generateNewId();

      event.preventDefault();

      tm_pb_close_all_right_click_options();

      // Enable history saving and set meta for history
      TM_PageBuilder_App.allowHistorySaving( 'added', 'section' );

      this.collection.add( [ {
        type : 'section',
        module_type : 'section',
        tm_pb_fullwidth : 'off',
        tm_pb_specialty : 'off',
        cid : module_id,
        view : this,
        created : 'auto',
        admin_label : tm_pb_options.noun['section']
      } ] );
    },

    addFullwidthSection : function( event ) {
      var module_id = TM_PageBuilder_Layout.generateNewId();

      event.preventDefault();

      tm_pb_close_all_right_click_options();

      // Enable history saving and set meta for history
      TM_PageBuilder_App.allowHistorySaving( 'added', 'fullwidth_section' );

      this.collection.add( [ {
        type : 'section',
        module_type : 'section',
        tm_pb_fullwidth : 'on',
        tm_pb_specialty : 'off',
        cid : module_id,
        view : this,
        created : 'auto',
        admin_label : tm_pb_options.noun['section']
      } ] );
    },

    addSpecialtySection : function( event ) {
      var module_id = TM_PageBuilder_Layout.generateNewId(),
        $event_target = $(event.target),
        template_type = typeof $event_target !== 'undefined' && typeof $event_target.data( 'is_template' ) !== 'undefined' ? 'section' : '';

      event.preventDefault();

      tm_pb_close_all_right_click_options();

      // Enable history saving and set meta for history
      TM_PageBuilder_App.allowHistorySaving( 'added', 'specialty_section' );

      this.collection.add( [ {
        type : 'section',
        module_type : 'section',
        tm_pb_fullwidth : 'off',
        tm_pb_specialty : 'on',
        cid : module_id,
        template_type : template_type,
        view : this,
        created : 'auto',
        admin_label : tm_pb_options.noun['section']
      } ] );
    },

    addSavedSection : function( event ) {
      var parent_cid = this.model.get( 'cid' ),
        view_settings = {
          attributes : {
            'data-open_view' : 'saved_templates',
            'data-parent_cid' : parent_cid
          },
          view : this
        },
        main_view = new TM_PageBuilder.ModalView( view_settings );

      tm_pb_close_all_right_click_options();

      $( 'body' ).append( main_view.render().el );

      generate_templates_view( 'include_global', '', 'section', $( '.tm-pb-saved-modules-tab' ), 'regular', 0, 'all' );

      event.preventDefault();
    },

    expandSection : function( event ) {
      event.preventDefault();

      var $parent = this.$el.closest('.tm_pb_section');

      $parent.removeClass('tm_pb_collapsed');

      // Add attribute to shortcode
      this.options.model.attributes.tm_pb_collapsed = 'off';

      // Enable history saving and set meta for history
      TM_PageBuilder_App.allowHistorySaving( 'expanded', 'section' );

      // Rebuild shortcodes
      TM_PageBuilder_App.saveAsShortcode();
    },

    unlockSection : function( event ) {
      event.preventDefault();

      var this_el = this,
        $parent = this_el.$el.closest('.tm_pb_section'),
        request = tm_pb_user_lock_permissions(),
        children_views;

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

          // Enable history saving and set meta for history
          TM_PageBuilder_App.allowHistorySaving( 'unlocked', 'section' );

          // Rebuild shortcodes
          TM_PageBuilder_App.saveAsShortcode();
        } else {
          alert( tm_pb_options.locked_section_permission_alert );
        }
      });
    },

    addRow : function( appendAfter ) {
      var module_id = TM_PageBuilder_Layout.generateNewId(),
        global_parent = typeof this.model.get( 'tm_pb_global_module' ) !== 'undefined' && '' !== this.model.get( 'tm_pb_global_module' ) ? this.model.get( 'tm_pb_global_module' ) : '',
        global_parent_cid = '' !== global_parent ? this.model.get( 'cid' ) : '',
        new_row_view;

      this.collection.add( [ {
        type : 'row',
        module_type : 'row',
        cid : module_id,
        parent : this.model.get( 'cid' ),
        view : this,
        appendAfter : appendAfter,
        tm_pb_global_parent : global_parent,
        global_parent_cid : global_parent_cid,
        admin_label : tm_pb_options.noun['row']
      } ] );
      new_row_view = TM_PageBuilder_Layout.getView( module_id );
      new_row_view.displayColumnsOptions();
    },

    cloneSection : function( event ) {
      event.preventDefault();

      if ( this.isSectionLocked() ) {
        return;
      }

      var $cloned_element = this.$el.clone(),
        content,
        clone_section,
        view_settings = {
          model      : this.model,
          view       : this.$el,
          view_event : event
        };

      clone_section = new TM_PageBuilder.RightClickOptionsView( view_settings, true );

      // Enable history saving and set meta for history
      TM_PageBuilder_App.allowHistorySaving( 'cloned', 'section' );

      clone_section.copy( event );

      clone_section.pasteAfter( event );
    },

    makeRowsSortable : function() {
      var this_el = this,
        sortable_el = this_el.model.get( 'tm_pb_fullwidth' ) !== 'on'
          ? '.tm-pb-section-content'
          : '.tm_pb_fullwidth_sortable_area',
        connectWith = ':not(.tm_pb_locked) > ' + sortable_el;

      if ( this_el.model.get( 'tm_pb_specialty' ) === 'on' ) {
        return;
      }

      this_el.$el.find( sortable_el ).sortable( {
        connectWith: connectWith,
        delay: 100,
        cancel : '.tm-pb-settings, .tm-pb-clone, .tm-pb-remove, .tm-pb-row-add, .tm-pb-insert-module, .tm-pb-insert-column, .tm_pb_locked, .tm-pb-disable-sort',
        update : function( event, ui ) {
          if ( ! $( ui.item ).closest( event.target ).length ) {

            // don't allow to move the row to another section if the section has only one row
            if ( ! $( event.target ).find( '.tm_pb_row' ).length ) {
              $(this).sortable( 'cancel' );
              alert( tm_pb_options.section_only_row_dragged_away );
            }

            // do not allow to drag rows into sections where sorting is disabled
            if ( $( ui.item ).closest( '.tm-pb-disable-sort').length ) {
              $( event.target ).sortable( 'cancel' );
            }
            // makes sure the code runs one time, if row is dragged into another section
            return;

          }

          if ( $( ui.item ).closest( '.tm_pb_section.tm_pb_global' ).length && $( ui.item ).hasClass( 'tm_pb_global' ) ) {
            $( ui.sender ).sortable( 'cancel' );
            alert( tm_pb_options.global_row_alert );
          } else if ( ( $( ui.item ).closest( '.tm_pb_section.tm_pb_global' ).length || $( ui.sender ).closest( '.tm_pb_section.tm_pb_global' ).length ) && '' === tm_pb_options.template_post_id ) {
            var module_cid = ui.item.data( 'cid' ),
                model,
                global_module_cid,
                $moving_from,
                $moving_to;

            $moving_from = $( ui.sender ).closest( '.tm_pb_section.tm_pb_global' );
            $moving_to = $( ui.item ).closest( '.tm_pb_section.tm_pb_global' );


            if ( $moving_from === $moving_to ) {
              model = this_el.collection.find( function( model ) {
                return model.get('cid') == module_cid;
              } );

              global_module_cid = model.get( 'global_parent_cid' );

              tm_pb_update_global_template( global_module_cid );
              tm_reinitialize_builder_layout();
            } else {
              var $global_element = $moving_from;
              for ( var i = 1; i <= 2; i++ ) {
                global_module_cid = $global_element.find( '.tm-pb-section-content' ).data( 'cid' );

                if ( typeof global_module_cid !== 'undefined' && '' !== global_module_cid ) {

                  tm_pb_update_global_template( global_module_cid );
                  tm_reinitialize_builder_layout();
                }

                $global_element = $moving_to;
              };
            }
          }

          TM_PageBuilder_Layout.setNewParentID( ui.item.find( '.tm-pb-row-content' ).data( 'cid' ), this_el.model.attributes.cid );

          // Enable history saving and set meta for history
          TM_PageBuilder_App.allowHistorySaving( 'moved', 'row' );

          TM_PageBuilder_Events.trigger( 'tm-sortable:update' );

          // Prepare collection sorting based on layout position
          var section_cid       = parseInt( $(this).attr( 'data-cid') ),
            sibling_row_index = 0;

          // Loop row block based on DOM position to ensure its index order
          $(this).find('.tm-pb-row-content').each(function(){
            sibling_row_index++;

            var sibling_row_cid = parseInt( $(this).data('cid') ),
              layout_index    = section_cid + sibling_row_index,
              sibling_model   = TM_PageBuilder_Modules.findWhere({ cid : sibling_row_cid });

            // Set layout_index
            sibling_model.set({ layout_index : layout_index });
          });

          // Sort collection based on layout_index
          TM_PageBuilder_Modules.comparator = 'layout_index';
          TM_PageBuilder_Modules.sort();
        },
        start : function( event, ui ) {
          tm_pb_close_all_right_click_options();
        }
      } );
    },

    addChildView : function( view ) {
      this.child_views.push( view );
    },

    removeChildViews : function() {
      _.each( this.child_views, function( view ) {
        if ( typeof view.model !== 'undefined' )
          view.model.destroy();

        view.remove();
      } );
    },

    removeSection : function( event, remove_all ) {
      var rows,
        remove_last_specialty_section = false;

      if ( event ) event.preventDefault();

      if ( this.isSectionLocked() || TM_PageBuilder_Layout.isChildrenLocked( this.model.get( 'cid' ) ) ) {
        return;
      }

      if ( this.model.get( 'tm_pb_fullwidth' ) === 'on' ) {
        this.removeChildViews();
      } else {
        rows = TM_PageBuilder_Layout.getChildViews( this.model.get('cid') );

        _.each( rows, function( row ) {
          if ( row.model.get( 'type' ) === 'column' ) {
            // remove column in specialty section
            row.removeColumn();
          } else {
            row.removeRow();
          }
        } );
      }

      // the only section left is specialty or fullwidth section
      if ( ! TM_PageBuilder_Layout.get( 'forceRemove' ) && ( this.model.get( 'tm_pb_specialty' ) === 'on' || this.model.get( 'tm_pb_fullwidth' ) === 'on' ) && TM_PageBuilder_Layout.getNumberOfModules( 'section' ) === 1 ) {
        remove_last_specialty_section = true;
      }

      // if there is only one section, don't remove it
      // allow to remove all sections if removeSection function is called directly
      // remove the specialty section even if it's the last one on the page
      if ( TM_PageBuilder_Layout.get( 'forceRemove' ) || remove_last_specialty_section || TM_PageBuilder_Layout.getNumberOfModules( 'section' ) > 1 ) {
        this.model.destroy();

        TM_PageBuilder_Layout.removeView( this.model.get('cid') );

        this.remove();
      }

      // start with the clean layout if the user removed the last specialty section on the page
      if ( remove_last_specialty_section ) {
        TM_PageBuilder_App.removeAllSections( true );

        return;
      }

      // Enable history saving and set meta for history
      if ( _.isUndefined( remove_all ) ) {
        TM_PageBuilder_App.allowHistorySaving( 'removed', 'section' );
      } else {
        TM_PageBuilder_App.allowHistorySaving( 'cleared', 'layout' );
      }

      // trigger remove event if the row was removed manually ( using a button )
      if ( event ) {
        TM_PageBuilder_Events.trigger( 'tm-module:removed' );
      }
    },

    isSectionLocked : function() {
      if ( 'on' === this.model.get( 'tm_pb_locked' ) ) {
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
    },

    renameModule : function() {
      this.$( '.tm-pb-section-title' ).html( this.model.get( 'admin_label' ) );
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
