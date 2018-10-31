<?php
/*
 * Customers without notification email address
 */

/**
 * Description of WaitingIncome
 *
 * @author vitex
 */
class WithoutEmail extends \FlexiPeeHP\Digest\DigestModule implements \FlexiPeeHP\Digest\DigestModuleInterface
{

    public function dig()
    {
        $addresser    = new \FlexiPeeHP\Adresar();
        $withoutEmail = $addresser->getColumnsFromFlexibee(['nazev', 'kod', 'ulice',
            'mesto', 'tel'], ['email' => 'is empty','typVztahuK'=>'typVztahu.odberDodav']);


        if (empty($withoutEmail)) {
            $this->addItem(_('none'));
        } else {
            $noMailTable = new Ease\Html\TableTag(null,
                ['class' => 'pure-table']);
            $noMailTable->addRowHeaderColumns([_('Company'), _('Street'), _('City'),
                _('Phone')]);
            $count       = 0;
            foreach ($withoutEmail as $address) {
                $addresser->setMyKey(\FlexiPeeHP\FlexiBeeRO::code($address['kod']));
                if (empty($addresser->getNotificationEmailAddress())) {
                    $count++;
                    $noMailTable->addRowColumns([new \Ease\Html\ATag($addresser->getApiURL(),
                            $address['nazev']), $address['ulice'], $address['mesto'],
                        new \Ease\Html\ATag('callto:'.$address['tel'],
                            $address['tel'])]);
                }
            }
            $this->addItem($noMailTable);
            $this->addItem(_('Total').': '.$count);
        }
    }

    function heading()
    {
        return _('Customers without notification email address');
    }
}
