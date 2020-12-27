<?php 
    error_reporting(E_ALL); 
    ini_set('display_errors',1); 

    include('dbcon.php');

    $android = strpos($_SERVER['HTTP_USER_AGENT'], "Android");


    if( (($_SERVER['REQUEST_METHOD'] == 'POST') && isset($_POST['submit'])) || $android )
    {
        $kickboard=$_POST['kickboard'];

        if(empty($kickboard)){
            $kickboard = 1;
        }   

        if(!isset($errMSG))
        {
            try{
                $stmt = $con->prepare('INSERT INTO accident (kickboard) VALUES(:kickboard)');
                $stmt->bindParam(':kickboard', $kickboard);

                if($stmt->execute())
                {
                    $successMSG = "New Data Added to Accident";
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
            <title>ACCIDENT RECORD</title>
            <style>
                .button {
                    border: none;
                    color:white;
                    padding: 15px 32px;
                    text-align: center;
                    text-decoration: none;
                    display: inline-block;
                    font-size: 24px;
                    margin: 4px 2px;
                }
                .blue {background-color: skyblue};
            </style>
        </head>
        <body>
            <header>
                <br>
                <button type="button" class="button blue" onclick="location.href='/index.html'">
                    HOME
                </button>
                <br>
                <button type="button" name="record" onclick="location.href='/gyro.html'">
                    PROTOTYPE 2 GYRO→
                </button>
                <?php include('shock_button.html'); ?>
            </header>

            <form action="<?php $_PHP_SELF ?>" method="POST">
                <center>
                </br>
                Kickboard Number: <input type = "text" name = "kickboard" />
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