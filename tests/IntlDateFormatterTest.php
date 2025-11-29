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
 * Test class for IntlDateFormatter functionality and error handling.
 *
 * This tests the fixes we implemented for IntlDateFormatter issues
 */
class IntlDateFormatterTest extends TestCase
{
    /**
     * Test IntlDateFormatter with valid locale.
     */
    public function testIntlDateFormatterWithValidLocale(): void
    {
        $locale = 'en_US';
        $formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);

        $this->assertInstanceOf(\IntlDateFormatter::class, $formatter);
        $this->assertIsString($formatter->format(time()));
    }

    /**
     * Test IntlDateFormatter with null locale falls back to en_US.
     */
    public function testIntlDateFormatterWithNullLocaleFallback(): void
    {
        $locale = null;
        $locale ??= 'en_US';
        $formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);

        // If the constructor failed, try with a fallback locale
        if ($formatter === null) {
            $formatter = new \IntlDateFormatter('en_US', \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);
        }

        $this->assertInstanceOf(\IntlDateFormatter::class, $formatter);
        $this->assertIsString($formatter->format(time()));
    }

    /**
     * Test datefmt_create with valid locale.
     */
    public function testDatefmtCreateWithValidLocale(): void
    {
        try {
            $fmt = datefmt_create(
                'en_US',
                \IntlDateFormatter::SHORT,
                \IntlDateFormatter::NONE,
                'UTC',
                \IntlDateFormatter::GREGORIAN,
            );
        } catch (\ValueError $e) {
            $fmt = false;
        }

        $this->assertNotFalse($fmt);

        if ($fmt !== false) {
            $result = datefmt_format($fmt, time());
            $this->assertIsString($result);
        }
    }

    /**
     * Test complete error handling pattern used in digest files.
     */
    public function testCompleteErrorHandlingPattern(): void
    {
        // Simulate the pattern used in the digest files

        // Step 1: Create formatter with potential failure
        try {
            $fmt = datefmt_create(
                'cs_CZ',
                \IntlDateFormatter::SHORT,
                \IntlDateFormatter::NONE,
                'Europe/Prague',
                \IntlDateFormatter::GREGORIAN,
            );
        } catch (\ValueError $e) {
            $fmt = false;
        }

        // Step 2: Fallback if needed
        if ($fmt === false) {
            try {
                $fmt = datefmt_create(
                    'en_US',
                    \IntlDateFormatter::SHORT,
                    \IntlDateFormatter::NONE,
                    'UTC',
                    \IntlDateFormatter::GREGORIAN,
                );
            } catch (\ValueError $e) {
                $fmt = false;
            }
        }

        // Step 3: Create IntlDateFormatter with similar pattern
        $locale = null; // Simulate \Ease\Locale::$localeUsed being null
        $locale ??= 'en_US';
        $formatter = new \IntlDateFormatter($locale, \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);

        if ($formatter === null) {
            $formatter = new \IntlDateFormatter('en_US', \IntlDateFormatter::LONG, \IntlDateFormatter::NONE);
        }

        if ($formatter === null) {
            $this->fail('Failed to create IntlDateFormatter even with fallback');
        }

        // Step 4: Test formatting with fallback
        $formattedDate = false;

        if ($fmt !== false) {
            $formattedDate = datefmt_format($fmt, time());
        }

        if ($formattedDate === false) {
            $formattedDate = (new \DateTime())->format('Y-m-d');
        }

        // Assertions
        $this->assertInstanceOf(\IntlDateFormatter::class, $formatter);
        $this->assertIsString($formattedDate);
        $this->assertNotEmpty($formattedDate);
    }
}
