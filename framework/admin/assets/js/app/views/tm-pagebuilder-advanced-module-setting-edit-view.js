( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.AdvancedModuleSettingEditView = Backbone.View.extend( {
    className : 'tm_pb_module_settings',

    initialize : function() {
      this.model = this.options.view.options.view.model;

      this.template = _.template( $( '#tm-builder-advanced-setting-' + this.model.get( 'module_type' ) ).html() );
    },

    events : {
    },

    render : function() {
      var $this_el = this.$el,
        $content_textarea,
        $content_textarea_container;

      this.$el.html( this.template( { data : this.model.toJSON() } ) );

      this.$el.find( '.tm-pb-main-settings' ).addClass( 'tm-pb-main-settings-advanced' );

      $content_textarea = this.$el.find( 'div#tm_pb_content_new' );

      if ( $content_textarea.length ) {
        $content_textarea_container = $content_textarea.closest( '.tm-pb-option-container' );

        content = $content_textarea.html();

        $content_textarea.remove();

        $content_textarea_container.prepend( tm_pb_content_html );

        setTimeout( function() {
          if ( typeof window.switchEditors !== 'undefined' )
            window.switchEditors.go( 'tm_pb_content_new', tm_get_editor_mode() );

          tm_pb_set_content( 'tm_pb_content_new', content );

          window.wpActiveEditor = 'tm_pb_content_new';
        }, 300 );
      }

      setTimeout( function() {
        $this_el.find('select, input, textarea, radio').filter(':eq(0)').focus();
      }, 1 );

      return this;
    }
  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, window.wp.Backbone ) );
