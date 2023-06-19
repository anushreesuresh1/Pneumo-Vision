<?php

$con = mysqli_connect("localhost","root","","register");
  // Initialize sessions
  session_start();

  // Check if the user is already logged in, if yes then redirect him to welcome page
  if(isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] === true){
    header("location: uploads.php");
    exit;
  }



  // Define variables and initialize with empty values
  $email = $password = '';
  $email_err = $password_err = '';

  // Process submitted form data
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // Check if email is empty
    if(empty(trim($_POST['email']))){
      $email_err = 'Please enter email.';
    } else{
      $email = trim($_POST['email']);
    }

    // Check if password is empty
    if(empty(trim($_POST['password']))){
      $password_err = 'Please enter your password.';
    } else{
      $password = trim($_POST['password']);
    }

    // Validate credentials
    if (empty($email_err) && empty($password_err)) {
      // Prepare a select statement
      $sql = 'SELECT id, email, password FROM users WHERE email = ?';

      if ($stmt = $con->prepare($sql)) {

        // Set parmater
        $param_email = $email;

        // Bind param to statement
        $stmt->bind_param('s', $param_email);

        // Attempt to execute
        if ($stmt->execute()) {

          // Store result
          $stmt->store_result();

          // Check if email exists. Verify user exists then verify
          if ($stmt->num_rows == 1) {
            // Bind result into variables
            $stmt->bind_result($id, $email, $hashed_password);

            if ($stmt->fetch()) {
              if (password_verify($password, $hashed_password)) {

                // Start a new session
                session_start();

                // Store data in sessions
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $id;
                $_SESSION['email'] = $email;

                // Redirect to user to page
                header('location: uploads.php');
              } else {
                // Display an error for passord mismatch
                $password_err = 'Invalid password';
              }
            }
          } else {
            $email_err = "email does not exists.";
          }
        } else {
          echo "Oops! Something went wrong please try again";
        }
        // Close statement
        $stmt->close();
      }

      // Close connection
      $con->close();
    }
  }
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Sign in</title>
  <link href="https://stackpath.bootstrapcdn.com/bootswatch/4.4.1/cosmo/bootstrap.min.css" rel="stylesheet" integrity="sha384-qdQEsAI45WFCO5QwXBelBe1rR9Nwiss4rGEqiszC+9olH1ScrLrMQr1KmDR964uZ" crossorigin="anonymous">
  <link rel="stylesheet" type= "text/css" href="style.css">
  <style>
    .wrapper{ 
      width: 500px; 
      padding: 20px; 
    }
    .wrapper h2 {text-align: center}
    .wrapper form .form-group span {color: red;}
   

  </style>
</head>
<body>
  <main>
 

    <section class="container wrapper">
      <h2 class="display-4 pt-5">Login</h2>
          <p class="text-center">Please fill this form to create an account.</p>
          <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="POST">
            <div class="form-group <?php (!empty($email_err))?'has_error':'';?>">
              <label for="email">email</label>
              <input type="text" name="email" id="email" class="form-control" value="<?php echo $email ?>">
              <span class="help-block"><?php echo $email_err;?></span>
            </div>

            <div class="form-group <?php (!empty($password_err))?'has_error':'';?>">
              <label for="password">Password</label>
              <input type="password" name="password" id="password" class="form-control" value="<?php echo $password ?>">
              <span class="help-block"><?php echo $password_err;?></span>
            </div>

            <div class="form-group">
              <input type="submit" class="btn btn-block btn-outline-primary" value="login">
            </div>
            <p>Don't have an account? <a href="register.php">Sign in</a>.</p>
          </form>
    </section>
  </main>
</body>
</html>