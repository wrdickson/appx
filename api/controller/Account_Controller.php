<?php

Class Account_Controller {

  public function create_account ( $f3 ) {
    $perms = ['permission' => 7, 'role' => 'edit_accounts'];
    $f3auth = F3Auth::authorize_token( $f3, $perms);
    $account = $f3auth['decoded']->account;
    $params = json_decode($f3->get('BODY'), true);
    //  validate
    $options = array (
      'email' => array (
        'is_email'
      ),
      'is_active' => array (
        'is_0_or_1'
      ),
      'password' => array(
        'is_length, 6, 36'
      ),
      'permission' => array (
        'is_integer',
        'is_between, 0, 12'
      ),
      'username' => array (
        'is_length, 4, 24'
      )
    );
    $validator = new Validate( $params, $options);
    $v = $validator->validate();
    if( $v['valid'] ) {
      //  proceed
      $newAccountId = Accounts::create_account($params['username'], $params['password'], $params['permission'], $params['roles'], $params['email'], $params['is_active']);
      $response['new_account_id'] = $newAccountId;
      $account = new Account($newAccountId);
      $response['new_account'] = $account->to_array();
    } else {
      $f3->error(400, 'invalid update parameters');
    }
    print json_encode($response);
  }

  public function get_accounts_pagination ( $f3 ) {
    $perms = ['permission' => 7, 'role' => 'edit_accounts'];
    $f3auth = F3Auth::authorize_token( $f3, $perms);
    $account = $f3auth['decoded']->account;
    $params = json_decode($f3->get('BODY'), true);
    $pdo = DataConnector::get_connection();
    //  first get total rows
    $stmt_count = $pdo->prepare("SELECT id FROM accounts");
    $stmt_count->execute();
    $row_count = $stmt_count->rowCount();
    $response['row_count'] = $row_count;
    $stmt = $pdo->prepare("SELECT * FROM accounts ORDER BY username ASC LIMIT :offset, :limit");
    $stmt->execute([
      ':offset' => $params['offset'],
      ':limit' => $params['limit']
    ]);
    $arr = array();
    while( $obj = $stmt->fetch(PDO::FETCH_OBJ)) {
      $i = array();
      $i['id'] = $obj->id;
      $i['username'] = $obj->username;
      $i['email'] = $obj->email;
      $i['permission'] = $obj->permission;
      $i['roles'] = json_decode($obj->roles);
      $i['is_active'] = $obj->is_active;
      array_push($arr, $i);
    }
    $response['accounts'] = $arr;
    print json_encode($response);
  }
  //  this one does NOT allow us to change username or password
  public function update_account ( $f3 ) {
    $perms = ['permission' => 7, 'role' => 'edit_accounts'];
    $f3auth = F3Auth::authorize_token( $f3, $perms);
    $params = json_decode($f3->get('BODY'), true);
    $options = array (
      'id' => array (
        'is_integer'
      ),
      'email' => array (
        'is_email'
      ),
      'is_active' => array (
        'is_0_or_1'
      ),
      'permission' => array (
        'is_integer',
        'is_between, 0, 12'
      ),
      'username' => array (
        'is_length, 4, 24'
      )
    );
    $v = new Validate( $params, $options);
    $valid_result = $v->validate();
    if($valid_result['valid'] == 1){
      //  update account
      $account = new Account($params['id']);
      $response['orig'] = $account->to_array();
      if($params['email'] !== $account->get_email() ) {
        $response['set_email'] = $account->set_email($params['email']);
      }
      if($params['is_active'] !== $account->get_is_active()) {
        $response['set_is_active'] = $account->set_is_active($params['is_active']);
      }
      if($params['permission'] !== $account->get_permission()) {
        $response['set_permission'] = $account->set_permission($params['permission']);
      }
      if($params['roles'] !== $account->get_roles() ) {
        $response['set_roles'] = $account->set_roles(json_encode($params['roles']));
      }
    } else {
      $f3->error(400, 'invalid update parameters');
    }
    $final = new Account($params['id']);
    $response['updated_account'] = $final->to_array();
    print json_encode($response);
  }

  public function update_password( $f3 ) {
    $perms = ['permission' => 7, 'role' => 'edit_accounts'];
    $f3auth = F3Auth::authorize_token( $f3, $perms);
    $params = json_decode($f3->get('BODY'), true);
    $response['params'] = $params;
    $options = array (
      'id' => array (
        'is_integer'
      ),
      'pwd' => array (
        'is_length, 4, 24'
      )
    );
    $v = new Validate( $params, $options);
    $valid_result = $v->validate();
    if($valid_result['valid'] == 1){
      $account = new Account($params['id']);
      $response['update'] = $account->set_password( $params['pwd']);
      
    } else {
      $f3->error(400, 'invalid update parameters');
    }
  }
}
