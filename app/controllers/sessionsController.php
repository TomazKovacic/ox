<?php

//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\HttpFoundation\Cookie;

class sessionsController extends baseController {

  protected $request;
  protected $session;

  function __construct() {


    $app = app();

    $this->request = $app->request;
    $this->session = $this->request->getSession();
    $this->cookies = $this->request->cookies;


    //print 'sessionsController::__construct() <br>';



  }

  function __destruct() {}



  function index() {

    //print 'sessionsController::index() <br>';


  	$data = array();

    $sd = $this->session->all();

    //print_r2( $sd );

  	$data['sessions'] = $sd;
    $data['sessions_array'] = get_r2($sd);

    $colorselect = array();
    
    if(isset($sd['color'])) {
      $colorkey = $sd['color'];
      $colorselect[$colorkey] = ' selected="SELECTED"';
      $data['selected']['color'] = $colorselect;
    }


    //print_r2($this->request->cookies); //exit();

    $cookies = $this->request->cookies->all();
    $data['cookies'] = get_r2($cookies);

    return View::make('sessions', $data);
  }






  public function process() {

    //print 'sessionsController::process <br>';

    $post = $this->request->request->all();

    if( isset($post) && (count($post)>0) ) {
      foreach($post as $k=>$v) {
      
        //print $k . ': ' . $v . '<br>';
        $this->session->set($k, $v);
      }
    }

    ////$this->cookies->set('color', $post['color']);

    //$response = new Response();
    //$response->headers->setCookie(  new Cookie('color', $post['color'], 0, '/', null, false, false)  );

    //$headers

    $cookies = array();
    $cookies = array('color' =>  $post['color']);


    //print_r2( $post ); 
    //exit();

    $headers = array();

    return Redirect::to('sessions', 302, $headers, $cookies);

  }
}
