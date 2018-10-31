<?php
/*
 * Customers without notification phone number
 */

/**
 * Description of WaitingIncome
 *
 * @author vitex
 */
class WithoutTel extends \FlexiPeeHP\Digest\DigestModule implements \FlexiPeeHP\Digest\DigestModuleInterface
{

    public function dig()
    {
        $addresser    = new \FlexiPeeHP\Adresar();
        $withoutEmail = $addresser->getColumnsFromFlexibee(['nazev', 'kod', 'ulice',
            'mesto', 'email'], ['tel' => 'is empty']);


        if (empty($withoutEmail)) {
            $this->addItem(_('none'));
        } else {
            $noTelTable = new Ease\Html\TableTag(null, ['class' => 'pure-table']);
            $noTelTable->addRowHeaderColumns([_('Company'), _('Street'), _('City'),
                _('Email')]);
            $count      = 0;
            foreach ($withoutEmail as $address) {
                $addresser->setMyKey(\FlexiPeeHP\FlexiBeeRO::code($address['kod']));
                if (empty($addresser->getAnyPhoneNumber())) {
                    $count++;
                    $noTelTable->addRowColumns([new \Ease\Html\ATag($addresser->getApiURL(),
                            $address['nazev']), $address['ulice'], $address['mesto'],
                        new \Ease\Html\ATag('mailto:'.$address['email'],
                            $address['email'])]);
                }
            }
            $this->addItem($noTelTable);
            $this->addItem(_('Total').': '.$count);
        }
    }

    function heading()
    {
        return _('Customers without notification phone number');
    }
}
