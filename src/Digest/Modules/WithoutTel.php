<?php

declare(strict_types=1);

/**
 * This file is part of the AbraFlexi-Digest package
 *
 * https://github.com/VitexSoftware/AbraFlexi-Digest/
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AbraFlexi\Digest\Modules;

/**
 *  Customers without notification phone number.
 *
 * @author vitex
 */
class WithoutTel extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface
{
    /**
     * Search for Customers without notification phone number.
     *
     * @return bool success
     */
    public function dig(): bool
    {
        if (\Ease\Shared::cfg('DIGEST_CHECK_SUPPLIER_CONTACT', false)) {
            $this->condition[] = "(typVztahuK='typVztahu.odberDodav' OR typVztahuK='typVztahu.dodavatel' OR typVztahuK='typVztahu.odberatel')";
        } else {
            $this->condition[] = "(typVztahuK='typVztahu.odberDodav' OR typVztahuK='typVztahu.odberatel')";
        }

        $addresser = new \AbraFlexi\Adresar();
        $withoutPhone = $addresser->getColumnsFromAbraFlexi(
            [
                'nazev',
                'kod',
                'ulice',
                'mesto',
                'email',
            ],
            array_merge($this->condition, ['tel is empty AND mobil is empty']),
        );

        if (empty($withoutPhone)) {
            $this->addItem(_('none'));
        } else {
            $noTelTable = new \AbraFlexi\Digest\Table([
                _('Company'),
                _('Street'),
                _('City'),
                _('Email')]);
            $count = 0;

            foreach ($withoutPhone as $id => $address) {
                $addresser->setMyKey(\AbraFlexi\Functions::code((string) $address['kod']));
                $phoneNumber = $addresser->getAnyPhoneNumber();

                if (empty($phoneNumber)) {
                    ++$count;
                    $noTelTable->addRowColumns([new \Ease\Html\ATag(
                        $addresser->getApiURL(),
                        $address['nazev'],
                    ), $address['ulice'], $address['mesto'],
                        new \Ease\Html\ATag(
                            'mailto:'.$address['email'],
                            $address['email'],
                        )]);
                } else {
                    unset($withoutPhone[$id]);
                }
            }

            if (\count($withoutPhone)) {
                $this->addItem($this->cardBody([$noTelTable, _('Total').': '.$count]));
            }
        }

        return !empty($withoutPhone);
    }

    /**
     * Module Headnig.
     */
    public function heading(): string
    {
        return _('Customers without notification phone number');
    }
}
