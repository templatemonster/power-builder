( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.visualizeHistoriesView = Backbone.View.extend( {

    el : '#tm-pb-histories-visualizer',

    template : _.template( $('#tm-builder-histories-visualizer-item-template').html() ),

    events : {
      'click li' : 'rollback'
    },

    verb : 'did',

    noun : 'module',

    noun_alias : undefined,

    addition : '',

    getItemID : function( model ) {
      return '#tm-pb-history-' + model.get( 'timestamp' );
    },

    getVerb : function() {
      var verb = this.verb;

      if ( ! _.isUndefined( tm_pb_options.verb[verb] ) ) {
        verb = tm_pb_options.verb[verb];
      }

      return verb;
    },

    getNoun : function() {
      var noun = this.noun;

      if ( ! _.isUndefined( this.noun_alias ) ) {
        noun = this.noun_alias;
      } else if ( ! _.isUndefined( tm_pb_options.noun[noun] ) ) {
        noun = tm_pb_options.noun[noun];
      }

      return noun;
    },

    getAddition : function() {
      var addition = this.addition;

      if ( ! _.isUndefined( tm_pb_options.addition[addition] ) ) {
        addition = tm_pb_options.addition[addition];
      }

      return addition;
    },

    addItem : function( model ) {
      // Setting the passed model as class' options so the template can be rendered correctly
      this.options = model;

      // Prepend history item to container
      this.$el.prepend( this.template() );

      // Fix max-height for history visualizer
      this.setHistoriesHeight();
    },

    changeItem : function( model ) {
      var item_id      = this.getItemID( model ),
        $item        = $( item_id ),
        active_model = model.collection.findWhere({ current_active_history : true }),
        active_index = model.collection.indexOf( active_model ),
        item_index   = model.collection.indexOf( model );

      // Setting the passed model as class' options so the template can be rendered correctly
      this.options = model;

      // Remove all class related to changed item
      this.$el.find('li').removeClass( 'undo redo active' );

      // Update currently item class, relative to current index
      // Use class change instead of redraw the whole index using template() because verb+noun changing is too tricky
      if ( active_index === item_index ) {
        $item.addClass( 'active' );

        this.$el.find('li:lt('+ $item.index() +')').addClass( 'redo' );

        this.$el.find('li:gt('+ $item.index() +')').addClass( 'undo' );
      } else {
        // Change upon history is tricky because there is no active model found. Assume that everything is undo action
        this.$el.find('li:not( .active, .redo )').addClass( 'undo' );
      }

      // Fix max-height for history visualizer
      this.setHistoriesHeight();
    },

    removeItem : function( model ) {
      var item_id = this.getItemID( model );

      // Remove model's item from UI
      this.$el.find( item_id ).remove();

      // Fix max-height for history visualizer
      this.setHistoriesHeight();
    },

    setHistoryMeta : function( verb, noun, noun_alias, addition ) {
      if ( ! _.isUndefined( verb ) ) {
        this.verb = verb;
      }

      if ( ! _.isUndefined( noun ) ) {
        this.noun = noun;
      }

      if ( ! _.isUndefined( noun_alias ) ) {
        this.noun_alias = noun_alias;
      } else {
        this.noun_alias = undefined;
      }

      if ( ! _.isUndefined( addition ) ) {
        this.addition = addition;
      }
    },

    setHistoriesHeight : function() {
      var this_el = this;

      // Wait for 200 ms before making change to ensure that $layout has been changed
      setTimeout( function(){
        var $layout                = $( '#tm_pb_layout' ),
          $layout_header         = $layout.find( '.hndle' ),
          $layout_controls       = $( '#tm_pb_layout_controls' ),
          visualizer_height      = $layout.outerHeight() - $layout_header.outerHeight() - $layout_controls.outerHeight();

        this_el.$el.css({ 'max-height' : visualizer_height });
      }, 200 );
    },

    rollback : function( event ) {
      event.preventDefault();

      var this_el     = this,
        $clicked_el = $( event.target ),
        $this_el    = $clicked_el.is( 'li' ) ? $clicked_el : $clicked_el.parent('li'),
        timestamp   = $this_el.data( 'timestamp' ),
        model       = this.options.collection.findWhere({ timestamp : timestamp }),
        shortcode   = model.get( 'shortcode' );

      // Turn off other current_active_history
      TM_PageBuilder_App.resetCurrentActiveHistoryMarker();

      // Update undo model's current_active_history
      model.set( { current_active_history : true });

      // add loading state
      TM_PageBuilder_Events.trigger( 'tm-pb-loading:started' );

      // Set shortcode to editor
      tm_pb_set_content( 'content', shortcode, 'saving_to_content' );

      // Rebuild the builder
      setTimeout( function(){
        var $builder_container = $( '#tm_pb_layout' ),
          builder_height     = $builder_container.innerHeight();

        $builder_container.css( { 'height' : builder_height } );

        TM_PageBuilder_App.removeAllSections();

        TM_PageBuilder_App.$el.find( '.tm_pb_section' ).remove();

        // Ensure that no history is added for rollback
        TM_PageBuilder_App.enable_history = false;

        TM_PageBuilder_App.createLayoutFromContent( tm_prepare_template_content( shortcode ), '', '', { is_reinit : 'reinit' } );

        $builder_container.css( { 'height' : 'auto' } );

        // remove loading state
        TM_PageBuilder_Events.trigger( 'tm-pb-loading:ended' );

        // Update undo button state
        TM_PageBuilder_App.updateHistoriesButtonState();
      }, 600 );
    }
  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, window.wp.Backbone ) );
