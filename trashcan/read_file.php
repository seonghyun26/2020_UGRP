<?php
    // 파일 열기
    $filename = "sample.txt";
    if(file_exists($filename)){
        $fp = fopen($filename, "r");
        $bump = array();

        while( !feof($fp) ) {
            $temp = fgets($fp);
            array_push($bump, $temp);
        }

        print_r($bump);
        fclose($fp);
    } else {
        echo $filename." file doesn't exist!";
    }
    

    
?>