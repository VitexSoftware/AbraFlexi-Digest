#!/bin/bash
git clone --depth=1 https://github.com/thomaspark/bootswatch.git

for styledir in bootswatch/dist/*; do
    stylename=$(basename $styledir)
#    cp -f $styledir/bootstrap.css src/css/themes/$stylename.css
    cp -f $styledir/bootstrap.min.css src/css/themes/$stylename.min.css
done

wget https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css -O src/css/themes/bootstrap.min.css

rm -rf bootswatch

