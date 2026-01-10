<?php
header('Content-Type: application/json');

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
    
    // Generate personalized report based on assessment
    $report = generateCareerReport($assessment);
    $html = formatReportHTML($report);
    
    echo json_encode([
        'success' => true,
        'assessment_id' => $assessmentId,
        'report' => $report,
        'reportHTML' => $html,
        'html_preview' => $html
    ]);
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

function generateCareerReport($assessment) {
    $profile = buildProfile($assessment);

    return [
        'executive_summary' => generateExecutiveSummary($profile),
        'three_career_paths' => generateCareerPaths($profile),
        'salary_benchmarks' => generateSalaryBenchmarks($profile),
        'personality_fit' => generatePersonalityFit($profile),
        'action_roadmap' => generateActionRoadmap($profile),
        'burnout_prevention' => generateBurnoutPrevention($profile),
        'next_steps' => generateNextSteps($profile),
        'profile' => $profile
    ];
}

function buildProfile($assessment) {
    $drainers = $assessment['energy_drainers'] ?? [];
    $gainers = $assessment['energy_gainers'] ?? [];
    $resume = trim($assessment['resume'] ?? '');
    $temperament = trim($assessment['temperament'] ?? '');
    $certs = trim($assessment['certifications'] ?? '');
    $idealCompany = trim($assessment['ideal_company'] ?? '');

    return [
        'id' => $assessment['id'] ?? '',
        'years_exp' => (int)($assessment['experience'] ?? 0),
        'salary_min' => (int)($assessment['salary_min'] ?? 0),
        'salary_max' => (int)($assessment['salary_max'] ?? 0),
        'resume' => $resume,
        'resume_len' => strlen($resume),
        'temperament' => $temperament,
        'certifications' => $certs,
        'ideal_company' => $idealCompany,
        'drainers' => $drainers,
        'gainers' => $gainers,
        'cv_uploaded' => !empty($assessment['cv_file_path']),
        'cv_name' => $assessment['cv_original_name'] ?? ''
    ];
}

function generateExecutiveSummary($profile) {
    $yearsExp = $profile['years_exp'];
    $gainers = $profile['gainers'];
    $drainers = $profile['drainers'];

    $parts = [];
    $parts[] = "You bring approximately {$yearsExp} years of experience with a blend of strengths and preferences that shape your next move.";

    if (in_array('problem_solving', $gainers)) {
        $parts[] = 'You thrive when solving complex problems and would excel in roles that reward analytical thinking and structured decision-making.';
    }
    if (in_array('autonomy', $gainers)) {
        $parts[] = 'You prefer autonomy and will likely feel energized in roles with clear ownership and decision rights.';
    }
    if (in_array('mentoring', $gainers)) {
        $parts[] = 'You gain energy from mentoring; people-lead roles or coaching components can boost fulfillment.';
    }
    if (in_array('fast_pace', $gainers)) {
        $parts[] = 'You are comfortable in fast-paced environments and can absorb rapid change.';
    }
    if (in_array('public_speaking', $drainers)) {
        $parts[] = 'Limit heavy presentation loads or provide speaking support while you build confidence.';
    }
    if (in_array('ambiguity', $drainers)) {
        $parts[] = 'Bias toward roles with clear KPIs, roadmaps, and documented processes to avoid ambiguity fatigue.';
    }

    $parts[] = 'Below are three tailored paths (Safety, Pivot, Moonshot), compensation guidance, and a 12-month action plan.';

    return implode(' ', $parts);
}

function generateCareerPaths($profile) {
    $gainers = $profile['gainers'];
    $drainers = $profile['drainers'];
    $years = $profile['years_exp'];

    $paths = [
        'safety' => [
            'title' => 'Safety Path: Steady Climber',
            'description' => 'Stay in your domain, deepen expertise, and pursue structured progression.',
            'roles' => ['Senior Specialist', 'Team Lead', 'Practice Lead'],
            'fit' => [],
            'watchouts' => []
        ],
        'pivot' => [
            'title' => 'Pivot Path: Strategic Shifter',
            'description' => 'Apply your strengths in an adjacent, higher-demand area with moderate upskilling.',
            'roles' => ['Product Manager', 'Strategic Consultant', 'Operations / Delivery Lead'],
            'fit' => [],
            'watchouts' => []
        ],
        'moonshot' => [
            'title' => 'Moonshot Path: Builder / Entrepreneur',
            'description' => 'Spin up a venture, consultancy, or portfolio career that maximizes autonomy and upside.',
            'roles' => ['Founder / Co-founder', 'Independent Consultant', 'Fractional Lead'],
            'fit' => [],
            'watchouts' => []
        ]
    ];

    if (in_array('autonomy', $gainers)) {
        $paths['moonshot']['fit'][] = 'High ownership and decision authority.';
    }
    if (in_array('problem_solving', $gainers)) {
        $paths['pivot']['fit'][] = 'Analytical problem-solving in ambiguous business contexts.';
    }
    if (in_array('fast_pace', $gainers)) {
        $paths['pivot']['fit'][] = 'Dynamic environments with rapid iteration.';
        $paths['moonshot']['fit'][] = 'Comfort with rapid change and experimentation.';
    }
    if (in_array('mentoring', $gainers)) {
        $paths['safety']['fit'][] = 'Team leadership and coaching in a stable org structure.';
    }

    if (in_array('public_speaking', $drainers)) {
        $paths['moonshot']['watchouts'][] = 'Reduce heavy pitching cycles; partner with a cofounder for GTM.';
        $paths['pivot']['watchouts'][] = 'Choose roles with internal focus vs. heavy evangelism.';
    }
    if (in_array('ambiguity', $drainers)) {
        $paths['moonshot']['watchouts'][] = 'Set explicit quarterly milestones to reduce ambiguity.';
        $paths['pivot']['watchouts'][] = 'Join orgs with mature processes and clear OKRs.';
    }
    if (in_array('politics', $drainers)) {
        $paths['safety']['watchouts'][] = 'Favor flat teams or mission-driven orgs with low bureaucracy.';
    }

    if ($years < 4) {
        $paths['moonshot']['watchouts'][] = 'Limited track record may slow trust-building with clients/investors—start with small pilots.';
    }

    return $paths;
}

function generateSalaryBenchmarks($profile) {
    $salaryMin = max(0, (int)$profile['salary_min']);
    $salaryMax = max($salaryMin, (int)$profile['salary_max']);

    $baseMin = $salaryMin ?: 40000;
    $baseMax = $salaryMax ?: 120000;

    return [
        'current_expectation' => ['min' => $baseMin, 'max' => $baseMax],
        'market_baseline' => ['min' => max(35000, $baseMin - 10000), 'max' => $baseMax + 5000],
        'safety_path_potential' => ['min' => $baseMin + 5000, 'max' => $baseMax + 15000],
        'pivot_path_potential' => ['min' => $baseMin + 15000, 'max' => $baseMax + 35000],
        'moonshot_path_potential' => ['min' => $baseMin + 25000, 'max' => $baseMax + 80000],
        'negotiation_strategy' => 'Lead with quantified achievements; align asks to market bands and tier-1 responsibilities.'
    ];
}

function generatePersonalityFit($profile) {
    $drainers = $profile['drainers'];
    $gainers = $profile['gainers'];

    $fit = [
        'strengths' => [],
        'growth_areas' => [],
        'ideal_environment' => []
    ];

    if (in_array('problem_solving', $gainers)) {
        $fit['strengths'][] = 'Strategic problem-solving and structured thinking.';
    }
    if (in_array('autonomy', $gainers)) {
        $fit['strengths'][] = 'Self-directed, comfortable owning outcomes end-to-end.';
    }
    if (in_array('mentoring', $gainers)) {
        $fit['strengths'][] = 'Coaching and developing others.';
    }
    if (in_array('creativity', $gainers)) {
        $fit['strengths'][] = 'Creative ideation and synthesis.';
    }

    if (in_array('public_speaking', $drainers)) {
        $fit['growth_areas'][] = 'Presentation confidence (consider coaching or templates).';
    }
    if (in_array('ambiguity', $drainers)) {
        $fit['growth_areas'][] = 'Operating with clearer briefs, milestones, and KPIs.';
    }
    if (in_array('politics', $drainers)) {
        $fit['growth_areas'][] = 'Stakeholder mapping and lightweight governance habits.';
    }

    if (in_array('fast_pace', $gainers)) {
        $fit['ideal_environment'][] = 'Rapid-iteration teams with short feedback loops.';
    }
    if (in_array('autonomy', $gainers)) {
        $fit['ideal_environment'][] = 'Roles with ownership of a product/workstream.';
    }
    if (!in_array('routine', $drainers)) {
        $fit['ideal_environment'][] = 'Varied work with scope for improvement projects.';
    }

    return $fit;
}

function generateActionRoadmap($profile) {
    $actions = [
        '0-3_months' => [
            'Map your accomplishments to metrics (impact statements for resume/LinkedIn).',
            'Connect with 5-10 practitioners in target roles for short discovery calls.',
            'Pick one certification or course aligned to your pivot/target path.',
            'Ship one proof-of-work artifact (case study, mini project, or playbook).'
        ],
        '3-6_months' => [
            'Launch a visible portfolio item (repo, deck, or demo) that matches desired roles.',
            'Request a stretch assignment to demonstrate leadership/ownership.',
            'Run 10 targeted applications with tailored impact bullets.',
            'Join a professional community or meetup to widen warm intros.'
        ],
        '6-12_months' => [
            'Negotiate internal move or external offer aligned to target path.',
            'Publish 2-3 thought pieces showing your approach to problems.',
            'If moonshot: validate a paid pilot with 1-2 clients or partners.',
            'Set quarterly review to recalibrate salary targets and scope.'
        ]
    ];

    return $actions;
}

function generateBurnoutPrevention($profile) {
    $drainers = $profile['drainers'];
    $strategies = [
        'boundaries' => 'Set meeting limits and protect weekly focus blocks.',
        'values_alignment' => 'Choose orgs with mission/values alignment to avoid friction.',
        'learning' => 'Keep a lightweight learning cadence to stay engaged.',
    ];

    if (in_array('interruptions', $drainers)) {
        $strategies['focus'] = 'Adopt no-meeting blocks and async updates to reduce interruptions.';
    }
    if (in_array('politics', $drainers)) {
        $strategies['culture'] = 'Favor flat teams and managers with transparent decision-making.';
    }
    if (in_array('ambiguity', $drainers)) {
        $strategies['clarity'] = 'Secure written briefs, milestones, and success metrics upfront.';
    }

    return $strategies;
}

function generateNextSteps($profile) {
    return [
        'week_1' => [
            'Tighten your resume summary to match the target path.',
            'Book two informational calls with people in your desired role.',
            'List 10 target companies and map a contact for each.'
        ],
        'week_2_4' => [
            'Publish one short post or case study demonstrating your approach.',
            'Join a relevant community; engage with 2-3 threads weekly.',
            'Apply selectively with tailored bullet points tied to impact.'
        ],
        'ongoing' => [
            'Weekly: 1-2 hours of upskilling aligned to your pivot.',
            'Monthly: one networking coffee or call.',
            'Quarterly: review compensation targets vs. market data.'
        ]
    ];
}

function formatReportHTML($report) {
    $html = '';

    // Executive Summary
    $html .= '<div class="report-section">';
    $html .= '<h4><i class="fa fa-briefcase"></i> Executive Summary</h4>';
    $html .= '<p>' . htmlspecialchars($report['executive_summary']) . '</p>';
    $html .= '</div>';

    // Three Career Paths
    $html .= '<div class="report-section">';
    $html .= '<h4><i class="fa fa-road"></i> 3 Career Paths</h4>';
    foreach ($report['three_career_paths'] as $path) {
        $html .= '<div class="report-subsection">';
        $html .= '<strong>' . htmlspecialchars($path['title']) . '</strong><br>';
        $html .= '<small>' . htmlspecialchars($path['description']) . '</small>';
        if (!empty($path['fit'])) {
            $html .= '<div class="report-item"><strong>Why it fits:</strong></div>';
            foreach ($path['fit'] as $fit) {
                $html .= '<div class="report-item">• ' . htmlspecialchars($fit) . '</div>';
            }
        }
        if (!empty($path['watchouts'])) {
            $html .= '<div class="report-item"><strong>Watch-outs:</strong></div>';
            foreach ($path['watchouts'] as $wo) {
                $html .= '<div class="report-item">• ' . htmlspecialchars($wo) . '</div>';
            }
        }
        $html .= '</div>';
    }
    $html .= '</div>';

    // Salary Benchmarks
    $html .= '<div class="report-section">';
    $html .= '<h4><i class="fa fa-pound"></i> Salary Benchmarks</h4>';
    $sb = $report['salary_benchmarks'];
    $html .= '<div class="report-item">Current Expectation: £' . $sb['current_expectation']['min'] . '–£' . $sb['current_expectation']['max'] . '</div>';
    $html .= '<div class="report-item">Market Baseline: £' . $sb['market_baseline']['min'] . '–£' . $sb['market_baseline']['max'] . '</div>';
    $html .= '<div class="report-item">Safety Path: £' . $sb['safety_path_potential']['min'] . '–£' . $sb['safety_path_potential']['max'] . '</div>';
    $html .= '<div class="report-item">Pivot Path: £' . $sb['pivot_path_potential']['min'] . '–£' . $sb['pivot_path_potential']['max'] . '</div>';
    $html .= '<div class="report-item">Moonshot Path: £' . $sb['moonshot_path_potential']['min'] . '–£' . $sb['moonshot_path_potential']['max'] . '</div>';
    $html .= '<div class="report-item">Negotiation: ' . htmlspecialchars($sb['negotiation_strategy']) . '</div>';
    $html .= '</div>';

    // Personality Fit
    $html .= '<div class="report-section">';
    $html .= '<h4><i class="fa fa-heart"></i> Personality Fit Analysis</h4>';
    if (!empty($report['personality_fit']['strengths'])) {
        $html .= '<strong>Key Strengths:</strong>';
        foreach ($report['personality_fit']['strengths'] as $strength) {
            $html .= '<div class="report-item">• ' . htmlspecialchars($strength) . '</div>';
        }
    }
    if (!empty($report['personality_fit']['growth_areas'])) {
        $html .= '<strong>Growth Areas:</strong>';
        foreach ($report['personality_fit']['growth_areas'] as $ga) {
            $html .= '<div class="report-item">• ' . htmlspecialchars($ga) . '</div>';
        }
    }
    if (!empty($report['personality_fit']['ideal_environment'])) {
        $html .= '<strong>Ideal Environment:</strong>';
        foreach ($report['personality_fit']['ideal_environment'] as $env) {
            $html .= '<div class="report-item">• ' . htmlspecialchars($env) . '</div>';
        }
    }
    $html .= '</div>';

    // Action Roadmap
    $html .= '<div class="report-section">';
    $html .= '<h4><i class="fa fa-road"></i> 12-Month Action Roadmap</h4>';
    foreach ($report['action_roadmap'] as $phase => $items) {
        $html .= '<div class="report-item"><strong>' . htmlspecialchars(strtoupper(str_replace('_', ' ', $phase))) . ':</strong></div>';
        foreach ($items as $item) {
            $html .= '<div class="report-item">• ' . htmlspecialchars($item) . '</div>';
        }
    }
    $html .= '</div>';

    // Burnout Prevention
    $html .= '<div class="report-section">';
    $html .= '<h4><i class="fa fa-heart-o"></i> Burnout Prevention Strategy</h4>';
    foreach ($report['burnout_prevention'] as $item) {
        $html .= '<div class="report-item">• ' . htmlspecialchars($item) . '</div>';
    }
    $html .= '</div>';

    // Next Steps
    $html .= '<div class="report-section">';
    $html .= '<h4><i class="fa fa-check-square-o"></i> Immediate Next Steps</h4>';
    foreach ($report['next_steps'] as $phase => $items) {
        $html .= '<div class="report-item"><strong>' . htmlspecialchars(strtoupper(str_replace('_', ' ', $phase))) . ':</strong></div>';
        foreach ($items as $item) {
            $html .= '<div class="report-item">• ' . htmlspecialchars($item) . '</div>';
        }
    }
    $html .= '</div>';

    return $html;
}
?>
