<?php 
    error_reporting(E_ALL); 
    ini_set('display_errors',1); 
    include('dbcon.php');
    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");

    if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android )
    {
        $kickboard=$_POST['kickboard'];
        $shock=$_POST['shock'];

        if(empty($kickboard)){
            $kickboard = 1;
        }   
        if(!isset($errMSG))
        {
            try{
                $stmt = $con->prepare('INSERT INTO mark1 (kickboard, shock) VALUES(:kickboard, :shock)');
                $stmt->bindParam(':kickboard', $kickboard);
                $stmt->bindParam(':shock', $shock);

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
            <title>INSERT PAGE</title>
        </head>

        <body>
        <header>
            <link rel="stylesheet" href="css/button.css">
            <?php include('shock_button.html'); ?>
        </header>

        <form action="<?php $_PHP_SELF ?>" method="POST">
            <center>
            </br>
            Kickboard: <input type = "text" name = "kickboard" />
            </br>
            </br>
            Shock: <input type = "text" name = "shock" />
            </br>
            </br>
            <input type = "submit" name = "submit" />
            </center>
        </form>
       
        </body>
    </html>

<?php 
    }
?>