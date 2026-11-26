<?php

namespace App\Http\Controllers;

use App\Actions\SubmitInquiry;
use App\Http\Requests\InquiryRequest;
use App\Settings\GeneralSettings;
use App\Support\JsonLd;
use App\Support\Seo;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class InquiryController extends Controller
{
    public function create(GeneralSettings $settings): Response
    {
        return Inertia::render('Quote', [
            'seo' => Seo::make('Yêu cầu khảo sát & báo giá', 'Gửi yêu cầu khảo sát công trình, tư vấn và báo giá cây xanh miễn phí.', jsonLd: [
                JsonLd::localBusiness($settings),
                JsonLd::breadcrumbs(['Yêu cầu báo giá' => null]),
            ]),
        ]);
    }

    public function store(InquiryRequest $request, SubmitInquiry $submitInquiry): RedirectResponse
    {
        $submitInquiry->handle($request->validated(), $request->ip());

        return back()->with('success', 'Cảm ơn bạn! Chúng tôi đã nhận yêu cầu và sẽ liên hệ trong thời gian sớm nhất.');
    }
}
