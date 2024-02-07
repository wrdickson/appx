<?php

Class Folio_Controller {

  public function get_folio( $f3 ) {
    $perms = ['permission'=> 1, 'role'=>'void'];
    $r = F3Auth::authorize_token( $f3, $perms );
    $folio = new Folio($f3->get('PARAMS.id'));
    $response['folio'] = $folio->to_array();
    $customer = new Customer($folio->to_array()['customer']);
    $response['customer'] =  $customer->to_array();
    $response['active_payment_types'] = Payment_Types::get_active_payment_types();
    print json_encode($response);
  }

  public function post_folio_sale( $f3 ) {
    $perms = ['permission'=> 4, 'role'=>'void'];
    $auth = F3Auth::authorize_token( $f3, $perms );
    $params = json_decode($f3->get('BODY'));

    $reposnse = array();
    $response['auth'] = $auth;
    $response['items'] = $params->items;
    $response['payments']= $params->payments;
    $response['folio'] = $params->folio;

    print json_encode($response);
  }

}