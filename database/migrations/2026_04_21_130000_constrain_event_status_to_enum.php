<?php

use App\Enums\EventStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::table('events')
            ->select(['id', 'status'])
            ->orderBy('id')
            ->get()
            ->each(function (object $event): void {
                DB::table('events')
                    ->where('id', $event->id)
                    ->update([
                        'status' => EventStatus::normalize((string) $event->status)->value,
                    ]);
            });

        Schema::table('events', function (Blueprint $table): void {
            $table->enum('status', EventStatus::values())
                ->default(EventStatus::OPEN->value)
                ->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('events', function (Blueprint $table): void {
            $table->string('status')->default(EventStatus::OPEN->value)->change();
        });
    }
};
