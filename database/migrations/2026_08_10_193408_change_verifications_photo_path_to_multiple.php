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
        Schema::table('verifications', function (Blueprint $table) {
            $table->json('photo_paths')->nullable()->after('case_id');
        });

        foreach (DB::table('verifications')->whereNotNull('photo_path')->get() as $verification) {
            DB::table('verifications')->where('id', $verification->id)->update([
                'photo_paths' => json_encode([$verification->photo_path]),
            ]);
        }

        Schema::table('verifications', function (Blueprint $table) {
            $table->dropColumn('photo_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('verifications', function (Blueprint $table) {
            $table->string('photo_path')->nullable()->after('case_id');
        });

        foreach (DB::table('verifications')->whereNotNull('photo_paths')->get() as $verification) {
            $paths = json_decode($verification->photo_paths, true);

            DB::table('verifications')->where('id', $verification->id)->update([
                'photo_path' => is_array($paths) ? ($paths[0] ?? null) : null,
            ]);
        }

        Schema::table('verifications', function (Blueprint $table) {
            $table->dropColumn('photo_paths');
        });
    }
};
