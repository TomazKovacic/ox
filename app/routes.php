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

  Route::post('login', 'loginController@process');   
   

  //routes
  Route::get('routes', 'routesController@index');
  Route::get('routes/{id}', 'routesController@single_page');
  Route::get('routes/page/{name}', 'routesController@named_page');
  Route::get('routes/page/{name}/id/{id}', 'routesController@third_page');

  //optional parameters
  // routes/page/{name?}  with ?

  //database
  Route::get('database', 'databaseController@index');

  //session
  Route::get('sessions', 'sessionsController@index');


  Route::get('testarray', array( 'as' => 'testing', 'uses' => 'sessionController@index' ) );


  Route::get('fntest', function() { return 'this is function test'; } );

  //laravel compatimility
  // Route::put, Route::delete, Route::match, Route::any, 

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
