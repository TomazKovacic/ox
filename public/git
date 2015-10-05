<html>
<head>
  <title>OX 2</title>
  
  <link rel="stylesheet" type="text/css" href="/style/bootstrap.css">
  <link rel="stylesheet" type="text/css" href="/style/style.css" />
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</heady>
<body>

<?php

//twig test
//- use composer's autoload
// create example from /app/views

 require  '../vendor/autoload.php'; 


$loader = new Twig_Loader_Filesystem('../app/views');
//$twig = new Twig_Environment($loader, array( 'cache' => '../app/cache/views') );
$twig = new Twig_Environment($loader, array( 'cache' => false ) );

$template = $twig->loadTemplate('index.twig.html');

$data = array();

echo $template->render( $data );
?>
<br>
============================================================================<br>
Twig test

