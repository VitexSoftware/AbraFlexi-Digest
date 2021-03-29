<?php

/**
 * Sent Reminds overview
 *
 * @author vitex
 */
class Reminds extends \AbraFlexi\Digest\DigestModule implements \AbraFlexi\Digest\DigestModuleInterface {

    /**
     * Reminds dates
     * @var array 
     */
    public $timeColumn = ['datUp1', 'datUp2', 'datSmir'];

    /**
     * Count several reminds
     * @var array of int
     */
    private $remids = ['datUp1' => 0, 'datUp2' => 0, 'datSmir' => 0];

    public function dig() {
        $invoicer = new \AbraFlexi\FakturaVydana();

        $faDatakturyRaw = $invoicer->getColumnsFromAbraFlexi(['kod', 'firma', 'popis',
            'sumCelkem', 'sumCelkemMen',
            'zbyvaUhradit', 'zbyvaUhraditMen', 'mena', 'datUp1', 'datUp2', 'datSmir'],
                $this->condition);
        if (empty($faDatakturyRaw)) {
            $this->addItem(_('none'));
        } else {
            $invoicer->addStatusMessage("Faktur: " . count($faDatakturyRaw));
            $adreser = new AbraFlexi\Adresar(null, ['offline' => 'true']);
            $invTable = new \AbraFlexi\Digest\Table([
                _('Client'),
                _('Invoice'),
                _('Amount'),
                _('Remind #1'),
                _('Remind #2'),
                _('Remind #3')
            ]);

            $overDues = [];

            foreach ($faDatakturyRaw as $invoice => $invoiceData) {

                $this->countReminds($invoiceData);

                $adreser->setMyKey($invoiceData['firma']);
                $invoicer->setMyKey(\AbraFlexi\RO::code($invoiceData['kod']));

                $nazevFirmy = array_key_exists('firma@showAs', $invoiceData) ? $invoiceData['firma@showAs'] : \AbraFlexi\RO::uncode($invoiceData['firma']);

                $invTable->addRowColumns([
                    new \Ease\Html\ATag($adreser->getApiURL(), $nazevFirmy),
                    new \Ease\Html\ATag($invoicer->getApiURL(),
                            trim($invoiceData['kod'] . ' ' . $invoiceData['popis'])),
                    (($invoiceData['mena'] != 'code:CZK') ? $invoiceData['zbyvaUhraditMen'] : $invoiceData['zbyvaUhradit']) .
                    ' ' . \AbraFlexi\RO::uncode($invoiceData['mena']),
                    empty($invoiceData['datUp1']) ? '' : $this->myDate($invoiceData['datUp1']),
                    empty($invoiceData['datUp2']) ? '' : $this->myDate($invoiceData['datUp2']),
                    empty($invoiceData['datSmir']) ? '' : $this->myDate($invoiceData['datSmir'])
                ]);
            }

            $invTable->addRowFooterColumns([count($faDatakturyRaw), '', '', $this->remids['datUp1'],
                $this->remids['datUp2'], $this->remids['datSmir']]);

                $this->addItem($this->cardBody($invTable));
        }
        return !empty($faDatakturyRaw);
    }

    /**
     * 
     * @param array $rowData
     */
    public function countReminds(array $rowData) {
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
     * count date is within digest date interval 
     * 
     * @param \DateTime $date
     * @param string $column
     */
    public function countRemind(\DateTime $date, string $column) {
        if (!array_key_exists($column, $this->remids)) {
            $this->remids[$column] = 0;
        }
        if (!empty($date) && $this->isMyDate($date)) {
            if (array_key_exists($column, $this->remids)) {
                $this->remids[$column]++;
            }
        }
    }

    /**
     * bold Date in digest interval
     * 
     * @param string $flexidate
     * 
     * @return mixed
     */
    public function myDate(\DateTime $flexidate) {
        if ($this->isMyDate($flexidate)) {
            $humanDate = new Ease\Html\StrongTag(self::humanDate($flexidate));
        } else {
            $humanDate = self::humanDate($flexidate);
        }
        return $humanDate;
    }

    /**
     * 
     * @return string
     */
    function heading() {
        return _('Reminds');
    }

}
