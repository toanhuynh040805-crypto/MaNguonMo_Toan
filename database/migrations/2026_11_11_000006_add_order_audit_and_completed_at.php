<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->timestamp('completed_at')->nullable()->index();
        });

        // Older demo orders may already be completed; preserve them when upgrading.
        DB::table('orders')->where('status', 'completed')
            ->update(['completed_at' => DB::raw('updated_at')]);

        Schema::create('order_status_histories', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->cascadeOnDelete();
            $table->foreignId('actor_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('from_status', 20)->nullable();
            $table->string('to_status', 20);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_status_histories');
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropColumn('completed_at');
        });
    }
};
