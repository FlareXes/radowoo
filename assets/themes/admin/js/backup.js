  //product and variant delete with sweetalert
  $("body").on("click", ".common_delete", function (e) {
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