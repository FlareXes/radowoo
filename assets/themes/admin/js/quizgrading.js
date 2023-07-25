(function ($) {
  "use strict";

  //select2 tool jquery  
  $(document).ready(function () 
  {
    $(".select_dropdown").select2();




      $(document).on('click',".add_more_quiz_level",function () 
      {
          var addmore_index = parseFloat($(this).attr("data-index"));
          var add_next = addmore_index + 1;
          $(this).attr("data-index", add_next);

          $(".section_quiz_levels_for_copy .added_parent_section_of_level_row .quiz_level_name").attr(
            "name",
            "quiz_level_name[" + add_next + "]"
          );

          $(".section_quiz_levels_for_copy .added_parent_section_of_level_row .quiz_level_marks").attr(
            "name",
            "quiz_level_marks[" + add_next + "]"
          );

          $(".section_quiz_levels_for_copy .added_parent_section_of_level_row .remove_quiz_leve_row_btn").attr(
            "data-index",
            add_next,
          );


          var html = "";
          html = $(".section_quiz_levels_for_copy").html();
          $(".section_quiz_levels").append(html);
      });




      $(document).on("click", ".remove_quiz_leve_row_btn", function () 
      {

        var parent_div  =  $(this).parents(".added_parent_section_of_level_row");
        var removed_index = $(this).data("index");
        removed_index = parseFloat(removed_index);
        removed_index = removed_index-1;
        if(removed_index && removed_index > 0)
        {
          removed_index = removed_index;
        }
        else
        {
          removed_index = 1;
        }
        console.log(parent_div);
        console.log(removed_index);
        
        swal({
              title: "Are you sure?",
              text: "Next All Level will Remove Together !",
              type: "warning",
              showCancelButton: true,
              confirmButtonColor: "#3085d6",
              cancelButtonColor: "#d33",
              confirmButtonText: "Yes,Remove !",
            },
            function (isConfirm) 
            {
              if (isConfirm == true) 
              {
                $(".add_more_quiz_level").attr("data-index", removed_index);
                parent_div.nextAll('.added_parent_section_of_level_row').remove();
                parent_div.remove();
              }
            }
          );
     
      });



  });

  var table;
  var csrfName = $("#csrf_hash").val();
  var csrf_token = $("#csrf_token").val();

  $(document).ready(function () 
  {
    table = $("#table").DataTable({
      language: {
        info: table_showing +
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
        url: BASE_URL + "admin/quiz-grading-list/",
        type: "POST",
      },

      //Set column definition initialisation properties.
      columnDefs: [{
        targets: [0, 2], //first column / numbering column
        orderable: false, //set not orderable
      }, ],
    });

  


  //product and variant delete with sweetalert
  $("body").on("click", ".common_delete", function (e) 
  {
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

  $(".popup").on("click", function () {
    $("#imagepreview").attr("src", $(this).attr("src"));
    $("#imagemodal").modal("show");
  });



    $(document).on("click", ".remove_block_btn_update", function () {
      var parent_div = $(this).parents(".copied_ticket_section");
      swal({
          title: are_you_sure,
          text: remove_from_company,
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: yes_remove_it,
        },
        function (isConfirm) {
          if (isConfirm == true) {
            parent_div.remove();
          }
        }
      );
    });
  });
})(jQuery);