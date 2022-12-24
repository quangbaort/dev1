<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEventNotifiesRepliesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('event_invite_responses', function (Blueprint $table) {
            $table->uuid('event_id');
            $table->uuid('user_id');
            $table->timestamp('notified_at')->nullable();
            $table->timestamp('answered_at')->nullable();
            $table->unsignedTinyInteger('answer')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->primary(['user_id', 'event_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('event_invite_responses');
    }
}
