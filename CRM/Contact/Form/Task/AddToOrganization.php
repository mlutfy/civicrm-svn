<?php
/*
 +--------------------------------------------------------------------+
 | CiviCRM version 1.6                                                |
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
 | Foundation at info[AT]civicrm[DOT]org.  If you have questions       |
 | about the Affero General Public License or the licensing  of       |
 | of CiviCRM, see the Social Source Foundation CiviCRM license FAQ   |
 | http://www.civicrm.org/licensing/                                  |
 +--------------------------------------------------------------------+
*/

/**
 *
 * @package CRM
 * @author Donald A. Lobo <lobo@civicrm.org>
 * @copyright CiviCRM LLC (c) 2004-2006
 * $Id$
 *
 */

require_once 'CRM/Contact/Form/Task.php';

/**
 * This class provides the functionality to add contact(s) to Organization
 */
class CRM_Contact_Form_Task_AddToOrganization extends CRM_Contact_Form_Task {
    /**
     * Build the form
     *
     * @access public
     * @return void
     */

    function preProcess( ) {
        /*
         * initialize the task and row fields
         */
        parent::preProcess( );
    }
    
    /**
     * Function to build the form
     *
     * @access public
     * @return None
     */
    function buildQuickForm( ) {

        CRM_Utils_System::setTitle( ts('Add Members to Organization') );
        $this->addElement('text', 'name'      , ts('Find Target Organization') );

        $this->add('select',
                          'relationship_type_id',
                          ts('Relationship Type'),
                          array('' => ts('- select -')) +
                          CRM_Contact_BAO_Relationship::getRelationType("Organization"),true);
    
        $searchRows    = $this->get( 'searchRows' );
        $searchCount   = $this->get( 'searchCount' );
        if ( $searchRows ) {
            $checkBoxes = array( );
            $chekFlag = 0;
            foreach ( $searchRows as $id => $row ) {
                if (!$chekFlag) {
                    $chekFlag = $id;
                }
                
                $checkBoxes[$id] = $this->createElement('radio',null, null,null,$id);
            }
            
            $this->addGroup($checkBoxes, 'contact_check');
            if ( $chekFlag ) {
                $checkBoxes[$chekFlag]->setChecked( true );
            }
            $this->assign('searchRows', $searchRows );

        }


        $this->assign( 'searchCount', $searchCount );
        $this->assign( 'searchDone'  , $this->get( 'searchDone'   ) );
        $this->assign( 'contact_type_display', ts('Organization') );
        $this->addElement( 'submit', $this->getButtonName('refresh'), ts('Search'), array( 'class' => 'form-submit' ) );
        $this->addElement( 'submit', $this->getButtonName('cancel' ), ts('Cancel'), array( 'class' => 'form-submit' ) );


        $this->addButtons( array(
                                 array ( 'type'      => 'next',
                                         'name'      => ts('Add to Organization'),
                                         'isDefault' => true   ),
                                 array ( 'type'       => 'cancel',
                                         'name'      => ts('Cancel') ),
                                 )
                           );
    }

    /**
     * process the form after the input has been submitted and validated
     *
     * @access public
     * @return None
     */
    public function postProcess() {
        require_once 'CRM/Contact/Form/Relationship.php';
        // store the submitted values in an array
        $params = $this->controller->exportValues( $this->_name );
        
        $this->set( 'searchDone', 0 );
        if ( CRM_Utils_Array::value( '_qf_AddToOrganization_refresh', $_POST ) ) {
            $searchParams['contact_type'] = array('Organization' => 'Organization');
            $searchParams['name']         = $params['name'];
            CRM_Contact_Form_Relationship::search( $searchParams );
            $this->set( 'searchDone', 1 );
            return;
        }
       
        $data = array ();
        //$params['relationship_type_id']='4_a_b';
        $data['relationship_type_id'] = $params['relationship_type_id'];
        $invalid = 0;
        $valid = 0;
        $duplicate = 0;
        if ( is_array($this->_contactIds)) {
            foreach ( $this->_contactIds as $value) {
                $ids = array();
                $ids['contact'] = $value;
                //contact b --> organization
                // contact a  -> individual
                $errors = CRM_Contact_BAO_Relationship::checkValidRelationship( $params, $ids, $params['contact_check']);
                if($errors)
                    {
                        $invalid=$invalid+1;
                        continue;
                    }
                
                if ( CRM_Contact_BAO_Relationship::checkDuplicateRelationship( $params,
                                                                               CRM_Utils_Array::value( 'contact', $ids ),
                                                                               $params['contact_check'])) { // step 2
                    $duplicate++;
                    continue;
                }
                CRM_Contact_BAO_Relationship::add($data, $ids, $params['contact_check']);
                $valid++;
            }
            
            $status = array(
                            ts('Added Contact(s) to Organization'),
                            ts('Total Selected Contact(s): %1', array(1 => $valid+$invalid+$duplicate))
                            );
            if ( $valid ) {
                $status[] = ts('New relationship record(s) created: %1.', array(1 => $valid)) . '<br/>';
            }
            if ( $invalid ) {
                $status[] = ts('Relationship record(s) not created due to invalid target contact type: %1.', array(1 => $invalid)) . '<br/>';
            }
            if ( $duplicate ) {
                $status[] = ts('Relationship record(s) not created - duplicate of existing relationship: %1.', array(1 => $duplicate)) . '<br/>';
            }
            CRM_Core_Session::setStatus( $status );
        }
    }//end of function

}

?>
