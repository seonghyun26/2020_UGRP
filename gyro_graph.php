<?php
  header('Content-Type: text/html; charset=utf-8');
  include('conn.php');  
  
  $kickboard_name=$_POST['kickboard_name'];
  if(empty($kickboard_name)) $kickboard_name = '1';

  $start_date=$_POST['start_date'];
  if(empty($start_date)) $start_date = '2020-09-25';
  $mysql_start_date = DateTime::createFromFormat('Y-m-d H:i:s', $start_date.' 00:00:00')->format('Y-m-d H:i:s');

  $end_date=$_POST['end_date'];
  if(empty($end_date)) $end_date = '2020-09-26';
  $mysql_end_date = DateTime::createFromFormat('Y-m-d H:i:s', $end_date.' 23:59:59')->format('Y-m-d H:i:s');

   
  if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  } else {
    echo "CONNECTION SUCCESS\n";
    $sql = "SELECT * FROM mark2 WHERE kickboard = '$kickboard_name' AND (record_date BETWEEN '$mysql_start_date' AND '$mysql_end_date')";
    $result_date_loc =  mysqli_query($conn, $sql);
    $result_data_loc_x = mysqli_query($conn, $sql);
    $result_data_loc_y = mysqli_query($conn, $sql);
    $result_data_loc_z = mysqli_query($conn, $sql);
    $result_date_acc =  mysqli_query($conn, $sql);
    $result_data_acc_x = mysqli_query($conn, $sql);
    $result_data_acc_y = mysqli_query($conn, $sql);
    $result_data_acc_z = mysqli_query($conn, $sql);
    $result_date_ang =  mysqli_query($conn, $sql);
    $result_data_ang_x = mysqli_query($conn, $sql);
    $result_data_ang_y = mysqli_query($conn, $sql);
    $result_data_ang_z = mysqli_query($conn, $sql);
  }
?>

<!DOCTYPE html>
<html style="font-size: 16px;">
  <title>Gyro Graph Test</title>

  <head>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
    <link rel="stylesheet" href="css/button.css">
    <style type="text/css">
      .container {
          width: 80%;
          margin: 15px auto;
      }
    </style>
  </head>

  <body>
    <header>
      <?php include('gyro_button.html'); ?>
    </header>
    
    <br>
    <form method='post'>
      <input type='text' name='kickboard_name' placeholder='블루투스 이름(default:1)' value='' style="width:240px;color:grey">
      <input type='text' name='start_date' placeholder='시작날짜(YYYY-mm-dd)' value='' style="color:grey">
      <input type='text' name='end_date'   placeholder='종료날짜(YYYY-mm-dd)' value='' style="color:grey">
      <input type='submit' value='SEARCH'>
    </form>

    <p><?php
      echo "Searched : ".$kickboard_name.
          " from ".date($mysql_start_date).
          " to ".date($mysql_end_date);
    ?></p>

    <div class="container">
      <canvas id="loc_Chart" width="1000" height="500"></canvas>
      <br>
      <br>
      <canvas id="acc_Chart" width="1000" height="500"></canvas>
      <br>
      <br>
      <canvas id="ang_Chart" width="1000" height="500"></canvas>
    </div>
  

    <!-- Location GRAPH -->
    <script>
      var ctx = document.getElementById("loc_Chart");
      var myChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: [<?php 
            while($rows = mysqli_fetch_array($result_date_loc) ){
              echo '"'.$rows['record_date'].'",';
            }
            ?>],
          datasets: [{
              label: 'Location X',
              data: [<?php
                while($rows_ = mysqli_fetch_array($result_data_loc_x) ){
                  echo '"'.$rows_['loc_x'].'",';
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
              label: 'Location Y',
              data: [<?php
                while($rows_ = mysqli_fetch_array($result_data_loc_y) ){
                  echo '"'.$rows_['loc_y'].'",';
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
              label: 'Location Z',
              data: [<?php
                while($rows_ = mysqli_fetch_array($result_data_loc_z) ){
                  echo '"'.$rows_['loc_z'].'",';
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
            text: 'Location Test Graph'
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


    <!-- Acceleration GRAPH -->
    <script>
      var ctx = document.getElementById("acc_Chart");
      var myChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: [<?php 
            while($rows = mysqli_fetch_array($result_date_acc) ){
              echo '"'.$rows['record_date'].'",';
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


    <!-- Angular Acceleration GRAPH -->
    <script>
      var ctx = document.getElementById("ang_Chart");
      var myChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: [<?php 
            while($rows = mysqli_fetch_array($result_date_ang) ){
              echo '"'.$rows['record_date'].'",';
            }
            ?>],
          datasets: [{
              label: 'Angular Acceleration X',
              data: [<?php
                while($rows_ = mysqli_fetch_array($result_data_ang_x) ){
                  echo '"'.$rows_['ang_x'].'",';
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
              label: 'Angular Acceleration Y',
              data: [<?php
                while($rows_ = mysqli_fetch_array($result_data_ang_y) ){
                  echo '"'.$rows_['ang_y'].'",';
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
              label: 'Angular Acceleration Z',
              data: [<?php
                while($rows_ = mysqli_fetch_array($result_data_ang_z) ){
                  echo '"'.$rows_['ang_z'].'",';
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
            text: 'Angular Acceleration Test Graph'
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