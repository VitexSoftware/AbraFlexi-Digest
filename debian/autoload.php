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
