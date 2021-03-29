<?php

/*
 * Customers without notification email address
 */

/**
 * Find Customers Without Email
 *
 * @author vitex
 */
class WithoutEmail extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface {

    /**
     * Find Customers Without Email
     * 
     * @return boolean
     */
    public function dig() {
        $addresser = new \AbraFlexi\Adresar();
        $withoutEmail = $addresser->getColumnsFromAbraFlexi(['nazev', 'kod', 'ulice',
            'mesto', 'tel'],
                ['email' => 'is empty', 'typVztahuK' => 'typVztahu.odberDodav']);

        if (empty($withoutEmail)) {
            $this->addItem(_('none'));
        } else {
            $noMailTable = new \AbraFlexi\Digest\Table([_('Company'), _('Street'),
                _('City'),
                _('Phone')]);
            $count = 0;
            foreach ($withoutEmail as $address) {
                $addresser->setMyKey(\AbraFlexi\RO::code($address['kod']));
                if (empty($addresser->getNotificationEmailAddress())) {
                    $count++;
                    $noMailTable->addRowColumns([new \Ease\Html\ATag($addresser->getApiURL(),
                                $address['nazev']), $address['ulice'], $address['mesto'],
                        new \Ease\Html\ATag('callto:' . $address['tel'],
                                $address['tel'])]);
                }
            }
            $this->addItem($this->cardBody([$noMailTable, _('Total') . ': ' . $count]));
                    }
        return !empty($withoutEmail);
    }

    function heading() {
        return _('Customers without notification email address');
    }

}
