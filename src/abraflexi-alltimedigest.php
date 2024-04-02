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
$subject = sprintf(_('AbraFlexi %s Alltime Digest'), $myCompanyName);
$digestor = new Digestor($subject);
$start = new \DateTime();
$start->modify('-10 years');
$end = new \DateTime();
$period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
$digestor->dig($period, array_merge(\Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules'), \Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules\AllTime')));
