<?php
/**
 * FlexiBee Digest - Dayly 
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018 Vitex Software
 */

namespace FlexiPeeHP\Digest;

define('EASE_APPNAME', 'FlexiBeeDayDigest');

require_once __DIR__.'/init.php';

$period = new \DateTime();

$subject = \sprintf(_('FlexiBee %s Daily digest for %s'), $myCompanyName,
    \strftime('%x', $period->getTimestamp()));

$digestor = new Digestor($subject);
$digestor->dig($period, [constant('MODULE_DAILY_PATH'), constant('MODULE_PATH')]);
