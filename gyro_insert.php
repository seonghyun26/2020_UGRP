<?php 
    error_reporting(E_ALL); 
    ini_set('display_errors',1); 

    include('dbcon.php');

    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


    if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android )
    {
        $kickboard=$_POST['kickboard'];
        $deg_x=$_POST['deg_x'];
        $deg_y=$_POST['deg_y'];
        $deg_z=$_POST['deg_z'];
        $agv_x=$_POST['agv_x'];
        $agv_y=$_POST['agv_y'];
        $agv_z=$_POST['agv_z'];
        $acc_x=$_POST['acc_x'];
        $acc_y=$_POST['acc_y'];
        $acc_z=$_POST['acc_z'];

        $latitude=$_POST['latitude'];
        $longitude=$_POST['longitude'];

        if(empty($kickboard)){
            $kickboard = 1;
        }   

        if(!isset($errMSG))
        {
            try{
                $stmt = $con->prepare('INSERT INTO mark2 (kickboard, deg_x, deg_y, deg_z, agv_x, agv_y, agv_z, acc_x, acc_y, acc_z, latitude, longitude )
                VALUES(:kickboard, :deg_x, :deg_y, :deg_z, :agv_x, :agv_y, :agv_z, :acc_x, :acc_y, :acc_z, :latitude, :longitude)');
                $stmt->bindParam(':kickboard', $kickboard);
                $stmt->bindParam(':deg_x', $deg_x);
                $stmt->bindParam(':deg_y', $deg_y);
                $stmt->bindParam(':deg_z', $deg_z);
                $stmt->bindParam(':agv_x', $agv_x);
                $stmt->bindParam(':agv_y', $agv_y);
                $stmt->bindParam(':agv_z', $agv_z);
                $stmt->bindParam(':acc_x', $acc_x);
                $stmt->bindParam(':acc_y', $acc_y);
                $stmt->bindParam(':acc_z', $acc_z);
                $stmt->bindParam(':latitude', $latitude);
                $stmt->bindParam(':longitude', $longitude);


                if($stmt->execute())
                {
                    $successMSG = "New Data Added to DB";
                }
                else
                {
                    $errMSG = "Error : 나한테 알려줘";
                }

            } catch(PDOException $e) {
                die("Database error: " . $e->getMessage()); 
            }
        }
    }
?>


<?php 
    if (isset($errMSG)) echo $errMSG;
    if (isset($successMSG)) echo $successMSG;

	$android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");
   
    if( !$android )
    {
?>
    <html>
        <head>
            <title>Gyro Insert</title>
            <link rel="stylesheet" href="css/button.css">
        </head>

        <body>
        <header>
            <?php include('gyro_button.html'); ?>
        </header>
        <div align="center">
            <form action="<?php $_PHP_SELF ?>" method="POST">
                </br>
                Kickboard: <input type = "text" name = "kickboard" />
                </br>
                deg_x: <input type = "text" name = "deg_x" />
                </br>
                deg_y: <input type = "text" name = "deg_y" />
                </br>
                deg_z: <input type = "text" name = "deg_z" />
                </br>
                agv_x: <input type = "text" name = "agv_x" />
                </br>
                agv_y: <input type = "text" name = "agv_y" />
                </br>
                agv_z: <input type = "text" name = "agv_z" />
                </br>
                acc_x: <input type = "text" name = "acc_x" />
                </br>
                acc_y: <input type = "text" name = "acc_y" />
                </br>
                acc_z: <input type = "text" name = "acc_z" />
                </br>
                Latitude: <input type = "text" name = "latitude" />
                </br>
                Longitude: <input type = "text" name = "longitude" />
                </br>
                <input type = "submit" name = "submit" />
            </form>
        </div>
       
        </body>
    </html>

<?php 
    }
?>