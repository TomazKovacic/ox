<?php

  use ox\Facades\Facade;

  define('ROOT_DIR', __DIR__ );

  require ROOT_DIR . '/vendor/autoload.php';
  //print_r2( spl_autoload_functions() );

  $config = array();
  $config = require_once ROOT_DIR . '/app/config/config.php';
  //print_r2($_SERVER); print '---'; print_r2($config); exit();
  $config['paths']  = require_once ROOT_DIR . '/app/config/paths.php';

  // check if database.php, if not, stop, inform about database.sample.php
  if( !file_exists( ROOT_DIR . '/app/config/database.php' )) { print 'copy <kbd><u>app/config/database.sample.php</u></kbd> to <kbd><u>app/config/database.php</u></kbd> and edit it.'; exit(); }

  $config['database'] = require_once ROOT_DIR . '/app/config/database.php';


  $app = new ox\Framework\Application();

  $app->setConfiguration($config);
  Facade::init();
  Facade::setApplication($app);

  $aliases = array(
      //'App'     => 'ox\Facades\App',
      'Auth'     => 'ox\Facades\Auth',
      'DB'       => 'ox\Facades\DB',
      'Form'     => 'ox\Facades\Form',
      'HTML'     => 'ox\Facades\HTML',
      'Input'    => 'ox\Facades\Input',
      'Lang'     => 'ox\Facades\Lang',
      'Redirect' => 'ox\Facades\Redirect',
      'Request'  => 'ox\Facades\Request',
      'Route'    => 'ox\Facades\Route',
      'Session'  => 'ox\Facades\Session',
      'URL'      => 'ox\Facades\URL',
      'View'     => 'ox\Facades\View'
  );

  foreach($aliases as $alias => $original) {

    //print('alias: ' . $original . ' * ' . $alias . '<br>');
    class_alias( $original, $alias );
  }
  $app->registerCoreContainerAliases();

  require ROOT_DIR . '/app/routes.php';
  
  //print basename(__FILE__).'/'. __LINE__ . ':'; print_r2($app['Route']->getRoutes());

  return $app;
