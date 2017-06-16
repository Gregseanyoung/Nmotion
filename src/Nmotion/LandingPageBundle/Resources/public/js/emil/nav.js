
/* JQUERY STUFF */
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
        $(".profile-popup").fadeOut(300);
        e.stopPropagation();
      }
    });
    $("body").keydown(function(e){  
      if (e.keyCode == 27 && $(".profile-popup").is(":visible")) {
        $(".profile-popup").fadeOut(300);        
      }
    });

    $(".profile-popup-link").click(function(e){
      e.preventDefault();
      
      $(".profile-popup").fadeIn(300);

      return false;
    });

	});
})(jQuery);


