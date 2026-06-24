<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        // Bersihkan data lama sebelum menambahkan unique index.
        $seen = [];

        DB::table('search_histories')
            ->orderByDesc('updated_at')
            ->orderByDesc('id')
            ->get()
            ->each(function ($row) use (&$seen): void {
                $keyword = Str::squish((string) $row->keyword);
                $type = Str::lower(Str::squish((string) $row->type));
                $key = $row->user_id.'|'.mb_strtolower($keyword).'|'.$type;

                if (isset($seen[$key])) {
                    DB::table('search_histories')->where('id', $row->id)->delete();
                    return;
                }

                $seen[$key] = true;

                DB::table('search_histories')
                    ->where('id', $row->id)
                    ->update([
                        'keyword' => $keyword,
                        'type' => $type,
                    ]);
            });

        Schema::table('search_histories', function (Blueprint $table): void {
            $table->unique(
                ['user_id', 'keyword', 'type'],
                'search_histories_user_keyword_type_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::table('search_histories', function (Blueprint $table): void {
            $table->dropUnique('search_histories_user_keyword_type_unique');
        });
    }
};
