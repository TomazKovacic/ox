<?php  namespace ox\Users;

use \DB as DB;
/**
 *
 */
class Users {

  private $registerStatus;

  function __construct() {

    //$app = app();
		//$this->request = $app->request;
		//$this->session = $app->request->getSession();
		//$this->cookies = $app->request->cookies;

    $this->registerStatus = false;
    $this->user = array();
  }

  // ---------------------------------------------------------------------------

  public static function check($request) {

    //print 'Users::check()<br>';

    $app = app();
    $cookies = $request->cookies;
    $users = $app['users'];

    $access_token = $cookies->get('accessToken');
    $device_token = $cookies->get('deviceToken');

    $sql = "SELECT * FROM users u
      LEFT JOIN licences l on u.id = l.user_id
        WHERE l.access_token = :access_token AND l.logged = 1";

    $user_data = DB::selectOne($sql,  [':access_token' => $access_token]);

    $sql = "SELECT *
      FROM users u
      LEFT JOIN licences l ON u.id = l.user_id
      LEFT JOIN devices d ON l.id = d.licence_id
      WHERE d.device_token = :device_token";

    $device_data = DB::selectOne($sql,  [':device_token' => $device_token]);

    //print_r2($device_data);

    //print '--- debug: --- <br><br>';
    //print 'access token: ' . $access_token . '<br>';
    //print 'device token: ' . $device_token . '<br>';
    //print 'sql: ' . $sql . '<br><br>';
    //print 'user data:'; print_r2( $user_data );


    if( $user_data != null ) {
      //IT IS
      //print 'token it is : '. $access_token.'<br>';

      $users = (array)$user_data;
      $app['users']->registerStatus = true;
      $app['users']->user = $users;

      return true;

    } elseif( $device_data != null ) {

      //preveri, za device

      $users = (array)$device_data;
      $users['is_device'] = 1;
      $app['users']->registerStatus = true;
      $app['users']->user = $users;

      return true;

    } else {
      // IT IS NOT

      //print 'no token<br>';
      return false;
    }
  }

  // ---------------------------------------------------------------------------

  public static function isRegistered() {

    $app = app();
    return $app['users']->registerStatus;

  }

  // ---------------------------------------------------------------------------

  public static function isUnregistered() {

    return !$users->registerStatus;  // NOT $users->registerStatus
  }

  // ---------------------------------------------------------------------------

  public static function isDevice() {

    $app = app();
    $user = $app['users']->user;
    if( isset($user['is_device'])) { return true; }
    return false;
  }


  // ---------------------------------------------------------------------------

  public static function deviceNumber() {

    $app = app();
    $user = $app['users']->user;
    if(isset($user['number'])) { return $user['number']; }
    return '';
  }

  // ---------------------------------------------------------------------------

  public static function deviceToken() {

    $app = app();
    $user = $app['users']->user;

    //print 'ttt';print_r2($user);
    if(isset($user['device_token'])) { return $user['device_token']; }
    return '';
  }

  // ---------------------------------------------------------------------------

  public static function deviceTokenShort() {

    $app = app();
    $user = $app['users']->user;

    //print 'ttt';print_r2($user);
    if(isset($user['device_token'])) { return substr($user['device_token'],0,6); }
    return 0;
  }

  // ---------------------------------------------------------------------------

  public static function getTitle() {
    $app = app();
    if( isset($app['users']->user['title']) ) {
        return $app['users']->user['title'];
    }
    return null;
  }

  // ---------------------------------------------------------------------------

  public static function getUserInfo() {
    $app = app();
    if( empty($app['users']->user) ) return null;
    return $app['users']->user;
  }

  // ---------------------------------------------------------------------------
  public static function getLicences() {
    //get licences for current, logged user

    $app = app();
    $user = $app['users']->user;
    $user_id = $user['user_id'];
    //print_r2($user);

    $sql = "SELECT l.*, u.title as name, p.title as project_name,

            least( 100,
            round( datediff(now(), date(l.expiration_date - interval 1 year) ) /
                   dayofyear( last_day(date_add(now(), INTERVAL 12-month(now()) month)) ) * 100 ) ) as expire_procent


        FROM licences l
        LEFT JOIN users u ON u.id = l.user_id
        LEFT JOIN projects p ON l.version_id = p.version_id
        WHERE l.user_id = ? AND l.record_status = 1
        ORDER BY l.id ASC";

    $data = DB::select($sql, [$user_id] );

    //print_r2($data);
    return $data;

  }
  // ---------------------------------------------------------------------------

  public static function getDeviceLicences($deviceToken) {
    //get licences for current, logged user
    //print 'device token: ' . $deviceToken . '<br>';

    $app = app();
    $user = $app['users']->user;
    $user_id = $user['user_id'];
    //print_r2($user);

    $sql = "SELECT l.*, u.title as name, p.title as project_name,

            least( 100,
            round( datediff(now(), date(l.expiration_date - interval 1 year) ) /
                   dayofyear( last_day(date_add(now(), INTERVAL 12-month(now()) month)) ) * 100 ) ) as expire_procent


                   FROM devices d
                       LEFT JOIN licences l ON l.id = d.licence_id
                       LEFT JOIN users u ON u.id = l.user_id
                       LEFT JOIN projects p ON l.version_id = p.version_id
        WHERE l.user_id = ?
          AND d.device_token = ?
          AND l.record_status = 1
        ORDER BY l.id ASC";

    $data = DB::select($sql, [$user_id, $deviceToken] );

    //print_r2($data);
    return $data;

  }

  // ---------------------------------------------------------------------------
  public static function canAccessProject($version_id) {

    $app = app();
    $user = $app['users']->user;
    if( !isset($user['user_id']) ) {
        // no access, bye
        return false;
    }

    $user_id = $user['user_id'];
    //print_r2($user);

    if(isset($user['is_device']) && ($user['is_device'] == 1)) {

      //check for device
      $sql = "SELECT *
          FROM licences l
          LEFT JOIN devices d ON d.licence_id = l.id
          WHERE l.user_id = :user_id
            AND l.version_id = :version_id
            AND l.record_status = 1
            AND l.expiration_date>date_format(now(),'%Y-%m-%d')
            AND d.device_token = :device_token";

      $data = DB::selectOne($sql, [':user_id' => $user_id, ':version_id' => $version_id, ':device_token' => $user['device_token']] );

      //D: print nl2br($sql);


    }  else {
      $sql = "SELECT *
          FROM licences
          WHERE user_id = :user_id AND version_id = :version_id AND record_status = 1 AND expiration_date>date_format(now(),'%Y-%m-%d')";

      $data = DB::selectOne($sql, [':user_id' => $user_id, ':version_id' => $version_id] );
    }



    if($data !== null) {  //success
      //D print_r2($data);
      return true;
    }

    return false;

  }

}
