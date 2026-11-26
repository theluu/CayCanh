<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServicePackage;
use App\Support\JsonLd;
use App\Support\Seo;
use Inertia\Inertia;
use Inertia\Response;

class PriceController extends Controller
{
    public function __invoke(): Response
    {
        $services = Service::published()->ordered()->whereHas('packages')->with('packages')->get();

        return Inertia::render('Price', [
            'seo' => Seo::make('Bảng giá dịch vụ cho thuê và chăm sóc cây xanh', 'Bảng giá tham khảo các gói cho thuê, chăm sóc cây xanh văn phòng và công trình.', jsonLd: [
                JsonLd::breadcrumbs(['Bảng giá' => null]),
            ]),
            'services' => $services->map(fn (Service $service) => [
                'id' => $service->id,
                'name' => $service->name,
                'url' => $service->publicPath(),
                'packages' => $service->packages->map(fn (ServicePackage $package) => [
                    'id' => $package->id,
                    'name' => $package->name,
                    'price' => $package->displayPrice(),
                    'unit' => $package->unit,
                    'features' => $package->features ?? [],
                    'highlighted' => $package->is_highlighted,
                ])->all(),
            ])->all(),
        ]);
    }
}
