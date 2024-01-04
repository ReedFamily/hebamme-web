#!/bin/bash
echo "*** BEGIN PACKAGE PROCESS ***"
[ ! -d "dist/upload" ] && mkdir -p "dist/upload/css/bootstrap"

GIT_COMMIT=$1
BUILD_NO=$2
COMMIT=${GIT_COMMIT:0:7}
VER=2.1.0
COPYRIGHT_YEAR=2024
LAST_MOD=$(date '+%Y-%m-%dT%H:%M:%S%z')
DEPLOYNAME="../hebamme-web-deploy_${VER}_${COMMIT}_${BUILD_NO}.tar.gz"
FLYWAYNAME="../hebamme-web-flyway_${VER}_${COMMIT}_${BUILD_NO}.tar.gz"

echo "*** COPY FILES ***"

cp ./index.html ./dist/upload/index.html
cp ./sitemap.xml ./dist/upload/sitemap.xml
cp ./LICENSE ./dist/upload/LICENSE
cp ./hebamme_favicon128.ico ./dist/upload/hebamme_favicon128.ico
cp ./favicon32.ico ./dist/upload/favicon32.ico
cp ./favicon.svg ./dist/upload/favicon.svg
cp ./404.html ./dist/upload/404.html
cp -R ./fonts ./dist/upload/fonts
cp -R ./js ./dist/upload/js
cp -R ./img ./dist/upload/img

# More acurately handles the css files for distribution instead of delivering the scss files.
cp ./css/*.css ./dist/upload/css
cp ./css/*.map ./dist/upload/css
cp ./css/bootstrap/*.map ./dist/upload/css/bootstrap
cp ./css/bootstrap/*.css ./dist/upload/css/bootstrap


cp -R ./backend ./dist/upload/backend 
cp -R ./admin ./dist/upload/admin

cp -R ./flyway ./dist

echo "*** PERFORM REPLACE ***"

grep -rl GIT_HASH ./dist | xargs sed -i 's/GIT_HASH/'${BUILD_NO}'/'
grep -rl REL_VER ./dist | xargs sed -i 's/REL_VER/'${VER}'/'
grep -rl COPYRIGHT_YEAR ./dist | xargs sed -i 's/COPYRIGHT_YEAR/'${COPYRIGHT_YEAR}'/'
grep -rl LAST_MOD ./dist | xargs sed -i 's/LAST_MOD/'${LAST_MOD}'/'

echo "*** BUILD PACKAGE ***"

cd dist
tar -czvf $DEPLOYNAME upload 
tar -czvf $FLYWAYNAME flyway
cd ..
echo "*** END PACKAGE PROCESS ***"