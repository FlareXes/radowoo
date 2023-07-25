(function ($) {
  "use strict"; 

  $(".statrt_quiz_btnnn").on("click", function (e) {
    var link = $(this).attr("href");
    var quiz_id = $(this).data("quiz_id");
    e.preventDefault(false);

    if (quiz_id && link) {
      $.ajax({
        url: link,
        type: "POST",
        data: {
          quiz_id: quiz_id
        },
        success: function (result) {
          result = JSON.parse(result);
          if (result.success) {} else {}
        },
        error: function (e) {
          console.log(e);
        },
      });
    } else {}
  });

  $("#Quiz_filter").on("change", function (e) {
    $("#Quiz_filter_form").submit();
  });

  $(".quiz_running").on("click", function (e) {
    var link = $(this).attr("href");

    e.preventDefault(false);

    swal({
        title: quiz_already_running,
        text: stop_running_quiz_msg,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: resume_quiz,
        cancelButtonText: stop_quiz,
      },
      function (isConfirm) {
        if (isConfirm == true) {
          window.location.href = link;
        } else {
          var session_quiz_id = $(".session_quiz_id").val();
          window.location.href = BASE_URL + "result/" + session_quiz_id;
        }
      }
    );
  });


  $(document).on("click",".myBtn",function(){

    var payment_id = $(this).data("payment_id");
    var encrypted_payment_id = $(this).data("encrypted_payment_id");
    $.ajax({
            url:BASE_URL+"view-payment-detail",
            type:"POST",
            data:{payment_id:payment_id},
            success: function (response)
            {
              response = JSON.parse(response);
              $('#myModal').modal('show');
              $('.payment-data').html(response);
              $('.invoice-bill').attr("href", BASE_URL+"invoice/"+encrypted_payment_id);
            },
            error: function(e)
            {
              console.log(e);
            }
        });     
         
    });

  $('.filter-click').on('click',function(){

    var category_slug = $('.category-slug').val();
    var basic_url = BASE_URL+'category/'+category_slug+'?';
    var filter_value = $(this).val();
    var url_paramiter = $(this).data('field_slug');
    var seasoning = '', tempArray = [];
    if(filter_value)
    {
      $('input[name="subcategory"]:checked').each(function() {
          tempArray.push($(this).val());
      });

      if(tempArray.length !== 0)
      {
         seasoning+= 'sub-category='+tempArray.join('~~||~~');
         tempArray = [];
      }

      $('input[name="rattings"]:checked').each(function() {
          tempArray.push($(this).val());
      });

      if(tempArray.length !== 0)
      {
         seasoning+= '&rating='+tempArray.toString();
         tempArray = [];
      }

      $('input[name="price"]:checked').each(function() {
          tempArray.push($(this).val());
      });

      if(tempArray.length !== 0)
      {
         seasoning+= '&price='+tempArray.toString(); 
      }

      //console.log(new_url+seasoning);
      var new_url = basic_url+seasoning;
      //var url = new URL(window.location);
      
      // url.searchParams.set(url_paramiter, seasoning); // setting your param
      //var newUrl = decodeURIComponent(url.href+seasoning);
      //console.log(newUrl); 
      window.location.href = new_url;
    }

  });  
})(jQuery);