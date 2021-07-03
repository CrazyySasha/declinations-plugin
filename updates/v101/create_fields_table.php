<?php namespace Crazy\Declinations\Updates\V101;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateFieldsTable Migration
 */
class CreateFieldsTable extends Migration
{
    public function up()
    {
        Schema::create('crazy_declinations_fields', function (Blueprint $table) {
            $table->increments('id');
            
            $table->string('name');
            $table->string('code');
            
            $table->integer('subject_id')->nullable();
            
            $table->integer("sort_order")->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('crazy_declinations_fields');
    }
}
