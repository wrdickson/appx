<?php

Class Product_Controller {

  //  public functions

  public function search_products_by_sku ( $f3 ) {
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
      $iArr['price'] = $obj->price;
      $iArr['tax_group'] = $obj->tax_group;
      $iArr['is_active'] = $obj->is_active;

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

    print json_encode($response);
  }

  //  private functions
  private function validate_account ( $f3, $perms ) {
    //  the request should have 'Jwt' ('jwt' works!!) property in header with user's token
    //  this throws an error if the token doesn't work OR user doesn't have permission
    $f3auth = F3Auth::authorize_token( $f3, $perms );
  }
}