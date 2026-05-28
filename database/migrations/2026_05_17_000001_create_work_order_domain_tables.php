<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('unit_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('type')->default('Syringe Pump');
            $table->string('model')->nullable();
            $table->string('serial_number')->unique();
            $table->string('inventory_number')->unique();
            $table->string('barcode_code')->unique();
            $table->string('status')->default('active')->index();
            $table->date('purchased_at')->nullable();
            $table->timestamps();
        });

        Schema::create('symptoms', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('category')->index();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('damage_indications', function (Blueprint $table) {
            $table->id();
            $table->string('code')->unique();
            $table->string('name');
            $table->string('severity')->default('medium')->index();
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('repair_suggestions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('damage_indication_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('step_order')->default(1);
            $table->text('action_text');
            $table->timestamps();
        });

        Schema::create('rules', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedInteger('weight')->default(50);
            $table->boolean('is_active')->default(true)->index();
            $table->timestamps();
        });

        Schema::create('rule_symptoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('symptom_id')->constrained()->cascadeOnDelete();
            $table->unique(['rule_id', 'symptom_id']);
        });

        Schema::create('rule_indications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('rule_id')->constrained()->cascadeOnDelete();
            $table->foreignId('damage_indication_id')->constrained()->cascadeOnDelete();
            $table->unique(['rule_id', 'damage_indication_id'], 'rule_indication_unique');
        });

        Schema::create('work_orders', function (Blueprint $table) {
            $table->id();
            $table->string('wo_number')->unique();
            $table->foreignId('device_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reporter_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('technician_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('status')->default('pending')->index();
            $table->text('description')->nullable();
            $table->timestamp('processed_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();
        });

        Schema::create('wo_symptoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('symptom_id')->constrained()->cascadeOnDelete();
            $table->unique(['work_order_id', 'symptom_id']);
        });

        Schema::create('wo_indications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('damage_indication_id')->constrained()->cascadeOnDelete();
            $table->string('source')->default('system');
            $table->unsignedInteger('score')->default(0);
            $table->timestamps();
        });

        Schema::create('wo_updates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('work_order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('status')->nullable();
            $table->string('final_diagnosis')->nullable();
            $table->text('notes')->nullable();
            $table->string('photo_path')->nullable();
            $table->timestamps();
        });

        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('type');
            $table->json('data')->nullable();
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notifications');
        Schema::dropIfExists('wo_updates');
        Schema::dropIfExists('wo_indications');
        Schema::dropIfExists('wo_symptoms');
        Schema::dropIfExists('work_orders');
        Schema::dropIfExists('rule_indications');
        Schema::dropIfExists('rule_symptoms');
        Schema::dropIfExists('rules');
        Schema::dropIfExists('repair_suggestions');
        Schema::dropIfExists('damage_indications');
        Schema::dropIfExists('symptoms');
        Schema::dropIfExists('devices');
    }
};
