#!/bin/bash
echo "*** BEGIN PACKAGE PROCESS ***"
[ ! -d "dist/upload" ] && mkdir -p "dist/upload"

GIT_COMMIT=$1
BUILD_NO=$2
COMMIT=${GIT_COMMIT:0:7}
VER=2.0.1
DEPLOYNAME="../hebamme-web-deploy_${VER}_${COMMIT}_${BUILD_NO}.tar.gz"
FLYWAYNAME="../hebamme-web-flyway_${VER}_${COMMIT}_${BUILD_NO}.tar.gz"

echo "*** COPY FILES ***"

cp ./index.html ./dist/upload/index.html
cp ./LICENSE ./dist/upload/LICENSE
cp ./hebamme_favicon128.ico ./dist/upload/hebamme_favicon128.ico
cp ./favicon32.ico ./dist/upload/favicon32.ico
cp ./favicon.svg ./dist/upload/favicon.svg
cp ./404.html ./dist/upload/404.html
cp -R ./fonts ./dist/upload/fonts
cp -R ./js ./dist/upload/js
cp -R ./img ./dist/upload/img
cp -R ./fontawesome-free-5.15.3-web ./dist/upload/fontawesome-free-5.15.3-web
cp -R ./css ./dist/upload/css 
cp -R ./backend ./dist/upload/backend 
cp -R ./admin ./dist/upload/admin

cp -R ./flyway ./dist

echo "*** PERFORM REPLACE ***"

sed -i 's/GIT_HASH/'${BUILD_NO}'/' ./dist/upload/index.html

echo "*** BUILD PACKAGE ***"

cd dist
tar -czvf $DEPLOYNAME upload 
tar -czvf $FLYWAYNAME flyway
cd ..
echo "*** END PACKAGE PROCESS ***"