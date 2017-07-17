/**
 * Handler for CSS Collector
 */
function CherryCSSCollector() {

	'use strict';

	var style,
		collectedCSS = window.TmCollectedCSS;

	if ( undefined !== collectedCSS ) {

		style = document.createElement( 'style' );

		style.setAttribute( 'id', collectedCSS.title );
		style.setAttribute( 'type', collectedCSS.type );
		style.setAttribute( 'media', 'screen' );

		style.textContent = collectedCSS.css;

		document.head.appendChild( style );
	}
}

CherryCSSCollector();
