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
        $outcomes = $banker->getColumnsFromFlexibee('mena,sumCelkem',
            array_merge($this->condition,
                ['typPohybuK' => 'typPohybu.vydej', 'storno' => false]));
        $total    = [];
        if (empty($outcomes)) {
            $this->addItem(_('none'));
        } else {
            foreach ($outcomes as $outcome) {
                $currency = self::getCurrency($outcome);
                if (array_key_exists($currency, $total)) {
                    $total[$currency] += floatval($outcome['sumCelkem']);
                } else {
                    $total[$currency] = floatval($outcome['sumCelkem']);
                }
            }
            foreach ($total as $currency => $amount) {
                $this->addItem(new \Ease\Html\DivTag(self::formatCurrency($amount).'&nbsp;'.$currency));
            }
        }
    }

    public function heading()
    {
        return _('Outcoming payments');
    }
}
