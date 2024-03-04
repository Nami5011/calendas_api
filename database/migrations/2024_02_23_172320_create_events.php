<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
// use Illuminate\Support\Str; // Str::uuid()

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
			$table->integer('user_id');
			$table->string('title');
			$table->string('event_code')->unique();
			$table->string('description')->nullable();
			$table->string('confirmation_message')->nullable();
			$table->json('available_datetime')->nullable();
			$table->integer('start_day_length')->nullable()->comment('Buffer time: if 1, available from next day');
			$table->integer('count')->nullable();
			$table->boolean('is_active')->default(true);
			$table->boolean('send_email_flg')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
