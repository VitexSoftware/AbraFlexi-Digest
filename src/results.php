<?php
/**
 * FlexiBee Digest - Monthly 
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018 Vitex Software
 */

namespace FlexiPeeHP\Digest;

define('EASE_APPNAME', 'FlexiBee Digest');

require_once __DIR__.'/init.php';

$oPage = new \Ease\TWB\WebPage($myCompanyName.' '._('FlexiBee digest'));

$container = new \Ease\TWB\Container(new \Ease\Html\H1Tag(new \Ease\Html\ATag($myCompany->getApiURL(),
    $myCompanyName).' '._('FlexiBee digest results')));


$reports = [];
foreach (scandir($shared->getConfigValue('SAVETO')) as $file) {
    if (preg_match('/^flexibee-(.*)digest_(.*).html/', $file, $matches)) {
        $reports[$matches[1]][$matches[2]] = $shared->getConfigValue('SAVETO').'/'.$file;
    }
}


$scopeTabs = new \Ease\TWB\Tabs('ScopeTabs');

foreach ($reports as $scope => $reports) {
    $reportTabs = new \Ease\TWB\Tabs( $scope . 'Reports');
    foreach ($reports as $reportName => $reportFile) {
        $reportTabs->addTab($reportName, str_replace('pure-table','table', file_get_contents($reportFile)));
    }
    $scopeTabs->addTab($scope, $reportTabs);
}

$container->addItem($scopeTabs);

$oPage->addItem($container);

//$oPage->addItem(new \Ease\FuelUX\Loader("Preloader"));
$oPage->draw();
