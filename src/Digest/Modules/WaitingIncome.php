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

use AbraFlexi\Digest\Outlook\TableTag;

/**
 * Income we wait for.
 *
 * @author vitex
 *
 * @no-named-arguments
 */
class WaitingIncome extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface
{
    public function __construct(\DatePeriod $interval)
    {
        $this->timeColumn = 'datSplat';
        parent::__construct($interval);
    }

    /**
     * Search for invoices.
     */
    public function dig(): bool
    {
        $totals = [];
        $checker = new \AbraFlexi\FakturaVydana();
        $outInvoices = $checker->getColumnsFromAbraFlexi(
            [
                'kod', 'firma', 'sumCelkem',
                'sumCelkemMen',
                'mena',
            ],
            array_merge(
                $this->condition,
                [
                    "(stavUhrK is null OR stavUhrK eq 'stavUhr.castUhr')",
                    'storno' => false,
                ],
            ),
        );

        if (empty($outInvoices)) {
            $this->addItem(_('none'));
        } else {
            $adreser = new \AbraFlexi\Adresar(null, ['offline' => 'true']);
            $invTable = new TableTag(null, ['class' => 'table']);
            $invTable->addRowHeaderColumns([
                _('Position'), _('Code'), _('Partner'),
                _('Amount'),
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
                        $outInvoiceData['kod'],
                    ),
                    new \Ease\Html\ATag(
                        $adreser->getApiUrl(),
                        (string) $outInvoiceData['firma'],
                    ),
                    (($currency !== 'CZK') ? $outInvoiceData['sumCelkemMen'] : $outInvoiceData['sumCelkem']).' '.$currency,
                ]);

                if (\array_key_exists($currency, $totals)) {
                    $totals[$currency] += (float) $outInvoiceData['sumCelkem'];
                } else {
                    $totals[$currency] = (float) $outInvoiceData['sumCelkem'];
                }
            }

            $currDiv = new \Ease\Html\DivTag();

            foreach ($totals as $currency => $amount) {
                $currDiv->addItem(new \Ease\Html\DivTag(self::formatCurrency((float) $amount).'&nbsp;'.$currency));
            }

            $this->addItem($this->cardBody([new \Ease\Html\H3Tag(_('Total')), $invTable, $currDiv]));
        }

        return !empty($outInvoices);
    }

    public function heading(): string
    {
        return _('Waiting Income');
    }
}
