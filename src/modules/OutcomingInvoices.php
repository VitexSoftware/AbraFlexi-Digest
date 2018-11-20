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
            'sumZalohy', 'uhrazeno', 'storno', 'mena', 'juhSum', 'juhSumMen'],
            $this->condition);
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

                $amount = floatval($outInvoiceData['sumCelkem']) + floatval($outInvoiceData['sumZalohy']);
                
                if (array_key_exists($outInvoiceData['typDokl'], $typDoklCounts)) {
                    $typDoklCounts[$outInvoiceData['typDokl']] ++;
                    $typDoklTotals[$outInvoiceData['typDokl']][$outInvoiceData['mena']]
                        += $amount;
                } else {
                    $typDoklCounts[$outInvoiceData['typDokl']]                          = 1;
                    $typDoklTotals[$outInvoiceData['typDokl']][$outInvoiceData['mena']]
                        = $amount;
                }

                if (array_key_exists($outInvoiceData['mena'], $invoicedRaw)) {
                    $invoicedRaw[$outInvoiceData['mena']] += $amount;
                } else {
                    $invoicedRaw[$outInvoiceData['mena']] = $amount;
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
                $tableFooter[] = self::formatCurrency( $invoicedRaw[$currencyCode] ).' '.FlexiPeeHP\FlexiBeeRO::uncode($currencyCode);
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
