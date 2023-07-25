(function ($) {
  "use strict";

  $(".submit_test").on("click", function (q) {
    q.preventDefault();
    submit_test();
  });

  var is_last_question = $('.is_last_question').val();
  var is_first_question = $('.is_first_question').val();
  var is_last_question_answerd = $('.is_last_question_answerd').val();

  if(is_first_question == "YES")
  {
    $(".preview_quiz").prop('disabled', true);
  }

  if(is_last_question == "YES")
  {
    $(".save_or_next_quiz").text("");
    $(".save_or_next_quiz").append("<i class='fe fe-save'></i> Save Quesion");
    $(".mark_or_next_quiz").text("");
    $(".mark_or_next_quiz").append("<i class='fe fe-check-circle'></i> Mark For Review");
    $(".next_quiz").prop('disabled', true);
  }

  if(is_last_question == "YES" && is_last_question_answerd == "YES")
  {
    submit_test();
  }

  // $(".save_or_next_quiz").on("click", function (q) 
  // {
    

     
  // });


  function submit_test()
  {
    var test_submit = "submit";
    $.ajax({
      type: "POST",
      url: BASE_URL + "test-submit-request",
      data: {
        test_submit: test_submit
      },

      success: function (data) {
        if (data) {
          data = JSON.parse(data);
          if (data.status == "success") {
            swal({
                title: are_you_sure,
                text: total_attemp + " " + data.attemp,
                type: "warning",
                showCancelButton: true,
                confirmButtonColor: "#3085d6",
                cancelButtonColor: "#d33",
                confirmButtonText: yes_submit_now,
              },
              function (isConfirm) {
                if (isConfirm == true) {
                  var input = $("<input>")
                    .attr("type", "hidden")
                    .attr("name", "submit_test")
                    .val("submit_test");

                  $("#myform").append($(input));
                  $("#myform").submit();
                }
              }
            );
          } else {
            alert(data.msg);
          }
        } else {
          alert("Server Error");
        }
      },
      error: function (e) {
        console.log(e);
      },
    });
  }

  $(".answer_given").on("click", function (q) {
    var checked_or_not = $(".answer_input:checked").val();
    if (checked_or_not) {
      console.log(checked_or_not);
    } else {
      q.preventDefault(false);

      swal({
        title: no_answer_given_yet, 
      });
    }
  });
  $(document).ready(function(){
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
  });

$(function() {

  $(this).find('.question-area textarea').autogrow();

})

$(document).on('shown.bs.modal', function(e) { //background-image: white url("../images/sunny.png");
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




})(jQuery); 