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
        $inInvoicesData = $digger->getColumnsFromFlexibee(['kod', 'typDokl', 'sumCelkem',
            'uhrazeno', 'storno', 'mena', 'juhSum', 'juhSumMen'],
            $this->condition);
        $exposed        = 0;
        $invoicedRaw    = [];
        $paid           = [];
        $storno         = 0;

        $typDoklRaw = [];
        if (empty($inInvoicesData)) {
            $this->addStatusMessage(_('none'));
        } else {
            foreach ($inInvoicesData as $outInvoiceData) {
                $exposed++;
                if ($outInvoiceData['storno'] == 'true') {
                    $storno++;
                }

                if (array_key_exists($outInvoiceData['typDokl'], $typDoklRaw)) {
                    $typDoklRaw[$outInvoiceData['typDokl']] ++;
                } else {
                    $typDoklRaw[$outInvoiceData['typDokl']] = 1;
                }

                if (array_key_exists($outInvoiceData['mena'], $outInvoiceData)) {
                    $invoicedRaw[$outInvoiceData['mena']] += floatval($outInvoiceData['sumCelkem']);
                } else {
                    $invoicedRaw[$outInvoiceData['mena']] = floatval($outInvoiceData['sumCelkem']);
                }
            }

            $typDokl = [];
            foreach ($typDoklRaw as $type => $count) {
                $typDokl[] = $count.' x '.FlexiPeeHP\FlexiBeeRO::uncode($type);
            }
            $this->addItem(new \Ease\Html\DivTag(sprintf(implode('<br>', $typDokl))));

            $invoiced = [];
            foreach ($invoicedRaw as $currencyCode => $amount) {
                $invoiced[] = self::formatCurrency($amount).' '.FlexiPeeHP\FlexiBeeRO::uncode($currencyCode);
            }

            $this->addItem(new \Ease\Html\DivTag(sprintf(_('Invoiced amount %s'),
                    implode('<br>', $invoiced))));

            $this->addItem(new \Ease\Html\DivTag(sprintf(_('Exposed %s invoices'),
                    $exposed)));
        }
    }

    public function heading()
    {
        return _('Incoming invoices');
    }
}
