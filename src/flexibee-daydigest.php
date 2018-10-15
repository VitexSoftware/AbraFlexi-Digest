<?php
/**
 * FlexiBee DigestMail - Dayly 
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018 Vitex Software
 */

namespace FlexiPeeHP\DigestMail;

define('EASE_APPNAME', 'FlexiBeeDigestMail');
define('MODULE_DIR', '../modules');

require_once '../vendor/autoload.php';
$shared = new \Ease\Shared();
$shared->loadConfig('../client.json', true);
$shared->loadConfig('../digestmail.json', true);

$period = new \DateTime();

$subject = \sprintf(_('FlexiBee Dayly digest for %s'),
    \strftime('%x', $period->getTimestamp()));

$postman = new Mailer($shared->getConfigValue('EASE_MAILTO'), $subject,
    constant('MODULE_DIR'));
$postman->dig($period);
$postman->send();
