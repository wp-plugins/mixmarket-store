jQuery( document ).ready( function( $ ) {
	var brands = mm_brands.brands.split( '|' );
	$( "#mm_good_g_brand" ).autocomplete( brands );
} );
