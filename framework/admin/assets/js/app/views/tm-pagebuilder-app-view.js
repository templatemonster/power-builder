( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.AppView = Backbone.View.extend( {

    el : $('#tm_pb_main_container'),

    template : _.template( $('#tm-builder-app-template').html() ),

    template_button : _.template( $('#tm-builder-add-specialty-section-button').html() ),

    events: {
      'click .tm-pb-layout-buttons-save' : 'saveLayout',
      'click .tm-pb-layout-buttons-load' : 'loadLayout',
      'click .tm-pb-layout-buttons-clear' : 'clearLayout',
      'click .tm-pb-layout-buttons-history' : 'toggleHistory',
      'click #tm-pb-histories-visualizer-overlay' : 'closeHistory',
      'contextmenu #tm-pb-histories-visualizer-overlay' : 'closeHistory',
      'click .tm-pb-layout-buttons-redo' : 'redo',
      'click .tm-pb-layout-buttons-undo' : 'undo',
      'contextmenu .tm-pb-layout-buttons-save' : 'showRightClickOptions',
      'contextmenu .tm-pb-layout-buttons-load' : 'showRightClickOptions',
      'contextmenu .tm-pb-layout-buttons-clear' : 'showRightClickOptions',
      'contextmenu .tm-pb-layout-buttons-redo' : 'showRightClickOptions',
      'contextmenu .tm-pb-layout-buttons-undo' : 'showRightClickOptions',
      'contextmenu #tm_pb_main_container_right_click_overlay' : 'showRightClickOptions',
      'click #tm_pb_main_container_right_click_overlay' : 'hideRightClickOptions'
    },

    initialize : function() {
      this.listenTo( this.collection, 'add', this.addModule );
      this.listenTo( TM_PageBuilder_Histories, 'add', this.addVisualizeHistoryItem );
      this.listenTo( TM_PageBuilder_Histories, 'change', this.changeVisualizeHistoryItem );
      this.listenTo( TM_PageBuilder_Histories, 'remove', this.removeVisualizeHistoryItem );
      this.listenTo( TM_PageBuilder_Events, 'tm-sortable:update', _.debounce( this.saveAsShortcode, 128 ) );
      this.listenTo( TM_PageBuilder_Events, 'tm-model-changed-position-within-column', _.debounce( this.saveAsShortcode, 128 ) );
      this.listenTo( TM_PageBuilder_Events, 'tm-module:removed', _.debounce( this.saveAsShortcode, 128 ) );
      this.listenTo( TM_PageBuilder_Events, 'tm-pb-loading:started', this.startLoadingAnimation );
      this.listenTo( TM_PageBuilder_Events, 'tm-pb-loading:ended', this.endLoadingAnimation );
      this.listenTo( TM_PageBuilder_Events, 'tm-pb-content-updated', this.recalculateModulesOrder );
      this.listenTo( TM_PageBuilder_Events, 'tm-advanced-module:updated_order', this.updateAdvancedModulesOrder );

      this.$builder_toggle_button = $( 'body' ).find( '#tm_pb_toggle_builder' );
      this.$builder_toggle_button_wrapper = $( 'body' ).find( '.tm_pb_toggle_builder_wrapper' );

      this.render();

      this.maybeGenerateInitialLayout();
    },

    render : function() {
      this.$el.html( this.template() );

      this.makeSectionsSortable();

      this.addLoadingAnimation();

      $('#tm_pb_main_container_right_click_overlay').remove();

      this.$el.prepend('<div id="tm_pb_main_container_right_click_overlay" />');

      this.updateHistoriesButtonState();

      return this;
    },

    addLoadingAnimation : function() {
      $( 'body' ).append( '<div id="tm_pb_loading_animation"></div>' );

      this.$loading_animation = $( '#tm_pb_loading_animation' ).hide();
    },

    startLoadingAnimation : function() {
      if ( this.pageBuilderIsActive() ) {
        // place the loading animation container before the closing body tag
        if ( this.$loading_animation.next().length ) {
          $( 'body' ).append( this.$loading_animation );
          this.$loading_animation = $( '#tm_pb_loading_animation' );
        }

        this.$loading_animation.show();
      };
    },

    endLoadingAnimation : function() {
      this.$loading_animation.hide();
    },

    pageBuilderIsActive : function() {
      // check the button wrapper class as well because button may not be added in some cases
      return this.$builder_toggle_button.hasClass( 'tm_pb_builder_is_used' ) || this.$builder_toggle_button_wrapper.hasClass( 'tm_pb_builder_is_used' );
    },

    saveLayout : function( event ) {
      event.preventDefault();

      tm_pb_close_all_right_click_options();

      tm_pb_create_prompt_modal( 'save_layout' );
    },

    loadLayout : function( event ) {
      event.preventDefault();

      var view;

      tm_pb_close_all_right_click_options();

      view = new TM_PageBuilder.ModalView( {
        attributes : {
          'data-open_view' : 'save_layout'
        },
        view : this
      } );

      $('body').append( view.render().el );
    },

    clearLayout : function( event ) {
      event.preventDefault();

      tm_pb_close_all_right_click_options();

      tm_pb_create_prompt_modal( 'clear_layout' );
    },

    getHistoriesCount : function() {
      return this.options.history.length;
    },

    getHistoriesIndex : function() {
      var active_model       = this.options.history.findWhere({ current_active_history : true }),
        active_model_index = _.isUndefined( active_model ) ? ( this.options.history.models.length - 1 ) : this.options.history.indexOf( active_model );

      return active_model_index;
    },

    enableHistory : function() {
      if ( _.isUndefined( this.enable_history ) ) {
        return false;
      } else {
        return this.enable_history;
      }
    },

    allowHistorySaving : function( verb, noun, noun_alias, addition ) {
      this.enable_history = true;

      // Enable history saving and set meta for history
      TM_PageBuilder_Visualize_Histories.setHistoryMeta( verb, noun, noun_alias, addition );
    },

    reviseHistories : function() {
      var model,
        this_el = this;

      if ( this.hasRedo() ) {
        // Prepare reversed index (deleting unused model using ascending index changes the order of collection)
        var history_index = _.range( ( this.getHistoriesIndex() + 1 ), this.getHistoriesCount() ).reverse();

        // Loop the reversed index then delete the matched models
        _.each( history_index, function( index ) {
          model = this_el.options.history.at( index );
          this_el.options.history.remove( model );
        } );
      }

      // Update undo button state
      this.updateHistoriesButtonState();
    },

    resetCurrentActiveHistoryMarker : function() {
      var current_active_histories = this.options.history.where({ current_active_history : true });

      if ( ! _.isEmpty( current_active_histories ) ) {
        _.each( current_active_histories, function( current_active_history ) {
          current_active_history.set({ current_active_history : false });
        } );
      }

    },

    hasUndo : function() {
      return this.getHistoriesIndex() > 0 ? true : false;
    },

    hasRedo : function() {
      return ( this.getHistoriesCount() - this.getHistoriesIndex() ) > 1 ? true : false;
    },

    hasOverlayRendered : function() {
      if ( $('.tm_pb_modal_overlay').length ) {
        return true;
      }

      return false;
    },

    updateHistoriesButtonState : function() {
      if ( this.hasUndo() ) {
        $( '.tm-pb-layout-buttons-undo' ).removeClass( 'disabled' );
      } else {
        $( '.tm-pb-layout-buttons-undo' ).addClass( 'disabled' );
      }

      if ( this.hasRedo() ) {
        $( '.tm-pb-layout-buttons-redo' ).removeClass( 'disabled' );
      } else {
        $( '.tm-pb-layout-buttons-redo' ).addClass( 'disabled' );
      }

      if ( this.hasUndo() || this.hasRedo() ) {
        $( '.tm-pb-layout-buttons-history' ).removeClass( 'disabled' );
      } else {
        $( '.tm-pb-layout-buttons-history' ).addClass( 'disabled' );
      }
    },

    getUndoModel : function() {
      var model = this.options.history.at( this.getHistoriesIndex() - 1 );

      if ( _.isUndefined( model ) ) {
        return false;
      } else {
        return model;
      }
    },

    undo : function( event ) {
      event.preventDefault();

      var this_el = this,
        undo_model = this.getUndoModel(),
        undo_content,
        current_active_histories;

      // Bail if there's no undo histories to be used
      if ( ! this.hasUndo() ) {
        return;
      }

      // Bail if no undo model found
      if ( _.isUndefined( undo_model ) ) {
        return;
      }

      // Bail if there is overlay rendered (usually via hotkeys)
      if ( this.hasOverlayRendered() ) {
        return;
      }

      // Get undo content
      undo_content     = undo_model.get( 'shortcode' );

      // Turn off other current_active_history
      this.resetCurrentActiveHistoryMarker();

      // Update undo model's current_active_history
      undo_model.set( { current_active_history : true });

      // add loading state
      TM_PageBuilder_Events.trigger( 'tm-pb-loading:started' );

      // Set last history's content into main editor
      tm_pb_set_content( 'content', undo_content, 'saving_to_content' );

      // Rebuild the builder
      setTimeout( function(){
        var $builder_container = $( '#tm_pb_layout' ),
          builder_height     = $builder_container.innerHeight();

        $builder_container.css( { 'height' : builder_height } );

        TM_PageBuilder_App.removeAllSections();

        TM_PageBuilder_App.$el.find( '.tm_pb_section' ).remove();


        // Temporarily disable history until new layout has been generated
        this_el.enable_history = false;

        TM_PageBuilder_App.createLayoutFromContent( tm_prepare_template_content( undo_content ), '', '', { is_reinit : 'reinit' } );

        $builder_container.css( { 'height' : 'auto' } );

        // remove loading state
        TM_PageBuilder_Events.trigger( 'tm-pb-loading:ended' );

        // Update undo button state
        this_el.updateHistoriesButtonState();
      }, 600 );
    },

    getRedoModel : function() {
      var model = this.options.history.at( this.getHistoriesIndex() + 1 );

      if ( _.isUndefined( model ) ) {
        return false;
      } else {
        return model;
      }
    },

    toggleHistory : function( event ) {
      event.preventDefault();

      var $tm_pb_history_visualizer = $('#tm-pb-histories-visualizer');

      if ( $tm_pb_history_visualizer.hasClass( 'active' ) ) {
        $tm_pb_history_visualizer.addClass( 'fadeout' );

        // Remove class after being animated
        setTimeout( function() {
          $tm_pb_history_visualizer.removeClass( 'fadeout' );
        }, 500 );
      }

      $( '.tm-pb-layout-buttons-history, #tm-pb-histories-visualizer, #tm-pb-histories-visualizer-overlay' ).toggleClass( 'active' );
    },

    closeHistory : function( event ) {
      event.preventDefault();

      this.toggleHistory( event );
    },

    redo : function( event ) {
      event.preventDefault();

      var this_el = this,
        redo_model = this.getRedoModel(),
        redo_model_index,
        redo_content,
        current_active_histories;

      // Bail if there's no redo histories to be used
      if ( ! this.hasRedo() ) {
        return;
      }

      // Bail if no redo model found
      if ( _.isUndefined( redo_model ) || ! redo_model ) {
        return;
      }

      // Bail if there is overlay rendered (usually via hotkeys)
      if ( this.hasOverlayRendered() ) {
        return;
      }

      redo_model_index = this.options.history.indexOf( redo_model );
      redo_content     = redo_model.get( 'shortcode' );

      // Turn off other current_active_history
      this.resetCurrentActiveHistoryMarker();

      // Update redo model's current_active_history
      redo_model.set( { current_active_history : true });

      // add loading state
      TM_PageBuilder_Events.trigger( 'tm-pb-loading:started' );

      // Set last history's content into main editor
      tm_pb_set_content( 'content', redo_content, 'saving_to_content' );

      // Rebuild the builder
      setTimeout( function(){
        var $builder_container = $( '#tm_pb_layout' ),
          builder_height     = $builder_container.innerHeight();

        $builder_container.css( { 'height' : builder_height } );

        TM_PageBuilder_App.removeAllSections();

        TM_PageBuilder_App.$el.find( '.tm_pb_section' ).remove();

        // Temporarily disable history until new layout has been generated
        this_el.enable_history = false;

        TM_PageBuilder_App.createLayoutFromContent( tm_prepare_template_content( redo_content ), '', '', { is_reinit : 'reinit' } );

        $builder_container.css( { 'height' : 'auto' } );

        // remove loading state
        TM_PageBuilder_Events.trigger( 'tm-pb-loading:ended' );

        // Update redo button state
        this_el.updateHistoriesButtonState();
      }, 600 );
    },

    addHistory : function( content ) {
      if ( this.enableHistory() ) {
        var date = new Date(),
          hour = date.getHours() > 12 ? date.getHours() - 12 : date.getHours(),
          minute = date.getMinutes(),
          datetime_suffix = date.getHours() > 12 ? "PM" : "AM";

        // If there's a redo, remove models after active model
        if ( this.hasRedo() ) {
          this.reviseHistories();
        }

        this.resetCurrentActiveHistoryMarker();

        // Save content to builder history for undo/redo
        this.options.history.add({
          timestamp : _.now(),
          datetime : ( "0" + hour).slice(-2) + ":" + ( "0" + minute ).slice(-2) + " " + datetime_suffix,
          shortcode : content,
          current_active_history : true,
          verb : TM_PageBuilder_Visualize_Histories.verb,
          noun : TM_PageBuilder_Visualize_Histories.noun
        }, { validate : true });

        // Return history meta to default. Prevent confusion and for debugging
        TM_PageBuilder_Visualize_Histories.setHistoryMeta( 'did', 'something' );
      }

      // Update undo button state
      this.updateHistoriesButtonState();
    },

    addVisualizeHistoryItem : function( model ) {
      TM_PageBuilder_Visualize_Histories.addItem( model );
    },

    changeVisualizeHistoryItem : function( model ) {
      TM_PageBuilder_Visualize_Histories.changeItem( model );
    },

    removeVisualizeHistoryItem : function( model ) {
      TM_PageBuilder_Visualize_Histories.removeItem( model );
    },

    maybeGenerateInitialLayout : function() {
      var module_id = TM_PageBuilder_Layout.generateNewId(),
        this_el = this,
        fix_shortcodes = true,
        content = '';

      TM_PageBuilder_Events.trigger( 'tm-pb-loading:started' );

        /*
         * Visual editor adds paragraph tags around shortcodes,
         * it causes &nbsp; to be inserted into a module content area
         */
        content = tm_pb_get_content( 'content', fix_shortcodes );

      setTimeout( function() {

        // Enable history saving and set meta for history
        if ( content !== '' ) {
          this_el.allowHistorySaving( 'loaded', 'page' );
        }

        // Save page loaded
        this_el.addHistory( content );

        if  ( this_el.pageBuilderIsActive() ) {
          if ( -1 === content.indexOf( '[tm_pb_') ) {
            TM_PageBuilder_App.reInitialize();
          } else if ( -1 !== content.indexOf( 'specialty_placeholder') ) {
            this_el.createLayoutFromContent( tm_prepare_template_content( content ) );
            $( '.tm_pb_section_specialty' ).append( this_el.template_button() );
          } else {
            this_el.createLayoutFromContent( tm_prepare_template_content( content ) );
          }
        } else {
          this_el.createLayoutFromContent( content );
        }

        TM_PageBuilder_Events.trigger( 'tm-pb-content-updated' );

        TM_PageBuilder_Events.trigger( 'tm-pb-loading:ended' );

        $( '#tm_pb_main_container' ).addClass( 'tm_pb_loading_animation' );

        setTimeout( function() {
          $( '#tm_pb_main_container' ).removeClass( 'tm_pb_loading_animation' );
        }, 500 );

        // start listening to any collection events after all modules have been generated
        this_el.listenTo( this_el.collection, 'change reset add', _.debounce( this_el.saveAsShortcode, 128 ) );
      }, 1000 );
    },

    wp_regexp_not_global : _.memoize( function( tag ) {
      return new RegExp( '\\[(\\[?)(' + tag + ')(?![\\w-])([^\\]\\/]*(?:\\/(?!\\])[^\\]\\/]*)*?)(?:(\\/)\\]|\\](?:([^\\[]*(?:\\[(?!\\/\\2\\])[^\\[]*)*)(\\[\\/\\2\\]))?)(\\]?)' );
    }),

    getShortCodeParentTags : function () {
      var shortcodes = 'tm_pb_section|tm_pb_row|tm_pb_column|tm_pb_column_inner|tm_pb_row_inner'.split('|');

      shortcodes = shortcodes.concat( tm_pb_options.tm_builder_module_parent_shortcodes.split('|') );
      shortcodes = shortcodes.join('|');
      return shortcodes;
    },

    getShortCodeChildTags : function () {
      return tm_pb_options.tm_builder_module_child_shortcodes;
    },

    getShortCodeRawContentTags : function () {
      var raw_content_shortcodes = tm_pb_options.tm_builder_module_raw_content_shortcodes,
        raw_content_shortcodes_array;

      raw_content_shortcodes_array = raw_content_shortcodes.split( '|' )

      return raw_content_shortcodes_array;
    },
    //ignore_template_tag, current_row_cid, global_id, is_reinit, after_section, global_parent
    createLayoutFromContent : function( content, parent_cid, inner_shortcodes, additional_options ) {
      var this_el = this,
        tm_pb_shortcodes_tags = typeof inner_shortcodes === 'undefined' || '' === inner_shortcodes ? this.getShortCodeParentTags() : this.getShortCodeChildTags(),
        reg_exp = window.wp.shortcode.regexp( tm_pb_shortcodes_tags ),
        inner_reg_exp = this.wp_regexp_not_global( tm_pb_shortcodes_tags ),
        matches = content.match( reg_exp ),
        tm_pb_raw_shortcodes = this.getShortCodeRawContentTags(),
        additional_options_received = typeof additional_options === 'undefined' ? {} : additional_options;

      _.each( matches, function ( shortcode ) {
        var shortcode_element = shortcode.match( inner_reg_exp ),
          shortcode_name = shortcode_element[2],
          shortcode_attributes = shortcode_element[3] !== ''
            ? window.wp.shortcode.attrs( shortcode_element[3] )
            : '',
          shortcode_content = shortcode_element[5],
          module_cid = TM_PageBuilder_Layout.generateNewId(),
          module_settings,
          prefixed_attributes = {},
          found_inner_shortcodes = typeof shortcode_content !== 'undefined' && shortcode_content !== '' && shortcode_content.match( reg_exp ),
          global_module_id = '';

        if ( shortcode_name === 'tm_pb_section' || shortcode_name === 'tm_pb_row' || shortcode_name === 'tm_pb_column' || shortcode_name === 'tm_pb_row_inner' || shortcode_name === 'tm_pb_column_inner' )
          shortcode_name = shortcode_name.replace( 'tm_pb_', '' );

        module_settings = {
          type : shortcode_name,
          cid : module_cid,
          created : 'manually',
          module_type : shortcode_name
        }

        if ( typeof additional_options_received.current_row_cid !== 'undefined' && '' !== additional_options_received.current_row_cid ) {
          module_settings['current_row'] = additional_options_received.current_row_cid;
        }

        if ( typeof additional_options_received.global_id !== 'undefined' && '' !== additional_options_received.global_id ) {
          module_settings['tm_pb_global_module'] = additional_options_received.global_id;
        }

        if ( typeof additional_options_received.global_parent !== 'undefined' && '' !== additional_options_received.global_parent ) {
          module_settings['tm_pb_global_parent'] = additional_options_received.global_parent;
          module_settings['global_parent_cid'] = additional_options_received.global_parent_cid;
        }

        if ( shortcode_name === 'section' && ( typeof additional_options_received.after_section !== 'undefined' && '' !== additional_options_received.after_section ) ) {
          module_settings['after_section'] = additional_options_received.after_section;
        }

        if ( shortcode_name !== 'section' ) {
          module_settings['parent'] = parent_cid;
        }

        if ( shortcode_name.indexOf( 'tm_pb_' ) !== -1 ) {
          module_settings['type'] = 'module';

          module_settings['admin_label'] = TM_PageBuilder_Layout.getTitleByShortcodeTag( shortcode_name );
        } else {
          module_settings['admin_label'] = shortcode_name;
        }

        if ( _.isObject( shortcode_attributes['named'] ) ) {
          global_module_id = typeof shortcode_attributes['named']['global_module'] !== 'undefined' && '' === global_module_id ? shortcode_attributes['named']['global_module'] : global_module_id;

          for ( var key in shortcode_attributes['named'] ) {
            if ( typeof additional_options_received.ignore_template_tag === 'undefined' || '' === additional_options_received.ignore_template_tag || ( 'ignore_template' === additional_options_received.ignore_template_tag && 'template_type' !== key ) ) {
              var prefixed_key = key !== 'admin_label' && key !== 'specialty_columns' ? 'tm_pb_' + key : key;

              if ( ( shortcode_name === 'column' || shortcode_name === 'column_inner' ) && prefixed_key === 'tm_pb_type' )
                prefixed_key = 'layout';

              prefixed_attributes[prefixed_key] = shortcode_attributes['named'][key];
            }
          }

          module_settings = _.extend( module_settings, prefixed_attributes );

        }

        if ( typeof module_settings['specialty_columns'] !== 'undefined' ) {
          module_settings['layout_specialty'] = '1';
          module_settings['specialty_columns'] = parseInt( module_settings['specialty_columns'] );
        }

        if ( ! found_inner_shortcodes ) {
          if ( $.inArray( shortcode_name, tm_pb_raw_shortcodes ) > -1 ) {
            module_settings['tm_pb_raw_content'] = _.unescape( shortcode_content );
          } else {
            module_settings['tm_pb_content_new'] = shortcode_content;
          }
        }

        if ( ! module_settings['tm_pb_disabled'] !== 'undefined' && module_settings['tm_pb_disabled'] === 'on' ) {
          module_settings.className = ' tm_pb_disabled';
        }

        if ( ! module_settings['tm_pb_locked'] !== 'undefined' && module_settings['tm_pb_locked'] === 'on' ) {
          module_settings.className = ' tm_pb_locked';
        }

        this_el.collection.add( [ module_settings ] );

        if ( 'reinit' === additional_options_received.is_reinit || ( global_module_id === '' || ( global_module_id !== '' && 'row' !== shortcode_name && 'row_inner' !== shortcode_name && 'section' !== shortcode_name ) ) ) {
          if ( found_inner_shortcodes ) {
            var global_parent_id = typeof additional_options_received.global_parent === 'undefined' || '' === additional_options_received.global_parent ? global_module_id : additional_options_received.global_parent,
              global_parent_cid_new = typeof additional_options_received.global_parent_cid === 'undefined' || '' === additional_options_received.global_parent_cid
                ? typeof global_module_id !== 'undefined' && '' !== global_module_id ? module_cid : ''
                : additional_options_received.global_parent_cid;

            this_el.createLayoutFromContent( shortcode_content, module_cid, '', { is_reinit : additional_options_received.is_reinit, global_parent : global_parent_id, global_parent_cid : global_parent_cid_new } );
          }
        } else {
          //calculate how many global modules we requested on page
          window.tm_pb_globals_requested++;

          tm_pb_load_global_row( global_module_id, module_cid );
          this_el.createLayoutFromContent( shortcode_content, module_cid, '', { is_reinit : 'reinit' } );
        }
      } );
    },

    addModule : function( module ) {
      var view,
        modal_view,
        row_parent_view,
        row_layout,
        view_settings = {
          model : module,
          collection : TM_PageBuilder_Modules
        },
        cloned_cid = typeof module.get('cloned_cid') !== 'undefined' ? module.get('cloned_cid') : false;

      switch ( module.get( 'type' ) ) {
        case 'section' :
          view = new TM_PageBuilder.SectionView( view_settings );

          TM_PageBuilder_Layout.addView( module.get('cid'), view );

          if ( ! _.isUndefined( module.get( 'view' ) ) ){
            module.get( 'view' ).$el.after( view.render().el );
          } else if ( typeof module.get( 'after_section' ) !== 'undefined' && '' !== module.get( 'after_section' ) ) {
            TM_PageBuilder_Layout.getView( module.get( 'after_section' ) ).$el.after( view.render().el );
          } else if ( typeof module.get( 'current_row' ) !== 'undefined' ) {
            this.replaceElement( module.get( 'current_row' ), view );
          } else if ( cloned_cid ) {
            this.$el.find( 'div[data-cid="' + cloned_cid + '"]' ).closest('.tm_pb_section').after( view.render().el );
          } else {
            this.$el.append( view.render().el );
          }

          if ( 'on' === module.get( 'tm_pb_fullwidth' ) ) {
            $( view.render().el ).addClass( 'tm_pb_section_fullwidth' );

            var sub_view = new TM_PageBuilder.ColumnView( view_settings );

            view.addChildView( sub_view );

            $( view.render().el ).find( '.tm-pb-section-content' ).append( sub_view.render().el );
          }

          if ( 'on' === module.get( 'tm_pb_specialty' ) && 'auto' === module.get( 'created' ) ) {
            $( view.render().el ).addClass( 'tm_pb_section_specialty' );

            var tm_view;

            tm_view = new TM_PageBuilder.ModalView( {
              model : view_settings.model,
              collection : view_settings.collection,
              attributes : {
                'data-open_view' : 'column_specialty_settings'
              },
              tm_view : view,
              view : view
            } );

            $('body').append( tm_view.render().el );
          }

          // add Rows layout once the section has been created in "auto" mode

          if ( 'manually' !== module.get( 'created' ) && 'on' !== module.get( 'tm_pb_fullwidth' ) && 'on' !== module.get( 'tm_pb_specialty' ) ) {
            view.addRow();
          }

          break;
        case 'row' :
        case 'row_inner' :
          view = new TM_PageBuilder.RowView( view_settings );

          TM_PageBuilder_Layout.addView( module.get('cid'), view );

          /*this.$("[data-cid=" + module.get('parent') + "]").append( view.render().el );*/
          if ( ! _.isUndefined( module.get( 'current_row' ) ) ) {
            this.replaceElement( module.get( 'current_row' ), view );
          } else if ( ! _.isUndefined( module.get( 'appendAfter' ) ) ) {
            module.get( 'appendAfter' ).after( view.render().el );
          } else if ( cloned_cid ) {
            TM_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( 'div[data-cid="' + cloned_cid + '"]' ).parent().after( view.render().el );
          } else {
            if ( TM_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( '.tm-pb-section-content' ).length ) {
              TM_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( '.tm-pb-section-content' ).append( view.render().el );
            } else {
              TM_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( '> .tm-pb-insert-module' ).hide().end().append( view.render().el );
            }
          }

          // unset the columns_layout so it'll be calculated properly when columns added
          module.unset( 'columns_layout' );

          // add parent view to inner rows that have been converted from shortcodes
          if ( module.get('created') === 'manually' && module.get('module_type') === 'row_inner' ) {
            module.set( 'view', TM_PageBuilder_Layout.getView( module.get( 'parent' ) ), { silent : true } );
          }

          /*module.get( 'view' ).$el.find( '.tm-pb-section-content' ).append( view.render().el );*/

          break;
        case 'column' :
        case 'column_inner' :
          view_settings['className'] = 'tm-pb-column tm-pb-column-' + module.get( 'layout' );

          if ( ! _.isUndefined( module.get( 'layout_specialty' ) ) && '1' === module.get( 'layout_specialty' ) ) {
            view_settings['className'] += ' tm-pb-column-specialty';
          }

          view = new TM_PageBuilder.ColumnView( view_settings );

          TM_PageBuilder_Layout.addView( module.get('cid'), view );

          if ( _.isUndefined( module.get( 'layout_specialty' ) ) ) {
            /* Need to pass the columns layout into the parent row model to save the row template properly */
            row_parent_view = TM_PageBuilder_Layout.getView( module.get( 'parent' ) );
            row_layout = typeof row_parent_view.model.get( 'columns_layout' ) !== 'undefined' ? row_parent_view.model.get( 'columns_layout' ) + ',' + module.get( 'layout' ) : module.get( 'layout' );
            row_parent_view.model.set( 'columns_layout', row_layout );

            if ( TM_PageBuilder_Layout.getView( module.get( 'parent' ) ).model.get( 'tm_pb_specialty' ) !== 'on' ) {
              TM_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( '.tm-pb-row-container' ).append( view.render().el );

              TM_PageBuilder_Layout.getView( module.get( 'parent' ) ).toggleInsertColumnButton();
            } else {
              TM_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( '.tm-pb-section-content' ).append( view.render().el );
            }
          } else {
            TM_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( '.tm-pb-section-content' ).append( view.render().el );

            if ( '1' === module.get( 'layout_specialty' ) ) {
              if ( 'manually' !== module.get( 'created' ) ) {
                this.collection.add( [ {
                  type : 'row',
                  module_type : 'row',
                  cid : TM_PageBuilder_Layout.generateNewId(),
                  parent : module.get( 'cid' ),
                  view : view,
                  admin_label : tm_pb_options.noun['row']
                } ] );
              }

              TM_PageBuilder_Layout.getView( module.get( 'parent' ) ).model.set( 'specialty_columns', parseInt( module.get( 'specialty_columns' ) ) );
            }
          }

          /*module.get( 'view' ).$el.find( '.tm-pb-row-container' ).append( view.render().el );*/

          /*this.$("[data-cid=" + module.get('parent') + "] .tm-pb-row-container").append( view.render().el );*/

          break;
        case 'module' :
          view_settings['attributes'] = {
            'data-cid' : module.get( 'cid' )
          }

          if ( module.get( 'mode' ) !== 'advanced' && module.get( 'created' ) === 'manually' && TM_PageBuilder_Layout.getView( module.get( 'parent' ) ).model.get( 'module_type' ) === 'column_inner' ) {
            var inner_column_parent_row = TM_PageBuilder_Layout.getView( module.get( 'parent' ) ).model.get( 'parent' );

            TM_PageBuilder_Layout.getView( inner_column_parent_row ).$el.find( '.tm-pb-insert-column' ).hide();
          }

          if ( typeof module.get( 'mode' ) !== 'undefined' && module.get( 'mode' ) === 'advanced' ) {
            // create sortable tab

            view = new TM_PageBuilder.AdvancedModuleSettingView( view_settings );

            module.attributes.view.child_views.push( view );

            if ( typeof module.get( 'cloned_cid' ) !== 'undefined' && '' !== module.get( 'cloned_cid' ) ) {
              TM_PageBuilder_Layout.getView( module.get( 'cloned_cid' ) ).$el.after( view.render().el );
            } else {
              TM_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find('.tm-pb-sortable-options').append( view.render().el );
            }

            TM_PageBuilder_Layout.addView( module.get('cid'), view );


          } else {
            var template_type = '';

            TM_PageBuilder_Events.trigger( 'tm-new_module:show_settings' );

            view = new TM_PageBuilder.BlockModuleView( view_settings );

            if ( typeof module.attributes.view !== 'undefined' && module.attributes.view.model.get( 'tm_pb_fullwidth' ) === 'on' ) {
              TM_PageBuilder_Layout.getView( module.get( 'parent' ) ).addChildView( view );
              template_type = TM_PageBuilder_Layout.getView( module.get( 'parent' ) ).model.get( 'tm_pb_template_type' );
            } else if ( typeof module.attributes.view !== 'undefined' ) {
              template_type = TM_PageBuilder_Layout.getView( TM_PageBuilder_Layout.getView( module.get( 'parent' ) ).model.get( 'parent' ) ).model.get( 'tm_pb_template_type' );
            }

            // Append new module in proper position. Clone shouldn't be appended. It should be added after the cloned item
            if ( cloned_cid ) {
              TM_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( 'div[data-cid="' + cloned_cid + '"]' ).after( view.render().el );
            } else {
              // if .tm-pb-insert-module button exists, then add the module before that button. Otherwise append module to the parent
              if ( TM_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( '.tm-pb-insert-module' ).length ) {
                TM_PageBuilder_Layout.getView( module.get( 'parent' ) ).$el.find( '.tm-pb-insert-module' ).before( view.render().el );
              } else {
                var parent_view = TM_PageBuilder_Layout.getView( module.get( 'parent' ) );

                // append module to appropriate div if it's a fullwidth section
                if ( typeof parent_view.model.get( 'tm_pb_fullwidth' ) !== 'undefined' && 'on' === parent_view.model.get( 'tm_pb_fullwidth' ) ) {
                  parent_view.$el.find( '.tm_pb_fullwidth_sortable_area' ).append( view.render().el );
                } else {
                  parent_view.$el.append( view.render().el );
                }
              }
            }

            TM_PageBuilder_Layout.addView( module.get('cid'), view );

            if ( typeof template_type !== 'undefined' && 'module' === template_type ) {
              module.set( 'template_type', 'module', { silent : true } );
            }

            if ( 'manually' !== module.get( 'created' ) ) {
              view_settings['attributes'] = {
                'data-open_view' : 'module_settings'
              }
              this.openModuleSettings( view_settings );
            }
          }

          break;
      }

      // Always unset cloned_cid attribute after adding module.
      // It prevents module mishandling for module which is cloned multiple time
      module.unset('cloned_cid');
    },

    openModuleSettings : function( view_settings ) {
      var modal_view = new TM_PageBuilder.ModalView( view_settings ),
        that = this;

      if ( false === modal_view.render() ) {
        setTimeout( function() {
          that.openModuleSettings( view_settings );
        }, 500 );

        TM_PageBuilder_Events.trigger( 'tm-pb-loading:started' );

        return;
      }

      TM_PageBuilder_Events.trigger( 'tm-pb-loading:ended' );

      $('body').append( modal_view.render().el );
    },

    saveAsShortcode : function( tm_model, tm_collection, tm_options ) {
      var this_el = this,
        action_setting = arguments.length > 0 && typeof arguments[0] === 'object' && arguments[0]['tm_action'] || '';

      if ( tm_options && tm_options['update_shortcodes'] == 'false' )
        return;

      shortcode = this_el.generateCompleteShortcode();

      this.addHistory( shortcode );

      setTimeout( function(){
        // Save to content is performed each time, except when a layout is being loaded
        var action = action_setting || '';

        tm_pb_set_content( 'content', shortcode, action );

        TM_PageBuilder_Events.trigger( 'tm-pb-content-updated' );
      }, 500 );
    },

    generateCompleteShortcode : function( cid, layout_type, ignore_global_tag, ignore_global_tabs ) {
      var shortcode = '',
        this_el = this,
        all_sections = typeof cid === 'undefined' ? true : false,
        layout_type = typeof layout_type === 'undefined' ? '' : layout_type;

      this.$el.find( '.tm_pb_section' ).each( function() {
        var $this_section = $(this).find( '.tm-pb-section-content' ),
          include_whole_section = false,
          skip_section = typeof $this_section.data( 'skip' ) === 'undefined' ? false : $this_section.data( 'skip' );

        if ( ( ( false === all_sections && cid === $this_section.data( 'cid' ) ) || true === all_sections ) && true !== skip_section ) {
          shortcode += this_el.generateModuleShortcode( $(this), true, layout_type, ignore_global_tag );
          include_whole_section = true;
        }

        if ( $this_section.closest( '.tm_pb_section' ).hasClass( 'tm_pb_section_fullwidth' ) ) {
          $this_section.find( '.tm_pb_module_block' ).each( function() {
            var fullwidth_module_cid = $( this ).data( 'cid' );
            if ( ( false === all_sections && ( cid === fullwidth_module_cid || true === include_whole_section ) ) || true === all_sections ) {
              shortcode += this_el.generateModuleShortcode( $(this), false, layout_type, ignore_global_tag, '', ignore_global_tabs );
            }
          } );
        } else if ( $this_section.closest( '.tm_pb_section' ).hasClass( 'tm_pb_section_specialty' ) && ( ( true === include_whole_section || true === all_sections || 'module' === layout_type || 'row' === layout_type ) && true !== skip_section ) ) {
          $this_section.find( '> .tm-pb-column' ).each( function() {
            var $this_column = $(this),
              column_cid = $this_column.data( 'cid' ),
              module = TM_PageBuilder_Modules.findWhere( { cid : column_cid } ),
              specialty_columns = module.get( 'layout_specialty' ) === '1' ? ' specialty_columns="' + module.get( 'specialty_columns' ) + '"' : '',
              specialty_column_layout = module.get('layout');

            if ( true === include_whole_section || true === all_sections ) {
              shortcode += '[tm_pb_column type="' + specialty_column_layout + '"' + specialty_columns +']';
            }

            if ( $this_column.hasClass( 'tm-pb-column-specialty' ) ) {
              // choose each row
              $this_column.find( '.tm_pb_row' ).each( function() {
                var $this_row = $(this),
                  row_cid = $this_row.find( '.tm-pb-row-content' ).data( 'cid' ),
                  module = TM_PageBuilder_Modules.findWhere( { cid : row_cid } ),
                  include_whole_inner_row = false;

                if ( true === include_whole_section || true === all_sections || ( 'row' === layout_type && row_cid === cid ) ) {
                  include_whole_inner_row = true;
                  shortcode += this_el.generateModuleShortcode( $(this), true, layout_type, ignore_global_tag, 'row_inner' );
                }

                $this_row.find( '.tm-pb-column' ).each( function() {
                  var $this_column_inner = $(this),
                    column_cid = $this_column_inner.data( 'cid' ),
                    module = TM_PageBuilder_Modules.findWhere( { cid : column_cid } );

                  if ( true === include_whole_inner_row ) {
                    shortcode += '[tm_pb_column_inner type="' + module.get('layout') + '" saved_specialty_column_type="' + specialty_column_layout + '"]';
                  }

                  $this_column_inner.find( '.tm_pb_module_block' ).each( function() {
                    var inner_module_cid = $( this ).data( 'cid' );

                    if ( ( false === all_sections && ( cid === inner_module_cid || true === include_whole_section || true === include_whole_inner_row ) ) || true === all_sections ) {
                      shortcode += this_el.generateModuleShortcode( $(this), false, layout_type, ignore_global_tag, '', ignore_global_tabs );
                    }
                  } );

                  if ( true === include_whole_inner_row ) {
                    shortcode += '[/tm_pb_column_inner]';
                  }
                } );

                if ( true === include_whole_section || true === all_sections || ( 'row' === layout_type && row_cid === cid ) ) {
                  shortcode += '[/tm_pb_row_inner]';
                }
              } );
            } else {
              // choose each module
              $this_column.find( '.tm_pb_module_block' ).each( function() {
                var specialty_module_cid = $( this ).data( 'cid' );

                if ( ( false === all_sections && ( cid === specialty_module_cid || true === include_whole_section ) ) || true === all_sections ) {
                  shortcode += this_el.generateModuleShortcode( $(this), false, layout_type, ignore_global_tag, '', ignore_global_tabs );
                }
              } );
            }

            if ( true === include_whole_section || true === all_sections ) {
              shortcode += '[/tm_pb_column]';
            }
          } );
        } else {
          $this_section.find( '.tm_pb_row' ).each( function() {
            var $this_row = $(this),
              $this_row_content = $this_row.find( '.tm-pb-row-content' ),
              row_cid = $this_row_content.data( 'cid' ),
              include_whole_row = false,
              skip_row = typeof $this_row_content.data( 'skip' ) === 'undefined' ? false : $this_row_content.data( 'skip' );

            if ( ( ( false === all_sections && ( cid === row_cid || true === include_whole_section ) ) || true === all_sections ) && true !== skip_row ) {
              shortcode += this_el.generateModuleShortcode( $(this), true, layout_type, ignore_global_tag );
              include_whole_row = true;
            }

            $this_row.find( '.tm-pb-column' ).each( function() {
              var $this_column = $(this),
                column_cid = $this_column.data( 'cid' ),
                module = TM_PageBuilder_Modules.findWhere( { cid : column_cid } );

              if ( ( ( false === all_sections && ( true === include_whole_section || true === include_whole_row ) ) || true === all_sections ) && true !== skip_row ) {
                shortcode += '[tm_pb_column type="' + module.get('layout') + '"]';
              }

              $this_column.find( '.tm_pb_module_block' ).each( function() {
                var module_cid = $( this ).data( 'cid' );
                if ( ( false === all_sections && ( cid === module_cid || true === include_whole_section || true === include_whole_row ) ) || true === all_sections ) {
                  shortcode += this_el.generateModuleShortcode( $(this), false, layout_type, ignore_global_tag, '', ignore_global_tabs );
                }
              } );

              if ( ( ( false === all_sections && ( true === include_whole_section || true === include_whole_row ) ) || true === all_sections ) && true !== skip_row ) {
                shortcode += '[/tm_pb_column]';
              }

            } );

            if ( ( ( false === all_sections && ( cid === row_cid || true === include_whole_section ) ) || true === all_sections ) && true !== skip_row ) {
              shortcode += '[/tm_pb_row]';
            }

          } );
        }
        if ( ( ( false === all_sections && cid === $this_section.data( 'cid' ) ) || true === all_sections ) && true !== skip_section ) {
          shortcode += '[/tm_pb_section]';
        }

      } );

    return shortcode;
    },

    generateModuleShortcode : function( $module, open_tag_only, layout_type, ignore_global_tag, defined_module_type, ignore_global_tabs ) {
      var attributes = '',
        content = '',
        $this_module = $module,
        prefix = $this_module.is( '.tm_pb_section' ) || $this_module.is( '.tm_pb_row' ) || $this_module.is( '.tm_pb_row_inner' )
          ? 'tm_pb_'
          : '',
        module_cid = typeof $this_module.data( 'cid' ) === 'undefined'
          ? $this_module.find( '.tm-pb-data-cid' ).data( 'cid' )
          : $this_module.data( 'cid' ),
        module = TM_PageBuilder_Modules.find( function( model ) {
          return model.get('cid') == module_cid;
        } ),
        module_type = typeof module !== 'undefined' ? module.get( 'module_type' ) : 'undefined',
        module_settings,
        shortcode,
        template_module_type;

      if ( typeof defined_module_type !== 'undefined' && '' !== defined_module_type ) {
        module_type = defined_module_type;
      }

      module_settings = module.attributes;

      for ( var key in module_settings ) {
        if ( typeof ignore_global_tag === 'undefined' || 'ignore_global' !== ignore_global_tag || ( typeof ignore_global_tag !== 'undefined' && 'ignore_global' === ignore_global_tag && 'tm_pb_global_module' !== key && 'tm_pb_global_parent' !== key ) ) {
          if ( typeof ignore_global_tabs === 'undefined' || 'ignore_global_tabs' !== ignore_global_tabs || ( typeof ignore_global_tabs !== 'undefined' && 'ignore_global_tabs' === ignore_global_tabs && 'tm_pb_saved_tabs' !== key ) ) {
            var setting_name = key,
              setting_value;

            if ( setting_name.indexOf( 'tm_pb_' ) === -1 && setting_name !== 'admin_label' ) continue;

            setting_value = typeof( module.get( setting_name ) ) !== 'undefined' ? module.get( setting_name ) : '';

            if ( setting_name === 'tm_pb_content_new' || setting_name === 'tm_pb_raw_content' ) {
              content = setting_value;

              if ( setting_name === 'tm_pb_raw_content' ) {
                content = _.escape( content );
              }

              content = $.trim( content );

              if ( setting_name === 'tm_pb_content_new' ) {
                content = "\n\n" + content + "\n\n";
              }

            } else if ( setting_value !== '' ) {
              // check if there is a default value for a setting
              if ( typeof module_settings['module_defaults'] !== 'undefined' && typeof module_settings['module_defaults'][ setting_name ] !== 'undefined' ) {
                var module_setting_default = module_settings['module_defaults'][ setting_name ],
                  string_setting_value = setting_value + ''; // cast setting value to string to properly compare it with the module_setting_default

                // don't add an attribute to a shortcode, if default value is equal to the current value
                if ( module_setting_default === string_setting_value ) {
                  delete module.attributes[ setting_name ];
                  continue;
                }
              }

              setting_name = setting_name.replace( 'tm_pb_', '' );

              // Make sure double quotes are encoded, before adding values to shortcode
              if ( typeof setting_value === 'string' ) {
                setting_value = setting_value.replace( /\"/g, '%22' );
              }

              attributes += ' ' + setting_name + '="' + setting_value + '"';
            }
          }
        }
      }

      template_module_type = 'section' !== module_type && 'row' !== module_type ? 'module' : module_type;
      template_module_type = 'row_inner' === module_type ? 'row' : template_module_type;

      if ( typeof layout_type !== 'undefined' && ( layout_type === template_module_type ) ) {
        attributes += ' template_type="' + layout_type + '"';
      }

      if ( typeof module_settings['template_type'] !== 'undefined' ) {
        attributes += ' template_type="' + module_settings['template_type'] + '"';
      }

      shortcode = '[' + prefix + module_type + attributes;

      if ( content === '' && ( typeof module_settings['type'] !== 'undefined' && module_settings['type'] === 'module' ) ) {
        open_tag_only = true;
        shortcode += ' /]';
      } else {
        shortcode += ']';
      }

      if ( ! open_tag_only )
        shortcode += content + '[/' + prefix + module_type + ']';

      return shortcode;
    },

    makeSectionsSortable : function() {
      var this_el = this;

      this.$el.sortable( {
        items  : '> *:not(#tm_pb_layout_controls, #tm_pb_main_container_right_click_overlay, #tm-pb-histories-visualizer, #tm-pb-histories-visualizer-overlay)',
        cancel : '.tm-pb-settings, .tm-pb-clone, .tm-pb-remove, .tm-pb-section-add, .tm-pb-row-add, .tm-pb-insert-module, .tm-pb-insert-column, .tm_pb_locked, .tm-pb-disable-sort',
        delay: 100,
        update : function( event, ui ) {
          // Enable history saving and set meta for history
          this_el.allowHistorySaving( 'moved', 'section' );

          TM_PageBuilder_Events.trigger( 'tm-sortable:update' );
        },
        start : function( event, ui ) {
          tm_pb_close_all_right_click_options();
        }
      } );
    },

    reInitialize : function() {
      var content = tm_pb_get_content( 'content' ),
        contentIsEmpty = content == '',
        default_initial_column_type = tm_pb_options.default_initial_column_type,
        default_initial_text_module = tm_pb_options.default_initial_text_module;

      TM_PageBuilder_Events.trigger( 'tm-pb-loading:started' );

      this.removeAllSections();

      if ( content.indexOf( '[tm_pb_section' ) === -1 ) {
        if ( ! contentIsEmpty ) {
          content = '[tm_pb_column type="' + default_initial_column_type + '"][' + default_initial_text_module + ']' + content + '[/' + default_initial_text_module + '][/tm_pb_column]';
        }

        content = '[tm_pb_section][tm_pb_row]' + content + '[/tm_pb_row][/tm_pb_section]';
      }

      this.createNewLayout( content );

      TM_PageBuilder_Events.trigger( 'tm-pb-loading:ended' );
    },

    removeAllSections : function( create_initial_layout ) {
      var content;

      // force removal of all the sections and rows
      TM_PageBuilder_Layout.set( 'forceRemove', true );

      this.$el.find( '.tm-pb-section-content' ).each( function() {
        var $this_el = $(this),
          this_view = TM_PageBuilder_Layout.getView( $this_el.data( 'cid' ) );

        // don't remove cloned sections
        if ( typeof this_view !== 'undefined' ) {
          // Remove sections. Use remove_all flag so it can differ "remove section" and "clear layout"
          this_view.removeSection( false, true );
        }
      } );

      TM_PageBuilder_Layout.set( 'forceRemove', false );

      if ( create_initial_layout ) {
        content = '[tm_pb_section][tm_pb_row][/tm_pb_row][/tm_pb_section]';
        this.createNewLayout( content );
      }
    },

    // creates new layout from any content and saves new shortcodes once
    createNewLayout : function( content, action ) {
      var action = action || '';

      this.stopListening( this.collection, 'change reset add', this.saveAsShortcode );

      if ( action === 'load_layout' && typeof window.switchEditors !== 'undefined' ) {
        content = window.switchEditors.wpautop( content );

        content = content.replace( /<p>\[/g, '[' );
        content = content.replace( /\]<\/p>/g, ']' );
        content = content.replace( /\]<br \/>/g, ']' );
        content = content.replace( /<br \/>\n\[/g, '[' );
      }

      this.createLayoutFromContent( content );

      this.saveAsShortcode( { tm_action : action } );

      this.listenTo( this.collection, 'change reset add', _.debounce( this.saveAsShortcode, 128 ) );
    },

    //replaces the Original element with Replacement element in builder
    replaceElement : function ( original_cid, replacement_view ) {
      var original_view = TM_PageBuilder_Layout.getView( original_cid );

      original_view.$el.after( replacement_view.render().el );

      original_view.model.destroy();

      TM_PageBuilder_Layout.removeView( original_cid );

      original_view.remove();
    },

    showRightClickOptions : function( event ) {
      event.preventDefault();

      var tm_right_click_options_view,
        view_settings = {
          model      : {
            attributes : {
              type : 'app',
              module_type : 'app'
            }
          },
          view       : this.$el,
          view_event : event
        };

      tm_right_click_options_view = new TM_PageBuilder.RightClickOptionsView( view_settings );
    },

    hideRightClickOptions : function( event ) {
      event.preventDefault();

      tm_pb_close_all_right_click_options();
    },

    // calculates the order for each module in the builder
    recalculateModulesOrder : function() {
      var all_modules = this.collection;

      this.order_modules_array = [];
      this.order_modules_array['children_count'] = [];

      // go through all the modules in the builder content and set the module_order attribute for each.
      this.$el.find( '.tm_pb_section' ).each( function() {
        var $this_section = $(this).find( '.tm-pb-section-content' ),
          section_cid = $this_section.data( 'cid' );

        TM_PageBuilder_App.setModuleOrder( section_cid );

        if ( $this_section.closest( '.tm_pb_section' ).hasClass( 'tm_pb_section_fullwidth' ) ) {
          $this_section.find( '.tm_pb_module_block' ).each( function() {
            var fullwidth_module_cid = $( this ).data( 'cid' );

            TM_PageBuilder_App.setModuleOrder( fullwidth_module_cid );
          } );
        } else if ( $this_section.closest( '.tm_pb_section' ).hasClass( 'tm_pb_section_specialty' ) ) {
          $this_section.find( '> .tm-pb-column' ).each( function() {
            var $this_column = $(this),
              column_cid = $this_column.data( 'cid' );

            TM_PageBuilder_App.setModuleOrder( column_cid );

            if ( $this_column.hasClass( 'tm-pb-column-specialty' ) ) {
              // choose each row
              $this_column.find( '.tm_pb_row' ).each( function() {
                var $this_row = $(this),
                  row_cid = $this_row.find( '.tm-pb-row-content' ).data( 'cid' );

                TM_PageBuilder_App.setModuleOrder( row_cid );

                $this_row.find( '.tm-pb-column' ).each( function() {
                  var $this_column_inner = $(this),
                    column_cid = $this_column_inner.data( 'cid' );

                  TM_PageBuilder_App.setModuleOrder( column_cid );

                  $this_column_inner.find( '.tm_pb_module_block' ).each( function() {
                    var inner_module_cid = $( this ).data( 'cid' );

                    TM_PageBuilder_App.setModuleOrder( inner_module_cid );
                  });
                });
              });
            } else {
              // choose each module
              $this_column.find( '.tm_pb_module_block' ).each( function() {
                var specialty_module_cid = $( this ).data( 'cid' );

                TM_PageBuilder_App.setModuleOrder( specialty_module_cid, 'specialty' );
              });
            }
          });
        } else {
          $this_section.find( '.tm_pb_row' ).each( function() {
            var $this_row = $(this),
              $this_row_content = $this_row.find( '.tm-pb-row-content' ),
              row_cid = $this_row_content.data( 'cid' );

            TM_PageBuilder_App.setModuleOrder( row_cid );

            $this_row.find( '.tm-pb-column' ).each( function() {
              var $this_column = $(this),
                column_cid = $this_column.data( 'cid' );

              TM_PageBuilder_App.setModuleOrder( column_cid );

              $this_column.find( '.tm_pb_module_block' ).each( function() {
                var module_cid = $( this ).data( 'cid' );

                TM_PageBuilder_App.setModuleOrder( module_cid );
              });
            });
          });
        }
      });
    },

    // calculate and add the module_order attribute for the module.
    setModuleOrder: function( cid, is_specialty ) {
      var modules_with_child = $.parseJSON( tm_pb_options.tm_builder_modules_with_children ),
        current_model,
        module_order,
        parent_row,
        module_type,
        start_from,
        child_slug;

      current_model = TM_PageBuilder_Modules.findWhere( { cid : cid } );

      module_type = typeof current_model.attributes.module_type !== 'undefined' ? current_model.attributes.module_type : current_model.attributes.type;

      // determine the column type. Check the parent, if parent == row_inner, then column type = column_inner
      if ( 'column' === module_type || 'column_inner' === module_type || 'specialty' === is_specialty ) {
        parent_row = TM_PageBuilder_Modules.findWhere( { cid : current_model.attributes.parent } );

        // inner columns may have column module_type, so check the parent row to determine the column_inner type
        if ( 'column' === module_type && 'row_inner' === parent_row.attributes.module_type ) {
          module_type = 'column_inner';
        }
      }

      // check whether the module order exist for current module_type otherwise set to 0
      module_order = typeof this.order_modules_array[ module_type ] !== 'undefined' ? this.order_modules_array[ module_type ] : 0;

      current_model.attributes.module_order = module_order;

      // reset columns_order attribute to recalculate it properly
      if ( ( 'row' === module_type || 'row_inner' === module_type || 'section' === module_type ) && typeof current_model.attributes.columns_order !== 'undefined' ) {
        current_model.attributes.columns_order = [];
      }

      // columns order should be stored in the Row/Specialty section as well
      if ( 'column' === module_type || 'column_inner' === module_type || 'specialty' === is_specialty ) {
        if ( typeof parent_row.attributes.columns_order !== 'undefined' ) {
          parent_row.attributes.columns_order.push( module_order );
        } else {
          parent_row.attributes.columns_order = [ module_order ];
        }
      }

      // calculate child items for modules which support them and update count in module attributes
      if ( typeof modules_with_child[ module_type ] !== 'undefined' ) {
        child_slug = modules_with_child[ module_type ];
        start_from = typeof this.order_modules_array['children_count'][ child_slug ] !== 'undefined' ? this.order_modules_array['children_count'][ child_slug ] : 0;
        current_model.attributes.child_start_from = start_from; // this attributed used as a start point for calculation of child modules order

        if ( typeof current_model.attributes.tm_pb_content_new !== 'undefined' && '' !== current_model.attributes.tm_pb_content_new ) {
          tm_pb_shortcodes_tags = TM_PageBuilder_App.getShortCodeChildTags(),
          reg_exp = window.wp.shortcode.regexp( tm_pb_shortcodes_tags ),
          matches = current_model.attributes.tm_pb_content_new.match( reg_exp );
          start_from += null !== matches ? matches.length : 0;
        }

        this.order_modules_array['children_count'][ child_slug ] = start_from;
      }

      // increment the module order for current module_type
      this.order_modules_array[ module_type ] = module_order + 1;
    },

    updateAdvancedModulesOrder: function( $this_el ) {
      var $modules_container = typeof $this_el !== 'undefined' ? $this_el.find( '.tm-pb-option-advanced-module-settings' ) : $( '.tm-pb-option-advanced-module-settings' ),
        modules_count = 0,

        $modules_list;

      if ( $modules_container.length ) {
        $modules_list = $modules_container.find( '.tm-pb-sortable-options > li' );

        if ( $modules_list.length ) {
          $modules_list.each( function() {
            var $this_item = $( this ),
              this_cid = $this_item.data( 'cid' ),
              current_model,
              current_parent,
              start_from;

            current_model = TM_PageBuilder_Modules.findWhere( { cid : this_cid } );
            current_parent = TM_PageBuilder_Modules.findWhere( { cid : current_model.attributes.parent_cid } );

            start_from = typeof current_parent.attributes.child_start_from !== 'undefined' ? current_parent.attributes.child_start_from : 0;

            current_model.attributes.module_order = modules_count + start_from;

            modules_count++;
          });
        }
      }
    }
  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, window.wp.Backbone ) );
