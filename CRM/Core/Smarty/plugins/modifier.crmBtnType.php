<?php

/*
 +--------------------------------------------------------------------+
 | CiviCRM version 3.1                                                |
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

/**
 * Grab the button type from a passed button element 'name' by checking for reserved QF button type strings
 *
 * @param string $btnId
 *
 * @return string  button type, one of: 'upload', 'next', 'back', 'cancel', 'refresh')
 * @access public
 */
function smarty_modifier_crmBtnType($btnName)
{
    // default button type is 'upload'
    $btnType = 'upload';
    
    // check for _$btnType strings (listed above) in $btnName and assign type (should use regex since the btnType "keyword"
    //may not be at the end of the string). EX: btnName = '_qf_Contact_refresh_dedupe' type='refresh'
    if ( substr( $btnName, -7, 7 ) == '_cancel' ){
        $btnType = 'cancel';
    }
    return $btnType;
}


