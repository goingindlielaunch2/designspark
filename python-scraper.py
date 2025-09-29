import csv
import re
import requests
import time
from bs4 import BeautifulSoup
from pathlib import Path
from email.message import EmailMessage
import smtplib
import json

# === CONFIGURATION ===

GEMINI_API_KEY = "AIzaSyDtD6G7-NNaMD2zvonOQ35Xi5-P90XTd2Y"
GEMINI_API_URL = "https://api.generativeai.google/v1beta2/models/assistant-3p/text:generate"

SMTP_SERVER = 'smtp.gmail.com'
SMTP_PORT = 587
SMTP_USER = 'hello.withdesignspark@gmail.com'         # <--- Replace this with your Gmail address
SMTP_PASS = 'hwyzgcpdtsnwkbro'        # <--- Your Gmail App Password (spaces removed)

BATCH_SIZE = 10
DELAY_SECONDS = 30

OUTPUT_DIR = Path("output")
EMAILS_DIR = OUTPUT_DIR / "emails"
SENT_LOG = OUTPUT_DIR / "sent_emails.log"
LEADS_CSV = OUTPUT_DIR / "leads.csv"

OUTPUT_DIR.mkdir(exist_ok=True)
EMAILS_DIR.mkdir(exist_ok=True)
if not SENT_LOG.exists():
    SENT_LOG.write_text("")

# === FUNCTIONS ===

def scrape_business_info(url):
    data = {"url": url, "email": "", "contact_page": "", "error": ""}
    try:
        r = requests.get(url, timeout=10)
        soup = BeautifulSoup(r.text, 'html.parser')

        # Find email in page text
        match = re.search(r'[\w\.-]+@[\w\.-]+\.\w+', r.text)
        if match:
            data["email"] = match.group()
        else:
            # Find contact page link
            contact_link = soup.find('a', href=re.compile("contact", re.I))
            if contact_link and 'href' in contact_link.attrs:
                data["contact_page"] = contact_link['href']
    except Exception as e:
        data["error"] = str(e)
    return data

def query_gemini_ux(url):
    prompt = f"""
You are a top UX/UI expert. Evaluate this small business website homepage: {url}

Provide a JSON with:
- overallScore: 0-100
- issues: list of main UX issues (2-5 items)
- recommendations: 2-5 actionable UX improvements

Example output:
{{
  "overallScore": 75,
  "issues": ["Slow loading", "No mobile menu"],
  "recommendations": ["Optimize images", "Add mobile navigation"]
}}
"""
    headers = {
        "Authorization": f"Bearer {GEMINI_API_KEY}",
        "Content-Type": "application/json",
    }
    body = {
        "prompt": {
            "text": prompt,
        },
        "temperature": 0.3,
        "candidateCount": 1,
        "maxOutputTokens": 512,
    }
    response = requests.post(GEMINI_API_URL, headers=headers, json=body, timeout=20)
    response.raise_for_status()
    data = response.json()
    output_text = data["candidates"][0]["output"]
    try:
        ux_report = json.loads(output_text)
    except json.JSONDecodeError:
        start = output_text.find("{")
        end = output_text.rfind("}")
        if start != -1 and end != -1:
            ux_report = json.loads(output_text[start:end+1])
        else:
            raise
    return ux_report

def generate_email(business_data, audit):
    domain = business_data["url"].split("//")[-1].split("/")[0]
    issues = ", ".join(audit.get("issues", [])[:2])
    return f"""Subject: Quick UX suggestion for {domain}

Hi there,

I took a quick look at your website ({business_data['url']}) and noticed a couple of ways to improve user experience — especially: {issues}.

I run a local web design studio and specialize in helping businesses like yours improve website conversions. I’d be happy to share a free, customized UX improvement list if you’re interested.

Best,  
David 

Https://withDesignSpark.com

—
DesignSpark  
6211 S Highland Dr #4060  
Holladay, UT 84121

To unsubscribe, simply reply to this email with the word “Unsubscribe” and I will remove you from my list.
"""

def save_to_csv(data, filename=LEADS_CSV):
    keys = data[0].keys()
    with open(filename, "w", newline='', encoding='utf-8') as f:
        writer = csv.DictWriter(f, fieldnames=keys)
        writer.writeheader()
        writer.writerows(data)

def load_sent_emails():
    return set(line.strip() for line in SENT_LOG.read_text().splitlines() if line.strip())

def log_sent_email(email):
    with SENT_LOG.open("a", encoding="utf-8") as f:
        f.write(email + "\n")

def send_email(to_address, subject, body):
    msg = EmailMessage()
    msg['Subject'] = subject
    msg['From'] = SMTP_USER
    msg['To'] = to_address
    msg.set_content(body)
    with smtplib.SMTP(SMTP_SERVER, SMTP_PORT) as smtp:
        smtp.starttls()
        smtp.login(SMTP_USER, SMTP_PASS)
        smtp.send_message(msg)

# === MAIN PIPELINE ===

def run_pipeline(urls):
    all_data = []
    sent_emails = load_sent_emails()
    emails_sent_this_run = 0

    for idx, url in enumerate(urls):
        print(f"Processing {url} ({idx+1}/{len(urls)})")
        info = scrape_business_info(url)

        if info["error"]:
            print(f"  Scrape error: {info['error']}")
            all_data.append(info)
            continue

        if not (info["email"] or info["contact_page"]):
            print("  No contact info found, skipping.")
            all_data.append(info)
            continue

        try:
            audit = query_gemini_ux(url)
        except Exception as e:
            print(f"  Gemini API error: {e}")
            audit = {"overallScore": None, "issues": [], "recommendations": []}

        combined = {**info, **audit}
        all_data.append(combined)

        email_address = info.get("email")
        if email_address and email_address not in sent_emails:
            email_text = generate_email(info, audit)
            subject_line = email_text.splitlines()[0].replace("Subject: ", "").strip()
            try:
                send_email(email_address, subject_line, email_text)
                print(f"  Email sent to {email_address}")
                log_sent_email(email_address)
                emails_sent_this_run += 1
                if emails_sent_this_run >= BATCH_SIZE:
                    print(f"Reached batch limit of {BATCH_SIZE}, stopping for now.")
                    break
                time.sleep(DELAY_SECONDS)
            except Exception as e:
                print(f"  Failed to send email to {email_address}: {e}")
        else:
            print("  Email already sent or no email found, skipping sending.")

    save_to_csv(all_data)
    print(f"\nFinished. Leads saved to {LEADS_CSV}. Emails sent this run: {emails_sent_this_run}")

# === RUN SCRIPT ===

if __name__ == "__main__":
    target_urls = [
        "https://example.com",
        # Add more URLs here
    ]

    run_pipeline(target_urls)
