(function ($) {
  "use strict";



    var is_evaluation_test = $('.is_evaluation_test').val();
    var is_last_question = $('.is_last_question').val();
    var is_first_question = $('.is_first_question').val();
    var is_last_question_answerd = $('.is_last_question_answerd').val();



  $(document).ready(function () 
  {

      $(".submit_test").on("click", function (q) {
        q.preventDefault();
        submit_test();
      });

      $(".stop_this_quiz").on("click", function (q) {
        q.preventDefault();
        submit_test();
      });

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
        $(".next_quiz").hide();
      }

      if(is_last_question == "YES" && is_last_question_answerd == "YES")
      {
        setTimeout(
          function() 
          {
            submit_test();
            //do something special
          }, 1000);
        
      }

  });


  function evulation_test(input)
  {
    
    if(is_evaluation_test == "YES")
    {
      var current_question_json = $('.current_question_json').val();
      var current_question_answers_string = $('.current_question_answers_string').val();
      var current_question_answers_string_for_fillintheblank = $('#current_question_answers_string_for_fillintheblank').val();
      var current_question_answers_string_keys = $('.current_question_answers_string_keys').val();
      
      current_question_json = JSON.parse(current_question_json);
      var checked_answer = $(".answer_input:checked").val();
      var rendor_content = $('.render-content').val();
      
      var question_type_is_correct_value = $("#question_type_is_correct_value").val();
      var question_type_is_match = $(".question_type_is_match_value").val();
      var correct_answers = "";
      var correct_string = "";
      var is_correct = false;
      
      if(question_type_is_match == "YES")
      { 
        var jj = 0;
        correct_string = current_question_answers_string_keys;
        $('.answer_input_box').each( function(ww)
        {
          jj++;
          var index_values = $(this).val();
          var values = $(this).data('value')
          ;
          correct_answers += jj > 1 ? ","+index_values : index_values;
        });
         
        if(current_question_answers_string_keys == correct_answers)
        {
          is_correct = true;
        }
      }
      else
      {
        var i = 0;
        if(question_type_is_correct_value != 'text')
        {


          $(".answer_input:checked").each( function(ww)
          {
            i++;
            correct_string = current_question_answers_string;
            var values = $(this).val();
            correct_answers += i > 1 ? ","+values : values;
          });

          if(current_question_answers_string == correct_answers)
          {
            is_correct = true;
          }
        }
        else
        {
          correct_answers = $("#queston_choies_type_text_id").val();
          correct_answers = correct_answers.toString();
          correct_answers = correct_answers.trim();
          correct_string = current_question_answers_string_for_fillintheblank.toString();
          correct_string = current_question_answers_string_for_fillintheblank.trim();

          if(current_question_answers_string_for_fillintheblank == correct_answers)
          {
            is_correct = true;
          }
        }

        

      }
      
        var correct_string_html = correct_string;
        var type_box = is_correct == true ? "success" : "error";
        swal(
          {
            text: "Is The Correct Answer",
            title: correct_string,
            html: correct_string_html,
            type: type_box,
          }, 
          function() 
          {
              $("#myform").append($(input));
              $("#myform").submit();
          }
        );
    }
    else
    {
      $("#myform").append($(input));
      $("#myform").submit();
    }
    
  }

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





  $(document).ready(function()
  {

    if(is_answered_or_disable && is_last_question_of_test == false)
    {

          swal({
                title: already_attemped,
                text: you_cant_resubmit_this_quiz_answer,
                type: "error",
                showCancelButton: false,
                confirmButtonText: resume_quiz,
              },
              function (isConfirm) 
              {
                if(quiz_last_attempted_question_url)
                {
                  window.location.href = quiz_last_attempted_question_url;
                }
                
              });

          setTimeout(
          function() 
          {
            if(quiz_last_attempted_question_url)
            {
              window.location.href = quiz_last_attempted_question_url;
            }
          }, 2000);
    }

  $(".answer_given").on("click", function (q) 
  {
    q.preventDefault();
    
    var checked_or_not = $(".answer_input:checked").val();
    
    var answer_input_field = $(".answer_input_field").val();
    var answer_text_field = $(".answer_text_field").val();

    var question_type_is_match_value = $(".question_type_is_match_value").val();
    var correct_match_index = false;
    if(question_type_is_match_value == "YES")
    {
      var correct_match_index = true;
    }
    $(".correct_match_index").each(function()
    {
      if($(this).val() == "")
      {
        correct_match_index = false;
        no_answer_given_yet = "NO ANSWER GIVEN OR FILL ALL CHOIES INDEX";
      }
    });
    
    if(checked_or_not || answer_input_field || answer_text_field || correct_match_index == true) 
    {
      if($(this).hasClass("save_or_next_quiz"))
      {
        var input = $("<input>").attr("type", "hidden").attr("name", "save_or_next_quiz").val("Save & Next");

        evulation_test(input);
      }

      if($(this).hasClass("mark_or_next_quiz"))
      {
        var input = $("<input>").attr("type", "hidden").attr("name", "mark_or_next_quiz").val("Mark for Review and Next");
        evulation_test(input);
      }

      if($(this).hasClass("mark_for_answer_and_next"))
      {
        var input = $("<input>").attr("type", "hidden").attr("name", "mark_for_answer_and_next").val("Mark for Answer and Next");
        evulation_test(input);
      }
      console.log(checked_or_not);

    } 
    else 
    {
      swal({
        title: no_answer_given_yet, 
      });
    }
  });


    var not_attemp = $('.not-attemp').val();
    var correct = $('.correct').val();
    var wrong_answer = $('.wrong-answer').val();
    var myChart_div = document.getElementById('myChart');


    if(myChart_div)
    {
      var ctx = myChart_div.getContext('2d');
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