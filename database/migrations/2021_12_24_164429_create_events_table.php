<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('events', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('organization_id')->index();
            $table->uuid('department_id');

            $table->tinyText('title');
            $table->tinyInteger('is_allday')->nullable()->default(0);
            $table->tinyInteger('is_general_meeting')->nullable()->default(0);
            $table->timestamp('start')->nullable();
            $table->timestamp('end')->nullable();
            $table->string('place')->nullable();
            $table->longText('place_url')->nullable();
            $table->longText('detail')->nullable();
            $table->longText('detail_url')->nullable();
            $table->timestamp('period')->nullable();
            $table->tinyText('ok_title')->nullable();
            $table->tinyText('cancel_title')->nullable();
            $table->tinyText('theme_color')->nullable();
            $table->tinyText('icon')->nullable();
            $table->tinyInteger('repeat')->nullable()->default(0);
            $table->tinyText('repeat_week')->nullable();
            $table->tinyInteger('repeat_date')->nullable()->default(0);
            $table->timestamp('repeat_start')->nullable();
            $table->timestamp('repeat_end')->nullable();

            $table->timestamps();
            $table->softDeletes();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();
            $table->uuid('deleted_by')->nullable();

            $table->index(['department_id', 'organization_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('events');
    }
}
