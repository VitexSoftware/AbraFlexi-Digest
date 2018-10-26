<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace FlexiPeeHP\Digest;

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
        $this->setTagID(get_class($this));
        $this->addItem(new \Ease\Html\HrTag());
        $this->addItem(new \Ease\Html\H2Tag($this->heading()));
        $this->dig();
        $this->addStatusMessage($this->heading());
    }

    /**
     * Obtaining informations
     */
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

    /**
     * Print progress log
     */
    public function finalize()
    {
        $this->addStatusMessage($this->heading(), 'debug');
        parent::finalize();
    }
}
