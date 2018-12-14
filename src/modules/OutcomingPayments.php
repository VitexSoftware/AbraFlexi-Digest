<?php
/*
 * Outcoming payments
 */

/**
 * Description of OutcomingPayments
 *
 * @author vitex
 */
class OutcomingPayments extends \FlexiPeeHP\Digest\DigestModule implements \FlexiPeeHP\Digest\DigestModuleInterface
{
    public $timeColumn = 'datVyst';

    public function dig()
    {
        $banker   = new FlexiPeeHP\Banka();
        $outcomes = $banker->getColumnsFromFlexibee(['mena','sumCelkem',
            'sumCelkemMen'],
            array_merge($this->condition,
                ['typPohybuK' => 'typPohybu.vydej', 'storno' => false]));
        $total    = [];
        if (empty($outcomes)) {
            $this->addItem(_('none'));
        } else {
            foreach ($outcomes as $outcome) {
                $currency = self::getCurrency($outcome);

                if ($currency != 'CZK') {
                    $amount = floatval($outcome['sumCelkemMen']);
                } else {
                    $amount = floatval($outcome['sumCelkem']);
                }

                if (array_key_exists($currency, $total)) {
                    $total[$currency] += $amount;
                } else {
                    $total[$currency] = $amount;
                }
            }

            $totalsTable = new \FlexiPeeHP\Digest\Table([_('Amount'), _('Currency')]);
            foreach ($total as $currency => $amount) {
                $totalsTable->addRowColumns([self::formatCurrency($amount), $currency]);
            }
        }
    }

    public function heading()
    {
        return _('Outcoming payments');
    }
}
