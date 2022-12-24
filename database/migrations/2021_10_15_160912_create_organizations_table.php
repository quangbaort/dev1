<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name');
            $table->string('simple_name');
            $table->string('name_kana');
            $table->string('zip_code', 7);
            $table->string('prefecture', 15);
            $table->string('city', 15);
            $table->string('street', 127);
            $table->string('building', 127)->nullable();
            $table->string('tel', 11)->nullable();
            $table->string('fax', 11)->nullable();
            $table->tinyInteger('sort')->default(0);
            $table->integer('account_limit')->default(0);
            $table->integer('storage_limit')->default(0)->comment('単位：GB');
            $table->mediumInteger('storage_used')->nullable()->default(0)->comment('単位：KB');
            $table->string('s3_bucket_name', 100)->nullable();
            $table->tinyInteger('calendar_enabled')->default(0)->nullable()->comment('0：無効、1：有効、無効の場合メニュー非表示');
            $table->tinyInteger('news_enabled')->default(0)->nullable()->comment('0：無効、1：有効、無効の場合メニュー非表示');
            $table->tinyInteger('safety_check_enabled')->default(0)->nullable()->comment('0：無効、1：有効、無効の場合メニュー非表示');
            $table->tinyInteger('library_enabled')->default(0)->nullable()->comment('0：無効、1：有効、無効の場合メニュー非表示');
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
        Schema::dropIfExists('organizations');
    }
}
