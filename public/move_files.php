<?php
$pagesDir = '/var/www/html/cms/resources/views/pages';

// Create directories
@mkdir("$pagesDir/db-tracker", 0777, true);
@mkdir("$pagesDir/settings", 0777, true);
@mkdir("$pagesDir/account-settings", 0777, true);

// Move files
$files = glob("$pagesDir/db-tracker*.blade.php");
foreach ($files as $file) {
    $basename = basename($file);
    // Remove "db-tracker-" prefix, except for db-tracker.blade.php
    if ($basename === 'db-tracker.blade.php') {
        $newName = 'index.blade.php';
    } else {
        $newName = str_replace('db-tracker-', '', $basename);
    }
    rename($file, "$pagesDir/db-tracker/$newName");
}

@rename("$pagesDir/settings.blade.php", "$pagesDir/settings/index.blade.php");
@rename("$pagesDir/account-settings.blade.php", "$pagesDir/account-settings/index.blade.php");

echo "Files moved successfully.";
