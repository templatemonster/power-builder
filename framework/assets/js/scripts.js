var $tm_pb_slider  = jQuery( '.tm_pb_slider' ),
	$tm_pb_tabs    = jQuery( '.tm_pb_tabs' ),
	$tm_pb_tabs_li = $tm_pb_tabs.find( '.tm_pb_tabs_controls li' ),
	$tm_pb_video_section = jQuery('.tm_pb_section_video_bg'),
	$tm_pb_newsletter_button = jQuery( '.tm_pb_newsletter_button' ),
	$tm_pb_filterable_portfolio = jQuery( '.tm_pb_filterable_portfolio' ),
	$tm_pb_fullwidth_portfolio = jQuery( '.tm_pb_fullwidth_portfolio' ),
	$tm_pb_gallery = jQuery( '.tm_pb_gallery' ),
	$tm_pb_countdown_timer = jQuery( '.tm_pb_countdown_timer' ),
	$tm_post_gallery = jQuery( '.tm_post_gallery' ),
	$tm_lightbox_image = jQuery( '.tm_pb_lightbox_image'),
	$tm_pb_map    = jQuery( '.tm_pb_map_container' ),
	$tm_pb_circle_counter = jQuery( '.tm_pb_circle_counter_bar' ),
	$tm_pb_number_counter = jQuery( '.tm_pb_number_counter' ),
	$tm_pb_parallax = jQuery( '.tm_parallax_bg' ),
	$tm_pb_shop = jQuery( '.tm_pb_shop' ),
	$tm_pb_post_fullwidth = jQuery( '.single.tm_pb_pagebuilder_layout.tm_full_width_page' ),
	tm_is_mobile_device = navigator.userAgent.match( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/ ),
	tm_is_ipad = navigator.userAgent.match( /iPad/ ),
	$tm_container = ! tm_pb_custom.is_builder_plugin_used ? jQuery( '.container' ) : jQuery( '.tm_pb_row' ),
	tm_container_width = $tm_container.width(),
	tm_is_fixed_nav = jQuery( 'body' ).hasClass( 'tm_fixed_nav' ),
	tm_is_vertical_fixed_nav = jQuery( 'body' ).hasClass( 'tm_vertical_fixed' ),
	tm_is_rtl = jQuery( 'body' ).hasClass( 'rtl' ),
	tm_hide_nav = jQuery( 'body' ).hasClass( 'tm_hide_nav' ),
	tm_header_style_left = jQuery( 'body' ).hasClass( 'tm_header_style_left' ),
	tm_vertical_navigation = jQuery( 'body' ).hasClass( 'tm_vertical_nav' ),
	$top_header = jQuery('#top-header'),
	$main_header = jQuery('#main-header'),
	$main_container_wrapper = jQuery( '#page-container' ),
	$tm_transparent_nav = jQuery( '.tm_transparent_nav' ),
	$tm_pb_first_row = jQuery( 'body.tm_pb_pagebuilder_layout .tm_pb_section:first-child' ),
	$tm_main_content_first_row = jQuery( '#main-content .container:first-child' ),
	$tm_main_content_first_row_meta_wrapper = $tm_main_content_first_row.find('.tm_post_meta_wrapper:first'),
	$tm_main_content_first_row_meta_wrapper_title = $tm_main_content_first_row_meta_wrapper.find( 'h1' ),
	$tm_main_content_first_row_content = $tm_main_content_first_row.find('.entry-content:first'),
	$tm_single_post = jQuery( 'body.single-post' ),
	$tm_window = jQuery(window),
	etRecalculateOffset = false,
	tm_header_height,
	tm_header_modifier,
	tm_header_offset,
	tm_primary_header_top,
	$tm_vertical_nav = jQuery('.tm_vertical_nav'),
	$tm_header_style_split = jQuery('.tm_header_style_split'),
	$tm_top_navigation = jQuery('#tm-top-navigation'),
	$logo = jQuery('#logo'),
	$tm_sticky_image = jQuery('.tm_pb_image_sticky'),
	$tm_pb_counter_amount = jQuery('.tm_pb_counter_amount'),
	$tm_pb_carousel = jQuery( '.tm_pb_carousel' ),
	$tm_menu_selector = tm_pb_custom.is_divi_theme_used ? jQuery( 'ul.nav' ) : jQuery( '.tm_pb_fullwidth_menu ul.nav' );

jQuery(document).ready( function($){
	var $tm_top_menu = $tm_menu_selector,
		tm_parent_menu_longpress_limit = 300,
		tm_parent_menu_longpress_start,
		tm_parent_menu_click = true;

	$( '.tm_pb_posts' ).each( function() {

		var $item  = $( this ),
			loader = '<div class="tm-pb-spinner tm-pb-spinner-double-bounce"><div class="tm-pb-double-bounce1"></div><div class="tm-pb-double-bounce2"></div></div>';

		$item.on( 'click', '.tm_pb_ajax_more', function( event ) {

			var $this   = $( this ),
				$result = $item.find( '.tm-posts_listing .row' ),
				pages   = $item.data( 'pages' ),
				data    = new Object();

			event.preventDefault();

			if ( $this.hasClass( 'in-progress' ) ) {
				return;
			}

			data.page   = $item.data( 'page' );
			data.atts   = $item.data( 'atts' );
			data.action = 'tm_pb_load_more';

			$this.addClass( 'in-progress' ).after( loader );

			$.ajax({
				url: window.tm_pb_custom.ajaxurl,
				type: 'post',
				dataType: 'json',
				data: data,
				error: function() {
					$this.removeClass( 'in-progress' ).next( '.tm-pb-spinner' ).remove();
				}
			}).done( function( response ) {
				$result.append( response.data.result );
				$item.data( 'page', response.data.page );
				$this.removeClass( 'in-progress' ).next( '.tm-pb-spinner' ).remove();
				if ( response.data.page == pages ) {
					$this.addClass( 'btn-hidden' );
				}

			});

		});

	});

	if ( $( '.tm_pb_row' ).length ) {
		$( '.tm_pb_row' ).each( function() {
			var $this_row = $( this ),
				row_class = '';

			row_class = tm_get_column_types( $this_row.find( '>.tm_pb_column' ) );

			if ( '' !== row_class && ( -1 !== row_class.indexOf( '1-4' ) || '_4col' === row_class ) ) {
				$this_row.addClass( 'tm_pb_row' + row_class );
			}

			if ( $this_row.find( '.tm_pb_row_inner' ).length ) {
				$this_row.find( '.tm_pb_row_inner' ).each( function() {
					var $this_row_inner = $( this );
					row_class = tm_get_column_types( $this_row_inner.find( '.tm_pb_column' ) );

					if ( '' !== row_class && -1 !== row_class.indexOf( '1-4' ) ) {
						$this_row_inner.addClass( 'tm_pb_row' + row_class );
					}
				});
			}
		});
	}

	function tm_get_column_types( $columns ) {
		var row_class = '';

		if ( $columns.length ) {
			$columns.each( function() {
				var $this_column = $( this ),
					column_type = $this_column.attr( 'class' ).split( 'tm_pb_column_' )[1],
					column_type_clean = column_type.split( ' ', 1 )[0],
					column_type_updated = column_type_clean.replace( '_', '-' ).trim();

				row_class += '_' + column_type_updated;
			});

			row_class = '_1-4_1-4_1-4_1-4' === row_class ? '_4col' : row_class;
		}

		return row_class;
	}

	$tm_top_menu.find( 'li' ).hover( function() {
		if ( ! $(this).closest( 'li.mega-menu' ).length || $(this).hasClass( 'mega-menu' ) ) {
			$(this).addClass( 'tm-show-dropdown' );
			$(this).removeClass( 'tm-hover' ).addClass( 'tm-hover' );
		}
	}, function() {
		var $this_el = $(this);

		$this_el.removeClass( 'tm-show-dropdown' );

		setTimeout( function() {
			if ( ! $this_el.hasClass( 'tm-show-dropdown' ) ) {
				$this_el.removeClass( 'tm-hover' );
			}
		}, 200 );
	} );

	// Dropdown menu adjustment for touch screen
	$tm_top_menu.find('.menu-item-has-children > a').on( 'touchstart', function(){
		tm_parent_menu_longpress_start = new Date().getTime();
	} ).on( 'touchend', function(){
		var tm_parent_menu_longpress_end = new Date().getTime()
		if ( tm_parent_menu_longpress_end  >= tm_parent_menu_longpress_start + tm_parent_menu_longpress_limit ) {
			tm_parent_menu_click = true;
		} else {
			tm_parent_menu_click = false;

			// Close sub-menu if toggled
			var $tm_parent_menu = $(this).parent('li');
			if ( $tm_parent_menu.hasClass( 'tm-hover') ) {
				$tm_parent_menu.trigger( 'mouseleave' );
			} else {
				$tm_parent_menu.trigger( 'mouseenter' );
			}
		}
		tm_parent_menu_longpress_start = 0;
	} ).click(function() {
		if ( tm_parent_menu_click ) {
			return true;
		}

		return false;
	} );

	$tm_top_menu.find( 'li.mega-menu' ).each(function(){
		var $li_mega_menu           = $(this),
			$li_mega_menu_item      = $li_mega_menu.children( 'ul' ).children( 'li' ),
			li_mega_menu_item_count = $li_mega_menu_item.length;

		if ( li_mega_menu_item_count < 4 ) {
			$li_mega_menu.addClass( 'mega-menu-parent mega-menu-parent-' + li_mega_menu_item_count );
		}
	});

	$tm_sticky_image.each( function() {
		var $this_el            = $(this),
			$row                = $this_el.closest('.tm_pb_row'),
			$section            = $row.closest('.tm_pb_section'),
			$column             = $this_el.closest( '.tm_pb_column' ),
			sticky_class        = 'tm_pb_section_sticky',
			sticky_mobile_class = 'tm_pb_section_sticky_mobile';

		// If it is not in the last row, continue
		if ( ! $row.is( ':last-child' ) ) {
			return true;
		}

		// Make sure sticky image is the last element in the column
		if ( ! $this_el.is( ':last-child' ) ) {
			return true;
		}

		// If it is in the last row, find the parent section and attach new class to it
		if ( ! $section.hasClass( sticky_class ) ) {
			$section.addClass( sticky_class );
		}

		$column.addClass( 'tm_pb_row_sticky' );

		if ( ! $section.hasClass( sticky_mobile_class ) && $column.is( ':last-child' ) ) {
			$section.addClass( sticky_mobile_class );
		}
	} );

	if ( tm_is_mobile_device ) {
		$( '.tm_pb_section_video_bg' ).each( function() {
			var $this_el = $(this);

			$this_el.css( 'visibility', 'hidden' ).closest( '.tm_pb_preload' ).removeClass( 'tm_pb_preload' )
		} );

		$( 'body' ).addClass( 'tm_mobile_device' );

		if ( ! tm_is_ipad ) {
			$( 'body' ).addClass( 'tm_mobile_device_not_ipad' );
		}
	}

	if ( $tm_pb_video_section.length ) {
		$tm_pb_video_section.find( 'video' ).mediaelementplayer( {
			pauseOtherPlayers: false,
			success : function( mediaElement, domObject ) {
				mediaElement.addEventListener( 'loadeddata', function() {
					tm_pb_resize_section_video_bg( $(domObject) );
					tm_pb_center_video( $(domObject) );
				}, false );

				mediaElement.addEventListener( 'canplay', function() {
					$(domObject).closest( '.tm_pb_preload' ).removeClass( 'tm_pb_preload' );
				}, false );
			}
		} );
	}

	if ( $tm_post_gallery.length ) {
		// swipe support in magnific popup only if gallery exists
		var magnificPopup = $.magnificPopup.instance;

		$( 'body' ).on( 'swiperight', '.mfp-container', function() {
			magnificPopup.prev();
		} );
		$( 'body' ).on( 'swipeleft', '.mfp-container', function() {
			magnificPopup.next();
		} );

		$tm_post_gallery.each(function() {
			$(this).magnificPopup( {
				delegate: 'a',
				type: 'image',
				removalDelay: 500,
				gallery: {
					enabled: true,
					navigateByImgClick: true
				},
				mainClass: 'mfp-fade',
				zoom: {
					enabled: true,
					duration: 500,
					opener: function(element) {
						return element.find('img');
					}
				}
			} );
		} );
		// prevent attaching of any further actions on click
		$tm_post_gallery.find( 'a' ).unbind( 'click' );
	}

	if ( $tm_lightbox_image.length ) {
		// prevent attaching of any further actions on click
		$tm_lightbox_image.unbind( 'click' );
		$tm_lightbox_image.bind( 'click' );

		$tm_lightbox_image.magnificPopup( {
			type: 'image',
			removalDelay: 500,
			mainClass: 'mfp-fade',
			zoom: {
				enabled: true,
				duration: 500,
				opener: function(element) {
					return element.find('img');
				}
			}
		} );
	}

	if ( $tm_pb_slider.length ) {
		$tm_pb_slider.each( function() {
			var $this_slider = $(this),
				tm_slider_settings = {
					fade_speed 		: 700,
					slide			: ! $this_slider.hasClass( 'tm_pb_gallery' ) ? '.tm_pb_slide' : '.tm_pb_gallery_item'
				}

			if ( $this_slider.hasClass('tm_pb_slider_no_arrows') )
				tm_slider_settings.use_arrows = false;

			if ( $this_slider.hasClass('tm_pb_slider_no_pagination') )
				tm_slider_settings.use_controls = false;

			if ( $this_slider.hasClass('tm_slider_auto') ) {
				var tm_slider_autospeed_class_value = /tm_slider_speed_(\d+)/g;

				tm_slider_settings.slideshow = true;

				tm_slider_autospeed = tm_slider_autospeed_class_value.exec( $this_slider.attr('class') );

				tm_slider_settings.slideshow_speed = tm_slider_autospeed[1];
			}

			if ( $this_slider.parent().hasClass('tm_pb_video_slider') ) {
				tm_slider_settings.controls_below = true;
				tm_slider_settings.append_controls_to = $this_slider.parent();

				setTimeout( function() {
					$( '.tm_pb_preload' ).removeClass( 'tm_pb_preload' );
				}, 500 );
			}

			if ( $this_slider.hasClass('tm_pb_slider_carousel') )
				tm_slider_settings.use_carousel = true;

			$this_slider.tm_pb_simple_slider( tm_slider_settings );

		} );
	}

	$tm_pb_carousel  = $( '.tm_pb_carousel' );
	if ( $tm_pb_carousel.length ) {
		$tm_pb_carousel.each( function() {
			var $this_carousel = $(this),
				tm_carousel_settings = {
					fade_speed 		: 1000
				};

			$this_carousel.tm_pb_simple_carousel( tm_carousel_settings );
		} );
	}

	if ( $tm_pb_fullwidth_portfolio.length ) {

		function set_fullwidth_portfolio_columns( $the_portfolio, carousel_mode ) {
			var columns,
				$portfolio_items = $the_portfolio.find('.tm_pb_portfolio_items'),
				portfolio_items_width = $portfolio_items.width(),
				$the_portfolio_items = $portfolio_items.find('.tm_pb_portfolio_item'),
				portfolio_item_count = $the_portfolio_items.length;

			// calculate column breakpoints
			if ( portfolio_items_width >= 1600 ) {
				columns = 5;
			} else if ( portfolio_items_width >= 1024 ) {
				columns = 4;
			} else if ( portfolio_items_width >= 768 ) {
				columns = 3;
			} else if ( portfolio_items_width >= 480 ) {
				columns = 2;
			} else {
				columns = 1;
			}

			// set height of items
			portfolio_item_width = portfolio_items_width / columns;
			portfolio_item_height = portfolio_item_width * .75;

			if ( carousel_mode ) {
				$portfolio_items.css({ 'height' : portfolio_item_height });
			}

			$the_portfolio_items.css({ 'height' : portfolio_item_height });

			if ( columns === $portfolio_items.data('portfolio-columns') ) {
				return;
			}

			if ( $the_portfolio.data('columns_setting_up') ) {
				return;
			}

			$the_portfolio.data('columns_setting_up', true );

			var portfolio_item_width_percentage = ( 100 / columns ) + '%';
			$the_portfolio_items.css({ 'width' : portfolio_item_width_percentage });

			// store last setup column
			$portfolio_items.removeClass('columns-' + $portfolio_items.data('portfolio-columns') );
			$portfolio_items.addClass('columns-' + columns );
			$portfolio_items.data('portfolio-columns', columns );

			if ( !carousel_mode ) {
				return $the_portfolio.data('columns_setting_up', false );
			}

			// kill all previous groups to get ready to re-group
			if ( $portfolio_items.find('.tm_pb_carousel_group').length ) {
				$the_portfolio_items.appendTo( $portfolio_items );
				$portfolio_items.find('.tm_pb_carousel_group').remove();
			}

			// setup the grouping
			var the_portfolio_items = $portfolio_items.data('items' ),
				$carousel_group = $('<div class="tm_pb_carousel_group active">').appendTo( $portfolio_items );

			$the_portfolio_items.data('position', '');
			if ( the_portfolio_items.length <= columns ) {
				$portfolio_items.find('.tm-pb-slider-arrows').hide();
			} else {
				$portfolio_items.find('.tm-pb-slider-arrows').show();
			}

			for ( position = 1, x=0 ;x < the_portfolio_items.length; x++, position++ ) {
				if ( x < columns ) {
					$( the_portfolio_items[x] ).show();
					$( the_portfolio_items[x] ).appendTo( $carousel_group );
					$( the_portfolio_items[x] ).data('position', position );
					$( the_portfolio_items[x] ).addClass('position_' + position );
				} else {
					position = $( the_portfolio_items[x] ).data('position');
					$( the_portfolio_items[x] ).removeClass('position_' + position );
					$( the_portfolio_items[x] ).data('position', '' );
					$( the_portfolio_items[x] ).hide();
				}
			}

			$the_portfolio.data('columns_setting_up', false );

		}

		function tm_carousel_auto_rotate( $carousel ) {
			if ( 'on' === $carousel.data('auto-rotate') && $carousel.find('.tm_pb_portfolio_item').length > $carousel.find('.tm_pb_carousel_group .tm_pb_portfolio_item').length && ! $carousel.hasClass( 'tm_carousel_hovered' ) ) {

				tm_carousel_timer = setTimeout( function() {
					$carousel.find('.tm-pb-arrow-next').click();
				}, $carousel.data('auto-rotate-speed') );

				$carousel.data('tm_carousel_timer', tm_carousel_timer);
			}
		}

		$tm_pb_fullwidth_portfolio.each(function(){
			var $the_portfolio = $(this),
				$portfolio_items = $the_portfolio.find('.tm_pb_portfolio_items');

				$portfolio_items.data('items', $portfolio_items.find('.tm_pb_portfolio_item').toArray() );
				$the_portfolio.data('columns_setting_up', false );

			if ( $the_portfolio.hasClass('tm_pb_fullwidth_portfolio_carousel') ) {
				// add left and right arrows
				$portfolio_items.prepend('<div class="tm-pb-slider-arrows"><a class="tm-pb-arrow-prev" href="#">' + '<span>' + tm_pb_custom.previous + '</span>' + '</a><a class="tm-pb-arrow-next" href="#">' + '<span>' + tm_pb_custom.next + '</span>' + '</a></div>');

				set_fullwidth_portfolio_columns( $the_portfolio, true );

				tm_carousel_auto_rotate( $the_portfolio );

				// swipe support
				$the_portfolio.on( 'swiperight', function() {
					$( this ).find( '.tm-pb-arrow-prev' ).click();
				});
				$the_portfolio.on( 'swipeleft', function() {
					$( this ).find( '.tm-pb-arrow-next' ).click();
				});

				$the_portfolio.hover(
					function(){
						$(this).addClass('tm_carousel_hovered');
						if ( typeof $(this).data('tm_carousel_timer') != 'undefined' ) {
							clearInterval( $(this).data('tm_carousel_timer') );
						}
					},
					function(){
						$(this).removeClass('tm_carousel_hovered');
						tm_carousel_auto_rotate( $(this) );
					}
				);

				$the_portfolio.data('carouseling', false );

				$the_portfolio.on('click', '.tm-pb-slider-arrows a', function(e){
					var $the_portfolio = $(this).parents('.tm_pb_fullwidth_portfolio'),
						$portfolio_items = $the_portfolio.find('.tm_pb_portfolio_items'),
						$the_portfolio_items = $portfolio_items.find('.tm_pb_portfolio_item'),
						$active_carousel_group = $portfolio_items.find('.tm_pb_carousel_group.active'),
						slide_duration = 700,
						items = $portfolio_items.data('items'),
						columns = $portfolio_items.data('portfolio-columns'),
						item_width = $active_carousel_group.innerWidth() / columns, //$active_carousel_group.children().first().innerWidth(),
						original_item_width = ( 100 / columns ) + '%';

					e.preventDefault();

					if ( $the_portfolio.data('carouseling') ) {
						return;
					}

					$the_portfolio.data('carouseling', true);

					$active_carousel_group.children().each(function(){
						$(this).css({'width': $(this).innerWidth() + 1, 'position':'absolute', 'left': ( $(this).innerWidth() * ( $(this).data('position') - 1 ) ) });
					});

					if ( $(this).hasClass('tm-pb-arrow-next') ) {
						var $next_carousel_group,
							current_position = 1,
							next_position = 1,
							active_items_start = items.indexOf( $active_carousel_group.children().first()[0] ),
							active_items_end = active_items_start + columns,
							next_items_start = active_items_end,
							next_items_end = next_items_start + columns;

						$next_carousel_group = $('<div class="tm_pb_carousel_group next" style="display: none;left: 100%;position: absolute;top: 0;">').insertAfter( $active_carousel_group );
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

						sorted = $portfolio_items.find('.container_append, .delayed_container_append_dup').sort(function (a, b) {
							var el_a_position = parseInt( $(a).data('next_position') );
							var el_b_position = parseInt( $(b).data('next_position') );
							return ( el_a_position < el_b_position ) ? -1 : ( el_a_position > el_b_position ) ? 1 : 0;
						});

						$( sorted ).show().appendTo( $next_carousel_group );

						$next_carousel_group.children().each(function(){
							$(this).css({'width': item_width, 'position':'absolute', 'left': ( item_width * ( $(this).data('next_position') - 1 ) ) });
						});

						$active_carousel_group.animate({
							left: '-100%'
						}, {
							duration: slide_duration,
							complete: function() {
								$portfolio_items.find('.delayed_container_append').each(function(){
									$(this).css({'width': item_width, 'position':'absolute', 'left': ( item_width * ( $(this).data('next_position') - 1 ) ) });
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
									$(this).css({'position': '', 'width': '', 'left': ''});
									$(this).appendTo( $portfolio_items );
								});

								$active_carousel_group.remove();

								tm_carousel_auto_rotate( $the_portfolio );

							}
						} );

						$next_carousel_group.addClass('active').css({'position':'absolute', 'top':0, left: '100%'});
						$next_carousel_group.animate({
							left: '0%'
						}, {
							duration: slide_duration,
							complete: function(){
								setTimeout(function(){
									$next_carousel_group.removeClass('next').addClass('active').css({'position':'', 'width':'', 'top':'', 'left': ''});

									$next_carousel_group.find('.delayed_container_append_dup').remove();

									$next_carousel_group.find('.changing_position').each(function( index ){
										position = $(this).data('position');
										current_position = $(this).data('current_position');
										next_position = $(this).data('next_position');
										$(this).removeClass('container_append delayed_container_append position_' + position + ' ' + 'changing_position current_position current_position_' + current_position + ' next_position next_position_' + next_position );
										$(this).data('current_position', '');
										$(this).data('next_position', '');
										$(this).data('position', ( index + 1 ) );
									});

									$next_carousel_group.children().css({'position': '', 'width': original_item_width, 'left': ''});

									$the_portfolio.data('carouseling', false);
								}, 100 );
							}
						} );

					} else {
						var $prev_carousel_group,
							current_position = columns,
							prev_position = columns,
							columns_span = columns - 1,
							active_items_start = items.indexOf( $active_carousel_group.children().last()[0] ),
							active_items_end = active_items_start - columns_span,
							prev_items_start = active_items_end - 1,
							prev_items_end = prev_items_start - columns_span;

						$prev_carousel_group = $('<div class="tm_pb_carousel_group prev" style="display: none;left: 100%;position: absolute;top: 0;">').insertBefore( $active_carousel_group );
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

						sorted = $portfolio_items.find('.container_append, .delayed_container_append_dup').sort(function (a, b) {
							var el_a_position = parseInt( $(a).data('prev_position') );
							var el_b_position = parseInt( $(b).data('prev_position') );
							return ( el_a_position < el_b_position ) ? -1 : ( el_a_position > el_b_position ) ? 1 : 0;
						});

						$( sorted ).show().appendTo( $prev_carousel_group );

						$prev_carousel_group.children().each(function(){
							$(this).css({'width': item_width, 'position':'absolute', 'left': ( item_width * ( $(this).data('prev_position') - 1 ) ) });
						});

						$active_carousel_group.animate({
							left: '100%'
						}, {
							duration: slide_duration,
							complete: function() {
								$portfolio_items.find('.delayed_container_append').reverse().each(function(){
									$(this).css({'width': item_width, 'position':'absolute', 'left': ( item_width * ( $(this).data('prev_position') - 1 ) ) });
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
									$(this).css({'position': '', 'width': '', 'left': ''});
									$(this).appendTo( $portfolio_items );
								});

								$active_carousel_group.remove();
							}
						} );

						$prev_carousel_group.addClass('active').css({'position':'absolute', 'top':0, left: '-100%'});
						$prev_carousel_group.animate({
							left: '0%'
						}, {
							duration: slide_duration,
							complete: function(){
								setTimeout(function(){
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

									$prev_carousel_group.children().css({'position': '', 'width': original_item_width, 'left': ''});
									$the_portfolio.data('carouseling', false);
								}, 100 );
							}
						} );
					}

					return false;
				});

			} else {
				// setup fullwidth portfolio grid
				set_fullwidth_portfolio_columns( $the_portfolio, false );
			}

		});
	}

	function tm_audio_module_set() {
		if ( $( '.tm_pb_audio_module .mejs-audio' ).length || $( '.tm_audio_content .mejs-audio' ).length ) {
			$( '.tm_audio_container' ).each( function(){
				var $this_player = $( this ),
					$time_rail = $this_player.find( '.mejs-time-rail' ),
					$time_slider = $this_player.find( '.mejs-time-slider' );
				// remove previously added width and min-width attributes to calculate the new sizes accurately
				$time_rail.removeAttr( 'style' );
				$time_slider.removeAttr( 'style' );

				var $count_timer = $this_player.find( 'div.mejs-currenttime-container' ),
					player_width = $this_player.width(),
					controls_play_width = $this_player.find( '.mejs-play' ).outerWidth(),
					time_width = $this_player.find( '.mejs-currenttime-container' ).outerWidth(),
					volume_icon_width = $this_player.find( '.mejs-volume-button' ).outerWidth(),
					volume_bar_width = $this_player.find( '.mejs-horizontal-volume-slider' ).outerWidth(),
					new_time_rail_width;

				$count_timer.addClass( 'custom' );
				$this_player.find( '.mejs-controls div.mejs-duration-container' ).replaceWith( $count_timer );
				new_time_rail_width = player_width - ( controls_play_width + time_width + volume_icon_width + volume_bar_width + 100 );

				if ( 0 < new_time_rail_width ) {
					$time_rail.attr( 'style', 'min-width: ' + new_time_rail_width + 'px;' );
					$time_slider.attr( 'style', 'min-width: ' + new_time_rail_width + 'px;' );
				}
			});
		}
	}

	if ( $('.tm_pb_section_video').length ) {
		window._wpmejsSettings.pauseOtherPlayers = false;
	}

	if ( $tm_pb_filterable_portfolio.length ) {

		$(window).load(function(){

			$tm_pb_filterable_portfolio.each(function(){
				var $the_portfolio = $(this),
					$the_portfolio_items = $the_portfolio.find('.tm_pb_portfolio_items'),
					$left_orientatation = true == $the_portfolio.data( 'rtl' ) ? false : true;

				$the_portfolio.show();

				set_filterable_grid_items( $the_portfolio );

				$the_portfolio.on('click', '.tm_pb_portfolio_filter a', function(e){
					e.preventDefault();
					var category_slug = $(this).data('category-slug');
					$the_portfolio_items = $(this).parents('.tm_pb_filterable_portfolio').find('.tm_pb_portfolio_items');

					if ( 'all' == category_slug ) {
						$the_portfolio.find('.tm_pb_portfolio_filter a').removeClass('active');
						$the_portfolio.find('.tm_pb_portfolio_filter_all a').addClass('active');
						$the_portfolio.find('.tm_pb_portfolio_item').removeClass('active inactive');
						$the_portfolio.find('.tm_pb_portfolio_item').show();
						$the_portfolio.find('.tm_pb_portfolio_item').addClass('active');
					} else {
						$the_portfolio.find('.tm_pb_portfolio_filter_all').removeClass('active');
						$the_portfolio.find('.tm_pb_portfolio_filter a').removeClass('active');
						$the_portfolio.find('.tm_pb_portfolio_filter_all a').removeClass('active');
						$(this).addClass('active');

						$the_portfolio_items.find('.tm_pb_portfolio_item').hide();
						$the_portfolio_items.find('.tm_pb_portfolio_item').addClass( 'inactive' );
						$the_portfolio_items.find('.tm_pb_portfolio_item').removeClass('active');
						$the_portfolio_items.find('.tm_pb_portfolio_item.project_category_' + $(this).data('category-slug') ).show();
						$the_portfolio_items.find('.tm_pb_portfolio_item.project_category_' + $(this).data('category-slug') ).addClass('active').removeClass( 'inactive' );
					}

					set_filterable_grid_items( $the_portfolio );
					setTimeout(function(){
						set_filterable_portfolio_hash( $the_portfolio );
					}, 500 );
				});

				$(this).on('tm_hashchange', function( event ){
					var params = event.params;
					$the_portfolio = $( '#' + event.target.id );

					if ( !$the_portfolio.find('.tm_pb_portfolio_filter a[data-category-slug="' + params[0] + '"]').hasClass('active') ) {
						$the_portfolio.find('.tm_pb_portfolio_filter a[data-category-slug="' + params[0] + '"]').click();
					}

					if ( params[1] ) {
						setTimeout(function(){
							if ( !$the_portfolio.find('.tm_pb_portofolio_pagination a.page-' + params[1]).hasClass('active') ) {
								$the_portfolio.find('.tm_pb_portofolio_pagination a.page-' + params[1]).addClass('active').click();
							}
						}, 300 );
					}
				});
			});

		}); // End $(window).load()

		function set_filterable_grid_items( $the_portfolio ) {
			var active_category = $the_portfolio.find('.tm_pb_portfolio_filter > a.active').data('category-slug'),
				container_width = $the_portfolio.find( '.tm_pb_portfolio_items' ).innerWidth(),
				item_width = $the_portfolio.find( '.tm_pb_portfolio_item' ).outerWidth( true ),
				last_item_margin = item_width - $the_portfolio.find( '.tm_pb_portfolio_item' ).outerWidth(),
				columns_count = Math.round( ( container_width + last_item_margin ) / item_width ),
				counter = 1,
				first_in_row = 1;

				$the_portfolio.find( '.tm_pb_portfolio_item' ).removeClass( 'last_in_row first_in_row' );
				$the_portfolio.find( '.tm_pb_portfolio_item' ).each( function() {
					var $this_el = $( this );

					if ( ! $this_el.hasClass( 'inactive' ) ) {
						if ( first_in_row === counter ) {
							$this_el.addClass( 'first_in_row' );
						}

						if ( 0 === counter % columns_count ) {
							$this_el.addClass( 'last_in_row' );
							first_in_row = counter + 1;
						}
						counter++;
					}
				});

			if ( 'all' === active_category ) {
				$the_portfolio_visible_items = $the_portfolio.find('.tm_pb_portfolio_item');
			} else {
				$the_portfolio_visible_items = $the_portfolio.find('.tm_pb_portfolio_item.project_category_' + active_category);
			}

			var visible_grid_items = $the_portfolio_visible_items.length,
				posts_number = $the_portfolio.data('posts-number'),
				pages = Math.ceil( visible_grid_items / posts_number );

			set_filterable_grid_pages( $the_portfolio, pages );

			var visible_grid_items = 0;
			var _page = 1;
			$the_portfolio.find('.tm_pb_portfolio_item').data('page', '');
			$the_portfolio_visible_items.each(function(i){
				visible_grid_items++;
				if ( 0 === parseInt( visible_grid_items % posts_number ) ) {
					$(this).data('page', _page);
					_page++;
				} else {
					$(this).data('page', _page);
				}
			});

			$the_portfolio_visible_items.filter(function() {
				return $(this).data('page') == 1;
			}).show();

			$the_portfolio_visible_items.filter(function() {
				return $(this).data('page') != 1;
			}).hide();
		}

		function set_filterable_grid_pages( $the_portfolio, pages ) {
			$pagination = $the_portfolio.find('.tm_pb_portofolio_pagination');

			if ( !$pagination.length ) {
				return;
			}

			$pagination.html('<ul></ul>');
			if ( pages <= 1 ) {
				return;
			}

			$pagination_list = $pagination.children('ul');
			$pagination_list.append('<li class="prev" style="display:none;"><a href="#" data-page="prev" class="page-prev">' + tm_pb_custom.prev + '</a></li>');
			for( var page = 1; page <= pages; page++ ) {
				var first_page_class = page === 1 ? ' active' : '',
					last_page_class = page === pages ? ' last-page' : '',
					hidden_page_class = page >= 5 ? ' style="display:none;"' : '';
				$pagination_list.append('<li' + hidden_page_class + ' class="page page-' + page + '"><a href="#" data-page="' + page + '" class="page-' + page + first_page_class + last_page_class + '">' + page + '</a></li>');
			}
			$pagination_list.append('<li class="next"><a href="#" data-page="next" class="page-next">' + tm_pb_custom.next + '</a></li>');
		}

		$tm_pb_filterable_portfolio.on('click', '.tm_pb_portofolio_pagination a', function(e){
			e.preventDefault();

			var to_page = $(this).data('page'),
				$the_portfolio = $(this).parents('.tm_pb_filterable_portfolio'),
				$the_portfolio_items = $the_portfolio.find('.tm_pb_portfolio_items');

			tm_pb_smooth_scroll( $the_portfolio, false, 800 );

			if ( $(this).hasClass('page-prev') ) {
				to_page = parseInt( $(this).parents('ul').find('a.active').data('page') ) - 1;
			} else if ( $(this).hasClass('page-next') ) {
				to_page = parseInt( $(this).parents('ul').find('a.active').data('page') ) + 1;
			}

			$(this).parents('ul').find('a').removeClass('active');
			$(this).parents('ul').find('a.page-' + to_page ).addClass('active');

			var current_index = $(this).parents('ul').find('a.page-' + to_page ).parent().index(),
				total_pages = $(this).parents('ul').find('li.page').length;

			$(this).parent().nextUntil('.page-' + ( current_index + 3 ) ).show();
			$(this).parent().prevUntil('.page-' + ( current_index - 3 ) ).show();

			$(this).parents('ul').find('li.page').each(function(i){
				if ( !$(this).hasClass('prev') && !$(this).hasClass('next') ) {
					if ( i < ( current_index - 3 ) ) {
						$(this).hide();
					} else if ( i > ( current_index + 1 ) ) {
						$(this).hide();
					} else {
						$(this).show();
					}

					if ( total_pages - current_index <= 2 && total_pages - i <= 5 ) {
						$(this).show();
					} else if ( current_index <= 3 && i <= 4 ) {
						$(this).show();
					}

				}
			});

			if ( to_page > 1 ) {
				$(this).parents('ul').find('li.prev').show();
			} else {
				$(this).parents('ul').find('li.prev').hide();
			}

			if ( $(this).parents('ul').find('a.active').hasClass('last-page') ) {
				$(this).parents('ul').find('li.next').hide();
			} else {
				$(this).parents('ul').find('li.next').show();
			}

			$the_portfolio.find('.tm_pb_portfolio_item').hide();
			$the_portfolio.find('.tm_pb_portfolio_item').filter(function( index ) {
				return $(this).data('page') === to_page;
			}).show();

			setTimeout(function(){
				set_filterable_portfolio_hash( $the_portfolio );
			}, 500 );
		});

		function set_filterable_portfolio_hash( $the_portfolio ) {

			if ( !$the_portfolio.attr('id') ) {
				return;
			}

			var this_portfolio_state = [];
			this_portfolio_state.push( $the_portfolio.attr('id') );
			this_portfolio_state.push( $the_portfolio.find('.tm_pb_portfolio_filter > a.active').data('category-slug') );

			if ( $the_portfolio.find('.tm_pb_portofolio_pagination a.active').length ) {
				this_portfolio_state.push( $the_portfolio.find('.tm_pb_portofolio_pagination a.active').data('page') );
			} else {
				this_portfolio_state.push( 1 );
			}

			this_portfolio_state = this_portfolio_state.join( tm_hash_module_param_seperator );

			tm_set_hash( this_portfolio_state );
		}
	} /*  end if ( $tm_pb_filterable_portfolio.length ) */

	if ( $tm_pb_gallery.length ) {

		function set_gallery_grid_items( $the_gallery ) {
			var $the_gallery_items_container = $the_gallery.find('.tm_pb_gallery_items'),
				$the_gallery_items = $the_gallery_items_container.find('.tm_pb_gallery_item');

			var total_grid_items = $the_gallery_items.length,
				posts_number = $the_gallery_items_container.data('per_page'),
				pages = Math.ceil( total_grid_items / posts_number );

			set_gallery_grid_pages( $the_gallery, pages );

			var total_grid_items = 0;
			var _page = 1;
			$the_gallery_items.data('page', '');
			$the_gallery_items.each(function(i){
				total_grid_items++;
				if ( 0 === parseInt( total_grid_items % posts_number ) ) {
					$(this).data('page', _page);
					_page++;
				} else {
					$(this).data('page', _page);
				}

			});

			var visible_items = $the_gallery_items.filter(function() {
				return $(this).data('page') == 1;
			}).show();

			$the_gallery_items.filter(function() {
				return $(this).data('page') != 1;
			}).hide();
		}

		function set_gallery_grid_pages( $the_gallery, pages ) {
			$pagination = $the_gallery.find('.tm_pb_gallery_pagination');

			if ( !$pagination.length ) {
				return;
			}

			$pagination.html('<ul></ul>');
			if ( pages <= 1 ) {
				$pagination.hide();
				return;
			}

			$pagination_list = $pagination.children('ul');
			$pagination_list.append('<li class="prev" style="display:none;"><a href="#" data-page="prev" class="page-prev">' + tm_pb_custom.prev + '</a></li>');
			for( var page = 1; page <= pages; page++ ) {
				var first_page_class = page === 1 ? ' active' : '',
					last_page_class = page === pages ? ' last-page' : '',
					hidden_page_class = page >= 5 ? ' style="display:none;"' : '';
				$pagination_list.append('<li' + hidden_page_class + ' class="page page-' + page + '"><a href="#" data-page="' + page + '" class="page-' + page + first_page_class + last_page_class + '">' + page + '</a></li>');
			}
			$pagination_list.append('<li class="next"><a href="#" data-page="next" class="page-next">' + tm_pb_custom.next + '</a></li>');
		}

		function set_gallery_hash( $the_gallery ) {

			if ( !$the_gallery.attr('id') ) {
				return;
			}

			var this_gallery_state = [];
			this_gallery_state.push( $the_gallery.attr('id') );

			if ( $the_gallery.find('.tm_pb_gallery_pagination a.active').length ) {
				this_gallery_state.push( $the_gallery.find('.tm_pb_gallery_pagination a.active').data('page') );
			} else {
				this_gallery_state.push( 1 );
			}

			this_gallery_state = this_gallery_state.join( tm_hash_module_param_seperator );

			tm_set_hash( this_gallery_state );
		}

		$tm_pb_gallery.each(function(){
			var $the_gallery = $(this);

			if ( $the_gallery.hasClass( 'tm_pb_gallery_grid' ) ) {

				$the_gallery.show();
				set_gallery_grid_items( $the_gallery );

				$the_gallery.on('tm_hashchange', function( event ){
					var params = event.params;
					$the_gallery = $( '#' + event.target.id );

					if ( page_to = params[0] ) {
						if ( !$the_gallery.find('.tm_pb_gallery_pagination a.page-' + page_to ).hasClass('active') ) {
							$the_gallery.find('.tm_pb_gallery_pagination a.page-' + page_to ).addClass('active').click();
						}
					}
				});
			}

		});

		$tm_pb_gallery.data('paginating', false );
		$tm_pb_gallery.on('click', '.tm_pb_gallery_pagination a', function(e){
			e.preventDefault();

			var to_page = $(this).data('page'),
				$the_gallery = $(this).parents('.tm_pb_gallery'),
				$the_gallery_items_container = $the_gallery.find('.tm_pb_gallery_items'),
				$the_gallery_items = $the_gallery_items_container.find('.tm_pb_gallery_item');

			if ( $the_gallery.data('paginating') ) {
				return;
			}

			$the_gallery.data('paginating', true );

			if ( $(this).hasClass('page-prev') ) {
				to_page = parseInt( $(this).parents('ul').find('a.active').data('page') ) - 1;
			} else if ( $(this).hasClass('page-next') ) {
				to_page = parseInt( $(this).parents('ul').find('a.active').data('page') ) + 1;
			}

			$(this).parents('ul').find('a').removeClass('active');
			$(this).parents('ul').find('a.page-' + to_page ).addClass('active');

			var current_index = $(this).parents('ul').find('a.page-' + to_page ).parent().index(),
				total_pages = $(this).parents('ul').find('li.page').length;

			$(this).parent().nextUntil('.page-' + ( current_index + 3 ) ).show();
			$(this).parent().prevUntil('.page-' + ( current_index - 3 ) ).show();

			$(this).parents('ul').find('li.page').each(function(i){
				if ( !$(this).hasClass('prev') && !$(this).hasClass('next') ) {
					if ( i < ( current_index - 3 ) ) {
						$(this).hide();
					} else if ( i > ( current_index + 1 ) ) {
						$(this).hide();
					} else {
						$(this).show();
					}

					if ( total_pages - current_index <= 2 && total_pages - i <= 5 ) {
						$(this).show();
					} else if ( current_index <= 3 && i <= 4 ) {
						$(this).show();
					}

				}
			});

			if ( to_page > 1 ) {
				$(this).parents('ul').find('li.prev').show();
			} else {
				$(this).parents('ul').find('li.prev').hide();
			}

			if ( $(this).parents('ul').find('a.active').hasClass('last-page') ) {
				$(this).parents('ul').find('li.next').hide();
			} else {
				$(this).parents('ul').find('li.next').show();
			}

			$the_gallery_items.hide();
			var visible_items = $the_gallery_items.filter(function( index ) {
				return $(this).data('page') === to_page;
			}).show();

			$the_gallery.data('paginating', false );

			setTimeout(function(){
				set_gallery_hash( $the_gallery );
			}, 100 );

			$( 'html, body' ).animate( { scrollTop : $the_gallery.offset().top - 200 }, 200 );
		});

	} /*  end if ( $tm_pb_gallery.length ) */

	if ( $tm_pb_counter_amount.length ) {
		$tm_pb_counter_amount.each(function(){
			var $bar_item           = $(this),
				bar_item_width      = $bar_item.attr( 'data-width' ),
				bar_item_padding    = Math.ceil( parseFloat( $bar_item.css('paddingLeft') ) ) + Math.ceil( parseFloat( $bar_item.css('paddingRight') ) ),
				$bar_item_text      = $bar_item.children( '.tm_pb_counter_amount_number' ),
				bar_item_text_width = $bar_item_text.width() + bar_item_padding;

			$bar_item.css({
				'width' : bar_item_width,
				'min-width' : bar_item_text_width
			});
		});
	} /* $tm_pb_counter_amount.length */

	function tm_countdown_timer( timer ) {
		var end_date = parseInt( timer.data( 'end-timestamp') ),
			current_date = new Date().getTime() / 1000,
			seconds_left = ( end_date - current_date );

		days = parseInt(seconds_left / 86400);
		days = days > 0 ? days : 0;
		seconds_left = seconds_left % 86400;

		hours = parseInt(seconds_left / 3600);
		hours = hours > 0 ? hours : 0;

		seconds_left = seconds_left % 3600;

		minutes = parseInt(seconds_left / 60);
		minutes = minutes > 0 ? minutes : 0;

		seconds = parseInt(seconds_left % 60);
		seconds = seconds > 0 ? seconds : 0;

		if ( days == 0 ) {
			if ( !timer.find('.days > .value').parent('.section').hasClass('zero') ) {
				timer.find('.days > .value').html( '00' ).parent('.section').addClass('zero').next().addClass('zero');
			}
		} else {
			days_slice = days.toString().length >= 2 ? days.toString().length : 2;
			timer.find('.days > .value').html( ('00' + days).slice(-days_slice) );
		}

		if ( days == 0 && hours == 0 ) {
			if ( !timer.find('.hours > .value').parent('.section').hasClass('zero') ) {
				timer.find('.hours > .value').html('00').parent('.section').addClass('zero').next().addClass('zero');
			}
		} else {
			timer.find('.hours > .value').html( ( '0' + hours ).slice(-2) );
		}

		if ( days == 0 && hours == 0 && minutes == 0 ) {
			if ( !timer.find('.minutes > .value').parent('.section').hasClass('zero') ) {
				timer.find('.minutes > .value').html('00').parent('.section').addClass('zero').next().addClass('zero');
			}
		} else {
			timer.find('.minutes > .value').html( ( '0' + minutes ).slice(-2) );
		}

		if ( days == 0 && hours == 0 && minutes == 0 && seconds == 0 ) {
			if ( !timer.find('.seconds > .value').parent('.section').hasClass('zero') ) {
				timer.find('.seconds > .value').html('00').parent('.section').addClass('zero');
			}
		} else {
			timer.find('.seconds > .value').html( ( '0' + seconds ).slice(-2) );
		}
	}

	function tm_countdown_timer_labels( timer ) {
		if ( timer.closest( '.tm_pb_column_3_8' ).length || timer.closest( '.tm_pb_column_1_4' ).length || timer.children('.tm_pb_countdown_timer_container').width() <= 400 ) {
			timer.find('.days .label').html( timer.find('.days').data('short') );
			timer.find('.hours .label').html( timer.find('.hours').data('short') );
			timer.find('.minutes .label').html( timer.find('.minutes').data('short') );
			timer.find('.seconds .label').html( timer.find('.seconds').data('short') );
		} else {
			timer.find('.days .label').html( timer.find('.days').data('full') );
			timer.find('.hours .label').html( timer.find('.hours').data('full') );
			timer.find('.minutes .label').html( timer.find('.minutes').data('full') );
			timer.find('.seconds .label').html( timer.find('.seconds').data('full') );
		}
	}

	if ( $tm_pb_countdown_timer.length ) {
		$tm_pb_countdown_timer.each(function(){
			var timer = $(this);
			tm_countdown_timer_labels( timer );
			tm_countdown_timer( timer );
			setInterval(function(){
				tm_countdown_timer( timer );
			}, 1000);
		});
	}

	if ( $tm_pb_tabs.length ) {
		$tm_pb_tabs.tm_pb_simple_slider( {
			use_controls   : false,
			use_arrows     : false,
			slide          : '.tm_pb_all_tabs > div',
			tabs_animation : true
		} ).on('tm_hashchange', function( event ){
			var params = event.params;
			var $the_tabs = $( '#' + event.target.id );
			var active_tab = params[0];
			if ( !$the_tabs.find( '.tm_pb_tabs_controls li' ).eq( active_tab ).hasClass('tm_pb_tab_active') ) {
				$the_tabs.find( '.tm_pb_tabs_controls li' ).eq( active_tab ).click();
			}
		});

		$tm_pb_tabs_li.click( function() {
			var $this_el        = $(this),
				$tabs_container = $this_el.closest( '.tm_pb_tabs' ).data('tm_pb_simple_slider');

			if ( $tabs_container.tm_animation_running ) return false;

			$this_el.addClass( 'tm_pb_tab_active' ).siblings().removeClass( 'tm_pb_tab_active' );

			$tabs_container.data('tm_pb_simple_slider').tm_slider_move_to( $this_el.index() );

			if ( $this_el.closest( '.tm_pb_tabs' ).attr('id') ) {
				var tab_state = [];
				tab_state.push( $this_el.closest( '.tm_pb_tabs' ).attr('id') );
				tab_state.push( $this_el.index() );
				tab_state = tab_state.join( tm_hash_module_param_seperator );
				tm_set_hash( tab_state );
			}

			return false;
		} );
	}

	if ( $tm_pb_map.length ) {
		google.maps.event.addDomListener(window, 'load', function() {
			$tm_pb_map.each(function(){
				var $this_map_container = $(this),
					$this_map = $this_map_container.children('.tm_pb_map'),
					this_map_grayscale = $this_map_container.data( 'grayscale' ) || 0,
					infowindow_active,
					marker_icon = $this_map.data( 'marker-icon' ) || [ tm_pb_custom.builder_images_uri + '/marker.png', 34, 53, false ],
					map_style = $this_map.data( 'map-style' );

					if ( this_map_grayscale !== 0 ) {
						this_map_grayscale = '-' + this_map_grayscale.toString();
					}

					$this_map_container.data('map', new google.maps.Map( $this_map[0], {
						zoom: parseInt( $this_map.data('zoom') ),
						center: new google.maps.LatLng( parseFloat( $this_map.data('center-lat') ) , parseFloat( $this_map.data('center-lng') )),
						mapTypeId: google.maps.MapTypeId.ROADMAP,
						scrollwheel: $this_map.data('mouse-wheel') == 'on' ? true : false,
						panControlOptions: {
							position: $this_map_container.is( '.tm_beneath_transparent_nav' ) ? google.maps.ControlPosition.LEFT_BOTTOM : google.maps.ControlPosition.LEFT_TOP
						},
						zoomControlOptions: {
							position: $this_map_container.is( '.tm_beneath_transparent_nav' ) ? google.maps.ControlPosition.LEFT_BOTTOM : google.maps.ControlPosition.LEFT_TOP
						},
						/*styles: [ {
							stylers: [
								{ saturation: parseInt( this_map_grayscale ) }
							]
						} ],*/
						styles: map_style
					}));

					$this_map_container.data('bounds', new google.maps.LatLngBounds() );
					$this_map_container.find('.tm_pb_map_pin').each(function(){

						var $this_marker = $(this),
							position = new google.maps.LatLng( parseFloat( $this_marker.data('lat') ) , parseFloat( $this_marker.data('lng') ) );

						$this_map_container.data('bounds').extend( position );

						var marker = new google.maps.Marker({
							position: position,
							map: $this_map_container.data('map'),
							title: $this_marker.data('title'),
							icon: {
								url: marker_icon[0],
								size: new google.maps.Size( marker_icon[1], marker_icon[2] ),
								anchor: new google.maps.Point( marker_icon[1] / 2, marker_icon[2] )
							},
							//shape: { coord: [1, 1, marker_icon[1], marker_icon[2] ], type: 'rect' },
						 //anchorPoint: new google.maps.Point(0, -45),
							animation: google.maps.Animation.DROP
						});

						if ( $this_marker.find('.infowindow').length ) {
							var infowindow = new google.maps.InfoWindow({
								content: $this_marker.html()
							});

							google.maps.event.addListener( $this_map_container.data('map'), 'click', function() {
								infowindow.close();
							});

							google.maps.event.addListener(marker, 'click', function() {
								if( infowindow_active ) {
									infowindow_active.close();
								}
								infowindow_active = infowindow;

								infowindow.open( $this_map_container.data('map'), marker );
							});
						}
					});

					google.maps.event.addListener( $this_map_container.data('map'), 'bounds_changed', function() {

						if ( !$this_map_container.data('map').getBounds().contains( $this_map_container.data('bounds').getNorthEast() ) || !$this_map_container.data('map').getBounds().contains( $this_map_container.data('bounds').getSouthWest() ) ) {
							$this_map_container.data('map').fitBounds( $this_map_container.data('bounds') );
						}

					});
			});
		} );
	}

	if ( $tm_pb_shop.length ) {
		$tm_pb_shop.each( function() {
			var $this_el = $(this),
				icon     = $this_el.data('icon') || '';

			if ( icon === '' ) {
				return true;
			}

			$this_el.find( '.tm_overlay' )
				.attr( 'data-icon', icon )
				.addClass( 'tm_pb_inline_icon' );
		} );
	}

	if ( $tm_pb_circle_counter.length ) {

		window.tm_pb_circle_counter_init = function($the_counter, animate) {
			if ( 0 === $the_counter.width() ) {
				return;
			}

			$the_counter.easyPieChart({
				animate: {
					duration: 1800,
					enabled: true
				},
				size: 0 !== $the_counter.width() ? $the_counter.width() : 10, // set the width to 10 if actual width is 0 to avoid js errors
				barColor: $the_counter.data( 'bar-bg-color' ),
				trackColor: $the_counter.data( 'color' ) || '#000000',
				trackAlpha: $the_counter.data( 'alpha' ) || '0.1',
				lineWidth: $the_counter.data( 'circle-width' ) || '5',
				size: $the_counter.data( 'circle-size' ) || '110',
				scaleColor: false,
				lineCap: $the_counter.data( 'bar-type' ) || 'round',
				onStart: function() {
					$(this.el).find('.percent p').css({ 'visibility' : 'visible' });
				},
				onStep: function(from, to, percent) {
					$(this.el).find('.percent-value').text( Math.round( parseInt( percent ) ) );
				},
				onStop: function(from, to) {
					$(this.el).find('.percent-value').text( $(this.el).data('number-value') );
				}
			});
		}

		window.tm_pb_reinit_circle_counters = function( $tm_pb_circle_counter ) {
			$tm_pb_circle_counter.each(function(){
				var $the_counter = $(this);
				window.tm_pb_circle_counter_init($the_counter, false);

				$the_counter.on('containerWidthChanged', function( event ){
					$the_counter = $( event.target );
					$the_counter.find('canvas').remove();
					$the_counter.removeData('easyPieChart' );
					window.tm_pb_circle_counter_init($the_counter, true);
				});

			});
		}
		window.tm_pb_reinit_circle_counters( $tm_pb_circle_counter );
	}

	if ( $tm_pb_number_counter.length ) {
		window.tm_pb_reinit_number_counters = function( $tm_pb_number_counter ) {

			if ( $.fn.fitText ) {
				$tm_pb_number_counter.find( '.percent' ).fitText( 0.3 );
			}

			$tm_pb_number_counter.each(function(){
				var $this_counter = $(this);
				$this_counter.easyPieChart({
					animate: {
						duration: 1800,
						enabled: true
					},
					size: 0,
					trackColor: false,
					scaleColor: false,
					lineWidth: 0,
					onStart: function() {
						$(this.el).find('.percent').css({ 'visibility' : 'visible' });
					},
					onStep: function(from, to, percent) {
						if ( percent != to )
							$(this.el).find('.percent-value').text( Math.round( parseInt( percent ) ) );
					},
					onStop: function(from, to) {
						$(this.el).find('.percent-value').text( $(this.el).data('number-value') );
					}
				});
			});
		}
		window.tm_pb_reinit_number_counters( $tm_pb_number_counter );
	}

	function tm_apply_parallax() {
		var $this = $(this),
			element_top = $this.offset().top,
			window_top = $tm_window.scrollTop(),
			y_pos = ( ( ( window_top + $tm_window.height() ) - element_top ) * 0.3 ),
			main_position;

		main_position = 'translate(0, ' + y_pos + 'px)';

		$this.find('.tm_parallax_bg').css( {
			'-webkit-transform' : main_position,
			'-moz-transform'    : main_position,
			'-ms-transform'     : main_position,
			'transform'         : main_position
		} );
	}

	function tm_parallax_set_height() {
		var $this = $(this),
			bg_height;

		bg_height = ( $tm_window.height() * 0.3 + $this.innerHeight() );

		$this.find('.tm_parallax_bg').css( { 'height' : bg_height } );
	}

	$('.tm_pb_toggle_title').click( function(){
		var $this_heading         = $(this),
			$module               = $this_heading.closest('.tm_pb_toggle'),
			$section              = $module.parents( '.tm_pb_section' ),
			$content              = $module.find('.tm_pb_toggle_content'),
			$accordion            = $module.closest( '.tm_pb_accordion' ),
			is_accordion          = $accordion.length,
			is_accordion_toggling = $accordion.hasClass( 'tm_pb_accordion_toggling' ),
			$accordion_active_toggle;

		if ( is_accordion ) {
			if ( $module.hasClass('tm_pb_toggle_open') || is_accordion_toggling ) {
				return false;
			}

			$accordion.addClass( 'tm_pb_accordion_toggling' );
			$accordion_active_toggle = $module.siblings('.tm_pb_toggle_open');
		}

		if ( $content.is( ':animated' ) ) {
			return;
		}

		$content.slideToggle( 700, function() {
			if ( $module.hasClass('tm_pb_toggle_close') ) {
				$module.removeClass('tm_pb_toggle_close').addClass('tm_pb_toggle_open');
			} else {
				$module.removeClass('tm_pb_toggle_open').addClass('tm_pb_toggle_close');
			}

			if ( $section.hasClass( 'tm_pb_section_parallax' ) && !$section.children().hasClass( 'tm_pb_parallax_css') ) {
				$.proxy( tm_parallax_set_height, $section )();
			}
		} );

		if ( is_accordion ) {
			$accordion_active_toggle.find('.tm_pb_toggle_content').slideToggle( 700, function() {
				$accordion_active_toggle.removeClass( 'tm_pb_toggle_open' ).addClass('tm_pb_toggle_close');
				$accordion.removeClass( 'tm_pb_accordion_toggling' );
			} );
		}
	} );

	var $tm_contact_container = $( '.tm_pb_contact_form_container' );

	if ( $tm_contact_container.length ) {
		$tm_contact_container.each( function() {
			var $this_contact_container = $( this ),
				$tm_contact_form = $this_contact_container.find( 'form' ),
				$tm_contact_submit = $this_contact_container.find( 'input.tm_pb_contact_submit' ),
				$tm_inputs = $tm_contact_form.find( 'input[type=text],textarea' ),
				tm_email_reg = /^[\w-]+(\.[\w-]+)*@([a-z0-9-]+(\.[a-z0-9-]+)*?\.[a-z]{2,6}|(\d{1,3}\.){3}\d{1,3})(:\d{4})?$/,
				redirect_url = typeof $this_contact_container.data( 'redirect_url' ) !== 'undefined' ? $this_contact_container.data( 'redirect_url' ) : '';

			$tm_contact_form.on( 'submit', function( event ) {
				var $this_contact_form = $( this ),
					$this_inputs = $this_contact_form.find( '.tm_pb_contact_form_input, .tm_pb_contact_captcha' ),
					this_tm_contact_error = false,
					$tm_contact_message = $this_contact_form.closest( '.tm_pb_contact_form_container' ).find( '.tm-pb-contact-message' ),
					tm_message = '',
					tm_fields_message = '',
					$this_contact_container = $this_contact_form.closest( '.tm_pb_contact_form_container' ),
					$captcha_field = $this_contact_form.find( '.tm_pb_contact_captcha' ),
					form_unique_id = typeof $this_contact_container.data( 'form_unique_num' ) !== 'undefined' ? $this_contact_container.data( 'form_unique_num' ) : 0,
					inputs_list = [];
				tm_message = '<ul>';

				$this_inputs.removeClass( 'tm_contact_error' );

				$this_inputs.each( function(){
					var $this_el = $( this ),
						this_val = $this_el.val(),
						this_label = $this_el.siblings( 'label' ).text(),
						field_type = typeof $this_el.data( 'field_type' ) !== 'undefined' ? $this_el.data( 'field_type' ) : 'text',
						required_mark = typeof $this_el.data( 'required_mark' ) !== 'undefined' ? $this_el.data( 'required_mark' ) : 'not_required',
						original_id = typeof $this_el.data( 'original_id' ) !== 'undefined' ? $this_el.data( 'original_id' ) : '',
						field_name;

					// add current field data into array of inputs
					if ( typeof $this_el.attr( 'id' ) !== 'undefined' ) {
						inputs_list.push( { 'field_id' : $this_el.attr( 'id' ), 'original_id' : original_id, 'required_mark' : required_mark, 'field_type' : field_type, 'field_label' : this_label } );
					}

					// add error message for the field if it is required and empty
					if ( 'required' === required_mark && ( '' === this_val || this_label === this_val || null === this_val ) ) {
						$this_el.addClass( 'tm_contact_error' );
						this_tm_contact_error = true;

						field_name = $this_el.data( 'original_title' );

						if ( ! field_name ) {
							field_name = $this_el.attr( 'name' );
						}

						tm_fields_message += '<li>' + field_name + '</li>';
					}

					// add error message if email field is not empty and fails the email validation
					if ( 'email' === field_type && '' !== this_val && this_label !== this_val && ! tm_email_reg.test( this_val ) ) {
						$this_el.addClass( 'tm_contact_error' );
						this_tm_contact_error = true;

						if ( ! tm_email_reg.test( this_val ) ) {
							tm_message += '<li>' + tm_pb_custom.invalid + '</li>';
						}
					}
				});

				// check the captcha value if required for current form
				if ( $captcha_field.length && '' !== $captcha_field.val() ) {
					var first_digit = parseInt( $captcha_field.data( 'first_digit' ) ),
						second_digit = parseInt( $captcha_field.data( 'second_digit' ) );

					if ( parseInt( $captcha_field.val() ) !== first_digit + second_digit ) {

						tm_message += '<li>' + tm_pb_custom.wrong_captcha + '</li>';
						this_tm_contact_error = true;

						// generate new digits for captcha
						first_digit = Math.floor( ( Math.random() * 15 ) + 1 );
						second_digit = Math.floor( ( Math.random() * 15 ) + 1 );

						// set new digits for captcha
						$captcha_field.data( 'first_digit', first_digit );
						$captcha_field.data( 'second_digit', second_digit );

						// regenerate captcha on page
						$this_contact_form.find( '.tm_pb_contact_captcha_question' ).empty().append( first_digit  + ' + ' + second_digit );
					}

				}

				if ( ! this_tm_contact_error ) {
					var $href = $( this ).attr( 'action' ),
						form_data = $( this ).serializeArray();

					form_data.push( { 'name': 'tm_pb_contact_email_fields_' + form_unique_id, 'value' : JSON.stringify( inputs_list ) } );

					$this_contact_container.fadeTo( 'fast', 0.2 ).load( $href + ' #' + $this_contact_form.closest( '.tm_pb_contact_form_container' ).attr( 'id' ), form_data, function( responseText ) {
						// redirect if redirect URL is not empty and no errors in contact form
						if ( '' !== redirect_url && ! $( responseText ).find( '.tm_pb_contact_error_text').length ) {
							window.location.href = redirect_url;
						}

						$this_contact_container.fadeTo( 'fast', 1 );
					} );
				}

				tm_message += '</ul>';

				if ( '' !== tm_fields_message ) {
					if ( tm_message != '<ul></ul>' ) {
						tm_message = '<p class="tm_normal_padding">' + tm_pb_custom.contact_error_message + '</p>' + tm_message;
					}

					tm_fields_message = '<ul>' + tm_fields_message + '</ul>';

					tm_fields_message = '<p>' + tm_pb_custom.fill_message + '</p>' + tm_fields_message;

					tm_message = tm_fields_message + tm_message;
				}

				if ( tm_message != '<ul></ul>' ) {
					$tm_contact_message.html( tm_message );
				}

				event.preventDefault();
			});
		});
	}

	$( '.tm_pb_video .tm_pb_video_overlay, .tm_pb_video_wrap .tm_pb_video_overlay' ).click( function() {
		var $this        = $(this),
			$video_image = $this.closest( '.tm_pb_video_overlay' );

		$video_image.fadeTo( 500, 0, function() {
			var $image = $(this);

			$image.css( 'display', 'none' );
		} );

		return false;
	} );

	function tm_pb_resize_section_video_bg( $video ) {
		$element = typeof $video !== 'undefined' ? $video.closest( '.tm_pb_section_video_bg' ) : $( '.tm_pb_section_video_bg' );

		$element.each( function() {
			var $this_el = $(this),
				ratio = ( typeof $this_el.attr( 'data-ratio' ) !== 'undefined' )
					? $this_el.attr( 'data-ratio' )
					: $this_el.find('video').attr( 'width' ) / $this_el.find('video').attr( 'height' ),
				$video_elements = $this_el.find( '.mejs-video, video, object' ).css( 'margin', 0 ),
				$container = $this_el.closest( '.tm_pb_section_video' ).length
					? $this_el.closest( '.tm_pb_section_video' )
					: $this_el.closest( '.tm_pb_slides' ),
				body_width = $container.width(),
				container_height = $container.innerHeight(),
				width, height;

			if ( typeof $this_el.attr( 'data-ratio' ) == 'undefined' )
				$this_el.attr( 'data-ratio', ratio );

			if ( body_width / container_height < ratio ) {
				width = container_height * ratio;
				height = container_height;
			} else {
				width = body_width;
				height = body_width / ratio;
			}

			$video_elements.width( width ).height( height );
		} );
	}

	function tm_pb_center_video( $video ) {
		$element = typeof $video !== 'undefined' ? $video : $( '.tm_pb_section_video_bg .mejs-video' );

		$element.each( function() {
			var $video_width = $(this).width() / 2;
			var $video_width_negative = 0 - $video_width;
			$(this).css("margin-left",$video_width_negative );

			if ( typeof $video !== 'undefined' ) {
				if ( $video.closest( '.tm_pb_slider' ).length && ! $video.closest( '.tm_pb_first_video' ).length )
					return false;
			}
		} );
	}

	function tm_fix_slider_height() {
		if ( ! $tm_pb_slider.length ) return;

		$tm_pb_slider.each( function() {
			var $slide_section = $(this).parent( '.tm_pb_section' ),
				$slide = $(this).find( '.tm_pb_slide' ),
				$slide_container = $slide.find( '.tm_pb_container' ),
				max_height = 0;

			// If this is appears at the first section benath transparent nav, skip it
			// leave it to tm_fix_page_container_position()
			if ( $slide_section.is( '.tm_pb_section_first' ) ){
				return true;
			}

			$slide_container.css( 'min-height', 0 );

			$slide.each( function() {
				var $this_el = $(this),
					height = $this_el.innerHeight();

				if ( max_height < height )
					max_height = height;
			} );

			$slide_container.css( 'min-height', max_height );
		} );
	}

	/**
	 * Add conditional class to prevent unwanted dropdown nav
	 */
	function tm_fix_nav_direction() {
		window_width = $(window).width();
		$('.nav li.tm-reverse-direction-nav').removeClass( 'tm-reverse-direction-nav' );
		$('.nav li li ul').each(function(){
			var $dropdown       = $(this),
				dropdown_width  = $dropdown.width(),
				dropdown_offset = $dropdown.offset(),
				$parents        = $dropdown.parents('.nav > li');

			if ( dropdown_offset.left > ( window_width - dropdown_width ) ) {
				$parents.addClass( 'tm-reverse-direction-nav' );
			}
		});
	}
	tm_fix_nav_direction();

	tm_pb_form_placeholders_init( $( '.tm_pb_newsletter_form' ) );

	$('.tm_pb_fullwidth_menu ul.nav').each(function(i) {
		i++;
		tm_duplicate_menu( $(this), $(this).parents('.tm_pb_row').find('div .mobile_nav'), 'mobile_menu' + i, 'tm_mobile_menu' );
	});

	window.tm_fix_testimonial_inner_width = function() {
		var window_width = $( window ).width();

		if( window_width > 767 ){
			$( '.tm_pb_testimonial' ).each( function() {
				if ( ! $(this).is(':visible') ) {
					return;
				}

				var $testimonial      = $(this),
					testimonial_width = $testimonial.width(),
					$portrait         = $testimonial.find( '.tm_pb_testimonial_portrait' ),
					portrait_width    = $portrait.width(),
					$testimonial_inner= $testimonial.find( '.tm_pb_testimonial_description_inner' ),
					$outer_column     = $testimonial.closest( '.tm_pb_column' ),
					testimonial_inner_width = testimonial_width,
					subtract = ! ( $outer_column.hasClass( 'tm_pb_column_1_3' ) || $outer_column.hasClass( 'tm_pb_column_1_4' ) || $outer_column.hasClass( 'tm_pb_column_3_8' ) ) ? portrait_width + 31 : 0;

					$testimonial_inner.width( testimonial_inner_width - subtract );
			} );
		} else {
			$( '.tm_pb_testimonial_description_inner' ).removeAttr( 'style' );
		}
	}
	window.tm_fix_testimonial_inner_width();

	window.tm_reinint_waypoint_modules = function() {
		if ( $.fn.waypoint ) {
			var $tm_pb_circle_counter = $( '.tm_pb_circle_counter_bar' ),
				$tm_pb_number_counter = $( '.tm_pb_number_counter' );

			$( '.tm_pb_counter_container, .tm-waypoint' ).waypoint( {
				offset: '75%',
				handler: function() {
					$(this.element).addClass( 'tm-animated' );
				}
			} );

			if ( $tm_pb_circle_counter.length ) {
				$tm_pb_circle_counter.each(function(){
					var $this_counter = $(this);
					if ( ! $this_counter.is( ':visible' ) ) {
						return;
					}
					$this_counter.waypoint({
						offset: '65%',
						handler: function() {
							$this_counter.data('easyPieChart').update( $this_counter.data('number-value') );
						}
					});
				});
			}

			if ( $tm_pb_number_counter.length ) {
				$tm_pb_number_counter.each(function(){
					var $this_counter = $(this);
					$this_counter.waypoint({
						offset: '75%',
						handler: function() {
							$this_counter.data('easyPieChart').update( $this_counter.data('number-value') );
						}
					});
				});
			}
		}
	}

	window.tm_calc_fullscreen_section = function() {
		var $tm_window = $(window),
			$body = $( 'body' ),
			$wpadminbar = $( '#wpadminbar' ),
			tm_is_vertical_nav = $body.hasClass( 'tm_vertical_nav' ),
			$this_section = $(this),
			this_section_index = $this_section.index('.tm_pb_fullwidth_header'),
			$header = $this_section.children('.tm_pb_fullwidth_header_container'),
			$header_content = $header.children('.header-content-container'),
			$header_image = $header.children('.header-image-container'),
			sectionHeight = $tm_window.height(),
			$wpadminbar = $('#wpadminbar'),
			$top_header = $('#top-header'),
			$main_header = $('#main-header'),
			tm_header_height,
			secondary_nav_height;

			secondary_nav_height = $top_header.length && $top_header.is( ':visible' ) ? $top_header.innerHeight() : 0;
			tm_header_height = $main_header.length ? $main_header.innerHeight() + secondary_nav_height : 0;

		var calc_header_offset = ( $wpadminbar.length ) ? tm_header_height + $wpadminbar.innerHeight() - 1 : tm_header_height - 1;

		// Section height adjustment differs in vertical and horizontal nav
		if ( $body.hasClass('tm_vertical_nav') ) {
			if ( $tm_window.width() >= 980 && $top_header.length ) {
				sectionHeight -= $top_header.height();
			}

			if ( $wpadminbar.length ) {
				sectionHeight -= $wpadminbar.height();
			}
		} else {
			if ( $body.hasClass('tm_hide_nav' ) ) {
				// If user is logged in and hide navigation is in use, adjust the section height
				if ( $wpadminbar.length ) {
					sectionHeight -= $wpadminbar.height();
				}

				// In mobile, header always appears. Adjust the section height
				if ( $tm_window.width() < 981 && ! $body.hasClass('tm_transparent_nav') ) {
					sectionHeight -= $('#main-header').height();
				}
			} else {
				if ( $this_section.offset().top <= calc_header_offset + 3 ) {
					if ( tm_is_vertical_nav ) {
						var $top_header = $('#top-header'),
							top_header_height = ( $top_header.length && 0 === $this_section.index( '.tm_pb_fullscreen' ) ) ? $top_header.height() : 0,
							wpadminbar_height = ( $wpadminbar.length && 0 === $this_section.index( '.tm_pb_fullscreen' ) ) ? $wpadminbar.height() : 0,
							calc_header_offset_vertical = wpadminbar_height + top_header_height;

						sectionHeight -= calc_header_offset_vertical;
					} else {
						sectionHeight -= calc_header_offset;
					}
				}
			}
		}

		// If the transparent primary nav + hide nav until scroll is being used,
		// cancel automatic padding-top added by transparent nav mechanism
		if ( $body.hasClass('tm_transparent_nav') && $body.hasClass( 'tm_hide_nav' ) &&  0 === this_section_index ) {
			$this_section.css( 'padding-top', '' );
		}

		$this_section.css('min-height', sectionHeight + 'px' );
		$header.css('min-height', sectionHeight + 'px' );

		if ( $header.hasClass('center') && $header_content.hasClass('bottom') && $header_image.hasClass('bottom') ) {
			$header.addClass('bottom-bottom');
		}

		if ( $header.hasClass('center') && $header_content.hasClass('center') && $header_image.hasClass('center') ) {
			$header.addClass('center-center');
		}

		if ( $header.hasClass('center') && $header_content.hasClass('center') && $header_image.hasClass('bottom') ) {
			$header.addClass('center-bottom');

			var contentHeight = sectionHeight - $header_image.outerHeight( true );

			if ( contentHeight > 0 ) {
				$header_content.css('min-height', contentHeight + 'px' );
			}
		}

		if ( $header.hasClass('center') && $header_content.hasClass('bottom') && $header_image.hasClass('center') ) {
			$header.addClass('bottom-center');
		}

		if ( ( $header.hasClass('left') || $header.hasClass('right') ) && !$header_content.length && $header_image.length ) {
			$header.css('justify-content', 'flex-end');
		}

		if ( $header.hasClass('center') && $header_content.hasClass('bottom') && !$header_image.length ) {
			$header_content.find('.header-content').css( 'margin-bottom', 80 + 'px' );
		}

		if ( $header_content.hasClass('bottom') && $header_image.hasClass('center') ) {
			$header_image.find('.header-image').css( 'margin-bottom', 80 + 'px' );
			$header_image.css('align-self', 'flex-end');
		}
	}

	$( window ).resize( function(){
		var window_width                = $tm_window.width(),
			tm_container_css_width      = $tm_container.css( 'width' ),
			tm_container_width_in_pixel = ( typeof tm_container_css_width !== 'undefined' ) ? tm_container_css_width.substr( -1, 1 ) !== '%' : '',
			tm_container_actual_width   = ( tm_container_width_in_pixel ) ? $tm_container.width() : ( ( $tm_container.width() / 100 ) * window_width ), // $tm_container.width() doesn't recognize pixel or percentage unit. It's our duty to understand what it returns and convert it properly
			containerWidthChanged       = tm_container_width !== tm_container_actual_width;

		tm_pb_resize_section_video_bg();
		tm_pb_center_video();
		tm_fix_slider_height();
		tm_fix_nav_direction();

		$tm_pb_fullwidth_portfolio.each(function(){
			set_container_height = $(this).hasClass('tm_pb_fullwidth_portfolio_carousel') ? true : false;
			set_fullwidth_portfolio_columns( $(this), set_container_height );
		});

		if ( containerWidthChanged ) {
			$('.container-width-change-notify').trigger('containerWidthChanged');

			setTimeout( function() {
				$tm_pb_filterable_portfolio.each(function(){
					set_filterable_grid_items( $(this) );
				});
				$tm_pb_gallery.each(function(){
					if ( $(this).hasClass( 'tm_pb_gallery_grid' ) ) {
						set_gallery_grid_items( $(this) );
					}
				});
			}, 100 );

			tm_container_width = tm_container_actual_width;

			etRecalculateOffset = true;

			if ( $tm_pb_circle_counter.length ) {
				$tm_pb_circle_counter.each(function(){
					var $this_counter = $(this);
					if ( ! $this_counter.is( ':visible' ) ) {
						return;
					}

					$this_counter.data('easyPieChart').update( $this_counter.data('number-value') );
				});
			}
			if ( $tm_pb_countdown_timer.length ) {
				$tm_pb_countdown_timer.each(function(){
					var timer = $(this);
					tm_countdown_timer_labels( timer );
				} );
			}
		}

		window.tm_fix_testimonial_inner_width();

		tm_audio_module_set();
	} );

	$( window ).ready( function(){
		if ( $.fn.fitVids ) {
			$( '.tm_pb_slide_video' ).fitVids();
			$( '.tm_pb_module' ).fitVids( { customSelector: "iframe[src^='http://www.hulu.com'], iframe[src^='http://www.dailymotion.com'], iframe[src^='http://www.funnyordie.com'], iframe[src^='https://embed-ssl.ted.com'], iframe[src^='http://embed.revision3.com'], iframe[src^='https://flickr.com'], iframe[src^='http://blip.tv'], iframe[src^='http://www.collegehumor.com']"} );
		}

		tm_fix_video_wmode('.fluid-width-video-wrapper');

		tm_fix_slider_height();
	} );

	$( window ).load( function(){
		tm_fix_fullscreen_section();

		$( 'section.tm_pb_fullscreen' ).each( function(){
			var $this_section = $( this );

			$.proxy( tm_calc_fullscreen_section, $this_section )();

			$tm_window.on( 'resize', $.proxy( tm_calc_fullscreen_section, $this_section ) );
		});

		$( '.tm_pb_fullwidth_header_scroll a' ).click( function( event ) {
			event.preventDefault();

			var $this_section      = $(this).parents( 'section' ),
				is_next_fullscreen = $this_section.next().hasClass( 'tm_pb_fullscreen' ),
				$wpadminbar        = $('#wpadminbar'),
				wpadminbar_height  = ( $wpadminbar.length && ! is_next_fullscreen ) ? $wpadminbar.height() : 0,
				main_header_height = is_next_fullscreen || ! tm_is_fixed_nav ? 0 : $main_header.height(),
				top_header_height  = is_next_fullscreen || ! tm_is_fixed_nav ? 0 : $top_header.height(),
				section_bottom     = $this_section.offset().top + $this_section.outerHeight( true ) - ( wpadminbar_height + top_header_height + main_header_height );

			if ( $this_section.length ) {
				$( 'html, body' ).animate( { scrollTop : section_bottom }, 800 );

				if ( ! $( '#main-header' ).hasClass( 'tm-fixed-header' ) && $( 'body' ).hasClass( 'tm_fixed_nav' ) && $( window ).width() > 980 ) {
					setTimeout(function(){
						var section_offset_top = $this_section.offset().top,
							section_height     = $this_section.outerHeight( true ),
							main_header_height = is_next_fullscreen ? 0 : $main_header.height(),
							section_bottom     = section_offset_top + section_height - ( main_header_height + top_header_height + wpadminbar_height);

						$( 'html, body' ).animate( { scrollTop : section_bottom }, 280, 'linear' );
					}, 780 );
				}
			}
		});

		setTimeout( function() {
			$( '.tm_pb_preload' ).removeClass( 'tm_pb_preload' );
		}, 500 );

		if ( $.fn.hashchange ) {
			$(window).hashchange( function(){
				var hash = window.location.hash.substring(1);
				process_tm_hashchange( hash );
			});
			$(window).hashchange();
		}

		if ( $tm_pb_parallax.length && !tm_is_mobile_device ) {
			$tm_pb_parallax.each(function(){
				if ( $(this).hasClass('tm_pb_parallax_css') ) {
					return;
				}

				var $this_parent = $(this).parent();

				$.proxy( tm_parallax_set_height, $this_parent )();

				$.proxy( tm_apply_parallax, $this_parent )();

				$tm_window.on( 'scroll', $.proxy( tm_apply_parallax, $this_parent ) );

				$tm_window.on( 'resize', $.proxy( tm_parallax_set_height, $this_parent ) );
				$tm_window.on( 'resize', $.proxy( tm_apply_parallax, $this_parent ) );

				$this_parent.find('.tm-learn-more .heading-more').click( function() {
					setTimeout(function(){
						$.proxy( tm_parallax_set_height, $this_parent )();
					}, 300 );
				});
			});
		}

		tm_audio_module_set();

		window.tm_reinint_waypoint_modules();
	} );

	if ( $( '.tm_section_specialty' ).length ) {
		$( '.tm_section_specialty' ).each( function() {
			var this_row = $( this ).find( '.tm_pb_row' );

			this_row.find( '>.tm_pb_column:not(.tm_pb_specialty_column)' ).addClass( 'tm_pb_column_single' );
		});
	}

	/**
	* In particular browser, map + parallax doesn't play well due the use of CSS 3D transform
	*/
	if ( $('.tm_pb_section_parallax').length && $('.tm_pb_map').length ) {
		$('body').addClass( 'parallax-map-support' );
	}

	/**
	 * Add conditional class for search widget in sidebar module
	 */
	$('.tm_pb_widget_area ' + tm_pb_custom.widget_search_selector ).each( function() {
		var $search_wrap              = $(this),
			$search_input_submit      = $search_wrap.find('input[type="submit"]'),
			search_input_submit_text = $search_input_submit.attr( 'value' ),
			$search_button            = $search_wrap.find('button'),
			search_button_text       = $search_button.text(),
			has_submit_button         = $search_input_submit.length || $search_button.length ? true : false,
			min_column_width          = 150;

		if ( ! $search_wrap.find( 'input[type="text"]' ).length && ! $search_wrap.find( 'input[type="search"]' ).length ) {
			return;
		}

		// Mark no button state
		if ( ! has_submit_button ) {
			$search_wrap.addClass( 'tm-no-submit-button' );
		}

		// Mark narrow state
		if ( $search_wrap.width() < 150 ) {
			$search_wrap.addClass( 'tm-narrow-wrapper' );
		}

		// Fixes issue where theme's search button has no text: treat it as non-existent
		if ( $search_input_submit.length && ( typeof search_input_submit_text == 'undefined' || search_input_submit_text === '' ) ) {
			$search_input_submit.remove();
			$search_wrap.addClass( 'tm-no-submit-button' );
		}

		if ( $search_button.length && ( typeof search_button_text == 'undefined' || search_button_text === '' ) ) {
			$search_button.remove();
			$search_wrap.addClass( 'tm-no-submit-button' );
		}

	} );

	if ( $( '.tm_pb_search' ).length ) {
		$( '.tm_pb_search' ).each( function() {
			var $this_module = $( this ),
				$input_field = $this_module.find( '.tm_pb_s' ),
				$button = $this_module.find( '.tm_pb_searchsubmit' ),
				input_padding = $this_module.hasClass( 'tm_pb_text_align_right' ) ? 'paddingLeft' : 'paddingRight',
				disabled_button = $this_module.hasClass( 'tm_pb_hide_search_button' );

			if ( $button.innerHeight() > $input_field.innerHeight() ) {
				$input_field.height( $button.innerHeight() );
			}

			if ( ! disabled_button ) {
				$input_field.css( input_padding, $button.innerWidth() + 10 );
			}
		});
	}

	// apply required classes for the Reply buttons in Comments Module
	if ( $( '.tm_pb_comments_module' ).length ) {
		$( '.tm_pb_comments_module' ).each( function() {
			var $comments_module = $( this ),
				$comments_module_button = $comments_module.find( '.comment-reply-link' );

			if ( $comments_module_button.length ) {
				$comments_module_button.addClass( 'tm_pb_button' );

				if ( typeof $comments_module.data( 'icon' ) !== 'undefined' ) {
					$comments_module_button.attr( 'data-icon', $comments_module.data( 'icon' ) );
					$comments_module_button.addClass( 'tm_pb_custom_button_icon' );
				}
			}
		});
	}

	//  Carousel Module
	if ( $( '.tm_pb_swiper' )[0] ) {
		$( '.tm_pb_swiper' ).each( function() {
			var $this = $( this ),
				settings = $this.data('settings'),
				pagination = ( 'on' === settings['pagination'] ) ? true : false ,
				navigateButton = ( 'on' === settings['navigateButton'] ) ? true : false ,
				autoplay = ( 'on' === settings['autoplay'] ) ? 3500 : false ,
				centeredSlides = ( 'on' === settings['centeredSlides'] ) ? true : false ,
				spaceBetweenSlides = settings['spaceBetweenSlides'] || 0 ,
				slidesPerView = settings['slidesPerView'],
				swiperContainer = $( '.swiper-container', $this ),
				swiper = new Swiper( swiperContainer , {
						slidesPerView: +slidesPerView,
						autoplay: autoplay,
						centeredSlides: centeredSlides,
						mousewheelControl: false,
						paginationClickable: true,
						spaceBetween: +spaceBetweenSlides,
						speed: 500,
						nextButton: ( navigateButton ) ? $('.swiper-button-next', $this) : null,
						prevButton: ( navigateButton ) ? $('.swiper-button-prev', $this) : null,
						pagination: ( pagination ) ? $('.swiper-pagination', $this) : null,
						onInit: function(){
							if ( ! navigateButton ) {
								$('.swiper-button-next, .swiper-button-prev', $this).remove();
							}

							if ( ! pagination ) {
								$('.swiper-pagination', $this).remove();
							}
						},
						breakpoints: {
							1200: {
								slidesPerView: Math.floor( slidesPerView * 0.75 ),
								spaceBetween: Math.floor( spaceBetweenSlides * 0.75 )
							},
							992: {
								slidesPerView: Math.floor( slidesPerView * 0.5 ),
								spaceBetween: Math.floor( spaceBetweenSlides * 0.5 )
							},
							769: {
								slidesPerView: ( 0 !== Math.floor( slidesPerView * 0.25 ) ) ? Math.floor( slidesPerView * 0.25 ) : 1
							},
						}
				});
		});

		$( window ).on( 'load', loadHandler );
		function loadHandler() {
			$( '.tm_pb_swiper' ).css({ 'opacity': '1' });
		}
	}

});
