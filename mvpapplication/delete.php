<?php require_once('dbconnect.php'); ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DELETE</title>
  <script>
        function confirmDelete() {
          return confirm("Do you want to delete this record?");
        }
    </script>
</head>

<body>
  <?php
  $id = $_GET['id'];
  if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
    $stmt = $dbconnection->prepare("SELECT * FROM user_approvals WHERE user_id = ?");
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $stmt->store_result();
    $count = $stmt->num_rows;
    $stmt->close();
    if ($count > 0) {
      try {
        $sql = "DELETE FROM user_approvals WHERE user_id = $id";
        $stmt = mysqli_query($dbconnection, $sql);
        $delsql = "DELETE FROM users WHERE id = $id";
        $stmt1 = mysqli_query($dbconnection, $delsql);
        if (!$stmt && ! $stmt1) {
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
     <script>
        if (confirmDelete()) {
            window.location.href = 'delete.php?id=<?php echo $id; ?>&confirm=yes';
        } else {
            window.location.href = 'adminlistingpage.php';
        }
    </script>
</body>

</html>