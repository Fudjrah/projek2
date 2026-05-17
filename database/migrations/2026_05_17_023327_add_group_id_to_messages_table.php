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
        Schema::table('messages', function (Blueprint $table) {
         $table->foreignId('receiver_id')->nullable()->change();
        
        // Tambahkan group_id setelah receiver_id dan buat nullable
        $table->foreignId('group_id')->nullable()->after('receiver_id')->constrained()->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('messages', function (Blueprint $table) {
            // Hapus foreign key dan kolomnya jika di-rollback
        $table->dropForeign(['group_id']);
        $table->dropColumn('group_id');
        
        // Kembalikan receiver_id jadi tidak nullable (jika bawaan awalnya begitu)
        $table->foreignId('receiver_id')->change();
        });
    }
};
