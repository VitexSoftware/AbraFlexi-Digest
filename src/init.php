<?php
/**
 * FlexiBee Digest - Dayly 
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018 Vitex Software
 */

namespace FlexiPeeHP\Digest;

define('MODULE_DIR', './modules');
define('STYLE_DIR', './css');

require_once '../vendor/autoload.php';
$shared  = \Ease\Shared::instanced();
$shared->loadConfig('../client.json', true);
$shared->loadConfig('../digest.json', true);
$localer = new \Ease\Locale('cs_CZ', '../i18n', 'flexibee-digest');
