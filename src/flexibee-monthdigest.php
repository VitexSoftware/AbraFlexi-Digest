<?php
/**
 * FlexiBee Digest - Monthly 
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018 Vitex Software
 */

namespace FlexiPeeHP\Digest;

define('EASE_APPNAME', 'FlexiBeeDigest');
define('MODULE_DIR', './modules');

require_once '../vendor/autoload.php';
$shared = new \Ease\Shared();
$shared->loadConfig('../client.json', true);
$shared->loadConfig('../digest.json', true);

$start  = new \DateTime();
$start->modify('-1 month');
$end    = new \DateTime();
$period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

$subject = sprintf(
    _('FlexiBee Monthly digest from %s to %s'),
    \strftime('%x', $period->getStartDate()->getTimestamp()),
    \strftime('%x', $period->getEndDate()->getTimestamp())
);


$postman = new Mailer($shared->getConfigValue('EASE_MAILTO'), $subject,
    constant('MODULE_DIR'));
$postman->dig($period);
$postman->send();
$postman->htmlDocument->draw();


