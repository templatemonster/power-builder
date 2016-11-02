( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.TemplatesModal = Backbone.View.extend( {
    className : 'tm_pb_modal_settings',

    template : _.template( $( '#tm-builder-load_layout-template' ).html() ),

    events : {
      'click .tm-pb-options-tabs-links li a' : 'switchTab'
    },

    render: function() {

      this.$el.html( this.template( { "display_switcher" : "off" } ) );

      this.$el.addClass( 'tm_pb_modal_no_tabs' );

      return this;
    },

    switchTab: function( event ) {
      var $this_el = $( event.currentTarget ).parent();
      event.preventDefault();

      tm_handle_templates_switching( $this_el, 'section', '' );
    }

  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, window.wp.Backbone ) );
