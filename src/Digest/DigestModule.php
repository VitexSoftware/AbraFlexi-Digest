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
     * Flexibe Evidence Column(s) used to filter by date
     * @var string|array 
     */
    public $timeColumn = null;

    /**
     * Initial date to process
     * @var  \DateInterval
     */
    public $timeInterval = null;

    /**
     * Prepare condition, add header with anchors
     * 
     * @param \DateInterval $interval
     */
    public function __construct($interval)
    {
        if (!empty($interval) && $this->timeColumn) {
            if (is_array($this->timeColumn)) {
                $condParts = [];
                foreach ($this->timeColumn as $timeColumn) {
                    $condParts[$timeColumn] = $interval;
                }
                $this->condition = [\FlexiPeeHP\FlexiBeeRO::flexiUrl($condParts,
                        ' or ')];
            } else {
                $this->condition = [$this->timeColumn => $interval];
            }
        }
        $this->timeInterval = $interval;
        parent::__construct();
        $this->setTagID(get_class($this));
    }

    /**
     * Proccess data digging
     * 
     * @return boolean
     */
    public function process()
    {
        $this->addItem(new \Ease\Html\H2Tag(new \Ease\Html\ATag('#index',
            $this->heading(), ['name' => get_class($this)])));
        $this->addStatusMessage($this->heading());
        return $this->dig();
    }

    /**
     * Obtaining informations
     */
    public function dig()
    {
        $this->addItem(new \Ease\Html\ATag('https://www.vitexsoftware.cz/cenik.php',
            _('Please contact Vitex Software to make this module working.')));
        return true;
    }

    /**
     * Default Heading
     * 
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
        return array_key_exists('mena@showAs', $data) ? current(explode(':',
                    $data['mena@showAs'])) : \FlexiPeeHP\FlexiBeeRO::uncode($data['mena']);
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
     * FlexiBee date in human readable form 
     * 
     * @param string $flexiDate
     * 
     * @return string
     */
    public static function humanDate($flexiDate)
    {
        return \FlexiPeeHP\FlexiBeeRW::flexiDateToDateTime($flexiDate)->format('d. m. Y');
    }

    /**
     * Is Date between dates
     * 
     * @param DateTime $date Date that is to be checked if it falls between $startDate and $endDate
     * @param DateTime $startDate Date should be after this date to return true
     * @param DateTime $endDate Date should be before this date to return true
     * 
     * return bool
     */
    public static function isDateBetweenDates(\DateTime $date,
                                              \DateTime $startDate,
                                              \DateTime $endDate)
    {
        return $date > $startDate && $date < $endDate;
    }

    /**
     * Is datw within date interval
     * 
     * @param \FlexiPeeHP\Digest\DateTime $date
     * @param \DateInterval               $interval
     * 
     * @return boolean
     */
    public static function isDateWithinInterval(\DateTime $date,
                                                \DatePeriod $interval)
    {
        return self::isDateBetweenDates($date, $interval->getStartDate(),
                $interval->getEndDate());
    }

    /**
     * Is date subject of digest ?
     * 
     * @param \FlexiPeeHP\Digest\DateTime $date
     * 
     * @return boolean
     */
    public function isMyDate(\DateTime $date)
    {
        switch (get_class($this->timeInterval)) {
            case 'DatePeriod':
                $result = self::isDateWithinInterval($date, $this->timeInterval);
                break;
            case 'DateTime':
                $result = !date_diff($this->timeInterval,$date);
                break;

            default:
                $result = true;
                break;
        }
        return $result;
    }

    /**
     * Return Totals for serveral currencies
     * 
     * @param array $totals [currency=>amount,currency2=>amount2]
     * 
     * @return \Ease\Html\DivTag
     */
    public static function getTotalsDiv(array $totals)
    {
        $total = new \Ease\Html\DivTag();
        foreach ($totals as $currency => $amount) {
            $total->addItem(new \Ease\Html\DivTag(self::formatCurrency($amount).'&nbsp;'.\FlexiPeeHP\FlexiBeeRO::uncode($currency)));
        }
        return $total;
    }

    /**
     * Save HTML digest fragment
     * 
     * @param string $saveTo directory
     */
    public function saveToHtml($saveTo)
    {
        $filename = $saveTo.$this->getReportFilename();
        $this->addStatusMessage(sprintf(_('Module output Saved to %s'),
                $filename),
            file_put_contents($filename, $this->getRendered()) ? 'success' : 'error');
    }

    /**
     * Remove reportfile
     * 
     * @param string $saveTo
     */
    public function fileCleanUP($saveTo)
    {
        $filename = $saveTo.$this->getReportFilename();
        if (file_exists($filename)) {
            $this->addStatusMessage(sprintf(_('Module output %s wiped out'),
                    $filename), unlink($filename) ? 'success' : 'error');
        }
    }

    public function getReportFilename()
    {
        return pathinfo($_SERVER['SCRIPT_FILENAME'], PATHINFO_FILENAME).'_'.pathinfo(get_class($this),
                PATHINFO_FILENAME).'.html';
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
