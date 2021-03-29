<?php

/*
 * Outcoming Invoices
 */

use AbraFlexi\Digest\DigestModule;
use AbraFlexi\Digest\DigestModuleInterface;
use AbraFlexi\ui\DocumentLink;
use AbraFlexi\Digest\Table;

/**
 * Description of OutcomingInvoices
 *
 * @author vitex
 */
class OutcomingInvoicesHiddenToCustomer extends DigestModule implements DigestModuleInterface {

    /**
     * Column used to filter by date
     * @var string 
     */
    public $timeColumn = 'datVyst';

    public function dig() {
        $digger = new \AbraFlexi\FakturaVydana();
        $outInvoicesData = $digger->getColumnsFromAbraFlexi(['kod', 'typDokl', 'firma',
            'stavMailK', 'kontaktEmail'],
                array_merge($this->condition,
                        ['((stavMailK eq \'stavMail.odeslat\') OR (stavMailK is empty))', 'storno' => false]));

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

            $outInvoicesTable = new Table($tableHeader);

            foreach ($outInvoicesData as $outInvoiceData) {

                $addresser->setMyKey($outInvoiceData['firma']);

                if (!empty($outInvoiceData['stavMailK'])) {
                    $outInvoiceData['stavMailK'] = _('to send');
                }

                $outInvoiceData['firma@showAs'] = empty($outInvoiceData['firma']) ? '' : new DocumentLink(\AbraFlexi\RW::code($outInvoiceData['firma']), $addresser);

                $outInvoiceData['kod'] = new DocumentLink(\AbraFlexi\RW::code($outInvoiceData['kod']),
                        $digger);

                $outInvoiceData['custcontact'] = $addresser->getNotificationEmailAddress();

                if (!empty($outInvoiceData['kontaktEmail'])) {
                    $outInvoiceData['kontaktEmail'] = new \Ease\Html\ATag('mailto:' . $outInvoiceData['kontaktEmail'],
                            $outInvoiceData['kontaktEmail']);
                }
                if (!empty($outInvoiceData['custcontact'])) {
                    $outInvoiceData['custcontact'] = new \Ease\Html\ATag('mailto:' . $outInvoiceData['custcontact'],
                            $outInvoiceData['custcontact']);
                }


                unset($outInvoiceData['id']);
                unset($outInvoiceData['external-ids']);
                unset($outInvoiceData['typDokl']);
                unset($outInvoiceData['typDokl@ref']);
                unset($outInvoiceData['firma@ref']);
                unset($outInvoiceData['stavMailK@showAs']);
                unset($outInvoiceData['firma']);
                $outInvoicesTable->addRowColumns($outInvoiceData);
            }

            $tableFooter = [count($outInvoicesData) . ' ' . _('items'), '', '', '', '',
                ''];

            $outInvoicesTable->addRowFooterColumns($tableFooter);

            $this->addItem( $this->cardBody($outInvoicesTable));
        }
        return !empty($outInvoicesData);
    }

    /**
     * "Outcoming invoices" heading
     * 
     * @return string
     */
    public function heading() {
        return _('Outcoming invoices not notified to customer');
    }

    public function description() {
        
    }

}
