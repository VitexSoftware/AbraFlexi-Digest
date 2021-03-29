<?php

/*
 * Incoming payments for us
 */

/**
 * Description of IncomingPayments
 *
 * @author vitex
 */
class IncomingPayments extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface {

    public $timeColumn = 'datVyst';

    /**
     * Process Incoming payments
     * 
     * @return boolean
     */
    public function dig() {
        $results =  new \Ease\Container();
        $banker = new AbraFlexi\Banka();
        $incomes = $banker->getColumnsFromAbraFlexi(['mena', 'sumCelkem', 'sumCelkemMen'],
                array_merge($this->condition,
                        ['typPohybuK' => 'typPohybu.prijem', 'storno' => false]));
        $total = [];
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
                $results->addItem(new \Ease\Html\DivTag(self::formatCurrency($amount) . '&nbsp;' . $currency));
            }
        }

        $this->addItem($this->cardBody( $results )) ;

        return !empty($incomes);
    }

    public function heading() {
        return _('Incoming payments');
    }

}
