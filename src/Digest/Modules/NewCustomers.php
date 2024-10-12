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

/*
 * New Customers
 */

namespace AbraFlexi\Digest\Modules;

/**
 * Description of NewCustomers.
 *
 * @author vitex
 */
class NewCustomers extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface
{
    public function __construct(\DatePeriod $interval)
    {
        $this->timeColumn = 'lastUpdate';
        parent::__construct($interval);
    }

    public function dig(): bool
    {
        $digger = new \AbraFlexi\Adresar();
        $newCustomersData = $digger->getColumnsFromAbraFlexi(['kod', 'nazev', 'tel',
            'email'], $this->condition);
        $typDoklRaw = [];

        if (empty($newCustomersData)) {
            $this->addItem(_('none'));
        } else {
            $userTable = new \AbraFlexi\Digest\Table([_('Position'), _('Code'),
                _('Name'),
                _('Email'), _('Phone')]);

            foreach ($newCustomersData as $pos => $newCustomerData) {
                $digger->setMyKey(\AbraFlexi\RO::code($newCustomerData['kod']));
                $userTable->addRowColumns([
                    $pos,
                    new \Ease\Html\ATag(
                        $digger->getApiURL(),
                        $newCustomerData['kod'],
                    ),
                    $newCustomerData['nazev'],
                    new \Ease\Html\ATag(
                        'mailto:'.$newCustomerData['email'],
                        $newCustomerData['email'],
                    ),
                    new \Ease\Html\ATag(
                        'callto:'.$newCustomerData['tel'],
                        $newCustomerData['tel'],
                    ),
                ]);
            }

            $this->addItem($this->cardBody(
                [
                    $userTable,
                    new \Ease\Html\DivTag(sprintf(_('%d new Customers'), \count($newCustomersData))),
                ],
            ));
        }

        return !empty($newCustomersData);
    }

    /**
     * "New or updated customers" heading.
     */
    public function heading(): string
    {
        return _('New or updated customers');
    }
}
