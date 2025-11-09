<?php

declare(strict_types=1);

/**
 * Migration script for AbraFlexi-Digest modularization
 *
 * This script demonstrates how to migrate from the old structure to the new modular approach
 */

namespace AbraFlexi\Digest;

/**
 * Migration helper for updating AbraFlexi-Digest to use modular structure
 */
class DigestMigrator
{
    /**
     * Create updated composer.json for AbraFlexi-Digest
     */
    public static function updateComposerJson(): array
    {
        return [
            'require' => [
                'php' => '>=8.1',
                'vitexsoftware/digest-modules' => '^1.0',
                'vitexsoftware/digest-renderer' => '^1.0',
                // Keep existing dependencies
                'vitexsoftware/ease-core' => '*',
                'vitexsoftware/abraflexi' => '*',
            ],
            'repositories' => [
                [
                    'type' => 'path',
                    'url' => '../DigestModules',
                ],
                [
                    'type' => 'path',
                    'url' => '../DigestRenderer',
                ],
            ],
        ];
    }

    /**
     * Example migration of a digest script
     */
    public static function migrateDigestScript(): string
    {
        return '<?php

declare(strict_types=1);

/**
 * Migrated AbraFlexi Digest Script
 */

require_once __DIR__ . "/vendor/autoload.php";

use AbraFlexi\Digest\ModularDigestor;

// Create digest with new modular structure
$digestor = ModularDigestor::createWithModules(
    "Monthly Digest Report",
    [
        "outcoming_invoices", 
        "debtors",
        // Add more modules as needed
    ]
);

// Optional: Set custom styling
$digestor->setCustomCss("
    .digest-header { background: #your-brand-color; }
    .company-logo { max-height: 60px; }
");

// Create analysis period
$period = new DatePeriod(
    new DateTime("first day of last month"),
    new DateInterval("P1M"),
    new DateTime("first day of this month")
);

// Generate and save HTML report
$digestor->saveToFile($period, "monthly_digest.html");

// Send by email
$emailTo = $_ENV["DIGEST_EMAIL"] ?? "admin@company.com";
$digestor->sendByEmail($period, $emailTo);

// Optionally save raw JSON data for further processing
$digestor->saveJsonToFile($period, "monthly_digest_data.json");

echo "Digest generation completed!\n";
';
    }

    /**
     * Create migration guide
     */
    public static function createMigrationGuide(): string
    {
        return '# AbraFlexi-Digest Migration Guide

## Overview

The AbraFlexi-Digest has been refactored to use a modular architecture with two separate libraries:

1. **DigestModules** - Data collection and analysis (returns JSON)
2. **DigestRenderer** - HTML/email output generation

## Benefits

- **Reusable**: Modules can be used with different accounting systems (Pohoda, Money S3, etc.)
- **Flexible**: Data can be exported as JSON for custom processing
- **Maintainable**: Clear separation between data collection and presentation
- **Testable**: Each module can be unit tested independently

## Migration Steps

### 1. Update dependencies

Add to your `composer.json`:

```json
{
    "require": {
        "vitexsoftware/digest-modules": "^1.0",
        "vitexsoftware/digest-renderer": "^1.0"
    }
}
```

### 2. Replace old Digestor usage

**Before:**
```php
$digestor = new \\AbraFlexi\\Digest\\Digestor($subject);
$digestor->processModules($modules, $probePeriod);
```

**After:**
```php
$digestor = new \\AbraFlexi\\Digest\\ModularDigestor($subject);
$digestor->addModule("outcoming_invoices")
         ->addModule("debtors");
$html = $digestor->generate($probePeriod);
```

### 3. JSON Data Access

Get raw data for custom processing:

```php
$jsonData = $digestor->getJsonData($period);
$digestor->saveJsonToFile($period, "data.json");
```
';
```
';
    }
}

    }
}
    }
}