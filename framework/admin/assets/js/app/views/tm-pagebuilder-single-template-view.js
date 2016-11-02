( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.SingleTemplateView = Backbone.View.extend( {
    tagName : 'li',

    template: _.template( $( '#tm-builder-saved-entry' ).html() ),

    events: {
      'click' : 'insertSection',
    },

    initialize: function(){
      this.render();
    },

    render: function() {
      this.$el.html( this.template( this.model.toJSON() ) );

      if ( typeof this.model.get( 'module_type' ) !== 'undefined' && '' !== this.model.get( 'module_type' ) && 'module' === this.model.get( 'layout_type' ) ) {
        this.$el.addClass( this.model.get( 'module_type' ) ).attr( 'data-icon', this.model.get( 'module_icon' ) );
      }
    },

    insertSection : function( event ) {
      var clicked_button     = $( event.target ),
        parent_id          = typeof clicked_button.closest( '.tm_pb_modal_settings' ).data( 'parent_cid' ) !== 'undefined' ? clicked_button.closest( '.tm_pb_modal_settings' ).data( 'parent_cid' ) : '',
        current_row        = typeof $( '.tm-pb-settings-heading' ).data( 'current_row' ) !== 'undefined' ? $( '.tm-pb-settings-heading' ).data( 'current_row' ) : '',
        global_id          = 'global' === this.model.get( 'is_global' ) ? this.model.get( 'ID' ) : '',
        specialty_row      = typeof $( '.tm-pb-saved-modules-switcher' ).data( 'specialty_columns' ) !== 'undefined' ? 'on' : 'off',
        shortcode          = this.model.get( 'shortcode' ),
        update_global      = false,
        global_holder_id   = 'row' === this.model.get( 'layout_type' ) ? current_row : parent_id,
        global_holder_view = TM_PageBuilder_Layout.getView( global_holder_id ),
        history_noun       = this.options.model.get( 'layout_type' ) === 'row_inner' ? 'saved_row' : 'saved_' + this.options.model.get( 'layout_type' );

        if ( 'on' === specialty_row ) {
          global_holder_id = global_holder_view.model.get( 'parent' );
          global_holder_view = TM_PageBuilder_Layout.getView( global_holder_id );
        }

        if ( 'section' !== this.model.get( 'layout_type' ) && ( ( typeof global_holder_view.model.get( 'global_parent_cid' ) !== 'undefined' && '' !== global_holder_view.model.get( 'global_parent_cid' ) ) || ( typeof global_holder_view.model.get( 'tm_pb_global_module' ) !== 'undefined' && '' !== global_holder_view.model.get( 'tm_pb_global_module' ) ) ) ) {
          update_global = true;
        }

      // Enable history saving and set meta for history
      TM_PageBuilder_App.allowHistorySaving( 'added', history_noun );

      event.preventDefault();
      TM_PageBuilder_App.createLayoutFromContent( shortcode , parent_id, '', { ignore_template_tag : 'ignore_template', current_row_cid : current_row, global_id : global_id, after_section : parent_id, is_reinit : 'reinit' } );
      tm_reinitialize_builder_layout();

      if ( true === update_global ) {
          global_module_cid = typeof global_holder_view.model.get( 'global_parent_cid' ) !== 'undefined' ? global_holder_view.model.get( 'global_parent_cid' ) : global_holder_id;

        tm_pb_update_global_template( global_module_cid );
      }
    }
  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, window.wp.Backbone ) );
