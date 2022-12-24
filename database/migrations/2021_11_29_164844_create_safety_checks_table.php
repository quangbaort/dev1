<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSafetyChecksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('safety_checks', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->index();
            $table->uuid('department_id')->index();
            $table->tinyText('title');
            $table->longText('detail')->nullable();
            $table->longText('detail_url')->nullable();
            $table->unsignedTinyInteger('repeat')->default(0)->comment('0：無効、1：日、2：週、3：月');
            $table->tinyText('repeat_week')->nullable()->comment('曜日毎に0：OFF、1：ONを管理。例）1,0,0,0,0,0,0　 ⇒　月曜ON');
            $table->unsignedTinyInteger('repeat_date')->nullable()->default(0);
            $table->date('repeat_start')->nullable();
            $table->date('repeat_end')->nullable();

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
        Schema::dropIfExists('safety_checks');
    }
}
