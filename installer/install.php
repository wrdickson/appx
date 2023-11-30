<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="lib/bootstrap-5.2.3.css" rel="stylesheet">
    <title>installer.php</title>
  </head>
  <body>
    
    <div class="container">
      <h1>Installer</h1>
      <div class="row">
        
          <?php
          require('../api/vendor/autoload.php');
          require('./lib/Installer.php');
          require('lib/Validate.php');

          use \Brick\Money\Money;
          
          echo "<p>Post variables: </p>";
          echo "<pre>";
          var_dump($_POST);
          echo "</pre>";
          //  TODO validate inputs!

          //  ASSIGN POST VARIABLES
          $db_user = $_POST['db_user'];
          $db_pass = $_POST['db_pass'];
          $db_name = $_POST['db_name'];
          $db_host = $_POST['db_host'];
          $admin_user = $_POST['admin_user'];
          $admin_user_email = $_POST['admin_user_email'];
          $admin_user_password_1 = $_POST['admin_user_password_1'];
          $admin_user_password_2 = $_POST['admin_user_password_2'];
          $cur_code = $_POST['currency_code'];
          $jwt_key = $_POST['jwt_key'];
          $site_name = $_POST['site_name'];
          $default_locale = $_POST['default_locale'];
          $house_account_name = $_POST['house_account_name'];
          $unassigned_space_name = $_POST['unassigned_space_name'];
          $time_zone = $_POST['tz'];

          //  TODO: a little validation here, huh?

          //  THIS WILL THROW IF PDO CAN'T CONNECT
          try {
            $installer = new Installer ( 
            $db_user, 
            $db_pass,
            $db_name,
            $db_host,
            $admin_user,
            $admin_user_email,
            $admin_user_password_1,
            $admin_user_password_2,
            $cur_code,
            $jwt_key,
            $site_name,
            $default_locale,
            $house_account_name,
            $time_zone,
            $unassigned_space_name
          );
        } catch ( Exception $e ) {
          echo $e->getMessage();
        }




          $installer->write_config_file();

          echo "<h1>HERE</h1>";

          $installer->create_tables();

          $installer->apply_constraints();

          //  DEBUG add dummy initial data
          $installer->x_insert_initial_data();


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