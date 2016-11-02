( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.Histories = Backbone.Collection.extend( {

    model : TM_PageBuilder.History

  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, Backbone ) );
