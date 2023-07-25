

  $(document).ready(function()
  {


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




    var userregistration = document.getElementById("userregistration");
  
    Chart.defaults.global.defaultFontFamily = "Lato";
    Chart.defaults.global.defaultFontSize = 18;

    var speedData = {
      labels: months,
      datasets: [
                  {
                    label: "User Registrations Per Month",
                    data: month_wise_users,
                    backgroundColor: "rgba(0, 0, 0, 0)",
                    borderColor: "#fb0404",
                  },
                  {
                    label: "Tutor Registrations Per Month",
                    data: month_wise_tutors,
                    backgroundColor: "rgba(0, 0, 0, 0)",
                    borderColor: "#727cf5",
                  }
      ]
    };

    var chartOptions = {
      legend: {
        display: true,
        position: 'top',
        labels: {
          boxWidth: 80,
          fontColor: 'black'
        }
      }
    };

    var lineChart = new Chart(userregistration, {
      type: 'line',
      data: speedData,
      options: chartOptions
    });





    var adminrevenu = document.getElementById("adminrevenu");
  
    Chart.defaults.global.defaultFontFamily = "Lato";
    Chart.defaults.global.defaultFontSize = 18;

    var speedData = {
      labels: months,
      datasets: [{
        label: "Payments Per Month",
        backgroundColor: "rgba(114, 124, 245, 0.3)",
        borderColor: "#727cf5",
        data: month_wise_payments,
      }]
    };

    var chartOptions = {
      legend: {
        display: true,
        position: 'top',
        labels: {
          boxWidth: 80,
          fontColor: 'black'
        }
      }
    };

    var lineChart = new Chart(adminrevenu, {
      type: 'line',
      data: speedData,
      options: chartOptions
    });



    var new_content_added = document.getElementById("new_content_added");
  
    Chart.defaults.global.defaultFontFamily = "Lato";
    Chart.defaults.global.defaultFontSize = 18;

    var speedData = {
      labels: months,
      datasets: [
          {
            label: "quizes",
            backgroundColor: "rgba(0, 0, 0, 0)",
            borderColor: "#fb0404",
            data: month_wise_quizess,
          },
          {
            label: "materials",
            backgroundColor: "rgba(0, 0, 0, 0)",
            borderColor: "#997cf5",
            data: month_wise_materials,
          },
      ]
    };

    var chartOptions = {
      legend: {
        display: true,
        position: 'top',
        labels: {
          boxWidth: 80,
          fontColor: 'black'
        }
      }
    };

    var lineChart = new Chart(new_content_added, {
      type: 'line',
      data: speedData,
      options: chartOptions
    });



  });
