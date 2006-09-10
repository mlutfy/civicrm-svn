<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.5                                                |
 +--------------------------------------------------------------------+
 | Copyright CiviCRM LLC (c) 2004-2006                                  |
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
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Core/Page/Basic.php';
require_once 'CRM/Core/DAO/OptionValue.php';
/**
 * Page for displaying list of Individual Suffix
 */
class CRM_Admin_Page_IndividualSuffix extends CRM_Core_Page_Basic 
{
    /**
     * The action links that we need to display for the browse screen
     *
     * @var array
     * @static
     */
    static $_links = null;

    /**
     * Get BAO Name
     *
     * @return string Classname of BAO.
     */
    function getBAOName() 
    {
        return 'CRM_Core_BAO_OptionValue';
    }

    /**
     * Get action Links
     *
     * @return array (reference) of action links
     */
    function &links()
    {
        if (!(self::$_links)) {
            // helper variable for nicer formatting
            $disableExtra = ts('Are you sure you want to disable this Individual Suffix?') . '\n\n' . ts('Users will no longer be able to select this value when adding or editing Individual Suffix.');

            self::$_links = array(
                                  CRM_Core_Action::UPDATE  => array(
                                                                    'name'  => ts('Edit'),
                                                                    'url'   => 'civicrm/admin/individualSuffix',
                                                                    'qs'    => 'action=update&id=%%id%%&reset=1',
                                                                    'title' => ts('Edit Individual Suffix') 
                                                                   ),
                                  CRM_Core_Action::DISABLE => array(
                                                                    'name'  => ts('Disable'),
                                                                    'url'   => 'civicrm/admin/individualSuffix',
                                                                    'qs'    => 'action=disable&id=%%id%%',
                                                                    'extra' => 'onclick = "return confirm(\'' . $disableExtra . '\');"',
                                                                    'title' => ts('Disable Individual Suffix') 
                                                                   ),
                                  CRM_Core_Action::ENABLE  => array(
                                                                    'name'  => ts('Enable'),
                                                                    'url'   => 'civicrm/admin/individualSuffix',
                                                                    'qs'    => 'action=enable&id=%%id%%',
                                                                    'title' => ts('Enable Individual Suffix') 
                                                                    ),
                                   CRM_Core_Action::DELETE  => array(
                                                                    'name'  => ts('Delete'),
                                                                    'url'   => 'civicrm/admin/individualSuffix',
                                                                    'qs'    => 'action=delete&id=%%id%%',
                                                                    'title' => ts('Delete Individual Suffix') 
                                                                   )
                                 );
        }
        return self::$_links;
    }

    /**
     * Browse all individual suffix
     *  
     * 
     * @return void
     * @access public
     * @static
     */
    function browse() 
    {
        $groupParams = array( 'name' => 'individual_suffix' );
        require_once 'CRM/Core/OptionValue.php';
        $optionValue = CRM_Core_OptionValue::getRows($groupParams, $this->links());   
        $this->assign('rows', $optionValue);
    }
    
    /**
     * Get name of edit form
     *
     * @return string Classname of edit form.
     */
    function editForm() 
    {
        return 'CRM_Admin_Form_IndividualSuffix';
    }
    
    /**
     * Get edit form name
     *
     * @return string name of this page.
     */
    function editName() 
    {
        return 'Individual Suffix';
    }
    
    /**
     * Get user context.
     *
     * @return string user context.
     */
    function userContext($mode = null) 
    {
        return 'civicrm/admin/individualSuffix';
    }
}

?>
