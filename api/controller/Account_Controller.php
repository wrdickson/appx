<?php

Class Account_Controller {
  public function foo ( $f3) {
    print 'hello';
  }

  public function get_accounts_pagination ( $f3 ) {
    $perms = ['permission' => 7, 'role' => 'edit_accounts'];
    $f3auth = F3Auth::authorize_token( $f3, $perms);
  
    $account = $f3auth['decoded']->account;
    $params = json_decode($f3->get('BODY'), true);

    $response['account'] = $account;
    $response['params'] = $params;

    $pdo = DataConnector::get_connection();

    //  first get total rows
    $stmt_count = $pdo->prepare("SELECT id FROM accounts");
    $stmt_count->execute();
    $row_count = $stmt_count->rowCount();
    $response['row_count'] = $row_count;
    
    $stmt = $pdo->prepare("SELECT * FROM ACCOUNTS ORDER BY username ASC LIMIT :offset, :limit");
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

  public function update_account ( $f3 ) {
    $perms = ['permission' => 7, 'role' => 'edit_accounts'];
    $f3auth = F3Auth::authorize_token( $f3, $perms);
  
    $account = $f3auth['decoded']->account;
    $params = json_decode($f3->get('BODY'), true);

    $response['account'] = $account;
    $response['params'] = $params;


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
      )
    );

    $test_values = $params;

    $v = new Validate( $test_values, $options);
    
    $response['valid'] = $v->validate();

    




    print json_encode($response);
  }
}
