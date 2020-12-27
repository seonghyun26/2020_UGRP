<?php
  header('Content-Type: text/html; charset=utf-8');
  include('../conn.php');  
  
   
  if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  } else {
    $sql = "SELECT * FROM mark1_test WHERE (shock_date BETWEEN '2020-10-20 16:38:57' AND '2020-10-20 16:44:17')";
    $result_date_acc =  mysqli_query($conn, $sql);
    $result_data_acc_x = mysqli_query($conn, $sql);
    $result_data_acc_y = mysqli_query($conn, $sql);
    $result_data_acc_z = mysqli_query($conn, $sql);    
  }
?>

<!DOCTYPE html>
<html style="font-size: 16px;">
  <title>ACC Graph Test</title>

  <head>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
    <style type="text/css">
      .container {
          width: 80%;
          margin: 15px auto;
      }
      .button {
          border: none;
          color:white;
          padding: 15px 32px;
          text-align: center;
          text-decoration: none;
          display: inline-block;
          font-size: 24px;
          margin: 4px 2px;
      }
      .blue {background-color: skyblue};
    </style>
  </head>

  <body>
    <p><?php
      echo "Searched 2020-10-20 16:38:57 to 2020-10-20 16:44:17";
    ?></p>

    <div class="container">
      <canvas id="acc_Chart" width="1000" height="500"></canvas>
    </div>

    <!-- Acceleration GRAPH -->
    <script>
      var ctx = document.getElementById("acc_Chart");
      var myChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: [<?php 
            while($rows = mysqli_fetch_array($result_date_acc) ){
              echo '"'.$rows['shock_date'].'",';
            }
            ?>],
          datasets: [{
              label: 'Acceleration X',
              data: [<?php
                while($rows_ = mysqli_fetch_array($result_data_acc_x) ){
                  echo '"'.$rows_['acc_x'].'",';
                }
              ?>],
              backgroundColor: [
                'rgba(255, 0, 0, 0.2)',
              ],
              borderColor: [
                'rgba(255, 0, 0, 0.6)',
              ],
              borderWidth: 1
            }, {
              label: 'Acceleration Y',
              data: [<?php
                while($rows_ = mysqli_fetch_array($result_data_acc_y) ){
                  echo '"'.$rows_['acc_y'].'",';
                }
              ?>],
              backgroundColor: [
                'rgba(0, 255, 0, 0.2)',
              ],
              borderColor: [
                'rgba(0, 255, 0, 0.6)',
              ],
              borderWidth: 1
            }, {
              label: 'Acceleration Z',
              data: [<?php
                while($rows_ = mysqli_fetch_array($result_data_acc_z) ){
                  echo '"'.$rows_['acc_z'].'",';
                }
              ?>],
              backgroundColor: [
                'rgba(0, 0, 255, 0.2)',
              ],
              borderColor: [
                'rgba(0, 0, 255, 0.6)',
              ],
              borderWidth: 1
            }
          ]
        },
        options: {
          responsive: true,
          title: {
            display: true,
            text: 'Acceleration Test Graph'
          },
          scales: {
            xAxes: [{
              display: true,
              scaleLabel: {
                display: true,
                labelString: 'Date',
                fontSize: '16',
              }
            }],
            yAxes: [{
              ticks: {
                  beginAtZero: true
              },
              display: true,
              scaleLabel: {
                display: true,
                labelString: 'Value',
                fontSize: '16'
              }
            }]
          }
        }
      });
    </script>


    
  </body>

</html>