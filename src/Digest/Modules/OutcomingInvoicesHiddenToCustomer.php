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

use AbraFlexi\Digest\DigestModule;
use AbraFlexi\Digest\DigestModuleInterface;
use AbraFlexi\Digest\Outlook\TableTag;
use AbraFlexi\ui\TWB5\DocumentLink;

/**
 * Description of OutcomingInvoices.
 *
 * @author vitex
 *
 * @no-named-arguments
 */
class OutcomingInvoicesHiddenToCustomer extends DigestModule implements DigestModuleInterface
{
    public function __construct(\DatePeriod $interval)
    {
        $this->timeColumn = 'datVyst';
        parent::__construct($interval);
    }

    public function dig(): bool
    {
        $digger = new \AbraFlexi\FakturaVydana();
        $outInvoicesData = $digger->getColumnsFromAbraFlexi(
            [
                'kod',
                'typDokl',
                'firma',
                'stavMailK',
                'kontaktEmail',
            ],
            array_merge(
                $this->condition,
                ['((stavMailK eq \'stavMail.odeslat\') OR (stavMailK is empty))', 'storno' => false],
            ),
        );

        if (empty($outInvoicesData)) {
            $this->addItem(_('none'));
        } else {
            $addresser = new \AbraFlexi\Adresar();
            $tableHeader[] = _('Code');
            $tableHeader[] = _('Document subject');
            $tableHeader[] = _('Customer');
            $tableHeader[] = _('Mail status');
            $tableHeader[] = _('Document Contact');
            $tableHeader[] = _('Customer\'s Contact');
            $outInvoicesTable = new TableTag($tableHeader);

            foreach ($outInvoicesData as $outInvoiceData) {
                $addresser->setMyKey($outInvoiceData['firma']);

                if (!empty($outInvoiceData['stavMailK'])) {
                    $outInvoiceData['stavMailK'] = _('to send');
                }

                $outInvoiceData['firma'] = empty($outInvoiceData) ? '' : new DocumentLink($addresser, (string) $outInvoiceData['firma']);
                $outInvoiceData['kod'] = new DocumentLink($digger, \AbraFlexi\Functions::code($outInvoiceData['kod']));
                $outInvoiceData['custcontact'] = $addresser->getNotificationEmailAddress();

                if (!empty($outInvoiceData['kontaktEmail'])) {
                    $outInvoiceData['kontaktEmail'] = new \Ease\Html\ATag(
                        'mailto:'.$outInvoiceData['kontaktEmail'],
                        $outInvoiceData['kontaktEmail'],
                    );
                }

                if (!empty($outInvoiceData['custcontact'])) {
                    $outInvoiceData['custcontact'] = new \Ease\Html\ATag(
                        'mailto:'.$outInvoiceData['custcontact'],
                        $outInvoiceData['custcontact'],
                    );
                }

                unset($outInvoiceData['id'], $outInvoiceData['external-ids'], $outInvoiceData['typDokl'], $outInvoiceData['firma']);

                $outInvoicesTable->addRowColumns($outInvoiceData);
            }

            $tableFooter = [\count($outInvoicesData).' '._('items'), '', '', '', '', ''];
            $outInvoicesTable->addRowFooterColumns($tableFooter);
            $this->addItem($this->cardBody($outInvoicesTable));
        }

        return !empty($outInvoicesData);
    }

    /**
     * "Outgoing invoices" heading.
     */
    public function heading(): string
    {
        return _('Issued invoices not notified to the client');
    }

    public function description(): void
    {
    }
}
