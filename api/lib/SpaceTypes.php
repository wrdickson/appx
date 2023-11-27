<?php

class SpaceTypes {

  public static function get_space_types () {

    $pdo = DataConnector::get_connection();
  
    $stmt = $pdo->prepare("SELECT * FROM space_types WHERE is_unassigned = 0");
    $execute= $stmt->execute();
    $arr = array();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
        $iArr = array();
        $iArr['id'] = $obj->id;
        $iArr['title'] = $obj->title;
        $iArr['is_active'] = $obj->is_active;
        $iArr['display_order'] = $obj->display_order;
        array_push($arr, $iArr);
    };
    return $arr;
  }

  public static function create_space_type ( $title, $is_active, $display_order ) {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("INSERT INTO space_types ( title, is_active, display_order, is_unassigned ) VALUES ( :t, :ia, :do, 0 )");
    $stmt->bindParam(':t', $title);
    $stmt->bindParam(':ia', $is_active);
    $stmt->bindParam(':do', $display_order);
    return $stmt->execute();
  }

  public static function update_space_type ( $id, $title, $display_order, $is_active ) {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("UPDATE space_types SET title = :t, is_active = :ia, display_order = :do WHERE id = :id ");
    $stmt->bindParam(':t', $title);
    $stmt->bindParam(':ia', $is_active);
    $stmt->bindParam(':do', $display_order);
    $stmt->bindParam(':id', $id); 
    return $stmt->execute();
  }

  public static function delete_space_type ( $id ) {
    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("DELETE FROM space_types WHERE id = :id ");
    $stmt->bindParam(':id', $id); 
    return $stmt->execute();
  }

}
