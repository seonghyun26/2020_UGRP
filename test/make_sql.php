<?php
    header('Content-Type: text/html; charset=utf-8');
    include('../conn.php');  
    
    $file_name = $_POST['file_name'];
?>

<!DOCTYPE html>

  <head>
    <meta charset="utf-8">
    <title>Making SQL</title>
    <link rel="stylesheet" href="../css/button.css">
  </head>

  <body>
    <button type="button" class="button color" onclick="location.href='./test_list.html'">
        <- TEST
    </button>
    <h1>Making SQL</h1>

    <form method='POST'>
        <input type='text' name='file_name' value='<?php echo $file_name; ?>'>
        <input type='submit' name='make' value="MAKE" class="button dark">
    </form>

    <?php
        if (empty($file_name)){
            $dir = "../data";
            $handle  = opendir($dir);
            $files = array();
            $count = 0;
            
            // 디렉터리에 포함된 파일을 저장
            while (false !== ($filename = readdir($handle))) {
                if($filename == "." || $filename == ".."){
                    continue;
                }
                if(is_file($dir . "/" . $filename)){
                    $files[] = $filename; 
                    $count++;
                }
            }
            
            closedir($handle);
            sort($files);
            echo "<b>FILES</b><br>";
            echo "<hr size='4px' color='darkcyan' width='20%' align='left'>";
            for($i = 0 ; $i < $count ; $i = $i + 1){
                echo "<a>".$files[$i]."</a>";
                echo "<br><br>";
            }
        } else {
            $file = "../data/".$file_name;
            $handle = fopen($file, "r");
            $num = 0;

            if( is_file($file) ) {
                echo "From File: ".$file_name."<br>";
                while (($data = fgetcsv($handle, 1000, ",")) !== FALSE && !empty($data[1]) ) {
                    $num++;
                    $error = 0;
                    echo "<br>";
                    //echo "<br><br>Record ".$data[0].": ";
                    // echo "start_date: ".$data[1];
                    // echo ", end_date: ".$data[2];
                    // echo ", kickboard: ".$data[3];
                    
                    // echo ", road: ".$data[4];
                    $road = 0;
                    if( $data[4] == "도로") $road = 1;
                    else if ( $data[4] == "인도" )  $road = 2;
                    else $error = 1;
                    
                    // echo ", scope: ".$data[5];
                    $scope = 0;
                    if( $data[5] == "평지" ) $scope = 1;
                    else if ( $data[5] == "오르막" )  $scope = 2;
                    else if ( $data[5] == "내리막" )  $scope = 3;
                    else $error = 1;

                    // echo ", status: ".$data[6];
                    $status = 0;
                    if( $data[6] == "운행" ) $status = 1;
                    else if ( $data[6] == "보행" )  $status = 2;
                    else if ( $data[6] == "정치" )  $status = 3;
                    else if ( $data[6] == "주차" )  $status = 3;
                    else $error = 1;

                    // echo ", attach: ".$data[7];
                    $attach = 0;
                    if( $data[7] == "차량") $attach = 1;
                    else if ( $data[7] == "헬멧" )  $attach = 2;
                    else $error = 1;

                    // echo ", event: ".$data[8];
                    $event = 0 ;
                    if( $data[8] == "급발진" ) $event = 1;
                    else if ( $data[8] == "급커브" )  $event = 2;
                    else if ( $data[8] == "급정지" )  $event = 3;
                    else if ( $data[8] == "방지턱" )  $event = 4;
                    else if ( $data[8] == "턱(인도->도로)" )  $event = 5;
                    else if ( $data[8] == "충돌" )  $event = 6;
                    else if ( $data[8] == "전복" )  $event = 7;
                    else if ( $data[8] == "턱(도로->인도)" )    $event = 8;
                    else if ( $data[8] == "X" || $data[8] == "x" )  $event = 0;
                    else $error = 1;

                    if ( $error == 0 ){
                        $sql = "UPDATE mark2 SET road = '$road', scope='$scope', status='$status', attach='$attach'
                        WHERE kickboard='$data[3]' AND
                        (record_date BETWEEN '$data[1]' AND '$data[2]');";
                        $result = mysqli_query($conn, $sql);
                        if( $event != 0){
                            $sql_event = "UPDATE mark2 SET event='$event'
                            WHERE kickboard='$data[3]'
                            AND record_date='$data[9]';";
                            $result_event = mysqli_query($conn, $sql_event);
                        }
                    } else {
                        $sql = "Error";
                    }
                    echo "<p id='$data[0]'>"."SELECT * FROM mark2 where kickboard='$data[3]' AND (record_date BETWEEN '$data[1]' AND '$data[2]');";
                    echo "<br><br>".$sql."<br>";
                    if($result) echo "Success";
                    else echo "Fail";
                    echo "<br>";
                    if( $event != 0){
                        echo $sql_event."<br>";
                        if($result_event) echo "Event Success";
                        else echo "Event Fail";
                    }    
                    echo "<br></p><br><br>";
                    if( empty($data[1]))    break;
                }
                echo "<br><br>".$num." Records";
                fclose($handle);
            }
            else {
                echo "File doesn't exist";
            }
        }
    ?>
  </body>

</html>