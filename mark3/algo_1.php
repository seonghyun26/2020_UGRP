<?php
  include('../conn.php');

  $kickboard = $_POST['kickboard'];
  $date = $_POST['date'];
  $start_time = $_POST['start_time'];
  $end_time = $_POST['end_time'];

  if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
  } else {
    $sql = "SELECT * FROM mark3 WHERE kickboard = '$kickboard' AND (record_date BETWEEN '$mysql_start_date' AND '$mysql_end_date')";
    $result_date_acc =  mysqli_query($conn, $sql);
    $result_data_acc_x = mysqli_query($conn, $sql);
    $result_data_acc_y = mysqli_query($conn, $sql);
    $result_data_acc_z = mysqli_query($conn, $sql);
  }

?>

<!DOCTYPE html>
  <head>

  </head>

  <body>
    LOADING
    <?php
      echo "<script>alert('1번 알고리즘: {$test_var}');</script>";
    ?>

    <form method="POST" action="eval.php'">
      <input type="hidden" name="x" value="10">
      <input type="hidden" name="y" value="20">
      <input type="hidden" name="z" value="30">
      <input type="submit" value="Back">
    </form>
  </body>

</html>
