(function ($) {
  "use strict";

  $(document).ready(function () {
    /**
     * Delete a user
     */
    $(".btn-delete-user").on("click", function () 
    {
      var assisn_to_new_user = $(this).attr("data-assisn_to_new_user");
      var user_to_be_delete = $(this).attr("data-id");
      if(assisn_to_new_user == 'YES')
      {
          var user_to_be_asign = $("#modal-"+user_to_be_delete+" #new_user_id_"+user_to_be_delete).val();
          var user_to_be_asign = parseInt(user_to_be_asign);

          if(user_to_be_asign > 0)
          {
            window.location.href = BASE_URL + "/admin/users/delete/" + user_to_be_delete + "/"+user_to_be_asign;
          }
          else
          {
            alert('something went wrong');
          }
      }
      else
      {
        window.location.href = BASE_URL + "/admin/users/delete/" + user_to_be_delete;
      }
    });
  });

  $(".popup").on("click", function () {
    $("#imagepreview").attr("src", $(this).attr("src"));
    $("#imagemodal").modal("show");
  });


    $(document).on("click", ".common_delete", function (e) {
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


})(jQuery);
