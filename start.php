<?php

  define('ROOT_DIR', __DIR__ );

  //define('GDEBUG', true );
  define('GDEBUG', false );


  require ROOT_DIR . '/vendor/autoload.php'; 

//print_r2( spl_autoload_functions() );

  $config = array();
  $config['paths'] = require_once ROOT_DIR . '/app/paths.php';

  $aliases = array(
      //'App'     => 'ox\Facades\App',
      'Auth'    => 'ox\Facades\Auth',
      'DB'      => 'ox\Facades\DB',
      'Form'    => 'ox\Facades\Form',
      'HTML'    => 'ox\Facades\HTML',
      'Input'   => 'ox\Facades\Input',
      'Lang'    => 'ox\Facades\Lang',
      'Request' => 'ox\Facades\Request',
      'Route'   => 'ox\Facades\Route',
      'Session' => 'ox\Facades\Session',
      'URL'     => 'ox\Facades\URL',
      'View'    => 'ox\Facades\View'
  );

  foreach($aliases as $alias => $original) {

    //print('alias: ' . $original . ' * ' . $alias . '<br>');
    class_alias( $original, $alias );
  }

  $app = new ox\framework\Application();


  use ox\Facades\Facade;

  Facade::init();
  Facade::setApplication($app);

  require ROOT_DIR . '/app/routes.php';

  return $app;
