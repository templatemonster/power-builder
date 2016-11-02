( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.Module = Backbone.Model.extend( {

    defaults: {
      type : 'element'
    }

  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, Backbone ) );
