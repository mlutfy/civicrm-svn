<?php 

function uf_field_create_example(){
    $params = array(
    
                  'field_name' 		=> 'country',
                  'field_type' 		=> 'Contact',
                  'visibility' 		=> 'Public Pages and Listings',
                  'weight' 		=> '1',
                  'label' 		=> 'Test Country',
                  'is_searchable' 		=> '1',
                  'is_active' 		=> '1',
                  'version' 		=> '3',

  );
  require_once 'api/api.php';
  $result = civicrm_api( 'civicrm_uf_field_create','UFField',$params );

  return $result;
}

/*
 * Function returns array of result expected from previous function
 */
function uf_field_create_expectedresult(){

  $expectedResult = 
            array(
                  'id' 		=> '35',
                  'uf_group_id' 		=> '11',
                  'field_name' 		=> 'country',
                  'is_active' 		=> '1',
                  'is_view' 		=> '',
                  'is_required' 		=> '',
                  'weight' 		=> '1',
                  'help_post' 		=> '',
                  'help_pre' 		=> '',
                  'visibility' 		=> 'Public Pages and Listings',
                  'in_selector' 		=> '',
                  'is_searchable' 		=> '1',
                  'location_type_id' 		=> 'null',
                  'phone_type_id' 		=> '',
                  'label' 		=> 'Test Country',
                  'field_type' 		=> 'Contact',
                  'is_reserved' 		=> '',

  );

  return $expectedResult  ;
}

