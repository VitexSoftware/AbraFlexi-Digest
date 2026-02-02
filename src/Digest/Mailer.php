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
 * AbraFlexi Digest Mailer.
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2017-2026 Vitex Software
 *
 * @no-named-arguments
 */
class Mailer extends \Ease\HtmlMailer
{
    /**
     * Digest Mailer.
     *
     * @param string $sendTo
     * @param string $subject
     */
    public function __construct($sendTo, $subject)
    {
        $this->fromEmailAddress = \Ease\Shared::cfg('DIGEST_FROM', 'digest@'.gethostname());
        parent::__construct($sendTo, $subject);
        $this->htmlDocument = new \Ease\Html\HtmlTag(
            [<<<'EOD'
<!--[if gte mso 9]>
<xml>
<o:OfficeDocumentSettings>
<o:AllowPNG/>
<o:PixelsPerInch>96</o:PixelsPerInch>
</o:OfficeDocumentSettings>
</xml>
<![endif]-->
EOD,
                new \Ease\Html\SimpleHeadTag([
                    new \Ease\Html\TitleTag($this->emailSubject),
                    '<style>'.
                    Digestor::$msocss.
                    Digestor::$purecss.
                    Digestor::getCustomCss().
                    Digestor::getWebPageInlineCSS().
                    '</style>'])],
            [
                'xmlns' => 'http://www.w3.org/1999/xhtml',
                'xmlns:o' => 'urn:schemas-microsoft-com:office:office',
            ],
        );
        $this->htmlBody = $this->htmlDocument->addItem(new \Ease\Html\BodyTag(null, [
            'width' => '100%',
            'style' => 'margin: 0; padding: 0 !important; mso-line-height-rule: exactly;',
        ]));
    }

    /**
     * Přidá položku do těla mailu.
     *
     * @param mixed      $item         EaseObjekt nebo cokoliv s metodou draw();
     * @param null|mixed $pageItemName
     *
     * @return null|Ease\Embedable|string ukazatel na vložený obsah
     */
    public function &addItem($item, $pageItemName = null)
    {
        $mailBody = '';

        if (\is_object($item)) {
            if (\is_object($this->htmlDocument)) {
                if (null === $this->htmlBody) {
                    $this->htmlBody = new \Ease\Html\BodyTag();
                }

                $mailBody = $this->htmlBody->addItem($item, $pageItemName);
            } else {
                $mailBody = $this->htmlDocument;
            }
        } else {
            $this->textBody .= \is_array($item) ? implode("\n", $item) : $item;
            $this->mimer->setTXTBody($this->textBody);
        }

        return $mailBody;
    }

    public function getCss(): void
    {
    }
}
