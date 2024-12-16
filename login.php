<?php

// Include Composer's autoloader to load PHPMailer.
require 'vendor/autoload.php';

/**
 * Class LoginLogic
 * Handles login and password reset functionality.
 */
class LoginLogic {

  /**
   * Database connection instance.
   * @var mysqli
   */
  private $conn;

  /**
   * Stores login error message.
   * @var string|null
   */
  public $error;

  /**
   * Key for resetting password.
   * @var string
   */
  private $key;

  /**
   * Status for reset mail.
   * @var string
   */
  public $mail_send_status;

  /**
   * Constructor to initialize the class.
   * @return void
   */
  public function __construct() {
    // Start the session to track user login status.
    session_start();

    // Connect to the database.
    $this->connectDatabase();

    // Check if the user is already logged in.
    if ($_SESSION['logged_in'] == TRUE) {
      // Redirect to the requested page or default to index.php with query parameter q=4.
      $redirectTo = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php?q=4';
      header("Location: $redirectTo");
      exit();
    }

    // Handle the form submission.
    $this->handleForm();
  }

  /**
   * Establishes a database connection.
   *
   * @return void
   */
  private function connectDatabase() {
    $this->conn = new mysqli('localhost', 'assignmentuser', 'Hello@123', 'registration_form_db');

    // Check for connection error.
    if ($this->conn->connect_error) {
      die('Connection failed: ' . $this->conn->connect_error);
    }
  }

  /**
   * Handles form submission for login.
   *
   * @return void
   */
  public function handleForm() {
    // Ensure username and password are provided.
    if (!empty($_POST['username']) && !empty($_POST['password'])) {
      if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize and assign the form inputs to variables.
        $username = htmlspecialchars($_POST['username']);
        $password = htmlspecialchars($_POST['password']);

        // Credentials for validation.
        $validUsername = 'admin';

        // Query to fetch the password for the given username.
        $sql = "SELECT password FROM credentials WHERE username = '$validUsername'";

        $result = $this->conn->query($sql);

        // Check for query execution errors.
        if (!$result) {
          die("Error executing query: " . $this->conn->error);
        }

        // Fetch the row with the password.
        $row = $result->fetch_assoc();

        // Check if the row is not empty, meaning the username was found.
        if ($row) {
          $validPassword = $row["password"];
        }
        else {
          echo "Username not found.";
        }

        // Validate credentials.
        if ($username === $validUsername && $password === $validPassword) {
          // Successful login, set session variable to indicate user is logged in.
          $_SESSION['logged_in'] = TRUE;

          // Redirect to the requested page or default to index.php with query parameter q=4.
          $redirectTo = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php?q=4';
          header("Location: $redirectTo");
          exit();
        }
        else {
          // Invalid credentials, show error message.
          $this->error = "Invalid username or password.";
        }
      }
    }
  }

  /**
   * Handles password reset functionality.
   *
   * @return void
   */
  public function resetPasswordMail() {
    // Checks if the entered mail is of authorized person.
    if ($_POST['email'] == 'anirudh@example.com') {
      // Stores entered mail id.
      $email = htmlspecialchars($_POST['email']);
      // Creates a unique key for authentication.
      $this->key = uniqid();

      // Delete any previous stored keys in database.
      $sql = "DELETE FROM login_key";

      if (!$this->conn->query($sql)) {
        echo "Error: " . $sql . "<br>" . $this->conn->error;
      }

      // Stores the unique authentication key in database.
      $sql = "INSERT INTO login_key (unique_key) VALUES ('$this->key')";

      if (!$this->conn->query($sql)) {
        echo "Error: " . $sql . "<br>" . $this->conn->error;
      }

      // PHPMailer instance.
      $mail = new PHPMailer\PHPMailer\PHPMailer();

      // Sending email with exception handling.
      try {
        // Server settings.
        $mail->isSMTP();
        $mail->Host = 'smtp.example.com'; // Set the SMTP server to use.
        $mail->SMTPAuth = TRUE;
        $mail->Username = 'anirudh@example.com'; // SMTP username.
        $mail->Password = 'anirudh'; // SMTP password.
        $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Recipients.
        $mail->setFrom('anirudh@example.com', 'Anirudh');
        $mail->addAddress($email); // Add recipient email.

        // Content.
        $mail->isHTML(TRUE);
        $mail->Subject = 'Password Reset Request';
        // Sending url with the unique id which will be later used for authentication.
        $mail->Body = "<a href='http://example.com/credentials.php?key=" . $this->key . "'>Reset Password</a>";

        // Send email.
        $mail->send();
      }
      catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
      }
    } else {
      $this->mail_send_status = "This is not admin's email.";
    }
  }

  /**
   * Close database connection.
   */
  public function __destruct() {
    $this->conn->close();
  }
}

// Check for request method to be POST.
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Create instance of the class.
  $login = new LoginLogic();
  // Calls the function for sending the email with reset password link.
  $login->resetPasswordMail();
}
?>

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
