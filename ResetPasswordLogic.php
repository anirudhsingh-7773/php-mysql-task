<?php

/**
 * Class ResetPassword
 * 
 * Handles user password reset functionality, including database connection
 * and validation of reset key.
 */
class ResetPassword {

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
   * Stores the login key from database.
   * @var string $login_key
   */
  public $login_key;

  /**
   * Stores the key from get request.
   * @var string $url_key
   */
  public $url_key;

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
      $this->login_key = $row['unique_key'];
      // If the login_key is empty that means there was no key in database.
      if ($this->login_key == '') {
        // Set login_key as 1 so url_key and login_key are not equal.
        $this->login_key = 1;
      }
    } 
    else {
      echo "Error: " . $this->conn->error;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'GET') {
      // Sanitized key to avoid XSS
      $this->url_key = htmlspecialchars($_GET['key']);
      // Check if the provided reset key matches the expected value.
      if ($this->url_key != $this->login_key) {
        if ($this->login_key == 1) {
          echo '<h1>No Authentication key Found.</h1>';
        } 
        else {
          echo '<h1>Authentication key is incorrect.</h1>';
        } 
        exit();
      }
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
$resetPassword = new ResetPassword();

$resetPassword->authKey();

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $resetPassword->setPassword();
}