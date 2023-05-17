<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('doctor_id')->nullable();;
            $table->foreign('doctor_id')->references('id')->on('doctors');
            $table->string('appointment_no');
            $table->date('appointment_date');
            $table->string('patient_name');
            $table->string('patient_phone');
            $table->decimal('total_fee', 8, 2);
            $table->decimal('paid_amount', 8, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('appointments');
    }
};
