( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.SaveLayoutSettingsView = Backbone.View.extend( {

    className : 'tm_pb_modal_settings',

    template : _.template( $('#tm-builder-load_layout-template').html() ),

    events : {
      'click .tm_pb_layout_button_load' : 'loadLayout',
      'click .tm_pb_layout_button_delete' : 'deleteLayout',
      'click .tm-pb-options-tabs-links li a' : 'switchTab'
    },

    initialize : function( attributes ) {
      this.options = attributes;

      this.layoutIsLoading = false;

      this.listenTo( TM_PageBuilder_Events, 'tm-modal-view-removed', this.remove );
    },

    render : function() {
      var $this_el = this.$el,
        post_type = $('#post_type').val();

      $this_el.html( this.template( { "display_switcher" : "on" } ) );

      tm_load_saved_layouts( 'predefined', 'tm-pb-all-modules-tab', $this_el, post_type );
      tm_load_saved_layouts( 'not_predefined', 'tm-pb-saved-modules-tab', $this_el, post_type );

      return this;
    },

    deleteLayout : function( event ) {
      event.preventDefault();

      var $layout = $( event.currentTarget ).closest( 'li' );

      if ( $layout.hasClass( 'tm_pb_deleting_layout' ) )
        return;
      else
        $layout.addClass( 'tm_pb_deleting_layout' );

      $.ajax( {
        type: "POST",
        url: tm_pb_options.ajaxurl,
        data:
        {
          action : 'tm_pb_delete_layout',
          tm_admin_load_nonce : tm_pb_options.tm_admin_load_nonce,
          tm_layout_id : $layout.data( 'layout_id' )
        },
        beforeSend : function() {
          TM_PageBuilder_Events.trigger( 'tm-pb-loading:started' );

          $layout.css( 'opacity', '0.5' );
        },
        complete : function() {
          TM_PageBuilder_Events.trigger( 'tm-pb-loading:ended' );
        },
        success: function( data ){
          if ( $layout.closest( 'ul' ).find( '> li' ).length == 1 )
            $layout.closest( 'ul' ).prev( 'h3' ).hide();

          $layout.remove();
        }
      } );
    },

    loadLayout : function( event ) {
      event.preventDefault();

      if ( this.layoutIsLoading ) {
        return;
      } else {
        this.layoutIsLoading = true;

        this.$el.find( '.tm-pb-main-settings' ).css( { 'opacity' : '0.5' } );
      }

      var $layout = $( event.currentTarget ).closest( 'li' ),
        replace_content = $layout.closest( '.tm-pb-main-settings' ).find( '#tm_pb_load_layout_replace' ).is( ':checked' ),
        content = tm_pb_get_content( 'content' ),
        this_el = this;

      $.ajax( {
        type: "POST",
        url: tm_pb_options.ajaxurl,
        data:
        {
          action : 'tm_pb_load_layout',
          tm_admin_load_nonce : tm_pb_options.tm_admin_load_nonce,
          tm_layout_id : $layout.data( 'layout_id' ),
          tm_replace_content : ( replace_content ? 'on' : 'off' )
        },
        beforeSend : function() {
          TM_PageBuilder_Events.trigger( 'tm-pb-loading:started' );
        },
        complete : function() {
          TM_PageBuilder_Events.trigger( 'tm-pb-loading:ended' );

          TM_PageBuilder_Events.trigger( 'tm-saved_layout:loaded' );
        },
        success: function( data ){
          content = replace_content ? data : data + content;

          TM_PageBuilder_App.removeAllSections();

          if ( content !== '' ) {
            TM_PageBuilder_App.allowHistorySaving( 'loaded', 'layout' );
          }

          TM_PageBuilder_App.createNewLayout( content, 'load_layout' );
        }
      } );
    },

    switchTab: function( event ) {
      var $this_el = $( event.currentTarget ).parent();
      event.preventDefault();

      tm_handle_templates_switching( $this_el, 'layout', '' );
    }

  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, window.wp.Backbone ) );
