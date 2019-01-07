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
    /**
     * Column used to filter by date
     * @var string 
     */
    public $timeColumn = 'lastUpdate';

    public function dig()
    {
        $digger           = new FlexiPeeHP\Adresar();
        $newCustomersData = $digger->getColumnsFromFlexibee(['kod', 'nazev', 'tel',
            'email'], $this->condition);

        $typDoklRaw = [];

        if (empty($newCustomersData)) {
            $this->addItem(_('none'));
        } else {
            $userTable = new \FlexiPeeHP\Digest\Table([_('Position'), _('Code'),
                _('Name'),
                _('Email'), _('Phone')]);

            foreach ($newCustomersData as $pos => $newCustomerData) {
                $digger->setMyKey(FlexiPeeHP\FlexiBeeRO::code($newCustomerData['kod']));
                $userTable->addRowColumns([
                    $pos,
                    new \Ease\Html\ATag($digger->getApiURL(),
                        $newCustomerData['kod']),
                    $newCustomerData['nazev'],
                    new \Ease\Html\ATag('mailto:'.$newCustomerData['email'],
                        $newCustomerData['email']),
                    new \Ease\Html\ATag('callto:'.$newCustomerData['tel'],
                        $newCustomerData['tel'])
                ]);
            }

            $this->addItem($userTable);

            $this->addItem(new \Ease\Html\DivTag(sprintf(_('%d new Customers'),
                    count($newCustomersData))));
        }
        return !empty($inInvoicesData);
    }

    /**
     * "New or updated customers" heading
     * 
     * @return string
     */
    public function heading()
    {
        return _('New or updated customers');
    }
}
