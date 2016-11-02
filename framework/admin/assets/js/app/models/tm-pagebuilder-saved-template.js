( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.SavedTemplate = Backbone.Model.extend( {

    defaults: {
      title : 'template',
      ID : 0,
      shortcode : '',
      is_global : 'false',
      layout_type : '',
      module_type : '',
      module_icon : '',
      categories : []
    }

  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, Backbone ) );
