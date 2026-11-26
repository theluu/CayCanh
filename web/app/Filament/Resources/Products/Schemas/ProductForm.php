<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Enums\ArtAvailability;
use App\Enums\ProductKind;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema, ProductKind $kind = ProductKind::Standard): Schema
    {
        $isArt = $kind === ProductKind::Art;

        return $schema
            ->columns(3)
            ->components([
                Group::make()->columnSpan(2)->schema([
                    Section::make('Thông tin chính')->columns(2)->schema([
                        TextInput::make('name')->label($isArt ? 'Tên cây' : 'Tên sản phẩm')->required()->maxLength(255),
                        TextInput::make('slug')->label('Đường dẫn (slug)')
                            ->helperText('Để trống để tự tạo từ tên.')
                            ->maxLength(255),
                        Select::make('category_id')->label('Danh mục')
                            ->relationship('category', 'name', fn ($query) => $query->where('scope', $kind))
                            ->searchable()
                            ->preload(),
                        TextInput::make('sku')->label('Mã SKU')->unique(ignoreRecord: true),
                        Textarea::make('summary')->label('Mô tả ngắn')->rows(3)->columnSpanFull(),
                        RichEditor::make('description')->label('Mô tả chi tiết')->columnSpanFull(),
                    ]),
                    Section::make('Hình ảnh & video')->schema([
                        SpatieMediaLibraryFileUpload::make('gallery')->label('Album ảnh')
                            ->collection('gallery')
                            ->multiple()
                            ->reorderable()
                            ->image()
                            ->maxSize(8192),
                        TextInput::make('video_url')->label('Link video (YouTube/TikTok)')->url(),
                    ]),
                    Section::make($isArt ? 'Thông số cây' : 'Thông số')->schema([
                        KeyValue::make('specs')->label('')
                            ->keyLabel('Thuộc tính')
                            ->valueLabel('Giá trị')
                            ->default($isArt
                                ? ['Dáng thế' => '', 'Chiều cao' => '', 'Hoành tán' => '', 'Đường kính gốc' => '', 'Tuổi cây' => '', 'Xuất xứ' => '']
                                : ['Chiều cao' => '', 'Kích thước chậu' => '']),
                    ]),
                    Section::make('SEO')->collapsed()->schema([
                        TextInput::make('seo_title')->label('Tiêu đề SEO')->maxLength(255),
                        Textarea::make('seo_description')->label('Mô tả SEO')->maxLength(300)->rows(2),
                    ]),
                ]),
                Group::make()->schema([
                    Section::make('Giá')->schema([
                        TextInput::make('price')->label('Giá (đ)')->numeric()->minValue(0)
                            ->required(! $isArt),
                        TextInput::make('sale_price')->label('Giá khuyến mãi (đ)')->numeric()->minValue(0)
                            ->hidden($isArt),
                        Toggle::make('show_price')->label('Hiển thị giá')
                            ->default(! $isArt)
                            ->helperText($isArt ? 'Cây nghệ thuật luôn chỉ nhận báo giá, kể cả khi hiện giá.' : null),
                    ]),
                    Section::make('Trạng thái')->schema([
                        Select::make('availability')->label('Tình trạng')
                            ->options(ArtAvailability::options())
                            ->default(ArtAvailability::Available->value)
                            ->required()
                            ->visible($isArt),
                        Toggle::make('in_stock')->label('Còn hàng')->default(true)->hidden($isArt),
                        Toggle::make('is_published')->label('Xuất bản')->default(true),
                        Toggle::make('is_featured')->label('Nổi bật trên trang chủ'),
                        DateTimePicker::make('published_at')->label('Ngày xuất bản'),
                    ]),
                ]),
            ]);
    }
}
