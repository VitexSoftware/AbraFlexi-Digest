<?php

/**
 * AbraFlexi Digest.
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2017 Vitex Software
 */

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

namespace AbraFlexi\Digest;

/**
 * @author vitex
 *
 * @no-named-arguments
 */
interface DigestModuleInterface
{
    /**
     * Default module Heading.
     */
    public function heading(): string;

    /**
     * Obtaining informations.
     *
     * @return bool dig success
     */
    public function dig(): bool;

    /**
     * Return Pure data (no markup).
     */
    public function digJson(): array;
}
