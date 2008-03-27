<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.1                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2008                                |
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
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Contact/Form/Task.php';

/**
 * This class provides the functionality to group 
 * contacts. This class provides functionality for the actual
 * addition of contacts to groups.
 */
class CRM_Contact_Form_Task_AddToGroup extends CRM_Contact_Form_Task {
    /**
     * The context that we are working on
     *
     * @var string
     */
    protected $_context;

    /**
     * the groupId retrieved from the GET vars
     *
     * @var int
     */
    protected $_id;

    /**
     * the title of the group
     *
     * @var string
     */
    protected $_title;

    /**
     * build all the data structures needed to build the form
     *
     * @return void
     * @access public
     */
    function preProcess( ) {
        /*
         * initialize the task and row fields
         */
        parent::preProcess( );

        $this->_context = $this->get( 'context' );
        $this->_id      = $this->get( 'amtgID'  );
    }

    /**
     * Build the form
     *
     * @access public
     * @return void
     */
    function buildQuickForm( ) {
       
        //create radio buttons to select existing group or add a new group
        $options  = array( ts('Add Contact To Existing Group'), ts('Create New Group') );
        $this->addRadio( 'group_option', ts( 'Group Options' ), $options, array('onclick' =>"return showElements();"));
        
        $this->add('text', 'title'       , ts('Group Name:') . ' ' ,
                   CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Group', 'title' ) );
        $this->addRule( 'title', ts('Name already exists in Database.'),
                        'objectExists', array( 'CRM_Contact_DAO_Group', $this->_id, 'title' ) );
        
        $this->add('text', 'description', ts('Description:') . ' ', 
                   CRM_Core_DAO::getAttribute( 'CRM_Contact_DAO_Group', 'description' ) );

        // add select for groups
        $group = array( '' => ts('- select group -')) + CRM_Core_PseudoConstant::group( );
        
        $groupElement = $this->add('select', 'group_id', ts('Select Group'), $group);
        
        $this->_title  = $group[$this->_id];

        if ( $this->_context === 'amtg' ) {
            $groupElement->freeze( );

            // also set the group title
            $groupValues = array( 'id' => $this->_id, 'title' => $this->_title );
            $this->assign_by_ref( 'group', $groupValues );
        }
         
        // Set dynamic page title for 'Add Members Group (confirm)'
        if ( $this->_id ) {
            CRM_Utils_System::setTitle( ts('Add Contacts: %1', array(1 => $this->_title)) );
        }
        else {
            CRM_Utils_System::setTitle( ts('Add Contacts to A Group') );
        }

        $this->addDefaultButtons( ts('Add to Group') );
    }

    /**
     * Set the default form values
     *
     * @access protected
     * @return array the default array reference
     */
    function &setDefaultValues() {
        $defaults = array();

        if ( $this->_context === 'amtg' ) {
            $defaults['group_id'] = $this->_id;
        }
        
        $defaults['group_option'] = 0;
        return $defaults;
    }

    /**
     * Add local and global form rules
     *
     * @access protected
     * @return void
     */
    function addRules( ) 
    {
        $this->addFormRule( array( 'CRM_Contact_Form_task_AddToGroup', 'formRule') );
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
    static function formRule( &$params ) 
    {
        $errors = array( );
       
        if ( $params['group_option'] && !$params['title'] ) {
            $errors['title'] = "Group Name is a required field";
        } else if ( !$params['group_option'] && !$params['group_id']) {
            $errors['group_id'] = "Select Group is a required field.";
        }
        
        return empty($errors) ? true : $errors;
    }
    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() {
                
        $groupOption = $this->controller->exportValue( 'AddToGroup', 'group_option'  );
        if ( $groupOption ) {
            $groupParams = array();
            $groupParams['title'      ] = $this->controller->exportValue( 'AddToGroup', 'title' );
            $groupParams['description'] = $this->controller->exportValue( 'AddToGroup', 'description' );
            $groupParams['visibility' ] = "User and User Admin Only";
            $groupParams['domain_id'  ] = CRM_Core_Config::domainID( );
            $groupParams['group_type' ] = '';
            $groupParams['is_active'  ] = 1;
           
            require_once 'CRM/Contact/BAO/Group.php';
            $createdGroup   =& CRM_Contact_BAO_Group::create( $groupParams );
            $groupId        = $createdGroup->id;
            $groupName      = $groupParams['title'];
             
        } else {
            $groupId   = $this->controller->exportValue( 'AddToGroup', 'group_id'  );
            $group   =& CRM_Core_PseudoConstant::group( );
            $groupName = $group[$groupId];
            
        }
        
        list( $total, $added, $notAdded ) = CRM_Contact_BAO_GroupContact::addContactsToGroup( $this->_contactIds, $groupId );
        
        $status = array(
                        ts('Added Contact(s) to %1', array(1 => $groupName)),
                        ts('Total Selected Contact(s): %1', array(1 => $total))
                        );
        if ( $added ) {
            $status[] = ts('Total Contact(s) added to group: %1', array(1 => $added));
        }
        if ( $notAdded ) {
            $status[] = ts('Total Contact(s) already in group: %1', array(1 => $notAdded));
        }
        $status = implode( '<br/>', $status );
        CRM_Core_Session::setStatus( $status );
        
    }//end of function


}


