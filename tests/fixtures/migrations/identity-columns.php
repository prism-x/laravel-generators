<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRelationshipsTable extends Migration
{
    public function up()
    {
        Schema::create('relationships', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('post_id');
            $table->unsignedBigInteger('another');
        });
    }

    public function down()
    {
        Schema::dropIfExists('relationships');
    }
}
