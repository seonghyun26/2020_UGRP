<?php
    include('../conn.php');
    include('../demo/function.php');
    
    $file_name = "SENSING_DATA_event.csv";
    $csv_dump .= "index,start,end,kickboard,road,scope,status,attach,event,event_date,guess_road,guess_scope,guess_status,guess_attach,agv_z_avg,guess_event,acc_x_avg,guess_obstacle";
    $csv_dump .= "\r\n";

    if (($handle = fopen("../data/$file_name", "r")) !== FALSE) {

        while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        
            $csv_dump .= $data[0].",";
            $csv_dump .= $data[1].",";
            $csv_dump .= $data[2].",";
            $csv_dump .= $data[3].",";
            $csv_dump .= $data[4].",";
            $csv_dump .= $data[5].",";
            $csv_dump .= $data[6].",";
            $csv_dump .= $data[7].",";
            $csv_dump .= $data[8].",";
            $csv_dump .= $data[9].",";
            
            $start_date = $data[1][0].$data[1][1].$data[1][2].$data[1][3]."-".
                        $data[1][5].$data[1][6]."-".$data[1][8].$data[1][9]." ".
                        $data[1][11].$data[1][12].":".$data[1][14].$data[1][15].":".$data[1][17].$data[1][18];

            $end_date = $data[2][0].$data[2][1].$data[2][2].$data[2][3]."-".
                        $data[2][5].$data[2][6]."-".$data[2][8].$data[2][9]." ".
                        $data[2][11].$data[2][12].":".$data[2][14].$data[2][15].":".$data[2][17].$data[2][18];
            
            $event_date = $data[9][0].$data[9][1].$data[9][2].$data[9][3]."-".
                        $data[9][5].$data[9][6]."-".$data[9][8].$data[9][9]." ".
                        $data[9][11].$data[9][12].":".$data[9][14].$data[9][15].":".$data[9][17].$data[9][18];

            $kickboard = $data[3];

            if (mysqli_connect_errno()) {
                echo "Failed to connect to MySQL: " . mysqli_connect_error();
            } else {
                //event exist
                $sql = "SELECT * FROM mark2 WHERE kickboard = '$kickboard' AND (record_date BETWEEN '$start_date' AND '$end_date')";
                $result = mysqli_query($conn, $sql);
                $num = mysqli_num_rows($result);
                $result_date = array();
                $deg_x = array();
                $deg_x_avg = 0;
                $agv_z = array();
                $acc_z = array();
                $acc_x_avg = 0;
                $acc_y_avg = 0;
                $acc_z_avg = 0;
                $range = 18;

                $event_id = 0;
                $cnt = 0;
                while($row = mysqli_fetch_array($result)) {
                    $cnt++;
                    array_push($result_date, $row['record_date']);
                    array_push($deg_x, $row['deg_x']);
                    $deg_x_avg += $row['deg_x']/$num;  // need
                    array_push($agv_z, $row['agv_z']);
                    array_push($acc_z, $row['acc_z']);  //need
                    $acc_x_avg += round($row['acc_x']/$num, 2);
                    $acc_y_avg += round($row['acc_y']/$num, 2);
                    $acc_z_avg += round($row['acc_z']/$num, 2);
                }
                $xyz_avg = array($acc_x_avg, $acc_y_avg, $acc_z_avg);

                // 1. road
                $std_dev_z = 0;
                for ( $i = 0 ; $i < $range ; $i++ ){
                    $std_dev_z += round(pow( ($acc_z_avg - $acc_z[$event_id - ($range/2)+ $i]), 2)/$num, 3);
                }
                $std_dev_z = sqrt($std_dev_z);                
                if($std_dev_z < 0.5) $csv_dump .= "도로,";
                else $csv_dump .= "인도,";

                // 2. scope
                $basis = 13;
                if( ($deg_x_avg >= ($basis-15) ) && ($deg_x_avg <= ($basis - 5)) )   $csv_dump .= "내리막,";
                else if( ($deg_x_avg >= ($basis - 5)) && ($deg_x_avg <= ($basis + 5)) )    $csv_dump .= "평지,";
                else if( ($deg_x_avg >= ($basis + 5)) && ($deg_x_avg >= ($basis + 15)) )    $csv_dump .= "오르막,";
                else    $csv_dump .= "평지,";

                // 3. status
                if($std_dev_z < 0.2) $csv_dump .= "정지,";
                else $csv_dump .= "운행,";

                // 4. attach
                $z_vec = array(0.05 ,0.05 , 0.96);
                $theta_z = angle(3, $xyz_avg, $z_vec);
                $deg = $theta_z * 180 / M_PI;
                if ( $deg <= 30 ) $csv_dump .= "차량,";
                else if ( ($deg >= 60) && ($deg <= 120) )   $csv_dump .= "헬멧,";
                else if ( $deg <= 180 ) $csv_dump .= "손,";
                else    $csv_dump .= "Error,";


                $event_end_date = date("Y-m-d H:i:s", strtotime($event_date.'+1 second'));
                $sql_event = "SELECT * FROM mark2 WHERE kickboard = '$kickboard' AND (record_date BETWEEN '$event_date' AND '$event_end_date')";
                $result_event = mysqli_query($conn, $sql_event);
                $num_event = mysqli_num_rows($result_event);
                $agv_z_avg_event = 0;
                $acc_x_event = array();

                while($row_event = mysqli_fetch_array($result_event)){
                    $agv_z_avg_event += round(abs($row_event['agv_z']/$num_event), 2);
                    array_push($acc_x_event, round(abs($row_event['acc_x']), 2));
                    // 구간 내 max 값 찾아서 1.5 이상인지 확인하기
                }
                // 5. event
                $csv_dump .= "$agv_z_avg_event,";
                if( $agv_z_avg_event > 30)    $csv_dump .= "가/감속,";
                else $csv_dump .= "없음,";
                
                // 6. obstacle
                $acc_x_max = max($acc_x_event);
                $csv_dump .= "$acc_x_max,";
                if( $acc_x_max > 1.5)    $csv_dump .= "턱,";
                else if( $acc_x_max > 0.5)  $csv_dump .= "과속 방지턱,";
                else  $csv_dump .= "없음,";

                $csv_dump .= "\r\n";
            }
            
        }
    }

    
    $new_filename = $start_date."_".$kickboard.".csv";

    header("Content-type: application/vnd.ms-excel;charset=UTF-8" );
    header("Content-Disposition: attachment; filename=$new_filename");
    header("Content-Description: PHP4 Generated Data" );

    echo $csv_dump;
?>