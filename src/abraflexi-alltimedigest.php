<?php

/**
 * AbraFlexi Digest - Yearly 
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018 Vitex Software
 */

namespace AbraFlexi\Digest;

define('EASE_APPNAME', 'AbraFlexiAllTimeDigest');
require_once __DIR__ . '/init.php';
$subject = sprintf(_('AbraFlexi %s Alltime'), $myCompanyName);
$digestor = new Digestor($subject);
$digestor->dig(null, [constant('MODULE_ALLTIME_PATH'), constant('MODULE_PATH')]);
