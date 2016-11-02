( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.History = Backbone.Model.extend( {

    defaults : {
      timestamp : _.now(),
      shortcode : '',
      current_active_history : false,
      verb : 'did',
      noun : 'something'
    },

    max_history_limit : 100,

    validate : function( attributes, options ) {
      var histories_count = options.collection.length,
        active_history_model = options.collection.findWhere({ current_active_history : true }),
        shortcode            = attributes.shortcode,
        last_model           = _.isUndefined( active_history_model ) ? options.collection.at( ( options.collection.length - 1 ) ) : active_history_model,
        last_shortcode       = _.isUndefined( last_model ) ? false : last_model.get( 'shortcode' ),
        previous_active_histories;

      if ( shortcode === last_shortcode ) {
        return 'duplicate';
      }

      // Turn history tracking off
      TM_PageBuilder_App.enable_history = false;

      // Limit number of history limit
      var histories_count = options.collection.models.length,
        remove_limit = histories_count - ( this.max_history_limit - 1 ),
        ranges,
        deleted_model;

      // Some models are need to be removed
      if ( remove_limit > 0 ) {
        // Loop and shift (remove first model in collection) n-times
        for (var i = 1; i <= remove_limit; i++) {
          options.collection.shift();
        };
      }
    }

  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, Backbone ) );
