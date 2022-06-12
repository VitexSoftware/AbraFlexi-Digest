#!/usr/bin/env bash
export DEBIAN_FRONTEND="noninteractive"
sudo apt install lsb-release wget

echo "deb http://repo.vitexsoftware.cz $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/vitexsoftware.list
sudo wget -O /etc/apt/trusted.gpg.d/vitexsoftware.gpg http://repo.vitexsoftware.cz/keyring.gpg
echo "deb [trusted=yes] file:///vagrant/deb ./" > /etc/apt/sources.list.d/local.list

sudo apt update
apt install -y php-cli php-curl php-pear php-intl php-zip composer dpkg-dev devscripts php-apigen-theme-default debhelper gdebi-core


cd /vagrant
debuild -i -us -uc -b

#mkdir -p /vagrant/deb
#mv /*.deb /vagrant/deb
#cd /vagrant/deb
#dpkg-scanpackages . /dev/null | gzip -9c > Packages.gz
#echo "deb file:/vagrant/deb ./" > /etc/apt/sources.list.d/local.list
#apt-get update
export DEBCONF_DEBUG="developer"

#apt-get -y --allow-unauthenticated install flexibee-digest
gdebi -n  ../flexibee-digest_*_all.deb 

cp -f /vagrant/tests/digest.json /etc/flexibee/digest.json

flexibee-daydigest
flexibee-monthdigest
flexibee-weekdigest
flexibee-yeardigest
