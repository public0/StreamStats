<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRequestLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requestlog', function (Blueprint $table) {
            $table->id('ID');
            $table->string('Path');
            $table->string('ResponseCode');
            $table->string('Message');
            $table->timestamp(\App\Models\MainModel::CREATED_AT);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('requestlog');
    }
}
