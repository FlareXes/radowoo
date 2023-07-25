(function ($) {
  "use strict";

  //select2 tool jquery
  $(document).ready(function () {
    $(".select_dropdown").select2();
  });



  Dropzone.autoDiscover = false;
  $(function () {
    var myDropzone = $("#imageupload").dropzone({
      url: BASE_URL + "admin/quiz/dropzone-file",
      maxFilesize: 5,
      maxFiles: 5,
      renameFile: function (file) {
        var dt = new Date();
        var time = dt.getTime();
        return time + convertToSlug(file.name);
      },
      addRemoveLinks: true,
      dictResponseError: "Server not Configured",
      acceptedFiles: ".png,.jpg,.gif,.bmp,.jpeg",
      timeout: 50000,

      removedfile: function (file) {
        var name = file.upload.filename;

        $.ajax({
          type: "POST",
          url: BASE_URL + "admin/quiz/dropzone-file-remove",
          data: {
            filename: name
          },

          success: function (data) {
            if (data) {
              data = JSON.parse(data);
              console.log("File has been successfully removed!!" + data);
              $('.featured_image_block :input[value="' + data + '"]').remove();
            } else {
              alert("error");
            }
          },
          error: function (e) {
            console.log(e);
          },
        });
        var fileRef;
        return (fileRef = file.previewElement) != null ?
          fileRef.parentNode.removeChild(file.previewElement) :
          void 0;
      },

      success: function (file, response) {
        response = JSON.parse(response);
        if (response.name) {
          if ($(".featured_image_input").length) {
            $(".featured_image_block")
              .last()
              .after(
                '<input type="hidden" name="featured_image[]" class="form-control featured_image_input" value="' +
                response.name +
                '">'
              );
          } else {
            $(".featured_image_block").append(
              '<input type="hidden" name="featured_image[]" class="form-control featured_image_input" value="' +
              response.name +
              '">'
            );
          }
        } else {
          alert("error");
        }
      },

      error: function (file, response) {
        return false;
      },

      init: function () {
        var self = this;
        // config
        self.options.addRemoveLinks = true;
        self.options.dictRemoveFile = "Delete";
        //New file added
        self.on("addedfile", function (file) {
          console.log("new file added ", file);
        });
        // Send file starts
        self.on("sending", function (file, xhr, formData) {
          formData.append([csrf_Name], csrf_Hash);
          console.log("upload started", file);
          $(".meter").show();
        });

        // File upload Progress
        self.on("totaluploadprogress", function (progress) {
          console.log("progress ", progress);
          $(".roller").width(progress + "%");
        });

        self.on("queuecomplete", function (progress) {
          $(".meter").delay(999).slideUp(999);
        });

        // On removing file
        self.on("removedfile", function (file) {
          console.log(file);
        });

        self.on("maxfilesexceeded", function (file) {
          // alert("No more files please!")
          alert("No more files please !");
          this.removeFile(file);
        });
      },
    });
  });

  function convertToSlug(Text) {
    return Text.toLowerCase().replace(/ /g, "-");
  }

  var table;
  var csrfName = $("#csrf_hash").val();
  var csrf_token = $("#csrf_token").val();

  $(document).ready(function () {
    //datatables
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
        url: BASE_URL + "admin/QuestionController/question_list",
        type: "POST",
      },

      //Set column definition initialisation properties.
      columnDefs: [{
        targets: [0, 3], //first column / numbering column
        orderable: false, //set not orderable
      }, ],
    });
  });

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

  $(".popup").on("click", function () {
    $("#imagepreview").attr("src", $(this).attr("src"));
    $("#imagemodal").modal("show");
  });

  $(".delete_featured_image").on("click", function (e) {
    e.preventDefault();

    var quiz_id = $(this).data("quiz_id");
    var featured_image_name = $(this).data("image_name");
    var img_box = $(this).closest(".col-1");

    if (quiz_id && featured_image_name) {
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
            $.ajax({
              dataType: "json",
              type: "post",
              data: {
                quiz_id: quiz_id,
                featured_image_name: featured_image_name,
              },
              url: BASE_URL + "admin/quiz/delete-image/" + quiz_id,
              success: function (response) {
                if (response) {
                  console.log("image Removed Success");
                  img_box.remove();
                } else {
                  console.log("error During Remove Image");
                }
              },
              error: function (jqXHR, status, err) {
                console.log(jqXHR);
              },
            });
          }
        }
      );
    } else {
      return false;
    }
  });

  $(document).ready(function () {
    $(".add-more").on('click',function () {
      var addmore_index = parseFloat($(this).attr("data-index"));
      var add_next = addmore_index + 1;
      $(this).attr("data-index", add_next);

      $(".copy_ticket_section .togle_button .is_correct").attr(
        "name",
        "is_correct[" + add_next + "]"
      );
      $(".copy_ticket_section .choice_block .choices").attr(
        "name",
        "choices[" + add_next + "]"
        
      );
      $(".copy_ticket_section .choice_block .choices").addClass('editor');

      var html = "";
      html = $(".copy_ticket_section").html();
      $(".after_ticket_section").append(html);
      $(".copied_ticket_section textarea.editor").each(function(i)
      {
          $(".editor").eq(i).summernote(
          {
              height: 300,
              callbacks: {
                          //onImageUpload: function (image) 
                          onImageUpload: function (image) {
  
                            uploadImage_by_loop(image[0],i);
                          }
                      },
          });
      });
    });  

    function uploadImage_by_loop(image,i) 
  {
    console.log('uploadImage_by_loop'); 
    var data = new FormData();
    data.append("image", image);
    data.append(csrf_Name,csrf_Hash);
    
    $.ajax({
        url: BASE_URL+"admin/quizcontroller/summernoteimg",
        cache: false,
        contentType: false,
        processData: false,
        data: data,
        type: "post",
        success: function (url) 
        {
          url = JSON.parse(url);
            if (url.status == 1) {
                var image = $('<img>').attr('src',url.path);
                $("textarea.editor").eq(i).summernote("insertNode", image[0]);
                 
            }
            else
            {
              console.log(url.message);
            }
        },
        error: function (data) {
            console.log(data);
        }
    });
  }

    $(document).on("click", ".remove_block_btn", function () {
      $(this).parents(".copied_ticket_section").remove();
    });

    $(document).on("click", ".add-more_update", function () {
      var html = $(".copy_ticket_section").html();
      swal({
          title: "Are you sure?",
          text: "You Need To Update Company Also !",
          type: "warning",
          showCancelButton: true,
          confirmButtonColor: "#3085d6",
          cancelButtonColor: "#d33",
          confirmButtonText: "Yes, Add More Field !",
        },
        function (isConfirm) {
          if (isConfirm == true) {
            $(".after_ticket_section").append(html);
          }
        }
      );
    });

    $(document).on("click", ".remove_block_btn_update", function () {
      var parent_div = $(this).parents(".copied_ticket_section");

      parent_div.remove();
    });



    $(document).on("change", ".question_type_is_match", function (e) 
    {
      var question_type_is_match = $(this).val();
      if(question_type_is_match)
      {
        if(question_type_is_match == "YES")
        {
          $(".after_ticket_section").hide();
          $(".muliple_selection_work").show();
        }
        else
        {
          $(".after_ticket_section").show();
          $(".muliple_selection_work").hide();
        }
      }
    });

    



      $(document).on('click',".add-more-cross",function () 
      {
          var addmore_index = parseFloat($(this).attr("data-index"));
          var add_next = addmore_index + 1;
          $(this).attr("data-index", add_next);

          $(".copy_cross_section .match_question_choices_one").attr(
            "name",
            "mark_choices[" + add_next + "]"
          );
          $(".copy_cross_section .match_question_choices_two").attr(
            "name",
            "mark_is_correct[" + add_next + "]"
          );
          $(".copy_cross_section .match_question_display_index").attr(
            "name",
            "is_correct_display_order[" + add_next + "]"
          );
          $(".copy_cross_section .remove_cross_block_btn").attr(
            "data-index",
            add_next,
          );
          $(".copy_cross_section .match_question_choices_two_index").text(add_next);

          var html = "";
          html = $(".copy_cross_section").html();
          $(".after_cross_input_section").append(html);
      });

      $(document).on("click", ".remove_cross_block_btn", function () 
      {

        var parent_div  =  $(this).parents(".copied_cross_section");
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
        
        swal({
          title: "Are you sure?",
          text: "Next choices will Remove Together !",
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
            $(".add-more-cross").attr("data-index", removed_index);
            parent_div.nextAll('.copied_cross_section').remove();
            parent_div.remove();
          }
        }
      );
  });


  });

$(document).on("click", ".delete_attachment", function (e) {
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