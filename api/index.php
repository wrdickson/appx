<?php

require 'vendor/autoload.php';
require 'config/config.php';

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

$f3->route('GET /test', 'Test_Controller->index');

$f3->route('GET /test/do-something', 'Test_Controller->do_something');

//  start the router
$f3->run();