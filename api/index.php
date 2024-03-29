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
require 'lib/Accounts.php';
require 'lib/Reservation.php';
require 'lib/Folios.php';
require 'lib/Folio.php';
require 'lib/Validate.php';
require 'lib/Payment_Types.php';


//  instantiate $f3 
$f3 = \Base::instance();

//  get a db connection
$db = new DB\SQL(
  'mysql:host=' . DB_HOST. ';port=3306;dbname=' . DB_NAME, DB_USER, DB_PASS
);

// assign the db connection to $f3 object
$f3->set('DB', $db);
// autoload the controllers into the $f3 object
$f3->set('AUTOLOAD', 'controller/');

//  BOILERPLATE DEM ROUTES . . .

//  DEBUG
$f3->route('GET /test', 'Test_Controller->index');
$f3->route('GET /test/do-something', 'Test_Controller->do_something');

//  ACCOUNTS
$f3->route('POST /accounts-pagination', 'Account_Controller->get_accounts_pagination');
$f3->route('POST /accounts-update', 'Account_Controller->update_account');
$f3->route('POST /accounts-create', 'Account_Controller->create_account');
$f3->route('POST /accounts-update-pwd', 'Account_Controller->update_password');

//  AUTH
$f3->route('POST /login', 'Auth_Controller->login');
$f3->route('POST /authorize-token', 'Auth_Controller->authorize_token');

//  CUSTOMERS
$f3->route('POST /customers/search', 'Customer_Controller->search_customers');
$f3->route('POST /customers/', 'Customer_Controller->create_customer');

//  FOLIOS
$f3->route('POST /folio/@id', 'Folio_Controller->get_folio');
$f3->route('POST /folio-post-sale', 'Folio_Controller->post_folio_sale');

//  OPTIONS
$f3->route('GET /autoload-options', 'Options_Controller->get_autoload_options');

//  PAYMENT_TYPE
$f3->route('POST /get-active-payment-types', 'Payment_Type_Controller->get_active_payment_types');

//  PRODUCT
$f3->route('POST /product/search', 'Product_Controller->search_products_by_sku');

//  RESERVATIONS
$f3->route('POST /reservations/', 'Reservation_Controller->create_reservation');
$f3->route('POST /reservations/range', 'Reservation_Controller->get_reservations_by_range');
$f3->route('POST /reservations/availability', 'Reservation_Controller->check_availability_by_dates');
$f3->route('POST /reservations/range-ignore-res', 'Reservation_Controller->check_availability_by_dates_ignore_res');
$f3->route('POST /reservations/update1/', 'Reservation_Controller->update_reservation_1');

//  ROOT SPACES
$f3->route('GET /root-spaces', 'Root_Space_Controller->get_root_spaces');
$f3->route('POST /root-spaces/update/@root_space_id', 'Root_Space_Controller->update_root_space');
$f3->route('POST /root-spaces-create', 'Root_Space_Controller->create_root_space');
$f3->route('POST /root-spaces-delete', 'Root_Space_Controller->delete_root_space');

//  SPACE TYPES
$f3->route('GET /space-types', 'Space_Type_Controller->get_space_types');
$f3->route('POST /space-type-update', 'Space_Type_Controller->update_space_type');
$f3->route('POST /space-type-create', 'Space_Type_Controller->create_space_type');
$f3->route('POST /space-type-delete', 'Space_Type_Controller->delete_space_type');

//  start the router
$f3->run();