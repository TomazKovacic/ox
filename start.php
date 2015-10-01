<?php

  define('ROOT_DIR', __DIR__ );

  //define('GDEBUG', true );
  define('GDEBUG', false );


  require ROOT_DIR . '/vendor/autoload.php'; 

//print_r2( spl_autoload_functions() );


  $config = array();
  $config['paths'] = require_once ROOT_DIR . '/app/paths.php';

  // check if database.php, if not, stop, inform about database.sample.php
  if( !file_exists( ROOT_DIR . '/app/config/database.php' )) { print 'copy app/config/database.php to app/config/database.php and edit it.'; exit(); } 

  $config['database'] = require_once ROOT_DIR . '/app/config/database.php';


  $app = new ox\framework\Application();

  $app->setConfiguration($config);

//print 'Exit QQ in line ' . __LINE__ . '<br>'; exit();

  use ox\Facades\Facade;

  Facade::init();
  Facade::setApplication($app);

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

  $app->registerCoreContainerAliases();


  require ROOT_DIR . '/app/routes.php';

  return $app;
