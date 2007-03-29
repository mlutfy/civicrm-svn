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
 | License along with this program; if not, contact the Social Source |
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions      |
 | about the Affero General Public License or the licensing  of       |
 | CiviCRM, see the CiviCRM license FAQ at                            |
 | http://civicrm.org/licensing/                                      |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @copyright CiviCRM LLC (c) 2004-2007
 * $Id$
 *
 */

require_once 'CRM/Core/Selector/Base.php';
require_once 'CRM/Core/Selector/API.php';

require_once 'CRM/Utils/Pager.php';
require_once 'CRM/Utils/Sort.php';

require_once 'CRM/Contact/BAO/Query.php';

/**
 * This class is used to retrieve and display a range of
 * contacts that match the given criteria (specifically for
 * results of advanced search options.
 *
 */
class CRM_Member_Selector_Search extends CRM_Core_Selector_Base implements CRM_Core_Selector_API 
{
    /**
     * This defines two actions- View and Edit.
     *
     * @var array
     * @static
     */
    static $_links = null;

    /**
     * we use desc to remind us what that column is, name is used in the tpl
     *
     * @var array
     * @static
     */
    static $_columnHeaders;

    /**
     * Properties of contact we're interested in displaying
     * @var array
     * @static
     */
    static $_properties = array( 'contact_id', 'membership_id',
                                 'contact_type',
                                 'sort_name',
                                 'membership_type',
                                 'join_date',
                                 'start_date',
                                 'end_date',
                                 'source',
                                 'status_id',
                                 );

    /** 
     * are we restricting ourselves to a single contact 
     * 
     * @access protected   
     * @var boolean   
     */   
    protected $_single = false;

    /**  
     * are we restricting ourselves to a single contact  
     *  
     * @access protected    
     * @var boolean    
     */    
    protected $_limit = null;

    /**
     * what context are we being invoked from
     *   
     * @access protected     
     * @var string
     */     
    protected $_context = null;

    /**
     * queryParams is the array returned by exportValues called on
     * the HTML_QuickForm_Controller for that page.
     *
     * @var array
     * @access protected
     */
    public $_queryParams;

    /**
     * represent the type of selector
     *
     * @var int
     * @access protected
     */
    protected $_action;

    /** 
     * The additional clause that we restrict the search with 
     * 
     * @var string 
     */ 
    protected $_memberClause = null;

    /** 
     * The query object
     * 
     * @var string 
     */ 
    protected $_query;

    /**
     * Class constructor
     *
     * @param array   $queryParams array of parameters for query
     * @param int     $action - action of search basic or advanced.
     * @param string  $memberClause if the caller wants to further restrict the search (used in memberships)
     * @param boolean $single are we dealing only with one contact?
     * @param int     $limit  how many memberships do we want returned
     *
     * @return CRM_Contact_Selector
     * @access public
     */
    function __construct(&$queryParams,
                         $action = CRM_Core_Action::NONE,
                         $memberClause = null,
                         $single = false,
                         $limit = null,
                         $context = 'search' ) 
    {
        // submitted form values
        $this->_queryParams =& $queryParams;

        $this->_single  = $single;
        $this->_limit   = $limit;
        $this->_context = $context;

        $this->_memberClause = $memberClause;
        
        // type of selector
        $this->_action = $action;
        $this->_query =& new CRM_Contact_BAO_Query( $this->_queryParams, null, null, false, false,
                                                    CRM_Contact_BAO_Query::MODE_MEMBER );
        // CRM_Core_Error::debug( 'q', $this->_query );
       

    }//end of constructor


    /**
     * This method returns the links that are given for each search row.
     * currently the links added for each row are 
     * 
     * - View
     * - Edit
     *
     * @return array
     * @access public
     *
     */
    static function &links( $status = 'all' )
    {
        
        if ( !self::$_links['view'] ) {
            self::$_links['view'] = array(
                                          CRM_Core_Action::VIEW   => array(
                                                                   'name'     => ts('View'),
                                                                   'url'      => 'civicrm/contact/view/membership',
                                                                   'qs'       => 'reset=1&id=%%id%%&cid=%%cid%%&action=view&context=%%cxt%%&selectedChild=member',
                                                                   'title'    => ts('View Membership'),
                                                                   )
                                  );
        }
        
        if ( !self::$_links['all'] ) {
            $extraLinks = array(
                                CRM_Core_Action::UPDATE => array(
                                                                 'name'     => ts('Edit'),
                                                                 'url'      => 'civicrm/contact/view/membership',
                                                                   'qs'       => 'reset=1&action=update&id=%%id%%&cid=%%cid%%&context=%%cxt%%',
                                                                 'title'    => ts('Edit Membership'),
                                                                 ),
                                CRM_Core_Action::DELETE => array(
                                                                 'name'     => ts('Delete'),
                                                                 'url'      => 'civicrm/contact/view/membership',
                                                                 'qs'       => 'reset=1&action=delete&id=%%id%%&cid=%%cid%%&context=%%cxt%%',
                                                                 'title'    => ts('Delete Membership'),
                                                                 ),
                                );
            self::$_links['all'] = self::$_links['view'] + $extraLinks;
        }
        
        return self::$_links[$status];
    } //end of function


    /**
     * getter for array of the parameters required for creating pager.
     *
     * @param 
     * @access public
     */
    function getPagerParams($action, &$params) 
    {
        $params['status']       = ts('Member') . ' %%StatusMessage%%';
        $params['csvString']    = null;
        if ( $this->_limit ) {
            $params['rowCount']     = $this->_limit;
        } else {
            $params['rowCount']     = CRM_Utils_Pager::ROWCOUNT;
        }

        $params['buttonTop']    = 'PagerTopButton';
        $params['buttonBottom'] = 'PagerBottomButton';
    } //end of function

    /**
     * Returns total number of rows for the query.
     *
     * @param 
     * @return int Total number of rows 
     * @access public
     */
    function getTotalCount($action)
    {
        return $this->_query->searchQuery( 0, 0, null,
                                           true, false, 
                                           false, false, 
                                           false, 
                                           $this->_memberClause );
    }

    
    /**
     * returns all the rows in the given offset and rowCount
     *
     * @param enum   $action   the action being performed
     * @param int    $offset   the row number to start from
     * @param int    $rowCount the number of rows to return
     * @param string $sort     the sql string that describes the sort order
     * @param enum   $output   what should the result set include (web/email/csv)
     *
     * @return int   the total number of rows for this action
     */
     function &getRows($action, $offset, $rowCount, $sort, $output = null) 
     {
         $result = $this->_query->searchQuery( $offset, $rowCount, $sort,
                                               false, false, 
                                               false, false, 
                                               false, 
                                               $this->_memberClause );

         // process the result of the query
         $rows = array( );
         
         // check is the user has view/edit membership permission
         $permission = CRM_Core_Permission::VIEW;
         if ( CRM_Core_Permission::check( 'edit memberships' ) ) {
             $permission = CRM_Core_Permission::EDIT;
         }
         require_once 'CRM/Member/PseudoConstant.php';
         $statusTypes  = CRM_Member_PseudoConstant::membershipStatus( );
         
         $mask = CRM_Core_Action::mask( $permission );
         while ($result->fetch()) {
             $row = array();
             // the columns we are interested in
             foreach (self::$_properties as $property) {
                 $row[$property] = $result->$property;
             }
             //fix status display
             $row['status']   = $statusTypes[$row['status_id']];
             
             if ($this->_context == 'search') {
                 $row['checkbox'] = CRM_Core_Form::CB_PREFIX . $result->membership_id;
             }
             
             if ( ! $result->owner_membership_id ) {
                 $row['action']   = CRM_Core_Action::formLink( self::links( 'all' ), $mask,
                                                               array( 'id'  => $result->membership_id,
                                                                      'cid' => $result->contact_id,
                                                                      'cxt' => $this->_context ) );
             } else {
                 $row['action']   = CRM_Core_Action::formLink( self::links( 'view' ) , $mask,
                                                               array( 'id'  => $result->membership_id,
                                                                      'cid' => $result->contact_id,
                                                                      'cxt' => $this->_context ) );
             }
             
             $config =& CRM_Core_Config::singleton( );
             $contact_type    = '<img src="' . $config->resourceBase . 'i/contact_';
             switch ($result->contact_type) {
             case 'Individual' :
                 $contact_type .= 'ind.gif" alt="' . ts('Individual') . '" />';
                 break;
             case 'Household' :
                 $contact_type .= 'house.png" alt="' . ts('Household') . '" height="16" width="16" />';
                 break;
             case 'Organization' :
                 $contact_type .= 'org.gif" alt="' . ts('Organization') . '" height="16" width="18" />';
                 break;
             }
             $row['contact_type'] = $contact_type;
             
             $rows[] = $row;
         }
         return $rows;
     }
     
     
     /**
      * @return array              $qill         which contains an array of strings
      * @access public
      */
     
     // the current internationalisation is bad, but should more or less work
     // for most of "European" languages
     public function getQILL( )
     {
         return $this->_query->qill( );
     }
     
     /** 
      * returns the column headers as an array of tuples: 
     * (name, sortName (key to the sort array)) 
     * 
     * @param string $action the action being performed 
     * @param enum   $output what should the result set include (web/email/csv) 
     * 
     * @return array the column headers that need to be displayed 
     * @access public 
     */ 
    public function &getColumnHeaders( $action = null, $output = null ) 
    {
        if ( ! isset( self::$_columnHeaders ) ) {
            self::$_columnHeaders = array(
                                          array(
                                                'name'      => ts('Type'),
                                                'sort'      => 'membership_type',
                                                'direction' => CRM_Utils_Sort::DONTCARE,
                                                ),
                                          array('name'      => ts('Member Since'),
                                                'sort'      => 'join_date',
                                                'direction' => CRM_Utils_Sort::DESCENDING,
                                                ),
                                          array(
                                                'name'      => ts('Start Date'),
                                                'sort'      => 'start_date',
                                                'direction' => CRM_Utils_Sort::DONTCARE,
                                                ),
                                          array(
                                                'name'      => ts('End Date'),
                                                'sort'      => 'end_date',
                                                'direction' => CRM_Utils_Sort::DONTCARE,
                                                ),
                                          array(
                                                'name'      => ts('Source'),
                                                'sort'      => 'source',
                                                'direction' => CRM_Utils_Sort::DONTCARE,
                                                ),
                                          array(
                                                'name'      => ts('Status'),
                                                'sort'      => 'status_id',
                                                'direction' => CRM_Utils_Sort::DONTCARE,
                                                ),
                                         
                                          array('desc' => ts('Actions') ),
                                          );

            if ( ! $this->_single ) {
                $pre = array( 
                             array('desc' => ts('Contact Type') ), 
                             array( 
                                   'name'      => ts('Name'), 
                                   'sort'      => 'sort_name', 
                                   'direction' => CRM_Utils_Sort::DONTCARE, 
                                   )
                             );
                self::$_columnHeaders = array_merge( $pre, self::$_columnHeaders );
            }
        }
        return self::$_columnHeaders;
    }
    
    function &getQuery( ) {
        return $this->_query;
    }

    /** 
     * name of export file. 
     * 
     * @param string $output type of output 
     * @return string name of the file 
     */ 
     function getExportFileName( $output = 'csv') { 
         return ts('CiviCRM Member Search'); 
     } 

}//end of class

?>
