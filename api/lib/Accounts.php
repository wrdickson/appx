<?php

class Accounts {

  public static function create_account( $username, $password, $permission, $roles, $email, $is_active ){
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $jRoles = json_encode($roles);
    //  initially, we won't set this
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare('INSERT INTO accounts (username, email, permission, roles, registered, last_activity, last_login, is_active, password) VALUES (:u, :e, :p, :ro, NOW(), NOW(), NOW(),:ia, :pwd)');
    $stmt->bindParam(':u', $username);
    $stmt->bindParam(':e', $email);
    $stmt->bindParam(':p', $permission);
    $stmt->bindParam(':ro', $jRoles);
    $stmt->bindParam(':pwd', $password_hash);
    $stmt->bindParam(':ia', $is_active);
    $execute = $stmt->execute();
    return $pdo->lastInsertId();
  }

  public static function get_all_accounts () {
    $response = array();
    $pdo = DataConnector::get_connection();
    $accountsArr = array();
    $stmt = $pdo->prepare('SELECT * FROM accounts ORDER BY username ASC');
    $stmt->execute();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
      $iArr = array();
      $iArr['id'] = $obj->id;
      $iArr['username'] = $obj->username;
      $iArr['email'] = $obj->email;
      $iArr['permission'] = $obj->permission;
      $iArr['roles'] = json_decode($obj->roles, true);
      $iArr['registered'] = $obj->registered;
      $iArr['last_login'] = $obj->last_login;
      $iArr['last_activity'] = $obj->last_activity;
      $iArr['is_active'] = $obj->is_active;
      array_push($accountsArr, $iArr);
    }
    return $accountsArr;
  }

}
