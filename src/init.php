<?php

declare(strict_types=1);

/**
 * This file is part of the AbraFlexi-Digest package
 *
 * https://github.com/VitexSoftware/AbraFlexi-Digest/
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AbraFlexi\Digest;

// \define('STYLE_DIR', './css/themes/');

require_once '../vendor/autoload.php';
$shared = \Ease\Shared::instanced();

if (\Ease\Document::isPosted() && \Ease\Document::getPostValue('url')) {
    \define('SHOW_CONNECTION_FORM', true);
    \define('ABRAFLEXI_URL', \Ease\Document::getPostValue('url'));
    \define('ABRAFLEXI_LOGIN', \Ease\Document::getPostValue('user'));
    \define('ABRAFLEXI_PASSWORD', \Ease\Document::getPostValue('password'));
    \define('ABRAFLEXI_COMPANY', \Ease\Document::getPostValue('company'));
    $shared->setConfigValue('EASE_MAILTO', \Ease\Document::getPostValue('recipient'));
} else {
    $conffile = (isset($argv) && \is_array($argv) && array_key_exists(1, $argv) && file_exists($argv[1])) ? $argv[1] : '../.env';

    if (file_exists($conffile)) {
        $shared->loadConfig($conffile, true);
    }
}

$oPage = new WebPage();

$localer = \Ease\Locale::singleton(null, '../i18n', 'abraflexi-digest');
