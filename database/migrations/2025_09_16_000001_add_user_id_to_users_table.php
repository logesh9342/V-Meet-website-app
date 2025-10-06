<?php

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
        // Add the column if it doesn't exist
        if (! Schema::hasColumn('users', 'user_id')) {
            Schema::table('users', function (Blueprint $table) {
                $table->string('user_id', 5)->nullable()->after('id');
            });

            // Backfill existing users with unique 5-digit codes
            DB::transaction(function () {
                // Collect existing codes to avoid duplicates
                $existing = DB::table('users')
                    ->whereNotNull('user_id')
                    ->pluck('user_id')
                    ->toArray();
                $existing = array_flip($existing);

                $users = DB::table('users')->select('id')->whereNull('user_id')->get();

                foreach ($users as $user) {
                    // Generate a unique 5-digit code
                    do {
                        $code = str_pad(random_int(0, 99999), 5, '0', STR_PAD_LEFT);
                    } while (isset($existing[$code]));

                    $existing[$code] = true;

                    DB::table('users')->where('id', $user->id)->update(['user_id' => $code]);
                }
            });

            // Now enforce uniqueness and non-null
            Schema::table('users', function (Blueprint $table) {
                $table->string('user_id', 5)->unique()->nullable(false)->change();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('users', 'user_id')) {
            Schema::table('users', function (Blueprint $table) {
                // Drop unique index implicitly when dropping the column
                $table->dropColumn('user_id');
            });
        }
    }
};
