<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateNewsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news', function (Blueprint $table) {
            $table->uuid('id')->primary()->index();
            $table->uuid('organization_id')->index();
            $table->uuid('department_id')->index();

            $table->string('title', 255);
            $table->string('image_s3_url', 1023)->nullable();
            $table->timestamp('start')->nullable()->default(null);
            $table->timestamp('end')->nullable()->default(null);

            $table->string('place', 255)->nullable();
            $table->string('place_url', 1023)->nullable();
            $table->string('detail', 1023)->nullable();
            $table->string('detail_url')->nullable();
            $table->string('theme_color', 15)->nullable();
            $table->string('icon', 255)->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news');
    }
}
