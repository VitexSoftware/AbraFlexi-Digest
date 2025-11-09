<?php

declare(strict_types=1);

/**
 * This file is part of the AbraFlexi-Digest package (refactored)
 *
 * https://github.com/VitexSoftware/AbraFlexi-Digest/
 *
 * (c) Vítězslav Dvořák <http://vitexsoftware.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AbraFlexi\Digest;

use VitexSoftware\DigestModules\Core\ModuleRunner;
use VitexSoftware\DigestModules\Providers\AbraFlexiDataProvider;
use VitexSoftware\DigestRenderer\DigestRenderer;

/**
 * Refactored Digestor using new modular structure
 *
 * @author Vítězslav Dvořák <info@vitexsoftware.cz>
 */
class ModularDigestor
{
    /**
     * Subject/title of the digest
     */
    private string $subject;

    /**
     * Module runner for data collection
     */
    private ModuleRunner $moduleRunner;

    /**
     * HTML renderer for output generation
     */
    private DigestRenderer $renderer;

    /**
     * Available modules mapping
     */
    private array $availableModules = [
        'outcoming_invoices' => \VitexSoftware\DigestModules\Modules\OutcomingInvoices::class,
        'debtors' => \VitexSoftware\DigestModules\Modules\Debtors::class,
        // Add more modules as they are converted
    ];

    /**
     * Constructor
     *
     * @param string $subject Digest title
     * @param array<string, mixed> $abraFlexiConfig AbraFlexi connection configuration
     */
    public function __construct(string $subject, array $abraFlexiConfig = [])
    {
        $this->subject = $subject;
        
        // Create data provider
        $dataProvider = new AbraFlexiDataProvider($abraFlexiConfig);
        
        // Create module runner
        $this->moduleRunner = new ModuleRunner($dataProvider);
        
        // Create renderer
        $this->renderer = new DigestRenderer();
        
        $this->logBanner();
    }

    /**
     * Add module to the digest
     *
     * @param string $moduleKey Module identifier
     * @param string|null $moduleClass Module class (optional, auto-detected)
     * @return self
     */
    public function addModule(string $moduleKey, ?string $moduleClass = null): self
    {
        $moduleClass = $moduleClass ?? $this->availableModules[$moduleKey] ?? null;
        
        if (!$moduleClass) {
            throw new \InvalidArgumentException("Unknown module: $moduleKey");
        }

        $this->moduleRunner->addModule($moduleKey, $moduleClass);
        
        return $this;
    }

    /**
     * Run digest generation for specified period
     *
     * @param \DatePeriod $period Time period to analyze
     * @param string $theme Rendering theme ('bootstrap', 'email')
     * @return string HTML output
     */
    public function generate(\DatePeriod $period, string $theme = 'bootstrap'): string
    {
        // Collect data from all modules
        $digestData = $this->moduleRunner->run($period);
        
        // Set theme and render
        $this->renderer->setTheme($theme);
        
        return $this->renderer->render($digestData);
    }

    /**
     * Generate digest and send by email
     *
     * @param \DatePeriod $period Time period
     * @param string $emailTo Recipient email
     * @param string $emailTheme Email theme ('email' recommended)
     * @return bool Success status
     */
    public function sendByEmail(\DatePeriod $period, string $emailTo, string $emailTheme = 'email'): bool
    {
        try {
            // Generate email-friendly HTML
            $emailHtml = $this->generate($period, $emailTheme);
            
            // Create mailer (keeping compatibility with existing Mailer class)
            $mailer = new Mailer($emailTo, $this->subject);
            $mailer->setHtmlContent($emailHtml);
            
            return $mailer->send() === true;
            
        } catch (\Throwable $e) {
            error_log("Email sending failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Generate digest and save to file
     *
     * @param \DatePeriod $period Time period
     * @param string $filename Output filename
     * @param string $theme Theme to use
     * @return bool Success status
     */
    public function saveToFile(\DatePeriod $period, string $filename, string $theme = 'bootstrap'): bool
    {
        try {
            $html = $this->generate($period, $theme);
            
            return file_put_contents($filename, $html) !== false;
            
        } catch (\Throwable $e) {
            error_log("File saving failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get raw JSON data without HTML rendering
     *
     * @param \DatePeriod $period Time period
     * @return array<string, mixed> Raw digest data
     */
    public function getJsonData(\DatePeriod $period): array
    {
        return $this->moduleRunner->run($period);
    }

    /**
     * Save JSON data to file
     *
     * @param \DatePeriod $period Time period
     * @param string $filename Output filename
     * @return bool Success status
     */
    public function saveJsonToFile(\DatePeriod $period, string $filename): bool
    {
        try {
            $data = $this->getJsonData($period);
            $json = json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
            
            return file_put_contents($filename, $json) !== false;
            
        } catch (\Throwable $e) {
            error_log("JSON saving failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Set custom CSS for rendering
     *
     * @param string $css Custom CSS
     * @return self
     */
    public function setCustomCss(string $css): self
    {
        $this->renderer->setCustomCss($css);
        
        return $this;
    }

    /**
     * Set custom template for rendering
     *
     * @param string $templatePath Template file path
     * @return self
     */
    public function setCustomTemplate(string $templatePath): self
    {
        $this->renderer->setTemplate($templatePath);
        
        return $this;
    }

    /**
     * Legacy method: Process modules using old interface for backward compatibility
     *
     * @param \DatePeriod $period Time period
     * @param array<string, string> $modules Module classes
     * @deprecated Use addModule() and generate() instead
     */
    public function processModules(\DatePeriod $period, array $modules): void
    {
        foreach ($modules as $moduleKey => $moduleClass) {
            $this->addModule($moduleKey, $moduleClass);
        }
        
        // Generate default output
        $html = $this->generate($period);
        
        // Handle legacy save/email behavior
        $saveTo = \Ease\Shared::cfg('RESULT_FILE');
        if ($saveTo) {
            $this->saveToFile($period, $saveTo);
        }
        
        $emailTo = \Ease\Shared::cfg('DIGEST_MAILTO', \Ease\Shared::cfg('EASE_MAILTO', ''));
        if ($emailTo) {
            $this->sendByEmail($period, $emailTo);
        }
    }

    /**
     * Log banner for backward compatibility
     */
    private function logBanner(): void
    {
        $prober = new \AbraFlexi\Company();
        $prober->logBanner(' AbraFlexi Modular Digest ' . \Ease\Shared::appVersion() . ' ' . ($_SERVER['SCRIPT_FILENAME'] ?? ''));
    }

    /**
     * Factory method to create digestor with common modules
     *
     * @param string $subject Digest title
     * @param array<string> $moduleKeys Module keys to include
     * @param array<string, mixed> $config AbraFlexi configuration
     * @return self
     */
    public static function createWithModules(string $subject, array $moduleKeys, array $config = []): self
    {
        $digestor = new self($subject, $config);
        
        foreach ($moduleKeys as $moduleKey) {
            $digestor->addModule($moduleKey);
        }
        
        return $digestor;
    }
}