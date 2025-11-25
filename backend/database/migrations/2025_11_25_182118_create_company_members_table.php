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
        Schema::create('company_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('company_id');
            $table->uuid('organizer_id');
            $table->tinyInteger('can_view_analytics')->default(0);
            $table->tinyInteger('can_manage_members')->default(0);
            $table->tinyInteger('can_manage_settings')->default(0);
            $table->tinyInteger('can_create_events')->default(0);
            $table->tinyInteger('can_manage_all_events')->default(0);
            $table->tinyInteger('can_manage_wallet')->default(0);
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies');
            $table->foreign('organizer_id')->references('id')->on('organizers');

            $table->unique(['company_id', 'organizer_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('company_members');
    }
};
