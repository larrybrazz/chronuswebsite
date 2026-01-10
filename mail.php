<?php
  if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];

    $to = 'redranks0@gmail.com';
    $headers = "From: " . $email;
    $txt = "You have received an email from " . $name . ".\n\n" . $message;

    mail($to,$subject,$txt,$headers);
    header("Location: index.php");
  }
?>

<!DOCTYPE html>
<html>
<head>
  <title>Contact Form</title>
<style>
.container {
  width: 50%;
  margin: 0 auto;
  padding: 20px;
  border: 1px solid #ccc;
  border-radius: 5px;
  text-align: center;
}

label {
  display: block;
  margin-bottom: 10px;
  font-size: 18px;
}

input[type="text"], input[type="email"], textarea {
  width: 100%;
  padding: 12px 20px;
  margin-bottom: 20px;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
  resize: vertical;
}

input[type="submit"] {
  background-color: #4CAF50;
  color: white;
  padding: 12px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

input[type="submit"]:hover {
  background-color: #45a049;
}
</style>
</head>
<body>
  <div class="container">
    <form action="mail.php" method="post">
      <label for="name">Name:</label>
      <input type="text" id="name" name="name" required>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required>

      <label for="subject">Subject:</label>
      <input type="text" id="subject" name="subject" required>

      <label for="message">Message:</label>
      <textarea id="message" name="message" required></textarea>

      <input type="submit" value="Send">
    </form>
  </div>
<a href="https://php.org" title="PHP tutorials">Powered by PHP.org</a>
</body>
</html>