<?php
/*
 * Incoming Invoices accepted by us
 */

/**
 * Description of IncomingInvoices
 *
 * @author vitex
 */
class IncomingInvoices extends \FlexiPeeHP\Digest\DigestModule implements \FlexiPeeHP\Digest\DigestModuleInterface
{
    /**
     * Column used to filter by date
     * @var string 
     */
    public $timeColumn = 'datVyst';

    public function dig()
    {
        $totals = [];

        $digger         = new \FlexiPeeHP\FakturaPrijata();
        $inInvoicesData = $digger->getColumnsFromFlexibee(['kod', 'typDokl', 'sumCelkem','sumCelkemMen',
            'uhrazeno', 'storno', 'mena', 'juhSum', 'juhSumMen'],
            $this->condition);
        $accepted        = 0;
        $invoicedRaw    = [];
        $paid           = [];
        $storno         = 0;
        $totals         = [];

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
                    $typDoklRaw[$outInvoiceData['typDokl']] ++;
                } else {
                    $typDoklRaw[$outInvoiceData['typDokl']] = 1;
                }

                $amount = ($outInvoiceData['mena'] == 'code:CZK') ? floatval($outInvoiceData['sumCelkem'])
                        : floatval($outInvoiceData['sumCelkemMen']);

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
                    $totals[$outInvoiceData['typDokl']][$outInvoiceData['mena']]
                        = 0;
                }

                $totals[$outInvoiceData['typDokl']][$outInvoiceData['mena']] += $amount;
            }

            $invoiced = [];
            foreach ($invoicedRaw as $currencyCode => $amount) {
                $invoiced[] = self::formatCurrency($amount).' '.FlexiPeeHP\FlexiBeeRO::uncode($currencyCode);
            }


            $inInvoicesTable = new \FlexiPeeHP\Digest\Table([_('Count'), _('Type'),
                _('Total')]);
            foreach ($typDoklRaw as $type => $count) {
                $inInvoicesTable->addRowColumns([$count, FlexiPeeHP\FlexiBeeRO::uncode($type),
                    self::getTotalsDiv($totals[$type])]);
            }


            $inInvoicesTable->addRowFooterColumns([$accepted, 0, $invoiced]);


            $this->addItem($inInvoicesTable);
        }

        return !empty($inInvoicesData);
    }

    public function heading()
    {
        return _('Incoming invoices');
    }
}
