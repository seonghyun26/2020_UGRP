<?php
    header('Content-Type: text/html; charset=utf-8');
    include('../conn.php');
    
    if (mysqli_connect_errno()) {
        echo "Failed to connect to MySQL: " . mysqli_connect_error();
    } else {
        echo "CONNECTION SUCCESS\n";
        $sql = "SELECT * FROM mark1 WHERE kickboard='SCK06' AND (shock_date between '2020-12-28 00:00:00' AND '2020-12-28 16:00:00')";
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
                //$sql_a = "INSERT INTO mark1_test (shock_date, shock, acc_x, acc_y, acc_z)
                //VALUES ('$rows[shock_date]', 10, $acc_x, $acc_y, $acc_z)";
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