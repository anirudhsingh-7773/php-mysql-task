<?php

/**
 * Class Credentials
 * 
 * Handles user password reset functionality, including database connection
 * and validation of reset key.
 */
class Credentials {

  /**
   * @var string $password
   * The new password to be set for the user.
   */
  private $password;

  /**
   * @var mysqli $conn
   * Database connection object for interacting with the MySQL database.
   */
  private $conn;

  /**
   * @var string $status
   * Status message indicating the result of the password reset operation.
   */
  public $status;

  /**
   * Credentials constructor.
   * Initializes the database connection and redirects logged-in users.
   */
  public function __construct() {
    // Start the session to track user login status.
    session_start();

    // Check if the user is already logged in.
    if ($_SESSION['logged_in'] == true) {
      // Redirect to the requested page or default to index.php with query parameter q=4.
      $redirectTo = isset($_GET['redirect']) ? $_GET['redirect'] : 'index.php?q=4';
      header("Location: $redirectTo");
      exit(); // Exit after header redirection to stop further script execution.
    }

    // Establish the database connection.
    $this->connectDatabase();
  }

  /**
   * Establishes a connection to the MySQL database.
   * @return void
   */
  private function connectDatabase() {
    $this->conn = new mysqli('localhost', 'assignmentuser', 'Hello@123', 'registration_form_db');

    // Check for a connection error.
    if ($this->conn->connect_error) {
      die('Connection failed: ' . $this->conn->connect_error);
    }
  }

  /**
   * Validates the reset key.
   * @return void
   */
  public function authKey() {

    $sql = "SELECT * FROM login_key LIMIT 1";

    $result = $this->conn->query($sql);

    if ($result) {
      // Fetch the record
      $row = $result->fetch_assoc();
    
      // Save the value of the first (and only) column in a variable
      $login_key = $row['unique_key'];
    } 
    else {
      echo "Error: " . $this->conn->error;
    }

    // Sanitized key to avoid XSS
    $url_key = htmlspecialchars($_GET['key']);
    // Check if the provided reset key matches the expected value.
    if ($url_key != $login_key) {
      echo '<h1>Authentication key is incorrect.</h1>';
      exit();
    }
  }

  /**
   * Sets a new password for the user and sends to login page.
   * @return void
   */
  public function setPassword() {
    // Sanitize the input to prevent XSS.
    $this->password = htmlspecialchars($_POST['password']);

    // Update the password in the database.
    $sql = "UPDATE credentials SET password = '$this->password' WHERE username = 'admin'";

    if (!$this->conn->query($sql)) {
      die("Error: " . $this->conn->error);
    }

    // Set a success message.
    echo "Password reset successful. You will be redirected to the login page in 3 seconds...";

    // Delete the unique key from database after password reset.
    $sql = "DELETE FROM login_key";

    if (!$this->conn->query($sql)) {
      echo "Error: " . $sql . "<br>" . $this->conn->error;
    }
    
    // close database connection.
    $this->conn->close();
    // Redirect to login page.
    header("Refresh: 3; url=login.php");
    exit();
  }
}

// Instantiate the Credentials class.
$credentials = new Credentials();

// Check if the request method is GET
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
  $credentials->authKey();
}
// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $credentials->setPassword();
}
?>

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
      <form action="credentials.php" method="post">
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