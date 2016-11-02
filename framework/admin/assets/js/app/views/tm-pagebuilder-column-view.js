( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.ColumnView = Backbone.View.extend( {
    template : _.template( $('#tm-builder-column-template').html() ),

    events : {
      'click .tm-pb-insert-module' : 'addModule',
      'contextmenu > .tm-pb-insert-module' : 'showRightClickOptions',
      'click' : 'hideRightClickOptions'
    },

    initialize : function() {
      this.$el.attr( 'data-cid', this.model.get( 'cid' ) );
    },

    render : function() {
      var this_el = this,
        is_fullwidth_section = this.model.get( 'module_type' ) === 'section' && this.model.get( 'tm_pb_fullwidth' ) === 'on',
        connect_with = ( ! is_fullwidth_section ? ".tm-pb-column:not(.tm-pb-column-specialty, .tm_pb_parent_locked)" : ".tm_pb_fullwidth_sortable_area" );

      this.$el.html( this.template( this.model.toJSON() ) );

      if ( is_fullwidth_section )
        this.$el.addClass( 'tm_pb_fullwidth_sortable_area' );

      if ( this.model.get( 'layout_specialty' ) === '1' ) {
        connect_with = '.tm-pb-column-specialty:not(.tm_pb_parent_locked)';
      }

      if ( this.model.get( 'created' ) === 'manually' && ! _.isUndefined( this.model.get( 'tm_pb_specialty_columns' ) ) ) {
        this.$el.addClass( 'tm-pb-column-specialty' );
      }

      if ( this.isColumnParentLocked( this.model.get( 'parent' ) ) ) {
        this.$el.addClass( 'tm_pb_parent_locked' );
        this.model.set( 'tm_pb_parent_locked', 'on', { silent : true } );
      }

      this.$el.sortable( {
        cancel : '.tm-pb-settings, .tm-pb-clone, .tm-pb-remove, .tm-pb-insert-module, .tm-pb-insert-column, .tm_pb_locked, .tm-pb-disable-sort',
        connectWith: connect_with,
        delay: 100,
        items : ( this.model.get( 'layout_specialty' ) !== '1' ? '.tm_pb_module_block' : '.tm_pb_row' ),
        receive: function(event, ui) {
          var $this = $(this),
            columns_number,
            cancel_action = false;

          if ( $this.hasClass( 'tm-pb-column-specialty' ) ) {
            // revert if the last row is being dragged out of the specialty section
            // or the module block is placed directly into the section
            // or 3-column row is placed into the row that can't handle it
            if ( ! $( ui.sender ).find( '.tm_pb_row' ).length || $( ui.item ).is( '.tm_pb_module_block' ) ) {
              alert( tm_pb_options.section_only_row_dragged_away );
              cancel_action = true;
            } else {
              columns_number = $(ui.item).find( '.tm-pb-row-container > .tm-pb-column' ).length;

              if ( columns_number === 3 && parseInt( TM_PageBuilder_Layout.getView( $this.data( 'cid' ) ).model.get( 'specialty_columns' ) ) !== 3 ) {
                alert( tm_pb_options.stop_dropping_3_col_row );
                cancel_action = true;
              }
            }
          }

          // do not allow to drag modules into sections and rows where sorting is disabled
          if ( $( ui.item ).closest( '.tm-pb-disable-sort').length ) {
            cancel_action = true;
          }

          if ( ( $( ui.item ).closest( '.tm_pb_section.tm_pb_global' ).length || $( ui.item ).closest( '.tm_pb_row.tm_pb_global' ).length ) && $( ui.item ).hasClass( 'tm_pb_global' ) ) {
            alert( tm_pb_options.global_module_alert );
            cancel_action = true;
          } else if ( ( $( ui.item ).closest( '.tm_pb_section.tm_pb_global' ).length || $( ui.item ).closest( '.tm_pb_row.tm_pb_global' ).length || $( ui.sender ).closest( '.tm_pb_row.tm_pb_global' ).length || $( ui.sender ).closest( '.tm_pb_section.tm_pb_global' ).length ) && '' === tm_pb_options.template_post_id ) {
            var module_cid = ui.item.data( 'cid' ),
              model,
              global_module_cid,
              $moving_from,
              $moving_to;

            $moving_from = $( ui.sender ).closest( '.tm_pb_row.tm_pb_global' ).length ? $( ui.sender ).closest( '.tm_pb_row.tm_pb_global' ) : $( ui.sender ).closest( '.tm_pb_section.tm_pb_global' );
            $moving_to = $( ui.item ).closest( '.tm_pb_row.tm_pb_global' ).length ? $( ui.item ).closest( '.tm_pb_row.tm_pb_global' ) : $( ui.item ).closest( '.tm_pb_section.tm_pb_global' );


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
                global_module_cid = typeof $global_element.find( '.tm-pb-section-content' ).data( 'cid' ) !== 'undefined' ? $global_element.find( '.tm-pb-section-content' ).data( 'cid' ) : $global_element.find( '.tm-pb-row-content' ).data( 'cid' );

                if ( typeof global_module_cid !== 'undefined' && '' !== global_module_cid ) {

                  tm_pb_update_global_template( global_module_cid );
                  tm_reinitialize_builder_layout();
                }

                $global_element = $moving_to;
              };
            }
          }

          if ( cancel_action ) {
            $(ui.sender).sortable('cancel');
            tm_reinitialize_builder_layout();
          }
        },
        update : function( event, ui ) {
          var model,
            $module_block,
            module_cid = ui.item.data( 'cid' );

          $module_block = $( ui.item );

          if ( typeof module_cid === 'undefined' && $(event.target).is('.tm-pb-column-specialty') ) {
            $module_block = $( ui.item ).closest( '.tm_pb_row' ).find( '.tm-pb-row-content' );

            module_cid = $module_block.data( 'cid' );
          }

          // if the column doesn't have modules, add the dragged module before 'Insert Module' button or append to column
          if ( ! $(event.target).is('.tm-pb-column-specialty') && $( ui.item ).closest( event.target ).length && $( event.target ).find( '.tm_pb_module_block' ).length === 1 ) {
            // if .tm-pb-insert-module button exists, then add the module before that button. Otherwise append to column
            if ( $( event.target ).find( '.tm-pb-insert-module' ).length ) {
              $module_block.insertBefore( $( event.target ).find( '.tm-pb-insert-module' ) );
            } else {
              $( event.target ).append( $module_block );
            }
          }

          model = this_el.collection.find( function( model ) {
            return model.get('cid') == module_cid;
          } );

          // Enable history saving and set meta for history
          TM_PageBuilder_App.allowHistorySaving( 'moved', 'module', model.get( 'admin_label' ) );

          if ( model.get( 'parent' ) === this_el.model.attributes.cid && $( ui.item ).closest( event.target ).length ) {
            // order of items have been changed within the same row

            TM_PageBuilder_Events.trigger( 'tm-model-changed-position-within-column' );
          } else {
            model.set( 'parent', this_el.model.attributes.cid );
          }

          // Prepare collection sorting based on layout position
          var column_cid             = parseInt( $(this).attr( 'data-cid') ),
            sibling_module_index   = 0;

          // Loop module block based on DOM position to ensure its index order
          $(this).find('.tm_pb_module_block').each(function(){
            sibling_module_index++;

            var sibling_module_cid = parseInt( $(this).data('cid') ),
              layout_index       = column_cid + sibling_module_index,
              sibling_model      = TM_PageBuilder_Modules.findWhere({ cid : sibling_module_cid });

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

      return this;
    },

    addModule : function( event ) {
      var $event_target = $(event.target),
        $add_module_button = $event_target.is( 'span' ) ? $event_target.parent('.tm-pb-insert-module') : $event_target;

      event.preventDefault();
      event.stopPropagation();

      if ( this.isColumnLocked() )
        return;

      if ( ! $add_module_button.parent().is( event.delegateTarget ) ) {
        return;
      }

      tm_pb_close_all_right_click_options();

      var view;

      view = new TM_PageBuilder.ModalView( {
        model : this.model,
        collection : this.collection,
        attributes : {
          'data-open_view' : 'all_modules'
        },
        view : this
      } );

      $('body').append( view.render().el );
    },

    // Add New Row functionality for the specialty section column
    addRow : function( appendAfter ) {
      var module_id = TM_PageBuilder_Layout.generateNewId(),
        global_parent = typeof this.model.get( 'tm_pb_global_parent' ) !== 'undefined' && '' !== this.model.get( 'tm_pb_global_parent' ) ? this.model.get( 'tm_pb_global_parent' ) : '',
        global_parent_cid = '' !== global_parent ? this.model.get( 'global_parent_cid' ) : '',
        new_row_view;

      if ( this.isColumnLocked() ) {
        return;
      }

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

    removeColumn : function() {
      var modules;

      modules = TM_PageBuilder_Layout.getChildViews( this.model.get('cid') );

      _.each( modules, function( module ) {
        if ( module.model.get( 'type' ) === 'row' || module.model.get( 'type' ) === 'row_inner' ) {
          module.removeRow();
        } else {
          module.removeModule();
        }
      } );

      TM_PageBuilder_Layout.removeView( this.model.get('cid') );

      this.model.destroy();

      this.remove();
    },

    isColumnLocked : function() {
      if ( 'on' === this.model.get( 'tm_pb_locked' ) || 'on' === this.model.get( 'tm_pb_parent_locked' ) ) {
        return true;
      }

      return false;
    },

    isColumnParentLocked : function( cid ) {
      var parent_view = TM_PageBuilder_Layout.getView( cid );

      if ( ! _.isUndefined( parent_view ) && ( 'on' === parent_view.model.get('tm_pb_locked' ) || 'on' === parent_view.model.get('tm_pb_parent_locked' ) ) ) {
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

      // Fullwidth and regular section uses different type for column ( section vs column )
      // Add marker so it can be identified
      view_settings.model.attributes.is_insert_module = true;

      tm_right_click_options_view = new TM_PageBuilder.RightClickOptionsView( view_settings );

      return;
    },

    hideRightClickOptions : function( event ) {
      event.preventDefault();

      tm_pb_close_all_right_click_options();
    },

  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, window.wp.Backbone ) );
