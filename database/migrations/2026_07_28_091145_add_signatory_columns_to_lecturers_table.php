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
        Schema::table('lecturers', function (Blueprint $table) {
            $table->string('position')->nullable()->after('name');
            $table->string('stamp_path')->nullable()->after('signature_path');
            $table->integer('default_width')->default(150)->after('stamp_path');
            $table->integer('default_height')->default(75)->after('default_width');
            $table->boolean('include_name')->default(true)->after('default_height');
            $table->boolean('include_position')->default(false)->after('include_name');
            $table->boolean('include_date')->default(false)->after('include_position');
            $table->json('allowed_roles')->nullable()->after('include_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lecturers', function (Blueprint $table) {
            $table->dropColumn([
                'position', 
                'stamp_path', 
                'default_width', 
                'default_height', 
                'include_name', 
                'include_position', 
                'include_date', 
                'allowed_roles'
            ]);
        });
    }
};
