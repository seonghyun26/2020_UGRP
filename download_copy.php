<?php
    include('conn.php');
    
    $kickboard = $_COOKIE['kickboard'];
    $date = $_COOKIE['date'];
    $start_time = $_COOKIE['start_time'];
    $end_time = $_COOKIE['end_time'];
    $mysql_start_date = DateTime::createFromFormat('Y-m-d H:i:s', $date. $start_time)->format('Y-m-d H:i:s');
    $mysql_end_date = DateTime::createFromFormat("Y-m-d H:i:s", $date. $end_time)->format('Y-m-d H:i:s');

    $sql = "SELECT * FROM mark2 WHERE kickboard = '$kickboard' AND (record_date BETWEEN '$mysql_start_date' AND '$mysql_end_date')";
    $result =  mysqli_query($conn, $sql);
    $num = mysqli_num_rows($result);

    $csv_dump .= "Record Date,degree_x,degree_y,degree_z,angular_v_x,angular_v_y,angular_v_z,acc_x,acc_y,acc_z";
    $csv_dump .= "\r\n";


    /////////////////////


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
    $road = array();
    $scope = array();
    $scope = array();
    
    $deg_x_avg = 0;
    while($row = mysqli_fetch_array($result)) {
        //print_r($row);
        array_push($result_date, $row['record_date']);
        array_push($deg_x, $row['deg_x']);
        array_push($deg_y, $row['deg_y']);
        array_push($deg_z, $row['deg_z']);
        $deg_x_avg += $rows['deg_x']/$num;
        
        array_push($agv_x, $row['agv_x']);
        array_push($agv_y, $row['agv_y']);
        array_push($agv_z, $row['agv_z']);
        array_push($acc_x, $row['acc_x']);
        array_push($acc_y, $row['acc_y']);
        array_push($acc_z, $row['acc_z']);
    }
    $range = 18;
    //$error_range = 0.2;
    
    include('/demo/function.php');

    for ($j = 0 ; $j < $num ; $j++){
        
         // average value of xyz vector
        $obs_x_avg = 0;
        $z_agv_avg = 0;
        $x_avg = 0;
        $y_avg = 0;
        $z_avg = 0;
        for ( $i = 0 ; $i < $range ; $i++){
            $obs_x_avg += round(abs($acc_x[$num - $range + $i]/$range), 2);
            $z_agv_avg += round(abs($agv_z[$num - $range + $i]/$range), 2);
            $x_avg += round($acc_x[$num - $range + $i]/$range, 2);
            $y_avg += round($acc_y[$num - $range + $i]/$range, 2);
            $z_avg += round($acc_z[$num - $range + $i]/$range, 2);
        }
        $xyz_avg = array($x_avg, $y_avg, $z_avg);
        
        $z_vec = array(0,0,1);
        $seta_z = angle(3, $xyz_avg, $z_vec);

        // road 도로 or 인도 
        $std_dev_z = 0;
        for ( $i = 0 ; $i < 18 ; $i++ ){
            $std_dev_z += round(pow( ($z_avg - $acc_z[$num - 18 + $i]), 2)/18, 3);
        }
        $std_dev_z = sqrt($std_dev_z);
        if($std_dev_z < 0.5) $road = 1;
        else $road = 2;

        if($std_dev_z < 0.2) $run = 1;
        else $run = 2;

        
        // 부착 위치
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

        // 경사로
        $scope = 1;
        $basis = 13;
        if( ($deg_x_avg >= ($basis-15) ) && ($deg_x_avg <= ($basis - 5)) )    $scope = 3;
        else if( ($deg_x_avg >= ($basis - 5)) && ($deg_x_avg <= ($basis + 5)) )    $scope = 1;
        else if( ($deg_x_avg >= ($basis + 5)) && ($deg_x_avg >= ($basis + 15)) )    $scope = 2;

        // z angular velocity -> 가속, 감속
        if ( $z_agv_avg > 30)   $event = 3;
        else $event = 0;
        
        // 턱 -> 등반, 낙하
        $obstacle = 0;
        if($obs_x_avg > 1.5)    $obstacle = 3;


        $csv_dump .= $deg_x[$i].",";
        $csv_dump .= $deg_y[$i].",";
        $csv_dump .= $deg_z[$i].",";
        $csv_dump .= $agv_x[$i].",";
        $csv_dump .= $agv_y[$i].",";
        $csv_dump .= $agv_z[$i].",";
        $csv_dump .= $acc_x[$i].",";
        $csv_dump .= $acc_y[$i].",";
        $csv_dump .= $acc_z[$i].",";


        $csv_dump .= "\r\n";
    }


    $filename = $date." ".$start_time."_".$kickboard.".csv";

    header("Content-type: application/vnd.ms-excel;charset=KSC5601" );
    header("Content-Disposition: attachment; filename=$filename");
    header("Content-Description: PHP4 Generated Data" );

    echo $csv_dump;
?>