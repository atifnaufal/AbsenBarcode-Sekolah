<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('avatar')->nullable()->after('email');
        });

        Schema::table('school_settings', function (Blueprint $table) {
            $table->boolean('registration_enabled_students')->default(false);
            $table->boolean('registration_enabled_teachers')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('avatar');
        });

        Schema::table('school_settings', function (Blueprint $table) {
            $table->dropColumn(['registration_enabled_students', 'registration_enabled_teachers']);
        });
    }
};
