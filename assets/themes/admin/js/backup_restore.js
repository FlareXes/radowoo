(function ($) {
  "use strict";
  
  $("body").on("click", ".blog_cat_delete", function (e) {
    var link = $(this).attr("href");

    e.preventDefault(false);
    swal({
        title: are_you_sure,
        text: permanently_deleted,
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: yes_delere_it,
      },
      function (isConfirm) {
        if (isConfirm == true) {
          window.location.href = link;
        }
      }
    );
  });

  $("body").on("click", ".restore", function (e) {
    var link = $(this).attr("href");
    

    e.preventDefault(false);
    swal({
        title: are_you_sure,
        text: "You want to restore your database",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes",
      },
      function (isConfirm) {
        if (isConfirm == true) {
          window.location.href = link;
        }
      }
    );
  });

  $(document).ready(function()
  {
    var type = $(".data_for").val();
    $(document).on('change','#category_id',function(e){
      var category_id  = $(this).val();
      if(category_id && type)
      {
        window.location.href = BASE_URL+"admin/backup/export/"+type+"/"+$(this).val();
      }
    });

    var temp_site_update_path = $("#temp_site_update_path").val();
    if(temp_site_update_path)
    {
          setTimeout(
          function() 
          {
            window.open(temp_site_update_path); 
            // window.location.href = temp_site_update_path;
            //do something special
          }, 1000);
        
    }

  });

})(jQuery);  