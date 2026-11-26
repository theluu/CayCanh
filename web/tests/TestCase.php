<?php

namespace Tests;

use App\Jobs\RegenerateSitemap;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Queue;

abstract class TestCase extends BaseTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutVite();

        // Never let model events rewrite public/sitemap.xml while the suite runs.
        Queue::fake([RegenerateSitemap::class]);
    }
    //
}
