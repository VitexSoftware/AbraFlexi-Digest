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

        $invoicer = new \FlexiPeeHP\FakturaVydana(null,
            ['evidence' => 'faktura-vydana-polozka']);
        $items    = $invoicer->getColumnsFromFlexibee(['cenik', 'nazev'],
            $this->condition);


        if (empty($items)) {
            $this->addItem(_('none'));
        } else {
            $topProductsTable = new \FlexiPeeHP\Digest\Table([_('Pricelist'), _('Name'), _('Quantity')]);

            $products = [];
            foreach ($items as $item) {
                $itemIdent = !empty($item['cenik']) ? \FlexiPeeHP\FlexiBeeRO::uncode($item['cenik'])
                        : $item['nazev'];
                if (array_key_exists($itemIdent, $products)) {
                    $products[$itemIdent] ++;
                } else {
                    $products[$itemIdent] = 1;
                }
            }

            arsort($products);

            foreach ($products as $productCode => $productInfo) {
                $topProductsTable->addRowColumns($productInfo);
            }
            
            $this->addItem($topProductsTable);

            $this->addItem(new \Ease\Html\DivTag(sprintf(_('%d new Customers'),
                        count($newCustomersData))));
        }
    }

    public function heading()
    {
        return _('Best selling products');
    }
}
