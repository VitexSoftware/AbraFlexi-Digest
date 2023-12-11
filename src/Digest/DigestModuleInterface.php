<?php

/**
 * AbraFlexi Digest
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2017 Vitex Software
 */

declare(strict_types=1);

namespace AbraFlexi\Digest;

/**
 *
 * @author vitex
 */
interface DigestModuleInterface
{
    /**
     * Default module Heading
     *
     * @return string
     */
    public function heading(): string;

    /**
     * Obtaining informations
     *
     * @return boolean dig success
     */
    public function dig(): bool;

    /**
     * Return Pure data (no markup)
     *
     * @return array
     */
    public function digJson(): array;
}
