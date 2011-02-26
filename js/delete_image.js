jQuery( document ).ready( function( $ ) {
	function gup( name )
	{
		name = name.replace(/[\[]/,"\\\[").replace(/[\]]/,"\\\]");
		var regexS = "[\\?&]"+name+"=([^&#]*)";
		var regex = new RegExp( regexS );
		var results = regex.exec( window.location.href );
		if( results == null )
			return "";
		else
			return results[1];
	}

	$( 'a.delete_image' ).click( function() {
		if ( confirm( mm_delete_image.delete_message ) ) {
			var good_id = gup( 'good_id');
            var cur_link = $( this );
            var t_cell = cur_link.parent();
            var good_img = t_cell.find( 'img' );
            var file_field = t_cell.find( 'input' );
			if ( '' != good_id ) {
				$.post( mm_delete_image.script_url, { 'id' : good_id }, function( data ) {
                    var response = eval( '(' + data + ')' );
                    if ( response.status == 'OK' ) {
                        cur_link.remove();
                        good_img.remove();
                        file_field.attr( 'value', '' );
                        alert( 'Картинка удалена' );
                    }
				} );
			}
		}
		return false;
	} );
});