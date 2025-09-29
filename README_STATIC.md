# DesignSpark Static Version

This repository contains a static-only version of the original PHP-powered DesignSpark site for deployment on GitHub Pages (or any static host).

## What Changed

| Feature | Original (PHP) | Static Version |
|---------|----------------|----------------|
| Dynamic reviews & report counts | Database + PHP includes | Replaced with hardcoded sample values |
| Website Evaluation Tool | Backend API (`api_handler.php`) + Stripe session | Client-only demo with random sample scores |
| Stripe Checkout | PHP session creation (`create-checkout-session.php`) | Direct Stripe Payment Links |
| Contact Form | `process-contact-form.php` (server-side email) | Formspree placeholder (requires endpoint replacement) |
| Advanced Report PDF | Server-stored report ID / DB | Removed (only mock basic demo) |

## Files Added
- `index.html` – Static homepage replacement for `index.php`
- `README_STATIC.md` – This guide

## Safe to Publish?
Yes — all secret keys and server logic were removed. Do **not** expose any Stripe *secret* keys (`sk_...`). Only public Payment Links and publishable keys are safe.

## Activating the Contact Form
1. Create a free Formspree form: https://formspree.io
2. Replace the `action` attribute in `index.html` contact form:  
   `<form action="https://formspree.io/f/your-id" method="POST">`
3. (Optional) Add a hidden `_subject` field:  
   `<input type="hidden" name="_subject" value="New Contact Submission" />`

## Deploy to GitHub Pages
1. Commit `index.html` to the root of the repository.
2. Push to `main` (or default branch).
3. In GitHub: Settings → Pages → Source: `Deploy from a branch`, select `/(root)`.
4. Wait for build. Site will be at: `https://<username>.github.io/<repo>/`

## Optional Improvements
- Extract inline `<script>` in `index.html` into `assets/js/main.js`
- Add a lightweight service worker for asset caching
- Replace random evaluation scores with a real API via serverless function (Netlify / Vercel)
- Add analytics (e.g., Plausible or Simple Analytics for privacy)

## Reverting to Dynamic Version
Keep the original PHP files (they won't run on GitHub Pages). To enable dynamic features again, deploy to a PHP-capable host (e.g., Render, Hostinger, DigitalOcean, PlanetScale + Vercel hybrid).

## License / Attribution
All content © DesignSpark. Payment links belong to your Stripe account. Ensure product pricing matches your Stripe dashboard.

---
Need help wiring this to a serverless backend later? Open an issue or extend with a `/api` folder using Cloudflare Workers / Netlify Functions.
