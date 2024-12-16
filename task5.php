<?php 
// Include session check to ensure the user is logged in
include 'checkSession.php'; 
?>
<!-- task5.php -->
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="css/style.css">
  <title>PHP Task 5</title>
</head>

<body>
  <div class="container">
    <section>
      <h1>Fill the Form</h1>

      <!-- Form submission using POST method with file upload support -->
      <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . '?q=5'); ?>" enctype="multipart/form-data">
        
        <!-- First Name input -->
        <label for="fname">First Name: </label>
        <input type="text" name="fname" id="fname" oninput="autofill('fname','lname','fullname')" required placeholder="John">
        <span class="error" style="color: red;">* </span>
        <br><br>

        <!-- Last Name input -->
        <label for="lname">Last Name: </label>
        <input type="text" name="lname" id="lname" oninput="autofill('fname','lname','fullname')" required placeholder="Doe">
        <span class="error" style="color: red;">* </span>
        <br><br>

        <!-- Disabled input field for Full Name -->
        <label for="name">Full Name: </label>
        <input type="text" name="name" id="fullname" value="" disabled>
        <br><br>

        <!-- File upload input for image -->
        <label for="image">Choose an image:</label>
        <input type="file" id="image" name="uploadImage">
        <br><br>

        <!-- Textarea input for Subject Marks -->
        <label for="marks">Subject Marks (Subject|marks):</label>
        <textarea name="marks" id="marks" rows="5" cols="25" placeholder="English|80"></textarea>
        <br><br>

        <!-- Phone Number input with a placeholder for format validation -->
        <label for="phone">Phone (+91 xxxxxxxxxx): </label>
        <input type="text" id="phone" name="phone" placeholder="+91 1231231234" value="+91 ">
        <br><br>

        <!-- Email input with placeholder for validation -->
        <label for="email">Enter E-mail: </label>
        <input type="text" id="email" name="email" placeholder="email@example.com">
        <br><br>

        <!-- Submit button -->
        <input type="submit" name="submit" id="submit">
      </form>

      <!-- Navigation Pager to switch between task pages -->
      <div class="pager">
        <a href="index.php?q=<?php echo $taskNumber = 1; ?>">Question 1</a>
        <a href="index.php?q=<?php echo $taskNumber = 2; ?>">Question 2</a>
        <a href="index.php?q=<?php echo $taskNumber = 3; ?>">Question 3</a>
        <a href="index.php?q=<?php echo $taskNumber = 4; ?>">Question 4</a>
        <a style="background-color: black; color: white;" href="index.php?q=<?php echo $taskNumber = 5; ?>">Question 5</a>
        <a href="index.php?q=<?php echo $taskNumber = 6; ?>">Question 6</a>
        <a href="logout.php">Logout</a>
      </div>

      <!-- Include form.php for handling form submission logic -->
      <?php require 'form.php'; ?>
    </section>
  </div>

  <!-- External JavaScript file for additional functionality -->
  <script src="/js/script.js"></script>

</body>

</html>
