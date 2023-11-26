<?php

/**
 * AbraFlexi Digest - What we have to pay
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018-2023 Vitex Software
 */

namespace AbraFlexi\Digest\Modules;

/**
 * Incoming invoices without payment
 *
 * @author vitex
 */
class WaitingPayments extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface
{
    /**
     * Module heading
     *
     * @return string
     */
    public function heading()
    {
        return _('We have to pay');
    }

    /**
     * Column used to filter by date
     * @var string
     */
    public $timeColumn = 'datSplat';

    /**
     * Dig Waiting Payments
     *
     * @return type
     */
    public function dig()
    {
        $totals = [];
        $checker = new \AbraFlexi\FakturaPrijata();
        $inInvoices = $checker->getColumnsFromAbraFlexi(
            ['kod', 'firma', 'sumCelkem',
            'zbyvaUhradit', 'zbyvaUhraditMen', 'datSplat',
            'mena'],
            array_merge(
                $this->condition,
                ["(stavUhrK is null OR stavUhrK eq 'stavUhr.castUhr')",
                'storno' => false]
            )
        );
        if (empty($inInvoices)) {
            $this->addItem(_('none'));
        } else {
            $adreser = new \AbraFlexi\Adresar(null, ['offline' => 'true']);
            $invTable = new \AbraFlexi\Digest\Table([_('Position'), _('Code'), _('Partner'),
                _('Due Days'),
                _('Amount')]);
            $pos = 0;
            foreach ($inInvoices as $inInvoiceData) {
                if (self::getCurrency($inInvoiceData) != 'CZK') {
                    $amount = floatval($inInvoiceData['zbyvaUhraditMen']);
                } else {
                    $amount = floatval($inInvoiceData['zbyvaUhradit']);
                }

                $currency = current(explode(':', $inInvoiceData['mena']->showAs));
                $checker->setMyKey(urlencode($inInvoiceData['kod']));
                $adreser->setMyKey($inInvoiceData['firma']);
                $invTable->addRowColumns([
                    ++$pos,
                    new \Ease\Html\ATag(
                        $checker->getApiUrl(),
                        $inInvoiceData['kod']
                    ),
                    new \Ease\Html\ATag(
                        $adreser->getApiUrl(),
                        empty($inInvoiceData['firma']) ? '' : $inInvoiceData['firma']->showAs
                    ),
                    \AbraFlexi\FakturaVydana::overdueDays($inInvoiceData['datSplat']),
                    $amount . ' ' . current(explode(
                        ':',
                        $inInvoiceData['mena']->showAs
                    ))
                ]);
                if (array_key_exists($currency, $totals)) {
                    $totals[$currency] += floatval($inInvoiceData['sumCelkem']);
                } else {
                    $totals[$currency] = floatval($inInvoiceData['sumCelkem']);
                }
            }

            $currDiv = new \Ease\Html\DivTag();
            foreach ($totals as $currency => $amount) {
                $currDiv->addItem(new \Ease\Html\DivTag(self::formatCurrency($amount) . '&nbsp;' . $currency));
            }
            $this->addItem($this->cardBody([new \Ease\Html\H3Tag(_('Total')), $invTable, $currDiv]));
        }

        return !empty($inInvoices);
    }

    /**
     *
     * @param type $param
     */
    public function functionName($param)
    {
        ['datSplat lte \'' . \AbraFlexi\RW::dateToFlexiDate(new \DateTime()) . '\' AND (stavUhrK is null OR stavUhrK eq \'stavUhr.castUhr\') AND storno eq false'];
    }
}
