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

\define('EASE_APPNAME', 'AbraFlexi 🌜 Monthly Digest');

require_once 'init.php';

$start = new \DateTime('-1 month');
$end = new \DateTime();
$period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

$myCompany = new \AbraFlexi\Company(\Ease\Shared::cfg('ABRAFLEXI_COMPANY'));
$subject = sprintf(_('AbraFlexi %s 🌜 Monthly digest'), $myCompany->getDataValue('nazev'));

$digestor = new ModularDigestor($subject);
$digestor->registerModules('monthly');
$digestor->run($period);
