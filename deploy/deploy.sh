#!/bin/bash

set -euo pipefail

SCRIPT_DIR="$(cd -- "$(dirname -- "${BASH_SOURCE[0]}")" && pwd)"
BUILD_DIR="$SCRIPT_DIR/build"
PASSWORD_FILE="${DEPLOY_PASSWORD_FILE:-$SCRIPT_DIR/.ssh-password}"
REMOTE_HOST="${DEPLOY_REMOTE_HOST:-robijnsbos}"
REMOTE_BASE_DIR="${DEPLOY_REMOTE_BASE_DIR:-.}"
SSH_OPTIONS=(
  -o BatchMode=no
  -o StrictHostKeyChecking=accept-new
)

require_command() {
  if ! command -v "$1" >/dev/null 2>&1; then
    echo "Required command not found: $1" >&2
    exit 1
  fi
}

sync_remote_directory() {
  local directory_name="$1"
  local local_directory="$BUILD_DIR/$directory_name"
  local remote_directory="$REMOTE_BASE_DIR/$directory_name"

  sshpass -f "$PASSWORD_FILE" ssh "${SSH_OPTIONS[@]}" "$REMOTE_HOST" \
    "mkdir -p \"$remote_directory\""

  rsync -az --delete \
    --rsh="sshpass -f \"$PASSWORD_FILE\" ssh ${SSH_OPTIONS[*]}" \
    "$local_directory/" \
    "$REMOTE_HOST:$remote_directory/"
}

require_command docker
require_command rsync
require_command ssh
require_command sshpass

if [[ ! -f "$PASSWORD_FILE" ]]; then
  echo "SSH password file not found: $PASSWORD_FILE" >&2
  exit 1
fi

mkdir -p "$BUILD_DIR"
find "$BUILD_DIR" -mindepth 1 -maxdepth 1 -exec rm -rf {} +

# build
docker run --rm \
  -v "$BUILD_DIR:/mnt/build" \
  "$(docker build -q "$SCRIPT_DIR" -f "$SCRIPT_DIR/deploy.dockerfile")"

BUILD_DIRECTORIES=()
while IFS= read -r -d '' directory_path; do
  BUILD_DIRECTORIES+=("$(basename "$directory_path")")
done < <(find "$BUILD_DIR" -mindepth 1 -maxdepth 1 -type d -print0)

if [[ "${#BUILD_DIRECTORIES[@]}" -eq 0 ]]; then
  echo "No directories found in $BUILD_DIR after build." >&2
  exit 1
fi

for directory_name in "${BUILD_DIRECTORIES[@]}"; do
  sync_remote_directory "$directory_name"
done

sshpass -f "$PASSWORD_FILE" ssh "${SSH_OPTIONS[@]}" "$REMOTE_HOST" \
  "cd \"$REMOTE_BASE_DIR/robijnsbos_dt_app\" && php artisan migrate"

find "$BUILD_DIR" -mindepth 1 -maxdepth 1 -exec rm -rf {} +
