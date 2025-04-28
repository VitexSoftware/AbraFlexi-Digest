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

/**
 * Description of DigestMod.
 *
 * @author vitex
 */
class DigestModule extends DivTag implements DigestModuleInterface
{
    /**
     * Which records we want to see ?
     *
     * @param array<string, string> $condition
     */
    public $condition = ['limit' => '0'];

    /**
     * AbraFlexi Evidence Column(s) used to filter by date.
     */
    public array|string $timeColumn;

    /**
     * Initial date to process.
     */
    public \DatePeriod $probePeriod;

    /**
     * Prepare condition, add header with anchors.
     */
    public function __construct(\DatePeriod $period)
    {
        if (!empty($period) && $this->timeColumn) {
            if (\is_array($this->timeColumn)) {
                $condParts = [];

                foreach ($this->timeColumn as $timeColumn) {
                    $condParts[$timeColumn] = $period;
                }

                $this->condition = [\AbraFlexi\Functions::flexiUrl(
                    $condParts,
                    ' or ',
                )];
            } else {
                $this->condition = [$this->timeColumn => $period];
            }
        }

        $this->probePeriod = $period;
        parent::__construct(null, ['class' => 'card']);
        $this->setTagID('module'.\get_class($this));
        $this->addCSS('.module-result {  border: 1px green solid; margin: 20px; padding: 20px }');
    }

    /**
     * Process data digging.
     *
     * @return bool
     */
    public function process()
    {
        $this->addItem(new \Ease\Html\ATag('', '', ['id' => \get_class($this)]));
        $this->addItem(new DivTag(
            new \Ease\Html\H2Tag(
                new \Ease\Html\ButtonTag($this->heading(), [
                    'class' => 'btn btn-link btn-block text-left collapsed',
                    'type' => 'button',
                    'data-toggle' => 'collapse',
                    'data-target' => '#collapse'.\get_class($this),
                    'aria-expanded' => 'false',
                    'aria-controls' => 'collapse'.\get_class($this)]),
                ['class' => 'mb-0'],
            ),
            ['class' => 'card-header', 'id' => 'heading'.\get_class($this)],
        ));
        $this->addStatusMessage($this->heading());

        return $this->dig();
    }

    /**
     * Collapsible card div.
     *
     * @param mixed $content
     *
     * @return DivTag
     */
    public function cardBody($content)
    {
        return new DivTag(new DivTag($content, ['class' => 'card-body']), [
            'id' => 'collapse'.\get_class($this),
            'class' => 'Xcollapse show',
            'aria-labelledby' => 'heading'.\get_class($this),
            'data-parent' => '#accordionExample',
        ]);
    }

    /**
     * Obtaining information.
     *
     * @return bool dig success
     */
    public function dig(): bool
    {
        $this->addItem(new \Ease\Html\ATag(
            'https://www.vitexsoftware.cz/kontakt.php',
            _('Please contact Vitex Software to make this module working.'),
        ));

        return false;
    }

    /**
     * Return Pure data (no markup).
     *
     * @return array<mixed>
     */
    public function digJson(): array
    {
        $this->addStatusMessage(_('Module does not support JSON mode'), 'debug');

        return [];
    }

    /**
     * Default module Heading.
     */
    public function heading(): string
    {
        return _('No heading set');
    }

    /**
     * Get Currency name.
     *
     * @param array<string, string> $data
     *
     * @return string
     */
    public static function getCurrency(array $data)
    {
        return \AbraFlexi\Functions::uncode((string) (string) $data['mena']);
    }

    /**
     * Get Amount.
     *
     * @return float
     */
    public static function getAmount(array $data)
    {
        return \array_key_exists('sumCelkem', $data) ? $data['sumCelkem'] : 0.0;
    }

    /**
     * Format Czech Currency.
     */
    public static function formatCurrency(float $price): string
    {
        return number_format($price, 2, ',', ' ');
    }

    /**
     * AbraFlexi date in human readable form.
     *
     * @param string $flexiDate
     *
     * @return string
     */
    public static function humanDate($flexiDate)
    {
        return \is_string($flexiDate) ? \AbraFlexi\RW::flexiDateToDateTime($flexiDate)->format('d. m. Y') : $flexiDate->format('d. m. Y');
    }

    /**
     * Is Date between dates.
     *
     * @param \DateTime $date      Date that is to be checked if it falls between $startDate and $endDate
     * @param \DateTime $startDate Date should be after this date to return true
     * @param \DateTime $endDate   Date should be before this date to return true
     *
     * @return bool
     */
    public static function isDateBetweenDates(
        \DateTime $date,
        \DateTime $startDate,
        \DateTime $endDate,
    ) {
        return $date > $startDate && $date < $endDate;
    }

    /**
     * Is Date within interval.
     *
     * @param \DateTime   $date     Date that is to be checked if it falls within the interval
     * @param \DatePeriod $interval DatePeriod object that represents the interval
     */
    public static function isDateWithinInterval(
        \DateTime $date,
        \DatePeriod $interval,
    ): bool {
        return self::isDateBetweenDates(
            $date,
            $interval->getStartDate(),
            $interval->getEndDate(),
        );
    }

    /**
     * Is date subject of digest ?
     *
     * @return bool
     */
    public function isMyDate(\DateTime $date)
    {
        switch (\get_class($this->probePeriod)) {
            case 'DatePeriod':
                $result = self::isDateWithinInterval($date, $this->probePeriod);

                break;
            case 'DateTime':
                $result = !date_diff($this->probePeriod, $date);

                break;

            default:
                $result = true;

                break;
        }

        return $result;
    }

    /**
     * Get Price.
     *
     * @return float
     */
    public static function getPrice(array $data)
    {
        return \array_key_exists('sumCelkem', $data) ? $data['sumCelkem'] : 0;
    }

    /**
     * Return Totals for several currencies.
     *
     * @param array<string, float> $totals [currency=>amount,currency2=>amount2]
     *
     * @return \Ease\Html\DivTag
     */
    public static function getTotalsDiv(array $totals)
    {
        $total = new \Ease\Html\DivTag();

        foreach ($totals as $currency => $amount) {
            $total->addItem(new \Ease\Html\DivTag(self::formatCurrency((float) $amount).'&nbsp;'.\AbraFlexi\Functions::uncode((string) $currency)));
        }

        return $total;
    }

    /**
     * Save HTML digest fragment.
     *
     * @param string $saveTo directory
     */
    public function saveToHtml(string $saveTo): void
    {
        $filename = $saveTo.$this->getReportFilename();
        $this->addStatusMessage(
            sprintf(
                _('Module output Saved to %s'),
                $filename,
            ),
            file_put_contents($filename, $this->getRendered()) ? 'success' : 'error',
        );
    }

    /**
     * Remove report file.
     */
    public function fileCleanUP(string $saveTo): void
    {
        $filename = $saveTo.$this->getReportFilename();

        if (file_exists($filename)) {
            $this->addStatusMessage(sprintf(
                _('Module output %s wiped out'),
                $filename,
            ), unlink($filename) ? 'success' : 'error');
        }
    }

    /**
     * Get Report Filename.
     */
    public function getReportFilename(): string
    {
        return pathinfo($_SERVER['SCRIPT_FILENAME'], \PATHINFO_FILENAME).'_'.pathinfo(
            \get_class($this),
            \PATHINFO_FILENAME,
        ).'.html';
    }

    /**
     * Print progress log.
     */
    public function finalize(): void
    {
        $this->addStatusMessage($this->heading(), 'debug');
        parent::finalize();
    }
}
