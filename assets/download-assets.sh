#!/bin/bash
BASE="https://themecraze.net/html/volia/assets/images"
cd /Applications/MAMP/htdocs/ikwa/assets/images

# Background files
BG_FILES="2 3 4 7 8 10"
for f in $BG_FILES; do
  OUT="background/${f}.png"
  echo -n "Downloading background/${f}... "
  curl -sL "$BASE/background/${f}.html" -o "$OUT"
  SZ=$(wc -c < "$OUT")
  TYPE=$(file "$OUT" | cut -d: -f2)
  echo "${SZ} bytes | ${TYPE}"
done

# Icon files
ICON_FILES="circle-1 circle-2 circle-4 icon-dotted-circle icon-world icon-bull-eye icon-bull-eye-2 icon-select object-1 object-2 object-3 object-4 object-5 object-7 object-8 object-9 object-10 object-11 object-12 object-13 object-14 object-15 object-16 pattern-1 pattern-2 pattern-3 pattern-4 pattern-5 pattern-6 wave divider divider-centered curved-border counter-bg boxed-bg pricing-bg pricing-bg-2 schedule-1 schedule-2 schedule-3 schedule-4 fun-fact-one fun-fact-two fun-fact-three fun-fact-four"
for f in $ICON_FILES; do
  OUT="icons/${f}.png"
  echo -n "Downloading icons/${f}... "
  curl -sL "$BASE/icons/${f}.html" -o "$OUT"
  SZ=$(wc -c < "$OUT")
  TYPE=$(file "$OUT" | cut -d: -f2)
  echo "${SZ} bytes | ${TYPE}"
done

echo ""
echo "Done. Check above for any HTML (failed) downloads."
