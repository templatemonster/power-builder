( function ( $, TM_PageBuilder, Backbone ) {

  TM_PageBuilder.AdvancedModuleSettingsView = Backbone.View.extend( {
    initialize : function() {
      this.listenTo( TM_PageBuilder_Events, 'tm-advanced-module:updated', this.generateContent );

      this.listenTo( TM_PageBuilder_Events, 'tm-modal-view-removed', this.removeModule );

      this.module_type = this.$el.data( 'module_type' );

      TM_PageBuilder.Events = TM_PageBuilder_Events;

      this.child_views = [];

      this.$el.attr( 'data-cid', this.attributes['cid'] );

      this.$sortable_options = this.$el.find('.tm-pb-sortable-options');

      this.$content_textarea = this.$el.siblings('.tm-pb-option-main-content').find('#tm_pb_content_main');

      this.$sortable_options.sortable( {
        axis : 'y',
        cancel : '.tm-pb-advanced-setting-remove, .tm-pb-advanced-setting-options',
        update : function( event, ui ) {
          TM_PageBuilder_Events.trigger( 'tm-advanced-module:updated' );
          TM_PageBuilder_Events.trigger( 'tm-advanced-module:updated_order' );
        }
      } );

      this.$add_sortable_item = this.$el.find( '.tm-pb-add-sortable-option' ).addClass( 'tm-pb-add-sortable-initial' );
    },

    events : {
      'click .tm-pb-add-sortable-option' : 'addModule',
      'click .tm-pb-advanced-setting-clone' : 'cloneModule'
    },

    render : function() {
      return this;
    },

    addModule : function( event ) {
      event.preventDefault();
      this.model.collection.add( [ {
        type : 'module',
        module_type : this.module_type,
        cid : TM_PageBuilder_Layout.generateNewId(),
        view : this,
        created : 'manually',
        mode : 'advanced',
        parent : this.attributes['cid'],
        parent_cid : this.model.model.attributes['cid']
      } ], { update_shortcodes : 'false' } );

      this.$add_sortable_item.removeClass( 'tm-pb-add-sortable-initial' );
      TM_PageBuilder_Events.trigger( 'tm-advanced-module:updated_order' );
    },

    cloneModule : function( event ) {

      event.preventDefault();
      var cloned_cid = $( event.target ).closest( 'li' ).data( 'cid' ),
        cloned_model = TM_PageBuilder_App.collection.find( function( model ) {
          return model.get('cid') == cloned_cid;
        } ),
        module_attributes = _.clone( cloned_model.attributes );

      module_attributes.created = 'manually';
      module_attributes.cloned_cid = cloned_cid;
      module_attributes.cid = TM_PageBuilder_Layout.generateNewId();

      this.model.collection.add( module_attributes );

      TM_PageBuilder_Events.trigger( 'tm-advanced-module:updated' );
      TM_PageBuilder_Events.trigger( 'tm-advanced-module:saved' );
      TM_PageBuilder_Events.trigger( 'tm-advanced-module:updated_order' );
    },

    generateContent : function() {
      var content = '';

      this.$sortable_options.find( 'li' ).each( function() {
        var $this_el = $(this);

        content += TM_PageBuilder_App.generateModuleShortcode( $this_el, false );
      } );

      // Replace double quotes with ^^ in temporary shortcodes
      content = content.replace( /%22/g, '^^' );

      this.$content_textarea.html( content );

      if ( ! this.$sortable_options.find( 'li' ).length )
        this.$add_sortable_item.addClass( 'tm-pb-add-sortable-initial' );
      else
        this.$add_sortable_item.removeClass( 'tm-pb-add-sortable-initial' );
    },

    generateAdvancedSortableItems : function( content, module_type ) {
      var this_el = this,
        tm_pb_shortcodes_tags = TM_PageBuilder_App.getShortCodeChildTags(),
        reg_exp = window.wp.shortcode.regexp( tm_pb_shortcodes_tags ),
        inner_reg_exp = TM_PageBuilder_App.wp_regexp_not_global( tm_pb_shortcodes_tags ),
        matches = content.match( reg_exp );

      if ( content !== '' )
        this.$add_sortable_item.removeClass( 'tm-pb-add-sortable-initial' );

      _.each( matches, function ( shortcode ) {
        var shortcode_element = shortcode.match( inner_reg_exp ),
          shortcode_name = shortcode_element[2],
          shortcode_attributes = shortcode_element[3] !== ''
            ? window.wp.shortcode.attrs( shortcode_element[3] )
            : '',
          shortcode_content = shortcode_element[5],
          module_cid = TM_PageBuilder_Layout.generateNewId(),
          module_settings,
          prefixed_attributes = {},
          found_inner_shortcodes = typeof shortcode_content !== 'undefined' && shortcode_content !== '' && shortcode_content.match( reg_exp );

        module_settings = {
          type : 'module',
          module_type : module_type,
          cid : TM_PageBuilder_Layout.generateNewId(),
          view : this_el,
          created : 'auto',
          mode : 'advanced',
          parent : this_el.attributes['cid'],
          parent_cid : this_el.model.model.attributes['cid']
        }

        if ( _.isObject( shortcode_attributes['named'] ) ) {
          for ( var key in shortcode_attributes['named'] ) {
            var prefixed_key = key !== 'admin_label' ? 'tm_pb_' + key : key,
              setting_value;

            if ( shortcode_name === 'column' && prefixed_key === 'tm_pb_type' )
              prefixed_key = 'layout';

            setting_value = shortcode_attributes['named'][key];

            // Replace temporary ^^ signs with double quotes
            setting_value = setting_value.replace( /\^\^/g, '"' );

            prefixed_attributes[prefixed_key] = setting_value;
          }

          module_settings['tm_pb_content_new'] = shortcode_content;

          module_settings = _.extend( module_settings, prefixed_attributes );
        }

        if ( ! found_inner_shortcodes ) {
          module_settings['tm_pb_content_new'] = shortcode_content;
        }

        this_el.model.collection.add( [ module_settings ], { update_shortcodes : 'false' } );
      } );
    },

    removeModule : function() {
      // remove Module settings, when modal window is closed or saved

      _.each( this.child_views, function( view ) {
        view.removeView();
      } );

      this.remove();
    }

  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, window.wp.Backbone ) );
