<?php
/*
 * New Customers
 */

/**
 * Description of NewCustomers
 *
 * @author vitex
 */
class NewCustomers extends \FlexiPeeHP\Digest\DigestModule implements \FlexiPeeHP\Digest\DigestModuleInterface
{

    public function dig()
    {
        $digger           = new FlexiPeeHP\Adresar();
        $newCustomersData = $digger->getColumnsFromFlexibee(['kod', 'nazev', 'tel',
            'email'], ['lastUpdate' => $this->interval]);

        $typDoklRaw = [];

        if (empty($newCustomersData)) {
            $this->addItem(_('No new/changed clients'));
        } else {
            $userTable = new Ease\Html\TableTag(null, ['class' => 'pure-table']);
            $userTable->addRowHeaderColumns([_('Position'), _('Code'), _('Name'),
                _('Email'), _('Phone')]);

            foreach ($newCustomersData as $pos => $newCustomerData) {
                $userTable->addRowColumns([$pos, $newCustomerData['kod'], $newCustomerData['nazev'],
                    $newCustomerData['email'], $newCustomerData['tel']]);
            }

            $this->addItem($userTable);

            $this->addItem(new \Ease\Html\DivTag(sprintf(_('%d new Customers'),
                    count($newCustomersData))));
        }
    }

    public function heading()
    {
        return _('New or updated customers');
    }
}
