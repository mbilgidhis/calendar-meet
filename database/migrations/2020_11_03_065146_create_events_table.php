<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->engine = 'InnoDB';
            $table->collation = 'utf8mb4_general_ci';
            $table->uuid('id')->primary();
            $table->string('name', 50);
            $table->string('description', 150)->nullable();
            $table->dateTime('start_at');
            $table->dateTime('end_at');
            $table->boolean('active')->default(1);
            $table->string('event_id', 30)->nullable();
            $table->string('event_link', 255)->nullable();
            $table->string('meet_link', 50)->nullable();

            // $table->uuid('user_id');
            $table->foreignUuid('user_id')->references('id')->on('users');

            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
