<?php

namespace App\Filament\Resources\Inquiries\Pages;

use App\Actions\ReleaseArtTree;
use App\Actions\ReserveArtTree;
use App\Enums\ArtAvailability;
use App\Enums\InquiryStatus;
use App\Exceptions\ArtNotAvailable;
use App\Filament\Resources\Inquiries\InquiryResource;
use App\Models\Inquiry;
use App\Models\Product;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

/**
 * @property Inquiry $record
 */
class EditInquiry extends EditRecord
{
    protected static string $resource = InquiryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('reserveTree')
                ->label('Giữ cây này')
                ->icon('heroicon-o-lock-closed')
                ->color('warning')
                ->requiresConfirmation()
                ->modalDescription(fn () => 'Đặt cây "'.$this->artTree()?->name.'" sang trạng thái Đang giữ cho yêu cầu này?')
                ->visible(fn () => $this->artTree()?->availability === ArtAvailability::Available)
                ->action(function (ReserveArtTree $reserve) {
                    try {
                        $reserve->handle($this->artTree(), $this->record);
                        $this->refreshFormData(['status']);
                        Notification::make()->success()->title('Đã giữ cây cho yêu cầu #'.$this->record->id)->send();
                    } catch (ArtNotAvailable $exception) {
                        Notification::make()->danger()->title($exception->getMessage())->send();
                    }
                }),
            Action::make('releaseTree')
                ->label('Trả cây về còn hàng')
                ->icon('heroicon-o-lock-open')
                ->color('gray')
                ->requiresConfirmation()
                ->visible(fn () => $this->holdsTree())
                ->action(function (ReleaseArtTree $release) {
                    $release->handle($this->artTree());
                    Notification::make()->success()->title('Cây đã trở về trạng thái Còn hàng')->send();
                }),
            Action::make('viewTree')
                ->label('Xem cây')
                ->icon('heroicon-o-eye')
                ->color('gray')
                ->url(fn () => $this->artTree()?->publicPath(), shouldOpenInNewTab: true)
                ->visible(fn () => $this->artTree() !== null),
        ];
    }

    protected function afterSave(): void
    {
        if ($this->record->status === InquiryStatus::Lost && $this->holdsTree()) {
            Notification::make()
                ->warning()
                ->title('Yêu cầu đang giữ cây '.$this->artTree()->name)
                ->body('Yêu cầu đã chuyển sang "Không thành công". Bấm "Trả cây về còn hàng" nếu muốn mở bán lại cây.')
                ->persistent()
                ->send();
        }
    }

    private function artTree(): ?Product
    {
        $subject = $this->record->subject()->first();

        return $subject instanceof Product && $subject->isArt() ? $subject : null;
    }

    private function holdsTree(): bool
    {
        $tree = $this->artTree();

        return $tree !== null
            && $tree->availability === ArtAvailability::Reserved
            && $tree->reserved_inquiry_id === $this->record->id;
    }
}
