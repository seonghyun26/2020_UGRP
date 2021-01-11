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

    while ( $row = mysqli_fetch_array($result)){
        $csv_dump .= $row['record_date'].",";
        $csv_dump .= $row['deg_x'].",";
        $csv_dump .= $row['deg_y'].",";
        $csv_dump .= $row['deg_z'].",";
        $csv_dump .= $row['agv_x'].",";
        $csv_dump .= $row['agv_y'].",";
        $csv_dump .= $row['agv_z'].",";
        $csv_dump .= $row['acc_x'].",";
        $csv_dump .= $row['acc_y'].",";
        $csv_dump .= $row['acc_z'].",";
        $csv_dump .= "\r\n";
    }


    $filename = $date." ".$start_time."_".$kickboard.".csv";

    header("Content-type: application/vnd.ms-excel;charset=KSC5601" );
    header("Content-Disposition: attachment; filename=$filename");
    header("Content-Description: PHP4 Generated Data" );

    echo $csv_dump;
?>