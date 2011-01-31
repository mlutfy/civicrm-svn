<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.3                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2010                                |
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
 | License and the CiviCRM Licensing Exception along                  |
 | with this program; if not, contact CiviCRM LLC                     |
 | at info[AT]civicrm[DOT]org. If you have questions about the        |
 | GNU Affero General Public License or the licensing of CiviCRM,     |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+
*/

require_once 'CiviTest/CiviUnitTestCase.php';
require_once 'api/v3/GroupOrganization.php';

/**
 * Test class for GroupOrganization API - civicrm_group_organization_*
 *
 *  @package   CiviCRM
 */
class api_v3_GroupOrganizationTest extends CiviUnitTestCase
{
    protected $_apiversion;
    function get_info( )
    {
        return array(
                     'name'        => 'Group Organization',
                     'description' => 'Test all Group Organization API methods.',
                     'group'       => 'CiviCRM API Tests',
                     );
    }

    /**
     * Sets up the fixture, for example, opens a network connection.
     * This method is called before a test is executed.
     *
     * @access protected
     */
    protected function setUp()
    {
            $this->markTestSkipped( "Reason for skipping:<a href='http://forum.civicrm.org/index.php/topic,18053.0.html'>version issue</a>" );
         $this->_apiversion =3;
        parent::setUp();
        $this->_groupID = $this->groupCreate(null,3);

        $this->_orgID   = $this->organizationCreate(null,3 );

    }

    /**
     * Tears down the fixture, for example, closes a network connection.
     * This method is called after a test is executed.
     *
     * @access protected
     */
    protected function tearDown()
    {
    }

    ///////////////// civicrm_group_organization_get methods

    /**
     * Test civicrm_group_organization_get with valid params.
     */
    public function testGroupOrganizationGet()
    {

        $params = array( 'organization_id' => $this->_orgID,
                         'group_id'        => $this->_groupID,
                         'version'			 => $this->_apiversion,
                         );
        $result =& civicrm_group_organization_create( $params );

        $paramsGet = array( 'organization_id' => $result['result']['organization_id']  );
        
        $result    = civicrm_group_organization_get($paramsGet);
        $this->assertEquals( $result['is_error'], 0);
    }

     /**
     * Test civicrm_group_organization_get with group_id.
     */
    public function testGroupOrganizationGetWithGroupId()
    {

        $params = array( 'organization_id' => $this->_orgID,
                         'group_id'        => $this->_groupID,
                         'version'			 => $this->_apiversion,
                         );
        $result =& civicrm_group_organization_create( $params );

        $paramsGet = array( 'organization_id' => $result['result']['organization_id']  );
        
        $result    = civicrm_group_organization_get($params);
        $this->assertEquals( $result['is_error'], 0);
    } 
  
    /**
     * Test civicrm_group_organization_get with empty params.
     */
    public function testGroupOrganizationGetWithEmptyParams()
    {
        $params = array( );
        $result =& civicrm_group_organization_get($params);

        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'No input parameter present' );
    }

    /**
     * Test civicrm_group_organization_get with wrong params.
     */
    public function testGroupOrganizationGetWithWrongParams()
    {
        $params = 'groupOrg';
        $result =& civicrm_group_organization_get($params);

        $this->assertEquals( $result['is_error'], 1);
        $this->assertEquals( $result['error_message'], 'Input parameter is not an array' );
    }

    /**
     * Test civicrm_group_organization_get invalid keys.
     */
    public function testGroupOrganizationGetWithInvalidKeys()
    {
        $params = array( 'invalid_key' => 1 );
        $result =& civicrm_group_organization_get($params);

        $this->assertEquals( $result['is_error'], 1);
        $this->assertEquals( $result['error_message'], 'at least one of organization_id or group_id is a required field' );
    }

    ///////////////// civicrm_group_organization_create methods

    /**
     * check with valid params
     */
    public function testGroupOrganizationCreate()
    {
        $params = array( 'organization_id' => $this->_orgID,
                         'group_id'        => $this->_groupID,
                         'version'			 => $this->_apiversion,
                         );
        $result =& civicrm_group_organization_create($params);

        $this->assertEquals( $result['is_error'], 0);
    }    

    /**
     * check with empty params array
     */
    public function testGroupOrganizationCreateWithEmptyParams()
    {
        $params = array( );
        $result =& civicrm_group_organization_create($params);

        $this->assertEquals( $result['is_error'], 1);
        $this->assertEquals( $result['error_message'], 'No input parameter present' );
    }

    /**
     * check with invalid params
     */
    public function testGroupOrganizationCreateParamsNotArray()
    {
        $params = 'group_org';
        $result =& civicrm_group_organization_create( $params );

        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'Input parameter is not an array' );
    }
    
    /**
     * check with invalid params keys
     */
    public function testGroupOrganizationCreateWithInvalidKeys()
    {
        $params = array( 'invalid_key' => 1 );
        $result =& civicrm_group_organization_create( $params );

        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'organization_id and group_id are required field' );
    }

    ///////////////// civicrm_group_organization_remove methods

    /**
     *  Test civicrm_group_organization_remove with params not an array.
     */
    public function testGroupOrganizationRemoveParamsNotArray()
    {
        $params = 'delete';
        $result =& civicrm_group_organization_remove($params);
        
        $this->assertEquals( $result['is_error'], 1);
        $this->assertEquals( $result['error_message'], 'Input parameter is not an array' );

    }


    /**
     * Test civicrm_group_organization_remove with empty params.
     */
    public function testGroupOrganizationRemoveWithEmptyParams()
    {
        $params = array( );
        $result =& civicrm_group_organization_remove($params);

        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'No input parameter present' );
    }

    /**
     *  Test civicrm_group_organization_remove with valid params.
     */
    public function testGroupOrganizationRemove()
    {   
        $paramsC = array( 'organization_id' => $this->_orgID,
                         'group_id'        => $this->_groupID,
                         'version'			 => $this->_apiversion,
                         );
        $result =& civicrm_group_organization_create( $paramsC );

        $params = array( 'id' => $result['result']['id'],
                                'version'			 => $this->_apiversion,  );
        $result =& civicrm_group_organization_delete( $paramsDelete );
        $this->documentMe($params,$result,__FUNCTION__,__FILE__); 
        $this->assertEquals( $result['is_error'], 0 );

    }

    /**
     *  Test civicrm_group_organization_remove with invalid params key.
     */
    public function testGroupOrganizationRemoveWithInvalidKey()
    {   
        $paramsDelete = array( 'invalid_key' => 1 );
        $result =& civicrm_group_organization_remove( $paramsDelete );
 
        $this->assertEquals( $result['is_error'], 1 );
        $this->assertEquals( $result['error_message'], 'Invalid or no value for Group Organization ID' );

    }

}
?>
