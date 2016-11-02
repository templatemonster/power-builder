( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.AdvancedModuleSettingTitleView = Backbone.View.extend( {
    tagName : 'span',

    className : 'tm-sortable-title',

    initialize : function() {
      template_name = '#tm-builder-advanced-setting-' + this.model.get( 'module_type' ) + '-title';

      this.template = _.template( $( template_name ).html() );

      this.listenTo( TM_PageBuilder_Events, 'tm-advanced-module:updated', this.render );
    },

    render : function() {
      var view;

      // If admin label is empty, delete it so builder will use heading value instead
      if ( ! _.isUndefined( this.model.attributes.tm_pb_admin_title ) && this.model.attributes.tm_pb_admin_title === '' ) {
        delete this.model.attributes.tm_pb_admin_title;
      }

      this.$el.html( this.template( this.model.attributes ) );

      return this;
    }
  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, window.wp.Backbone ) );
