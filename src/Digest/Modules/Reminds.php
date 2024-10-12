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
 * Sent Reminds overview.
 *
 * @author vitex
 */
class Reminds extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface
{
    /**
     * Count several reminds.
     *
     * @var array of int
     */
    private array $remids = ['datUp1' => 0, 'datUp2' => 0, 'datSmir' => 0];

    public function __construct(\DatePeriod $interval)
    {
        $this->timeColumn = ['datUp1', 'datUp2', 'datSmir'];
        parent::__construct($interval);
    }

    public function dig(): bool
    {
        $invoicer = new \AbraFlexi\FakturaVydana();
        $faDatakturyRaw = $invoicer->getColumnsFromAbraFlexi(
            ['kod', 'firma', 'popis',
                'sumCelkem', 'sumCelkemMen',
                'zbyvaUhradit', 'zbyvaUhraditMen', 'mena', 'datUp1', 'datUp2', 'datSmir'],
            $this->condition,
        );

        if (empty($faDatakturyRaw)) {
            $this->addItem(_('none'));
        } else {
            $invoicer->addStatusMessage('Faktur: '.\count($faDatakturyRaw));
            $adreser = new \AbraFlexi\Adresar(null, ['offline' => 'true']);
            $invTable = new \AbraFlexi\Digest\Table([
                _('Client'),
                _('Invoice'),
                _('Amount'),
                _('Remind #1'),
                _('Remind #2'),
                _('Remind #3'),
            ]);
            $overDues = [];

            foreach ($faDatakturyRaw as $invoice => $invoiceData) {
                $this->countReminds($invoiceData);
                $adreser->setMyKey($invoiceData['firma']);
                $invoicer->setMyKey(\AbraFlexi\Functions::code((string)$invoiceData['kod']));
                $nazevFirmy = \AbraFlexi\Functions::uncode((string)(string)$invoiceData['firma']);
                $invTable->addRowColumns([
                    new \Ease\Html\ATag($adreser->getApiURL(), $nazevFirmy),
                    new \Ease\Html\ATag(
                        $invoicer->getApiURL(),
                        trim($invoiceData['kod'].' '.$invoiceData['popis']),
                    ),
                    (($invoiceData['mena'] !== 'code:CZK') ? $invoiceData['zbyvaUhraditMen'] : $invoiceData['zbyvaUhradit']).
                    ' '.\AbraFlexi\Functions::uncode((string)(string)$invoiceData['mena']),
                    empty($invoiceData['datUp1']) ? '' : $this->myDate($invoiceData['datUp1']),
                    empty($invoiceData['datUp2']) ? '' : $this->myDate($invoiceData['datUp2']),
                    empty($invoiceData['datSmir']) ? '' : $this->myDate($invoiceData['datSmir']),
                ]);
            }

            $invTable->addRowFooterColumns([\count($faDatakturyRaw), '', '', $this->remids['datUp1'],
                $this->remids['datUp2'], $this->remids['datSmir']]);
            $this->addItem($this->cardBody($invTable));
        }

        return !empty($faDatakturyRaw);
    }

    public function countReminds(array $rowData): void
    {
        if (!empty($rowData['datUp1'])) {
            $this->countRemind($rowData['datUp1'], 'datUp1');
        }

        if (!empty($rowData['datUp2'])) {
            $this->countRemind($rowData['datUp2'], 'datUp2');
        }

        if (!empty($rowData['datSmir'])) {
            $this->countRemind($rowData['datSmir'], 'datSmir');
        }
    }

    /**
     * count date is within digest date interval.
     */
    public function countRemind(\DateTime $date, string $column): void
    {
        if (!\array_key_exists($column, $this->remids)) {
            $this->remids[$column] = 0;
        }

        if ($this->isMyDate($date)) {
            if (\array_key_exists($column, $this->remids)) {
                ++$this->remids[$column];
            }
        }
    }

    /**
     * bold Date in digest interval.
     *
     * @return mixed
     */
    public function myDate(\DateTime $flexidate)
    {
        if ($this->isMyDate($flexidate)) {
            $humanDate = new \Ease\Html\StrongTag(self::humanDate($flexidate));
        } else {
            $humanDate = self::humanDate($flexidate);
        }

        return $humanDate;
    }

    public function heading(): string
    {
        return _('Reminds');
    }
}
