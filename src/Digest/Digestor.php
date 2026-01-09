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

use Ease\Html\DivTag;
use Ease\Html\PTag;

/**
 * Description of Digestor.
 *
 * @author vitex
 *
 * @no-named-arguments
 */
class Digestor extends \Ease\Html\DivTag
{
    /**
     * Default Style.
     */
    public static string $purecss = '';
    public static $msocss = <<<'EOD'
    /* Remove space around the email design. */

   html,

   body {

       margin: 0 auto !important;

       padding: 0 !important;

       height: 100% !important;

       width: 100% !important;

   }

   /* Stop Outlook resizing small text. */

   * {

       -ms-text-size-adjust: 100%;

   }

   /* Stop Outlook from adding extra spacing to tables. */

   table,

   td {

       mso-table-lspace: 0pt !important;

       mso-table-rspace: 0pt !important;

   }

   /* Use a better rendering method when resizing images in Outlook IE. */

   img {

       -ms-interpolation-mode:bicubic;

   }

 /* Prevent Windows 10 Mail from underlining links. Styles for underlined links should be inline. */

   a {

       text-decoration: none;

   }
EOD;

    /**
     * App Logo.
     */
    public static string $logo = '<svg width="250" height="250" enable-background="new -0.161 -0.355 237 211" version="1.1" viewBox="-0.161 -0.355 250 250" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"><linearGradient id="a" x1="148.69" x2="148.69" y1="80.375" y2="205" gradientUnits="userSpaceOnUse"><stop stop-color="#CDAD2D" offset="0"/><stop stop-color="#917D2E" offset="1"/></linearGradient><metadata><rdf:RDF><cc:Work><dc:format>image/svg+xml</dc:format><dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage"/><cc:license rdf:resource="http://creativecommons.org/publicdomain/zero/1.0/"/><dc:publisher><cc:Agent rdf:about="http://openclipart.org/"><dc:title>Openclipart</dc:title></cc:Agent></dc:publisher><dc:title>Email</dc:title><dc:date>2008-06-14T10:49:47</dc:date><dc:description>Email icon.</dc:description><dc:source>https://openclipart.org/detail/17371/email-by-ytknick</dc:source><dc:creator><cc:Agent><dc:title>ytknick</dc:title></cc:Agent></dc:creator><dc:subject><rdf:Bag><rdf:li>email</rdf:li><rdf:li>envelope</rdf:li><rdf:li>icon</rdf:li><rdf:li>letter</rdf:li><rdf:li>mail</rdf:li><rdf:li>postage</rdf:li></rdf:Bag></dc:subject></cc:Work><cc:License rdf:about="http://creativecommons.org/publicdomain/zero/1.0/"><cc:permits rdf:resource="http://creativecommons.org/ns#Reproduction"/><cc:permits rdf:resource="http://creativecommons.org/ns#Distribution"/><cc:permits rdf:resource="http://creativecommons.org/ns#DerivativeWorks"/></cc:License></rdf:RDF></metadata><g transform="translate(6,-18)"><polygon transform="translate(0,39)" points="148.69 142.69 229.32 80.375 229.32 205 68.055 205 68.055 80.375" fill="url(#a)"/><polygon transform="translate(0,39)" points="229.32 80.375 148.69 142.69 68.055 80.375" fill="#826a2a" stroke="#000" stroke-width="5"/><rect x="68.055" y="119.38" width="161.27" height="124.62" fill="none" stroke="#000" stroke-width="10"/><polyline transform="translate(0,39)" points="68.055 80.375 148.69 142.69 229.32 80.375" fill="none" stroke="#000" stroke-width="3"/><path d="m108.85 95.927c0-28.679-23.246-51.927-51.922-51.927-28.68 0-51.928 23.248-51.928 51.927s23.248 51.927 51.928 51.927c3.191 0 6.312-0.303 9.344-0.854l27.316 17.951-5.141-27.768c12.403-9.489 20.403-24.434 20.403-41.255z" fill="#812619" stroke="#000" stroke-width="10"/><g transform="matrix(.097526 0 0 -.097526 -96.586 115.38)" clip-rule="evenodd" image-rendering="optimizeQuality" shape-rendering="geometricPrecision"><path d="m1708.7 0 133.78 231.62 133.71-231.62z" fill="#f9ae2d"/><path d="m1708.7 0-133.75 231.62h267.53z" fill="#d28b25"/><path d="m1574.9 231.62 133.75 231.68 133.78-231.68z" fill="#936327"/><path d="m1708.7 463.3h-267.47l-267.56-463.3h267.56l267.47 463.3" fill="#767a7c"/></g></g></svg>';

    /**
     * Top menu.
     */
    public \Ease\TWB5\Navbar $topMenu;

    /**
     * Benchmark results.
     */
    public array $benchmark = [];

    /**
     * Subject.
     */
    private string $subject;

    /**
     * Index of included modules.
     */
    private array $index = [];

    /**
     * Digest Engine.
     *
     * @param string $subject
     */
    public function __construct($subject)
    {
        parent::__construct(null, ['class' => 'Xaccordion', 'id' => 'accordionExample']);
        $this->subject = $subject;
        $this->addHeading($subject);
    }

    /**
     * Start Timer by name.
     *
     * @param string $timerName
     */
    public function timerStart($timerName): void
    {
        $this->benchmark[$timerName] = ['start' => microtime()];
    }

    /**
     * Cout the time pass.
     *
     * @param string $timerName
     */
    public function timerStop($timerName): void
    {
        $this->benchmark[$timerName]['end'] = microtime();
    }

    /**
     * @param array<string> $startEnd
     *
     * @return string
     */
    public function timerValue(array $startEnd)
    {
        $time_start = explode(' ', $startEnd['start']);
        $time_end = explode(' ', $startEnd['end']);

        return number_format($time_end[1] + $time_end[0] - ($time_start[1] + $time_start[0]), 3);
    }

    /**
     * Digest page Heading.
     *
     * @param mixed $subject
     */
    public function addHeading($subject): void
    {
        $this->addItem(new \Ease\Html\ATag('', '', ['name' => 'index']));
        $this->addItem(new \AbraFlexi\ui\TWB5\CompanyLogo([
            'align' => 'right', 'id' => 'companylogo',
            'height' => '50', 'title' => _('Company logo'),
        ]));
        $this->addItem(new \Ease\Html\H1Tag($subject));
        $prober = new \AbraFlexi\Company();
        $prober->logBanner(' AbraFlexi Digest '.\Ease\Shared::appVersion().' '.$_SERVER['SCRIPT_FILENAME']);

        try {
            $infoRaw = $prober->getFlexiData();
        } catch (\Exception $exc) {
            echo $exc->getTraceAsString();
        }

        if (!empty($infoRaw) && !\array_key_exists('success', $infoRaw)) {
            $info = \Ease\Functions::reindexArrayBy($infoRaw, 'dbNazev');
            $myCompany = $prober->getCompany();

            if (\array_key_exists($myCompany, $info)) {
                $return = new \Ease\Html\ATag(
                    $prober->url.'/c/'.$myCompany,
                    $info[$myCompany]['nazev'],
                );
            } else {
                $return = new \Ease\Html\ATag(
                    $prober->getApiURL(),
                    _('Connection Problem'),
                );
            }

            $this->addItem(new \Ease\Html\StrongTag(
                $return,
                ['class' => 'companylink'],
            ));
        }

        $this->topMenu = $this->addItem(new \Ease\TWB5\Navbar());
    }

    /**
     * Include all classes in modules directory.
     */
    public function dig(\DatePeriod $probePeriod, array $modules): void
    {
        $this->processModules($modules, $probePeriod);
        $this->addIndex();
        $this->addFoot();
        $emailto = \Ease\Shared::cfg('DIGEST_MAILTO', \Ease\Shared::cfg('EASE_MAILTO', ''));

        if ($emailto) {
            $this->sendByMail($emailto);
        } else {
            $this->addStatusMessage('EASE_MAILTO not defined - not sending result', 'debug');
        }

        $saveto = \Ease\Shared::cfg('RESULT_FILE');

        if ($saveto) {
            $this->saveToHtml($saveto);
        } else {
            $this->addStatusMessage('SAVETO not defined - not saving result to file', 'debug');
        }
    }

    /**
     * Process All modules in specified Dir.
     *
     * @param array                 $modules  [classname=>filepath]
     * @param \DatePeriod|\DateTime $interval
     */
    public function processModules(array $modules, \DatePeriod $interval): void
    {
        $saveto = \Ease\Shared::cfg('DIGEST_SAVETO', false);

        foreach ($modules as $class => $classFile) {
            $this->timerStart($class);
            $module = new $class($interval);

            if ($module->process()) {
                //                $this->addItem(new \Ease\Html\HrTag());
                $this->addToIndex($this->addItem($module));

                if ($saveto) {
                    $module->saveToHtml($saveto);
                }
            } else {
                $this->addStatusMessage(sprintf(
                    _('Module %s did not find results'),
                    $class,
                ));

                if ($saveto) {
                    $module->fileCleanUP($saveto);
                }
            }

            $this->timerStop($class);
        }
    }

    /**
     * @param DigestModule $element
     */
    public function addToIndex($element): void
    {
        $this->index[$element::class] = $element->heading();
    }

    /**
     * Add Index to digest.
     */
    public function addIndex(): void
    {
        $this->addItem(new \Ease\Html\H1Tag(new \Ease\Html\ATag(
            '',
            _('Index'),
            ['name' => 'index2'],
        )));
        $this->addItem(new \Ease\Html\HrTag());
        $index = new \Ease\Html\NavTag(null, ['class' => 'nav']);

        foreach ($this->index as $class => $heading) {
            $index->addItem(new \Ease\Html\ATag('#'.$class, $heading, ['class' => 'nav-link btn btn-light']));
            $this->topMenu->addItem(new \Ease\Html\ATag('#'.$class, $heading, [
                'class' => 'nav-link btn btn-light',
                'style' => 'display: inline-block;',
            ]));
        }

        $this->addItem($index);
    }

    //    /**
    //     * Include next element into current page (if not closed).
    //     *
    //     * @param mixed  $pageItem     value or EaseClass with draw() method
    //     * @param string $pageItemName Custom 'storing' name
    //     *
    //     * @return mixed Pointer to included object
    //     */
    //    public function addItem($pageItem, $pageItemName = null)
    //    {
    //        return parent::addItem($pageItem, $pageItemName);
    //    }

    /**
     * Sent digest by mail.
     *
     * @param string $mailto
     *
     * @return bool
     */
    public function sendByMail($mailto)
    {
        $postman = new Mailer($mailto, $this->subject);
        $postman->addItem($this);

        return $postman->send() === true;
    }

    /**
     * Save HTML digest.
     *
     * @param string $saveTo directory
     */
    public function saveToHtml($saveTo): void
    {
        $filename = $saveTo;
        $webPage = new \Ease\Html\HtmlTag(new \Ease\Html\SimpleHeadTag([
            new \Ease\Html\TitleTag($this->subject),
            '<style>'.self::$purecss.self::getCustomCss().self::getWebPageInlineCSS().'</style>',
        ]));
        $webPage->addItem(new \Ease\Html\BodyTag($this));
        $this->addStatusMessage(
            sprintf(_('Saved to %s'), $filename),
            file_put_contents($filename, $webPage->getRendered()) ? 'success' : 'error',
        );
    }

    public static function getWebPageInlineCSS()
    {
        //        $easeShared = \Ease\Shared::webPage();
        //        if (isset($easeShared->cascadeStyles) && count($easeShared->cascadeStyles)) {
        //            $cascadeStyles = [];
        //            foreach ($easeShared->cascadeStyles as $StyleRes => $Style) {
        //                if ($StyleRes != $Style) {
        //                    $cascadeStyles[] = $Style;
        //                }
        //            }
        //        }
        //        return implode('', $cascadeStyles);
        return VerticalChart::$chartCss;
    }

    /**
     * Obtain Custom CSS - THEME in digest.json.
     *
     * @return string
     */
    public static function getCustomCss()
    {
        $theme = \Ease\Shared::cfg('THEME', 'bootstrap.min.css');
        $cssfile = \Ease\Shared::cfg('STYLE_DIR').'/'.$theme;

        return (file_exists($cssfile) && is_file($cssfile)) ? file_get_contents($cssfile) : '';
    }

    /**
     * Page Bottom.
     */
    public function addFoot(): void
    {
        $this->addItem(new \Ease\Html\HrTag());
        $this->addItem(new \Ease\Html\ImgTag(
            'data:image/svg+xml;base64,'.base64_encode(self::$logo),
            'Logo',
            ['align' => 'right', 'width' => '50'],
        ));
        $this->addItem(new \Ease\Html\SmallTag(new \Ease\Html\DivTag([
            _('Generated by'),
            '&nbsp;', new \Ease\Html\ATag(
                'https://github.com/VitexSoftware/AbraFlexi-Digest',
                _('AbraFlexi Digest').' '._('version').' '.\Ease\Shared::appVersion(),
            ),
        ])));
        $this->addItem(new \Ease\Html\SmallTag(new \Ease\Html\DivTag([
            _('(G) 2018-2023'),
            '&nbsp;', new \Ease\Html\ATag(
                'https://www.vitexsoftware.cz/',
                'Vitex Software',
            ),
        ])));
        $this->addItem(new \Ease\Html\PTag(new \Ease\Html\SmallTag(\Ease\Shared::appName().' v'.\Ease\Shared::appVersion())));
    }

    /**
     * Undocumented function.
     */
    public function printResults()
    {
        $results = new DivTag();
        $results->addItem(new PTag(vsprintf("%-30s; %s; %s\n", ['operation', 'read time', 'write time'])));

        foreach (array_keys($this->benchmark) as $testName) {
            $resRow = new \Ease\TWB5\Row();
            $resRow->addColumn(4, $testName);
            $resRow->addColumn(8, $this->timerValue($this->benchmark[$testName]));
            $results->addItem($resRow);
        }

        return $results;
    }
}
