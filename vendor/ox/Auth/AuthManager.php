<?php

namespace ox\Auth;

use \Firebase\JWT\JWT;

use \DB as DB;
use \Redirect as Redirect;

class AuthManager {

	protected $loggedOut = false;

	protected $user;
	protected $request;
	protected $session;
	protected $cookies;

	protected $connection;

	function __construct() {		
		
		$app = app();
		$this->request = $app->request;
		$this->session = $app->request->getSession();
		$this->cookies = $app->request->cookies;
		//$this->connection = DB::getPdo();

	}

	// ----------------

	public function check() {

		//print 'AuthManager::check() <br>';

		$cookie_token  = $this->cookies->get('token');
		$session_token = $this->session->get('token');

		//print 'cookie_token: '. $cookie_token . '<br>' . 'session_token: '. $session_token . '<br>' ;  exit();

		if( empty($cookie_token) || empty($session_token) ) { return false; }

		if( $cookie_token == $session_token ) {

			return true;
			//return false;
		}

		return false;
	}
		

	public function guest() {
		return ! $this->check();
	}



	public function attempt(array $credentials = array(), $remember = false, $login = true) {

		//print 'AuthManager::attempt() <br>';
		//exit();

		//print 'AuthManager::attempt() credentials: '. implode(', ', $credentials) .'<br>';

		$this->user = $this->retrieveByCredentials($credentials);
		return $this->user;
	}

  

	// ----------------


	function retrieveByCredentials($credentials) {



		$fieldnames = '';
		$table = 'users'; //future: config ...
		$whereSegment = '';

		foreach($credentials as $key=>$val) {

			if ( $key != 'password') {


				if($whereSegment) $whereSegment .= ' AND ';
				$whereSegment .= $key . '=' . "'" . $val. "'" ;

			}
		}

		//$sql = "SELECT $fieldnames FROM $table WHERE $whereSegment";
		$sql = "SELECT * FROM $table WHERE $whereSegment";

		//print 'AuthManager::retrieveByCredentials() sql = '. $sql .'<br>';

		$user = DB::select($sql);


		//dbg: print 'type test $user: ' . gettype($user) . ', value: /'.implode('#', $user) .'/<br><br>';

		if ( (!is_null($user)) && (!empty($user)) && (count($user)>0) ) {

			$user = $user[0];  // take first


			if( password_verify($credentials['password'], $user['password']) ) {

				//
				print 'AuthManager::retrieveByCredentials() password_verify SUCCESS <br>';

				//print_r2($user);

				$this->user = $user;

				$this->login();

				unset( $user['password']);
				return $user;

			} else {
				//
				print 'AuthManager::retrieveByCredentials() password_verify FAIL <br>';

				return null;
			}



		} else {
			return null;
		}

	}

	function login() {

		//print 'AuthManager::login()';

		$token_id    = base64_encode(mcrypt_create_iv(32));
		$server_name = $this->request->headers->get('host');
		$issued_at   = time();
		$not_before  = $issued_at;   // +10
		$expire      = $issued_at + 8*3600;  //+60

		$token_data = array(
		'iat' => $issued_at,       // issued timestamp
		'jti' => $token_id,        //Json Token Id
		'iss' => $server_name,     //Issuer
		'nbf' => $not_before,
		'data' => array(
		    'user_id'  => $this->user['id'],
		    'username' => $this->user['username']
		  )
		);



		$jwt = JWT::encode($token_data, $token_id);
		$decoded = JWT::decode($jwt, $token_id, array('HS256'));

		//print 'JWT:' . $jwt . '<br>';
		//print 'Decoded:';
		//print_r2 ($decoded);
		//exit();

		$this->session->set('token', $jwt);
		Redirect::setCookie('token', $jwt);

	}

	function logout() {

		
		$this->session->remove('token');

		Redirect::removeCookie('token');

		//$response->headers->clearCookie('nameOfTheCookie');

	}


	function retrieveByID($id) { //not used yet

		$id = intval($id);
		$sql = "SELECT * FROM $table WHERE id = ?";
		$user = DB::select($sql, array($id));


		return $user;
	}

	// ----------------

	function bcrypt_hash( $input_password ) {
	
		$options = array('cost' => 10);
		$password = password_hash( $input_password, PASSWORD_BCRYPT, $options);
		
		return $password;
	
	}

	// ----------------


	public function user($id) { //not used

		//print 'AuthManager::user() <br>';

		if ($this->loggedOut) return;

		if ( ! is_null($this->user) ) {
			return $this->user;
		}


		$user = null;

		if ( ! is_null($id)) {
			$user = $this->retrieveByID($id);
		}

		//future
		//cookie support, @see Laravel 4, Illuminate\Auth\Guard.php

		/*
		$recaller = $this->getRecaller();

		if (is_null($user) && ! is_null($recaller))
		{
			$user = $this->getUserByRecaller($recaller);
		}
		*/


		return $this->user = $user;
	}


}//class end