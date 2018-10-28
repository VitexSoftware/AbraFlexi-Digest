<?php
/*
 * What we have to pay
 */

/**
 * Description of WaitingPayments
 *
 * @author vitex
 */
class WaitingPayments extends \FlexiPeeHP\Digest\DigestModule implements \FlexiPeeHP\Digest\DigestModuleInterface
{

    public function heading()
    {
        return _('We have to pay');
    }
    /**
     * Column used to filter by date
     * @var string 
     */
    public $timeColumn = 'datSplat';

    public function dig()
    {
        $totals     = [];
        $checker    = new \FlexiPeeHP\FakturaPrijata();
        $inInvoices = $checker->getColumnsFromFlexibee(['kod', 'firma', 'sumCelkem',
            'mena'],
            array_merge($this->condition,
                ["(stavUhrK is null OR stavUhrK eq 'stavUhr.castUhr')",
            'storno' => false]));

        if (empty($inInvoices)) {
            $this->addItem(_('none'));
        } else {
            $adreser  = new FlexiPeeHP\Adresar(null, ['offline' => 'true']);
            $invTable = new Ease\Html\TableTag(null, ['class' => 'pure-table']);
            $invTable->addRowHeaderColumns([_('Position'), _('Code'), _('Partner'),
                _('Amount')]);
            $pos      = 0;

            foreach ($inInvoices as $inInvoiceData) {
                $currency = current(explode(':', $inInvoiceData['mena@showAs']));

                $checker->setMyKey(urlencode($inInvoiceData['kod']));
                $adreser->setMyKey($inInvoiceData['firma']);

                $invTable->addRowColumns([
                    ++$pos,
                    new \Ease\Html\ATag($checker->getApiUrl(),
                        $inInvoiceData['kod']),
                    new \Ease\Html\ATag($adreser->getApiUrl(),
                        empty($inInvoiceData['firma']) ? '' : $inInvoiceData['firma@showAs']),
                    $inInvoiceData['sumCelkem'].' '.current(explode(':',
                            $inInvoiceData['mena@showAs']))
                ]);

                if (array_key_exists($currency, $totals)) {
                    $totals[$currency] += floatval($inInvoiceData['sumCelkem']);
                } else {
                    $totals[$currency] = floatval($inInvoiceData['sumCelkem']);
                }
            }
            $this->addItem($invTable);
            $this->addItem(new Ease\Html\H3Tag(_('Total')));
            foreach ($totals as $currency => $amount) {
                $this->addItem(new \Ease\Html\DivTag(self::formatCurrency($amount).'&nbsp;'.$currency));
            }
        }
    }

    public function functionName($param)
    {
        ['datSplat lte \''.\FlexiPeeHP\FlexiBeeRW::dateToFlexiDate(new \DateTime()).'\' AND (stavUhrK is null OR stavUhrK eq \'stavUhr.castUhr\') AND storno eq false'];
    }
}
