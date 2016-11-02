( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.AdvancedModuleSettingView = Backbone.View.extend( {
    tagName : 'li',

    initialize : function() {
      this.template = _.template( $( '#tm-builder-advanced-setting' ).html() );
    },

    events : {
      'click .tm-pb-advanced-setting-options' : 'showSettings',
      'click .tm-pb-advanced-setting-remove' : 'removeView'
    },

    render : function() {
      var view;

      this.$el.html( this.template( this.model.attributes ) );

      view = new TM_PageBuilder.AdvancedModuleSettingTitleView( {
        model : this.model,
        view : this
      } );

      this.$el.prepend( view.render().el );

      this.child_view = view;

      if ( typeof this.model.get( 'cloned_cid' ) === 'undefined' || '' === this.model.get( 'cloned_cid' ) ) {
        this.showSettings();
      }

      return this;
    },

    showSettings : function( event ) {
      var view;

      if ( event ) event.preventDefault();

      view = new TM_PageBuilder.AdvancedModuleSettingEditViewContainer( {
        view : this,
        attributes : {
          show_settings_clicked : ( event ? true : false )
        }
      } );

      $('.tm_pb_modal_settings_container').after( view.render().el );
    },

    removeView : function( event ) {
      if ( event ) event.preventDefault();

      if ( typeof this.child_view !== 'undefined' ) {
        this.child_view.remove();
      }

      this.remove();

      this.model.destroy();

      TM_PageBuilder_Events.trigger( 'tm-advanced-module:updated' );
      TM_PageBuilder_Events.trigger( 'tm-advanced-module:updated_order' );
    }
  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, window.wp.Backbone ) );
