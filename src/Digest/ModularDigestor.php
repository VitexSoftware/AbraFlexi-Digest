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

use AbraFlexi\Digest\Providers\AbraFlexiDataProvider;
use VitexSoftware\DigestModules\Core\ModuleRunner;
use VitexSoftware\DigestModules\Modules;
use VitexSoftware\DigestRenderer\DigestRenderer;

/**
 * Digestor using modular architecture.
 *
 * Collects data via AbraFlexiDataProvider (this package),
 * renders via DigestRenderer (Markdown → HTML/PDF).
 *
 * Supports OUTPUT_FORMAT env: md (default), html, pdf.
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class ModularDigestor
{
    /** @var array<string, string> Universal modules (all periods) */
    private const UNIVERSAL_MODULES = [
        'debtors' => Modules\Debtors::class,
        'outcoming_invoices' => Modules\OutcomingInvoices::class,
        'incoming_invoices' => Modules\IncomingInvoices::class,
        'incoming_payments' => Modules\IncomingPayments::class,
        'outcoming_payments' => Modules\OutcomingPayments::class,
        'new_customers' => Modules\NewCustomers::class,
        'without_email' => Modules\WithoutEmail::class,
        'without_tel' => Modules\WithoutTel::class,
        'waiting_income' => Modules\WaitingIncome::class,
        'waiting_payments' => Modules\WaitingPayments::class,
        'reminds' => Modules\Reminds::class,
        'best_sellers' => Modules\BestSellers::class,
        'unmatched_payments' => Modules\UnmatchedPayments::class,
        'unmatched_invoices' => Modules\UnmatchedInvoices::class,
        'outcoming_invoices_hidden' => Modules\OutcomingInvoicesHiddenToCustomer::class,
    ];

    /** @var array<string, array<string, string>> Period-specific modules */
    private const PERIOD_MODULES = [
        'daily' => [],
        'weekly' => [
            'weekly_income_chart' => Modules\Weekly\WeeklyIncomeChart::class,
        ],
        'monthly' => [
            'daily_income_chart' => Modules\Monthly\DailyIncomeChart::class,
        ],
        'yearly' => [],
        'alltime' => [
            'purchase_price_lower_than_sales' => Modules\AllTime\PurchasePriceLowerThanSales::class,
        ],
    ];

    private string $subject;
    private ModuleRunner $moduleRunner;
    private DigestRenderer $renderer;

    /**
     * @param string               $subject        Digest title
     * @param array<string, mixed> $abraFlexiConfig AbraFlexi connection config
     */
    public function __construct(string $subject, array $abraFlexiConfig = [])
    {
        $this->subject = $subject;
        $dataProvider = new AbraFlexiDataProvider($abraFlexiConfig);
        $this->moduleRunner = new ModuleRunner($dataProvider);
        $this->renderer = new DigestRenderer();

        self::logBanner();
    }

    /**
     * Register universal modules plus period-specific ones.
     *
     * @param string $periodType daily|weekly|monthly|yearly|alltime
     */
    public function registerModules(string $periodType = 'daily'): self
    {
        foreach (self::UNIVERSAL_MODULES as $key => $class) {
            $this->moduleRunner->addModule($key, $class);
        }

        foreach (self::PERIOD_MODULES[$periodType] ?? [] as $key => $class) {
            $this->moduleRunner->addModule($key, $class);
        }

        return $this;
    }

    /**
     * Register a single additional module.
     *
     * @param string $moduleKey   Module identifier
     * @param string $moduleClass Fully qualified class name
     */
    public function addModule(string $moduleKey, string $moduleClass): self
    {
        $this->moduleRunner->addModule($moduleKey, $moduleClass);

        return $this;
    }

    /**
     * Generate digest output.
     *
     * @param \DatePeriod $period Time period
     * @param string|null $format Output format (null = read OUTPUT_FORMAT env, fallback 'html')
     *
     * @return string Rendered output (Markdown, HTML, or raw PDF bytes)
     */
    public function generate(\DatePeriod $period, ?string $format = null): string
    {
        $format ??= \Ease\Shared::cfg('OUTPUT_FORMAT', 'html');

        if ($format === 'html' || $format === 'pdf') {
            $theme = \Ease\Shared::cfg('THEME', 'bootstrap');
            $this->renderer->setTheme($theme);
            $this->renderer->setHeaderExtra($this->buildLogoHtml());
            $this->renderer->setFooterExtra($this->buildAppInfoHtml());
        }

        $digestData = $this->moduleRunner->run($period);

        return $this->renderer->render($digestData, $format);
    }

    private function buildLogoHtml(): string
    {
        $svgPath = \dirname(__DIR__, 2) . '/abraflexi-digest.svg';

        if (!file_exists($svgPath)) {
            return '';
        }

        $svg = file_get_contents($svgPath);
        // Force the SVG to fill its container instead of rendering at its natural size
        $svg = preg_replace('/<svg\b/', '<svg style="width:100%;height:100%;display:block;"', $svg, 1);

        return '<div class="app-logo" style="float:right;width:80px;height:80px;overflow:hidden;margin:0 0 12px 20px;">'
            . $svg
            . '</div>';
    }

    private function buildAppInfoHtml(): string
    {
        $name     = \Ease\Shared::cfg('APP_NAME', 'AbraFlexi Digest');
        $version  = \Ease\Shared::appVersion();
        $homepage = 'https://github.com/VitexSoftware/AbraFlexi-Digest';

        return sprintf(
            '<p class="app-info" style="margin:8px 0 0;font-size:0.85em;">'
            . '<strong>%s</strong> %s &mdash; <a href="%s">%s</a></p>',
            htmlspecialchars($name),
            htmlspecialchars($version),
            htmlspecialchars($homepage),
            htmlspecialchars($homepage),
        );
    }

    /**
     * Run the full digest pipeline: generate, save to file, send by email.
     *
     * @param \DatePeriod $period Time period
     */
    public function run(\DatePeriod $period): void
    {
        $format = \Ease\Shared::cfg('OUTPUT_FORMAT', 'html');
        $output = $this->generate($period, $format);

        // Save to file
        $saveTo = \Ease\Shared::cfg('DIGEST_SAVETO', \Ease\Shared::cfg('RESULT_FILE', ''));

        if ($saveTo) {
            $ext = match ($format) {
                'md' => '.md',
                'pdf' => '.pdf',
                default => '.html',
            };

            $filename = str_ends_with($saveTo, $ext) ? $saveTo : $saveTo . $ext;
            file_put_contents($filename, $output);
            \Ease\Shared::logger()->addToLog(sprintf(_('Saved to %s'), $filename), 'success');
        }

        // Send email (always as HTML for email clients)
        $emailTo = \Ease\Shared::cfg('DIGEST_MAILTO', \Ease\Shared::cfg('EASE_MAILTO', ''));

        if ($emailTo) {
            $emailHtml = ($format === 'html')
                ? $output
                : $this->generate($period, 'html');

            $this->sendEmail($emailTo, $emailHtml);
        }
    }

    /**
     * Send digest by email.
     *
     * @param string $emailTo  Recipient address(es)
     * @param string $htmlBody Full HTML document
     */
    private function sendEmail(string $emailTo, string $htmlBody): void
    {
        try {
            $mailer = new Mailer($emailTo, $this->subject);
            $mailer->setHtmlContent($htmlBody);
            $mailer->send();
        } catch (\Throwable $e) {
            \Ease\Shared::logger()->addToLog(
                sprintf(_('Email sending failed: %s'), $e->getMessage()),
                'warning',
            );
        }
    }

    /**
     * Log startup banner.
     */
    private static function logBanner(): void
    {
        $prober = new \AbraFlexi\Company();
        $prober->logBanner(
            ' AbraFlexi Modular Digest '
            . \Ease\Shared::appVersion() . ' '
            . ($_SERVER['SCRIPT_FILENAME'] ?? ''),
        );
    }
}
