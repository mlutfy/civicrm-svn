<?php 

function relationship_type_create_example(){
    $params = array(
    
                  'name_a_b' 		=> 'Relation 1 for relationship type create',
                  'name_b_a' 		=> 'Relation 2 for relationship type create',
                  'contact_type_a' 		=> 'Individual',
                  'contact_type_b' 		=> 'Organization',
                  'is_reserved' 		=> '1',
                  'is_active' 		=> '1',
                  'version' 		=> '3',
                  'sequential' 		=> '1',
                  'label_a_b' 		=> 'Relation 1 for relationship type create',
                  'label_b_a' 		=> 'Relation 2 for relationship type create',

  );
  require_once 'api/api.php';
  $result = civicrm_api( 'relationship_type','create',$params );

  return $result;
}

/*
 * Function returns array of result expected from previous function
 */
function relationship_type_create_expectedresult(){

  $expectedResult = 
     array(
           'is_error' 		=> '0',
           'version' 		=> '3',
           'count' 		=> '12',
           'id' 		=> '10',
           'values' 		=>            array(           'id' => '10',                      'name_a_b' => 'Relation 1 for relationship type create',                      'label_a_b' => 'Relation 1 for relationship type create',                      'name_b_a' => 'Relation 2 for relationship type create',                      'label_b_a' => 'Relation 2 for relationship type create',                      'description' => '',                      'contact_type_a' => 'Individual',                      'contact_type_b' => 'Organization',                      'contact_sub_type_a' => '',                      'contact_sub_type_b' => '',                      'is_reserved' => '1',                      'is_active' => '1',           ),
      );

  return $expectedResult  ;
}

