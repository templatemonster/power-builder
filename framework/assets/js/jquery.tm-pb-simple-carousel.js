(function($) {
  $.tm_pb_simple_carousel = function(el, options) {
    var settings = $.extend( {
      slide_duration	: 500,
    }, options );

    var $tm_carousel 			= $(el),
      $carousel_items 		= $tm_carousel.find('.tm_pb_carousel_items'),
      $the_carousel_items 	= $carousel_items.find('.tm_pb_carousel_item');

    $tm_carousel.tm_animation_running = false;

    $tm_carousel.addClass('container-width-change-notify').on('containerWidthChanged', function( event ){
      set_carousel_columns( $tm_carousel );
      set_carousel_height( $tm_carousel );
    });

    $carousel_items.data('items', $the_carousel_items.toArray() );
    $tm_carousel.data('columns_setting_up', false );

    $carousel_items.prepend('<div class="tm-pb-slider-arrows"><a class="tm-pb-slider-arrow et-pb-arrow-prev" href="#">' + '<span>' + tm_pb_custom.previous + '</span>' + '</a><a class="tm-pb-slider-arrow et-pb-arrow-next" href="#">' + '<span>' + tm_pb_custom.next + '</span>' + '</a></div>');

    set_carousel_columns( $tm_carousel );
    set_carousel_height( $tm_carousel );

    $tm_carousel_next 	= $tm_carousel.find( '.tm-pb-arrow-next' );
    $tm_carousel_prev 	= $tm_carousel.find( '.tm-pb-arrow-prev'  );

    $tm_carousel_next.click( function(){
      if ( $tm_carousel.tm_animation_running ) return false;

      $tm_carousel.tm_carousel_move_to( 'next' );

      return false;
    } );

    $tm_carousel_prev.click( function(){
      if ( $tm_carousel.tm_animation_running ) return false;

      $tm_carousel.tm_carousel_move_to( 'previous' );

      return false;
    } );

    // swipe support requires et-jquery-touch-mobile
    $tm_carousel.on( 'swipeleft', function() {
      $tm_carousel.tm_carousel_move_to( 'next' );
    });
    $tm_carousel.on( 'swiperight', function() {
      $tm_carousel.tm_carousel_move_to( 'previous' );
    });

    function set_carousel_height( $the_carousel ) {
      var carousel_items_width = $the_carousel_items.width(),
        carousel_items_height = $the_carousel_items.height();

      $carousel_items.css('height', carousel_items_height + 'px' );
    }

    function set_carousel_columns( $the_carousel ) {
      var columns,
        $carousel_parent = $the_carousel.parents('.tm_pb_column'),
        carousel_items_width = $carousel_items.width(),
        carousel_item_count = $the_carousel_items.length;

      if ( $carousel_parent.hasClass('tm_pb_column_4_4') || $carousel_parent.hasClass('tm_pb_column_3_4') || $carousel_parent.hasClass('tm_pb_column_2_3') ) {
        if ( $tm_window.width() < 768 ) {
          columns = 3;
        } else {
          columns = 4;
        }
      } else if ( $carousel_parent.hasClass('tm_pb_column_1_2') || $carousel_parent.hasClass('tm_pb_column_3_8') || $carousel_parent.hasClass('tm_pb_column_1_3') ) {
        columns = 3;
      } else if ( $carousel_parent.hasClass('tm_pb_column_1_4') ) {
        if ( $tm_window.width() > 480 && $tm_window.width() < 980 ) {
          columns = 3;
        } else {
          columns = 2;
        }
      }

      if ( columns === $carousel_items.data('portfolio-columns') ) {
        return;
      }

      if ( $the_carousel.data('columns_setting_up') ) {
        return;
      }

      $the_carousel.data('columns_setting_up', true );

      // store last setup column
      $carousel_items.removeClass('columns-' + $carousel_items.data('portfolio-columns') );
      $carousel_items.addClass('columns-' + columns );
      $carousel_items.data('portfolio-columns', columns );

      // kill all previous groups to get ready to re-group
      if ( $carousel_items.find('.tm-carousel-group').length ) {
        $the_carousel_items.appendTo( $carousel_items );
        $carousel_items.find('.tm-carousel-group').remove();
      }

      // setup the grouping
      var the_carousel_items = $carousel_items.data('items'),
        $carousel_group = $('<div class="tm-carousel-group active">').appendTo( $carousel_items );

      $the_carousel_items.data('position', '');
      if ( the_carousel_items.length <= columns ) {
        $carousel_items.find('.tm-pb-slider-arrows').hide();
      } else {
        $carousel_items.find('.tm-pb-slider-arrows').show();
      }

      for ( position = 1, x=0 ;x < the_carousel_items.length; x++, position++ ) {
        if ( x < columns ) {
          $( the_carousel_items[x] ).show();
          $( the_carousel_items[x] ).appendTo( $carousel_group );
          $( the_carousel_items[x] ).data('position', position );
          $( the_carousel_items[x] ).addClass('position_' + position );
        } else {
          position = $( the_carousel_items[x] ).data('position');
          $( the_carousel_items[x] ).removeClass('position_' + position );
          $( the_carousel_items[x] ).data('position', '' );
          $( the_carousel_items[x] ).hide();
        }
      }

      $the_carousel.data('columns_setting_up', false );

    } /* end set_carousel_columns() */

    $tm_carousel.tm_carousel_move_to = function ( direction ) {
      var $active_carousel_group 	= $carousel_items.find('.tm-carousel-group.active'),
        items 					= $carousel_items.data('items'),
        columns 				= $carousel_items.data('portfolio-columns');

      $tm_carousel.tm_animation_running = true;

      var left = 0;
      $active_carousel_group.children().each(function(){
        $(this).css({'position':'absolute', 'left': left });
        left = left + $(this).outerWidth(true);
      });

      if ( direction == 'next' ) {
        var $next_carousel_group,
          current_position = 1,
          next_position = 1,
          active_items_start = items.indexOf( $active_carousel_group.children().first()[0] ),
          active_items_end = active_items_start + columns,
          next_items_start = active_items_end,
          next_items_end = next_items_start + columns;

        $next_carousel_group = $('<div class="tm-carousel-group next" style="display: none;left: 100%;position: absolute;top: 0;">').insertAfter( $active_carousel_group );
        $next_carousel_group.css({ 'width': $active_carousel_group.innerWidth() }).show();

        // this is an endless loop, so it can decide internally when to break out, so that next_position
        // can get filled up, even to the extent of an element having both and current_ and next_ position
        for( x = 0, total = 0 ; ; x++, total++ ) {
          if ( total >= active_items_start && total < active_items_end ) {
            $( items[x] ).addClass( 'changing_position current_position current_position_' + current_position );
            $( items[x] ).data('current_position', current_position );
            current_position++;
          }

          if ( total >= next_items_start && total < next_items_end ) {
            $( items[x] ).data('next_position', next_position );
            $( items[x] ).addClass('changing_position next_position next_position_' + next_position );

            if ( !$( items[x] ).hasClass( 'current_position' ) ) {
              $( items[x] ).addClass('container_append');
            } else {
              $( items[x] ).clone(true).appendTo( $active_carousel_group ).hide().addClass('delayed_container_append_dup').attr('id', $( items[x] ).attr('id') + '-dup' );
              $( items[x] ).addClass('delayed_container_append');
            }

            next_position++;
          }

          if ( next_position > columns ) {
            break;
          }

          if ( x >= ( items.length -1 )) {
            x = -1;
          }
        }

        var sorted = $carousel_items.find('.container_append, .delayed_container_append_dup').sort(function (a, b) {
          var el_a_position = parseInt( $(a).data('next_position') );
          var el_b_position = parseInt( $(b).data('next_position') );
          return ( el_a_position < el_b_position ) ? -1 : ( el_a_position > el_b_position ) ? 1 : 0;
        });

        $( sorted ).show().appendTo( $next_carousel_group );

        var left = 0;
        $next_carousel_group.children().each(function(){
          $(this).css({'position':'absolute', 'left': left });
          left = left + $(this).outerWidth(true);
        });

        $active_carousel_group.animate({
          left: '-100%'
        }, {
          duration: settings.slide_duration,
          complete: function() {
            $carousel_items.find('.delayed_container_append').each(function(){
              left = $( '#' + $(this).attr('id') + '-dup' ).css('left');
              $(this).css({'position':'absolute', 'left': left });
              $(this).appendTo( $next_carousel_group );
            });

            $active_carousel_group.removeClass('active');
            $active_carousel_group.children().each(function(){
              position = $(this).data('position');
              current_position = $(this).data('current_position');
              $(this).removeClass('position_' + position + ' ' + 'changing_position current_position current_position_' + current_position );
              $(this).data('position', '');
              $(this).data('current_position', '');
              $(this).hide();
              $(this).css({'position': '', 'left': ''});
              $(this).appendTo( $carousel_items );
            });

            $active_carousel_group.remove();

          }
        } );

        next_left = $active_carousel_group.width() + parseInt( $the_carousel_items.first().css('marginRight').slice(0, -2) );
        $next_carousel_group.addClass('active').css({'position':'absolute', 'top':0, left: next_left });
        $next_carousel_group.animate({
          left: '0%'
        }, {
          duration: settings.slide_duration,
          complete: function(){
            $next_carousel_group.removeClass('next').addClass('active').css({'position':'', 'width':'', 'top':'', 'left': ''});

            $next_carousel_group.find('.changing_position').each(function( index ){
              position = $(this).data('position');
              current_position = $(this).data('current_position');
              next_position = $(this).data('next_position');
              $(this).removeClass('container_append delayed_container_append position_' + position + ' ' + 'changing_position current_position current_position_' + current_position + ' next_position next_position_' + next_position );
              $(this).data('current_position', '');
              $(this).data('next_position', '');
              $(this).data('position', ( index + 1 ) );
            });

            $next_carousel_group.children().css({'position': '', 'left': ''});
            $next_carousel_group.find('.delayed_container_append_dup').remove();

            $tm_carousel.tm_animation_running = false;
          }
        } );

      } else if ( direction == 'previous' ) {
        var $prev_carousel_group,
          current_position = columns,
          prev_position = columns,
          columns_span = columns - 1,
          active_items_start = items.indexOf( $active_carousel_group.children().last()[0] ),
          active_items_end = active_items_start - columns_span,
          prev_items_start = active_items_end - 1,
          prev_items_end = prev_items_start - columns_span;

        $prev_carousel_group = $('<div class="tm-carousel-group prev" style="display: none;left: 100%;position: absolute;top: 0;">').insertBefore( $active_carousel_group );
        $prev_carousel_group.css({ 'left': '-' + $active_carousel_group.innerWidth(), 'width': $active_carousel_group.innerWidth() }).show();

        // this is an endless loop, so it can decide internally when to break out, so that next_position
        // can get filled up, even to the extent of an element having both and current_ and next_ position
        for( x = ( items.length - 1 ), total = ( items.length - 1 ) ; ; x--, total-- ) {

          if ( total <= active_items_start && total >= active_items_end ) {
            $( items[x] ).addClass( 'changing_position current_position current_position_' + current_position );
            $( items[x] ).data('current_position', current_position );
            current_position--;
          }

          if ( total <= prev_items_start && total >= prev_items_end ) {
            $( items[x] ).data('prev_position', prev_position );
            $( items[x] ).addClass('changing_position prev_position prev_position_' + prev_position );

            if ( !$( items[x] ).hasClass( 'current_position' ) ) {
              $( items[x] ).addClass('container_append');
            } else {
              $( items[x] ).clone(true).appendTo( $active_carousel_group ).addClass('delayed_container_append_dup').attr('id', $( items[x] ).attr('id') + '-dup' );
              $( items[x] ).addClass('delayed_container_append');
            }

            prev_position--;
          }

          if ( prev_position <= 0 ) {
            break;
          }

          if ( x == 0 ) {
            x = items.length;
          }
        }

        var sorted = $carousel_items.find('.container_append, .delayed_container_append_dup').sort(function (a, b) {
          var el_a_position = parseInt( $(a).data('prev_position') );
          var el_b_position = parseInt( $(b).data('prev_position') );
          return ( el_a_position < el_b_position ) ? -1 : ( el_a_position > el_b_position ) ? 1 : 0;
        });

        $( sorted ).show().appendTo( $prev_carousel_group );

        var left = 0;
        $prev_carousel_group.children().each(function(){
          $(this).css({'position':'absolute', 'left': left });
          left = left + $(this).outerWidth(true);
        });

        $active_carousel_group.animate({
          left: '100%'
        }, {
          duration: settings.slide_duration,
          complete: function() {
            $carousel_items.find('.delayed_container_append').reverse().each(function(){
              left = $( '#' + $(this).attr('id') + '-dup' ).css('left');
              $(this).css({'position':'absolute', 'left': left });
              $(this).prependTo( $prev_carousel_group );
            });

            $active_carousel_group.removeClass('active');
            $active_carousel_group.children().each(function(){
              position = $(this).data('position');
              current_position = $(this).data('current_position');
              $(this).removeClass('position_' + position + ' ' + 'changing_position current_position current_position_' + current_position );
              $(this).data('position', '');
              $(this).data('current_position', '');
              $(this).hide();
              $(this).css({'position': '', 'left': ''});
              $(this).appendTo( $carousel_items );
            });

            $active_carousel_group.remove();
          }
        } );

        prev_left = (-1) * $active_carousel_group.width() - parseInt( $the_carousel_items.first().css('marginRight').slice(0, -2) );
        $prev_carousel_group.addClass('active').css({'position':'absolute', 'top':0, left: prev_left });
        $prev_carousel_group.animate({
          left: '0%'
        }, {
          duration: settings.slide_duration,
          complete: function(){
            $prev_carousel_group.removeClass('prev').addClass('active').css({'position':'', 'width':'', 'top':'', 'left': ''});

            $prev_carousel_group.find('.delayed_container_append_dup').remove();

            $prev_carousel_group.find('.changing_position').each(function( index ){
              position = $(this).data('position');
              current_position = $(this).data('current_position');
              prev_position = $(this).data('prev_position');
              $(this).removeClass('container_append delayed_container_append position_' + position + ' ' + 'changing_position current_position current_position_' + current_position + ' prev_position prev_position_' + prev_position );
              $(this).data('current_position', '');
              $(this).data('prev_position', '');
              position = index + 1;
              $(this).data('position', position );
              $(this).addClass('position_' + position );
            });

            $prev_carousel_group.children().css({'position': '', 'left': ''});
            $tm_carousel.tm_animation_running = false;
          }
        } );
      }
    }
  }

  $.fn.tm_pb_simple_carousel = function( options ) {
    return this.each(function() {
      new $.tm_pb_simple_carousel(this, options);
    });
  }

}(jQuery));
