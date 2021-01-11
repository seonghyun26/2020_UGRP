<?php
    $range = 12;
    $error_range = 0.2;
    include('./function.php');  

    // average value of xyz vector
    $x_avg = 0;
    $y_avg = 0;
    $z_avg = 0;
    $z_agv_avg = 0;
    for ( $i = 0 ; $i < $range ; $i++){
        $x_avg += round($acc_x[$num - $range + $i]/$range, 2);
        $y_avg += round($acc_y[$num - $range + $i]/$range, 2);
        $z_avg += round($acc_z[$num - $range + $i]/$range, 2);
        $z_agv_avg += round(abs($agv_z[$num - $range + $i]/$range), 2);
    }
    echo $x_avg;
    $xyz_avg = array($x_avg, $y_avg, $z_avg);

    $g_avg_mea = sqrt(pow($x_avg, 2) + pow($y_avg, 2) + pow($z_avg, 2));
    echo "Measured G value: ".round($g_avg_mea, 2);

    // Calculate theeta & new g vector
    $x_vec = array(1,0);
    $z_vec = array(0,0,1);
    $xy_avg = array($xyz_avg[0], $xyz_avg[1]);

    if($xyz_avg[1] < 0) $sign = 1;
    else $sign = -1;
    if($xyz_avg[0] < 0) $seta_x = asin(($xyz_avg[1]) / sqrt(pow($xyz_avg[0],2) + pow($xyz_avg[1],2))) + (M_PI*$sign);
    else $seta_x = asin((-$xyz_avg[1]) / sqrt(pow($xyz_avg[0],2) + pow($xyz_avg[1],2)));
    $seta_z = angle(3, $xyz_avg, $z_vec);


    //회전행렬 계산
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

    // new x y z 
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
?>