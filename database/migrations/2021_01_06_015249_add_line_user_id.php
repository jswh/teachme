<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLineUserId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('teachers', function (Blueprint $table) {
            $table->string('line_user_id')->nullable()->before('created_at');
        });
        Schema::table('students', function (Blueprint $table) {
            $table->string('line_user_id')->nullable()->before('created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (Schema::hasTable('teachers')) {
            Schema::table('teachers', function (Blueprint $t) {
                $t->dropColumn('line_user_id');
            });
        }
        if (Schema::hasTable('students')) {
            Schema::table('students', function (Blueprint $t) {
                $t->dropColumn('line_user_id');
            });
        }
    }
}
