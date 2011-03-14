<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.4                                                |
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
 * @copyright CiviCRM LLC (c) 2004-2011
 * $Id$
 *
 */

require_once 'CRM/Core/Page.php';
require_once 'CRM/Campaign/BAO/Petition.php';

/**
 * Page for displaying Petition Signatures
 */
class CRM_Campaign_Page_Petition extends CRM_Core_Page 
{

    function browse( ) {
        require_once 'CRM/Core/Permission.php';

    	//get the survey id
        $surveyId 	= CRM_Utils_Request::retrieve('sid', 'Positive', $this );
        
        $signatures = CRM_Campaign_BAO_Petition::getPetitionSignature( $surveyId );

        $this->assign('signatures', $signatures);      
    }

    function run( ) {
        $action = CRM_Utils_Request::retrieve('action', 'String',
                                              $this, false, 0 ); 
        $this->assign('action', $action);
        $this->browse();

        parent::run();
    }

}