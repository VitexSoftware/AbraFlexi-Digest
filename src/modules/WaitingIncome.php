<?php
/*
 * Debts
 */

/**
 * Description of WaitingIncome
 *
 * @author vitex
 */
class WaitingIncome extends \FlexiPeeHP\Digest\DigestModule implements \FlexiPeeHP\Digest\DigestModuleInterface
{
    /**
     * Column used to filter by date
     * @var string 
     */
    public $timeColumn = 'datSplat';

    public function dig()
    {
        $totals      = [];
        $checker     = new \FlexiPeeHP\FakturaVydana();
        $outInvoices = $checker->getColumnsFromFlexibee(['kod', 'firma', 'sumCelkem',
            'sumCelkemMen',
            'mena'],
            array_merge($this->condition,
                ["(stavUhrK is null OR stavUhrK eq 'stavUhr.castUhr')",
                    'storno' => false]));

        if (empty($outInvoices)) {
            $this->addItem(_('none'));
        } else {
            $adreser  = new FlexiPeeHP\Adresar(null, ['offline' => 'true']);
            $invTable = new \FlexiPeeHP\Digest\Table([_('Position'), _('Code'), _('Partner'),
                _('Amount')]);
            $pos      = 0;

            foreach ($outInvoices as $outInvoiceData) {
                $currency = self::getCurrency($outInvoiceData);
                $checker->setMyKey(urlencode($outInvoiceData['kod']));
                $adreser->setMyKey($outInvoiceData['firma']);

                $invTable->addRowColumns([
                    ++$pos,
                    new \Ease\Html\ATag($checker->getApiUrl(),
                        $outInvoiceData['kod']),
                    new \Ease\Html\ATag($adreser->getApiUrl(),
                        empty($outInvoiceData['firma']) ? '' : $outInvoiceData['firma@showAs']),
                    (($currency != 'CZK') ? $outInvoiceData['sumCelkemMen'] : $outInvoiceData['sumCelkem']).' '.$currency
                ]);

                if (array_key_exists($currency, $totals)) {
                    $totals[$currency] += floatval($outInvoiceData['sumCelkem']);
                } else {
                    $totals[$currency] = floatval($outInvoiceData['sumCelkem']);
                }
            }
            $this->addItem($invTable);

            $this->addItem(new Ease\Html\H3Tag(_('Total')));
            foreach ($totals as $currency => $amount) {
                $this->addItem(new \Ease\Html\DivTag(self::formatCurrency($amount).'&nbsp;'.$currency));
            }
        }
    }

    public function heading()
    {
        return _('Waiting Income');
    }
}
