<?php

Class Folio_Controller {

  public function get_folio( $f3 ) {
    $perms = ['permission'=> 1, 'role'=>'void'];
    $r = F3Auth::authorize_token( $f3, $perms );
    
    $folio = new Folio($f3->get('PARAMS.id'));
    $response['folio'] = $folio->to_array();
    $customer = new Customer($folio->to_array()['customer']);
    $response['customer'] =  $customer->to_array();



    print json_encode($response);
  }

}