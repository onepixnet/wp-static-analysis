<?php
require __DIR__ . '/../vendor/autoload.php';

$GLOBALS['PHP_CODESNIFFER_STANDARD_DIRS'] = [];
$GLOBALS['PHP_CODESNIFFER_TEST_DIRS'] = [];
$GLOBALS['PHP_CODESNIFFER_SNIFF_CODES'] = [];
$GLOBALS['PHP_CODESNIFFER_FIXABLE_CODES'] = [];

$srcPath = __DIR__ . '/../OnepixStandard/';
$testPath = __DIR__ . '/../OnepixStandard/Tests/';

$allTestFiles = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($testPath));
$testFiles = new RegexIterator($allTestFiles, '/Test\.php$/');

foreach ($testFiles as $file) {
    $content = file_get_contents($file->getPathname());

    if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
        $namespace = $matches[1];
    } else {
        $namespace = '';
    }

    if (preg_match('/class\s+(\w+)/', $content, $matches)) {
        $className = $matches[1];

        $fullClassName = $namespace ? $namespace . '\\' . $className : $className;

        $GLOBALS['PHP_CODESNIFFER_STANDARD_DIRS'][$fullClassName] = $srcPath;
        $GLOBALS['PHP_CODESNIFFER_TEST_DIRS'][$fullClassName] = $testPath;
    }
}

require __DIR__ . '/../vendor/squizlabs/php_codesniffer/tests/bootstrap.php';