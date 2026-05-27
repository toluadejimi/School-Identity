<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->string('session')->nullable()->after('department');
            $table->string('faculty')->nullable()->after('session');
            $table->string('level')->nullable()->after('faculty');
            $table->string('address')->nullable()->after('phone');
            $table->string('guardian_name')->nullable()->after('address');
            $table->string('guardian_phone')->nullable()->after('guardian_name');
        });
    }

    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            $table->dropColumn([
                'session',
                'faculty',
                'level',
                'address',
                'guardian_name',
                'guardian_phone',
            ]);
        });
    }
};
