<?php

/*
 * Incoming payments for us
 */

/**
 * Description of IncomingPayments
 *
 * @author vitex
 */
class UnmatchedPayments extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface {

    public $timeColumn = 'datVyst';

    /**
     * Process Incoming payments
     * 
     * @return boolean
     */
    public function dig() {
        $banker = new AbraFlexi\Banka(null, ['nativeTypes' => false]);
        $adresser = new AbraFlexi\Adresar();
        $bucer = new AbraFlexi\Adresar(null,
                ['evidence' => 'adresar-bankovni-ucet']);
        $incomes = $banker->getColumnsFromAbraFlexi(['kod', 'mena', 'popis', 'sumCelkem',
            'sumCelkemMen',
            'buc', 'firma', 'datVyst'],
                array_merge($this->condition,
                        ['typPohybuK' => 'typPohybu.prijem', 'storno' => false,
                            'zuctovano' => false,
                            'sparovano' => false]), 'datVyst');
        $total = [];
        if (empty($incomes)) {
            $this->addItem(_('none'));
        } else {
            $incomesTable = new \AbraFlexi\Digest\Table([_('Document'), _('Description'),
                _('Bank Account'), _('Company'), _('Date'), _('Amount')]);
            foreach ($incomes as $income) {
                $adresser->dataReset();
                if (empty($income['firma']) && !empty($income['buc'])) {
                    $candidates = $bucer->getColumnsFromAbraFlexi(['firma'],
                            ['buc' => $income['buc']]);
                    if (!empty($candidates)) {
                        $income['firma'] = $candidates[0]['firma'];
                        $income['firma@showAs'] = $candidates[0]['firma@showAs'];
                    }
                }
                $adresser->takeData($income);

                $amount = self::getAmount($income);
                $currency = self::getCurrency($income);
                if (array_key_exists($currency, $total)) {
                    $total[$currency] += $amount;
                } else {
                    $total[$currency] = $amount;
                }

                $income['kod'] = new \AbraFlexi\ui\DocumentLink($income['kod'], $banker);
                $income['price'] = self::getAmount($income);
                $adresser->setMyKey($adresser);
                $income['firma'] = new \Ease\Html\ATag(array_key_exists('firma@showAs', $income) ?  $income['firma@showAs'] : '', $adresser->getApiUrl() . $income['firma']);

                unset($income['id']);
                unset($income['sumCelkem']);
                unset($income['sumCelkemMen']);
                unset($income['mena']);
                unset($income['mena@ref']);
                unset($income['mena@showAs']);
                unset($income['firma@ref']);
                unset($income['firma@showAs']);
                $incomesTable->addRowColumns($income);
            }
            $currDiv = new \Ease\Html\DivTag();
           
            foreach ($total as $currency => $amount) {
                $currDiv->addItem(new \Ease\Html\DivTag(self::formatCurrency($amount) . '&nbsp;' . $currency));
            
            }
            $this->addItem($this->cardBody([$incomesTable, $currDiv]));
        }
        return !empty($incomes);
    }

    public function heading() {
        return _('Unmatched payments');
    }

    /**
     * Default Description
     * 
     * @return string
     */
    public function description() {
        return _('Unrecognized and non-deducted earnings');
    }

}
