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

require_once __DIR__.'/init.php';

$myCompany = new \AbraFlexi\Company(\Ease\Shared::cfg('ABRAFLEXI_COMPANY'));
$myCompanyName = $myCompany->getDataValue('nazev');

$subject = sprintf(_('AbraFlexi %s 🌌 Alltime Digest'), $myCompanyName);
$digestor = new Digestor($subject);
$start = new \DateTime();
$start->modify('-10 years');
$end = new \DateTime();
$period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
\Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules');
\Ease\Functions::loadClassesInNamespace('AbraFlexi\Digest\Modules\AllTime');

$digestor->dig($period, array_merge(\Ease\Functions::classesInNamespace('AbraFlexi\Digest\Modules',true), \Ease\Functions::classesInNamespace('AbraFlexi\Digest\Modules\AllTime',true)));
