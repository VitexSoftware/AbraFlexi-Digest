<?php

/**
 * AbraFlexi Digest Engine
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018 Vitex Software
 */

namespace AbraFlexi\Digest;

/**
 * Description of Digestor
 *
 * @author vitex
 */
class Digestor extends \Ease\Html\DivTag {

    /**
     * Subject
     * @var string 
     */
    private $subject;

    /**
     * Index of included modules
     * @var array 
     */
    private $index = [];

    /**
     * Default Style
     * @var string 
     */
    static $purecss = '';

    /**
     * App Logo
     * @var string 
     */
    static $logo = '<svg width="250" height="250" enable-background="new -0.161 -0.355 237 211" version="1.1" viewBox="-0.161 -0.355 250 250" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"><linearGradient id="a" x1="148.69" x2="148.69" y1="80.375" y2="205" gradientUnits="userSpaceOnUse"><stop stop-color="#CDAD2D" offset="0"/><stop stop-color="#917D2E" offset="1"/></linearGradient><metadata><rdf:RDF><cc:Work><dc:format>image/svg+xml</dc:format><dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage"/><cc:license rdf:resource="http://creativecommons.org/publicdomain/zero/1.0/"/><dc:publisher><cc:Agent rdf:about="http://openclipart.org/"><dc:title>Openclipart</dc:title></cc:Agent></dc:publisher><dc:title>Email</dc:title><dc:date>2008-06-14T10:49:47</dc:date><dc:description>Email icon.</dc:description><dc:source>https://openclipart.org/detail/17371/email-by-ytknick</dc:source><dc:creator><cc:Agent><dc:title>ytknick</dc:title></cc:Agent></dc:creator><dc:subject><rdf:Bag><rdf:li>email</rdf:li><rdf:li>envelope</rdf:li><rdf:li>icon</rdf:li><rdf:li>letter</rdf:li><rdf:li>mail</rdf:li><rdf:li>postage</rdf:li></rdf:Bag></dc:subject></cc:Work><cc:License rdf:about="http://creativecommons.org/publicdomain/zero/1.0/"><cc:permits rdf:resource="http://creativecommons.org/ns#Reproduction"/><cc:permits rdf:resource="http://creativecommons.org/ns#Distribution"/><cc:permits rdf:resource="http://creativecommons.org/ns#DerivativeWorks"/></cc:License></rdf:RDF></metadata><g transform="translate(6,-18)"><polygon transform="translate(0,39)" points="148.69 142.69 229.32 80.375 229.32 205 68.055 205 68.055 80.375" fill="url(#a)"/><polygon transform="translate(0,39)" points="229.32 80.375 148.69 142.69 68.055 80.375" fill="#826a2a" stroke="#000" stroke-width="5"/><rect x="68.055" y="119.38" width="161.27" height="124.62" fill="none" stroke="#000" stroke-width="10"/><polyline transform="translate(0,39)" points="68.055 80.375 148.69 142.69 229.32 80.375" fill="none" stroke="#000" stroke-width="3"/><path d="m108.85 95.927c0-28.679-23.246-51.927-51.922-51.927-28.68 0-51.928 23.248-51.928 51.927s23.248 51.927 51.928 51.927c3.191 0 6.312-0.303 9.344-0.854l27.316 17.951-5.141-27.768c12.403-9.489 20.403-24.434 20.403-41.255z" fill="#812619" stroke="#000" stroke-width="10"/><g transform="matrix(.097526 0 0 -.097526 -96.586 115.38)" clip-rule="evenodd" image-rendering="optimizeQuality" shape-rendering="geometricPrecision"><path d="m1708.7 0 133.78 231.62 133.71-231.62z" fill="#f9ae2d"/><path d="m1708.7 0-133.75 231.62h267.53z" fill="#d28b25"/><path d="m1574.9 231.62 133.75 231.68 133.78-231.68z" fill="#936327"/><path d="m1708.7 463.3h-267.47l-267.56-463.3h267.56l267.47 463.3" fill="#767a7c"/></g></g></svg>';

    /**
     * Top menu 
     * @var \Ease\TWB4\Navbar
     */
    public $topMenu;

    /**
     * Digest Engine
     * 
     * @param string $subject
     */
    public function __construct($subject) {
        parent::__construct(null,['class'=>'accordion', 'id'=>'accordionExample']);
        $this->subject = $subject;
        $this->addHeading($subject);
        $this->shared = \Ease\Shared::instanced();
    }

    /**
     * Digest page Heading
     */
    public function addHeading($subject) {
        $this->addItem(new \Ease\Html\ATag('', '', ['name' => 'index']));
        $this->addItem(new \AbraFlexi\ui\CompanyLogo(['align' => 'right', 'id' => 'companylogo',
                    'height' => '50', 'title' => _('Company logo')]));
        $this->addItem(new \Ease\Html\H1Tag($subject));
        $prober = new \AbraFlexi\Company();
        $prober->logBanner(' AbraFlexi Digest ' . self::getAppVersion() . ' ' . $_SERVER['SCRIPT_FILENAME']);
        $infoRaw = $prober->getFlexiData();
        if (!empty($infoRaw) && !array_key_exists('success', $infoRaw)) {
            $info = \Ease\Functions::reindexArrayBy($infoRaw, 'dbNazev');
            $myCompany = $prober->getCompany();
            if (array_key_exists($myCompany, $info)) {
                $return = new \Ease\Html\ATag($prober->url . '/c/' . $myCompany,
                        $info[$myCompany]['nazev']);
            } else {
                $return = new \Ease\Html\ATag($prober->getApiURL(),
                        _('Connection Problem'));
            }

            $this->addItem(new \Ease\Html\StrongTag($return,
                            ['class' => 'companylink']));
        }

        $this->topMenu = $this->addItem(new \Ease\Html\NavTag('', ['class' => 'nav']));
    }

    /**
     * Include all classes in modules directory
     * 
     * @param \DateInterval $interval
     */
    public function dig($interval, $moduleDir) {
        $this->processModules(self::getModules($moduleDir), $interval);

        $this->addIndex();
        $this->addFoot();

        $shared = \Ease\Shared::instanced();
        $emailto = $shared->getConfigValue('EASE_MAILTO');
        if ($emailto) {
            $this->sendByMail($emailto);
        }
        $saveto = $shared->getConfigValue('SAVETO');
        if ($saveto) {
            $this->saveToHtml($saveto);
        }
    }

    /**
     * Process All modules in specified Dir
     * 
     * @param array $modules [classname=>filepath]
     * @param \DateTime|\DatePeriod $interval
     */
    public function processModules($modules, $interval) {
        foreach ($modules as $class => $classFile) {
            include_once $classFile;
            $module = new $class($interval);
            $saveto = $this->shared->getConfigValue('SAVETO');
            if ($module->process()) {
//                $this->addItem(new \Ease\Html\HrTag());
                $this->addToIndex($this->addItem($module));
                if ($saveto) {
                    $module->saveToHtml($saveto);
                }
            } else {
                $this->addStatusMessage(sprintf(_('Module %s do not found results'),
                                $class));
                if ($saveto) {
                    $module->fileCleanUP($saveto);
                }
            }
        }
    }
    
    /**
     * Process All modules in specified Dir
     * 
     * @param string $moduleDir path
     */
    public static function getModules($moduleDir) {
        $modules = [];
        if (is_array($moduleDir)) {
            foreach ($moduleDir as $module) {
                $modules = array_merge($modules, self::getModules($module));
            }
        } else {
            if (is_dir($moduleDir)) {
                $d = dir($moduleDir);
                while (false !== ($entry = $d->read())) {
                    if (is_file($moduleDir . '/' . $entry)) {
                        $class = pathinfo($entry, PATHINFO_FILENAME);
                        $modules[$class] = realpath($moduleDir . '/' . $entry);
                    }
                }
                $d->close();
            } else {
                if (is_file($moduleDir)) {
                    $class = pathinfo($moduleDir, PATHINFO_FILENAME);
                    $modules[$class] = realpath($moduleDir);
                } else {
                    \Ease\Shared::logger()->addToLog('Digestor', sprintf(_('Module dir %s is wrong'),
                                    $moduleDir), 'error');
                }
            }
        }
        return $modules;
    }

    /**
     * 
     * @param DigestModule $element
     */
    public function addToIndex($element) {
        $this->index[get_class($element)] = $element->heading();
    }

    /**
     * Add Index to digest
     */
    public function addIndex() {
        $this->addItem(new \Ease\Html\H1Tag(new \Ease\Html\ATag('', _('Index'),
                                ['name' => 'index2'])));
        $this->addItem(new \Ease\Html\HrTag());

        $index = new \Ease\Html\NavTag(null, ['class' => 'nav']);

        foreach ($this->index as $class => $heading) {
            $index->addItem(new \Ease\Html\ATag('#' . $class, $heading, ['class' => 'nav-link btn btn-light']));
            $this->topMenu->addItem(new \Ease\Html\ATag('#' . $class, $heading, ['class' => 'nav-link btn btn-light']));
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
     * Sent digest by mail 
     * 
     * @param string $mailto
     */
    public function sendByMail($mailto) {
        $postman = new Mailer($mailto, $this->subject);
        $postman->addItem($this);
        $postman->send();
    }

    /**
     * Save HTML digest
     * 
     * @param string $saveTo directory
     */
    public function saveToHtml($saveTo) {
        $filename = $saveTo . pathinfo($_SERVER['SCRIPT_FILENAME'],
                        PATHINFO_FILENAME) . '.html';
        $webPage = new \Ease\Html\HtmlTag(new \Ease\Html\SimpleHeadTag([
                    new \Ease\Html\TitleTag($this->subject),
                    '<style>' . Digestor::$purecss . Digestor::getCustomCss() . Digestor::getWebPageInlineCSS() . '</style>']));
        $webPage->addItem(new \Ease\Html\BodyTag($this));
        $this->addStatusMessage(sprintf(_('Saved to %s'), $filename),
                file_put_contents($filename, $webPage->getRendered()) ? 'success' : 'error');
    }

    static public function getWebPageInlineCSS() {
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
     * Obtain Custom CSS - THEME in digest.json
     * @return string
     */
    public static function getCustomCss() {

        $theme = \Ease\Shared::instanced()->getConfigValue('THEME');
        $cssfile = constant('STYLE_DIR') . '/' . $theme;
        return file_exists($cssfile) ? file_get_contents($cssfile) : '';
    }

    /**
     * Obtain Version of application
     * @return string
     */
    static public function getAppVersion() {
        $composerInfo = json_decode(file_get_contents('../composer.json'), true);
        return array_key_exists('version', $composerInfo) ? $composerInfo['version'] : 'dev-master';
    }

    /**
     * Page Bottom
     */
    public function addFoot() {
        $this->addItem(new \Ease\Html\HrTag());
        $this->addItem(new \Ease\Html\ImgTag('data:image/svg+xml;base64,' . base64_encode(self::$logo),
                        'Logo', ['align' => 'right', 'width' => '50']));
        $this->addItem(new \Ease\Html\SmallTag(new \Ease\Html\DivTag([_('Generated by'),
                            '&nbsp;', new \Ease\Html\ATag('https://github.com/VitexSoftware/AbraFlexi-Digest',
                                    _('AbraFlexi Digest') . ' ' . _('version') . ' ' . self::getAppVersion())])));

        $this->addItem(new \Ease\Html\SmallTag(new \Ease\Html\DivTag([_('(G) 2018-2021'),
                            '&nbsp;', new \Ease\Html\ATag('https://www.vitexsoftware.cz/',
                                    'Vitex Software')])));
    }

}
