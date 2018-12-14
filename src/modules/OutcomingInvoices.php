<?php
/*
 * Outcoming Invoices
 */

/**
 * Description of OutcomingInvoices
 *
 * @author vitex
 */
class OutcomingInvoices extends \FlexiPeeHP\Digest\DigestModule implements \FlexiPeeHP\Digest\DigestModuleInterface
{
    /**
     * Column used to filter by date
     * @var string 
     */
    public $timeColumn = 'datVyst';

    public function dig()
    {
        $digger          = new FlexiPeeHP\FakturaVydana();
        $outInvoicesData = $digger->getColumnsFromFlexibee(['kod', 'typDokl', 'sumCelkem',
            'sumCelkemMen',
            'sumZalohy', 'sumZalohyMen', 'uhrazeno', 'storno', 'mena', 'juhSum',
            'juhSumMen'], $this->condition);
        $exposed         = 0;
        $invoicedRaw     = [];
        $paid            = [];
        $storno          = 0;

        $typDoklCounts = [];
        $typDoklTotals = [];

        if (empty($outInvoicesData)) {
            $this->addItem(_('none'));
        } else {
            foreach ($outInvoicesData as $outInvoiceData) {
                $exposed++;
                if ($outInvoiceData['storno'] == 'true') {
                    $storno++;
                }
                $currency = self::getCurrency($outInvoiceData);
                $typDokl  = $outInvoiceData['typDokl'];

                if ($currency != 'CZK') {
                    $amount = floatval($outInvoiceData['sumCelkemMen']) + floatval($outInvoiceData['sumZalohyMen']);
                } else {
                    $amount = floatval($outInvoiceData['sumCelkem']) + floatval($outInvoiceData['sumZalohy']);
                }

                if (!array_key_exists($typDokl, $typDoklTotals)) {
                    $typDoklTotals[$typDokl] = [];
                }

                if (!array_key_exists($currency, $typDoklTotals[$typDokl])) {
                    $typDoklTotals[$typDokl][$currency] = 0;
                }

                if (array_key_exists($typDokl, $typDoklCounts)) {
                    $typDoklCounts[$typDokl] ++;
                    $typDoklTotals[$typDokl][$currency] += $amount;
                } else {
                    $typDoklCounts[$typDokl]            = 1;
                    $typDoklTotals[$typDokl][$currency] = $amount;
                }

                if (array_key_exists($currency, $invoicedRaw)) {
                    $invoicedRaw[$currency] += $amount;
                } else {
                    $invoicedRaw[$currency] = $amount;
                }
            }

            $tableHeader[] = _('Count');
            $tableHeader[] = _('Document type');
            $currencies    = array_keys($invoicedRaw);
            foreach ($currencies as $currencyCode) {
                $tableHeader[] = _('Total').' '.\FlexiPeeHP\FlexiBeeRO::uncode($currencyCode);
            }

            $outInvoicesTable = new \FlexiPeeHP\Digest\Table($tableHeader);

            foreach ($typDoklTotals as $typDokl => $typDoklTotal) {
                $tableRow   = [$typDoklCounts[$typDokl]];
                $tableRow[] = \FlexiPeeHP\FlexiBeeRO::uncode($typDokl);

                foreach ($currencies as $currencyCode) {
                    $tableRow[] = array_key_exists($currencyCode,
                            $typDoklTotals[$typDokl]) ? $typDoklTotals[$typDokl][$currencyCode]
                            : '';
                }

                $outInvoicesTable->addRowColumns($tableRow);
            }

            $tableFooter = [$exposed, count(array_keys($typDoklTotals))];
            foreach ($currencies as $currencyCode) {
                $tableFooter[] = self::formatCurrency($invoicedRaw[$currencyCode]).' '.FlexiPeeHP\FlexiBeeRO::uncode($currencyCode);
            }
            $outInvoicesTable->addRowFooterColumns($tableFooter);

            $this->addItem($outInvoicesTable);
        }
    }

    public function heading()
    {
        return _('Outcoming invoices');
    }
}
