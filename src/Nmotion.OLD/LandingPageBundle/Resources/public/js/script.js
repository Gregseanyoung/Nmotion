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