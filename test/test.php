<?php
    header('Content-Type: text/html; charset=utf-8');
    include('../conn.php');
    
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    } else {
        echo "CONNECTION SUCCESS\n";
        echo " 김민준 므싰다 ";
        $sql = "SELECT * FROM mark1 WHERE kickboard='SCK00' AND (shock_date between '2020-10-20 16:38:57' AND '2020-10-20 16:44:17')";
        $result =  mysqli_query($conn, $sql);
    }
?>


<!DOCTYPE html>
<html style="font-size: 16px;">

    <head>
    <title>Value Dividing Test Page</title>
    </head>

    <body>
    <div style="text-align:center">
        <?php
            while($rows = mysqli_fetch_array($result) ){
                echo $rows['shock_date'].' Record: ';
                echo $rows['shock'].'   -->   x: ';
                $acc_x = round(($rows['shock']/1000000))/100;
                $acc_y = round(($rows['shock']%1000000)/1000)/100;
                $acc_z = round($rows['shock']%1000)/100;
                echo $acc_x.',  y: ';
                echo $acc_y.',  z: ';
                echo $acc_z;
                //$sql_a = "INSERT INTO mark1_test (shock_date, acc_x, acc_y, acc_z)
                //VALUES ('$rows[shock_date]',$acc_x, $acc_y, $acc_z)";
                $result_a = mysqli_query($conn, $sql_a);
                if($result_a === false){
                    echo mysqli_error($conn);
                }
                echo '<br>';
            }
        ?>
    </div>

    </body>

</html>