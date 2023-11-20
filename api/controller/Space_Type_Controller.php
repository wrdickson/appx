<?php

Class Space_Type_Controller {

  public function get_space_types ( $f3 ) {
    $perms = [ 'permission' => 1, 'role' => 'get_root_spaces' ];
    //  the request should have 'Jwt' property in header with user's token
    //  this throws an error if the token doesn't work OR user doesn't have permission
    $f3auth = F3Auth::authorize_token( $f3, $perms );
    
    $account = $f3auth['decoded']->account;
    $params = json_decode($f3->get('BODY'));

    $response = array();
    //$response['account'] = $account;
    $response['space_types'] = SpaceTypes::get_space_types();

    print json_encode( $response );
  }

}