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

namespace AbraFlexi\Digest\Providers;

use VitexSoftware\DigestModules\Core\DataProviderInterface;

/**
 * AbraFlexi data provider
 *
 * Fetches data from AbraFlexi via the php-abraflexi library and normalizes
 * all returned records to the neutral field schema defined by DataProviderInterface.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class AbraFlexiDataProvider implements DataProviderInterface
{
    /** @var array<string, string> Maps ENTITY_* → AbraFlexi evidence name */
    private array $entityMapping = [
        DataProviderInterface::ENTITY_OUTCOMING_INVOICES => 'faktura-vydana',
        DataProviderInterface::ENTITY_INCOMING_INVOICES  => 'faktura-prijata',
        DataProviderInterface::ENTITY_BANK_STATEMENTS    => 'banka',
        DataProviderInterface::ENTITY_CONTACTS           => 'adresar',
        DataProviderInterface::ENTITY_PRODUCTS           => 'cenik',
        DataProviderInterface::ENTITY_REMINDERS          => 'faktura-vydana',
        DataProviderInterface::ENTITY_ORDERS             => 'objednavka-prijata',
    ];

    /** @var array<string, string> Maps neutral DATE_COLUMN_* → AbraFlexi field */
    private array $dateColumnMapping = [
        DataProviderInterface::DATE_COLUMN_ISSUE_DATE     => 'datVyst',
        DataProviderInterface::DATE_COLUMN_DUE_DATE       => 'datSplat',
        DataProviderInterface::DATE_COLUMN_LAST_UPDATED   => 'lastUpdate',
        DataProviderInterface::DATE_COLUMN_FIRST_REMINDER => 'datUp1',
    ];

    /** @var array<string> Standard columns requested for invoices */
    private const INVOICE_COLUMNS = [
        'kod', 'firma', 'sumCelkem', 'sumCelkemMen', 'sumZalohy', 'sumZalohyMen',
        'zbyvaUhradit', 'zbyvaUhraditMen', 'mena', 'datVyst', 'datSplat',
        'storno', 'typDokl', 'stavUhrK', 'stavMailK', 'popis', 'kontaktEmail',
        'stavOdpocetK', 'datUp1', 'datUp2', 'datSmir',
    ];

    /** @var array<string> Standard columns requested for bank statements */
    private const BANK_COLUMNS = [
        'kod', 'firma', 'buc', 'sumCelkem', 'sumCelkemMen', 'mena',
        'datVyst', 'storno', 'popis', 'zuctovano', 'sparovano', 'typPohybuK',
    ];

    /** @var array<string> Standard columns requested for contacts */
    private const CONTACT_COLUMNS = ['kod', 'nazev', 'email', 'tel', 'ulice', 'mesto'];

    /** @var array<string> Standard columns requested for products */
    private const PRODUCT_COLUMNS = ['kod', 'nazev', 'nakupCena', 'cenaZakl'];

    public function __construct(
        private readonly array $config = [],
    ) {
    }

    /**
     * {@inheritDoc}
     */
    public function getData(string $entity, array $conditions = [], array $columns = []): array
    {
        if (!isset($this->entityMapping[$entity])) {
            throw new \InvalidArgumentException("Unsupported entity: $entity");
        }

        $abraFlexiEntity = $this->entityMapping[$entity];
        $instance = $this->createInstance($abraFlexiEntity);

        if (!$instance) {
            throw new \RuntimeException("Could not create AbraFlexi instance for: $entity");
        }

        [$wqlConditions, $hasItems] = $this->buildConditions($conditions);

        $entityColumns = $this->resolveColumns($entity, $hasItems);
        $raw = $instance->getColumnsFromAbraFlexi($entityColumns, $wqlConditions);

        if (!is_array($raw)) {
            return [];
        }

        return $this->normalize($entity, $raw, $hasItems);
    }

    /**
     * {@inheritDoc}
     */
    public function getSystemName(): string
    {
        return 'abraflexi';
    }

    /**
     * {@inheritDoc}
     */
    public function getSupportedEntities(): array
    {
        return array_keys($this->entityMapping);
    }

    /**
     * {@inheritDoc}
     */
    public function supportsFeature(string $feature): bool
    {
        return \in_array($feature, [
            'date_filtering', 'currency_conversion', 'document_types',
            'payment_status', 'custom_fields', 'relations',
        ], true);
    }

    /**
     * {@inheritDoc}
     */
    public function getCompanyInfo(): array
    {
        try {
            if (class_exists('\\AbraFlexi\\Company')) {
                $company = new \AbraFlexi\Company();
                $data = $company->getFlexiData();

                if (is_array($data) && !empty($data)) {
                    $first = reset($data);

                    return [
                        'name'   => $first['nazev'] ?? 'Unknown',
                        'code'   => $first['dbNazev'] ?? '',
                        'system' => 'AbraFlexi',
                        'url'    => $company->getApiURL() ?? '',
                    ];
                }
            }
        } catch (\Exception) {
            // fall through
        }

        return ['name' => 'AbraFlexi Company', 'system' => 'AbraFlexi'];
    }

    /**
     * {@inheritDoc}
     */
    public function formatDate(\DateTime $date): string
    {
        return $date->format('Y-m-d');
    }

    /**
     * {@inheritDoc}
     */
    public function formatDatePeriod(string $column, \DatePeriod $period): string
    {
        $abraCol = $this->dateColumnMapping[$column] ?? $column;
        $start   = $this->formatDate($period->getStartDate());
        $end     = $this->formatDate($period->getEndDate());

        return "$abraCol between '$start' and '$end'";
    }

    // ── Private helpers ────────────────────────────────────────────────────

    /**
     * Build AbraFlexi WQL conditions array from neutral filter keys.
     *
     * @param array<string, mixed> $conditions Neutral filter conditions
     * @return array{array<mixed>, bool} [wqlConditions, hasItems]
     */
    private function buildConditions(array $conditions): array
    {
        $wql      = [];
        $hasItems = false;

        foreach ($conditions as $key => $value) {
            switch ($key) {
                case DataProviderInterface::FILTER_LIMIT:
                    $wql['limit'] = (int) $value;
                    break;

                case DataProviderInterface::FILTER_DATE_PERIOD:
                    $this->applyDatePeriod($value, $wql);
                    break;

                case DataProviderInterface::FILTER_PAYMENT_DIRECTION:
                    $this->applyPaymentDirection((string) $value, $wql);
                    break;

                case DataProviderInterface::FILTER_PAYMENT_STATUS:
                    $this->applyPaymentStatus((string) $value, $wql);
                    break;

                case DataProviderInterface::FILTER_CANCELLED:
                    $wql['storno'] = (bool) $value;
                    break;

                case DataProviderInterface::FILTER_ACCOUNTED:
                    $wql['zuctovano'] = (bool) $value;
                    break;

                case DataProviderInterface::FILTER_MATCHED:
                    $wql['sparovano'] = (bool) $value;
                    break;

                case DataProviderInterface::FILTER_DOCUMENT_TYPE:
                    $this->applyDocumentType((string) $value, $wql);
                    break;

                case DataProviderInterface::FILTER_EXCLUDE_DOCUMENT_TYPE:
                    $this->applyExcludeDocumentType((string) $value, $wql);
                    break;

                case DataProviderInterface::FILTER_MAIL_PENDING:
                    if ($value) {
                        $wql[] = "(stavMailK eq 'stavMail.odeslat' OR stavMailK is null)";
                    }
                    break;

                case DataProviderInterface::FILTER_MISSING_EMAIL:
                    if ($value) {
                        $wql['email'] = 'is empty';
                    }
                    break;

                case DataProviderInterface::FILTER_MISSING_PHONE:
                    if ($value) {
                        $wql[] = 'tel is empty AND mobil is empty';
                    }
                    break;

                case DataProviderInterface::FILTER_WITH_ITEMS:
                    if ($value) {
                        $wql['relations'] = 'polozkyDokladu';
                        $hasItems         = true;
                    }
                    break;

                case DataProviderInterface::FILTER_OVERDUE:
                    if ($value) {
                        $wql[] = "datSplat lte '" . date('Y-m-d') . "'";
                    }
                    break;

                case DataProviderInterface::FILTER_HAS_BUY_PRICE:
                    if ($value) {
                        $wql['nakupCena'] = 'is not empty';
                    }
                    break;

                case DataProviderInterface::FILTER_HAS_SELL_PRICE:
                    if ($value) {
                        $wql['cenaZakl'] = 'is not empty';
                    }
                    break;

                case DataProviderInterface::FILTER_RELATIONSHIP:
                    $this->applyRelationship((string) $value, $wql);
                    break;
            }
        }

        return [$wql, $hasItems];
    }

    private function applyDatePeriod(mixed $value, array &$wql): void
    {
        if (!is_array($value) || !isset($value['column'], $value['period'])) {
            return;
        }

        $wql[] = $this->formatDatePeriod($value['column'], $value['period']);
    }

    private function applyPaymentDirection(string $value, array &$wql): void
    {
        $wql['typPohybuK'] = match ($value) {
            DataProviderInterface::DIRECTION_INCOMING => 'typPohybu.prijem',
            DataProviderInterface::DIRECTION_OUTGOING => 'typPohybu.vydej',
            default => $value,
        };
    }

    private function applyPaymentStatus(string $value, array &$wql): void
    {
        match ($value) {
            DataProviderInterface::PAYMENT_STATUS_UNPAID_OR_PARTIAL =>
                $wql[] = "(stavUhrK is null OR stavUhrK eq 'stavUhr.castUhr')",

            DataProviderInterface::PAYMENT_STATUS_PAID =>
                $wql['stavUhrK'] = 'stavUhr.uhrazeno',

            DataProviderInterface::PAYMENT_STATUS_UNPAID =>
                $wql[] = 'stavUhrK is null',

            default => null,
        };
    }

    private function applyDocumentType(string $value, array &$wql): void
    {
        $abraKey = match ($value) {
            DataProviderInterface::DOCUMENT_TYPE_INVOICE     => 'typDokladu.faktura',
            DataProviderInterface::DOCUMENT_TYPE_PROFORMA    => 'typDokladu.zalohFaktura',
            DataProviderInterface::DOCUMENT_TYPE_CREDIT_NOTE => 'typDokladu.dobropis',
            default => $value,
        };

        $wql['typDokl.typDoklK'] = $abraKey;
    }

    private function applyExcludeDocumentType(string $value, array &$wql): void
    {
        if ($value === DataProviderInterface::DOCUMENT_TYPE_CREDIT_NOTE) {
            $wql[] = "not(typDokl.typDoklK eq 'typDokladu.dobropis')";
        }
    }

    private function applyRelationship(string $value, array &$wql): void
    {
        if ($value === 'customer') {
            $wql[] = "(typVztahuK='typVztahu.odberDodav' OR typVztahuK='typVztahu.odberatel')";
        } elseif ($value === 'supplier') {
            $wql[] = "typVztahuK='typVztahu.dodavatel'";
        }
    }

    /** @return array<string> */
    private function resolveColumns(string $entity, bool $hasItems): array
    {
        return match ($entity) {
            DataProviderInterface::ENTITY_OUTCOMING_INVOICES,
            DataProviderInterface::ENTITY_INCOMING_INVOICES,
            DataProviderInterface::ENTITY_REMINDERS => $hasItems
                ? array_merge(self::INVOICE_COLUMNS, ['polozkyDokladu(cenik,nazev,sumZkl,typPolozkyK)'])
                : self::INVOICE_COLUMNS,

            DataProviderInterface::ENTITY_BANK_STATEMENTS => self::BANK_COLUMNS,
            DataProviderInterface::ENTITY_CONTACTS        => self::CONTACT_COLUMNS,
            DataProviderInterface::ENTITY_PRODUCTS        => self::PRODUCT_COLUMNS,

            default => ['*'],
        };
    }

    /**
     * Normalize raw AbraFlexi records to neutral schema.
     *
     * @param array<array<string, mixed>> $raw
     * @return array<array<string, mixed>>
     */
    private function normalize(string $entity, array $raw, bool $hasItems): array
    {
        return match ($entity) {
            DataProviderInterface::ENTITY_OUTCOMING_INVOICES,
            DataProviderInterface::ENTITY_INCOMING_INVOICES,
            DataProviderInterface::ENTITY_REMINDERS =>
                array_map(fn ($r) => $this->normalizeInvoice($r, $hasItems), $raw),

            DataProviderInterface::ENTITY_BANK_STATEMENTS =>
                array_map($this->normalizeBank(...), $raw),

            DataProviderInterface::ENTITY_CONTACTS =>
                array_map($this->normalizeContact(...), $raw),

            DataProviderInterface::ENTITY_PRODUCTS =>
                array_map($this->normalizeProduct(...), $raw),

            default => $raw,
        };
    }

    /** @param array<string, mixed> $inv */
    private function normalizeInvoice(array $inv, bool $hasItems): array
    {
        return [
            DataProviderInterface::FIELD_CODE                     => (string) ($inv['kod'] ?? ''),
            DataProviderInterface::FIELD_DATE                     => $this->toDateString($inv['datVyst'] ?? null),
            DataProviderInterface::FIELD_DUE_DATE                 => $this->toDateString($inv['datSplat'] ?? null),
            DataProviderInterface::FIELD_COMPANY                  => $this->extractCompany($inv['firma'] ?? null),
            DataProviderInterface::FIELD_TOTAL_AMOUNT             => (float) ($inv['sumCelkem'] ?? 0),
            DataProviderInterface::FIELD_TOTAL_AMOUNT_FOREIGN     => (float) ($inv['sumCelkemMen'] ?? 0),
            DataProviderInterface::FIELD_REMAINING_AMOUNT         => (float) ($inv['zbyvaUhradit'] ?? 0),
            DataProviderInterface::FIELD_REMAINING_AMOUNT_FOREIGN => (float) ($inv['zbyvaUhraditMen'] ?? 0),
            DataProviderInterface::FIELD_DEPOSIT_AMOUNT           => (float) ($inv['sumZalohy'] ?? 0),
            DataProviderInterface::FIELD_DEPOSIT_AMOUNT_FOREIGN   => (float) ($inv['sumZalohyMen'] ?? 0),
            DataProviderInterface::FIELD_CURRENCY                 => $this->extractCurrency($inv['mena'] ?? null),
            DataProviderInterface::FIELD_CANCELLED                => $this->toBool($inv['storno'] ?? false),
            DataProviderInterface::FIELD_DOCUMENT_TYPE            => $this->normalizeDocumentType($inv['typDokl'] ?? null),
            DataProviderInterface::FIELD_PAYMENT_STATUS           => $this->normalizePaymentStatus($inv['stavUhrK'] ?? null),
            DataProviderInterface::FIELD_MAIL_STATUS              => $this->normalizeMailStatus($inv['stavMailK'] ?? null),
            DataProviderInterface::FIELD_DESCRIPTION              => (string) ($inv['popis'] ?? ''),
            DataProviderInterface::FIELD_CONTACT_EMAIL            => (string) ($inv['kontaktEmail'] ?? ''),
            DataProviderInterface::FIELD_DEDUCTION_STATUS         => $this->normalizeDeductionStatus($inv['stavOdpocetK'] ?? null),
            DataProviderInterface::FIELD_FIRST_REMINDER_DATE      => $this->toDateString($inv['datUp1'] ?? null),
            DataProviderInterface::FIELD_SECOND_REMINDER_DATE     => $this->toDateString($inv['datUp2'] ?? null),
            DataProviderInterface::FIELD_PRE_LITIGATION_DATE      => $this->toDateString($inv['datSmir'] ?? null),
            DataProviderInterface::FIELD_ITEMS                    => $hasItems ? $this->normalizeItems($inv) : [],
        ];
    }

    /** @param array<string, mixed> $pay */
    private function normalizeBank(array $pay): array
    {
        $typPohybu = $pay['typPohybuK'] ?? '';
        if (is_array($typPohybu)) {
            $typPohybu = $typPohybu['typPohybuK'] ?? '';
        }

        return [
            DataProviderInterface::FIELD_CODE                   => (string) ($pay['kod'] ?? ''),
            DataProviderInterface::FIELD_DATE                   => $this->toDateString($pay['datVyst'] ?? null),
            DataProviderInterface::FIELD_COMPANY                => $this->extractCompany($pay['firma'] ?? null),
            DataProviderInterface::FIELD_BANK_ACCOUNT           => (string) ($pay['buc'] ?? ''),
            DataProviderInterface::FIELD_TOTAL_AMOUNT           => (float) ($pay['sumCelkem'] ?? 0),
            DataProviderInterface::FIELD_TOTAL_AMOUNT_FOREIGN   => (float) ($pay['sumCelkemMen'] ?? 0),
            DataProviderInterface::FIELD_CURRENCY               => $this->extractCurrency($pay['mena'] ?? null),
            DataProviderInterface::FIELD_DIRECTION              => $typPohybu === 'typPohybu.prijem'
                ? DataProviderInterface::DIRECTION_INCOMING
                : DataProviderInterface::DIRECTION_OUTGOING,
            DataProviderInterface::FIELD_CANCELLED              => $this->toBool($pay['storno'] ?? false),
            DataProviderInterface::FIELD_DESCRIPTION            => (string) ($pay['popis'] ?? ''),
            DataProviderInterface::FIELD_ACCOUNTED              => $this->toBool($pay['zuctovano'] ?? false),
            DataProviderInterface::FIELD_MATCHED                => $this->toBool($pay['sparovano'] ?? false),
        ];
    }

    /** @param array<string, mixed> $contact */
    private function normalizeContact(array $contact): array
    {
        return [
            DataProviderInterface::FIELD_CODE   => (string) ($contact['kod'] ?? ''),
            DataProviderInterface::FIELD_NAME   => (string) ($contact['nazev'] ?? ''),
            DataProviderInterface::FIELD_EMAIL  => (string) ($contact['email'] ?? ''),
            DataProviderInterface::FIELD_PHONE  => (string) ($contact['tel'] ?? ''),
            DataProviderInterface::FIELD_STREET => (string) ($contact['ulice'] ?? ''),
            DataProviderInterface::FIELD_CITY   => (string) ($contact['mesto'] ?? ''),
        ];
    }

    /** @param array<string, mixed> $product */
    private function normalizeProduct(array $product): array
    {
        return [
            DataProviderInterface::FIELD_CODE       => (string) ($product['kod'] ?? ''),
            DataProviderInterface::FIELD_NAME       => (string) ($product['nazev'] ?? ''),
            DataProviderInterface::FIELD_BUY_PRICE  => (float) ($product['nakupCena'] ?? 0),
            DataProviderInterface::FIELD_SELL_PRICE => (float) ($product['cenaZakl'] ?? 0),
        ];
    }

    /**
     * Normalize invoice line items.
     *
     * @param array<string, mixed> $inv Invoice record
     * @return array<array<string, mixed>>
     */
    private function normalizeItems(array $inv): array
    {
        $items = $inv['polozkyDokladu'] ?? [];

        if (!is_array($items)) {
            return [];
        }

        $result = [];

        foreach ($items as $item) {
            if (!is_array($item)) {
                continue;
            }

            $typPolozky = $item['typPolozkyK'] ?? '';
            $itemType   = match ($typPolozky) {
                'typPolozky.katalog' => DataProviderInterface::ITEM_TYPE_CATALOG,
                'typPolozky.text'    => DataProviderInterface::ITEM_TYPE_TEXT,
                default              => (string) $typPolozky,
            };

            $result[] = [
                'product_code' => (string) ($item['cenik'] ?? ''),
                'name'         => (string) ($item['nazev'] ?? ''),
                'amount'       => (float) ($item['sumZkl'] ?? 0),
                'item_type'    => $itemType,
            ];
        }

        return $result;
    }

    private function extractCurrency(mixed $mena): string
    {
        if (is_array($mena)) {
            $mena = $mena['kod'] ?? '';
        }

        $currency = str_replace('code:', '', (string) $mena);

        return $currency !== '' ? $currency : 'CZK';
    }

    private function extractCompany(mixed $firma): string
    {
        if (is_array($firma)) {
            return (string) ($firma['nazev'] ?? '');
        }

        return (string) ($firma ?? '');
    }

    private function toDateString(mixed $value): string
    {
        if ($value === null || $value === '') {
            return '';
        }

        if ($value instanceof \DateTime) {
            return $value->format('Y-m-d');
        }

        return (string) $value;
    }

    private function toBool(mixed $value): bool
    {
        return $value === true || $value === 'true' || $value === 1;
    }

    private function normalizePaymentStatus(mixed $stavUhr): string
    {
        if (is_array($stavUhr)) {
            $stavUhr = $stavUhr['stavUhrazenikK'] ?? null;
        }

        return match ((string) ($stavUhr ?? '')) {
            'stavUhr.uhrazeno' => DataProviderInterface::PAYMENT_STATUS_PAID,
            'stavUhr.castUhr'  => DataProviderInterface::PAYMENT_STATUS_PARTIAL,
            default            => DataProviderInterface::PAYMENT_STATUS_UNPAID,
        };
    }

    private function normalizeMailStatus(mixed $stavMail): string
    {
        if (is_array($stavMail)) {
            $stavMail = $stavMail['stavMailK'] ?? null;
        }

        return match ((string) ($stavMail ?? '')) {
            'stavMail.odeslano' => DataProviderInterface::MAIL_STATUS_SENT,
            'stavMail.odeslat'  => DataProviderInterface::MAIL_STATUS_PENDING,
            default             => DataProviderInterface::MAIL_STATUS_EMPTY,
        };
    }

    private function normalizeDocumentType(mixed $typDokl): string
    {
        if (is_array($typDokl)) {
            $typDoklK = $typDokl['typDoklK'] ?? '';
        } else {
            $typDoklK = (string) ($typDokl ?? '');
        }

        return match ($typDoklK) {
            'typDokladu.zalohFaktura' => DataProviderInterface::DOCUMENT_TYPE_PROFORMA,
            'typDokladu.dobropis'     => DataProviderInterface::DOCUMENT_TYPE_CREDIT_NOTE,
            'typDokladu.faktura'      => DataProviderInterface::DOCUMENT_TYPE_INVOICE,
            default                   => $typDoklK,
        };
    }

    private function normalizeDeductionStatus(mixed $stavOdp): string
    {
        return match ((string) ($stavOdp ?? '')) {
            'stavOdp.komplet'  => DataProviderInterface::DEDUCTION_STATUS_COMPLETE,
            'stavOdp.vytvZdd'  => DataProviderInterface::DEDUCTION_STATUS_TAX_DOCUMENT_CREATED,
            'stavOdp.castecny' => DataProviderInterface::DEDUCTION_STATUS_PARTIAL,
            default            => DataProviderInterface::DEDUCTION_STATUS_NONE,
        };
    }

    /**
     * @param array<string, mixed>  $classMap
     */
    private function createInstance(string $entity): mixed
    {
        $classMap = [
            'faktura-vydana'      => '\\AbraFlexi\\FakturaVydana',
            'faktura-prijata'     => '\\AbraFlexi\\FakturaPrijata',
            'adresar'             => '\\AbraFlexi\\Adresar',
            'banka'               => '\\AbraFlexi\\Banka',
            'cenik'               => '\\AbraFlexi\\Cenik',
            'objednavka-prijata'  => '\\AbraFlexi\\ObjednavkaPrijata',
        ];

        if (isset($classMap[$entity]) && class_exists($classMap[$entity])) {
            return new $classMap[$entity]();
        }

        if (class_exists('\\AbraFlexi\\RO')) {
            return new \AbraFlexi\RO(null, ['evidence' => $entity]);
        }

        return null;
    }
}
