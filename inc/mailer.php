<?php
// Minimal mail helper. For local dev this uses PHP mail(); for production, replace with PHPMailer/SMTP.
function send_registration_email($toEmail, $toName, $eventTitle, $status = 'registered', $notes = '') {
    $from = 'info@chronussolutions.co.uk';
    $subject = "Registration confirmation: $eventTitle";
    $message = "Hello " . ($toName ?: '') . ",\n\n";
    if ($status === 'paid') {
        $message .= "Thank you — your payment has been received and your registration for '$eventTitle' is confirmed.\n\n";
    } else {
        $message .= "Your registration for '$eventTitle' is confirmed.\n\n";
    }

    if (!empty($notes)) {
        $message .= "Notes from attendee:\n" . wordwrap($notes, 70) . "\n\n";
    }

    $message .= "We will contact you with further details.\n\n";
    $message .= "Regards,\nChronus Solutions";

    $headers = 'From: Chronus <' . $from . '>\r\n' .
               'Reply-To: ' . $from . '\r\n' .
               'X-Mailer: PHP/' . phpversion();

    // Use mail() - may not work on local dev without configuration.
    return mail($toEmail, $subject, $message, $headers);
}
