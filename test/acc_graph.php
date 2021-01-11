<?php
  header('Content-Type: text/html; charset=utf-8');
  include('../conn.php');  
  
  $num=$_POST['num'];
  if(empty($num)) $num = 30;
  
  if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  } else {
    $sql = "SELECT * FROM mark_test WHERE SHOCK=$num AND (record_date BETWEEN '2020-12-28 00:00:00' AND '2020-12-28 15:35:00')";
    $result_date_acc =  mysqli_query($conn, $sql);
    $result_data_acc_x = mysqli_query($conn, $sql);
    $result_data_acc_x_bg = mysqli_query($conn, $sql);
    $result_data_acc_y = mysqli_query($conn, $sql);
    $result_data_acc_z = mysqli_query($conn, $sql);
    $result_data_acc_z_bg = mysqli_query($conn, $sql);    
  }
  $danger_x = 0.7;
  $danger_y = 0.5;
  $danger_z = 1.2;
?>

<!DOCTYPE html>
<html style="font-size: 16px;">
  <title>ACC Graph</title>

  <head>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
    <link rel="stylesheet" href="../css/button.css">
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
  <button type="button" class="button color" onclick="location.href='test_list.html'">
    ← Back
  </button>
  <p><?php
    echo "Value : ".$num;
  ?></p>
  <form method='post'>
    <input type='text' name='num' value='<?php echo $num; ?>' style="width:240px; color:grey">
  </form>

  <div class="container">
    <canvas id="acc_Chart" width="1000" height="400"></canvas>
  </div>
  

  <!-- Acceleration GRAPH -->
  <script>
    var ctx = document.getElementById("acc_Chart");
    var myChart = new Chart(ctx, {
      type: 'line',
      data: {
        labels: [<?php 
          $axis_num = 0;
          while( $rows = mysqli_fetch_array($result_date_acc) ){
            echo '"'.$rows['record_date'].'",';
            $axis_num++;
          }
          ?>],
        datasets: [{
            label: 'Acceleration X',
            data: [<?php
              $x_overnum = 0;
              while($rows_ = mysqli_fetch_array($result_data_acc_x) ){
                echo '"'.$rows_['acc_x'].'",';
                if($rows_['acc_x'] > $danger_x) $x_overnum++;
              }
            ?>],
            backgroundColor:[
              'rgba(255, 255, 255, 0.2)',
            ],
            borderColor: [
              'rgba(255, 0, 0, 0.6)',
            ],
            borderWidth: 1,
            pointRadius: 0
          },
          
          {
            label: 'Acceleration Y',
            data: [<?php
              $y_overnum = 0;
              while($rows_ = mysqli_fetch_array($result_data_acc_y) ){
                echo '"'.$rows_['acc_y'].'",';
                if($rows_['acc_y'] > $danger_y) $y_overnum++;
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
          },
          
          {
            label: 'Acceleration Z',
            data: [<?php
              $z_overnum = 0;
              while($rows_ = mysqli_fetch_array($result_data_acc_z) ){
                echo '"'.$rows_['acc_z'].'",';
                if($rows_['acc_z'] > $danger_z) $z_overnum++;
              }
            ?>],
            backgroundColor: [
              'rgba(255, 255, 255, 0.2)',
            ],
            borderColor: [
              'rgba(0, 0, 255, 0.6)',
            ],
            borderWidth: 1,
            pointRadius: 0,
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
                beginAtZero: false
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

  <div align="center">
    <h2>Caution</h2>
    <?php
      echo "x:  ".round($x_overnum / $axis_num * 100, 2)."%,   ";
      echo "y:  ".round($y_overnum / $axis_num * 100, 2)."%,   ";
      echo "z:  ".round($z_overnum / $axis_num * 100, 2)."%";
    ?>
  </div>
    
  </body>

</html>