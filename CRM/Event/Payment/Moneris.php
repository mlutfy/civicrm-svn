<?php 

/* 
 +--------------------------------------------------------------------+ 
 | CiviCRM version 1.7                                                | 
 +--------------------------------------------------------------------+ 
 | Copyright CiviCRM LLC (c) 2004-2007                                | 
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
 | License along with this program; if not, contact CiviCRM LLC       |
 | at info[AT]civicrm[DOT]org.  If you have questions about the       |
 | Affero General Public License or the licensing  of CiviCRM,        |
 | see the CiviCRM license FAQ at http://civicrm.org/licensing        |
 +--------------------------------------------------------------------+ 
*/ 
 
/** 
 * 
 * @package CRM 
 * @author Alan Dixon
 * @copyright CiviCRM LLC (c) 2004-2007 
 * $Id$ 
 * 
 */ 

require_once 'CRM/Core/Payment/Moneris.php';

class CRM_Event_Payment_Moneris extends CRM_Core_Payment_Moneris { 
    /** 
     * We only need one instance of this object. So we use the singleton 
     * pattern and cache the instance in this variable 
     * 
     * @var object 
     * @static 
     */ 
    static private $_singleton = null; 

    /** 
     * Constructor 
     *
     * @param string $mode the mode of operation: live or test
     * 
     * @return void 
     */ 
    function __construct( $mode ) {
        parent::__construct( $mode );
    }

    /** 
     * singleton function used to manage this object 
     * 
     * @param string $mode the mode of operation: live or test
 
     * @return object 
     * @static 
     * 
     */ 
    static function &singleton( $mode ) {
        if (self::$_singleton === null ) { 
            self::$_singleton =& new CRM_Event_Payment_Moneris( $mode );
        } 
        return self::$_singleton; 
    } 
}

?>
