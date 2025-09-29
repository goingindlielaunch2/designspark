# Email Notification Setup Guide

## Current Issue
The server's built-in mail() function may not be working properly. Here are several alternative solutions:

## Option 1: Test Current Setup
1. Visit: `http://yoursite.com/test-email.php`
2. This will test your server's email functionality
3. Check your email at hello@withdesignspark.com

## Option 2: Use a Webhook Service (Recommended)
Set up a free webhook with Zapier or Make.com:

### Zapier Setup:
1. Go to zapier.com and create a free account
2. Create a new Zap with "Webhooks by Zapier" as trigger
3. Choose "Catch Hook" and copy the webhook URL
4. Set the action to "Email by Zapier" 
5. Configure it to send to hello@withdesignspark.com
6. Update the webhook URL in process-contact-form.php

### Make.com Setup:
1. Go to make.com and create a free account
2. Create a new scenario with "Webhooks" as trigger
3. Choose "Custom webhook" and copy the URL
4. Add "Email" module as the next step
5. Configure to send to hello@withdesignspark.com

## Option 3: SMTP Configuration
If your hosting provider supports SMTP, you can use PHPMailer:

1. Download PHPMailer
2. Configure with your hosting provider's SMTP settings
3. Replace the mail() function calls

## Option 4: Check Server Logs
The improved code now logs all email attempts. Check:
- Server error logs
- The file `email_notifications.log` in your site directory

## Option 5: Hosting Provider Support
Contact your hosting provider to:
- Enable mail() function
- Get SMTP credentials
- Check email server configuration

## Current Fallback
The system now logs all contact form submissions to `email_notifications.log` so you won't lose any messages even if email fails.
