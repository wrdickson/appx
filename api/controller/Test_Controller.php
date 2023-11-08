<?php

Class Test_Controller {

  public function index ( $f3 ) {
    
    $test = new DB\SQL\Mapper($f3->get('DB'),'options');
    $all_test = $test->find();

    $response = array();
    foreach( $all_test as $i_test ) {
      array_push($response, $i_test->cast());
    }
    print json_encode($response);
  }

  public function do_something ( $f3 ) {
    print 'do_something';
  }

}