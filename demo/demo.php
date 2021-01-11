<?php
  header('Content-Type: text/html; charset=utf-8');
  include('../conn.php');
  include('./algo_2.php');
  
?>

<!DOCTYPE html>
<html style="font-size: 16px;">
  <title>Demo PAGE</title>

  <head>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/button.css">
    <link rel="stylesheet" href="../css/tab.css">
    <meta http-equiv="refresh" content="1">
    <style type="text/css">
      .container {
        width: 80%;
      }
      div.left{
        width: 25%;
        float: left;
      }
      div.right{
        width: 75%;
        float: right;
      }
    </style>
    <script>
      function setCookie(name, value, exp) {
        var date = new Date();
        date.setTime(date.getTime() + exp*24*60*60*1000);
        document.cookie = name + '=' + value + ';expires=' + date.toUTCString() + ';path=/demo';
      }
    </script>
  </head>

  <body>
    
    <br>
    <form method='post'>
      <input type='hidden' name='kickboard' value='<?php echo $kickboard; ?>'>
    </form>
    
    <button type="button" class="button color" onclick="location.href='./setup.php'">
        <- Back
    </button>

    <div align="center">
        <img src="../images/scooker_v3.png" alt="No Image Found" width="60%"><br>
        <h2><?php echo $kickboard; ?></h2>
        <h3><?php echo "$mysql_start_date ~ $mysql_end_date"?></h3>
        <hr size="4px" color="darkcyan" width="90%">
    </div>

    <div align="center" style="width:100%; height:500px; vertical-align: middle;">
      <br>
      <div align="center" name="labels" class="container left">
        
        <!-- 운행 -->
        <p>
          <b class= "label">운행</b>
          <br><br>
          <?php
            switch($run){
              case 1:
                echo "<span class='label_selected'>정지</span>";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>운행";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>보행";
                break;
              case 2:
                echo "<span class='label'>정지";
                echo "<span class='label'>  |  ";
                echo "<span class='label_selected'>운행</span>";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>보행";
                break;
              case 3:
                echo "<span class='label'>정지";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>운행";
                echo "<span class='label'>  |  ";
                echo "<span class='label_selected'>보행</span>";
                break;
              default:
                echo "<span class='label'> -- Error -- ";
                break;
            }
          ?>
        </p>
        
        <!-- 도로 -->
        <p>
          <b class= "label">도로</b>
          <br><br>
          <?php
            switch($road){
              case 1:
                echo "<span class='label_selected'>도로</span>";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>인도";
                break;
              case 2:
                echo "<span class='label'>도로";
                echo "<span class='label'>  |  ";
                echo "<span class='label_selected'>인도</span>";
                break;
              default:
                echo "<span class='label'> -- Error -- ";
                break;
            }
          ?>
        </p>
        
        <!-- 장착 -->
        <p>
          <b class= "label">장착</b>
          <br><br>
          <?php
            switch($attach){
              case 1:
                echo "<span class='label_selected'>차량</span>";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>헬멧";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>손";
                break;
              case 2:
                echo "<span class='label'>차량";
                echo "<span class='label'>  |  ";
                echo "<span class='label_selected'>헬멧</span>";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>손";
                break;
              case 3:
                echo "<span class='label'>차량";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>헬멧";
                echo "<span class='label'>  |  ";
                echo "<span class='label_selected'>손</span>";
                break;
              default:
                echo "<span class='label'> -- Error -- ";
                break;
            }
          ?>
        </p>

        <!-- 경사 -->
        <p>
          <b class= "label">경사</b>
          <br><br>
          <?php
            switch($scope){
              case 1:
                echo "<span class='label_selected'>평지</span>";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>오르막";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>내리막";
                break;
              case 2:
                echo "<span class='label'>평지";
                echo "<span class='label'>  |  ";
                echo "<span class='label_selected'>오르막</span>";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>내리막";
                break;
              case 3:
                echo "<span class='label'>평지";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>오르막";
                echo "<span class='label'>  |  ";
                echo "<span class='label_selected'>내리막</span>";
                break;
              default:
                echo "<span class='label'> -- Error -- ";
                break;
            }
          ?>
        </p>
        
        <!-- 변속 -->
        <p>
          <b class= "label">변속</b>
          <br><br>
          <?php
            switch($event){
              case 0:
                echo "<span class='label_selected'>등속</span>";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>가속";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>감속";
                break;
              case 1:
                echo "<span class='label'>등속</span>";
                echo "<span class='label'>  |  ";
                echo "<span class='label_selected'>가속</span>";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>감속";
                break;
              case 2:
                echo "<span class='label'>등속</span>";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>가속";
                echo "<span class='label'>  |  ";
                echo "<span class='label_selected'>감속</span>";
                break;
              case 3:
                echo "<span class='label'>등속</span>";
                echo "<br><br>";
                echo "<span class='label_selected'>가속</span>";
                echo "<span class='label'>  |  ";
                echo "<span class='label_selected'>감속</span>";
                break;
              default:
                echo "<span class='label'> -- Error -- ";
                break;
            }
          ?>
        </p>

        <!-- 장애물 -->
        <p>
          <b class= "label">장애물(턱)</b>
          <br><br>
          <?php
            switch($obstacle){
              case 0:
                echo "<span class='label_selected'>이상없음</span>";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>과속방지턱";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>턱";
                break;
              case 1:
                echo "<span class='label'>이상없음</span>";
                echo "<span class='label'>  |  ";
                echo "<span class='label_selected'>과속방지턱</span>";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>턱";
                break;
              case 2:
                echo "<span class='label'>이상없음</span>";
                echo "<span class='label'>  |  ";
                echo "<span class='label'>과속방지턱";
                echo "<span class='label'>  |  ";
                echo "<span class='낙하'>턱</span>";
                break;
              case 3:
                echo "<span class='label'>이상없음</span>";
                echo "<br><br>";
                echo "<span class='label_selected'>과속방지턱</span>";
                echo "<span class='label'>  |  ";
                echo "<span class='label_selected'>턱</span>";
                break;
              default:
                echo "<span class='label'> -- Error -- ";
                break;
            }
          ?>
        </p>

      </div>

      <br><br>
      
      <script type="text/javascript">
        function change_1() {
          setCookie('check', '1', 1);
          console.log(<?php echo $_COOKIE['check'];?>)
        }
        function change_2() {
          setCookie('check', '2', 1);
          console.log(<?php echo $_COOKIE['check'];?>)
        }
        function change_3() {
          setCookie('check', '3', 1);
          console.log(<?php echo $_COOKIE['check'];?>)
        }
      </script>

      <div id="css_tabs" class="container right">
        <input id="tab1" type="radio" value="1" name="tab1" onclick="change_1()" <?php
          if( $_COOKIE['check'] == 1) echo "checked";
          else echo "";
        ?>/>
        <input id="tab2" type="radio" value="2" name="tab2" onclick="change_2()" <?php
          if( $_COOKIE['check'] == 2) echo "checked";
          else echo "";
        ?>/>
        <input id="tab3" type="radio" value="3" name="tab3" onclick="change_3()" <?php
          if( $_COOKIE['check'] == 3) echo "checked";
          else echo "";
        ?>/>
        

        <label for="tab1">Degree</label>
        <label for="tab2">Angular Velocity</label>
        <label for="tab3">Acceleration</label>

        <div class="tab1_content">
          <canvas id="Degree_Chart" width="90%"></canvas>
        </div>
        <div class="tab2_content">
          <canvas id="agv_Chart" width="90%" ></canvas>
        </div>
        <div class="tab3_content">
          <canvas id="acc_Chart" width="90%"></canvas>
        </div>
      </div>
      
    </div>
  

    <!-- Degree GRAPH -->
    <script>
      var ctx = document.getElementById("Degree_Chart");
      var myChart = new Chart(ctx, {
        type: 'line',
        data: {
          labels: [<?php 
            for($i = 0 ; $i < $num ; $i++){
              echo '"'.$result_date[$i].'",';
            }
            ?>],
          datasets: [
            {
              label: 'Degree X',
              data: [<?php
                for($i = 0 ; $i < $num ; $i++){
                  echo '"'.$deg_x[$i].'",';
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
                for($i = 0 ; $i < $num ; $i++){
                  echo '"'.$deg_y[$i].'",';
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
                for($i = 0 ; $i < $num ; $i++){
                  echo '"'.$deg_z[$i].'",';
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
          hover: {mode: null},
          responsive: true,
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
            for($i = 0 ; $i < $num ; $i++){
              echo '"'.$result_date[$i].'",';
            }
            ?>],
          datasets: [
            {
              label: 'Angular Velocity X',
              data: [<?php
                for($i = 0 ; $i < $num ; $i++){
                  echo '"'.$agv_x[$i].'",';
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
                for($i = 0 ; $i < $num ; $i++){
                  echo '"'.$agv_y[$i].'",';
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
                for($i = 0 ; $i < $num ; $i++){
                  echo '"'.$agv_z[$i].'",';
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
          hover: {mode: null},
          responsive: true,
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
            for($i = 0 ; $i < $num ; $i++){
              echo '"'.$result_date[$i].'",';
            }
            ?>],
          datasets: [
            {
              label: 'Acceleration X',
              data: [<?php
                for($i = 0 ; $i < $num ; $i++){
                  echo '"'.$acc_x[$i].'",';
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
                for($i = 0 ; $i < $num ; $i++){
                  echo '"'.$acc_y[$i].'",';
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
                for($i = 0 ; $i < $num ; $i++){
                  echo '"'.$acc_z[$i].'",';
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
          hover: {mode: null},
          responsive: true,
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