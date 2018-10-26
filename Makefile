all: fresh build install

composer:
	composer update

fresh:
	echo fresh

install: 
	echo install
	
build:
	echo build

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
test: composer testphp

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

testphp: test70


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


.PHONY : install
	