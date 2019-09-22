<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->bigIncrements('id');

            // User relation
            $table->unsignedInteger("user_id")->nullable();
            $table->foreign("user_id")
                ->references("id")
                ->on("users")
                ->onUpdate("cascade")
                ->onDelete("cascade");

            $table->string("key", 50)->index();
            $table->string("description")->nullable();
            $table->text("arguments")->nullable();

            // Multi steps actions
            $table->unsignedTinyInteger("steps")->default(1);
            $table->unsignedTinyInteger("stage")->default(0);

            // Action state flags
            $table->unsignedTinyInteger("is_confirmed")->default(1);
            $table->unsignedTinyInteger("is_done")->default(0);
            $table->unsignedTinyInteger("is_canceled")->default(0);
            $table->unsignedTinyInteger("cancel_reason")->nullable();

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
        Schema::dropIfExists('actions');
    }
}
