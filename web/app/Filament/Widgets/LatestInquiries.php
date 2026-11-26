<?php

namespace App\Filament\Widgets;

use App\Enums\InquiryStatus;
use App\Enums\InquiryType;
use App\Enums\Role;
use App\Models\Inquiry;
use App\Models\User;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;

class LatestInquiries extends TableWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Yêu cầu mới nhất';

    public static function canView(): bool
    {
        $user = auth()->user();

        return $user instanceof User && $user->hasAnyStaffRole(Role::Admin, Role::Sales);
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(Inquiry::query()->latest()->limit(8))
            ->paginated(false)
            ->columns([
                TextColumn::make('type')->label('Loại')->badge()->formatStateUsing(fn (InquiryType $state) => $state->label()),
                TextColumn::make('name')->label('Khách')->description(fn (Inquiry $record) => $record->phone),
                TextColumn::make('subject_label')->label('Quan tâm')->state(fn (Inquiry $record) => $record->subjectLabel())->placeholder('—'),
                TextColumn::make('status')->label('Trạng thái')->badge()
                    ->formatStateUsing(fn (InquiryStatus $state) => $state->label())
                    ->color(fn (InquiryStatus $state) => $state->color()),
                TextColumn::make('created_at')->label('Lúc')->since(),
            ])
            ->recordActions([
                Action::make('open')->label('Xử lý')->url(fn (Inquiry $record) => '/admin/inquiries/'.$record->id.'/edit'),
            ]);
    }
}
