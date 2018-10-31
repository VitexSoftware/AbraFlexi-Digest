<?php
/**
 * FlexiBee Digest Engine
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018 Vitex Software
 */

namespace FlexiPeeHP\Digest;

/**
 * Description of Digestor
 *
 * @author vitex
 */
class Digestor extends \Ease\Html\DivTag
{
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
    static $purecss = '/*!
Pure v1.0.0
Copyright 2013 Yahoo!
Licensed under the BSD License.
https://github.com/yahoo/pure/blob/master/LICENSE.md
normalize.css v^3.0 | MIT License | git.io/normalize
Copyright (c) Nicolas Gallagher and Jonathan Neal
normalize.css v3.0.3 | MIT License | github.com/necolas/normalize.css */.pure-button:focus,a:active,a:hover{outline:0}.pure-table,table{border-collapse:collapse;border-spacing:0}html{font-family:sans-serif;-ms-text-size-adjust:100%;-webkit-text-size-adjust:100%}body{margin:0}article,aside,details,figcaption,figure,footer,header,hgroup,main,menu,nav,section,summary{display:block}audio,canvas,progress,video{display:inline-block;vertical-align:baseline}audio:not([controls]){display:none;height:0}[hidden],template{display:none}a{background-color:transparent}abbr[title]{border-bottom:1px dotted}b,optgroup,strong{font-weight:700}dfn{font-style:italic}h1{font-size:2em;margin:.67em 0}mark{background:#ff0;color:#000}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:baseline}sup{top:-.5em}sub{bottom:-.25em}img{border:0}svg:not(:root){overflow:hidden}figure{margin:1em 40px}hr{box-sizing:content-box;height:0}pre,textarea{overflow:auto}code,kbd,pre,samp{font-family:monospace,monospace;font-size:1em}button,input,optgroup,select,textarea{color:inherit;font:inherit;margin:0}.pure-button,input{line-height:normal}button{overflow:visible}button,select{text-transform:none}button,html input[type=button],input[type=reset],input[type=submit]{-webkit-appearance:button;cursor:pointer}button[disabled],html input[disabled]{cursor:default}button::-moz-focus-inner,input::-moz-focus-inner{border:0;padding:0}input[type=checkbox],input[type=radio]{box-sizing:border-box;padding:0}input[type=number]::-webkit-inner-spin-button,input[type=number]::-webkit-outer-spin-button{height:auto}input[type=search]{-webkit-appearance:textfield;box-sizing:content-box}.pure-button,.pure-form input:not([type]),.pure-menu{box-sizing:border-box}input[type=search]::-webkit-search-cancel-button,input[type=search]::-webkit-search-decoration{-webkit-appearance:none}fieldset{border:1px solid silver;margin:0 2px;padding:.35em .625em .75em}legend,td,th{padding:0}legend{border:0}.hidden,[hidden]{display:none!important}.pure-img{max-width:100%;height:auto;display:block}.pure-g{letter-spacing:-.31em;text-rendering:optimizespeed;font-family:FreeSans,Arimo,"Droid Sans",Helvetica,Arial,sans-serif;display:-webkit-box;display:-webkit-flex;display:-ms-flexbox;display:flex;-webkit-flex-flow:row wrap;-ms-flex-flow:row wrap;flex-flow:row wrap;-webkit-align-content:flex-start;-ms-flex-line-pack:start;align-content:flex-start}@media all and (-ms-high-contrast:none),(-ms-high-contrast:active){table .pure-g{display:block}}.opera-only :-o-prefocus,.pure-g{word-spacing:-.43em}.pure-u,.pure-u-1,.pure-u-1-1,.pure-u-1-12,.pure-u-1-2,.pure-u-1-24,.pure-u-1-3,.pure-u-1-4,.pure-u-1-5,.pure-u-1-6,.pure-u-1-8,.pure-u-10-24,.pure-u-11-12,.pure-u-11-24,.pure-u-12-24,.pure-u-13-24,.pure-u-14-24,.pure-u-15-24,.pure-u-16-24,.pure-u-17-24,.pure-u-18-24,.pure-u-19-24,.pure-u-2-24,.pure-u-2-3,.pure-u-2-5,.pure-u-20-24,.pure-u-21-24,.pure-u-22-24,.pure-u-23-24,.pure-u-24-24,.pure-u-3-24,.pure-u-3-4,.pure-u-3-5,.pure-u-3-8,.pure-u-4-24,.pure-u-4-5,.pure-u-5-12,.pure-u-5-24,.pure-u-5-5,.pure-u-5-6,.pure-u-5-8,.pure-u-6-24,.pure-u-7-12,.pure-u-7-24,.pure-u-7-8,.pure-u-8-24,.pure-u-9-24{letter-spacing:normal;word-spacing:normal;vertical-align:top;text-rendering:auto;display:inline-block;zoom:1}.pure-g [class*=pure-u]{font-family:sans-serif}.pure-u-1-24{width:4.1667%}.pure-u-1-12,.pure-u-2-24{width:8.3333%}.pure-u-1-8,.pure-u-3-24{width:12.5%}.pure-u-1-6,.pure-u-4-24{width:16.6667%}.pure-u-1-5{width:20%}.pure-u-5-24{width:20.8333%}.pure-u-1-4,.pure-u-6-24{width:25%}.pure-u-7-24{width:29.1667%}.pure-u-1-3,.pure-u-8-24{width:33.3333%}.pure-u-3-8,.pure-u-9-24{width:37.5%}.pure-u-2-5{width:40%}.pure-u-10-24,.pure-u-5-12{width:41.6667%}.pure-u-11-24{width:45.8333%}.pure-u-1-2,.pure-u-12-24{width:50%}.pure-u-13-24{width:54.1667%}.pure-u-14-24,.pure-u-7-12{width:58.3333%}.pure-u-3-5{width:60%}.pure-u-15-24,.pure-u-5-8{width:62.5%}.pure-u-16-24,.pure-u-2-3{width:66.6667%}.pure-u-17-24{width:70.8333%}.pure-u-18-24,.pure-u-3-4{width:75%}.pure-u-19-24{width:79.1667%}.pure-u-4-5{width:80%}.pure-u-20-24,.pure-u-5-6{width:83.3333%}.pure-u-21-24,.pure-u-7-8{width:87.5%}.pure-u-11-12,.pure-u-22-24{width:91.6667%}.pure-u-23-24{width:95.8333%}.pure-u-1,.pure-u-1-1,.pure-u-24-24,.pure-u-5-5{width:100%}.pure-button{display:inline-block;zoom:1;white-space:nowrap;vertical-align:middle;text-align:center;cursor:pointer;-webkit-user-drag:none;-webkit-user-select:none;-moz-user-select:none;-ms-user-select:none;user-select:none}.pure-button::-moz-focus-inner{padding:0;border:0}.pure-button-group{letter-spacing:-.31em;text-rendering:optimizespeed}.opera-only :-o-prefocus,.pure-button-group{word-spacing:-.43em}.pure-button{font-family:inherit;font-size:100%;padding:.5em 1em;color:#444;color:rgba(0,0,0,.8);border:1px solid #999;border:transparent;background-color:#E6E6E6;text-decoration:none;border-radius:2px}.pure-button-hover,.pure-button:focus,.pure-button:hover{filter:alpha(opacity=90);background-image:-webkit-linear-gradient(transparent,rgba(0,0,0,.05) 40%,rgba(0,0,0,.1));background-image:linear-gradient(transparent,rgba(0,0,0,.05) 40%,rgba(0,0,0,.1))}.pure-button-active,.pure-button:active{box-shadow:0 0 0 1px rgba(0,0,0,.15) inset,0 0 6px rgba(0,0,0,.2) inset;border-color:#000\9}.pure-button-disabled,.pure-button-disabled:active,.pure-button-disabled:focus,.pure-button-disabled:hover,.pure-button[disabled]{border:none;background-image:none;filter:alpha(opacity=40);opacity:.4;cursor:not-allowed;box-shadow:none;pointer-events:none}.pure-button-hidden{display:none}.pure-button-primary,.pure-button-selected,a.pure-button-primary,a.pure-button-selected{background-color:#0078e7;color:#fff}.pure-button-group .pure-button{letter-spacing:normal;word-spacing:normal;vertical-align:top;text-rendering:auto;margin:0;border-radius:0;border-right:1px solid #111;border-right:1px solid rgba(0,0,0,.2)}.pure-button-group .pure-button:first-child{border-top-left-radius:2px;border-bottom-left-radius:2px}.pure-button-group .pure-button:last-child{border-top-right-radius:2px;border-bottom-right-radius:2px;border-right:none}.pure-form input[type=password],.pure-form input[type=email],.pure-form input[type=url],.pure-form input[type=date],.pure-form input[type=month],.pure-form input[type=time],.pure-form input[type=datetime],.pure-form input[type=datetime-local],.pure-form input[type=week],.pure-form input[type=tel],.pure-form input[type=color],.pure-form input[type=number],.pure-form input[type=search],.pure-form input[type=text],.pure-form select,.pure-form textarea{padding:.5em .6em;display:inline-block;border:1px solid #ccc;box-shadow:inset 0 1px 3px #ddd;border-radius:4px;vertical-align:middle;box-sizing:border-box}.pure-form input:not([type]){padding:.5em .6em;display:inline-block;border:1px solid #ccc;box-shadow:inset 0 1px 3px #ddd;border-radius:4px}.pure-form input[type=color]{padding:.2em .5em}.pure-form input:not([type]):focus,.pure-form input[type=password]:focus,.pure-form input[type=email]:focus,.pure-form input[type=url]:focus,.pure-form input[type=date]:focus,.pure-form input[type=month]:focus,.pure-form input[type=time]:focus,.pure-form input[type=datetime]:focus,.pure-form input[type=datetime-local]:focus,.pure-form input[type=week]:focus,.pure-form input[type=tel]:focus,.pure-form input[type=color]:focus,.pure-form input[type=number]:focus,.pure-form input[type=search]:focus,.pure-form input[type=text]:focus,.pure-form select:focus,.pure-form textarea:focus{outline:0;border-color:#129FEA}.pure-form input[type=file]:focus,.pure-form input[type=checkbox]:focus,.pure-form input[type=radio]:focus{outline:#129FEA auto 1px}.pure-form .pure-checkbox,.pure-form .pure-radio{margin:.5em 0;display:block}.pure-form input:not([type])[disabled],.pure-form input[type=password][disabled],.pure-form input[type=email][disabled],.pure-form input[type=url][disabled],.pure-form input[type=date][disabled],.pure-form input[type=month][disabled],.pure-form input[type=time][disabled],.pure-form input[type=datetime][disabled],.pure-form input[type=datetime-local][disabled],.pure-form input[type=week][disabled],.pure-form input[type=tel][disabled],.pure-form input[type=color][disabled],.pure-form input[type=number][disabled],.pure-form input[type=search][disabled],.pure-form input[type=text][disabled],.pure-form select[disabled],.pure-form textarea[disabled]{cursor:not-allowed;background-color:#eaeded;color:#cad2d3}.pure-form input[readonly],.pure-form select[readonly],.pure-form textarea[readonly]{background-color:#eee;color:#777;border-color:#ccc}.pure-form input:focus:invalid,.pure-form select:focus:invalid,.pure-form textarea:focus:invalid{color:#b94a48;border-color:#e9322d}.pure-form input[type=file]:focus:invalid:focus,.pure-form input[type=checkbox]:focus:invalid:focus,.pure-form input[type=radio]:focus:invalid:focus{outline-color:#e9322d}.pure-form select{height:2.25em;border:1px solid #ccc;background-color:#fff}.pure-form select[multiple]{height:auto}.pure-form label{margin:.5em 0 .2em}.pure-form fieldset{margin:0;padding:.35em 0 .75em;border:0}.pure-form legend{display:block;width:100%;padding:.3em 0;margin-bottom:.3em;color:#333;border-bottom:1px solid #e5e5e5}.pure-form-stacked input:not([type]),.pure-form-stacked input[type=password],.pure-form-stacked input[type=email],.pure-form-stacked input[type=url],.pure-form-stacked input[type=date],.pure-form-stacked input[type=month],.pure-form-stacked input[type=time],.pure-form-stacked input[type=datetime],.pure-form-stacked input[type=datetime-local],.pure-form-stacked input[type=week],.pure-form-stacked input[type=tel],.pure-form-stacked input[type=color],.pure-form-stacked input[type=file],.pure-form-stacked input[type=number],.pure-form-stacked input[type=search],.pure-form-stacked input[type=text],.pure-form-stacked label,.pure-form-stacked select,.pure-form-stacked textarea{display:block;margin:.25em 0}.pure-form-aligned .pure-help-inline,.pure-form-aligned input,.pure-form-aligned select,.pure-form-aligned textarea,.pure-form-message-inline{display:inline-block;vertical-align:middle}.pure-form-aligned textarea{vertical-align:top}.pure-form-aligned .pure-control-group{margin-bottom:.5em}.pure-form-aligned .pure-control-group label{text-align:right;display:inline-block;vertical-align:middle;width:10em;margin:0 1em 0 0}.pure-form-aligned .pure-controls{margin:1.5em 0 0 11em}.pure-form .pure-input-rounded,.pure-form input.pure-input-rounded{border-radius:2em;padding:.5em 1em}.pure-form .pure-group fieldset{margin-bottom:10px}.pure-form .pure-group input,.pure-form .pure-group textarea{display:block;padding:10px;margin:0 0 -1px;border-radius:0;position:relative;top:-1px}.pure-form .pure-group input:focus,.pure-form .pure-group textarea:focus{z-index:3}.pure-form .pure-group input:first-child,.pure-form .pure-group textarea:first-child{top:1px;border-radius:4px 4px 0 0;margin:0}.pure-form .pure-group input:first-child:last-child,.pure-form .pure-group textarea:first-child:last-child{top:1px;border-radius:4px;margin:0}.pure-form .pure-group input:last-child,.pure-form .pure-group textarea:last-child{top:-2px;border-radius:0 0 4px 4px;margin:0}.pure-form .pure-group button{margin:.35em 0}.pure-form .pure-input-1{width:100%}.pure-form .pure-input-3-4{width:75%}.pure-form .pure-input-2-3{width:66%}.pure-form .pure-input-1-2{width:50%}.pure-form .pure-input-1-3{width:33%}.pure-form .pure-input-1-4{width:25%}.pure-form .pure-help-inline,.pure-form-message-inline{display:inline-block;padding-left:.3em;color:#666;vertical-align:middle;font-size:.875em}.pure-form-message{display:block;color:#666;font-size:.875em}@media only screen and (max-width :480px){.pure-form button[type=submit]{margin:.7em 0 0}.pure-form input:not([type]),.pure-form input[type=password],.pure-form input[type=email],.pure-form input[type=url],.pure-form input[type=date],.pure-form input[type=month],.pure-form input[type=time],.pure-form input[type=datetime],.pure-form input[type=datetime-local],.pure-form input[type=week],.pure-form input[type=tel],.pure-form input[type=color],.pure-form input[type=number],.pure-form input[type=search],.pure-form input[type=text],.pure-form label{margin-bottom:.3em;display:block}.pure-group input:not([type]),.pure-group input[type=password],.pure-group input[type=email],.pure-group input[type=url],.pure-group input[type=date],.pure-group input[type=month],.pure-group input[type=time],.pure-group input[type=datetime],.pure-group input[type=datetime-local],.pure-group input[type=week],.pure-group input[type=tel],.pure-group input[type=color],.pure-group input[type=number],.pure-group input[type=search],.pure-group input[type=text]{margin-bottom:0}.pure-form-aligned .pure-control-group label{margin-bottom:.3em;text-align:left;display:block;width:100%}.pure-form-aligned .pure-controls{margin:1.5em 0 0}.pure-form .pure-help-inline,.pure-form-message,.pure-form-message-inline{display:block;font-size:.75em;padding:.2em 0 .8em}}.pure-menu-fixed{position:fixed;left:0;top:0;z-index:3}.pure-menu-item,.pure-menu-list{position:relative}.pure-menu-list{list-style:none;margin:0;padding:0}.pure-menu-item{padding:0;margin:0;height:100%}.pure-menu-heading,.pure-menu-link{display:block;text-decoration:none;white-space:nowrap}.pure-menu-horizontal{width:100%;white-space:nowrap}.pure-menu-horizontal .pure-menu-list{display:inline-block}.pure-menu-horizontal .pure-menu-heading,.pure-menu-horizontal .pure-menu-item,.pure-menu-horizontal .pure-menu-separator{display:inline-block;zoom:1;vertical-align:middle}.pure-menu-item .pure-menu-item{display:block}.pure-menu-children{display:none;position:absolute;left:100%;top:0;margin:0;padding:0;z-index:3}.pure-menu-horizontal .pure-menu-children{left:0;top:auto;width:inherit}.pure-menu-active>.pure-menu-children,.pure-menu-allow-hover:hover>.pure-menu-children{display:block;position:absolute}.pure-menu-has-children>.pure-menu-link:after{padding-left:.5em;content:"\25B8";font-size:small}.pure-menu-horizontal .pure-menu-has-children>.pure-menu-link:after{content:"\25BE"}.pure-menu-scrollable{overflow-y:scroll;overflow-x:hidden}.pure-menu-scrollable .pure-menu-list{display:block}.pure-menu-horizontal.pure-menu-scrollable .pure-menu-list{display:inline-block}.pure-menu-horizontal.pure-menu-scrollable{white-space:nowrap;overflow-y:hidden;overflow-x:auto;-ms-overflow-style:none;-webkit-overflow-scrolling:touch;padding:.5em 0}.pure-menu-horizontal.pure-menu-scrollable::-webkit-scrollbar{display:none}.pure-menu-horizontal .pure-menu-children .pure-menu-separator,.pure-menu-separator{background-color:#ccc;height:1px;margin:.3em 0}.pure-menu-horizontal .pure-menu-separator{width:1px;height:1.3em;margin:0 .3em}.pure-menu-horizontal .pure-menu-children .pure-menu-separator{display:block;width:auto}.pure-menu-heading{text-transform:uppercase;color:#565d64}.pure-menu-link{color:#777}.pure-menu-children{background-color:#fff}.pure-menu-disabled,.pure-menu-heading,.pure-menu-link{padding:.5em 1em}.pure-menu-disabled{opacity:.5}.pure-menu-disabled .pure-menu-link:hover{background-color:transparent}.pure-menu-active>.pure-menu-link,.pure-menu-link:focus,.pure-menu-link:hover{background-color:#eee}.pure-menu-selected .pure-menu-link,.pure-menu-selected .pure-menu-link:visited{color:#000}.pure-table{empty-cells:show;border:1px solid #cbcbcb}.pure-table caption{color:#000;font:italic 85%/1 arial,sans-serif;padding:1em 0;text-align:center}.pure-table td,.pure-table th{border-left:1px solid #cbcbcb;border-width:0 0 0 1px;font-size:inherit;margin:0;overflow:visible;padding:.5em 1em}.pure-table td:first-child,.pure-table th:first-child{border-left-width:0}.pure-table thead{background-color:#e0e0e0;color:#000;text-align:left;vertical-align:bottom}.pure-table td{background-color:transparent}.pure-table-odd td,.pure-table-striped tr:nth-child(2n-1) td{background-color:#f2f2f2}.pure-table-bordered td{border-bottom:1px solid #cbcbcb}.pure-table-bordered tbody>tr:last-child>td{border-bottom-width:0}.pure-table-horizontal td,.pure-table-horizontal th{border-width:0 0 1px;border-bottom:1px solid #cbcbcb}.pure-table-horizontal tbody>tr:last-child>td{border-bottom-width:0}';

    /**
     * App Logo
     * @var string 
     */
    static $logo = '<svg width="250" height="250" enable-background="new -0.161 -0.355 237 211" version="1.1" viewBox="-0.161 -0.355 250 250" xml:space="preserve" xmlns="http://www.w3.org/2000/svg" xmlns:cc="http://creativecommons.org/ns#" xmlns:dc="http://purl.org/dc/elements/1.1/" xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"><linearGradient id="a" x1="148.69" x2="148.69" y1="80.375" y2="205" gradientUnits="userSpaceOnUse"><stop stop-color="#CDAD2D" offset="0"/><stop stop-color="#917D2E" offset="1"/></linearGradient><metadata><rdf:RDF><cc:Work><dc:format>image/svg+xml</dc:format><dc:type rdf:resource="http://purl.org/dc/dcmitype/StillImage"/><cc:license rdf:resource="http://creativecommons.org/publicdomain/zero/1.0/"/><dc:publisher><cc:Agent rdf:about="http://openclipart.org/"><dc:title>Openclipart</dc:title></cc:Agent></dc:publisher><dc:title>Email</dc:title><dc:date>2008-06-14T10:49:47</dc:date><dc:description>Email icon.</dc:description><dc:source>https://openclipart.org/detail/17371/email-by-ytknick</dc:source><dc:creator><cc:Agent><dc:title>ytknick</dc:title></cc:Agent></dc:creator><dc:subject><rdf:Bag><rdf:li>email</rdf:li><rdf:li>envelope</rdf:li><rdf:li>icon</rdf:li><rdf:li>letter</rdf:li><rdf:li>mail</rdf:li><rdf:li>postage</rdf:li></rdf:Bag></dc:subject></cc:Work><cc:License rdf:about="http://creativecommons.org/publicdomain/zero/1.0/"><cc:permits rdf:resource="http://creativecommons.org/ns#Reproduction"/><cc:permits rdf:resource="http://creativecommons.org/ns#Distribution"/><cc:permits rdf:resource="http://creativecommons.org/ns#DerivativeWorks"/></cc:License></rdf:RDF></metadata><g transform="translate(6,-18)"><polygon transform="translate(0,39)" points="148.69 142.69 229.32 80.375 229.32 205 68.055 205 68.055 80.375" fill="url(#a)"/><polygon transform="translate(0,39)" points="229.32 80.375 148.69 142.69 68.055 80.375" fill="#826a2a" stroke="#000" stroke-width="5"/><rect x="68.055" y="119.38" width="161.27" height="124.62" fill="none" stroke="#000" stroke-width="10"/><polyline transform="translate(0,39)" points="68.055 80.375 148.69 142.69 229.32 80.375" fill="none" stroke="#000" stroke-width="3"/><path d="m108.85 95.927c0-28.679-23.246-51.927-51.922-51.927-28.68 0-51.928 23.248-51.928 51.927s23.248 51.927 51.928 51.927c3.191 0 6.312-0.303 9.344-0.854l27.316 17.951-5.141-27.768c12.403-9.489 20.403-24.434 20.403-41.255z" fill="#812619" stroke="#000" stroke-width="10"/><g transform="matrix(.097526 0 0 -.097526 -96.586 115.38)" clip-rule="evenodd" image-rendering="optimizeQuality" shape-rendering="geometricPrecision"><path d="m1708.7 0 133.78 231.62 133.71-231.62z" fill="#f9ae2d"/><path d="m1708.7 0-133.75 231.62h267.53z" fill="#d28b25"/><path d="m1574.9 231.62 133.75 231.68 133.78-231.68z" fill="#936327"/><path d="m1708.7 463.3h-267.47l-267.56-463.3h267.56l267.47 463.3" fill="#767a7c"/></g></g></svg>';

    /**
     * Top menu 
     * @var \Ease\Html\DivTag 
     */
    public $topMenu;

    /**
     * Digest Engine
     * @param string $subject
     */
    public function __construct($subject)
    {
        parent::__construct();
        $this->subject = $subject;
        $this->addHeading($subject);
    }

    /**
     * Digest page Heading
     */
    public function addHeading($subject)
    {
        $this->addItem(new \Ease\Html\ATag('', '', ['name' => 'index']));
        $this->addItem(new \FlexiPeeHP\ui\CompanyLogo(['align' => 'right', 'id' => 'companylogo',
            'height' => '50', 'title' => _('Company logo')]));
        $this->addItem(new \Ease\Html\H1Tag($subject));
        $prober  = new \FlexiPeeHP\Company();
        $prober->logBanner(' FlexiBee Digest '.self::getAppVersion().' '.$_SERVER['SCRIPT_FILENAME']);
        $infoRaw = $prober->getFlexiData();
        if (count($infoRaw) && !array_key_exists('success', $infoRaw)) {
            $info      = self::reindexArrayBy($infoRaw, 'dbNazev');
            $myCompany = $prober->getCompany();
            if (array_key_exists($myCompany, $info)) {
                $return = new \Ease\Html\ATag($prober->url.'/c/'.$myCompany,
                    $info[$myCompany]['nazev']);
            } else {
                $return = new \Ease\Html\ATag($prober->getApiURL(),
                    _('Connection Problem'));
            }
        }

        $this->addItem(new \Ease\Html\StrongTag($return,
            ['class' => 'companylink']));
        $this->topMenu = $this->addItem(new \Ease\Html\DivTag(null,
            ['class' => 'topmenu']));
    }

    /**
     * Include all classes in modules directory
     * 
     * @param \DateInterval $interval
     */
    public function dig($interval, $moduleDir)
    {
        if (is_dir($moduleDir)) {
            $d     = dir($moduleDir);
            while (false !== ($entry = $d->read())) {
                if ($entry[0] == '.') {
                    continue;
                }
                include_once $moduleDir.'/'.$entry;
                $class = pathinfo($entry, PATHINFO_FILENAME);
                $this->addToIndex($this->addItem(new $class($interval)));
            }
            $d->close();
        } else {
            $this->addStatusMessage(sprintf(_('Module dir %s is wrong'),
                    $moduleDir), 'error');
        }
        $this->addIndex();
        $this->addFoot();

        $shared  = \Ease\Shared::instanced();
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
     * 
     * @param DigestModule $element
     */
    public function addToIndex($element)
    {
        $this->index[get_class($element)] = $element->heading();
    }

    public function addIndex()
    {
        $this->addItem(new \Ease\Html\H1Tag(new \Ease\Html\ATag('', _('Index'),
            ['name' => 'index2'])));
        $this->addItem(new \Ease\Html\HrTag());

        $index = new \Ease\Html\UlTag(null, ['class' => 'pure-menu-list']);

        foreach ($this->index as $class => $heading) {
            $index->addItemSmart(new \Ease\Html\ATag('#'.$class, $heading,
                ['class' => 'pure-menu-link']),
                ['class' => 'pure-menu-item', 'style' => 'height: inherit']);

            $this->topMenu->addItem(new \Ease\Html\ATag('#'.$class, $heading,
                ['class' => 'topmenu-item']));
        }

        $this->addItem(new \Ease\Html\DivTag($index,
            ['class' => 'pure-menu', 'css' => 'display: inline-block;']));
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
    public function sendByMail($mailto)
    {
        $postman = new Mailer($mailto, $this->subject);
        $postman->addItem($this);
        $postman->send();
    }

    /**
     * Save HTML digest
     * @param string $saveTo directory
     */
    public function saveToHtml($saveTo)
    {
        $filename = $saveTo.pathinfo($_SERVER['SCRIPT_FILENAME'],
                PATHINFO_FILENAME).'.html';
        $webPage  = new \Ease\Html\HtmlTag(new \Ease\Html\SimpleHeadTag([
            new \Ease\Html\TitleTag($this->subject),
            '<style>'.Digestor::$purecss.Digestor::getCustomCss().'</style>']));
        $webPage->addItem(new \Ease\Html\BodyTag($this));
        $this->addStatusMessage(sprintf(_('Saved to %s'), $filename),
            file_put_contents($filename, $webPage->getRendered()) ? 'success' : 'error');
    }

    /**
     * Obtain Custom CSS - THEME in digest.json
     * @return string
     */
    public static function getCustomCss()
    {

        $theme   = \Ease\Shared::instanced()->getConfigValue('THEME');
        $cssfile = constant('STYLE_DIR').'/'.$theme.'.css';
        return file_exists($cssfile) ? file_get_contents($cssfile) : '';
    }

    /**
     * Obtain Version of application
     * @return string
     */
    static public function getAppVersion()
    {
        $composerInfo = json_decode( file_get_contents('../composer.json'), true);
        return array_key_exists('version', $composerInfo) ? $composerInfo['version']
                : 'dev-master';
    }

    /**
     * Page Bottom
     */
    public function addFoot()
    {
        $this->addItem(new \Ease\Html\HrTag());
        $this->addItem(new \Ease\Html\ImgTag('data:image/svg+xml;base64,'.base64_encode(self::$logo),
            'Logo', ['align' => 'right', 'width' => '50']));
        $this->addItem(new \Ease\Html\SmallTag(new \Ease\Html\DivTag([_('Generated by'),
            '&nbsp;', new \Ease\Html\ATag('https://github.com/VitexSoftware/FlexiBee-Digest',
                _('FlexiBee Digest').' '._('version').' '.self::getAppVersion())])));

        $this->addItem(new \Ease\Html\SmallTag(new \Ease\Html\DivTag([_('(G) 2018'),
            '&nbsp;', new \Ease\Html\ATag('https://www.vitexsoftware.cz/',
                'Vitex Software')])));
    }
}
