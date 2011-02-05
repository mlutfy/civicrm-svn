<?php 

function activity_type_get_example(){
    $params = array(
    
                  'version' 		=> '3',

  );
  require_once 'api/api.php';
  $result = civicrm_api( 'civicrm_activity_type_get','ActivityType',$params );

  return $result;
}

/*
 * Function returns array of result expected from previous function
 */
function activity_type_get_expectedresult(){

  $expectedResult = 
            array(
                  '1' 		=> 'Meeting',
                  '2' 		=> 'Phone Call',
                  '3' 		=> 'Email',
                  '4' 		=> 'Text Message (SMS)',
                  '5' 		=> 'Event Registration',
                  '6' 		=> 'Contribution',
                  '7' 		=> 'Membership Signup',
                  '8' 		=> 'Membership Renewal',
                  '9' 		=> 'Tell a Friend',
                  '10' 		=> 'Pledge Acknowledgment',
                  '11' 		=> 'Pledge Reminder',
                  '12' 		=> 'Inbound Email',
                  '13' 		=> 'Open Case',
                  '14' 		=> 'Follow up',
                  '15' 		=> 'Change Case Type',
                  '16' 		=> 'Change Case Status',
                  '17' 		=> 'Membership Renewal Reminder',
                  '18' 		=> 'Change Case Start Date',
                  '19' 		=> 'Bulk Email',
                  '20' 		=> 'Assign Case Role',
                  '21' 		=> 'Remove Case Role',
                  '22' 		=> 'Print PDF Letter',
                  '23' 		=> 'Merge Case',
                  '24' 		=> 'Reassigned Case',
                  '25' 		=> 'Link Cases',
                  '26' 		=> 'Change Case Tags',
                  '27' 		=> 'Add Client To Case',
                  '28' 		=> 'Survey',
                  '29' 		=> 'Canvass',
                  '30' 		=> 'PhoneBank',
                  '31' 		=> 'WalkList',
                  '32' 		=> 'Petition',

  );

  return $expectedResult  ;
}

