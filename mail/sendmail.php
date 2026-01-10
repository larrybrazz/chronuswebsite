<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $newsletter = isset($_POST['news']) ? 'Yes' : 'No';

    $mail = new PHPMailer(true);

    try {
        //Server settings
        $mail->SMTPDebug = 0;                      
        $mail->isSMTP();                                            
        $mail->Host       = 'smtp.gmail.com';                    
        $mail->SMTPAuth   = true;                                   
        $mail->Username   = 'stephenie7533@gmail.com';     // Your Gmail address
        $mail->Password   = 'pvtk fodt qamz jhqq';                             // Your Gmail password or App Password
        $mail->SMTPSecure = 'tls';         
        $mail->Port       = 587;                                    

        //Recipients
        $mail->setFrom($email, $name);
        $mail->addAddress('stephenie7533@gmail.com');     // Your email address
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(true);                                  
        $mail->Subject = "New Contact Form Submission: $subject";
        $mail->Body    = "You have received a new message from the user $name.<br>".
                         "Email: $email<br>".
                         "Phone: $phone<br>".
                         "Message:<br>$message<br>".
                         "Subscribe to Newsletter: $newsletter";

        $mail->send();
        echo "<script>alert('Message sent successfully!');</script>";
    } catch (Exception $e) {
        echo "<script>alert('Failed to send message. Error: {$mail->ErrorInfo}');</script>";
    }
}
?>
