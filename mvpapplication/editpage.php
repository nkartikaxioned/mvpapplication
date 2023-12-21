<?php require_once("dbconnect.php"); ?>
<?php
 if ($_COOKIE['sessionID']) {
  ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>EDIT</title>
</head>

<body>
  <?php
  $edit_id = $id = $nameErr = $emailErr = $phonenoErr = $passwordErr = $genderErr = $lastnameErr = "";
  $name = $gender = $lastname = $email = $phoneno = $password = $errorMsg = "";
  $genderErrorFlag = $emailerrorflag = $nameerrorflag = $passErrorFlag = $phoneerrorflag = 0;

  if (isset($_GET['id'])) {
    $edit_id = $_GET['id'];
    $editsql = "SELECT * FROM users WHERE id = $edit_id";
    $editstmt = mysqli_query($dbconnection, $editsql);

    try {
      if (!$editstmt) {
        throw new Exception("Error Fetching Data" . mysqli_error($dbconnection));
      }
      if (mysqli_num_rows($editstmt) > 0) {
        $result = mysqli_fetch_assoc($editstmt);
        $name = $result['name'];
        $lastname = $result['lastname'];
        $phoneno = $result['phoneno'];
        $email = $result['email'];
        $gender = $result['gender'];
        $password = $result['password'];
      } else {
        throw new Exception("No data found for editing with ID: $edit_id");
      }
    } catch (Exception $e) {
      echo "Error" . $e->getMessage();
    }
  }

  function validateInput($data)
  {
    $data = trim($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  function validateGender($gender)
  {
    global $genderErrorFlag;
    if (!isset($gender) || empty($gender)) {
      $genderErrorFlag = 1;
      return "Gender is required";
    }
    $genderErrorFlag = 0;
    return "";
  }

  function validatePassword($password)
  {
    global $passErrorFlag;
    $password = validateInput($password);
    if (empty($password)) {
      $passErrorFlag = 1;
      return "Password is required";
    } elseif (!preg_match("/^(?=.*[A-Za-z])(?=.*\d)(?=.*[@#$%^&+=!])[A-Za-z\d@#$%^&+=!]{8,}$/", $password)) {
      $passErrorFlag = 1;
      return "Enter a valid password";
    }
    $passErrorFlag = 0;
    return "";
  }

  function validateName($name)
  {
    global $nameerrorflag;
    $name = validateInput($name);
    if (empty($name)) {
      $nameerrorflag = 1;
      return "Value is required";
    } elseif (strlen($name) < 3) {
      $nameerrorflag = 1;
      return "Valid Value is required";
    } elseif (!preg_match("/^[a-zA-Z-']*$/", $name)) {
      $nameerrorflag = 1;
      return "Only letters are allowed";
    }
    $nameerrorflag = 0;
    return "";
  }

  function validateEmail($dbconnection, $email)
  {
    global $emailerrorflag;
    $email = validateInput($email);
    if (empty($email)) {
      $emailerrorflag = 1;
      return "Email is required";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $emailerrorflag = 1;
      return "Invalid email format";
    }
    $emailerrorflag = 0;
    return "";
  }

  function validatePhoneno($dbconnection, $phoneno)
  {
    global $phoneerrorflag;
    $phoneno = validateInput($phoneno);
    if (empty($phoneno)) {
      $phoneerrorflag = 1;
      return "Phone number is required";
    } elseif (!preg_match("/^[0-9]*$/", $phoneno)) {
      $phoneerrorflag = 1;
      return "Invalid phone number format";
    } elseif (strlen($phoneno) != 10) {
      $phoneerrorflag = 1;
      return "Invalid phone number length";
    }
    $phoneerrorflag = 0;
    return "";
  }

  if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['firstname'];
    $lastname = $_POST['lastname'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $phoneno = $_POST['phoneno'];
    $password = $_POST['password'];
    $nameErr = validateName($name);
    $lastnameErr = validateName($lastname);
    $emailErr = validateEmail($dbconnection, $email);
    $phonenoErr = validatePhoneno($dbconnection, $phoneno);
    $genderErr = validateGender($gender);
    $passwordErr = validatePassword($password);
    if ($nameerrorflag === 0 && $emailerrorflag === 0 && $passErrorFlag === 0 && $phoneerrorflag === 0 && $genderErrorFlag === 0 && (isset($_POST['id']))) {
      $edit_id = mysqli_real_escape_string($dbconnection, $_POST['id']);
      try {
        $updatesql = "UPDATE users SET name=?, lastname=?, phoneno=?, email=?, gender=? where id = ?";
        $stmt = mysqli_prepare($dbconnection, $updatesql);
        if (!$stmt) {
          throw new Exception("Error in preparing statement" . mysqli_connect($dbconnection));
        }
        mysqli_stmt_bind_param($stmt, "sssssi", $name, $lastname, $phoneno, $email, $gender, $edit_id);

        $result = mysqli_stmt_execute($stmt);
        if ($result) {
          echo "<h2>Data Updated Successfully</h2>";

          header("Location: adminlistingpage.php");
          exit();
        } else {
          echo "Error updating record: " . mysqli_error($conn);
        }
        mysqli_stmt_close($stmt);
      } catch (Exception $e) {
        echo "Error" . $e->getMessage();
      }
    }
  }
  ?>
  <section class="form-section">
    <h2>Enter Detail :</h2>
    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
      <input type="hidden" name="id" value="<?php echo $edit_id; ?>">
      <div class="form-field">
        <label for="fname">Name :</label>
        <input type="text" name="firstname" value="<?php echo $name; ?>"><br>
        <span class="error"><?php echo $nameErr; ?></span>
      </div>
      <br>
      <div class="form-field">
        <label for="Lname">Last Name :</label>
        <input type="text" name="lastname" value="<?php echo $lastname; ?>"><br>
        <span class="error"><?php echo $lastnameErr; ?></span>
      </div>
      <br>
      <div class="form-field">
        <label for="email">Email :</label>
        <input type="text" name="email" class="email" value="<?php echo $email; ?>"> <br>
        <span class="error"><?php echo $emailErr; ?></span>
      </div>
      <br>
      <div class="form-input">
        <label for="phoneno">Phone no :</label>
        <input type="number" name="phoneno" value="<?php echo $phoneno; ?>"><br>
        <span class="error"><?php echo $phonenoErr; ?></span>
      </div>
      <br>
      <div class="form-field">
        <label for="gender">Gender :</label>
        <input type="radio" name="gender" value="male" <?php echo ($gender == 'male') ? 'checked' : ''; ?>> Male
        <input type="radio" name="gender" value="female" <?php echo ($gender == 'female') ? 'checked' : ''; ?>> Female
        <input type="radio" name="gender" value="other" <?php echo ($gender == 'other') ? 'checked' : ''; ?>> Other<br>
        <span class="error"><?php echo $genderErr; ?></span>
      </div>
      <br>
      <div class="form-field">
        <label for="password">password :</label>
        <input type="password" name="password" value="<?php echo $password; ?>"><br>
        <span class="error"><?php echo $passwordErr; ?></span>
      </div>
      <br>
      <div class="submit-btn">
        <input type="submit" name="submit">
      </div>
    </form>
  </section>
  <p class="main-error"><?php echo $errorMsg; ?></p>
</body>

</html>
<?php
 }else{
   header("Location: index.php");
     exit();
 }
 ?>