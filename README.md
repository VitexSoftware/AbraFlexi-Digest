Digest for AbraFlexi  
====================

![Digest for AbraFlexi](social-preview.svg?raw=true "Digest for AbraFlexi")

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
* add config file path as first parameter

```env
EASE_LOGGER="syslog|mail|console"         - how to log progress and results
EASE_MAILTO="info@yourdomain.net"         - send digest mail
DIGEST_FROM="noreply@vitexsoftware.cz"    - digest mail sender address 
THEME="happy",                            - additional css
DIGEST_SAVETO="/var/tmp/"                        - save html digest to 
SHOW_CONNECTION_FORM="true"               - show custom server connection form (web only)
DIGEST_CHECK_SUPPLIER_CONTACT=false       - Do not notify if the supplier does not have contact details 
```

Web interface
-------------

We Also provide form to test Digest modules. Availble as [index.php](src/index.php)

![Example](form.png?raw=true "Week Digest")

See in action: https://www.vitexsoftware.cz/abraflexi-digest/

Modules
-------

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

![Day Icon >](abraflexi-daydigest.svg?raw=true)

Daily Modules
-------------

none yet

![Week Icon >](abraflexi-weekdigest.svg?raw=true)

Weekly Modules
--------------

none yet

![Month Icon >](abraflexi-monthdigest.svg?raw=true)

Monthly Modules
---------------

 Applied once per month

* DailyIncomeChart.php

![Average Income](https://raw.githubusercontent.com/VitexSoftware/AbraFlexi-Digest/master/monthly-average-income-chart.png "Week Digest")

![Year Icon >](abraflexi-yeardigest.svg?raw=true)

Yearly modules
--------------

none yet

Alltime modules
---------------

none yet

See also
--------

* https://github.com/VitexSoftware/abraflexi-reminder
* https://github.com/VitexSoftware/php-abraflexi-matcher
* https://github.com/VitexSoftware/AbraFlexi-email-importer
* https://github.com/VitexSoftware/php-abraflexi-mailer
* https://github.com/VitexSoftware/AbraFlexi-Tools

Thanks
----------

This software would not be created without the support of:

[![Spoje.Net](spojenet.gif?raw=true "Spoje.Net s.r.o.")](https://spoje.net/)


MultiFlexi
----------

Digest for AbraFlexi is ready for run as [MultiFlexi](https://multiflexi.eu) application.
See the full list of ready-to-run applications within the MultiFlexi platform on the [application list page](https://www.multiflexi.eu/apps.php).

[![MultiFlexi App](https://github.com/VitexSoftware/MultiFlexi/blob/main/doc/multiflexi-app.svg)](https://www.multiflexi.eu/apps.php)
