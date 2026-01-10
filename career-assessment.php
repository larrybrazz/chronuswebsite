<?php include 'hd.html'; ?>

<!-- Breadcrumbs -->
<div class="breadcrumbs overlay">
    <div class="container">
        <div class="bread-inner">
            <div class="row">
                <div class="col-12">
                    <h2>Career Assessment</h2>
                    <ul class="bread-list">
                        <li><a href="index.php">Home</a></li>
                        <li><i class="icofont-simple-right"></i></li>
                        <li class="active">Career Blueprint</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<section class="career-assessment-section" style="padding: 60px 0; background: #f8f9fa;">
    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section" style="text-align: center; margin-bottom: 50px;">
            <h1 style="font-size: 2.5rem; color: #2c3e50; margin-bottom: 20px; font-weight: 700;">Your Career Blueprint Awaits</h1>
            <p style="font-size: 1.1rem; color: #555; max-width: 700px; margin: 0 auto 30px; line-height: 1.6;">
                Discover your ideal career path by analyzing your skills, personality, and aspirations across three expert dimensions.
            </p>
        </div>

        <!-- Main Assessment Form -->
        <div style="max-width: 800px; margin: 0 auto;">
            <!-- Progress Steps -->
            <div style="display: flex; justify-content: space-between; margin-bottom: 50px; align-items: center;">
                <div class="step" data-step="1" style="display: flex; flex-direction: column; align-items: center; flex: 1;">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: #9acd32; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; margin-bottom: 10px;">1</div>
                    <span style="color: #666; font-weight: 600; text-align: center;">Background</span>
                </div>
                <div style="flex: 1; height: 2px; background: #ddd; margin: 0 10px; margin-top: 25px;"></div>
                <div class="step" data-step="2" style="display: flex; flex-direction: column; align-items: center; flex: 1;">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: #ddd; color: #999; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; margin-bottom: 10px;">2</div>
                    <span style="color: #999; font-weight: 600; text-align: center;">Personality</span>
                </div>
                <div style="flex: 1; height: 2px; background: #ddd; margin: 0 10px; margin-top: 25px;"></div>
                <div class="step" data-step="3" style="display: flex; flex-direction: column; align-items: center; flex: 1;">
                    <div style="width: 50px; height: 50px; border-radius: 50%; background: #ddd; color: #999; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 1.2rem; margin-bottom: 10px;">3</div>
                    <span style="color: #999; font-weight: 600; text-align: center;">Contact</span>
                </div>
            </div>

            <!-- Form Container -->
            <div style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 20px rgba(0,0,0,0.08);">
                <form id="careerForm" method="POST" enctype="multipart/form-data" style="display: block;">

                    <!-- STEP 1: BACKGROUND -->
                    <div class="form-step" data-step="1" style="display: block;">
                        <h3 style="color: #2c3e50; margin-bottom: 30px; font-size: 1.5rem; font-weight: 700;">Your Professional Background</h3>
                        
                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Professional History & Resume</label>
                            <textarea id="resume" name="resume" placeholder="Describe your career journey, job titles, industries, and key achievements..." required style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-family: Arial, sans-serif; font-size: 0.95rem; min-height: 120px; display: block; box-sizing: border-box; resize: vertical; background: white; color: #333;"></textarea>
                            <small style="display: block; margin-top: 5px; color: #999;">Be specific: roles, years, industries, achievements.</small>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Upload Your CV (optional)</label>
                            <input type="file" id="cv_file" name="cv_file" accept=".pdf,.doc,.docx" style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-family: Arial, sans-serif; font-size: 0.95rem; background: white; color: #333;" aria-label="Upload CV">
                            <small style="display: block; margin-top: 5px; color: #999;">Attach a PDF, DOC, or DOCX, or paste your resume above.</small>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Years of Professional Experience</label>
                            <div style="display: flex; gap: 10px; align-items: center;">
                                <input type="number" id="experience" name="experience" min="0" max="70" placeholder="e.g., 5" required style="width: 120px; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-family: Arial, sans-serif; font-size: 0.95rem; display: block; box-sizing: border-box; background: white; color: #333;">
                                <span style="color: #666; font-weight: 600;">years</span>
                            </div>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Certifications & Qualifications</label>
                            <textarea id="certifications" name="certifications" placeholder="e.g., MBA, AWS Certified, PMP, Google Cloud Professional, etc." style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-family: Arial, sans-serif; font-size: 0.95rem; min-height: 100px; display: block; box-sizing: border-box; resize: vertical; background: white; color: #333;"></textarea>
                            <small style="display: block; margin-top: 5px; color: #999;">Include degrees, professional certifications, bootcamps.</small>
                        </div>
                    </div>

                    <!-- STEP 2: PERSONALITY -->
                    <div class="form-step" data-step="2" style="display: none;">
                        <h3 style="color: #2c3e50; margin-bottom: 30px; font-size: 1.5rem; font-weight: 700;">Your Personality & Work Preferences</h3>
                        
                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Describe Your Work Style</label>
                            <textarea id="temperament" name="temperament" placeholder="e.g., Analytical, dislike public speaking, thrive in fast-paced environments, prefer autonomy..." required style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-family: Arial, sans-serif; font-size: 0.95rem; min-height: 100px; display: block; box-sizing: border-box; resize: vertical; background: white; color: #333;"></textarea>
                        </div>

                        <div style="margin-bottom: 30px;">
                            <label style="display: block; margin-bottom: 15px; font-weight: 600; color: #2c3e50;">What Drains Your Energy? (Select all that apply)</label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 500; color: #333;">
                                    <input type="checkbox" name="energy_drainers[]" value="public_speaking" style="width: 18px; height: 18px; margin-right: 10px; cursor: pointer;">
                                    Public Speaking
                                </label>
                                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 500; color: #333;">
                                    <input type="checkbox" name="energy_drainers[]" value="client_facing" style="width: 18px; height: 18px; margin-right: 10px; cursor: pointer;">
                                    Constant Client Interaction
                                </label>
                                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 500; color: #333;">
                                    <input type="checkbox" name="energy_drainers[]" value="routine" style="width: 18px; height: 18px; margin-right: 10px; cursor: pointer;">
                                    Repetitive Tasks
                                </label>
                                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 500; color: #333;">
                                    <input type="checkbox" name="energy_drainers[]" value="politics" style="width: 18px; height: 18px; margin-right: 10px; cursor: pointer;">
                                    Office Politics
                                </label>
                                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 500; color: #333;">
                                    <input type="checkbox" name="energy_drainers[]" value="ambiguity" style="width: 18px; height: 18px; margin-right: 10px; cursor: pointer;">
                                    Ambiguity & Unclear Goals
                                </label>
                                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 500; color: #333;">
                                    <input type="checkbox" name="energy_drainers[]" value="interruptions" style="width: 18px; height: 18px; margin-right: 10px; cursor: pointer;">
                                    Constant Interruptions
                                </label>
                            </div>
                        </div>

                        <div style="margin-bottom: 30px;">
                            <label style="display: block; margin-bottom: 15px; font-weight: 600; color: #2c3e50;">What Energizes You? (Select all that apply)</label>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 500; color: #333;">
                                    <input type="checkbox" name="energy_gainers[]" value="problem_solving" style="width: 18px; height: 18px; margin-right: 10px; cursor: pointer;">
                                    Problem Solving
                                </label>
                                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 500; color: #333;">
                                    <input type="checkbox" name="energy_gainers[]" value="autonomy" style="width: 18px; height: 18px; margin-right: 10px; cursor: pointer;">
                                    Autonomy & Independence
                                </label>
                                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 500; color: #333;">
                                    <input type="checkbox" name="energy_gainers[]" value="mentoring" style="width: 18px; height: 18px; margin-right: 10px; cursor: pointer;">
                                    Mentoring Others
                                </label>
                                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 500; color: #333;">
                                    <input type="checkbox" name="energy_gainers[]" value="fast_pace" style="width: 18px; height: 18px; margin-right: 10px; cursor: pointer;">
                                    Fast-Paced Environments
                                </label>
                                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 500; color: #333;">
                                    <input type="checkbox" name="energy_gainers[]" value="creativity" style="width: 18px; height: 18px; margin-right: 10px; cursor: pointer;">
                                    Creative & Strategic Work
                                </label>
                                <label style="display: flex; align-items: center; cursor: pointer; font-weight: 500; color: #333;">
                                    <input type="checkbox" name="energy_gainers[]" value="results" style="width: 18px; height: 18px; margin-right: 10px; cursor: pointer;">
                                    Tangible Results
                                </label>
                            </div>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Ideal Company Culture</label>
                            <textarea id="ideal_company" name="ideal_company" placeholder="e.g., Startup culture, remote-friendly, data-driven, collaborative..." required style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-family: Arial, sans-serif; font-size: 0.95rem; min-height: 100px; display: block; box-sizing: border-box; resize: vertical; background: white; color: #333;"></textarea>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Desired Annual Salary Range</label>
                            <div style="display: flex; gap: 15px; align-items: center;">
                                <div style="display: flex; align-items: center; gap: 5px; flex: 1;">
                                    <span style="color: #9acd32; font-weight: 700; font-size: 1.2rem;">GBP</span>
                                    <input type="number" id="salary_min" name="salary_min" placeholder="Min" min="20000" style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-family: Arial, sans-serif; font-size: 0.95rem; display: block; box-sizing: border-box; background: white; color: #333;">
                                </div>
                                <span style="color: #999; font-weight: 600;">to</span>
                                <div style="display: flex; align-items: center; gap: 5px; flex: 1;">
                                    <span style="color: #9acd32; font-weight: 700; font-size: 1.2rem;">GBP</span>
                                    <input type="number" id="salary_max" name="salary_max" placeholder="Max" min="20000" style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-family: Arial, sans-serif; font-size: 0.95rem; display: block; box-sizing: border-box; background: white; color: #333;">
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 3: CONTACT -->
                    <div class="form-step" data-step="3" style="display: none;">
                        <h3 style="color: #2c3e50; margin-bottom: 30px; font-size: 1.5rem; font-weight: 700;">How Can We Reach You?</h3>
                        
                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Email Address *</label>
                            <input type="email" id="email" name="email" placeholder="your@email.com" required style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-family: Arial, sans-serif; font-size: 0.95rem; display: block; box-sizing: border-box; background: white; color: #333;">
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Phone Number (Optional)</label>
                            <input type="tel" id="phone" name="phone" placeholder="+44 (0) 123 456 7890" style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-family: Arial, sans-serif; font-size: 0.95rem; display: block; box-sizing: border-box; background: white; color: #333;">
                        </div>

                        <div style="margin-bottom: 25px; padding: 15px; background: #f9f9f9; border-radius: 6px; border-left: 4px solid #9acd32;">
                            <label style="display: flex; align-items: center; cursor: pointer; font-weight: 500; color: #333;">
                                <input type="checkbox" id="newsletter" name="newsletter" checked style="width: 18px; height: 18px; margin-right: 10px; cursor: pointer;">
                                Send me weekly career insights and industry trends
                            </label>
                        </div>

                        <div style="padding: 15px; background: #e8f5e9; border-radius: 6px; border-left: 4px solid #28a745; color: #2c5f2d;">
                            <strong>Your assessment takes 10-15 minutes</strong><br>
                            Results will be delivered within <strong>24 hours</strong>
                        </div>
                    </div>

                    <!-- Form Navigation -->
                    <div style="display: flex; gap: 15px; justify-content: center; margin-top: 40px;">
                        <button type="button" id="prevBtn" onclick="previousStep()" style="display: none; padding: 12px 30px; border: 2px solid #999; background: white; color: #333; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 1rem; transition: all 0.3s ease;">
                            Previous
                        </button>
                        <button type="button" id="nextBtn" onclick="nextStep()" style="display: block; padding: 12px 40px; background: #9acd32; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 1rem; transition: all 0.3s ease;">
                            Next
                        </button>
                        <button type="submit" id="submitBtn" onclick="submitForm(event)" style="display: none; padding: 12px 40px; background: #28a745; color: white; border: none; border-radius: 6px; font-weight: 600; cursor: pointer; font-size: 1rem; transition: all 0.3s ease;">
                            Generate My Blueprint
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<!-- Success Modal -->
<div id="successModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 1000; justify-content: center; align-items: center; padding: 20px; box-sizing: border-box;">
    <div style="background: white; padding: 40px; border-radius: 10px; max-width: 600px; width: 100%; max-height: 90vh; overflow-y: auto; box-shadow: 0 10px 40px rgba(0,0,0,0.2);">
        <h2 style="color: #28a745; font-size: 1.8rem; margin-bottom: 15px;">Assessment Submitted Successfully!</h2>
        <p style="color: #666; font-size: 1rem; margin-bottom: 20px;">Your career blueprint is being generated. You'll receive it via email within 24 hours.</p>
        <div id="reportPreview" style="background: #f9f9f9; padding: 20px; border-radius: 6px; margin-bottom: 20px; display: none;">
            <h4 style="color: #2c3e50; margin-bottom: 15px;">Your Career Paths:</h4>
            <div id="reportContent" style="color: #555; line-height: 1.6;"></div>
        </div>
        <div style="display: flex; gap: 10px; justify-content: flex-end;">
            <button onclick="closeModal()" style="padding: 10px 25px; background: #ddd; color: #333; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Close</button>
            <button onclick="downloadReport()" style="padding: 10px 25px; background: #9acd32; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600;">Download Report</button>
            <a href="cv-tools.php" style="padding: 10px 25px; background: #28a745; color: white; border: none; border-radius: 6px; cursor: pointer; font-weight: 600; text-decoration: none; display: inline-block;">Revamp Your CV</a>
        </div>
    </div>
</div>

<script>
let currentStep = 1;
const totalSteps = 3;
let assessmentData = {};

function showStep(step) {
    // Hide all form steps
    document.querySelectorAll('.form-step').forEach(el => {
        el.style.display = 'none';
    });

    // Show the correct form step
    const activeStep = document.querySelector(`.form-step[data-step="${step}"]`);
    if (activeStep) activeStep.style.display = 'block';

    // Update progress indicators
    document.querySelectorAll('.step').forEach(el => {
        const stepNum = parseInt(el.dataset.step, 10);
        const circle = el.querySelector('div');
        const label = el.querySelector('span');
        if (stepNum <= step) {
            circle.style.background = stepNum === step ? '#9acd32' : '#28a745';
            circle.style.color = 'white';
            label.style.color = '#333';
        } else {
            circle.style.background = '#ddd';
            circle.style.color = '#999';
            label.style.color = '#999';
        }
    });

    // Update button visibility
    document.getElementById('prevBtn').style.display = step > 1 ? 'block' : 'none';
    document.getElementById('nextBtn').style.display = step < totalSteps ? 'block' : 'none';
    document.getElementById('submitBtn').style.display = step === totalSteps ? 'block' : 'none';
}

function nextStep() {
    if (currentStep < totalSteps) {
        currentStep++;
        showStep(currentStep);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function previousStep() {
    if (currentStep > 1) {
        currentStep--;
        showStep(currentStep);
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
}

function submitForm(e) {
    e.preventDefault();
    
    // Collect form data
    const formData = new FormData(document.getElementById('careerForm'));
    assessmentData = Object.fromEntries(formData);
    
    // Send to backend
    fetch('process_career_assessment.php', {
        method: 'POST',
        body: formData
    })
    .then(async response => {
        const payload = await response.json().catch(() => ({}));
        if (!response.ok || !payload.success) {
            const msg = payload.message || payload.error || 'Unexpected error. Please try again.';
            throw new Error(msg);
        }
        return payload;
    })
    .then(data => {
        // Show success modal
        document.getElementById('successModal').style.display = 'flex';
        
        // Generate and display report
        fetch(`generate_career_report.php?id=${data.assessment_id}`)
            .then(response => response.json())
            .then(report => {
                if (report.success) {
                    document.getElementById('reportPreview').style.display = 'block';
                    document.getElementById('reportContent').innerHTML = report.html_preview || report.reportHTML || '';
                    assessmentData.assessment_id = data.assessment_id;
                }
            });
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Error: ' + (error.message || 'Please try again.'));
    });
}

function closeModal() {
    document.getElementById('successModal').style.display = 'none';
    // Reset form
    currentStep = 1;
    showStep(1);
    document.getElementById('careerForm').reset();
}

function downloadReport() {
    if (assessmentData.assessment_id) {
        window.location.href = `download_report.php?id=${assessmentData.assessment_id}`;
    }
}

// Initialize
showStep(1);
</script>

<style>
/* Force all form content to render visibly */
.form-step {
    padding: 10px 0;
    background: #fff;
}
.form-step * {
    display: block !important;
    opacity: 1 !important;
    visibility: visible !important;
    color: #222 !important;
}
.form-step input,
.form-step textarea,
.form-step select {
    width: 100% !important;
    background: #fff !important;
    border: 2px solid #ddd !important;
    border-radius: 6px !important;
    padding: 12px 15px !important;
    box-sizing: border-box !important;
}
.form-step label { font-weight: 600 !important; margin-bottom: 8px !important; }
.form-step small { margin-top: 5px !important; color: #777 !important; }
.form-step .checkbox-group { display: grid !important; grid-template-columns: 1fr 1fr !important; gap: 10px !important; }
.form-step .checkbox-group label { display: flex !important; align-items: center !important; gap: 8px !important; font-weight: 500 !important; }
.form-step .checkbox-group input { width: 18px !important; height: 18px !important; padding: 0 !important; }
</style>

<?php include 'ft.html'; ?>
