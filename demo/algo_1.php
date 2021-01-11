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
    
    $deg_x = array();
    $deg_y = array();
    $deg_z = array();
    $agv_x = array();
    $agv_y = array();
    $agv_z = array();
    $acc_x = array();
    $acc_y = array();
    $acc_z = array();
    $num = mysqli_num_rows($result_date);

    $acc_x_avg = 0;
    $acc_y_avg = 0;
    $acc_z_avg = 0;

    while( $rows = mysqli_fetch_array($result_date) ){
        array_push($deg_x, $rows['deg_x']);
        array_push($deg_y, $rows['deg_y']);
        array_push($deg_z, $rows['deg_z']);

        array_push($agv_x, $rows['agv_x']);
        array_push($agv_y, $rows['agv_y']);
        array_push($agv_z, $rows['agv_z']);

        array_push($acc_x, $rows['acc_x']);
        $acc_x_avg += $rows['acc_x']/$num;
        array_push($acc_y, $rows['acc_y']);
        $acc_y_avg += $rows['acc_y']/$num;
        array_push($acc_z, $rows['acc_z']);
        $acc_z_avg += $rows['acc_z']/$num;
    }

    $range = 12;

    $deg_x_avg_2s = 0;
    $deg_y_avg_2s = 0;
    $deg_z_avg_2s = 0;
    $acc_x_var = 0;
    $acc_y_var = 0;
    $acc_z_var = 0;

    $filename = "../data/layer_value.txt";
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
    //print_r($layer);
    
    $bump = 0;
    $bump_value = 10;

    for( $i = $num ; $i > $num - $range ; $i--){
        $deg_x_avg_2s += $deg_x[$i - 1]/$range;
        $deg_y_avg_2s += $deg_y[$i - 1]/$range;
        $deg_z_avg_2s += $deg_z[$i - 1]/$range;

        // $acc_x_var += pow( ( $acc_x[$i - 1] - $acc_x_avg), 2 ) / $range;
        // $acc_y_var += pow( ( $acc_y[$i - 1] - $acc_y_avg), 2 ) / $range;
        // $acc_z_var += pow( ( $acc_z[$i - 1] - $acc_z_avg), 2 ) / $range;

        $bump += (  abs($acc_z[$i - 1]) * $layer[$i - $num + $range - 1] );
    }

    $scope = 1;
    if( $deg_y_avg_2s >  $deg_x_avg_2s) $scope = 2;
    else if( $deg_y_avg_2s <  $deg_x_avg_2s) $scope = 3;
    else $scope = 1;
    
    $event = 0;
    // $acc_var_avg = ($acc_x_var + $acc_y_var + $acc_z_var) / 3;
    if( $bump > $bump_value)    $event = 1;
    
?>