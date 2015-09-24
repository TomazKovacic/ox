<?php

/*
  Route::get('/', function() {

    return View::make('index');

  });
  */

#print('debug_backtrace:<pre>'); 
//print_r( debug_backtrace());
#debug_print_backtrace();
#print('</pre>');


#print('routes.php SKIP<br><br>'); return;

   //ox\Classes\Route::get('/', 'homeController@index');
   Route::get('/', 'homeController@index');


   //Route::post('/home', 'homeController@save');

   Route::get('about',  'aboutController@index');
   Route::get('login',  'loginController@index');
   Route::get('logout', 'logoutController@index');

   //POST

  Route::post('save', 'saveController@index');   
   


	/*
	* syntax for future


	Route::get('*',  '__some__Controller@index');
	Route::get('some/*',  '__some__Controller@index');
	Route::get('some/*',  '__some__Controller@index');

	Route::get('/test', function() {
     return 'This is test';
	});

	Route::get('number/{id}', function($id) {
    	return 'Number '.$id;
	});

  */
