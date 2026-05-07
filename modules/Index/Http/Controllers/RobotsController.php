<?php

namespace Modules\Index\Http\Controllers;

use Illuminate\Http\Response;
use Modules\Support\Http\Controllers\SiteController;

class RobotsController extends SiteController
{
    public function __invoke(): Response
    {
        $sitemapUrl = route('sitemap.index');

        $content = implode("\n", [
            'User-agent: *',
            'Allow: /',
            '',
            '# Block admin and private areas',
            'Disallow: /admin',
            'Disallow: /admin/',
            'Disallow: /admin-auth/',
            'Disallow: /upazila/',
            'Disallow: /union/',
            '',
            '# Block recycle bin and internal API',
            'Disallow: /*/recycle-bin',
            '',
            '# Sitemap',
            "Sitemap: {$sitemapUrl}",
        ]);

        return response($content, 200, ['Content-Type' => 'text/plain']);
    }
}
