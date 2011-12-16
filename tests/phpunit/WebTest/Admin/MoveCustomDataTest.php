<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 4.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2011                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007 and the CiviCRM Licensing Exception.   |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the GNU Affero General Public License for more details.        |
 |                                                                    |
 | You should have received a copy of the GNU Affero General Public   |
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

require_once 'CiviTest/CiviSeleniumTestCase.php';


 
class WebTest_Admin_MoveCustomDataTest extends CiviSeleniumTestCase {

  protected function setUp()
  {
      parent::setUp();
  }

  function testCreateCustomFields() {
    // This is the path where our testing install resides. 
    // The rest of URL is defined in CiviSeleniumTestCase base class, in
    // class attributes.
    $this->open( $this->sboxPath );
    
    // Logging in. Remember to wait for page to load. In most cases,
    // you can rely on 30000 as the value that allows your test to pass, however,
    // sometimes your test might fail because of this. In such cases, it's better to pick one element
    // somewhere at the end of page and use waitForElementPresent on it - this assures you, that whole
    // page contents loaded and you can continue your test execution.
    $this->webtestLogin( );
   
    $cid_all = $this->_createContact( );
    $cid_from_missing  = $this->_createContact( );
    $cid_to_missing  = $this->_createContact( );
    
    $from_group_id = $this->_buildCustomFieldSet( "source" ); 
    $this->_fillCustomDataForContact( $cid_all, $from_group_id );
    $this->_fillCustomDataForContact( $cid_to_missing, $from_group_id );

    $to_group_id = $this->_buildCustomFieldSet( "destination" ); 
    $this->_fillCustomDataForContact( $cid_all, $to_group_id );
    $this->_fillCustomDataForContact( $cid_from_missing, $to_group_id );

    //to verify data hasn't been lost, we load the values for each contact
    $pre_move_values = array();
    $pre_move_values[$cid_all]['source']      = $this->_loadDataFromApi( $cid_all, $from_group_id );
    $pre_move_values[$cid_all]['destination'] = $this->_loadDataFromApi( $cid_all, $to_group_id );
    $pre_move_values[$cid_from_missing]['source']      = $this->_loadDataFromApi( $cid_from_missing, $from_group_id );
    $pre_move_values[$cid_from_missing]['destination'] = $this->_loadDataFromApi( $cid_from_missing, $to_group_id );
    $pre_move_values[$cid_to_missing]['source']      = $this->_loadDataFromApi( $cid_to_missing, $from_group_id );
    $pre_move_values[$cid_to_missing]['destination'] = $this->_loadDataFromApi( $cid_to_missing, $to_group_id );


    //ok, so after all that setup, we are now good to actually move a field
    
    //first, pick a random field from the source group to move
    $fields = $this->webtest_civicrm_api("CustomField", "get", array( 'custom_group_id' => $from_group_id ) );
    $field_to_move = array_rand($fields['values']);

    //move the field
    $this->_moveCustomField( $field_to_move, $from_group_id, $to_group_id );

    //now lets verify the data, load up the new values from the api...
    $post_move_values = array();
    $post_move_values[$cid_all]['source']      = $this->_loadDataFromApi( $cid_all, $from_group_id, true );
    $post_move_values[$cid_all]['destination'] = $this->_loadDataFromApi( $cid_all, $to_group_id );
    $post_move_values[$cid_from_missing]['source']      = $this->_loadDataFromApi( $cid_from_missing, $from_group_id );
    $post_move_values[$cid_from_missing]['destination'] = $this->_loadDataFromApi( $cid_from_missing, $to_group_id );
    $post_move_values[$cid_to_missing]['source']      = $this->_loadDataFromApi( $cid_to_missing, $from_group_id );
    $post_move_values[$cid_to_missing]['destination'] = $this->_loadDataFromApi( $cid_to_missing, $to_group_id );

    
    //Make sure that only the appropriate values have changed 
    foreach( array( $cid_all, $cid_from_missing, $cid_to_missing ) as $cid) {
      foreach(array('source', 'destination') as $fieldset) {
        foreach($pre_move_values[$cid][$fieldset] as $id => $value) {
          if( $id != $field_to_move ) {
            //All fields that were there should still be there
            $this->assertTrue(isset($post_move_values[$cid][$fieldset][$id]), "A custom field that was not moved is missing!");
            //All fields should have the same value as when we started
            $this->assertTrue($post_move_values[$cid][$fieldset][$id] == $value, "A custom field value has changed in the source custom field set");
          }
        } 
      }
      //check that the field is actually moved
      $this->assertTrue( ! isset ( $post_move_values[$cid]['source'][$field_to_move] ), "Moved field is still present in the source fieldset" );
      $this->assertTrue( isset ( $post_move_values[$cid]['destination'][$field_to_move] ), "Moved field is not present in the destination fieldset" );
      $this->assertTrue( $pre_move_values[$cid]['source'][$field_to_move] == $post_move_values[$cid]['destination'][$field_to_move] , "The moved field has changed values!" );
    }

    //Go to the contacts page and check that the custom field is in the right group
    $this->open($this->sboxPath . "/civicrm/contact/view?reset=1&cid=" . $cid_all);
    $this->waitForPageToLoad("30000");

    //load the names of the custom fieldsets
    $source = $this->webtest_civicrm_api("CustomGroup", "get", array( 'id' => $from_group_id ) );
    $source = $source['values'][$from_group_id];
    $destination = $this->webtest_civicrm_api("CustomGroup", "get", array( 'id' => $to_group_id ) );
    $destination = $destination['values'][$to_group_id];

    //assert that the moved custom field is missing from the source fieldset
    $this->assertElementNotContainsText("css=div." . $source['name'], $fields['values'][$field_to_move]['label'], "Moved value still displays in the old fieldset on the contact record");
    $this->assertElementContainsText("css=div." . $destination['name'], $fields['values'][$field_to_move]['label'], "Moved value does not display in the new fieldset on the contact record");
  }

  //moves a field from one field to another
  function _moveCustomField( $field_to_move, $from_group_id, $to_group_id ) {
    //go to the move field page
    $this->open($this->sboxPath . "civicrm/admin/custom/group/field/move?reset=1&fid=" . $field_to_move);
    $this->waitForPageToLoad("30000");

    //select the destination field set from select box
    $this->click("dst_group_id");
    $this->select("dst_group_id", "value=" . $to_group_id);
    $this->click("//option[@value='" . $to_group_id . "']");
    
    //click the save button
    $this->click("_qf_MoveField_next");
    $this->waitForPageToLoad("30000");
    
    //asser that the success text is present
    $this->assertTrue($this->isTextPresent("has been moved"), "Move field success message not displayed");

    //assert that the custom field not on old data set page
    $this->assertTrue( ! $this->isElementPresent("row_" . $field_to_move), "The moved custom field still displays on the old fieldset page");

    //go to the destination fieldset and make sure the field is present
    $this->open($this->sboxPath . "civicrm/admin/custom/group/field?reset=1&action=browse&gid=" . $to_group_id);
    $this->waitForPageToLoad("30000");
    $this->assertTrue( $this->isElementPresent("row_" . $field_to_move), "The moved custom field does not display on the new fieldset page");

  }

  //create a contact and return the contact id
  function _createContact( ) {
    //dont care about the contacts info, so defaults are ok
    $this->webtestAddContact( );
    $url = $this->parseURL( );
    $cid = $url['queryString']['cid'];
    $this->assertType('numeric', $cid);
    return $cid;
  }

  //Get all custom field values for a given contact and custom group id using the api
  function _loadDataFromApi( $contact_id, $group_id, $reset_cache = false ) {
    static $field_ids = array( ); //cache the fields, just to speed things up a little

    if($reset_cache) {
      $field_ids = array();
    }

    //if the field ids havent been cached yet, grab them
    if( ! isset( $field_ids[$group_id] ) ) {
      $fields = $this->webtest_civicrm_api("CustomField", "get", array( 'custom_group_id' => $group_id ) );
      $field_ids[$group_id] = array();
      foreach($fields['values'] as $id => $field) {
        $field_ids[$group_id][] = $id;
      }
    }


    $params = array('contact_id' => $contact_id);
    foreach($field_ids[$group_id] as $id) {
      $params['return.custom_' . $id] = 1;
    }

    $contact = $this->webtest_civicrm_api("Contact", "get", $params);

    //clean up the api results a bit....
    $results = array();
    foreach($field_ids[$group_id]  as $id) {
      if(isset($contact['values'][$contact_id]['custom_' . $id])) {
        $results[$id] = $contact['values'][$contact_id]['custom_' . $id];
      }
    }

    return $results;
  }


  //creates a new custom group and fields in that group, and returns the group Id
  function _buildCustomFieldset( $prefix ) {
    $group_id = $this->_createCustomGroup( $prefix );
    $field_ids[] = $this->_addCustomFieldToGroup( $group_id, 0, "CheckBox", $prefix );
    $field_ids[] = $this->_addCustomFieldToGroup( $group_id, 0, "Radio", $prefix );
    $field_ids[] = $this->_addCustomFieldToGroup( $group_id, 0, "Text", $prefix );
    return $group_id;
  }

  //Creates a custom field group for a specific entity type and returns the custom group Id
  function _createCustomGroup( $prefix="custom", $entity="Contact" ) {
      // Go directly to the URL of the screen that you will be testing (New Custom Group).
      $this->open($this->sboxPath . "civicrm/admin/custom/group?action=add&reset=1");

      $this->waitForPageToLoad("30000");
      
      //fill custom group title
      $customGroupTitle = $prefix . '_'.substr(sha1(rand()), 0, 7);
      $this->click("title");
      $this->type("title", $customGroupTitle);

      //custom group extends 
      $this->click("extends[0]");
      $this->select("extends[0]", "value=" . $entity);
      $this->click("//option[@value='" . $entity . "']");
      $this->click("_qf_Group_next-bottom");
      $this->waitForElementPresent("_qf_Field_cancel-bottom");

      //Is custom group created?
      $this->assertTrue($this->isTextPresent("Your custom field set '{$customGroupTitle}' has been added. You can add custom fields now."), "Group title missing");

      $url = $this->parseURL( );
      $group_id = $url['queryString']['gid'];
      $this->assertType( 'numeric', $group_id );

      return $group_id;
  }

  function _addCustomFieldToGroup( $group_id, $type='0', $widget='CheckBox', $prefix='' ) {

    //Go to the add custom field page for the given group id
    $this->open($this->sboxPath . "civicrm/admin/custom/group/field/add?action=add&reset=1&gid=" . $group_id);
    $this->waitForPageToLoad("30000");


    //Do common setup for all field types

    //set the field label
    $fieldLabel = (isset($prefix) ? $prefix . "_" : "") . $widget . "_" . substr(sha1(rand()), 0, 6);
    $this->click("label");
    $this->type("label", $fieldLabel);

    //enter pre help message
    $this->type("help_pre", "this is field pre help for " .  $fieldLabel);

    //enter post help message
    $this->type("help_post", "this field post help for " . $fieldLabel);

    //Is searchable?
    $this->click("is_searchable");

    //Fill in the html type and widget type
    $this->click("data_type[0]");
    $this->select("data_type[0]", "value=" . $type);
    $this->click("//option[@value='" . $type ."']");
    $this->click("data_type[1]");
    $this->select("data_type[1]", "value=" . $widget);
    $this->click("//option[@value='" . $widget ."']");

    //fill in specific ellements for different widgets
    switch($widget) {
    case 'CheckBox':
      $this->_createFieldOptions();
      $this->type("options_per_line", "2");
      break;
    case 'Radio':
      $this->_createFieldOptions();
      $this->type("options_per_line", "1");
      break;
      //TODO allow for more things....
    }

    //clicking save
    $this->click("_qf_Field_next");
    $this->waitForPageToLoad("30000");

    //Is custom field created?
    $this->assertTrue($this->isTextPresent("Your custom field '$fieldLabel' has been saved."), "Field was not created successfully");

    //get the custom id of the custom field that was just created
    $results = $this->webtest_civicrm_api( "CustomField", "get", array( 'label' => $fieldLabel, 'custom_group_id' => $group_id ) );

    //While there _technically_ could be two fields with the same name, its highly unlikely
    //so assert that exactly one result is return    
    $this->assertTrue( $results['count'] == 1, "Could not uniquely get custom field id");
    return $results['id'];
  }

  function _createFieldOptions( $count = 3, $prefix = "option", $values = array( ) ) {
    $count = $count > 10 ? 10 : $count; //Only support up to 10 options on the creation form

    for($i = 1; $i <= $count; $i++) {
      $label = $prefix . '_' . substr(sha1(rand()), 0, 6);
      $this->type("option_label_" . $i, $label );
      $this->type("option_value_" . $i,  $i);
    }
  }

  function _fillCustomDataForContact( $contact_id, $group_id ) {
    //edit the given contact
    $this->open($this->sboxPath . "civicrm/contact/add?reset=1&action=update&cid=" . $contact_id );
    $this->waitForPageToLoad("30000");

    $this->click("expand");
    $this->waitForElementPresent("address_1_street_address");

    //get the custom fields for the group
    $fields = $this->webtest_civicrm_api("CustomField", "get", array( 'custom_group_id' => $group_id ) );
    $fields = $fields['values'];

    //fill a value in for each field
    foreach($fields as $field_id => $field) {

      //if there is an option group id, we grab the labels and select on randomly
      if( isset($field['option_group_id']) ) {
        $options = $this->webtest_civicrm_api("OptionValue", "get", array('option_group_id' => $field['option_group_id'] ) );
        $options = $options['values'];
        $pick_me = $options[array_rand($options)]['label'];
        $this->click("xpath=//table//tr/td/label[text()=\"$pick_me\"]");
      } else {
        //gonna go ahead and assume its an alphanumeric text question.  This
        //will really only work if the custom data group has not yet been
        //filled out for this contact
        $this->type("custom_" . $field['id'] . '_-1', sha1(rand()));
      }
    } 

    //save the form
    $this->click("_qf_Contact_upload_view");
    $this->waitForPageToLoad("30000");

    //assert success
    $this->assertTrue($this->isTextPresent("record has been saved"), "Contact Record could not be saved");

  }
}
?>