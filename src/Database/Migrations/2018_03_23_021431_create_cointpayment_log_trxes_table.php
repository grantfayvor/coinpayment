<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCointpaymentLogTrxesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cointpayment_log_trxes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->string('payment_id')->nullable();
            $table->string('payment_address');
            $table->string('coin', 10)->default('BTC');
            $table->string('fiat', 10)->default('USD');
            $table->string('status_text')->nullable();
            $table->integer('status')->default(0);
            $table->datetime('payment_created_at')->nullable();
            $table->datetime('expired')->nullable();
            $table->datetime('confirmation_at')->nullable();
            $table->double('amount_to_pay', 20, 8)->nullable();
            $table->double('amount', 20, 8)->nullable();
            $table->integer('confirms_needed')->nullable();
            $table->string('qrcode_url')->nullable();
            $table->string('status_url')->nullable();
            $table->text('payload')->nullable();
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
        Schema::dropIfExists('cointpayment_log_trxes');
    }
}
