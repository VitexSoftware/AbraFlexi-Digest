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

\define('EASE_APPNAME', 'AbraFlexi Digest');

require_once __DIR__ . '/../src/init.php';
$oPage = new \Ease\TWB5\WebPage($myCompanyName . ' ' . _('AbraFlexi digest'));
$from = $oPage->getRequestValue('from');
$to = $oPage->getRequestValue('to');
$start = new \DateTime();

if (empty($from)) {
    $start->modify('-1 month');
    $from = $start->format('Y-m-d');
} else {
    [$year, $month, $day] = explode('-', $from);
    $start->setDate((int)$year, (int)$month, (int)$day);
}

$end = new \DateTime();

if (empty($to)) {
    $to = date('Y-m-d');
} else {
    [$year, $month, $day] = explode('-', $to);
    $end->setDate((int)$year, (int)$month, (int)$day);
}

if (\Ease\Document::isPosted()) {
    $formatter = new \IntlDateFormatter(\Ease\Locale::$localeUsed, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);
    $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);
    $subject = sprintf(
            _('AbraFlexi %s digest from %s to %s'),
            $myCompanyName,
            $formatter->format($period->getStartDate()->getTimestamp()),
            $formatter->format($period->getEndDate()->getTimestamp()),
    );
    $digestor = new Digestor($subject);
    $shared->setConfigValue('EASE_MAILTO', $oPage->getRequestValue('recipient'));
    $shared->setConfigValue('SAVETO', $oPage->getRequestValue('outdir'));
    $shared->setConfigValue('THEME', $oPage->getRequestValue('theme'));
    $digestor->dig($period, $oPage->getRequestValue('modules'));
    //    $digestor->addItem(new \Ease\Html\ATag('index.php', _('New Digest')));
    //    $oPage->addCss(Digestor::$purecss);
    $oPage->addCss(Digestor::getCustomCss());
    $oPage->addCss(Digestor::getWebPageInlineCSS());
    $oPage->setPageTitle($subject);
    $oPage->addItem(new \Ease\TWB5\Container($digestor));
    $oPage->addItem(new \Ease\TWB5\Container($digestor->printResults()));
    //    exit();
}

$candidates = [
    _('Common modules') => 'AbraFlexi\Digest\Modules',
    _('Daily modules') => 'AbraFlexi\Digest\Modules\Daily',
    _('Weekly modules') => 'AbraFlexi\Digest\Modules\Weekly',
    _('Monthly modules') => 'AbraFlexi\Digest\Modules\Monthly',
    _('Yearly modules') => 'AbraFlexi\Digest\Modules\Yearly',
    _('Alltime modules') => 'AbraFlexi\Digest\Modules\AllTime',
];
$fromtoForm = new \Ease\TWB5\Form(['name' => 'fromto', 'class' => 'form-horizontal']);
$container = new \Ease\TWB5\Container(new \Ease\Html\H1Tag(new \Ease\Html\ATag(
                        $myCompany->getApiURL(),
                        $myCompanyName,
                ) . ' ' . _('AbraFlexi digest')));
$container->addItem(new \AbraFlexi\ui\CompanyLogo(['class' => 'img-fluid']));
$container->addItem(new \AbraFlexi\ui\TWB5\StatusInfoBox());
$formColumns = new \Ease\TWB5\Row();
$modulesCol = $formColumns->addColumn(6, new \Ease\Html\H2Tag(_('Modules')));
$modulesCol->addItem(new \Ease\Html\ATag('#', _('Check All'), ['onClick' => '$(\'input:checkbox\').prop(\'checked\', true);']));

$period = new \DatePeriod(new \DateTime(), new \DateInterval('P1D'), new \DateTime());

foreach ($candidates as $heading => $namespace) {
    $modules = \Ease\Functions::loadClassesInNamespace($namespace);
    $modulesCol->addItem(new \Ease\Html\H3Tag($heading));
    asort($modules);

    foreach ($modules as $className => $classFile) {
        $module = new $className($period);
        $modulesCol->addItem(new \Ease\TWB5\Widgets\Toggle(
                        'modules[' . $className . ']',
                        isset($_REQUEST) && \array_key_exists('modules', $_REQUEST) && \array_key_exists($className, $_REQUEST['modules']),
                        $classFile,
                        ['class' => 'module', 'data-ontitle' => $module->heading(), 'data-offtitle' => $module->heading()],
        ));
    }
}

$optionsCol = $formColumns->addColumn(6, new \Ease\Html\H2Tag(_('Options')));
$themes = [];
$d = dir(\Ease\Shared::cfg('STYLE_DIR','../src/css/themes/'));

while (false !== ($entry = $d->read())) {
    if (pathinfo($entry, \PATHINFO_EXTENSION) === 'css') {
        $themes[pathinfo($entry, \PATHINFO_BASENAME)] = ucfirst(pathinfo(pathinfo($entry, \PATHINFO_FILENAME), \PATHINFO_FILENAME));
    }
}

$d->close();
$oPage->addJavaScript(<<<'EOD'


var od = $( "input[name='from']" );

$( "#yesterday" ).click(function() {
    var today = new Date();
    today.setDate(today.getDate() - 1);
    od.val( today.toISOString().split('T')[0] );
});

$( "#lastweek" ).click(function() {
    var today = new Date();
    today.setDate(today.getDate() - 7);
    od.val( today.toISOString().split('T')[0] );
});

$( "#lastmonth" ).click(function() {
    var today = new Date();
    today.setMonth(today.getMonth() - 1);
    od.val( today.toISOString().split('T')[0] );
});

$( "#lastyear" ).click(function() {
    var today = new Date();
    today.setFullYear(today.getFullYear() - 1);
    od.val( today.toISOString().split('T')[0] );
});


EOD);
$optionsCol->addItem(new \Ease\TWB5\LinkButton(
                '#',
                _('Yesterday'),
                'inverse',
                ['id' => 'yesterday'],
));
$optionsCol->addItem(new \Ease\TWB5\LinkButton(
                '#',
                _('Week'),
                'inverse',
                ['id' => 'lastweek'],
));
$optionsCol->addItem(new \Ease\TWB5\LinkButton(
                '#',
                _('Month'),
                'inverse',
                ['id' => 'lastmonth'],
));
$optionsCol->addItem(new \Ease\TWB5\LinkButton(
                '#',
                _('Year'),
                'inverse',
                ['id' => 'lastyear'],
));
$optionsCol->addItem(new \Ease\TWB5\FormGroup(
                _('From'),
                new \Ease\Html\InputDateTag('from', $from),
));
$optionsCol->addItem(new \Ease\TWB5\FormGroup(
                _('To'),
                new \Ease\Html\InputDateTag('to', $to),
));
$optionsCol->addItem(new \Ease\TWB5\FormGroup(
                _('Theme name'),
                new \Ease\Html\SelectTag('theme', $themes, (string)$shared->getConfigValue('THEME')),
));
$optionsCol->addItem(new \Ease\TWB5\FormGroup(
                _('Output Directory'),
                new \Ease\Html\InputTextTag('outdir', $shared->getConfigValue('SAVETO')),
));
$optionsCol->addItem(new \Ease\TWB5\FormGroup(
                _('Send by mail to'),
                new \Ease\Html\InputEmailTag(
                        'recipient',
                        $shared->getConfigValue('EASE_MAILTO'),
                ),
));
$optionsCol->addItem(new \Ease\TWB5\FormGroup(_('Language select'), new \Ease\Html\Widgets\LangSelect('lang',[])));

if (\Ease\Shared::cfg('SHOW_CONNECTION_FORM')) {
    $optionsCol->addItem(new \AbraFlexi\ui\TWB5\ConnectionForm($myCompany->getConnectionOptions()));
}

$fromtoForm->addItem($formColumns);
$fromtoForm->addItem(new \Ease\TWB5\SubmitButton(
                _('Generate digest'),
                'success btn-lg btn-block',
                ['onClick' => "window.scrollTo(0, 0); $('#wrap').css('visibility', 'visible');",
            'style' => 'height: 90%'],
));
$container->addItem($fromtoForm);
$oPage->addItem($container);
$container = $oPage->setTagID('footer');
$oPage->addItem('<hr>');
$footrow = new \Ease\TWB5\Row();
$author = '<a href="https://github.com/VitexSoftware/AbraFlexi-Digest">AbraFlexi Digest</a> v.: ' . \Ease\Shared::appVersion() . '&nbsp;&nbsp; &copy; 2018-2023 <a href="https://vitexsoftware.cz/">Vitex Software</a>';
$footrow->addColumn(6, [$author]);
$oPage->addItem(new \Ease\TWB5\Container($footrow));
$oPage->addItem(new \AbraFlexi\Digest\SandClock());
$oPage->draw();
