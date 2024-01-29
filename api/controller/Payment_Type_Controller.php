<?php

Class Payment_Type_Controller {

  public function get_active_payment_types ( $f3 ) {
    $perms = [ 'permission' => 2, 'role' => 'create_customer' ];
    //  the request should have 'jwt' property in header with user's token
    //  this throws an error if the token doesn't work OR user doesn't have permission
    $f3auth = F3Auth::authorize_token( $f3, $perms );

    print json_encode(Payment_Types::get_active_payment_types());
  
  }
}