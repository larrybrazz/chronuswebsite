<?php
header('Content-Type: application/json');

try {
    $assessmentId = $_POST['id'] ?? null;
    $recipientEmail = $_POST['email'] ?? null;
    $personalMessage = $_POST['message'] ?? '';
    
    if (!$assessmentId || !$recipientEmail) {
        throw new Exception('Assessment ID and email required');
    }
    
    if (!filter_var($recipientEmail, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email address');
    }
    
    // Load assessment data
    $assessmentFile = __DIR__ . '/data/assessment_' . basename($assessmentId) . '.json';
    
    if (!file_exists($assessmentFile)) {
        throw new Exception('Assessment not found');
    }
    
    $assessment = json_decode(file_get_contents($assessmentFile), true);
    
    if (!$assessment) {
        throw new Exception('Invalid assessment data');
    }
    
    // Generate report content
    $reportContent = generateReportContent($assessment);
    
    // Prepare email
    $emailSubject = "Your Personalized Career Blueprint Report";
    $emailBody = generateEmailBody($assessment, $reportContent, $personalMessage);
    
    // Send email
    $headers = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: support@chronuswebsite.com\r\n";
    $headers .= "Reply-To: support@chronuswebsite.com\r\n";
    
    $mailSent = mail($recipientEmail, $emailSubject, $emailBody, $headers);
    
    if (!$mailSent) {
        throw new Exception('Failed to send email. Please try again or contact support.');
    }
    
    // Log email sent
    $logEntry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'assessment_id' => $assessmentId,
        'recipient' => $recipientEmail,
        'subject' => $emailSubject
    ];
    
    echo json_encode([
        'success' => true,
        'message' => 'Report sent successfully to ' . $recipientEmail
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function generateReportContent($assessment) {
    $yearsExp = $assessment['experience'] ?? 'N/A';
    $salaryMin = $assessment['salary_min'] ?? '40,000';
    $salaryMax = $assessment['salary_max'] ?? '120,000';
    
    $content = [
        'yearsExp' => $yearsExp,
        'salaryRange' => '£' . number_format($salaryMin) . ' - £' . number_format($salaryMax),
        'energyGainers' => $assessment['energy_gainers'] ?? [],
        'energyDrainers' => $assessment['energy_drainers'] ?? [],
        'idealCulture' => $assessment['ideal_company'] ?? '',
        'temperament' => $assessment['temperament'] ?? ''
    ];
    
    return $content;
}

function generateEmailBody($assessment, $reportContent, $personalMessage) {
    $yearsExp = $reportContent['yearsExp'];
    $salaryRange = $reportContent['salaryRange'];
    
    $emailBody = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
            .container { max-width: 600px; margin: 0 auto; padding: 20px; }
            .header { background: linear-gradient(135deg, #9acd32 0%, #7fa62d 100%); color: white; padding: 30px; border-radius: 8px 8px 0 0; text-align: center; }
            .header h1 { margin: 0; font-size: 28px; }
            .header p { margin: 10px 0 0 0; font-size: 14px; }
            .content { background: #f9f9f9; padding: 30px; border: 1px solid #e0e0e0; border-radius: 0 0 8px 8px; }
            .section { margin-bottom: 25px; }
            .section h2 { color: #9acd32; font-size: 18px; border-bottom: 2px solid #9acd32; padding-bottom: 10px; margin: 0 0 15px 0; }
            .section p { margin: 0 0 10px 0; }
            .paths { display: grid; grid-template-columns: 1fr; gap: 15px; margin: 15px 0; }
            .path-box { background: white; padding: 15px; border-radius: 6px; border-left: 4px solid #9acd32; }
            .path-box h3 { margin: 0 0 8px 0; color: #2c3e50; font-size: 14px; }
            .path-box p { margin: 0; font-size: 13px; color: #666; }
            .cta-button { display: inline-block; background: #9acd32; color: white; padding: 12px 30px; text-decoration: none; border-radius: 6px; margin-top: 15px; font-weight: bold; }
            .footer { background: #f0f0f0; padding: 20px; text-align: center; font-size: 12px; color: #666; border-top: 1px solid #ddd; }
        </style>
    </head>
    <body>
        <div class='container'>
            <div class='header'>
                <h1>Your Career Blueprint</h1>
                <p>Personalized Analysis Based on Your Profile</p>
            </div>
            
            <div class='content'>
                <div class='section'>
                    <h2>Hello!</h2>
                    <p>We've completed a comprehensive analysis of your career profile and personality. Based on your " . $yearsExp . " years of experience, your strengths, and your work preferences, we've identified three distinct career paths that align with your goals and earning potential.</p>
                </div>
                
                <div class='section'>
                    <h2>Your Profile Summary</h2>
                    <p><strong>Years of Experience:</strong> " . $yearsExp . "</p>
                    <p><strong>Target Salary Range:</strong> " . $salaryRange . "</p>
                </div>
                
                <div class='section'>
                    <h2>The 3 Career Paths Ahead</h2>
                    <div class='paths'>
                        <div class='path-box'>
                            <h3>🛡️ Safety Path: The Steady Climber</h3>
                            <p>Predicted salary: £" . (intval($assessment['salary_max'] ?? 100000) + 20000) . "–£" . (intval($assessment['salary_max'] ?? 100000) + 50000) . "</p>
                            <p>Stable growth, deep expertise, predictable progression in your field.</p>
                        </div>
                        <div class='path-box'>
                            <h3>📊 Pivot Path: The Strategic Shifter</h3>
                            <p>Predicted salary: £" . (intval($assessment['salary_max'] ?? 100000) + 40000) . "–£" . (intval($assessment['salary_max'] ?? 100000) + 80000) . "</p>
                            <p>Leverage skills in adjacent industries with higher market demand.</p>
                        </div>
                        <div class='path-box'>
                            <h3>🚀 Moonshot Path: The Ambitious Entrepreneur</h3>
                            <p>Predicted salary: £" . (intval($assessment['salary_max'] ?? 100000) + 100000) . "+</p>
                            <p>Build your own venture with unlimited earning potential.</p>
                        </div>
                    </div>
                </div>
                
                <div class='section'>
                    <h2>What's Included in Your Full Report</h2>
                    <p>Your complete career blueprint contains:</p>
                    <ul>
                        <li>✓ Detailed 3-career path analysis with industry benchmarks</li>
                        <li>✓ Personalized salary projections for each path</li>
                        <li>✓ 12-month action roadmap with specific milestones</li>
                        <li>✓ Burnout prevention and sustainability strategies</li>
                        <li>✓ Next steps and recommended resources</li>
                    </ul>
                </div>
                
                <div class='section'>
                    <h2>Your 12-Month Action Roadmap</h2>
                    <p><strong>Months 0-3:</strong> Foundation & Exploration</p>
                    <p style='margin-left: 20px; font-size: 13px;'>Skill audit, network building, personal brand optimization</p>
                    
                    <p style='margin-top: 15px;'><strong>Months 3-6:</strong> Development & Positioning</p>
                    <p style='margin-left: 20px; font-size: 13px;'>Upskilling, portfolio projects, informational interviews</p>
                    
                    <p style='margin-top: 15px;'><strong>Months 6-12:</strong> Transition & Implementation</p>
                    <p style='margin-left: 20px; font-size: 13px;'>Certification completion, leadership demonstration, market entry</p>
                </div>";
    
    if (!empty($personalMessage)) {
        $emailBody .= "
                <div class='section'>
                    <h2>Personal Message</h2>
                    <p>" . htmlspecialchars($personalMessage) . "</p>
                </div>";
    }
    
    $emailBody .= "
                <div class='section'>
                    <p><strong>Next Steps:</strong></p>
                    <ol>
                        <li>Review the three career paths and identify which resonates most</li>
                        <li>Identify 2-3 skill gaps you need to address</li>
                        <li>Schedule 5-10 informational interviews in your target field</li>
                        <li>Create a 90-day action plan based on your chosen path</li>
                        <li>Share feedback with us—we'd love to hear how the blueprint helps</li>
                    </ol>
                </div>
                
                <div class='section'>
                    <p style='background: #e8f4f8; padding: 15px; border-radius: 6px; border-left: 4px solid #17a2b8;'>
                        <strong>Need Support?</strong><br>
                        Our career coaches and mentors are available to help you implement your blueprint. 
                        <a href='https://chronuswebsite.com/contact/' style='color: #17a2b8;'>Schedule a consultation</a>
                    </p>
                </div>
            </div>
            
            <div class='footer'>
                <p>© " . date('Y') . " Chronus Career Assessment. All rights reserved.</p>
                <p>This report is confidential and prepared specifically for you based on your responses.</p>
            </div>
        </div>
    </body>
    </html>";
    
    return $emailBody;
}
?>
