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

namespace AbraFlexi\Digest;

/**
 * Description of WebPage.
 *
 * @author vitex
 *
 * @no-named-arguments
 */
class WebPage extends \Ease\TWB5\WebPage
{
    /**
     * Put page contents here.
     */
    public \Ease\TWB5\Container $container;

    /**
     * @param string $pageTitle
     */
    public function __construct($pageTitle = '')
    {
        parent::__construct($pageTitle);
        \Ease\TWB5\Part::jQueryze();
        $this->container = $this->addItem(new \Ease\TWB5\Container());
        $this->container->setTagClass('container-fluid');
        $this->addCSS(<<<'EOD'


EOD);
    }
}
