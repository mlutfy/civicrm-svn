<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.2                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2009                                |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the GNU Affero General Public License           |
 | Version 3, 19 November 2007.                                       |
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

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2009
 * $Id$
 *
 */

require_once 'CRM/Core/Form.php';

/**
 * This class is to build the form for adding Group
 */
class CRM_Contact_Form_Domain extends CRM_Core_Form {

    /**
     * the group id, used when editing a group
     *
     * @var int
     */
    protected $_id;
    
    /**
     * default from email address option value id.
     *
     * @var int
     */
    protected $_fromEmailId = null;
    
    /**
     * how many locationBlocks should we display?
     *
     * @var int
     * @const
     */
    const LOCATION_BLOCKS = 1;

    function preProcess( ) {
        
        CRM_Utils_System::setTitle(ts('Domain Information'));
        $breadCrumbPath = CRM_Utils_System::url( 'civicrm/admin', 'reset=1' );
        CRM_Utils_System::appendBreadCrumb( ts('Administer CiviCRM'), $breadCrumbPath );

        $this->_id = 1;
        $this->_action = CRM_Utils_Request::retrieve( 'action', 'String',
                                                      $this, false, 'view' );
        
    }
    
    /*
     * This function sets the default values for the form.
     * the default values are retrieved from the database
     *
     * @access public
     * @return None
     */
    
    function setDefaultValues( ) {
        
        require_once 'CRM/Core/BAO/Domain.php';

        $defaults = array( );
        $params   = array( );
        $locParams = array();
        
        if ( isset( $this->_id ) ) {
            $params['id'] = $this->_id ;
            CRM_Core_BAO_Domain::retrieve( $params, $defaults );
            
            //get the default domain from email address. fix CRM-3552
            require_once 'CRM/Utils/Mail.php';
            require_once 'CRM/Core/OptionValue.php';
            $optionValues = array( );
            $grpParams['name'] = 'from_email_address';
            CRM_Core_OptionValue::getValues( $grpParams, $optionValues );
            foreach ( $optionValues as $Id => $value ) {
                if ( $value['is_default'] && $value['is_active'] ) {
                    $this->_fromEmailId        = $Id;
                    $defaults['email_name']    = CRM_Utils_Array::value( 1, explode('"', $value['label'] ) );
                    $defaults['email_address'] = CRM_Utils_Mail::pluckEmailFromHeader( $value['label'] );
                    break;
                }
            }
            
            unset($params['id']);
            $locParams = $params + array('entity_id' => $this->_id, 'entity_table' => 'civicrm_domain');
            require_once 'CRM/Core/BAO/Location.php';
            CRM_Core_BAO_Location::getValues( $locParams, $defaults);

            //set defaults for country-state dojo widget
            if ( ! empty ( $defaults['location'] ) ) {
                foreach ( $defaults['location'] as $key => $value ) {
                    if ( isset( $value['address'] ) ) {
                        CRM_Contact_Form_Address::fixStateSelect( $this,
                                                                  "location[$key][address][country_id]",
                                                                  "location[$key][address][state_province_id]",
                                                                  CRM_Utils_Array::value( 'country_id',
                                                                                          $value['address'] ) );
                    }
                }
            }
        }
        return $defaults;
    }
    
    /**
     * Function to actually build the form
     *
     * @return None
     * @access public
     */

    public function buildQuickForm( ) {
        
        $this->add('text', 'name' , ts('Domain Name') , array('size' => 25), true);
        $this->add('text', 'description', ts('Description'), array('size' => 25) );

        $this->add('text', 'email_name', ts('FROM Name'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email','email'), true);

        $this->add('text', 'email_address', ts('FROM Email Address'), CRM_Core_DAO::getAttribute('CRM_Core_DAO_Email','email'), true);
        $this->addRule( "email_address", ts('Domain Email Address must use a valid email address format (e.g. \'info@example.org\').'), 'email' );

        //blocks to be displayed
        $this->assign( 'locationCount', self::LOCATION_BLOCKS + 1);    
   
        require_once 'CRM/Contact/Form/Location.php';
        $locationCompoments = array('Phone', 'Email');
        CRM_Contact_Form_Location::buildLocationBlock( $this, self::LOCATION_BLOCKS ,$locationCompoments);
        $this->assign( 'index' , 1 );
        $this->assign( 'blockCount'   , 1 );

        //hack the address sequence so that state province always comes after country
        $config =& CRM_Core_Config::singleton( );
        $addressSequence = $config->addressSequence();
        $key = array_search( 'country', $addressSequence);
        unset($addressSequence[$key]);

        $key = array_search( 'state_province', $addressSequence);
        unset($addressSequence[$key]);

        $addressSequence = array_merge( $addressSequence, array ( 'country', 'state_province' ) );
        $this->assign( 'addressSequence', $addressSequence );

        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Save'),
                                         'subName'   => 'view',
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => ts('Cancel') ) ) );
        
        if ($this->_action & CRM_Core_Action::VIEW ) { 
            $this->freeze();
        }        
        $this->assign('emailDomain',true);
    }

    /**
     * Add local and global form rules
     *
     * @access protected
     * @return void
     */
    function addRules( ) 
    {
        $this->addFormRule( array( 'CRM_Contact_Form_Domain', 'formRule' ) );
    }
    
    /**
     * global validation rules for the form
     *
     * @param array $fields posted values of the form
     *
     * @return array list of errors to be posted back to the form
     * @static
     * @access public
     */
    static function formRule( &$fields ) 
    {
        $errors = array( );
        // check for state/country mapping
        CRM_Contact_Form_Address::formRule($fields, $errors);
        
        //fix for CRM-3552, 
        //as we use "fromName"<emailaddresss> format for domain email.
        if ( strpos( $fields['email_name'], '"' ) !== false ) {
            $errors['email_name'] = ts( 'Double quotes are not allow in from name.' );
        }
        
        return empty($errors) ? true : $errors;
    }    

    /**
     * Process the form when submitted
     *
     * @return void
     * @access public
     */

    public function postProcess( ) {

        require_once 'CRM/Core/BAO/Domain.php';

        $params = array( );
        
        $params = $this->exportValues();
        $params['entity_id'] = $this->_id;
        $params['entity_table'] = CRM_Core_BAO_Domain::getTableName();
        $domain = CRM_Core_BAO_Domain::edit($params, $this->_id);

        require_once 'CRM/Core/BAO/LocationType.php';
        $defaultLocationType =& CRM_Core_BAO_LocationType::getDefault();
        
        $location = array();
        $params['location'][1]['location_type_id'] = $defaultLocationType->id;
        $location = CRM_Core_BAO_Location::create($params, true, 'domain');
        
        $params['loc_block_id'] = $location['id'];
        
        require_once 'CRM/Core/BAO/Domain.php';
        CRM_Core_BAO_Domain::edit($params, $this->_id);
        
        //set domain from email address, CRM-3552 
        $emailName = '"' . $params['email_name'] . '"<' . $params['email_address'] . '>';
        
        $emailParams = array( 'label'       => $emailName,
                              'description' => $params['description'],
                              'is_active'   => 1,
                              'is_default'  => 1 );
        
        $groupParams = array( 'name' => 'from_email_address' );
        
        //get the option value wt.
        if ( $this->_fromEmailId ) {
            $action = $this->_action;
            $emailParams['weight'] = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionValue', $this->_fromEmailId, 'weight' );
        } else {
            //add from email address.
            $action = CRM_Core_Action::ADD;
            require_once 'CRM/Utils/Weight.php';
            $grpId = CRM_Core_DAO::getFieldValue( 'CRM_Core_DAO_OptionGroup', 'from_email_address', 'id', 'name' );
            $fieldValues = array('option_group_id' => $grpId );
            $emailParams['weight'] = CRM_Utils_Weight::getDefaultWeight('CRM_Core_DAO_OptionValue', $fieldValues );
        }
        
        require_once 'CRM/Core/OptionValue.php';
        CRM_Core_OptionValue::addOptionValue( $emailParams, $groupParams, $action, $this->_fromEmailId );
       
        CRM_Core_Session::setStatus( ts('Domain information for \'%1\' has been saved.', array( 1 => $domain->name )) );
        $session =& CRM_Core_Session::singleton( );
        $session->replaceUserContext(CRM_Utils_System::url('civicrm/admin', 'reset=1' ) );

    }
    
}


