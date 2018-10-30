<?php
/*
 * Debts
 */

/**
 * Description of WaitingIncome
 *
 * @author vitex
 */
class Debtors extends \FlexiPeeHP\Digest\DigestModule implements \FlexiPeeHP\Digest\DigestModuleInterface
{

    public function dig()
    {
        $invoicer = new \FlexiPeeHP\FakturaVydana();

        $cond = ['datSplat lte \''.\FlexiPeeHP\FlexiBeeRW::dateToFlexiDate(new \DateTime()).'\' AND (stavUhrK is null OR stavUhrK eq \'stavUhr.castUhr\') AND storno eq false'];

        $faDatakturyRaw = $invoicer->getColumnsFromFlexiBee(['kod', 'firma', 'sumCelkem',
            'zbyvaUhradit', 'mena', 'datSplat'], $cond);

        $invoicer->addStatusMessage("Faktur: ".count($faDatakturyRaw));

        $totals  = [];
        $totalsByCurrency = [];
        $overdue = [];

        foreach ($faDatakturyRaw as $faData) {
            $invoicesByFirma[$faData['firma']][$faData['id']] = $faData;

            if (!isset($totals[$faData['firma']][self::getCurrency($faData)])) {
                $totals[$faData['firma']][self::getCurrency($faData)] = 0;
            }
            if (!isset($totalsByCurrency[self::getCurrency($faData)])) {
                $totalsByCurrency[self::getCurrency($faData)] = 0;
            }

            $totals[$faData['firma']][self::getCurrency($faData)] += floatval($faData['zbyvaUhradit']);
            $totalsByCurrency[self::getCurrency($faData)] += floatval($faData['zbyvaUhradit']);
            
            
            
            $oDays = \FlexiPeeHP\FakturaVydana::overdueDays($faData['datSplat']);

            if (array_key_exists($faData['firma'], $overdue)) {
                if ($oDays > $overdue[$faData['firma']]) {
                    $overdue[$faData['firma']] = $oDays;
                }
            } else {
                $overdue[$faData['firma']] = $oDays;
            }
        }


        if (empty($invoicesByFirma)) {
            $this->addItem(_('none'));
        } else {
            $adreser  = new FlexiPeeHP\Adresar(null, ['offline' => 'true']);
            $invTable = new Ease\Html\TableTag(null, ['class' => 'pure-table']);
            $invTable->addRowHeaderColumns([_('Company'), _('Amount'), _('Overdue days'),
                _('Invoices')]);


            foreach ($invoicesByFirma as $firma => $fakturyFirmy) {

                $overdueInvoices = new \Ease\Html\DivTag();
                foreach ($fakturyFirmy as $invoiceData) {
                    $invoicer->setMyKey($invoiceData['id']);

                    $overdueInvoice = \FlexiPeeHP\FlexiBeeRO::uncode($invoiceData['kod']);

                    $overdueInvoices->addItem(new Ease\Html\DivTag([new \Ease\Html\ATag($invoicer->getApiURL(),
                            $overdueInvoice, ['css' => 'margin: 5px;']),
                        '&nbsp;<small>'.$invoiceData['zbyvaUhradit'].self::getCurrency($invoiceData).' '.\FlexiPeeHP\FakturaVydana::overdueDays($invoiceData['datSplat']).' '._('days').'</small>']
                    ));
                }

                $adreser->setMyKey($firma);

                $nazevFirmy = array_key_exists('firma@showAs',
                        current($fakturyFirmy)) ? current($fakturyFirmy)['firma@showAs']
                        : \FlexiPeeHP\FlexiBeeRO::uncode($firma);

                $invTable->addRowColumns([new \Ease\Html\ATag($adreser->getApiURL(),
                        $nazevFirmy), self::getTotalsDiv($totals[$firma]),
                    $overdue[$firma], $overdueInvoices]);
            }

            $this->addItem($invTable);
            
            $this->addItem(new \Ease\Html\H3Tag(_('Total')));
            $this->addItem(self::getTotalsDiv($totalsByCurrency));
        }
    }

    function heading()
    {
        return _('Debtors');
    }
}
