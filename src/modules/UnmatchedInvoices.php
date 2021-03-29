<?php

use Ease\Html\DivTag;

/**
 * Incoming payments for us
 *
 * @author vitex
 */
class UnmatchedInvoices extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface {

    public $timeColumn = 'datVyst';

    /**
     * Process Incoming payments
     * 
     * @return boolean
     */
    public function dig() {
        $invoicer = new AbraFlexi\FakturaVydana(null,['nativeTypes'=>false]);
        $adresser = new AbraFlexi\Adresar();
        $proformas = $invoicer->getColumnsFromAbraFlexi(['kod', 'mena', 'popis', 'sumCelkem',
            'sumCelkemMen', 'stavOdpocetK', 'typDokl', 'firma', 'datVyst'],
                array_merge($this->condition,
                        ['typPohybuK' => 'typPohybu.prijem', 'storno' => false,
                            'zuctovano' => false,
                            'typDokl.typDoklK' => 'typDokladu.zalohFaktura',
                            'stavUhrK' => 'stavUhr.uhrazeno']), 'datVyst');
        $total = [];
        $totals = [];
        if (empty($proformas)) {
            $this->addItem($this->cardBody(_('none')));
        } else {
            $incomesTable = new \AbraFlexi\Digest\Table([_('Document'), _('Description'),
                _('Denunc state'), _('Document type'), _('Company'), _('Date'), _('Amount')]);
            foreach ($proformas as $proforma) {

                switch ($proforma['stavOdpocetK']) {
                    case 'stavOdp.komplet':
                    case 'stavOdp.vytvZdd':
                        break;

                    default:
                        unset($proforma['external-ids']);
                        unset($proforma['id']);
                        unset($proforma['typDokl@ref']);
                        $adresser->takeData($proforma);

                        $amount = self::getAmount($proforma);
                        $currency = self::getCurrency($proforma);
                        if (array_key_exists($currency, $total)) {
                            $total[$currency] += $amount;
                            $totals[$currency]++;
                        } else {
                            $total[$currency] = $amount;
                            $totals[$currency] = 1;
                        }

                        $proforma['kod'] = new \AbraFlexi\Digest\DocumentLink($proforma['kod'],
                                $invoicer);
                        $proforma['price'] = self::getPrice($proforma);

                        $proforma['firma'] = new AbraFlexi\Digest\CompanyLink($proforma['firma'],
                                $adresser);

                        unset($proforma['typDokl']);
                        unset($proforma['sumCelkem']);
                        unset($proforma['sumCelkemMen']);
                        unset($proforma['mena']);
                        unset($proforma['mena@ref']);
                        unset($proforma['mena@showAs']);
                        unset($proforma['stavOdpocetK']);
                        unset($proforma['firma@ref']);
                        unset($proforma['firma@showAs']);
                        $incomesTable->addRowColumns($proforma);

                        break;
                }
            }

            $currDiv = new DivTag();
            

            foreach ($total as $currency => $amount) {
                $currDiv->addItem(new \Ease\Html\DivTag($totals[$currency] . 'x' . ' ' . self::formatCurrency($amount) . '&nbsp;' . $currency));
            }

            $this->addItem($this->cardBody([$incomesTable,$currDiv]));

        }
        return !empty($total);
    }

    public function heading() {
        return _('Non-deducted proformas');
    }

    /**
     * Default Description
     * 
     * @return string
     */
    public function description() {
        return _('Non-deducted proformas');
    }

}
