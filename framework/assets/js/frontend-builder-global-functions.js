(function($){
	window.tm_pb_smooth_scroll = function( $target, $top_section, speed, easing ) {
		var $window_width = $( window ).width();

		if ( $( 'body' ).hasClass( 'tm_fixed_nav' ) && $window_width > 980 ) {
			$menu_offset = $( '#top-header' ).outerHeight() + $( '#main-header' ).outerHeight() - 1;
		} else {
			$menu_offset = -1;
		}

		if ( $ ('#wpadminbar').length && $window_width > 600 ) {
			$menu_offset += $( '#wpadminbar' ).outerHeight();
		}

		//fix sidenav scroll to top
		if ( $top_section ) {
			$scroll_position = 0;
		} else {
			$scroll_position = $target.offset().top - $menu_offset;
		}

		// set swing (animate's scrollTop default) as default value
		if( typeof easing === 'undefined' ){
			easing = 'swing';
		}

		$( 'html, body' ).animate( { scrollTop :  $scroll_position }, speed, easing );
	}

	window.tm_fix_video_wmode = function( video_wrapper ) {
		$( video_wrapper ).each( function() {
			if ( $(this).find( 'iframe' ).length ) {
				var $this_el = $(this).find( 'iframe' ),
					src_attr = $this_el.attr('src'),
					wmode_character = src_attr.indexOf( '?' ) == -1 ? '?' : '&amp;',
					this_src = src_attr + wmode_character + 'wmode=opaque';

				$this_el.attr('src', this_src);
			}
		} );
	}

	window.tm_pb_form_placeholders_init = function( $form ) {
		$form.find('input:text, textarea').each(function(index,domEle){
			var $tm_current_input = jQuery(domEle),
				$tm_comment_label = $tm_current_input.siblings('label'),
				tm_comment_label_value = $tm_current_input.siblings('label').text();
			if ( $tm_comment_label.length ) {
				$tm_comment_label.hide();
				if ( $tm_current_input.siblings('span.required') ) {
					tm_comment_label_value += $tm_current_input.siblings('span.required').text();
					$tm_current_input.siblings('span.required').hide();
				}
				$tm_current_input.val(tm_comment_label_value);
			}
		}).bind('focus',function(){
			var tm_label_text = jQuery(this).siblings('label').text();
			if ( jQuery(this).siblings('span.required').length ) tm_label_text += jQuery(this).siblings('span.required').text();
			if (jQuery(this).val() === tm_label_text) jQuery(this).val("");
		}).bind('blur',function(){
			var tm_label_text = jQuery(this).siblings('label').text();
			if ( jQuery(this).siblings('span.required').length ) tm_label_text += jQuery(this).siblings('span.required').text();
			if (jQuery(this).val() === "") jQuery(this).val( tm_label_text );
		});
	}

	window.tm_duplicate_menu = function( menu, append_to, menu_id, menu_class, menu_click_event ){
		append_to.each( function() {
			var $this_menu = $(this),
				$cloned_nav;

			// make this function work with existing menus, without cloning
			if ( '' !== menu ) {
				menu.clone().attr('id',menu_id).removeClass().attr('class',menu_class).appendTo( $this_menu );
			}

			$cloned_nav = $this_menu.find('> ul');
			$cloned_nav.find('.menu_slide').remove();
			$cloned_nav.find('li:first').addClass('tm_first_mobile_item');

			$cloned_nav.find( 'a' ).on( 'click', function(){
				$this_menu.trigger( 'click' );
			} );

			if ( 'no_click_event' !== menu_click_event ) {
				$this_menu.on( 'click', '.mobile_menu_bar', function(){
					if ( $this_menu.hasClass('closed') ){
						$this_menu.removeClass( 'closed' ).addClass( 'opened' );
						$cloned_nav.stop().slideDown( 500 );
					} else {
						$this_menu.removeClass( 'opened' ).addClass( 'closed' );
						$cloned_nav.stop().slideUp( 500 );
					}
					return false;
				} );
			}
		} );

		$('#mobile_menu .centered-inline-logo-wrap').remove();
	}

	// remove placeholder text before form submission
	window.tm_pb_remove_placeholder_text = function( $form ) {
		$form.find('input:text, textarea').each(function(index,domEle){
			var $tm_current_input = jQuery(domEle),
				$tm_label = $tm_current_input.siblings('label'),
				tm_label_value = $tm_current_input.siblings('label').text();

			if ( $tm_label.length && $tm_label.is(':hidden') ) {
				if ( $tm_label.text() == $tm_current_input.val() )
					$tm_current_input.val( '' );
			}
		});
	}

	window.tm_fix_fullscreen_section = function() {
		var $tm_window = $(window);

		$( 'section.tm_pb_fullscreen' ).each( function(){
			var $this_section = $( this );

			$.proxy( tm_calc_fullscreen_section, $this_section )();

			$tm_window.on( 'resize', $.proxy( tm_calc_fullscreen_section, $this_section ) );
		});
	}
})(jQuery)
