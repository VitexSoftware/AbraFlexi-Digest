<?php
/*
 * Incoming payments for us
 */

/**
 * Description of IncomingPayments
 *
 * @author vitex
 */
class UnmatchedPayments extends \FlexiPeeHP\Digest\DigestModule implements \FlexiPeeHP\Digest\DigestModuleInterface
{
    public $timeColumn = 'datVyst';

    /**
     * Process Incoming payments
     * 
     * @return boolean
     */
    public function dig()
    {
        $banker   = new FlexiPeeHP\Banka();
        $adresser = new FlexiPeeHP\Adresar();
        $bucer    = new FlexiPeeHP\Adresar(null,
            ['evidence' => 'adresar-bankovni-ucet']);
        $incomes  = $banker->getColumnsFromFlexibee(['kod', 'mena', 'popis', 'sumCelkem',
            'sumCelkemMen',
            'buc', 'firma', 'datVyst'],
            array_merge($this->condition,
                ['typPohybuK' => 'typPohybu.prijem', 'storno' => false,
                    'zuctovano' => false,
                    'sparovano' => false]), 'datVyst');
        $total    = [];
        if (empty($incomes)) {
            $this->addItem(_('none'));
        } else {
            $incomesTable = new \FlexiPeeHP\Digest\Table([_('Document'), _('Description'),
                _('Bank Account'), _('Company'), _('Date'), _('Amount')]);
            foreach ($incomes as $income) {
                $adresser->dataReset();
                if (empty($income['firma']) && !empty($income['buc'])) {
                    $candidates = $bucer->getColumnsFromFlexiBee(['firma'],
                        ['buc' => $income['buc']]);
                    if (!empty($candidates)) {
                        $income['firma']        = $candidates[0]['firma'];
                        $income['firma@showAs'] = $candidates[0]['firma@showAs'];
                    }
                }
                $adresser->takeData($income);

                $amount   = self::getAmount($income);
                $currency = self::getCurrency($income);
                if (array_key_exists($currency, $total)) {
                    $total[$currency] += $amount;
                } else {
                    $total[$currency] = $amount;
                }

                $income['kod']   = new \FlexiPeeHP\Digest\DocumentLink($income['kod'],
                    $banker);
                $income['price'] = self::getPrice($income);

                $income['firma'] = new FlexiPeeHP\Digest\CompanyLink($income['firma'],
                    $adresser);


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

            $this->addItem($incomesTable);

            foreach ($total as $currency => $amount) {
                $this->addItem(new \Ease\Html\DivTag(self::formatCurrency($amount).'&nbsp;'.$currency));
            }
        }
        return !empty($incomes);
    }

    public function heading()
    {
        return _('Unmatched payments');
    }

    /**
     * Default Description
     * 
     * @return string
     */
    public function description()
    {
        return _('Unrecognized and non-deducted earnings');
    }
}
