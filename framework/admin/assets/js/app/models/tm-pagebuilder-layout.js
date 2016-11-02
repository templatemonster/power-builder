( function ( $, TM_PageBuilder, Backbone ) {

  // helper module
  TM_PageBuilder.Layout = Backbone.Model.extend( {

    defaults: {
      moduleNumber : 0,
      forceRemove : false,
      modules : $.parseJSON( tm_pb_options.tm_builder_modules ),
      views : [
      ]
    },

    initialize : function() {
      // Single and double quotes are replaced with %% in tm_builder_modules
      // to avoid js conflicts.
      // Replace them with appropriate signs.
      _.each( this.get( 'modules' ), function( module ) {
        module['title'] = module['title'].replace( /%%/g, '"' );
        module['title'] = module['title'].replace( /\|\|/g, "'" );
      } );
    },

    addView : function( module_cid, view ) {
      var views = this.get( 'views' );
      views[module_cid] = view;
      if ( 'undefined' !== typeof view ) {
        this.set( { 'views' : views } );
      }
    },

    getView : function( cid ) {
      return this.get( 'views' )[cid];
    },

    getChildViews : function( parent_id ) {
      var views = this.get( 'views' ),
        child_views = {};
      _.each( views, function( view, key ) {
        if ( 'undefined' !== typeof view && view['model']['attributes']['parent'] === parent_id )
          child_views[key] = view;
      } );

      return child_views;
    },

    getChildrenViews : function( parent_id ) {
      var this_el = this,
        views = this_el.get( 'views' ),
        child_views = {},
        grand_children;

      _.each( views, function( view, key ) {
        if (  'undefined' !== typeof view && view['model']['attributes']['parent'] === parent_id ) {
          grand_children = this_el.getChildrenViews( view['model']['attributes']['cid'] );

          if ( ! _.isEmpty( grand_children ) ) {
            _.extend( child_views, grand_children );
          }

          child_views[key] = view;
        }

      } );

      return child_views;
    },

    getParentViews : function( parent_cid ) {
      var parent_view = this.getView( parent_cid ),
        parent_views = {};

      while( ! _.isUndefined( parent_view ) ) {

        parent_views[parent_view['model']['attributes']['cid']] = parent_view;
        parent_view = this.getView( parent_view['model']['attributes']['parent'] );
      }

      return parent_views;
    },

    getSectionView : function( parent_cid ) {
      var views = this.getParentViews( parent_cid ),
        section_view;

      section_view = _.filter( views, function( item ) {
        if ( item.model.attributes.type === "section" ) {
          return true;
        } else {
          return false;
        }
      } );

      if ( _.isUndefined( section_view[0] ) ) {
        return false;
      } else {
        return section_view[0];
      }
    },

    setNewParentID : function( cid, new_parent_id ) {
      var views = this.get( 'views' );

      views[cid]['model']['attributes']['parent'] = new_parent_id;

      this.set( { 'views' : views } );
    },

    removeView : function( cid ) {
      var views = this.get( 'views' ),
        new_views = {};

      _.each( views, function( value, key ) {
        if ( key != cid )
          new_views[key] = value;
      } );

      this.set( { 'views' : new_views } );
    },

    generateNewId : function() {
      var moduleNumber = this.get( 'moduleNumber' ) + 1;

      this.set( { 'moduleNumber' : moduleNumber } );

      return moduleNumber;
    },

    generateTemplateName : function( name ) {
      var default_elements = [ 'row', 'row_inner', 'section', 'column', 'column_inner'];

      if ( -1 !== $.inArray( name, default_elements ) ) {
        name = 'tm_pb_' + name;
      }

      return '#tm-builder-' + name + '-module-template';
    },

    getModuleOptionsNames : function( module_type ) {
      var modules = this.get('modules');

      return this.addAdminLabel( _.findWhere( modules, { label : module_type } )['options'] );
    },

    getNumberOf : function( element_name, module_cid ) {
      var views = this.get( 'views' ),
        num = 0;

      _.each( views, function( view ) {

        if ( 'undefined' !== typeof view ) {
          var type = view['model']['attributes']['type'];
          if ( view['model']['attributes']['parent'] === module_cid && ( type === element_name || type === ( element_name + '_inner' ) ) )
            num++;
        }

      } );

      return num;
    },

    getNumberOfModules : function( module_name ) {
      var views = this.get( 'views' ),
        num = 0;

      _.each( views, function( view ) {
          if ( 'undefined' !== typeof view && view['model']['attributes']['type'] === module_name )
            num++;
      } );

      return num;
    },

    getTitleByShortcodeTag : function ( tag ) {
      var modules = this.get('modules');

      return _.findWhere( modules, { label : tag } )['title'];
    },

    isModuleFullwidth : function ( module_type ) {
      var modules = this.get('modules');

      return 'on' === _.findWhere( modules, { label : module_type } )['fullwidth_only'] ? true : false;
    },

    isChildrenLocked : function ( module_cid ) {
      var children_views = this.getChildrenViews( module_cid ),
        children_locked = false;

      _.each( children_views, function( child ) {
        if ( child.model.get( 'tm_pb_locked' ) === 'on' || child.model.get( 'tm_pb_parent_locked' ) === 'on' ) {
          children_locked = true;
        }
      } );

      return children_locked;
    },

    addAdminLabel : function ( optionsNames ) {
      return _.union( optionsNames, ['admin_label'] );
    }

  } );

  // Export globals
  window.TM_PageBuilder = TM_PageBuilder;

} ( jQuery, TM_PageBuilder, Backbone ) );
