( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.Modules = Backbone.Collection.extend( {

    model : TM_PageBuilder.Module

  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, Backbone ) );
