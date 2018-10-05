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

    $this-> key = $app->config['key'];

		//$this->connection = DB::getPdo();

	}

	// ----------------

	public function check() {

		//print 'AuthManager::check() <br>';

		$cookie_token  = $this->cookies->get('token');
		$session_token = $this->session->get('token');


        //print 'cookie_token: <br>'. $cookie_token . '<br>' . 'session_token: <br>'. $session_token . '<br>' ;  //exit();

        //$e =  $encryptor->encrypt($cookie_token);
        //print 'cookie_token E : <br>'. $e . '<br>';

        //$n =  $encryptor->decrypt($e);
        //print 'cookie_token N : <br>'. $n . '<br>';
        //print '<br>'. $session_token . '<br>' . $n . '<br><br>';


		if( empty($cookie_token) || empty($session_token) ) { return false; }

		if( $cookie_token == $session_token ) {
			return true;
		}

        //če še ni dekriptan:
        $encryptor = new \ox\Encryption\Encrypter($this-> key);

		if( $encryptor->decrypt( $cookie_token ) == $session_token ) {
			return true;
		}


        /*
        print 'Test 1:<br><br>';

        print  $cookie_token . '<br>' . $session_token . '<br><br>' ;




        print 'Test 2:<br><br>';

        print  $encryptor->decrypt( $cookie_token ) . '<br>' . $session_token . '<br><br>' ;
        */


		return false;
	}


	public function guest() {
		return ! $this->check();
	}



	public function attempt(array $credentials = array(), $remember = false, $login = true) {

		//print 'AuthManager::attempt() <br>';//exit();
		//print 'AuthManager::attempt() credentials: '. implode(', ', $credentials) .'<br>';

		$this->user = $this->retrieveByCredentials($credentials);
		return $this->user;
	}



	// ----------------


	function retrieveByCredentials($credentials) {

		$table = 'users'; //future: config ...

		$app = app();
		$config = $app->config;

		if(isset($config['auth']['table'])) {
			$table = $config['auth']['table'];
		}

		//print 'retrieveByCredentials. config: auth table: '. $table .'<br>';
		//print 'c: '; print_r2( $config['auth'] );



		//print_r2( $config );

		$whereSegment = '';

        $bindings = array();

		foreach($credentials as $key=>$val) {

			if ( $key != 'password') {

				if($whereSegment) $whereSegment .= ' AND ';
                $whereSegment .= $key . '= :' . $key;

                $xkey = ':' . $key;
                $bindings[$xkey] = $val;

			}
		}

		$sql = "SELECT * FROM $table WHERE $whereSegment";

        //dbg: print 'SQL:<br>';
        //dbg: print $sql . '<br>';
        //dbg: print 'Bindings: <br>';
        //dbg: print_r2 ($bindings);
        //dbg: exit();


		$user = DB::select($sql, $bindings);


		//dbg: print_r2($user); print '<br><br>';





		if ( (!is_null($user)) && (!empty($user)) && (count($user)>0) ) {

			$user = $user[0];  // take first



			if( password_verify($credentials['password'], $user->password) ) {

				//print 'AuthManager::retrieveByCredentials() password_verify SUCCESS <br>';
				//print_r2($user);

				$this->user = $user;

				$this->login();
				unset( $user->password);

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
		    'user_id'  => $this->user->id,
		    'username' => $this->user->username
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

        //tmp, naj bo iz config.a
        $table = 'user';
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


	public function user($id = NULL) { //new usate 16.7.17

		//print 'AuthManager::user() <br>';

		if ($this->loggedOut) return;

		if ( ! is_null($this->user) ) {
			return $this->user;
		}

        //$id = $this->session->get($this->getName());

        //print_r2($this->session->get('user'));

        if ( ! is_null( $this->session->get('user') )) {

            //print 'user session found. <br>';
            return $this->session->get('user');
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
