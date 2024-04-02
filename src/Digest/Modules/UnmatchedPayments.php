<?php

/**
 * AbraFlexi Digest - Incoming payments for us
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2018-2023 Vitex Software
 */

namespace AbraFlexi\Digest\Modules;

/**
 * Payments without invoices
 *
 * @author vitex
 */
class UnmatchedPayments extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface
{
    public $timeColumn = 'datVyst';

    /**
     * Process Incoming payments
     *
     * @return boolean
     */
    public function dig(): bool
    {
        $banker = new \AbraFlexi\Banka(null, ['nativeTypes' => false]);
        $adresser = new \AbraFlexi\Adresar();
        $bucer = new \AbraFlexi\Adresar(
            null,
            ['evidence' => 'adresar-bankovni-ucet']
        );
        $incomes = $banker->getColumnsFromAbraFlexi(
            ['kod', 'mena', 'popis', 'sumCelkem',
            'sumCelkemMen',
            'buc', 'firma', 'datVyst'],
            array_merge(
                $this->condition,
                ['typPohybuK' => 'typPohybu.prijem', 'storno' => false,
                            'zuctovano' => false,
                'sparovano' => false]
            ),
            'datVyst'
        );
        $total = [];
        if (empty($incomes)) {
            $this->addItem(_('none'));
        } else {
            $incomesTable = new \AbraFlexi\Digest\Table([_('Document'), _('Description'),
                _('Bank Account'), _('Company'), _('Date'), _('Amount')]);
            foreach ($incomes as $income) {
                $adresser->dataReset();
                if (empty((string) $income['firma']) && !empty($income['buc'])) {
                    $candidates = $bucer->getColumnsFromAbraFlexi(
                        ['firma'],
                        ['buc' => $income['buc']]
                    );
                    if (!empty($candidates)) {
                        $income['firma'] = $candidates[0]['firma']->showAs;
                    }
                }
                $adresser->takeData($income);
                $amount = self::getAmount($income);
                $currency = self::getCurrency($income);
                if (array_key_exists($currency, $total)) {
                    $total[$currency] += $amount;
                } else {
                    $total[$currency] = $amount;
                }

                $income['kod'] = new \AbraFlexi\ui\DocumentLink('code:' . $income['kod'], $banker);
                $income['price'] = self::getAmount($income) . ' ' . $currency;
                $adresser->setMyKey($adresser);
//                $income['firma'] = new \Ease\Html\ATag(empty($income['firma']->showAs) ? $adresser->getApiUrl() . $income['firma'] : $income['firma']->showAs);
                unset($income['id']);
                unset($income['sumCelkem']);
                unset($income['sumCelkemMen']);
                unset($income['mena']);
                unset($income['mena@ref']);
                unset($income['mena@showAs']);
                $incomesTable->addRowColumns($income);
            }
            $currDiv = new \Ease\Html\DivTag();
            foreach ($total as $currency => $amount) {
                $currDiv->addItem(new \Ease\Html\DivTag(self::formatCurrency($amount) . '&nbsp;' . $currency));
            }
            $this->addItem($this->cardBody([$incomesTable, $currDiv]));
        }
        return !empty($incomes);
    }

    /**
     * @inheritDoc
     */
    public function heading(): string
    {
        return _('Unmatched payments');
    }

    /**
     * Default Description
     *
     * @return string
     */
    public function description()
    {
        return _('Unrecognized and non-deducted earnings');
    }
}
