---
description: AbraFlexi-Digest - analytics and reporting tool for AbraFlexi accounting system
applyTo: '**'
---

# AbraFlexi-Digest - Copilot Instructions

## Project Overview
AbraFlexi-Digest is a **comprehensive analytics solution** for AbraFlexi accounting system:
- **Legacy Application**: Established analytics tool for AbraFlexi business data
- **Modern Architecture**: Uses proven VitexSoftware patterns and libraries
- **MultiFlexi Integration**: Part of the MultiFlexi ecosystem for business applications
- **Production Ready**: Debian packaged with web interface and CLI tools
- **JSON Output**: Structured data format for further processing and visualization

## 🏗️ System Architecture
This is a **complete analytics application** built on VitexSoftware foundation:

```
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│  AbraFlexi API  │ ◄──┤ AbraFlexiDataProv│ ◄──┤ Analytics Engine│
│                 │    │                  │    │                 │
│ • REST/JSON API │    │ • Connection Mgmt│    │ • Data Analysis │
│ • Business Data │    │ • Data Adapter   │    │ • Report Gen    │
└─────────────────┘    └──────────────────┘    └─────────────────┘
                                                        │
                                                        ▼
┌─────────────────┐    ┌──────────────────┐    ┌─────────────────┐
│    Web Reports  │ ◄──┤ EaseBootstrap UI │ ◄──┤ AbraFlexi-Digest│
│                 │    │                  │    │                 │
│ • HTML Interface│    │ • Theme System   │    │ • CLI Interface │
│ • Bootstrap UI  │    │ • Responsive     │    │ • MultiFlexi    │
└─────────────────┘    └──────────────────┘    └─────────────────┘
```

## 📋 Development Standards

### Core Coding Guidelines
- **PHP 8.4+**: Use modern PHP features and strict types: `declare(strict_types=1);`
- **PSR-12**: Follow PHP-FIG coding standards for consistency
- **Type Safety**: Include type hints for all parameters and return types
- **Documentation**: PHPDoc blocks for all public methods and classes
- **Testing**: PHPUnit tests for all new functionality
- **Internationalization**: Use `_()` functions for translatable strings

### VitexSoftware Patterns
- **EasePHP Framework**: Built on php-vitexsoftware-ease-core foundation
- **AbraFlexi Integration**: Uses php-spojenet-abraflexi for API communication
- **Bootstrap UI**: EaseBootstrap4/5 for modern responsive interfaces
- **MultiFlexi Compatibility**: Follows MultiFlexi application patterns
- **Debian Packaging**: Professional distribution with proper dependencies

## 🔧 Application Structure

### Key Components
- **Web Interface** (`web/`): Bootstrap-based web UI for interactive analytics
- **CLI Tools** (`src/`): Command-line interface for automated reporting
- **MultiFlexi Config** (`multiflexi/`): Application definition and configuration
- **Analytics Modules**: Built-in analysis functions for business data
- **Report Generators**: HTML and JSON output formatters

### Core Libraries Used
- **php-vitexsoftware-ease-core**: Foundation framework for all operations
- **php-spojenet-abraflexi**: AbraFlexi API communication library  
- **php-vitexsoftware-ease-bootstrap4/5**: UI components and themes
- **php-vitexsoftware-multiflexi-core**: MultiFlexi ecosystem integration

### Data Flow Architecture
```php
AbraFlexi API → DataProvider → Analytics Engine → Report Generator → Output (HTML/JSON)
```

## 💻 Development Environment

### Running the Application
When developing or testing, always use proper entry points with correct working directories:

**Web Interface:**
```bash
cd web/
php index.php
```

**CLI Tools:**
```bash
cd src/
php abraflexi-daydigest.php
```

**Important**: The application uses relative paths intentionally (`../vendor/autoload.php`, `../.env`) which are resolved during Debian packaging via sed commands in `debian/rules` for production deployment.

### Code Quality Requirements
- **Syntax Validation**: After every PHP file edit, run `php -l filename.php` for syntax checking
- **Error Handling**: Implement comprehensive try-catch blocks with meaningful error messages
- **JSON Output**: Support `--format json` option for all CLI commands and operations
- **Security**: Never expose sensitive information in output or logs
- **Performance**: Optimize database queries and API calls for production use

## 📊 Analytics Patterns

### Standard Output Format
All analytics should return structured data following this pattern:

```json
{
    "module": "analytics_identifier",
    "heading": "Human Readable Title",
    "summary": {
        "total_amount": 125000.50,
        "currency": "CZK",
        "count": 45,
        "processing_time": 0.234
    },
    "details": [
        {
            "item": "Detail item",
            "value": 1234.56,
            "metadata": {"additional": "data"}
        }
    ],
    "metadata": {
        "generated_at": "2024-12-23T10:30:45+01:00",
        "provider": "AbraFlexi",
        "system_version": "2023.1"
    }
}
```

### AbraFlexi Integration Patterns
```php
<?php declare(strict_types=1);

// Standard AbraFlexi connection pattern
use Spojenet\AbraFlexi\RO;

class AbraFlexiAnalytics extends RO
{
    protected string $evidence = 'vydana-faktura';
    
    public function getInvoiceAnalytics(): array
    {
        try {
            $invoices = $this->getColumnsFromAbraFlexi(['kod', 'sumCelkem', 'datVyst']);
            return $this->processInvoiceData($invoices);
        } catch (\Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}
```

## 🌐 MultiFlexi Integration

### Schema Compliance
All MultiFlexi files must conform to official schemas:

- **Application Config** (`multiflexi/*.app.json`): 
  https://raw.githubusercontent.com/VitexSoftware/php-vitexsoftware-multiflexi-core/refs/heads/main/multiflexi.app.schema.json

- **Report Output** (generated reports):
  https://raw.githubusercontent.com/VitexSoftware/php-vitexsoftware-multiflexi-core/refs/heads/main/multiflexi.report.schema.json

### Application Definition Pattern
```json
{
    "name": "AbraFlexi-Digest",
    "description": "Analytics and reporting for AbraFlexi",
    "version": "1.0.0",
    "vendor": "vitexsoftware",
    "homepage": "https://github.com/VitexSoftware/AbraFlexi-Digest",
    "requirements": {
        "php": ">=8.4",
        "abraflexi": ">=2023.1"
    }
}
```

## 🎨 UI Development Guidelines

### Bootstrap Theme Usage
- **EaseBootstrap4/5**: Use VitexSoftware's Bootstrap extensions
- **Responsive Design**: Mobile-first approach for all interfaces
- **Component Consistency**: Use established UI patterns across the application
- **Accessibility**: Implement ARIA labels and semantic HTML

### Web Interface Patterns
```php
<?php
use Ease\Html\H1Tag;
use Ease\TWB4\WebPage;

$webPage = new WebPage(_('AbraFlexi Analytics'));
$webPage->addItem(new H1Tag(_('Business Reports')));
$webPage->addJavaScript('analytics.js');
```

## 🔍 Testing and Quality Assurance

### Unit Testing Requirements
- **PHPUnit Integration**: All new classes require corresponding test files
- **Mock AbraFlexi**: Use mocks for AbraFlexi API during testing
- **Coverage Goals**: Aim for comprehensive test coverage
- **Integration Tests**: Test MultiFlexi compatibility

### Test Structure Pattern
```php
<?php declare(strict_types=1);

use PHPUnit\Framework\TestCase;

class AnalyticsModuleTest extends TestCase
{
    protected function setUp(): void
    {
        // Setup test environment
    }
    
    public function testAnalyticsGeneration(): void
    {
        // Test analytics functionality
        $this->assertIsArray($result);
        $this->assertArrayHasKey('summary', $result);
    }
}
```

## ⚠️ Important Notes for Copilot

1. **Legacy Compatibility**: This is an established application with existing patterns - follow them
2. **VitexSoftware Ecosystem**: Integrate with existing VitexSoftware libraries and patterns
3. **AbraFlexi Specificity**: All analytics are specifically designed for AbraFlexi data structures
4. **MultiFlexi Standards**: Follow MultiFlexi application development guidelines
5. **Production Focus**: This is a production application - prioritize stability and performance
6. **Internationalization**: Always use translation functions for user-facing text

When working with this codebase:
- Follow established VitexSoftware patterns and conventions
- Maintain compatibility with existing MultiFlexi ecosystem
- Use AbraFlexi-specific data structures and API patterns
- Implement proper error handling and logging
- Ensure all new functionality is properly tested
