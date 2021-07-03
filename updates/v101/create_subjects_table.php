<?php namespace Crazy\Declinations\Updates\V101;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateSubjectsTable Migration
 */
class CreateSubjectsTable extends Migration
{
    public function up()
    {
        Schema::create('crazy_declinations_subjects', function (Blueprint $table) {
            $table->increments('id');
            
            $table->boolean("is_active")->default(0);
            $table->boolean("is_translate")->default(0);
            $table->string('name');
            $table->string('code');
            $table->string('controller');
            $table->string('model');
            
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('crazy_declinations_subjects');
    }
}
