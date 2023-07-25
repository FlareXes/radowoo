  var table;
  var csrfName = $("#csrf_hash").val();
  var csrf_token = $("#csrf_token").val();

  $(document).ready(function () {

    


    //datatables
    var quiz_id = $('.quiz_id').val();
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
        url: BASE_URL + "tutor/ReportController/quiz_history_list",
        type: "POST",
        data: {quiz_id:quiz_id},
      },
      aLengthMenu: [
        [25, 50, 100, 200, -1],
        [25, 50, 100, 200, "All"]
      ],

      //Set column definition initialisation properties.
      columnDefs: [{
        targets: [0,8], //first column / numbering column
        orderable: false, //set not orderable
      }, ],
    });

    var reportquestiontable;
    reportquestiontable = $("#reportquestiontable").DataTable({
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
      url: BASE_URL + "admin/ReportController/report_question_list",
      type: "POST",
    },

    //Set column definition initialisation properties.
    columnDefs: [{
      targets: [], //first column / numbering column
      orderable: false, //set not orderable
    }, ],
  });

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

    $(document).on('shown.bs.modal', function(e) {
      $(this).find('.question-area textarea').autogrow();
      $(this).find('.solution-area textarea').autogrow();
    });

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