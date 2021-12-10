<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStreamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('streams', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('twitch_id');
            $table->unsignedBigInteger('user_id')->unique();
            $table->string('user_login');
            $table->string('user_name');
            $table->string('game_id');
            $table->string('game_name');
            $table->string('type');
            $table->string('title');
            $table->string('viewer_count');
            $table->string('started_at');
            $table->string('language');
            $table->string('thumbnail_url');
            $table->json('tag_ids');
            $table->string('is_mature');

            $table->timestamp(\App\Models\MainModel::CREATED_AT)->default(\Illuminate\Support\Facades\DB::raw('CURRENT_TIMESTAMP'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('streams');
    }
}
