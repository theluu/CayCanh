<?php

namespace App\Actions;

use App\Enums\ArtAvailability;
use App\Enums\InquiryStatus;
use App\Exceptions\ArtNotAvailable;
use App\Models\Inquiry;
use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ReserveArtTree
{
    /**
     * @throws ArtNotAvailable when the product is not an art tree or is not available
     */
    public function handle(Product $product, Inquiry $inquiry): void
    {
        DB::transaction(function () use ($product, $inquiry) {
            $locked = Product::whereKey($product->id)->lockForUpdate()->firstOrFail();

            if (! $locked->isArt() || $locked->availability !== ArtAvailability::Available) {
                throw new ArtNotAvailable("Cây \"{$locked->name}\" hiện không thể giữ (".$locked->availability->label().').');
            }

            $locked->update(['availability' => ArtAvailability::Reserved, 'reserved_inquiry_id' => $inquiry->id]);

            if ($inquiry->status === InquiryStatus::New) {
                $inquiry->update(['status' => InquiryStatus::Processing]);
            }
        });

        $product->refresh();
    }
}
