import requests
from bs4 import BeautifulSoup
import re
import time
import csv
from pathlib import Path

def get_yelp_business_urls(city="holladay", state="ut", category="web design", pages=1):
    base_url = "https://www.yelp.com"
    all_sites = []

    for page in range(pages):
        search_url = f"{base_url}/search?find_desc={category}&find_loc={city}%2C%20{state}&start={page*10}"
        print(f"Searching Yelp page {page+1}...")

        headers = {"User-Agent": "Mozilla/5.0"}
        r = requests.get(search_url, headers=headers)
        soup = BeautifulSoup(r.text, "html.parser")

        profile_links = soup.select("a[href^='/biz/']")
        biz_links = list(set(link['href'] for link in profile_links if '/adredir?' not in link['href']))
        print(f"Found {len(biz_links)} businesses on page {page+1}")

        for rel_link in biz_links:
            biz_url = base_url + rel_link
            try:
                time.sleep(1)  # Be nice to Yelp
                biz_resp = requests.get(biz_url, headers=headers)
                biz_soup = BeautifulSoup(biz_resp.text, "html.parser")

                # Attempt to find a real website link
                website_anchor = biz_soup.select_one("a[href^='http'][target='_blank']")
                if website_anchor:
                    ext_url = website_anchor['href']
                    if "yelp.com" not in ext_url:
                        clean_url = re.sub(r"\?.*", "", ext_url)
                        business_name = biz_soup.select_one("h1")  # Usually the business name
                        all_sites.append({
                            "business_name": business_name.text.strip() if business_name else "N/A",
                            "yelp_profile": biz_url,
                            "website": clean_url
                        })
                        print(f"✔ Found: {clean_url}")
            except Exception as e:
                print(f"✖ Failed to load {biz_url}: {e}")

    return all_sites

def save_pending_to_csv(data, filename="yelp_results_pending.csv"):
    if not data:
        print("No business websites found.")
        return
    with open(filename, "w", newline="", encoding="utf-8") as f:
        writer = csv.DictWriter(f, fieldnames=["business_name", "yelp_profile", "website"])
        writer.writeheader()
        writer.writerows(data)
    print(f"\n✅ Saved {len(data)} businesses to {filename} for manual approval.")

# === RUN SCRAPER ===
if __name__ == "__main__":
    results = get_yelp_business_urls(
        city="kaysville", state="ut", category="landscaping", pages=2
    )
    save_pending_to_csv(results)
