<?php


class loginController extends baseController {

  function __construct() {
    
    $app = app();
    $this->request  = $app->request;
    $this->response = $app->response;
    $this->session  = $app->request->getSession();


  }

  function index() {

		return View::make('login');
  }


  function process() {

    $post = $this->request->request->all();


    if( Auth::check() ) {
      return Redirect::to('/user');

    } else {

      if( Auth::attempt( array('username' => $post['username'], 'password' => $post['password']) )  ) {

        return Redirect::to('/user');

      } else {

        return Redirect::back();
      }

    }

  }

}
