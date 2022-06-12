currentversion=$(shell dpkg-parsechangelog --show-field Version)
repoversion=$(shell LANG=C aptitude show abraflexi-digest | grep Version: | awk '{print $$2}')
nextversion=$(shell echo $(repoversion) | perl -ne 'chomp; print join(".", splice(@{[split/\./,$$_]}, 0, -1), map {++$$_} pop @{[split/\./,$$_]}), "\n";')


all:

composer:
	composer update

fresh: composer git

git:
	git pull

install: 
	echo install
	
build:
	echo build

compass:
	compass compile  --output-style compressed --force  .


#pretest:
#	composer --ansi --no-interaction update
#	php -f tests/PrepareForTest.php

daydigest:
	cd src &&  php -f abraflexi-daydigest.php && cd ..
weekdigest:
	cd src &&  php -f abraflexi-weekdigest.php && cd ..
monthdigest:
	cd src &&  php -f abraflexi-monthdigest.php && cd ..
yeardigest:
	cd src &&  php -f abraflexi-yeardigest.php && cd ..
alltimedigest:
	cd src &&  php -f abraflexi-alltimedigest.php && cd ..

match: incoming outcoming parujnew2old
#test: composer testrun

testrun:
	@echo '################################################### Default PHP '
	cd src &&  php -f abraflexi-daydigest.php && cd ..
	cd src &&  php -f abraflexi-weekdigest.php && cd ..
	cd src &&  php -f abraflexi-monthdigest.php && cd ..
	cd src &&  php -f abraflexi-yeardigest.php && cd ..
	cd src &&  php -f abraflexi-alltimedigest.php && cd ..


test74:
	@echo '################################################### PHP 7.4'
	cd src &&  php7.4 -f abraflexi-daydigest.php && cd ..
	cd src &&  php7.4 -f abraflexi-weekdigest.php && cd ..
	cd src &&  php7.4 -f abraflexi-monthdigest.php && cd ..
	cd src &&  php7.4 -f abraflexi-yeardigest.php && cd ..
	cd src &&  php7.4 -f abraflexi-alltimedigest.php && cd ..

test80:
	@echo '################################################### PHP 8.0'
	cd src &&  php8.0 -f abraflexi-daydigest.php && cd ..
	cd src &&  php8.0 -f abraflexi-weekdigest.php && cd ..
	cd src &&  php8.0 -f abraflexi-monthdigest.php && cd ..
	cd src &&  php8.0 -f abraflexi-yeardigest.php && cd ..
	cd src &&  php8.0 -f abraflexi-alltimedigest.php && cd ..



clean:
	rm -rf debian/abraflexi-digest 
	rm -rf debian/*.substvars debian/*.log debian/*.debhelper debian/files debian/debhelper-build-stamp
	rm -rf vendor composer.lock

deb:
	dch -i
	dpkg-buildpackage -A -us -uc

dimage:
	docker build -t vitexsoftware/abraflexi-digest .

dtest:
	docker-compose run --rm default install
        
drun: dimage
	docker run  -dit --name AbraFlexiDigest -p 2323:80 vitexsoftware/abraflexi-digest

vagrant: deb
	vagrant destroy -f
	mkdir -p deb
	debuild -us -uc
	mv ../abraflexi-digest_$(currentversion)_all.deb deb
	cd deb ; dpkg-scanpackages . /dev/null | gzip -9c > Packages.gz; cd ..
	vagrant up
	sensible-browser http://localhost:8080/multi-abraflexi-setup?login=demo\&password=demo

release:
	echo Release v$(nextversion)
	dch -v $(nextversion) `git log -1 --pretty=%B | head -n 1`
	debuild -i -us -uc -b
	git commit -a -m "Release v$(nextversion)"
	git tag -a $(nextversion) -m "version $(nextversion)"


.PHONY : install
	