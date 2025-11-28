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
        Schema::create('mail_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('mail_template_id')->nullable()->index();
            $table->string('template_code')->nullable();
            $table->string('recipient')->index();
            $table->string('locale')->default('en');
            $table->string('status')->index();
            $table->string('subject')->nullable();
            $table->timestamp('sent_at')->nullable()->index();
            $table->timestamp('failed_at')->nullable();
            $table->text('error_message')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamp('created_at');
            $table->timestamp('updated_at');

            $table->foreign('mail_template_id')->references('id')->on('mail_templates');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mail_logs');
    }
};
