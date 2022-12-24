<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSafetyCheckNotifiesRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('safety_check_responses', function (Blueprint $table) {
            $table->uuid('safety_check_id');
            $table->uuid('user_id');
            $table->date('notified_at');
            $table->timestamp('answered_at')->nullable();
            $table->unsignedTinyInteger('answer')->default(0)->comment('0：未回答、1：無事（OK）、2：要支援（NG）');
            $table->longText('comment')->nullable();
            $table->unsignedTinyInteger('response')->default(0)->comment('0：未入力、1：対応中、2：対応済み');

            $table->timestamps();
            $table->uuid('created_by')->nullable();
            $table->uuid('updated_by')->nullable();

            $table->primary(['safety_check_id', 'user_id', 'notified_at'], 'safety_responses_primary_keys');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('safety_check_responses');
    }
}
