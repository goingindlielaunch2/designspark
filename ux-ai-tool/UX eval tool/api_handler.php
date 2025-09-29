<?php
// Added for more robust error reporting during debugging
ini_set('display_errors', 0); // Don't display errors to the browser directly
error_reporting(E_ALL);

// Set headers to allow cross-origin requests and define content type
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
header('Content-Type: application/json');

// Handle preflight request for CORS
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    exit;
}

// --- CONFIGURATION ---
// Gemini API Key
$geminiApiKey = 'AIzaSyC_JE5rLIUpz2lZLzmMFUh4cbRJJyrR9pM'; 

// --- FUNCTION TO SEND ERROR RESPONSE ---
function send_error($message) {
    http_response_code(400);
    // Log the error to the server's error log for more detailed debugging
    error_log("UX Tool Error: " . $message);
    echo json_encode(['error' => $message]);
    exit;
}

// --- MAIN LOGIC ---

// 1. Get and validate the input URL from the frontend
$input = json_decode(file_get_contents('php://input'), true);
if (!isset($input['url']) || !filter_var($input['url'], FILTER_VALIDATE_URL)) {
    send_error('Invalid or missing URL.');
}
$targetUrl = $input['url'];

// 2. Get a screenshot of the website using Thum.io's free tier.
$desktopScreenshotUrl = "https://image.thum.io/get/width/1280/crop/800/" . $targetUrl;
$mobileScreenshotUrl = "https://image.thum.io/get/width/400/crop/800/" . $targetUrl;

$context = stream_context_create(['http' => ['timeout' => 45]]);
$desktopImage = @file_get_contents($desktopScreenshotUrl, false, $context);
// Add a small delay to help prevent potential caching issues on the screenshot service
sleep(1); 
$mobileImage = @file_get_contents($mobileScreenshotUrl, false, $context);

if ($desktopImage === false || $mobileImage === false) {
    send_error('Could not take a screenshot of the provided URL. Please ensure it is a live, public website.');
}
$base64DesktopImage = base64_encode($desktopImage);
$base64MobileImage = base64_encode($mobileImage);

// 3. Prepare the request for the Gemini API
$geminiApiUrl = "https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key={$geminiApiKey}";

// **FIXED**: Updated the prompt to explicitly label the desktop and mobile screenshots.
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


// **FIXED**: Explicitly label each image for the AI.
$requestBody = [
    'contents' => [
        [
            'parts' => [
                ['text' => $prompt],
                ['text' => "\n\n--- DESKTOP SCREENSHOT ---"],
                [
                    'inline_data' => [
                        'mime_type' => 'image/png',
                        'data' => $base64DesktopImage
                    ]
                ],
                ['text' => "\n\n--- MOBILE SCREENSHOT ---"],
                [
                    'inline_data' => [
                        'mime_type' => 'image/png',
                        'data' => $base64MobileImage
                    ]
                ]
            ]
        ]
    ]
];

// 4. Make the cURL request to the Gemini API with retry logic
$maxRetries = 3;
$retryDelay = 2; // in seconds

for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $geminiApiUrl);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($requestBody));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_TIMEOUT, 60);

    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);

    if ($httpcode === 200 && $response !== false) {
        break;
    }
    
    if ($httpcode === 503 || $httpcode === 500) {
        error_log("Attempt {$attempt} failed with status {$httpcode}. Retrying in {$retryDelay} seconds...");
        if ($attempt < $maxRetries) {
            sleep($retryDelay);
        }
    } else {
        break;
    }
}

// 5. Process and return the response
if ($response === false) {
    send_error("cURL Error after all retries: {$curl_error}");
}

if ($httpcode !== 200) {
    if ($httpcode === 503 || $httpcode === 500) {
        send_error("The AI is currently busy or taking a short break. Please try again in a few minutes.");
    }
    send_error("Failed to get a response from the AI. API returned status: {$httpcode}. Response: {$response}");
}

$responseData = json_decode($response, true);

if (isset($responseData['candidates'][0]['content']['parts'][0]['text'])) {
    $aiJsonText = $responseData['candidates'][0]['content']['parts'][0]['text'];
    error_log("DEBUG: AI Raw Text Part: " . $aiJsonText); 

    $firstBracket = strpos($aiJsonText, '{');
    $lastBracket = strrpos($aiJsonText, '}');
    $json = '';

    if ($firstBracket !== false && $lastBracket !== false && $lastBracket > $firstBracket) {
        $json = substr($aiJsonText, $firstBracket, $lastBracket - $firstBracket + 1);
    } else {
        send_error("Could not find a valid JSON object in the AI's response.");
    }
    
    $decodedJson = json_decode($json, true);
    if (json_last_error() === JSON_ERROR_NONE) {
        if (isset($decodedJson['basicReport']['scores'])) {
            error_log("DEBUG: Successfully decoded JSON. Sending to frontend.");
            echo $json;
        } else {
            send_error("AI response was parsed but is missing the required 'basicReport.scores' object/array.");
        }
    } else {
        $json_error = json_last_error_msg();
        error_log("DEBUG: JSON Decode Error: " . $json_error . " on text: " . $json);
        send_error("The AI returned an invalid analysis. JSON decode error: {$json_error}. Please try again.");
    }
} elseif (isset($responseData['error']['message'])) {
    send_error("AI API Error: " . $responseData['error']['message']);
} 
else {
    send_error('The AI response was not in the expected format. No text part found in candidate.');
}

?>
