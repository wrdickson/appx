<?php

Class Root_Space_Controller {

  public function create_root_space ( $f3 ) {
    $perms= [ 'permission' => 7, 'role' => 'edit_root_spaces' ];
    $f3auth = F3Auth::authorize_token( $f3, $perms );
    $account = $f3auth['decoded']->account;
    $params = json_decode($f3->get('BODY'));
  
    //  TODO validate params
  
    $response['create'] = RootSpaces::create_root_space($params->beds, $params->childOf, $params->displayOrder, $params->people, $params->showChildren, $params->spaceType, $params->title, $params->isActive);
    $root_spaces_pre_children = RootSpaces::get_root_spaces();
    $root_spaces_children_parents = array();
    foreach( $root_spaces_pre_children as $rspc ) {
      $rspc['children'] = RootSpaces::get_root_space_children($rspc['id']);
      $rspc['parents'] = RootSpaces::get_root_space_parents($rspc['id']);
      array_push($root_spaces_children_parents, $rspc);
    }
  
    $response['account'] = $account;
    $response['params'] = $params;
    $response['root_spaces_children_parents'] = $root_spaces_children_parents;
  
    print json_encode( $response );
  }

  public function get_root_spaces ( $f3 ) {
    
    $root_spaces_pre_children = RootSpaces::get_root_spaces();
    $root_spaces_children_parents = array();
    foreach( $root_spaces_pre_children as $rspc ) {
      $rspc['children'] = RootSpaces::get_root_space_children($rspc['id']);
      $rspc['parents'] = RootSpaces::get_root_space_parents($rspc['id']);
      array_push($root_spaces_children_parents, $rspc);
    }
    
    //$response['account'] = $account;
    //$response['params'] = $params;
    $response['root_spaces_pre_children'] = $root_spaces_pre_children;
    $response['root_spaces_children_parents'] = $root_spaces_children_parents;
    print json_encode($response);
  }

  public function delete_root_space ( $f3 ) {
    $perms= [ 'permission' => 7, 'role' => 'edit_root_spaces' ];
    $f3auth = F3Auth::authorize_token( $f3, $perms );
    $account = $f3auth['decoded']->account;
    $params = json_decode($f3->get('BODY'));
  
    $response['account'] = $account;
    $response['params'] = $params;
  
    $response['delete'] = RootSpaces::delete_root_space($params->root_space_id);
  
    $root_spaces_pre_children = RootSpaces::get_root_spaces();
    $response['root_spaces_pre_children'] = $root_spaces_pre_children;
    $root_spaces_children_parents = array();
    foreach( $root_spaces_pre_children as $rspc ) {
      $rspc['children'] = RootSpaces::get_root_space_children($rspc['id']);
      $rspc['parents'] = RootSpaces::get_root_space_parents($rspc['id']);
      array_push($root_spaces_children_parents, $rspc);
    }
    $response['root_spaces_children_parents'] = $root_spaces_children_parents;
    //  2. bump all reservations with this space_code to unassigned
    //      make sure to fix is_assigned and space_type_pref, too
  
    //  1. recalculate all space_codes on reservations
  
    print json_encode( $response );
  }

  public function update_root_space ( $f3 ) {
    $perms = [ 'permission' => 7, 'role' => 'edit_root_spaces' ];
    $f3auth = F3Auth::authorize_token( $f3, $perms );
    $account = $f3auth['decoded']->account;
    $params = json_decode($f3->get('BODY'));
    $root_space_id = $f3->get('PARAMS["root_space_id"]');
    $u_root_space = $params->updateSpace;
    $response['account'] = $account;
    $response['params'] = $params;
    $response['root_space_id'] = $root_space_id;
    $response['u_root_space'] = $u_root_space;
    $response['titlex'] = $u_root_space->title;
  
    $response['execute'] = RootSpaces::update_root_space($root_space_id, $u_root_space->title, $u_root_space->childOf, $u_root_space->displayOrder, $u_root_space->showChildren, $u_root_space->spaceType, $u_root_space->people, $u_root_space->beds, $u_root_space->isActive);
  
    $rootSpacesPreChildren = RootSpaces::get_root_spaces();
    $rootSpacesWithChildrenAndParents = array();
    foreach( $rootSpacesPreChildren as $rspc ) {
      $rspc['children'] = RootSpaces::get_root_space_children($rspc['id']);
      $rspc['parents'] = RootSpaces::get_root_space_parents($rspc['id']);
      array_push($rootSpacesWithChildrenAndParents, $rspc);
    }
    $response['root_spaces_children_parents'] = $rootSpacesWithChildrenAndParents;
  
    print json_encode($response);
  }
    
  }

