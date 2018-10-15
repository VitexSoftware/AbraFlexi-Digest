<?php

namespace FlexiPeeHP\DigestMail;

/**
 * FlexiBee Digest Mailer
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2017 Vitex Software
 */
class Mailer extends \Ease\Mailer
{
    public $moduleDir = null;

    /**
     * 
     * @param type $emailAddress
     * @param type $subject
     * @param type $moduleDir
     */
    public function __construct($emailAddress, $subject, $moduleDir)
    {
        $this->moduleDir = $moduleDir;
        $digger          = new \FlexiPeeHP\FlexiBeeRO(null, ['offline' => true]);
        parent::__construct($emailAddress, $subject);
    }

    public function dig($interval)
    {
        if (is_dir($this->moduleDir)) {
            $d     = dir($this->moduleDir);
            while (false !== ($entry = $d->read())) {
                if ($entry[0] == '.') {
                    continue;
                }
                include_once $this->moduleDir.'/'.$entry;
                $class = pathinfo($entry, PATHINFO_FILENAME);
                $this->addItem(new $class($interval));
            }
            $d->close();
        }
    }
}
