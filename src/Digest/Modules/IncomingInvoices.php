<?php

/**
 * AbraFlexi Digest - Incoming Invoices accepted by us
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018-2023 Vitex Software
 */

namespace AbraFlexi\Digest\Modules;

/**
 * Description of IncomingInvoices
 *
 * @author vitex
 */
class IncomingInvoices extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface
{

    /**
     * Column used to filter by date
     * @var string 
     */
    public $timeColumn = 'datVyst';

    public function dig()
    {
        $totals = [];
        $digger = new \AbraFlexi\FakturaPrijata(null, ['nativeTypes' => false]);
        $inInvoicesData = $digger->getColumnsFromAbraFlexi(['kod', 'typDokl', 'sumCelkem', 'sumCelkemMen',
            'uhrazeno', 'storno', 'mena', 'juhSum', 'juhSumMen'],
                $this->condition);
        $accepted = 0;
        $invoicedRaw = [];
        $paid = [];
        $storno = 0;
        $totals = [];
        $typDoklRaw = [];
        if (empty($inInvoicesData)) {
            $this->addStatusMessage(_('none'));
        } else {
            foreach ($inInvoicesData as $outInvoiceData) {
                $accepted++;
                if ($outInvoiceData['storno'] == 'true') {
                    $storno++;
                }

                if (array_key_exists($outInvoiceData['typDokl'], $typDoklRaw)) {
                    $typDoklRaw[$outInvoiceData['typDokl']]++;
                } else {
                    $typDoklRaw[$outInvoiceData['typDokl']] = 1;
                }

                $amount = ($outInvoiceData['mena'] == 'code:CZK') ? floatval($outInvoiceData['sumCelkem']) : floatval($outInvoiceData['sumCelkemMen']);
                if (array_key_exists($outInvoiceData['mena'], $invoicedRaw)) {
                    $invoicedRaw[$outInvoiceData['mena']] += $amount;
                } else {
                    $invoicedRaw[$outInvoiceData['mena']] = $amount;
                }

                if (!array_key_exists($outInvoiceData['typDokl'], $totals)) {
                    $totals[$outInvoiceData['typDokl']] = [];
                }

                if (!array_key_exists($outInvoiceData['mena'],
                                $totals[$outInvoiceData['typDokl']])) {
                    $totals[$outInvoiceData['typDokl']][$outInvoiceData['mena']] = 0;
                }

                $totals[$outInvoiceData['typDokl']][$outInvoiceData['mena']] += $amount;
            }

            $invoiced = [];
            foreach ($invoicedRaw as $currencyCode => $amount) {
                $invoiced[] = self::formatCurrency($amount) . ' ' . \AbraFlexi\RO::uncode($currencyCode);
            }


            $inInvoicesTable = new \AbraFlexi\Digest\Table([_('Count'), _('Type'),
                _('Total')]);
            foreach ($typDoklRaw as $type => $count) {
                $inInvoicesTable->addRowColumns([$count, \AbraFlexi\RO::uncode($type),
                    self::getTotalsDiv($totals[$type])]);
            }


            $inInvoicesTable->addRowFooterColumns([$accepted, 0, $invoiced]);
            $this->addItem($this->cardBody($inInvoicesTable));
        }

        return !empty($inInvoicesData);
    }

    public function heading()
    {
        return _('Incoming invoices');
    }
}