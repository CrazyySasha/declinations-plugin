<?php namespace Crazy\Declinations\Updates\V101;

use Schema;
use October\Rain\Database\Schema\Blueprint;
use October\Rain\Database\Updates\Migration;

/**
 * CreateDeclinablesTable Migration
 */
class CreateDeclinablesTable extends Migration
{
    public function up()
    {
        Schema::create('crazy_declinations_declinables', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('declinable_id');
            $table->string('declinable_type');
            $table->string('field');
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('crazy_declinations_declinables');
    }
}
