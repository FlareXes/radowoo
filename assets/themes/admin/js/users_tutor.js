(function ($) {
  "use strict";

  var table;
  var csrfName = $("#csrf_hash").val();
  var csrf_token = $("#csrf_token").val();

  //datatables
  table = $("#table").DataTable({
    language: {
      info:
        table_showing +
        " _START_ " +
        table_to +
        " _END_ " +
        table_of +
        " _TOTAL_ " +
        table_entries,
      paginate: {
        previous: table_previous,
        next: table_next,
      },
      sLengthMenu: table_show + " _MENU_ " + table_entries,
      sSearch: table_search,
    },

    processing: true, //Feature control the processing indicator.
    serverSide: true, //Feature control DataTables' server-side processing mode.
    order: [],
    ajax: {
      url: BASE_URL + "admin/users/tutor_list",
      type: "POST",
    },

    //Set column definition initialisation properties.
    columnDefs: [
      {
        targets: [0, 4], //first column / numbering column
        orderable: false, //set not orderable
      },
    ],
  });

  $(document).on("click", ".tutor_status", function (e) 
  {
    var user_id = $(this).data('user_id');
    var link = BASE_URL+'admin/users/approve_tutor/'+user_id;

    e.preventDefault(false);
    swal(
      {
        title: are_you_sure,
        text: "Activate Tutor Account",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes Approve",
      },
      function (isConfirm) 
      {
        if (isConfirm == true) 
        {
          window.location.href = link;
        }
      }
    );
  });

  $(document).on("click", ".reject_tutor_account_request", function (e) 
  {
    var user_id = $(this).data('user_id');
    var link = BASE_URL+'admin/users/tutor_request_reject/'+user_id;

    e.preventDefault(false);
    swal(
      {
        title: are_you_sure,
        text: "Reject Tutor Request",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Yes Reject",
      },
      function (isConfirm) 
      {
        if (isConfirm == true) 
        {
          window.location.href = link;
        }
      }
    );
  });

  $(document).on("click", ".reject_tutor_account_request_info", function (e) 
  {
    var user_id = $(this).data('user_id');
    var link = BASE_URL+'admin/users/tutor_account_request_info/'+user_id;

        $.ajax({
              dataType: "json",
              type: "post",
              data: { user_id: user_id, },
              url: link,
              success: function (response) 
              {
                if (response) 
                {
                  // response = JSON.parse(response);
                  console.log(response);
                  if(response.status == "success")
                  {
                    $("#user_qualification_experience_modal").modal('show');
                    $(".user_qualification_and_experience").html(response.data.user_qualification_experience);

                  }
                  else
                  {
                   alert('something went wrong');
                  }
                } 
                else 
                {
                  console.log("error During Remove Image");
                }
              },
              error: function (jqXHR, status, err) 
              {
                console.log(jqXHR);
              },
            });
  
  });

})(jQuery);
