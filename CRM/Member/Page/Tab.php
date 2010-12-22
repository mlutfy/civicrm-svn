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

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2010
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';
require_once 'CRM/Member/BAO/Membership.php';

class CRM_Member_Page_Tab extends CRM_Core_Page {

    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;
    static $_membershipTypesLinks = null;
    
    public $_permission = null; 
    public $_contactId  = null;
    
   /**
     * This function is called when action is browse
     * 
     * return null
     * @access public
     */
    function browse( ) 
    { 
        $links =& self::links( 'all', $this->_isPaymentProcessor, $this->_accessContribution );

        $membership = array();
        require_once 'CRM/Member/DAO/Membership.php';
        $dao = new CRM_Member_DAO_Membership();
        $dao->contact_id = $this->_contactId;
        $dao->is_test = 0;
        //$dao->orderBy('name');
        $dao->find();
       
        //CRM--4418, check for view, edit, delete
        $permissions = array( CRM_Core_Permission::VIEW );
        if ( CRM_Core_Permission::check( 'edit memberships' ) ) {
            $permissions[] = CRM_Core_Permission::EDIT;
        }
        if ( CRM_Core_Permission::check( 'delete in CiviMember' ) ) {
            $permissions[] = CRM_Core_Permission::DELETE;
        }
        $mask = CRM_Core_Action::mask( $permissions );
        
        // get deceased status id
        require_once 'CRM/Member/PseudoConstant.php';
        $allStatus        = CRM_Member_PseudoConstant::membershipStatus( );
        $deceasedStatusId = array_search( 'Deceased', $allStatus );

        //checks membership of contact itself
        while ($dao->fetch()) {
            $membership[$dao->id] = array();
            CRM_Core_DAO::storeValues( $dao, $membership[$dao->id]); 
            
            //get the membership status and type values.
            $statusANDType = CRM_Member_BAO_Membership::getStatusANDTypeVaues( $dao->id );
            foreach ( array( 'status', 'membership_type' ) as $fld ) {
                $membership[$dao->id][$fld] = CRM_Utils_Array::value( $fld, $statusANDType[$dao->id] );
            }
            if ( CRM_Utils_Array::value( 'is_current_member', $statusANDType[$dao->id] ) ) {
                $membership[$dao->id]['active'] = true;
            }
            if ( ! $dao->owner_membership_id ) {
                // unset renew and followup link for deceased membership
                $currentMask = $mask;
                if ( $dao->status_id == $deceasedStatusId ) { 
                    $currentMask = $currentMask & ~CRM_Core_Action::RENEW & ~CRM_Core_Action::FOLLOWUP;
                }
                
                
                $isCancelSupported = CRM_Member_BAO_Membership::isCancelSubscriptionSupported( $membership[$dao->id]['membership_id'] );
                
                
                $membership[$dao->id]['action'] = CRM_Core_Action::formLink( self::links( 'all', 
                                                                                          null, 
                                                                                          null, 
                                                                                          $isCancelSupported ),
                                                                             $currentMask, 
                                                                             array('id' => $dao->id, 
                                                                                   'cid'=> $this->_contactId)
                                                                              );
            } else {
                $membership[$dao->id]['action'] = CRM_Core_Action::formLink( self::links( 'view' ),
                                                                             $mask, 
                                                                             array('id' => $dao->id, 
                                                                                   'cid'=> $this->_contactId));
            }
            
            //does membership is auto renew CRM-7137.
            $membership[$dao->id]['auto_renew'] = CRM_Utils_Array::value( 'contribution_recur_id', 
                                                                          $membership[$dao->id] ); 
        }
        
        //Below code gives list of all Membership Types associated
        //with an Organization(CRM-2016)
        include_once 'CRM/Member/BAO/MembershipType.php';
        $membershipTypes = CRM_Member_BAO_MembershipType::getMembershipTypesByOrg( $this->_contactId );        
        foreach ( $membershipTypes as $key => $value ) {   
            $membershipTypes[$key]['action'] = CRM_Core_Action::formLink( self::membershipTypeslinks(),
                                                                          $mask, 
                                                                          array('id' => $value['id'], 
                                                                                'cid'=> $this->_contactId));
        }
        
        $activeMembers = CRM_Member_BAO_Membership::activeMembers( $membership );
        $inActiveMembers = CRM_Member_BAO_Membership::activeMembers( $membership, 'inactive');
        $this->assign('activeMembers',   $activeMembers);
        $this->assign('inActiveMembers', $inActiveMembers);
        $this->assign('membershipTypes', $membershipTypes);
        
        if ( $this->_contactId ) {
            require_once 'CRM/Contact/BAO/Contact.php';
            $displayName = CRM_Contact_BAO_Contact::displayName( $this->_contactId );
            $this->assign( 'displayName', $displayName );
        }        
    }

    /** 
     * This function is called when action is view
     *  
     * return null 
     * @access public 
     */ 
    function view( ) 
    {
        $controller = new CRM_Core_Controller_Simple( 'CRM_Member_Form_MembershipView', 'View Membership',  
                                                       $this->_action ); 
        $controller->setEmbedded( true );  
        $controller->set( 'id' , $this->_id );  
        $controller->set( 'cid', $this->_contactId );  
        
        return $controller->run( ); 
    }

    /** 
     * This function is called when action is update or new 
     *  
     * return null 
     * @access public 
     */ 
    function edit( ) 
    {
        // set https for offline cc transaction        
        $mode = CRM_Utils_Request::retrieve( 'mode', 'String', $this );
        if ( $mode == 'test' || $mode == 'live' ) {
            CRM_Utils_System::redirectToSSL( );
        }
        
        if( $this->_action != CRM_Core_Action::ADD ) {
            // get associated contributions only on edit/renew/delete
            $this->associatedContribution( );
        }
        
        if ( $this->_action & CRM_Core_Action::RENEW ) { 
            $path  = 'CRM_Member_Form_MembershipRenewal';
            $title = ts('Renew Membership');
        } else {        
            $path  = 'CRM_Member_Form_Membership';
            $title = ts('Create Membership');
        }
        $controller = new CRM_Core_Controller_Simple( $path, $title, $this->_action );
        $controller->setEmbedded( true ); 
        $controller->set('BAOName', $this->getBAOName());
        $controller->set( 'id' , $this->_id ); 
        $controller->set( 'cid', $this->_contactId ); 
        return $controller->run( );
    }
    
    function preProcess( ) {
        $context       = CRM_Utils_Request::retrieve('context', 'String', $this );
        $this->_action = CRM_Utils_Request::retrieve('action', 'String', $this, false, 'browse');
        $this->_id     = CRM_Utils_Request::retrieve( 'id', 'Positive', $this );

        if ( $context == 'standalone' ) {
            $this->_action = CRM_Core_Action::ADD;
        } else {
            $this->_contactId = CRM_Utils_Request::retrieve( 'cid', 'Positive', $this, true );
            $this->assign( 'contactId', $this->_contactId );

            // check logged in url permission
            require_once 'CRM/Contact/Page/View.php';
            CRM_Contact_Page_View::checkUserPermission( $this );
            
            // set page title
            CRM_Contact_Page_View::setTitle( $this->_contactId );
        }      

        $this->assign('action', $this->_action );     
        
        if ( $this->_permission == CRM_Core_Permission::EDIT && ! CRM_Core_Permission::check( 'edit memberships' ) ) {
            $this->_permission = CRM_Core_Permission::VIEW; // demote to view since user does not have edit membership rights
            $this->assign( 'permission', 'view' );
        }
    }

   /**
     * This function is the main function that is called when the page loads, it decides the which action has to be taken for the page.
     * 
     * return null
     * @access public
     */
    function run( ) 
    {
        $this->preProcess( );

        // check if we can process credit card membership
        $processors = CRM_Core_PseudoConstant::paymentProcessor( false, false,
                                                                 "billing_mode IN ( 1, 3 )" );
        if ( count( $processors ) > 0 ) {
            $this->assign( 'newCredit', true );
            $this->_isPaymentProcessor = true;
        } else {
            $this->assign( 'newCredit', false );
            $this->_isPaymentProcessor = false;
        }
        
        // Only show credit card membership signup if user has CiviContribute permission
        if ( CRM_Core_Permission::access( 'CiviContribute' ) ) {
            $this->_accessContribution = true;
            $this->assign( 'accessContribution', true );
        } else {
            $this->_accessContribution = false;
            $this->assign( 'accessContribution', false );
        }
               
        if ( $this->_action & CRM_Core_Action::VIEW ) { 
            $this->view( ); 
        } else if ( $this->_action & ( CRM_Core_Action::UPDATE | CRM_Core_Action::ADD | CRM_Core_Action::DELETE | CRM_Core_Action::RENEW ) ) { 
            $this->setContext( );
            $this->edit( ); 
        } else {
            $this->setContext( );
            $this->browse( );
        }

        return parent::run( );
    }

    function setContext( $contactId = null ) {
        $context      = CRM_Utils_Request::retrieve( 'context'     ,
                                                     'String', $this, false, 'search' );
        
        $qfKey = CRM_Utils_Request::retrieve( 'key', 'String', $this );
        //validate the qfKey
        require_once 'CRM/Utils/Rule.php';
        if ( !CRM_Utils_Rule::qfKey( $qfKey ) ) $qfKey = null;
        
        if ( ! $contactId ) {
            $contactId = $this->_contactId;
        }
        
        switch ( $context ) {

        case 'dashboard':
            $url = CRM_Utils_System::url( 'civicrm/member',
                                          'reset=1' );
            break;

        case 'membership':
            $url = CRM_Utils_System::url( 'civicrm/contact/view',
                                          "reset=1&force=1&cid={$contactId}&selectedChild=member" );
            break;

        case 'search':
            $urlParams = 'force=1';
            if ( $qfKey ) $urlParams .= "&qfKey=$qfKey";
            $this->assign( 'searchKey',  $qfKey );
            
            $url = CRM_Utils_System::url( 'civicrm/member/search', $urlParams );
            break;
            
        case 'home':
            $url = CRM_Utils_System::url( 'civicrm/dashboard', 'reset=1' );
            break;

        case 'activity':
            $url = CRM_Utils_System::url( 'civicrm/contact/view',
                                          "reset=1&force=1&cid={$contactId}&selectedChild=activity" );
            break;

        case 'standalone':
            $url = CRM_Utils_System::url( 'civicrm/dashboard', 'reset=1' );
            break;
            
        case 'fulltext':
            $action = CRM_Utils_Request::retrieve('action', 'String', $this);
            $keyName   = '&qfKey';
            $urlParams = 'force=1';
            $urlString = 'civicrm/contact/search/custom';
            if ( $action == CRM_Core_Action::UPDATE ) {
                if ( $this->_contactId ) {
                    $urlParams .= '&cid=' . $this->_contactId;
                }
                $keyName    = '&key';
                $urlParams .= '&context=fulltext&action=view';
                $urlString  = 'civicrm/contact/view/membership';
            }
            if ( $qfKey ) $urlParams .= "$keyName=$qfKey";
            $this->assign( 'searchKey',  $qfKey );
            $url = CRM_Utils_System::url( $urlString, $urlParams );
            break;
            
        default:
            $cid = null;
            if ( $contactId ) {
                $cid = '&cid=' . $contactId;
            }
            $url = CRM_Utils_System::url( 'civicrm/member/search', 
                                          'force=1' . $cid );
            break;
        }

        $session = CRM_Core_Session::singleton( ); 
        $session->pushUserContext( $url );
    }

    /**
     * Get action links
     *
     * @return array (reference) of action links
     * @static
     */
    static function &links( $status = 'all', 
                            $isPaymentProcessor = null, 
                            $accessContribution = null, 
                            $isCancelSupported = false )
    {
        if ( ! CRM_Utils_Array::value( 'view', self::$_links ) ) {
            self::$_links['view'] = array(
                                  CRM_Core_Action::VIEW    => array(
                                                                    'name'  => ts('View'),
                                                                    'url'   => 'civicrm/contact/view/membership',
                                                                    'qs'    => 'action=view&reset=1&cid=%%cid%%&id=%%id%%&context=membership&selectedChild=member',
                                                                    'title' => ts('View Membership')
                                                                    ),
                                  );
        }

        if ( ! CRM_Utils_Array::value( 'all', self::$_links ) ) {
            $extraLinks = array(
                                CRM_Core_Action::UPDATE => array(
                                                                 'name'  => ts('Edit'),
                                                                 'url'   => 'civicrm/contact/view/membership',
                                                                 'qs'    => 'action=update&reset=1&cid=%%cid%%&id=%%id%%&context=membership&selectedChild=member',
                                                                 'title' => ts('Edit Membership')
                                                                 ),
                                CRM_Core_Action::RENEW => array(
                                                                  'name'  => ts('Renew'),
                                                                  'url'   => 'civicrm/contact/view/membership',
                                                                  'qs'    => 'action=renew&reset=1&cid=%%cid%%&id=%%id%%&context=membership&selectedChild=member',
                                                                  'title' => ts('Renew Membership')
                                                                  ),
                                CRM_Core_Action::FOLLOWUP => array(
                                                                   'name'  => ts('Renew-Credit Card'),
                                                                   'url'   => 'civicrm/contact/view/membership',
                                                                   'qs'    => 'action=renew&reset=1&cid=%%cid%%&id=%%id%%&context=membership&selectedChild=member&mode=live',
                                                                   'title' => ts('Renew Membership Using Credit Card')
                                                                  ),
                                CRM_Core_Action::DELETE => array(
                                                                 'name'  => ts('Delete'),
                                                                 'url'   => 'civicrm/contact/view/membership',
                                                                 'qs'    => 'action=delete&reset=1&cid=%%cid%%&id=%%id%%&context=membership&selectedChild=member',
                                                                 'title' => ts('Delete Membership')
                                                                 ),
                                );
            if( ! $isPaymentProcessor || ! $accessContribution ) {
                //unset the renew with credit card when payment
                //processor is not available or user is not permitted to create contributions
                unset( $extraLinks[CRM_Core_Action::FOLLOWUP] );
            }
            self::$_links['all'] = self::$_links['view'] + $extraLinks;
        }
       
        
        if ( $isCancelSupported ) {
            self::$_links['all'][CRM_Core_Action::DISABLE] = array( 
                                                                   'name' => ts('Cancel Subscription'),
                                                                   'url'  => 'civicrm/contribute/unsubscribe',
                                                                   'qs'   => 'reset=1&mid=%%id%%',
                                                                   'title'=> ts('Cancel Auto Renew Subscription')
                                                                    );
        } else if ( isset( self::$_links['all'][CRM_Core_Action::DISABLE] ) ) {
            unset( self::$_links['all'][CRM_Core_Action::DISABLE] );
        }
        return self::$_links[$status];
    }
    
    /**
     * Function to define action links for membership types of related organization
     *
     * @return array self::$_membershipTypesLinks array of action links
     * @access public
     */
    static function &membershipTypesLinks( ) 
    {
        if ( ! self::$_membershipTypesLinks ) {
            self::$_membershipTypesLinks =
                array(
                      CRM_Core_Action::VIEW   => array(
                                                       'name'  => ts('Members'),
                                                       'url'   => 'civicrm/member/search/',
                                                       'qs'    => 'reset=1&force=1&type=%%id%%',
                                                       'title' => ts('Search')
                                                       ),
                      CRM_Core_Action::UPDATE => array(
                                                       'name'  => ts('Edit'),
                                                       'url'   => 'civicrm/admin/member/membershipType',
                                                       'qs'    => 'action=update&id=%%id%%&reset=1',
                                                       'title' => ts('Edit Membership Type') 
                                                       ),
                      );
        }
        return self::$_membershipTypesLinks;
    }

    /** 
     * This function is used for the to show the associated
     * contribution for the membership 
     * @form array $form (ref.) an assoc array of name/value pairs
     * return null 
     * @access public 
     */ 
    function associatedContribution( $contactId = null, $membershipId = null )
    {
        if ( ! $contactId ) {
            $contactId = $this->_contactId;
        }
        
        if ( !$membershipId ) {
            $membershipId = $this->_id;
        }
        
        // retrieive membership contributions if the $membershipId is set
        if ( CRM_Core_Permission::access( 'CiviContribute' ) && $membershipId ) {
            $this->assign( 'accessContribution', true );
            $controller = new CRM_Core_Controller_Simple( 'CRM_Contribute_Form_Search', ts('Contributions'), null );  
            $controller->setEmbedded( true );                           
            $controller->reset( );  
            $controller->set( 'force', 1 );
            $controller->set( 'cid'  , $contactId );
            $controller->set( 'memberId', $membershipId );
            $controller->set( 'context', 'contribution' ); 
            $controller->process( );  
            $controller->run( );
        } else {
            $this->assign( 'accessContribution', false );
        }
    }

    /**
     * Get BAO Name
     *
     * @return string Classname of BAO.
     */
    function getBAOName() 
    {
        return 'CRM_Member_BAO_Membership';
    }
    
}


