<?php
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Career-Blueprint-Report.pdf"');

try {
    $assessmentId = $_GET['id'] ?? null;
    
    if (!$assessmentId) {
        throw new Exception('Assessment ID required');
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
    
    // Generate plain text report
    $reportText = generatePlainTextReport($assessment);
    
    // For now, output as text (you can integrate with a PDF library like TCPDF later)
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="Career-Blueprint-Report.pdf"');
    
    // Simple PDF generation (basic version)
    echo generateSimplePDF($reportText, $assessment);
    
} catch (Exception $e) {
    header('Content-Type: text/plain');
    echo "Error: " . $e->getMessage();
}

function generatePlainTextReport($assessment) {
    $report = "PERSONALIZED CAREER BLUEPRINT REPORT\n";
    $report .= "Generated: " . date('F j, Y') . "\n";
    $report .= str_repeat("=", 60) . "\n\n";
    
    $report .= "PROFILE SUMMARY\n";
    $report .= str_repeat("-", 60) . "\n";
    $report .= "Years of Experience: " . ($assessment['experience'] ?? 'N/A') . "\n";
    $report .= "Target Salary Range: £" . ($assessment['salary_min'] ?? 'N/A') . " - £" . ($assessment['salary_max'] ?? 'N/A') . "\n\n";
    
    $report .= "ENERGY PROFILE\n";
    $report .= str_repeat("-", 60) . "\n";
    $report .= "Energy Gainers:\n";
    $gainers = $assessment['energy_gainers'] ?? [];
    if (is_array($gainers)) {
        foreach ($gainers as $gainer) {
            $report .= "  • " . ucfirst(str_replace('_', ' ', $gainer)) . "\n";
        }
    }
    $report .= "\nEnergy Drainers:\n";
    $drainers = $assessment['energy_drainers'] ?? [];
    if (is_array($drainers)) {
        foreach ($drainers as $drainer) {
            $report .= "  • " . ucfirst(str_replace('_', ' ', $drainer)) . "\n";
        }
    }
    
    $report .= "\n\nTHREE CAREER PATHS ANALYSIS\n";
    $report .= str_repeat("-", 60) . "\n\n";
    
    $report .= "1. SAFETY PATH: The Steady Climber\n";
    $report .= "   Predicted Salary Range: £" . (($assessment['salary_max'] ?? 100000) + 20000) . " - £" . (($assessment['salary_max'] ?? 100000) + 50000) . "\n";
    $report .= "   Characteristics: Stable growth, deep expertise, predictable progression\n\n";
    
    $report .= "2. PIVOT PATH: The Strategic Shifter\n";
    $report .= "   Predicted Salary Range: £" . (($assessment['salary_max'] ?? 100000) + 40000) . " - £" . (($assessment['salary_max'] ?? 100000) + 80000) . "\n";
    $report .= "   Characteristics: Higher earning potential, fresh challenges, adjacent industries\n\n";
    
    $report .= "3. MOONSHOT PATH: The Ambitious Entrepreneur\n";
    $report .= "   Predicted Salary Range: £" . (($assessment['salary_max'] ?? 100000) + 100000) . "+\n";
    $report .= "   Characteristics: Unlimited potential, full autonomy, maximum fulfillment\n\n";
    
    $report .= "\n12-MONTH ACTION ROADMAP\n";
    $report .= str_repeat("-", 60) . "\n";
    $report .= "Months 0-3:  Foundation & Exploration\n";
    $report .= "  → Skill audit and gap analysis\n";
    $report .= "  → Network building (target 5+ connections)\n";
    $report .= "  → Personal brand optimization\n\n";
    
    $report .= "Months 3-6:  Development & Positioning\n";
    $report .= "  → Upskilling and certification\n";
    $report .= "  → Portfolio projects (2-3 pieces)\n";
    $report .= "  → 10+ informational interviews\n\n";
    
    $report .= "Months 6-12: Transition & Implementation\n";
    $report .= "  → Certification completion\n";
    $report .= "  → Leadership demonstration\n";
    $report .= "  → Market entry or role transition\n\n";
    
    $report .= "\nBURNOUT PREVENTION STRATEGY\n";
    $report .= str_repeat("-", 60) . "\n";
    $report .= "• Establish clear work-life boundaries\n";
    $report .= "• Prioritize roles with autonomy and decision-making authority\n";
    $report .= "• Seek mission-aligned organizations\n";
    $report .= "• Allocate time for continuous learning\n\n";
    
    $report .= "\nNEXT STEPS\n";
    $report .= str_repeat("-", 60) . "\n";
    $report .= "This Week:\n";
    $report .= "  1. Optimize LinkedIn profile\n";
    $report .= "  2. Create list of 10 target companies\n";
    $report .= "  3. Schedule initial strategy call\n\n";
    
    $report .= "This Month:\n";
    $report .= "  1. Reach out to 10 contacts\n";
    $report .= "  2. Enroll in skill courses\n";
    $report .= "  3. Document key achievements\n\n";
    
    $report .= str_repeat("=", 60) . "\n";
    $report .= "Report Generated: " . date('F j, Y \a\t H:i') . "\n";
    $report .= "For questions or support, contact: support@chronuswebsite.com\n";
    
    return $report;
}

function generateSimplePDF($reportText, $assessment) {
    // Simple PDF generation using basic PDF format
    // For production, use TCPDF or similar library
    
    // For now, return as text file with .pdf extension
    // In production, integrate with TCPDF or similar
    
    $pdf = "%PDF-1.4\n";
    $pdf .= "1 0 obj\n<</Type/Catalog/Pages 2 0 R>>\nendobj\n";
    $pdf .= "2 0 obj\n<</Type/Pages/Kids[3 0 R]/Count 1>>\nendobj\n";
    
    // Create a simple text-based PDF (basic version)
    // For production, use a proper PDF library
    
    // Generate as downloadable text for now
    return $reportText;
}
?>
