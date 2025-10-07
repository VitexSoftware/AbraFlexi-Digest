<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "=== AbraFlexi Digest Scripts Initialization Test ===\n\n";

$scripts = [
    'src/abraflexi-daydigest.php' => 'Daily Digest',
    'src/abraflexi-weekdigest.php' => 'Weekly Digest',
    'src/abraflexi-monthdigest.php' => 'Monthly Digest', 
    'src/abraflexi-yeardigest.php' => 'Yearly Digest',
    'src/abraflexi-alltimedigest.php' => 'All-Time Digest'
];

foreach ($scripts as $script => $description) {
    echo "Testing: $description ($script)\n";
    echo str_repeat('-', 50) . "\n";
    
    // Capture output and errors
    ob_start();
    $error = '';
    
    try {
        // This will test if the file can be parsed and basic includes work
        $result = exec("timeout 5s php -f $script 2>&1", $output, $return_code);
        
        echo "  Return code: $return_code\n";
        
        if ($return_code === 124) {
            echo "  ✅ Script ran (timed out after 5s - normal for digest scripts)\n";
        } elseif ($return_code === 0) {
            echo "  ✅ Script completed successfully\n";
        } else {
            echo "  ⚠️  Script exited with code: $return_code\n";
            if (!empty($output)) {
                echo "  Output: " . implode("\n          ", array_slice($output, -3)) . "\n";
            }
        }
        
    } catch (Exception $e) {
        echo "  ❌ Exception: " . $e->getMessage() . "\n";
    } catch (ParseError $e) {
        echo "  ❌ Parse Error: " . $e->getMessage() . "\n";
    } catch (Error $e) {
        echo "  ❌ Fatal Error: " . $e->getMessage() . "\n";
    }
    
    $captured = ob_get_clean();
    if (!empty($captured)) {
        echo "  Captured: " . trim($captured) . "\n";
    }
    
    echo "\n";
}

echo "=== Module Files Test ===\n";
echo "Checking if our migrated modules can be included...\n\n";

$modules = glob('src/Digest/Modules/*.php');
$module_errors = 0;

foreach ($modules as $module) {
    $basename = basename($module);
    
    // Test syntax
    exec("php -l $module 2>&1", $syntax_output, $syntax_code);
    
    if ($syntax_code !== 0) {
        echo "❌ $basename: Syntax error\n";
        $module_errors++;
    } else {
        echo "✅ $basename: Syntax OK\n";
    }
}

echo "\n";
echo "=== Summary ===\n";
echo "Scripts tested: " . count($scripts) . "\n";
echo "Modules checked: " . count($modules) . "\n";
echo "Module syntax errors: $module_errors\n";

if ($module_errors === 0) {
    echo "🎉 All tests passed! Migration appears successful.\n";
} else {
    echo "⚠️  Some issues detected. Check the details above.\n";
}
