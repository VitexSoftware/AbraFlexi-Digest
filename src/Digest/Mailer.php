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

use Ease\Sand;
use Symfony\Component\Mailer\Mailer as SymfonyMailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;

/**
 * AbraFlexi Digest Mailer — sends HTML digest via Symfony Mailer.
 *
 * @author     Vítězslav Dvořák <info@vitexsofware.cz>
 * @copyright  (G) 2017-2026 Vitex Software
 *
 * @no-named-arguments
 */
class Mailer extends Sand
{
    public string $emailAddress = '';
    public string $emailSubject = '';
    public string $fromEmailAddress = '';
    public bool $notify = true;
    public ?bool $sendResult = false;
    public $htmlDocument;
    public $htmlBody;

    /**
     * @var array<string, string>
     */
    public array $mailHeaders = [];
    public bool $finalized = false;
    private Email $email;
    private SymfonyMailer $mailer;

    /**
     * @param string $sendTo  recipient address
     * @param string $subject email subject
     */
    public function __construct(string $sendTo, string $subject)
    {
        $this->fromEmailAddress = \Ease\Shared::cfg('DIGEST_FROM', \Ease\Shared::cfg('MAIL_FROM', 'digest@'.gethostname()));

        $this->setMailHeaders([
            'To' => $sendTo,
            'From' => $this->fromEmailAddress,
            'Reply-To' => $this->fromEmailAddress,
            'Subject' => $subject,
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Transfer-Encoding' => '8bit',
        ]);

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

        $dsn = \Ease\Shared::cfg('MAIL_DSN', '');

        if (empty($dsn)) {
            $dsn = 'sendmail://default';
        }

        $transport = Transport::fromDsn($dsn);
        $this->mailer = new SymfonyMailer($transport);
        $this->email = new Email();
    }

    /**
     * Sets mail headers.
     */
    public function setMailHeaders(array $mailHeaders): bool
    {
        $this->mailHeaders = array_merge($this->mailHeaders, $mailHeaders);

        if (isset($this->mailHeaders['To'])) {
            $this->emailAddress = $this->mailHeaders['To'];
        }

        if (isset($this->mailHeaders['From'])) {
            $this->fromEmailAddress = $this->mailHeaders['From'];
        }

        if (isset($this->mailHeaders['Subject'])) {
            $this->emailSubject = $this->mailHeaders['Subject'];
        }

        $this->finalized = false;

        return true;
    }

    /**
     * Adds an item to the HTML body of the mail.
     *
     * @param mixed      $item         EaseObject or anything with draw()
     * @param null|mixed $pageItemName
     *
     * @return mixed pointer to the inserted content
     */
    public function &addItem($item, $pageItemName = null)
    {
        $mailBody = '';

        if (\is_object($item)) {
            if (null === $this->htmlBody) {
                $this->htmlBody = new \Ease\Html\BodyTag();
            }

            $mailBody = $this->htmlBody->addItem($item, $pageItemName);
        }

        return $mailBody;
    }

    /**
     * Builds the Symfony Email object from the HTML document.
     */
    public function finalize(): void
    {
        $html = method_exists($this->htmlDocument, 'getRendered')
            ? $this->htmlDocument->getRendered()
            : (string) $this->htmlDocument;

        $this->email->html($html);

        if (!empty($this->fromEmailAddress)) {
            $this->email->from(Address::create($this->fromEmailAddress));
        }

        if (!empty($this->emailAddress)) {
            foreach (explode(',', $this->emailAddress) as $address) {
                if (trim($address) !== '') {
                    $this->email->addTo(Address::create(trim($address)));
                }
            }
        }

        if (!empty($this->emailSubject)) {
            $this->email->subject($this->emailSubject);
        }

        if (isset($this->mailHeaders['Cc'])) {
            foreach (explode(',', $this->mailHeaders['Cc']) as $address) {
                if (trim($address) !== '') {
                    $this->email->addCc(Address::create(trim($address)));
                }
            }
        }

        if (isset($this->mailHeaders['Bcc'])) {
            foreach (explode(',', $this->mailHeaders['Bcc']) as $address) {
                if (trim($address) !== '') {
                    $this->email->addBcc(Address::create(trim($address)));
                }
            }
        }

        if (isset($this->mailHeaders['Reply-To'])) {
            $this->email->replyTo(Address::create($this->mailHeaders['Reply-To']));
        }

        $headers = $this->email->getHeaders();

        foreach ($this->mailHeaders as $headerName => $headerValue) {
            if (!\in_array(strtolower($headerName), ['to', 'from', 'subject', 'cc', 'bcc', 'reply-to', 'content-type', 'content-transfer-encoding', 'date'], true)) {
                $headers->addTextHeader($headerName, $headerValue);
            }
        }

        $this->finalized = true;
    }

    /**
     * Sends the digest mail.
     */
    public function send(): bool
    {
        if (!$this->finalized) {
            $this->finalize();
        }

        try {
            $this->mailer->send($this->email);
            $this->sendResult = true;
        } catch (\Exception $e) {
            $this->sendResult = false;

            if ($this->notify) {
                $mailStripped = str_replace(['<', '>'], '', $this->emailAddress);
                $this->addStatusMessage(sprintf(
                    _('Message %s, for %s was not sent because of %s'),
                    $this->emailSubject,
                    $mailStripped,
                    $e->getMessage(),
                ), 'warning');
            }

            return false;
        }

        if ($this->notify) {
            $mailStripped = str_replace(['<', '>'], '', $this->emailAddress);
            $this->addStatusMessage(sprintf(_('Message %s was sent to %s'), $this->emailSubject, $mailStripped), 'success');
        }

        return true;
    }

    /**
     * Do not draw mail when included in a page.
     */
    public function draw(): void
    {
        $this->drawStatus = true;
    }
}
