<?php
/*
 * New Customers
 */

/**
 * Description of NewCustomers
 *
 * @author vitex
 */
class NewCustomers extends \FlexiPeeHP\DigestMail\DigestModule implements \FlexiPeeHP\DigestMail\DigestModuleInterface
{

    public function dig()
    {
        $digger           = new FlexiPeeHP\Adresar();
        $newCustomersData = $digger->getColumnsFromFlexibee(['kod', 'nazev'],
            ['lastUpdate' => $this->interval]);

        $typDoklRaw = [];

        $custList = [];
        foreach ($newCustomersData as $newCustomerData) {
            $custList[] = $newCustomerData['kod'].': '.$newCustomerData['nazev'];
        }

        $this->addItem(new \Ease\Html\DivTag(sprintf(_('%d new Customers'),
                count($newCustomersData))));
        $this->addItem(implode(',', $custList));
    }

    public function heading()
    {
        return _('New or updated customers');
    }
}
