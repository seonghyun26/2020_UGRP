<?php
    $kickboard = $_POST['kickboard'];
    if(empty($kickboard)){
        $kickboard = $_COOKIE['kickboard'];
        if(empty($kickboard))  $kickboard = 'SCK14';
    }
    setcookie("kickboard", $kickboard, time() + 86400);

    $now = date("Y-m-d H:i:s");
    $past = date("Y-m-d H:i:s", strtotime("-6 second"));
    $mysql_start_date = DateTime::createFromFormat('Y-m-d H:i:s', $past)->format('Y-m-d H:i:s');
    $mysql_end_date = DateTime::createFromFormat('Y-m-d H:i:s', $now)->format('Y-m-d H:i:s');

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    } else {
        $sql = "SELECT * FROM mark2 WHERE kickboard = '$kickboard' AND (record_date BETWEEN '$mysql_start_date' AND '$mysql_end_date')";
        $result = mysqli_query($conn, $sql);
        $num = mysqli_num_rows($result);

        $result_date = array();
        $deg_x = array();
        $deg_y = array();
        $deg_z = array();
        $agv_x = array();
        $agv_y = array();
        $agv_z = array();
        $acc_x = array();
        $acc_y = array();
        $acc_z = array();
        
        $deg_x_avg = 0;
        while($row = mysqli_fetch_array($result)) {
            //print_r($row);
            array_push($result_date, $row['record_date']);
            array_push($deg_x, $row['deg_x']);
            array_push($deg_y, $row['deg_y']);
            array_push($deg_z, $row['deg_z']);
            $deg_x_avg += $row['deg_x']/$num;
            
            array_push($agv_x, $row['agv_x']);
            array_push($agv_y, $row['agv_y']);
            array_push($agv_z, $row['agv_z']);
            array_push($acc_x, $row['acc_x']);
            array_push($acc_y, $row['acc_y']);
            array_push($acc_z, $row['acc_z']);
        }
    }
    
    if( ($num > 18) && ($_COOKIE['basis'] == 0) ){
        setcookie("basis", $deg_x_avg, time() + 86400);
    }
    
    $range = 18;
    
    include('./function.php');  

    // average value of xyz vector
    $obs_x = array();
    $z_agv_avg = 0;
    $x_avg = 0;
    $y_avg = 0;
    $z_avg = 0;
    for ( $i = 0 ; $i < $range ; $i++){
        array_push($obs_x, round(abs($acc_x[$num - $range + $i]/$range), 2) );
        $z_agv_avg += round(abs($agv_z[$num - $range + $i]/$range), 2);
        $x_avg += round($acc_x[$num - $range + $i]/$range, 2);
        $y_avg += round($acc_y[$num - $range + $i]/$range, 2);
        $z_avg += round($acc_z[$num - $range + $i]/$range, 2);
    }
    $xyz_avg = array($x_avg, $y_avg, $z_avg);
    
    // $g_avg_mea = sqrt(pow($x_avg, 2) + pow($y_avg, 2) + pow($z_avg, 2));
    // echo "Measured G value: ".round($g_avg_mea, 2);

    // Calculate theta & new g vector
    // $x_vec = array(1,0);
    // if($xyz_avg[1] < 0) $sign = 1;
    // else $sign = -1;
    // if($xyz_avg[0] < 0) $seta_x = asin(($xyz_avg[1]) / sqrt(pow($xyz_avg[0],2) + pow($xyz_avg[1],2))) + (M_PI*$sign);
    // else $seta_x = asin((-$xyz_avg[1]) / sqrt(pow($xyz_avg[0],2) + pow($xyz_avg[1],2)));
    
    $z_vec = array(0,0,1);
    $seta_z = angle(3, $xyz_avg, $z_vec);

    // 회전행렬 계산
    /*
    if( ($g_avg_mea <= (1 + $error_range)) && ($g_avg_mea >= (1 - $error_range)) ){
        echo " -> G in range";
    } else {
        echo " -> G out of range";
        $seta_x = 0;
        $seta_z = 0;
    }
    echo " -> seta: ".$seta_x*180/M_PI.", ".$seta_z*180/M_PI;

    $cos_x = cos($seta_x);
    $sin_x = sin($seta_x);
    $rot_mat_z = array(
        array($cos_x, -$sin_x, 0),
        array($sin_x, $cos_x, 0),
        array(0, 0, 1)
    );

    $cos_z = cos(-$seta_z);
    $sin_z = sin(-$seta_z);
    $rot_mat_y = array(
        array($cos_z, 0, $sin_z),
        array(0, 1, 0),
        array(-$sin_z, 0, $cos_z)
    );

    echo "<br>Original Vector : ";
    print_r($xyz_avg);
    $temp = array(0,0,0);
    $temp = mat_mul($rot_mat_z, $xyz_avg);
    $temp[0] = round($temp[0] , 3);
    $temp[1] = round($temp[1] , 3);
    $temp[2] = round($temp[2] , 3);

    $new_xyz = array(0 , 0, 0);
    $new_xyz = mat_mul($rot_mat_y, $temp);
    $new_xyz[0] = round($new_xyz[0] , 3);
    $new_xyz[1] = round($new_xyz[1] , 3);
    $new_xyz[2] = round($new_xyz[2] , 3);

    echo "<br>New Vector : ";    
    print_r($new_xyz);
    */

    // 1. run
    $std_dev_z = 0;
    for ( $i = 0 ; $i < 18 ; $i++ ){
        $std_dev_z += round(pow( ($z_avg - $acc_z[$num - 18 + $i]), 2)/18, 3);
    }
    $std_dev_z = sqrt($std_dev_z);
    if($std_dev_z < 0.5) $road = 1;
    else $road = 2;

    // 2. road 도로 or 인도 
    if($std_dev_z < 0.2) $run = 1;
    else $run = 2;

    // 3. attach
    $attach = 1;
    $deg = $seta_z*180/M_PI;
    //echo "DEG: ".$deg;
    if ( $deg <= 30 ) {
        $attach = 1;
    } else if ( ($deg >= 60) && ($deg <= 120) ){
        $attach = 2;
    } else if ( $deg <= 180 ) {
        $attach = 3;
    }

    // 4. scope
    $scope = 1;
    $basis = $_COOKIE['basis'];
    if( ($deg_x_avg >= ($basis - 20) ) && ($deg_x_avg <= ($basis - 5)) )    $scope = 3;   //내리막
    else if( ($deg_x_avg >= ($basis - 5)) && ($deg_x_avg <= ($basis + 5)) )    $scope = 1;  //평지
    else if( ($deg_x_avg >= ($basis + 5)) && ($deg_x_avg >= ($basis + 20)) )    $scope = 2; //오르막

    // 5. event
    // z angular velocity -> 가속, 감속
    if ( $z_agv_avg > 30)   $event = 3;
    else $event = 0;
    
    // 6. obstacle
    // 턱 -> 등반, 낙하
    $acc_x_max = max($obs_x);
    //echo $acc_x_max;
    if( $acc_x_max > 1.5)    $obstacle = 2;
    else if( $acc_x_max > 0.5)  $obstacle = 1;
    else  $obstacle = 0;
?>