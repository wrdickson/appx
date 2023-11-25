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

  public function check_availability_by_dates_ignore_res ( $f3 ) {
    $perms = [ 'permission' => 0, 'role' => 'get_availability' ];
    //  the request should have 'Jwt' property in header with user's token
    //  this throws an error if the token doesn't work OR user doesn't have permission
    $f3auth = F3Auth::authorize_token( $f3, $perms );
  
    $account = $f3auth['decoded']->account;
    $params = json_decode($f3->get('BODY'));
    //  TODO validate dates??

    $response['account'] = $account;
    $response['params'] = $params;
    $response['availableSpaceIds'] = Reservations::check_availability_by_dates_ignore_res($params->start_date, $params->end_date, $params->res_id);
    print json_encode($response);
  }

  public function create_reservation ( $f3 ) {
    $perms = ['permission' => 2, 'role' => 'create_reservation' ];
    $f3auth = F3Auth::authorize_token( $f3, $perms);
  
    $account = $f3auth['decoded']->account;
    $params = json_decode($f3->get('BODY'), true);
    //  TODO . . . recheck that it's available
  
    /*
    * DEBUG DEBUG DEBUG
    */
    $params['space_type_pref'] = 4;

    $response['create'] = Reservations::create_reservation($account->id, $params['customer']['id'], $params['checkin'], $params['checkout'], $params['space_id'], $params['beds'], $params['people'], $params['is_assigned'], $params['space_type_pref'] );
    
    if( $response && $response['create']['new_res_id'] ) {
      $response['nri'] = $response['create']['new_res_id'];
      $iRes = new Reservation($response['create']['new_res_id']);
      $response['history_added'] = $iRes->add_history("Created", $account->id, $account->username);
    }
    
    $response['account'] = $account;
    $response['params'] = $params;
    print json_encode($response);
  }

  public function get_reservations_by_range ( $f3) {
    $perms = [ 'permission' => 1, 'role' => 'get_reservations' ];
    //  the request should have 'Jwt' property in header with user's token
    //  this throws an error if the token doesn't work OR user doesn't have permission
    $f3auth = F3Auth::authorize_token( $f3, $perms );
    
    $account = $f3auth['decoded']->account;
    $params = json_decode($f3->get('BODY'));
  
    //  TODO validate dates??
  
    $response['account'] = $account;
    $response['params'] = $params;
    $response['reservations'] = Reservations::get_reservations_date_range($params->startDate, $params->endDate);
  
    print json_encode($response);
  }

  public function update_reservation_1 ( $f3 ) {
    $perms = [ 'permission' => 3, 'role' => 'modify_reservations' ];
    //  the request should have 'Jwt' property in header with user's token
    //  this throws an error if the token doesn't work OR user doesn't have permission
    $f3auth = F3Auth::authorize_token( $f3, $perms );
  
    $account = $f3auth['decoded']->account;
    $params = json_decode($f3->get('BODY'));
    $resObj = $params->res_obj;
  
    $response['auth'] = $f3auth;
    $response['params'] = $params;
  
    //  TODO validate params
    
    //  create local param copies
    $checkin = $resObj->checkin;
    $checkout = $resObj->checkout;
    $people = $resObj->people;
    $beds = $resObj->beds;
    $space_id = $resObj->space_id;
    $res_id = $resObj->res_id;
    $is_assigned = $resObj->is_assigned;
    $space_type_pref = $resObj->space_type_pref;
  
    $iRes = new Reservation($res_id);
    $response['rid'] = $res_id;
    $iResArray = $iRes->to_array();
    $response['origResArray'] = $iResArray;
  
    $response['error'] = array();
    $response['success'] = true;
  
    //  go through the params and see if the properties have changed and
    //  do the updates. we are only handling changes of checkin, checkout,  
    //  beds, people, space_id, is_assigned, and space_type_pref with this function
    //  beds
    //  TODO this really should be done as a transaction that can be
    //  unwound if there is an error
  
    /**
     * CHECKIN
     */
    if( $checkin != $iRes->get_checkin() ) {
      //  make sure this reservation is available
      $is_available = Reservations::check_conflicts_ignore_res( $checkin, $iRes->get_checkout(), $space_id, $iRes->get_id() );
      $response['ci change available'] = $is_available;
      if( $is_available ) {
        $response['set_checkin'] = $iRes->set_checkin( $checkin );
      } else {
        array_push( $response['error'], 'checkin change violates availability' );
        $response['success'] = false;
      }
    }
  
    /**
     * CHECKOUT
     */
    if( $checkout != $iRes->get_checkout() ) {
      //  make sure this reservation is available
      $is_available = Reservations::check_conflicts_ignore_res( $iRes->get_checkin(), $checkout, $space_id, $iRes->get_id() );
      $response['co change available'] = $is_available;
      if( $is_available ) {
        $response['set_checkout'] = $iRes->set_checkout( $checkout );
      } else {
        array_push( $response['error'], 'checkout change violates availability' );
        $response['success'] = false;
      }
    }
  
    /**
     * BEDS
     */
    if( $beds != $iRes->get_beds() ){
      $response['set_beds'] = $iRes->set_beds( $beds );
    }
  
    /**
     * PEOPLE
     */
    if( $people != $iRes->get_people() ) {
      $response['set_people'] = $iRes->set_people( $people );
    }
  
    /**
     * SPACE_ID
     */
    if( $space_id != $iRes->get_space_id() ) {
      //  if we change the space_id, we also have to change space_code
      //  BUT the setter handles this 
      // make sure this reservation is available
      $is_available = Reservations::check_conflicts_ignore_res( $checkin, $checkout, $space_id, $iRes->get_id() );
      //$is_available = true;
      $response['space_id change available'] = $is_available;
      if( $is_available ) {
        $response['set_space_id'] = $iRes->set_space_id( $space_id );
      } else {
        array_push( $response['error'], 'space_id change not available' );
        $response['success'] = false;
      }
    }
  
    /**
     * IS_ASSIGNED
     */
    if( $is_assigned != $iRes->get_is_assigned() ) {
      $response['set_is_assigned'] = $iRes->set_is_assigned( $is_assigned );
    }
  
    /**
     * SPACE_TYPE_PREF
     */
    if( $space_type_pref != $iRes->get_space_type_pref() ) {
      /*
      *  OVERRIDE TEMP DEBUG
      */
      $space_type_pref = 4;
      $response['set_space_type_pref'] = $iRes->set_space_type_pref( $space_type_pref );
    }
  
    // add history
    $response['add_history'] = $iRes->add_history("Modified", $account->id,  $account->username);
  
    // no matter what happened, return the res as it is now
    $jRes = new Reservation($res_id);
    $response['current_res'] = $jRes->to_array();
    print json_encode($response);
  }
}
