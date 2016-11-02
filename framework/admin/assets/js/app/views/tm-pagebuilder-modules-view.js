( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.ModulesView = Backbone.View.extend( {

    className : 'tm_pb_modal_settings',

    template : _.template( $('#tm-builder-modules-template').html() ),

    events : {
      'click .tm-pb-all-modules li' : 'addModule',
      'click .tm-pb-options-tabs-links li a' : 'switchTab'
    },

    initialize : function( attributes ) {
      this.options = attributes;

      this.listenTo( TM_PageBuilder_Events, 'tm-modal-view-removed', this.remove );
    },

    render : function() {
      var template_type_holder = typeof TM_PageBuilder_Layout.getView( this.model.get('parent') ) !== 'undefined' ? TM_PageBuilder_Layout.getView( this.model.get('parent') ) : this;
      this.$el.html( this.template( TM_PageBuilder_Layout.toJSON() ) );

      if ( TM_PageBuilder_Layout.getView( this.model.get('cid') ).$el.closest( '.tm_pb_global' ).length || typeof template_type_holder.model.get('tm_pb_template_type') !== 'undefined' && 'module' === template_type_holder.model.get('tm_pb_template_type') ) {
        this.$el.addClass( 'tm_pb_no_global' );
      }

      return this;
    },

    addModule : function( event ) {
      var $this_el             = $( event.currentTarget ),
        label                = $this_el.find( '.tm_module_title' ).text(),
        type                 = $this_el.attr( 'class' ).replace( ' tm_pb_fullwidth_only_module', '' ),
        icon                 = $this_el.data( 'icon' ),
        global_module_cid    = '',
        parent_view          = TM_PageBuilder_Layout.getView( this.model.get('parent') ),
        template_type_holder = typeof parent_view !== 'undefined' ? parent_view : this;

      event.preventDefault();

      if ( typeof this.model.get( 'tm_pb_global_parent' ) !== 'undefined' && typeof this.model.get( 'tm_pb_global_parent' ) !== '' ) {
        global_module_cid = this.model.get( 'global_parent_cid' );
      }

      // Enable history saving and set meta for history
      TM_PageBuilder_App.allowHistorySaving( 'added', 'module', label );

      this.collection.add( [ {
        type : 'module',
        cid : TM_PageBuilder_Layout.generateNewId(),
        module_type : type,
        module_icon : icon,
        admin_label : label,
        parent : this.attributes['data-parent_cid'],
        view : this.options.view,
        global_parent_cid : global_module_cid
      } ] );

      this.remove();

      if ( '' !== global_module_cid ) {
        tm_pb_update_global_template( global_module_cid );
      }

      if ( typeof template_type_holder.model.get( 'tm_pb_template_type' ) !== 'undefined' && 'module' === template_type_holder.model.get( 'tm_pb_template_type' ) ) {
        tm_add_template_meta( '_tm_pb_module_type', type );
      }

      tm_pb_open_current_tab();
    },

    switchTab : function( event ) {
      var $this_el = $( event.currentTarget ).parent(),
        module_width = typeof this.model.get( 'tm_pb_fullwidth' ) && 'on' === this.model.get( 'tm_pb_fullwidth' ) ? 'fullwidth' : 'regular';

      event.preventDefault();

      tm_handle_templates_switching( $this_el, 'module', module_width );
    }

  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, window.wp.Backbone ) );
