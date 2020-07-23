repoversion=$(shell LANG=C aptitude show flexibee-digest | grep Version: | awk '{print $$2}')
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
	cd src &&  php -f flexibee-daydigest.php && cd ..
weekdigest:
	cd src &&  php -f flexibee-weekdigest.php && cd ..
monthdigest:
	cd src &&  php -f flexibee-monthdigest.php && cd ..
yeardigest:
	cd src &&  php -f flexibee-yeardigest.php && cd ..
alltimedigest:
	cd src &&  php -f flexibee-alltimedigest.php && cd ..

match: incoming outcoming parujnew2old
#test: composer testrun

testrun:
	@echo '################################################### Default PHP '
	cd src &&  php -f flexibee-daydigest.php && cd ..
	cd src &&  php -f flexibee-weekdigest.php && cd ..
	cd src &&  php -f flexibee-monthdigest.php && cd ..
	cd src &&  php -f flexibee-yeardigest.php && cd ..
	cd src &&  php -f flexibee-alltimedigest.php && cd ..


test56:
	@echo '################################################### PHP 5.6'
	cd src &&  php5.6 -f flexibee-daydigest.php && cd ..
	cd src &&  php5.6 -f flexibee-weekdigest.php && cd ..
	cd src &&  php5.6 -f flexibee-monthdigest.php && cd ..
	cd src &&  php5.6 -f flexibee-yeardigest.php && cd ..
	cd src &&  php5.6 -f flexibee-alltimedigest.php && cd ..
	
test70:
	@echo '################################################### PHP 7.0'
	cd src &&  php7.0 -f flexibee-daydigest.php && cd ..
	cd src &&  php7.0 -f flexibee-weekdigest.php && cd ..
	cd src &&  php7.0 -f flexibee-monthdigest.php && cd ..
	cd src &&  php7.0 -f flexibee-yeardigest.php && cd ..
	cd src &&  php7.0 -f flexibee-alltimedigest.php && cd ..

test71:
	@echo '################################################### PHP 7.1'
	cd src &&  php7.1 -f flexibee-daydigest.php && cd ..
	cd src &&  php7.1 -f flexibee-weekdigest.php && cd ..
	cd src &&  php7.1 -f flexibee-monthdigest.php && cd ..
	cd src &&  php7.1 -f flexibee-yeardigest.php && cd ..
	cd src &&  php7.1 -f flexibee-alltimedigest.php && cd ..

test72:
	@echo '################################################### PHP 7.2'
	cd src &&  php7.2 -f flexibee-daydigest.php && cd ..
	cd src &&  php7.2 -f flexibee-weekdigest.php && cd ..
	cd src &&  php7.2 -f flexibee-monthdigest.php && cd ..
	cd src &&  php7.2 -f flexibee-yeardigest.php && cd ..
	cd src &&  php7.2 -f flexibee-alltimedigest.php && cd ..

test73:
	@echo '################################################### PHP 7.3'
	cd src &&  php7.3 -f flexibee-daydigest.php && cd ..
	cd src &&  php7.3 -f flexibee-weekdigest.php && cd ..
	cd src &&  php7.3 -f flexibee-monthdigest.php && cd ..
	cd src &&  php7.3 -f flexibee-yeardigest.php && cd ..
	cd src &&  php7.3 -f flexibee-alltimedigest.php && cd ..

clean:
	rm -rf debian/php-flexibee-digest 
	rm -rf debian/*.substvars debian/*.log debian/*.debhelper debian/files debian/debhelper-build-stamp
	rm -rf vendor composer.lock

deb:
	dch -i
	dpkg-buildpackage -A -us -uc

dimage:
	docker build -t vitexsoftware/flexibee-digest .

dtest:
	docker-compose run --rm default install
        
drun: dimage
	docker run  -dit --name FlexiBeeDigest -p 2323:80 vitexsoftware/flexibee-digest

release:
	echo Release v$(nextversion)
	dch -v $(nextversion) `git log -1 --pretty=%B | head -n 1`
	debuild -i -us -uc -b
	git commit -a -m "Release v$(nextversion)"
	git tag -a $(nextversion) -m "version $(nextversion)"


.PHONY : install
	