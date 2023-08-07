<?php

/**
 * AbraFlexi Digest - Init
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018-2023 Vitex Software
 */

namespace AbraFlexi\Digest;

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
    $conffile = (array_key_exists(1, $argv) && file_exists($argv[1])) ? $argv[1] :  '../.env';
    if (file_exists($conffile)) {
        $shared->loadConfig($conffile, true);
    }
}

$localer = \Ease\Locale::singleton(null, '../i18n', 'abraflexi-digest');
$myCompany = new \AbraFlexi\Company(\Ease\Functions::cfg('ABRAFLEXI_COMPANY'));
$myCompanyName = $myCompany->getDataValue('nazev');
