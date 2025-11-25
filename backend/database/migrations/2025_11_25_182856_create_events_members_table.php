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
        Schema::create('events_members', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('event_id');
            $table->uuid('member_id');
            $table->tinyInteger('can_edit_details')->default(0);
            $table->tinyInteger('can_manage_tickets')->default(0);
            $table->tinyInteger('can_view_analytics')->default(0);
            $table->tinyInteger('can_view_buyer_contacts')->default(0);
            $table->tinyInteger('can_cancel_tickets')->default(0);
            $table->tinyInteger('can_scan_tickets')->default(0);
            $table->timestamps();

            $table->foreign('event_id')->references('id')->on('events');
            $table->foreign('member_id')->references('id')->on('company_members');
            $table->unique(['event_id', 'member_id']);});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('events_members');
    }
};
