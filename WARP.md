# WARP.md

This file provides guidance to WARP (warp.dev) when working with code in this repository.

## Project Overview

AbraFlexi-Digest is a PHP application that generates periodic digest reports from AbraFlexi accounting system. It creates HTML reports showing financial and operational summaries that can be saved to files or sent via email. The application supports multiple time periods: daily, weekly, monthly, yearly, and all-time.

## Development Commands

### Testing & Development
```bash
# Run all digest types (testrun)
make testrun

# Run specific digest period (must be run from src/ directory or use make)
make daydigest      # Daily digest
make weekdigest     # Weekly digest  
make monthdigest    # Monthly digest
make yeardigest     # Yearly digest
make alltimedigest  # All-time digest

# Run PHP unit tests
make tests
# or
vendor/bin/phpunit tests
```

### Code Quality
```bash
# Run PHP CS Fixer to fix coding standards
make cs
# or
vendor/bin/php-cs-fixer fix --config=.php-cs-fixer.dist.php --diff --verbose

# Run PHPStan static analysis
make static-code-analysis
# or
vendor/bin/phpstan analyse --configuration=phpstan-default.neon.dist --memory-limit=-1

# Generate PHPStan baseline
make static-code-analysis-baseline
```

### Dependencies
```bash
# Install dependencies
make vendor
# or
composer install

# Update dependencies
make composer
# or
composer update
```

### Debian Packaging
```bash
# Build Debian package
make deb

# Clean build artifacts
make clean
```

### MultiFlexi Validation
When updating `multiflexi/*.app.json` files, validate them with:
```bash
multiflexi-cli application validate-json --json multiflexi/[filename].app.json
```

All MultiFlexi JSON files must conform to: https://raw.githubusercontent.com/VitexSoftware/php-vitexsoftware-multiflexi-core/refs/heads/main/multiflexi.app.schema.json

## Architecture

### Core Structure

The application follows a modular digest system:

1. **Entry Points** (`src/abraflexi-*digest.php`): Five time-period scripts that initialize the digest generation
2. **Digestor** (`src/Digest/Digestor.php`): Main orchestrator that processes modules and generates output
3. **DigestModule** (`src/Digest/DigestModule.php`): Base class for all digest modules
4. **Modules** (`src/Digest/Modules/`): Individual reporting components

### Module System

Digest modules implement `DigestModuleInterface` and extend `DigestModule`. Each module:
- Takes a `DatePeriod` in constructor
- Implements `dig()` for HTML output
- Implements `digJson()` for JSON output  
- Implements `heading()` for display name
- Can filter data by `timeColumn` (date-based filtering)

**Universal Modules** (in `src/Digest/Modules/`): Run for all time periods
- Debtors, IncomingInvoices, IncomingPayments, NewCustomers
- OutcomingInvoices, OutcomingPayments, Reminds, WaitingIncome
- WaitingPayments, WithoutEmail, WithoutTel, etc.

**Period-Specific Modules**:
- `src/Digest/Modules/Monthly/` - e.g., DailyIncomeChart
- `src/Digest/Modules/Daily/` - (none currently)
- `src/Digest/Modules/AllTime/` - (none currently)

### Configuration

Configuration loaded from `.env` file (not tracked in git). Key variables:
- `ABRAFLEXI_URL`, `ABRAFLEXI_LOGIN`, `ABRAFLEXI_PASSWORD`, `ABRAFLEXI_COMPANY`
- `EASE_LOGGER` - logging output (syslog|mail|console)
- `DIGEST_MAILTO` - email recipient
- `DIGEST_FROM` - sender address
- `THEME` - CSS theme name
- `DIGEST_SAVETO` - output directory
- `SHOW_CONNECTION_FORM` - show web form for custom connection

### Dependencies & Namespacing

**Core Libraries**:
- `vitexsoftware/ease-*` - UI widgets and HTML generation
- `vitexsoftware/abraflexi-bricks` - AbraFlexi API integration
- PHP packages: intl (date formatting), pear/net_smtp (email)

**Namespaces**:
- `AbraFlexi\Digest` - Core digest classes
- `AbraFlexi\Digest\Modules` - Universal modules
- `AbraFlexi\Digest\Modules\{Daily|Weekly|Monthly|Yearly|AllTime}` - Period-specific
- `AbraFlexi\Digest\Outlook` - Outlook-compatible HTML components

### Important Patterns

**Running Scripts**: Due to relative path dependencies (`../vendor/autoload.php`, `../.env`), always run main scripts from their directory:
```bash
cd src/
php abraflexi-daydigest.php
```
Or use the Makefile targets. Paths are adjusted by `debian/rules` during packaging.

**Module Discovery**: Modules are auto-discovered using `\Ease\Functions::loadClassesInNamespace()`:
```php
$digestor->dig($period, array_merge(
    \Ease\Functions::loadClassesInNamespace('AbraFlexi\\Digest\\Modules'),
    \Ease\Functions::loadClassesInNamespace('AbraFlexi\\Digest\\Modules\\Daily')
));
```

**Date Filtering**: Modules set `$this->timeColumn` to filter AbraFlexi records by date:
```php
public function __construct(\DatePeriod $interval) {
    $this->timeColumn = 'datVyst';  // Issue date
    parent::__construct($interval);
}
```

## Coding Standards

- PHP 8.4+ (declared in copilot-instructions.md)
- PSR-12 coding standard
- Use `_()` functions for i18n (gettext)
- All docblocks required for functions/classes
- Type hints required for parameters and return types
- English for all code, comments, and messages
- Use meaningful variable names, avoid magic numbers/strings
- Always create/update PHPUnit tests when modifying classes
- Use `\Ease\TWB4\Widgets\FaIcon` instead of deprecated `\Ease\Html\I`
- Use `\Ease\Html\PTag` instead of deprecated `BR` tag

## Web Interface

`web/index.php` provides a form-based interface for testing digest generation with custom AbraFlexi connection parameters. See in action at: https://www.vitexsoftware.cz/abraflexi-digest/

## MultiFlexi Integration

The project includes 5 MultiFlexi applications (in `multiflexi/`):
- `daily_digest.multiflexi.app.json`
- `weekly_digest.multiflexi.app.json`  
- `monthly_digest.multiflexi.app.json`
- `yearly_digest.multiflexi.app.json`
- `alltime_digest.multiflexi.app.json`

These define the applications for the MultiFlexi platform, specifying environment variables, executables, and deployment instructions.

## Debian Packaging

- Package name: `abraflexi-digest`
- Installs commands: `abraflexi-daydigest`, `abraflexi-weekdigest`, etc.
- Cron jobs in `debian/*.cron.{daily,weekly,monthly}`
- MultiFlexi integration package: `multiflexi-abraflexi-digest`
