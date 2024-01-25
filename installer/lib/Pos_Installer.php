<?php

Class Pos_Installer {

  private $db_user;
  private $db_pass;
  private $db_name;
  private $db_host;
  private $pdo;

  //  constructor

  public function __construct ($db_user, $db_pass, $db_name, $db_host) {

    //  ASSIGN BOILERPLATE FORM VALUES FROM $_POST PARAMS
    $this->db_user = $db_user;
    $this->db_pass = $db_pass;
    $this->db_name = $db_name;
    $this->db_host = $db_host;

    //  INSTANTIATE PDO
    try {
      $this->pdo = new PDO('mysql:host=' . $this->db_host .';dbname=' . $this->db_name, $this->db_user, $this->db_pass);
      //  see https://stackoverflow.com/questions/60174/how-can-i-prevent-sql-injection-in-php?rq=1
      $this->pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
      $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch ( PDOException $e ) {
      throw new Exception('unable to connet to database');
      
    }

    //  CREATE TABLES
    if( $this->pdo ) {
      $this->create_pos_tables();
      $this->apply_constraints();
    }
  }

  //  public functions

  private function apply_constraints () {

      // payment
      $sql = "ALTER TABLE payment
              ADD CONSTRAINT FK_PaymentSale
              FOREIGN KEY (sale) REFERENCES sale(id)";
      $stmt = $this->pdo->prepare($sql);
      if( $execute = $stmt->execute() ) {
      echo "<p>Payment - Sale constraint added . . . </p>";
      }

      $sql = "ALTER TABLE payment
              ADD CONSTRAINT FK_PaymentPaymentType
              FOREIGN KEY (payment_type) REFERENCES payment_type(id)";
      $stmt = $this->pdo->prepare($sql);
      if( $execute = $stmt->execute() ) {
      echo "<p>Payment - PaymentType constraint added . . . </p>";
      }

      //  product
      $sql = "ALTER TABLE product
              ADD CONSTRAINT FK_ProductTaxGroup
              FOREIGN KEY (tax_group) REFERENCES tax_group(id)";
      $stmt = $this->pdo->prepare($sql);
      if( $execute = $stmt->execute() ) {
      echo "<p>Product - Tax Group constraint added . . . </p>";
      }

      //  sale
      $sql = "ALTER TABLE sale
              ADD CONSTRAINT FK_SaleFolio
              FOREIGN KEY (folio) REFERENCES folios(id)";
      $stmt = $this->pdo->prepare($sql);
      if( $execute = $stmt->execute() ) {
      echo "<p>Sale - Folio constraint added . . . </p>";
      }

      $sql = "ALTER TABLE sale
              ADD CONSTRAINT FK_SaleAccounts
              FOREIGN KEY (sold_by) REFERENCES accounts(id)";
      $stmt = $this->pdo->prepare($sql);
      if( $execute = $stmt->execute() ) {
      echo "<p>Sale - Folio constraint added . . . </p>";
      }

      //  sale_item
      $sql = "ALTER TABLE sale_item
              ADD CONSTRAINT FK_SaleItemSale
              FOREIGN KEY (sale) REFERENCES sale(id)";
      $stmt = $this->pdo->prepare($sql);
      if( $execute = $stmt->execute() ) {
      echo "<p>SaleItem - Sale constraint added . . . </p>";
      }

      $sql = "ALTER TABLE sale_item
              ADD CONSTRAINT FK_SaleItemProduct
              FOREIGN KEY (product) REFERENCES product(id)";
      $stmt = $this->pdo->prepare($sql);
      if( $execute = $stmt->execute() ) {
      echo "<p>SaleItem - Product constraint added . . . </p>";
      }

      //CONSTRAINT `sale_item_ibfk_1` FOREIGN KEY (`sale`) REFERENCES `sale` (`id`),
      //CONSTRAINT `sale_item_ibfk_2` FOREIGN KEY (`product`) REFERENCES `product` (`id`)


  }

  private function create_pos_tables () {
    echo "create_pos_tables() . . .()";
    $this->payment_drop();
    $this->payment_create();
    $this->payment_type_drop();
    $this->payment_type_create();
    $this->product_drop();
    $this->product_create();
    $this->sale_drop();
    $this->sale_create();
    $this->sale_item_drop();
    $this->sale_item_create();
    $this->tax_group_drop();
    $this->tax_group_create();
    $this->tax_type_drop();
    $this->tax_type_create();
  }

  //  private functions

  private function payment_create () {
    $sql = "CREATE TABLE `payment` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `sale` int(11) NOT NULL,
      `payment_type` int(11) NOT NULL,
      `payment_amount` int(11) NOT NULL,
      `payment_date` datetime NOT NULL,
      `payment_ref` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
      PRIMARY KEY (`id`),
      KEY `sale_id` (`sale`),
      KEY `payment_type` (`payment_type`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $stmt = $this->pdo->prepare( $sql );
    if( $execute = $stmt->execute() ) {
      echo "<p>Payment table created . . .</p>";
    }
  }

  private function payment_drop () {
    $sql = "DROP TABLE IF EXISTS `payment`";
    $stmt = $this->pdo->prepare( $sql );
    if( $execute = $stmt->execute() ) {
      echo "<p>Payment table dropped . . .</p>";
    }
  }

  private function payment_type_create () {
    $sql = "CREATE TABLE `payment_type` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `payment_title` varchar(244) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
      `is_active` tinyint(4) NOT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `payment_title` (`payment_title`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $stmt = $this->pdo->prepare( $sql );
    if( $execute = $stmt->execute() ) {
      echo "<p>Payment_type table created . . .</p>";
    }
  }

  private function payment_type_drop () {
    $sql = "DROP TABLE IF EXISTS `payment_type`";
    $stmt = $this->pdo->prepare( $sql );
    if( $execute = $stmt->execute() ) {
      echo "<p>Payment_type table dropped . . .</p>";
    }
  }

  private function product_create () {
    $sql = "CREATE TABLE `product` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `product_title` varchar(244) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
      `sku` varchar(244) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
      `price` int(11) NOT NULL,
      `tax_group` int(11) NOT NULL,
      `is_active` tinyint(4) NOT NULL,
      PRIMARY KEY (`id`),
      UNIQUE KEY `product_code` (`sku`),
      KEY `pos_product` (`tax_group`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $stmt = $this->pdo->prepare( $sql );
    if( $execute = $stmt->execute() ) {
      echo "<p>Product table created . . .</p>";
    }
  }

  private function product_drop () {
    $sql = "DROP TABLE IF EXISTS `product`";
    $stmt = $this->pdo->prepare( $sql );
    if( $execute = $stmt->execute() ) {
      echo "<p>Product table dropped . . .</p>";
    }
  }

  private function sale_create () {
    $sql = "CREATE TABLE `sale` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `folio` int(11) NOT NULL,
      `sold_by` int(11) NOT NULL,
      `sale_date` datetime NOT NULL,
      PRIMARY KEY (`id`),
      KEY `folio` (`folio`),
      KEY `sold_by` (`sold_by`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $stmt = $this->pdo->prepare( $sql );
    if( $execute = $stmt->execute() ) {
      echo "<p>Sale table created . . .</p>";
    }
  }

  private function sale_drop () {
    $sql = "DROP TABLE IF EXISTS `sale`";
    $stmt = $this->pdo->prepare( $sql );
    if( $execute = $stmt->execute() ) {
      echo "<p>Sale table dropped . . .</p>";
    }
  }

  private function sale_item_create () {
    $sql = "CREATE TABLE `sale_item` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `sale` int(11) NOT NULL,
      `product` int(11) NOT NULL,
      `quantity` int(11) NOT NULL,
      `unit_price` int(11) NOT NULL,
      `subtotal` int(11) NOT NULL,
      `tax` int(11) NOT NULL,
      `tax_spread` json NOT NULL,
      PRIMARY KEY (`id`),
      KEY `sale` (`sale`),
      KEY `product` (`product`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $stmt = $this->pdo->prepare( $sql );
    if( $execute = $stmt->execute() ) {
      echo "<p>Sale_item table created . . .</p>";
    }
  }

  private function sale_item_drop () {
    $sql = "DROP TABLE IF EXISTS `sale_item`";
    $stmt = $this->pdo->prepare( $sql );
    if( $execute = $stmt->execute() ) {
      echo "<p>Sale_item table dropped . . .</p>";
    }
  }

  private function tax_group_create () {
    $sql = "CREATE TABLE `tax_group` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `tax_group_title` varchar(244) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
      `tax_types` json NOT NULL,
      `is_active` tinyint(4) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    $stmt = $this->pdo->prepare( $sql );
    if( $execute = $stmt->execute() ) {
      echo "<p>Tax_group table created . . .</p>";
    }
  }

  private function tax_group_drop () {
    $sql = "DROP TABLE IF EXISTS `tax_group`";
    $stmt = $this->pdo->prepare( $sql );
    if( $execute = $stmt->execute() ) {
      echo "<p>Tax_group table dropped . . .</p>";
    }
  }

  private function tax_type_create () {
    $sql = "CREATE TABLE `tax_type` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `tax_title` varchar(244) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL,
      `rate` float NOT NULL,
      `is_active` tinyint(4) NOT NULL,
      PRIMARY KEY (`id`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8";
    $stmt = $this->pdo->prepare( $sql );
    if( $execute = $stmt->execute() ) {
      echo "<p>Tax_type table dropped . . .</p>";
    }
  }

  private function tax_type_drop () {
    $sql = "DROP TABLE IF EXISTS `tax_type`";
    $stmt = $this->pdo->prepare( $sql );
    if( $execute = $stmt->execute() ) {
      echo "<p>Tax_type table dropped . . .</p>";
    }
  }

}
