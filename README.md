![Package Logo](https://raw.githubusercontent.com/VitexSoftware/FlexiBee-Digest/master/package-logo.png "Project Logo")

FlexiBee Digest mail generator
==============================

There are four scripts:

[flexibee-daydigest.php](src/flexibee-daydigest.php)     - Generate FlexiBee digest for one day
[flexibee-weekdigest.php](src/flexibee-weekdigest.php)   - Generate FlexiBee digest for one week
[flexibee-monthdigest.php](src/flexibee-monthdigest.php) - Generate FlexiBee digest for one month
[flexibee-yeardigest.php](src/flexibee-yeardigest.php)   - Generate FlexiBee digest for one year


Debian/Ubuntu
-------------

Packages are availble. Please use repo :

```        
    wget -O - http://v.s.cz/info@vitexsoftware.cz.gpg.key|sudo apt-key add -
    echo deb http://v.s.cz/ stable main > /etc/apt/sources.list.d/ease.list
    apt update
    apt install php-flexibee-digest
```

Po instalaci balíku jsou v systému k dispozici dva nové příkazy:

  * **flexibee-daydigest**   - Generate and send FlexiBee digest for one day
  * **flexibee-monthdigest** - Generate and send FlexiBee digest for one week
  * **flexibee-weekdigest**  - Generate and send FlexiBee digest for one month
  * **flexibee-yeardigest**  - Generate and send FlexiBee digest for one year


Závislosti
----------

Tento nástroj ke svojí funkci využívá následující knihovny:

 * [**EasePHP Framework**](https://github.com/VitexSoftware/EaseFramework) - pomocné funkce např. logování
 * [**FlexiPeeHP**](https://github.com/Spoje-NET/FlexiPeeHP)        - komunikace s [FlexiBee](https://flexibee.eu/)
 * [**FlexiPeeHP Bricks**](https://github.com/VitexSoftware/FlexiPeeHP-Bricks) - používají se třídy Zákazníka, Upomínky a Upomínače

Konfigurace
-----------

 * [/etc/flexibee/client.json](client.json)   - společná konfigurace připojení k FlexiBee serveru
 * [/etc/flexibee/digest.json](digest.json) - nastavení párovače:

```
    "EASE_MAILTO": "info@yourdomain.net",         - mail digest recipient
    "EASE_LOGGER": "syslog|mail|console",         - how to log progress and results
```


See also
--------

  * https://github.com/VitexSoftware/php-flexibee-reminder
  * https://github.com/VitexSoftware/php-flexibee-matcher

Poděkování
----------

Tento software by nevznikl pez podpory:

[ ![Spoje.Net](https://raw.githubusercontent.com/VitexSoftware/php-flexibee-digest/master/doc/spojenet.gif "Spoje.Net s.r.o.") ](https://spoje.net/)

