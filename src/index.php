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



$from = $oPage->getRequestValue('from');
$to   = $oPage->getRequestValue('to');

$start = new \DateTime();
if (empty($from)) {
    $start->modify('-1 month');
    $from = $start->format('Y-m-d');
} else {
    list($year, $month, $day) = explode('-', $from);
    $start->setDate($year, $month, $day);
}

$end = new \DateTime();
if (empty($to)) {
    $to = date('Y-m-d');
} else {
    list($year, $month, $day) = explode('-', $to);
    $end->setDate($year, $month, $day);
}



if ($oPage->isPosted()) {

    $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

    $subject = sprintf(
        _('FlexiBee %s digest from %s to %s'), $myCompanyName,
        \strftime('%x', $period->getStartDate()->getTimestamp()),
        \strftime('%x', $period->getEndDate()->getTimestamp())
    );

    $digestor = new Digestor($subject);

    $shared->setConfigValue('EASE_MAILTO', $oPage->getRequestValue('recipient'));
    $shared->setConfigValue('SAVETO', $oPage->getRequestValue('outdir'));

    $digestor->dig($period, $oPage->getRequestValue('modules'));

    $digestor->addItem(new \Ease\Html\ATag('index.php', _('New Digest')));

    $oPage->addCss(Digestor::$purecss);
    $oPage->addCss(Digestor::getCustomCss());
    $oPage->addCss(Digestor::getWebPageInlineCSS());
    $oPage->setPageTitle($subject);
    $oPage->body = $digestor;
    $oPage->draw();
    exit();
}


$candidates[_('Common modules')]  = Digestor::getModules(constant('MODULE_PATH'));
$candidates[_('Daily modules')]   = Digestor::getModules(constant('MODULE_DAILY_PATH'));
$candidates[_('Weekly modules')]  = Digestor::getModules(constant('MODULE_WEEKLY_PATH'));
$candidates[_('Monthly modules')] = Digestor::getModules(constant('MODULE_MONTHLY_PATH'));
$candidates[_('Yearly modules')]  = Digestor::getModules(constant('MODULE_YEARLY_PATH'));
$candidates[_('Alltime modules')] = Digestor::getModules(constant('MODULE_ALLTIME_PATH'));


$fromtoForm = new \Ease\TWB\Form('fromto');
$fromtoForm->addTagClass('form-horizontal');

$container = new \Ease\TWB\Container(new \Ease\Html\H1Tag(new \Ease\Html\ATag($myCompany->getApiURL(),
    $myCompanyName).' '._('FlexiBee digest')));


$formColumns = new \Ease\TWB\Row();
$modulesCol  = $formColumns->addColumn(6, new \Ease\Html\H2Tag(_('Modules')));

foreach ($candidates as $heading => $modules) {
    $modulesCol->addItem(new \Ease\Html\H3Tag($heading));
    asort($modules);
    foreach ($modules as $className => $classFile) {
        include_once $classFile;
        $module = new $className(null);
        $modulesCol->addItem(new \Ease\TWB\Checkbox('modules['.$className.']',
            $classFile, '&nbsp;'.$module->heading()));
    }
}

$optionsCol = $formColumns->addColumn(6, new \Ease\Html\H2Tag(_('Options')));

$themes = [];
$d      = dir(constant('STYLE_DIR'));
while (false !== ($entry  = $d->read())) {
    if (pathinfo($entry, PATHINFO_EXTENSION) == 'css') {
        $themes[pathinfo($entry, PATHINFO_FILENAME)] = pathinfo($entry,
            PATHINFO_FILENAME);
    }
}
$d->close();


$optionsCol->addItem(new \Ease\TWB\FormGroup(_('From'),
    new \Ease\Html\InputDateTag('from', $from)));

$optionsCol->addItem(new \Ease\TWB\FormGroup(_('To'),
    new \Ease\Html\InputDateTag('to', $to)));

$optionsCol->addItem(new \Ease\TWB\FormGroup(_('Theme name'),
    new \Ease\Html\SelectTag('theme', $themes)));

$optionsCol->addItem(new \Ease\TWB\FormGroup(_('Output Directory'),
    new \Ease\Html\InputTextTag('outdir', $shared->getConfigValue('SAVETO'))));

$optionsCol->addItem(new \Ease\TWB\FormGroup(_('Send by mail to'),
    new \Ease\Html\InputEmailTag('recipient',
    $shared->getConfigValue('EASE_MAILTO'))));


$fromtoForm->addItem($formColumns);
$fromtoForm->addItem(new \Ease\TWB\SubmitButton(_('Generate digest'),
    'success btn-lg btn-block',
    ['onClick' => "window.scrollTo(0, 0); $('#Preloader').css('visibility', 'visible');",
    'style' => 'height: 90%']));

$container->addItem($fromtoForm);


$oPage->addItem($container);

//$oPage->addItem(new \Ease\FuelUX\Loader("Preloader"));
$oPage->draw();
