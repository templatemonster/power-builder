( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.TemplatesView = Backbone.View.extend( {
    className : 'tm_pb_saved_layouts_list',

    tagName : 'ul',

    render: function() {
      var global_class = '',
        layout_category = typeof this.options.category === 'undefined' ? 'all' : this.options.category;

      this.collection.each( function( single_template ) {
        if ( 'all' === layout_category || ( -1 !== $.inArray( layout_category, single_template.get( 'categories' ) ) ) ) {
          var single_template_view = new TM_PageBuilder.SingleTemplateView( { model: single_template } );
          this.$el.append( single_template_view.el );
          global_class = typeof single_template_view.model.get( 'is_global' ) !== 'undefined' && 'global' === single_template_view.model.get( 'is_global' ) ? 'global' : '';
        }
      }, this );

      if ( 'global' === global_class ) {
        this.$el.addClass( 'tm_pb_global' );
      }

      return this;
    }

  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, window.wp.Backbone ) );
