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

/**
 * Description of WaitingIncome.
 *
 * @author vitex
 */
class Debtors extends DigestModule implements DigestModuleInterface
{
    /**
     * Who does no pay its bills ?
     *
     * @return bool success
     */
    public function dig(): bool
    {
        $invoicer = new \AbraFlexi\FakturaVydana();
        $cond = ['datSplat lte \''.\AbraFlexi\RW::dateToFlexiDate(new \DateTime()).'\' AND (stavUhrK is null OR stavUhrK eq \'stavUhr.castUhr\') AND storno eq false ', 
            "(not(typDokl.typDoklK eq 'typDokladu.dobropis'))",
            'includes'=>'faktura-vydana/typDokl/',
            'limit' => 0]; // AND typDoklK != \'typDokladu.dobropis\'

        $faDatakturyRaw = $invoicer->getColumnsFromAbraFlexi(
            ['kod', 'firma', 'sumCelkem', 'typDokl(typDoklK)',
                'sumCelkemMen', 'zbyvaUhradit', 'zbyvaUhraditMen', 'mena', 'datSplat'],
            $cond,
        );
        $totals = [];
        $totalsByCurrency = [];
        $overdue = [];

        if (empty($faDatakturyRaw)) {
            $invoicer->addStatusMessage(sprintf(_('Invoices: %d'), 0));
        } else {
            $invoicer->addStatusMessage(sprintf(_('Invoices: %d'), \count($faDatakturyRaw)));

            foreach ($faDatakturyRaw as $faData) {
                $currency = self::getCurrency($faData);
                $invoicesByFirma[(string) $faData['firma']][$faData['id']] = $faData;

                if (!isset($totals[(string) $faData['firma']][$currency])) {
                    $totals[(string) $faData['firma']][$currency] = 0;
                }

                if (!isset($totalsByCurrency[$currency])) {
                    $totalsByCurrency[$currency] = 0;
                }

                if ($currency !== 'CZK') {
                    $amount = (float) $faData['zbyvaUhraditMen'];
                } else {
                    $amount = (float) $faData['zbyvaUhradit'];
                }

                $totals[(string) $faData['firma']][$currency] += $amount;
                $totalsByCurrency[$currency] += $amount;
                $oDays = \AbraFlexi\FakturaVydana::overdueDays($faData['datSplat']);

                if (\array_key_exists((string) $faData['firma'], $overdue)) {
                    if ($oDays > $overdue[(string) $faData['firma']]) {
                        $overdue[(string) $faData['firma']] = $oDays;
                    }
                } else {
                    $overdue[(string) $faData['firma']] = $oDays;
                }
            }
        }

        if (empty($invoicesByFirma)) {
            $this->addItem(_('none'));
        } else {
            $adreser = new \AbraFlexi\Adresar(null, ['offline' => 'true']);
            $invTable = new TableTag(null, ['class' => 'table']);
            $invTable->addRowHeaderColumns([_('Company'), _('Overdue days'), _('Invoices'), _('Amount')]);

            foreach ($invoicesByFirma as $firma => $fakturyFirmy) {
                $overdueInvoices = new \Ease\Html\DivTag();

                foreach ($fakturyFirmy as $invoiceData) {
                    $invoicer->setMyKey($invoiceData['id']);
                    $currency = self::getCurrency($invoiceData);
                    $overdueInvoice = \AbraFlexi\Functions::uncode((string) $invoiceData['kod']);
                    $overdueInvoices->addItem(new \Ease\Html\DivTag([new \Ease\Html\ATag(
                        $invoicer->getApiURL(),
                        $overdueInvoice,
                        ['css' => 'margin: 5px;'],
                    ),
                        '&nbsp;<small>'.(($currency !== 'CZK') ? $invoiceData['zbyvaUhraditMen'] : $invoiceData['zbyvaUhradit']).' '.$currency.' '.\AbraFlexi\FakturaVydana::overdueDays($invoiceData['datSplat']).' '._('days').'</small>']));
                }

                $adreser->setMyKey($firma);
                $invoice = current($fakturyFirmy);
                $nazevFirmy = \array_key_exists('firma', $invoice) && \is_object($invoice['firma']) && $invoice['firma']->showAs ? $invoice['firma']->showAs : \AbraFlexi\Functions::uncode((string) $firma);
                $invTable->addRowColumns([
                    new \Ease\Html\ATag($adreser->getApiURL(), $nazevFirmy),
                    $overdue[$firma],
                    $overdueInvoices,
                    self::getTotalsDiv($totals[$firma]),
                ]);
            }

            $invTable->addRowFooterColumns(['', '', _('Total'), self::getTotalsDiv($totalsByCurrency)]);
            $this->addItem($this->cardBody($invTable));
        }

        return !empty($invoicesByFirma);
    }

    public function heading(): string
    {
        return _('Debtors');
    }
}
