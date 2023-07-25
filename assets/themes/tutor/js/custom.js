/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 *
 */
(function ($) {
  "use strict";
  // this script needs to be loaded on every page where an ajax POST
  $.ajaxSetup({
    data: {
      [csrf_Name]: csrf_Hash,
    },
  });

  $(document).on("click", ".common_copy_record", function (e) {
    var link = $(this).attr("href");

    e.preventDefault(false);
    swal({
        title: are_you_sure,
        text: "Copy This Record",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#3085d6",
        cancelButtonColor: "#d33",
        confirmButtonText: "Copy Now",
      },
      function (isConfirm) {
        if (isConfirm == true) {
          window.location.href = link;
        }
      }
    );
  });
  $("#table").on("hover", function (e) {
    $(function () {
      $('[data-toggle="tooltip"]').tooltip();
    });
  });

  $(document).ready(function () {
    $(function () {
      $('[data-toggle="tooltip"]').tooltip();
    });

    //datatables
    var user_id = $(".user-id").val();
    var user_table;

    user_table = $("#user-quiz-history-table").DataTable({
      processing: true, //Feature control the processing indicator.
      serverSide: true, //Feature control DataTables' server-side processing mode.
      order: [],
      ajax: {
        url: BASE_URL + "tutor/users/user_quiz_history_list/" + user_id,
        type: "POST",
      },

      dom: "lBfrtip",
      buttons: ["copy", "csv", "excel", "pdf", "print"],
      lengthMenu: [
        [10, 25, 50, -1],
        [10, 25, 50, "All"],
      ],
      lengthChange: true,

      //Set column definition initialisation properties.
      columnDefs: [{
        targets: [0], //first column / numbering column
        orderable: false, //set not orderable
      }, ],
    });

    /**
    var not_attemp = $('.not-attemp').val();
    var correct = $('.correct').val();
    var wrong_answer = $('.wrong-answer').val();
    var ctx = document.getElementById('myChart').getContext('2d');
    var myChart = new Chart(ctx, {
      type: 'pie',
      data: {
          labels: ['Not Attemp', 'Right Answer', 'Wrong Answer'],
          datasets: [{
              label: '# of Question',
              data: [not_attemp, correct, wrong_answer,],
              backgroundColor: [
                  'rgba(255, 99, 132, 0.2)',
                  'rgba(54, 162, 235, 0.2)',
                  'rgba(255, 206, 86, 0.2)',
                  'rgba(75, 192, 192, 0.2)',
                  'rgba(153, 102, 255, 0.2)',
                  'rgba(255, 159, 64, 0.2)'
              ],
              borderColor: [
                  'rgba(255, 99, 132, 1)',
                  'rgba(54, 162, 235, 1)',
                  'rgba(255, 206, 86, 1)',
                  'rgba(75, 192, 192, 1)',
                  'rgba(153, 102, 255, 1)',
                  'rgba(255, 159, 64, 1)'
              ],
              borderWidth: 1
          }]
      },
      options: {
        scales: {
              xAxes: [{
                  display: false
              }],
              yAxes: [{
                  display: false
              }]
          }
      }
    });
    **/


   $(document).ready(function()
   {
     if ($("textarea.editor").length) 
     {
       $("textarea.editor").each(function(i)
       {
         var id = $(this).attr("id");
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
     }
     
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


   $(document).ready(function()
   {
     
     $('.editor').summernote(
     {
       height: 300,
       width: '100%',
      
       callbacks: { onImageUpload: function (image) { uploadImage(image[0]); },
           
       },  
     });
     

   });
   


   function uploadImage(image) {
     
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
                       
                       $("textarea.editor").summernote("insertNode", image[0]);
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



    $(function() 
    {
      var Grid = function(width, height) 
      {
        var not_attemp = $('.not-attemp').val();
        var correct = $('.correct').val();
        var wrong_answer = $('.wrong-answer').val();
        var ctx = document.getElementById('myChart').getContext('2d');
        var myChart = new Chart(ctx, {
          type: 'pie',
          data: {
              labels: ['Not Attemp', 'Right Answer', 'Wrong Answer'],
              datasets: [{
                  label: '# of Question',
                  data: [not_attemp, correct, wrong_answer,],
                  backgroundColor: [
                      'rgba(255, 99, 132, 0.2)',
                      'rgba(54, 162, 235, 0.2)',
                      'rgba(255, 206, 86, 0.2)',
                      'rgba(75, 192, 192, 0.2)',
                      'rgba(153, 102, 255, 0.2)',
                      'rgba(255, 159, 64, 0.2)'
                  ],
                  borderColor: [
                      'rgba(255, 99, 132, 1)',
                      'rgba(54, 162, 235, 1)',
                      'rgba(255, 206, 86, 1)',
                      'rgba(75, 192, 192, 1)',
                      'rgba(153, 102, 255, 1)',
                      'rgba(255, 159, 64, 1)'
                  ],
                  borderWidth: 1
              }]
          },
          options: {
            scales: {
                  xAxes: [{
                      display: false
                  }],
                  yAxes: [{
                      display: false
                  }]
              }
          }
        });
      }
    });


    // $(document).on('shown.bs.modal', function(e) {
    //   $(this).find('.question-area textarea').autogrow();
    //   $(this).find('.solution-area textarea').autogrow();
    // });

    function printData()
    {
      var print_div = document.getElementById("print-area");
      var print_area = window.open();
      print_area.document.write(print_div.innerHTML);
      print_area.document.close();
      print_area.focus();
      print_area.print();
      print_area.close();

     //  var printContents = document.getElementById("print-area").innerHTML;
     // var originalContents = document.body.innerHTML;

     // document.body.innerHTML = printContents;

     // window.print();

     // document.body.innerHTML = originalContents;

    }

    $(document).on('click','.print-btn',function(){
      printData();
    });

  });

  
})(jQuery);