<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionActivitiesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('action_activities', function (Blueprint $table) {
            $table->bigIncrements('id');
            // Action relation
            $table->unsignedBigInteger("action_id");
            $table->foreign("action_id")
                ->references("id")
                ->on("actions")
                ->onUpdate("cascade")
                ->onDelete("cascade");

            $table->unsignedTinyInteger("stage")->default(0);
            $table->text("data")->nullable();
            $table->unsignedTinyInteger("status")->default(1);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('action_activities');
    }
}
