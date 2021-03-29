![Package Logo](package-logo.png?raw=true "Project Logo")

AbraFlexi Digest generator
==========================

Features:

  * **Extensible**             - you can write your own Digest modules
  * **Static page export**     - Digest is saved to file
  * **Send by eMail**          - Digest is send by email   
  * **Skinable**               - You can choose or add custom css
  * **Localised**              - Czech and English gettext localization   

There are four scripts:

|  File                                                       | Description                             |
|-------------------------------------------------------------|-----------------------------------------| 
|  [abraflexi-daydigest.php](src/abraflexi-daydigest.php)     | Generate AbraFlexi digest for one day   |
|  [abraflexi-weekdigest.php](src/abraflexi-weekdigest.php)   | Generate AbraFlexi digest for one week  |
|  [abraflexi-monthdigest.php](src/abraflexi-monthdigest.php) | Generate AbraFlexi digest for one month |
|  [abraflexi-yeardigest.php](src/abraflexi-yeardigest.php)   | Generate AbraFlexi digest for one year  |
|  [abraflexi-alltimedigest.php](src/abraflexi-yeardigest.php)| Generate AbraFlexi digest for all time  |


![Example](weekdigest.png?raw=true "Week Digest")

Debian/Ubuntu
-------------

Packages are availble. Please use repo :

```shell
sudo apt install lsb-release wget
echo "deb http://repo.vitexsoftware.cz $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/vitexsoftware.list
sudo wget -O /etc/apt/trusted.gpg.d/vitexsoftware.gpg http://repo.vitexsoftware.cz/keyring.gpg
sudo apt update
sudo apt install abraflexi-digest
```

After package installation you can use this new commands:

  * **abraflexi-daydigest**      - Generate AbraFlexi digest for one day
  * **abraflexi-monthdigest**    - Generate AbraFlexi digest for one week
  * **abraflexi-weekdigest**     - Generate AbraFlexi digest for one month
  * **abraflexi-yeardigest**     - Generate AbraFlexi digest for one year
  * **abraflexi-alltimedigest**  - Generate AbraFlexi digest for all time


Configuration
-------------

 * [/etc/abraflexi/.env](.env)   - Shared configuration file to override default Environment settings
 
```
    "EASE_LOGGER": "syslog|mail|console",         - how to log progress and results
    "EASE_MAILTO": "info@yourdomain.net",         - send digest mail
    "DIGEST_FROM": "noreply@vitexsoftware.cz",    - digest mail sender address 
    "THEME":  "happy",                            - additional css
    "SAVETO": "/var/tmp/"                         - save html digest to 
    "SHOW_CONNECTION_FORM": "true"                - show custom server connection form
```

Web interface 
-------------

We Also provide form to test Digest modules. Availble as [index.php](src/index.php)

![Example](form.png?raw=true "Week Digest")

See in action: https://www.vitexsoftware.cz/abraflexi-digest/

Modules
=======

Digest is generated using modules located in [src/modules](src/modules)

This Module add Company logo to Digest:

```php
class Logo extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface
{

    public function dig()
    {
        $configurator = new \AbraFlexi\Nastaveni();
        $logoInfo     = $configurator->getFlexiData('1/logo');
        if (is_array($logoInfo) && isset($logoInfo[0])) {
            $this->addItem(new \Ease\Html\ImgTag('data:'.$logoInfo[0]['contentType'].';'.$logoInfo[0]['content@encoding'].','.$logoInfo[0]['content'],
                $logoInfo[0]['nazSoub']));
        }
    }

    public function heading()
    {
        return _('Company Logo')';
    }
}
```

Universal Modules
-----------------

Applied in every case

* Debtors.php  
* IncomingInvoices.php  
* IncomingPayments.php  
* NewCustomers.php  
* OutcomingInvoices.php  
* OutcomingPayments.php  
* Reminds.php  
* WaitingIncome.php  
* WaitingPayments.php  
* WithoutEmail.php  
* WithoutTel.php


Daily Modules
-------------

none yet

Weekly Modules
--------------

none yet

Monthly Modules
---------------

 Applied once per month

 * DailyIncomeChart.php

![Average Income](https://raw.githubusercontent.com/VitexSoftware/AbraFlexi-Digest/master/monthly-average-income-chart.png "Week Digest")

Yearly modules
--------------

none yet

Alltime modules
---------------

none yet

Dependencies
------------

Powered by:

 * [**EasePHP Framework**](https://github.com/VitexSoftware/php-ease-core) - pomocné funkce např. logování
 * [**AbraFlexi**](https://github.com/Spoje-NET/php-abraflexi)        - komunikace s [AbraFlexi](https://abraflexi.eu/)
 * [**AbraFlexi Bricks**](https://github.com/VitexSoftware/php-abraflexi-bricks) - Company Logo image widget


See also
--------

  * https://github.com/VitexSoftware/abraflexi-reminder
  * https://github.com/VitexSoftware/php-abraflexi-matcher
  * https://github.com/VitexSoftware/AbraFlexi-email-importer
  * https://github.com/VitexSoftware/php-abraflexi-mailer
  * https://github.com/VitexSoftware/AbraFlexi-Tools

Poděkování
----------

Tento software by nevznikl pez podpory:

[ ![Spoje.Net](spojenet.gif?raw=true "Spoje.Net s.r.o.") ](https://spoje.net/)

