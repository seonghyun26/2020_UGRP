<?php
    $kickboard = $_POST['kickboard'];
    if(empty($kickboard)){
        $kickboard = $_COOKIE['kickboard'];
        if(empty($kickboard))  $kickboard = 'SCK10';
    }
    setcookie("kickboard", $kickboard, time() + 86400);

    $now = date("Y-m-d H:i:s", strtotime("-1 second"));
    $range = date("Y-m-d H:i:s", strtotime("-7 second"));
    $mysql_start_date = DateTime::createFromFormat('Y-m-d H:i:s', $range)->format('Y-m-d H:i:s');
    $mysql_end_date = DateTime::createFromFormat('Y-m-d H:i:s', $now)->format('Y-m-d H:i:s');

    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    } else {
        $sql = "SELECT * FROM mark2 WHERE kickboard = '$kickboard' AND (record_date BETWEEN '$mysql_start_date' AND '$mysql_end_date')";
        $result_date =  mysqli_query($conn, $sql);
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
    
    $acc_xyz = array();
    $num = mysqli_num_rows($result_date);

    while( $rows = mysqli_fetch_array($result_date) ){
        array_push($acc_xyz, array($rows['acc_x'], $rows['acc_y'], $rows['acc_z']));
    }

    $scope = 1;
    $event = 0;

    $range = 6;
    $error_range = 0.2;

    $xyz_std = array(0.05, 0.05, 0.96);
    $xy_std = array(0.05, 0.05, 0);
    $yz_std = array(0, 0.05, 0.96);

    function angle($vector_1, $vector_2){
        $dot = 0;
        for($i = 0 ; $i < 3 ; $i++){
            $dot += $vector_1[$i] * $vector_2[$i];
            $size_1 += pow($vector_1[$i],2);
            $size_2 += pow($vector_2[$i],2);
        }
        $size_1 = sqrt($size_1);
        $size_2 = sqrt($size_2);
        $theta = acos( $dot / ( $size_1 * $size_2) );
        return $theta;
    }

    function mat_mul($matrix, $vector){
        $result = array(0,0,0);
        for($i = 0 ; $i < 3; $i++){
            for($j = 0; $j < 3; $j++){
                $result[$i] += $matrix[$i][$j] * $vector[$j]; 
            }         
        }
        return $result;
    }

    // Calculate current rotation matrix
    for ( $i = 0 ; $i < $range ; $i++){
        $theta_z = angle($acc_xyz[$num - $range + $i], $yz_std);
        $theta_x = angle($acc_xyz[$num - $range + $i], $xy_std);
        $cos_x = cos($theta_x);
        $sin_x = sin($theta_x);
        $rot_mat_z = array(
            array($cos_x, -$sin_x, 0),
            array($sin_x, $cos_x, 0),
            array(0, 0, 1)
        );
        $cos_z = cos($theta_z);
        $sin_z = sin($theta_z);
        $rot_mat_y = array(
            array($cos_z, 0, $sin_z),
            array(0, 1, 0),
            array(-$sin_z, 0, $cos_z)
        );

        $temp = mat_mul($rot_mat_z, $acc_xyz[$num - $range + $i]);
        $new_xyz = mat_mul($rot_mat_y, $temp);
        $new_xyz[0] = round($new_xyz[0] , 2);
        $new_xyz[1] = round($new_xyz[1] , 2);
        $new_xyz[2] = round($new_xyz[2] , 2);

        $error += angle($new_xyz, $xyz_std)/$range*180/3.14;
    }
    echo "Difference(in °): ".$error;

    
?>