<?php
  header('Content-Type: text/html; charset=utf-8');
  include('../conn.php');
  
  $avg = $_POST['avg'];
  if(empty($avg)) $avg = 1;

  $insert = $_POST['insert'];
  if(empty($insert))  $insert = 0;

  $kickboard = $_POST['kickboard'];
  if(empty($kickboard))   $kickboard = 'SCK06';

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


    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    } else {
        $sql = "SELECT * FROM mark1 WHERE kickboard='$kickboard' AND (shock_date between '$mysql_start_date' AND '$mysql_end_date')";
        $result =  mysqli_query($conn, $sql);
    }
    $con_x = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    $con_y = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    $con_z = array(0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0);
    $convulted_x = 0;
    $convulted_y = 0;
    $convulted_z = 0;
    $insert = 0;
?>

<!DOCTYPE html>
<html style="font-size: 16px;">

    <head>
        <title>
            Convolution Test
        </title>
        <link rel="stylesheet" href="../css/button.css">
    </head>

    <body>
    <button type="button" class="button color" onclick="location.href='test_list.html'">
        <- TEST
    </button>
    <form method='post'>
      Convolution(yes:1, no:0): <input type='text' name='insert' value='<?php echo $insert; ?>' style="width:40px; color:grey">
      <br>
      Range : <input type='text' name='avg' value='<?php echo $avg; ?>' style="width:40px; color:grey">
      <br>
      <input type='text' name='kickboard' placeholder='' value='<?php echo $kickboard; ?>' style="width:60px; color:grey">
      <br>
      <input type='text' name='date' placeholder='YYYY-mm-dd' value='<?php echo $date; ?>' style="width:100px; color:grey">
      <br>
      <input type='text' name='start_time' placeholder='00:00:00' value='<?php echo $start_time; ?>' style="width:120px;color:grey">
      ~
      <input type='text' name='end_time' placeholder='23:59:59' value='<?php echo $end_time; ?>' style="width:120px;color:grey">
      <input type='submit' value='CONVOLUTION' style="height:30px;">
    </form>
    <?php echo mysqli_num_rows($result);?> Records Convolution from mark1 -> mark_test
    <div style="text-align:center">
    <?php
      $num = 0;
        while($rows = mysqli_fetch_array($result) ){
          $num++;
          echo $rows['shock_date'].' Record ACC x: ';
          $acc_x = round(($rows['shock']/1000000))/100;
          $acc_y = round(($rows['shock']%1000000)/1000)/100;
          $acc_z = round($rows['shock']%1000)/100;
          echo $acc_x.',  y: ';
          echo $acc_y.',  z: ';
          echo $acc_z;   
          
          for ( $i = $avg - 1 ; $i > 0 ; $i--) {
              $con_x[$i] = $con_x[$i - 1];
              $con_y[$i] = $con_y[$i - 1];
              $con_z[$i] = $con_z[$i - 1];
          }
          $con_x[0] = $acc_x;
          $con_y[0] = $acc_y;
          $con_z[0] = $acc_z;
          
          if($num > $avg) {
            $convulted_x = 0;
            $convulted_y = 0;
            $convulted_z = 0;
            for ( $i = $avg - 1 ; $i >= 0 ; $i--) {
              $convulted_x += ($con_x[$i]/$avg);
              $convulted_y += ($con_y[$i]/$avg);
              $convulted_z += ($con_z[$i]/$avg);
            }

            echo " -->  Convolution x: ".$convulted_x.", y: ".$convulted_y.", z: ".$convulted_z;
        
            if( $avg != 1 && $insert == 1234 ){
              $sql_insert = "INSERT INTO mark_test (record_date, shock, acc_x, acc_y, acc_z)
              VALUES ('2020-12-30', 1000, $convulted_x, $convulted_y, $convulted_z)";
              $result_insert = mysqli_query($conn, $sql_insert);
              if($result_insert === false) echo mysqli_error($conn);
            }
          }
          echo '<br>';
        }
        if($avg != 1){
            $avg = 1;                
        }
        $insert = 0;  
    ?>
    </div>

    </body>

</html>