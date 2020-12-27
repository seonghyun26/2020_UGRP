<?php 
    error_reporting(E_ALL); 
    ini_set('display_errors',1); 

    include('dbcon.php');

    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


    if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android )
    {
        $kickboard=$_POST['kickboard'];
        $loc_x=$_POST['loc_x'];
        $loc_y=$_POST['loc_y'];
        $loc_z=$_POST['loc_z'];
        $acc_x=$_POST['acc_x'];
        $acc_y=$_POST['acc_y'];
        $acc_z=$_POST['acc_z'];
        $ang_x=$_POST['ang_x'];
        $ang_y=$_POST['ang_y'];
        $ang_z=$_POST['ang_z'];
        $latitude=$_POST['latitude'];
        $longitude=$_POST['longitude'];

        if(empty($kickboard)){
            $kickboard = 1;
        }   

        if(!isset($errMSG))
        {
            try{
                $stmt = $con->prepare('INSERT INTO mark2 (kickboard, loc_x, loc_y, loc_z, acc_x, acc_y, acc_z, ang_x, ang_y, ang_z, latitude, longitude ) VALUES(:kickboard, :loc_x, :loc_y, :loc_z, :acc_x, :acc_y, :acc_z, :ang_x, :ang_y, :ang_z, :latitude, :longitude)');
                $stmt->bindParam(':kickboard', $kickboard);
                $stmt->bindParam(':loc_x', $loc_x);
                $stmt->bindParam(':loc_y', $loc_y);
                $stmt->bindParam(':loc_z', $loc_z);
                $stmt->bindParam(':acc_x', $acc_x);
                $stmt->bindParam(':acc_y', $acc_y);
                $stmt->bindParam(':acc_z', $acc_z);
                $stmt->bindParam(':ang_x', $ang_x);
                $stmt->bindParam(':ang_y', $ang_y);
                $stmt->bindParam(':ang_z', $ang_z);
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
                loc_x: <input type = "text" name = "loc_x" />
                </br>
                loc_y: <input type = "text" name = "loc_y" />
                </br>
                loc_z: <input type = "text" name = "loc_z" />
                </br>
                acc_x: <input type = "text" name = "acc_x" />
                </br>
                acc_y: <input type = "text" name = "acc_y" />
                </br>
                acc_z: <input type = "text" name = "acc_z" />
                </br>
                ang_x: <input type = "text" name = "ang_x" />
                </br>
                ang_y: <input type = "text" name = "ang_y" />
                </br>
                ang_z: <input type = "text" name = "ang_z" />
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