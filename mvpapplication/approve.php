<?php
session_start();
require_once('dbconnect.php');
if ($_SESSION['user'] === 'admin' && $_COOKIE['sessionID']) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <script>
        function confirmApprove() {
          return confirm("Do you want to Aprove this record?");
        }
    </script>
</head>
<body>
    <?php 
$id = $_GET['id'];
$adminid=1;
if (isset($_GET['confirm']) && $_GET['confirm'] === 'yes') {
   $updatesql = "UPDATE users SET is_approved = 1 WHERE id = ?";
   $resultUpdate = mysqli_prepare($dbconnection, $updatesql);
   if (!$resultUpdate) {
    throw new Exception("Error in preparing statement" . mysqli_connect($dbconnection));
  }
  mysqli_stmt_bind_param($resultUpdate, "i", $id);
  $result = mysqli_stmt_execute($resultUpdate);

   $sql = "INSERT INTO user_approvals (user_id, approved_by)
VALUES (?,?)";
$resultInsert = mysqli_prepare($dbconnection, $sql);
if (!$resultInsert) {
    throw new Exception("Error in preparing statement" . mysqli_connect($dbconnection));
  }
  mysqli_stmt_bind_param($resultInsert, "ii", $id, $adminid);
  $result = mysqli_stmt_execute($resultInsert);

if ($resultUpdate && $resultInsert) {
    echo "User approved successfully.";
    header("Location: adminlistingpage.php");
} else {
    echo "Error: " . mysqli_error($dbconnection);
}

mysqli_close($dbconnection);
}
    ?>
    <script>
        if (confirmApprove()) {
            window.location.href = 'approve.php?id=<?php echo $id; ?>&confirm=yes';
        } else {
            window.location.href = 'adminlistingpage.php';
        }
    </script>
</body>
</html>
<?php
}else{
    header("Location: index.php");
    exit();
}
?>