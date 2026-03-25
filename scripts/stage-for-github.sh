#!/usr/bin/env bash
set -euo pipefail

ROOT="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
STAGE_DIR="$ROOT/github-publish"

echo "Cleaning stage folder..."
rm -rf "$STAGE_DIR"
mkdir -p "$STAGE_DIR/popdata"
mkdir -p "$STAGE_DIR/popclockV2"

echo "Building popclock..."
cd "$ROOT/frontend/apps/popclock"
npm run build

echo "Copying backend publish files..."
mkdir -p "$STAGE_DIR/popdata"
cp -R "$ROOT/backend/config" "$STAGE_DIR/popdata/"
mkdir -p "$STAGE_DIR/popdata/data"
cp -R "$ROOT/backend/data/static" "$STAGE_DIR/popdata/data/"
cp -R "$ROOT/backend/public" "$STAGE_DIR/popdata/"
cp -R "$ROOT/backend/src" "$STAGE_DIR/popdata/"
if [ -f "$ROOT/backend/.htaccess" ]; then
  cp "$ROOT/backend/.htaccess" "$STAGE_DIR/popdata/"
fi

echo "Copying frontend publish files..."
cp -R "$ROOT/frontend/apps/popclock/dist/." "$STAGE_DIR/popclockV2/"

echo "Done."
echo
echo "Publish folder created at:"
echo "  $STAGE_DIR"
echo
echo "Contents:"
find "$STAGE_DIR" -maxdepth 3 | sort