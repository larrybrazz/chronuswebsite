<?php include 'hd.html'; ?>

<!-- Breadcrumbs -->
<div class="breadcrumbs overlay">
    <div class="container">
        <div class="bread-inner">
            <div class="row">
                <div class="col-12">
                    <h2>CV Revamp & SOP Generator</h2>
                    <ul class="bread-list">
                        <li><a href="index.php">Home</a></li>
                        <li><i class="icofont-simple-right"></i></li>
                        <li class="active">Career Tools</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tools Section -->
<section class="tools-section" style="padding: 80px 0; background: #f8f9fa;">
    <div class="container">
        <!-- Hero Section -->
        <div class="hero-section" style="text-align: center; margin-bottom: 60px;">
            <h1 style="font-size: 2.5rem; color: #2c3e50; margin-bottom: 20px; font-weight: 700;">Transform Your Career Documents</h1>
            <p style="font-size: 1.1rem; color: #555; max-width: 800px; margin: 0 auto 30px; line-height: 1.6;">
                AI-powered CV optimization and Statement of Purpose generation designed for ATS compatibility and authenticity.
            </p>
        </div>

        <div class="row">
            <!-- CV Revamp Tool -->
            <div class="col-lg-6 col-md-12">
                <div class="tool-card" style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 20px rgba(0,0,0,0.08); margin-bottom: 30px;">
                    <div class="tool-header" style="text-align: center; margin-bottom: 30px;">
                        <i class="icofont-file-document" style="font-size: 3rem; color: #9acd32; margin-bottom: 15px;"></i>
                        <h2 style="color: #2c3e50; font-size: 1.8rem; font-weight: 700; margin-bottom: 10px;">CV Revamp</h2>
                        <p style="color: #666; font-size: 1rem;">ATS-optimized resume tailored to your target role</p>
                    </div>

                    <form id="cvRevampForm" enctype="multipart/form-data" style="display: block;">
                        <!-- File Upload or Text Paste Toggle -->
                        <div style="margin-bottom: 20px; text-align: center;">
                            <div style="display: inline-flex; background: #f0f0f0; border-radius: 6px; padding: 3px;">
                                <button type="button" onclick="toggleCVInput('file')" id="fileBtn" style="padding: 8px 20px; border: none; background: #9acd32; color: white; border-radius: 4px; cursor: pointer; font-weight: 600;">Upload File</button>
                                <button type="button" onclick="toggleCVInput('text')" id="textBtn" style="padding: 8px 20px; border: none; background: transparent; color: #666; border-radius: 4px; cursor: pointer; font-weight: 600;">Paste Text</button>
                            </div>
                        </div>

                        <!-- File Upload Option -->
                        <div id="fileUploadSection" style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Upload Your Current CV</label>
                            <input type="file" name="current_cv" id="cvFile" accept=".pdf,.doc,.docx" style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 0.95rem; background: white; color: #333; box-sizing: border-box;">
                            <small style="display: block; margin-top: 5px; color: #999;">Supported formats: PDF, DOC, DOCX</small>
                        </div>

                        <!-- Text Paste Option -->
                        <div id="textPasteSection" style="margin-bottom: 25px; display: none;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Paste Your CV Text</label>
                            <textarea name="cv_text" id="cvText" placeholder="Paste your complete CV text here (copy from your PDF or Word document)..." style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 0.95rem; min-height: 200px; background: white; color: #333; box-sizing: border-box; resize: vertical;"></textarea>
                            <small style="display: block; margin-top: 5px; color: #28a745;">✓ Best option for scanned PDFs or image-based documents</small>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Target Job Title *</label>
                            <input type="text" name="target_job" placeholder="e.g., Senior Product Manager" required style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 0.95rem; background: white; color: #333; box-sizing: border-box;">
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Job Description (Optional)</label>
                            <textarea name="job_description" placeholder="Paste the job description here for better optimization..." style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 0.95rem; min-height: 150px; background: white; color: #333; box-sizing: border-box; resize: vertical;"></textarea>
                            <small style="display: block; margin-top: 5px; color: #999;">Include keywords, requirements, and responsibilities</small>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Your Email *</label>
                            <input type="email" name="email" placeholder="your@email.com" required style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 0.95rem; background: white; color: #333; box-sizing: border-box;">
                        </div>

                        <div style="padding: 15px; background: #e8f5e9; border-radius: 6px; border-left: 4px solid #28a745; color: #2c5f2d; margin-bottom: 25px;">
                            <strong>What you'll get:</strong>
                            <ul style="margin: 10px 0 0 20px; padding: 0;">
                                <li>ATS-optimized format</li>
                                <li>Transferable skills highlighted</li>
                                <li>Achievement-focused bullets</li>
                                <li>Keyword optimization</li>
                            </ul>
                        </div>

                        <button type="submit" style="width: 100%; padding: 15px; background: #9acd32; color: white; border: none; border-radius: 6px; font-weight: 700; font-size: 1.1rem; cursor: pointer; transition: all 0.3s ease;">
                            Generate Revamped CV
                        </button>
                    </form>

                    <div id="cvResult" style="display: none; margin-top: 20px; padding: 20px; background: #f9f9f9; border-radius: 6px;"></div>
                </div>
            </div>

            <!-- SOP Generator Tool -->
            <div class="col-lg-6 col-md-12">
                <div class="tool-card" style="background: white; padding: 40px; border-radius: 10px; box-shadow: 0 2px 20px rgba(0,0,0,0.08); margin-bottom: 30px;">
                    <div class="tool-header" style="text-align: center; margin-bottom: 30px;">
                        <i class="icofont-paper" style="font-size: 3rem; color: #28a745; margin-bottom: 15px;"></i>
                        <h2 style="color: #2c3e50; font-size: 1.8rem; font-weight: 700; margin-bottom: 10px;">SOP Generator</h2>
                        <p style="color: #666; font-size: 1rem;">Compelling Statement of Purpose for your application</p>
                    </div>

                    <form id="sopForm" enctype="multipart/form-data" style="display: block;">
                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Upload Your CV *</label>
                            <input type="file" name="cv_file" accept=".pdf,.doc,.docx" required style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 0.95rem; background: white; color: #333; box-sizing: border-box;">
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Program/Position *</label>
                            <input type="text" name="program_name" placeholder="e.g., MBA Program, Software Engineer Role" required style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 0.95rem; background: white; color: #333; box-sizing: border-box;">
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Institution/Company *</label>
                            <input type="text" name="institution_name" placeholder="e.g., University of Oxford, Google" required style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 0.95rem; background: white; color: #333; box-sizing: border-box;">
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Job Description *</label>
                            <textarea name="job_description" placeholder="Paste the complete job description or program requirements..." required style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 0.95rem; min-height: 120px; background: white; color: #333; box-sizing: border-box; resize: vertical;"></textarea>
                            <small style="display: block; margin-top: 5px; color: #999;">Include key responsibilities, requirements, and qualifications</small>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Organization Core Values *</label>
                            <textarea name="core_values" placeholder="e.g., Innovation, Integrity, Collaboration, Excellence, Customer Focus..." required style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 0.95rem; min-height: 80px; background: white; color: #333; box-sizing: border-box; resize: vertical;"></textarea>
                            <small style="display: block; margin-top: 5px; color: #999;">List the organization's key values or cultural principles</small>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Success Profile for the Role *</label>
                            <textarea name="success_profile" placeholder="Describe what success looks like in this role (key competencies, achievements, behaviors)..." required style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 0.95rem; min-height: 100px; background: white; color: #333; box-sizing: border-box; resize: vertical;"></textarea>
                            <small style="display: block; margin-top: 5px; color: #999;">What traits, skills, and outcomes define top performers?</small>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Your Career Goals *</label>
                            <textarea name="career_goals" placeholder="Describe your short-term and long-term career aspirations..." required style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 0.95rem; min-height: 100px; background: white; color: #333; box-sizing: border-box; resize: vertical;"></textarea>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Why This Program/Role? *</label>
                            <textarea name="motivation" placeholder="What attracts you to this specific opportunity?" required style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 0.95rem; min-height: 100px; background: white; color: #333; box-sizing: border-box; resize: vertical;"></textarea>
                        </div>

                        <div style="margin-bottom: 25px;">
                            <label style="display: block; margin-bottom: 8px; font-weight: 600; color: #2c3e50;">Your Email *</label>
                            <input type="email" name="email" placeholder="your@email.com" required style="width: 100%; padding: 12px 15px; border: 2px solid #ddd; border-radius: 6px; font-size: 0.95rem; background: white; color: #333; box-sizing: border-box;">
                        </div>

                        <div style="padding: 15px; background: #e3f2fd; border-radius: 6px; border-left: 4px solid #2196F3; color: #1565C0; margin-bottom: 25px;">
                            <strong>Your SOP will include:</strong>
                            <ul style="margin: 10px 0 0 20px; padding: 0;">
                                <li>Personalized narrative aligned to role</li>
                                <li>Skills mapped to success profile</li>
                                <li>Values alignment demonstrated</li>
                                <li>Authentic voice</li>
                                <li>Structured academic/professional format</li>
                            </ul>
                        </div>

                        <button type="submit" style="width: 100%; padding: 15px; background: #28a745; color: white; border: none; border-radius: 6px; font-weight: 700; font-size: 1.1rem; cursor: pointer; transition: all 0.3s ease;">
                            Generate Statement of Purpose
                        </button>
                    </form>

                    <div id="sopResult" style="display: none; margin-top: 20px; padding: 20px; background: #f9f9f9; border-radius: 6px;"></div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// Toggle between file upload and text paste
function toggleCVInput(mode) {
    const fileSection = document.getElementById('fileUploadSection');
    const textSection = document.getElementById('textPasteSection');
    const fileBtn = document.getElementById('fileBtn');
    const textBtn = document.getElementById('textBtn');
    const fileInput = document.getElementById('cvFile');
    const textInput = document.getElementById('cvText');
    
    if (mode === 'file') {
        fileSection.style.display = 'block';
        textSection.style.display = 'none';
        fileBtn.style.background = '#9acd32';
        fileBtn.style.color = 'white';
        textBtn.style.background = 'transparent';
        textBtn.style.color = '#666';
        fileInput.required = true;
        textInput.required = false;
    } else {
        fileSection.style.display = 'none';
        textSection.style.display = 'block';
        fileBtn.style.background = 'transparent';
        fileBtn.style.color = '#666';
        textBtn.style.background = '#9acd32';
        textBtn.style.color = 'white';
        fileInput.required = false;
        textInput.required = true;
    }
}

// CV Revamp Form Handler
document.getElementById('cvRevampForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const resultDiv = document.getElementById('cvResult');
    const submitBtn = this.querySelector('button[type="submit"]');
    
    submitBtn.textContent = 'Processing...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('process_cv_revamp.php', {
            method: 'POST',
            body: formData
        });
        
        // Check if response is ok
        if (!response.ok) {
            throw new Error('Server error: ' + response.status);
        }
        
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            const text = await response.text();
            console.error('Non-JSON response:', text);
            throw new Error('Server returned invalid response. Check console for details.');
        }
        
        const data = await response.json();
        
        if (data.success) {
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = `
                <div style="text-align: center;">
                    <i class="icofont-check-circled" style="font-size: 3rem; color: #28a745;"></i>
                    <h3 style="color: #28a745; margin: 15px 0;">CV Revamp Complete!</h3>
                    <p style="color: #666; margin-bottom: 20px;">${data.message}</p>
                    ${data.download_url ? `<a href="${data.download_url}" style="display: inline-block; padding: 12px 30px; background: #9acd32; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">Download Revamped CV</a>` : ''}
                </div>
            `;
            this.reset();
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Form submission error:', error);
        alert('Error: ' + error.message);
    } finally {
        submitBtn.textContent = 'Generate Revamped CV';
        submitBtn.disabled = false;
    }
});

// SOP Form Handler
document.getElementById('sopForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const resultDiv = document.getElementById('sopResult');
    const submitBtn = this.querySelector('button[type="submit"]');
    
    submitBtn.textContent = 'Generating...';
    submitBtn.disabled = true;
    
    try {
        const response = await fetch('process_sop_generation.php', {
            method: 'POST',
            body: formData
        });
        
        // Check if response is ok
        if (!response.ok) {
            throw new Error('Server error: ' + response.status);
        }
        
        const contentType = response.headers.get("content-type");
        if (!contentType || !contentType.includes("application/json")) {
            const text = await response.text();
            console.error('Non-JSON response:', text);
            throw new Error('Server returned invalid response. Check console for details.');
        }
        
        const data = await response.json();
        
        if (data.success) {
            resultDiv.style.display = 'block';
            resultDiv.innerHTML = `
                <div style="text-align: center;">
                    <i class="icofont-check-circled" style="font-size: 3rem; color: #28a745;"></i>
                    <h3 style="color: #28a745; margin: 15px 0;">SOP Generated Successfully!</h3>
                    <p style="color: #666; margin-bottom: 20px;">${data.message}</p>
                    ${data.download_url ? `<a href="${data.download_url}" style="display: inline-block; padding: 12px 30px; background: #28a745; color: white; text-decoration: none; border-radius: 6px; font-weight: 600;">Download SOP</a>` : ''}
                </div>
            `;
            this.reset();
        } else {
            alert('Error: ' + (data.error || 'Unknown error'));
        }
    } catch (error) {
        console.error('Form submission error:', error);
        alert('Error: ' + error.message);
    } finally {
        submitBtn.textContent = 'Generate Statement of Purpose';
        submitBtn.disabled = false;
    }
});
</script>

<?php include 'ft.html'; ?>
