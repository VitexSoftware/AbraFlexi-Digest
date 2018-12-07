<?php
/**
 * FlexiBee Digest - Dayly 
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018 Vitex Software
 */

namespace FlexiPeeHP\Digest;

define('MODULE_PATH', './modules');
define('MODULE_DAILY_PATH', './modules.daily');
define('MODULE_WEEKLY_PATH', './modules.weekly');
define('MODULE_MONTHLY_PATH', './modules.monthly');
define('MODULE_YEARLY_PATH', './modules.yearly');
define('MODULE_ALLTIME_PATH', './modules.alltime');

define('STYLE_DIR', './css');

require_once '../vendor/autoload.php';
$shared  = \Ease\Shared::instanced();
$shared->loadConfig('../client.json', true);
$shared->loadConfig('../digest.json', true);
$localer = new \Ease\Locale('cs_CZ', '../i18n', 'flexibee-digest');

$myCompany     = new \FlexiPeeHP\Company($shared->getConfigValue('FLEXIBEE_COMPANY'));
$myCompanyName = $myCompany->getDataValue('nazev');
