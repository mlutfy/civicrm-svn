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

class CRM_PledgeBank_BAO_Query 
{
    
    static function &getFields( ) 
    {
        $fields = array( );
        require_once 'CRM/PledgeBank/DAO/Pledge.php';
        $fields = array_merge( $fields, CRM_PledgeBank_DAO_Pledge::import( ) );
        return $fields;
    }

    /** 
     * build select for PledgeBank
     * 
     * @return void  
     * @access public  
     */
    static function select( &$query ) 
    {

    }

    static function where( &$query ) 
    {

    }
    
  
    static function whereClauseSingle( &$values, &$query ) 
    {

    }

    static function from( $name, $mode, $side ) 
    {
        return $from;
    }

    /**
     * getter for the qill object
     *
     * @return string
     * @access public
     */
    function qill( ) {
        return (isset($this->_qill)) ? $this->_qill : "";
    }
   
    static function defaultReturnProperties( $mode ) 
    {
        return $properties;
    }

    static function buildSearchForm( &$form ) 
    {

        $form->assign( 'validPledge', true );
    }
    
    static function searchAction( &$row, $id ) 
    {
    }

    static function tableNames( &$tables ) 
    {

    }
  
}


