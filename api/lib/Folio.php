<?php

use Brick\Money\Money;

Class Folio{
  private $id;
  //  $customer is retreived from customer table
  private $customer;
  //  $sales is retreived from sale table
  private $sales;
  //  $currency_code is retreived from options table
  private $currency_code;
  

  public function __construct( $id ) {

    $this->get_currency_code();

    $this->sales = array();
    $pdo = DataConnector::get_connection();

    //first get the basics: id, customer
    $stmt = $pdo->prepare("SELECT * FROM folios WHERE id = :id");
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
      $this->id = $obj->id;
      $this->customer = $obj->customer;
      
    }
      //  1. sales:
    $stmt = $pdo->prepare("SELECT * FROM sale WHERE folio = :i");
    $stmt->bindParam(':i', $id);
    $i = $stmt->execute();
    $sales = array();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)) {
      $arr = array();
      $arr['id'] = $obj->id;
      $arr['folio'] = $obj->folio;
      $arr['sold_by'] = $obj->sold_by;
      $arr['sale_date'] = $obj->sale_date;
      $arr['payments'] = array();
      $arr['items'] = array();
      

      /*
      *  handle the case where browser is pointed to a folio that doesn't exist
      *  if that's the case, we throw a 500 error "$arr does not exist"
      *  TODO this could be better handled
      */
      if($arr) {

        //  get payments per sale
        $stmt2 = $pdo->prepare("SELECT payment.id, payment.payment_type, payment.payment_amount, payment.payment_date, payment.payment_ref, payment_type.payment_title FROM payment INNER JOIN payment_type ON payment.payment_type = payment_type.id WHERE payment.sale = :si");
        $stmt2->bindParam(':si', $arr['id']);
        $stmt2->execute();
        while($obj2 = $stmt2->fetch(PDO::FETCH_OBJ)){
          $arr2 = array();
          $arr2['payment_id'] = $obj2->id;
          $arr2['type_id'] = $obj2->payment_type;
          $arr2['amount'] = Money::ofMinor($obj2->payment_amount, $this->currency_code)->getAmount();
          $arr2['date'] = $obj2->payment_date;
          $arr2['ref'] = $obj2->payment_ref;
          $arr2['type'] = $obj2->payment_title;
          array_push($arr['payments'], $arr2);
        }

        //  get sale items per sale
        $stmt3 = $pdo->prepare("SELECT sale_item.id, sale_item.quantity, sale_item.unit_price, sale_item.subtotal, sale_item.tax, sale_item.tax_spread, product.product_title, product.sku, product.tax_group, tax_group.tax_group_title FROM sale_item INNER JOIN product ON sale_item.product = product.id INNER JOIN tax_group ON product.tax_group WHERE sale_item.sale = :si");
        $stmt3->bindParam(':si', $arr['id']);
        $stmt3->execute();
        while($obj3 = $stmt3->fetch(PDO::FETCH_OBJ)){
          $arr3 = array();
          $arr3['id'] = $obj3->id;
          $arr3['quantity'] = $obj3->quantity;
          $arr3['unit_price'] = Money::ofMinor($obj3->unit_price, $this->currency_code)->getAmount();
          $arr3['subtotal'] = Money::ofMinor($obj3->subtotal, $this->currency_code)->getAmount();
          $arr3['tax'] = Money::ofMinor($obj3->tax, $this->currency_code)->getAmount();
          $total = $obj3->subtotal + $obj3->tax;
          $arr3['total'] = Money::ofMinor($total, $this->currency_code)->getAmount();
          $arr3['tax_spread'] = json_decode($obj3->tax_spread);
          $arr3['product'] = $obj3->product_title;
          $arr3['sku'] = $obj3->sku;
          $arr3['tax_group'] = $obj3->tax_group_title;
          array_push($arr['items'], $arr3);
        }
      }
      array_push($this->sales, $arr);
    }
  }

  public function get_id(){
    return $this->id;
  }
  
  public function to_array(){
    $arr = array();
    $arr['id'] = $this->id;
    $arr['customer'] = $this->customer;
    $arr['sales'] = $this->sales;
    $arr['currency_code'] = $this->currency_code;
    return $arr;
  }

  //  PRIVATE FUNCTIONS

  private function get_currency_code() {
    $pdo = DataConnector::get_connection();
    $stmt=$pdo->prepare("SELECT option_value FROM options WHERE option_name = 'currency_code'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    $this->currency_code = $result->option_value;
  }
}
