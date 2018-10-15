<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FlexiPeeHP\DigestMail;

/**
 * Description of DigestMod
 *
 * @author vitex
 */
class DigestModule extends \Ease\Html\DivTag implements DigestModuleInterface
{
    /**
     * 
     * @param \DateTime|\DateInterval $interval
     */
    public $interval = null;

    /**
     * 
     * @param type $interval
     */
    public function __construct($interval)
    {
        $this->interval = $interval;
        parent::__construct();
        $this->dig();
        $this->setTagID(get_class($this));
    }
    
    public function dig()
    {
        
    }

    /**
     * Default Heading
     * @return string
     */
    public function heading()
    {
        return _('No heading set');
    }
}
