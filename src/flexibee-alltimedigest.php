<?php
/**
 * FlexiBee Digest - Yearly 
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018 Vitex Software
 */

namespace FlexiPeeHP\Digest;

define('EASE_APPNAME', 'FlexiBeeDigest');
define('MODULE_DIR', './modules');
define('STYLE_DIR', './css');

require_once '../vendor/autoload.php';
$shared = \Ease\Shared::instanced();
$shared->loadConfig('../client.json', true);
$shared->loadConfig('../digest.json', true);

$subject = sprintf(_('FlexiBee Alltime'));

$digestor = new Digestor($subject);
$digestor->dig(null, constant('MODULE_DIR'));
