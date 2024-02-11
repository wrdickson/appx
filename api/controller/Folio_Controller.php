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
    $response['sold_by'] = $params->sold_by;

    $folio_id = $params->folio->id;
    $sold_by = $params->sold_by->id;
    $items = $params->items;
    $payments = $params->payments;

    //  instantiate folio
    $folio = new Folio($folio_id);
    $response['initial_folio'] = $folio->to_array();

    /*  Validate:
    *     a. folio id, customer matches db
    *     b. sold_by (user) data matches db
    *     c. item:  id matches sku and description[?]
                    tax_types is corredt
    *               unit price and qty are integers
    *               tax, tax_spread, subtotal, and total calculations are correct
    *     d. payments:  payment types match db
    *                   totals are integers
    *                   total equals total of items
    */

    //  use folio object to post sale and regenerate totals
    $response['post_sale'] = $folio->post_folio_sale( $sold_by, $items, $payments );


    print json_encode($response);
  }

}