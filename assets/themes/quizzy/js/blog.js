(function ($) {
  "use strict";

  $(".like-post i").on("click", function (e) {
  	e.preventDefault();
    var post_id = $(this).data("post_id");
    var element = $(this);
    // if($(this).hasClass("text-muted")){
    	$.ajax({
	        url: BASE_URL + "Blog_Controller/like_or_unlike_post",
	        type: "POST",
	        data: {post_id: post_id },
	        success: function (result) 
	        {
	        	result = JSON.parse(result);
	        	if (result.status == 'success') 
	        	{
	        		if(result.action == "like")
	        		{
	        			element.removeClass("text-muted");
	        			element.addClass("text-success");
	        		}
	        		else
	        		{
	        			element.removeClass("text-success");
			            element.addClass("text-muted");
	        		}

	        		if(result.total_like > 0)
	        		{
	        			element.next(".like-quiz-count").text(result.total_like);
	        		}
	        		else
	        		{
	        			element.next(".like-quiz-count").text('');
	        		}
		        } 
		        else if (result.status == "redirect") 
		        {
		            window.location.href = BASE_URL + "login";
		        } 
		        else
		        {
		            alert(result.msg);
		        }
	        },
	        error: function (e) 
	        {
	        	console.log(e);
	        },
        });

     });

})(jQuery);  