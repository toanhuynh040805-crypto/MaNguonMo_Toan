#!/usr/bin/env python3
"""Download real, freely licensed Wikimedia Commons photographs for OpenShop.
The output is derived WebP thumbnails; licenses and authors are in resources/data.
No AI images and no fabricated product photos are used.
"""
import io
import json
import os
import pathlib
import sys
import time
import urllib.parse
import urllib.request

from PIL import Image, ImageOps

ROOT = pathlib.Path(__file__).resolve().parent.parent
CATALOG = ROOT / "resources/data/real_product_photos.json"
OUT = ROOT / "public/images/real-products"
USER_AGENT = "OpenShop-Coursework/1.0 (https://github.com/toanhuynh040805-crypto/MaNguonMo_Toan; educational Wikimedia image attribution)"
MAX_BYTES = 25_000_000


def get_bytes(url):
    request = urllib.request.Request(url, headers={
        "User-Agent": USER_AGENT,
        "Accept": "image/avif,image/webp,image/png,image/jpeg,*/*",
    })
    with urllib.request.urlopen(request, timeout=60) as response:
        content_type = response.headers.get("Content-Type", "")
        blob = response.read(MAX_BYTES + 1)
        if len(blob) > MAX_BYTES:
            raise ValueError("Image exceeds 25 MB")
        if not content_type.startswith("image/"):
            raise ValueError(f"Expected image, got {content_type}; url={url}")
        return blob


def thumbnail_url(filename):
    # Use Wikimedia imageinfo API to get a sized thumbnail URL.
    params = urllib.parse.urlencode({
        "action": "query", "format": "json", "prop": "imageinfo",
        "iiprop": "url", "iiurlwidth": 960,
        "titles": "File:" + filename,
    })
    url = "https://commons.wikimedia.org/w/api.php?" + params
    request = urllib.request.Request(url, headers={"User-Agent": USER_AGENT})
    with urllib.request.urlopen(request, timeout=45) as response:
        data = json.load(response)
    for page in data["query"]["pages"].values():
        info = page.get("imageinfo")
        if info:
            return info[0].get("thumburl", info[0]["url"])
    raise ValueError("Wikimedia file not found: " + filename)


def process(photo):
    dest = OUT / (photo["slug"] + ".webp")
    if dest.exists() and dest.stat().st_size > 5000:
        print("Already downloaded:", dest.name, flush=True)
        return
    last_error = None
    for attempt in range(3):
        try:
            url = thumbnail_url(photo["file"])
            data = get_bytes(url)
            with Image.open(io.BytesIO(data)) as source:
                source.load()
                image = ImageOps.exif_transpose(source).convert("RGB")
                image.thumbnail((960, 960))
                image.save(dest, "WEBP", quality=82, method=6)
            if dest.stat().st_size < 5000:
                raise ValueError("Unexpectedly small thumbnail")
            print(f"OK {photo['slug']}: {dest.stat().st_size} bytes", flush=True)
            return
        except Exception as exc:
            last_error = exc
            print(f"Retry {attempt + 1}: {photo['slug']} — {exc}", flush=True)
            time.sleep(2 * (attempt + 1))
    raise RuntimeError("Failed to download " + photo["slug"]) from last_error


def main():
    OUT.mkdir(parents=True, exist_ok=True)
    photos = json.loads(CATALOG.read_text(encoding="utf-8"))
    if len(photos) != 15:
        raise ValueError("Expected 15 catalog photographs")
    for p in photos:
        process(p)

    # Homepage uses a licensed photograph of a real gaming computer setup.
    process({
        "slug": "hero-tech",
        "file": "Gaming PC-Setup - Astaroth- The Completed System.jpg",
    })
    print("DONE: 15 real product photos and one real desk setup photo", flush=True)


if __name__ == "__main__":
    main()
