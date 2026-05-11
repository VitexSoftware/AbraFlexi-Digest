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
 * Digest Mailer — sends HTML digest via Symfony Mailer.
 *
 * Follows the same pattern as AbraFlexi\Mailer\HtmlMailer from abraflexi-mailer.
 * Configuration via MAIL_DSN, DIGEST_FROM / MAIL_FROM env variables.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 * @copyright (G) 2017-2026 Vitex Software
 */
class Mailer extends Sand
{
    /**
     * Recipient email address(es).
     */
    public string $emailAddress = '';

    /**
     * Email subject.
     */
    public string $emailSubject = '';

    /**
     * Sender email address.
     */
    public string $fromEmailAddress = '';

    /**
     * Show user notification about sending?
     */
    public bool $notify = true;

    /**
     * Result of the last send attempt.
     */
    public ?bool $sendResult = false;

    /**
     * @var array<string, string> Mail headers
     */
    public array $mailHeaders = [];

    /**
     * Whether finalize() has been called.
     */
    public bool $finalized = false;

    /**
     * Pre-rendered HTML content (set via setHtmlContent).
     */
    private string $htmlContent = '';

    private Email $email;
    private SymfonyMailer $mailer;

    /**
     * @param string $sendTo  Recipient address (comma-separated for multiple)
     * @param string $subject Email subject
     */
    public function __construct(string $sendTo, string $subject)
    {
        $this->fromEmailAddress = \Ease\Shared::cfg(
            'DIGEST_FROM',
            \Ease\Shared::cfg('MAIL_FROM', 'digest@' . gethostname()),
        );

        $this->setMailHeaders([
            'To' => $sendTo,
            'From' => $this->fromEmailAddress,
            'Reply-To' => $this->fromEmailAddress,
            'Subject' => $subject,
            'Content-Type' => 'text/html; charset=utf-8',
            'Content-Transfer-Encoding' => '8bit',
        ]);

        $dsn = \Ease\Shared::cfg('MAIL_DSN', '');

        if (empty($dsn)) {
            $dsn = 'sendmail://default';
        }

        $transport = Transport::fromDsn($dsn);
        $this->mailer = new SymfonyMailer($transport);
        $this->email = new Email();
    }

    /**
     * Set pre-rendered HTML content as the email body.
     *
     * When this is set, finalize() uses this content directly
     * instead of rendering an Ease HTML document.
     *
     * @param string $html Full HTML document
     */
    public function setHtmlContent(string $html): self
    {
        $this->htmlContent = $html;
        $this->finalized = false;

        return $this;
    }

    /**
     * Set mail headers.
     *
     * @param array<string, string> $mailHeaders Associative header array
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
     * Build the Symfony Email object.
     */
    public function finalize(): void
    {
        if ($this->htmlContent !== '') {
            $this->email->html($this->htmlContent);
        }

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
            if (!\in_array(
                strtolower($headerName),
                ['to', 'from', 'subject', 'cc', 'bcc', 'reply-to', 'content-type', 'content-transfer-encoding', 'date'],
                true,
            )) {
                $headers->addTextHeader($headerName, $headerValue);
            }
        }

        $this->finalized = true;
    }

    /**
     * Send the email.
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
            $this->addStatusMessage(
                sprintf(_('Message %s was sent to %s'), $this->emailSubject, $mailStripped),
                'success',
            );
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
