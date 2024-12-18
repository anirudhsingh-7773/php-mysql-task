<?php include 'loginLogic.php' ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>Login</title>
</head>
<body>
  <div class="container">
    <!-- Login section -->
    <section>
      <h1>Login</h1>
      <!-- Login form -->
      <form method="post" action="login.php">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
        <br><br>

        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
        <br><br>

        <!-- Display error message if login fails. -->
        <?php if (isset($login->error)) {
          echo "<p style='color: red;'>{$login->error}</p>";
        } ?>

        <!-- Submit button. -->
        <button type="submit">Login</button>
      </form><br><br>

      <!-- Anchor tag to show form for resetting the password -->
      <a href="javascript:void(0);" onclick="document.getElementById('resetForm').style.display='block'">Reset Password</a><br>
      <?php echo "<span>" . $login->mail_send_status . "</span>" ?>
      
      <!-- Element containing the form (initially hidden) -->
      <div id="resetForm" style="display:none;">
        <!-- Form for entering mail id to send email with link. -->
        <form method="post" action="login.php">
          <label for="email">Your Email: </label>
          <input type="email" id="email" name="email" required>
          <br><br>

          <!-- Submit button for password reset. -->
          <button type="submit" name="reset_password">Submit</button>
        </form>
      </div>

    </section>
  </div>
</body>
</html>
