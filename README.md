Digest for AbraFlexi
====================

![Digest for AbraFlexi](social-preview.svg?raw=true "Digest for AbraFlexi")

Periodical business digest for [AbraFlexi](https://www.abraflexi.eu) accounting system. Collects data via the [php-abraflexi](https://github.com/Spoje-NET/php-abraflexi) library, runs analytics modules from [vitexsoftware/digest-modules](https://github.com/VitexSoftware/DigestModules), and outputs Markdown, HTML, or PDF reports.

## Architecture

```
vitexsoftware/abraflexi-digest  (this package)
  src/Digest/Providers/AbraFlexiDataProvider.php   — AbraFlexi → neutral schema
  src/Digest/ModularDigestor.php                   — wires provider + modules + renderer
  src/Digest/Mailer.php                            — sends digest by email

vitexsoftware/digest-modules
  src/Modules/*.php                                — system-agnostic analytics modules

vitexsoftware/digest-renderer
  src/                                             — Markdown/HTML/PDF rendering
```

`AbraFlexiDataProvider` (namespace `AbraFlexi\Digest\Providers`) implements `DataProviderInterface` from `vitexsoftware/digest-modules`. It translates the neutral `FILTER_*` / `FIELD_*` constants into AbraFlexi WQL queries and normalizes raw AbraFlexi records (including `AbraFlexi\Relation` objects for `firma`, `mena`, `typDokl`) into the neutral field schema.

## Features

* **Extensible** — add custom modules from `vitexsoftware/digest-modules`
* **Multiple output formats** — Markdown, HTML, PDF (via DigestRenderer)
* **Email delivery** — Symfony Mailer integration
* **Skinnable** — custom CSS themes
* **Localized** — Czech and English gettext localizations

## Scripts

| File | Description |
|------|-------------|
| [abraflexi-daydigest.php](src/abraflexi-daydigest.php) | Generate digest for one day |
| [abraflexi-weekdigest.php](src/abraflexi-weekdigest.php) | Generate digest for one week |
| [abraflexi-monthdigest.php](src/abraflexi-monthdigest.php) | Generate digest for one month |
| [abraflexi-yeardigest.php](src/abraflexi-yeardigest.php) | Generate digest for one year |
| [abraflexi-alltimedigest.php](src/abraflexi-alltimedigest.php) | Generate digest for all time |

## Installation

```bash
composer require vitexsoftware/abraflexi-digest
```

Or via Debian package:

```shell
wget -qO- https://repo.vitexsoftware.com/KEY.gpg | sudo tee /etc/apt/trusted.gpg.d/vitexsoftware.gpg
echo "deb [signed-by=/etc/apt/trusted.gpg.d/vitexsoftware.gpg] https://repo.vitexsoftware.com $(lsb_release -sc) main" | sudo tee /etc/apt/sources.list.d/vitexsoftware.list
sudo apt update
sudo apt install abraflexi-digest
```

After installation:

* `abraflexi-daydigest` — digest for one day
* `abraflexi-weekdigest` — digest for one week
* `abraflexi-monthdigest` — digest for one month
* `abraflexi-yeardigest` — digest for one year
* `abraflexi-alltimedigest` — digest for all time

## Configuration

Create `/etc/abraflexi/.env` (or pass path as first CLI argument):

```env
ABRAFLEXI_URL="https://your-abraflexi.example.com"
ABRAFLEXI_LOGIN="username"
ABRAFLEXI_PASSWORD="password"
ABRAFLEXI_COMPANY="your-company-db"

EASE_LOGGER="syslog|mail|console"
DIGEST_MAILTO="info@yourdomain.net"
DIGEST_FROM="noreply@vitexsoftware.cz"
THEME="happy"
DIGEST_SAVETO="/var/tmp/"
```

## Programmatic usage

```php
<?php
use AbraFlexi\Digest\ModularDigestor;
use AbraFlexi\Digest\Providers\AbraFlexiDataProvider;
use VitexSoftware\DigestModules\Core\ModuleRunner;
use VitexSoftware\DigestModules\Modules\Debtors;
use VitexSoftware\DigestModules\Modules\OutcomingInvoices;

$provider = new AbraFlexiDataProvider();

$runner = new ModuleRunner($provider);
$runner->addModule('outcoming_invoices', new OutcomingInvoices());
$runner->addModule('debtors', new Debtors());

$period = new \DatePeriod(
    new \DateTime('first day of last month'),
    new \DateInterval('P1M'),
    new \DateTime('first day of this month'),
);

$result = $runner->run($period);
```

## Available modules

Modules live in `vitexsoftware/digest-modules`. All are supported by `AbraFlexiDataProvider`:

* `OutcomingInvoices` — issued invoices in the period
* `IncomingInvoices` — received invoices in the period
* `IncomingPayments` — bank receipts in the period
* `OutcomingPayments` — bank outflows in the period
* `Debtors` — all overdue unpaid receivables
* `UnmatchedInvoices` — issued invoices not matched to a payment
* `UnmatchedPayments` — bank movements not matched to an invoice
* `WaitingIncome` — proforma invoices awaiting settlement
* `WaitingPayments` — invoices awaiting payment
* `NewCustomers` — contacts created in the period
* `Reminds` — invoices with pending payment reminders
* `BestSellers` — top products/services sold in the period
* `WithoutEmail` — contacts missing an email address
* `WithoutTel` — contacts missing a phone number
* `AllTime\PurchasePriceLowerThanSales` — products sold below purchase price

## See also

* [vitexsoftware/digest-modules](https://github.com/VitexSoftware/DigestModules) — analytics modules
* [vitexsoftware/digest-renderer](https://github.com/VitexSoftware/DigestRenderer) — rendering engine
* [abraflexi-reminder](https://github.com/VitexSoftware/abraflexi-reminder)
* [php-abraflexi-matcher](https://github.com/VitexSoftware/php-abraflexi-matcher)

## MultiFlexi

Digest for AbraFlexi is ready to run as a [MultiFlexi](https://multiflexi.eu) application.

[![MultiFlexi App](https://github.com/VitexSoftware/MultiFlexi/blob/main/doc/multiflexi-app.svg)](https://www.multiflexi.eu/apps.php)

## Thanks

This software would not be created without the support of:

[![Spoje.Net](spojenet.gif?raw=true "Spoje.Net s.r.o.")](https://spoje.net/)

## License

MIT
