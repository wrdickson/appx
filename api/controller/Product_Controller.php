<?php

use Brick\Money\Money;

Class Product_Controller {

  //  public functions

  public function search_products_by_sku ( $f3 ) {

    //  get currency units
    $pdo = DataConnector::get_connection();
    $stmt=$pdo->prepare("SELECT option_value FROM options WHERE option_name = 'currency_code'");
    $stmt->execute();
    $result = $stmt->fetch(PDO::FETCH_OBJ);
    $currency_code = $result->option_value;

    //  instantiate an array that will hold all applicable tax types
    $applicable_tax_types = array();

    //  set the permission level on this action
    $perms = [ 'permission' => 3, 'role' => 'search_products' ];
    //  throws an error and stops execution if auth fails
    $this->validate_account( $f3, $perms );

    //  create response array
    $response = array();

    //  get params
    $params = json_decode($f3->get('BODY'));
    $response['params'] = $params;
    $response['search_term'] = $params->searchTerm;
    $search_term = $params->searchTerm;
    $offset = $params->offset;
    $limit = $params->pageSize;

    $pdo = DataConnector::get_connection();
    $stmt = $pdo->prepare("SELECT product.id, product.product_title, product.sku, product.price, product.tax_group, product.is_active, tax_group.tax_types FROM product INNER JOIN tax_group ON tax_group.id = product.tax_group WHERE product.sku LIKE :search ORDER BY product.product_title ASC LIMIT :limit OFFSET :offset ");
    $stmt->execute([
      ':search' => $search_term . '%',
      ':limit' => $limit,
      ':offset' => $offset
    ]);
    $arr = array();
    while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
      $iArr = array();
      $iArr['id'] = $obj->id;
      $iArr['product_title'] = $obj->product_title;
      $iArr['sku'] = $obj->sku;
      $iArr['price'] = Money::ofMinor($obj->price, $currency_code)->getAmount();
      $iArr['tax_group'] = $obj->tax_group;
      $iArr['tax_types'] = json_decode($obj->tax_types);
      $iArr['is_active'] = $obj->is_active;

      //  keep the applicable tax_types array current
      foreach ($iArr['tax_types'] as $tax_type) {
        if( !in_array($tax_type, $applicable_tax_types) ) {
          array_push($applicable_tax_types, $tax_type);
        }
      }

      array_push($arr, $iArr);
    }
    $response['products'] = $arr;

    //  get the total row count for pagination
    $stmt = $pdo->prepare("SELECT id FROM product WHERE sku LIKE :search");
    $stmt->execute([
      ':search' => $search_term . '%'
    ]);
    $count = $stmt->rowCount();
    $response['rowCount'] = $count;

    //  add applicable tax types
    $response['applicable_tax_types'] = $applicable_tax_types;
    //  build an array-ish string for the query: we're looking for "(1,6,7)" format
    $arr_str = "";
    $arr_str .="(";
    foreach( $applicable_tax_types as $tt) {
      $arr_str .= $tt;
      $arr_str .= ",";
    }
    //  strip the last comma
    $arr_str = rtrim($arr_str, ","); 
    $arr_str .= ")";
    //$response['arr_str'] = $arr_str;
    //  don't do anything if we don't have any results
    if( count($applicable_tax_types) > 0 ){
      $sql = "SELECT * FROM tax_type WHERE id IN " . $arr_str . ";";
      $stmt = $pdo->prepare($sql);
      $stmt->execute();
      $response['tax_types'] = array();
      $arr = array();
      while($obj = $stmt->fetch(PDO::FETCH_OBJ)){
        $arr['id'] = $obj->id;
        $arr['tax_title'] = $obj->tax_title;
        $arr['rate'] = $obj->rate;
        $arr['is_active'] = $obj->is_active;
        $response['tax_types'][$obj->id] = $arr;
      }
    } else {
      //  empty array
      $response['tax_types'] = array();
    }

    print json_encode($response);
  }

  //  private functions
  private function validate_account ( $f3, $perms ) {
    //  the request should have 'Jwt' ('jwt' works!!) property in header with user's token
    //  this throws an error if the token doesn't work OR user doesn't have permission
    $f3auth = F3Auth::authorize_token( $f3, $perms );
  }
}