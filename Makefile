# vim: set tabstop=8 softtabstop=8 noexpandtab:
.PHONY: help
help: ## Displays this list of targets with descriptions
	@grep -E '^[a-zA-Z0-9_-]+:.*?## .*$$' $(MAKEFILE_LIST) | sort | awk 'BEGIN {FS = ":.*?## "}; {printf "\033[32m%-30s\033[0m %s\n", $$1, $$2}'

.PHONY: static-code-analysis
static-code-analysis: vendor ## Runs a static code analysis with phpstan/phpstan
	vendor/bin/phpstan analyse --configuration=phpstan-default.neon.dist --memory-limit=-1

.PHONY: static-code-analysis-baseline
static-code-analysis-baseline: check-symfony vendor ## Generates a baseline for static code analysis with phpstan/phpstan
	vendor/bin/phpstan analyze --configuration=phpstan-default.neon.dist --generate-baseline=phpstan-default-baseline.neon --memory-limit=-1

.PHONY: tests
tests: vendor
	vendor/bin/phpunit tests

.PHONY: vendor
vendor: composer.json composer.lock ## Installs composer dependencies
	composer install

.PHONY: cs
cs: ## Update Coding Standards
	vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --diff --verbose


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
	

.PHONY: validate-multiflexi-app
validate-multiflexi-app: ## Validates the multiflexi JSON
	@if [ -d multiflexi ]; then \
		for file in multiflexi/*.multiflexi.app.json; do \
			if [ -f "$$file" ]; then \
				echo "Validating $$file"; \
				multiflexi-cli app validate-json --file="$$file"; \
			fi; \
		done; \
	else \
		echo "No multiflexi directory found"; \
	fi
