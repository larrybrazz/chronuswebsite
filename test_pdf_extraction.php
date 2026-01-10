<?php
/**
 * PDF Extraction Test Tool
 * Upload a PDF/DOC/DOCX file to test text extraction
 */

// Load autoloader
if (file_exists(__DIR__ . '/vendor/autoload.php')) {
    require_once __DIR__ . '/vendor/autoload.php';
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PDF Extraction Test</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #2c3e50;
            border-bottom: 3px solid #95cf4a;
            padding-bottom: 10px;
        }
        .upload-form {
            margin: 20px 0;
            padding: 20px;
            background: #f9f9f9;
            border-radius: 5px;
        }
        input[type="file"] {
            display: block;
            margin: 10px 0;
        }
        button {
            background: #95cf4a;
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        button:hover {
            background: #7ab837;
        }
        .result {
            margin-top: 30px;
            padding: 20px;
            background: #f0f0f0;
            border-left: 4px solid #95cf4a;
            border-radius: 5px;
        }
        .error {
            background: #fee;
            border-left-color: #c00;
            color: #c00;
        }
        .success {
            background: #efe;
            border-left-color: #0c0;
        }
        .extracted-text {
            background: white;
            padding: 15px;
            margin-top: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            max-height: 400px;
            overflow-y: auto;
            white-space: pre-wrap;
            font-family: 'Courier New', monospace;
            font-size: 12px;
        }
        .stats {
            margin: 15px 0;
            padding: 10px;
            background: #e8f4f8;
            border-radius: 5px;
        }
        .stats span {
            font-weight: bold;
            color: #2c3e50;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>📄 PDF Text Extraction Test</h1>
        <p>Upload a CV file (PDF, DOC, or DOCX) to test text extraction capabilities.</p>
        
        <div class="upload-form">
            <form method="POST" enctype="multipart/form-data">
                <label for="test_file"><strong>Select CV File:</strong></label>
                <input type="file" name="test_file" id="test_file" accept=".pdf,.doc,.docx" required>
                <button type="submit">Test Extraction</button>
            </form>
        </div>

        <?php
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['test_file'])) {
            $file = $_FILES['test_file'];
            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            echo '<div class="stats">';
            echo '<p><span>File:</span> ' . htmlspecialchars($file['name']) . '</p>';
            echo '<p><span>Size:</span> ' . number_format($file['size']) . ' bytes</p>';
            echo '<p><span>Type:</span> ' . $extension . '</p>';
            echo '<p><span>PDF Parser Available:</span> ' . (class_exists('Smalot\PdfParser\Parser') ? '✅ Yes' : '❌ No') . '</p>';
            echo '<p><span>ZipArchive Available:</span> ' . (class_exists('ZipArchive') ? '✅ Yes' : '❌ No') . '</p>';
            echo '</div>';
            
            // Try extraction
            $text = '';
            $methods_tried = [];
            
            if ($extension === 'pdf') {
                // Method 1: PDF Parser
                if (class_exists('Smalot\PdfParser\Parser')) {
                    try {
                        $parser = new \Smalot\PdfParser\Parser();
                        $pdf = $parser->parseFile($file['tmp_name']);
                        $text = $pdf->getText();
                        $methods_tried[] = '✅ PDF Parser Library (successful)';
                    } catch (Exception $e) {
                        $methods_tried[] = '❌ PDF Parser Library: ' . $e->getMessage();
                    }
                }
                
                // Method 2: pdftotext
                if (empty(trim($text))) {
                    $output = @shell_exec("pdftotext " . escapeshellarg($file['tmp_name']) . " - 2>&1");
                    if (!empty($output) && strpos($output, 'not recognized') === false) {
                        $text = $output;
                        $methods_tried[] = '✅ pdftotext command (successful)';
                    } else {
                        $methods_tried[] = '❌ pdftotext command: Not available or failed';
                    }
                }
                
                // Method 3: Manual extraction
                if (empty(trim($text))) {
                    $raw = file_get_contents($file['tmp_name']);
                    // Basic extraction
                    if (preg_match_all('/\((.*?)\)\s*Tj/s', $raw, $matches)) {
                        $text = implode("\n", $matches[1]);
                        $methods_tried[] = '✅ Manual extraction (Tj operators)';
                    } else {
                        $text = preg_replace('/[^\x20-\x7E\n]/', '', $raw);
                        $methods_tried[] = '⚠️ Raw text extraction (may contain artifacts)';
                    }
                }
            } elseif ($extension === 'docx') {
                if (class_exists('ZipArchive')) {
                    $zip = new ZipArchive();
                    if ($zip->open($file['tmp_name']) === true) {
                        $xml = $zip->getFromName('word/document.xml');
                        $zip->close();
                        if ($xml) {
                            $xml_obj = simplexml_load_string($xml);
                            $text = strip_tags($xml_obj->asXML());
                            $methods_tried[] = '✅ DOCX XML extraction (successful)';
                        }
                    }
                } else {
                    $methods_tried[] = '❌ ZipArchive not available';
                }
            }
            
            // Display results
            $text_length = strlen(trim($text));
            
            echo '<div class="result ' . ($text_length > 0 ? 'success' : 'error') . '">';
            echo '<h3>Extraction Methods Tried:</h3>';
            echo '<ul>';
            foreach ($methods_tried as $method) {
                echo '<li>' . $method . '</li>';
            }
            echo '</ul>';
            
            if ($text_length > 0) {
                echo '<p><strong>✅ Extraction Successful!</strong></p>';
                echo '<p>Extracted <strong>' . $text_length . '</strong> characters</p>';
                echo '<div class="extracted-text">';
                echo htmlspecialchars(substr($text, 0, 3000));
                if ($text_length > 3000) {
                    echo "\n\n... (showing first 3000 characters)";
                }
                echo '</div>';
            } else {
                echo '<p><strong>❌ No text could be extracted</strong></p>';
                echo '<p>Suggestions:</p>';
                echo '<ul>';
                echo '<li>The PDF may be image-based (scanned) - try using OCR software first</li>';
                echo '<li>The file may be password-protected or corrupted</li>';
                echo '<li>Try saving the file in a different format (e.g., DOCX)</li>';
                echo '<li>Make sure the PDF contains actual text, not just images</li>';
                echo '</ul>';
            }
            echo '</div>';
        }
        ?>
    </div>
</body>
</html>
