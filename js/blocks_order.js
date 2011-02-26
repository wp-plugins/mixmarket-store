jQuery( document ).ready( function( $ ) {
    $( '.blocks_order' ).sortable( {
        revert: true,
        update: function ( event, ui ) {
            var blocks_order = [];
            $( '.blocks_order li' ).each( function () {
                blocks_order.push( $(this).attr( 'id' ).substr( 2 ) );
            } );
            $( 'input[name="mm_blocks_order"]' ).val( blocks_order.join( '|' ) );
        }
    } );
	$( '#import_data' ).click( function() {
		if ( $( 'input[name="mm_db_dump"]' ).val() == '' ) {
			alert(MM_Options.select_file);
			return false;
		}
		return true;
	} );
    $( '#mm_remove_data' ).click( function() {
        return confirm( MM_Options.confirm_data_delete );
    });
	$( 'input[name="mm_image_height"]' ).attr( 'readonly', true );
	$( 'input[name="mm_image_thumb_height"]' ).attr( 'readonly', true );
	$( 'input[name="mm_image_width"]' ).bind( 'keyup change', function() {
		$( 'input[name="mm_image_height"]' ).val( ( $( this ).val() ) );
	});
	$( 'input[name="mm_image_thumb_width"]' ).bind( 'keyup change', function() {
		$( 'input[name="mm_image_thumb_height"]' ).val( ( $( this ).val() ) );
	});
});