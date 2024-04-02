<?php

/**
 * AbraFlexi Digest - Best Sellers
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018-2023 Vitex Software
 */

namespace AbraFlexi\Digest\Modules;

/**
 * Description of NewCustomers
 *
 * @author vitex
 */
class BestSellers extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface
{
    /**
     * Column used to filter by date
     * @var string
     */
    public $timeColumn = 'datVyst';

    /**
     *
     */
    public function dig(): bool
    {
        $invoicer = new \AbraFlexi\FakturaVydana();
        $this->condition['relations'] = 'polozkyDokladu';
        $this->condition['typDokl'] = \AbraFlexi\RO::code('FAKTURA');
        $invoicesRaw = $invoicer->getColumnsFromAbraFlexi(['polozkyDokladu(cenik,nazev,sumZkl,typPolozkyK)',
            'typDokl'], $this->condition, 'kod');
        $items = [];
        if (!empty($invoicesRaw)) {
            foreach ($invoicesRaw as $invoiceCode => $invoiceData) {
                if (array_key_exists('polozkyDokladu', $invoiceData)) {
                    foreach ($invoiceData['polozkyDokladu'] as $itemRaw) {
                        $items[] = $itemRaw;
                    }
                }
            }
        }
        if (empty($items)) {
            $this->addItem(_('none'));
            return false;
        } else {
            $topProductsTable = new \AbraFlexi\Digest\Table([_('Pricelist'),
                _('Quantity'), _('Total')]);
            $products = [];
            $totals = [];
            foreach ($items as $item) {
                if ($item['typPolozkyK'] != 'typPolozky.katalog') {
                    continue;
                }


                $itemIdent = !empty($item['cenik']) ? \AbraFlexi\RO::uncode($item['cenik']) : $item['nazev'];
                if (array_key_exists($itemIdent, $products)) {
                    $products[$itemIdent]++;
                } else {
                    $products[$itemIdent] = 1;
                }

                if (array_key_exists($itemIdent, $totals)) {
                    $totals[$itemIdent] += $item['sumZkl'];
                } else {
                    $totals[$itemIdent] = floatval($item['sumZkl']);
                }
            }

            arsort($products);
            $productor = new \AbraFlexi\Cenik();
            foreach ($products as $productCode => $productInfo) {
                if ($products[$productCode] > 1) {
                    $productor->setMyKey($productCode);
                    $topProductsTable->addRowColumns([new \Ease\Html\ATag(
                        $productor->getApiURL(),
                        $productCode
                    ), $products[$productCode],
                        $totals[$productCode]]);
                }
            }

            $this->addItem($this->cardBody([$topProductsTable, new \Ease\Html\DivTag(sprintf(
                _('%d top products'),
                $topProductsTable->getItemsCount()
            ))]));
            return !empty($topProductsTable->getItemsCount());
        }
    }

    public function heading(): string
    {
        return _('Best selling products');
    }
}
