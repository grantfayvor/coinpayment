<?php

Route::group([
  'middleware' => [
    'web',
    'auth',
    Harrison\CoinPayment\Http\Middleware\listenConfigFileMiddleware::class
  ],
  'prefix' => 'coinpayment',
  'namespace' => 'Harrison\CoinPayment\Http\Controllers'
],
function() {
    Route::get('/', function(){
      return abort(404);
    })->name('coinpayment.home');
    Route::get('/{serialize}', 'CoinPaymentController@index')->name('coinpayment.create.transaction');
    Route::get('/withdrawal/{serialize}', 'CoinPaymentController@withdrawal')->name('coinpayment.createwithdrawal');
    Route::get('/ajax/rates/{usd}', 'CoinPaymentController@ajax_rates')->name('coinpayment.ajax.rate.usd');
    Route::get('/ajax/transaction/histories', 'CoinPaymentController@transactions_list_any')->name('coinpayment.ajax.transaction.histories');
    Route::post('/ajax/maketransaction', 'CoinPaymentController@make_transaction')->name('coinpayment.ajax.store.transaction');
    Route::post('/ajax/trxinfo', 'CoinPaymentController@trx_info')->name('coinpayment.ajax.trxinfo');
    Route::post('/ajax/transaction/manual/check', 'CoinPaymentController@manual_check')->name('coinpayment.ajax.transaction.manual.check');
    Route::post('/ajax/createtransfer', 'CoinPaymentController@create_transfer')->name('coinpayment.ajax.create.transfer');
    Route::post('/ajax/createwithdrawal', 'CoinPaymentController@create_withdrawal')->name('coinpayment.ajax.create.withdrawal');
    Route::post('/ajax/balances', 'CoinPaymentController@get_balances')->name('coinpayment.ajax.balances');
    Route::post('/ajax/callback_address', 'CoinPaymentController@get_callback_address')->name('coinpayment.ajax.callback.address');

    Route::get('/transactions/histories', 'CoinPaymentController@transactions_list')->name('coinpayment.transaction.histories');
});

Route::group([
    'namespace' => 'Harrison\CoinPayment\Http\Controllers'
], function(){
  Route::post('/coinpayment/ipn', 'CoinPaymentController@receive_webhook')
    ->middleware('web')
    ->name('coinpayment.ipn.received');
});
