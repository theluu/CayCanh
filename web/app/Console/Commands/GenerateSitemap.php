<?php

namespace App\Console\Commands;

use App\Models\Post;
use App\Models\Product;
use App\Models\Project;
use App\Models\Service;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Model;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\Tags\Url;

#[Signature('sitemap:generate {--path= : Đường dẫn file sitemap} {--robots= : Đường dẫn file robots.txt}')]
#[Description('Tạo sitemap.xml và robots.txt từ nội dung đã xuất bản')]
class GenerateSitemap extends Command
{
    /** @var array<string, float> */
    private const STATIC_PAGES = [
        '/' => 1.0,
        '/dich-vu' => 0.9,
        '/cay-canh-nghe-thuat' => 0.9,
        '/san-pham' => 0.9,
        '/du-an' => 0.8,
        '/bang-gia' => 0.8,
        '/tin-tuc' => 0.7,
        '/gioi-thieu' => 0.6,
        '/bao-gia' => 0.6,
        '/lien-he' => 0.6,
        '/cau-hoi-thuong-gap' => 0.5,
        '/danh-gia-khach-hang' => 0.5,
    ];

    public function handle(): int
    {
        $sitemap = Sitemap::create();

        foreach (self::STATIC_PAGES as $path => $priority) {
            $sitemap->add(Url::create(url($path))->setPriority($priority));
        }

        $collections = [
            [Service::published()->get(), 0.8],
            [Product::published()->get(), 0.7],
            [Project::published()->get(), 0.6],
            [Post::published()->get(), 0.5],
        ];

        foreach ($collections as [$models, $priority]) {
            $models->each(fn (Model $model) => $sitemap->add(
                Url::create(url($model->publicPath()))->setLastModificationDate($model->updated_at)->setPriority($priority)
            ));
        }

        $sitemap->writeToFile($this->option('path') ?: public_path('sitemap.xml'));
        file_put_contents($this->option('robots') ?: public_path('robots.txt'), $this->robots());

        $this->info('Đã tạo sitemap.');

        return self::SUCCESS;
    }

    private function robots(): string
    {
        return implode("\n", [
            'User-agent: *',
            'Disallow: /admin',
            'Disallow: /livewire',
            'Disallow: /gio-hang',
            'Disallow: /dat-hang',
            '',
            'Sitemap: '.url('/sitemap.xml'),
            '',
        ]);
    }
}
