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
        Schema::table('cases', function (Blueprint $table) {
            $table->foreignId('template_id')->after('reference_number')->nullable()->constrained('verification_templates');
            $table->json('fields')->nullable()->after('template_id');
        });

        $transferId = DB::table('verification_templates')->where('slug', 'transfer')->value('id');

        if ($transferId === null && DB::table('cases')->exists()) {
            $transferId = DB::table('verification_templates')->insertGetId([
                'name' => 'Konfirmasi Transfer',
                'slug' => 'transfer',
                'title' => 'Verifikasi Transaksi',
                'button_text' => 'Konfirmasi Transfer',
                'theme' => 'indigo',
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('cases')->whereNull('template_id')->update(['template_id' => $transferId]);

        foreach (DB::table('cases')->get() as $case) {
            $fields = [];

            foreach (['target_name', 'bank_name', 'account_number', 'amount'] as $column) {
                if ($case->{$column} !== null) {
                    $fields[$column] = (string) $case->{$column};
                }
            }

            DB::table('cases')->where('id', $case->id)->update([
                'fields' => json_encode($fields),
            ]);
        }

        Schema::table('cases', function (Blueprint $table) {
            $table->dropColumn(['target_name', 'bank_name', 'account_number', 'amount']);
        });

        Schema::table('cases', function (Blueprint $table) {
            $table->unsignedBigInteger('template_id')->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cases', function (Blueprint $table) {
            $table->string('target_name')->nullable()->after('reference_number');
            $table->string('bank_name')->nullable()->after('target_name');
            $table->string('account_number')->nullable()->after('bank_name');
            $table->decimal('amount', 15, 2)->nullable()->after('account_number');
        });

        foreach (DB::table('cases')->get() as $case) {
            $fields = json_decode($case->fields ?? '{}', true) ?: [];

            DB::table('cases')->where('id', $case->id)->update([
                'target_name' => $fields['target_name'] ?? null,
                'bank_name' => $fields['bank_name'] ?? null,
                'account_number' => $fields['account_number'] ?? null,
                'amount' => $fields['amount'] ?? null,
            ]);
        }

        Schema::table('cases', function (Blueprint $table) {
            $table->dropConstrainedForeignId('template_id');
            $table->dropColumn('fields');
        });
    }
};
