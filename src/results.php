<?php

/**
 * AbraFlexi Digest - Monthly
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018-2023 Vitex Software
 */

namespace AbraFlexi\Digest;

define('EASE_APPNAME', 'AbraFlexi Digest');
require_once __DIR__ . '/init.php';
$oPage = new \Ease\TWB4\WebPage($myCompanyName . ' ' . _('AbraFlexi digest'));
$container = new \Ease\TWB4\Container(new \Ease\Html\H1Tag(new \Ease\Html\ATag(
    $myCompany->getApiURL(),
    $myCompanyName
) . ' ' . _('AbraFlexi digest results')));
$reports = [];
foreach (scandir($shared->getConfigValue('SAVETO')) as $file) {
    if (preg_match('/^abraflexi-(.*)digest_(.*).html/', $file, $matches)) {
        $reports[$matches[1]][$matches[2]] = $shared->getConfigValue('SAVETO') . '/' . $file;
    }
}


$scopeTabs = new \Ease\TWB4\Tabs('ScopeTabs');
foreach ($reports as $scope => $reports) {
    $reportTabs = new \Ease\TWB4\Tabs($scope . 'Reports');
    foreach ($reports as $reportName => $reportFile) {
        $reportTabs->addTab($reportName, str_replace('table', 'table', file_get_contents($reportFile)));
    }
    $scopeTabs->addTab($scope, $reportTabs);
}

$container->addItem($scopeTabs);
$oPage->addItem($container);
//$oPage->addItem(new \Ease\FuelUX\Loader("Preloader"));
$oPage->draw();
