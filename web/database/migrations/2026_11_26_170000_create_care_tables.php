<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('care_contracts', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('phone', 20)->nullable();
            $table->string('address');
            $table->foreignId('service_package_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('inquiry_id')->nullable()->constrained()->nullOnDelete();
            $table->date('starts_on');
            $table->date('ends_on')->nullable();
            $table->string('frequency', 20)->default('weekly');
            $table->foreignId('default_technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->text('note')->nullable();
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('care_visits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('care_contract_id')->constrained()->cascadeOnDelete();
            $table->date('scheduled_on')->index();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status', 20)->default('scheduled')->index();
            $table->text('note')->nullable();
            $table->timestamps();
            $table->unique(['care_contract_id', 'scheduled_on']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('care_visits');
        Schema::dropIfExists('care_contracts');
    }
};
