<?php
  header('Content-Type: text/html; charset=utf-8');
  include('conn.php');  
  
  $kickboard_name=$_POST['kickboard_name'];
  if(empty($kickboard_name)) $kickboard_name = 'HC-06';

  $start_date=$_POST['start_date'];
  if(empty($start_date)) $start_date = date("Y-m-d");
  $start_time=$_POST['start_time'];
  if(empty($start_time)) $start_time = '00:00:00';
  $mysql_start_date = DateTime::createFromFormat('Y-m-dH:i:s', $start_date. $start_time)->format('Y-m-d H:i:s');
  
  $end_date=$_POST['end_date'];
  if(empty($end_date)) $end_date = date("Y-m-d");
  $end_time=$_POST['end_time'];
  if(empty($end_time)) $end_time = '23:59:59';
  $mysql_end_date = DateTime::createFromFormat("Y-m-dH:i:s", $end_date. $end_time)->format('Y-m-d H:i:s');


  $caution = 150;
  $danger = 200;
   
  if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  } else {
    echo "CONNECTION SUCCESS\n";
    $sql = "SELECT * FROM mark1 WHERE kickboard = '$kickboard_name' AND (shock_date BETWEEN '$mysql_start_date' AND '$mysql_end_date')";
    $result_date =  mysqli_query($conn, $sql);
    $result_shock = mysqli_query($conn, $sql);
    $result_number = mysqli_query($conn, $sql);
    $result_background = mysqli_query($conn, $sql);
  }

  $number = 0;
  while($test = mysqli_fetch_array($result_number)) {
    $number++;
  }
?>

<!DOCTYPE html>
<html style="font-size: 16px;">
  <title>graph test</title>

  <head>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
  </head>

  <body>
    <header>
      <link rel="stylesheet" href="css/button.css">
      <?php include('shock_button.html'); ?>
    </header>
    
    <br>
    <form method='post'>
      <input type='text' name='kickboard_name' placeholder='블루투스 이름(default HC-06)' value='' style="width:240px;color:grey">
      <br>
      <input type='text' name='start_date' placeholder='시작날짜(YYYY-mm-dd)' value='' style="color:grey">
      <input type='text' name='start_time' placeholder='시간(HH-MM-SS) (default 00:00:00)' value='' style="width:240px;color:grey">
      <br>
      <input type='text' name='end_date'   placeholder='종료날짜(YYYY-mm-dd)' value='' style="color:grey">
      <input type='text' name='end_time' placeholder='시간(HH-MM-SS) (default 23:59:59)' value='' style="width:240px;color:grey">
      <input type='submit' value='SEARCH'>
    </form>

    <p><?php
      echo "Searched : ".$kickboard_name.
          " from ".date($mysql_start_date).
          " to ".date($mysql_end_date);
    ?></p>

    <div class="container">
      <canvas id="myChart" width="1000" height="500" style="background-color: #fff;"></canvas>
    </div>

    <script>
      var ctx = document.getElementById("myChart");
      var myChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: [<?php 
            while($rows = mysqli_fetch_array($result_date) ){
              echo '"'.$rows['shock_date'].'",';
            }
            ?>],
          datasets: [{
            label: 'Shock',
            borderColor: 'rgba(150, 150, 150, 0.2)',
            pointBackgroundColor: [ <?php 
              while($rows_2 = mysqli_fetch_array($result_background) ){
                if ($rows_2['shock'] < $caution ){
                  echo '\'rgba(130, 200, 250, 0.8)\',';
                } else if ($rows_2['shock'] < $danger ) {
                  echo '\'rgba(250, 160, 40, 0.8)\',';
                } else {
                  echo '\'rgba(250, 10, 10, 0.8)\',';
                }
              } ?>
            ],
            fill: true,
            data: [<?php
              while($rows_ = mysqli_fetch_array($result_shock) ){
                echo '"'.$rows_['shock'].'",';
              } ?>
            ],
            borderWidth: 2
          }]
        },
        options: {
          elements : {
            line: {
              tension: 0
            }
          },
          responsive: true,
          title: {
            display: true,
            text: 'Shock Test Graph'
          },
          legend: {
            display: false,
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
                labelString: 'Value'
              }
            }]
          }
        }
      });
    </script>

  </body>

</html>