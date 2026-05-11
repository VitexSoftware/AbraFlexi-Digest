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

namespace Test\AbraFlexi\Digest\Providers;

use AbraFlexi\Digest\Providers\AbraFlexiDataProvider;
use PHPUnit\Framework\TestCase;
use VitexSoftware\DigestModules\Core\DataProviderInterface;

/**
 * Tests for AbraFlexiDataProvider normalization logic.
 *
 * We test private helpers via reflection because they contain the critical
 * Relation-object handling code that has historically been a source of bugs.
 */
class AbraFlexiDataProviderTest extends TestCase
{
    private AbraFlexiDataProvider $provider;

    protected function setUp(): void
    {
        $this->provider = new AbraFlexiDataProvider();
    }

    // ── Helper to call private methods ──────────────────────────────────────

    private function callPrivate(string $method, mixed ...$args): mixed
    {
        $ref = new \ReflectionMethod(AbraFlexiDataProvider::class, $method);
        $ref->setAccessible(true);

        return $ref->invoke($this->provider, ...$args);
    }

    // ── extractCompany ───────────────────────────────────────────────────────

    public function testExtractCompanyFromRelationShowAs(): void
    {
        $relation         = new \stdClass();
        $relation->showAs = '22165223: Buršík a spol., s.r.o.';
        $relation->value  = 'code:22165223';

        $result = $this->callPrivate('extractCompany', $relation);

        $this->assertSame('Buršík a spol., s.r.o.', $result);
    }

    public function testExtractCompanyFromRelationValueFallback(): void
    {
        $relation        = new \stdClass();
        $relation->value = 'code:ABC123';

        $result = $this->callPrivate('extractCompany', $relation);

        $this->assertSame('ABC123', $result);
    }

    public function testExtractCompanyFromString(): void
    {
        $result = $this->callPrivate('extractCompany', 'code:TESTCOMPANY');

        $this->assertSame('TESTCOMPANY', $result);
    }

    public function testExtractCompanyFromArray(): void
    {
        $result = $this->callPrivate('extractCompany', ['nazev' => 'Array Company']);

        $this->assertSame('Array Company', $result);
    }

    public function testExtractCompanyFromNull(): void
    {
        $result = $this->callPrivate('extractCompany', null);

        $this->assertSame('', $result);
    }

    // ── extractCurrency ──────────────────────────────────────────────────────

    public function testExtractCurrencyFromRelationObject(): void
    {
        $relation        = new \stdClass();
        $relation->value = 'code:EUR';

        $result = $this->callPrivate('extractCurrency', $relation);

        $this->assertSame('EUR', $result);
    }

    public function testExtractCurrencyFromStringWithPrefix(): void
    {
        $result = $this->callPrivate('extractCurrency', 'code:CZK');

        $this->assertSame('CZK', $result);
    }

    public function testExtractCurrencyFromArray(): void
    {
        $result = $this->callPrivate('extractCurrency', ['kod' => 'USD']);

        $this->assertSame('USD', $result);
    }

    public function testExtractCurrencyDefaultsToCzk(): void
    {
        $result = $this->callPrivate('extractCurrency', null);

        $this->assertSame('CZK', $result);
    }

    public function testExtractCurrencyEmptyStringDefaultsToCzk(): void
    {
        $result = $this->callPrivate('extractCurrency', '');

        $this->assertSame('CZK', $result);
    }

    // ── normalizeDocumentType ────────────────────────────────────────────────

    public function testNormalizeDocumentTypeFromRelationShowAs(): void
    {
        $relation         = new \stdClass();
        $relation->showAs = 'FAKTURA: Faktura - daňový doklad';
        $relation->value  = 'code:FAKTURA';

        $result = $this->callPrivate('normalizeDocumentType', $relation);

        $this->assertSame('Faktura - daňový doklad', $result);
    }

    public function testNormalizeDocumentTypeProforma(): void
    {
        $result = $this->callPrivate('normalizeDocumentType', 'typDokladu.zalohFaktura');

        $this->assertSame(DataProviderInterface::DOCUMENT_TYPE_PROFORMA, $result);
    }

    public function testNormalizeDocumentTypeCreditNote(): void
    {
        $result = $this->callPrivate('normalizeDocumentType', 'typDokladu.dobropis');

        $this->assertSame(DataProviderInterface::DOCUMENT_TYPE_CREDIT_NOTE, $result);
    }

    // ── normalizePaymentStatus ───────────────────────────────────────────────

    public function testNormalizePaymentStatusPaid(): void
    {
        $result = $this->callPrivate('normalizePaymentStatus', 'stavUhr.uhrazeno');

        $this->assertSame(DataProviderInterface::PAYMENT_STATUS_PAID, $result);
    }

    public function testNormalizePaymentStatusPartial(): void
    {
        $result = $this->callPrivate('normalizePaymentStatus', 'stavUhr.castUhr');

        $this->assertSame(DataProviderInterface::PAYMENT_STATUS_PARTIAL, $result);
    }

    public function testNormalizePaymentStatusUnpaidDefault(): void
    {
        $result = $this->callPrivate('normalizePaymentStatus', null);

        $this->assertSame(DataProviderInterface::PAYMENT_STATUS_UNPAID, $result);
    }

    // ── normalizeMailStatus ──────────────────────────────────────────────────

    public function testNormalizeMailStatusSent(): void
    {
        $result = $this->callPrivate('normalizeMailStatus', 'stavMail.odeslano');

        $this->assertSame(DataProviderInterface::MAIL_STATUS_SENT, $result);
    }

    public function testNormalizeMailStatusPending(): void
    {
        $result = $this->callPrivate('normalizeMailStatus', 'stavMail.odeslat');

        $this->assertSame(DataProviderInterface::MAIL_STATUS_PENDING, $result);
    }

    public function testNormalizeMailStatusEmptyDefault(): void
    {
        $result = $this->callPrivate('normalizeMailStatus', null);

        $this->assertSame(DataProviderInterface::MAIL_STATUS_EMPTY, $result);
    }

    // ── toBool ───────────────────────────────────────────────────────────────

    public function testToBoolTrue(): void
    {
        $this->assertTrue($this->callPrivate('toBool', true));
        $this->assertTrue($this->callPrivate('toBool', 'true'));
        $this->assertTrue($this->callPrivate('toBool', 1));
    }

    public function testToBoolFalse(): void
    {
        $this->assertFalse($this->callPrivate('toBool', false));
        $this->assertFalse($this->callPrivate('toBool', 'false'));
        $this->assertFalse($this->callPrivate('toBool', 0));
        $this->assertFalse($this->callPrivate('toBool', null));
    }

    // ── toDateString ─────────────────────────────────────────────────────────

    public function testToDateStringFromDateTime(): void
    {
        $result = $this->callPrivate('toDateString', new \DateTime('2024-03-15'));

        $this->assertSame('2024-03-15', $result);
    }

    public function testToDateStringFromString(): void
    {
        $result = $this->callPrivate('toDateString', '2024-03-15');

        $this->assertSame('2024-03-15', $result);
    }

    public function testToDateStringFromNull(): void
    {
        $result = $this->callPrivate('toDateString', null);

        $this->assertSame('', $result);
    }

    // ── Public interface ─────────────────────────────────────────────────────

    public function testGetSystemName(): void
    {
        $this->assertSame('abraflexi', $this->provider->getSystemName());
    }

    public function testGetSupportedEntities(): void
    {
        $entities = $this->provider->getSupportedEntities();

        $this->assertContains(DataProviderInterface::ENTITY_OUTCOMING_INVOICES, $entities);
        $this->assertContains(DataProviderInterface::ENTITY_INCOMING_INVOICES, $entities);
        $this->assertContains(DataProviderInterface::ENTITY_BANK_STATEMENTS, $entities);
        $this->assertContains(DataProviderInterface::ENTITY_CONTACTS, $entities);
        $this->assertContains(DataProviderInterface::ENTITY_PRODUCTS, $entities);
    }

    public function testSupportsFeature(): void
    {
        $this->assertTrue($this->provider->supportsFeature('date_filtering'));
        $this->assertTrue($this->provider->supportsFeature('payment_status'));
        $this->assertFalse($this->provider->supportsFeature('nonexistent_feature'));
    }

    public function testFormatDate(): void
    {
        $result = $this->provider->formatDate(new \DateTime('2024-06-15'));

        $this->assertSame('2024-06-15', $result);
    }

    public function testFormatDatePeriodIssueDate(): void
    {
        $period = new \DatePeriod(
            new \DateTime('2024-01-01'),
            new \DateInterval('P1M'),
            new \DateTime('2024-02-01'),
        );

        $result = $this->provider->formatDatePeriod(DataProviderInterface::DATE_COLUMN_ISSUE_DATE, $period);

        $this->assertStringContainsString('datVyst', $result);
        $this->assertStringContainsString('2024-01-01', $result);
        $this->assertStringContainsString('2024-02-01', $result);
    }

    public function testGetDataThrowsForUnsupportedEntity(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->provider->getData('unsupported_entity_xyz');
    }
}
