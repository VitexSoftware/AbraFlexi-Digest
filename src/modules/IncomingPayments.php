<?php
/*
 * Incoming payments for us
 */

/**
 * Description of IncomingPayments
 *
 * @author vitex
 */
class IncomingPayments extends \FlexiPeeHP\Digest\DigestModule implements \FlexiPeeHP\Digest\DigestModuleInterface
{
    public $timeColumn = 'datVyst';

    public function dig()
    {
        $banker  = new FlexiPeeHP\Banka();
        $incomes = $banker->getColumnsFromFlexibee(['mena', 'sumCelkem','sumCelkemMen'],
            array_merge($this->condition,
                ['typPohybuK' => 'typPohybu.prijem', 'storno' => false]));
        $total   = [];
        if (empty($incomes)) {
            $this->addItem(_('none'));
        } else {
            foreach ($incomes as $income) {
                $currency = self::getCurrency($income);

                if ($currency == 'CZK') {
                    $amount = floatval($income['sumCelkem']);
                } else {
                    $amount = floatval($income['sumCelkemMen']);
                }

                if (array_key_exists($currency, $total)) {
                    $total[$currency] += $amount;
                } else {
                    $total[$currency] = $amount;
                }
            }
            foreach ($total as $currency => $amount) {
                $this->addItem(new \Ease\Html\DivTag(self::formatCurrency($amount).'&nbsp;'.$currency));
            }
        }
    }

    public function heading()
    {
        return _('Incoming payments');
    }
}
