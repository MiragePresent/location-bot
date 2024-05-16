<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateChurchPatchesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('church_patches', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('church_id');
            $table->foreign('church_id')
                ->references('id')
                ->on('churches')
                ->onUpdate('cascade')
                ->onUpdate('cascade');

            $table->string('address', 255)->nullable();
            $table->decimal('latitude')->nullable();
            $table->decimal('longitude')->nullable();

            $table->json('original');

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
        Schema::dropIfExists('church_patches');
    }
}
