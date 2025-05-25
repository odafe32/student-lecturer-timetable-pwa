<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->string('endpoint');
            $table->text('keys'); 
            $table->string('user_id')->nullable();
            $table->timestamps();
            
            $table->unique('endpoint');
        });
    }

    public function down()
    {
        Schema::dropIfExists('push_subscriptions');
    }
};