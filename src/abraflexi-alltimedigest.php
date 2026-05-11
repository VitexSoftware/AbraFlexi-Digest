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

\define('EASE_APPNAME', 'AbraFlexi 🌌 All Time Digest');

require_once 'init.php';

$start = new \DateTime('-10 years');
$end = new \DateTime();
$period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

$myCompany = new \AbraFlexi\Company(\Ease\Shared::cfg('ABRAFLEXI_COMPANY'));
$subject = sprintf(_('AbraFlexi %s 🌌 Alltime digest'), $myCompany->getDataValue('nazev'));

$digestor = new ModularDigestor($subject);
$digestor->registerModules('alltime');
$digestor->run($period);
