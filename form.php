<?php

/**
 * @file
 * Contains the Form class for handling form data validation and storage.
 */

/**
 * Class Form.
 *
 * Represents a form with inputs and validation logic.
 */
class Form {

  /**
   * The first name input.
   *
   * @var string
   */
  public $fname;

  /**
   * The last name input.
   *
   * @var string
   */
  public $lname;

  /**
   * The full name concatenated from first and last names.
   *
   * @var string
   */
  public $full_name;

  /**
   * An array to hold marks for subjects.
   *
   * @var array
   */
  public $marks_array = [];

  /**
   * Holds the raw input for marks.
   *
   * @var string
   */
  public $marks_input;

  /**
   * Holds the phone number.
   *
   * @var string
   */
  public $phone;

  /**
   * Holds the email address.
   *
   * @var string
   */
  public $email;

  /**
   * Holds all the output for the document file.
   *
   * @var string
   */
  public $content;

  /**
   * Name for the file.
   *
   * @var string
   */
  public $file_name;

  /**
   * Database connection object.
   *
   * @var \mysqli
   */
  public $conn;

  /**
   * Unique identifier for the user.
   *
   * @var string
   */
  public $unique_id;

  /**
   * Constructs a Form object and connects to the database.
   */
  public function __construct() {
    $this->connectToDatabase();
  }

  /**
   * Connects to the database.
   * @return void
   */
  public function connectToDatabase() {
    $this->conn = new mysqli('localhost', 'assignmentuser', 'Hello@123', 'registration_form_db');

    // Checks for error in connecting to database.
    if ($this->conn->connect_error) {
      die('Connection failed: ' . $this->conn->connect_error);
    }
  }

  /**
   * Cleans input data.
   *
   * @param string $data
   *   The input data to clean.
   *
   * @return string
   *   The sanitized input data.
   */
  public function testInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
  }

  /**
   * Validates and processes user names.
   * @return void
   */
  public function userNames() {
    // Checks if the input is empty.
    if (empty($_POST['fname'])) {
      $this->content .= '<p style="color: red;">Enter First Name</p>';
    }
    // Check if pattern is incorrect for input.
    elseif (!preg_match('/^[a-zA-Z]+$/', $_POST['fname'])) {
      $this->content .= '<p style="color: red;">First name can only contain letters!</p>';
    }
    // Store the input in variable after validation.
    else {
      $this->fname = $this->testInput($_POST['fname']);
    }

    // Checks if the input is empty.
    if (empty($_POST['lname'])) {
      $this->content .= '<p style="color: red;">Enter Last Name</p>';
    }
    // Check if pattern is incorrect for input.
    elseif (!preg_match('/^[a-zA-Z]+$/', $_POST['lname'])) {
      $this->content .= '<p style="color: red;">Last name can only contain letters!</p>';
    }
    // Store the input in variable after validation.
    else {
      $this->lname = $this->testInput($_POST['lname']);
    }

    // Checks that first and last name aren't empty
    if (!empty($this->fname) && !empty($this->lname)) {
      // Concatenate first and last name and store it as full name.
      $this->full_name = $this->fname . ' ' . $this->lname;
      $this->content .= '<h1>Hello ' . $this->full_name . '!</h1>';
      // Creating a unique id for setting as primary key in database.
      $this->unique_id = uniqid();

      // Inserting the validated input in database.
      $sql = "INSERT IGNORE INTO user_info (unique_id, first_name, last_name, full_name)
              VALUES ('$this->unique_id', '$this->fname', '$this->lname', '$this->full_name')";

      if (!$this->conn->query($sql)) {
        echo "Error: " . $sql . "<br>" . $this->conn->error;
      }
    }
  }

  /**
   * Validates and processes an uploaded image.
   * @return void
   */
  public function imageValidation() {
    // Check if any file has been uploaded.
    if (!isset($_FILES['uploadImage'])) {
      $this->content .= "<p style='color: red;'>No file uploaded.</p>";
      return;
    }

    // Storing the directory and file name.
    $target_dir = __DIR__ . '/uploads/';
    $target_file = $target_dir . basename($_FILES['uploadImage']['name']);
    $image_file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
    umask(0);

    // Check for invalid file type and stop execution.
    if (!in_array($image_file_type, ['jpg', 'jpeg', 'png', 'gif'])) {
      $this->content .= "<p style='color: red;'>Sorry, only JPG, JPEG, PNG & GIF files are allowed.</p>";
      return;
    }

    // Update the database with directory of uploaded file using $unique_id.
    $sql = "UPDATE user_info SET img_file = '$target_file' WHERE unique_id = '$this->unique_id'";

    if (!$this->conn->query($sql)) {
      echo "Error: " . $sql . "<br>" . $this->conn->error;
    }

    // Trying to store the file in server.
    if (move_uploaded_file($_FILES["uploadImage"]["tmp_name"], $target_file)) {
      // Check for question number. To set path for file accordingly.
      $img_src = $_GET['q'] == 6 ? __DIR__ . '/uploads/' : 'uploads/';
      $this->content .= '<img width="300" height="300" src="' . $img_src . htmlspecialchars($_FILES["uploadImage"]["name"]) . '"/>';
    }
    else {
      $this->content .= "<p style='color: red;'>Sorry, there was an error uploading your file.</p>";
    }
  }

  /**
   * Validates and processes marks input.
   * @return void
   */
  public function marksValidation() {
    // Checks that marks is not empty.
    if (!empty($_POST['marks'])) {
      $this->marks_input = $_POST['marks'];
      // Stores the input marks as an array.
      $lines = explode("\n", trim($this->marks_input));

      // Loop to store subjects and marks as an associated array.
      foreach ($lines as $line) {
        $line = trim($line);
        if (preg_match("/^[a-zA-Z\s]+\|[0-9]+$/", $line)) {
          list($subject, $mark) = explode('|', $line);
          // Convert marks into integer.
          $mark = (int) $mark;

          if ($mark <= 100) {
            $this->marks_array[] = [
              'subject' => $subject,
              'mark' => $mark,
            ];

            // Update the database and set marks in table using unique_id.
            $sql = "UPDATE user_info SET subject_marks = '$this->marks_input' WHERE unique_id = '$this->unique_id'";
            if (!$this->conn->query($sql)) {
              echo "Error: " . $sql . "<br>" . $this->conn->error;
            }
          }
          else {
            $this->content .= "<p style='color: red;'>Marks for $subject must be between 0 and 100.</p>";
            return;
          }
        }
        else {
          $this->content .= "<p style='color: red;'>Invalid format: $line (Correct format: Subject|Marks)</p>";
          return;
        }
      }
    }

    // Displaying the marks table.
    if (!empty($this->marks_array)) {
      $this->content .= '<h2>Your Marks:</h2>';
      $this->content .= '<table border="1" style="border-collapse: collapse; width: 50%; text-align: left;">';
      $this->content .= '<tr><th>Subject</th><th>Marks</th></tr>';

      foreach ($this->marks_array as $entry) {
        $this->content .= '<tr>';
        $this->content .= '<td>' . htmlspecialchars($entry['subject']) . '</td>';
        $this->content .= '<td>' . htmlspecialchars($entry['mark']) . '</td>';
        $this->content .= '</tr>';
      }

      $this->content .= '</table>';
    }
    else {
      $this->content .= '<p style="color: red;">No valid marks provided.</p>';
    }
  }

  /**
   * Validates and processes phone input.
   * @return void
   */
  public function phoneValidation() {
    // Checks for empty phone input.
    if (empty($_POST['phone'])) {
      $this->content .= '<p style="color: red;">Enter Phone Number</p>';
    } 
    // Checks for wrong phone number pattern.
    elseif (!preg_match('/^\+91\s?\d{10}$/', $_POST['phone'])) {
      $this->content .= '<p style="color: red;">Invalid Format For Phone Number</p>';
    } 
    // Updates the data and saves it into database.
    else {
      // Store input after validation.
      $this->phone = $this->testInput($_POST['phone']);
      $this->content .= '<p>Your phone number is ' . $this->phone . '</p>';

      // Update database and set phone num using unique_id.
      $sql = "UPDATE user_info SET phone_num = '$this->phone' WHERE unique_id = '$this->unique_id'";

      if (!$this->conn->query($sql)) {
        echo "Error: " . $sql . "<br>" . $this->conn->error;
      }
    } 
  }

  /**
   * Validates and processes email input.
   * @return void
   */
  public function emailValidation() {
    // Checks for empty input.
    if (empty($_POST['email'])) {
      $this->content .= "<p style='color: red;'>Enter Your Email</p>";
    }
    // Check for incorrect pattern in input.
    elseif (!preg_match('/^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,6}$/', $_POST['email'])) {
      $this->content .= "<p style='color: red;'>Invalid Format For Email</p>";
    }
    // Connect to API for email validation after validating the pattern.
    else {
      $access_key = '46ff2d5dd622a481bd7721e49f8e8660';
      $email_address = $this->testInput($_POST['email']);

      $ch = curl_init('https://apilayer.net/api/check?access_key=' . $access_key . '&email=' . $email_address . '');
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

      $json = curl_exec($ch);
      curl_close($ch);

      $validationResult = json_decode($json, true);

      if (!$validationResult['mx_found']) {
        $this->content .= "<p style='color: red;'>Correct syntax but invalid email</p>";
      }
      else {
        $this->email = $email_address;
        $this->content .= '<p>Your Email is valid</p>';
        // Update the database and set email address using unique_id.
        $sql = "UPDATE user_info SET email_address = '$this->email' WHERE unique_id = '$this->unique_id'";

        if (!$this->conn->query($sql)) {
          echo "Error: " . $sql . "<br>" . $this->conn->error;
        }
      }
    }
  }

  /**
   * Prints the content.
   * @return void
   */
  public function printContent() {
    echo $this->content;
  }

  /**
   * Generating PDF and download by user.
   * @return void
   */
  public function pdfDownload() {
    // Load TCPDF
    require_once('vendor/tecnickcom/tcpdf/tcpdf.php');

    // Generate PDF
    $pdf = new TCPDF();
    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Form Submission');
    $pdf->SetTitle('Form Data Output');
    $pdf->SetSubject('Generated PDF');
    $pdf->SetKeywords('form, pdf, download');

    // Set default header data
    $pdf->SetHeaderData('', 0, 'Form Submission Output', '');

    // Set margins
    $pdf->SetMargins(15, 27, 15);
    $pdf->SetHeaderMargin(5);
    $pdf->SetFooterMargin(10);

    // Add a page
    $pdf->AddPage();

    // Add the form content
    $htmlContent = $this->content;
    $pdf->writeHTML($htmlContent, true, false, true, false, '');

    // Output PDF to browser for download
    $pdf->Output('form-data.pdf', 'D'); // 'D' forces download
  }

  /**
   * Close database connection.
   */
  public function __destruct() {
    $this->conn->close();
  }
}

// Execute form actions if POST request is made
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $formdata = new Form();
  // Selects the question using url
  switch ($_GET['q']) {
    case 1:
      // For Question 1.
      $formdata->userNames();
      echo $formdata->content;
      break;
    case 2:
      // For Question 2.
      $formdata->userNames();
      $formdata->imageValidation();
      echo $formdata->content;
      break;
    case 3:
      // For Question 3.
      $formdata->userNames();
      $formdata->imageValidation();
      $formdata->marksValidation();
      echo $formdata->content;
      break;
    case 4:
      // For Question 4.
      $formdata->userNames();
      $formdata->imageValidation();
      $formdata->marksValidation();
      $formdata->phoneValidation();
      echo $formdata->content;
      break;
    case 5:
      // For Question 5.
      $formdata->userNames();
      $formdata->imageValidation();
      $formdata->marksValidation();
      $formdata->phoneValidation();
      $formdata->emailValidation();
      echo $formdata->content;
      break;
    case 6:
      // For Question 6.
      $formdata->userNames();
      $formdata->imageValidation();
      $formdata->marksValidation();
      $formdata->phoneValidation();
      $formdata->emailValidation();
      $formdata->pdfDownload();
      break;
    default:
      // For Invalid question number 
      echo "<h1>Error</h1>";
  }
}
