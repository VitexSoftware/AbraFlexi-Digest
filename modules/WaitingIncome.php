<?php
/*
 * Debts
 */

/**
 * Description of WaitingIncome
 *
 * @author vitex
 */
class WaitingIncome extends \FlexiPeeHP\DigestMail\DigestModule implements \FlexiPeeHP\DigestMail\DigestModuleInterface
{

    public function dig()
    {
        $checker     = new \FlexiPeeHP\FakturaVydana();
        $outInvoices = $checker->getColumnsFromFlexibee(['kod', 'firma', 'sumCelkem',
            'mena'],
            ['datSplat' => $this->interval, "(stavUhrK is null OR stavUhrK eq 'stavUhr.castUhr')",
            'storno' => false]);
        
        if (!empty($outInvoices)) {
            $adreser = new FlexiPeeHP\Adresar(null,['offline' => 'true']);
            $invTable = new Ease\Html\TableTag(null, ['class' => 'pure-table']);
            $invTable->addRowHeaderColumns([_('Position'), _('Code'), _('Partner'),
                _('Amount')]);
            $pos      = 0;
            
            foreach ($outInvoices as $outInvoiceData) {
                $checker->setMyKey(urlencode($outInvoiceData['kod']));
                $adreser->setMyKey($outInvoiceData['firma']);
                
                $invTable->addRowColumns([
                    ++$pos,
                    new \Ease\Html\ATag($checker->getApiUrl(),
                        $outInvoiceData['kod']),
                    new \Ease\Html\ATag($adreser->getApiUrl(),
                        $outInvoiceData['firma@showAs']),
                    $outInvoiceData['sumCelkem'].' '.current(explode(':',
                            $outInvoiceData['mena@showAs']))
                ]);
            }
            $this->addItem($invTable);
        } else {
            $this->addItem(_('No invoices wainting for payments'));
        }
    }

    public function heading()
    {
        return _('Waiting Income');
    }
}
