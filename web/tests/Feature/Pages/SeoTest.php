<?php

use App\Settings\GeneralSettings;
use Inertia\Testing\AssertableInertia;

it('appends the site name to page titles', function () {
    $siteName = app(GeneralSettings::class)->site_name;

    $this->get('/lien-he')->assertInertia(fn (AssertableInertia $page) => $page
        ->where('seo.title', 'Liên hệ | '.$siteName));
});

it('does not repeat the site name when the title already contains it', function () {
    $siteName = app(GeneralSettings::class)->site_name;

    $this->get('/')->assertInertia(fn (AssertableInertia $page) => $page
        ->where('seo.title', fn (string $title) => substr_count($title, $siteName) === 1));
});

it('marks every page noindex when the environment must stay out of search engines', function () {
    config(['app.noindex' => true]);

    $this->get('/lien-he')->assertSee('<meta name="robots" content="noindex, nofollow">', false);
});

it('lets search engines index pages by default', function () {
    $this->get('/lien-he')->assertDontSee('name="robots"', false);
});
