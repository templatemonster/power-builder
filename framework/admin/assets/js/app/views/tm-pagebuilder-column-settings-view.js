( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.ColumnSettingsView = Backbone.View.extend( {

    className : 'tm_pb_modal_settings',

    template : _.template( $('#tm-builder-column-settings-template').html() ),

    events : {
      'click .tm-pb-column-layouts li' : 'addColumns',
      'click .tm-pb-options-tabs-links li a' : 'switchTab'
    },

    initialize : function( attributes ) {
      this.listenTo( TM_PageBuilder_Events, 'tm-add:columns', this.removeView );
      this.listenTo( TM_PageBuilder_Events, 'tm-modal-view-removed', this.removeViewAndEmptySection );

      this.options = attributes;
    },

    render : function() {
      this.$el.html( this.template( this.model.toJSON() ) );

      if ( TM_PageBuilder_Layout.getView( this.model.get('cid') ).$el.closest( '.tm_pb_global' ).length ) {
        this.$el.addClass( 'tm_pb_no_global' );
      }

      if ( typeof this.model.get( 'tm_pb_specialty' ) !== 'undefined' && 'on' === this.model.get( 'tm_pb_specialty' ) || typeof this.model.get( 'change_structure' ) !== 'undefined' && 'true' === this.model.get( 'change_structure' ) ) {
        this.$el.addClass( 'tm_pb_modal_no_tabs' );
      }

      return this;
    },

    addColumns : function( event ) {
      event.preventDefault();

      var that = this,
        $layout_el = $(event.target).is( 'li' ) ? $(event.target) : $(event.target).closest( 'li' ),
        layout = $layout_el.data('layout').split(','),
        layout_specialty = 'section' === that.model.get( 'type' ) && 'on' === that.model.get( 'tm_pb_specialty' )
          ? $layout_el.data('specialty').split(',')
          : '',
        layout_elements_num = _.size( layout ),
        this_view = this.options.view;

      if ( typeof that.model.get( 'change_structure' ) !== 'undefined' && 'true' === that.model.get( 'change_structure' ) ) {
        var row_columns = TM_PageBuilder_Layout.getChildViews( that.model.get( 'cid' ) ),
          columns_structure_old = [],
          index_count = 0,
          global_module_cid = typeof that.model.get( 'global_parent_cid' ) !== 'undefined' ? that.model.get( 'global_parent_cid' ) : '';

        _.each( row_columns, function( row_column ) {
          columns_structure_old[index_count] = row_column.model.get( 'cid' );
          index_count = index_count + 1;
        } );
      }

      _.each( layout, function( element, index ) {
        var update_content = layout_elements_num == ( index + 1 )
          ? 'true'
          : 'false',
          column_attributes = {
            type : 'column',
            cid : TM_PageBuilder_Layout.generateNewId(),
            parent : that.model.get( 'cid' ),
            layout : element,
            view : this_view
          }

        if ( typeof that.model.get( 'tm_pb_global_parent' ) !== 'undefined' && '' !== that.model.get( 'tm_pb_global_parent' ) ) {
          column_attributes.tm_pb_global_parent = that.model.get( 'tm_pb_global_parent' );
          column_attributes.global_parent_cid = that.model.get( 'global_parent_cid' );
        }

        if ( '' !== layout_specialty ) {
          column_attributes.layout_specialty = layout_specialty[index];
          column_attributes.specialty_columns = parseInt( $layout_el.data('specialty_columns') );
        }

        if ( typeof that.model.get( 'specialty_row' ) !== 'undefined' ) {
          that.model.set( 'module_type', 'row_inner', { silent : true } );
          that.model.set( 'type', 'row_inner', { silent : true } );
        }

        that.collection.add( [ column_attributes ], { update_shortcodes : update_content } );
      } );

      if ( typeof that.model.get( 'change_structure' ) !== 'undefined' && 'true' === that.model.get( 'change_structure' ) ) {
        var columns_structure_new = [];

        row_columns = TM_PageBuilder_Layout.getChildViews( that.model.get( 'cid' ) );
        index_count = 0;

        _.each( row_columns, function( row_column ) {
          columns_structure_new[index_count] = row_column.model.get( 'cid' );
          index_count = index_count + 1;
        } );

        // delete old columns IDs
        columns_structure_new.splice( 0, columns_structure_old.length );

        for	( index = 0; index < columns_structure_old.length; index++ ) {
          var is_extra_column = ( columns_structure_old.length > columns_structure_new.length ) && ( index > ( columns_structure_new.length - 1 ) ) ? true : false,
            old_column_cid = columns_structure_old[index],
            new_column_cid = is_extra_column ? columns_structure_new[columns_structure_new.length-1] : columns_structure_new[index],
            column_html = TM_PageBuilder_Layout.getView( old_column_cid ).$el.html(),
            modules = TM_PageBuilder_Layout.getChildViews( old_column_cid ),
            $updated_column,
            column_html_old = '';

          TM_PageBuilder_Layout.getView( old_column_cid ).model.destroy();

          TM_PageBuilder_Layout.getView( old_column_cid ).remove();

          TM_PageBuilder_Layout.removeView( old_column_cid );

          $updated_column = $('.tm-pb-column[data-cid="' + new_column_cid + '"]');

          if ( ! is_extra_column ) {
            $updated_column.html( column_html );
          } else {
            $updated_column.find( '.tm-pb-insert-module' ).remove();

            column_html_old = $updated_column.html();

            $updated_column.html( column_html_old + column_html );
          }

          _.each( modules, function( module ) {
            module.model.set( 'parent', new_column_cid, { silent : true } );
          } );
        }

        // Enable history saving and set meta for history
        TM_PageBuilder_App.allowHistorySaving( 'edited', 'column' );

        tm_reinitialize_builder_layout();
      }

      if ( typeof that.model.get( 'template_type' ) !== 'undefined' && 'section' === that.model.get( 'template_type' ) && 'on' === that.model.get( 'tm_pb_specialty' ) ) {
        tm_reinitialize_builder_layout();
      }

      if ( typeof that.model.get( 'tm_pb_template_type' ) !== 'undefined' && 'row' === that.model.get( 'tm_pb_template_type' ) ) {
        tm_add_template_meta( '_tm_pb_row_layout', $layout_el.data( 'layout' ) );
      }

      if ( typeof global_module_cid !== 'undefined' && '' !== global_module_cid ) {
        tm_pb_update_global_template( global_module_cid );
      }

      // Enable history saving and set meta for history
      TM_PageBuilder_App.allowHistorySaving( 'added', 'column' );

      TM_PageBuilder_Events.trigger( 'tm-add:columns' );
    },

    removeView : function() {
      var that = this;

      // remove it with some delay to make sure animation applied to modal before removal
      setTimeout( function() {
        that.remove();
      }, 300 );
    },

    switchTab : function( event ) {
      var $this_el = $( event.currentTarget ).parent();

      event.preventDefault();

      tm_handle_templates_switching( $this_el, 'row', '' );
    },

    /**
     * Remove modal view and empty specialty section, if the user hasn't selected a section layout
     * and closed a modal window
     */
    removeViewAndEmptySection : function() {
      if ( this.model.get( 'tm_pb_specialty' ) === 'on' ) {
        this.options.view.model.destroy();

        TM_PageBuilder_Layout.removeView( this.options.view.model.get('cid') );

        this.options.view.remove();
      }

      this.remove();
    }

  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, window.wp.Backbone ) );
