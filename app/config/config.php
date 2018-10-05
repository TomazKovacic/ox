<?php

return array(
  'url' => 'http://' . $_SERVER['SERVER_NAME'] . '/',

  'key' => 'e71bbfa516acdf6a313830ea9e822f26',

  'providers' => array(
    'ox\Auth\AuthServiceProvider',
    'ox\Cookie\CookieServiceProvider',
    'ox\Database\DatabaseServiceProvider',
    'ox\Encryption\EncryptionServiceProvider',
    'ox\Hash\HashServiceProvider',
    'ox\Routing\RoutingServiceProvider',
    'ox\Session\SessionServiceProvider',
    'ox\View\ViewServiceProvider',
    'ox\UserAgent\UserAgentServiceProvider',
    'ox\Users\UsersServiceProvider'

  ),

  'aliases' => array(
    'Auth'     => 'ox\Facades\Auth',
    'DB'       => 'ox\Facades\DB',
    'Hash'     => 'ox\Facades\Hash',
    'Route'    => 'ox\Facades\Route',
    'View'     => 'ox\Facades\View',
    'Redirect' => 'ox\Facades\Redirect'
  ),

  'session' => array(
    'driver' => 'file',
    'lifetime' => 120,
    'expire_on_close' => false,
    'files' => null, // ... laravel storage_path().'/sessions',
    'connection' => null,
    'table' => 'sessions',
    'lottery' => array(2, 100),
    'path' => '/',
    'domain' => null,
    'secure' => false
  ),
  'auth' => array(
    'table' => 'administrators'
  )
);
