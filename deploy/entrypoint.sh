#!/bin/bash

set -e
shopt -s dotglob

BUILD_FOLDER="/mnt/build"
PATCH_FILES="/mnt/patch_files"
REPO="/mnt/repo"

# pull from git & build
cd $REPO
git clone https://github.com/gerb-ster/RobijnsBos
cd RobijnsBos
LATEST_TAG="$(git tag --sort=-v:refname | head -n 1)"
if [[ -z "$LATEST_TAG" ]]; then
  echo "No git tags found to check out." >&2
  exit 1
fi
git checkout "$LATEST_TAG"
cd web-app
composer install
npm install && npm run build && npm prune --production && rm -Rf node_modules

# copy files from volume_mount into build directory
cd $BUILD_FOLDER
mkdir dt.robijnsbos.nl
mkdir robijnsbos_dt_app
mv $REPO/RobijnsBos/web-app/public/* ./dt.robijnsbos.nl
mv $REPO/RobijnsBos/web-app/* ./robijnsbos_dt_app
rm -f ./dt.robijnsbos.nl/hot
rm -Rf ./robijnsbos_dt_app/public/

# apply patch files
cd $PATCH_FILES
cp -rf index.php $BUILD_FOLDER/dt.robijnsbos.nl/index.php
cp -rf .env $BUILD_FOLDER/robijnsbos_dt_app/.env
