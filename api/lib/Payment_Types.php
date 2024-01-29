<?php

Class Payment_Types {

  public static function get_active_payment_types () {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT * FROM payment_type WHERE is_active = 1");
    $stmt->execute();
    $active_payment_types = array();
    while( $obj = $stmt->fetchObject() ){
      $i = array();
      $i['id'] = $obj->id;
      $i['payment_title'] = $obj->payment_title;
      $i['is_active'] = $obj->is_active;
      array_push( $active_payment_types, $i );
    }
    return $active_payment_types;
  }

}