<?php
/*
 * New Customers
 */

/**
 * Description of NewCustomers
 *
 * @author vitex
 */
class BestSellers extends \FlexiPeeHP\Digest\DigestModule implements \FlexiPeeHP\Digest\DigestModuleInterface
{
    /**
     * Column used to filter by date
     * @var string 
     */
    public $timeColumn = 'datVyst';

    public function dig()
    {

        $invoicer                     = new \FlexiPeeHP\FakturaVydana();
        $this->condition['relations'] = 'polozkyDokladu';
        $this->condition['typDokl']   = FlexiPeeHP\FlexiBeeRO::code('FAKTURA');
        $invoicesRaw                  = $invoicer->getColumnsFromFlexibee(['polozkyDokladu(cenik,nazev,sumZkl,typPolozkyK)',
            'typDokl'], $this->condition, 'kod');

        $items = [];
        foreach ($invoicesRaw as $invoiceCode => $invoiceData) {
            if (array_key_exists('polozkyDokladu', $invoiceData))
                    foreach ($invoiceData['polozkyDokladu'] as $itemRaw) {
                    $items[] = $itemRaw;
                }
        }

        if (empty($items)) {
            $this->addItem(_('none'));
        } else {
            $topProductsTable = new \FlexiPeeHP\Digest\Table([_('Pricelist'),
                _('Quantity'), _('Total')]);

            $products = [];
            $totals   = [];
            foreach ($items as $item) {
                if($item['typPolozkyK']!='typPolozky.katalog'){
                    continue;
                }
                
                
                $itemIdent = !empty($item['cenik']) ? \FlexiPeeHP\FlexiBeeRO::uncode($item['cenik'])
                        : $item['nazev'];
                if (array_key_exists($itemIdent, $products)) {
                    $products[$itemIdent] ++;
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

            $productor = new FlexiPeeHP\Cenik();

            foreach ($products as $productCode => $productInfo) {
                $productor->setMyKey($productCode);
                $topProductsTable->addRowColumns([new \Ease\Html\ATag($productor->getApiURL(),
                        $productCode), $products[$productCode],
                    $totals[$productCode]]);
            }

            $this->addItem($topProductsTable);

            $this->addItem(new \Ease\Html\DivTag(sprintf(_('%d top products'),
                        count($topProductsTable))));
        }
    }

    public function heading()
    {
        return _('Best selling products');
    }
}
