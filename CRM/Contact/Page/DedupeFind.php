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

require_once 'CRM/Core/Page/Basic.php';
require_once 'CRM/Dedupe/Finder.php';
require_once 'CRM/Dedupe/DAO/Rule.php';
require_once 'CRM/Dedupe/DAO/RuleGroup.php';
require_once 'CRM/Contact/BAO/Contact/Permission.php';

class CRM_Contact_Page_DedupeFind extends CRM_Core_Page_Basic
{
    protected $_cid = null;
    protected $_rgid;
    protected $_mainContacts;
    protected $_gid;

    /**
     * Get BAO Name
     *
     * @return string Classname of BAO.
     */
    function getBAOName()
    {
        return 'CRM_Dedupe_BAO_RuleGroup';
    }

    /**
     * Get action Links
     *
     * @return array (reference) of action links
     */
    function &links()
    {

    }
    /**
     * Browse all rule groups
     *  
     * @return void
     * @access public
     */
    function run()
    {
        $gid     = CRM_Utils_Request::retrieve( 'gid',     'Positive', $this, false, 0 );
        $action  = CRM_Utils_Request::retrieve( 'action',  'String',   $this, false, 0 );
        $context = CRM_Utils_Request::retrieve( 'context', 'String',   $this );
        
        $session = CRM_Core_Session::singleton( );
        $contactIds = $session->get( 'selectedSearchContactIds' );
        if ( $context == 'search' || !empty( $contactIds ) ) {
            $context = 'search';
            $this->assign( 'backURL', $session->readUserContext( ) );
        }
        
        if ( $action & CRM_Core_Action::UPDATE || 
             $action & CRM_Core_Action::BROWSE ) {
            $cid  = CRM_Utils_Request::retrieve( 'cid',  'Positive', $this, false, 0 );
            $rgid = CRM_Utils_Request::retrieve( 'rgid', 'Positive', $this, false, 0 );
            $this->action = CRM_Core_Action::UPDATE;
            
            //calculate the $contactType
            if ( $rgid ) {
                $contactType = CRM_Core_DAO::getFieldValue( 'CRM_Dedupe_DAO_RuleGroup', 
                                                            $rgid,
                                                            'contact_type' );
            }
            
            //reload from cache table
            $cacheKeyString  = "merge $contactType";
            $cacheKeyString .= $rgid ? "_{$rgid}" : '_0';
            $cacheKeyString .= $gid ? "_{$gid}" : '_0';
            
            require_once 'CRM/Core/BAO/PrevNextCache.php';
            $join = " JOIN civicrm_dedupe_exception de ON ! ( pn.entity_id1 = de.contact_id1 AND 
                                                              pn.entity_id2 = de.contact_id2 )";
            $this->_mainContacts = CRM_Core_BAO_PrevNextCache::retrieve( $cacheKeyString, $join );
            if ( empty( $this->_mainContacts ) ) {
                if ( $gid ) {
                    $foundDupes = $this->get( "dedupe_dupes_$gid" );
                    if ( !$foundDupes ) {
                        $foundDupes = CRM_Dedupe_Finder::dupesInGroup( $rgid, $gid );
                    }
                    $this->set( "dedupe_dupes_$gid", $foundDupes );
                } else if ( !empty( $contactIds ) ) {
                    $foundDupes = $this->get( "search_dedupe_dupes_$gid" );
                    if ( !$foundDupes ) {
                        $foundDupes = CRM_Dedupe_Finder::dupes( $rgid, $contactIds );
                    }
                    $this->get( "search_dedupe_dupes_$gid", $foundDupes );
                } else {
                    $foundDupes = $this->get( 'dedupe_dupes' );
                    if ( !$foundDupes ) {
                        $foundDupes = CRM_Dedupe_Finder::dupes( $rgid );
                    }
                    $this->set( 'dedupe_dupes', $foundDupes );
                }
                if ( !$foundDupes ) {
                    $ruleGroup = new CRM_Dedupe_BAO_RuleGroup();
                    $ruleGroup->id = $rgid;
                    $ruleGroup->find(true);
                    
                    $session = CRM_Core_Session::singleton();
                    $session->setStatus( ts('No possible duplicates were found using %1 rule.', 
                                            array( 1 => $ruleGroup->name ) ) );
                    $url = CRM_Utils_System::url( 'civicrm/contact/deduperules', 'reset=1' );
                    if ( $context == 'search' ) {
                        $url = $session->readUserContext( ); 
                    }
                    CRM_Utils_System::redirect( $url );
                } else {
                    $cids = array( );
                    foreach ( $foundDupes as $dupe ) {
                        $cids[$dupe[0]] = 1;
                        $cids[$dupe[1]] = 1;
                    }
                    $cidString = implode( ', ', array_keys( $cids ) );
                    $sql = "SELECT id, display_name FROM civicrm_contact WHERE id IN ($cidString) ORDER BY sort_name";
                    $dao = new CRM_Core_DAO();
                    $dao->query( $sql );
                    $displayNames = array();
                    while ( $dao->fetch() ) {
                        $displayNames[$dao->id] = $dao->display_name;
                    }
                    // FIXME: sort the contacts; $displayName 
                    // is already sort_name-sorted, so use that
                    // (also, consider sorting by dupe count first)
                    // lobo - change the sort to by threshold value
                    // so the more likely dupes are sorted first
                    $session = CRM_Core_Session::singleton();
                    $userId  = $session->get( 'userID' );
                    $mainContacts = $permission = array();
                    
                    foreach ( $foundDupes as $dupes ) {
                        $srcID = $dupes[0];
                        $dstID = $dupes[1];
                        if ( $dstID == $userId ) {
                            $srcID = $dupes[1];
                            $dstID = $dupes[0];
                        }
                        
                        if ( !array_key_exists( $srcID, $permission ) ) {
                            $permission[$srcID] = CRM_Contact_BAO_Contact_Permission::allow( $srcID, CRM_Core_Permission::EDIT );
                        }
                        if ( !array_key_exists( $dstID, $permission ) ) {
                            $permission[$dstID] = CRM_Contact_BAO_Contact_Permission::allow( $dstID, CRM_Core_Permission::EDIT );
                        }
                        
                        $canMerge = ( $permission[$dstID] && $permission[$srcID] );

                        $mainContacts[] = $row = array( 'srcID'   => $srcID,
                                                        'srcName' => $displayNames[$srcID],
                                                        'dstID'   => $dstID,
                                                        'dstName' => $displayNames[$dstID],
                                                        'weight'  => $dupes[2],
                                                        'canMerge'=> $canMerge );
                        
                        $data = serialize( $row );
                        $values[] = " ( 'civicrm_contact', $srcID, $dstID, '$cacheKeyString', '$data' ) ";
                    }
                    if ( $cid ) {
                        $this->_cid = $cid;
                    }
                    if ( $gid ) {
                        $this->_gid = $gid;
                    }
                    $this->_rgid = $rgid;
                    $this->_mainContacts = $mainContacts;

                    CRM_Core_BAO_PrevNextCache::setItem( $values );
                    $session = CRM_Core_Session::singleton( );
                    if ( $this->_cid ) {
                        $session->pushUserContext( CRM_Utils_System::url( 'civicrm/contact/deduperules', 
                                                                          "action=update&rgid={$this->_rgid}&gid={$this->_gid}&cid={$this->_cid}" ) );
                    } else {
                        $session->pushUserContext( CRM_Utils_System::url( 'civicrm/contact/dedupefind', 
                                                                          "reset=1&action=update&rgid={$this->_rgid}" ) );
                    }
                }
                
            } else {
                if ( $cid ) {
                    $this->_cid = $cid;
                }
                if ( $gid ) {
                    $this->_gid = $gid;
                }
                $this->_rgid = $rgid;
            } 
            
            $this->assign( 'action', $this->action );
            $this->browse();
        } else {
            $this->action = CRM_Core_Action::UPDATE;
            $this->edit( $this->action );
            $this->assign( 'action', $this->action );
        }
        $this->assign( 'context', $context );
        
        // parent run
        return parent::run();
    }

    /**
     * Browse all rule groups
     *  
     * @return void
     * @access public
     */
    function browse()
    {
        $this->assign( 'main_contacts', $this->_mainContacts );
     
        if ( $this->_cid ) {
            $this->assign( 'cid', $this->_cid );
        }
        if ( isset( $this->_gid ) || $this->_gid ) {
            $this->assign( 'gid', $this->_gid );
        }
        $this->assign( 'rgid', $this->_rgid );
    }

    /**
     * Get name of edit form
     *
     * @return string  classname of edit form
     */
    function editForm()
    {
        return 'CRM_Contact_Form_DedupeFind';
    }

    /**
     * Get edit form name
     *
     * @return string  name of this page
     */
    function editName()
    {
        return 'DedupeFind';
    }
    
    /**
     * Get user context
     *
     * @return string  user context
     */
    function userContext( $mode = null )
    {
        return 'civicrm/contact/dedupefind';
    }

}


