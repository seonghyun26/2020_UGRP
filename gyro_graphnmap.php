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
  
  $range = $_POST['range'];
  if(empty($range)){
    $range = $_COOKIE['range'];
    if(empty($range)) $range = 24;
  }
  setcookie("range", $range, time() + 86400);

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

    $sql_gps = "SELECT * FROM mark2 WHERE kickboard = '$kickboard' AND (record_date BETWEEN '$mysql_start_date' AND '$mysql_end_date')";
    $result = mysqli_query($conn, $sql_gps);
    $length = mysqli_num_rows($result);

    $gps=[];
    while( $rows = mysqli_fetch_array($result) ){
      array_push($gps, array('lat' => $rows['latitude'], 'long' => $rows['longitude'] ));
    }
    $gps_json = json_encode($gps);
  }
?>

<!DOCTYPE html>
<html style="font-size: 16px;">
  <title>Gyro Graph</title>

  <head>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/button.css">
    <style type="text/css">
      .container {
          width: 80%;
          margin: 15px auto;
      }
      div.left{
        width: 50%;
        float: left;
      }
      div.right{
        width: 50%;
        float: right;
      }
      input{
        text-align: center;
        border-radius: 5px;
        border: none;
        margin: 2px 2px;
        background: #DDDDDD;
        font-size: 16px;
        padding:2px 2px;
      }
    </style>
    <script>
      var length = <?php echo $length;?>;
      var gps = <?php echo $gps_json; ?>;
    </script>
  </head>

  <body>
    <header>
      <link rel="stylesheet" href="../css/main.css">
      <link rel="stylesheet" href="../css/button.css">
      <script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=943099a8532d50175446b338d9f13a37"></script>
      <script type="text/javascript" src="//dapi.kakao.com/v2/maps/sdk.js?appkey=APIKEY&libraries=services,clusterer,drawing"></script>
      <?php include('gyro_button.html'); ?>
      <br><br>
      <?php include('gyro_search.html')?>
    </header>

    <p><?php
      echo "Searched : ".$kickboard.
          " from ".date($mysql_start_date).
          " ~ ".date($mysql_end_date);
    ?></p>

    <div align="center" style="width:100%; height:500px;">

      <div class="container left">
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
        <br>
      </div>

      <div align="center" class="right">
        <img src="../images/unlock.png" alt="No Image Found" width="30px" id="lock_image">
        <input type="checkbox" onclick="lock()" class="switch" id="check">
        <div id="map" style="width:100%; height:500px;"></div>
      </div>
      <br>

    </div>
    <br><br>

    <!-- Bump Calculation -->
    <div align="center">
      <br><br>
      <h2 align="center">Bump Value</h2>
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
            $bump += ( abs( $acc_x[$j]) + abs($acc_z[$j]) ) * $layer[$j - $i] ;
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

    <!-- Kakao Map -->
    <script>
        var container = document.getElementById('map');
        var options = {
          center: new kakao.maps.LatLng(36.012277245185594, 129.32373470210902),
          level: 5
        };

        var map = new kakao.maps.Map(container, options);

        var mapTypeControl = new kakao.maps.MapTypeControl();
        map.addControl(mapTypeControl, kakao.maps.ControlPosition.TOPRIGHT);
        var zoomControl = new kakao.maps.ZoomControl();
        map.addControl(zoomControl, kakao.maps.ControlPosition.RIGHT);

        function lock(){
          var chk = document.getElementById('check').checked;
          if(chk) document.getElementById('lock_image').src="../images/lock.png";
          else document.getElementById('lock_image').src="../images/unlock.png";
          map.setDraggable(!chk);
          map.setZoomable(!chk);
        }

        var positions=[];
        for (var i = 0; i < length ; i++) {
          positions.push( new kakao.maps.LatLng(gps[i].lat, gps[i].long) );
        }

        var polyline = new kakao.maps.Polyline({
          path: positions, // 선을 구성하는 좌표배열 입니다
          strokeWeight: 3, // 선의 두께 입니다
          strokeColor: '#1EE4A9', // 선의 색깔입니다
          strokeOpacity: 0.8, // 선의 불투명도 입니다 1에서 0 사이의 값이며 0에 가까울수록 투명합니다
          strokeStyle: 'solid' // 선의 스타일입니다
        });
        polyline.setMap(map);  
        
        for (var i = 0; i < positions.length; i+=100) {
          var marker = new kakao.maps.Marker({
            map: map, // 마커를 표시할 지도
            position: positions[i], // 마커를 표시할 위치
            title : i // 마커의 타이틀, 마커에 마우스를 올리면 타이틀이 표시됩니다
          });
        }
        marker.setMap(map);

    </script>

  </body>

</html>