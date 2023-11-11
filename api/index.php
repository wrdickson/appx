<?php

require 'vendor/autoload.php';
require 'config/config.php';
require 'lib/Auth.php';
require 'lib/DataConnector.php';
require 'lib/F3Auth.php';


//  instantiate $f3 
$f3 = \Base::instance();

$db = new DB\SQL(
  'mysql:host=' . DB_HOST. ';port=3306;dbname=' . DB_NAME,
  DB_USER,
  DB_PASS
);

// assign the db connection to $f3 object
$f3->set('DB', $db);
// autoload the controllers and models into the $f3 object
$f3->set('AUTOLOAD', 'controller/');

//  ITERATE THEM ROUTES . . .

$f3->route('GET /test', 'Test_Controller->index');

$f3->route('GET /test/do-something', 'Test_Controller->do_something');

//  AUTH
$f3->route('POST /login', 'Auth_Controller->login');
$f3->route('POST /authorize-token', 'Auth_Controller->authorize_token');

//  Options
$f3->route('GET /autoload-options', 'Options_Controller->get_autoload_options');

//  start the router
$f3->run();