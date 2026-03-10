import urllib.request
import os

base_url = "https://themecraze.net/html/volia/images/"
target_dir = "/Applications/MAMP/htdocs/ikwa/assets/images/"

files_to_download = [
    # Backgrounds (judging by earlier tests, these are mostly .jpg or .png)
    ("background/4.jpg", "background/4.jpg"),
    ("background/7.jpg", "background/7.jpg"),
    ("background/8.jpg", "background/8.jpg"),
    
    # Icons (.png)
    ("icons/icon-dotted-circle.png", "icons/icon-dotted-circle.png"),
    ("icons/icon-world.png", "icons/icon-world.png"),
    ("icons/icon-bull-eye.png", "icons/icon-bull-eye.png"),
    ("icons/icon-bull-eye-2.png", "icons/icon-bull-eye-2.png"),
    ("icons/icon-select.png", "icons/icon-select.png"),
    ("icons/object-1.png", "icons/object-1.png"),
    ("icons/object-7.png", "icons/object-7.png"),
    ("icons/object-8.png", "icons/object-8.png"),
    ("icons/object-9.png", "icons/object-9.png"),
    ("icons/object-11.png", "icons/object-11.png"),
    ("icons/object-12.png", "icons/object-12.png"),
    ("icons/object-13.png", "icons/object-13.png"),
    ("icons/object-14.png", "icons/object-14.png"),
    ("icons/pattern-4.png", "icons/pattern-4.png"),
    ("icons/pattern-5.png", "icons/pattern-5.png"),
    ("icons/pattern-6.png", "icons/pattern-6.png"),
    ("icons/curved-border.png", "icons/curved-border.png"),
    ("icons/boxed-bg.png", "icons/boxed-bg.png"),
    ("icons/pricing-bg-2.png", "icons/pricing-bg-2.png"),
    ("icons/schedule-1.png", "icons/schedule-1.png"),
    ("icons/schedule-2.png", "icons/schedule-2.png"),
    ("icons/schedule-3.png", "icons/schedule-3.png"),
    ("icons/schedule-4.png", "icons/schedule-4.png"),
    ("icons/fun-fact-one.png", "icons/fun-fact-one.png"),
    ("icons/fun-fact-two.png", "icons/fun-fact-two.png"),
    ("icons/fun-fact-three.png", "icons/fun-fact-three.png"),
    ("icons/fun-fact-four.png", "icons/fun-fact-four.png"),
]

for src, dest in files_to_download:
    url = base_url + src
    out_path = os.path.join(target_dir, dest)
    os.makedirs(os.path.dirname(out_path), exist_ok=True)
    
    # Skip if already exists and is non-empty (and not a fake .html file)
    if os.path.exists(out_path) and os.path.getsize(out_path) > 0:
        continue

    print(f"Downloading {url} to {out_path}...")
    try:
        urllib.request.urlretrieve(url, out_path)
        print(f"✅ Downloaded {dest}")
    except Exception as e:
        print(f"❌ Failed to download {dest}: {e}")
        # Try .png if .jpg fails, or vice versa for backgrounds
        if src.startswith("background"):
            alt_src = src.replace('.jpg', '.png') if src.endswith('.jpg') else src.replace('.png', '.jpg')
            url = base_url + alt_src
            print(f"  -> Retrying with {url}...")
            try:
                urllib.request.urlretrieve(url, out_path)
                print(f"✅ Downloaded {dest} (as alt format)")
            except Exception as e2:
                print(f"❌ Failed alternative {dest}: {e2}")

print("Download script finished.")
