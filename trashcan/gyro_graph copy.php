<?php
  header('Content-Type: text/html; charset=utf-8');
  include('conn.php');  
  
  $kickboard = $_POST['kickboard'];
  if(empty($kickboard)){
    $kickboard = $_COOKIE['kickboard'];
    if(empty($kickboard))  $kickboard = '120';
  }
  setcookie("kickboard", $kickboard, time() + 86400);
  
  $date = $_POST['date'];
  if(empty($date)) {
    $date = $_COOKIE['date'];
    if(empty($date))  $date = date("Y-m-d");
  }
  setcookie("date", $date, time() + 86400);
  
  $start_time = $_POST['start_time'];
  if(empty($start_time)) {
    $start_time = $_COOKIE['start_time'];
    if(empty($start_time))  $start_time = date("H:00:00", strtotime("-1 hour"));
  }
  $mysql_start_date = DateTime::createFromFormat('Y-m-d H:i:s', $date. $start_time)->format('Y-m-d H:i:s');
  setcookie("start_time", $start_time, time() + 86400);

  $end_time = $_POST['end_time'];
  if(empty($end_time)) {
    $end_time = $_COOKIE['end_time'];
    if(empty($end_time))  $end_time = date("H:00:00", strtotime("-50 minute"));
  }
  $mysql_end_date = DateTime::createFromFormat("Y-m-d H:i:s", $date. $end_time)->format('Y-m-d H:i:s');
  setcookie("end_time", $end_time, time() + 86400);
  
  if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  } else {
    echo "CONNECTION SUCCESS\n";
    $sql = "SELECT * FROM mark2 WHERE kickboard = '$kickboard' AND (record_date BETWEEN '$mysql_start_date' AND '$mysql_end_date')";
    $result_bump =  mysqli_query($conn, $sql);
    $result_date_deg =  mysqli_query($conn, $sql);
    $result_data_deg_x = mysqli_query($conn, $sql);
    $result_data_deg_y = mysqli_query($conn, $sql);
    $result_data_deg_z = mysqli_query($conn, $sql);
    $result_date_acc =  mysqli_query($conn, $sql);
    $result_data_acc_x = mysqli_query($conn, $sql);
    $result_data_acc_y = mysqli_query($conn, $sql);
    $result_data_acc_z = mysqli_query($conn, $sql);
    $result_date_agv =  mysqli_query($conn, $sql);
    $result_data_agv_x = mysqli_query($conn, $sql);
    $result_data_agv_y = mysqli_query($conn, $sql);
    $result_data_agv_z = mysqli_query($conn, $sql);
  }
?>

<!DOCTYPE html>
<html style="font-size: 16px;">
  <title>Gyro Graph</title>

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
    <form method='post' align="center">
      <input type='text' name='kickboard' placeholder='' value='<?php echo $kickboard; ?>' style="color:grey">
      <br>
      <input type='text' name='date' placeholder='YYYY-mm-dd' value='<?php echo $date; ?>' style="color:grey">
      <br>
      <input type='text' name='start_time' value='<?php echo $start_time; ?>' style="width:120px;color:grey">
      ~
      <input type='text' name='end_time' value='<?php echo $end_time; ?>' style="width:120px;color:grey">
      <br>
      <input type='submit' value='SEARCH'>
    </form>

    <p><?php
      echo "Searched : ".$kickboard.
          " from ".date($mysql_start_date).
          " ~ ".date($mysql_end_date);
    ?></p>

    <div class="container">
      <details>
        <summary align="center">Degree Chart</summary>
        <p><canvas id="Degree_Chart" width="1000" height="500"></canvas></p>
      </details>  
      <br>
      <details>
        <summary align="center">Angular Velocity Chart</summary>
        <p><canvas id="agv_Chart" width="1000" height="500"></canvas></p>
      </details>
      <br>
      <canvas id="acc_Chart" width="1000" height="500"></canvas>
    </div>
      
    <div align="center">
      <br>
      <h2>Bump Value</h2>
      <?php
        $acc_x = array();
        $acc_y = array();
        $acc_z = array();
        $num = mysqli_num_rows($result_bump);

        while ( $rows = mysqli_fetch_array($result_bump)){
          array_push($acc_x, $rows['acc_x']);
          array_push($acc_y, $rows['acc_y']);
          array_push($acc_z, $rows['acc_z']);
        }
        
        $filename = "data/layer_value.txt";
        if(file_exists($filename)){
          $fp = fopen($filename, "r");
          $layer = array();
          while( !feof($fp) ) {
              $temp = fgets($fp);
              array_push($layer, $temp);
          }
          fclose($fp);
        } else {
            $layer = array(0, 1, 2, 3, 4, 5, 5, 4, 3, 1, 0);
        }
        
        $first = 0;
        $second = 0;
        $thrid = 0;
        $max = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $min = array(100, 0, 0, 0, 0, 0, 0, 0, 0, 0);
        $bump_std = 42;
        $num_1 = 0;
        $num_2 = 0;

        for ( $i = 0 ; $i < $num - 12 ; $i++ ){
          $bump = 0;
          for( $j = $i ; $j < $i + 12 ; $j++) {
            $bump += ( $acc_z[$j] ) * $layer[$j - $i] ;
          }
          if($bump >= $bump_std) $num_1++;
          else $num_2++;
          // echo $bump."<br>";
          for( $j = 0 ; $j < 10; $j++ ){
            if( $bump > $max[$j]) {            
              for( $k = 9 ; $k > $j ; $k-- ){
                $max[$k] = $max[$k - 1];
              }
              $max[$j] = $bump;
              break;
            }
          }
          for( $j = 0 ; $j < 10; $j++ ){
            if( $bump < $min[$j]) {            
              for( $k = 9 ; $k > $j ; $k-- ){
                $min[$k] = $min[$k - 1];
              }
              $min[$j] = $bump;
              break;
            }
          }
        }

        echo $num_1." Values bigger than ".$bump_std."<br>";
        echo $num_2." Values smaller than ".$bump_std."<br>";
        echo "in ".$num." Records <br><br>";

        for( $i = 0 ; $i < 10 ; $i++ ){
          echo ($i+1)."th Max Value : ".$max[$i];
          echo "<br>";
        }
        echo "...<br>";
        for( $i = 9 ; $i >= 0 ; $i-- ){
          echo ($i+1)."th Min Value : ".$min[$i];
          echo "<br>";
        }


      ?>
      <br>
      ------------------------------------
    </div>

    <!-- Degree GRAPH -->
    <script>
      var ctx = document.getElementById("Degree_Chart");
      var myChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: [<?php 
            while($rows = mysqli_fetch_array($result_date_deg) ){
              echo '"'.$rows['record_date'].'",';
            }
            ?>],
          datasets: [
            {
              label: 'Degree X',
              data: [<?php
                while($rows_ = mysqli_fetch_array($result_data_deg_x) ){
                  echo '"'.$rows_['deg_x'].'",';
                }
              ?>],
              backgroundColor: [
                'rgba(255, 255, 255, 0.2)',
              ],
              borderColor: [
                'rgba(255, 0, 0, 0.6)',
              ],
              borderWidth: 1,
              pointRadius: 0
            }, {
              label: 'Degree Y',
              data: [<?php
                while($rows_ = mysqli_fetch_array($result_data_deg_y) ){
                  echo '"'.$rows_['deg_y'].'",';
                }
              ?>],
              backgroundColor: [
                'rgba(255, 255, 255, 0.2)',
              ],
              borderColor: [
                'rgba(0, 255, 0, 0.6)',
              ],
              borderWidth: 1,
              pointRadius: 0
            }, {
              label: 'Degree Z',
              data: [<?php
                while($rows_ = mysqli_fetch_array($result_data_deg_z) ){
                  echo '"'.$rows_['deg_z'].'",';
                }
              ?>],
              backgroundColor: [
                'rgba(255, 255, 255, 0.2)',
              ],
              borderColor: [
                'rgba(0, 0, 255, 0.6)',
              ],
              borderWidth: 1,
              pointRadius: 0
            }
          ]
        },
        options: {
          responsive: true,
          title: {
            display: true,
            text: 'Degree Graph'
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
                labelString: 'Degree °',
                fontSize: '16'
              }
            }]
          }
        }
      });
    </script>

    <!-- Angular Velocity GRAPH -->
    <script>
      var ctx = document.getElementById("agv_Chart");
      var myChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: [<?php 
            while($rows = mysqli_fetch_array($result_date_agv) ){
              echo '"'.$rows['record_date'].'",';
            }
            ?>],
          datasets: [
            {
              label: 'Angular Velocity X',
              data: [<?php
                while($rows_ = mysqli_fetch_array($result_data_agv_x) ){
                  echo '"'.$rows_['agv_x'].'",';
                }
              ?>],
              backgroundColor: [
                'rgba(255, 255, 255, 0.2)',
              ],
              borderColor: [
                'rgba(255, 0, 0, 0.6)',
              ],
              borderWidth: 1,
              pointRadius: 0
            }, {
              label: 'Angular Velocity Y',
              data: [<?php
                while($rows_ = mysqli_fetch_array($result_data_agv_y) ){
                  echo '"'.$rows_['agv_y'].'",';
                }
              ?>],
              backgroundColor: [
                'rgba(255, 255, 255, 0.2)',
              ],
              borderColor: [
                'rgba(0, 255, 0, 0.6)',
              ],
              borderWidth: 1,
              pointRadius: 0
            }, {
              label: 'Angular Velocity Z',
              data: [<?php
                while($rows_ = mysqli_fetch_array($result_data_agv_z) ){
                  echo '"'.$rows_['agv_z'].'",';
                }
              ?>],
              backgroundColor: [
                'rgba(255, 255, 255, 0.2)',
              ],
              borderColor: [
                'rgba(0, 0, 255, 0.6)',
              ],
              borderWidth: 1,
              pointRadius: 0
            }
          ]
        },
        options: {
          responsive: true,
          title: {
            display: true,
            text: 'Angular Velocity Graph'
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
                labelString: '[°/s]',
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
          datasets: [
            {
              label: 'Acceleration X',
              data: [<?php
                while($rows_ = mysqli_fetch_array($result_data_acc_x) ){
                  echo '"'.$rows_['acc_x'].'",';
                }
              ?>],
              backgroundColor: [
                'rgba(255, 255, 255, 0.2)',
              ],
              borderColor: [
                'rgba(255, 0, 0, 0.6)',
              ],
              borderWidth: 1,
              pointRadius: 0
            }, {
              label: 'Acceleration Y',
              data: [<?php
                while($rows_ = mysqli_fetch_array($result_data_acc_y) ){
                  echo '"'.$rows_['acc_y'].'",';
                }
              ?>],
              backgroundColor: [
                'rgba(255, 255, 255, 0.2)',
              ],
              borderColor: [
                'rgba(0, 255, 0, 0.6)',
              ],
              borderWidth: 1,
              pointRadius: 0
            }, {
              label: 'Acceleration Z',
              data: [<?php
                while($rows_ = mysqli_fetch_array($result_data_acc_z) ){
                  echo '"'.$rows_['acc_z'].'",';
                }
              ?>],
              backgroundColor: [
                'rgba(255, 255, 255, 0.2)',
              ],
              borderColor: [
                'rgba(0, 0, 255, 0.6)',
              ],
              borderWidth: 1,
              pointRadius: 0
            }
          ]
        },
        options: {
          responsive: true,
          title: {
            display: true,
            text: 'Acceleration Graph'
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
                labelString: '[g]',
                fontSize: '16'
              }
            }]
          }
        }
      });
    </script>

  </body>

</html>