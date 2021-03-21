<?php

/*
 * Debts
 */

/**
 * Description of WaitingIncome
 *
 * @author vitex
 */
class Debtors extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface {

    public function dig() {
        $invoicer = new \AbraFlexi\FakturaVydana();

        $cond = ['datSplat lte \'' . \AbraFlexi\RW::dateToFlexiDate(new \DateTime()) . '\' AND (stavUhrK is null OR stavUhrK eq \'stavUhr.castUhr\') AND storno eq false'];

        $faDatakturyRaw = $invoicer->getColumnsFromAbraFlexi(['kod', 'firma', 'sumCelkem',
            'sumCelkemMen', 'zbyvaUhradit', 'zbyvaUhraditMen', 'mena', 'datSplat'],
                $cond);

        $totals = [];
        $totalsByCurrency = [];
        $overdue = [];

        if (empty($faDatakturyRaw)) {
            $invoicer->addStatusMessage("Faktur: 0");
        } else {
            $invoicer->addStatusMessage("Faktur: " . count($faDatakturyRaw));

            foreach ($faDatakturyRaw as $faData) {
                $currency = self::getCurrency($faData);
                $invoicesByFirma[$faData['firma']][$faData['id']] = $faData;

                if (!isset($totals[$faData['firma']][$currency])) {
                    $totals[$faData['firma']][$currency] = 0;
                }
                if (!isset($totalsByCurrency[$currency])) {
                    $totalsByCurrency[$currency] = 0;
                }

                if ($currency != 'CZK') {
                    $amount = floatval($faData['zbyvaUhraditMen']);
                } else {
                    $amount = floatval($faData['zbyvaUhradit']);
                }

                $totals[$faData['firma']][$currency] += $amount;
                $totalsByCurrency[$currency] += $amount;

                $oDays = \AbraFlexi\FakturaVydana::overdueDays($faData['datSplat']);

                if (array_key_exists($faData['firma'], $overdue)) {
                    if ($oDays > $overdue[$faData['firma']]) {
                        $overdue[$faData['firma']] = $oDays;
                    }
                } else {
                    $overdue[$faData['firma']] = $oDays;
                }
            }
        }

        if (empty($invoicesByFirma)) {
            $this->addItem(_('none'));
        } else {
            $adreser = new AbraFlexi\Adresar(null, ['offline' => 'true']);
            $invTable = new Ease\Html\TableTag(null, ['class' => 'table']);
            $invTable->addRowHeaderColumns([_('Company'), _('Amount'), _('Overdue days'),
                _('Invoices')]);

            foreach ($invoicesByFirma as $firma => $fakturyFirmy) {

                $overdueInvoices = new \Ease\Html\DivTag();
                foreach ($fakturyFirmy as $invoiceData) {
                    $invoicer->setMyKey($invoiceData['id']);
                    $currency = self::getCurrency($invoiceData);

                    $overdueInvoice = \AbraFlexi\RO::uncode($invoiceData['kod']);

                    $overdueInvoices->addItem(new Ease\Html\DivTag([new \Ease\Html\ATag($invoicer->getApiURL(),
                                        $overdueInvoice, ['css' => 'margin: 5px;']),
                                '&nbsp;<small>' . ( ($currency != 'CZK') ? $invoiceData['zbyvaUhraditMen'] : $invoiceData['zbyvaUhradit']) . ' ' . $currency . ' ' . \AbraFlexi\FakturaVydana::overdueDays($invoiceData['datSplat']) . ' ' . _('days') . '</small>']
                    ));
                }

                $adreser->setMyKey($firma);

                $nazevFirmy = array_key_exists('firma@showAs',
                                current($fakturyFirmy)) ? current($fakturyFirmy)['firma@showAs'] : \AbraFlexi\RO::uncode($firma);

                $invTable->addRowColumns([new \Ease\Html\ATag($adreser->getApiURL(),
                            $nazevFirmy), self::getTotalsDiv($totals[$firma]),
                    $overdue[$firma], $overdueInvoices]);
            }

            $this->addItem($invTable);

            $totalRow=new \Ease\TWB4\Row();
            $totalRow->addColumn(9);
            $totalRow->addColumn(3,[new \Ease\Html\H3Tag(_('Total')),self::getTotalsDiv($totalsByCurrency)]);
            $this->addItem($totalRow);
        }
        return !empty($invoicesByFirma);
    }

    function heading() {
        return _('Debtors');
    }

}
