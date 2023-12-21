<?php require_once('dbconnect.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DELETE</title>
</head>

<body>
  <?php
  $id = $_GET['id'];
  
  if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    $stmt = $dbconnection->prepare("SELECT * FROM user_approvals WHERE id = ?");
    $stmt->bind_param('s', $phoneno);
    $stmt->execute();
    $stmt->store_result();
    $count = $stmt->num_rows;
    $stmt->close();
    if ($count > 0) {
      try {
        $sql = "DELETE users, user_approvals
            FROM users
            LEFT JOIN user_approvals ON users.id = user_approvals.user_id
            WHERE users.id = $id";
        $stmt = mysqli_query($dbconnection, $sql);
        if (!$stmt) {
          throw new Exception("Error in query: " . mysqli_error($dbconnection));
        }
      } catch (Exception $e) {
        echo  "Error :" . $e->getMessage();
      } finally {
        mysqli_close($dbconnection);
        header("Location: adminlistingpage.php");
      }
    }else {
      try {
        $sql = "DELETE FROM users WHERE id = $id";
        $stmt = mysqli_query($dbconnection, $sql);
        if (!$stmt) {
          throw new Exception("Error in query: " . mysqli_error($dbconnection));
        }
      } catch (Exception $e) {
        echo  "Error :" . $e->getMessage();
      } finally {
        mysqli_close($dbconnection);
        header("Location: adminlistingpage.php");
      }
    }
  }
  ?>
</body>

</html>