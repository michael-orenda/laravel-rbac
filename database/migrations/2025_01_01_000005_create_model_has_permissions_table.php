<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('model_id');
            $table->string('model_type');
            $table->unsignedBigInteger('permission_id');
            $table->timestamps();

            $table->unique(['model_id', 'model_type', 'permission_id'], 'model_permission_unique');
        });
    }

    public function down(): void {
        Schema::dropIfExists('model_has_permissions');
    }
};
