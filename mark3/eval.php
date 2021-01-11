<?php
  header('Content-Type: text/html; charset=utf-8');
  include('../conn.php');  
  
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
    if(empty($end_time))  $end_time = date("H:00:00", strtotime("+1 hour"));
  }
  $mysql_end_date = DateTime::createFromFormat("Y-m-d H:i:s", $date. $end_time)->format('Y-m-d H:i:s');
  setcookie("end_time", $end_time, time() + 86400);

  if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  } else {
    $sql = "SELECT * FROM mark3 WHERE kickboard = '$kickboard' AND (record_date BETWEEN '$mysql_start_date' AND '$mysql_end_date')";
    $result_date_acc =  mysqli_query($conn, $sql);
    $result_data_acc_x = mysqli_query($conn, $sql);
    $result_data_acc_y = mysqli_query($conn, $sql);
    $result_data_acc_z = mysqli_query($conn, $sql);
  }

  $x = $_POST['x'];
  if(empty($x)) $x = 0;
  
?>

<!DOCTYPE html>

  <head>
    <meta charset="utf-8">
    <title>Convol Evaluation</title>
    <link rel="stylesheet" href="../css/button.css">
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
    <style type="text/css">
      .container {
          width: 80%;
          margin: 15px auto;
      }
    </style>
  </head>

    <body>
        <header>
            <?php include('convol_button.html'); ?>
        </header>

        <div align="center">
            <br>
            <form method='post'>
                <input type='text' name='kickboard' placeholder='' value='<?php echo $kickboard; ?>' style="width:100px;color:grey">
                <br>
                <input type='text' name='date' placeholder='YYYY-mm-dd' value='<?php echo $date; ?>' style="width:120px;color:grey">
                <input type='text' name='start_time' value='<?php echo $start_time; ?>' style="width:120px;color:grey">
                ~
                <input type='text' name='end_time' value='<?php echo $end_time; ?>' style="width:120px;color:grey">
                <br>
                <input type='submit' value='SEARCH'>
            </form>
            
            <p><?php
                echo date($mysql_start_date)." ~ ".date($mysql_end_date)." from DB mark3";
            ?></p>
            
            <div class="container">
                <canvas id="acc_Chart" width="1000" height="500"></canvas>
            </div>

            <div align="center">
                <form action="algo_1.php" method="POST">
                    <input type="hidden" name="kickboard" value="<?php echo $kickboard; ?>">
                    <input type='hidden' name='date' placeholder='YYYY-mm-dd' value='<?php echo $date; ?>' style="width:120px;color:grey">
                    <input type='hidden' name='start_time' value='<?php echo $start_time; ?>' style="width:120px;color:grey">
                    <input type='hidden' name='end_time' value='<?php echo $end_time; ?>' style="width:120px;color:grey">
                    <input type="submit" class="button color" name="submit" value="algo 1">
                    Result: x <?php echo $x; ?>%, y %, z %
                </form>
            </div>

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

        </div>
    </body>

</html>