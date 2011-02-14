<?php 

function activity_delete_example(){
    $params = array(
    
                  'id' 		=> '13',
                  'activity_type_id' 		=> '1',
                  'version' 		=> '3',

  );
  require_once 'api/api.php';
  $result = civicrm_api_legacy( 'civicrm_activity_delete','Activity',$params );

  return $result;
}

/*
 * Function returns array of result expected from previous function
 */
function activity_delete_expectedresult(){

  $expectedResult = 
            array(
                  'is_error' 		=> '0',
                  'version' 		=> '3',
                  'count' 		=> '1',
                  'values' 		=> '1',

  );

  return $expectedResult  ;
}

