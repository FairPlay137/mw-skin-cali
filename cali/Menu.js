/**
 * Menu navigation and other JavaScript functions used by the Cali skin.
 *
 * @file
 * @author Jack Phoenix - cleanup & removal of YUI dependency, etc.
 */
/* global getElementsByClassName, window, document, setTimeout, clearTimeout */
( function ( $ ) {

var CaliSkin = {
	last_clicked: '',
	m_timer: '',
	displayed_menus: [],
	last_displayed: '',
	last_over: '',
	show: 'false',
	_shown: false,
	_hide_timer: '',
	menuitem_array: [],
	submenu_array: [],
	submenuitem_array: [],

	submenu: function( id ) {
		var on_tabs, x;

		// Clear all tab classes
		on_tabs = getElementsByClassName( document, 'a', 'tab-on' );
		for ( x = 0; x <= on_tabs.length - 1; x++ ) {
			$( '#' + on_tabs[x] ).addClass( 'tab-off' );
		}

		on_tabs = getElementsByClassName( document, 'div', 'sub-menu' );
		for ( x = 0; x <= on_tabs.length - 1; x++ ) {
			$( '#' + on_tabs[x] ).hide();
		}

		// Hide submenu that might have been previously clicked
		if ( CaliSkin.last_clicked ) {
			$( '#submenu-' + CaliSkin.last_clicked ).hide();
		}

		// Update tab class you clicked on/show its submenu
		if ( $( '#menu-' + id ).hasClass( 'tab-off' ) ) {
			$( '#menu-' + id ).addClass( 'tab-on' );
		}

		$( '#submenu-' + id ).show();

		CaliSkin.last_clicked = id;
	},

	editMenuToggle: function() {
		var submenu = document.getElementById( 'edit-sub-menu-id' );

		if ( submenu.style.display === 'block' ) {
			submenu.style.display = 'none';
		} else {
			submenu.style.display = 'block';
		}
	},
	// End menu nav

	// Skin Navigation
	menuItemAction: function( e ) {
		clearTimeout( CaliSkin.m_timer );

		if ( !e ) {
			e = window.event;
		}
		e.cancelBubble = true;
		if ( e.stopPropagation ) {
			e.stopPropagation();
		}

		var source_id = '*';
		try {
			source_id = e.target.id;
		} catch ( ex ) {
			source_id = e.srcElement.id;
		}

		if ( source_id.indexOf( 'a-' ) === 0 ) {
			source_id = source_id.substr( 2 );
		}

		if ( source_id && CaliSkin.menuitem_array[source_id] ) {
			if ( CaliSkin.last_over !== '' && document.getElementById( CaliSkin.last_over ) ) {
				document.getElementById( CaliSkin.last_over ).style.backgroundColor = '#dfd7d4';
			}
			CaliSkin.last_over = source_id;
			document.getElementById( source_id ).style.backgroundColor = '#fff7d4';
			CaliSkin.check_item_in_array( CaliSkin.menuitem_array[source_id] );
		}
	},

	check_item_in_array: function( item ) {
		clearTimeout( CaliSkin.m_timer );
		var sub_menu_item = 'sub-menu' + item,
			exit, count, the_last_displayed;

		if (
			CaliSkin.last_displayed === '' ||
			( ( sub_menu_item.indexOf( CaliSkin.last_displayed ) !== -1 ) &&
				( sub_menu_item !== CaliSkin.last_displayed ) )
		)
		{
			CaliSkin.do_menuItemAction( item );
		} else {
			exit = false;
			count = 0;
			while ( !exit && CaliSkin.displayed_menus.length > 0 ) {
				the_last_displayed = CaliSkin.displayed_menus.pop();
				if ( ( sub_menu_item.indexOf( the_last_displayed ) === -1 ) ) {
					CaliSkin.doClear( the_last_displayed, '' );
				} else {
					CaliSkin.displayed_menus.push( the_last_displayed );
					exit = true;
					CaliSkin.do_menuItemAction( item );
				}
				count++;
			}

			CaliSkin.do_menuItemAction( item );
		}
	},

	do_menuItemAction: function( item ) {
		if ( document.getElementById( 'sub-menu' + item ) ) {
			if((document.getElementById( 'sub-menu' + item ).style.display != 'block') && ( document.getElementById( 'sub-menu' + item ).classList.contains('menu3d'))) {
				CaliSkin.submenu_3danim_in(document.getElementById( 'sub-menu' + item ));
			}
			document.getElementById( 'sub-menu' + item ).style.display = 'block';
			CaliSkin.displayed_menus.push( 'sub-menu' + item );
			CaliSkin.last_displayed = 'sub-menu' + item;
		}
	},

	sub_menuItemAction: function( e ) {
		clearTimeout( CaliSkin.m_timer );

		if ( !e ) {
			e = window.event;
		}
		e.cancelBubble = true;
		if ( e.stopPropagation ) {
			e.stopPropagation();
		}

		var source_id = '*',
			second_start, second_uscore;
		try {
			source_id = e.target.id;
		} catch ( ex ) {
			source_id = e.srcElement.id;
		}

		if ( source_id && CaliSkin.submenuitem_array[source_id] ) {
			CaliSkin.check_item_in_array( CaliSkin.submenuitem_array[source_id] );

			if ( source_id.indexOf( '_' ) ) {
				if ( source_id.indexOf( '_', source_id.indexOf( '_' ) ) ) {
					second_start = source_id.substr( 4 + source_id.indexOf( '_' ) - 1 );
					second_uscore = second_start.indexOf( '_' );
					try {
						source_id = source_id.substr( 4, source_id.indexOf( '_' ) + second_uscore - 1 );
						if ( CaliSkin.menuitem_array[source_id] ) {
							document.getElementById( source_id ).style.backgroundColor = '#fff7d4';
						}
					} catch( ex ) {}
				} else {
					source_id = source_id.substr( 4 );
					if ( CaliSkin.menuitem_array[source_id] ) {
						document.getElementById( source_id ).style.backgroundColor = '#fff7d4';
					}
				}
			}
		}
	},

	clearBackground: function( e ) {
		if ( !e ) {
			e = window.event;
		}
		e.cancelBubble = true;
		if ( e.stopPropagation ) {
			e.stopPropagation();
		}

		var source_id = '*';
		try {
			source_id = e.target.id;
		} catch ( ex ) {
			source_id = e.srcElement.id;
		}

		if (
			source_id &&
			document.getElementById( source_id ) &&
			CaliSkin.menuitem_array[source_id]
		)
		{
			document.getElementById( source_id ).style.backgroundColor = '#dfd7d4';
			CaliSkin.clearMenu( e );
		}
	},

	resetMenuBackground: function( e ) {
		if ( !e ) {
			e = window.event;
		}
		e.cancelBubble = true;
		if ( e.stopPropagation ) {
			e.stopPropagation();
		}

		var source_id = '*';
		try {
			source_id = e.target.id;
		} catch ( ex ) {
			source_id = e.srcElement.id;
		}

		source_id = source_id.substr( 2 );

		document.getElementById( source_id ).style.backgroundColor = '#fff7d4';
	},

	clearMenu: function( e ) {
		if ( !e ) {
			e = window.event;
		}
		e.cancelBubble = true;
		if ( e.stopPropagation ) {
			e.stopPropagation();
		}

		var source_id = '*';
		try {
			source_id = e.target.id;
		} catch ( ex ) {
			source_id = e.srcElement.id;
		}

		clearTimeout( CaliSkin.m_timer );
		CaliSkin.m_timer = setTimeout( function() { CaliSkin.doClearAll(); }, 200 );
	},

	doClear: function( item, type ) {
		if ( document.getElementById( type + item ) ) {
			document.getElementById( type + item ).style.display = 'none';
		}
	},

	doClearAll: function() {
		// Otherwise the CaliSkin.displayed_menus[0] line below causes a TypeError about
		// CaliSkin.displayed_menus[0] being undefined
		if ( !CaliSkin.displayed_menus.length ) {
			return;
		}
		var epicElement = document.getElementById( 'menu-item' + CaliSkin.displayed_menus[0].substr( CaliSkin.displayed_menus[0].indexOf( '_' ) ) ),
			the_last_displayed, exit;
		if ( CaliSkin.displayed_menus.length && epicElement ) {
			epicElement.style.backgroundColor = '#dfd7d4';
		}
		exit = false;
		while ( !exit && CaliSkin.displayed_menus.length > 0 ) {
			the_last_displayed = CaliSkin.displayed_menus.pop();
			CaliSkin.doClear( the_last_displayed, '' );
		}

		CaliSkin.last_displayed = '';
	},

	show_more_category: function( el, toggle ) {
		if ( toggle !== undefined ) {
			$( '#' + el ).toggle( toggle );
		} else {
			$( '#' + el ).toggle();
		}
	},

	show_actions: function( el, type ) {
		if ( type === 'show' ) {
			clearTimeout( CaliSkin._hide_timer );
			if ( !CaliSkin._shown ) {
				$( '#more-tab' ).removeClass( 'more-tab-off' ).addClass( 'more-tab-on' );
				$( '#' + el ).show();
				CaliSkin._shown = true;
			}
		} else {
			$( '#more-tab' ).removeClass( 'more-tab-on' ).addClass( 'more-tab-off' );
			$( '#' + el ).hide();
			CaliSkin._shown = false;
		}
	},

	delay_hide: function( el ) {
		CaliSkin._hide_timer = setTimeout( function() {
			CaliSkin.show_actions( el, 'hide' );
		}, 500 );
	},
	
	submenu_3danim_in: function( elem ) { 
		var rot = -90;
		var xzofforig = -50;
		var negxzofforig = -xzofforig;
		var xoff = xzofforig*(Math.cos(-(rot/180)*Math.PI));
		var zoff = xzofforig*(Math.sin(-(rot/180)*Math.PI));
		var opac = 1+(rot/90);
		elem.style.opacity = opac;
		elem.style.transform = 'rotateY('+rot.toString()+'deg) translateX('+xoff.toString()+'px) translateZ('+zoff.toString()+'px) translate('+negxzofforig.toString()+'px, 0)';
		var id = setInterval(frame, 5);
		function frame() {
			xoff = xzofforig*(Math.cos((rot/180)*Math.PI));
			zoff = xzofforig*(Math.sin((rot/180)*Math.PI));
			opac = 1+(rot/90);
			if (rot >= 0) {
				clearInterval(id);
				elem.style.opacity = '';
				elem.style.transform = '';
			} else {
				rot += 3;
				elem.style.opacity = opac;
				elem.style.transform = 'rotateY('+rot.toString()+'deg) translateX('+xoff.toString()+'px) translateZ('+zoff.toString()+'px) translate('+negxzofforig.toString()+'px, 0)';
			}
		}
	}
};

$( function() {
	// Top-level menus
	$( 'div[id^="menu-item_"]' ).each( function( idx, elem ) {
		var id = $( elem ).attr( 'id' );
		CaliSkin.menuitem_array[id] = id.replace( /menu\-item/gi, '' );

		$( this ).on( 'mouseover', CaliSkin.menuItemAction );
		$( this ).on( 'mouseout', CaliSkin.clearBackground );

		if ( document.getElementById( id ).captureEvents ) {
			document.getElementById( id ).captureEvents( Event.MOUSEOUT );
		}

		document.getElementById( 'a-' + id ).onmouseover = CaliSkin.menuItemAction;
		if ( document.getElementById( 'a-' + id ).captureEvents ) {
			document.getElementById( 'a-' + id ).captureEvents( Event.MOUSEOVER );
		}
	} );

	// Sub-menus...
	$( 'div[id^="sub-menu_"]' ).each( function() {
		var id = $( this ).attr( 'id' );
		CaliSkin.submenu_array[id] = id.replace( /sub\-menu/gi, '' );

		$( this ).on( 'mouseout', CaliSkin.clearMenu );

		if ( document.getElementById( id ).captureEvents ) {
			document.getElementById( id ).captureEvents( Event.MOUSEOUT );
		}
	} );

	// ...and their items
	$( 'div[id^="sub-menu-item_"]' ).each( function() {
		var id = $( this ).attr( 'id' );
		CaliSkin.submenuitem_array[id] = id.replace( /sub\-menu\-item/gi, '' );

		$( this ).on( 'mouseover', CaliSkin.sub_menuItemAction );

		if ( document.getElementById( id ).captureEvents ) {
			document.getElementById( id ).captureEvents( Event.MOUSEOVER );
		}
	} );

	$( '#more-tab' ).on( 'mouseover', function() {
		CaliSkin.show_actions( 'article-more-container', 'show' );
	} ).on( 'mouseout', function() {
		CaliSkin.delay_hide( 'article-more-container' );
	} );

	$( '#article-more-container' ).on( 'mouseover', function() {
		clearTimeout( CaliSkin._hide_timer );
	} ).on( 'mouseout', function() {
		CaliSkin.show_actions( 'article-more-container', 'hide' );
	} );

	$( '#tp-more-category' ).on( 'click', function( e ) {
		CaliSkin.show_more_category( 'more-wikis-menu' );
		e.stopPropagation();
	} );

	$( 'body' ).on( 'click', function () {
		CaliSkin.show_more_category( 'more-wikis-menu', false );
	} );
} );

}( jQuery ) );
