<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('staff_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('table_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('order_id')->nullable()->constrained()->nullOnDelete();
            $table->text('message');
            $table->string('audio_path')->nullable();
            $table->string('status')->default('new');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('staff_notifications');
    }
};
