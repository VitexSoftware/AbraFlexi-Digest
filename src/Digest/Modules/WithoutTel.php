<?php

/**
 * AbraFlexi Digest - Customers without notification phone number
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018-2023 Vitex Software
 */

namespace AbraFlexi\Digest\Modules;

/**
 *  Customers without notification phone number
 *
 * @author vitex
 */
class WithoutTel extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface
{

    /**
     * Search for Customers without notification phone number
     * 
     * @return boolean success
     */
    public function dig()
    {
        $addresser = new \AbraFlexi\Adresar();
        $withoutEmail = $addresser->getColumnsFromAbraFlexi(['nazev', 'kod', 'ulice',
            'mesto', 'email'],
                ['tel' => 'is empty', 'typVztahuK' => 'typVztahu.odberDodav']);
        if (empty($withoutEmail)) {
            $this->addItem(_('none'));
        } else {
            $noTelTable = new \AbraFlexi\Digest\Table([_('Company'), _('Street'),
                _('City'),
                _('Email')]);
            $count = 0;
            foreach ($withoutEmail as $address) {
                $addresser->setMyKey(\AbraFlexi\RO::code($address['kod']));
                if (empty($addresser->getAnyPhoneNumber())) {
                    $count++;
                    $noTelTable->addRowColumns([new \Ease\Html\ATag($addresser->getApiURL(),
                                $address['nazev']), $address['ulice'], $address['mesto'],
                        new \Ease\Html\ATag('mailto:' . $address['email'],
                                $address['email'])]);
                }
            }
            $this->addItem($this->cardBody([$noTelTable, _('Total') . ': ' . $count]));
        }
        return !empty($withoutEmail);
    }

    function heading()
    {
        return _('Customers without notification phone number');
    }
}