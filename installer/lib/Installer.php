<?php

use \Brick\Money\Money;

class Installer {

  private $db_user;
  private $db_pass;
  private $db_name;
  private $db_host;
  private $admin_user;
  private $admin_user_email;
  private $admin_user_password_1;
  private $admin_user_password_2;
  private $cur_code;
  private $jwt_key;
  private $site_name;
  private $default_locale;
  private $house_account_name;
  private $unassigned_space_name;
  private $time_zone;
  //  generated at __construct()
  private $currency_code;
  private $currency_fraction_digits;
  private $currency_numeric_code;
  private $currency_name;
  //  PDO ios generated at __construct();
  private $pdo;

  //  these are generated as we insert into tables . . .
  private $house_account_id;
  private $house_account_folio_id;
  private $unassigned_space_type_id;
  private $unassigned_root_space_id;

  public function __construct ($db_user, $db_pass, $db_name, $db_host, $admin_user, $admin_user_email, $admin_user_password_1, $admin_user_password_2, $cur_code, $jwt_key, $site_name, $default_locale, $house_account_name, $time_zone, $unassigned_space_name) {

    //  ASSIGN BOILERPLATE FORM VALUES FROM $_POST PARAMS
    $this->db_user = $db_user;
    $this->db_pass = $db_pass;
    $this->db_name = $db_name;
    $this->db_host = $db_host;
    $this->admin_user = $admin_user;
    $this->admin_user_email = $admin_user_email;
    $this->admin_user_password_1 = $admin_user_password_1;
    $this->admin_user_password_2 = $admin_user_password_2;
    $this->cur_code = $cur_code;
    $this->jwt_key = $jwt_key;
    $this->site_name = $site_name;
    $this->default_locale = $default_locale;
    $this->house_account_name  = $house_account_name; 
    $this->time_zone = $time_zone;
    $this->unassigned_space_name = $unassigned_space_name;

    //  INSTANTIATE PDO
    try {
      $pdo = new PDO('mysql:host=' . $db_host .';dbname=' . $db_name, $db_user, $db_pass);
      //  see https://stackoverflow.com/questions/60174/how-can-i-prevent-sql-injection-in-php?rq=1
      $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
      $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
      $this->pdo = $pdo;
    } catch ( PDOException $e ) {
      throw new Exception('unable to connet to database');
      
    }

    //  use Brick/Money to generate 
    //  currency_fraction_digits
    //  currency_numeric_code
    //  currency_name
    $u1 = Money::of(1, $cur_code);
    $this->currency_code = $u1->getCurrency()->getCurrencyCode();
    $this->currency_fraction_digits = $u1->getCurrency()->getDefaultFractionDigits();
    $this->currency_numeric_code = $u1->getCurrency()->getNumericCode();
    $this->currency_name = $u1->getCurrency()->getName();

  }

  public function apply_constraints () {

    echo "<p>Apply constraints . . . </p>";

    //  folios
    $sql = "ALTER TABLE folios
            ADD CONSTRAINT FK_FolioCustomer
            FOREIGN KEY (customer) REFERENCES customers(id)";
    $stmt = $this->pdo->prepare($sql);
    if( $execute = $stmt->execute() ) {
      echo "<p>Folios - Customers constraint added . . . </p>";
    }

    //  root spaces
    $sql = "ALTER TABLE root_spaces
            ADD CONSTRAINT FK_RootSpaceType
            FOREIGN KEY (space_type) REFERENCES space_types(id)";
    $stmt = $this->pdo->prepare($sql);
    if( $execute = $stmt->execute() ) {
      echo "<p>RootSpaces - space_type constraint added . . . </p>";
    }

    //  reservations - folio
    $sql = "ALTER TABLE reservations
            ADD CONSTRAINT FK_ResFolio
            FOREIGN KEY (folio) REFERENCES folios(id)";
    $stmt = $this->pdo->prepare($sql);
    if( $execute = $stmt->execute() ) {
      echo "<p>Reservations - folio constraint added . . . </p>";
    }

    //  reservations - space_id
    $sql = "ALTER TABLE reservations
            ADD CONSTRAINT FK_ResSpaceId
            FOREIGN KEY (space_id) REFERENCES root_spaces(id)";
    $stmt = $this->pdo->prepare($sql);
    if( $execute = $stmt->execute() ) {
      echo "<p>Reservations - space_id constraint added . . . </p>";
    }

    //  reservations - space_type_pref
    $sql = "ALTER TABLE reservations
            ADD CONSTRAINT FK_ResSpaceTypePref
            FOREIGN KEY (space_type_pref) REFERENCES space_types(id)";
    $stmt = $this->pdo->prepare($sql);
    if( $execute = $stmt->execute() ) {
      echo "<p>Reservations - space_type_pref constraint added . . . </p>";
    }
  }

  public function create_tables () {
    //  FIRST disable foreign key checks so we can drop tables
    echo "<p>create_tables() . . .</p>";
    $sql = "SET FOREIGN_KEY_CHECKS = 0";
    $stmt = $this->pdo->prepare($sql);
    if( $stmt->execute() ) {
      echo "<p>FOREIGN_KEY_CHECKS = 0  . . .</p>";
    }else{
    }
    //  accounts
    echo "<p>accounts_drop . . .</p>";
    $this->accounts_drop();
    echo "<p>accounts_create . . .</p>";
    $this->accounts_create();
    echo "<p>accouonts_insert_admin . . .</p>";
    $this->accounts_insert_admin();
    //  options
    $this->options_drop();
    echo "<p>options_create() . . .</p>";
    $this->options_create();
    echo "<p>options_insert() . . .</p>";
    $this->options_insert();
    //  customers
    $this->customers_drop();
    $this->customers_create();
    echo "<p>insert_house_account() . . .</p>";
    $this->customers_insert_house_account();
    //  do this AFTER customers_insert_house_account() . . . 
    echo "<p>insert_folio_option() . . .</p>";
    $this->customers_insert_folio_option();
    //  space_types
    $this->space_types_drop();
    $this->space_types_create();
    $this->space_types_insert_unassigned();
    $this->space_types_insert_unassigned_option();
    //  root spaces
    //  must execute AFTER space_types creation so we have access to the unassigned space type
    $this->root_spaces_drop();
    $this->root_spaces_create();
    $this->root_spaces_insert_unassigned();
    $this->root_spaces_insert_unassigned_option();
    //  folios
    $this->folios_drop();
    $this->folios_create();
    $this->folios_insert_house_account();
    //  do this AFTER folios_insert_house_account()
    $this->folios_insert_house_account_option();
    //  reservations
    $this->reservations_drop();
    $this->reservations_create();
    //  FINALLY restore foreign key checks
    $sql = "SET FOREIGN_KEY_CHECKS = 1";
    $stmt = $this->pdo->prepare($sql);
    if( $stmt->execute() ) {
      echo "<p>FOREIGN_KEY_CHECKS = 1  . . .</p>";
    }
  }

  private function accounts_create () {
    $create_accounts_sql = "CREATE TABLE `accounts` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `username` varchar(45) NOT NULL,
      `password` text NOT NULL,
      `permission` int(11) NOT NULL,
      `roles` json NOT NULL,
      `email` varchar(144) NOT NULL,
      `registered` datetime NOT NULL,
      `last_activity` datetime NOT NULL,
      `last_login` datetime NOT NULL,
      `is_active` tinyint(4) NOT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `username` (`username`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $stmt = $this->pdo->prepare( $create_accounts_sql );
    if( $execute = $stmt->execute() ) {
      echo "<p>Accounts table created . . .</p>";
    }
  }

  private function accounts_drop () {
    $drop_accounts_sql = "DROP TABLE IF EXISTS `accounts`";
    //  run the query to drop the accounts table
    $stmt = $this->pdo->prepare( $drop_accounts_sql );
    if( $execute = $stmt->execute() ) {
      echo "<p>Accounts table dropped . . .</p>";
    }
  }

  private function accounts_insert_admin () {
    $password_hash = password_hash($this->admin_user_password_1, PASSWORD_DEFAULT);
    $sql = "INSERT INTO accounts (username, email, permission, roles, registered, last_activity, last_login, is_active, password) VALUES (:u, :e, '10', '[]', NOW(), NOW(), NOW(), '1', :pwd)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(":u", $this->admin_user);
    $stmt->bindParam(":e", $this->admin_user_email);
    $stmt->bindParam(":pwd", $password_hash);
    if( $stmt->execute() ) {
      echo "<p>Admin account inserted . . .</p>";
    }
  }

  private function customers_create () {
    $create_customers_sql = "CREATE TABLE `customers` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `last_name` varchar(144) NOT NULL,
      `first_name` varchar(144) NOT NULL,
      `email` varchar(144) NOT NULL DEFAULT '',
      `phone` varchar(45) NOT NULL DEFAULT '',
      `address_1` varchar(144) NOT NULL DEFAULT '',
      `address_2` varchar(144) NOT NULL DEFAULT '',
      `city` varchar(144) NOT NULL DEFAULT '',
      `region` varchar(144) NOT NULL DEFAULT '',
      `country` varchar(144) NOT NULL DEFAULT '',
      `postal_code` varchar(45) NOT NULL DEFAULT '',
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $stmt = $this->pdo->prepare($create_customers_sql);
    if( $stmt->execute() ) {
      echo "<p>Customers table created . . .</p>";
    }
  }

  private function customers_drop () {
    $drop_customers_sql = "DROP TABLE IF EXISTS `customers`";
    $stmt = $this->pdo->prepare($drop_customers_sql);
    if( $stmt->execute() ) {
      echo "<p>Customers table dropped . . .</p>";
    }
  }

  private function customers_insert_folio_option () {
    $stmt = $this->pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( 'house_account_customer', :ha, 1 )");
    $stmt->bindParam(':ha', $this->house_account_id);
    if( $stmt->execute() ) {
      echo"<p>House account option added . . .</p>";
    }
  }

  private function customers_insert_house_account () {
    //  insert house account customer
    $stmt = $this->pdo->prepare("INSERT INTO customers ( id, last_name, first_name ) VALUES ( 1, :ln, '' )");
    $stmt->bindParam(':ln', $this->house_account_name);
    if( $stmt->execute() ) {
      $this->house_account_id = $this->pdo->lastInsertId();
      echo"<p>House account inserted into customers . . .</p>";
      echo "<p>House account id: " . $this->house_account_id . " . . .</p>";
    }
  }

  private function folios_create () {
    $create_folios_sql = "CREATE TABLE `folios` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `customer` int(11) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `customer` (`customer`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $stmt = $this->pdo->prepare($create_folios_sql);
    if( $stmt->execute() ) {
      echo "<p>Folios table created . . .</p>";
    }
  }

  private function folios_insert_house_account_option () {
    $stmt = $this->pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( 'house_account_folio', :haf, 1 )");
    $stmt->bindParam(':haf', $this->house_account_folio_id);
    if( $stmt->execute() ) {
      echo"<p>House folio option added . . .</p>";
    }
  }

  private function folios_insert_house_account () {
    $stmt = $this->pdo->prepare("INSERT INTO folios ( customer ) VALUES ( :hai )");
    $stmt->bindParam(':hai', $this->house_account_id);
    if( $stmt->execute() ) {
      $this->house_account_folio_id = $this->pdo->lastInsertId();
      echo"<p>House account folio inserted . . .</p>";
      echo"<p>House account folio: " . $this->house_account_folio_id . " . . . </p>";
    }
  }

  private function folios_drop () {
    $drop_folios_sql = "DROP TABLE IF EXISTS `folios`";
    $stmt = $this->pdo->prepare($drop_folios_sql);
    if( $stmt->execute() ) {
      echo "<p>Folios table dropped . . .</p>";
    }
  }

  private function options_create () {
    $create_options_sql = "CREATE TABLE `options` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `option_name` varchar(144) NOT NULL,
      `option_value` longtext  NOT NULL,
      `autoload` tinyint(4) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $stmt = $this->pdo->prepare($create_options_sql);
    if( $stmt->execute() ) {
      echo "<p>Options table created . . .</p>";
    }
  }

  private function options_drop () {
    $drop_options_sql = "DROP TABLE IF EXISTS `options`";
    $stmt = $this->pdo->prepare($drop_options_sql);
    if( $stmt->execute() ) {
      echo "<p>Options table dropped . . .</p>";
    }
  }

  private function options_insert () {
    // insert site_name into options . . .
    $stmt = $this->pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
    // without this, i get a 'cannot pass value by reference' error
    $nnn = 'site_name';
    $stmt->bindParam(':name', $nnn);
    $stmt->bindParam(':value', $this->site_name);
    if( $stmt->execute() ) {
      echo "<p>Insert site_name into options . . .</p>";
    }
    // insert currency_code into options . . .
    $stmt = $this->pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
    // without this, i get a 'cannot pass value by reference' error
    $nnn = 'currency_code';
    $stmt->bindParam(':name', $nnn);
    $stmt->bindParam(':value', $this->currency_code);
    if( $stmt->execute() ) {
      echo "<p>Insert currency_code into options . . .</p>";
    }
    // insert currency_fraction_digits into options . . .
    $stmt = $this->pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
    // without this, i get a 'cannot pass value by reference' error
    $nnn = 'currency_fraction_digits';
    $stmt->bindParam(':name', $nnn);
    $stmt->bindParam(':value', $this->currency_fraction_digits);
    if( $stmt->execute() ) {
      echo "<p>Insert currency_fraction_digits into options . . .</p>";
    }
    // insert currency_numeric_code into options . . .
    $stmt = $this->pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
    // without this, i get a 'cannot pass value by reference' error
    $nnn = 'currency_numeric_code';
    $stmt->bindParam(':name', $nnn);
    $stmt->bindParam(':value', $this->currency_numeric_code);
    if( $stmt->execute() ) {
      echo "<p>Insert currency_code into options . . .</p>";
    }
    // insert currency_name into options . . .
    $stmt = $this->pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
    // without this, i get a 'cannot pass value by reference' error
    $nnn = 'currency_name';
    $stmt->bindParam(':name', $nnn);
    $stmt->bindParam(':value', $this->currency_name);
    if( $stmt->execute() ) {
      echo "<p>Insert currency_name into options . . .</p>";
    }
    //  insert default locale
    $stmt = $this->pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
    $nnn = 'default_locale';
    $stmt->bindParam(':name', $nnn);
    $stmt->bindParam(':value', $this->default_locale);
    if( $stmt->execute() ) {
      echo"<p>Default locale inserted . . .</p>";
    }
    //  insert default timezone
    $stmt = $this->pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
    $nnn = 'default_timezone';
    $stmt->bindParam(':name', $nnn);
    $stmt->bindParam(':value', $this->time_zone);
    if( $stmt->execute() ) {
      echo"<p>Default locale inserted . . .</p>";
    }
  }

  private function reservations_create () {
    $create_reservations_sql = "CREATE TABLE `reservations` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `folio` int(11) NOT NULL,
      `is_assigned` tinyint(4) NOT NULL,
      `space_type_pref` int(11) NOT NULL,
      `space_id` int(11) NOT NULL,
      `space_code` json NOT NULL,
      `checkin` date NOT NULL,
      `checkout` date NOT NULL,
      `people` int(11) NOT NULL,
      `beds` int(11) NOT NULL,
      `history` json NOT NULL,
      `status` int(11) NOT NULL,
      `notes` json NOT NULL,
      PRIMARY KEY (`id`),
      KEY `folio` (`folio`),
      Key `space_type_pref` (`space_type_pref`),
      KEY `space_id` (`space_id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $stmt = $this->pdo->prepare($create_reservations_sql);
    if( $stmt->execute() ) {
      echo "<p>Reservations table created . . . </p>";
    }
  }

  private function reservations_drop () {
    $drop_reservations_sql = "DROP TABLE IF EXISTS `reservations`";
    $stmt = $this->pdo->prepare($drop_reservations_sql);
    if( $stmt->execute() ) {
      echo "<p>Reservations table dropped . . .</p>";
    }
  }

  private function root_spaces_create () {
    $create_root_spaces_sql = "CREATE TABLE `root_spaces` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `space_type` int(11) NOT NULL,
      `title` varchar(45) NOT NULL,
      `child_of` int(11) NOT NULL,
      `show_children` tinyint(4) NOT NULL,
      `people` int(11) NOT NULL,
      `beds` int(11) NOT NULL,
      `display_order` int(11) NOT NULL,
      `is_active` tinyint(4) NOT NULL,
      `is_unassigned` tinyint(4) NOT NULL,
      PRIMARY KEY (`id`),
      KEY `space_type` (`space_type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $stmt = $this->pdo->prepare($create_root_spaces_sql);
    if( $stmt->execute() ) {
      echo "<p>Root spaces table created . . .</p>";
    }
  }

  private function root_spaces_drop () {
    $drop_root_spaces_sql = "DROP TABLE IF EXISTS `root_spaces`";
    $stmt = $this->pdo->prepare($drop_root_spaces_sql);
    if( $stmt->execute() ) {
      echo "<p>Root spaces table dropped . . .</p>";
    }
  }

  private function root_spaces_insert_unassigned () {
    $insert_unassigned_space_sql = "INSERT INTO `root_spaces` (`space_type`, `title`, `child_of`, `show_children`, `people`, `beds`, `display_order`, `is_active`, `is_unassigned`) VALUES (:st, :na, '0', '0', '1000000', '1000000', '1', '1', '1');";
    $stmt = $this->pdo->prepare($insert_unassigned_space_sql);
    $stmt->bindParam(':st', $this->unassigned_space_type_id);
    $stmt->bindParam(':na', $this->unassigned_space_name);
    if( $stmt->execute() ){
      $this->unassigned_root_space_id = $this->pdo->lastInsertId();
      echo "<p>Unassigned root space inserted . . .</p>";
      echo "<p>Id: " . $this->unassigned_root_space_id . " . . .</p>";
    }
  }

  private function root_spaces_insert_unassigned_option () {
    $stmt = $this->pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
    $nnn = 'unassigned_root_space';
    $stmt->bindParam(':name', $nnn);
    $stmt->bindParam(':value', $this->unassigned_root_space_id);
    if( $stmt->execute() ) {
      echo"<p>Unassigned root space inserted into options . . .</p>";
    }
  }

  private function space_types_create () {
    $create_space_types_sql = "CREATE TABLE `space_types` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `title` varchar(45) NOT NULL,
      `is_active` tinyint(4) NOT NULL,
      `display_order` int(11) NOT NULL,
      `is_unassigned` tinyint(4) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $stmt = $this->pdo->prepare($create_space_types_sql);
    if( $stmt->execute() ) {
      echo "<p>Space types table created . . .</p>";
    }
  }

  private function space_types_drop () {
    $drop_space_types_sql = "DROP TABLE IF EXISTS `space_types`";
    $stmt = $this->pdo->prepare($drop_space_types_sql);
    if( $stmt->execute() ) {
      echo "<p>Space types table dropped . . .</p>";
    }
  }

  private function space_types_insert_unassigned () {
    $sql = "INSERT INTO `space_types` ( title, is_active, display_order, is_unassigned ) VALUES ( :t, 1, 100000, 1)";
    $stmt = $this->pdo->prepare($sql);
    $stmt->bindParam(':t', $this->unassigned_space_name);
    if( $stmt->execute() ) {
      $this->unassigned_space_type_id = $this->pdo->lastInsertId();
      echo "<p>Unassigned space_type inserted . . .</p>";
      echo "<p>Unassigned space type id: " . $this->unassigned_space_type_id . " . . .</p>";
    }
  }

  private function space_types_insert_unassigned_option () {
    $stmt = $this->pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
    $nnn = 'unassigned_space_type';
    $stmt->bindParam(':name', $nnn);
    $stmt->bindParam(':value', $this->unassigned_space_type_id);
    if( $stmt->execute() ) {
      echo"<p>Unassigned root space inserted into options . . .</p>";
    }
  }

  public function write_config_file () {

    echo "<p>Writing config file . . .</p>";

    $f = fopen("../api/config/config.php", "w");
    fwrite( $f, "<?php\n");
    $s = "define('DB_HOST', '{$this->db_host}');\n";
    fwrite( $f, $s ); 
    $s ="define('DB_USER', '{$this->db_user}');\n";
    fwrite(  $f, $s);
    $s = "define('DB_PASS', '{$this->db_pass}');\n";
    fwrite( $f, $s );
    $s = "define('DB_NAME', '{$this->db_name}');\n";
    fwrite( $f, $s );
    $s = "define('SERVER_NAME', '{$this->db_host}');\n";
    fwrite( $f, $s);
    $s = "define('JWT_KEY', '{$this->jwt_key}');\n";
    fwrite( $f, $s );
    $s = "define('DEFAULT_LOCALE', '{$this->default_locale}');\n";
    fwrite( $f, $s );
    $s = "define('DEFAULT_TIMEZONE', '{$this->time_zone}');\n";
    fwrite( $f, $s );
    $s = "define('CURRENCY_CODE', '{$this->cur_code}');\n";
    fwrite( $f, $s );
    $s = "define('CURRENCY_FRACTION_DIGITS', '{$this->currency_fraction_digits}');\n";
    fwrite( $f, $s );
    $s = "define('CURRENCY_NUMERIC_CODE', '{$this->currency_numeric_code}');\n";
    fwrite( $f, $s );
    $s = "define('CURRENCY_NAME', '{$this->currency_name}');\n";
    fwrite( $f, $s );
    $s = "date_default_timezone_set(DEFAULT_TIMEZONE);\n";
    fwrite( $f, $s );
    $s = "// written from Installer Class test: 2\n";
    fwrite( $f, $s );

    echo "<p>Config file written . . .</p>";
  }

  public function x_insert_initial_data () {
    //  customers
    $sql = "
    INSERT INTO `customers` (`id`, `last_name`, `first_name`, `email`, `phone`, `address_1`, `address_2`, `city`, `region`, `country`, `postal_code`) VALUES
    (2,	'Hiker',	'Harry',	'',	'',	'123',	'',	'',	'',	'',	''),
    (3,	'Biker',	'Dazeda',	'asdf@something.net',	'509 389 2462',	'123 S Main St',	'Apt 323',	'Moab',	'UT',	'USA',	'84532'),
    (4,	'Climber',	'Carl',	'',	'',	'',	'',	'',	'',	'',	''),
    (6,	'Bazooka',	'Bobby',	'dsfasda',	'123412',	'PO Box 123',	'',	'asdfasd',	'6686796',	'tuitiuy',	'vcxnxbcv'),
    (7,	'Cunningham',	'Carl',	'',	'',	'',	'',	'',	'',	'',	''),
    (8,	'Schumacher',	'Shenanigans',	'',	'',	'',	'',	'',	'',	'',	''),
    (9,	'Dickson',	'Bill',	'',	'',	'1213 S Hyw 1913',	'',	'Moab',	'UT',	'',	'84532'),
    (11,	'Wallbanger',	'Harvey',	'',	'',	'',	'',	'',	'',	'',	''),
    (12,	'Dormer',	'Huge',	'',	'',	'',	'',	'',	'',	'',	''),
    (13,	'Bentley',	'Scott',	'',	'',	'1213 S Hwy 191',	'',	'',	'',	'',	''),
    (14,	'Ottinger',	'Bill',	'',	'',	'',	'',	'',	'',	'',	''),
    (15,	'Kane',	'',	'',	'',	'',	'',	'',	'',	'',	''),
    (16,	'Materno',	'Jose',	'',	'',	'',	'',	'',	'',	'',	''),
    (18,	'Fernandez',	'Felecia',	'',	'',	'',	'',	'',	'',	'',	''),
    (21,	'Jimenez',	'Jorge',	'jorge@trekbill.com',	'55 11 22 33 44 55',	'123 Calle b',	'Apartmiento 4',	'San Pedro La Laguna',	'Solala',	'Guatemala',	'abd 6 he 5'),
    (22,	'Baloney',	'Bangus',	'',	'',	'',	'',	'',	'',	'',	''),
    (23,	'Crew',	'J',	'aa@bb.com',	'12341234',	'',	'',	'',	'',	'',	''),
    (24,	'Escobar',	'Pablo',	'aa@bb.com',	'12341234',	'',	'',	'',	'',	'',	''),
    (25,	'Burke',	'P',	'',	'',	'',	'',	'',	'',	'',	''),
    (26,	'Uuru',	'U',	'',	'',	'',	'',	'',	'',	'',	''),
    (27,	'Baloney',	'Biggus',	'bg@ll.com',	'12341234',	'',	'',	'',	'',	'',	''),
    (28,	'AAkerman',	'AAron',	'',	'',	'',	'',	'',	'',	'',	''),
    (30,	'Springer',	'Clive',	'asdf@asdf.com',	'12341234',	'',	'',	'',	'',	'',	''),
    (31,	'Gillespie',	'Happy',	'',	'',	'',	'',	'',	'',	'',	''),
    (32,	'Turbid',	'Truly',	'',	'',	'',	'',	'',	'',	'',	''),
    (33,	'Beleagered',	'Badly',	'',	'',	'',	'',	'',	'',	'',	''),
    (34,	'Eggregious',	'Enormously',	'',	'',	'',	'',	'',	'',	'',	''),
    (35,	'Cautions',	'Cathy',	'',	'',	'',	'',	'',	'',	'',	''),
    (37,	'Escobar',	'Enrique',	'',	'',	'',	'',	'',	'',	'',	''),
    (38,	'Greeley',	'Horace',	'',	'',	'',	'',	'',	'',	'',	''),
    (39,	'Hillsborough',	'Winema',	'',	'',	'',	'',	'',	'',	'',	''),
    (40,	'Bazooka',	'Yesslin',	'',	'',	'',	'',	'',	'',	'',	''),
    (41,	'Monster',	'Ukia',	'',	'',	'',	'',	'',	'',	'',	''),
    (42,	'Benson',	'George',	'',	'',	'',	'',	'',	'',	'',	''),
    (43,	'Tuesday',	'Tricia',	'',	'',	'',	'',	'',	'',	'',	''),
    (80,	'Zalento',	'Zaforia',	'zaput@zippy.com',	'123412345',	'123 S Main',	'Apt 2',	'Zaputnik',	'Zamoria',	'Zambia',	'Z1234'),
    (81,	'Bojangels',	'Barney',	'',	'',	'',	'',	'',	'',	'',	''),
    (82,	'Darwin',	'Charles',	'',	'',	'',	'',	'',	'',	'',	''),
    (83,	'Jefe',	'El',	'fu@yado.com',	'123456',	'',	'',	'',	'',	'',	''),
    (84,	'Hatch',	'Hiker',	'hh@trekbill.com',	'',	'',	'',	'',	'',	'',	''),
    (85,	'Cinnamon',	'Cindy',	'',	'',	'',	'',	'',	'',	'',	''),
    (86,	'Violencia',	'Victoria',	'',	'',	'',	'',	'',	'',	'',	''),
    (87,	'Drala',	'Darla',	'',	'',	'',	'',	'',	'',	'',	''),
    (92,	'Zatty',	'Kris',	'',	'',	'',	'',	'',	'',	'',	''),
    (93,	'Jonathon',	'Casper',	'',	'',	'',	'',	'',	'',	'',	''),
    (94,	'Bins',	'Jabari',	'',	'',	'',	'',	'',	'',	'',	''),
    (95,	'Marquardt',	'Consuelo',	'',	'',	'',	'',	'',	'',	'',	''),
    (96,	'Oberbrunner',	'Beryl',	'',	'',	'',	'',	'',	'',	'',	''),
    (97,	'Heidenreich',	'Mercedes',	'',	'',	'',	'',	'',	'',	'',	''),
    (98,	'Lesch',	'Carlee',	'',	'',	'',	'',	'',	'',	'',	''),
    (99,	'Murphy',	'Emery',	'',	'',	'',	'',	'',	'',	'',	''),
    (100,	'Beatty',	'Marlon',	'',	'',	'',	'',	'',	'',	'',	''),
    (102,	'Hackman',	'Harry',	'',	'',	'',	'',	'',	'',	'',	''),
    (104,	'Cox',	'Michail',	'',	'',	'',	'',	'',	'',	'',	''),
    (106,	'Mason',	'Mulligan',	'',	'',	'',	'',	'',	'',	'',	''),
    (108,	'Baxoo',	'Boingo',	'',	'',	'',	'',	'',	'',	'',	''),
    (109,	'Hulligan',	'Harry',	'',	'',	'',	'',	'',	'',	'',	''),
    (111,	'Rogers',	'Doug',	'',	'',	'',	'',	'',	'',	'',	''),
    (112,	'Hawkins',	'Stephen',	'',	'',	'',	'',	'',	'',	'',	''),
    (113,	'Bazooka',	'Barney',	'',	'',	'',	'',	'',	'',	'',	''),
    (114,	'Cahoonga',	'Carly',	'',	'',	'',	'',	'',	'',	'',	''),
    (115,	'Schmulderhulnd',	'Schwancy',	'',	'',	'',	'',	'',	'',	'',	'');
    ";
    $stmt = $this->pdo->prepare($sql);
    if( $stmt->execute() ) {
      echo "<p>Initial customers inserted . . .</p>";
    }

    //  space_types
    $sql = "
    INSERT INTO `space_types` (`id`, `title`, `is_active`, `display_order`, `is_unassigned`) VALUES
    (2,	'Dorm Bed',	1,	10, 0),
    (3,	'Room',	1,	20, 0),
    (4,	'Cabin',	1,	30, 0),
    (5,	'Camping',	1,	40, 0),
    (6,	'House',	1,	50, 0);
    ";
    $stmt = $this->pdo->prepare($sql);
    if( $stmt->execute() ) {
      echo "<p>Initial space_types inserted . . .</p>";
    }

    //  root_spaces
    $sql = "
    INSERT INTO `root_spaces` (`id`, `space_type`, `title`, `child_of`, `show_children`, `people`, `beds`, `display_order`, `is_active`, `is_unassigned`) VALUES
    (2,	3,	'1 - Room',	0,	1,	4,	4,	100,	1,	0),
    (3,	6,	'Castle',	0,	1,	0,	0,	1000,	1,	0),
    (4,	3,	'Castle - Room 1',	3,	0,	4,	4,	1005,	1,	0),
    (5,	2,	'Castle - 1 - Bed 1',	4,	1,	1,	1,	1020,	1,	0),
    (6,	2,	'Castle - 1 - Bed 2',	4,	1,	0,	0,	1030,	1,	0),
    (7,	2,	'Castle - 1 - Bed 3',	4,	1,	0,	0,	1040,	1,	0),
    (8,	3,	'Castle - Room 2',	3,	1,	0,	0,	1200,	1,	0),
    (9,	3,	'3 - Room',	0,	0,	0,	0,	301,	1,	0),
    (10,	3,	'Castle - Room 3',	3,	0,	0,	0,	1300,	1,	0),
    (11,	3,	'Castle - Room 4',	3,	1,	0,	0,	1400,	1,	0),
    (12,	3,	'Castle - Room 5',	3,	1,	4,	2,	1500,	1,	0),
    (13,	3,	'2 - Room',	0,	1,	4,	2,	200,	1,	0),
    (14,	2,	'Castle 5 - Bed 1',	12,	1,	1,	1,	1530,	1,	0),
    (15,	2,	'Castle 5 - Bed 2',	12,	0,	1,	1,	1540,	1,	0),
    (16,	2,	'Castle 5 - Bed 3',	12,	0,	1,	1,	1550,	1,	0),
    (17,	2,	'Castle - 1 -  Bed 4',	4,	0,	1,	1,	1050,	1,	0),
    (18,	4,	'Land Yacht',	0,	0,	2,	1,	1600,	1,	0);
    ";
    $stmt = $this->pdo->prepare($sql);
    if( $stmt->execute() ) {
      echo "<p>Initial root_spaces inserted . . .</p>";
    }
  }

}
