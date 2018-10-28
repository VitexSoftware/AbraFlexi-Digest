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
        $incomes = $banker->getColumnsFromFlexibee('mena,sumCelkem',
            array_merge($this->condition,
                ['typPohybuK' => 'typPohybu.prijem', 'storno' => false]));
        $total   = [];
        if (empty($incomes)) {
            $this->addItem(_('none'));
        } else {
            foreach ($incomes as $income) {
                $currency = self::getCurrency($income);
                if (array_key_exists($currency, $total)) {
                    $total[$currency] += floatval($income['sumCelkem']);
                } else {
                    $total[$currency] = floatval($income['sumCelkem']);
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
