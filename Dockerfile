FROM debian:latest
MAINTAINER Vítězslav Dvořák <info@vitexsoftware.cz>
ENV DEBIAN_FRONTEND=noninteractive

RUN apt update
RUN apt-get update && apt-get install -my wget gnupg lsb-release

RUN echo "deb http://repo.vitexsoftware.cz $(lsb_release -sc) main" | tee /etc/apt/sources.list.d/vitexsoftware.list
RUN wget -O /etc/apt/trusted.gpg.d/vitexsoftware.gpg http://repo.vitexsoftware.cz/keyring.gpg
RUN apt update
RUN apt-get -y upgrade
RUN apt -y install apache2 libapache2-mod-php php-pear php-curl php-mbstring curl composer php-intl php-gettext locales-all unzip ssmtp
#RUN DEBIAN_FRONTEND=noninteractive apt-get -y install php-fpm php-pear php-curl php-mbstring curl lynx-cur composer php-intl php-gettext locales-all unzip ssmtp
    
RUN rm -f /var/www/html/index.html ; mkdir /var/www/input; mkdir /var/www/done; chown www-data:www-data /var/www/input/ ; chown www-data:www-data /var/www/done
COPY src/ /var/www/html/
COPY tests/*.json /var/www/
RUN ln -s /var/www/html/ /var/www/src
COPY i18n/ /var/www/i18n
RUN sed -i '/#ServerName/s/.*/UseCanonicalName on/'  /etc/apache2/sites-enabled/000-default.conf
COPY composer.json   /var/www
COPY debian/conf/mail.ini   /etc/php/7.0/conf.d/mail.ini
COPY debian/conf/ssmtp.conf /etc/ssmtp/ssmtp.conf

RUN composer install --no-dev --no-plugins --no-scripts --classmap-authoritative  -d /var/www/

ENV APACHE_RUN_USER www-data
ENV APACHE_RUN_GROUP www-data
ENV APACHE_LOG_DIR /var/log/apache2
EXPOSE 80
CMD ["/usr/sbin/apachectl","-DFOREGROUND"]
