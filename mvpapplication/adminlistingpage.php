<?php
session_start();
require_once('dbconnect.php');
if ($_SESSION['user'] === 'admin' && $_COOKIE['sessionID']) {
  if (isset($_POST['logout'])) {
    $_SESSION = array();
    if (ini_get("session.use_cookies")) {
      $params = session_get_cookie_params();
      setcookie(session_name(), '', time() - 42000, $params["path"], $params["domain"], $params["secure"], $params["httponly"]);
    }

    session_destroy();
    setcookie('sessionID', '', time() - 3600, '/', '', false, true);
    header("Location: index.php");
  }
?>
  <!DOCTYPE html>
  <html lang="en">

  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ADMIN</title>
  </head>

  <body>
    <?php
    $id = 0;
    try {
      $email = $_SESSION['user'];
      $sql = "SELECT * FROM users";
      $stmt = mysqli_query($dbconnection, $sql);

      if (!$stmt) {
        throw new Exception("Error in query: " . mysqli_error($dbconnection));
      }
      $total = mysqli_num_rows($stmt);
      if ($total != 0) {
    ?>
        <section class="display-section">
          <h2 align="center">View Page</h2>
          <table align="center" border="1px" cellpadding="8px" cellspacing="5px">
            <tr>
              <th>Name</th>
              <th>LastName</th>
              <th>Phoneno</th>
              <th>Email</th>
              <th>Gender</th>
              <th>Is Approved</th>
              <th>Edit/Delete</th>
              <th>Approve</th>
            </tr>
            <?php
            while ($result = mysqli_fetch_assoc($stmt)) {
              $id = $result['id'];
              echo "
                <tr>
                    <td>" . $result['name'] . "</td>
                    <td>" . $result['lastname'] . "</td>
                    <td>" . $result['phoneno'] . "</td>
                    <td>" . $result['email'] . "</td>
                    <td>" . $result['gender'] . "</td>
                    <td>" . $result['is_approved'] . "</td>
                    <td><a href='editpage.php?userid={$result['id']}'>Edit</a> | <a href='#FIXME'  onclick='confirmDelete()'>Delete</a></td>
                    <td><a href='#FIXME'  onclick='confirmApprove()'>Approve</a></td>
                </tr>
                ";
            }
            ?>
          </table>
        </section><br>
        <form method="post" action="<?php htmlspecialchars($_SERVER["PHP_SELF"]); ?>" align="center">
          <input type="submit" name="logout" value="Logout">
        </form>
  <?php
      } else {
        echo "No records found";
      }
    } catch (Exception $e) {
      echo "Error: " . $e->getMessage();
    } finally {
      mysqli_close($dbconnection);
    }
  } else {
    header("Location: index.php");
    exit();
  }
  ?>
  <script>
    function confirmDelete(id) {
      if (confirm("Are you sure you want to delete this record?")) {
        window.location.href = 'delete.php?id=<?php echo $id; ?>&confirm=yes';
      }
    }
    function confirmApprove() {
      if (confirm("Are you sure you want to Approve this record?")) {
        window.location.href = 'approve.php?id=<?php echo $id; ?>&confirm=yes';
      }
    }
  </script>
  </body>

  </html>
  