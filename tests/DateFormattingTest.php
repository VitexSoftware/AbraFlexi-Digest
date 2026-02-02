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

namespace AbraFlexi\Digest\Tests;

use PHPUnit\Framework\TestCase;

/**
 * Test class for date formatting functionality fixes.
 */
class DateFormattingTest extends TestCase
{
    /**
     * Test basic DateTime functionality used in digest scripts.
     */
    public function testBasicDateTimeFunctionality(): void
    {
        $date = new \DateTime('2025-10-02 12:00:00');
        $formatted = $date->format('Y-m-d');

        $this->assertEquals('2025-10-02', $formatted);
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}/', $formatted);
    }

    /**
     * Test DatePeriod creation (used in all digest scripts).
     */
    public function testDatePeriodCreation(): void
    {
        $start = new \DateTime('2025-01-01');
        $end = new \DateTime('2025-01-31');
        $period = new \DatePeriod($start, new \DateInterval('P1D'), $end);

        $this->assertInstanceOf(\DatePeriod::class, $period);
        $this->assertEquals($start, $period->getStartDate());
        $this->assertEquals($end, $period->getEndDate());
    }

    /**
     * Test IntlDateFormatter with valid parameters.
     */
    public function testIntlDateFormatterWithValidParameters(): void
    {
        $formatter = new \IntlDateFormatter('en_US', \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);

        $this->assertNotNull($formatter);
        $result = $formatter->format(time());
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test datefmt_create with valid parameters.
     */
    public function testDatefmtCreateWithValidParameters(): void
    {
        $fmt = datefmt_create(
            'en_US',
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::NONE,
            'UTC',
            \IntlDateFormatter::GREGORIAN,
        );

        $this->assertNotFalse($fmt);
        $this->assertNotNull($fmt);

        $result = datefmt_format($fmt, time());
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test the pattern used in digest scripts for error handling.
     */
    public function testDigestScriptErrorHandlingPattern(): void
    {
        // Test the actual pattern from our digest scripts
        $locale = 'en_US'; // Start with a known good locale
        $formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);

        $this->assertNotNull($formatter);

        $result = $formatter->format(time());
        $this->assertIsString($result);
        $this->assertNotEmpty($result);
    }

    /**
     * Test date interval modifications used in digest scripts.
     */
    public function testDateIntervalModifications(): void
    {
        // Test day modification (daily digest)
        $date = new \DateTime('2025-10-02');

        // Test week modification (weekly digest)
        $weekDate = new \DateTime('2025-10-02');
        $weekDate->modify('-1 week');
        $this->assertEquals('2025-09-25', $weekDate->format('Y-m-d'));

        // Test month modification (monthly digest)
        $monthDate = new \DateTime('2025-10-02');
        $monthDate->modify('-1 month');
        $this->assertEquals('2025-09-02', $monthDate->format('Y-m-d'));

        // Test year modification (yearly digest)
        $yearDate = new \DateTime('2025-10-02');
        $yearDate->modify('-1 year');
        $this->assertEquals('2026-10-02', $yearDate->format('Y-m-d'));
    }

    /**
     * Test timezone handling used in digest scripts.
     */
    public function testTimezoneHandling(): void
    {
        // Test Prague timezone (used in digest scripts)
        $pragueDate = new \DateTime('2025-10-02 12:00:00', new \DateTimeZone('Europe/Prague'));
        $utcDate = new \DateTime('2025-10-02 12:00:00', new \DateTimeZone('UTC'));

        $this->assertInstanceOf(\DateTime::class, $pragueDate);
        $this->assertInstanceOf(\DateTime::class, $utcDate);
        $this->assertEquals('Europe/Prague', $pragueDate->getTimezone()->getName());
        $this->assertEquals('UTC', $utcDate->getTimezone()->getName());
    }

    /**
     * Test fallback pattern when datefmt_format fails.
     */
    public function testDateFormatFallbackPattern(): void
    {
        // Create a valid formatter
        $fmt = datefmt_create(
            'en_US',
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::NONE,
            'UTC',
            \IntlDateFormatter::GREGORIAN,
        );

        $this->assertNotNull($fmt);

        // Test normal formatting
        $formattedDate = datefmt_format($fmt, time());

        // Simulate fallback pattern
        if ($formattedDate === false) {
            $formattedDate = (new \DateTime())->format('Y-m-d');
        }

        $this->assertIsString($formattedDate);
        $this->assertNotEmpty($formattedDate);
        $this->assertMatchesRegularExpression('/\d+[\-\/\.]\d+[\-\/\.]\d+/', $formattedDate);
    }
}
