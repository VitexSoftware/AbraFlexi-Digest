<?php

/**
 * AbraFlexi Digest - WaitingIncome
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018-2023 Vitex Software
 */

namespace AbraFlexi\Digest\Modules;

/**
 * Income we wait for
 *
 * @author vitex
 */
class WaitingIncome extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface
{
    /**
     * Column used to filter by date
     * @var string
     */
    public $timeColumn = 'datSplat';

    /**
     * Seach for invoices
     *
     * @return boolean
     */
    public function dig()
    {
        $totals = [];
        $checker = new \AbraFlexi\FakturaVydana();
        $outInvoices = $checker->getColumnsFromAbraFlexi(
            [
                    'kod', 'firma', 'sumCelkem',
                    'sumCelkemMen',
                    'mena'
                ],
            array_merge(
                $this->condition,
                [
                            "(stavUhrK is null OR stavUhrK eq 'stavUhr.castUhr')",
                            'storno' => false
                        ]
            )
        );
        if (empty($outInvoices)) {
            $this->addItem(_('none'));
        } else {
            $adreser = new \AbraFlexi\Adresar(null, ['offline' => 'true']);
            $invTable = new \AbraFlexi\Digest\Table([
                _('Position'), _('Code'), _('Partner'),
                _('Amount')
            ]);
            $pos = 0;
            foreach ($outInvoices as $outInvoiceData) {
                $currency = self::getCurrency($outInvoiceData);
                $checker->setMyKey(urlencode($outInvoiceData['kod']));
                $adreser->setMyKey($outInvoiceData['firma']);
                $invTable->addRowColumns([
                    ++$pos,
                    new \Ease\Html\ATag(
                        $checker->getApiUrl(),
                        $outInvoiceData['kod']
                    ),
                    new \Ease\Html\ATag(
                        $adreser->getApiUrl(),
                        (string) $outInvoiceData['firma']
                    ),
                    (($currency != 'CZK') ? $outInvoiceData['sumCelkemMen'] : $outInvoiceData['sumCelkem']) . ' ' . $currency
                ]);
                if (array_key_exists($currency, $totals)) {
                    $totals[$currency] += floatval($outInvoiceData['sumCelkem']);
                } else {
                    $totals[$currency] = floatval($outInvoiceData['sumCelkem']);
                }
            }

            $currDiv = new \Ease\Html\DivTag();
            foreach ($totals as $currency => $amount) {
                $currDiv->addItem(new \Ease\Html\DivTag(self::formatCurrency($amount) . '&nbsp;' . $currency));
            }
            $this->addItem($this->cardBody([new \Ease\Html\H3Tag(_('Total')), $invTable, $currDiv]));
        }
        return !empty($outInvoices);
    }

    public function heading()
    {
        return _('Waiting Income');
    }
}
