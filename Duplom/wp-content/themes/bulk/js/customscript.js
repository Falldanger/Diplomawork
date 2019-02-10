// scroll to top button
jQuery( document ).ready( function ( $ ) {
    // Menu fixes
    $( function () {
        if ( $( window ).width() > 767 ) {
            $( ".dropdown" ).hover(
                function () {
                    $( this ).addClass( 'open' )
                },
                function () {
                    $( this ).removeClass( 'open' )
                }
            );
        }
    } );
    $( '.navbar .dropdown-toggle' ).hover( function () {
        $( this ).addClass( 'disabled' );
    } );
    $( window ).scroll( function () {
        var topmenu = $( '.top-menu-on #top-navigation' ).outerHeight();
        if ( $( document ).scrollTop() > ( topmenu + 50 ) ) {
            $( 'nav#site-navigation' ).addClass( 'shrink' );
        } else {
            $( 'nav#site-navigation' ).removeClass( 'shrink' );
        }
    } );
    
    $('.open-panel').each(function(){
        var menu = $( this ).data( 'panel' );
        $( "#" +menu ).click( function () {
            $( "#blog" ).toggleClass( "openNav" );
            $( "#" +menu+ ".open-panel" ).toggleClass( "open" );
            var navHeight = jQuery( '#site-navigation' ).height();
        } );
    });
    $( '.custom-header a[href*=#], .top-header a[href*=#]' ).on( 'click', function ( e ) {
        e.preventDefault();
        $( 'html, body' ).animate( { scrollTop: $( $( this ).attr( 'href' ) ).offset().top -60 }, 500, 'linear' );
    } );

} );

jQuery( window ).on( 'load resize', function () {
    var navHeight = jQuery( '#site-navigation' ).height();
    jQuery( '.page-area' ).css( 'padding-top', navHeight + 'px' );
    
    // Mobile menu height fix
    if ( jQuery( window ).width() < 768 ) {
        var vindowHeight = jQuery( window ).height();
        jQuery( '.menu-container' ).css( 'max-height', vindowHeight - navHeight + 'px' );
        jQuery( '.menu-container' ).css( 'padding-bottom', '60px' );
    }
} );
