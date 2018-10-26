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

$start  = new \DateTime();
$start->modify('-1 year');
$end    = new \DateTime();
$period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

$subject = sprintf(
    _('FlexiBee Year digest from %s to %s'),
    \strftime('%x', $period->getStartDate()->getTimestamp()),
    \strftime('%x', $period->getEndDate()->getTimestamp())
);

$digestor = new Digestor($subject);
$digestor->dig($period, constant('MODULE_DIR'));
