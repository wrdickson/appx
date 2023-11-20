<?php

require 'vendor/autoload.php';
require 'config/config.php';
require 'lib/Auth.php';
require 'lib/DataConnector.php';
require 'lib/F3Auth.php';
require 'lib/RootSpaces.php';
require 'lib/SpaceTypes.php';
require 'lib/Reservations.php';
require 'lib/Customers.php';
require 'lib/Customer.php';
require 'lib/Account.php';
require 'lib/Reservation.php';
require 'lib/Folios.php';
require 'lib/Folio.php';


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

//  DEBUG
$f3->route('GET /test', 'Test_Controller->index');
$f3->route('GET /test/do-something', 'Test_Controller->do_something');

//  AUTH
$f3->route('POST /login', 'Auth_Controller->login');
$f3->route('POST /authorize-token', 'Auth_Controller->authorize_token');

//  CUSTOMERS
$f3->route('POST /customers/search', 'Customer_Controller->search_customers');
$f3->route('POST /customers/', 'Customer_Controller->create_customer');

//  OPTIONS
$f3->route('GET /autoload-options', 'Options_Controller->get_autoload_options');

//  RESERVATIONS
$f3->route('POST /reservations/', 'Reservation_Controller->create_reservation');
$f3->route('POST /reservations/availability', 'Reservation_Controller->check_availability_by_dates');

//  ROOT SPACES
$f3->route('POST /root-spaces', 'Root_Space_Controller->get_root_spaces');

//  SPACE TYPES
$f3->route('POST /space-types', 'Space_Type_Controller->get_space_types');


//  SPACE_TYPES


//  start the router
$f3->run();