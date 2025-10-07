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
 * Description of NewCustomers.
 *
 * @author vitex
 *
 * @no-named-arguments
 */
class BestSellers extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface
{
    public function __construct(\DatePeriod $interval)
    {
        $this->timeColumn = 'datVyst';
        parent::__construct($interval);
    }

    public function dig(): bool
    {
        $invoicer = new \AbraFlexi\FakturaVydana();
        $this->condition['relations'] = 'polozkyDokladu';
        $this->condition['typDokl'] = \AbraFlexi\Functions::code((string) 'FAKTURA');
        $invoicesRaw = $invoicer->getColumnsFromAbraFlexi(['polozkyDokladu(cenik,nazev,sumZkl,typPolozkyK)',
            'typDokl'], $this->condition, 'kod');
        $items = [];

        if (!empty($invoicesRaw)) {
            foreach ($invoicesRaw as $invoiceCode => $invoiceData) {
                if (\array_key_exists('polozkyDokladu', $invoiceData)) {
                    foreach ($invoiceData['polozkyDokladu'] as $itemRaw) {
                        $items[] = $itemRaw;
                    }
                }
            }
        }

        if (empty($items)) {
            $this->addItem(_('none'));

            return false;
        }

        $topProductsTable = new TableTag(null, ['class' => 'table']);
        $topProductsTable->addRowHeaderColumns([_('Pricelist'),
            _('Quantity'), _('Total')]);
        $products = [];
        $totals = [];

        foreach ($items as $item) {
            if ($item['typPolozkyK'] !== 'typPolozky.katalog') {
                continue;
            }

            $itemIdent = !empty($item['cenik']) ? \AbraFlexi\Functions::uncode((string) $item['cenik']) : $item['nazev'];

            if (\array_key_exists($itemIdent, $products)) {
                ++$products[$itemIdent];
            } else {
                $products[$itemIdent] = 1;
            }

            if (\array_key_exists($itemIdent, $totals)) {
                $totals[$itemIdent] += $item['sumZkl'];
            } else {
                $totals[$itemIdent] = (float) $item['sumZkl'];
            }
        }

        arsort($products);
        $productor = new \AbraFlexi\Cenik();

        foreach ($products as $productCode => $productInfo) {
            if ($products[$productCode] > 1) {
                $productor->setMyKey($productCode);
                $topProductsTable->addRowColumns([new \Ease\Html\ATag(
                    $productor->getApiURL(),
                    $productCode,
                ), $products[$productCode],
                    $totals[$productCode]]);
            }
        }

        $this->addItem($this->cardBody([$topProductsTable, new \Ease\Html\DivTag(sprintf(
            _('%d top products'),
            $topProductsTable->getItemsCount(),
        ))]));

        return !empty($topProductsTable->getItemsCount());
    }

    public function heading(): string
    {
        return _('Best selling products');
    }
}
