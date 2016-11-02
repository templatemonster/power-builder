( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.SavedTemplates = Backbone.Collection.extend( {

    model : TM_PageBuilder.SavedTemplate

  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, Backbone ) );
