<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 2.0                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2007                                |
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

require_once 'CRM/Contact/Page/View.php';

/**
 * This class handle case related functions
 *
 */
class CRM_Contact_Page_View_Case extends CRM_Contact_Page_View 
{
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;
    
    /**
     * View details of a case
     *
     * @return void
     * @access public
     */
    function view( ) 
    {
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Case_Form_Case',  
                                                       'View Case',  
                                                       $this->_action ); 
        $controller->setEmbedded( true ); 
        $controller->set( 'id' , $this->_id );  
        $controller->set( 'cid', $this->_contactId );
        $controller->run();
        
        $this->assign( 'caseId',$this->_id);
        require_once 'CRM/Activity/Selector/Activity.php' ;
        require_once 'CRM/Core/Selector/Controller.php';
        $output = CRM_Core_Selector_Controller::SESSION;
        $selector   =& new CRM_Activity_Selector_Activity($this->_contactId, $this->_permission, false, 'case' );
        $controller =& new CRM_Core_Selector_Controller($selector, $this->get(CRM_Utils_Pager::PAGE_ID),
                                                        $sortID, CRM_Core_Action::VIEW, $this,  $output, null, $this->_id);
        
        
        $controller->setEmbedded(true);

        $controller->run();
        $controller->moveFromSessionToTemplate( );

        $this->assign( 'context', 'case');
    }

    /**
     * This function is called when action is browse
     *
     * return null
     * @access public
     */
    function browse( ) {

        $links  =& self::links( );
        $action = array_sum(array_keys($links));
        $caseStatus = CRM_Core_OptionGroup::values('case_status');
        $caseType   = CRM_Core_OptionGroup::values('case_type');

        require_once 'CRM/Case/BAO/Case.php';
        $case = new CRM_Case_DAO_Case( );
        $case->contact_id = $this->_contactId;
        $case->find();
        while ( $case->fetch() ) {
            CRM_Core_DAO::storeValues( $case, $values[$case->id] );
            $values[$case->id]['action'] = CRM_Core_Action::formLink( $links,
                                                                      $action,
                                                                      array( 'id'  => $case->id,
                                                                             'cid' => $this->_contactId ) );

            $caseTypeIds =  explode( CRM_Case_BAO_Case::VALUE_SEPERATOR, $values[$case->id]['case_type_id'] );
            foreach ( $caseTypeIds as $id => $val ) {
                if ( $val ) {
                    $names[] = $caseType[$val];
                }
            }
            
            $values[$case->id]['case_type_id'] = implode ( ':::' , $names);
            $values[$case->id]['status_id']    = $caseStatus[$values[$case->id]['status_id']];
        } 
        
        $this->assign( 'cases', $values );
    }

    /**
     * This function is called when action is update or new
     * 
     * return null
     * @access public
     */
    function edit( ) 
    {
        $this->_id = CRM_Utils_Request::retrieve('id', 'Integer', $this);
        $controller =& new CRM_Core_Controller_Simple( 'CRM_Case_Form_Case', 
                                                       'Create Case', 
                                                       $this->_action );
        $controller->setEmbedded( true );
        $controller->set( 'id' , $this->_id ); 
        $controller->set( 'cid', $this->_contactId ); 
        
        return $controller->run( );
    }
    
    /**
     * This function is the main function that is called when the page loads,
     * it decides the which action has to be taken for the page.
     *
     * return null
     * @access public
     */
    function run( ) 
    {
        $this->preProcess( );

        $this->setContext( );

        if ( $this->_action & CRM_Core_Action::VIEW ) {
            $this->view( );
        } else if ( $this->_action & ( CRM_Core_Action::UPDATE | CRM_Core_Action::ADD | CRM_Core_Action::DELETE ) ) {
            $this->edit( );
        }
                
        $this->browse( );
        return parent::run( );
    }


    /**
     * Get action links
     *
     * @return array (reference) of action links
     * @static
     */
    static function &links()
    {
        if (!(self::$_links)) {
            $deleteExtra = ts('Are you sure you want to delete this case?');

            self::$_links = array(
                                  CRM_Core_Action::UPDATE  => array(
                                                                    'name'  => ts('Edit'),
                                                                    'url'   => 'civicrm/contact/view/case',
                                                                    'qs'    => 'action=update&reset=1&cid=%%cid%%&id=%%id%%&selectedChild=case',
                                                                    'title' => ts('Edit Case')
                                                                    ),
//                                   CRM_Core_Action::FOLLOWUP  => array(
//                                                                     'name'  => ts('Add Activity'),
//                                                                     'url'   => 'civicrm/activity',
//                                                                     'qs'    => 'action=add&reset=1&context=case&caseid=%%id%%&cid=%%cid%%',
//                                                                     'title' => ts('Add Activity')
//                                                                     ),
                                  CRM_Core_Action::DELETE  => array(
                                                                    'name'  => ts('Delete'),
                                                                    'url'   => 'civicrm/contact/view/case',
                                                                    'qs'    => 'action=delete&reset=1&cid=%%cid%%&id=%%id%%&selectedChild=case',
                                                                    'title' => ts('Add Activity')
                                                                    ),
                                  
                                  );
        }
        return self::$_links;
    }

    function setContext( ) 
    {
        $context = CRM_Utils_Request::retrieve( 'context', 'String', $this );
        $url = null;

        switch ( $context ) {
        case 'home':
            $url = CRM_Utils_System::url( 'civicrm/dashboard', 'reset=1' );

            break;

        case 'activity':
            $url = CRM_Utils_System::url( 'civicrm/contact/view',
                                          "reset=1&force=1&cid={$this->_contactId}&selectedChild=activity" );
            break;

        default :
            $url = CRM_Utils_System::url( 'civicrm/contact/view',
                                          "reset=1&force=1&cid={$this->_contactId}&selectedChild=case" );
            break;
        }
        
        $session =& CRM_Core_Session::singleton( ); 
        if ( $url ) {
            $session->pushUserContext( $url );
        }
    }
}

?>
