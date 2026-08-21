<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::dropIfExists('signatories');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::create('signatories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('position');
            $table->string('role');
            $table->string('email');
            $table->boolean('is_active')->default(true);
            $table->string('signature_path');
            $table->string('stamp_path')->nullable();
            $table->integer('default_width')->default(150);
            $table->integer('default_height')->default(75);
            $table->boolean('include_name')->default(true);
            $table->boolean('include_position')->default(false);
            $table->boolean('include_date')->default(false);
            $table->json('allowed_roles')->nullable();
            $table->timestamps();
        });
    }
};
