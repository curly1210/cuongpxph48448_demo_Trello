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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->integer('position')->default(0);
            $table->string('author')->nullable();
            $table->foreignId('lane_id')->constrained()->onDelete('cascade');
            $table->timestamp('created_at')->useCurrent();
            $table->string('priority');
            $table->integer('comments_count')->default(0);
            $table->string('description')->nullable();
            $table->string('link_issue')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
