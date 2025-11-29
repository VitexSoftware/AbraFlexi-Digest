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
 * Description of OutcomingInvoices.
 *
 * @author vitex
 *
 * @no-named-arguments
 */
class OutcomingInvoices extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface
{
    public function __construct(\DatePeriod $interval)
    {
        $this->timeColumn = 'datVyst';
        parent::__construct($interval);
    }

    public function dig(): bool
    {
        $digger = new \AbraFlexi\FakturaVydana();
        $outInvoicesData = $digger->getColumnsFromAbraFlexi(['kod', 'typDokl', 'sumCelkem',
            'sumCelkemMen',
            'sumZalohy', 'sumZalohyMen', 'uhrazeno', 'storno', 'mena', 'juhSum',
            'juhSumMen'], $this->condition);
        $exposed = 0;
        $invoicedRaw = [];
        $paid = [];
        $storno = 0;
        $typDoklCounts = [];
        $typDoklTotals = [];

        if (empty($outInvoicesData)) {
            $this->addItem(_('none'));
        } else {
            foreach ($outInvoicesData as $outInvoiceData) {
                ++$exposed;

                if ($outInvoiceData['storno'] === 'true') {
                    ++$storno;
                }

                $currency = self::getCurrency($outInvoiceData);
                $typDokl = (string) $outInvoiceData['typDokl'];

                if ($currency !== 'CZK') {
                    $amount = (float) $outInvoiceData['sumCelkemMen'] + (float) $outInvoiceData['sumZalohyMen'];
                } else {
                    $amount = (float) $outInvoiceData['sumCelkem'] + (float) $outInvoiceData['sumZalohy'];
                }

                if (!\array_key_exists($typDokl, $typDoklTotals)) {
                    $typDoklTotals[$typDokl] = [];
                }

                if (!\array_key_exists($currency, $typDoklTotals[$typDokl])) {
                    $typDoklTotals[$typDokl][$currency] = 0;
                }

                if (\array_key_exists($typDokl, $typDoklCounts)) {
                    ++$typDoklCounts[$typDokl];
                    $typDoklTotals[$typDokl][$currency] += $amount;
                } else {
                    $typDoklCounts[$typDokl] = 1;
                    $typDoklTotals[$typDokl][$currency] = $amount;
                }

                if (\array_key_exists($currency, $invoicedRaw)) {
                    $invoicedRaw[$currency] += $amount;
                } else {
                    $invoicedRaw[$currency] = $amount;
                }
            }

            $tableHeader[] = _('Count');
            $tableHeader[] = _('Document type');
            $currencies = array_keys($invoicedRaw);

            foreach ($currencies as $currencyCode) {
                $tableHeader[] = _('Total').' '.\AbraFlexi\Functions::uncode((string) $currencyCode);
            }

            $outInvoicesTable = new TableTag(null, ['class' => 'table']);
            $outInvoicesTable->addRowHeaderColumns($tableHeader);

            foreach ($typDoklTotals as $typDokl => $typDoklTotal) {
                $tableRow = [$typDoklCounts[$typDokl]];
                $tableRow[] = \AbraFlexi\Functions::uncode((string) $typDokl);

                foreach ($currencies as $currencyCode) {
                    $tableRow[] = \array_key_exists(
                        $currencyCode,
                        $typDoklTotals[$typDokl],
                    ) ? $typDoklTotals[$typDokl][$currencyCode] : '';
                }

                $outInvoicesTable->addRowColumns($tableRow);
            }

            $tableFooter = [$exposed, \count(array_keys($typDoklTotals))];

            foreach ($currencies as $currencyCode) {
                $tableFooter[] = self::formatCurrency((float) $invoicedRaw[$currencyCode]).' '.\AbraFlexi\Functions::uncode((string) $currencyCode);
            }

            $outInvoicesTable->addRowFooterColumns($tableFooter);
            $this->addItem($this->cardBody($outInvoicesTable));
        }

        return !empty($outInvoicesData);
    }

    /**
     * "Outcoming invoices" heading.
     */
    public function heading(): string
    {
        return _('Outcoming invoices');
    }
}
