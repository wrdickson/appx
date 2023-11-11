<?php

Class Options_Controller {

  public function get_autoload_options ( $f3 ) {
    $response = array();
    //  customer
    $st = new DB\SQL\Mapper($f3->get('DB'),'options');
    $autoload_options = $st->find(array('autoload = ?', '1'));
    $response['options'] = array();
    foreach($autoload_options as $option){
      array_push( $response['options'], $option->cast() );
    }
    print json_encode($response);
  }
}