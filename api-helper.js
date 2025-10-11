/**
 * API Helper module for Gemini integration
 */

const GEMINI_API_KEY = 'gen-lang-client-0418804085'; // Replace with your actual API key
const GEMINI_ENDPOINT = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-lite-preview-06-17:generateContent';

/**
 * Captures screenshots of a website using thum.io service
 * @param {string} websiteUrl - The URL to capture
 * @returns {Promise<{desktop: string, mobile: string}>} Base64 encoded screenshots
 */
async function captureScreenshots(websiteUrl) {
    const desktopScreenshotUrl = `https://image.thum.io/get/width/1280/crop/800/${websiteUrl}`;
    const mobileScreenshotUrl = `https://image.thum.io/get/width/400/crop/800/${websiteUrl}`;

    try {
        const [desktopResponse, mobileResponse] = await Promise.all([
            fetch(desktopScreenshotUrl),
            fetch(mobileScreenshotUrl)
        ]);

        if (!desktopResponse.ok || !mobileResponse.ok) {
            throw new Error('Failed to capture screenshots');
        }

        const [desktopBlob, mobileBlob] = await Promise.all([
            desktopResponse.blob(),
            mobileResponse.blob()
        ]);

        const [desktopBase64, mobileBase64] = await Promise.all([
            blobToBase64(desktopBlob),
            blobToBase64(mobileBlob)
        ]);

        return {
            desktop: desktopBase64,
            mobile: mobileBase64
        };
    } catch (error) {
        console.error('Screenshot capture failed:', error);
        throw new Error('Could not capture website screenshots. Please ensure the URL is accessible.');
    }
}

/**
 * Converts a Blob to base64 string
 * @param {Blob} blob - The blob to convert
 * @returns {Promise<string>} Base64 encoded string
 */
function blobToBase64(blob) {
    return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.onload = () => {
            const base64String = reader.result.split(',')[1];
            resolve(base64String);
        };
        reader.onerror = () => reject(new Error('Failed to convert screenshot to base64'));
        reader.readAsDataURL(blob);
    });
}

/**
 * The evaluation prompt template for Gemini
 */
const EVALUATION_PROMPT = `You are a world-class UX/UI and conversion rate optimization expert.

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
- Be honest — most small business sites will score in the 50–75 range.`;

/**
 * Makes a request to the Gemini API with retries
 * @param {Object} requestBody - The request body to send
 * @param {number} maxRetries - Maximum number of retry attempts
 * @returns {Promise<Object>} The parsed JSON response
 */
async function makeGeminiRequest(requestBody, maxRetries = 2) {
    let lastError;

    for (let attempt = 1; attempt <= maxRetries; attempt++) {
        try {
            const response = await fetch(`${GEMINI_ENDPOINT}?key=${GEMINI_API_KEY}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(requestBody)
            });

            if (!response.ok) {
                throw new Error(`HTTP error: ${response.status}`);
            }

            const data = await response.json();
            
            if (!data.candidates || !data.candidates[0]?.content?.parts?.[0]?.text) {
                throw new Error('Invalid response format from Gemini API');
            }

            const jsonText = data.candidates[0].content.parts[0].text;
            const firstBracket = jsonText.indexOf('{');
            const lastBracket = jsonText.lastIndexOf('}');

            if (firstBracket === -1 || lastBracket === -1) {
                throw new Error('Could not find valid JSON in response');
            }

            const jsonString = jsonText.substring(firstBracket, lastBracket + 1);
            const result = JSON.parse(jsonString);

            if (!result.basicReport?.scores) {
                throw new Error('Response missing required score data');
            }

            return result;

        } catch (error) {
            lastError = error;
            if (attempt < maxRetries) {
                await new Promise(resolve => setTimeout(resolve, 2000)); // 2 second delay between retries
                continue;
            }
        }
    }

    throw new Error(`Failed after ${maxRetries} attempts. Last error: ${lastError.message}`);
}

/**
 * Performs a complete website evaluation
 * @param {string} websiteUrl - The URL to evaluate
 * @returns {Promise<Object>} The evaluation results
 */
async function evaluateWebsite(websiteUrl) {
    try {
        // Capture screenshots
        const screenshots = await captureScreenshots(websiteUrl);

        // Prepare request body
        const requestBody = {
            contents: [{
                parts: [
                    { text: EVALUATION_PROMPT },
                    { text: "\n\n--- DESKTOP SCREENSHOT ---" },
                    { inline_data: { mime_type: 'image/png', data: screenshots.desktop }},
                    { text: "\n\n--- MOBILE SCREENSHOT ---" },
                    { inline_data: { mime_type: 'image/png', data: screenshots.mobile }}
                ]
            }]
        };

        // Make request to Gemini API
        const result = await makeGeminiRequest(requestBody);

        return result;

    } catch (error) {
        console.error('Website evaluation failed:', error);
        throw error;
    }
}

export {
    evaluateWebsite,
    captureScreenshots
};