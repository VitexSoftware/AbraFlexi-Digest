<?php

declare(strict_types=1);

/**
 * This file is part of the AbraFlexi-Digest package
 *
 * https://github.com/VitexSoftware/AbraFlexi-Digest/
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AbraFlexi\Digest\Modules;

/**
 * Incoming invoices without payment.
 *
 * @author vitex
 */
class WaitingPayments extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface
{
    public function __construct(\DatePeriod $interval)
    {
        $this->timeColumn = 'datSplat';
        parent::__construct($interval);
    }

    /**
     * Module heading.
     */
    public function heading(): string
    {
        return _('We have to pay');
    }

    /**
     * Dig Waiting Payments.
     */
    public function dig(): bool
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
                    'storno' => false],
            ),
        );

        if (empty($inInvoices)) {
            $this->addItem(_('none'));

            return false;
        }

        $adreser = new \AbraFlexi\Adresar(null, ['offline' => 'true']);
        $invTable = new \AbraFlexi\Digest\Table([_('Position'), _('Code'), _('Partner'),
            _('Due Days'),
            _('Amount')]);
        $pos = 0;

        foreach ($inInvoices as $inInvoiceData) {
            if (self::getCurrency($inInvoiceData) !== 'CZK') {
                $amount = (float) $inInvoiceData['zbyvaUhraditMen'];
            } else {
                $amount = (float) $inInvoiceData['zbyvaUhradit'];
            }

            $currency = current(explode(':', $inInvoiceData['mena']->showAs));
            $checker->setMyKey(urlencode($inInvoiceData['kod']));
            $adreser->setMyKey($inInvoiceData['firma']);
            $invTable->addRowColumns([
                ++$pos,
                new \Ease\Html\ATag(
                    $checker->getApiUrl(),
                    $inInvoiceData['kod'],
                ),
                new \Ease\Html\ATag(
                    $adreser->getApiUrl(),
                    empty($inInvoiceData['firma']) ? '' : $inInvoiceData['firma']->showAs,
                ),
                \AbraFlexi\FakturaVydana::overdueDays($inInvoiceData['datSplat']),
                $amount.' '.current(explode(
                    ':',
                    $inInvoiceData['mena']->showAs,
                )),
            ]);

            if (\array_key_exists($currency, $totals)) {
                $totals[$currency] += (float) $inInvoiceData['sumCelkem'];
            } else {
                $totals[$currency] = (float) $inInvoiceData['sumCelkem'];
            }
        }

        $currDiv = new \Ease\Html\DivTag();

        foreach ($totals as $currency => $amount) {
            $currDiv->addItem(new \Ease\Html\DivTag(self::formatCurrency((float) $amount).'&nbsp;'.$currency));
        }

        $this->addItem($this->cardBody([new \Ease\Html\H3Tag(_('Total')), $invTable, $currDiv]));

        return !empty($inInvoices);
    }

    //    public function functionName($param)
    //    {
    //        ['datSplat lte \'' . \AbraFlexi\RW::dateToFlexiDate(new \DateTime()) . '\' AND (stavUhrK is null OR stavUhrK eq \'stavUhr.castUhr\') AND storno eq false'];
    //    }
}
