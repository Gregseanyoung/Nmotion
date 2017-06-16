$(function() {
	initTabs();
});


function initTabs(){
	$(".tabsBlock").each(function(){
		var block = $(this);

		block.find(".tabsItems li").click(function(){
			var li = $(this);

			li.parents(".tabsItems").eq(0).find("li").removeClass("active");
			li.addClass("active");

			li.parents(".tabsBlock").eq(0).find(".tabItem").removeClass("active");
			li.parents(".tabsBlock").eq(0).find(".tabItem").eq(li.index()).addClass("active");

		});
	});
}
function showPopup(id){
	$("#podlojka").css("display", "block");
	$("#" + id).css("display", "block");
}
function hidePopup(){
	$("#podlojka").css("display", "none");
	$(".popupBlock").css("display", "none");
}

function initFaqBlocks(){
    $(".faqItemQuestionLink").each(function(){
        $(this).attr("rel", $(this).attr("href").substr(1, $(this).attr("href").length));
        $(this).removeAttr("href");
        $(this).click(function(){
            fleXenv.scrollTo($(this).attr("rel"));
            return false;
        });
    });
}

function showVideoPopup(youtubeId){
    showPopup('popupVideo');
    $("#popupVideo").find("div.contactInfoPopup").html('<iframe width="545" height="315" src="https://www.youtube.com/embed/' + youtubeId + '/?modestbranding=1&rel=0" frameborder="0" allowfullscreen></iframe>');
    $("#popupVideo").css("top", ($(window).height() - $("#popupVideo").height())/2 + "px");
}

function getElementPosition(el){
    var elem = el;

    if(elem){
        var w = elem.offsetWidth;
        var h = elem.offsetHeight;

        var l = 0;
        var t = 0;

        while (elem)
        {
            l += elem.offsetLeft;
            t += elem.offsetTop;
            elem = elem.offsetParent;
        }

        return {"left":l, "top":t, "width": w, "height":h};
    }
    else{
        return {"left":0, "top":0, "width": 0, "height":0};
    }
}
