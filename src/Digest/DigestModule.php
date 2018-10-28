<?php
/**
 * FlexiBee Digest
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018 Vitex Software
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
     * Which records we want to see ?
     * @param array $condition
     */
    public $condition = [];

    /**
     * Flexibe Evidence Column used to filter by date
     * @var string 
     */
    public $timeColumn = null;

    /**
     * 
     * @param type $interval
     */
    public function __construct($interval)
    {
        if (!empty($interval) && $this->timeColumn) {
            $this->condition = [$this->timeColumn => $interval];
        }

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
        $this->addItem(new \Ease\Html\ATag('https://www.vitexsoftware.cz/cenik.php',
            _('Please contact Vitex Software to make this module working.')));
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
     * Get Currency name
     * 
     * @param array $data
     * 
     * @return string
     */
    public static function getCurrency($data)
    {
        return current(explode(':', $data['mena@showAs']));
    }

    /**
     * Format Czech Currency
     * 
     * @param float $price
     * 
     * @return string
     */
    public static function formatCurrency($price)
    {
        return number_format($price, 2, ',', ' ');
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
