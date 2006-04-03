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
 | at http://www.openngo.org/faqs/licensing.html                      |
 +--------------------------------------------------------------------+
*/


/**
 * Personal Information Form Page
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@yahoo.com>
 * @copyright Donald A. Lobo (c) 2005
 * $Id$
 *
 */

require_once 'CRM/Quest/Form/App.php';
require_once 'CRM/Core/OptionGroup.php';

/**
 * This class generates form components for relationship
 * 
 */
class CRM_Quest_Form_App_Essay extends CRM_Quest_Form_App
{

    static $_essayId;

    /**
     * This function sets the default values for the form. Relationship that in edit/view action
     * the default values are retrieved from the database
     * 
     * @access public
     * @return void
     */
    function setDefaultValues( ) 
    {
        $defaults = array( );
        $contactID = $this->get( 'contact_id' );
        if ( $contactID )  {
            require_once 'CRM/Quest/DAO/Essay.php';
            $dao = & new CRM_Quest_DAO_Essay();
            $dao->contact_id = $contactID;
            if ( $dao->find(true) ) {
                $defaults['essay'] = $dao->essay;
                $this->_essayId = $dao->id;
                $this->set('essayId',$this->_essayId );
            }
        }
        
        return $defaults;
    }
    

    /**
     * Function to actually build the form
     *
     * @return void
     * @access public
     */
    public function buildQuickForm( ) 
    {
        $attributes = CRM_Core_DAO::getAttribute('CRM_Quest_DAO_Essay');

        // primary method to access internet
        $this->addElement('textarea',
                          'essay',
                          ts( 'List and describe the factors in your life that have most shaped you (1500 characters max).' ),
                          $attributes['essay'] );
        parent::buildQuickForm();


    }//end of function

    /**
     * Return a descriptive name for the page, used in wizard header
     *
     * @return string
     * @access public
     */
    public function getTitle()
    {
        return ts('Essay');
    }

  public function postProcess() 
    {
        $params = $this->controller->exportValues( $this->_name );
      
        require_once 'CRM/Quest/BAO/Essay.php';
     
        $contact_id = $this->get('contact_id');
        $params['contact_id'] =  $contact_id;
        $ids = array();
        $this->_essayId = $this->get('essayId');
        if ( $this->_essayId ) {
            $ids['id'] = $this->_essayId;
        }
        $essay = CRM_Quest_BAO_Essay::create( $params,$ids);
        $this->set('essayId', $essay->id );
        
    }//end of function



}

?>