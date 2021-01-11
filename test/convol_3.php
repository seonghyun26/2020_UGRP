<?php
  header('Content-Type: text/html; charset=utf-8');
  include('../conn.php');

  $insert = $_POST['insert'];
  if(empty($insert) )  $insert = 0;
    
  $avg = $_POST['avg'];
  if(empty($avg)) $avg = 1;

  $kickboard = $_POST['kickboard'];
  if(empty($kickboard)){
    $kickboard = $_COOKIE['kickboard'];
    if(empty($kickboard))  $kickboard = 'SCK';
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
    if(empty($start_time))  $start_time = '00:00:00';
  }
  setcookie("start_time", $start_time, time() + 86400);
  $mysql_start_date = DateTime::createFromFormat('Y-m-d H:i:s', $date. $start_time)->format('Y-m-d H:i:s');

  $end_time = $_POST['end_time'];
  if(empty($end_time)) {
    $end_time = $_COOKIE['end_time'];
    if(empty($end_time))  $end_time = '23:59:59';
  }
  setcookie("end_time", $end_time, time() + 86400);
  $mysql_end_date = DateTime::createFromFormat("Y-m-d H:i:s", $date. $end_time)->format('Y-m-d H:i:s');

  if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  } else {
    $sql = "SELECT * FROM mark2 WHERE kickboard='$kickboard' AND (record_date between '$mysql_start_date' AND '$mysql_end_date')";
    $result =  mysqli_query($conn, $sql);
  }
  $deg_con_x = array(0, 0, 0, 0, 0, 0, 0, 0);
  $deg_con_y = array(0, 0, 0, 0, 0, 0, 0, 0);
  $deg_con_z = array(0, 0, 0, 0, 0, 0, 0, 0);
  $agv_con_x = array(0, 0, 0, 0, 0, 0, 0, 0);
  $agv_con_y = array(0, 0, 0, 0, 0, 0, 0, 0);
  $agv_con_z = array(0, 0, 0, 0, 0, 0, 0, 0);
  $acc_con_x = array(0, 0, 0, 0, 0, 0, 0, 0);
  $acc_con_y = array(0, 0, 0, 0, 0, 0, 0, 0);
  $acc_con_z = array(0, 0, 0, 0, 0, 0, 0, 0);

  $deg_convulted_x = 0;
  $deg_convulted_y = 0;
  $deg_convulted_z = 0;
  $agv_convulted_x = 0;
  $agv_convulted_y = 0;
  $agv_convulted_z = 0;
  $acc_convulted_x = 0;
  $acc_convulted_y = 0;
  $acc_convulted_z = 0;

  $gfunc_par = 0;
  $gfunc_par_1 = $_POST['gfunc_par_1'];
  if(empty($gfunc_par_1)) $gfunc_par_1 = 1.5;

  $gfunc_par_2 = $_POST['gfunc_par_2'];
  if(empty($gfunc_par_2)) $gfunc_par_2 = 1;

  $gfunc_par_3 = $_POST['gfunc_par_3'];
  if(empty($gfunc_par_3)) $gfunc_par_3 = 1;

  $gfunc_par_4 = $_POST['gfunc_par_4'];
  if(empty($gfunc_par_4)) $gfunc_par_4 = 0.5;

  $gfunc_par_5 = $_POST['gfunc_par_5'];
  if(empty($gfunc_par_5)) $gfunc_par_5 = 0.5;

  $gfunc_par_6 = $_POST['gfunc_par_6'];
  if(empty($gfunc_par_6)) $gfunc_par_6 = 1;

  $gfunc_par_7 = $_POST['gfunc_par_7'];
  if(empty($gfunc_par_7)) $gfunc_par_7 = 1;

  $gfunc_par_8 = $_POST['gfunc_par_8'];
  if(empty($gfunc_par_8)) $gfunc_par_8 = 1.5;

  $convol_gfunc = array($gfunc_par_1, $gfunc_par_2, $gfunc_par_3, $gfunc_par_4, $gfunc_par_5, $gfunc_par_6, $gfunc_par_7, $gfunc_par_8);
  for ($i = 0 ; $i < 8 ; $i ++){
    $gfunc_par += $convol_gfunc[$i];
  }
  
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
    <button type="button" class="button color" onclick="location.href='./test_list.html'">
        <- TEST
    </button>
    <form method='post'>
      Convolution:
      <input type='text' name='insert' value='0' style="width:40px;">
      <br>
      Range : <input type='text' name='avg' value='<?php echo $avg; ?>' style="width:20px;">
      <br>
      <input type='text' name='gfunc_par_1' value='<?php echo $gfunc_par_1; ?>' style="width:30px;">
      <input type='text' name='gfunc_par_2' value='<?php echo $gfunc_par_2; ?>' style="width:30px;">
      <input type='text' name='gfunc_par_3' value='<?php echo $gfunc_par_3; ?>' style="width:30px;">
      <input type='text' name='gfunc_par_4' value='<?php echo $gfunc_par_4; ?>' style="width:30px;">
      <input type='text' name='gfunc_par_5' value='<?php echo $gfunc_par_5; ?>' style="width:30px;">
      <input type='text' name='gfunc_par_6' value='<?php echo $gfunc_par_6; ?>' style="width:30px;">
      <input type='text' name='gfunc_par_7' value='<?php echo $gfunc_par_7; ?>' style="width:30px;">
      <input type='text' name='gfunc_par_8' value='<?php echo $gfunc_par_8; ?>' style="width:30px;">
      <br><input type='text' name='kickboard' placeholder='' value='<?php echo $kickboard; ?>' style="width:60px;">
      <br>
      <input type='text' name='date' placeholder='YYYY-mm-dd' value='<?php echo $date; ?>' style="width:100px;">
      <br>
      <input type='text' name='start_time' placeholder='00:00:00' value='<?php echo $start_time; ?>' style="width:120px;">
      ~
      <input type='text' name='end_time' placeholder='23:59:59' value='<?php echo $end_time; ?>' style="width:120px;">
      <input type='submit' value='CONVOLUTION' style="height:30px;">
    </form>
    <?php echo mysqli_num_rows($result); ?> Records Convolution from mark2 -> mark3
    <div style="text-align:center">
    <?php
      $num = 0;
      while($rows = mysqli_fetch_array($result) ){
        $num++;
        // array: current ----- past
        for ( $i = $avg - 1 ; $i > 0 ; $i--) {
          $deg_con_x[$i] = $deg_con_x[$i - 1];
          $deg_con_y[$i] = $deg_con_y[$i - 1];
          $deg_con_z[$i] = $deg_con_z[$i - 1];
          $agv_con_x[$i] = $agv_con_x[$i - 1];
          $agv_con_y[$i] = $agv_con_y[$i - 1];
          $agv_con_z[$i] = $agv_con_z[$i - 1];
          $acc_con_x[$i] = $acc_con_x[$i - 1];
          $acc_con_y[$i] = $acc_con_y[$i - 1];
          $acc_con_z[$i] = $acc_con_z[$i - 1];
        }
        $deg_con_x[0] = $rows['deg_x'];
        $deg_con_y[0] = $rows['deg_y'];
        $deg_con_z[0] = $rows['deg_z'];
        $agv_con_x[0] = $rows['agv_x'];
        $agv_con_y[0] = $rows['agv_y'];
        $agv_con_z[0] = $rows['agv_z'];
        $acc_con_x[0] = $rows['acc_x'];
        $acc_con_y[0] = $rows['acc_y'];
        $acc_con_z[0] = $rows['acc_z'];

        if($num > $avg) {
          $deg_convulted_x = 0;
          $deg_convulted_y = 0;
          $deg_convulted_z = 0;
          $agv_convulted_x = 0;
          $agv_convulted_y = 0;
          $agv_convulted_z = 0;
          $acc_convulted_x = 0;
          $acc_convulted_y = 0;
          $acc_convulted_z = 0;

          for ( $i = $avg - 1 ; $i >= 0 ; $i--) {
            $deg_convulted_x += ($deg_con_x[$i]*$convol_gfunc[$i]/$gfunc_par);
            $deg_convulted_y += ($deg_con_y[$i]*$convol_gfunc[$i]/$gfunc_par);
            $deg_convulted_z += ($deg_con_z[$i]*$convol_gfunc[$i]/$gfunc_par);
            $agv_convulted_x += ($agv_con_x[$i]*$convol_gfunc[$i]/$gfunc_par);
            $agv_convulted_y += ($agv_con_y[$i]*$convol_gfunc[$i]/$gfunc_par);
            $agv_convulted_z += ($agv_con_z[$i]*$convol_gfunc[$i]/$gfunc_par);
            $acc_convulted_x += ($acc_con_x[$i]*$convol_gfunc[$i]/$gfunc_par);
            $acc_convulted_y += ($acc_con_y[$i]*$convol_gfunc[$i]/$gfunc_par);
            $acc_convulted_z += ($acc_con_z[$i]*$convol_gfunc[$i]/$gfunc_par);
          }

          echo "Convolution --> Degree: ".$deg_convulted_x.", y: ".$deg_convulted_y.", z: ".$deg_convulted_z;
          echo ",  AGV x: ".$agv_convulted_x.", y: ".$agv_convulted_y.", z: ".$agv_convulted_z;
          echo ",  ACC x: ".$acc_convulted_x.", y: ".$acc_convulted_y.", z: ".$acc_convulted_z;
          if( $avg != 1 && $insert == 1234 ){
            echo $insert;
            $sql_a = "INSERT INTO mark3 (record_date, kickboard, deg_x, deg_y, deg_z, agv_x, agv_y, agv_z, acc_x, acc_y, acc_z)
            VALUES ('$rows[record_date]', 'gfunc_3', $deg_convulted_x, $deg_convulted_y, $deg_convulted_z, $agv_convulted_x, $agv_convulted_y, $agv_convulted_z, $acc_convulted_x, $acc_convulted_y, $acc_convulted_z)";
            $result_a = mysqli_query($conn, $sql_a);
            if($result_a === false) echo mysqli_error($conn);
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