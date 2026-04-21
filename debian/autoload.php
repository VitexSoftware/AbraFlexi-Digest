<?php
// Debian autoloader for abraflexi-digest
// Load dependency autoloaders
require_once '/usr/share/php/Ease/autoload.php';
require_once '/usr/share/php/EaseTWB5Widgets/autoload.php';
require_once '/usr/share/php/EaseTWB5WidgetsAbraFlexi/autoload.php';
require_once '/usr/share/php/AbraFlexiBricks/autoload.php';
require_once '/usr/share/php/EaseHtmlWidgets/autoload.php';

// PSR-4 autoloader for application classes
spl_autoload_register(function ($class) {
    $prefixes = [
        'AbraFlexi\\Digest\\' => '/usr/lib/abraflexi-digest/Digest/',
        'AbraFlexi\\Digest\\Outlook\\' => '/usr/lib/abraflexi-digest/Digest/Outlook/',
        'AbraFlexi\\Digest\\Modules\\' => '/usr/lib/abraflexi-digest/Digest/Modules/',
    ];
    foreach ($prefixes as $prefix => $baseDir) {
        $len = strlen($prefix);
        if (strncmp($prefix, $class, $len) !== 0) {
            continue;
        }
        $relativeClass = substr($class, $len);
        $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
        if (file_exists($file)) {
            require $file;
            return;
        }
    }
});

require_once '/usr/share/php/Composer/InstalledVersions.php';

(function (): void {
    $versions = [];
    foreach (\Composer\InstalledVersions::getAllRawData() as $d) {
        $versions = array_merge($versions, $d['versions'] ?? []);
    }
    $name    = defined('APP_NAME') ? APP_NAME    : 'unknown';
    $version = defined('APP_VERSION') ? APP_VERSION : '0.0.0';
    $versions[$name] = ['pretty_version' => $version, 'version' => $version,
        'reference' => null, 'type' => 'library', 'install_path' => __DIR__,
        'aliases' => [], 'dev_requirement' => false];
    \Composer\InstalledVersions::reload([
        'root' => ['name' => $name, 'pretty_version' => $version, 'version' => $version,
            'reference' => null, 'type' => 'project', 'install_path' => __DIR__,
            'aliases' => [], 'dev' => false],
        'versions' => $versions,
    ]);
})();
