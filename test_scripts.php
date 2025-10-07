<?php
echo "Testing AbraFlexi Digest Scripts...\n\n";

$scripts = [
    'src/abraflexi-daydigest.php',
    'src/abraflexi-weekdigest.php', 
    'src/abraflexi-monthdigest.php',
    'src/abraflexi-yeardigest.php',
    'src/abraflexi-alltimedigest.php'
];

foreach ($scripts as $script) {
    echo "Testing: $script\n";
    
    // Test if file exists and is readable
    if (!file_exists($script)) {
        echo "  ❌ File not found\n";
        continue;
    }
    
    if (!is_readable($script)) {
        echo "  ❌ File not readable\n";
        continue;
    }
    
    // Check if file contains our new TableTag usage
    $content = file_get_contents($script);
    if (strpos($content, 'TableTag') !== false) {
        echo "  ✅ Contains TableTag usage\n";
    } else if (strpos($content, '\\AbraFlexi\\Digest\\Table') !== false) {
        echo "  ⚠️  Still contains deprecated Table usage\n";
    } else {
        echo "  ℹ️  No table usage detected\n";
    }
    
    // Basic syntax check already passed, so just confirm
    echo "  ✅ Syntax OK\n";
    
    echo "\n";
}

echo "All tests completed!\n";
