<?php

$dir = new RecursiveDirectoryIterator(__DIR__.'/../resources/views');
$iterator = new RecursiveIteratorIterator($dir);
$files = new RegexIterator($iterator, '/^.+\.blade\.php$/i', RecursiveRegexIterator::GET_MATCH);

$replacements = [
    'dark:bg-[#1A1D21]' => 'dark:bg-[#0a0a0a]',
    'dark:bg-[#131619]' => 'dark:bg-black',
    'dark:bg-gray-900' => 'dark:bg-[#0a0a0a]',
    'dark:bg-gray-800' => 'dark:bg-[#161615]',
    'dark:border-gray-800' => 'dark:border-white/10',
    'dark:border-gray-700' => 'dark:border-white/10',
    'bg-gray-50 dark:bg-[#0a0a0a]' => 'bg-[#f8fafc] dark:bg-black',
];

foreach ($files as $file) {
    $path = $file[0];
    $content = file_get_contents($path);
    $changed = false;
    
    foreach ($replacements as $search => $replace) {
        if (strpos($content, $search) !== false) {
            $content = str_replace($search, $replace, $content);
            $changed = true;
        }
    }
    
    if ($changed) {
        file_put_contents($path, $content);
        echo "Updated: $path\n";
    }
}
echo "Theme update completed.\n";
