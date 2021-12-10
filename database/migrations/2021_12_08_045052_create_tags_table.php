<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTagsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tags', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->index('user_id');

//            $table->foreign('user_id')->references('user_id')->on('streams');
            $table->string('tag_id');
            $table->timestamp(\App\Models\MainModel::CREATED_AT)->default(\Illuminate\Support\Facades\DB::raw('CURRENT_TIMESTAMP'));
        });

        Schema::table('tags', function (Blueprint $table) {
           // $table->index('user_id');
            $table->foreign('user_id')->references('user_id')->on('streams');
        });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tags');
    }
}
