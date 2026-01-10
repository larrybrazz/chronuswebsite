<?php
// Process career assessment form and store for AI analysis
header('Content-Type: application/json');

function json_error($message, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'error' => $message]);
    exit;
}

try {
    // Sanitize inputs
    $email = isset($_POST['email']) ? filter_var($_POST['email'], FILTER_SANITIZE_EMAIL) : '';
    $resume = isset($_POST['resume']) ? substr($_POST['resume'], 0, 5000) : '';
    $certifications = isset($_POST['certifications']) ? substr($_POST['certifications'], 0, 2000) : '';
    $temperament = isset($_POST['temperament']) ? substr($_POST['temperament'], 0, 2000) : '';
    $ideal_company = isset($_POST['ideal_company']) ? substr($_POST['ideal_company'], 0, 1000) : '';
    $phone = isset($_POST['phone']) ? filter_var($_POST['phone'], FILTER_SANITIZE_STRING) : '';
    $experience = isset($_POST['experience']) ? (int)$_POST['experience'] : 0;
    $salary_min = isset($_POST['salary_min']) ? (int)$_POST['salary_min'] : 0;
    $salary_max = isset($_POST['salary_max']) ? (int)$_POST['salary_max'] : 0;

    // Handle optional CV upload
    $cvPath = '';
    $cvOriginal = '';
    if (!empty($_FILES['cv_file']) && is_array($_FILES['cv_file']) && $_FILES['cv_file']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['pdf', 'doc', 'docx'];
        $ext = strtolower(pathinfo($_FILES['cv_file']['name'], PATHINFO_EXTENSION));
        if (in_array($ext, $allowed, true)) {
            $uploadDir = __DIR__ . '/data/uploads';
            if (!is_dir($uploadDir)) {
                if (!mkdir($uploadDir, 0755, true)) {
                    json_error('Could not create upload directory', 500);
                }
            }
            $safeName = preg_replace('/[^a-zA-Z0-9-_\.]/', '_', basename($_FILES['cv_file']['name']));
            $filename = uniqid('cv_', true) . '_' . $safeName;
            $dest = $uploadDir . '/' . $filename;
            if (move_uploaded_file($_FILES['cv_file']['tmp_name'], $dest)) {
                $cvPath = $dest;
                $cvOriginal = $_FILES['cv_file']['name'];
            } else {
                json_error('Unable to save uploaded CV', 500);
            }
        } else {
            json_error('Invalid CV file type. Please upload PDF, DOC, or DOCX.', 400);
        }
    }

    // Validate
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        json_error('Valid email required', 400);
    }
    if (empty($resume) || empty($temperament)) {
        json_error('Resume and temperament fields required', 400);
    }

    // Store assessment in DB or file
    $assessmentId = uniqid('career_', true);
    $timestamp = date('c');

    // Save to data directory
    $dataDir = __DIR__ . '/data';
    if (!is_dir($dataDir)) {
        if (!mkdir($dataDir, 0755, true)) {
            json_error('Could not create data directory', 500);
        }
    }

    $assessmentFile = $dataDir . '/assessment_' . $assessmentId . '.json';

    $energyDrainers = isset($_POST['energy_drainers']) ? (array)$_POST['energy_drainers'] : [];
    $energyGainers = isset($_POST['energy_gainers']) ? (array)$_POST['energy_gainers'] : [];

    $assessment = [
        'id' => $assessmentId,
        'email' => $email,
        'phone' => $phone,
        'timestamp' => $timestamp,
        'experience' => $experience,
        'salary_min' => $salary_min,
        'salary_max' => $salary_max,
        'resume' => $resume,
        'certifications' => $certifications,
        'temperament' => $temperament,
        'ideal_company' => $ideal_company,
        'energy_drainers' => $energyDrainers,
        'energy_gainers' => $energyGainers,
        'newsletter' => isset($_POST['newsletter']) ? 1 : 0,
        'cv_file_path' => $cvPath,
        'cv_original_name' => $cvOriginal,
        'status' => 'submitted',
    ];

    if (file_put_contents($assessmentFile, json_encode($assessment, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)) === false) {
        json_error('Unable to save assessment', 500);
    }

    // Send confirmation email (best-effort; ignore failure)
    $to = $email;
    $subject = 'Career Assessment Received - Your Blueprint Coming Soon';
    $message = "Hello,\n\n";
    $message .= "Thank you for completing the Chronus Career Assessment.\n\n";
    $message .= "We've received your profile and our expert panel is analyzing your background.\n\n";
    $message .= "Your personalized career blueprint will be emailed to you within 24 hours. You'll receive:\n";
    $message .= "• 3 distinct career paths (Safety, Pivot, Moonshot)\n";
    $message .= "• Salary benchmarks and market insights\n";
    $message .= "• 12-month action roadmap\n";
    $message .= "• Burnout prevention strategies\n\n";
    $message .= "In the meantime, check out our Events and Training pages for professional development opportunities.\n\n";
    $message .= "Best regards,\n";
    $message .= "Chronus Solutions";

    $headers = 'From: careers@chronussolutions.co.uk' . "\r\n";
    $headers .= 'Reply-To: careers@chronussolutions.co.uk' . "\r\n";

    @mail($to, $subject, $message, $headers);

    // Return success
    http_response_code(200);
    echo json_encode([
        'success' => true,
        'assessment_id' => $assessmentId,
        'email' => $email,
        'message' => 'Assessment submitted successfully. Your blueprint is being generated...'
    ]);
    exit;

} catch (Throwable $e) {
    json_error('Server error: ' . $e->getMessage(), 500);
}
