<?php
  header('Content-Type: text/html; charset=utf-8');
  setcookie("basis", 0, time() + 86400);
?>

<!DOCTYPE html>
<html style="font-size: 16px;">
  <title>Setup Demo</title>

  <head>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js"></script>
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="../css/button.css">
    <style type="text/css">
      .container {
          width: 80%;
          margin: 15px auto;
      }
    </style>
  </head>

  <body>
    <div align="center">
      <img src="../images/scooker_v3.png" alt="No Image Found" width="70%" onclick="location.href='../index.html'"><br>
    </div>
    
    <form method='post' action="./demo.php" align="center">
        <h1 style="font-size: 50px;">Kickboard Number</h1><br>
        <input class="number" type='text' name='kickboard' value='<?php echo $kickboard; ?>' style="text-align:center; font-size:30px; height: 60px;">
        <br><br><br>
        <input class="button demo" type='submit' value='SEARCH'>
    </form>
  
  </body>

</html>