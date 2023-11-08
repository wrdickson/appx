<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="lib/bootstrap-5.2.3.css" rel="stylesheet">
    <title>install_uno.php</title>
  </head>
  <body>
    
    <div class="container">
      <h1>Installer</h1>
      <div class="row">
        
          <?php
          require('./../api/vendor/autoload.php');
          use \Brick\Money\Money;
          require('lib/init_sql.php');
          require('lib/Validate.php');
          
          echo "<p>Post variables: </p>";
          echo "<PRE>" . var_dump($_POST) . "</PRE>";

          //  TODO validate inputs!

          /**
           * test the the post data with try db connection
           */
          $db_user = $_POST['db_user'];
          $db_pass = $_POST['db_pass'];
          $db_name = $_POST['db_name'];
          $db_host = $_POST['db_host'];
          $admin_user = $_POST['admin_user'];
          $admin_user_email = $_POST['admin_user_email'];
          $admin_user_password_1 = $_POST['admin_user_password_1'];
          $admin_user_password_2 = $_POST['admin_user_password_2'];
          $jwt_key = $_POST['jwt_key'];
          $site_name = $_POST['site_name'];
          $default_locale = $_POST['default_locale'];
          if(isset($_POST['currency_code'])){
            $cur_code = $_POST['currency_code'];
          } else {
            $cur_code = 'USD';
          }
          $time_zone = $_POST['tz'];

          $u1 = Money::of(1, $cur_code);
          $currency_code = $u1->getCurrency()->getCurrencyCode();
          $currency_fraction_digits = $u1->getCurrency()->getDefaultFractionDigits();
          $currency_numeric_code = $u1->getCurrency()->getNumericCode();
          $currency_name = $u1->getCurrency()->getName();
          echo "<hr>";
          echo "<p>Currency code: " . $currency_code . "</p>";
          echo "<p>Fraction digits: " . $currency_fraction_digits . "</p>";
          echo "<p>Numeric code: " . $currency_numeric_code . "</p>";
          echo "<p>Currency name: " . $currency_name . "</p>";
          echo "<hr>";

          echo "<p>Timezone:" . $time_zone . "</p>";
          echo "<hr>";


          try {
            $pdo = new PDO('mysql:host=' . $db_host .';dbname=' . $db_name, $db_user, $db_pass);
            //  see https://stackoverflow.com/questions/60174/how-can-i-prevent-sql-injection-in-php?rq=1
            $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            echo '<p>Connected to database.  Continue with installation . . . </p>';

            /**
             * if pdo passes, write the config file
             */
            echo "<p>Writing config file . . .</p>";
            $f = fopen("../api/config/config.php", "w");
            fwrite( $f, "<?php\n");
            $s = "define('DB_HOST', '{$db_host}');\n";
            fwrite( $f, $s ); 
            $s ="define('DB_USER', '{$db_user}');\n";
            fwrite(  $f, $s);
            $s = "define('DB_PASS', '{$db_pass}');\n";
            fwrite( $f, $s );
            $s = "define('DB_NAME', '{$db_name}');\n";
            fwrite( $f, $s );
            $s = "define('SERVER_NAME', '$db_host');\n";
            fwrite( $f, $s);
            $s = "define('JWT_KEY', '{$jwt_key}');\n";
            fwrite( $f, $s );
            $s = "define('DEFAULT_LOCALE', '{$default_locale}');\n";
            fwrite( $f, $s );
            $s = "define('DEFAULT_TIMEZONE', '{$time_zone}');\n";
            fwrite( $f, $s );
            $s = "define('CURRENCY_CODE', '{$currency_code}');\n";
            fwrite( $f, $s );
            $s = "define('CURRENCY_FRACTION_DIGITS', '{$currency_fraction_digits}');\n";
            fwrite( $f, $s );
            $s = "define('CURRENCY_NUMERIC_CODE', '{$currency_numeric_code}');\n";
            fwrite( $f, $s );
            $s = "define('CURRENCY_NAME', '{$currency_name}');\n";
            fwrite( $f, $s );
            $s = "date_default_timezone_set(DEFAULT_TIMEZONE);\n";
            fwrite( $f, $s );


            //  run the query to drop the accounts table
            $stmt = $pdo->prepare( $drop_accounts_sql );
            if( $execute = $stmt->execute() ) {
              echo "<p>Accounts table dropped . . .</p>";
            }

            //  run the query to build the accounts table
            $stmt = $pdo->prepare( $create_accounts_sql );
            if( $execute = $stmt->execute() ) {
              echo "<p>Accounts table created . . .</p>";
            }

            //  insert the admin user
            $password_hash = password_hash($admin_user_password_1, PASSWORD_DEFAULT);
            $sql = "INSERT INTO accounts (username, email, permission, roles, registered, last_activity, last_login, is_active, password) VALUES (:u, :e, '10', '[]', NOW(), NOW(), NOW(), '1', :pwd)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(":u", $admin_user);
            $stmt->bindParam(":e", $admin_user_email);
            $stmt->bindParam(":pwd", $password_hash);
            if( $stmt->execute() ) {
              echo "<p>Admin account created . . .</p>";
            }

            /**
             * Create the rest of the tables
             */

            //  options
            $stmt = $pdo->prepare($drop_options_sql);
            if( $stmt->execute() ) {
              echo "<p>Options table dropped . . .</p>";
            }

            $stmt = $pdo->prepare($create_options_sql);
            if( $stmt->execute() ) {
              echo "<p>Options table created . . .</p>";
            }

            // insert site_name into options . . .
            $stmt = $pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
            // without this, i get a 'cannot pass value by reference' error
            $nnn = 'site_name';
            $stmt->bindParam(':name', $nnn);
            $stmt->bindParam(':value', $site_name);
            if( $stmt->execute() ) {
              echo "<p>Insert site_name into options . . .</p>";
            }

            // insert currency_code into options . . .
            $stmt = $pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
            // without this, i get a 'cannot pass value by reference' error
            $nnn = 'currency_code';
            $stmt->bindParam(':name', $nnn);
            $stmt->bindParam(':value', $currency_code);
            if( $stmt->execute() ) {
              echo "<p>Insert currency_code into options . . .</p>";
            }

            // insert currency_fraction_digits into options . . .
            $stmt = $pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
            // without this, i get a 'cannot pass value by reference' error
            $nnn = 'currency_fraction_digits';
            $stmt->bindParam(':name', $nnn);
            $stmt->bindParam(':value', $currency_fraction_digits);
            if( $stmt->execute() ) {
              echo "<p>Insert currency_fraction_digits into options . . .</p>";
            }

            // insert currency_numeric_code into options . . .
            $stmt = $pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
            // without this, i get a 'cannot pass value by reference' error
            $nnn = 'currency_numeric_code';
            $stmt->bindParam(':name', $nnn);
            $stmt->bindParam(':value', $currency_numeric_code);
            if( $stmt->execute() ) {
              echo "<p>Insert currency_code into options . . .</p>";
            }

            // insert currency_name into options . . .
            $stmt = $pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
            // without this, i get a 'cannot pass value by reference' error
            $nnn = 'currency_name';
            $stmt->bindParam(':name', $nnn);
            $stmt->bindParam(':value', $currency_name);
            if( $stmt->execute() ) {
              echo "<p>Insert currency_name into options . . .</p>";
            }

            //  insert default locale
            $stmt = $pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
            $nnn = 'default_locale';
            $stmt->bindParam(':name', $nnn);
            $stmt->bindParam(':value', $default_locale);
            if( $stmt->execute() ) {
              echo"<p>Default locale inserted . . .</p>";
            }

            //  insert default timezone
            $stmt = $pdo->prepare("INSERT INTO options ( option_name, option_value, autoload ) VALUES ( :name, :value, 1 )");
            $nnn = 'default_timezone';
            $stmt->bindParam(':name', $nnn);
            $stmt->bindParam(':value', $time_zone);
            if( $stmt->execute() ) {
              echo"<p>Default locale inserted . . .</p>";
            }
            
          } catch ( PDOException $e ) {
              echo "Error creating tables: " . $e->getMessage() . "<br/>";
              die();
          } 
          ?>
      </div>
    </div>
    <style type="text/css">
      p {
        margin-bottom: 4px;
      }
    </style>

    <script src="lib/bootstrap.bundle.min.js"></script>
  </body>
</html>