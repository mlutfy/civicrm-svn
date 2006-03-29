<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.4                                                |
 +--------------------------------------------------------------------+
 | Copyright (c) 2005 Donald A. Lobo                                  |
 +--------------------------------------------------------------------+
 | This file is a part of CiviCRM.                                    |
 |                                                                    |
 | CiviCRM is free software; you can copy, modify, and distribute it  |
 | under the terms of the Affero General Public License Version 1,    |
 | March 2002.                                                        |
 |                                                                    |
 | CiviCRM is distributed in the hope that it will be useful, but     |
 | WITHOUT ANY WARRANTY; without even the implied warranty of         |
 | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.               |
 | See the Affero General Public License for more details.            |
 |                                                                    |
 | You should have received a copy of the Affero General Public       |
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]socialsourcefoundation[DOT]org.  If you have |
 | questions about the Affero General Public License or the licensing |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | at http://www.openngo.org/faqs/licensing.html                       |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Core/Controller.php';

class CRM_Quest_Controller_PreApp extends CRM_Core_Controller {

    /**
     * class constructor
     */
    function __construct( $title = null, $action = CRM_Core_Action::NONE, $modal = true ) {
        parent::__construct( $title, $modal );

        require_once 'CRM/Quest/StateMachine/PreApp.php';
        $this->_stateMachine =& new CRM_Quest_StateMachine_PreApp( $this, $action );

        // create and instantiate the pages
        $this->addPages( $this->_stateMachine, $action );

        // add all the actions
        $config =& CRM_Core_Config::singleton( );
        $this->addActions( $config->uploadDir, array( 'uploadFile' ) );

        // set contact id and welcome name
        if ( ! $this->get( 'contact_id' ) ) {
            $session =& CRM_Core_Session::singleton( );
            $cid = $session->get( 'userID' );
            $this->set( 'contact_id' , $cid );
            $dao =& new CRM_Contact_DAO_Individual( );
            $dao->contact_id = $cid;
            if ( $dao->find( true ) ) {
                $this->set( 'welcome_name',
                            $dao->first_name );
            }
        }
    }

    function addWizardStyle( &$wizard ) {
        $wizard['style'] = array('barClass'          => 'preApp',
                                 'stepPrefixCurrent' => ' ',
                                 'stepPrefixPast'    => ' ',
                                 'stepPrefixFuture'  => ' ' );
    }

}

?>