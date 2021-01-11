<?php
  header('Content-Type: text/html; charset=utf-8');
  include('conn.php');  
  
  $kickboard=$_POST['kickboard'];
  if(empty($kickboard)) $kickboard = 'SCK06';

  $date = $_POST['date'];
  if(empty($date)) {
    $date = $_COOKIE['date'];
    if(empty($date))  $date = date("Y-m-d");
  }
  setcookie("date", $date, time() + 86400);

  $start_time = $_POST['start_time'];
  if(empty($start_time)) $start_time = '00:00:00';
  $mysql_start_date = DateTime::createFromFormat('Y-m-d H:i:s', $date. $start_time)->format('Y-m-d H:i:s');
  $end_time = $_POST['end_time'];
  if(empty($end_time)) $end_time = '23:59:59';
  $mysql_end_date = DateTime::createFromFormat("Y-m-d H:i:s", $date. $end_time)->format('Y-m-d H:i:s');

  $caution = 150;
  $danger = 200;
   
  if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  } else {
    echo "CONNECTION SUCCESS\n";
    $sql = "SELECT * FROM mark1 WHERE kickboard = '$kickboard' AND (shock_date BETWEEN '$mysql_start_date' AND '$mysql_end_date')";
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
    <form method='post' align="center">
        <input type='text' name='kickboard' placeholder='' value='<?php echo $kickboard; ?>' style="color:grey">
        <br>
        <input type='text' name='date' placeholder='YYYY-mm-dd' value='<?php echo $date; ?>' style="color:grey">
        <br>
        <input type='text' name='start_time' placeholder='00:00:00' value='<?php echo $start_time; ?>' style="width:120px;color:grey">
        ~
        <input type='text' name='end_time' placeholder='23:59:59' value='<?php echo $end_time; ?>' style="width:120px;color:grey">
        <br>
        <input type='submit' value='SEARCH'>
      </form>

    <p><?php
      echo "Searched : ".$kickboard.
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
            data: [<?php
              while($rows_ = mysqli_fetch_array($result_shock) ){
                echo '"'.$rows_['shock'].'",';
              } ?>
            ],
            backgroundColor: [
              'rgba(255, 255, 255, 0.2)',
            ],
            borderColor: [
              'rgba(0, 0, 0, 0.4)',
            ],
            borderWidth: 2,
            pointRadius: 0
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