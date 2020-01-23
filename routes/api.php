<?php

use Illuminate\Http\Request;

Route::post('register', 'UserController@register');
Route::post('login', 'UserController@login');

Route::middleware(['jwt.verify'])->group(function(){
	//book
	Route::get('/book/{limit}/{offset}', "BookController@getAll");
	Route::post('/book', "BookController@store");
	Route::post('/book/find/{limit}/{offset}', "BookController@find");
	Route::delete('/book/{id}', "BookController@delete");
	Route::post('/book/{id}', "BookController@update");
	
	//user
	Route::get('user/{limit}/{offset}', "UserController@getAll");
	Route::post('user/{limit}/{offset}', "UserController@find");
	Route::delete('user/delete/{id}', "UserController@delete");
	Route::post('user/ubah', "UserController@ubah");

	//cek login
	Route::get('user/check' , "UserController@getAuthenticatedUser");

		//Peminjam
		Route::post('pinjam/{id}', "PinjamController@index");
		Route::post('kembali/{id}', "PinjamController@kembali");
		Route::get('pinjam/{limit}/{offset}', "PinjamController@getAll");
});
