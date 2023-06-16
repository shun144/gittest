<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('templates', function (Blueprint $table) {
            $table->unsignedBigInteger('id', true);
            $table->unsignedBigInteger('message_id');
            $table->softDeletes();
            $table->timestamp('created_at')->useCurrent()->nullable();
            $table->timestamp('updated_at')->useCurrent()->nullable();
            $table->foreign('message_id')->references('id')->on('messages');
        });
    }
    public function down(): void
    {
        Schema::dropIfExists('templates');
    }
};
