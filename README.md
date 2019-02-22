![Package Logo](https://raw.githubusercontent.com/VitexSoftware/FlexiBee-Digest/master/package-logo.png "Project Logo")

FlexiBee Digest generator
=========================

Features:

  * **Extensible**             - you can write your own Digest modules
  * **Static page export**     - Digest is saved to file
  * **Send by eMail**          - Digest is send by email   
  * **Skinable**               - You can choose or add custom css
  * **Localised**              - Czech and English gettext localization   

There are four scripts:

|  File                                                     | Description                            |
|-----------------------------------------------------------|----------------------------------------| 
|  [flexibee-daydigest.php](src/flexibee-daydigest.php)     | Generate FlexiBee digest for one day   |
|  [flexibee-weekdigest.php](src/flexibee-weekdigest.php)   | Generate FlexiBee digest for one week  |
|  [flexibee-monthdigest.php](src/flexibee-monthdigest.php) | Generate FlexiBee digest for one month |
|  [flexibee-yeardigest.php](src/flexibee-yeardigest.php)   | Generate FlexiBee digest for one year  |
|  [flexibee-alltimedigest.php](src/flexibee-yeardigest.php)| Generate FlexiBee digest for all time  |


![Example](https://raw.githubusercontent.com/VitexSoftware/FlexiBee-Digest/master/weekdigest.png "Week Digest")

Debian/Ubuntu
-------------

Packages are availble. Please use repo :

```        
    wget -O - http://v.s.cz/info@vitexsoftware.cz.gpg.key|sudo apt-key add -
    echo deb http://v.s.cz/ stable main | sudo tee /etc/apt/sources.list.d/vitexsoftware.list 
    sudo apt update
    sudo apt install flexibee-digest
```

After package installation you can use this new commands:

  * **flexibee-daydigest**      - Generate FlexiBee digest for one day
  * **flexibee-monthdigest**    - Generate FlexiBee digest for one week
  * **flexibee-weekdigest**     - Generate FlexiBee digest for one month
  * **flexibee-yeardigest**     - Generate FlexiBee digest for one year
  * **flexibee-alltimedigest**  - Generate FlexiBee digest for all time


Konfigurace
-----------

 * [/etc/flexibee/client.json](client.json)   - společná konfigurace připojení k FlexiBee serveru
 * [/etc/flexibee/digest.json](digest.json) - nastavení párovače:

```
    "EASE_LOGGER": "syslog|mail|console",         - how to log progress and results
    "EASE_MAILTO": "info@yourdomain.net",         - send digest mail
    "DIGEST_FROM": "noreply@vitexsoftware.cz",    - digest mail sender address 
    "THEME":  "happy",                            - additional css
    "SAVETO": "/var/tmp/"                         - save html digest to 
```

Web interface 
-------------

We Also provide form to test Digest modules. Availble as [index.php](src/index.php)

![Example](https://raw.githubusercontent.com/VitexSoftware/FlexiBee-Digest/master/doc/CustomDigestForm.png "Week Digest")


Modules
=======

Digest is generated using modules located in [src/modules](src/modules)

This Module add Company logo to Digest:

```php
class Logo extends \FlexiPeeHP\Digest\DigestModule implements \FlexiPeeHP\Digest\DigestModuleInterface
{

    public function dig()
    {
        $configurator = new \FlexiPeeHP\Nastaveni();
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

![Average Income](https://raw.githubusercontent.com/VitexSoftware/FlexiBee-Digest/master/monthly-average-income-chart.png "Week Digest")

Yearly modules
--------------

none yet

Alltime modules
---------------

none yet

Dependencies
------------

Tento nástroj ke svojí funkci využívá následující knihovny:

 * [**EasePHP Framework**](https://github.com/VitexSoftware/EaseFramework) - pomocné funkce např. logování
 * [**FlexiPeeHP**](https://github.com/Spoje-NET/FlexiPeeHP)        - komunikace s [FlexiBee](https://flexibee.eu/)
 * [**FlexiPeeHP Bricks**](https://github.com/VitexSoftware/FlexiPeeHP-Bricks) - Company Logo image widget


See also
--------

  * https://github.com/VitexSoftware/php-flexibee-reminder
  * https://github.com/VitexSoftware/php-flexibee-matcher

Poděkování
----------

Tento software by nevznikl pez podpory:

[ ![Spoje.Net](https://raw.githubusercontent.com/VitexSoftware/FlexiBee-Digest/master/spojenet.gif "Spoje.Net s.r.o.") ](https://spoje.net/)

