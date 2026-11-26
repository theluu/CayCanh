<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('kind', 20)->default('standard')->index();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->nullable()->unique();
            $table->unsignedInteger('price')->nullable();
            $table->unsignedInteger('sale_price')->nullable();
            $table->boolean('show_price')->default(true);
            $table->string('availability', 20)->default('available')->index();
            $table->boolean('in_stock')->default(true);
            $table->text('summary')->nullable();
            $table->longText('description')->nullable();
            $table->json('specs')->nullable();
            $table->string('video_url')->nullable();
            $table->boolean('is_featured')->default(false)->index();
            $table->boolean('is_published')->default(true)->index();
            $table->timestamp('published_at')->nullable();
            // Plain column: inquiries table is created later; integrity is enforced in the app layer.
            $table->unsignedBigInteger('reserved_inquiry_id')->nullable()->index();
            $table->string('seo_title')->nullable();
            $table->string('seo_description', 300)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
