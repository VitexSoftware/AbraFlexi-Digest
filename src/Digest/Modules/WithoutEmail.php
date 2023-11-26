<?php

/**
 * AbraFlexi Digest - Customers without notification email address
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018-2023 Vitex Software
 */

namespace AbraFlexi\Digest\Modules;

/**
 * Find Customers Without Email
 *
 * @author vitex
 */
class WithoutEmail extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface
{

    /**
     * Find Customers Without Email
     * 
     * @return boolean
     */
    public function dig()
    {
        $addresser = new \AbraFlexi\Adresar();
        if (\Ease\Shared::cfg('DIGEST_CHECK_SUPPLIER_CONTACT',false)) {
            $this->condition[] = 'AND (typVztahuK=typVztahu.odberDodav OR typVztahuK=typVztahu.dodavatel OR typVztahuK=typVztahu.odberatel)';
        } else {
            $this->condition[] = 'AND (typVztahuK=typVztahu.odberDodav OR typVztahuK=typVztahu.odberatel)';
        }
         
        $withoutEmail = $addresser->getColumnsFromAbraFlexi(['nazev', 'kod', 'ulice',
            'mesto', 'tel'],
                array_merge($this->condition, ['email' => 'is empty']));
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

    function heading()
    {
        return _('Customers without notification email address');
    }
}
