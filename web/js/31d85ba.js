var AppShowcase = (function() {
	
	var $el = $( '#ac-wrapper' ),
		// device element
		$device = $el.find( '.ac-device' ),
		// the device image wrapper
		$trigger = $device.children( 'a:first' ),
		// the screens
		$screens = $el.find( '.ac-grid > a' ),
		// the device screen image
		$screenImg = $device.find( 'img' ),
		// the device screen title
		$screenTitle = $device.find( '.ac-title' ),
		// HTML Body element
		$body = $( 'body' ); 

	function init() {
		// show grid
		$trigger.on( 'click', showGrid );
		// when a grid´s screen is clicked, show the respective image on the device
		$screens.on( 'click', function() {
			showScreen( $( this ) );
			return false;
		} );
	}

	function showGrid() {
		$el.addClass( 'ac-gridview' );
		// clicking somewhere else on the page closes the grid view
		$body.off( 'click' ).on( 'click', function() { showScreen(); } );
		return false;
	}

	function showScreen( $screen ) {
		$el.removeClass( 'ac-gridview' );
		if( $screen ) {
			// update image and title on the device
			$screenImg.attr( 'src', $screen.find( 'img' ).attr( 'src' ) );
			$screenTitle.text( $screen.find( 'span' ).text() );
		}
	}

	return { init : init };

})();
/* - BASE HTML TEMPLATE
------------------------------------------------- 
	Description: JS Scripts
*/


(function($){
	$(function(){
	

   

    /* PROFILE POPUP STUFF */
    $(".profile-popup *").click(function(e){
      console.log($(this).attr("class"));
      if ($(this).hasClass("profile-twitter-link")){
        // Clicked a twitter link
        e.stopPropagation();
      }
      else{
        // Clicked anywhere else
        $(".profile-popup").fadeOut(500);
        e.stopPropagation();
      }
    });
    $("body").keydown(function(e){  
      if (e.keyCode == 27 && $(".profile-popup").is(":visible")) {
        $(".profile-popup").fadeOut(500);        
      }
    });

    $(".profile-popup-link").click(function(e){
      e.preventDefault();
      
      $(".profile-popup").fadeIn(500);

      return false;
    });

	});
})(jQuery);






		$(function() {
				AppShowcase.init();
			});