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
 * Description of IncomingPayments.
 *
 * @author vitex
 *
 * @no-named-arguments
 */
class IncomingPayments extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface
{
    public array|string $timeColumn = 'datVyst';

    /**
     * Process Incoming payments.
     */
    public function dig(): bool
    {
        $results = new \Ease\Container();
        $banker = new \AbraFlexi\Banka();
        $incomes = $banker->getColumnsFromAbraFlexi(
            ['mena', 'sumCelkem', 'sumCelkemMen'],
            array_merge(
                $this->condition,
                ['typPohybuK' => 'typPohybu.prijem', 'storno' => false],
            ),
        );
        $total = [];

        if (empty($incomes)) {
            $this->addItem(_('none'));
        } else {
            foreach ($incomes as $income) {
                $currency = self::getCurrency($income);

                if ($currency === 'CZK') {
                    $amount = (float) $income['sumCelkem'];
                } else {
                    $amount = (float) $income['sumCelkemMen'];
                }

                if (array_key_exists($currency, $total)) {
                    $total[$currency] += $amount;
                } else {
                    $total[$currency] = $amount;
                }
            }

            foreach ($total as $currency => $amount) {
                $results->addItem(new \Ease\Html\DivTag(self::formatCurrency((float) $amount).'&nbsp;'.$currency));
            }
        }

        $this->addItem($this->cardBody($results));

        return !empty($incomes);
    }

    public function heading(): string
    {
        return _('Incoming payments');
    }
}
