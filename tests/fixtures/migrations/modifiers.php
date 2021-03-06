<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateModifiersTable extends Migration
{
    public function up()
    {
        Schema::create('modifiers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('title')->nullable();
            $table->string('name', 1000)->unique()->charset('utf8');
            $table->string('content')->default('');
            $table->decimal('total', 10, 2);
            $table->char('ssn', 11);
            $table->enum('role', ["user","admin","owner"]);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('modifiers');
    }
}
