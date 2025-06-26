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

use AbraFlexi\ui\DocumentLink;
use Ease\Html\DivTag;

/**
 * Incoming payments for us.
 *
 * @author vitex
 */
class UnmatchedInvoices extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface
{
    public array|string $timeColumn = 'datVyst';

    /**
     * Process Incoming payments.
     */
    public function dig(): bool
    {
        $invoicer = new \AbraFlexi\FakturaVydana(null, ['nativeTypes' => false]);
        $adresser = new \AbraFlexi\Adresar();
        $proformas = $invoicer->getColumnsFromAbraFlexi(
            ['kod', 'mena', 'popis', 'sumCelkem',
                'sumCelkemMen', 'stavOdpocetK', 'typDokl', 'firma', 'datVyst'],
            array_merge(
                $this->condition,
                ['typPohybuK' => 'typPohybu.prijem', 'storno' => false,
                    'zuctovano' => false,
                    'typDokl.typDoklK' => 'typDokladu.zalohFaktura',
                    'stavUhrK' => 'stavUhr.uhrazeno'],
            ),
            'datVyst',
        );
        $total = [];
        $totals = [];

        if (empty($proformas)) {
            $this->addItem($this->cardBody(_('none')));
        } else {
            $incomesTable = new \AbraFlexi\Digest\Table([_('Document'), _('Description'),
                _('Denunc state'), _('Document type'), _('Company'), _('Date'), _('Amount')]);

            foreach ($proformas as $proforma) {
                switch ($proforma['stavOdpocetK']) {
                    case 'stavOdp.komplet':
                    case 'stavOdp.vytvZdd':
                        break;

                    default:
                        unset($proforma['external-ids'], $proforma['id'], $proforma['typDokl@ref']);

                        $adresser->takeData($proforma);
                        $amount = self::getAmount($proforma);
                        $currency = self::getCurrency($proforma);

                        if (\array_key_exists($currency, $total)) {
                            $total[$currency] += $amount;
                            ++$totals[$currency];
                        } else {
                            $total[$currency] = $amount;
                            $totals[$currency] = 1;
                        }

                        $proforma['kod'] = new DocumentLink($invoicer, $proforma['kod']);
                        $proforma['price'] = self::getPrice($proforma);
                        $proforma['firma'] = new \AbraFlexi\Digest\CompanyLink(
                            $proforma['firma'],
                            $adresser,
                        );
                        unset($proforma['typDokl'], $proforma['sumCelkem'], $proforma['sumCelkemMen'], $proforma['mena'], $proforma['mena@ref'], $proforma['mena@showAs'], $proforma['stavOdpocetK'], $proforma['firma@ref'], $proforma['firma@showAs']);

                        $incomesTable->addRowColumns($proforma);

                        break;
                }
            }

            $currDiv = new DivTag();

            foreach ($total as $currency => $amount) {
                $currDiv->addItem(new \Ease\Html\DivTag($totals[$currency].'x '.self::formatCurrency((float) $amount).'&nbsp;'.$currency));
            }

            $this->addItem($this->cardBody([$incomesTable, $currDiv]));
        }

        return !empty($total);
    }

    /**
     * module Heading.
     */
    public function heading(): string
    {
        return _('Non-deducted proformas');
    }

    /**
     * Default Description.
     *
     * @return string
     */
    public function description()
    {
        return _('Non-deducted proformas');
    }
}
