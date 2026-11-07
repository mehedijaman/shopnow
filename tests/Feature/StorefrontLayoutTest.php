<?php

function storefrontViewFiles(): array
{
    $files = [];
    foreach ([base_path('modules'), base_path('resources-site/views')] as $root) {
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($root));
        foreach ($iterator as $file) {
            if ($file->isFile() && $file->getExtension() === 'blade.php') {
                $files[] = $file->getPathname();
            }
        }
    }

    return $files;
}

test('every storefront page loads a site JavaScript entry so shared chrome mounts', function () {
    $missing = [];
    foreach (storefrontViewFiles() as $path) {
        $contents = file_get_contents($path);
        if (! str_contains($contents, "@extends('site-layout')")) {
            continue;
        }

        $loadsEntry = str_contains($contents, 'resources-site/js/index-app.js')
            || str_contains($contents, 'resources-site/js/blog-app.js');

        if (! $loadsEntry) {
            $missing[] = $path;
        }
    }

    // Pages without an entry never mount <mobile-bottom-nav>, <shop-search>, etc.
    expect($missing)->toBe([]);
});
