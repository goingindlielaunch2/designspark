<?php
// File: api_handler.php (Production Version)
// =================================================================
// 1. Saves the evaluation request to the database.
// 2. Takes screenshots of the user's website.
// 3. Calls the Google Gemini AI API to generate a website report.
// 4. Returns the report as a JSON object to the frontend.
// =================================================================

// ===========================================================
// Add google pagespeed api to this for additional metrics.
// ===========================================================

// --- ERROR REPORTING & HEADERS ---
ini_set('display_errors', 0); // Do not display errors to the browser in production
error_reporting(E_ALL);
// Log errors to a file for debugging
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

// --- CONFIGURATION & DEPENDENCIES ---
// This path now points to the config file inside your admin directory.
require_once __DIR__ . '/manageone/config.php';

// IMPORTANT: For better security, add your API key to your config.php file.
// In config.php, add: define('GEMINI_API_KEY', 'your_real_api_key');
$geminiApiKey = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : 'YOUR_GEMINI_API_KEY_HERE'; 

// --- HELPER FUNCTION ---
function send_error($message) {
    http_response_code(400);
    if (is_array($message)) {
        // Flatten array to a readable string
        $parts = [];
        foreach ($message as $key => $val) {
            if ($val !== null && $val !== '') {
                $parts[] = "$key: $val";
            }
        }
        $msg = implode(" | ", $parts);
        error_log("API Handler Error: " . $msg);
        echo json_encode(['error' => $msg]);
    } else {
        error_log("API Handler Error: " . $message);
        echo json_encode(['error' => $message]);
    }
    exit;
}

// --- STEP 1: VALIDATE AND SAVE SUBMISSION TO DATABASE ---

if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    send_error('Invalid request method.');
}

// Validate required fields from FormData
if (empty($_POST['name']) || empty($_POST['email']) || empty($_POST['website_url'])) {
    send_error('Missing required fields.');
}

$conn = get_db_connection();

$name = $conn->real_escape_string(trim($_POST['name']));
$email = $conn->real_escape_string(trim($_POST['email']));
$website_url = $conn->real_escape_string(trim($_POST['website_url']));
$subscribed = isset($_POST['newsletter']) && $_POST['newsletter'] === '1' ? 1 : 0;

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || !filter_var($website_url, FILTER_VALIDATE_URL)) {
    send_error('Invalid email or URL format.');
}

$stmt = $conn->prepare("INSERT INTO evaluations (name, email, website_url, subscribed_newsletter) VALUES (?, ?, ?, ?)");
$stmt->bind_param("sssi", $name, $email, $website_url, $subscribed);

if (!$stmt->execute()) {
    send_error('Database error: Could not save submission.');
}

$stmt->close();
$conn->close();

// --- STEP 2: GET SCREENSHOTS ---
$desktopScreenshotUrl = "https://image.thum.io/get/width/1280/crop/800/" . $website_url;
$mobileScreenshotUrl = "https://image.thum.io/get/width/400/crop/800/" . $website_url;

$context = stream_context_create(['http' => ['timeout' => 45]]);
$desktopImage = @file_get_contents($desktopScreenshotUrl, false, $context);
sleep(1); 
$mobileImage = @file_get_contents($mobileScreenshotUrl, false, $context);

if ($desktopImage === false || $mobileImage === false) {
    send_error('Could not take a screenshot of the provided URL. Please ensure it is a live, public website.');
}
$base64DesktopImage = base64_encode($desktopImage);
$base64MobileImage = base64_encode($mobileImage);

// --- STEP 3: PREPARE AND SEND REQUEST TO GEMINI API ---

// $geminiApiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key={$geminiApiKey}";
// Updated to use the latest Gemini 2.5 Flash model
$geminiApiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite-preview-06-17:generateContent?key={$geminiApiKey}";

$prompt = <<<PROMPT
You are a world-class UX/UI and conversion rate optimization expert.

Your audience is non-technical small business owners. Based on the two provided screenshots (desktop and mobile) of a website homepage, return a single, **valid JSON object** that evaluates both **user experience** and **conversion potential**.

### Output Requirements:
- Do not include any text, explanation, or formatting outside the JSON.
- The JSON must include these top-level keys:
  - "overallScore": integer 0–100 (general effectiveness)
  - "basicReport": object with:
    - "scores": an object containing the basic UX/CRO scores.
    - "topOpportunities": array of 2–3 short sentences (plain-language CRO tips).
  - "advancedReport": object with:
    - "scores": an object containing all detailed scores, including advanced UX/CRO.
    - "advancedFeedback": an object where each key is a metric name and the value is another object with "feedback" and "example" keys.
    - "abTestIdeas": (optional) array of specific A/B test ideas as strings.

### Scoring Rules:
- Use a score from 0 to 100 for each metric.
- If a screenshot does not show enough information to evaluate a metric, return -1 for its score and explain why in "advancedFeedback".
- Be honest — most small business sites will score in the 50–75 range.

---

### Evaluate These Metrics:

#### Basic Metrics (for "basicReport.scores"):
- "HeroClarity": Is the main message above the fold instantly clear and benefit-driven?
- "VisualDesignLayout": Is the layout modern, clean, and trustworthy?
- "CallToAction": Is the primary CTA obvious, compelling, and easy to click?
- "ReadabilityTypography": Are fonts legible, with adequate size and contrast?
- "SocialProofTrust": Are there trust-building elements such as testimonials, logos, or contact info?

#### Advanced Metrics (for "advancedReport.scores"):
- "HeroClarity"
- "VisualDesignLayout"
- "CallToAction"
- "ReadabilityTypography"
- "SocialProofTrust"
- "HeroTrustSignals": Are there trust signals (e.g., star ratings, customer counts, testimonials, badges, logos) visible in the hero?
- "PersuasiveCopy": Does the site use benefit-driven language, urgency, or risk reversal?
- "AttentionRatio": Is the page focused, or are there too many competing links/actions?
- "NavigationClarity": Are menus and orientation elements clear and intuitive?
- "AccessibilityContrast": Is there sufficient contrast between background and text?
- "MobileResponsiveHints": Does the layout show signs of mobile responsiveness (hamburger menu, flexible grid, readable text)?

---

### Example of "topOpportunities":
[
  "Move the 4.9/5 star rating higher up in the hero section to boost first-impression trust.",
  "Simplify the navigation menu to reduce distraction and increase conversions.",
  "Add a more benefit-focused headline like 'Save time with hassle-free scheduling.'"
]

---

### Example of "advancedFeedback":
For each metric, the value should be an object like this:
{
  "feedback": "plain-language explanation for the business owner",
  "example": "suggested change or rewrite"
}

Explain *why* each improvement would increase conversions, using analogies or simple examples (e.g., “Think of your homepage like a storefront window – if people don’t get what’s inside in 3 seconds, they’ll leave”).

### Final Reminder:
Output a **single JSON object only**. Do not include markdown or any other formatting outside of the JSON.
PROMPT;


$requestBody = [
    'contents' => [
        [
            'parts' => [
                ['text' => $prompt],
                ['text' => "\n\n--- DESKTOP SCREENSHOT ---"],
                ['inline_data' => ['mime_type' => 'image/png', 'data' => $base64DesktopImage]],
                ['text' => "\n\n--- MOBILE SCREENSHOT ---"],
                ['inline_data' => ['mime_type' => 'image/png', 'data' => $base64MobileImage]]
            ]
        ]
    ]
];

$maxRetries = 2;
for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $geminiApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
    $response = curl_exec($ch);
    $curlError = curl_error($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($httpcode === 200 && $response !== false) break;
    if ($attempt < $maxRetries) sleep(2);
}

// --- STEP 4: PROCESS AND RETURN THE RESPONSE ---
if ($response === false || $httpcode !== 200) {
    $errorDetails = [
        "message" => "Failed to get a response from the AI after retries. Please try again.",
        "http_code" => $httpcode,
    ];
    if (!empty($curlError)) {
        $errorDetails["curl_error"] = $curlError;
    }
    // Try to extract Gemini API error message if present
    $apiError = null;
    if ($response !== false) {
        $apiErrorData = json_decode($response, true);
        if (isset($apiErrorData['error']['message'])) {
            $apiError = $apiErrorData['error']['message'];
        }
    }
    if ($apiError) {
        $errorDetails["api_error"] = $apiError;
    }
    send_error($errorDetails);
}

$responseData = json_decode($response, true);
if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
    $aiJsonText = $responseData['candidates'][0]['content']['parts'][0]['text'];
    $firstBracket = strpos($aiJsonText, '{');
    $lastBracket = strrpos($aiJsonText, '}');
    if ($firstBracket !== false && $lastBracket !== false) {
        $json = substr($aiJsonText, $firstBracket, $lastBracket - $firstBracket + 1);
        $decodedJson = json_decode($json, true);
        if (json_last_error() === JSON_ERROR_NONE && isset($decodedJson['basicReport']['scores'])) {
            echo $json; // Success! Send the valid JSON to the frontend.
        } else {
            send_error("The AI returned an invalid analysis. Could not decode JSON.");
        }
    } else {
        send_error("Could not find a valid JSON object in the AI's response.");
    }
} else {
    send_error('The AI response was not in the expected format.');
}
?>
