( function( $ ) {
	$( '.submitdelete' ).click( function( e ) {
		return confirm( mm_confirm_settings.delete_message );
	} );
} )( jQuery );