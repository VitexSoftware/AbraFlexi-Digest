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

namespace AbraFlexi\Digest\Modules\AllTime;

/**
 *  Purchase Price Lower Than Sales.
 *
 * @author vitex
 *
 * @no-named-arguments
 */
class PurchasePriceLowerThanSales extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface
{
    /**
     * Which records we want to see ?
     *
     * @param array $condition
     */
    public $condition = ['nakupCena' => 'is not empty', 'cenaZakl' => 'is not empty'];

    public function dig(): bool
    {
        $pricer = new \AbraFlexi\Cenik();
        $productsRaw = $pricer->getColumnsFromAbraFlexi(
            ['nazev', 'nakupCena', 'cenaZakl'],
            $this->condition,
            'kod',
        );
        $products = [];

        if (!empty($productsRaw)) {
            foreach ($productsRaw as $productsCode => $productsData) {
                if ((float) $productsData['nakupCena'] > (float) $productsData['cenaZakl']) {
                    $products[$productsData['kod']] = [
                        'kod' => $productsData['kod'],
                        'nazev' => $productsData['nazev'],
                        'nakupCena' => $productsData['nakupCena'],
                        'cenaZakl' => $productsData['cenaZakl'],
                        'provar' => $productsData['nakupCena'] - $productsData['cenaZakl'],
                    ];
                }
            }
        }

        if (empty($products)) {
            $this->addItem(_('none'));

            return false;
        }

        $topProductsTable = new \AbraFlexi\Digest\Table([
            _('Code'),
            _('Name'),
            _('Buy'),
            _('Sell'),
            _('Difference'),
        ]);
        $products = \Ease\Functions::reindexArrayBy($products, 'provar');
        krsort($products);

        foreach ($products as $productInfo) {
            $productsCode = $productInfo['kod'];
            $pricer->setMyKey($productsCode);
            $topProductsTable->addRowColumns([
                new \Ease\Html\ATag($pricer->getApiURL(), $productsCode),
                $productInfo['nazev'],
                $productInfo['nakupCena'],
                $productInfo['cenaZakl'],
                $productInfo['provar'],
            ]);
        }

        $this->addItem($this->cardBody([new \Ease\Html\DivTag(sprintf(_('%d disadvantageous products'), $products)), $topProductsTable]));

        return !empty($topProductsTable->getItemsCount());
    }

    public function heading(): string
    {
        return _('Product purchase price lower than sales');
    }
}
