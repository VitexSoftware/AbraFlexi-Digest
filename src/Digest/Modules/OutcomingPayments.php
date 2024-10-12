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
 * Description of OutcomingPayments.
 *
 * @author vitex
 */
class OutcomingPayments extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface
{
    public function __construct(\DatePeriod $interval)
    {
        $this->timeColumn = 'datVyst';
        parent::__construct($interval);
    }

    /**
     * Check outgoing Payments.
     */
    public function dig(): bool
    {
        $banker = new \AbraFlexi\Banka();
        $outcomes = $banker->getColumnsFromAbraFlexi(
            ['mena', 'sumCelkem',
                'sumCelkemMen'],
            array_merge(
                $this->condition,
                ['typPohybuK' => 'typPohybu.vydej', 'storno' => false],
            ),
        );
        $total = [];

        if (empty($outcomes)) {
            $this->addItem(_('none'));
        } else {
            foreach ($outcomes as $outcome) {
                $currency = self::getCurrency($outcome);

                if ($currency !== 'CZK') {
                    $amount = (float) $outcome['sumCelkemMen'];
                } else {
                    $amount = (float) $outcome['sumCelkem'];
                }

                if (\array_key_exists($currency, $total)) {
                    $total[$currency] += $amount;
                } else {
                    $total[$currency] = $amount;
                }
            }

            $totalsTable = new \AbraFlexi\Digest\Table([_('Amount'), _('Currency')]);

            foreach ($total as $currency => $amount) {
                $totalsTable->addRowColumns([self::formatCurrency($amount), $currency]);
            }

            $this->addItem($this->cardBody($totalsTable));
        }

        return !empty($outcomes);
    }

    public function heading(): string
    {
        return _('Outcoming payments');
    }
}
