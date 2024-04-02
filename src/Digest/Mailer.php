<?php

namespace AbraFlexi\Digest;

/**
 * AbraFlexi Digest Mailer
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2017-2023 Vitex Software
 */
class Mailer extends \Ease\HtmlMailer
{
    /**
     * Digest Mailer
     *
     * @param string $sendTo
     * @param string $subject
     */
    public function __construct($sendTo, $subject)
    {

        $this->fromEmailAddress = \Ease\Functions::cfg('DIGEST_FROM');
        parent::__construct($sendTo, $subject);
        $this->htmlDocument = new \Ease\Html\HtmlTag(
            ['<!--[if gte mso 9]>
<xml>
<o:OfficeDocumentSettings>
<o:AllowPNG/>
<o:PixelsPerInch>96</o:PixelsPerInch>
</o:OfficeDocumentSettings>
</xml>
<![endif]-->',
            new \Ease\Html\SimpleHeadTag([
                new \Ease\Html\TitleTag($this->emailSubject),
                '<style>' .
                Digestor::$msocss .
                Digestor::$purecss .
                Digestor::getCustomCss() .
                Digestor::getWebPageInlineCSS() .
                '</style>'])],
            [
                'xmlns' => 'http://www.w3.org/1999/xhtml',
                'xmlns:o' => 'urn:schemas-microsoft-com:office:office'
                ]
        );
        $this->htmlBody = $this->htmlDocument->addItem(new \Ease\Html\BodyTag(null, [
                    'width' => '100%',
                    'style' => 'margin: 0; padding: 0 !important; mso-line-height-rule: exactly;'
        ]));
    }

    /**
     * Přidá položku do těla mailu.
     *
     * @param mixed $item EaseObjekt nebo cokoliv s metodou draw();
     *
     * @return Ease\Embedable|string|null ukazatel na vložený obsah
     */
    public function &addItem($item, $pageItemName = null)
    {
        $mailBody = '';
        if (is_object($item)) {
            if (is_object($this->htmlDocument)) {
                if (is_null($this->htmlBody)) {
                    $this->htmlBody = new \Ease\Html\BodyTag();
                }
                $mailBody = $this->htmlBody->addItem($item, $pageItemName);
            } else {
                $mailBody = $this->htmlDocument;
            }
        } else {
            $this->textBody .= is_array($item) ? implode("\n", $item) : $item;
            $this->mimer->setTXTBody($this->textBody);
        }

        return $mailBody;
    }

    public function getCss()
    {
    }
}
