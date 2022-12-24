<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMemosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('memos', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->index();

            $table->timestamp('start')->nullable()->default(null);
            $table->timestamp('end')->nullable()->default(null);

            $table->string('title', 255);
            $table->string('detail', 1023)->nullable();
            $table->string('theme_color', 15)->nullable();
            $table->string('icon', 255)->nullable();
            $table->string('image_s3_url', 1023)->nullable();
            $table->string('url', 1023)->nullable();
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
        Schema::dropIfExists('memos');
    }
}
