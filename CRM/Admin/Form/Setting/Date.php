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

require_once 'CRM/Admin/Form/Setting.php';

/**
 * This class generates form components for Date Formatting
 * 
 */
class CRM_Admin_Form_Setting_Date extends CRM_Admin_Form_Setting  
{
    /**
     * Function to build the form
     *
     * @return None
     * @access public
     */
    public function buildQuickForm( ) {
        CRM_Utils_System::setTitle(ts('Settings - Date'));

        $this->addElement('text', 'dateformatDatetime', ts('Complete Date and Time'));
        $this->addElement('text', 'dateformatFull', ts('Complete Date'));
        $this->addElement('text', 'dateformatPartial', ts('Month and Year'));
        $this->addElement('text', 'dateformatYear', ts('Year Only'));
        $this->addElement('text', 'dateformatTime', ts('Time Only'));
        $this->addElement('text', 'dateformatQfDate', ts('Complete Date'));
        $this->addElement('text', 'dateformatQfDatetime', ts('Complete Date and Time'));
        $this->add('date', 'fiscalYearStart', ts('Fiscal Year Start'),
                   CRM_Core_SelectValues::date( 'custom', null, null, "M\001d" ) );
        
        parent::buildQuickForm();
    }
}


