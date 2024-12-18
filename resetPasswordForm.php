<?php include 'ResetPasswordLogic.php' ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>Reset Password</title>
</head>

<body>
  <div class="container">
    <section>
      <form action="ResetPasswordLogic.php" method="post">
        <!-- Input field for the new password -->
        <input type="text" name="password" id="password" placeholder="Enter new password">
        <br><br>

        <!-- Submit button -->
        <input type="submit" name="submit">
      </form>

      <!-- Link to go to main page -->
      <a href="http://example.com">Login</a>
    </section>
  </div>
</body>

</html>