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
     * @param string $subject
     * @param \Ease\Container   $moduleDir
     */
    public function __construct($sendTo, $subject)
    {

        $this->fromEmailAddress = \Ease\Functions::cfg('DIGEST_FROM');
        parent::__construct($sendTo, $subject);
        $this->htmlDocument = new \Ease\Html\HtmlTag(new \Ease\Html\SimpleHeadTag([
                    new \Ease\Html\TitleTag($this->emailSubject),
                    '<style>' . Digestor::$purecss .
                    Digestor::getCustomCss() .
                    Digestor::getWebPageInlineCSS() .
                    '</style>']));
        $this->htmlBody = $this->htmlDocument->addItem(new \Ease\Html\BodyTag());
    }

    /**
     * Přidá položku do těla mailu.
     *
     * @param mixed $item EaseObjekt nebo cokoliv s metodou draw();
     *
     * @return Ease\pointer|null ukazatel na vložený obsah
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
