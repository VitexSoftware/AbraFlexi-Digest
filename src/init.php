<?php

/**
 * AbraFlexi Digest - Init
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018-2023 Vitex Software
 */

namespace AbraFlexi\Digest;

define('MODULE_PATH', './Digest/Modules');
define('MODULE_DAILY_PATH', './Digest/Modules/Daily');
define('MODULE_WEEKLY_PATH', './Digest/Modules/Weekly');
define('MODULE_MONTHLY_PATH', './Digest/Modules/Monthly');
define('MODULE_YEARLY_PATH', './Digest/Modules/Yearly');
define('MODULE_ALLTIME_PATH', './Digest/Modules/AllTime');

define('STYLE_DIR', './css/themes/');

require_once '../vendor/autoload.php';
$shared = \Ease\Shared::instanced();

if (\Ease\Document::isPosted() && \Ease\Document::getPostValue('url')) {
    define('SHOW_CONNECTION_FORM', true);
    define('ABRAFLEXI_URL', \Ease\Document::getPostValue('url'));
    define('ABRAFLEXI_LOGIN', \Ease\Document::getPostValue('user'));
    define('ABRAFLEXI_PASSWORD', \Ease\Document::getPostValue('password'));
    define('ABRAFLEXI_COMPANY', \Ease\Document::getPostValue('company'));
    $shared->setConfigValue('EASE_MAILTO', \Ease\Document::getPostValue('recipient'));
} else {
    if (file_exists('../.env')) {
        $shared->loadConfig('../.env', true);
    }
}

$localer = \Ease\Locale::singleton(null, '../i18n', 'abraflexi-digest');

$myCompany = new \AbraFlexi\Company(\Ease\Functions::cfg('ABRAFLEXI_COMPANY'));
$myCompanyName = $myCompany->getDataValue('nazev');
