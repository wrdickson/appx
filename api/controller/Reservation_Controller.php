<?php

class Reservation_Controller {
  public function check_availability_by_dates ( $f3 ) {
    $perms = [ 'permission' => 0, 'role' => 'get_availability' ];
    //  the request should have 'Jwt' property in header with user's token
    //  this throws an error if the token doesn't work OR user doesn't have permission
    $f3auth = F3Auth::authorize_token( $f3, $perms );
  
    $account = $f3auth['decoded']->account;
    $params = json_decode($f3->get('BODY'));
  
    $response['account'] = $account;
    $response['params'] = $params;
    $response['availability'] = Reservations::check_availability_by_dates( $params->startDate, $params->endDate );
    print json_encode($response);
  }

  public function create_reservation ( $f3 ) {
    $perms = ['permission' => 2, 'role' => 'create_reservation' ];
    $f3auth = F3Auth::authorize_token( $f3, $perms);
  
    $account = $f3auth['decoded']->account;
    $params = json_decode($f3->get('BODY'), true);
    //  TODO . . . recheck that it's available
  
    $response['create'] = Reservations::create_reservation($account->id, $params['customer']['id'], $params['checkin'], $params['checkout'], $params['space_id'], $params['beds'], $params['people'], $params['is_assigned'], $params['space_type_pref'] );
    
    if( $response['create']['new_res_id'] ) {
      $response['nri'] = $response['create']['new_res_id'];
      $iRes = new Reservation($response['create']['new_res_id']);
      $response['history_added'] = $iRes->add_history("Created", $account->id, $account->username);
    }
    
    $response['account'] = $account;
    $response['params'] = $params;
    print json_encode($response);
  }
}
