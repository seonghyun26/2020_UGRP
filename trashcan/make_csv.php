<?php
  include('../conn.php');
  header('Content-Encoding: UTF-8');
  
  require 'zip.php';

  //$zip = new DirectZip();
  //$zip->open('브라우저로 보낼 압축파일 이름.zip');  

  $file_name = "SENSING_DATA_2_copy.csv";
  $num = 0; 
  if (($handle = fopen("../data/$file_name", "r")) !== FALSE) {

    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {

      $start_date = $data[1][0].$data[1][1].$data[1][2].$data[1][3]."-".
                  $data[1][5].$data[1][6]."-".$data[1][8].$data[1][9]." ".
                  $data[1][11].$data[1][12].":".$data[1][14].$data[1][15].":".$data[1][17].$data[1][18];

      $end_date = $data[2][0].$data[2][1].$data[2][2].$data[2][3]."-".
                  $data[2][5].$data[2][6]."-".$data[2][8].$data[2][9]." ".
                  $data[2][11].$data[2][12].":".$data[2][14].$data[2][15].":".$data[2][17].$data[2][18];

      $kickboard = $data[3];

      if (mysqli_connect_errno()) {
          echo "Failed to connect to MySQL: " . mysqli_connect_error();
      } else {
        $sql = "SELECT * FROM mark2 WHERE kickboard = '$kickboard' AND (record_date BETWEEN '$start_date' AND '$end_date')";
        $result = mysqli_query($conn, $sql);
        $new_filename = $date." ".$start_time."_".$kickboard.".csv";

        $file = fopen("/uploads/asdf", 'w');
        $data = array();
        
        while($row = mysqli_fetch_array($result)){
          $temp = array(
            $start_date,
            $end_date,
            $row['kickboard'],
            $row['deg_x'],
            $row['deg_y'],
            $row['deg_z'],
            $row['agv_x'],
            $row['agv_y'],
            $row['agv_z'],
            $row['acc_x'],
            $row['acc_y'],
            $row['acc_z'],
            $data[4],
            $data[5],
            $data[6],
            $data[7]
          );
          array_push($data, $temp);
        }
        
        // save each row of the data
        foreach ($data as $row)
        {
          fputcsv($file, $row);
        }
        exit();
      }
      
      //$zip->addFile(`/tmp/$csv_dump`); // 압축파일에 파일을 '추가할 파일3.jpg'로 추가
    }
  }
?>