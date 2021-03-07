jQuery ( function( $ ) {
    function swp_fill_suggestion(ui) {
        if ( ui.item.domain !== 'default' ) {
            $( '.say_what_domain' ).val( ui.item.domain );
        } else {
            $( '.say_what_domain' ).val( '' );
        }
        if ( ui.item.context !== 'sw-default-context' ) {
            $( '.say_what_context' ).val( ui.item.context );
        } else {
            $( '.say_what_context' ).val( '' );
        }
        if ( typeof ui.item.translated_string !== 'undefined' && ui.item.translated_string.length > 0 ) {
            $( '.say_what_translated_string' ).html( ui.item.translated_string );
        } else {
            $( '.say_what_translated_string' ).html( '' );
        }
        $( '.say_what_orig_string' ).val( ui.item.orig_string );
        $( '.say_what_replacement_string' ).focus();
    }
    $( '.say_what_orig_string' ).autocomplete( {
		minLength: 1,
		source: say_what.autocomplete_url,
		select: function( event, ui ) {
			if ( ui.item.orig_string === 'SWP_NO_MATCHES' ) {
				return false;
			} else if ( ui.item.orig_string === 'SWP_NO_SUGGESTIONS' ) {
				window.location = window.say_what.string_discovery_url;
				return false;
			} else {
                swp_fill_suggestion( ui );
            }
			return false;
		}
	});
	jQuery.ui.autocomplete.prototype._resizeMenu = function () {
	  var ul = this.menu.element;
	  ul.outerWidth( this.element.outerWidth() );
	}
});
